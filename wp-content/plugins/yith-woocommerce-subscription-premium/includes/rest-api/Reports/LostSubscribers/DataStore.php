<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

// phpcs:disable  WordPress.DB.DirectDatabaseQuery.NoCaching
// phpcs:disable  WordPress.DateTime.CurrentTimeTimestamp.Requested
// phpcs:disable  WordPress.DB.PreparedSQL.InterpolatedNotPrepared
// phpcs:disable  WordPress.DB.DirectDatabaseQuery.DirectQuery
// phpcs:disable  WordPress.DB.PreparedSQL.NotPrepared

/**
 * DataStore class
 *
 * @class   \YITH\Subscription\RestApi\Reports\LostSubscribers\DataStore
 * @package YITH WooCommerce Subscription
 * @since   2.3.0
 * @author  YITH
 */


namespace YITH\Subscription\RestApi\Reports\LostSubscribers;

defined( 'ABSPATH' ) || exit;

use \Automattic\WooCommerce\Admin\API\Reports\DataStore as ReportsDataStore;
use \Automattic\WooCommerce\Admin\API\Reports\DataStoreInterface;
use \Automattic\WooCommerce\Admin\API\Reports\TimeInterval;
use \Automattic\WooCommerce\Admin\API\Reports\SqlQuery;

/**
 * Class DataStore
 */
class DataStore extends ReportsDataStore implements DataStoreInterface {

	/**
	 * Table used to get the data.
	 *
	 * @var string
	 */
	protected static $table_name = 'yith_ywsbs_stats';

	/**
	 * Cache identifier.
	 *
	 * @var string
	 */
	protected $cache_key = 'lost_subscribers';

	/**
	 * Mapping columns to data type to return correct response types.
	 *
	 * @var array
	 */
	protected $column_types = array(
		'customer'     => 'strval',
		'customer_id'  => 'intval',
		'product_name' => 'strval',
		'product_id'   => 'intval',
		'status'       => 'strval',
	);

	/**
	 * Data store context used to pass to filters.
	 *
	 * @var string
	 */
	protected $context = 'lost_subscribers';

	/**
	 * Assign report columns once full table name has been assigned.
	 */
	protected function assign_report_columns() {
		$table_name = self::get_db_table_name();

		$this->report_columns = array(
			'customer'     => "{$table_name}.customer_id as customer_id",
			'product_name' => "{$table_name}.product_name as product_name",
			'product_id'   => "{$table_name}.product_id as product_id",
			'status'       => "{$table_name}.status as status",
		);
	}

	/**
	 * Returns the report data based on parameters supplied by the user.
	 *
	 * @param array $query_args Query parameters.
	 * @return stdClass|WP_Error Data.
	 */
	public function get_data( $query_args ) {
		global $wpdb;

		$table_name = self::get_db_table_name();

		// These defaults are only partially applied when used via REST API, as that has its own defaults.
		$defaults = array(
			'per_page' => get_option( 'posts_per_page' ),
			'page'     => 1,
			'order'    => 'DESC',
			'orderby'  => 'date',
			'before'   => TimeInterval::default_before(),
			'after'    => TimeInterval::default_after(),
			'fields'   => '*',
		);

		$query_args = wp_parse_args( $query_args, $defaults );
		$this->normalize_timezones( $query_args, $defaults );

		/*
		 * We need to get the cache key here because
		 * parent::update_intervals_sql_params() modifies $query_args.
		 */
		$cache_key = $this->get_cache_key( $query_args );
		$data      = $this->get_cached_data( $cache_key );

		if ( false === $data || ( defined( 'YITH_YWSBS_TEST_ON' ) && YITH_YWSBS_TEST_ON ) ) {
			$this->initialize_queries();

			$data = (object) array(
				'data'    => array(),
				'total'   => 0,
				'pages'   => 0,
				'page_no' => 0,
			);

			$selections = $this->selected_columns( $query_args );
			$params     = $this->get_limit_params( $query_args );
			$this->add_order_by_sql_params( $query_args );

			$start_date = $query_args['after'];
			$end_date   = $query_args['before'];
			$start_date = $start_date->format( 'Y-m-d H:i:s' );
			$end_date   = $end_date->format( 'Y-m-d H:i:s' );
			$this->subquery->add_sql_clause( 'where', " AND {$table_name}.cancelled_date >= '{$start_date}' AND {$table_name}.cancelled_date <= '{$end_date}'" );

			$db_records_count = (int) $wpdb->get_var(
				"SELECT COUNT(*) FROM (
					{$this->subquery->get_query_statement()}
				) AS tt"
			); //phpcs:ignore.

			if ( 0 === $params['per_page'] ) {
				$total_pages = 0;
			} else {
				$total_pages = (int) ceil( $db_records_count / $params['per_page'] );
			}

			if ( $query_args['page'] < 1 || $query_args['page'] > $total_pages ) {
				$data = (object) array(
					'data'    => array(),
					'total'   => $db_records_count,
					'pages'   => 0,
					'page_no' => 0,
				);

				return $data;
			}

			$this->subquery->clear_sql_clause( 'select' );
			$this->subquery->add_sql_clause( 'select', $selections );

			$this->subquery->add_sql_clause( 'order_by', $this->get_sql_clause( 'order_by' ) );
			$this->subquery->add_sql_clause( 'limit', "LIMIT {$params['offset']}, {$params['per_page']}" );

			$subscriptions_data = $wpdb->get_results(
				$this->subquery->get_query_statement(),
				ARRAY_A
			); // phpcs:ignore

			if ( null === $subscriptions_data ) {
				return $data;
			}

			$subscriptions_data = array_map( array( $this, 'cast_numbers' ), $subscriptions_data );
			$this->normalize_customer_name( $subscriptions_data );
			$this->normalize_product_name( $subscriptions_data );
			$data = (object) array(
				'data'    => $subscriptions_data,
				'total'   => $db_records_count,
				'pages'   => $total_pages,
				'page_no' => (int) $query_args['page'],
			);

			$this->set_cached_data( $cache_key, $data );
		}

