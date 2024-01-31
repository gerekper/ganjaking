<?php
/**
 * Plugin Name: WooCommerce Order Delivery
 * Plugin URI: https://woo.com/products/woocommerce-order-delivery/
 * Description: Choose a delivery date during checkout for the order.
 * Version: 2.6.1
 * Author: KoiLab
 * Author URI: https://koilab.com/
 * Requires PHP: 5.6
 * Requires at least: 4.9
 * Tested up to: 6.4
 * Text Domain: woocommerce-order-delivery
 * Domain Path: /languages
 *
 * WC requires at least: 3.7
 * WC tested up to: 8.5
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
 * Plugin requirements.
 */
if ( ! class_exists( 'WC_OD_Requirements', false ) ) {
	require_once __DIR__ . '/includes/class-wc-od-requirements.php';
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
