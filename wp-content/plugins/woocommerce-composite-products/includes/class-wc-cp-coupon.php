<?php
/**
 * WC_CP_Coupon class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.14.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Composite Products Coupon functions and filters.
 *
 * @class    WC_CP_Coupon
 * @version  4.1.0
 */
class WC_CP_Coupon {

	/*
	 * Initilize.
	 */
	public static function init() {

		// Coupons - inherit children coupon validity from parent.
		add_filter( 'woocommerce_coupon_is_valid_for_product', array( __CLASS__, 'coupon_validity' ), 10, 4 );
	}

	/**
	 * Inherit coupon validity from parent:
	 *
	 * - Coupon is invalid for child item if parent is excluded.
	 * - Coupon is valid for child item if valid for parent, unless child item is excluded.
	 *
	 * @param  bool        $valid
	 * @param  WC_Product  $product
	 * @param  WC_Coupon   $coupon
	 * @param  array       $item
	 * @return bool
	 */
	public static function coupon_validity( $valid, $product, $coupon, $item ) {

		if ( ! $coupon->is_type( wc_get_product_coupon_types() ) ) {
			return $valid;
		}

		if ( is_a( $item, 'WC_Order_Item_Product' ) ) {

			if ( $container_item = wc_cp_get_composited_order_item_container( $item ) ) {

				$composite    = $container_item->get_product();
				$composite_id = $container_item[ 'product_id' ];
			}

		} elseif ( ! empty( WC()->cart ) ) {

			if ( $container_item = wc_cp_get_composited_cart_item_container( $item ) ) {

				$composite    = $container_item[ 'data' ];
				$composite_id = $composite->get_id();
			}
		}

		if ( ! isset( $composite, $composite_id ) || empty( $container_item ) ) {
			return $valid;
		}

		/**
		 * Filter to disable coupon validity inheritance from container.
		 *
		 * @param  boolean     $inherit
		 * @param  WC_Product  $product
		 * @param  WC_Coupon   $coupon
		 * @param  array       $item
		 * @param  array       $container_item
		 */
		if ( apply_filters( 'woocommerce_composite_inherit_coupon_validity', true, $product, $coupon, $item, $container_item ) ) {

			/*
			 * If the Component is eligible, ensure that the container item is not excluded.
			 */
			if ( $valid ) {

				$composite_cats = wc_get_product_cat_ids( $composite_id );

				// Container ID excluded from the discount?
				if ( count( $coupon->get_excluded_product_ids() ) && count( array_intersect( array( $composite_id ), $coupon->get_excluded_product_ids() ) ) ) {
					$valid = false;
				}

				// Container categories excluded from the discount?
				if ( count( $coupon->get_excluded_product_categories() ) && count( array_intersect( $composite_cats, $coupon->get_excluded_product_categories() ) ) ) {
					$valid = false;
				}

				// Container on sale and sale items excluded from discount?
				if ( $coupon->get_exclude_sale_items() && $composite->is_on_sale() ) {
					$valid = false;
				}

			/*
			 * Otherwise, check if the Component is specifically excluded, and if not, consider it as eligible if its container item is eligible.
			 */
			} else {

				$product_ids      = array( $product->get_id(), $product->get_parent_id() );
				$product_cats     = wc_get_product_cat_ids( $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id() );
				$product_excluded = false;

				// Product IDs excluded from the discount?
				if ( count( $coupon->get_excluded_product_ids() ) && count( array_intersect( $product_ids, $coupon->get_excluded_product_ids() ) ) ) {
					$product_excluded = true;
				}

				// Product categories excluded from the discount?
				if ( count( $coupon->get_excluded_product_categories() ) && count( array_intersect( $product_cats, $coupon->get_excluded_product_categories() ) ) ) {
					$product_excluded = true;
				}

				// Product on sale and sale items excluded from discount?
				if ( $coupon->get_exclude_sale_items() && $product->is_on_sale() ) {
					$product_excluded = true;
				}

				if ( ! $product_excluded && $coupon->is_valid_for_product( $composite, $container_item ) ) {
					$valid = true;
				}
			}
		}

		return $valid;
	}
}

WC_CP_Coupon::init();
