<?php
/**
 * Mix and Match Product Data Store
 *
 * @author   SomewhereWarm
 * @category Class
 * @package  WooCommerce Mix and Match Products/Data
 * @since    1.2.0
 * @version  1.4.3
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Product_MNM_Data_Store_CPT Class.
 *
 * MnM data stored as Custom Post Type. For use with the WC 3.0+ CRUD API.
 *
 * @uses  WC_Product_Data_Store_CPT
 */
class WC_Product_MNM_Data_Store_CPT extends WC_Product_Data_Store_CPT {

	/**
	 * Data stored in meta keys, but not considered "meta" for the MnM type.
	 * 
	 * @var array
	 */
	protected $extended_internal_meta_keys = array(
		'_mnm_base_price',
		'_mnm_base_regular_price',
		'_mnm_base_sale_price',
		'_mnm_layout_style',
		'_mnm_add_to_cart_form_location',
		'_mnm_min_container_size',
		'_mnm_max_container_size',
		'_mnm_data',
		'_mnm_per_product_pricing',
		'_mnm_per_product_discount',
		'_mnm_per_product_shipping',
		'_mnm_max_price',
		'_mnm_max_regular_price'
	);

	/**
	 * Maps extended properties to meta keys.
	 * 
	 * @var array
	 */
	protected $props_to_meta_keys = array(
		'min_raw_price'         => '_price',
		'min_raw_regular_price' => '_regular_price',
		'max_raw_price'         => '_mnm_max_price',
		'max_raw_regular_price' => '_mnm_max_regular_price',
		'price'                 => '_mnm_base_price',
		'regular_price'         => '_mnm_base_regular_price',
		'sale_price'            => '_mnm_base_sale_price',
		'layout'                     => '_mnm_layout_style',
		'add_to_cart_form_location'  => '_mnm_add_to_cart_form_location',
		'min_container_size'    => '_mnm_min_container_size',
		'max_container_size'    => '_mnm_max_container_size',
		'contents'              => '_mnm_data',
		'priced_per_product'    => '_mnm_per_product_pricing',
		'discount'				=> '_mnm_per_product_discount',
		'shipped_per_product'   => '_mnm_per_product_shipping'
	);

	/**
	 * Callback to exclude MnM-specific meta data.
	 *
	 * @param  object  $meta
	 * @return bool
	 */
	protected function exclude_internal_meta_keys( $meta ) {
		return parent::exclude_internal_meta_keys( $meta ) && ! in_array( $meta->meta_key, $this->extended_internal_meta_keys );
	}

	/**
	 * Reads all MnM-specific post meta.
	 *
	 * @param  WC_Product_Mix_and_Match  $product
	 */
	protected function read_extra_data( &$product ) {

		foreach ( $this->props_to_meta_keys as $property => $meta_key ) {

			// Get meta value.
			$function = 'set_' . $property;
			if ( is_callable( array( $product, $function ) ) ) {
				$product->{$function}( get_post_meta( $product->get_id(), $meta_key, true ) );
			}

		}

		// Base prices are overridden by NYP min price.
		if ( $product->is_nyp() ) {
			$min_price = $product->get_meta( '_min_price', true, 'edit' );
			$product->set_price( $min_price );
			$product->set_regular_price( $min_price );
			$product->set_sale_price( '' );
		}

	}

	/**
	 * Writes all MnM-specific post meta.
	 *
	 * @param  WC_Product_Mix_and_Match  $product
	 * @param  bool                   $force
	 */
	protected function update_post_meta( &$product, $force = false ) {

		$this->extra_data_saved = true;

		parent::update_post_meta( $product, $force );

		$id                 = $product->get_id();
		$meta_keys_to_props = array_flip( array_diff_key( $this->props_to_meta_keys, array( 'price' => 1, 'min_raw_price' => 1, 'min_raw_regular_price' => 1 ) ) );
		$props_to_update    = $force ? $meta_keys_to_props : $this->get_props_to_update( $product, $meta_keys_to_props );

		foreach ( $props_to_update as $meta_key => $property ) {

			$property_get_fn = 'get_' . $property;

			// Get meta value.
			$meta_value = $product->$property_get_fn( 'edit' );

			// Sanitize bool for storage.
			if ( is_bool( $meta_value ) ) {
				$meta_value = wc_bool_to_string( $meta_value );
			}

			if ( update_post_meta( $id, $meta_key, $meta_value ) && ! in_array( $property, $this->updated_props ) ) {
				$this->updated_props[] = $meta_key;
			}
		}
	}

