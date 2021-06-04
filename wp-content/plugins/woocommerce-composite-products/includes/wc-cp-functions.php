<?php
/**
 * Composite Products Functions
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*---------------*/
/*  Cart         */
/*---------------*/

/**
 * Given a composited cart item, find and return its container cart item - the Composite - or its cart id when the $return_id arg is true.
 *
 * @param  array    $maybe_composited_cart_item
 * @param  array    $cart_contents
 * @param  boolean  $return_id
 * @return mixed
 */
function wc_cp_get_composited_cart_item_container( $maybe_composited_cart_item, $cart_contents = false, $return_id = false ) {

	if ( ! $cart_contents ) {
		$cart_contents = isset( WC()->cart ) ? WC()->cart->cart_contents : array();
	}

	$container = false;

	if ( wc_cp_maybe_is_composited_cart_item( $maybe_composited_cart_item ) ) {

		$composited_by = $maybe_composited_cart_item[ 'composite_parent' ];

		if ( isset( $cart_contents[ $composited_by ] ) ) {
			$container = $return_id ? $composited_by : $cart_contents[ $composited_by ];
		}
	}

	return $container;
}

/**
 * Given a composite container cart item, find and return its child cart items - or their cart ids when the $return_ids arg is true.
 * Includes a deep mode argument to allow filtering the result of the internal cart item comparison.
 *
 * @param  array    $maybe_composite_container_cart_item
 * @param  array    $cart_contents
 * @param  boolean  $return_ids
 * @param  boolean  $deep_mode
 * @return mixed
 */
function wc_cp_get_composited_cart_items( $maybe_composite_container_cart_item, $cart_contents = false, $return_ids = false, $deep_mode = false ) {

	if ( ! $cart_contents ) {
		$cart_contents = isset( WC()->cart ) ? WC()->cart->cart_contents : array();
	}

	$composited_cart_items = array();

	if ( wc_cp_is_composite_container_cart_item( $maybe_composite_container_cart_item ) ) {

		$composited_items = $maybe_composite_container_cart_item[ 'composite_children' ];

		if ( ! empty( $cart_contents ) && ! empty( $composited_items ) && is_array( $composited_items ) ) {

			if ( $deep_mode ) {

					// First, find the container cart item key.
					$maybe_composite_container_cart_item_key = '';

					foreach ( $cart_contents as $search_item_key => $search_item ) {
						if ( wc_cp_is_composite_container_cart_item( $search_item ) && $search_item[ 'composite_data' ] === $maybe_composite_container_cart_item[ 'composite_data' ] ) {
							$maybe_composite_container_cart_item_key = $search_item_key;
							break;
						}
					}

					// Then, search all cart items and pass the result through the 'woocommerce_cart_item_is_child_of_composite' filter.
					if ( $maybe_composite_container_cart_item_key ) {
						foreach ( $cart_contents as $search_item_key => $search_item ) {
							/**
							 * Filter to allow sub-grouped cart items to be recognized as composite children.
							 *
							 * @param   boolean  $is_child
							 * @param   string   $checked_cart_item_key
							 * @param   array    $checked_cart_item_data
							 * @param   string   $cart_item_key
							 * @param   array    $cart_item_data
							 */
							if ( apply_filters( 'woocommerce_cart_item_is_child_of_composite', in_array( $search_item_key, $maybe_composite_container_cart_item[ 'composite_children' ] ), $search_item_key, $search_item, $maybe_composite_container_cart_item_key, $maybe_composite_container_cart_item ) ) {
								$composited_cart_items[ $search_item_key ] = $search_item;
							}
						}
					}

			} else {

				foreach ( $composited_items as $composited_cart_item_key ) {
					if ( isset( $cart_contents[ $composited_cart_item_key ] ) ) {
						$composited_cart_items[ $composited_cart_item_key ] = $cart_contents[ $composited_cart_item_key ];
					}
				}
			}
		}
	}

	return $return_ids ? array_keys( $composited_cart_items ) : $composited_cart_items;
}

