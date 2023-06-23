<?php
/**
 * Core Functions
 *
 * Cart/order item relationship functions.
 *
 * @package  WooCommerce Mix and Match Products/Functions
 * @since    1.2.0
 * @version  2.4.6
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the main instance of WC_Mix_and_Match to prevent the need to use globals.
 *
 * @return WooCommerce
 */
function WC_Mix_and_Match() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return WC_Mix_and_Match::instance();
}

/*---------------*/
/*  Frontend.    */
/*---------------*/

/**
 * Get a name prefix for quantity input.
 *
 * @since  1.7.0
 *
 * @param  int $container_id | Product ID of MnM container product.
 * @param  int $child_id - Product ID of child product.
 * @return string
 */
function wc_mnm_get_child_input_name( $container_id, $child_id = null ) {
	$name = apply_filters( 'wc_mnm_quantity_name_prefix', '', $container_id ) . 'mnm_quantity';
	if ( $child_id ) {
		$name .= '[' . $child_id . ']';
	}
	return $name;
}

/**
 * Given a MnM container, return the prompt for properly filling a container.
 *
 * @since  1.2.0
 *
 * @param  obj    $container WC_Product_Mix_and_Match
 * @return string
 */
function wc_mnm_get_quantity_message( $container ) {

	$message = '';

	if ( wc_mnm_is_product_container_type( $container ) ) {

		$min_container_size = $container->get_min_container_size();
		$max_container_size = $container->get_max_container_size();

		// No items required.
		if ( $min_container_size === 0 ) {
			$message = '';
			// Fixed container size.
		} else if ( $min_container_size > 0 && $max_container_size > 0 && $min_container_size == $max_container_size ) {
			// translators: %d quantity to select.
			$message = sprintf( _nx( 'Please select %d item to continue&hellip;', 'Please select %d items to continue&hellip;', $min_container_size, '[Frontend]', 'woocommerce-mix-and-match-products' ), $min_container_size );
			// Required minimum and required maximum, but unequal min/max.
		} else if ( $min_container_size > 0 && $max_container_size > 0 ) {
			// translators: %1$d is minimum quantity to select. %2$d is maximum quantity to select.
			$message = sprintf( _x( 'Please choose between %1$d and %2$d items to continue&hellip;', '[Frontend]', 'woocommerce-mix-and-match-products' ), $min_container_size, $max_container_size );
			// Required minimum.
		} else if ( $min_container_size > 0 ) {
			// translators: %d minimum quantity to select.
			$message = sprintf( _nx( 'Please choose at least %d item to continue&hellip;', 'Please choose at least %d items to continue&hellip;', $min_container_size, '[Frontend]', 'woocommerce-mix-and-match-products' ), $min_container_size );
			// Required maximum.
		} else if ( $max_container_size > 0 ) {
			// translators: %d maximum quantity to select.
			$message = sprintf( _nx( 'Please choose fewer than %d item to continue&hellip;', 'Please choose fewer than %d items to continue&hellip;', $max_container_size, '[Frontend]', 'woocommerce-mix-and-match-products' ), $max_container_size );
		}
	
	}

	/**
	 * Container quantity error message.
	 *
	 * @param  str $message
	 * @param  obj $container WC_Product.
	 */
	return apply_filters( 'wc_mnm_container_quantity_message', $message, $container );

}

/*---------------*/
/*  Cart.        */
/*---------------*/

/**
 * Given a child MnM cart item, find and return its container cart item or its cart ID when the $return_id arg is true.
 *
 * @since  1.7.0
 *
 * @param  array $child_cart_item
 * @param  array $cart_contents
 * @param  bool  $return_id
 * @return mixed
 */
function wc_mnm_get_cart_item_container( $child_cart_item, $cart_contents = false, $return_id = false ) {

	if ( ! $cart_contents ) {
		$cart_contents = isset( WC()->cart ) ? WC()->cart->cart_contents : array();
	}

	$container = false;

	if ( wc_mnm_maybe_is_child_cart_item( $child_cart_item ) ) {

		$possible_container = $child_cart_item['mnm_container'];

		// Check the container is still in the cart contents.
		if ( isset( $cart_contents[ $possible_container ] ) ) {
			$container = $return_id ? $possible_container : $cart_contents[ $possible_container ];
		}
	}

	return $container;
}

/**
 * Given a MnM container cart item, find and return its child cart items.
 *
 * @since  1.7.0
 *
 * @param  array    $container_cart_item
 * @param  array    $cart_contents
 * @param  bool  $return_ids
 * @return array Either cart items or their cart keys depending on if the $return_ids arg is true
 */
