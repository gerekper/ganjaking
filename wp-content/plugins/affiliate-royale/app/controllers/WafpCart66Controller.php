<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/** Handles all the integration hooks into Cart66
  */
class WafpCart66Controller {
  public static function load_hooks() {
    add_action('wafp-setcookie', 'WafpCart66Controller::set_cart66_cookie', 10, 3);

    /** Until the Cart66 team puts this hook into their code, users will have to add this manually */
    add_action('wafp_award_commission', 'WafpCart66Controller::track', 10, 5);

    /** DEPRECATED: This is only here to make sure this functionality doesn't break. */
    add_action('cart66_award_commission', 'WafpCart66Controller::track', 10, 5);
  }

  /** We need to set an additional cookie in order to get cart66 working with Affiliate Royale **/
  public static function set_cart66_cookie( $affiliate_id, $cookie_expire_time, $base='/' ) {
    setcookie('ap_id', $affiliate_id, $cookie_expire_time, $base);
  }

  // This sets the referring affiliate of this user in the database -- we'll
  // refer back to this later when the user's transaction is completed
  public static function track($affiliate_id, $sale_amount, $txn_id, $item_id, $buyer_email) {
    $_COOKIE['wafp_click'] = $affiliate_id; // artificially set this since it won't necessarily be set already
    WafpTransaction::track( $sale_amount, $txn_id, $item_id );
  }
}
