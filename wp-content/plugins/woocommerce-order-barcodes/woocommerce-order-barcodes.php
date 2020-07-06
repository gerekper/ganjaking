<?php
/**
 * Plugin Name: WooCommerce Order Barcodes
 * Version: 1.3.19
 * Plugin URI: https://woocommerce.com/products/woocommerce-order-barcodes/
 * Description: Generates unique barcodes for your orders - perfect for e-tickets, packing slips, reservations and a variety of other uses.
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Requires at least: 4.0
 * Tested up to: 5.3
 * WC requires at least: 2.6
 * WC tested up to: 4.2
 * Woo: 391708:889835bb29ee3400923653e1e44a3779
 *
 * @package woocommerce-order-barcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WC_ORDER_BARCODES_VERSION', '1.3.19' ); // WRCS: DEFINED_VERSION.
define( 'WC_ORDER_BARCODES_FILE', __FILE__ );

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

	load_plugin_textdomain( 'woocommerce-order-barcodes', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	// Autoload.
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';

	// Include plugin class files.
	require_once dirname( __FILE__ ) . '/includes/class-woocommerce-order-barcodes.php';
	require_once dirname( __FILE__ ) . '/includes/class-woocommerce-order-barcodes-settings.php';

	// Include plugin functions file.
	require_once dirname( __FILE__ ) . '/includes/woocommerce-order-barcodes-functions.php';

	if ( is_admin() ) {
		require_once dirname( __FILE__ ) . '/includes/class-woocommerce-order-barcodes-privacy.php';
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
