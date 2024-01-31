<?php
/**
 * WC_Product_Addons_PayPal_Compatibility class
 *
 * @package  Woo Product Add-Ons
 * @since    6.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PayPal Compatibility.
 *
 * @version 6.6.0
 */
class WC_Product_Addons_PayPal_Compatibility {

	// Hide smart buttons in product pages with add-ons.
	public static function init() {
		add_filter( 'woocommerce_paypal_payments_product_supports_payment_request_button', array( __CLASS__, 'handle_smart_buttons' ), 10, 2 );
	}

	/**
	 * Hide smart buttons in product pages with add-ons.
	 *
	 * @param  bool       $is_supported
	 * @param  WC_Product $product
	 * @return bool
	 */
	public static function handle_smart_buttons( $is_supported, $product ) {
		$addons = WC_Product_Addons_Helper::get_product_addons( $product->get_id() );

		if ( ! is_array( $addons ) || ! empty( $addons ) ) {
			$is_supported = false;
		}

		return $is_supported;
	}
}

WC_Product_Addons_PayPal_Compatibility::init();
