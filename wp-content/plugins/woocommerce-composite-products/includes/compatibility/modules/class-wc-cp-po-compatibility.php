<?php
/**
 * WC_CP_PO_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pre Orders compatibility.
 *
 * @version  3.7.0
 */
class WC_CP_PO_Compatibility {

	public static function init() {

		add_filter( 'wc_pre_orders_cart_item_meta', array( __CLASS__, 'remove_composite_pre_orders_cart_item_meta' ), 10, 2 );
		add_filter( 'wc_pre_orders_order_item_meta', array( __CLASS__, 'remove_composite_pre_orders_order_item_meta' ), 10, 3 );
	}

	/**
	 * Remove composited cart item meta "Available On" text.
	 *
	 * @param  array  $pre_order_meta
	 * @param  array  $cart_item_data
	 * @return array
	 */
	public static function remove_composite_pre_orders_cart_item_meta( $pre_order_meta, $cart_item_data ) {

		if ( wc_cp_is_composited_cart_item( $cart_item_data ) ) {
			$pre_order_meta = array();
		}

		return $pre_order_meta;
	}

	/**
	 * Remove composited order item meta "Available On" text.
	 *
	 * @param  array     $pre_order_meta
	 * @param  array     $order_item
	 * @param  WC_Order  $order
	 * @return array
	 */
	public static function remove_composite_pre_orders_order_item_meta( $pre_order_meta, $order_item, $order ) {

		if ( wc_cp_maybe_is_composited_order_item( $order_item, $order ) ) {
			$pre_order_meta = array();
		}

		return $pre_order_meta;
	}
}

WC_CP_PO_Compatibility::init();
