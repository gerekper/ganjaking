<?php
/**
 * Plugin Name: WooCommerce Pre-Orders
 * Plugin URI: https://woocommerce.com/products/woocommerce-pre-orders/
 * Description: Sell pre-orders for products in your WooCommerce store.
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Version: 1.7.0
 * Text Domain: wc-pre-orders
 * Domain Path: /languages/
 * Tested up to: 5.9
 * WC tested up to: 6.2
 * WC requires at least: 2.6
 *
 * Copyright: © 2022 WooCommerce
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * See https://docs.woocommerce.com/document/pre-orders/ for full documentation.
 *
 * Woo: 178477:b2dc75e7d55e6f5bbfaccb59830f66b7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce fallback notice.
 *
 * @since 1.5.25
 */
function woocommerce_pre_orders_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Pre Orders requires WooCommerce to be installed and active. You can download %s here.', 'wc-pre-orders' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

// When plugin is activated.
register_activation_hook( __FILE__, 'woocommerce_pre_orders_activate' );

/**
 * Actions to perform when plugin is activated.
 *
 * @since 1.4.7
 */
function woocommerce_pre_orders_activate() {
	add_rewrite_endpoint( 'pre-orders', EP_ROOT | EP_PAGES );
	flush_rewrite_rules();
}

if ( ! class_exists( 'WC_Pre_Orders' ) ) :
	define( 'WC_PRE_ORDERS_VERSION', '1.7.0' ); // WRCS: DEFINED_VERSION.
	define( 'WC_PRE_ORDERS_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
	define( 'WC_PRE_ORDERS_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ), basename( __FILE__ ) ) ) ) );
	define( 'WC_PRE_ORDERS_GUTENBERG_EXISTS', function_exists( 'register_block_type' ) ? true : false );
	require 'includes/class-wc-pre-orders.php';
endif;

add_action( 'plugins_loaded', 'woocommerce_pre_orders_init' );

/**
 * Initializes the extension.
 *
 * @return Object Instance of the extension.
 * @since 1.5.25
 */
function woocommerce_pre_orders_init() {
	load_plugin_textdomain( 'wc-pre-orders', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_pre_orders_missing_wc_notice' );

		return;
	}

	$GLOBALS['wc_pre_orders'] = new WC_Pre_Orders( __FILE__ );
}

add_action( 'plugins_loaded', 'woocommerce_pre_orders_init' );

/**
 * Loads the classes for the integration with WooCommerce Blocks.
 */
function load_block_classes() {
	\WC_Pre_Orders::load_block_classes();
}

if ( class_exists( 'Automattic\WooCommerce\Blocks\Package' ) && version_compare( \Automattic\WooCommerce\Blocks\Package::get_version(), '6.5.0', '>' ) ) {
	add_action( 'woocommerce_blocks_loaded', 'load_block_classes' );
}

