<?php
/**
 * WC_Product_Bundle_Data_Store_CPT class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Product Bundles
 * @since    5.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC Product Bundle Data Store class
 *
 * Bundle data stored as Custom Post Type. For use with the WC 2.7+ CRUD API.
 *
 * @class    WC_Product_Bundle_Data_Store_CPT
 * @version  6.3.0
 */
class WC_Product_Bundle_Data_Store_CPT extends WC_Product_Data_Store_CPT {

	/**
	 * Data stored in meta keys, but not considered "meta" for the Bundle type.
	 * @var array
	 */
	protected $extended_internal_meta_keys = array(
		'_wc_pb_group_mode',
		'_wc_pb_bundled_items_stock_status',
		'_wc_pb_base_price',
		'_wc_pb_base_regular_price',
		'_wc_pb_base_sale_price',
		'_wc_pb_layout_style',
		'_wc_pb_edit_in_cart',
		'_wc_pb_aggregate_weight',
		'_wc_pb_sold_individually_context',
		'_wc_pb_add_to_cart_form_location',
		'_wc_sw_max_price',
		'_wc_sw_max_regular_price'
	);

	/**
	 * Maps extended properties to meta keys.
	 * @var array
	 */
	protected $props_to_meta_keys = array(
		'group_mode'                 => '_wc_pb_group_mode',
		'bundled_items_stock_status' => '_wc_pb_bundled_items_stock_status',
		'price'                      => '_wc_pb_base_price',
		'regular_price'              => '_wc_pb_base_regular_price',
		'sale_price'                 => '_wc_pb_base_sale_price',
		'layout'                     => '_wc_pb_layout_style',
		'editable_in_cart'           => '_wc_pb_edit_in_cart',
		'aggregate_weight'           => '_wc_pb_aggregate_weight',
		'sold_individually_context'  => '_wc_pb_sold_individually_context',
		'add_to_cart_form_location'  => '_wc_pb_add_to_cart_form_location',
		'min_raw_price'              => '_price',
		'min_raw_regular_price'      => '_regular_price',
		'max_raw_price'              => '_wc_sw_max_price',
		'max_raw_regular_price'      => '_wc_sw_max_regular_price'
	);

	/**
	 * Callback to exclude bundle-specific meta data.
	 *
	 * @param  object  $meta
	 * @return bool
	 */
	protected function exclude_internal_meta_keys( $meta ) {
		return parent::exclude_internal_meta_keys( $meta ) && ! in_array( $meta->meta_key, $this->extended_internal_meta_keys );
	}

	/**
	 * Reads all bundle-specific post meta.
	 *
	 * @param  WC_Product_Bundle  $product
	 */
	protected function read_product_data( &$product ) {

		parent::read_product_data( $product );

		$id           = $product->get_id();
		$props_to_set = array();

		foreach ( $this->props_to_meta_keys as $property => $meta_key ) {

			// Get meta value.
			$meta_value = get_post_meta( $id, $meta_key, true );

			// Add to props array.
			$props_to_set[ $property ] = $meta_value;
		}

		// Base prices are overridden by NYP min price.
		if ( $product->is_nyp() ) {
			$props_to_set[ 'price' ]      = $props_to_set[ 'regular_price' ] = get_post_meta( $id, '_min_price', true );
			$props_to_set[ 'sale_price' ] = '';
		}

		$product->set_props( $props_to_set );
	}

	/**
	 * Writes all bundle-specific post meta.
	 *
	 * @param  WC_Product_Bundle  $product
	 * @param  boolean            $force
	 */
	protected function update_post_meta( &$product, $force = false ) {

		parent::update_post_meta( $product, $force );

		$id                 = $product->get_id();
		$meta_keys_to_props = array_flip( array_diff_key( $this->props_to_meta_keys, array( 'price' => 1, 'min_raw_price' => 1, 'min_raw_regular_price' => 1, 'max_raw_price' => 1, 'max_raw_regular_price' => 1 ) ) );
		$props_to_update    = $force ? $meta_keys_to_props : $this->get_props_to_update( $product, $meta_keys_to_props );

		foreach ( $props_to_update as $meta_key => $property ) {

			$property_get_fn = 'get_' . $property;

			// Get meta value.
			$meta_value = $product->$property_get_fn( 'edit' );

			// Sanitize it for storage.
			if ( in_array( $property, array( 'editable_in_cart', 'aggregate_weight' ) ) ) {
				$meta_value = wc_bool_to_string( $meta_value );
			}

			$updated = update_post_meta( $id, $meta_key, $meta_value );

			if ( $updated && ! in_array( $property, $this->updated_props ) ) {
				$this->updated_props[] = $property;
			}
		}
	}

