<?php
/**
 * Plugin Name: WooCommerce Purchase Order Payment Gateway
 * Plugin URI: https://woocommerce.com/products/woocommerce-gateway-purchase-order/
 * Description: Receive payments via purchase order with Woocommerce.
 * Version: 1.4.6
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Requires at least: 6.2
 * Tested up to: 6.4
 * WC requires at least: 8.2
 * WC tested up to: 8.4
 * Requires PHP: 7.4
 * PHP tested up to: 8.3
 * Text Domain: woocommerce-gateway-purchase-order
 * Domain Path: /languages/
 * Woo: 478542:573a92318244ece5facb449d63e74874
 *
 * Originally developed, and sold to WooCommerce in it's original state, by Viren Bohra ( http://enticesolution.com/ ).
 *
 * @package woocommerce-gateway-purchase-order
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WC_GATEWAY_PURCHASE_ORDER_VERSION', '1.4.6' ); // WRCS: DEFINED_VERSION.
define( 'WC_GATEWAY_PURCHASE_ORDER_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );
define( 'WC_GATEWAY_PURCHASE_ORDER_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

/**
 * Initialise the payment gateway.
 *
 * @since  1.0.0
 * @return void
 */
function woocommerce_gateway_purchase_order_init() {
	// If we don't have access to the WC_Payment_Gateway class, get out.
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	add_filter( 'woocommerce_payment_gateways', 'woocommerce_gateway_purchase_order_register_gateway' );

	// Localisation.
	load_plugin_textdomain( 'woocommerce-gateway-purchase-order', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	require_once __DIR__ . '/includes/class-woocommerce-gateway-purchase-order-privacy.php';

	// Additional admin screen logic.
	require_once __DIR__ . '/includes/class-woocommerce-gateway-purchase-order-admin.php';
	Woocommerce_Gateway_Purchase_Order_Admin();
}
add_action( 'plugins_loaded', 'woocommerce_gateway_purchase_order_init' );

/**
 * Register this payment gateway within WooCommerce.
 *
 * @since  1.0.0
 * @param  array $methods The array of registered payment gateways.
 * @return array          The modified array of registered payment gateways.
 */
function woocommerce_gateway_purchase_order_register_gateway( $methods ) {
	require_once __DIR__ . '/includes/class-woocommerce-gateway-purchase-order.php';

	$methods[] = 'Woocommerce_Gateway_Purchase_Order';
	return $methods;
}

/**
 * Declares compatibility with Woocommerce features.
 *
 * List of features:
 * - custom_order_tables
 * - product_block_editor
 *
 * @since 1.4.6 Rename function
 */
function woocommerce_gateway_purchase_order_declare_woocommerce_feature_compatibility(): void {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
			'custom_order_tables',
			__FILE__
		);

		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
			'product_block_editor',
			__FILE__
		);
	}
}
add_action( 'before_woocommerce_init', 'woocommerce_gateway_purchase_order_declare_woocommerce_feature_compatibility' );


/**
 * Add Purchase Order Payment Gateway to WooCommerce Blocks.
 *
 * @return void
 */
function woocommerce_gateway_purchase_order_block_support() {
	if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
		require_once __DIR__ . '/includes/class-woocommerce-gateway-purchase-order-blocks-support.php';
		add_action(
			'woocommerce_blocks_payment_method_type_registration',
			function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
				$payment_method_registry->register( new Woocommerce_Gateway_Purchase_Order_Blocks_Support() );
			}
		);
	}
}

// Add support for WooCommerce Blocks.
add_action( 'woocommerce_blocks_loaded', 'woocommerce_gateway_purchase_order_block_support' );
