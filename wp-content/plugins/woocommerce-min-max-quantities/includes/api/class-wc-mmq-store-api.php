<?php
/**
 * WC_MMQ_Store_API class
 *
 * @package  Woo Min/Max Quantities
 * @since    2.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;

/**
 * Filters the store public API.
 *
 * @version 4.1.2
 */
class WC_MMQ_Store_API {

	/**
	 * Plugin Identifier, unique to each plugin.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'min_max_quantities';

	/**
	 * Bootstraps the class and hooks required data.
	 */
	public static function init() {

		// Filter minimum cart item quantity.
		add_filter( 'woocommerce_store_api_product_quantity_minimum', array( __CLASS__, 'filter_min_cart_item_qty' ), 10, 3 );

		// Filter maximum cart item quantity.
		add_filter( 'woocommerce_store_api_product_quantity_maximum', array( __CLASS__, 'filter_max_cart_item_qty' ), 10, 3 );

		// Filter group of cart item quantity.
		add_filter( 'woocommerce_store_api_product_quantity_multiple_of', array( __CLASS__, 'filter_multiple_of_cart_item_qty' ), 10, 3 );

		// Validate cart based on Min/Max/Group of rules and add error notices.
		add_action( 'woocommerce_store_api_cart_errors', array( __CLASS__, 'validate_cart' ), 10, 2 );

		// Prevent access to the checkout block.
		add_action( 'woocommerce_store_api_checkout_update_order_meta', array( __CLASS__, 'validate_draft_order' ) );
	}

	/**
	 * Adjust cart item quantity limits to keep min quantity limited by Min/Max Quantity restrictions.
	 *
	 * If the $cart_item is null, then this means that the $product has not been added to the cart yet.
	 * In this case, the minimum product quantity can be read directly from the post meta.
	 * When the $cart_item exists, though, the minimum product quantity must be filtered by the `wc_min_max_quantity_minimum_allowed_quantity` first.
	 *
	 * @param mixed       $value The value being filtered.
	 * @param \WC_Product $product The product object.
	 * @param array|null  $cart_item The cart item if the product exists in the cart, or null.
	 *
	 * @return mixed
	 */
	public static function filter_min_cart_item_qty( $value, $product, $cart_item ) {

		if ( $product->is_type( 'variation' ) ) {
			$min_max_rules = get_post_meta( $product->get_id(), 'min_max_rules', true );
			if ( 'yes' === $min_max_rules ) {
				$value = absint( get_post_meta( $product->get_id(), 'variation_minimum_allowed_quantity', true ) );

				if ( ! is_null( $cart_item ) ) {

					/**
					 * Use this filter to filter the Minimum Quantity of a product/variation.
					 *
					 * @since 2.2.7
					 *
					 * @param  string  $quantity
					 * @param  int     $variation_id
					 * @param  string  $cart_item_key
					 * @param  array   $cart_item
					 */
					$value = absint( apply_filters( 'wc_min_max_quantity_minimum_allowed_quantity', $value, $product->get_id(), $cart_item[ 'key' ], $cart_item ) );
				}
			} else {
				$allow_combination = 'yes' === get_post_meta( $product->get_parent_id(), 'allow_combination', true );

				// Do not automatically update the min cart item quantity, if allow combination is enabled.
				if ( ! $allow_combination ) {
					$value = absint( get_post_meta( $product->get_parent_id(), 'minimum_allowed_quantity', true ) );
					if ( ! is_null( $cart_item ) ) {

						/**
						 * Use this filter to filter the Minimum Quantity of a product/variation.
						 *
						 * @since 2.2.7
						 *
						 * @param  string  $quantity
						 * @param  int     $variation_id
						 * @param  string  $cart_item_key
						 * @param  array   $cart_item
						 */
						$value = absint( apply_filters( 'wc_min_max_quantity_minimum_allowed_quantity', $value, $product->get_id(), $cart_item[ 'key' ], $cart_item ) );
					}
				}
			}
		} else {
			$value = absint( get_post_meta( $product->get_id(), 'minimum_allowed_quantity', true ) );

			if ( ! is_null( $cart_item ) ) {

				/**
				 * Use this filter to filter the Minimum Quantity of a product/variation.
				 *
				 * @since 2.2.7
				 *
				 * @param  string  $quantity
				 * @param  int     $product_id
				 * @param  string  $cart_item_key
				 * @param  array   $cart_item
				 */
				$value = absint( apply_filters( 'wc_min_max_quantity_minimum_allowed_quantity', $value, $product->get_id(), $cart_item[ 'key' ], $cart_item ) );
			}
		}

		return $value;
	}

