<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/** This is a special controller that handles all of the MarketPress specific
  * public static functions for the Affiliate Program.
  */

class WafpMarketPressController {
  public static function load_hooks()
  {
    add_action('mp_new_order', 'WafpMarketPressController::set_wafp_click_order_meta');
    add_action('transition_post_status', 'WafpMarketPressController::track_order', 10, 3);
  }

  public static function set_wafp_click_order_meta($order)
  {
    global $user_ID;

    if(isset($_COOKIE['wafp_click']) and !empty($_COOKIE['wafp_click']))
    {
      if($user_ID)
      {
        $wafp_user = new WafpUser($user_ID);
        $wafp_user->set_referrer($_COOKIE['wafp_click']);
        $wafp_user->store();
      }

      $data = array('wafp_click' => $_COOKIE['wafp_click'], 'user_id' => (int)$user_ID);

      //We're not guarenteed to have a user_id so lets store it in the post_meta for the order
      update_post_meta($order->ID, 'mp_wafp_data', $data);

      //If the order was paid immediately we need to track it here
      if(isset($order->mp_paid_time) && !empty($order->mp_paid_time) && is_numeric($order->mp_paid_time))
        self::track_order('order_paid', null, get_post($order->ID));
    }
  }

  public static function track_order($new_status, $old_status, $post)
  {
    if($post->post_type != 'mp_order' || !class_exists('MarketPress') || $new_status != 'order_paid')
      return;

    $mp = new MarketPress();
    $order = $mp->get_order($post->ID);

    $subtotal = (float)((float)$order->mp_order_total - (float)$order->mp_shipping_total - (float)$order->mp_tax_total);

    if($subtotal > 0.00)
    {
      $wafp_data = get_post_meta($order->ID, 'mp_wafp_data', true);

      if($wafp_data['wafp_click'])
      {
        $_COOKIE['wafp_click'] = $wafp_data['wafp_click'];

        WafpTransaction::track($subtotal, $order->post_title, __('MarketPress Purchase', 'affiliate-royale', 'easy-affiliate'), $wafp_data['user_id']);
      }
    }
  }
} //End class