	/**
	 * Handle updated meta props after updating meta data.
	 *
	 * @param  WC_Product_Mix_and_Match  $product
	 */
	protected function handle_updated_props( &$product ) {

		$id = $product->get_id();

		if ( in_array( 'date_on_sale_from', $this->updated_props ) || in_array( 'date_on_sale_to', $this->updated_props ) || in_array( 'regular_price', $this->updated_props ) || in_array( 'sale_price', $this->updated_props ) || ! metadata_exists( 'post', $product->get_id(), '_mnm_base_price' ) ) {
			if ( $product->is_on_sale( 'update-price' ) ) {
				update_post_meta( $id, '_mnm_base_price', $product->get_sale_price( 'edit' ) );
				$product->set_price( $product->get_sale_price( 'edit' ) );
			} else {
				update_post_meta( $id, '_mnm_base_price', $product->get_regular_price( 'edit' ) );
				$product->set_price( $product->get_regular_price( 'edit' ) );
			}
		}

		if ( in_array( 'stock_quantity', $this->updated_props ) ) {
			/**
			 * woocommerce_product_set_stock hook.
			 *
			 * @param  obj $product WC_Product
			 */
			do_action( 'woocommerce_product_set_stock', $product );
		}

		if ( in_array( 'stock_status', $this->updated_props ) ) {
			/**
			 * woocommerce_product_set_stock_status hook.
			 *
			 * @param  int $product_id
			 * @param  str $stock_status
			 * @param  obj $product WC_Product
			 */
			do_action( 'woocommerce_product_set_stock_status', $product->get_id(), $product->get_stock_status(), $product );
		}

		// Update WC 3.6+ lookup table.
		if ( WC_MNM_Core_Compatibility::is_wc_version_gte( '3.6' ) ) {
			if ( array_intersect( $this->updated_props, array( 'sku', 'total_sales', 'average_rating', 'stock_quantity', 'stock_status', 'manage_stock', 'downloadable', 'virtual' ) ) ) {
				$this->update_lookup_table( $product->get_id(), 'wc_product_meta_lookup' );
			}
		}

		/**
		 * woocommerce_product_object_updated_props hook.
		 *
		 * Trigger action so 3rd parties can deal with updated props.
		 *
		 * @param  obj $product WC_Product
		 * @param  array $updated_props
		 */
		do_action( 'woocommerce_product_object_updated_props', $product, $this->updated_props );

		// After handling, we can reset the props array.
		$this->updated_props = array();
	}

	/**
	 * Get data to save to a lookup table.
	 *
	 * @since  1.4.3
	 *
	 * @param  int     $id
	 * @param  string  $table
	 * @return array
	 */
	protected function get_data_for_lookup_table( $id, $table ) {

		if ( 'wc_product_meta_lookup' === $table ) {

			$min_price_meta   = (array) get_post_meta( $id, '_price', false );
			$max_price_meta   = (array) get_post_meta( $id, '_mnm_max_price', false );
			$manage_stock = get_post_meta( $id, '_manage_stock', true );
			$stock        = 'yes' === $manage_stock ? wc_stock_amount( get_post_meta( $id, '_stock', true ) ) : null;
			$price        = wc_format_decimal( get_post_meta( $id, '_price', true ) );
			$sale_price   = wc_format_decimal( get_post_meta( $id, '_sale_price', true ) );

			return array(
				'product_id'     => absint( $id ),
				'sku'            => get_post_meta( $id, '_sku', true ),
				'virtual'        => 'yes' === get_post_meta( $id, '_virtual', true ) ? 1 : 0,
				'downloadable'   => 'yes' === get_post_meta( $id, '_downloadable', true ) ? 1 : 0,
				'min_price'      => reset( $min_price_meta ),
				'max_price'      => end( $max_price_meta ),
				'onsale'         => $sale_price && $price === $sale_price ? 1 : 0,
				'stock_quantity' => $stock,
				'stock_status'   => get_post_meta( $id, '_stock_status', true ),
				'rating_count'   => array_sum( (array) get_post_meta( $id, '_wc_rating_count', true ) ),
				'average_rating' => get_post_meta( $id, '_wc_average_rating', true ),
				'total_sales'    => get_post_meta( $id, 'total_sales', true ),
			);
		}

		return array();
	}

	/**
	 * Writes MnM raw price meta to the DB.
	 *
	 * @param  WC_Product_Mix_and_Match  $product
	 */
	public function update_raw_prices( &$product ) {

		if ( defined( 'WC_MNM_UPDATING' ) ) {
			return;
		}

		$id = $product->get_id();

		$updated_props   = array();
		$props_to_update = array_intersect( array_flip( $this->props_to_meta_keys ), array( 'min_raw_price', 'min_raw_regular_price', 'max_raw_price', 'max_raw_regular_price' ) );

		foreach ( $props_to_update as $meta_key => $property ) {

			$property_get_fn = 'get_' . $property;
			$meta_value      = $product->$property_get_fn( 'edit' );

			if ( update_post_meta( $id, $meta_key, $meta_value ) ) {
				$updated_props[] = $property;
			}
		}

		if ( ! empty( $updated_props ) ) {

			$sale_price_changed = false;

			// Update sale price.
			if ( $product->is_on_sale( 'edit' ) ) {
				$sale_price_changed = update_post_meta( $id, '_sale_price', $product->get_min_raw_price( 'edit' ) );
			} else {
				$sale_price_changed = update_post_meta( $id, '_sale_price', '' );
			}

			// Delete on-sale transient.
			if ( $sale_price_changed ) {
				delete_transient( 'wc_products_onsale' );
			}

			// Update WC 3.6+ lookup table.
			if ( WC_MNM_Core_Compatibility::is_wc_version_gte( '3.6' ) ) {
				$this->update_lookup_table( $product->get_id(), 'wc_product_meta_lookup' );
			}

			do_action( 'woocommerce_product_object_updated_props', $product, $updated_props );
		}

	}
}
