<?php
/**
 * WC_Bundled_Item_Data class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bundled Item Data class.
 *
 * A container that represents a bundled item and handles CRUD - @see WC_PB_Install::get_schema(). Simplified version of @see WC_Data.
 * Could be modified to extend WC_Data in the future. For now, all required functionality is self-contained to maintain WC back-compat.
 *
 * @class    WC_Bundled_Item_Data
 * @version  6.16.0
 */

class WC_Bundled_Item_Data {

	/**
	 * Data array, with defaults.
	 *
	 * @var array
	 */
	protected $data = array(
		'bundled_item_id' => 0,
		'product_id'      => 0,
		'bundle_id'       => 0,
		'menu_order'      => 0
	);

	/**
	 * Stores meta data, defaults included.
	 * Meta keys are assumed unique by default. No meta is internal.
	 *
	 * @var array
	 */
	protected $meta_data = array(
		'quantity_min'                          => 1,
		'quantity_max'                          => 1,
		'quantity_default'                      => 1,
		'priced_individually'                   => 'no',
		'shipped_individually'                  => 'no',
		'override_title'                        => 'no',
		'title'                                 => '',
		'override_description'                  => 'no',
		'description'                           => '',
		'optional'                              => 'no',
		'hide_thumbnail'                        => 'no',
		'discount'                              => null,
		'override_variations'                   => 'no',
		'override_default_variation_attributes' => 'no',
		'allowed_variations'                    => null,
		'default_variation_attributes'          => null,
		'single_product_visibility'             => 'visible',
		'cart_visibility'                       => 'visible',
		'order_visibility'                      => 'visible',
		'single_product_price_visibility'       => 'visible',
		'cart_price_visibility'                 => 'visible',
		'order_price_visibility'                => 'visible',
		'stock_status'                          => null,
		'max_stock'                             => null
	);

	/**
	 * Sanitization function to apply to known meta values on the way in - @see sanitize_meta_value().
	 *
	 * @var array
	 */
	protected $meta_data_type_fn = array(
		'quantity_min'                          => 'absint',
		'quantity_max'                          => 'absint_if_not_empty',
		'quantity_default'                      => 'absint',
		'priced_individually'                   => 'yes_or_no',
		'shipped_individually'                  => 'yes_or_no',
		'override_title'                        => 'yes_or_no',
		'title'                                 => 'strval',
		'override_description'                  => 'yes_or_no',
		'description'                           => 'strval',
		'optional'                              => 'yes_or_no',
		'hide_thumbnail'                        => 'yes_or_no',
		'discount'                              => 'format_decimal_if_not_empty',
		'override_variations'                   => 'yes_or_no',
		'override_default_variation_attributes' => 'yes_or_no',
		'allowed_variations'                    => 'maybe_unserialize',
		'default_variation_attributes'          => 'maybe_unserialize',
		'single_product_visibility'             => 'visible_or_hidden',
		'cart_visibility'                       => 'visible_or_hidden',
		'order_visibility'                      => 'visible_or_hidden',
		'single_product_price_visibility'       => 'visible_or_hidden',
		'cart_price_visibility'                 => 'visible_or_hidden',
		'order_price_visibility'                => 'visible_or_hidden',
		'stock_status'                          => 'strval',
		'max_stock'                             => 'absint_if_not_empty'
	);

	/**
	 * Change data to JSON format.
	 *
	 * @return string
	 */
	public function __toString() {
		return json_encode( $this->get_data() );
	}

