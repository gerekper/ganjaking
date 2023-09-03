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
 * WC_PB_Analytics_Revenue_Data_Store class.
 *
 * @version 6.17.2
 */
class WC_PB_Analytics_Revenue_Data_Store extends WC_PB_Analytics_Data_Store {

	/**
	 * Table used to get the data.
	 *
	 * @var string
	 */
	protected static $table_name = 'wc_order_bundle_lookup';

	/**
	 * Cache identifier.
	 *
	 * @var string
	 */
	protected $cache_key = 'bundles_revenue';

	/**
	 * Mapping columns to data type to return correct response types.
	 *
	 * @var array
	 */
	protected $column_types = array(
		'date_start'         => 'strval',
		'date_end'           => 'strval',
		'product_id'         => 'intval',
		'items_sold'         => 'intval',
		'bundled_items_sold' => 'intval',
		'net_revenue'        => 'floatval',
		'orders_count'       => 'intval',
		// Extended attributes.
		'name'               => 'strval',
		'price'              => 'floatval',
		'image'              => 'strval',
		'permalink'          => 'strval',
		'stock_status'       => 'strval',
		'sku'                => 'strval',
	);

	/**
	 * Extended product attributes to include in the data.
	 *
	 * @var array
	 */
	protected $extended_attributes = array(
		'name',
		'price',
		'image',
		'permalink',
		'stock_status',
		'sku'
	);

	/**
	 * Data store context used to pass to filters.
	 *
	 * @var string
	 */
	protected $context = 'bundles';

	/**
	 * Assign report columns once full table name has been assigned.
	 */
	protected function assign_report_columns() {
		$table_name           = self::get_db_table_name();
		$this->report_columns = array(
			'product_id'         => 'bundle_id as product_id',
			'items_sold'         => "SUM( CASE WHEN product_id = bundle_id THEN {$table_name}.product_qty END ) as items_sold",
			'bundled_items_sold' => "SUM( CASE WHEN product_gross_revenue >= 0 AND product_id <> bundle_id THEN {$table_name}.product_qty END ) as bundled_items_sold",
			'net_revenue'        => 'SUM( product_net_revenue ) AS net_revenue',
			'orders_count'       => "COUNT( DISTINCT ( CASE WHEN product_gross_revenue >= 0 THEN {$table_name}.order_id END ) ) as orders_count",
		);
	}

	/*
	 * Set up all the hooks for maintaining and populating table data.
	 */
	public static function init() {
		// @see WC_PB_Admin_Analytics_Sync
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

		$type = 'join';
		// Order by product name requires extra JOIN.
		switch ( $query_args[ 'orderby' ] ) {
			case 'product_name':
				$join = " JOIN {$wpdb->posts} AS _products ON {$id_cell} = _products.ID";
				break;
			case 'sku':
				$join = " LEFT JOIN {$wpdb->postmeta} AS postmeta ON {$id_cell} = postmeta.post_id AND postmeta.meta_key = '_sku'";
				break;
			default:
				$join = '';
				break;
		}
		if ( $join ) {
			if ( 'inner' === $arg_name ) {
				$this->subquery->add_sql_clause( $type, $join );
			} else {
				$this->add_sql_clause( $type, $join );
			}
		}
	}

	/**
	 * Updates the database query with parameters used for Products report: categories and order status.
	 *
	 * @param array $query_args Query arguments supplied by the user.
	 */
	protected function add_sql_query_params( $query_args ) {
		global $wpdb;
		$order_product_lookup_table = self::get_db_table_name();

		$this->add_time_period_sql_params( $query_args, $order_product_lookup_table );
		$this->get_limit_sql_params( $query_args );
		$this->add_order_by_sql_params( $query_args );

		$included_products = $this->get_included_products( $query_args );
		if ( $included_products ) {
			$this->add_from_sql_params( $query_args, 'outer', 'default_results.product_id' );

			// Hint: We can "play" with the bundle id, as long as we are at the subquery level only.
			$this->subquery->add_sql_clause( 'where', "AND {$order_product_lookup_table}.bundle_id IN ({$included_products})" );

		} else {
			$this->add_from_sql_params( $query_args, 'inner', "{$order_product_lookup_table}.bundle_id" );
		}

		// Hint: We could add a bundled item id clause here, but we can't SUM the items_sold in a single query. We may need to revisit and and a UNION helper in such cases.

		$order_status_filter = $this->get_status_subquery( $query_args );
		if ( $order_status_filter ) {
			$this->subquery->add_sql_clause( 'join', "JOIN {$wpdb->prefix}wc_order_stats ON {$order_product_lookup_table}.order_id = {$wpdb->prefix}wc_order_stats.order_id" );
			$this->subquery->add_sql_clause( 'where', "AND ( {$order_status_filter} )" );
		}
	}

	/**
	 * Maps ordering specified by the user to columns in the database/fields in the data.
	 *
	 * @param string $order_by Sorting criterion.
	 * @return string
	 */
	protected function normalize_order_by( $order_by ) {
		if ( 'date' === $order_by ) {
			return self::get_db_table_name() . '.date_created';
		}
		if ( 'product_name' === $order_by ) {
			return 'post_title';
		}
		if ( 'sku' === $order_by ) {
			return 'meta_value';
		}
		return $order_by;
	}

