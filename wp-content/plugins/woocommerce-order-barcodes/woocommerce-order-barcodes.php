<?php
/**
 * Plugin Name: WooCommerce Order Barcodes
 * Version: 1.7.1
 * Plugin URI: https://woocommerce.com/products/woocommerce-order-barcodes/
 * Description: Generates unique barcodes for your orders - perfect for e-tickets, packing slips, reservations and a variety of other uses.
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Text Domain: woocommerce-order-barcodes
 * Requires at least: 4.0
 * Tested up to: 6.2
 * WC requires at least: 3.0
 * WC tested up to: 7.9
 * Woo: 391708:889835bb29ee3400923653e1e44a3779
 *
 * @package woocommerce-order-barcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WC_ORDER_BARCODES_VERSION', '1.7.1' ); // WRCS: DEFINED_VERSION.
define( 'WC_ORDER_BARCODES_FILE', __FILE__ );
define( 'WC_ORDER_BARCODES_DIR_PATH', untrailingslashit( plugin_dir_path( WC_ORDER_BARCODES_FILE ) ) );
define( 'WC_ORDER_BARCODES_DIR_URL', untrailingslashit( plugins_url( '/', WC_ORDER_BARCODES_FILE ) ) );

// Activation hook.
register_activation_hook( __FILE__, 'wc_order_barcodes_activate' );

/**
 * Activation function.
 */
function wc_order_barcodes_activate() {
	update_option( 'woocommerce_order_barcodes_version', WC_ORDER_BARCODES_VERSION );
}

// Plugin init hook.
add_action( 'plugins_loaded', 'wc_order_barcodes_init' );

/**
 * Initialize plugin.
 */
function wc_order_barcodes_init() {

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'wc_order_barcodes_woocommerce_deactivated' );
		return;
	}

	load_plugin_textdomain( 'woocommerce-order-barcodes', false, plugin_basename( WC_ORDER_BARCODES_DIR_PATH ) . '/languages' );

	// Autoload.
	require_once WC_ORDER_BARCODES_DIR_PATH . '/vendor/autoload.php';

	// Include order util trait class file.
	require_once WC_ORDER_BARCODES_DIR_PATH . '/includes/trait-woocommerce-order-util.php';

	// Include barcode generator files.
	require_once WC_ORDER_BARCODES_DIR_PATH . '/lib/barcode_generator/class-woocommerce-order-barcodes-generator-tclib.php';

	// Include plugin class files.
	require_once WC_ORDER_BARCODES_DIR_PATH . '/includes/class-woocommerce-order-barcodes.php';
	require_once WC_ORDER_BARCODES_DIR_PATH . '/includes/class-woocommerce-order-barcodes-settings.php';

	// Include plugin functions file.
	require_once WC_ORDER_BARCODES_DIR_PATH . '/includes/woocommerce-order-barcodes-functions.php';

	if ( is_admin() ) {
		require_once WC_ORDER_BARCODES_DIR_PATH . '/includes/class-woocommerce-order-barcodes-privacy.php';
	}

	// Initialise plugin.
	WC_Order_Barcodes();
}

/**
 * WooCommerce Deactivated Notice.
 */
function wc_order_barcodes_woocommerce_deactivated() {
	/* translators: %s: WooCommerce link */
	echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Order Barcodes requires %s to be installed and active.', 'woocommerce-order-barcodes' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
}
