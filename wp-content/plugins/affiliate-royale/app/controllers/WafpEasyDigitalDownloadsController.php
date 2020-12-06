<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/** Handles all the integration hooks into Easy Digital Downloads
  */

class WafpEasyDigitalDownloadsController
{
  public static function load_hooks()
  {
    add_action('edd_insert_payment', 'WafpEasyDigitalDownloadsController::set_wafp_click_order_meta', 10, 2);
    add_action('edd_update_payment_status', 'WafpEasyDigitalDownloadsController::track_order', 10, 3);
  }

  public static function set_wafp_click_order_meta($payment_id, $status)
  {
    $user_id = (int)get_post_meta($payment_id, '_edd_payment_user_id', true);

    if(isset($_COOKIE['wafp_click']) and !empty($_COOKIE['wafp_click']))
    {
      if($user_id)
      {
        $wafp_user = new WafpUser($user_id);
        $wafp_user->set_referrer($_COOKIE['wafp_click']);
        $wafp_user->store();
      }

      //We're not guarenteed to have a user_id so lets store it in the post_meta for the order
      update_post_meta($payment_id, '_edd_wafp_click', $_COOKIE['wafp_click']);
    }
  }

  public static function track_order($payment_id, $new_status, $old_status)
  {
    // Check if the payment was already set to complete
    if($old_status == 'publish' || $old_status == 'complete')
      return;

    // Make sure the receipt is only sent when new status is complete
    if($new_status != 'publish' && $new_status != 'complete')
      return;

    $order_subtotal = edd_get_payment_amount($payment_id);
    $user_id = (int)get_post_meta($payment_id, '_edd_payment_user_id', true);
    $order_key = get_post_meta($payment_id, '_edd_payment_purchase_key', true);

    if($order_subtotal > 0.00)
    {
      $wafp_click = (int)get_post_meta($payment_id, '_edd_wafp_click', true);

      if($wafp_click)
      {
        $_COOKIE['wafp_click'] = $wafp_click;

        WafpTransaction::track($order_subtotal, $order_key, __('EDD Purchase', 'affiliate-royale', 'easy-affiliate'), $user_id);
      }
    }
  }
} //End class