/**
 * True if a cart item is part of a composite.
 * Instead of relying solely on cart item data, the function also checks that the alleged parent item actually exists.
 *
 * @param  array  $maybe_composited_cart_item
 * @param  array  $cart_contents
 * @return boolean
 */
function wc_cp_is_composited_cart_item( $maybe_composited_cart_item, $cart_contents = false ) {

	$is_composited = false;

	if ( wc_cp_get_composited_cart_item_container( $maybe_composited_cart_item, $cart_contents ) ) {
		$is_composited = true;
	}

	return $is_composited;
}

/**
 * True if a cart item appears to be part of a composite.
 * The result is purely based on cart item data - the function does not check that a valid parent item actually exists.
 *
 * @param  array  $maybe_composited_cart_item
 * @return boolean
 */
function wc_cp_maybe_is_composited_cart_item( $maybe_composited_cart_item ) {

	$is_composited = false;

	if ( ! empty( $maybe_composited_cart_item[ 'composite_parent' ] ) && ! empty( $maybe_composited_cart_item[ 'composite_item' ] ) && ! empty( $maybe_composited_cart_item[ 'composite_data' ] ) ) {
		$is_composited = true;
	}

	return $is_composited;
}

/**
 * True if a cart item appears to be a composite container item.
 *
 * @param  array  $cart_item
 * @return boolean
 */
function wc_cp_is_composite_container_cart_item( $maybe_composite_container_cart_item ) {

	$is_composite = false;

	if ( isset( $maybe_composite_container_cart_item[ 'composite_children' ] ) && ! empty( $maybe_composite_container_cart_item[ 'composite_data' ] ) ) {
		$is_composite = true;
	}

	return $is_composite;
}

/*---------------*/
/*  Orders       */
/*---------------*/

/**
 * Given a composited order item, find and return its container order item - the Composite - or its order item id when the $return_id arg is true.
 *
 * @param  array     $maybe_composited_order_item
 * @param  WC_Order  $order
 * @param  boolean   $return_id
 * @return mixed
 */
function wc_cp_get_composited_order_item_container( $maybe_composited_order_item, $order = false, $return_id = false ) {

	$result = false;

	if ( wc_cp_maybe_is_composited_order_item( $maybe_composited_order_item ) ) {

		$container = WC_CP_Helpers::cache_get( 'order_item_container_' . $maybe_composited_order_item->get_id() );

		if ( null === $container ) {

			if ( false === $order ) {
				if ( is_callable( array( $maybe_composited_order_item, 'get_order' ) ) ) {

					$order_id = $maybe_composited_order_item->get_order_id();
					$order    = WC_CP_Helpers::cache_get( 'order_' . $order_id );

					if ( null === $order ) {
						$order = $maybe_composited_order_item->get_order();
						WC_CP_Helpers::cache_set( 'order_' . $order_id, $order );
					}

				} else {
					$msg = 'get_order() is not callable on the supplied $order_item. No $order object given.';
					_doing_it_wrong( __FUNCTION__ . '()', $msg, '3.10.0' );
				}
			}

			$order_items = is_object( $order ) ? $order->get_items( 'line_item' ) : $order;

			if ( ! empty( $order_items ) ) {
				foreach ( $order_items as $order_item_id => $order_item ) {

					$is_container = false;

					if ( isset( $order_item[ 'composite_cart_key' ] ) ) {
						$is_container = $maybe_composited_order_item[ 'composite_parent' ] === $order_item[ 'composite_cart_key' ];
					} else {
						$is_container = isset( $order_item[ 'composite_data' ] ) && $order_item[ 'composite_data' ] === $maybe_composited_order_item[ 'composite_data' ] && ! isset( $order_item[ 'composite_parent' ] );
					}

					if ( $is_container ) {
						WC_CP_Helpers::cache_set( 'order_item_container_' . $maybe_composited_order_item->get_id(), $order_item );
						$container = $order_item;
						break;
					}
				}
			}
		}

		if ( $container && is_callable( array( $container, 'get_id' ) ) ) {
			$result = $return_id ? $container->get_id() : $container;
		}
	}

	return $result;
}