	/**
	 * Constructor.
	 *
	 * @param  int|object|array  $item  ID to load from the DB (optional) or already queried data.
	 */
	public function __construct( $item = 0 ) {
		if ( $item instanceof WC_Bundled_Item_Data ) {
			$this->set_all( $item->get_data() );
		} elseif ( is_array( $item ) ) {
			$this->set_all( $item );
		} else {
			$this->read( $item );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Returns all data for this object.
	 *
	 * @return array
	 */
	public function get_data() {
		return array_merge( $this->data, array( 'meta_data' => $this->get_meta_data() ) );
	}

	/**
	 * Get bundled item ID.
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->get_bundled_item_id();
	}

	/**
	 * Get bundled item ID.
	 *
	 * @return int
	 */
	public function get_bundled_item_id() {
		return absint( $this->data[ 'bundled_item_id' ] );
	}

	/**
	 * Get bundled product ID.
	 *
	 * @return int
	 */
	public function get_product_id() {
		return absint( $this->data[ 'product_id' ] );
	}

	/**
	 * Get product bundle ID.
	 *
	 * @return int
	 */
	public function get_bundle_id() {
		return absint( $this->data[ 'bundle_id' ] );
	}

	/**
	 * Get bundled item menu order.
	 *
	 * @return int
	 */
	public function get_menu_order() {
		return absint( $this->data[ 'menu_order' ] );
	}

	/**
	 * Get All Meta Data.
	 *
	 * @return array
	 */
	public function get_meta_data() {
		return array_filter( $this->meta_data, array( $this, 'has_meta_value' ) );
	}

	/**
	 * Cleans null value meta when getting.
	 *
	 * @param  mixed  $value
	 * @return boolean
	 */
	private function has_meta_value( $value ) {
		return ! is_null( $value );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set all data based on input array.
	 *
	 * @param  array  $data
	 */
	public function set_all( $data ) {
		foreach ( $data as $key => $value ) {
			if ( is_callable( array( $this, "set_$key" ) ) ) {
				$this->{"set_$key"}( $value );
			} else {
				$this->data[ $key ] = $value;
			}
		}
	}

	/**
	 * Set ID.
	 *
	 * @param  int  $value
	 */
	public function set_id( $value ) {
		$this->set_bundled_item_id( $value );
	}

	/**
	 * Set bundled item ID.
	 *
	 * @param  int  $value
	 */
	public function set_bundled_item_id( $value ) {
		$this->data[ 'bundled_item_id' ] = absint( $value );
	}

	/**
	 * Set bundled product ID.
	 *
	 * @param  int  $value
	 */
	public function set_product_id( $value ) {
		$this->data[ 'product_id' ] = absint( $value );
	}

	/**
	 * Set product bundle is.
	 *
	 * @param  int  $value
	 */
	public function set_bundle_id( $value ) {
		$this->data[ 'bundle_id' ] = absint( $value );
	}

	/**
	 * Set bundled item menu order.
	 *
	 * @param  int  $value
	 */
	public function set_menu_order( $value ) {
		$this->data[ 'menu_order' ] = absint( $value );
	}

	/**
	 * Set all meta data from array.
	 *
	 * @param  array  $data
	 */
	public function set_meta_data( $data ) {
		if ( ! empty( $data ) && is_array( $data ) ) {
			foreach ( $data as $key => $value ) {
				if ( $this->has_meta_value( $value ) ) {
					$this->meta_data[ $key ] = $this->sanitize_meta_value( $value, $key );
				}
			}
		}
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete data from the database.
	|
	*/

	/**
	 * Insert data into the database.
	 */
	private function create() {

		global $wpdb;

		$wpdb->insert( $wpdb->prefix . 'woocommerce_bundled_items', array(
			'product_id' => $this->get_product_id(),
			'bundle_id'  => $this->get_bundle_id(),
			'menu_order' => $this->get_menu_order()
		) );

		$this->set_id( $wpdb->insert_id );

		do_action( 'woocommerce_new_bundled_item', $this );
	}

	/**
	 * Update data in the database.
	 */
	private function update() {

		global $wpdb;

		$wpdb->update( $wpdb->prefix . 'woocommerce_bundled_items', array(
			'product_id' => $this->get_product_id(),
			'bundle_id'  => $this->get_bundle_id(),
			'menu_order' => $this->get_menu_order()
		), array( 'bundled_item_id' => $this->get_id() ) );

		do_action( 'woocommerce_update_bundled_item', $this );
	}

	/**
	 * Read from the database.
	 *
	 * @param  int  $item
	 */
	public function read( $item ) {

		global $wpdb;

		if ( is_numeric( $item ) && ! empty( $item ) ) {
			$data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_bundled_items WHERE bundled_item_id = %d LIMIT 1;", $item ) );
		} elseif ( ! empty( $item->bundled_item_id ) ) {
			$data = $item;
		} else {
			$data = false;
		}

		if ( $data ) {
			$this->set_id( $data->bundled_item_id );
			$this->set_product_id( $data->product_id );
			$this->set_bundle_id( $data->bundle_id );
			$this->set_menu_order( $data->menu_order );
			$this->read_meta_data();
		}
	}

	/**
	 * Validates before saving for sanity.
	 *
	 * @since  5.2.0
	 */
	public function validate() {

		$quantity_min     = $this->get_meta( 'quantity_min' );
		$quantity_max     = $this->get_meta( 'quantity_max' );
		$quantity_default = $this->get_meta( 'quantity_default' );

		if ( $quantity_min > $quantity_max && '' !== $quantity_max && ! is_null( $quantity_max ) ) {
			$this->update_meta( 'quantity_max', $quantity_min );
		}

		if ( $quantity_default < $quantity_min || ( '' !== $quantity_max && ! is_null( $quantity_max ) && $quantity_default > $quantity_max ) ) {
			$this->update_meta( 'quantity_default', $quantity_min );
		}
	}

	/**
	 * Save data to the database.
	 *
	 * @return int
	 */
	public function save() {

		$this->validate();

		if ( ! $this->get_id() ) {
			$this->create();
		} else {
			$this->update();
		}

		$this->save_meta_data();

		if ( $this->get_bundle_id() ) {

			// Clear WC_Bundled_Item_Data objects from cache.
			$cache_key = WC_Cache_Helper::get_cache_prefix( 'bundled_data_items' ) . $this->get_bundle_id();
			wp_cache_delete( $cache_key, 'bundled_data_items' );

			// Clear WC_Bundled_Item objects from cache.
			WC_PB_Helpers::cache_invalidate( 'wc_bundled_item_' . $this->get_id() . '_' . $this->get_bundle_id() );

		} else {

			WC_PB_Core_Compatibility::invalidate_cache_group( 'bundled_data_items' );
		}

		return $this->get_id();
	}

	/**
	 * Delete data from the database.
	 */
	public function delete() {

		if ( $this->get_id() ) {
			global $wpdb;
			do_action( 'woocommerce_before_delete_bundled_item', $this );
			$wpdb->delete( $wpdb->prefix . 'woocommerce_bundled_items', array( 'bundled_item_id' => $this->get_id() ) );
			$wpdb->delete( $wpdb->prefix . 'woocommerce_bundled_itemmeta', array( 'bundled_item_id' => $this->get_id() ) );
			do_action( 'woocommerce_delete_bundled_item', $this );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Meta methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get Meta by Key.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function get_meta( $key ) {

		$value = null;

		if ( isset( $this->meta_data[ $key ] ) ) {
			$value = $this->meta_data[ $key ];
		}

		return $value;
	}

	/**
	 * Add meta data.
	 *
	 * @param  string  $key
	 * @param  string  $value
	 */
	public function add_meta( $key, $value ) {
		$this->update_meta( $key, $value );
	}

	/**
	 * Add meta data.
	 *
	 * @param  string  $key
	 * @param  string  $value
	 */
	public function update_meta( $key, $value ) {
		if ( is_null( $value ) ) {
			$this->delete_meta( $key );
		} else {
			$this->meta_data[ $key ] = $this->sanitize_meta_value( $value, $key );
		}
	}

	/**
	 * Delete meta data.
	 *
	 * @param  array  $key
	 */
	public function delete_meta( $key ) {
		$this->meta_data[ $key ] = null;
	}

	/**
	 * Read meta data from the database.
	 */
	protected function read_meta_data() {

		$this->meta_data = array();
		$cache_loaded    = false;

		if ( ! $this->get_id() ) {
			return;
		}

		$use_cache   = ! defined( 'WC_PB_DEBUG_OBJECT_CACHE' ) && $this->get_id() && $this->get_bundle_id();
		$cache_key   = WC_Cache_Helper::get_cache_prefix( 'bundled_item_meta' ) . $this->get_id();
		$cached_meta = $use_cache ? wp_cache_get( $cache_key, 'bundled_item_meta' ) : false;

		if ( false !== $cached_meta ) {
			$this->meta_data = $cached_meta;
			$cache_loaded    = true;
		}

		if ( ! $cache_loaded ) {
			global $wpdb;
			$raw_meta_data = $wpdb->get_results( $wpdb->prepare( "
				SELECT meta_id, meta_key, meta_value
				FROM {$wpdb->prefix}woocommerce_bundled_itemmeta
				WHERE bundled_item_id = %d ORDER BY meta_id
			", $this->get_id() ) );

			foreach ( $raw_meta_data as $meta ) {
				if ( defined( 'WC_PB_DEBUG_STOCK_SYNC' ) && 'stock_status' === $meta->meta_key ) {
					continue;
				}
				$this->meta_data[ $meta->meta_key ] = $this->sanitize_meta_value( $meta->meta_value, $meta->meta_key );
			}

			// Always make the 'quantity_default' meta mirror the 'quantity_min' meta.
			if ( ! isset( $this->meta_data[ 'quantity_default' ] ) && isset( $this->meta_data[ 'quantity_min' ] ) ) {
				$this->meta_data[ 'quantity_default' ] = $this->meta_data[ 'quantity_min' ];
			}

			if ( $use_cache ) {
				wp_cache_set( $cache_key, $this->meta_data, 'bundled_item_meta' );
			}
		}
	}

	/**
	 * Update Meta Data in the database.
	 */
	protected function save_meta_data() {

		global $wpdb;

		$raw_meta_data = $wpdb->get_results( $wpdb->prepare( "
			SELECT meta_id, meta_key, meta_value
			FROM {$wpdb->prefix}woocommerce_bundled_itemmeta
			WHERE bundled_item_id = %d ORDER BY meta_id
		", $this->get_id() ) );

		$updated_meta_keys = array();

		// Update or delete meta from the db.
		if ( ! empty( $raw_meta_data ) ) {

			$invalidate_stock_status = false;

			// Invalidate stock status if the min quantity, override variations, allowed variations or optional setting change.
			foreach ( $raw_meta_data as $meta ) {
				if ( 'quantity_min' === $meta->meta_key ) {
					if ( isset( $this->meta_data[ 'quantity_min' ] ) && absint( $meta->meta_value ) !== absint( $this->meta_data[ 'quantity_min' ] ) ) {
						$invalidate_stock_status = true;
					}
				} elseif ( in_array( $meta->meta_key, array( 'override_variations', 'optional' ), true ) ) {
					if ( isset( $this->meta_data[ $meta->meta_key ] ) && $meta->meta_value !== $this->meta_data[ $meta->meta_key ] ) {
						$invalidate_stock_status = true;
					}
				} elseif ( 'allowed_variations' === $meta->meta_key ) {
					if ( isset( $this->meta_data[ 'allowed_variations' ] ) && maybe_unserialize( $meta->meta_value ) !== $this->meta_data[ 'allowed_variations' ] ) {
						$invalidate_stock_status = true;
					}
				}
			}

			if ( $invalidate_stock_status ) {
				unset( $this->meta_data[ 'stock_status' ], $this->meta_data[ 'max_stock' ] );
			}

			// Update or delete meta from the db depending on their presence.
			foreach ( $raw_meta_data as $meta ) {
				if ( isset( $this->meta_data[ $meta->meta_key ] ) && null !== $this->meta_data[ $meta->meta_key ] && ! in_array( $meta->meta_key, $updated_meta_keys ) ) {
					update_metadata_by_mid( 'bundled_item', $meta->meta_id, $this->meta_data[ $meta->meta_key ], $meta->meta_key );
					$updated_meta_keys[] = $meta->meta_key;
				} else {
					delete_metadata_by_mid( 'bundled_item', $meta->meta_id );
				}
			}
		}

		// Add any meta that weren't updated.
		$add_meta_keys = array_diff( array_keys( $this->meta_data ), $updated_meta_keys );

		foreach ( $add_meta_keys as $meta_key ) {
			if ( null !== $this->meta_data[ $meta_key ] ) {
				add_metadata( 'bundled_item', $this->get_id(), $meta_key, $this->meta_data[ $meta_key ], true );
			}
		}

		// Clear meta cache.
		$cache_key = WC_Cache_Helper::get_cache_prefix( 'bundled_item_meta' ) . $this->get_id();
		wp_cache_delete( $cache_key, 'bundled_item_meta' );

		$this->read_meta_data();
	}

	/**
	 * Meta value type sanitization on the way in.
	 *
	 * @param  mixed   $meta_value
	 * @param  string  $meta_key
	 */
	private function sanitize_meta_value( $meta_value, $meta_key ) {

		// If the key is known, apply known sanitization function.
		if ( isset( $this->meta_data_type_fn[ $meta_key ] ) ) {

			$fn = $this->meta_data_type_fn[ $meta_key ];

			if ( 'yes_or_no' === $fn ) {
				// 'no' by default.
				if ( is_bool( $meta_value ) ) {
					$meta_value = true === $meta_value ? 'yes' : 'no';
				} else {
					$meta_value = 'yes' === $meta_value ? 'yes' : 'no';
				}
			} elseif ( 'visible_or_hidden' === $fn ) {
				// 'visible' by default.
				$meta_value = 'hidden' === $meta_value ? 'hidden' : 'visible';
			} elseif ( 'absint_if_not_empty' === $fn ) {
				$meta_value = '' !== $meta_value ? absint( $meta_value ) : '';
			} elseif ( 'format_decimal_if_not_empty' === $fn ) {
				$meta_value = '' !== $meta_value ? wc_format_decimal( $meta_value ) : '';
			} elseif ( function_exists( $fn ) ) {
				$meta_value = $fn( $meta_value );
			}

		// Otherwise, always attempt to unserialize on the way in.
		} else {
			$meta_value = maybe_unserialize( $meta_value );
		}

		return $meta_value;
	}
}
