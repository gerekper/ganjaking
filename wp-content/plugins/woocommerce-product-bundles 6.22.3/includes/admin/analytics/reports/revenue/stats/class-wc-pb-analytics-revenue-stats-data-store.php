<?php
/**
 * REST API Reports Stats data store
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
 * WC_PB_Analytics_Revenue_Stats_Data_Store class.
 *
 * @version 6.9.0
 */
class WC_PB_Analytics_Revenue_Stats_Data_Store extends WC_PB_Analytics_Revenue_Data_Store {

	/**
	 * Cache identifier.
	 *
	 * @var string
	 */
	protected $cache_key = 'bundles_revenue_stats';

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
		'products_count'     => 'intval',
	);

	/**
	 * Data store context used to pass to filters.
	 *
	 * @var string
	 */
	protected $context = 'bundles_stats';

	/**
	 * Assign report columns once full table name has been assigned.
	 */
	protected function assign_report_columns() {
		$table_name           = self::get_db_table_name();

		// API fields mapped to sql SELECT statements.
		$this->report_columns = array(
			'items_sold'         => "SUM( CASE WHEN product_id = bundle_id THEN {$table_name}.product_qty END ) as items_sold",
			'bundled_items_sold' => "SUM( CASE WHEN product_gross_revenue >= 0 AND product_id <> bundle_id THEN {$table_name}.product_qty END ) as bundled_items_sold",
			'net_revenue'        => 'SUM(product_net_revenue) AS net_revenue',
			'orders_count'       => "COUNT( DISTINCT ( CASE WHEN product_gross_revenue >= 0 THEN {$table_name}.order_id END ) ) as orders_count",
			'products_count'     => 'COUNT(DISTINCT bundle_id) as products_count'
		);
	}

	/**
	 * Updates the database query with parameters used for Products Stats report: categories and order status.
	 *
	 * @param  array  $query_args
	 * @return void
	 */
	protected function update_sql_query_params( $query_args ) {
		global $wpdb;

		$products_where_clause      = '';
		$products_from_clause       = '';
		$order_product_lookup_table = self::get_db_table_name();

		$included_products = $this->get_included_products( $query_args );
		if ( $included_products ) {
			$products_where_clause .= " AND {$order_product_lookup_table}.bundle_id IN ({$included_products})";
		}

		$order_status_filter = $this->get_status_subquery( $query_args );
		if ( $order_status_filter ) {
			$products_from_clause  .= " JOIN {$wpdb->prefix}wc_order_stats ON {$order_product_lookup_table}.order_id = {$wpdb->prefix}wc_order_stats.order_id";
			$products_where_clause .= " AND ( {$order_status_filter} )";
		}

		$this->add_time_period_sql_params( $query_args, $order_product_lookup_table );
		$this->total_query->add_sql_clause( 'where', $products_where_clause );
		$this->total_query->add_sql_clause( 'join', $products_from_clause );

		$this->add_intervals_sql_params( $query_args, $order_product_lookup_table );
		$this->interval_query->add_sql_clause( 'where', $products_where_clause );
		$this->interval_query->add_sql_clause( 'join', $products_from_clause );
		$this->interval_query->add_sql_clause( 'select', $this->get_sql_clause( 'select' ) . ' AS time_interval' );
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
			'category_includes' => array(),
			'interval'          => 'week',
			'product_includes'  => array(),
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

			$selections = $this->selected_columns( $query_args );
			$params     = $this->get_limit_params( $query_args );

			$this->update_sql_query_params( $query_args );
			$this->get_limit_sql_params( $query_args );
			$this->interval_query->add_sql_clause( 'where_time', $this->get_sql_clause( 'where_time' ) );


			$db_intervals = $wpdb->get_col(
				$this->interval_query->get_query_statement()
			);

			$db_interval_count       = count( $db_intervals );
			$expected_interval_count = TimeInterval::intervals_between( $query_args[ 'after' ], $query_args[ 'before' ], $query_args[ 'interval' ] );
			$total_pages             = (int) ceil( $expected_interval_count / $params[ 'per_page' ] );
			if ( $query_args[ 'page' ] < 1 || $query_args[ 'page' ] > $total_pages ) {
				return array();
			}

			$intervals = array();
			$this->update_intervals_sql_params( $query_args, $db_interval_count, $expected_interval_count, $table_name );
			$this->total_query->add_sql_clause( 'select', $selections );
			$this->total_query->add_sql_clause( 'where_time', $this->get_sql_clause( 'where_time' ) );

			$totals = $wpdb->get_results(
				$this->total_query->get_query_statement(),
				ARRAY_A
			);

			if ( null === $totals ) {
				return new \WP_Error( 'woocommerce_analytics_bundles_stats_result_failed', __( 'Sorry, fetching revenue data failed.', 'woocommerce-product-bundles' ) );
			}

			$this->interval_query->add_sql_clause( 'order_by', $this->get_sql_clause( 'order_by' ) );
			$this->interval_query->add_sql_clause( 'limit', $this->get_sql_clause( 'limit' ) );
			$this->interval_query->add_sql_clause( 'select', ", MAX(${table_name}.date_created) AS datetime_anchor" );
			if ( '' !== $selections ) {
				$this->interval_query->add_sql_clause( 'select', ', ' . $selections );
			}

			$intervals = $wpdb->get_results(
				$this->interval_query->get_query_statement(),
				ARRAY_A
			);

			if ( null === $intervals ) {
				return new \WP_Error( 'woocommerce_analytics_bundles_stats_result_failed', __( 'Sorry, fetching revenue data failed.', 'woocommerce-product-bundles' ) );
			}

			$totals = (object) $this->cast_numbers( $totals[ 0 ] );

			$data = (object) array(
				'totals'    => $totals,
				'intervals' => $intervals,
				'total'     => $expected_interval_count,
				'pages'     => $total_pages,
				'page_no'   => (int) $query_args[ 'page' ],
			);

			if ( TimeInterval::intervals_missing( $expected_interval_count, $db_interval_count, $params[ 'per_page' ], $query_args[ 'page' ], $query_args[ 'order' ], $query_args[ 'orderby' ], count( $intervals ) ) ) {
				$this->fill_in_missing_intervals( $db_intervals, $query_args[ 'adj_after' ], $query_args[ 'adj_before' ], $query_args[ 'interval' ], $data );
				$this->sort_intervals( $data, $query_args[ 'orderby' ], $query_args[ 'order' ] );
				$this->remove_extra_records( $data, $query_args[ 'page' ], $params[ 'per_page' ], $db_interval_count, $expected_interval_count, $query_args[ 'orderby' ], $query_args[ 'order' ] );
			} else {
				$this->update_interval_boundary_dates( $query_args[ 'after' ], $query_args[ 'before' ], $query_args[ 'interval' ], $data->intervals );
			}

			$this->create_interval_subtotals( $data->intervals );

			$this->set_cached_data( $cache_key, $data );
		}

		return $data;
	}

	/**
	 * Normalizes order_by clause to match to SQL query.
	 *
	 * @param  string  $order_by Order by option requested by user.
	 * @return string
	 */
	protected function normalize_order_by( $order_by ) {
		if ( 'date' === $order_by ) {
			return 'time_interval';
		}

		return $order_by;
	}

	/**
	 * Initialize query objects.
	 */
	protected function initialize_queries() {
		$this->clear_all_clauses();
		unset( $this->subquery );
		$this->total_query = new SqlQuery( $this->context . '_total' );
		$this->total_query->add_sql_clause( 'from', self::get_db_table_name() );

		$this->interval_query = new SqlQuery( $this->context . '_interval' );
		$this->interval_query->add_sql_clause( 'from', self::get_db_table_name() );
		$this->interval_query->add_sql_clause( 'group_by', 'time_interval' );
	}
}
