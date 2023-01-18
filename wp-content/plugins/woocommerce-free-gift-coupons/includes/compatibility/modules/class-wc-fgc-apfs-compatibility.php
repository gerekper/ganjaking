<?php
/**
 * All Product For Subscriptions Compatibility
 *
 * @package  WooCommerce Free Gift Coupons/Compatibility
 * @since    3.2.0
 * @version  3.4.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_FGC_APFS_Compatibility Class.
 *
 * Adds compatibility with WooCommerce All Products for Subscriptions.
 */
class WC_FGC_APFS_Compatibility {

	public static function init() {

		// Do not allow free gifts to be in cart subscriptions.
		add_filter( 'wcsatt_cart_item_is_supported', array( __CLASS__, 'disable_cart_subscriptions' ), 10, 2 );

		// Disable radio options in cart for free gift.
		add_filter( 'wcsatt_product_supports_feature', array( __CLASS__, 'disable_cart_scheme_options' ), 10, 4 );

		
		

	}

	/**
	 * Prevent APFS from adding schemes to free gifts
	 *
	 * @param bool $is_supported
	 * @param array $cart_item
	 * @return bool
	 */
	public static function disable_cart_subscriptions( $is_supported, $cart_item ) {

		if ( isset( $cart_item[ 'free_gift' ] ) ) {
			$is_supported = false;
		}

		return $is_supported;
	}

	/**
	 * Prevent APFS from adding scheme options to free gifts in cart
	 *
	 * @param  bool        $is_feature_supported
	 * @param  WC_Product  $product
	 * @param  string      $feature
	 * @param  array       $args
	 * @return bool
	 */
	public static function disable_cart_scheme_options( $is_feature_supported, $product, $feature, $args ) {

		if ( 'subscription_scheme_options_product_cart' === $feature && isset( $args['cart_item'] ) ) {
			if ( isset( $args['cart_item'][ 'free_gift' ] ) ) {
				$is_feature_supported = false;
			}

		}
		
		return $is_feature_supported;
	}

}

WC_FGC_APFS_Compatibility::init();
