<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Deposits_Product_Meta class.
 *
 * @since 1.2.0
 */
class WC_Deposits_Product_Meta {

	/** @var object Class Instance */
	private static $instance;

	/**
	 * Get the class instance.
	 */
	public static function get_instance() {
		return null === self::$instance ? ( self::$instance = new self ) : self::$instance;
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

		// For variation, meta is stored in parent product.
		if ( $product->is_type( 'variation' ) ) {
			$product_id = $product->get_parent_id();
		} else {
			$product_id = $product->get_id();
		}

		// Retrieve parent product instance.
		if ( $product->is_type( 'variation' ) ) {
			$product = wc_get_product( $product_id );
		}
		return $product->get_meta( $meta_key, true );
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