	/**
	 * Handle updated meta props after updating meta data.
	 *
	 * @param  WC_Product_Bundle  $product
	 */
	protected function handle_updated_props( &$product ) {

		$id = $product->get_id();

		if ( in_array( 'date_on_sale_from', $this->updated_props ) || in_array( 'date_on_sale_to', $this->updated_props ) || in_array( 'regular_price', $this->updated_props ) || in_array( 'sale_price', $this->updated_props ) ) {
			if ( $product->is_on_sale( 'update-price' ) ) {
				update_post_meta( $id, '_wc_pb_base_price', $product->get_sale_price( 'edit' ) );
				$product->set_price( $product->get_sale_price( 'edit' ) );
			} else {
				update_post_meta( $id, '_wc_pb_base_price', $product->get_regular_price( 'edit' ) );
				$product->set_price( $product->get_regular_price( 'edit' ) );
			}
		}

		if ( in_array( 'stock_quantity', $this->updated_props ) ) {
			do_action( 'woocommerce_product_set_stock', $product );
		}

		if ( in_array( 'stock_status', $this->updated_props ) ) {
			do_action( 'woocommerce_product_set_stock_status', $product->get_id(), $product->get_stock_status(), $product );
		}

		// Update WC 3.6+ lookup table.
		if ( WC_PB_Core_Compatibility::is_wc_version_gte( '3.6' ) ) {
			if ( array_intersect( $this->updated_props, array( 'sku', 'total_sales', 'average_rating', 'stock_quantity', 'stock_status', 'manage_stock', 'downloadable', 'virtual', 'tax_status', 'tax_class' ) ) ) {
				$this->update_lookup_table( $product->get_id(), 'wc_product_meta_lookup' );
			}
		}

		// Trigger action so 3rd parties can deal with updated props.
		do_action( 'woocommerce_product_object_updated_props', $product, $this->updated_props );

		// After handling, we can reset the props array.
		$this->updated_props = array();
	}

	/**
	 * Writes the bundled items stock status meta to the DB.
	 *
	 * @param  WC_Product_Bundle  $product
	 */
	public function save_bundled_items_stock_status( &$product ) {

		$id = $product->get_id();

		$bundled_items_stock_status = $product->get_bundled_items_stock_status( 'edit' );

		if ( update_post_meta( $id, '_wc_pb_bundled_items_stock_status', $bundled_items_stock_status ) ) {

			// Update WC 3.6+ lookup table.
			if ( WC_PB_Core_Compatibility::is_wc_version_gte( '3.6' ) ) {
				$this->update_lookup_table( $product->get_id(), 'wc_product_meta_lookup' );
			}

			$resync_visibility = ! defined( 'WC_PB_DEBUG_STOCK_SYNC' ) && ! defined( 'WC_PB_DEBUG_STOCK_PARENT_SYNC' );

			if ( $resync_visibility ) {

				if ( 'instock' === $product->get_stock_status() ) {

					$modified_visibility = false;

					if ( 'instock' === $bundled_items_stock_status ) {
						$modified_visibility = ! is_wp_error( wp_remove_object_terms( $id, 'outofstock', 'product_visibility' ) );
					} else {
						$modified_visibility = ! is_wp_error( wp_set_post_terms( $id, 'outofstock', 'product_visibility', true ) );
					}

					if ( $modified_visibility ) {
						delete_transient( 'wc_featured_products' );
						do_action( 'woocommerce_product_set_visibility', $product->get_id(), $product->get_catalog_visibility() );
					}
				}
			}

			do_action( 'woocommerce_product_object_updated_props', $product, array( 'bundled_items_stock_status' ) );
		}
	}

	/**
	 * Get data to save to a lookup table.
	 *
	 * @since  4.0.0
	 *
	 * @param  int     $id
	 * @param  string  $table
	 * @return array
	 */
	protected function get_data_for_lookup_table( $id, $table ) {

		if ( 'wc_product_meta_lookup' === $table ) {

			$min_price_meta = (array) get_post_meta( $id, '_price', false );
			$max_price_meta = (array) get_post_meta( $id, '_wc_sw_max_price', false );

			$manage_stock = get_post_meta( $id, '_manage_stock', true );
			$stock        = 'yes' === $manage_stock ? wc_stock_amount( get_post_meta( $id, '_stock', true ) ) : null;
			$price        = wc_format_decimal( get_post_meta( $id, '_price', true ) );
			$sale_price   = wc_format_decimal( get_post_meta( $id, '_sale_price', true ) );

			// If the children don't have enough stock, the parent is seen as out of stock in the lookup table.
			$stock_status = 'outofstock' === get_post_meta( $id, '_wc_pb_bundled_items_stock_status', true ) ? 'outofstock' : get_post_meta( $id, '_stock_status', true );

			$data = array(
				'product_id'     => absint( $id ),
				'sku'            => get_post_meta( $id, '_sku', true ),
				'virtual'        => 'yes' === get_post_meta( $id, '_virtual', true ) ? 1 : 0,
				'downloadable'   => 'yes' === get_post_meta( $id, '_downloadable', true ) ? 1 : 0,
				'min_price'      => reset( $min_price_meta ),
				'max_price'      => end( $max_price_meta ),
				'onsale'         => $sale_price && $price === $sale_price ? 1 : 0,
				'stock_quantity' => $stock,
				'stock_status'   => $stock_status,
				'rating_count'   => array_sum( (array) get_post_meta( $id, '_wc_rating_count', true ) ),
				'average_rating' => get_post_meta( $id, '_wc_average_rating', true ),
				'total_sales'    => get_post_meta( $id, 'total_sales', true )
			);

			if ( WC_PB_Core_Compatibility::is_wc_version_gte( '4.0' ) ) {
				$data = array_merge( $data, array(
					'tax_status' => get_post_meta( $id, '_tax_status', true ),
					'tax_class'  => get_post_meta( $id, '_tax_class', true )
				) );
			}

			return $data;
		}

		return array();
	}