/**
 * Given a composite container order item, find and return its child order items - or their order item ids when the $return_ids arg is true.
 * Includes a deep mode argument to allow filtering the result of the internal order item comparison.
 *
 * @param  array     $item
 * @param  WC_Order  $order
 * @param  boolean   $return_ids
 * @param  boolean   $deep_mode
 * @return mixed
 */
function wc_cp_get_composited_order_items( $maybe_composite_container_order_item, $order = false, $return_ids = false, $deep_mode = false ) {

	$composited_order_items = array();

	if ( wc_cp_is_composite_container_order_item( $maybe_composite_container_order_item ) ) {

		$composited_cart_keys = maybe_unserialize( $maybe_composite_container_order_item[ 'composite_children' ] );

		if ( ! empty( $composited_cart_keys ) && is_array( $composited_cart_keys ) ) {

			if ( false === $order ) {
				if ( is_callable( array( $maybe_composite_container_order_item, 'get_order' ) ) ) {

					$order_id = $maybe_composite_container_order_item->get_order_id();
					$order    = WC_CP_Helpers::cache_get( 'order_' . $order_id );

					if ( null === $order ) {
						$order = $maybe_composite_container_order_item->get_order();
						WC_CP_Helpers::cache_set( 'order_' . $order_id, $order );
					}

				} else {
					$msg = 'get_order() is not callable on the supplied $order_item. No $order object given.';
					_doing_it_wrong( __FUNCTION__ . '()', $msg, '3.10.0' );
				}
			}

			$order_items = is_object( $order ) ? $order->get_items( 'line_item' ) : $order;

			foreach ( $order_items as $order_item_id => $order_item ) {

				$is_child = false;

				if ( isset( $order_item[ 'composite_cart_key' ] ) ) {
					$is_child = in_array( $order_item[ 'composite_cart_key' ], $composited_cart_keys ) ? true : false;
				} else {
					$is_child = isset( $order_item[ 'composite_data' ] ) && $order_item[ 'composite_data' ] == $maybe_composite_container_order_item[ 'composite_data' ] && isset( $order_item[ 'composite_parent' ] ) ? true : false;
				}

				if ( $deep_mode ) {
					/**
					 * Filter to allow sub-grouped order items to be recognized as composite container order item children.
					 *
					 * @param   boolean   $is_child
					 * @param   array     $checked_order_item
					 * @param   string    $container_order_item
					 * @param   WC_Order  $order
					 */
					$is_child = apply_filters( 'woocommerce_order_item_is_child_of_composite', $is_child, $order_item, $maybe_composite_container_order_item, $order );
				}

				if ( $is_child ) {
					$composited_order_items[ $order_item_id ] = $order_item;
				}
			}
		}
	}

	return $return_ids ? array_keys( $composited_order_items ) : $composited_order_items;
}

/**
 * True if an order item is part of a composite.
 * Instead of relying solely on the existence of item meta, the function also checks that the alleged parent item actually exists.
 *
 * @param  array     $maybe_composited_order_item
 * @param  WC_Order  $order
 * @return boolean
 */
function wc_cp_is_composited_order_item( $maybe_composited_order_item, $order = false ) {

	$is_composited = false;

	if ( wc_cp_get_composited_order_item_container( $maybe_composited_order_item, $order ) ) {
		$is_composited = true;
	}

	return $is_composited;
}

/**
 * True if an order item appears to be part of a composite.
 * The result is purely based on item meta - the function does not check that a valid parent item actually exists.
 *
 * @param  array  $maybe_composited_order_item
 * @return boolean
 */
