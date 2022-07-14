<?php
/**
 * Product Bundles global functions
 *
 * @package  WooCommerce Product Bundles
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
|--------------------------------------------------------------------------
| Products.
|--------------------------------------------------------------------------
*/

/**
 * Create a WC_Bundled_Item instance.
 *
 * @since  5.0.0
 *
 * @param  mixed  $item
 * @param  mixed  $parent
 * @return mixed
 */
function wc_pb_get_bundled_item( $item, $parent = false ) {

	$data = null;

	if ( is_numeric( $item ) ) {
		$data = WC_PB_DB::get_bundled_item( absint( $item ) );
	} elseif ( $item instanceof WC_Bundled_Item_Data ) {
		$data = $item;
	}

	if ( ! is_null( $data ) ) {
		$bundled_item = new WC_Bundled_Item( $data, $parent );

		if ( $bundled_item->exists() ) {
			return $bundled_item;
		}
	}

	return false;
}

/**
 * Get a map of the bundled item DB IDs and product bundle post IDs associated with a (bundled) product.
 *
 * @since  5.0.0
 *
 * @param  mixed    $product
 * @param  boolean  $allow_cache
 * @return array
 */
function wc_pb_get_bundled_product_map( $product, $allow_cache = true ) {

	if ( is_object( $product ) ) {
		$product_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
	} else {
		$product_id = absint( $product );
	}

	$use_cache = $allow_cache && ! defined( 'WC_PB_DEBUG_TRANSIENTS' ) && ! defined( 'WC_PB_UPDATING' );

	$transient_name             = 'wc_bundled_product_data';
	$transient_version          = WC_Cache_Helper::get_transient_version( 'product' );
	$bundled_product_data_array = $use_cache ? get_transient( $transient_name ) : false;
	$bundled_product_data       = false;

	if ( $use_cache && is_array( $bundled_product_data_array ) && isset( $bundled_product_data_array[ $product_id ] ) && is_array( $bundled_product_data_array[ $product_id ] ) && isset( $bundled_product_data_array[ $product_id ][ 'bundle_ids' ] ) && is_array( $bundled_product_data_array[ $product_id ][ 'bundle_ids' ] ) ) {
		if ( isset( $bundled_product_data_array[ $product_id ][ 'version' ] ) && $transient_version === $bundled_product_data_array[ $product_id ][ 'version' ] ) {
			$bundled_product_data = $bundled_product_data_array[ $product_id ][ 'bundle_ids' ];
		}
	}

	if ( false === $bundled_product_data ) {

		$args = array(
			'product_id' => $product_id,
			'return'     => 'id=>bundle_id'
		);

		$bundled_product_data = WC_PB_DB::query_bundled_items( $args );

		if ( is_array( $bundled_product_data_array ) ) {

			$bundled_product_data_array[ $product_id ] = array(
				'bundle_ids' => $bundled_product_data,
				'version'    => $transient_version
			);

		} else {

			$bundled_product_data_array = array(
				$product_id => array(
					'bundle_ids' => $bundled_product_data,
					'version'    => $transient_version
				)
			);
		}

		if ( ! defined( 'WC_PB_UPDATING' ) ) {

			// Delete expired entries.
			if ( ! empty( $bundled_product_data_array ) ) {
				foreach ( $bundled_product_data_array as $product_id_key => $data ) {
					if ( ! isset( $data[ 'version' ] ) || $transient_version !== $data[ 'version' ] ) {
						unset( $bundled_product_data_array[ $product_id_key ] );
					}
				}
			}

			set_transient( $transient_name, $bundled_product_data_array, DAY_IN_SECONDS * 30 );
		}
	}

	return $bundled_product_data;
}

/*
|--------------------------------------------------------------------------
| Cart.
|--------------------------------------------------------------------------
*/

/**
 * Given a bundled cart item, find and return its container cart item - the Bundle - or its cart id when the $return_id arg is true.
 *
 * @since  5.0.0
 *
 * @param  array    $bundled_cart_item
 * @param  array    $cart_contents
 * @param  boolean  $return_id
 * @return mixed
 */
function wc_pb_get_bundled_cart_item_container( $bundled_cart_item, $cart_contents = false, $return_id = false ) {

	if ( ! $cart_contents ) {
		$cart_contents = isset( WC()->cart ) ? WC()->cart->cart_contents : array();
	}

	$container = false;

	if ( wc_pb_maybe_is_bundled_cart_item( $bundled_cart_item ) ) {

		$bundled_by = $bundled_cart_item[ 'bundled_by' ];

		if ( isset( $cart_contents[ $bundled_by ] ) ) {
			$container = $return_id ? $bundled_by : $cart_contents[ $bundled_by ];
		}
	}

	return $container;
}

/**
 * Given a bundle container cart item, find and return its child cart items - or their cart ids when the $return_ids arg is true.
 *
 * @since  5.0.0
 *
 * @param  array    $container_cart_item
 * @param  array    $cart_contents
 * @param  boolean  $return_ids
 * @return mixed
 */