	/**
	 * Writes bundle raw price meta to the DB.
	 *
	 * @param  WC_Product_Bundle  $product
	 */
	public function save_raw_prices( &$product ) {

		if ( defined( 'WC_PB_UPDATING' ) ) {
			return;
		}

		/**
		 * 'woocommerce_bundles_update_price_meta' filter.
		 *
		 * Use this to prevent bundle min/max raw price meta from being updated.
		 *
		 * @param  boolean            $update
		 * @param  WC_Product_Bundle  $this
		 */
		$update_raw_price_meta = apply_filters( 'woocommerce_bundles_update_price_meta', true, $product );

		if ( ! $update_raw_price_meta ) {
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
			if ( WC_PB_Core_Compatibility::is_wc_version_gte( '3.6' ) ) {
				$this->update_lookup_table( $product->get_id(), 'wc_product_meta_lookup' );
			}

			do_action( 'woocommerce_product_object_updated_props', $product, $updated_props );
		}
	}

	/**
	 * Sets the bundled items stock status meta of the specified IDs to the 'unsynced' value.
	 *
	 * @param  array  $bundle_ids
	 * @return void
	 */
	public function reset_bundled_items_stock_status( $bundle_ids = array() ) {

		global $wpdb;

		if ( empty( $bundle_ids ) ) {

			$wpdb->query( "
				UPDATE {$wpdb->postmeta}
				SET meta_value = 'unsynced'
				WHERE meta_key = '_wc_pb_bundled_items_stock_status'
			" );

			WC_PB_Core_Compatibility::invalidate_cache_group( 'bundled_data_items' );

		} else {

			$wpdb->query( "
				UPDATE {$wpdb->postmeta}
				SET meta_value = 'unsynced'
				WHERE meta_key = '_wc_pb_bundled_items_stock_status'
				AND post_id IN (" . implode( ',', $bundle_ids ) . ")
			" );

			foreach ( $bundle_ids as $bundle_id ) {
				wp_cache_delete( $bundle_id, 'post_meta' );
			}

			foreach ( $bundle_ids as $bundle_id ) {
				$cache_key = WC_Cache_Helper::get_cache_prefix( 'bundled_data_items' ) . $bundle_id;
				wp_cache_delete( $cache_key, 'bundled_data_items' );
			}
		}
	}

	/**
	 * Deletes the bundled items stock status meta of the specified IDs.
	 *
	 * @param  array  $ids
	 * @return void
	 */
	public function delete_bundled_items_stock_status( $ids ) {

		global $wpdb;

		if ( ! empty( $ids ) ) {
			$wpdb->query( "
				DELETE FROM {$wpdb->postmeta}
				WHERE meta_key = '_wc_pb_bundled_items_stock_status'
				AND post_id IN (" . implode( ',', $ids ) . ")
			" );

			foreach ( $ids as $id ) {
				wp_cache_delete( $id, 'post_meta' );
			}
		}
	}

	/**
	 * Gets bundle IDs having a bundled items stock status.
	 *
	 * @return array
	 */
	public function get_bundled_items_stock_status_ids( $status ) {

		global $wpdb;

		$results = $wpdb->get_results( "
			SELECT meta.post_id as id FROM {$wpdb->postmeta} AS meta
			WHERE meta.meta_key = '_wc_pb_bundled_items_stock_status' AND meta.meta_value = '$status'
			GROUP BY meta.post_id;
		" );

		return is_array( $results ) ? wp_list_pluck( $results, 'id' ) : array();
	}

	/**
	 * Use 'WP_Query' to preload product data from the 'posts' table.
	 * Useful when we know we are going to call 'wc_get_product' against a list of IDs.
	 *
	 * @since  5.5.3
	 *
	 * @param  array  $ids
	 * @return void
	 */
	public function preload_bundled_product_data( $ids ) {

		if ( empty( $ids ) ) {
			return;
		}

		$cache_key = 'wc_bundled_product_db_data_' . md5( json_encode( $ids ) );
		$data      = WC_PB_Helpers::cache_get( $cache_key );

		if ( null === $data ) {

			$data = new WP_Query( array(
				'post_type' => 'product',
				'nopaging'  => true,
				'post__in'  => $ids
			) );

			WC_PB_Helpers::cache_set( $cache_key, $data );
		}
	}
}