function wc_mnm_get_child_cart_items( $container_cart_item, $cart_contents = false, $return_ids = false ) {

	if ( ! $cart_contents ) {
		$cart_contents = isset( WC()->cart ) ? WC()->cart->cart_contents : array();
	}

	$child_cart_items = array();

	if ( wc_mnm_is_container_cart_item( $container_cart_item ) ) {

		$child_items = $container_cart_item['mnm_contents'];

		// Check the children are still in the cart contents.
		if ( ! empty( $child_items ) && is_array( $child_items ) ) {
			foreach ( $child_items as $child_cart_item_key ) {
				if ( isset( $cart_contents[ $child_cart_item_key ] ) ) {
					$child_cart_items[ $child_cart_item_key ] = $cart_contents[ $child_cart_item_key ];
				}
			}
		}
	}

	return $return_ids ? array_keys( $child_cart_items ) : $child_cart_items;
}

/**
 * True if a cart item is a child of a a MnM container.
 * Instead of relying solely on cart item data, the function also checks that the alleged parent item actually exists.
 *
 * @since  1.7.0
 *
 * @param  array  $cart_item
 * @param  array  $cart_contents
 * @return bool
 */
function wc_mnm_is_child_cart_item( $cart_item, $cart_contents = false ) {

	$is_child = false;

	if ( wc_mnm_get_cart_item_container( $cart_item, $cart_contents ) ) {
		$is_child = true;
	}

	return $is_child;
}

/**
 * True if a cart item appears to be a child product that is part of a MnM container.
 * The result is purely based on cart item data - the function does not check that a valid parent item actually exists.
 *
 * @since  1.7.0
 *
 * @param  array  $cart_item
 * @return bool
 */
function wc_mnm_maybe_is_child_cart_item( $cart_item ) {

	$is_child = false;

	if ( isset( $cart_item['mnm_container'] ) ) {
		$is_child = true;
	}

	return $is_child;
}

/**
 * True if a cart item appears to be a MnM container.
 *
 * @since  1.7.0
 *
 * @param  array  $cart_item
 * @return bool
 */
function wc_mnm_is_container_cart_item( $cart_item ) {

	$is_container = false;

	if ( isset( $cart_item['mnm_contents'] ) && isset( $cart_item['mnm_config'] ) ) {
		$is_container = true;
	}

	return $is_container;
}


/*---------------*/
/*  Orders.      */
/*---------------*/

/**
 * Given a MnM child order item, find and return its container order item or its order item ID when the $return_id arg is true.
 *
 * @since  1.7.0
 *
 * @param  array     $child_order_item
 * @param  mixed     array|object $order array of order items or WC_Order
 * @param  bool      $return_id
 * @return mixed
 */
function wc_mnm_get_order_item_container( $child_order_item, $order = false, $return_id = false ) {

	$container = false;

	if ( wc_mnm_maybe_is_child_order_item( $child_order_item ) ) {

		if ( false === $order ) {
			if ( is_callable( array( $child_order_item, 'get_order' ) ) ) {

				$order_id = $child_order_item->get_order_id();
				$order    = WC_MNM_Helpers::cache_get( 'order_' . $order_id );

				if ( null === $order ) {
					$order = $child_order_item->get_order();
					WC_MNM_Helpers::cache_set( 'order_' . $order_id, $order );
				}
			} else {
				$msg = __( 'get_order() is not callable on the supplied $order_item. No $order object given.', 'woocommerce-mix-and-match-products' );
				wc_doing_it_wrong( __FUNCTION__ . '()', $msg, '1.3.0' );
			}
		}

		$order_items = is_callable( array( $order, 'get_items' ) ) ? $order->get_items() : $order;

		if ( ! empty( $order_items ) ) {
			foreach ( $order_items as $order_item_id => $order_item ) {

				$is_container = isset( $order_item['mnm_cart_key'] ) && $child_order_item['mnm_container'] === $order_item['mnm_cart_key'];

				if ( $is_container ) {
					$container = $return_id ? $order_item_id : $order_item;
				}
			}
		}
	}

	return $container;
}

/**
 * Given a MnM container order item, find and return its child order items - or their order item IDs when the $return_ids arg is true.
 *
 * @since  1.7.0
 *
 * @param  array        $container_order_item
 * @param  array|object $order array of order items or WC_Order
 * @param  bool         $return_ids
 * @return mixed
 */
