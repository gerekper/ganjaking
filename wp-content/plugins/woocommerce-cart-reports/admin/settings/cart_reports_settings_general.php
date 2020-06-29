<?php
/**
 *
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
} // Exit if accessed directly

if ( class_exists( 'AV8_Cart_Reports_Settings_General', false ) ) {
  return new AV8_Cart_Reports_Settings_General();
}

class AV8_Cart_Reports_Settings_General extends WC_Settings_Page {

  /**
   *
   *
   */
  public function __construct() {
    $this->id    = 'cart_reports';
    $this->label = __( 'Cart Reports', 'cart_reports' );

    parent::__construct();
  }

  public function get_roles() {
    global $wp_roles;
    $roles          = $wp_roles->get_names();
    $roles['guest'] = 'Guest'; //Need to add guest manually

    return $roles;
  }

  /**
   * Initialize Cart Report Settings
   *
   */
  public function get_settings() {
    $cart_reports_settings = array(
      array(
        'name' => __( 'Cart Reports Settings', 'woocommerce_cart_reports' ),
        'type' => 'title',
        'desc' => '',
        'id'   => 'minmax_quantity_options'
      ),
      array(
        'name'     => __( 'Cart Timeout (seconds)', 'woocommerce_cart_reports' ),
        'desc'     => __( 'Site activity timeout length for cart abandonment, in seconds.',
          'woocommerce_cart_reports' ),
        'desc_tip' => true,
        'id'       => 'wc_cart_reports_timeout',
        'type'     => 'number',
        'defualt'  => '1200'
      ),
      array(
        'name'     => __( 'Widget Time Range (days)', 'woocommerce_cart_reports' ),
        'desc'     => __( 'Time-range displayed in the middle column of the "Recent Cart Activity" dashboard widget.',
          'woocommerce_cart_reports' ),
        'desc_tip' => true,
        'id'       => 'wc_cart_reports_dashboardrange',
        'default'  => '2',
        'type'     => 'number'
      ),
      array(
        'name'         => __( 'Show Products On The Cart Index Page', 'woocommerce_cart_reports' ),
        'desc'         => __( 'Displaying cart products may slow down table listing when showing many carts at once.',
          'woocommerce_cart_reports' ),
        'desc_tip'     => true,
        'id'           => 'wc_cart_reports_productsindex',
        'default'      => 'yes',
        'type'         => 'checkbox',
        'tooltip_html' => __( 'Hello', 'woocommerce_cart_reports' )
      ),
      array(
        'name'     => __( 'Excluded Roles', 'woocommerce_cart_reports' ),
        'desc'     => __( 'Choose WP Roles to exclude from cart tracking', 'woocommerce_cart_reports' ),
        'id'       => 'wc_cart_reports_trackedroles',
        'type'     => 'multiselect',
        'desc_tip' => true,
        'options'  => $this->get_roles(),
      ),
      array(
        'name' => __( 'Log Customer IP Address', 'woocommerce_cart_reports' ),
        'desc' => __( 'Logged IP addresses are visible in the edit cart view.<br/>NOTE: In order to comply with GDPR you must obtain a user\'s permission before collecting their IP address',
          'woocommerce_cart_reports' ),
        'id'   => 'wc_cart_reports_logip',
        'type' => 'checkbox'
      ),

      array(
        'name'     => __( 'Automatically delete carts?', 'woocommerce_cart_reports' ),
        'desc'     => __( 'Saving a large number of carts can affect site performance. Automatically clearing the cart lists can help increase site speed.',
          'woocommerce_cart_reports' ),
        'desc_tip' => true,
        'id'       => 'wc_cart_reports_expiration_opt_in',
        'type'     => 'checkbox'
      ),
      array(
        'name'     => __( 'Clear carts older than (days)', 'woocommerce_cart_reports' ),
        'desc'     => __( 'Any cart that becomes older than the number of days specified will be automatically deleted in the background. The deletion cannot be undone',
          'woocommerce_cart_reports' ),
        'desc_tip' => true,
        'id'       => 'wc_cart_reports_expiration',
        'default'  => '0',
        'type'     => 'number'
      ),
      array( 'type' => 'sectionend', 'id' => 'woocommerce_cart_report_settings' ),
    );

    return apply_filters( 'cart_reports_settings', $cart_reports_settings );
  }

  public function output() {
    $settings = $this->get_settings();

    WC_Admin_Settings::output_fields( $settings );
  }

  public function save() {
    $settings = $this->get_settings();

    WC_Admin_Settings::save_fields( $settings );
  }

}

return new AV8_Cart_Reports_Settings_General();