		return $data;

	}

	/**
	 * Normalize customer name
	 *
	 * @param array $data Array of data.
	 */
	protected function normalize_customer_name( &$data ) {

		$customers        = $this->get_customers_by_data( $data );
		$mapped_customers = $this->map_array_by_key( $customers, 'customer_id' );

		foreach ( $data as $key => $d ) {
			if ( $d['customer_id'] && isset( $mapped_customers[ $d['customer_id'] ] ) ) {
				$data[ $key ]['customer'] = $mapped_customers[ $d['customer_id'] ];
			}
		}
	}

	/**
	 * Normalize product name
	 *
	 * @param array $data Array of data.
	 */
	protected function normalize_product_name( &$data ) {
		foreach ( $data as $key => $subscription ) {
			$product = wc_get_product( $subscription['product_id'] );

			if ( ! $product ) {
				continue;
			}

			$data[ $key ]['product_name'] = $product->get_formatted_name();
		}

	}
	/**
	 * Returns the same array index by a given key
	 *
	 * @param array  $array Array to be looped over.
	 * @param string $key Key of values used for new array.
	 * @return array
	 */
	protected function map_array_by_key( $array, $key ) {
		$mapped = array();
		foreach ( $array as $item ) {
			$mapped[ $item[ $key ] ] = $item;
		}
		return $mapped;
	}


	/**
	 * Get customer data from Subscription data.
	 *
	 * @param array $data Array of data.
	 * @return array
	 */
	protected function get_customers_by_data( $data ) {
		global $wpdb;

		$customer_lookup_table = $wpdb->prefix . 'wc_customer_lookup';
		$customer_ids          = array();

		foreach ( $data as $d ) {
			if ( $d['customer_id'] ) {
				$customer_ids[] = intval( $d['customer_id'] );
			}
		}

		if ( empty( $customer_ids ) ) {
			return array();
		}

		$customer_ids = implode( ',', $customer_ids );
		$customers    = $wpdb->get_results(
			"SELECT * FROM {$customer_lookup_table} WHERE user_id IN ({$customer_ids})",
			ARRAY_A
		); // phpcs:ignore

		return $customers;
	}

	/**
	 * Initialize query objects.
	 */
	protected function initialize_queries() {
		$this->clear_all_clauses();
		$this->subquery = new SqlQuery( $this->context . '_subquery' );
		$this->subquery->add_sql_clause( 'select', self::get_db_table_name() . '.subscription_id' );
		$this->subquery->add_sql_clause( 'from', self::get_db_table_name() );
		$this->subquery->add_sql_clause( 'where', ' AND status LIKE "cancelled" ' );
	}

}
