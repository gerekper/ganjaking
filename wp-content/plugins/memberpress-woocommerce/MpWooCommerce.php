<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/*
Integration of WooCommerce into MemberPress
*/
class MpWooCommerce {
  public function __construct() {
  //PROTECTING PRODUCTS FROM PURCHASE WITH MEMBERPRESS RULES
    add_filter('woocommerce_is_purchasable',          array($this, 'override_is_purchasable'), 11, 2);
    add_filter('woocommerce_product_is_visible',      array($this, 'override_is_visible'), 11, 2);
    add_filter('mepr-pre-run-rule-content',           array($this, 'dont_hide_woocommerce_product_content'), 11, 3);

  //BUYING MEMBERSHIPS VIA WOOCOMMERCE
    add_filter('woocommerce_product_data_tabs',                 array($this, 'add_membership_product_tab'));
    add_action('woocommerce_product_data_panels',               array($this, 'show_membership_product_tab_content'));
    add_action('woocommerce_process_product_meta',              array($this, 'save_product_tab_content'));
    //woocommerce_checkout_registration_required - doesn't work because of a race-condition I think, instead I'll use pre_option_woocommerce_enable_guest_checkout
    add_filter('pre_option_woocommerce_enable_guest_checkout',  array($this, 'override_guest_checkout_if_membership'), 100, 2);
    add_filter('woocommerce_checkout_registration_enabled',     array($this, 'allow_registration_at_checkout_if_membership'), 100);
    add_filter('woocommerce_is_sold_individually',              array($this, 'maybe_disable_quantity_fields'), 10, 2);

    //Capture different status'
    add_action('woocommerce_order_status_pending',    array($this, 'maybe_sync_transaction'));
    add_action('woocommerce_order_status_failed',     array($this, 'maybe_sync_transaction'));
    add_action('woocommerce_order_status_on-hold',    array($this, 'maybe_sync_transaction'));
    add_action('woocommerce_order_status_processing', array($this, 'maybe_sync_transaction'));
    add_action('woocommerce_order_status_completed',  array($this, 'maybe_sync_transaction'));
    add_action('woocommerce_order_status_refunded',   array($this, 'maybe_sync_transaction'));
    add_action('woocommerce_order_status_cancelled',  array($this, 'maybe_sync_transaction'));

    //KILLING THESE FOR NOW - TOO MANY COMPLAINTS ABOUT IT
    //Integrating account pages
    add_action('template_redirect',         array($this, 'maybe_redirect_account_page'));
    add_action('mepr_account_nav',          array($this, 'add_shop_nav_tab'));
    add_action('mepr_account_nav_content',  array($this, 'add_shop_nav_content'));
    add_action('wp_head',                   array($this, 'hide_nav_on_woo_orders_page'));
    add_action('wp_footer',                 array($this, 'change_subscriptions_nav_title'));
  }


//PROTECTING PRODUCTS FROM PURCHASE WITH MEMBERPRESS RULES
  public function override_is_purchasable($is, $prd) {
    if(!$is) { return $is; } //if it's already not purchasable, no need to go further

    $post = get_post($prd->get_id());

    return !MeprRule::is_locked($post);
  }

  public function override_is_visible($is, $prd_id) {
    if(!$is) { return $is; } //if it's already not visible, no need to go further

    $post = get_post($prd_id);

    return !MeprRule::is_locked($post);
  }

  //Never hide WooCommerce the_content
  public function dont_hide_woocommerce_product_content($protect, $post, $uri) {
    if(isset($post) && isset($post->post_type) && $post->post_type == 'product') { return false; }

    return $protect;
  }


//BUYING MEMBERSHIPS VIA WOOCOMMERCE
  //Add a new tab when editing the WooCommerce Products for the Membership syncing settings
  public function add_membership_product_tab($tabs) {
    global $post;

    if(!isset($post->ID)) { return; }

    $tabs['mepr'] = array(
      'label'    => __('MemberPress', 'memberpress', 'memberpress-woocommerce'),
      'target'  => 'mepr_membership',
      'class'    => array('show_if_virtual')
    );
    return $tabs;
  }

