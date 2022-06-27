<?php
/**
 * Mix and Match Product Data Store
 *
 * @package  WooCommerce Mix and Match Products/Data
 * @since    1.2.0
 * @version  2.0.0
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
		'_mnm_layout_override',
		'_mnm_layout_style',
		'_mnm_add_to_cart_form_location',
		'_mnm_min_container_size',
		'_mnm_max_container_size',
		'_mnm_per_product_pricing',
		'_mnm_per_product_discount',
		'_mnm_packing_mode',
		'_mnm_max_price',
		'_mnm_max_regular_price',
		'_mnm_weight_cumulative',
		'_mnm_content_source',
		'_mnm_child_category_ids',
	);

	/**
	 * Maps extended properties to meta keys.
	 *
	 * @var array
	 */
	protected $props_to_meta_keys = array(
		'min_raw_price'             => '_price',
		'min_raw_regular_price'     => '_regular_price',
		'max_raw_price'             => '_mnm_max_price',
		'max_raw_regular_price'     => '_mnm_max_regular_price',
		'price'                     => '_mnm_base_price',
		'regular_price'             => '_mnm_base_regular_price',
		'sale_price'                => '_mnm_base_sale_price',
		'layout_override'           => '_mnm_layout_override',
		'layout'                    => '_mnm_layout_style',
		'add_to_cart_form_location' => '_mnm_add_to_cart_form_location',
		'min_container_size'        => '_mnm_min_container_size',
		'max_container_size'        => '_mnm_max_container_size',
		'priced_per_product'        => '_mnm_per_product_pricing',
		'discount'                  => '_mnm_per_product_discount',
		'packing_mode'              => '_mnm_packing_mode',
		'weight_cumulative'         => '_mnm_weight_cumulative',
		'content_source'            => '_mnm_content_source',
		'child_category_ids'        => '_mnm_child_category_ids',
	);

	/**
	 * Maps global properties to options.
	 *
	 * @var array
	 */
	protected $global_props = array(
		'layout'                     => 'wc_mnm_layout',
		'add_to_cart_form_location'  => 'wc_mnm_add_to_cart_form_location',
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

		foreach ( $this->get_props_to_meta_keys() as $property => $meta_key ) {

			// Get meta value.
			$function = 'set_' . $property;

			if ( is_callable( array( $product, $function ) ) ) {

				// Get a global value for layout/location props (always use global options in customizer).
				if ( array_key_exists( $property, $this->global_props ) && ( is_customize_preview() || ! $product->has_layout_override() ) ) {
					$value = get_option( $this->global_props[$property] );
				} else {
					$value = get_post_meta( $product->get_id(), $meta_key, true );
				}

				$product->{$function}( $value );
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
		$meta_keys_to_props = array_flip( array_diff_key( $this->get_props_to_meta_keys(), array( 'price' => 1, 'min_raw_price' => 1, 'min_raw_regular_price' => 1 ) ) );
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
		$props_to_update = array_intersect( array_flip( $this->get_props_to_meta_keys() ), array( 'min_raw_price', 'min_raw_regular_price', 'max_raw_price', 'max_raw_regular_price' ) );

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


	/**
	 * Gets props to meta keys pairs
	 *
	 * @since  1.10.7
	 * @return  array
	 */
	public function get_props_to_meta_keys() {
		return $this->props_to_meta_keys;
	}

	/**
	 * Reads the child contents from the DB.
	 * 
	 * @since 2.0.0
	 *
	 * @param  int|WC_Product_Mix_and_Match  $product
	 * @return WC_MNM_Child_Item[]
	 */
	public function read_child_items( $product ) {

		$child_items = array();

		if ( 'categories' === $product->get_content_source() ) {

			$child_items_data = $this->query_child_items_by_category( $product );
		
			if ( ! empty( $child_items_data ) && function_exists( '_prime_post_caches' ) ) {
				_prime_post_caches( $child_items_data );
			}

			foreach( $child_items_data as $product_id ) {

				/**
				 * Products without a DB entry, are keyed by their product ID.
				 * @ See WC_MNM_Child_Item::get_child_item_id()
				 */
				if ( ! in_array( 'product-' . $product_id, $child_items ) ) {
					$child_items[ 'product-' . $product_id ] = new WC_MNM_Child_Item( array( 
						'product_id'   => $product_id,
						'variation_id' => 0, // Querying by category currently does not support variations.
						'container_id' => $product->get_id(),
					),
					$product );
				}
			}

	   } else {

			$child_items_data = $this->query_child_items_by_container( $product );

			if ( ! empty( $child_items_data ) && function_exists( '_prime_post_caches' ) ) {
				_prime_post_caches( $child_items_data );
			}
	
			foreach( $child_items_data as $item_key => $item_data ) {
				$child_items[$item_key] = new WC_MNM_Child_Item( $item_key, $product );
			}
	   }

		
		return $child_items;
	}

	/**
	 * Reads the allowed contents from the DB.
	 * 
	 * @since 2.0.0
	 *
	 * @param  int|WC_Product_Mix_and_Match  $product
	 * @return array() - map of child item ids => child product ids
	 */
	public function query_child_items_by_container( $product ) {

		$product_id = $product instanceof WC_Product ? $product->get_id() : absint( $product );

		global $wpdb;

		// Get from cache if available.
		$child_items = 0 < $product_id ? wp_cache_get( 'wc-mnm-child-items-' . $product_id, 'products' ) : false;

		if ( false === $child_items ) {

			$child_items = $wpdb->get_results( $wpdb->prepare( "
				SELECT items.child_item_id, items.product_id, items.container_id, items.menu_order, p.post_parent as product_parent_id
				FROM {$wpdb->prefix}wc_mnm_child_items AS items 
				INNER JOIN {$wpdb->prefix}posts as p ON items.product_id = p.ID
				WHERE items.container_id = %d
				ORDER BY items.menu_order ASC",
				$product_id
			) );

			foreach ( $child_items as $child_item ) {
				wp_cache_set( 'wc-mnm-child-item-' . $child_item->child_item_id, $child_item, 'wc-mnm-child-items' );
			}

			if ( 0 < $product_id ) {
				wp_cache_set( 'wc-mnm-child-items-' . $product_id, $child_items, 'products' );
			}
		
		}

		return ! empty( $child_items ) ? array_unique( wp_list_pluck( $child_items, 'product_id', 'child_item_id' ) ) : array();

	}

	/**
	 * Reads the allowed contents from the DB.
	 * 
	 * @since 2.0.0
	 *
	 * @param  WC_Product_Mix_and_Match  $product
	 * @return int[] child product ids
	 */
	public function query_child_items_by_category( $product ) {

		$child_items_data = array();

		$cat_ids = $product->get_child_category_ids();

		if ( ! empty( $cat_ids ) ) {

			$args = apply_filters( 'wc_mnm_query_products_by_categories_args',
				array( 
					'type'                 => WC_Mix_and_Match_Helpers::get_supported_product_types(),
					'category_id'          => (array) $cat_ids,
					'orderby'              => 'title',
					'order'                => 'ASC',
					'return'               => 'ids',
					'limit'                => -1,
					'order_by_category_id' => (array) $cat_ids,
				)
			);

			$child_items_data = wc_get_products( $args );

		}

		return $child_items_data;

	}


	/**
	 * Find the MNM products a product belongs to.
	 * 
	 * @since 2.0.0
	 *
	 * @param  int|WC_Product  $product
	 * @return array() - map of child item ids / Mix and Match product ids
	 */
	public function query_containers_by_product( $product ) {
		
		$product_id = $product instanceof WC_Product ? $product->get_id : absint( $product );

		global $wpdb;

		// Get from cache if available.
		$container_ids = 0 < $product_id ? wp_cache_get( 'wc-mnm-container-products-' . $product_id, 'products' ) : false;

		if ( false === $container_ids ) {

			$container_ids = $wpdb->get_results( $wpdb->prepare( "
				SELECT items.child_item_id, items.container_id
				FROM {$wpdb->prefix}wc_mnm_child_items AS items 
				INNER JOIN {$wpdb->prefix}posts as p ON items.product_id = p.ID
				WHERE items.product_id = %d OR p.post_parent = %d
				ORDER BY items.menu_order ASC",
				$product_id
			) );

			if ( 0 < $product_id ) {
				wp_cache_set( 'wc-mnm-container-products-' . $product_id, $container_ids, 'products' );
			}
		
		}

		return ! empty( $container_ids ) ? array_unique( wp_list_pluck( $container_ids, 'container_id', 'child_item_id' ) ) : array();

	}

	/**
	 * Clear any caches.
	 *
	 * @param WC_Product $product Product object.
	 * @since 3.0.0
	 */
	protected function clear_caches( &$product ) {
		parent::clear_caches( $product );
		wp_cache_delete( 'wc-mnm-child-items-' . $product->get_id(), 'products' );
	}

}
