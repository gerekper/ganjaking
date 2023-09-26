<?php
/**
 * WCS_ATT_Intgeration_WC_Payments class
 *
 * @package  WooCommerce All Products For Subscriptions
 * @since    3.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Payments Integration.
 *
 * @version  3.2.0
 */
class WCS_ATT_Intgeration_WC_Payments {

	// Hide quick-pay buttons in product pages with Subscription plans.
	public static function init() {
		add_filter( 'wcpay_payment_request_is_product_supported', array( __CLASS__, 'handle_quick_pay_buttons' ), 10, 2 );
	}

	/**
	 * Hide quick-pay buttons in product pages with Subscription plans.
	 *
	 * @param  bool       $is_supported
	 * @param  WC_Product $product
	 * @return bool
	 */
	public static function handle_quick_pay_buttons( $is_supported, $product ) {

		if ( WCS_ATT_Product_Schemes::has_subscription_schemes( $product ) ) {
			$is_supported = false;
		}

		return $is_supported;
	}
}

WCS_ATT_Intgeration_WC_Payments::init();
