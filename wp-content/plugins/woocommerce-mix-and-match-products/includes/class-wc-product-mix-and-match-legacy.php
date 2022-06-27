<?php
/**
 * Legacy Class for deprecated product methods
 *
 * @package  WooCommerce Mix and Match Products/Classes/Products
 * @since    2.0.0
 * @version  2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Product_Mix_and_Match Class.
 *
 * The custom product type for WooCommerce.
 *
 * @uses  WC_Product
 */
class WC_Product_Mix_and_Match_Legacy extends WC_Product {

	/*
	|--------------------------------------------------------------------------
	| Deprecated methods.
	|
	--------------------------------------------------------------------------
	*/


    public function get_base_price() {
		wc_deprecated_function( __METHOD__ . '()', '1.2.0', __CLASS__ . '::get_price()' );
		return $this->get_price( 'edit' );
	}
	public function get_base_regular_price() {
		wc_deprecated_function( __METHOD__ . '()', '1.2.0', __CLASS__ . '::get_regular_price()' );
		return $this->get_regular_price( 'edit' );
	}
	public function get_base_sale_price() {
		wc_deprecated_function( __METHOD__ . '()', '1.2.0', __CLASS__ . '::get_sale_price()' );
		return $this->get_sale_price( 'edit' );
	}
	public function get_mnm_data() {
		wc_deprecated_function( __METHOD__ . '()', '1.2.0', __CLASS__ . '::get_contents()' );
		return $this->get_contents();
	}
	public function get_container_size( $context = 'view' ) {
		wc_deprecated_function( __METHOD__ . '()', '1.2.0', __CLASS__ . '::get_min_container_size()' );
		return $this->get_min_container_size();
	}
	public function maybe_sync() {
		wc_deprecated_function( __METHOD__ . '()', '1.10.0', __CLASS__ . '::sync()' );
		return $this->sync();
	}

    /**
	 * Contained product IDs getter.
	 *
	 * @since  1.2.0
	 * @deprecated 2.0.0
	 *
	 * @param  string $context
	 * @return array
	 */
	public function get_contents( $context = 'view' ) {

		wc_deprecated_function( __METHOD__ . '()', '2.0.0', __CLASS__ . '::get_child_items()' );

		$contents = array();

		foreach( $this->get_child_items( $context ) as $child_item ) {
			$child_id = $child_item->get_variation_id() ? $child_item->get_variation_id() : $child_item->get_product_id();
			$contents[ $child_id ] = array(
				'child_id'     => $child_id,
				'product_id'   => $child_item->get_product_id(),
				'variation_id' => $child_item->get_variation_id(),
			);
		}
		return $contents;
	}

	/**
	 * Get an array of available children for the current product.
	 *
     * @deprecated 2.0.0
	 * @return array
	 */
	public function get_available_children() {

		wc_deprecated_function( __METHOD__ . '()', '2.0.0', __CLASS__ . '::get_child_items( "view" )' );

		$available_children = WC_MNM_Helpers::cache_get( $this->get_id(), 'available_child_products' );

		if ( null === $available_children ) {

			$available_children = array();

			foreach( $this->get_child_items() as $child_item ) {

				$is_available = true;

				if ( has_filter( 'woocommerce_mnm_is_child_available' ) ) {
					$is_available = apply_filters( 'woocommerce_mnm_is_child_available', $is_available, $child, $this );
				};
				
				if ( $is_available ) {
					$available_children[ $child_item->get_product()->get_id() ] = $child_item->get_product();
				}
            }

			WC_MNM_Helpers::cache_set( $this->get_id(), $available_children, 'available_child_products' );

		}

		if ( has_filter( 'woocommerce_mnm_get_children' ) ) {
			/**
			 * Container's children.
			 *
			 * @param  array              $children
			 * @param  obj WC_Product     $this
			*/
			$available_children = apply_filters( 'woocommerce_mnm_get_children', $available_children, $this );
		}

		return $available_children;
	}

	/**
	 * Get the product object of one of the child items.
	 * 
	 * @deprecated 2.0.0
	 *
	 * @param  int      $child_id
	 * @return object   WC_Product or WC_Product_Variation
	 */
	public function get_child( $child_id ) {

		wc_deprecated_function( __METHOD__ . '()', '2.0.0', __CLASS__ . '::get_child_item( $item_id )' );

		$child_item = $this->get_child_item_by_product_id( $child_id );
		$child_product = $child_item ? $child_item->get_product() : false;

		/**
		 * Individual child product.
		 *
		 * @param  obj WC_Product or WC_Product_Variation  $child The child product or variation.
		 * @param  obj WC_Product                          $this
		*/
		return apply_filters( 'woocommerce_mnm_get_child', $child_product, $this );
	}


	/**
	 * Is child item available for inclusion in container.
     * 
     * @deprecated 2.0.0
	 *
	 * @param  int  $child_id
	 * @return bool
	 */
	public function is_child_available( $child_id ) {
		wc_deprecated_function( __METHOD__ . '()', '2.0.0', __CLASS__ . '::is_allowed_child_product()' );
		return in_array( $child_id, $this->is_allowed_child_product( $child_id ) );
	}

    /**
	 * Get min/max mnm price.
     * 
     * @deprecated 2.0.0
	 *
	 * @param  string $min_or_max
	 * @return mixed
	 */
	public function get_mnm_price( $min_or_max = 'min', $display = false ) {
		wc_deprecated_function( __METHOD__ . '()', '2.0.0', __CLASS__ . '::get_container_price()' );
		return $this->get_container_price( $min_or_max, $display );
	}