	/**
	 * Adjust cart item quantity limits to keep max quantity limited by Min/Max Quantity restrictions.
	 *
	 * If the $cart_item is null, then this means that the $product has not been added to the cart yet.
	 * In this case, the maximum product quantity can be read directly from the post meta.
	 * When the $cart_item exists, though, the maximum product quantity must be filtered by the `wc_min_max_quantity_maximum_allowed_quantity` first.
	 *
	 * @param mixed       $value The value being filtered.
	 * @param \WC_Product $product The product object.
	 * @param array|null  $cart_item The cart item if the product exists in the cart, or null.
	 *
	 * @return mixed
	 */
	public static function filter_max_cart_item_qty( $value, $product, $cart_item ) {

		if ( $product->is_type( 'variation' ) ) {
			$min_max_rules = get_post_meta( $product->get_id(), 'min_max_rules', true );
			if ( 'yes' === $min_max_rules ) {
				$max_quantity = absint( get_post_meta( $product->get_id(), 'variation_maximum_allowed_quantity', true ) );
				if ( ! is_null( $cart_item ) ) {

					/**
					 * Use this filter to filter the Maximum Quantity of a product/variation.
					 *
					 * @since 2.2.7
					 *
					 * @param  string  $quantity
					 * @param  int     $variation_id
					 * @param  string  $cart_item_key
					 * @param  array   $cart_item
					 */
					$max_quantity = absint( apply_filters( 'wc_min_max_quantity_maximum_allowed_quantity', $max_quantity, $product->get_id(), $cart_item[ 'key' ], $cart_item ) );
				}
			} else {

				$max_quantity = absint( get_post_meta( $product->get_parent_id(), 'maximum_allowed_quantity', true ) );
				if ( ! is_null( $cart_item ) ) {

					/**
					 * Use this filter to filter the Maximum Quantity of a product/variation.
					 *
					 * @since 2.2.7
					 *
					 * @param  string  $quantity
					 * @param  int     $variation_id
					 * @param  string  $cart_item_key
					 * @param  array   $cart_item
					 */
					$max_quantity = absint( apply_filters( 'wc_min_max_quantity_maximum_allowed_quantity', $max_quantity, $cart_item[ 'product_id' ], $cart_item[ 'key' ], $cart_item ) );
				}
			}
		} else {
			$max_quantity = absint( get_post_meta( $product->get_id(), 'maximum_allowed_quantity', true ) );
			if ( ! is_null( $cart_item ) ) {

				/**
				 * Use this filter to filter the Maximum Quantity of a product/variation.
				 *
				 * @since 2.2.7
				 *
				 * @param  string  $quantity
				 * @param  int     $product_id
				 * @param  string  $cart_item_key
				 * @param  array   $cart_item
				 */
				$max_quantity = absint( apply_filters( 'wc_min_max_quantity_maximum_allowed_quantity', $max_quantity, $product->get_id(), $cart_item[ 'key' ], $cart_item ) );
			}
		}

		// Avoid returning zero as the max quantity.
		return ! empty( $max_quantity ) ? $max_quantity : $value;
	}

