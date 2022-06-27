<?php
/**
 * WC_PB_BS_Product class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.8.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product-related functions and filters.
 *
 * @class    WC_PB_BS_Product
 * @version  6.15.4
 */
class WC_PB_BS_Product {

	/*
	|--------------------------------------------------------------------------
	| Application layer functions.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get bundle-sells IDs for a product.
	 *
	 * @param  mixed  $product
	 * @return array
	 */
	public static function get_bundle_sell_ids( $product, $context = 'view' ) {

		$bundle_sell_ids = array();

		if ( ! ( $product instanceof WC_Product ) ) {
			$product = wc_get_product( $product );
		}

		if ( ( $product instanceof WC_Product ) && self::supports_bundle_sells( $product ) ) {

			$bundle_sell_ids = $product->get_meta( '_wc_pb_bundle_sell_ids', true );

			if ( ! empty( $bundle_sell_ids ) && is_array( $bundle_sell_ids ) ) {

				$bundle_sell_ids = array_map( 'intval', $bundle_sell_ids );

				// Clean up unsupported product types.
				foreach ( $bundle_sell_ids as $bundle_sell_index => $bundle_sell_id ) {
					$product_type = WC_Product_Factory::get_product_type( $bundle_sell_id );
					if ( ! in_array( $product_type, array( 'simple', 'subscription' ) ) ) {
						unset( $bundle_sell_ids[ $bundle_sell_index ] );
					}
				}
			}

			/**
			 * 'wc_pb_bundle_sell_ids' filter.
			 *
			 * @param  array       $bundle_sell_ids  Array of bundle-sell IDs.
			 * @param  WC_Product  $product          Product containing the bundle-sells.
			 */
			$bundle_sell_ids = 'view' === $context ? apply_filters( 'wc_pb_bundle_sell_ids', $bundle_sell_ids, $product ) : $bundle_sell_ids;
		}

		return $bundle_sell_ids;
	}

	/**
	 * Prompt/title displayed above the bundle-sells section in single-product pages.
	 *
	 * @param  mixed  $product
	 * @return string
	 */
	public static function get_bundle_sells_title( $product, $context = 'view' ) {

		$title = '';

		if ( ! ( $product instanceof WC_Product ) ) {
			$product = wc_get_product( $product );
		}

		if ( ( $product instanceof WC_Product ) && false === $product->is_type( 'bundle' ) ) {

			$title = $product->get_meta( '_wc_pb_bundle_sells_title', true );

			if ( is_null( $title ) ) {
				$title = '';
			}

			/**
			 * 'wc_pb_bundle_sells_title' filter.
			 *
			 * @param  WC_Product  $product  Product containing the bundle-sells.
			 */
			$title = 'view' === $context ? apply_filters( 'wc_pb_bundle_sells_title', $title, $product ) : $title;
		}

		return $title;
	}

	/**
	 * Bundle-sells discount.
	 *
	 * @since  6.0.0
	 *
	 * @param  mixed  $product
	 * @return string
	 */
	public static function get_bundle_sells_discount( $product, $context = 'view' ) {

		$discount = '';

		if ( 'filters' !== WC_PB_Product_Prices::get_bundled_cart_item_discount_method() ) {
			return $discount;
		}

		if ( ! ( $product instanceof WC_Product ) ) {
			$product = wc_get_product( $product );
		}

		if ( ( $product instanceof WC_Product ) && false === $product->is_type( 'bundle' ) ) {

			$discount = WC_PB_Helpers::cache_get( 'bundle_sells_discount_' . $product->get_id() );

			if ( null === $discount ) {
				$discount = $product->get_meta( '_wc_pb_bundle_sells_discount', true, 'edit' );

				if ( is_null( $discount ) ) {
					$discount = 0;
				}

				WC_PB_Helpers::cache_get( 'bundle_sells_discount_' . $product->get_id(), $discount );
			}

			/**
			 * 'wc_pb_bundle_sells_discount' filter.
			 *
			 * @param  WC_Product  $product  Product containing the bundle-sells.
			 */
			$discount = 'view' === $context ? apply_filters( 'wc_pb_bundle_sells_discount', $discount, $product ) : $discount;
		}

		return $discount;
	}

	/**
	 * Arguments used to create new bundled item data objects from bundle-sell IDs.
	 *
	 * @param  int         $bundle_sell_id  The bundle-sell ID.
	 * @param  WC_Product  $product         The parent product.
	 * @return array
	 */
	public static function get_bundle_sell_data_item_args( $bundle_sell_id, $product ) {

		$discount = self::get_bundle_sells_discount( $product );

		/**
		 * 'wc_pb_bundle_sell_data_item_args' filter.
		 *
		 * @param  int         $bundle_sell_id  Bundle-sell ID.
		 * @param  WC_Product  $product         Product containing the bundle-sell.
		 */
		return apply_filters( 'wc_pb_bundle_sell_data_item_args', array(
			'bundle_id'  => $product->get_id(),
			'product_id' => $bundle_sell_id,
			'meta_data'  => array(
				'quantity_min'         => 1,
				'quantity_max'         => 1,
				'priced_individually'  => 'yes',
				'shipped_individually' => 'yes',
				'optional'             => 'yes',
				'discount'             => $discount ? $discount : null,
				'stock_status'         => null,
				'disable_addons'       => 'yes'
			)
		), $bundle_sell_id, $product );
	}

	/**
	 * Creates a "runtime" bundle object from a list of bundle-sell IDs.
	 *
	 * @param  array       $bundle_sell_ids  Array of bundle-sell IDs.
	 * @param  WC_Product  $product          Product containing the bundle-sells.
	 * @return WC_Product_Bundle
	 */
	public static function get_bundle( $bundle_sell_ids, $product ) {

		$bundle_sell_ids    = array_map( 'intval', $bundle_sell_ids );
		$bundle             = new WC_Product_Bundle( $product );
		$bundled_data_items = array();

		foreach ( $bundle_sell_ids as $bundle_sell_id ) {

			$args = self::get_bundle_sell_data_item_args( $bundle_sell_id, $product );

			$bundled_data_items[] = $args;
		}

		$bundle->set_bundled_data_items( $bundled_data_items );

		// Set default values for Bundle properties.
		// If the database has different settings associated with the product ID, the following code resets their values.
		// The following changes do not persist in the DB due to the "runtime" nature of the $bundle object.
		$bundle->set_layout( 'default' );
		$bundle->set_add_to_cart_form_location( 'default' );
		$bundle->set_group_mode( 'parent' );

		return apply_filters( 'wc_pb_bundle_sells_dummy_bundle', $bundle );
	}

	/**
	 * Get product types that don't support bundle-sells.
	 *
	 * @since  6.13.0
	 *
	 * @return array
	 */
	public static function get_unsupported_product_types() {
		return (array) apply_filters( 'wc_pb_bundle_sells_unsupported_product_types', array( 'bundle', 'grouped', 'external' ) );
	}

	/**
	 * True if product supports bundle-sells.
	 *
	 * @since  6.13.0
	 *
	 * @param  mixed  $product
	 * @return boolean
	 */
	public static function supports_bundle_sells( $product ) {

		$is_supported = false;

		if ( ! ( $product instanceof WC_Product ) ) {
			$product = wc_get_product( $product );
		}

		if ( $product instanceof WC_Product ) {
			$is_supported = ! $product->is_type( self::get_unsupported_product_types() );
		}

		return $is_supported;

	}

}
