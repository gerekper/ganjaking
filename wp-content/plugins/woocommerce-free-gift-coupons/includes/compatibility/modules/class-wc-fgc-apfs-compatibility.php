<?php
/**
 * All Product For Subscriptions Compatibility
 *
 * @package  WooCommerce Free Gift Coupons/Compatibility
 * @since    3.2.0
 * @version  3.2.0
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

}

WC_FGC_APFS_Compatibility::init();
