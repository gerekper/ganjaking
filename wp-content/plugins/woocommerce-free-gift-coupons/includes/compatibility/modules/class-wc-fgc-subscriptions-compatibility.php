<?php
/**
 * Subscriptions Compatibility
 *
 * @package  WooCommerce Free Gift Coupons/Compatibility
 * @since    2.1.2
 * @version  3.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_FGC_Subscriptions_Compatibility Class.
 *
 * Adds compatibility with WooCommerce Cost of Goods.
 */
class WC_FGC_Subscriptions_Compatibility {

	public static function init() {

		// Add compatibility for Subscriptions.
		add_filter( 'woocommerce_subscriptions_validate_coupon_type', array( __CLASS__, 'ignore_free_gift' ), 10, 2 );

		// Stop removing free gift coupons.
		add_filter( 'wcs_bypass_coupon_removal', array( __CLASS__, 'bypass_coupon_removal' ), 10, 2 );

		// Save FGC gifts for subscriptions coupons.
		add_filter( 'wc_free_gift_coupon_types', array( __CLASS__, 'add_supported_coupon_types' ) );

	}

	/**
	 * Prevent Subscriptions validating free gift coupons
	 *
	 * @param bool $validate
	 * @param obj $coupon
	 * @return bool
	 * @since 2.1.2
	 */
	public static function ignore_free_gift( $validate, $coupon ) {

		if ( $coupon->is_type( 'free_gift' ) ) {
			$validate = false;
		}

		return $validate;
	}

	/**
	 * Bypass subscriptions removing coupon from cart.
	 *
	 * @param  bool     $bypass
	 * @param  obj 		$coupon WC_Coupon
	 * @return bool
	 */
	public static function bypass_coupon_removal( $bypass, $coupon ) {
		if ( $coupon->is_type( 'free_gift' ) ) {
			$bypass = true;
		}
		return $bypass;
	}


	/**
	 * Add supported coupon types.
	 *
	 * @param  $types array - The supported coupon types
	 * @return	array
	 */
	public static function add_supported_coupon_types( $types ) {
		if ( is_callable( array( 'WC_Subscriptions_Coupon', 'filter_product_coupon_types' ) ) ) {
			$types = WC_Subscriptions_Coupon::filter_product_coupon_types( $types );
		}
		return $types;
	}
}

WC_FGC_Subscriptions_Compatibility::init();
