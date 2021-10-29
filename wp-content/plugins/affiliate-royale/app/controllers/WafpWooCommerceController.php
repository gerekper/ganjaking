<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/** Handles all the integration hooks into Woocommerce
  */

class WafpWooCommerceController {
  public static function load_hooks() {
    add_filter('save_post', 'WafpWooCommerceController::setup', 10, 2);
    add_filter('save_post', 'WafpWooCommerceController::track', 100, 2);
  }

  /** Store the affiliate id with a postmeta item associated with the WooCommerce
    */
  public static function setup($post_id, $post) {
    // When the order is created record the affiliate id in some post meta ...
    if(!is_super_admin() and $post->post_type == 'shop_order' and isset($_COOKIE['wafp_click'])) {
      $existing_ar_affiliate = get_post_meta($post_id, 'ar_affiliate', true);

      //don't override an existing affiliate
      if(!$existing_ar_affiliate)
        update_post_meta($post_id, 'ar_affiliate', $_COOKIE['wafp_click']);
    }
  }

  /** Tracks when a transaction completes
    */
  public static function track($post_id, $post) {
    global $wafp_options;

    if($post->post_type != 'shop_order') {
      return $post_id;
    }

    $subtotal   = 0.00;
    $order      = new WC_Order($post_id);
    $order_key  = (method_exists($order, 'get_order_key'))?$order->get_order_key():$order->order_key;
    $user       = $order->get_user();
    $status     = $order->get_status();

    if($status != 'completed') {
      return $post_id;
    }

    //Calculate order sub-totals
    foreach($order->get_items() as $item) {
      $subtotal += $item->get_subtotal();
    }

    //Get subtotal cost of cart then minus cart discounts
    //TODO - Figure out how shipping, shipping tax, fees, and discount taxes fit in here????
    $subtotal -= $order->get_total_discount();
    $subtotal = WafpUtils::format_float($subtotal);

    //Don't use the admins cookie ever! We'll get the real cookie below
    unset($_COOKIE['wafp_click']);

    //Set the cookie from what's stored in the post meta for this Order
    if($affiliate_id = get_post_meta($post_id, 'ar_affiliate', true)) {
      $_COOKIE['wafp_click'] = $affiliate_id;
    }

    //Override the $_COOKIE for future payments when no user_id (guest checkout) is available
    if(!isset($_COOKIE['wafp_click']) && $wafp_options->recurring && $user === false) {
      $email = WafpWooCommerceController::get_email_by_order_id($post_id);

      if($email !== false) {
        $prior_affiliate_id = WafpWooCommerceController::get_prior_affiliate_id($email);

        if(!empty($prior_affiliate_id) && (int)$prior_affiliate_id > 0) {
          $_COOKIE['wafp_click'] = $prior_affiliate_id;
          update_post_meta($post_id, 'ar_affiliate', $_COOKIE['wafp_click']);
        }
      }
    }

    //Override the $_COOKIE for recurring transactions when a user_id is available
    if(!isset($_COOKIE['wafp_click']) && $wafp_options->recurring && $user !== false) {
      $wafp_user = new WafpUser($user->ID);
      $referrer = $wafp_user->get_referrer();

      if(!empty($referrer) && (int)$referrer > 0) {
        $_COOKIE['wafp_click'] = $referrer;
        update_post_meta($post_id, 'ar_affiliate', $_COOKIE['wafp_click']);
      }
    }

    if($user === false) {
      WafpTransaction::track($subtotal, $order_key, __('Woo Commerce Purchase', 'affiliate-royale', 'easy-affiliate'));
    }
    else {
      WafpTransaction::track($subtotal, $order_key, __('Woo Commerce Purchase', 'affiliate-royale', 'easy-affiliate'), $user->ID);
    }
  }

  //Get the email address associated with a WooCommerce order
  public static function get_email_by_order_id($post_id) {
    $email = get_post_meta($post_id, '_billing_email', true);

    if(!empty($email) && is_email($email))
      return $email;

    return false;
  }

  //Look for a prior affilaite ID associated with an older order from this customer's email address
  public static function get_prior_affiliate_id($email) {
    global $wpdb;

    return $wpdb->get_var("SELECT meta_value
                            FROM {$wpdb->postmeta}
                            WHERE post_id IN (SELECT post_id
                                                FROM {$wpdb->postmeta}
                                                WHERE meta_key = '_billing_email'
                                                  AND meta_value = '{$email}')
                              AND meta_key = 'ar_affiliate'
                          ORDER BY post_id DESC
                          LIMIT 1");
  }
} //End class