	/**
	 * Enriches the product data with attributes specified by the extended_attributes.
	 *
	 * @param array $products_data Product data.
	 * @param array $query_args  Query parameters.
	 */
	protected function include_extended_info( &$products_data, $query_args ) {
		global $wpdb;
		$product_names = array();

		foreach ( $products_data as $key => $product_data ) {
			$extended_info = new \ArrayObject();
			if ( $query_args[ 'extended_info' ] ) {
				$product_id = $product_data[ 'product_id' ];
				$product    = wc_get_product( $product_id );
				// Product was deleted or changed type.
				if ( ! is_a( $product, 'WC_Product' ) || ! $product->is_type('bundle') ) {
					if ( ! isset( $product_names[ $product_id ] ) ) {
						$product_names[ $product_id ] = $wpdb->get_var(
							$wpdb->prepare(
								"SELECT i.order_item_name
								FROM {$wpdb->prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta m
								WHERE i.order_item_id = m.order_item_id
								AND m.meta_key = '_product_id'
								AND m.meta_value = %s
								ORDER BY i.order_item_id DESC
								LIMIT 1",
								$product_id
							)
						);
					}

					/* translators: %s is product name */
					$products_data[ $key ][ 'extended_info' ][ 'name' ] = $product_names[ $product_id ] ? sprintf( __( '%s (Deleted)', 'woocommerce-product-bundles' ), $product_names[ $product_id ] ) : __( '(Deleted)', 'woocommerce-product-bundles' );
					continue;
				}

				$extended_attributes = apply_filters( 'woocommerce_rest_reports_bundles_extended_attributes', $this->extended_attributes, $product_data );
				foreach ( $extended_attributes as $extended_attribute ) {

					if ( 'stock_status' === $extended_attribute ) {

						$stock_status = $product->get_stock_status();
						if ( $product->is_parent_in_stock() && ( 'outofstock' === $product->get_bundled_items_stock_status() || $product->contains( 'out_of_stock_strict' ) ) ) {
							$stock_status = 'insufficientstock';
						}

						$extended_info[ $extended_attribute ] = $stock_status;
					} else {

						$function = 'get_' . $extended_attribute;
						if ( is_callable( array( $product, $function ) ) ) {
							$value                                = $product->{$function}();
							$extended_info[ $extended_attribute ] = $value;
						}
					}
				}

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
			'before'            => TimeInterval::default_before(),
			'after'             => TimeInterval::default_after(),
			'fields'            => '*',
			'product_includes'  => array(),
			'extended_info'     => false,
		);
		$query_args = wp_parse_args( $query_args, $defaults );
		$this->normalize_timezones( $query_args, $defaults );

		/*
		 * We need to get the cache key here because
		 * parent::update_intervals_sql_params() modifies $query_args.
		 */
		$cache_key = $this->get_cache_key( $query_args );
		$data      = $this->get_cached_data( $cache_key );

		if ( false === $data ) {

			$this->initialize_queries();

			$data = (object) array(
				'data'    => array(),
				'total'   => 0,
				'pages'   => 0,
				'page_no' => 0,
			);

			$selections        = $this->selected_columns( $query_args );
			$included_products = $this->get_included_products_array( $query_args );
			$params            = $this->get_limit_params( $query_args );
			$this->add_sql_query_params( $query_args );

			if ( count( $included_products ) > 0 ) {
				$total_results = count( $included_products );
				$total_pages   = (int) ceil( $total_results / $params[ 'per_page' ] );

				if ( 'date' === $query_args[ 'orderby' ] ) {
					$selections .= ", {$table_name}.date_created";
				}

				$fields          = $this->get_fields( $query_args );
				$join_selections = $this->format_join_selections( $fields, array( 'product_id' ) );
				$ids_table       = $this->get_ids_table( $included_products, 'product_id' );

				$this->subquery->clear_sql_clause( 'select' );
				$this->subquery->add_sql_clause( 'select', $selections );
				$this->add_sql_clause( 'select', $join_selections );
				$this->add_sql_clause( 'from', '(' );
				$this->add_sql_clause( 'from', $this->subquery->get_query_statement() );
				$this->add_sql_clause( 'from', ") AS {$table_name}" );
				$this->add_sql_clause(
					'right_join',
					"RIGHT JOIN ( {$ids_table} ) AS default_results
					ON default_results.product_id = {$table_name}.product_id"
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
	 * Initialize query objects.
	 */
	protected function initialize_queries() {
		$this->clear_all_clauses();
		$this->subquery = new SqlQuery( $this->context . '_subquery' );
		$this->subquery->add_sql_clause( 'select', 'bundle_id' );
		$this->subquery->add_sql_clause( 'from', self::get_db_table_name() );
		$this->subquery->add_sql_clause( 'group_by', 'bundle_id' );
	}
}
