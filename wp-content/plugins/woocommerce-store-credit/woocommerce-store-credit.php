<?php
/**
 * Plugin Name: WooCommerce Store Credit
 * Plugin URI: https://woocommerce.com/products/store-credit/
 * Description: Create "store credit" coupons for customers which are redeemable at checkout. Also, generate and email store credit coupons to customers via the backend.
 * Version: 3.2.1
 * Author: Themesquad
 * Author URI: https://themesquad.com/
 * Requires at least: 4.7
 * Tested up to: 5.4
 * WC requires at least: 3.4
 * WC tested up to: 4.2
 * Woo: 18609:c4bf3ecec4146cb69081e5b28b6cdac4
 *
 * Text Domain: woocommerce-store-credit
 * Domain Path: /languages/
 *
 * Copyright: 2014-2020 WooCommerce.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WC_Store_Credit
 * @since   2.1.11
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
woothemes_queue_update( plugin_basename( __FILE__ ), 'c4bf3ecec4146cb69081e5b28b6cdac4', '18609' );

/**
 * Check if WooCommerce is active and the minimum requirements are satisfied.
 */
if ( ! is_woocommerce_active() || version_compare( get_option( 'woocommerce_db_version' ), '3.4', '<' ) ) {
	add_action( 'admin_notices', 'wc_store_credit_requirements_notice' );
	return;
}

/**
 * Displays an admin notice when the minimum requirements are not satisfied.
 *
 * @since 3.0.0
 */
function wc_store_credit_requirements_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	if ( is_woocommerce_active() ) {
		/* translators: %s: WooCommerce version */
		$message = sprintf( _x( '<strong>WooCommerce Store Credit</strong> requires WooCommerce %s or higher.', 'admin notice', 'woocommerce-store-credit' ), '3.4' );
	} else {
		$message = _x( '<strong>WooCommerce Store Credit</strong> requires WooCommerce to be activated to work.', 'admin notice', 'woocommerce-store-credit' );
	}

	if ( $message ) {
		printf( '<div class="error"><p>%s</p></div>', wp_kses_post( $message ) );
	}
}

// Define WC_STORE_CREDIT_FILE constant.
if ( ! defined( 'WC_STORE_CREDIT_FILE' ) ) {
	define( 'WC_STORE_CREDIT_FILE', __FILE__ );
}

// Include the main class of the plugin.
if ( ! class_exists( 'WC_Store_Credit' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-wc-store-credit.php';
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
