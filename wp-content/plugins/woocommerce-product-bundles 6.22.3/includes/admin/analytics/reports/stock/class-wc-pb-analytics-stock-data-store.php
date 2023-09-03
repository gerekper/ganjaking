<?php
/**
 * REST API Reports bundles datastore
 *
 * @package  WooCommerce Product Bundles
 * @since    6.9.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Admin\API\Reports\TimeInterval;
use Automattic\WooCommerce\Admin\API\Reports\SqlQuery;

/**
 * WC_PB_REST_Reports_Bundles_Data_Store class.
 *
 * @version 6.12.2
 */
class WC_PB_Analytics_Stock_Data_Store extends WC_PB_Analytics_Data_Store {

	/**
	 * Table used to get the data.
	 *
	 * @var string
	 */
	protected static $table_name = 'woocommerce_bundled_items';

	/**
	 * Cache identifier.
	 *
	 * @var string
	 */
	protected $cache_key = 'bundles_stock';

	/**
	 * Mapping columns to data type to return correct response types.
	 *
	 * @var array
	 */
	protected $column_types = array(
		'bundled_item_id' => 'intval',
		'product_id'      => 'intval',
		'bundle_id'       => 'intval',
		'units_required'  => 'intval',
		// Extended.
		'name'            => 'strval',
		'bundled_name'    => 'strval',
		'permalink'       => 'strval',
		'stock_status'    => 'strval',
		'stock_quantity'  => 'intval',
		'quantity_min'    => 'intval',
		'sku'             => 'strval',
	);

	/**
	 * Extended product attributes to include in the data.
	 *
	 * @var array
	 */
	protected $extended_attributes = array(
		'name',
		'permalink',
		'stock_status',
		'manage_stock',
		'stock_quantity',
		'sku'
	);

	/**
	 * Data store context used to pass to filters.
	 *
	 * @var string
	 */
	protected $context = 'bundles_stock';

	/**
	 * Assign report columns once full table name has been assigned.
	 */
	protected function assign_report_columns() {
		$table_name           = self::get_db_table_name();
		$this->report_columns = array(
			"{$table_name}.bundled_item_id" => "{$table_name}.bundled_item_id",
			'bundle_id'                     => 'bundle_id',
			'product_id'                    => 'product_id'
		);
	}

	/**
	 * Set up all the hooks for maintaining and populating table data.
	 */
	public static function init() {
		// Sync.
	}

	/**
	 * Enriches the product data with attributes specified by the extended_attributes.
	 *
	 * @param array $bundles_data Bundle Product data.
	 * @param array $query_args  Query parameters.
	 */
	protected function include_extended_info( &$products_data, $query_args ) {
		global $wpdb;
		$product_names = array();
		foreach ( $products_data as $key => $product_data ) {
			$extended_info = new \ArrayObject();
			if ( $query_args[ 'extended_info' ] ) {

				// Get bundled item.
				$bundled_item_id   = $product_data[ 'bundled_item_id' ];
				$bundled_item = wc_pb_get_bundled_item( $bundled_item_id );
				if ( ! $bundled_item ) {
					continue;
				}

				$extended_attributes = apply_filters( 'woocommerce_rest_reports_bundles_stock_extended_attributes', $this->extended_attributes, $product_data );
				foreach ( $extended_attributes as $extended_attribute ) {

					$function = 'get_' . $extended_attribute;
					if ( is_callable( array( $bundled_item->product, $function ) ) ) {
						$value                                = $bundled_item->product->{$function}();
						$extended_info[ $extended_attribute ] = $value;
					}
				}

				$extended_info[ 'units_required' ] = absint( $bundled_item->get_quantity() );

				// Lastly, grap the bundle name, the easy way.
				$extended_info[ 'bundle_name' ]    = wp_strip_all_tags( get_the_title( $product_data[ 'bundle_id' ] ) );


				$extended_info = $this->cast_numbers( $extended_info );
			}
			$products_data[ $key ][ 'extended_info' ] = $extended_info;
		}
	}

