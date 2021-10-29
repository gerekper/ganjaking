<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/** This is a special controller that handles all of the WP eCommerce specific
  * public static functions for the Affiliate Program.
  */
class WafpECommerceController {

  /**
   * the constructor. setup hooks and filters.
   */
  public static function load_hooks() {
    add_action( 'wpsc_confirm_checkout', 'WafpECommerceController::confirm_checkout');
    add_filter( 'wpsc_paypal_standard_notify_url', 'WafpECommerceController::send_click');
    add_filter( 'wpsc_paypal_express_notify_url', 'WafpECommerceController::send_click');
  }

  /**
   * Track this e-commerce transaction
   *
   * @return void
   * @author Brad Van Skyhawk
   **/
  public static function confirm_checkout( $purchase_id ) {
    global $wpdb;

    $success=3; // In WP E Commerce apparently 3 means the credit card processing was successful

    $processed = $wpdb->get_var( $wpdb->prepare( "SELECT processed FROM " . WPSC_TABLE_PURCHASE_LOGS . " WHERE id=%d", $purchase_id ) );

    if( $processed and intval($processed)==intval($success) ) {
      // Get the subtotal
      $purch_sql = $wpdb->prepare( "SELECT SUM(price*quantity) FROM `".WPSC_TABLE_CART_CONTENTS."` WHERE `purchaseid`=%d", $purchase_id );
      $subtotal = $wpdb->get_var( $purch_sql );

      // Set the cookie, if passed from paypal
      if(isset($_REQUEST['wafp_click']))
        $_COOKIE['wafp_click'] = $_REQUEST['wafp_click'];

      WafpTransaction::track( $subtotal, "{$purchase_id}-wpec", __('E-Commerce Purchase', 'affiliate-royale', 'easy-affiliate') );
    }
  }

  /**
   * Pass the click id to paypal. Then we get it back later
   *
   * @return string
   * @author Brad Van Skyhawk
   */
  public static function send_click( $url )
  {
    return $url . '&wafp_click=' . $_COOKIE['wafp_click'];
  }

}

