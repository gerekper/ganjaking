<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package     WC_Pre_Orders/Cart
 * @author      WooThemes
 * @copyright   Copyright (c) 2013, WooThemes
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Pre-Orders Cart class
 *
 * Customizes the cart
 *
 * @since 1.0
 */
class WC_Pre_Orders_Cart {


	/**
	 * Add hooks / filters
	 *
	 * @since 1.0
	 * @return \WC_Pre_Orders_Cart
	 */
	public function __construct() {

		// Remove other products from the cart when adding a pre-order
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_cart' ), 15, 2 );

		// Maybe add pre-order fees when calculating totals
		add_action( 'woocommerce_cart_calculate_fees', array( $this, 'maybe_add_pre_order_fee' ) );

		// Modify formatted totals
		add_filter( 'woocommerce_cart_total', array( $this, 'get_formatted_cart_total' ) );

		// Modify line item display in cart/checkout to show availability date/time
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );

	}


	/**
	 * Get the order total formatted to show when the order will be charged
	 *
	 * @since 1.0
	 * @param string $total price string ( note: this is already formatted by woocommerce_price() )
	 * @return string the formatted order total price string
	 */
	public function get_formatted_cart_total( $total ) {

		// this check prevents a formatted total from display anywhere but the cart/checkout page
		if ( $this->cart_contains_pre_order() )
			$total = WC_Pre_Orders_Manager::get_formatted_pre_order_total( $total, self::get_pre_order_product() );

		return $total;
	}


	/**
	 * Get item data to display on cart/checkout pages that shows the availability date of the pre-order
	 *
	 * @since 1.0
	 * @param array $item_data any existing item data
	 * @param array $cart_item the cart item
	 * @return array
	 */
	public function get_item_data( $item_data, $cart_item ) {

		// only modify pre-orders on cart/checkout page
		if ( ! $this->cart_contains_pre_order() )
			return $item_data;

		// get title text
		$name = get_option( 'wc_pre_orders_availability_date_cart_title_text' );

		// don't add if empty
		if ( ! $name )
			return $item_data;

		$pre_order_meta = apply_filters( 'wc_pre_orders_cart_item_meta', array(
			'name'    => $name,
			'display' => WC_Pre_Orders_Product::get_localized_availability_date( $cart_item['data'] ),
		), $cart_item );

		// add title and localized date
		if ( ! empty( $pre_order_meta ) )
			$item_data[] = $pre_order_meta;

		return $item_data;
	}


	/**
	 * When a pre-order is added to the cart, remove any other products
	 *
	 * @since 1.0
	 * @param bool $valid
	 * @param $product_id
	 * @return bool
	 */
	public function validate_cart( $valid, $product_id ) {
		global $woocommerce;

		if ( WC_Pre_Orders_Product::product_can_be_pre_ordered( $product_id ) ) {

			// if a pre-order product is being added to cart, check if the cart already contains other products and empty it if it does
			if( $woocommerce->cart->get_cart_contents_count() >= 1 ) {

				$woocommerce->cart->empty_cart();

				$string = __( 'Your previous cart was emptied because pre-orders must be purchased separately.', 'wc-pre-orders' );

				// Backwards compatible (pre 2.1) for outputting notice
				if ( function_exists( 'wc_add_notice' ) ) {
					wc_add_notice( $string );
				} else {
					$woocommerce->add_message( $string );
				}
			}

			// return what was passed in, allowing the pre-order to be added
			return $valid;

		} else {

			// if there's a pre-order in the cart already, prevent anything else from being added
			if ( $this->cart_contains_pre_order() ) {

				// Backwards compatible (pre 2.1) for outputting notice
				if ( function_exists( 'wc_add_notice' ) ) {
					wc_add_notice( __( 'This product cannot be added to your cart because it already contains a pre-order, which must be purchased separately.', 'wc-pre-orders' ) );
				} else {
					$woocommerce->add_error( __( 'This product cannot be added to your cart because it already contains a pre-order, which must be purchased separately.', 'wc-pre-orders' ) );
				}

				$valid = false;
			}
		}

		return $valid;
	}


	/**
	 * Add any applicable pre-order fees when calculating totals
	 *
	 * @since 1.0
	 */
	public function maybe_add_pre_order_fee() {
		global $woocommerce;

		// Only add pre-order fees if the cart contains a pre-order
		if ( ! $this->cart_contains_pre_order() ) {
			return;
		}

		// Make sure the pre-order fee hasn't already been added
		if ( $this->cart_contains_pre_order_fee() ) {
			return;
		}

		$product = self::get_pre_order_product();

		// Get pre-order amount
		$amount = WC_Pre_Orders_Product::get_pre_order_fee( $product );

		if ( 0 >= $amount ) {
			return;
		}

		$fee = apply_filters( 'wc_pre_orders_fee', array(
			'label' => __( 'Pre-Order Fee', 'wc-pre-orders' ),
			'amount' => $amount,
			'tax_status' => WC_Pre_Orders_Product::get_pre_order_fee_tax_status( $product ), // pre order fee inherits tax status of product
		) );

		// Add fee
		$woocommerce->cart->add_fee( $fee['label'], $fee['amount'], $fee['tax_status'] );
	}


	/**
	 * Checks if the current cart contains a product with pre-orders enabled
	 *
	 * @since 1.0
	 * @return bool true if the cart contains a pre-order, false otherwise
	 */
	public static function cart_contains_pre_order() {
		global $woocommerce;

		$contains_pre_order = false;

		if ( ! empty( $woocommerce->cart->cart_contents ) ) {

			foreach ( $woocommerce->cart->cart_contents as $cart_item ) {

				if ( WC_Pre_Orders_Product::product_can_be_pre_ordered( $cart_item['product_id'] ) ) {

					$contains_pre_order = true;
					break;
				}
			}
		}

		return $contains_pre_order;
	}


	/**
	 * Checks if the current cart contains a pre-order fee
	 *
	 * @since 1.0
	 * @return bool true if the cart contains a pre-order fee, false otherwise
	 */
	public static function cart_contains_pre_order_fee() {
		global $woocommerce;

		foreach ( $woocommerce->cart->get_fees() as $fee ) {

			if ( is_object( $fee ) && 'pre-order-fee' == $fee->id )
				return true;
		}

		return false;
	}


	/**
	 * Since a cart may only contain a single pre-ordered product, this returns the pre-ordered product object or
	 * null if the cart does not contain a pre-order
	 *
	 * @since 1.0
	 * @return object|null the pre-ordered product object, or null if the cart does not contain a pre-order
	 */
	public static function get_pre_order_product() {
		global $woocommerce;

		if ( self::cart_contains_pre_order() ) {

			foreach ( $woocommerce->cart->cart_contents as $cart_item ) {

				if ( WC_Pre_Orders_Product::product_can_be_pre_ordered( $cart_item['product_id'] ) ) {

					// return the product object
					return wc_get_product( $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'] );
				}
			}

		} else {

			// cart doesn't contain pre-order
			return null;
		}
	}


} // end \WC_Pre_Orders_Cart class
