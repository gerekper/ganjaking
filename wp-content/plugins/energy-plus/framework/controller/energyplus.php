<?php

/**
* EnergyPlus Core
*
* @since      1.0.0
* @package    EnergyPlus
* @subpackage EnergyPlus/framework
* @author     EN.ER.GY <support@en.er.gy>
*/


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}


class EnergyPlus {

  public static $theme      = 'one';

  /**
  * Construct of EnergyPlus
  *
  * @since    1.0.0
  */
  public function __construct() {
    //	Nothing to do
  }

  /**
  * Starts everyting
  *
  * @since  1.0.0
  */

  public function start() {

    /* EnergyPlus Admin is loading */
    if (is_admin())  {
      (new EnergyPlus_Admin());
    }

    add_action( 'energyplus_cron_daily', 'EnergyPlus_Reports::cron_daily');

    //
    add_action( 'save_post_shop_order',                 'EnergyPlus_Events::save_post_shop_order',  20, 1  );
    add_action( 'woocommerce_update_order',             'EnergyPlus_Events::save_post_shop_order',  10, 1  );
    add_action( 'woocommerce_checkout_order_processed', 'EnergyPlus_Events::new_order',  20, 1  );
    add_action( 'woocommerce_thankyou',                 'EnergyPlus_Events::save_post_shop_order',  10, 1  );
    add_action( 'comment_post',                         'EnergyPlus_Events::comment_post', 10, 2 );
    add_action( 'woocommerce_add_to_cart',              'EnergyPlus_Live::woocommerce_add_to_cart', 10, 6);
    add_action( 'woocommerce_remove_cart_item',         'EnergyPlus_Live::woocommerce_remove_cart_item', 10, 2);
    add_action( 'woocommerce_checkout_order_review',    'EnergyPlus_Live::woocommerce_checkout_order_review', 10, 2 );
    add_action( 'woocommerce_low_stock',                'EnergyPlus_Events::low_stock',  10, 1 );
    add_action( 'woocommerce_no_stock',                'EnergyPlus_Events::low_stock',  10, 1 );

    ////

    add_filter('post_updated_messages', 'EnergyPlus_Events::save_post_shop_order');

    // Starting Reactors

    if (EnergyPlus_Reactors::is_installed('login')) {
      $class = "Reactors__login__login";
      if (class_exists($class)) {
        $class::init();
      }
    }


// Enable EnergyPlus Pulse for tracking
if ( "1" === EnergyPlus::option('feature-pulse', "0")) {
  add_action('wp_ajax_nopriv_energyplus_pulse', 'EnergyPlus_Live::pulse');
  add_action('wp_ajax_energyplus_pulse', 'EnergyPlus_Live::pulse');
  self::enable_live_pulse();
}
}


/**
* Track pages in front-end
*
* @since  1.0.0
*/

public static function enable_live_pulse()  {
  add_action("wp_footer", function() {

    wp_enqueue_script('energyplus',  EnergyPlus_Public . 'js/energyplus.js');

    $JSvars = array(
      'ajax_url'                => admin_url('admin-ajax.php')
    );


    $JSvars["energyplus_p"] = "pulse";

    if (EnergyPlus_Helpers::get('s')) {

      $JSvars["energyplus_t"] = "s";
      $JSvars["energyplus_i"] = EnergyPlus_Helpers::get('s', '');
    } else if (is_front_page()) {

      $JSvars["energyplus_t"] = "h";
      $JSvars["energyplus_i"] = 0;

    } else if (is_product()) {
      global $product;

      $JSvars["energyplus_t"] = "p";
      $JSvars["energyplus_i"] = $product->get_id();

    } else if (is_product_category()) {
      global $wp_query;

      $cat = $wp_query->get_queried_object();

      $JSvars["energyplus_t"] = "c";
      $JSvars["energyplus_i"] = $cat->term_id;

    } else {

      global $wp_query;

      if (isset($wp_query->queried_object) && is_object($wp_query->queried_object) && isset($wp_query->queried_object->has_archive)) {
        $page = get_page_by_path($wp_query->queried_object->has_archive);
        if ($page) {
          $JSvars["energyplus_t"] = "o";
          $JSvars["energyplus_i"] = $page->ID;
        }
      } else if (isset($wp_query->post->ID) && $wp_query->post->ID > 0) {
        $JSvars["energyplus_t"] = "o";
        $JSvars["energyplus_i"] = intval($wp_query->post->ID);
      }
    }

    wp_localize_script('energyplus', 'EnergyPlus_vars', $JSvars);
  });
}

/**
* Options for EnergyPlus
*
* @since  1.0.0
*/

public static function option($key, $default = '', $action = 'get', $user = false) {

  if ($user) {
    $current_user    = wp_get_current_user();
    $key .= '__' . intval( $current_user->ID ) . '__';
  }

  if ('get' === $action) {
    $value = get_option( 'energyplus_' . $key, $default );
    return $value;
  }	else
  {
    update_option( 'energyplus_' . $key, $default );
  }
}

/**
* Starts Woocommece API
*
* @since  1.0.0
*/

public static function wc_engine()  {
  global $wpdb;

  WC()->api->includes();
  WC()->api->register_resources( new WC_API_Server( '/' ) );

}


}

?>
