<?php
/**
 * Plugin Name: WooCommerce Store Credit
 * Plugin URI: https://woo.com/products/store-credit/
 * Description: Create "store credit" coupons for customers which are redeemable at checkout.
 * Version: 4.4.1
 * Author: KoiLab
 * Author URI: https://koilab.com/
 * Requires PHP: 5.6
 * Requires at least: 4.9
 * Tested up to: 6.4
 * Text Domain: woocommerce-store-credit
 * Domain Path: /languages/
 *
 * WC requires at least: 3.7
 * WC tested up to: 8.4
 * Woo: 18609:c4bf3ecec4146cb69081e5b28b6cdac4
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WC_Store_Credit
 * @since   2.1.11
 */

defined( 'ABSPATH' ) || exit;

/**
 * Plugin requirements.
 */
if ( ! class_exists( 'WC_Store_Credit_Requirements', false ) ) {
	require_once __DIR__ . '/includes/class-wc-store-credit-requirements.php';
}

if ( ! WC_Store_Credit_Requirements::are_satisfied() ) {
	return;
}

// Define WC_STORE_CREDIT_FILE constant.
if ( ! defined( 'WC_STORE_CREDIT_FILE' ) ) {
	define( 'WC_STORE_CREDIT_FILE', __FILE__ );
}

// Include the main class of the plugin.
if ( ! class_exists( 'WC_Store_Credit' ) ) {
	include_once __DIR__ . '/includes/class-wc-store-credit.php';
}

/**
 * Main instance of the plugin.
 *
 * Returns the main instance of the plugin to prevent the need to use globals.
 *
 * @since 3.0.0
 *
 * @return WC_Store_Credit
 */
function wc_store_credit() {
	return WC_Store_Credit::instance();
}

wc_store_credit();