function wc_pb_get_bundled_cart_items( $container_cart_item, $cart_contents = false, $return_ids = false ) {

	if ( ! $cart_contents ) {
		$cart_contents = isset( WC()->cart ) ? WC()->cart->cart_contents : array();
	}

	$bundled_cart_items = array();

	if ( wc_pb_is_bundle_container_cart_item( $container_cart_item ) ) {

		$bundled_items = $container_cart_item[ 'bundled_items' ];

		if ( ! empty( $bundled_items ) && is_array( $bundled_items ) ) {
			foreach ( $bundled_items as $bundled_cart_item_key ) {
				if ( isset( $cart_contents[ $bundled_cart_item_key ] ) ) {
					$bundled_cart_items[ $bundled_cart_item_key ] = $cart_contents[ $bundled_cart_item_key ];
				}
			}
		}
	}

	return $return_ids ? array_keys( $bundled_cart_items ) : $bundled_cart_items;
}

/**
 * True if a cart item is part of a bundle.
 * Instead of relying solely on cart item data, the function also checks that the alleged parent item actually exists.
 *
 * @since  5.0.0
 *
 * @param  array  $cart_item
 * @param  array  $cart_contents
 * @return boolean
 */
function wc_pb_is_bundled_cart_item( $cart_item, $cart_contents = false ) {

	$is_bundled = false;

	if ( wc_pb_get_bundled_cart_item_container( $cart_item, $cart_contents ) ) {
		$is_bundled = true;
	}

	return $is_bundled;
}

/**
 * True if a cart item appears to be part of a bundle.
 * The result is purely based on cart item data - the function does not check that a valid parent item actually exists.
 *
 * @since  5.0.0
 *
 * @param  array  $cart_item
 * @return boolean
 */
function wc_pb_maybe_is_bundled_cart_item( $cart_item ) {

	$is_bundled = false;

	if ( ! empty( $cart_item[ 'bundled_by' ] ) && ! empty( $cart_item[ 'bundled_item_id' ] ) && ! empty( $cart_item[ 'stamp' ] ) ) {
		$is_bundled = true;
	}

	return $is_bundled;
}

/**
 * True if a cart item appears to be a bundle container item.
 *
 * @since  5.0.0
 *
 * @param  array  $cart_item
 * @return boolean
 */
function wc_pb_is_bundle_container_cart_item( $cart_item ) {

	$is_bundle = false;

	if ( isset( $cart_item[ 'bundled_items' ] ) && ! empty( $cart_item[ 'stamp' ] ) ) {
		$is_bundle = true;
	}

	return $is_bundle;
}

/*
|--------------------------------------------------------------------------
| Orders.
|--------------------------------------------------------------------------
*/

/**
 * Given a bundled order item, find and return its container order item - the Bundle - or its order item id when the $return_id arg is true.
 *
 * @since  5.0.0
 *
 * @param  WC_Order_Item  $bundled_order_item
 * @param  WC_Order       $order
 * @param  boolean        $return_id
 * @return mixed
 */
function wc_pb_get_bundled_order_item_container( $bundled_order_item, $order = false, $return_id = false ) {

	$result = false;

	if ( wc_pb_maybe_is_bundled_order_item( $bundled_order_item ) ) {

		$container = WC_PB_Helpers::cache_get( 'order_item_container_' . $bundled_order_item->get_id() );

		if ( null === $container ) {

			if ( false === $order ) {
				if ( is_callable( array( $bundled_order_item, 'get_order' ) ) ) {

					$order_id = $bundled_order_item->get_order_id();
					$order    = WC_PB_Helpers::cache_get( 'order_' . $order_id );

					if ( null === $order ) {
						$order = $bundled_order_item->get_order();
						WC_PB_Helpers::cache_set( 'order_' . $order_id, $order );
					}

				} else {
					$msg = 'get_order() is not callable on the supplied $order_item. No $order object given.';
					_doing_it_wrong( __FUNCTION__ . '()', $msg, '5.3.0' );
				}
			}

			$order_items = is_object( $order ) ? $order->get_items( 'line_item' ) : $order;

			if ( ! empty( $order_items ) ) {
				foreach ( $order_items as $order_item_id => $order_item ) {

					$is_container = false;

					if ( isset( $order_item[ 'bundle_cart_key' ] ) ) {
						$is_container = $bundled_order_item[ 'bundled_by' ] === $order_item[ 'bundle_cart_key' ];
					} else {
						$is_container = isset( $order_item[ 'stamp' ] ) && $order_item[ 'stamp' ] === $bundled_order_item[ 'stamp' ] && ! isset( $order_item[ 'bundled_by' ] );
					}

					if ( $is_container ) {
						WC_PB_Helpers::cache_set( 'order_item_container_' . $bundled_order_item->get_id(), $order_item );
						$container = $order_item;
						break;
					}
				}
			}
		}

		if ( $container && is_callable( array( $container, 'get_id' ) ) ) {
			$result = $return_id ? $container->get_id() : $container;
		}
	} else {

		// Invalidate order cache before moving to the next Composite Product in the order.
		if ( is_callable( array( $bundled_order_item, 'get_order_id' ) ) ) {
			WC_PB_Helpers::cache_delete( 'order_' . $bundled_order_item->get_order_id() );
		}

	}

	return $result;
}