  //Show the options on the MemberPress tab when editing a WooCommerce Product
  public function show_membership_product_tab_content() {
    global $post;

    if(!isset($post->ID)) { return; }

    $options = array('0' => __('None (Not Synced)', 'memberpress', 'memberpress-woocommerce'));
    $selected = $this->get_chosen_membership($post->ID);
    $checked = $this->get_sync_when_processing($post->ID);
    $all_memberships = MeprCptModel::all('MeprProduct');

    if(empty($all_memberships)) {
      echo '<div id="mepr_membership" class="panel woocommerce_options_panel">No Memberships Created Yet</div>';
      return;
    }

    foreach($all_memberships as $prd) {
      $options[$prd->ID.''] = $prd->post_title;
    }

    ?>
      <div id="mepr_membership" class="panel woocommerce_options_panel">
        <div class="options_group">
          <p>&nbsp;<?php _e('Select a Membership level to add the user to when they purchase this Product:', 'memberpress', 'memberpress-woocommerce'); ?></p>
          <?php
            woocommerce_wp_select (
              array(
                'id' => 'mepr_membership_id',
                'label' => __('Membership', 'memberpress', 'memberpress-woocommerce'),
                'description' => '',
                'value' => $selected,
                'options' => $options
              )
            );

            woocommerce_wp_checkbox (
              array(
                'id'          => 'mepr_sync_when_processing',
                'label'       => __('Access Before Payment', 'memberpress', 'memberpress-woocommerce'),
                'description' => __('Allow user to access membership content before Order is completed', 'memberpress', 'memberpress-woocommerce'),
                'value'       => $checked
              )
            );
          ?>
        </div>
      </div>
    <?php
  }

  //Save the Membership options when updating WooCommerce Product
  public function save_product_tab_content($product_id) {
    $prd = new WC_Product($product_id);

    if( (isset($_POST['_virtual']) || (!isset($_POST['_virtual']) && $prd->is_virtual())) && //Only virtual products supported yo
        isset($_POST['mepr_membership_id']) &&
        !empty($_POST['mepr_membership_id']) &&
        (int)$_POST['mepr_membership_id'] > 0
    ) {
      update_post_meta($product_id, '_mepr_membership_id', (int)$_POST['mepr_membership_id']);

      if(isset($_POST['mepr_sync_when_processing'])) {
        update_post_meta($product_id, '_mepr_sync_when_processing', 'yes');
      } else {
        update_post_meta($product_id, '_mepr_sync_when_processing', 'no');
      }
    }
    else {
      update_post_meta($product_id, '_mepr_membership_id', 0);
      update_post_meta($product_id, '_mepr_sync_when_processing', 'no');
    }
  }

  //Loop through the order's items, and see if we need to sync any memberships
  //This is where most of the magic happens yo!
  public function maybe_sync_transaction($order_id) {
    $order            = new WC_Order($order_id);
    $order_key        = $order->get_order_key();
    $status           = $order->get_status();
    $user             = $order->get_user();

    if($user === false) { return; } //Do nothing if there's no WP_User to associate this with

    $order_items = $order->get_items('line_item'); //LOOP THROUGH THESE

    foreach($order_items as $itm) {
      $itm_id           = $itm->get_id();
      $prd_id           = $itm->get_product_id();
      $prd              = $itm->get_product();
      $while_processing = $this->get_sync_when_processing($prd_id);
      $txn_num          = $order_key . '_i_' . $itm_id;
      $existing         = false;

      if(!$prd->is_virtual()) { continue; } // only supporting virtual products for now

      if(!($membership_id = $this->get_chosen_membership($prd_id))) { continue; } // nothing to sync here

      // let's check and make sure this txn doesn't already exist
      $ex_txn = MeprTransaction::get_one_by_trans_num($txn_num);

      //It doesn't exist
      if(!isset($ex_txn->id) || empty($ex_txn->id)) {
        $membership       = new MeprProduct($membership_id);
        $expires_at       = $membership->get_expires_at();
        $txn              = new MeprTransaction();
        $txn->amount      = $itm->get_total();
        $txn->total       = $itm->get_total();
        $txn->user_id     = $user->ID;
        $txn->product_id  = $membership_id;
        $txn->trans_num   = $txn_num;
        $txn->txn_type    = MeprTransaction::$payment_str;
        $txn->gateway     = 'manual';
        $txn->ip_addr     = $order->get_customer_ip_address();
        $txn->created_at  = gmdate('c');
        $txn->expires_at  = (is_null($expires_at)) ? MeprUtils::mysql_lifetime() : gmdate('c', $expires_at);
      }
      else { //It does exist
        $txn = new MeprTransaction($ex_txn->id);
        $existing = true;
      }

      //Fire some hooks for Corporate Accounts
      if(!$existing) {
        $txn->status = MeprTransaction::$pending_str;
        $txn->store();
        do_action('mepr-signup', $txn);
      }

      //Set the txn's status - default to pending if nothing else matches up
      if($status == 'failed') {
        $txn->status = MeprTransaction::$failed_str;
      }
      elseif($status == 'refunded') {
        $txn->status = MeprTransaction::$refunded_str;
      }
      // $while_processing will also cover on-hold, processing, and pending payment
      elseif($status == 'completed' || $while_processing != 'no') {
        $txn->status = MeprTransaction::$complete_str;
      }
      else {
        $txn->status = MeprTransaction::$pending_str;
      }

      //Finally, let's store this beast!
      $txn->store();
    }
  }

