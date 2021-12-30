<?php
/**
 * WC_PB_BS_Order class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.8.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Order-related functions and filters.
 *
 * @class    WC_PB_BS_Order
 * @version  5.8.0
 */
class WC_PB_BS_Order {

	/**
	 * Setup hooks.
	 */
	public static function init() {

		// Add bundle-sell meta to order items.
		add_action( 'woocommerce_checkout_create_order_line_item', array( __CLASS__, 'add_bundle_sell_order_item_meta' ), 10, 3 );
	}

	/*
	|--------------------------------------------------------------------------
	| Filter hooks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Add bundle-sell meta to order items.
	 *
	 * @param  WC_Order_Item  $order_item
	 * @param  string         $cart_item_key
	 * @param  array          $cart_item
	 * @return void
	 */
	public static function add_bundle_sell_order_item_meta( $order_item, $cart_item_key, $cart_item ) {

		if ( $bunde_sell_cart_items = wc_pb_get_bundle_sell_cart_items( $cart_item, false, true ) ) {
			$order_item->add_meta_data( '_bundle_sells', $bunde_sell_cart_items, true );
			$order_item->add_meta_data( '_bundle_sell_key', $cart_item_key, true );
		} elseif ( wc_pb_is_bundle_sell_cart_item( $cart_item ) ) {
			$order_item->add_meta_data( '_bundle_sell_of', $cart_item[ 'bundle_sell_of' ], true );
			$order_item->add_meta_data( '_bundle_sell_key', $cart_item_key, true );
		}
	}
}

WC_PB_BS_Order::init();
