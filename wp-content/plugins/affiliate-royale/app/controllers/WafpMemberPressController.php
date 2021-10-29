<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/** This is a special controller that handles all of the MemberPress specific
  * public static functions for the Affiliate Program.
  */
class WafpMemberPressController {
  public static function load_hooks() {
    add_action('mepr-txn-status-complete', 'WafpMemberPressController::track_transaction');
    add_action('mepr-txn-status-refunded', 'WafpMemberPressController::refund_transaction');

    // MemberPress Product Group Commission meta box integration
    add_action('mepr-product-admin-enqueue-script', 'WafpMemberPressController::enqueue_product_scripts');
    add_action('mepr-product-meta-boxes', 'WafpMemberPressController::product_meta_boxes');
    add_action('mepr-product-save-meta', 'WafpMemberPressController::save_product');

    // MemberPress Product Group Commission calculations
    add_filter('wafp_commission_percentages', 'WafpMemberPressController::commission_percentages', 10, 2);
    add_filter('wafp-recurring-commission', 'WafpMemberPressController::recurring_commission', 10, 2);
    add_filter('wafp-commission-type', 'WafpMemberPressController::commission_type', 10, 2);
    add_filter('wafp-commission-source', 'WafpMemberPressController::commission_source', 10, 2);

    // MemberPress specific customizations for the affiliate dashboard
    add_filter('wafp-affiliate-profile', 'WafpMemberPressController::affiliate_profile', 10, 2);
    add_filter('wafp-affiliate-profile-label', 'WafpMemberPressController::affiliate_profile_label', 10, 2);

    // Affiliate Based Coupons
    add_action('mepr-coupon-meta-boxes', 'WafpMemberPressController::coupon_meta_boxes');
    add_action('mepr-coupon-save-meta', 'WafpMemberPressController::save_coupon');
    add_action('mepr-coupon-admin-enqueue-script', 'WafpMemberPressController::enqueue_coupon_scripts');
    add_action('wafp-dashboard-links-page-li', 'WafpMemberPressController::list_my_coupons');

    // Extend the transaction obect with commissions if the MemberPress Dev tools is installed
    add_filter('mpdt-prepare-obj-transaction', 'WafpMemberPressController::extend_api_transaction');
  }

  /* Tracks when a transaction completes */
  public static function track_transaction($txn) {
    global $wafp_options;

    //Kill it if it's not a payment type
    if($txn->txn_type != MeprTransaction::$payment_str) {
      return;
    }

    // Track the coupon to an affiliate if a coupon exists and that coupon is tied to an affiliate
    self::track_coupon($txn);

    //Kill this here if txn_count is greater than 1 and recurring is false
    if(!$wafp_options->recurring && $txn->subscription_id) {
      $sub = $txn->subscription();
      if($sub->txn_count > 1) { return; }
    }

    //If the admin is manually completing a txn or creating a new txn
    //we need to unset the cookie that may be in their browser so a false
    //commission doesn't get paid.
    if(is_super_admin() && isset($_COOKIE['wafp_click'])) {
      unset($_COOKIE['wafp_click']);
    }

    if($txn->amount > 0.00) {
      $prd = $txn->product();
      $_REQUEST['mepr_product_for_wafp'] = $prd; //Don't delete this $_REQUEST item - I use it down the line in wafp-calculate-commission filter for some folks
      WafpTransaction::track($txn->amount, $txn->id, $prd->post_title, $txn->user_id);
    }

    if($afro_txn = WafpTransaction::get_one_by_trans_num($txn->id)) {
      WafpTransaction::update_refund($afro_txn->id, 0.00);
    }
  }

  public static function refund_transaction($txn) {
    if($afro_txn = WafpTransaction::get_one_by_trans_num($txn->id))
      WafpTransaction::update_refund($afro_txn->id, $txn->amount);
  }

  public static function product_meta_boxes($product) {
    add_meta_box( "memberpress-affiliate-royale-options",
                  __('Affiliate Royale Commissions', 'affiliate-royale', 'easy-affiliate'),
                  "WafpMemberPressController::product_meta_box",
                  MeprProduct::$cpt, "side", "default", array('product' => $product) );
  }

