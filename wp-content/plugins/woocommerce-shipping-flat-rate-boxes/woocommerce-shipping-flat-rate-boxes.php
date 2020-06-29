<?php
/**
 * Plugin Name: WooCommerce Flat Rate Box Shipping
 * Plugin URI: https://woocommerce.com/products/flat-rate-box-shipping/
 * Description: Flat rate box shipping lets you define costs for boxes to different destinations. Items are packed into boxes based on item size and volume.
 * Version: 2.0.14
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Requires at least: 4.0
 * Tested up to: 5.3
 * WC requires at least: 2.6
 * WC tested up to: 4.2
 *
 * Copyright: © 2020 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Woo: 182761:b28c4fbb609b15ef3f4a1a9db11b0a45
 *
 * @package woocommerce-shipping-flat-rate-boxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WC_BOX_SHIPPING_VERSION', '2.0.14' ); // WRCS: DEFINED_VERSION.

// Plugin init hook.
add_action( 'plugins_loaded', 'wc_shipping_flat_rate_boxes_init' );

/**
 * Initialize plugin.
 */
function wc_shipping_flat_rate_boxes_init() {

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'wc_shipping_flat_rate_boxes_woocommerce_deactivated' );
		return;
	}

	require_once __DIR__ . '/includes/class-wc-flat-rate-box-shipping.php';
	new WC_Flat_Rate_Box_Shipping();
}

/**
 * WooCommerce Deactivated Notice.
 */
function wc_shipping_flat_rate_boxes_woocommerce_deactivated() {
	/* translators: %s: WooCommerce link */
	echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Flat Rate Box Shipping requires %s to be installed and active.', 'woocommerce-shipping-flat-rate-boxes' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
}

// Activation hook.
register_activation_hook( __FILE__, 'wc_shipping_flat_rate_boxes_install' );

/**
 * Activation function.
 */
function wc_shipping_flat_rate_boxes_install() {
	include_once __DIR__ . '/installer.php';
	update_option( 'box_shipping_version', WC_BOX_SHIPPING_VERSION );
}

/**
 * Callback function for loading an instance of this method
 *
 * @param mixed $instance Instance ID.
 * @return WC_Shipping_Flat_Rate_Boxes
 */
function woocommerce_get_shipping_method_flat_rate_boxes( $instance = false ) {
	return new WC_Shipping_Flat_Rate_Boxes( $instance );
}