	/**
	 * Returns the report data based on parameters supplied by the user.
	 *
	 * @param array $query_args  Query parameters.
	 * @return stdClass|WP_Error Data.
	 */
	public function get_data( $query_args ) {
		global $wpdb;

		$table_name = self::get_db_table_name();
		// These defaults are only partially applied when used via REST API, as that has its own defaults.
		$defaults   = array(
			'per_page'          => get_option( 'posts_per_page' ),
			'page'              => 1,
			'order'             => 'DESC',
			'orderby'           => 'date',
			'fields'            => '*',
			'product_includes'  => array(),
			'extended_info'     => false,
		);

		$query_args = wp_parse_args( $query_args, $defaults );

		/*
		 * We need to get the cache key here because
		 * parent::update_intervals_sql_params() modifies $query_args.
		 */
		$cache_key = $this->get_cache_key( $query_args );
		$data      = $this->get_cached_data( $cache_key );

		if ( false === $data ) {
			$this->maybe_sync_stock_data();
			$this->initialize_queries();

			$data = (object) array(
				'data'    => array(),
				'total'   => 0,
				'pages'   => 0,
				'page_no' => 0,
			);

			$selections                 = $this->selected_columns( $query_args );
			$included_products          = $this->get_included_products_array( $query_args );
			$params                     = $this->get_limit_params( $query_args );
			$this->add_sql_query_params( $query_args );

			if ( count( $included_products ) > 0 ) {
				$total_results = count( $included_products );
				$total_pages   = (int) ceil( $total_results / $params[ 'per_page' ] );

				$fields          = $this->get_fields( $query_args );
				$join_selections = $this->format_join_selections( $fields, array( 'bundle_id' ) );
				$ids_table       = $this->get_ids_table( $included_products, 'bundle_id' );

				$this->subquery->clear_sql_clause( 'select' );
				$this->subquery->add_sql_clause( 'select', $selections );
				$this->add_sql_clause( 'select', $join_selections );
				$this->add_sql_clause( 'from', '(' );
				$this->add_sql_clause( 'from', $this->subquery->get_query_statement() );
				$this->add_sql_clause( 'from', ") AS {$table_name}" );
				$this->add_sql_clause(
					'right_join',
					"RIGHT JOIN ( {$ids_table} ) AS default_results
					ON default_results.bundle_id = {$table_name}.bundle_id"
				);

				$products_query = $this->get_query_statement();

			} else {

				$count_query      = "SELECT COUNT(*) FROM (
						{$this->subquery->get_query_statement()}
					) AS tt";
				$db_records_count = (int) $wpdb->get_var(
					$count_query // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				);

				$total_results = $db_records_count;
				$total_pages   = (int) ceil( $db_records_count / $params[ 'per_page' ] );

				if ( ( $query_args[ 'page' ] < 1 || $query_args[ 'page' ] > $total_pages ) ) {
					return $data;
				}

				$this->subquery->clear_sql_clause( 'select' );
				$this->subquery->add_sql_clause( 'select', $selections );
				$this->subquery->add_sql_clause( 'order_by', $this->get_sql_clause( 'order_by' ) );
				$this->subquery->add_sql_clause( 'limit', $this->get_sql_clause( 'limit' ) );
				$products_query = $this->subquery->get_query_statement();
			}

			$product_data = $wpdb->get_results(
				$products_query, // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				ARRAY_A
			);

			if ( null === $product_data ) {
				return $data;
			}

			$this->include_extended_info( $product_data, $query_args );

			$product_data = array_map( array( $this, 'cast_numbers' ), $product_data );
			$data         = (object) array(
				'data'    => $product_data,
				'total'   => $total_results,
				'pages'   => $total_pages,
				'page_no' => (int) $query_args[ 'page' ],
			);

			$this->set_cached_data( $cache_key, $data );
		}

		return $data;
	}

	/**
	 * Updates the database query with parameters used for Products report: categories and order status.
	 *
	 * @param array $query_args Query arguments supplied by the user.
	 */
	protected function add_sql_query_params( $query_args ) {
		global $wpdb;
		$bundled_items_table = self::get_db_table_name();

		$this->add_time_period_sql_params( $query_args, $bundled_items_table );
		$this->get_limit_sql_params( $query_args );
		$this->add_order_by_sql_params( $query_args );

		$included_products = $this->get_included_products( $query_args );
		if ( $included_products ) {
			$this->add_from_sql_params( $query_args, 'outer', 'default_results.bundle_id' );
			$this->add_sql_clause( 'where', "AND _products.post_status <> 'trash'" );

			// Hint: We can "play" with the bundle id, as long as we are at the subquery level only.
			$this->subquery->add_sql_clause( 'where', "AND {$bundled_items_table}.bundle_id IN ({$included_products})" );

		} else {
			$this->add_from_sql_params( $query_args, 'inner', "{$bundled_items_table}.product_id" );
			$this->subquery->add_sql_clause( 'where', "AND _products.post_status <> 'trash'" );
		}
	}