/**
 * Given a bundle container order item, find and return its child order items - or their order item ids when the $return_ids arg is true.
 *
 * @since  5.0.0
 *
 * @param  WC_Order_Item  $container_order_item
 * @param  WC_Order       $order
 * @param  boolean        $return_ids
 * @return mixed
 */
function wc_pb_get_bundled_order_items( $container_order_item, $order = false, $return_ids = false ) {

	$bundled_order_items = array();

	if ( wc_pb_is_bundle_container_order_item( $container_order_item ) ) {

		$bundled_cart_keys = maybe_unserialize( $container_order_item[ 'bundled_items' ] );

		if ( ! empty( $bundled_cart_keys ) && is_array( $bundled_cart_keys ) ) {

			if ( false === $order ) {
				if ( is_callable( array( $container_order_item, 'get_order' ) ) ) {

					$order_id = $container_order_item->get_order_id();
					$order    = WC_PB_Helpers::cache_get( 'order_' . $order_id );

					if ( null === $order ) {
						$order = $container_order_item->get_order();
						WC_PB_Helpers::cache_set( 'order_' . $order_id, $order );
					}

				} else {
					$msg = 'get_order() is not callable on the supplied $order_item. No $order object given.';
					_doing_it_wrong( __FUNCTION__ . '()', $msg, '5.3.0' );
				}
			}

			$order_items = is_object( $order ) ? $order->get_items( 'line_item' ) : $order;

			if ( ! empty( $order_items ) ) {
				foreach ( $order_items as $order_item_id => $order_item ) {

					$is_child = false;

					if ( isset( $order_item[ 'bundle_cart_key' ] ) ) {
						$is_child = in_array( $order_item[ 'bundle_cart_key' ], $bundled_cart_keys ) ? true : false;
					} else {
						$is_child = isset( $order_item[ 'stamp' ] ) && $order_item[ 'stamp' ] == $container_order_item[ 'stamp' ] && isset( $order_item[ 'bundled_by' ] ) ? true : false;
					}

					if ( $is_child ) {
						$bundled_order_items[ $order_item_id ] = $order_item;
					}
				}
			}
		}
	}

	return $return_ids ? array_keys( $bundled_order_items ) : $bundled_order_items;
}

/**
 * True if an order item is part of a bundle.
 * Instead of relying solely on the existence of item meta, the function also checks that the alleged parent item actually exists.
 *
 * @since  5.0.0
 *
 * @param  WC_Order_Item  $order_item
 * @param  WC_Order       $order
 * @return boolean
 */
function wc_pb_is_bundled_order_item( $order_item, $order = false ) {

	$is_bundled = false;

	if ( wc_pb_get_bundled_order_item_container( $order_item, $order ) ) {
		$is_bundled = true;
	}

	return $is_bundled;
}

/**
 * True if an order item appears to be part of a bundle.
 * The result is purely based on item meta - the function does not check that a valid parent item actually exists.
 *
 * @since  5.0.0
 *
 * @param  WC_Order_Item  $order_item
 * @return boolean
 */
function wc_pb_maybe_is_bundled_order_item( $order_item ) {

	$is_bundled = false;

	if ( ! empty( $order_item[ 'bundled_by' ] ) ) {
		$is_bundled = true;
	}

	return $is_bundled;
}

/**
 * True if an order item appears to be a bundle container item.
 *
 * @since  5.0.0
 *
 * @param  WC_Order_Item  $order_item
 * @return boolean
 */
function wc_pb_is_bundle_container_order_item( $order_item ) {

	$is_bundle = false;

	if ( isset( $order_item[ 'bundled_items' ] ) ) {
		$is_bundle = true;
	}

	return $is_bundle;
}

/*
|--------------------------------------------------------------------------
| Formatting.
|--------------------------------------------------------------------------
*/

/**
 * Get precision depending on context.
 *
 * @return string
 */
function wc_pb_price_num_decimals( $context = '' ) {

	$wc_price_num_decimals_cache_key = 'wc_price_num_decimals' . ( 'extended' === $context ? '_ext' : '' );
	$wc_price_num_decimals           = WC_PB_Helpers::cache_get( $wc_price_num_decimals_cache_key );

	if ( null === $wc_price_num_decimals ) {

		if ( 'extended' === $context ) {
			$wc_price_num_decimals = wc_get_rounding_precision();
		} else {
			$wc_price_num_decimals = wc_get_price_decimals();
		}

		WC_PB_Helpers::cache_set( $wc_price_num_decimals_cache_key, $wc_price_num_decimals );
	}

	return $wc_price_num_decimals;
}

/*
|--------------------------------------------------------------------------
| Conditionals.
|--------------------------------------------------------------------------
*/

/**
 * True if the current single product page is of a bundle-type product.
 *
 * @since  5.7.0
 *
 * @return boolean
 */
function wc_pb_is_product_bundle() {
	global $product;
	return function_exists( 'is_product' ) && is_product() && ! empty( $product ) && is_callable( array( $product, 'is_type' ) ) && $product->is_type( 'bundle' );
}
