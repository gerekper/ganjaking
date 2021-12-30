<?php
/**
 * WC_PB_PO_Compatibility class
 *
 * @package  WooCommerce Product Bundles
 * @since    4.11.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pre Orders Compatibility.
 *
 * @since  4.11.4
 */
class WC_PB_PO_Compatibility {

	public static function init() {

		// Pre-orders support.
		add_filter( 'wc_pre_orders_cart_item_meta', array( __CLASS__, 'remove_bundled_pre_orders_cart_item_meta' ), 10, 2 );
		add_filter( 'wc_pre_orders_order_item_meta', array( __CLASS__, 'remove_bundled_pre_orders_order_item_meta' ), 10, 3 );
	}

	/**
	 * Remove bundled cart item meta "Available On" text.
	 *
	 * @param  array  $pre_order_meta
	 * @param  array  $cart_item_data
	 * @return array
	 */
	public static function remove_bundled_pre_orders_cart_item_meta( $pre_order_meta, $cart_item_data ) {
		if ( wc_pb_is_bundled_cart_item( $cart_item_data ) ) {
			$pre_order_meta = array();
		}
		return $pre_order_meta;
	}

	/**
	 * Remove bundled order item meta "Available On" text.
	 *
	 * @param  array     $pre_order_meta
	 * @param  array     $order_item
	 * @param  WC_Order  $order
	 * @return array
	 */
	public static function remove_bundled_pre_orders_order_item_meta( $pre_order_meta, $order_item, $order ) {
		if ( wc_pb_maybe_is_bundled_order_item( $order_item, $order ) ) {
			$pre_order_meta = array();
		}
		return $pre_order_meta;
	}
}

WC_PB_PO_Compatibility::init();