	/**
	 * Ensure that cart item quantity can be changed in specific intervals.
	 *
	 * If the $cart_item is null, then this means that the $product has not been added to the cart yet.
	 * In this case, the group of product quantity can be read directly from the post meta.
	 * When the $cart_item exists, though, the group of product quantity must be filtered by the `wc_min_max_quantity_group_of_quantity` first.
	 *
	 * @param mixed       $value The value being filtered.
	 * @param \WC_Product $product The product object.
	 * @param array|null  $cart_item The cart item if the product exists in the cart, or null.
	 *
	 * @return mixed
	 */
	public static function filter_multiple_of_cart_item_qty( $value, $product, $cart_item ) {

		$group_of_quantity = 0;

		if ( $product->is_type( 'variation' ) ) {
			$min_max_rules = get_post_meta( $product->get_id(), 'min_max_rules', true );
			if ( 'yes' === $min_max_rules ) {
				$group_of_quantity = absint( get_post_meta( $product->get_id(), 'variation_group_of_quantity', true ) );

				if ( ! is_null( $cart_item ) ) {

					/**
					 * Use this filter to filter the Group of quantity of a product/variation.
					 *
					 * @since 2.2.7
					 *
					 * @param  string  $quantity
					 * @param  int     $variation_id
					 * @param  string  $cart_item_key
					 * @param  array   $cart_item
					 */
					$group_of_quantity = absint( apply_filters( 'wc_min_max_quantity_group_of_quantity', $group_of_quantity, $product->get_id(), $cart_item[ 'key' ], $cart_item ) );
				}
			} else {
				$allow_combination = 'yes' === get_post_meta( $product->get_parent_id(), 'allow_combination', true );

				if ( ! $allow_combination ) {
					$group_of_quantity = absint( get_post_meta( $product->get_parent_id(), 'group_of_quantity', true ) );
					if ( ! is_null( $cart_item ) ) {

						/**
						 * Use this filter to filter the Group of quantity of a product/variation.
						 *
						 * @since 2.2.7
						 *
						 * @param  string  $quantity
						 * @param  int     $variation_id
						 * @param  string  $cart_item_key
						 * @param  array   $cart_item
						 */
						$group_of_quantity = absint( apply_filters( 'wc_min_max_quantity_group_of_quantity', $group_of_quantity, $product->get_id(), $cart_item[ 'key' ], $cart_item ) );
					}
				}
			}
		} else {
			$group_of_quantity = absint( get_post_meta( $product->get_id(), 'group_of_quantity', true ) );
			if ( ! is_null( $cart_item ) ) {

				/**
				 * Use this filter to filter the Group of quantity of a product/variation.
				 *
				 * @since 2.2.7
				 *
				 * @param  string  $quantity
				 * @param  int     $product_id
				 * @param  string  $cart_item_key
				 * @param  array   $cart_item
				 */
				$group_of_quantity = absint( apply_filters( 'wc_min_max_quantity_group_of_quantity', $group_of_quantity, $product->get_id(), $cart_item[ 'key' ], $cart_item ) );
			}
		}

		if ( 0 === $group_of_quantity ) {
			$mmq_instance      = WC_Min_Max_Quantities::get_instance();
			$group_of_quantity = $mmq_instance->get_group_of_quantity_for_product( $product );
		}

		// Avoid returning zero as the group of quantity.
		return ! empty( $group_of_quantity ) ? $group_of_quantity : $value;
	}

	/**
	 * Validate cart based on Min/Max/Group of rules and add error notices.
	 *
	 * @throws RouteException
	 *
	 * @param WP_Error $errors
	 * @param WC_Cart  $cart
	 */
	public static function validate_cart( $errors, $cart ) {

		try {
			$mmq = WC_Min_Max_Quantities::get_instance();
			$mmq->check_cart_items();
		} catch ( Exception $e ) {
			$notice = html_entity_decode( wp_strip_all_tags( $e->getMessage() ), ENT_QUOTES );
			$errors->add( 'woocommerce_store_api_invalid_min_max_quantities', $notice );
		}
	}

	/**
	 * Prevents access to the checkout block if the cart item quantities are not correctly configured.
	 *
	 * @throws RouteException
	 *
	 * @param  WC_Order  $order
	 * @return array
	 */
	public static function validate_draft_order( $order ) {

		try {
			$mmq = WC_Min_Max_Quantities::get_instance();
			$mmq->check_cart_items();
		} catch ( Exception $e ) {
			$notice = html_entity_decode( wp_strip_all_tags($e->getMessage() ), ENT_QUOTES );
			throw new RouteException( 'woocommerce_store_api_invalid_min_max_quantities', $notice );
		}
	}
}
