<?php
/**
 * Pre-Orders Compatibility
 *
 * @author   SomewhereWarm
 * @category Compatibility
 * @package  WooCommerce Mix and Match Products/Compatibility
 * @since    1.0.5
 * @version  1.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_PO_Compatibility Class.
 *
 * Adds compatibility with WooCommerce Pre-Orders.
 */
class WC_MNM_PO_Compatibility {

	public static function init() {

		// Pre-orders support
		add_filter( 'wc_pre_orders_cart_item_meta', array( __CLASS__, 'remove_bundled_pre_orders_cart_item_meta' ), 10, 2 );
		add_filter( 'wc_pre_orders_order_item_meta', array( __CLASS__, 'remove_bundled_pre_orders_order_item_meta' ), 10, 3 );
	}

	/**
	 * Remove child cart item meta "Available On" text.
	 *
	 * @param  array  $pre_order_meta
	 * @param  array  $cart_item_data
	 * @return array
	 */
	public static function remove_bundled_pre_orders_cart_item_meta( $pre_order_meta, $cart_item_data ) {

		if ( isset( $cart_item_data[ 'mnm_container' ] ) ) {
			$pre_order_meta = array();
		}

		return $pre_order_meta;
	}

	/**
	 * Remove child order item meta "Available On" text.
	 *
	 * @param  array    $pre_order_meta
	 * @param  array    $order_item
	 * @param  WC_Order $order
	 * @return array
	 */
	public static function remove_bundled_pre_orders_order_item_meta( $pre_order_meta, $order_item, $order ) {

		if ( isset( $order_item[ 'mnm_container' ] ) ) {
			$pre_order_meta = array();
		}

		return $pre_order_meta;
	}
}

WC_MNM_PO_Compatibility::init();