    /**
	 * Get min/max MnM regular price.
     * 
     * @deprecated 2.0.0
	 *
	 * @param  string $min_or_max
	 * @return mixed
	 */
	public function get_mnm_regular_price( $min_or_max = 'min', $display = false ) {
		wc_deprecated_function( __METHOD__ . '()', '2.0.0', __CLASS__ . '::container_regular_price()' );
		return $this->get_container_regular_price( $min_or_max, $display );
	}

    /**
	 * MnM price including tax.
     * 
     * @deprecated 2.0.0
	 *
	 * @return mixed
	 */
	public function get_mnm_price_including_tax( $min_or_max = 'min', $qty = 1 ) {
		wc_deprecated_function( __METHOD__ . '()', '2.0.0', __CLASS__ . '::container_price_including_tax()' );
		return $this->get_container_price_including_tax( $min_or_max, $qty );
	}

    /**
	 * Min/max MnM price excl tax.
     * 
     * @deprecated 2.0.0
	 *
	 * @return mixed
	 */
	public function get_mnm_price_excluding_tax( $min_or_max = 'min', $qty = 1 ) {
		wc_deprecated_function( __METHOD__ . '()', '2.0.0', __CLASS__ . '::get_container_price_excluding_tax()' );
		return $this->get_container_price_excluding_tax( $min_or_max, $qty );
	}

    /**
	 * Per-Item Shipping getter.
	 *
	 * @since  1.2.0
     * @deprecated 2.0.0
	 *
	 * @param  string $context
	 * @return bool
	 */
	public function get_shipped_per_product( $context = 'view' ) {
		wc_deprecated_function( __METHOD__ . '()', '2.0.0', __CLASS__ . '::get_packing_mode()' );
		return 'separate' === $this->get_prop( 'packing_mode', $context );
	}
	
	/**
	 * Contained product IDs setter.
	 *
	 * @since  1.2.0
	 * @deprecated 2.0.0
	 *
	 * @param  array  $value
	 */
	public function set_contents( $value ) {

		wc_deprecated_function( __METHOD__ . '()', '2.0.0', __CLASS__ . '::set_child_items()' );

		$new_contents = array();

		if ( is_array( $value ) ) {

			foreach ( $value as $data ) {

				$child_item   = array();
				$product_id   = $variation_id = 0;

				$product_id   = isset( $data['product_id'] ) ? intval( $data['product_id'] ) : 0;
				$variation_id = isset( $data['variation_id'] ) ? intval( $data['variation_id'] ) : 0;
				$child_id     = $variation_id > 0 ? $variation_id : $product_id;

				if ( $child_id > 0 ) {
					$new_contents[ $child_id ]['product_id']   = $product_id;
					$new_contents[ $child_id ]['variation_id'] = $variation_id;
				}
			}
		}

		$this->set_child_items( $new_contents );
	}

    /**
	 * Per-Item Shipping setter.
	 *
	 * @since  1.2.0
     * @deprecated 2.0.0
	 *
	 * @param  string  $value
	 */
	public function set_shipped_per_product( $value ) {
		wc_deprecated_function( __METHOD__ . '()', '2.0.0', __CLASS__ . '::set_packing_mode( "separate" )' );
		$value = wc_string_to_bool( $value );
		$this->set_prop( 'packing_mode', $value ? 'separate' : 'together' );
	}

	
	/**
	 * Returns whether or not the product has any child product.
	 *
	 * @deprecated 2.0.0
     * 
	 * @return bool
	 */
	public function has_children() {
		wc_deprecated_function( __METHOD__ . '()', '2.0.0', 'Use WC_Product_Mix_and_Match::has_child_items( "edit" )' );
		return sizeof( $this->get_child_items( 'edit' ) );
	}


	/**
	 * Returns whether or not the product container has any available child items.
	 *
	 * @deprecated 2.0.0
     * 
	 * @return bool
	 */
	public function has_available_children() {
		wc_deprecated_function( __METHOD__ . '()', '2.0.0', 'Use WC_Product_Mix_and_Match::has_child_items()' );
		return sizeof( $this->get_child_items() );
	}

	
	/**
	 * Returns whether or not the child products are shipped separately.
	 * 
	 * @deprecated 2.0.0
	 *
	 * @param string $context
	 * @return bool
	 */
	public function is_shipped_per_product( $context = 'view' ) {
		wc_deprecated_function( __METHOD__ . '()', '2.0.0', __CLASS__ . '::is_shipped_per_product() is removed without direct replacement. But ' . __CLASS__ . '::is_packed_together() exists as its opposite.' );
		return ! $this->is_packed_together( $context );
	}

	/**
	 * Runtime application of discount to products in an MNM container.
	 *
	 * @since  1.4.0
	 * @deprecated 2.0.0
     * 
	 * @see WC_MNM_Child_Item
	 *
	 * @param WC_Product $child
	 */
	public function maybe_apply_discount_to_child( $child ) {

		wc_deprecated_function( __METHOD__ . '()', '2.0.0', 'Handled at the item level. See: WC_MNM_Child_Item::maybe_apply_discount()' );

		if ( $child && $this->has_discount() ) {
			// Apply discount to regular price and not sale price.
			$price = apply_filters( 'woocommerce_mnm_item_discount_from_regular', true, $this ) ? $child->get_regular_price() : $child->get_price();
			$discounted_price = round( (double) $price * ( 100 - $this->get_discount() ) / 100, wc_get_rounding_precision() );
			$child->set_price( $discounted_price );
			$child->set_sale_price( $discounted_price );
		}

	}
}