	/**
	 * Fills FROM clause of SQL request based on user supplied parameters.
	 *
	 * @param array  $query_args Parameters supplied by the user.
	 * @param string $arg_name   Target of the JOIN sql param.
	 * @param string $id_cell    ID cell identifier, like `table_name.id_column_name`.
	 */
	protected function add_from_sql_params( $query_args, $arg_name, $id_cell ) {
		global $wpdb;

		$table_name      = self::get_db_table_name();
		$type            = 'join';
		$products_joined = false;

		// Order by product name requires extra JOIN.
		switch ( $query_args[ 'orderby' ] ) {
			case 'bundle_name':
				$join            = " JOIN {$wpdb->posts} AS _bundles ON {$table_name}.bundle_id = _bundles.ID";
				break;
			case 'product_name':
				$products_joined = true;
				$join            = " JOIN {$wpdb->posts} AS _products ON {$id_cell} = _products.ID";
				break;
			case 'units_required':
				$join            = " LEFT JOIN {$wpdb->bundled_itemmeta} AS _meta ON {$table_name}.bundled_item_id = _meta.bundled_item_id AND _meta.meta_key = 'quantity_min'";
				break;
			default:
				$join            = '';
				break;
		}

		if ( $join ) {
			if ( 'inner' === $arg_name ) {

				$this->subquery->add_sql_clause( $type, $join );
				if ( ! $products_joined ) {
					$this->subquery->add_sql_clause( $type, "JOIN {$wpdb->posts} AS _products ON {$id_cell} = _products.ID" );
				}

			} else {

				$this->add_sql_clause( $type, $join );
				if ( ! $products_joined ) {
					$this->add_sql_clause( $type, "JOIN {$wpdb->posts} AS _products ON {$id_cell} = _products.ID" );
				}
			}
		}
	}

	/*-----------------------------------------------------------------*/
	/*  Helpers.                                                       */
	/*-----------------------------------------------------------------*/

	/**
	 * Maps ordering specified by the user to columns in the database/fields in the data.
	 *
	 * @param  string  $order_by
	 * @return string
	 */
	protected function normalize_order_by( $order_by ) {
		if ( 'bundle_name' === $order_by ) {
			return "_bundles.post_title";
		}

		if ( 'product_name' === $order_by ) {
			return "_products.post_title";
		}

		if ( 'units_required' === $order_by ) {
			return 'CAST(_meta.meta_value AS unsigned)';
		}

		return $order_by;
	}

	/**
	 * Checks if any unsynced Bundles and fix it.
	 *
	 * @return void.
	 */
	protected function maybe_sync_stock_data() {

		if ( ! defined( 'WC_PB_DEBUG_STOCK_PARENT_SYNC' ) && ! defined( 'WC_PB_DEBUG_STOCK_SYNC' ) ) {

			$data_store = WC_Data_Store::load( 'product-bundle' );
			$sync_ids   = $data_store->get_bundled_items_stock_sync_status_ids( 'unsynced' );

		} elseif ( ! defined( 'WC_PB_DEBUG_STOCK_SYNC' ) ) {

			$sync_ids = WC_PB_DB::query_bundled_items( array(
				'return'          => 'id=>bundle_id',
				'meta_query'      => array(
					array(
						'key'     => 'stock_status',
						'compare' => 'NOT EXISTS'
					),
				)
			) );

		} else {

			$sync_ids = WC_PB_DB::query_bundled_items( array(
				'return' => 'id=>bundle_id'
			) );
		}

		if ( ! empty( $sync_ids ) ) {
			foreach ( $sync_ids as $id ) {
				if ( ( $product = wc_get_product( $id ) ) && $product->is_type( 'bundle' ) ) {
					$product->sync_stock();
				}
			}
		}
	}

	/**
	 * Initialize query objects.
	 */
	protected function initialize_queries() {
		global $wpdb;

		$this->clear_all_clauses();

		$table_name     = self::get_db_table_name();
		$this->subquery = new SqlQuery( $this->context . '_subquery' );

		$this->subquery->add_sql_clause( 'select', 'product_id' );
		$this->subquery->add_sql_clause( 'join', " LEFT JOIN {$wpdb->bundled_itemmeta} AS _stockmeta ON {$table_name}.bundled_item_id = _stockmeta.bundled_item_id and _stockmeta.meta_key = 'stock_status' " );
		$this->subquery->add_sql_clause( 'where', " AND _stockmeta.meta_value='out_of_stock'" );
		$this->subquery->add_sql_clause( 'from', $table_name );
	}
}
