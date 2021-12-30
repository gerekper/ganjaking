<?php
/**
 * WC_PB_DB class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Bundles DB API class.
 *
 * Product Bundles DB API for manipulating bundled item data in the database.
 *
 * @class    WC_PB_DB
 * @version  6.4.2
 */
class WC_PB_DB {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'wpdb_bundled_items_table_fix' ), 0 );
		add_action( 'switch_blog', array( __CLASS__, 'wpdb_bundled_items_table_fix' ), 0 );
	}

	/**
	 * Make WP see 'bundled_item' as a meta type.
	 */
	public static function wpdb_bundled_items_table_fix() {
		global $wpdb;
		$wpdb->bundled_itemmeta = $wpdb->prefix . 'woocommerce_bundled_itemmeta';
		$wpdb->tables[]         = 'woocommerce_bundled_itemmeta';
	}

	/*
	|--------------------------------------------------------------------------
	| Bundled Items.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Query bundled item data from the DB.
	 *
	 * @param  array  $args  {
	 *     @type  string     $return           Return array format:
	 *
	 *         - 'all': entire row casted to array,
	 *         - 'ids': bundled item ids only,
	 *         - 'id=>bundle_id': map of bundled item ids / bundle ids,
	 *         - 'id=>product_id': map of bundled item ids / bundled product ids,
	 *         - 'objects': WC_Bundled_Item_Data objects.
	 *         - 'count': count.
	 *
	 *     @type  int|array  $bundled_item_id  Bundled item id(s) in WHERE clause.
	 *     @type  int|array  $product_id       Bundled product id(s) in WHERE clause.
	 *     @type  int|array  $bundle_id        Bundle id(s) in WHERE clause.
	 *     @type  array      $order_by         ORDER BY field => order pairs.
	 *     @type  array      $meta_query       Bundled item meta query parameters, uses 'WP_Meta_Query' - see https://codex.wordpress.org/Class_Reference/WP_Meta_Query .
	 * }
	 *
	 * @return array
	 */
	public static function query_bundled_items( $args ) {

		global $wpdb;

		$args = wp_parse_args( $args, array(
			'return'          => 'all', // 'ids' | 'id=>bundle_id' | 'id=>product_id' | 'objects' | 'count'
			'bundled_item_id' => 0,
			'product_id'      => 0,
			'bundle_id'       => 0,
			'order_by'        => array( 'bundled_item_id' => 'ASC' ),
			'meta_query'      => array()
		) );

		$table = $wpdb->prefix . 'woocommerce_bundled_items';

		if ( in_array( $args[ 'return' ], array( 'ids', 'objects' ) ) ) {
			$select = $table . '.bundled_item_id';
		} elseif ( 'count' === $args[ 'return' ] ) {
			$select = 'COUNT(' . $table . '.bundled_item_id' . ')';
		} elseif ( 'id=>bundle_id' === $args[ 'return' ] ) {
			$select = $table . '.bundled_item_id, ' . $table . '.bundle_id';
		} else {
			$select = '*';
		}

		$sql      = "SELECT " . $select . " FROM {$table}";
		$join     = '';
		$where    = '';
		$order_by = '';

		$where_clauses    = array( '1=1' );
		$order_by_clauses = array();

		// WHERE clauses.

		if ( $args[ 'bundled_item_id' ] ) {
			$bundled_item_ids = array_map( 'absint', is_array( $args[ 'bundled_item_id' ] ) ? $args[ 'bundled_item_id' ] : array( $args[ 'bundled_item_id' ] ) );
			$where_clauses[]  = "{$table}.bundled_item_id IN (" . implode( ",", array_map( 'esc_sql', $bundled_item_ids ) ) . ")";
		}

		if ( $args[ 'product_id' ] ) {
			$product_ids     = array_map( 'absint', is_array( $args[ 'product_id' ] ) ? $args[ 'product_id' ] : array( $args[ 'product_id' ] ) );
			$where_clauses[] = "{$table}.product_id IN (" . implode( ',', array_map( 'esc_sql', $product_ids ) ) . ")";
		}

		if ( $args[ 'bundle_id' ] ) {
			$bundle_ids      = array_map( 'absint', is_array( $args[ 'bundle_id' ] ) ? $args[ 'bundle_id' ] : array( $args[ 'bundle_id' ] ) );
			$where_clauses[] = "{$table}.bundle_id IN (" . implode( ",", array_map( 'esc_sql', $bundle_ids ) ) . ")";
		}

		// ORDER BY clauses.

		if ( $args[ 'order_by' ] && is_array( $args[ 'order_by' ] ) ) {
			foreach ( $args[ 'order_by' ] as $what => $how ) {
				$order_by_clauses[] = $table . '.' . esc_sql( strval( $what ) ) . " " . esc_sql( strval( $how ) );
			}
		}

		$order_by_clauses = empty( $order_by_clauses ) ? array( $table . '.bundled_item_id, ASC' ) : $order_by_clauses;

		// Build SQL query components.

		$where    = ' WHERE ' . implode( ' AND ', $where_clauses );
		$order_by = ' ORDER BY ' . implode( ', ', $order_by_clauses );

		// Append meta query SQL components.

		if ( $args[ 'meta_query' ] && is_array( $args[ 'meta_query' ] ) ) {

			$meta_query = new WP_Meta_Query();

			$meta_query->parse_query_vars( $args );

			$meta_sql = $meta_query->get_sql( 'bundled_item', $table, 'bundled_item_id' );

			if ( ! empty( $meta_sql ) ) {
				// Meta query JOIN clauses.
				if ( ! empty( $meta_sql[ 'join' ] ) ) {
					$join = $meta_sql[ 'join' ];
				}
				// Meta query WHERE clauses.
				if ( ! empty( $meta_sql[ 'where' ] ) ) {
					$where .= $meta_sql[ 'where' ];
				}
			}
		}

		// Assemble and run the query.

		$sql .= $join . $where . $order_by;

		if ( 'count' === $args[ 'return' ] ) {

			$result = $wpdb->get_var( $sql );

			return $result ? $result : 0;
		}

		$results = $wpdb->get_results( $sql );

		if ( empty( $results ) ) {
			return array();
		}

		$a = array();

		if ( 'objects' === $args[ 'return' ] ) {
			foreach ( $results as $result ) {
				$a[] = self::get_bundled_item( $result->bundled_item_id );
			}
		} elseif ( 'ids' === $args[ 'return' ] ) {
			foreach ( $results as $result ) {
				$a[] = $result->bundled_item_id;
			}
		} elseif ( 'id=>bundle_id' === $args[ 'return' ] ) {
			foreach ( $results as $result ) {
				$a[ $result->bundled_item_id ] = $result->bundle_id;
			}
		} elseif ( 'id=>product_id' === $args[ 'return' ] ) {
			foreach ( $results as $result ) {
				$a[ $result->bundled_item_id ] = $result->product_id;
			}
		} else {
			foreach ( $results as $result ) {
				$a[] = (array) $result;
			}
		}

		return $a;
	}

	/**
	 * Create a bundled item in the DB.
	 *
	 * @param  array  $args
	 * @return false|int
	 */
	public static function add_bundled_item( $args ) {

		$args = wp_parse_args( $args, array(
			'bundle_id'  => 0,
			'product_id' => 0,
			'menu_order' => 0,
			'meta_data'  => array()
		) );

		if ( ! $args[ 'bundle_id' ] || 'product' !== get_post_type( $args[ 'bundle_id' ] ) ) {
			return false;
		}

		if ( ! $args[ 'product_id' ] || 'product' !== get_post_type( $args[ 'product_id' ] ) ) {
			return false;
		}

		$item = new WC_Bundled_Item_Data( array(
			'bundle_id'  => $args[ 'bundle_id' ],
			'product_id' => $args[ 'product_id' ],
			'menu_order' => $args[ 'menu_order' ],
			'meta_data'  => $args[ 'meta_data' ]
		) );

		return $item->save();
	}

	/**
	 * Get a bundled item from the DB.
	 *
	 * @param  mixed  $item
	 * @return false|WC_Bundled_Item_Data
	 */
	public static function get_bundled_item( $item ) {

		if ( is_numeric( $item ) ) {
			$item = absint( $item );
			$item = new WC_Bundled_Item_Data( $item );
		} elseif ( $item instanceof WC_Bundled_Item_Data ) {
			$item = new WC_Bundled_Item_Data( $item );
		} else {
			$item = false;
		}

		if ( ! $item || ! is_object( $item ) || ! $item->get_id() ) {
			return false;
		}

		return $item;
	}

	/**
	 * Update a bundled item in the DB.
	 *
	 * @param  mixed  $item
	 * @param  array  $data
	 * @return boolean
	 */
	public static function update_bundled_item( $item, $data ) {

		if ( is_numeric( $item ) ) {
			$item = absint( $item );
			$item = new WC_Bundled_Item_Data( $item );
		}

		if ( is_object( $item ) && $item->get_id() && $item->get_bundle_id() && ! empty( $data ) && is_array( $data ) ) {
			$item->set_all( $data );
			return $item->save();
		}

		return false;
	}

	/**
	 * Delete a bundled item from the DB.
	 *
	 * @param  mixed  $item
	 * @return void
	 */
	public static function delete_bundled_item( $item ) {
		$item = self::get_bundled_item( $item );
		if ( $item ) {
			$item->delete();
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Bundled Item Meta.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Add bundled item meta to the DB. Unique only.
	 *
	 * @access public
	 * @param  mixed  $item_id
	 * @param  mixed  $meta_key
	 * @param  mixed  $meta_value
	 * @return int
	 */
	public static function add_bundled_item_meta( $item_id, $meta_key, $meta_value ) {
		if ( $meta_id = add_metadata( 'bundled_item', $item_id, $meta_key, $meta_value, true ) ) {

			$cache_key = WC_Cache_Helper::get_cache_prefix( 'bundled_item_meta' ) . $item_id;
			wp_cache_delete( $cache_key, 'bundled_item_meta' );

			WC_PB_Core_Compatibility::invalidate_cache_group( 'bundled_data_items' );

			return $meta_id;
		}
		return 0;
	}

	/**
	 * Get bundled item meta from the DB. Unique only.
	 *
	 * @param  int     $item_id
	 * @param  string  $key
	 * @return mixed
	 */
	public static function get_bundled_item_meta( $item_id, $key = '' ) {
		return get_metadata( 'bundled_item', $item_id, $key, true );
	}

	/**
	 * Update bundled item meta in the DB. Unique only.
	 *
	 * @param  mixed   $item_id
	 * @param  string  $meta_key
	 * @param  mixed   $meta_value
	 * @param  string  $prev_value
	 * @return boolean
	 */
	public static function update_bundled_item_meta( $item_id, $meta_key, $meta_value, $prev_value = '' ) {
		if ( update_metadata( 'bundled_item', $item_id, $meta_key, $meta_value, $prev_value ) ) {

			$cache_key = WC_Cache_Helper::get_cache_prefix( 'bundled_item_meta' ) . $item_id;
			wp_cache_delete( $cache_key, 'bundled_item_meta' );

			WC_PB_Core_Compatibility::invalidate_cache_group( 'bundled_data_items' );

			return true;
		}
		return false;
	}

	/**
	 * Delete bundled item meta from the DB.
	 *
	 * @param  int      $item_id
	 * @param  string   $meta_key
	 * @param  string   $meta_value
	 * @param  boolean  $delete_all
	 * @return boolean
	 */
	public static function delete_bundled_item_meta( $item_id, $meta_key, $meta_value = '', $delete_all = false ) {
		if ( delete_metadata( 'bundled_item', $item_id, $meta_key, $meta_value, $delete_all ) ) {

			$cache_key = WC_Cache_Helper::get_cache_prefix( 'bundled_item_meta' ) . $item_id;
			wp_cache_delete( $cache_key, 'bundled_item_meta' );

			WC_PB_Core_Compatibility::invalidate_cache_group( 'bundled_data_items' );

			return true;
		}
		return false;
	}

	/*
	|--------------------------------------------------------------------------
	| Bulk operations.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Bulk update bundled item meta in the DB.
	 *
	 * @since  5.8.0
	 *
	 * @param  array   $item_ids
	 * @param  string  $meta_key
	 * @param  mixed   $meta_value
	 * @return boolean
	 */
	public static function bulk_update_bundled_item_meta( $bundled_item_ids, $meta_key, $meta_value ) {

		global $wpdb;

		if ( ! empty( $bundled_item_ids ) ) {

			$rows_updated = $wpdb->query( "
				UPDATE {$wpdb->prefix}woocommerce_bundled_itemmeta
				SET meta_value = '" . wc_clean( $meta_value ) . "'
				WHERE meta_key = '" . $meta_key . "'
				AND bundled_item_id IN ( " . implode( ',', $bundled_item_ids ) . " )
			" );

			if ( $rows_updated !== count( $bundled_item_ids ) ) {

				$rows_affected = $wpdb->get_var( "
					SELECT COUNT(meta_id) FROM {$wpdb->prefix}woocommerce_bundled_itemmeta
					WHERE meta_key = '" . $meta_key . "'
					AND bundled_item_id IN ( " . implode( ',', $bundled_item_ids ) . " )
				" );

				if ( $rows_affected !== count( $bundled_item_ids ) ) {

					$wpdb->query( "
						INSERT INTO {$wpdb->prefix}woocommerce_bundled_itemmeta (bundled_item_id, meta_key, meta_value)
						SELECT bundled_items.bundled_item_id, '" . $meta_key . "', '" . $meta_value . "' FROM {$wpdb->prefix}woocommerce_bundled_items AS bundled_items
						LEFT OUTER JOIN {$wpdb->prefix}woocommerce_bundled_itemmeta AS item_meta ON item_meta.bundled_item_id = bundled_items.bundled_item_id AND item_meta.meta_key = '" . $meta_key . "'
						WHERE item_meta.meta_key IS NULL AND bundled_items.bundled_item_id IN ( " . implode( ',', $bundled_item_ids ) . " )
					" );
				}
			}

			foreach ( $bundled_item_ids as $bundled_item_id ) {
				$cache_key = WC_Cache_Helper::get_cache_prefix( 'bundled_item_meta' ) . $bundled_item_id;
				wp_cache_delete( $cache_key, 'bundled_item_meta' );
			}
		}

		return false;
	}

	/**
	 * Flush bundled items stock meta.
	 *
	 * @since  5.8.0
	 *
	 * @param  array  $bundled_item_ids
	 */
	public static function bulk_delete_bundled_item_stock_meta( $bundled_item_ids = array() ) {

		global $wpdb;

		if ( ! empty( $bundled_item_ids ) ) {

			$wpdb->query( "
				DELETE FROM {$wpdb->prefix}woocommerce_bundled_itemmeta
				WHERE meta_key IN ( 'stock_status', 'max_stock' )
				AND bundled_item_id IN (" . implode( ',', $bundled_item_ids ) . ")
			" );

			foreach ( $bundled_item_ids as $bundled_item_id ) {
				$cache_key = WC_Cache_Helper::get_cache_prefix( 'bundled_item_meta' ) . $bundled_item_id;
				wp_cache_delete( $cache_key, 'bundled_item_meta' );
			}

		} else {

			$wpdb->query( "
				DELETE FROM {$wpdb->prefix}woocommerce_bundled_itemmeta
				WHERE meta_key IN ( 'stock_status', 'max_stock' )
			" );

			WC_PB_Core_Compatibility::invalidate_cache_group( 'bundled_item_meta' );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Deprecated methods.
	|--------------------------------------------------------------------------
	*/

	public static function delete_bundled_items_stock_meta( $map = '' ) {

		_deprecated_function( __METHOD__ . '()', '5.8.0' );

		global $wpdb;

		if ( empty( $map ) ) {

			self::bulk_delete_bundled_item_stock_meta();

			$data_store = WC_Data_Store::load( 'product-bundle' );
			$data_store->reset_bundled_items_stock_status();

		} elseif ( is_array( $map ) ) {

			$bundled_item_ids = array_map( 'absint' , array_keys( $map ) );
			$bundle_ids       = array_map( 'absint' , $map );

			self::bulk_delete_bundled_item_stock_meta( $bundled_item_ids );

			$data_store = WC_Data_Store::load( 'product-bundle' );
			$data_store->reset_bundled_items_stock_status( $bundle_ids );
		}
	}

	public static function flush_stock_cache( $where = '' ) {
		_deprecated_function( __METHOD__ . '()', '5.5.0', __CLASS__ . '::delete_bundled_items_stock_meta()' );
		return self::delete_bundled_items_stock_meta( $where );
	}
}

WC_PB_DB::init();