function wc_cp_maybe_is_composited_order_item( $maybe_composited_order_item ) {

	$is_composited = false;

	if ( ! empty( $maybe_composited_order_item[ 'composite_parent' ] ) ) {
		$is_composited = true;
	}

	return $is_composited;
}

/**
 * True if an order item appears to be a composite container item.
 *
 * @param  array  $maybe_composited_container_order_item
 * @return boolean
 */
function wc_cp_is_composite_container_order_item( $maybe_composited_container_order_item ) {

	$is_composite = false;

	if ( isset( $maybe_composited_container_order_item[ 'composite_children' ] ) ) {
		$is_composite = true;
	}

	return $is_composite;
}

/*--------------------------*/
/*  Conditional functions.  */
/*--------------------------*/

/**
 * True if the current product page is a composite product.
 *
 * @return boolean
 */
function is_composite_product() {

	global $product;

	return function_exists( 'is_product' ) && is_product() && ! empty( $product ) && is_callable( array( $product, 'is_type' ) ) && $product->is_type( 'composite' );
}

/*----------------------------*/
/*  Helper functions.         */
/*----------------------------*/

/**
 * get_option( 'woocommerce_calc_taxes' ) cache.
 *
 * @return string
 */
function wc_cp_calc_taxes() {
	$wc_calc_taxes = WC_CP_Helpers::cache_get( 'wc_calc_taxes' );
	if ( null === $wc_calc_taxes ) {
		$wc_calc_taxes = get_option( 'woocommerce_calc_taxes' );
		WC_CP_Helpers::cache_set( 'wc_calc_taxes', $wc_calc_taxes );
	}
	return $wc_calc_taxes;
}

/**
 * get_option( 'woocommerce_prices_include_tax' ) cache.
 *
 * @return string
 */
function wc_cp_prices_include_tax() {
	$wc_prices_include_tax = WC_CP_Helpers::cache_get( 'wc_prices_include_tax' );
	if ( null === $wc_prices_include_tax ) {
		$wc_prices_include_tax = get_option( 'woocommerce_prices_include_tax' );
		WC_CP_Helpers::cache_set( 'wc_prices_include_tax', $wc_prices_include_tax );
	}
	return $wc_prices_include_tax;
}

/**
 * get_option( 'woocommerce_tax_display_shop' ) cache.
 *
 * @return string
 */
function wc_cp_tax_display_shop() {
	$wc_tax_display_shop = WC_CP_Helpers::cache_get( 'wc_tax_display_shop' );
	if ( null === $wc_tax_display_shop ) {
		$wc_tax_display_shop = get_option( 'woocommerce_tax_display_shop' );
		WC_CP_Helpers::cache_set( 'wc_tax_display_shop', $wc_tax_display_shop );
	}
	return $wc_tax_display_shop;
}

/**
 * get_option( 'woocommerce_price_decimal_sep' ) cache.
 *
 * @return string
 */
function wc_cp_price_decimal_sep() {
	$wc_price_decimal_sep = WC_CP_Helpers::cache_get( 'wc_price_decimal_sep' );
	if ( null === $wc_price_decimal_sep ) {
		$wc_price_decimal_sep = wc_get_price_decimal_separator();
		WC_CP_Helpers::cache_set( 'wc_price_decimal_sep', apply_filters( 'wc_get_price_decimal_separator', $wc_price_decimal_sep ) );
	}
	return $wc_price_decimal_sep;
}

/**
 * get_option( 'woocommerce_price_thousand_sep' ) cache.
 *
 * @return string
 */
function wc_cp_price_thousand_sep() {
	$wc_price_thousand_sep = WC_CP_Helpers::cache_get( 'wc_price_thousand_sep' );
	if ( null === $wc_price_thousand_sep ) {
		$wc_price_thousand_sep = wc_get_price_thousand_separator();
		WC_CP_Helpers::cache_set( 'wc_price_thousand_sep', apply_filters( 'wc_get_price_thousand_separator', $wc_price_thousand_sep ) );
	}
	return $wc_price_thousand_sep;
}