  //Which Membership are we syncing with?
  public function get_chosen_membership($product_id) {
    if(!$product_id) { return 0; }

    return get_post_meta($product_id, '_mepr_membership_id', true);
  }

  //Should we create the Membership when the Order status is "processing"?
  public function get_sync_when_processing($product_id) {
    if(!$product_id) { return 'no'; }

    return get_post_meta($product_id, '_mepr_sync_when_processing', 'no');
  }

  //See if a membership is in the cart
  public function is_membership_in_cart() {
    global $woocommerce;

    if(!isset($woocommerce->cart)) { return false; }

    $contents = $woocommerce->cart->get_cart(); //A_Array of items

    if(empty($contents)) { return false; }

    foreach($contents as $key => $item) {
      if(isset($item['product_id'])) {
        if($this->get_chosen_membership($item['product_id'])) {
          return true;
        }
      }
    }

    //If we made it here, there's no memberships in the cart yo
    return false;
  }

  //If guest checkout is enabled, but the user has a Membership in their cart -- we need to make sure they are FORCED to register for a user account
  public function override_guest_checkout_if_membership($override, $option) {
    if($this->is_membership_in_cart()) {
      return 'no';
    }

    return $override;
  }

  //If guest checkout is enabled, but the user has a Membership in their cart -- we need to make sure they are forced AND ALLOWED to register for a user account
  public function allow_registration_at_checkout_if_membership($enabled) {
    if($this->is_membership_in_cart()) {
      $enabled = true;
    }

    return $enabled;
  }

  //Disable the quantity field for membership-type Products
  public function maybe_disable_quantity_fields($individual, $product) {
    if($this->get_chosen_membership($product->get_id())) {
      $individual = true;
    }

    return $individual;
  }

  public function maybe_redirect_account_page() {
    $mepr_options = MeprOptions::fetch();

    if(function_exists('is_account_page') && is_account_page() && !is_view_order_page()) {
      MeprUtils::wp_redirect($mepr_options->account_page_url());
    }
  }

  public function add_shop_nav_tab($user) {
    if(!function_exists('woocommerce_account_orders')) { return; }
    $mepr_options = MeprOptions::fetch();

    $order_active = (isset($_GET['action']) && $_GET['action'] == 'mepr-shop-orders')?'mepr-active-nav-tab':'';
    $dls_active = (isset($_GET['action']) && $_GET['action'] == 'mepr-shop-dls')?'mepr-active-nav-tab':'';

    ?>
      <span class="mepr-nav-item mepr-shop-orders <?php echo $order_active; ?>">
        <a href="<?php echo $mepr_options->account_page_url('action=mepr-shop-orders'); ?>">Orders</a>
      </span>
      <span class="mepr-nav-item mepr-shop-dls <?php echo $dls_active; ?>">
        <a href="<?php echo $mepr_options->account_page_url('action=mepr-shop-dls'); ?>">Downloads</a>
      </span>
    <?php
  }

  public function add_shop_nav_content($action) {
    if(!function_exists('woocommerce_account_orders')) { return; }

    if($action == 'mepr-shop-orders') {
      // echo do_shortcode('[woocommerce_my_account]');
      woocommerce_account_orders(1);
    }
    elseif($action == 'mepr-shop-dls') {
      woocommerce_account_downloads();
    }
  }

  //hides the woocommerce links on the order page and also the "payments" tab on the account page
  public function hide_nav_on_woo_orders_page() {
    if(!function_exists('woocommerce_account_orders')) { return; }

    ?>
      <style>nav.woocommerce-MyAccount-navigation, span.mepr-payments { display:none !important; }</style>
    <?php
  }

  //Changes subscriptions to memberships
  public function change_subscriptions_nav_title() {
    if(!function_exists('woocommerce_account_orders')) { return; }

    ?>
      <script>
        var subscriptionElement = document.getElementById('mepr-account-subscriptions');

        if(subscriptionElement != null) {
          document.getElementById("mepr-account-subscriptions").innerHTML="<?php _e('Memberships', 'memberpress-woocommerce'); ?>";
        }
      </script>
    <?php
  }
} //End class
