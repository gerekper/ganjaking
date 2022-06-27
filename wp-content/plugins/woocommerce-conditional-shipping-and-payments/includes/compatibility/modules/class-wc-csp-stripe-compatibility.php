<?php
/**
 * WC_CSP_Stripe_Compatibility class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stripe Compatibility.
 *
 * @since  1.4.0
 */
class WC_CSP_Stripe_Compatibility {

	public static function init() {
		// Hide payment request buttons.
		add_filter( 'wc_stripe_hide_payment_request_on_product_page', array( __CLASS__, 'hide_payment_request' ) );
		add_filter( 'wc_stripe_show_payment_request_on_checkout', array( __CLASS__, 'hide_payment_request' ) );
	}

	/**
	 * Hide payment request buttons.
	 *
	 * @param  boolean  $hide
	 * @return boolean
	 */
	public static function hide_payment_request( $hide ) {

		if ( WC_CSP_Compatibility::is_gateway_restricted( 'stripe' ) ) {
			$hide = true;
		}

		return $hide;
	}
}

WC_CSP_Stripe_Compatibility::init();
