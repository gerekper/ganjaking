<?php
/**
 * Glocal-scope Bundle-Sell functions
 *
 * @package  WooCommerce Product Bundles
 * @since    5.8.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
|--------------------------------------------------------------------------
| Cart functions.
|--------------------------------------------------------------------------
*/

/**
 * True if a cart item is a bundle-sell.
 * Instead of relying solely on cart item data, the function also checks that the alleged parent item actually exists.
 *
 * @since  5.8.0
 *
 * @param  array  $cart_item
 * @param  array  $cart_contents
 * @return boolean
 */
function wc_pb_is_bundle_sell_cart_item( $cart_item, $cart_contents = false ) {

	$is_bundle_sell = false;

	if ( wc_pb_get_bundle_sell_cart_item_container( $cart_item, $cart_contents ) ) {
		$is_bundle_sell = true;
	}

	return $is_bundle_sell;
}

/**
 * Given a bundle-sell cart item, find and return its parent cart item.
 * Returns the cart key of its parent cart item when the $return_id arg is true.
 *
 * @since  5.8.0
 *
 * @param  array    $cart_item
 * @param  array    $cart_contents
 * @param  boolean  $return_id
 * @return mixed
 */
function wc_pb_get_bundle_sell_cart_item_container( $cart_item, $cart_contents = false, $return_id = false ) {

	if ( ! $cart_contents ) {
		$cart_contents = WC()->cart->cart_contents;
	}

	$container = false;

	if ( isset( $cart_item[ 'bundle_sell_of' ] ) ) {

		$bundled_sell_of = $cart_item[ 'bundle_sell_of' ];

		if ( isset( $cart_contents[ $bundled_sell_of ] ) ) {
			$container = $return_id ? $bundled_sell_of : $cart_contents[ $bundled_sell_of ];
		}
	}

	return $container;
}

/**
 * Given a bundle-sells parent cart item, find and return its child cart items -- or their cart ids when the $return_ids arg is true.
 *
 * @since  5.8.0
 *
 * @param  array    $cart_item
 * @param  array    $cart_contents
 * @param  boolean  $return_ids
 * @return mixed
 */
function wc_pb_get_bundle_sell_cart_items( $cart_item, $cart_contents = false, $return_ids = false ) {

	if ( ! $cart_contents ) {
		$cart_contents = WC()->cart->cart_contents;
	}

	$bundle_sell_cart_items = array();

	if ( isset( $cart_item[ 'bundle_sells' ] ) ) {

		$maybe_bundle_sell_cart_items = $cart_item[ 'bundle_sells' ];

		if ( ! empty( $maybe_bundle_sell_cart_items ) && is_array( $maybe_bundle_sell_cart_items ) ) {
			foreach ( $maybe_bundle_sell_cart_items as $maybe_bundle_sell_cart_item_key ) {
				if ( isset( $cart_contents[ $maybe_bundle_sell_cart_item_key ] ) ) {
					$bundle_sell_cart_items[ $maybe_bundle_sell_cart_item_key ] = $cart_contents[ $maybe_bundle_sell_cart_item_key ];
				}
			}
		}
	}

	return $return_ids ? array_keys( $bundle_sell_cart_items ) : $bundle_sell_cart_items;
}
