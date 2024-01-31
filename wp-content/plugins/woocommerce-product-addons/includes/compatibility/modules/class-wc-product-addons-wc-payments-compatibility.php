<?php
/**
 * WC_Product_Addons_WC_Payments_Compatibility class
 *
 * @package  Woo Product Add-Ons
 * @since    6.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooPayments Compatibility.
 *
 * @version 6.6.0
 */
class WC_Product_Addons_WC_Payments_Compatibility {

	// Hide express checkout buttons in product pages with add-ons.
	public static function init() {
		add_filter( 'wcpay_payment_request_is_product_supported', array( __CLASS__, 'handle_express_checkout_buttons' ), 10, 2 );
		add_filter( 'wcpay_woopay_button_is_product_supported', array( __CLASS__, 'handle_express_checkout_buttons' ), 10, 2 );
	}

	/**
	 * Hide express checkout buttons in product pages with add-ons.
	 *
	 * @param  bool       $is_supported
	 * @param  WC_Product $product
	 * @return bool
	 */
	public static function handle_express_checkout_buttons( $is_supported, $product ) {
		$addons = WC_Product_Addons_Helper::get_product_addons( $product->get_id() );

		if ( ! is_array( $addons ) || ! empty( $addons ) ) {
			$is_supported = false;
		}

		return $is_supported;
	}
}

WC_Product_Addons_WC_Payments_Compatibility::init();
