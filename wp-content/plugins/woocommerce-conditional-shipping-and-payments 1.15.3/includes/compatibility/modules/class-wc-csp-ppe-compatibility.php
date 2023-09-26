<?php
/**
 * WC_CSP_PPE_Compatibility class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PayPal Express Compatibility.
 *
 * @version  1.8.9
 */
class WC_CSP_PPE_Compatibility {

	private static $wc_ppe;

	/**
	 * Initialization.
	 */
	public static function init() {

		self::$wc_ppe = wc_gateway_ppec();

		// Remove buttons from (mini) cart.
		add_action( 'woocommerce_proceed_to_checkout', array( __CLASS__, 'maybe_remove_paypal_cart_button' ) );
		add_action( 'woocommerce_after_mini_cart', array( __CLASS__, 'maybe_remove_paypal_mini_cart_button' ) );
		add_action( 'woocommerce_widget_shopping_cart_buttons', array( __CLASS__, 'maybe_remove_paypal_mini_cart_button' ) );

		// Remove buttons from product page.
		add_action( 'woocommerce_after_add_to_cart_form', array( __CLASS__, 'maybe_remove_paypal_product_button' ), 0 );
	}

	/**
	 * Remove buttons from cart.
	 */
	public static function maybe_remove_paypal_product_button() {
		if ( WC_CSP_Compatibility::is_gateway_restricted( 'ppec_paypal' ) ) {
			remove_action( 'woocommerce_after_add_to_cart_form', array( self::$wc_ppe->cart, 'display_paypal_button_product' ), 1 );
		}
	}

	/**
	 * Remove buttons from mini-cart.
	 */
	public static function maybe_remove_paypal_mini_cart_button() {
		if ( WC_CSP_Compatibility::is_gateway_restricted( 'ppec_paypal' ) ) {
			remove_action( 'woocommerce_after_mini_cart', array( self::$wc_ppe->cart, 'display_mini_paypal_button' ), 20 );
			remove_action( 'woocommerce_widget_shopping_cart_buttons', array( self::$wc_ppe->cart, 'display_mini_paypal_button' ), 20 );
		}
	}

	/**
	 * Remove buttons from single product pages.
	 */
	public static function maybe_remove_paypal_cart_button() {
		if ( WC_CSP_Compatibility::is_gateway_restricted( 'ppec_paypal' ) ) {
			remove_action( 'woocommerce_proceed_to_checkout', array( self::$wc_ppe->cart, 'display_paypal_button' ), 20 );
		}
	}

	/**
	 * Remove buttons from checkout review.
	 *
	 * Used to be necessary with older versions of WC PPE:
	 *
	 * add_action( 'woocommerce_review_order_after_submit', array( __CLASS__, 'remove_paypal_checkout_button' ), 20 );
	 */
	public static function remove_paypal_checkout_button() {
		if ( WC_CSP_Compatibility::is_gateway_restricted( 'ppec_paypal' ) ) {
			wp_dequeue_script( 'wc-gateway-ppec-smart-payment-buttons' );
		}
	}
}

WC_CSP_PPE_Compatibility::init();
