<?php
/**
 * Deposits product meta
 *
 * @package woocommerce-deposits
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Deposits_Product_Meta class.
 *
 * @since 1.2.0
 */
class WC_Deposits_Product_Meta {

	/**
	 * Class instance
	 *
	 * @var WC_Deposits_Product_Meta
	 */
	private static $instance;

	/**
	 * Get the class instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Gets a piece of product meta in a version agnostic way.
	 *
	 * @version 1.2.2
	 *
	 * @param  int|WC_Product $product  Product ID or object.
	 * @param  string         $meta_key Meta key.
	 * @return mixed
	 */
	public static function get_meta( $product, $meta_key ) {
		$product = wc_get_product( $product );
		if ( ! $product ) {
			return null;
		}

		$value = $product->get_meta( $meta_key, true );

		// Check if the variation inherit deposits settings from the parent product.
		if ( $product->is_type( 'variation' ) && empty( $value ) ) {
			$parent_id = $product->get_parent_id();
			$parent    = wc_get_product( $parent_id );
			$value     = $parent->get_meta( $meta_key, true );
		}

		return $value;
	}

	/**
	 * Update a piece of product meta in a version agnostic way.
	 *
	 * @version 1.2.2
	 *
	 * @param  int|WC_Product $product    Product ID or object.
	 * @param  string         $meta_key   Meta key.
	 * @param  mixed          $meta_value Meta value.
	 */
	public static function update_meta( $product, $meta_key, $meta_value ) {
		$product = wc_get_product( $product );
		if ( ! is_object( $product ) ) {
			return;
		}

		$product->update_meta_data( $meta_key, $meta_value );
		$product->save();
	}
}

WC_Deposits_Product_Meta::get_instance();