  public static function enqueue_product_scripts($hook) {
    if('post.php' == $hook) {
      wp_enqueue_style( 'wafp-mp-products-css',
                        WAFP_CSS_URL.'/memberpress-products.css',
                        array('mepr-products-css'),
                        WAFP_VERSION );

      wp_enqueue_script('wafp-mp-products-js',
                        WAFP_JS_URL.'/memberpress-products.js',
                        array('jquery','mepr-products-js'),
                        WAFP_VERSION);
    }
  }

  //Don't use $post here, it is null on new product - use args instead
  public static function product_meta_box($post, $args) {
    $mepr_options = MeprOptions::fetch();
    $product = $args['args']['product'];
    $commission_groups_enabled = false;
    $commissions = array("0.00");
    $commission_type = 'percentage';
    $recurring = true;

    if($obj = get_post_meta($product->ID, 'wafp_commissions', true)) {
      $commissions = $obj;
      $commission_groups_enabled = get_post_meta($product->ID, 'wafp_commission_groups_enabled', true);
      $commission_type = get_post_meta($product->ID, 'wafp_commission_type', true);
      $recurring = get_post_meta($product->ID, 'wafp_recurring', true);
    }

    require(WAFP_VIEWS_PATH.'/options/memberpress_product_meta_box.php');
  }

  public static function save_product($product) {
    update_post_meta($product->ID, 'wafp_commission_groups_enabled', isset($_POST['wafp_enable_commission_group']));
    update_post_meta($product->ID, 'wafp_commission_type', $_POST['wafp_commission_type']);
    update_post_meta($product->ID, 'wafp_commissions', json_decode(stripslashes($_POST['wafp_commissions_json'])));
    update_post_meta($product->ID, 'wafp_recurring', isset($_POST['wafp_recurring']));
  }

  public static function commission_percentages($global, $user_id) {
    if($group = self::get_commission_group($user_id)) {
      return $group->commissions;
    }

    return $global;
  }

  public static function get_commission_group($user_id) {
    $usr = new MeprUser($user_id);
    $pids = $usr->active_product_subscriptions();

    foreach($pids as $pid) {
      $commission_groups_enabled = get_post_meta($pid,'wafp_commission_groups_enabled', true);

      // Just short circuit once we find our first product with groups enabled
      if($commission_groups_enabled) {
        $product = new MeprProduct($pid);
        return (object)array('commission_type' => get_post_meta($pid, 'wafp_commission_type', true),
                             'commission_source' => array('slug'=>"product-{$pid}", 'label' => sprintf(__('%s Commission Group', 'affiliate-royale', 'easy-affiliate'), $product->post_title)),
                             'commissions' => get_post_meta($pid, 'wafp_commissions', true),
                             'recurring' => get_post_meta($pid, 'wafp_recurring', true));
      }
    }

    return false;
  }

  public static function commission_type($commission_type, $user_id) {
    if($groups = self::get_commission_group($user_id)) {
      $commission_type = $groups->commission_type;
    }

    return $commission_type;
  }

  public static function recurring_commission($recurring, $user_id) {
    if($groups = self::get_commission_group($user_id)) { $recurring = $groups->recurring; }

    return $recurring;
  }

  public static function commission_source($source, $user_id) {
    if($groups = self::get_commission_group($user_id)) {
      $source = $groups->commission_source;
    }

    return $source;
  }

  public static function affiliate_profile($profile, $aff_id) {
    $aff = new MeprUser($aff_id);
    $prds = $aff->active_product_subscriptions('products');

    if(empty($prds)) {
      $profile['products'] = __('None','affiliate-royale', 'easy-affiliate');
    }
    else {
      $prd_names = array_map(
        function($prod) {
          return $prod->post_title;
        },
        $prds
      );
      $profile['products'] = implode(',', $prd_names);
    }

    return $profile;
  }

  public static function affiliate_profile_label($label, $slug) {
    if($slug == 'products') { $label = __('Products','affiliate-royale', 'easy-affiliate'); }

    return $label;
  }

