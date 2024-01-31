<?php
/**
 * WC_Product_Addons_Stripe_Compatibility class
 *
 * @package  Woo Product Add-Ons
 * @since    6.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stripe Compatibility.
 *
 * @version 6.6.0
 */
class WC_Product_Addons_Stripe_Compatibility {

	// Hide express checkout buttons in product pages with add-ons.
	public static function init() {
		add_filter( 'wc_stripe_hide_payment_request_on_product_page', array( __CLASS__, 'handle_express_checkout_buttons' ), 10, 2 );
	}

	/**
	 * Hide express checkout buttons in product pages with add-ons.
	 *
	 * @param  bool       $hide_button
	 * @param  WP_Post    $post
	 * @return bool
	 */
	public static function handle_express_checkout_buttons( $hide_button, $post ) {

		global $product;

		$the_product = $product && is_a( $product, 'WC_Product' ) ? $product : wc_get_product( $post->ID );

		if ( ! $the_product ) {
			return $hide_button;
		}

		$addons = WC_Product_Addons_Helper::get_product_addons( $the_product->get_id() );

		if ( ! is_array( $addons ) || ! empty( $addons ) ) {
			$hide_button = true;
		}

		return $hide_button;
	}
}

WC_Product_Addons_Stripe_Compatibility::init();
