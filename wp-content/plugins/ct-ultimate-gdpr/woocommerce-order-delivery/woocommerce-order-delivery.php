<?php
/**
 * Plugin Name: WooCommerce Order Delivery
 * Plugin URI: https://woocommerce.com/products/woocommerce-order-delivery/
 * Description: Choose a delivery date during checkout for the order.
 * Version: 2.5.1
 * Author: Themesquad
 * Author URI: https://themesquad.com/
 * Requires PHP: 5.6
 * Requires at least: 4.9
 * Tested up to: 6.2
 * Text Domain: woocommerce-order-delivery
 * Domain Path: /languages
 *
 * WC requires at least: 3.7
 * WC tested up to: 7.6
 * Woo: 976514:beaa91b8098712860ec7335d3dca61c0
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WC_OD
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Required functions.
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once 'woo-includes/woo-functions.php';
}

/**
 * Plugin updates.
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'beaa91b8098712860ec7335d3dca61c0', '976514' );

/**
 * Plugin requirements.
 */
if ( ! class_exists( 'WC_OD_Requirements', false ) ) {
	require_once dirname( __FILE__ ) . '/includes/class-wc-od-requirements.php';
}

if ( ! WC_OD_Requirements::are_satisfied() ) {
	return;
}

// Define plugin file constant.
if ( ! defined( 'WC_OD_FILE' ) ) {
	define( 'WC_OD_FILE', __FILE__ );
}

// Include the main plugin class.
if ( ! class_exists( 'WC_Order_Delivery' ) ) {
	include_once dirname( WC_OD_FILE ) . '/includes/class-wc-order-delivery.php';
}

/**
 * Gets the main instance of Order Delivery.
 *
 * Avoids the need to use a global variable.
 *
 * @since 1.0.0
 *
 * @return WC_Order_Delivery
 */
function WC_OD() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return WC_Order_Delivery::instance();
}

WC_OD();
