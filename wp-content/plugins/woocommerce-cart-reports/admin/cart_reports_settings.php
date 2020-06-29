<?php
/**
 *
 *
 */

class AV8_Cart_Reports_Settings {

  /**
   *
   *
   */
  public function __construct() {
    if ( is_admin() ) {
      add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_settings_scripts' ) );
    }

    add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_settings_page' ) );
  }

  /**
   * Initialize admin script
   */
  public function enqueue_settings_scripts() {
    wp_enqueue_script( 'wc_cart_reports_settings_script',
      plugin_dir_url( __FILE__ ) . '../assets/js/admin-settings.js' );
  }

  public function add_settings_page( $settings ) {
    $settings[] = include 'settings/cart_reports_settings_general.php';

    return $settings;
  }
} //END CLASS