/**
 * Wrapper around 'wc_get_rounding_precision' and 'wc_get_price_decimals' that caches results to avoid callback hell.
 *
 * @return string
 */
function wc_cp_price_num_decimals( $context = '' ) {

	$wc_price_num_decimals_cache_key = 'wc_price_num_decimals' . ( 'extended' === $context ? '_ext' : '' );
	$wc_price_num_decimals           = WC_CP_Helpers::cache_get( $wc_price_num_decimals_cache_key );

	if ( null === $wc_price_num_decimals ) {

		if ( 'extended' === $context ) {
			$wc_price_num_decimals = wc_get_rounding_precision();
		} else {
			$wc_price_num_decimals = wc_get_price_decimals();
		}

		WC_CP_Helpers::cache_set( $wc_price_num_decimals_cache_key, $wc_price_num_decimals );
	}

	return $wc_price_num_decimals;
}

/**
 * Builds terms tree of a flatten terms array.
 *
 * @since  7.0.0
 *
 * @param  array  $terms Array of WP_Term objects.
 * @param  int    $parent_id
 * @return array
 */
function wc_cp_build_taxonomy_tree( $terms, $parent_id = 0 ) {

	if ( empty( $terms ) ) {
		return array();
	}

	// Build.
	$tree = array();
	foreach ( $terms as $index => $term ) {
		if ( $term->parent === $parent_id && ! isset( $tree[ $term->term_id ] ) ) {
			$tree[ $term->term_id ]           = $term;
			$tree[ $term->term_id ]->children = wc_cp_build_taxonomy_tree( $terms, $term->term_id );
		}
	}

	return $tree;
}

/**
 * Prints <option/> elements for a given terms tree.
 *
 * @since  7.0.0
 *
 * @param  array  $terms Array of WP_Term objects.
 * @param  array  $selected_ids
 * @param  string $prefix_html
 * @param  array  $args
 * @return void
 */
function wc_cp_print_taxonomy_tree_options( $terms, $selected_ids = array(), $args = array() ) {

	$args = wp_parse_args( $args, array(
		'prefix_html'   => '',
		'seperator'     => _x( '%1$s&nbsp;&gt;&nbsp;%2$s', 'term separator', 'woocommerce-composite-products' ),
		'shorten_text'  => true,
		'shorten_level' => 3,
		'term_path'     => array()
	) );

	$term_path = $args[ 'term_path' ];

	foreach ( $terms as $term ) {

		$term_path[] = $term->name;
		$option_text = $term->name;

		if ( ! empty( $args[ 'prefix_html' ] ) ) {
			$option_text = sprintf( $args[ 'seperator' ], $args[ 'prefix_html' ], $option_text );
		}

		// Print option element.
		echo '<option value="' . $term->term_id . '" ' . selected( in_array( $term->term_id, $selected_ids ), true, false ) . '>';

		if ( $args[ 'shorten_text' ] && count( $term_path ) > $args[ 'shorten_level' ] ) {
			echo sprintf( _x( '%1$s&nbsp;&gt;&nbsp;&hellip;&nbsp;&gt;&nbsp;%2$s', 'many terms separator', 'woocommerce-composite-products' ), $term_path[ 0 ], $term_path[ count( $term_path ) - 1 ] );
		} else {
			echo $option_text;
		}

		echo '</option>';

		// Recursive call to print children.
		if ( ! empty( $term->children ) ) {

			// Reset `prefix_html` argument to recursive mode.
			$reset_args                  = $args;
			$reset_args[ 'prefix_html' ] = $option_text;
			$reset_args[ 'term_path' ]   = $term_path;

			wc_cp_print_taxonomy_tree_options( $term->children, $selected_ids, $reset_args );
		}

		$term_path = $args[ 'term_path' ];
	}
}
