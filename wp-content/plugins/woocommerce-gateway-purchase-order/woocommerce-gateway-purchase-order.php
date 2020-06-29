<?php
/**
 * Plugin Name: WooCommerce Purchase Order Payment Gateway
 * Plugin URI: https://woocommerce.com/products/woocommerce-gateway-purchase-order/
 * Description: Receive payments via purchase order with Woocommerce.
 * Version: 1.2.10
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Requires at least: 4.1.0
 * Tested up to: 5.3
 *
 * Text Domain: woocommerce-gateway-purchase-order
 * Domain Path: /languages/
 * Woo: 478542:573a92318244ece5facb449d63e74874
 * WC tested up to: 4.2
 * WC requires at least: 2.6
 *
 * Originally developed, and sold to WooCommerce in it's original state, by Viren Bohra ( http://enticesolution.com/ ).
 *
 * @package woocommerce-gateway-purchase-order
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