  //COUPON STUFF
  public static function track_coupon($txn) {
    global $wafp_options;

    if(($coupon = $txn->coupon()) && $coupon instanceof MeprCoupon) {
      $enabled = (isset($coupon->ID) && $coupon->ID)?get_post_meta($coupon->ID, 'wafp_coupon_affiliate_enabled', true):false;

      if($enabled && ($affiliate_id = get_post_meta($coupon->ID, 'wafp_coupon_affiliate', true))) {
        //Set the user's referrer too
        $wafp_user = new WafpUser($txn->user_id);
        $wafp_user->set_referrer($affiliate_id);
        $wafp_user->store();

        //The rest of this isn't really necessary as the user's referrer wins all
        //But let's do it anyways - in case we change that in the future
        $cookie_name = 'wafp_click';

        // Override the affiliate if there's a coupon associated with an affiliate
        $_COOKIE[$cookie_name] = $affiliate_id;
        $cookie_expire_time = time() + (60*60*24) * $wafp_options->expire_after_days; // Expire in 60 days

        setcookie($cookie_name, $affiliate_id, $cookie_expire_time, '/');
        do_action('wafp-setcookie', $affiliate_id, $cookie_expire_time, '/');
      }
    }
  }

  public static function coupon_meta_boxes($coupon) {
    add_meta_box( "memberpress-affiliate-royale-coupon-options",
                  __('Associate Affiliate', 'affiliate-royale', 'easy-affiliate'),
                  "WafpMemberPressController::coupon_meta_box",
                  MeprCoupon::$cpt, "side", "default", array('coupon' => $coupon) );
  }

  //Don't use $post here, it is null on new product - use args instead
  public static function coupon_meta_box($post, $args) {
    $mepr_options = MeprOptions::fetch();
    $coupon = $args['args']['coupon'];
    $enabled = (isset($coupon->ID) && $coupon->ID)?get_post_meta($coupon->ID, 'wafp_coupon_affiliate_enabled', true):false;
    $affiliate_login = ''; //We'll populate later
    $affiliate_id = (isset($coupon->ID) && $coupon->ID)?get_post_meta($coupon->ID, 'wafp_coupon_affiliate', true):false;

    if($affiliate_id) {
      $user = get_user_by('id', $affiliate_id);
      $affiliate_login = $user->user_login;
    }

    require(WAFP_VIEWS_PATH.'/options/memberpress_coupon_meta_box.php');
  }

  public static function save_coupon($coupon) {
    if(isset($_POST['mepr-associate-affiliate-enable']) && !empty($_POST['mepr-associate-affiliate-username'])) {
      $username = stripslashes($_POST['mepr-associate-affiliate-username']);
      $user = get_user_by('login', $username);

      if($user instanceof WP_User && isset($user->ID) && $user->ID && isset($coupon->ID) && $coupon->ID) {
        update_post_meta($coupon->ID, 'wafp_coupon_affiliate_enabled', 1);
        update_post_meta($coupon->ID, 'wafp_coupon_affiliate', $user->ID);
      }
    }
    else {
      if(isset($coupon->ID) && $coupon->ID) {
        update_post_meta($coupon->ID, 'wafp_coupon_affiliate_enabled', 0);
        update_post_meta($coupon->ID, 'wafp_coupon_affiliate', 0);
      }
    }
  }

  public static function enqueue_coupon_scripts($hook) {
      wp_enqueue_style( 'wafp-mp-coupons-css',
                        WAFP_CSS_URL.'/memberpress-coupons.css',
                        array(),
                        WAFP_VERSION );

      wp_enqueue_script('wafp-mp-coupons-js',
                        WAFP_JS_URL.'/memberpress-coupons.js',
                        array('jquery', 'suggest'),
                        WAFP_VERSION);
  }

  public static function list_my_coupons($aff_id) {
    global $wpdb;
    $my_coupons = $wpdb->get_col("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'wafp_coupon_affiliate' AND meta_value = {$aff_id}");

    if(!empty($my_coupons)) {
      require(WAFP_VIEWS_PATH.'/dashboard/memberpress-coupons.php');
    }
  }

  public static function extend_api_transaction($obj) {
    $txn = WafpTransaction::get_one_by_trans_num($obj['id']);

    $commissions = array();

    if(!empty($txn)) {
      $res = WafpCommission::get_all_by_transaction_id($txn->id);

      foreach($res as $row) {
        $user = new WafpUser($row->affiliate_id);
        $commissions[] = array(
          'affiliate'  => $user->get_field('user_login'),
          'level'      => ((int)$row->commission_level + 1),
          'type'       => $row->commission_type,
          'amount'     => $row->commission_amount,
          'correction' => $row->correction_amount,
          'name'       => $user->get_full_name(),
          'percent'    => $row->commission_percentage
        );
      }

      $obj['commissions'] = $commissions;
    }

    return $obj;
  }
} //End class