function wc_mnm_get_child_order_items( $container_order_item, $order = false, $return_ids = false ) {

	$child_order_items = array();

	if ( wc_mnm_is_container_order_item( $container_order_item ) ) {

		if ( false === $order ) {
			if ( is_callable( array( $container_order_item, 'get_order' ) ) ) {

				$order_id = $container_order_item->get_order_id();
				$order    = WC_MNM_Helpers::cache_get( 'order_' . $order_id );

				if ( null === $order ) {
					$order = $container_order_item->get_order();
					WC_MNM_Helpers::cache_set( 'order_' . $order_id, $order );
				}
			} else {
				$msg = __( 'get_order() is not callable on the supplied $order_item. No $order object given.', 'woocommerce-mix-and-match-products' );
				wc_doing_it_wrong( __FUNCTION__ . '()', $msg, '5.3.0' );
			}
		}

		$order_items = is_callable( array( $order, 'get_items' ) ) ? $order->get_items() : $order;

		if ( ! empty( $order_items ) ) {
			
			foreach ( $order_items as $order_item_id => $order_item ) {

				$is_child = ! empty( $order_item['mnm_container'] ) && isset( $container_order_item['mnm_cart_key'] ) && $order_item['mnm_container'] === $container_order_item['mnm_cart_key'];

				if ( $is_child ) {
					$child_order_items[ $order_item_id ] = $order_item;
				}
			}
		}

	}

	return $return_ids ? array_keys( $child_order_items ) : $child_order_items;
}

/**
 * True if an order item is part of a MnM container.
 * Instead of relying solely on the existence of item meta, the function also checks that the alleged parent item actually exists.
 *
 * @since  1.7.0
 *
 * @param  array     $order_item
 * @param  mixed     array|object $order array of order items or WC_Order
 * @return bool
 */
function wc_mnm_is_child_order_item( $order_item, $order = false ) {

	$is_child = false;

	if ( wc_mnm_get_order_item_container( $order_item, $order ) ) {
		$is_child = true;
	}

	return $is_child;
}

/**
 * True if an order item appears to be part of a MnM container.
 * The result is purely based on item meta - the function does not check that a valid parent item actually exists.
 *
 * @since  1.7.0
 *
 * @param  array  $order_item
 * @return bool
 */
function wc_mnm_maybe_is_child_order_item( $order_item ) {

	$is_child = false;

	if ( ! empty( $order_item['mnm_container'] ) ) {
		$is_child = true;
	}

	return $is_child;
}

/**
 * True if an order item appears to be a MnM container.
 *
 * @since  1.7.0
 *
 * @param  array  $order_item
 * @return bool
 */
function wc_mnm_is_container_order_item( $order_item ) {

	$is_container = false;

	if ( isset( $order_item['mnm_config'] ) ) {
		$is_container = true;
	}

	return $is_container;
}


/*---------------*/
/*  Products.    */
/*---------------*/

/**
 * Given a product, test if a Mix and Match container type.
 *
 * @since  2.2.0
 *
 * @param  mixed string|WC_Product $product - A product object or product type.
 * @return bool
 */
function wc_mnm_is_product_container_type( $product ) {

	$container_types = (array) apply_filters( 'wc_mnm_product_container_types', array( 'mix-and-match' ) );

	if ( $product instanceof WC_Product ) {
		$is_type = $product->is_type( $container_types );
	} else {
		$is_type = in_array( $product, $container_types );
	}

	return $is_type;
}

/*---------------*/
/*  Formatting.  */
/*---------------*/

/**
 * Given an array, prefix all keys with data-
 *
 * @since  2.3.0
 *
 * @param  array $attributes
 * @return array
 */
function wc_mnm_prefix_data_attribute_keys( $attributes ) {

	if ( ! empty ( $attributes ) ) {
		$new_keys = array_map(function ($key) { return 'data-' . $key; }, array_keys( $attributes ) );
		$attributes = array_combine( $new_keys, array_values( $attributes ) );	
	}

	return $attributes;
}

/*
|--------------------------------------------------------------------------
| Deprecated.
|--------------------------------------------------------------------------
*/

/**
 * Given a child MnM cart item, find and return its container cart item or its cart ID when the $return_id arg is true.
 *
 * @since  1.2.0
 * @deprecated 1.7.0
 *
 * @param  array    $child_cart_item
 * @param  array    $cart_contents
 * @param  bool  $return_id
 * @return mixed
 */
function wc_mnm_get_mnm_cart_item_container( $child_cart_item, $cart_contents = false, $return_id = false ) {
	return wc_mnm_get_cart_item_container( $child_cart_item, $cart_contents, $return_id );
}

