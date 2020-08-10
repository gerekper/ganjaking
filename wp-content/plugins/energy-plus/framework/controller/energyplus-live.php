<?php

/**
* EnergyPlus Helpers
*
* Functions for front-end
*
* @since      1.0.0
* @package    EnergyPlus
* @subpackage EnergyPlus/framework
* @author     EN.ER.GY <support@en.er.gy>
* */


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class EnergyPlus_Live extends EnergyPlus {

  /**
  * Starts everything
  *
  * @return void
  */

  public static function run() {
    // Nothing to do
  }

  /**
  * Track and log events in front-end
  *
  * @return none
  */

  public static function pulse() {
    global $wpdb, $woocommerce;

    $visitor = self::get_visitor();

    $data['ref'] = "";

    switch (EnergyPlus_Helpers::post('t')) {

      // Product
      case "p":

      $ID      = intval( EnergyPlus_Helpers::post('i', 0));
      $product = wc_get_product ($ID);

      if (!$product) {
        wp_die();
      }

      $data['type'] = 1;
      $data['id']   = $ID;

      self::add_request($data);

      break;

      // Category
      case "c":

      $ID       = intval( EnergyPlus_Helpers::post('i', 0) );
      $category = get_term_by( 'id', $ID, 'product_cat', 'ARRAY_A' );

      if (!$category) {
        wp_die();
      }

      $data['type'] = 2;
      $data['id']   = $ID;

      self::add_request($data);

      break;

      // Home
      case "h":

      $data['type'] = 7;
      $data['id']   = 1;

      self::add_request($data);

      break;

      // Search
      case "s":

      $data['type']  = 10;
      $data['id']    = 1;
      $data['extra'] = sanitize_text_field(EnergyPlus_Helpers::post('i', ''));

      self::add_request($data);

      break;

      // Pages
      case "o":

      $ID      =  intval(EnergyPlus_Helpers::post('i', 0));

      $title = get_the_title ($ID);

      if (!$title) {
        wp_die();
      }

      $data['type']  = 17;
      $data['id']    = $ID;
      $data['extra'] = sanitize_text_field($title);

      self::add_request($data);

      break;

    }

  }

  /**
  * Insert front-end events to database
  *
  * @since 1.0.0
  * @param array     $data
  */

  public static function add_request($data = array()) {
    global $wpdb, $woocommerce;

    // Detects if it is a bot.

    if (isset($_SERVER['HTTP_USER_AGENT'])) {
      $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
      $bot_identifiers = array(
        'bot',
        'slurp',
        'crawler',
        'spider',
        'curl',
        'facebook',
        'fetch'
      );

      foreach ($bot_identifiers as $identifier) {
        if (stripos($user_agent, $identifier) !== FALSE) {
          return TRUE;
        }
      }
    }

    if (!isset($data['visitor'])) {
      $data['visitor'] = self::get_visitor();
    }

    if (!isset($data['date'])) {
      $data['date'] = EnergyPlus_Helpers::time();
    }

    if (!isset($data['ip'])) {

      $data['ip'] =   preg_replace( '/[^0-9a-fA-F:., ]/', '', WC_Geolocation::get_ip_address());

      if (!filter_var($data['ip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $data['ip'] = '0.0.0.0';
      }

      $geo = WC_Geolocation::geolocate_ip($data['ip'],true);
      if (!empty($geo['country'])) {
        $data['ip'] =  $geo['country'] ." — " . $data['ip'];
      }

    }

    if (!isset($data['ref'])) {
      $data['ref'] = '';
    }

    if (!isset($data['extra'])) {
      $data['extra'] = '';
    }

    $insert = $wpdb->insert( $wpdb->prefix."energyplus_requests",
    array(
      'session_id' => self::get_session(),
      'visitor'    => $data['visitor'],
      'year'       => EnergyPlus_Helpers::strtotime($data['date'],'y'),
      'month'      => EnergyPlus_Helpers::strtotime($data['date'],'m'),
      'week'       => EnergyPlus_Helpers::strtotime($data['date'],'W'),
      'day'        => EnergyPlus_Helpers::strtotime($data['date'],'d'),
      'date'       => $data['date'],
      'ip'         => $data['ip'],
      'type'       => $data['type'],
      'id'         => $data['id'],
      'extra'      => $data['extra'],
      'ref'        => $data['ref']
    ),
    array('%s', '%s', '%d','%d','%d','%d', '%s', '%s','%s', '%s','%s', '%s')
  );

}


/**
* Get user id
*
* @return none
*/

public static function get_visitor() {

  $visitor = get_current_user_id();

  if ($visitor === 0) {
    if (!isset($_COOKIE["energyplus-u"])) {

      $visitor = md5(uniqid().rand(1,100000).WC_Geolocation::get_ip_address().time());
      setcookie("energyplus-u", $visitor, time()+(365*24*60*60), COOKIEPATH, COOKIE_DOMAIN);

    } else {
      $visitor = sanitize_key($_COOKIE["energyplus-u"]);
    }

    if (32 !== strlen($visitor)) {
      $visitor = md5(uniqid().time());
    }
    $visitor = "v".$visitor;
  }

  return $visitor;
}


/**
* Set and get session id
*
* @return none
*/

public static function get_session() {

  if (!isset($_COOKIE["energyplus-session"])) {
    $session_id = md5(uniqid().rand(1,100000).WC_Geolocation::get_ip_address().time());
    setcookie("energyplus-session", $session_id, time()+(20*60), COOKIEPATH, COOKIE_DOMAIN);
  } else {

    $_session_id = EnergyPlus_Helpers::clean($_COOKIE["energyplus-session"]);

    if ($_session_id AND 32 === strlen($_session_id)) {
      $session_id = sanitize_key($_session_id);
    } else {
      $session_id = md5(uniqid().rand(1,100000).WC_Geolocation::get_ip_address().time());
      setcookie("energyplus-session", $session_id, time()+(20*60), COOKIEPATH, COOKIE_DOMAIN);
    }
  }
  return $session_id;
}


/**
* Hook for add to cart
*
* @since 1.0.0
*/

public static function woocommerce_add_to_cart($key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {

  if (!isset($_COOKIE["energyplus-session"])) {
    return false;
  }

  $data['type']  = 4;
  $data['id']    = $product_id;
  $data['extra'] = serialize(array( 'quantity' => $quantity, 'variation_id' => $variation_id));

  self::add_request($data);

}

/**
* Hook for remove cart
*
* @since 1.0.0
*/


public static function woocommerce_remove_cart_item($cart_item_key, $_this) {
  global $woocommerce;

  $items = $woocommerce->cart->get_cart();

  if (isset ($items[$cart_item_key])) {
    $data['type']  = 5;
    $data['id']    = absint($items[$cart_item_key]['product_id']);
    $data['extra'] = serialize(array( 'line_total' =>  floatval($items[$cart_item_key]['line_total'] )));

    self::add_request($data);
  }
}

/**
* Hook for new order
*
* @since 1.0.0
*/


public static function woocommerce_checkout_order_review() {
  global $woocommerce;

  $data['type']  = 6;
  $data['id']    = 0;
  $data['extra'] = '';

  self::add_request($data);
}

}

?>
