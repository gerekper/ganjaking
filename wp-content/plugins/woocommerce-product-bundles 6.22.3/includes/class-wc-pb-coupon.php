<?php
/**
 * WC_PB_Coupon class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.8.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Bundle Coupon functions and filters.
 *
 * @class    WC_PB_Coupon
 * @version  5.11.0
 */
class WC_PB_Coupon {

	/*
	 * Initilize.
	 */
	public static function init() {

		// Coupons - inherit bundled item coupon validity from parent.
		add_filter( 'woocommerce_coupon_is_valid_for_product', array( __CLASS__, 'coupon_is_valid_for_product' ), 10, 4 );
	}

	/**
	 * Inherit coupon validity from parent:
	 *
	 * - Coupon is invalid for bundled item if parent is excluded.
	 * - Coupon is valid for bundled item if valid for parent, unless bundled item is excluded.
	 *
	 * @since  5.8.0
	 *
	 * @param  bool                   $valid
	 * @param  WC_Product             $product
	 * @param  WC_Coupon              $coupon
	 * @param  WC_Order_Item_Product  $item
	 * @return boolean
	 */
	public static function coupon_is_valid_for_product(  $valid, $product, $coupon, $item  ) {

		if ( ! $coupon->is_type( wc_get_product_coupon_types() ) ) {
			return $valid;
		}

		if ( is_a( $item, 'WC_Order_Item_Product' ) ) {

			if ( $container_item = wc_pb_get_bundled_order_item_container( $item ) ) {

				$bundle    = $container_item->get_product();
				$bundle_id = $container_item[ 'product_id' ];
			}

		} elseif ( ! empty( WC()->cart ) ) {

			if ( $container_item = wc_pb_get_bundled_cart_item_container( $item ) ) {

				$bundle    = $container_item[ 'data' ];
				$bundle_id = $container_item[ 'product_id' ];
			}
		}

		if ( ! isset( $bundle, $bundle_id ) || empty( $container_item ) ) {
			return $valid;
		}

		/**
		 * 'woocommerce_bundles_inherit_coupon_validity' filter.
		 *
		 * Uset this to prevent coupon valididty inheritance for bundled products.
		 *
		 * @param  boolean     $inherit
		 * @param  WC_Product  $product
		 * @param  WC_Coupon   $coupon
		 * @param  array       $item
		 * @param  array       $container_item
		 */
		if ( apply_filters( 'woocommerce_bundles_inherit_coupon_validity', true, $product, $coupon, $item, $container_item ) ) {

			/*
			 * If the bundled item is eligible, ensure that the container item is not excluded.
			 */
			if ( $valid ) {

				$bundle_cats = wc_get_product_cat_ids( $bundle_id );

				// Container ID excluded from the discount?
				if ( count( $coupon->get_excluded_product_ids() ) && count( array_intersect( array( $bundle_id ), $coupon->get_excluded_product_ids() ) ) ) {
					$valid = false;
				}

				// Container categories excluded from the discount?
				if ( count( $coupon->get_excluded_product_categories() ) && count( array_intersect( $bundle_cats, $coupon->get_excluded_product_categories() ) ) ) {
					$valid = false;
				}

				// Container on sale and sale items excluded from discount?
				if ( $coupon->get_exclude_sale_items() && $bundle->is_on_sale() ) {
					$valid = false;
				}

			/*
			 * Otherwise, check if the bundled item is specifically excluded, and if not, consider it as eligible if its container item is eligible.
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

				if ( ! $product_excluded && $coupon->is_valid_for_product( $bundle, $container_item ) ) {
					$valid = true;
				}
			}
		}

		return $valid;
	}
}

WC_PB_Coupon::init();