/**
 * Given a MnM container cart item, find and return its child cart items - or their cart IDs when the $return_ids arg is true.
 *
 * @since  1.2.0
 * @deprecated 1.7.0
 *
 * @param  array    $container_cart_item
 * @param  array    $cart_contents
 * @param  bool  $return_ids
 * @return mixed
 */
function wc_mnm_get_mnm_cart_items( $container_cart_item, $cart_contents = false, $return_ids = false ) {
	return wc_mnm_get_child_cart_items( $container_cart_item, $cart_contents, $return_ids );
}

/**
 * True if a cart item is a child in a MnM container.
 * Instead of relying solely on cart item data, the function also checks that the alleged parent item actually exists.
 *
 * @since  1.2.0
 * @deprecated 1.7.0
 *
 * @param  array  $cart_item
 * @param  array  $cart_contents
 * @return bool
 */
function wc_mnm_is_mnm_cart_item( $cart_item, $cart_contents = false ) {
	return wc_mnm_is_child_cart_item( $cart_item, $cart_contents );
}

/**
 * True if a cart item appears to be part of a MnM container.
 * The result is purely based on cart item data - the function does not check that a valid parent item actually exists.
 *
 * @since  1.2.0
 * @deprecated 1.7.0
 *
 * @param  array  $cart_item
 * @return bool
 */
function wc_mnm_maybe_is_mnm_cart_item( $cart_item ) {
	return wc_mnm_maybe_is_child_cart_item( $cart_item );
}

/**
 * True if a cart item appears to be a MnM container item.
 *
 * @since  1.2.0
 * @deprecated 1.7.0
 *
 * @param  array  $cart_item
 * @return bool
 */
function wc_mnm_is_mnm_container_cart_item( $cart_item ) {
	return wc_mnm_is_container_cart_item( $cart_item );
}

/**
 * Given a MnM child order item, find and return its container order item or its order item ID when the $return_id arg is true.
 *
 * @since  1.2.0
 * @deprecated 1.7.0
 *
 * @param  array     $child_order_item
 * @param  mixed     array|object $order array of order items or WC_Order
 * @param  bool      $return_id
 * @return mixed
 */
function wc_mnm_get_mnm_order_item_container( $child_order_item, $order = false, $return_id = false ) {
	return wc_mnm_get_order_item_container( $child_order_item, $order, $return_id );
}

/**
 * Given a MnM container order item, find and return its child order items - or their order item IDs when the $return_ids arg is true.
 *
 * @since  1.2.0
 * @deprecated 1.7.0
 *
 * @param  array        $container_order_item
 * @param  array|object $order array of order items or WC_Order
 * @param  bool         $return_ids
 * @return mixed
 */
function wc_mnm_get_mnm_order_items( $container_order_item, $order = false, $return_ids = false ) {
	return wc_mnm_get_child_order_items( $container_order_item, $order, $return_ids );
}

/**
 * True if an order item is part of a MnM bundle.
 * Instead of relying solely on the existence of item meta, the function also checks that the alleged parent item actually exists.
 *
 * @since  1.2.0
 * @deprecated 1.7.0
 *
 * @param  array     $order_item
 * @param  mixed     array|object $order array of order items or WC_Order
 * @return bool
 */
function wc_mnm_is_mnm_order_item( $order_item, $order = false ) {
	return wc_mnm_is_child_order_item( $order_item, $order );
}

/**
 * True if an order item appears to be part of a MnM bundle.
 * The result is purely based on item meta - the function does not check that a valid parent item actually exists.
 *
 * @since  1.2.0
 * @deprecated 1.7.0
 *
 * @param  array  $order_item
 * @return bool
 */
function wc_mnm_maybe_is_mnm_order_item( $order_item ) {
	return wc_mnm_maybe_is_child_order_item( $order_item );
}

/**
 * True if an order item appears to be a MnM container item.
 *
 * @since  1.2.0
 * @deprecated 1.7.0
 *
 * @param  array  $order_item
 * @return bool
 */
function wc_mnm_is_mnm_container_order_item( $order_item ) {
	return wc_mnm_is_container_order_item( $order_item );
}


/**
 * Returns the incl/excl tax coefficients for calculating prices incl/excl tax on the client side.
 *
 * @since  1.4.0
 * @deprecated 2.0.0
 *
 * @param  WC_Product  $product
 * @return array
 */
function wc_mnm_get_tax_ratios( $product ) {
	wc_deprecated_function( 'wc_mnm_get_tax_ratios()', '2.0', 'WC_MNM_Product_Prices::get_tax_ratios()' );
	return WC_MNM_Product_Prices::get_tax_ratios( $product );
}
