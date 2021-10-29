<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class WafpShortcodesController {
  public static function load_hooks() {
    add_shortcode('wafp_ipn', 'WafpShortcodesController::get_ipn');
    add_shortcode('wafp_custom_args', 'WafpShortcodesController::get_custom_args');
    add_shortcode('ar_track', 'WafpShortcodesController::ar_track');
    add_shortcode('wafp_show_if_referred', 'WafpShortcodesController::show_if_referred');
    add_shortcode('wafp_show_if_is_affiliate', 'WafpShortcodesController::show_if_is_affiliate');
    add_shortcode('wafp_show_affiliate_info', 'WafpShortcodesController::show_affiliate_info');
    add_shortcode('wafp_leaderboard', 'WafpShortcodesController::show_leaderboard');
    add_shortcode('wafp_dashboard_nav', 'WafpShortcodesController::show_nav');
  }

  public static function show_nav($atts, $content = '') {
    WafpDashboardHelper::nav();
  }

  //Shows a leaderboard of users
  public static function show_leaderboard($atts, $content = '') {
    $limit = (isset($atts['limit']) && is_numeric($atts['limit']) && $atts['limit'] > 0)?$atts['limit']:false;
    $days = (isset($atts['days']) && is_numeric($atts['days']) && $atts['days'] > 0)?$atts['days']:false;
    $data = WafpTransaction::get_leaderboard_data($limit, $days);

    ob_start();
    require(WAFP_VIEWS_PATH . "/shared/leaderboard_display.php");
    return ob_get_clean();
  }

  //Shows text wrapped in this shortcode if an affiliate cookie is set
  public static function show_if_referred($atts, $content = '') {
    if(isset($_COOKIE['wafp_click']) && is_numeric($_COOKIE['wafp_click'])) {
      return $content;
    }

    return false;
  }

  //Shows affiliate info if wafp_click is set
  // show="" can be anything listed here: http://codex.wordpress.org/Function_Reference/get_userdata
  public static function show_affiliate_info($atts, $content = '') {
    if(isset($_COOKIE['wafp_click']) && is_numeric($_COOKIE['wafp_click'])) {
      $user_data = get_userdata($_COOKIE['wafp_click']);

      $show = $atts['show'];

      if(isset($user_data->{$show}) && !empty($user_data->{$show})) {
        return $user_data->{$show};
      }
    }

    return '';
  }

  public static function show_if_is_affiliate($atts, $content = '') {
    global $user_ID;

    //User isn't logged in so don't show the content
    if(!isset($user_ID) || (int)$user_ID <= 0) {
      return '';
    }

    $wafp_user = new WafpUser($user_ID);

    if($wafp_user->is_affiliate()) {
      return $content;
    }

    return '';
  }

  public static function get_ipn($atts) {
    $ipn = WAFP_SCRIPT_URL . "&controller=paypal&action=ipn";

    $ipn = apply_filters('wafp_paypal_ipn', $ipn, $atts);

    if(isset($atts['urlencode']) && $atts['urlencode'] == 'true') {
      return urlencode($ipn);
    }

    return '<input type="hidden" name="notify_url" value="' . $ipn . '" />';
  }

  public static function get_custom_args($atts) {
    $custom_args = '';
    $ip_addr = $_SERVER['REMOTE_ADDR'];

    //Setup the $_COOKIE artificially if ?aff= is being used
    if(isset($_REQUEST['aff'])) {
      $id_or_login = urldecode($_REQUEST['aff']);

      if(is_numeric($id_or_login)) {
        $_COOKIE['wafp_click'] = $id_or_login;
      }
      else {
        $affiliate = new WafpUser();
        $affiliate->load_user_data_by_login( $id_or_login );

        if(isset($affiliate->userdata[ WafpUser::$id_str ])) {
          $_COOKIE['wafp_click'] = $affiliate->get_id();
        }
      }
    }
    //End artificial $_COOKIE code

    $affiliate_id = isset($_COOKIE['wafp_click'])?$_COOKIE['wafp_click']:null;

    if(isset($affiliate_id) and $affiliate_id and is_numeric($affiliate_id)) {
      $custom_args .= "aff_id={$affiliate_id}";
    }

    if(isset($ip_addr) and $ip_addr) {
      if(!empty($custom_args)) {
        $custom_args .= '&';
      }

      $custom_args .= "ip_addr={$ip_addr}";
    }

    $custom_args = apply_filters('wafp_paypal_custom_args', $custom_args, $atts);

    if(isset($atts['urlencode']) && $atts['urlencode'] == 'true') {
      return urlencode($custom_args);
    }

    return '<input type="hidden" name="custom" value="' . $custom_args . '" />';
  }

  public static function ar_track($atts)
  {
    if(isset($atts['use_params']) and $atts['use_params'] == 'true') {
      $use_params = true;
    }
    elseif(isset($atts['use_params'])) {
      $use_params = explode(",",$atts['use_params']);
    }
    else {
      $use_params = false;
    }

    $amount     = (isset($atts['amount']))?WafpUtils::with_default($atts['amount'], ''):'';
    $order_id   = (isset($atts['order_id']))?WafpUtils::with_default($atts['order_id'], ''):'';
    $prod_id    = (isset($atts['prod_id']))?WafpUtils::with_default($atts['prod_id'], ''):'';
    $aff_id     = (isset($atts['aff_id']))?WafpUtils::with_default($atts['aff_id'], ''):'';
    $subscr_id  = (isset($atts['subscr_id']))?WafpUtils::with_default($atts['subscr_id'], ''):'';

    if($use_params === true) {
      $amount     = (isset($_REQUEST[$amount]))?WafpUtils::with_default($_REQUEST[$amount], ''):$amount;
      $order_id   = (isset($_REQUEST[$order_id]))?WafpUtils::with_default($_REQUEST[$order_id], ''):$order_id;
      $prod_id    = (isset($_REQUEST[$prod_id]))?WafpUtils::with_default($_REQUEST[$prod_id], ''):$prod_id;
      $aff_id     = (isset($_REQUEST[$aff_id]))?WafpUtils::with_default($_REQUEST[$aff_id], ''):$aff_id;
      $subscr_id  = (isset($_REQUEST[$subscr_id]))?WafpUtils::with_default($_REQUEST[$subscr_id], ''):$subscr_id;
    }
    elseif($use_params === false) {
      $order_id   = WafpUtils::with_default($order_id, uniqid());
    }
    else {
      $amount     = WafpUtils::with_default((in_array('amount', $use_params)?$_REQUEST[$amount]:$amount), '');
      $order_id   = WafpUtils::with_default((in_array('order_id', $use_params)?$_REQUEST[$order_id]:$order_id ), (in_array($order_id, $use_params)?'':uniqid())); // Only generate unique id if no order_id is specified at all
      $prod_id    = WafpUtils::with_default((in_array('prod_id', $use_params)?$_REQUEST[$prod_id]:$prod_id), '');
      $aff_id     = WafpUtils::with_default((in_array('aff_id', $use_params)?$_REQUEST[$aff_id]:$aff_id), '');
      $subscr_id  = WafpUtils::with_default((in_array('subscr_id', $use_params)?$_REQUEST[$subscr_id]:$subscr_id), '');
    }

    if(!empty($order_id)) {
      WafpTransaction::track($amount, $order_id, $prod_id, $aff_id, $subscr_id);
    }
  }
} //End class
