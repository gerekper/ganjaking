<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

/** This is a special controller that handles all of the Shopp
  * specific public static functions for Affiliate Royale.
  */
class WafpShoppController {

  public static function load_hooks() {
    add_action('shopp_init', 'WafpShoppController::init');
    add_action('shopp_order_notifications', 'WafpShoppController::notify');
    add_action('shopp_order_success', 'WafpShoppController::track');
    add_action('shopp_order_txnstatus_update', 'WafpShoppController::record_refunds');
  }

  public static function init() {
    // put the affiliate id in the Shopp session
    if (isset($_COOKIE['wafp_click'])) {
      $affiliate_id = $_COOKIE['wafp_click'];
      ShoppingObject::store('wafp_affiliate_id', $affiliate_id);
    }
  }

  /**
   * Track the paypal transaction using Shopp session
   */
  public static function notify() {
    // Code from Shopp PayPal gateway
    // Cancel processing if this is not a PayPal Website Payments Standard/Express Checkout IPN
    if(isset($_POST['txn_type']) && $_POST['txn_type'] != "cart")
      return false;

    $target = false;

    if(isset($_POST['txn_id']) && !isset($_POST['parent_txn_id']))
      $target = $_POST['txn_id'];
    elseif(!empty($_POST['parent_txn_id']))
      $target = $_POST['parent_txn_id'];

    // No transaction target: invalid IPN, silently ignore the message
    if(!$target) return;

    $Purchase = new Purchase($target, 'txnid');

    // Purchase record exists, track it
    if($Purchase->txnid == $target && !empty($Purchase->id)) {
      if(! function_exists('get_plugins'))
        require_once(ABSPATH . '/wp-admin/includes/plugin.php');

      $plugin_folder = get_plugins('/' . plugin_basename('shopp'));
      $version = $plugin_folder['Shopp.php']['Version'];

      if(version_compare($version, '1.2.0') >= 0) {
        // The 1.2 way to get the affiliate id from the Shopp session
        Shopping::resession($_POST['custom']);
        $Shopping = ShoppShopping();
        $_COOKIE['wafp_click'] = $Shopping->data->wafp_affiliate_id;
      }
      else {
        // The 1.1 way to get the affiliate id from the Shopp session
        global $Shopp;
        $Shopp->resession($_POST['custom']);
        $_COOKIE['wafp_click'] = $Shopp->Shopping->data->wafp_affiliate_id;
      }

      WafpTransaction::track( $Purchase->subtotal - $Purchase->discount,
                              $Purchase->id,
                              __('Shopp Purchase', 'affiliate-royale', 'easy-affiliate') );
    }
  }

  /* Tracks when a transaction completes */
  public static function track($Purchase) {
    $plugin_folder = get_plugins('/' . plugin_basename('shopp'));
    $version = $plugin_folder['Shopp.php']['Version'];

    WafpTransaction::track($Purchase->subtotal - $Purchase->discount, $Purchase->id, __('Shopp Purchase', 'affiliate-royale', 'easy-affiliate'));
  }

  public static function record_refunds($txnstatus, $Purchase) {
    if(strtolower($txnstatus) == 'refunded')
      WafpTransaction::update_refund($Purchase->id, (float)($Purchase->subtotal - $Purchase->discount));
  }
}
