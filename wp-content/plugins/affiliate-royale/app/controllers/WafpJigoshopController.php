<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/** Handles all the integration hooks into Jigoshop
  */

class WafpJigoshopController
{
  public static function load_hooks()
  {
    add_action('jigoshop_new_order', 'WafpJigoshopController::set_wafp_click_order_meta');
    add_action('jigoshop_payment_complete', 'WafpJigoshopController::track_order');
    add_action('order_status_completed_to_refunded', 'WafpJigoshopController::refund_order');
    add_action('order_status_processing_to_refunded', 'WafpJigoshopController::refund_order');
  }

  public static function set_wafp_click_order_meta($order_id)
  {
    $order = new jigoshop_order($order_id);

    if(isset($_COOKIE['wafp_click']) and !empty($_COOKIE['wafp_click']))
    {
      if($order->user_id) //Don't do isset($order->user_id - doesn't work)
      {
        $wafp_user = new WafpUser($order->user_id);
        $wafp_user->set_referrer($_COOKIE['wafp_click']);
        $wafp_user->store();
      }

      //We're not guarenteed to have a user_id so lets store it in the post_meta for the order
      update_post_meta($order_id, '_jigoshop_wafp_click', $_COOKIE['wafp_click']);
    }
  }

  public static function track_order($order_id)
  {
    $order = new jigoshop_order($order_id);

    if($order->order_discount_subtotal > 0.00)
    {
      $wafp_click = (int)get_post_meta($order_id, '_jigoshop_wafp_click', true);

      if($wafp_click)
      {
        $_COOKIE['wafp_click'] = $wafp_click;

        WafpTransaction::track((float)($order->order_discount_subtotal - $order->order_shipping), $order->order_key, __('Jigoshop Purchase', 'affiliate-royale', 'easy-affiliate'), $order->user_id);
      }
    }
  }

  public static function refund_order($order_id)
  {
    $order = new jigoshop_order($order_id);

    //No way to see how much was refunded, so we need to refund 100% of it
    if($afro_txn = WafpTransaction::get_one_by_trans_num($order->order_key))
      WafpTransaction::update_refund($afro_txn->id, $afro_txn->sale_amount);
  }
} //End class
