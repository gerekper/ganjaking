<?php
/**
 * WC_PB_Stripe_Compatibility class
 *
 * @package  WooCommerce Product Bundles
 * @since    6.6.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stripe Compatibility.
 *
 * @version  6.6.4
 */
class WC_PB_Stripe_Compatibility {

	public static function init() {
		add_filter( 'wc_stripe_hide_payment_request_on_product_page', array( __CLASS__, 'hide_stripe_quickpay' ), 10, 2 );
	}

	/**
	 * Hide Stripe Quick-pay buttons for non-static Bundles.
	 *
	 * @since 6.6.1
	 *
	 */
	public static function hide_stripe_quickpay( $hide_button, $post ) {

		global $product;

		$the_product = $product && is_a( $product, 'WC_Product' ) ? $product : wc_get_product( $post->ID );

		if ( ! $the_product ) {
			return $hide_button;
		}

		if ( $the_product->is_type( 'bundle' ) ) {
			if ( $the_product->contains( 'priced_individually' ) || $the_product->contains( 'configurable_quantities' ) || $the_product->requires_input() ) {
				$hide_button = true;
			}
		} else {
			$bundle_sells = WC_PB_BS_Product::get_bundle_sell_ids( $the_product );
			if ( ! empty( $bundle_sells ) ) {
				$hide_button = true;
			}
		}

		return $hide_button;
	}
}

WC_PB_Stripe_Compatibility::init();
