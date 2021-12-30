<?php
/**
 * WC_PB_WC_Payments_Compatibility class
 *
 * @package  WooCommerce Product Bundles
 * @since    6.13.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Payments Integration.
 *
 * @version  6.13.1
 */
class WC_PB_WC_Payments_Compatibility {

	// Hide quick-pay buttons in Bundle product pages.
	public static function init() {
		add_filter( 'wcpay_payment_request_is_product_supported', array( __CLASS__, 'handle_quick_pay_buttons' ), 10, 2 );
	}

	/**
	 * Hide quick-pay buttons in Bundle product pages.
	 *
	 * @param  bool       $is_supported
	 * @param  WC_Product $product
	 * @return bool
	 */
	public static function handle_quick_pay_buttons( $is_supported, $product ) {

		if ( $product->is_type( 'bundle' ) ) {
			$is_supported = false;
		}

		return $is_supported;
	}
}

WC_PB_WC_Payments_Compatibility::init();
