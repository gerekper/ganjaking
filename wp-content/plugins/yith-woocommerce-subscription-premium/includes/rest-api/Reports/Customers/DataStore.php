<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

// phpcs:disable  WordPress.DB.DirectDatabaseQuery.NoCaching
// phpcs:disable  WordPress.DateTime.CurrentTimeTimestamp.Requested
// phpcs:disable  WordPress.DB.PreparedSQL.InterpolatedNotPrepared
// phpcs:disable  WordPress.DB.DirectDatabaseQuery.DirectQuery
// phpcs:disable  WordPress.DB.PreparedSQL.NotPrepared

/**
 * DataStore class
 *
 * @class   \YITH\Subscription\RestApi\Reports\Customers\DataStore
 * @package YITH WooCommerce Subscription
 * @since   2.3.0
 * @author  YITH
 */

namespace YITH\Subscription\RestApi\Reports\Customers;

defined( 'ABSPATH' ) || exit;

use \Automattic\WooCommerce\Admin\API\Reports\DataStore as ReportsDataStore;
use \Automattic\WooCommerce\Admin\API\Reports\DataStoreInterface;
use \Automattic\WooCommerce\Admin\API\Reports\TimeInterval;
use \Automattic\WooCommerce\Admin\API\Reports\SqlQuery;

use function YITH\Subscription\RestApi\get_sql_clauses_for_filters;

/**
 * Class DataStore
 */
class DataStore extends ReportsDataStore implements DataStoreInterface {

	/**
	 * Table used to get the data.
	 *
	 * @var string
	 */
	protected static $table_name = 'yith_ywsbs_order_lookup';

	/**
	 * Cache identifier.
	 *
	 * @var string
	 */
	protected $cache_key = 'customers';

	/**
	 * Mapping columns to data type to return correct response types.
	 *
	 * @var array
	 */
	protected $column_types = array(
		'customer'               => 'strval',
		'customer_id'            => 'intval',
		'total_paid'             => 'floatval',
		'username'               => 'strval',
		'first_name'             => 'strval',
		'last_name'              => 'strval',
		'email'                  => 'strval',
		'subscription_active'    => 'intval',
		'subscription_cancelled' => 'intval',
	);

	/**
	 * Data store context used to pass to filters.
	 *
	 * @var string
	 */
	protected $context = 'customers';

	/**
	 * Assign report columns once full table name has been assigned.
	 */
	protected function assign_report_columns() {
		$table_name = self::get_db_table_name();

		$this->report_columns = array(
			'customer'   => "{$table_name}.customer_id as customer_id",
			'total_paid' => "SUM( {$table_name}.net_total ) as total_paid",
			'user_id'    => 'customer_lookup.user_id as user_id',
			'username'   => 'customer_lookup.username as username',
			'first_name' => 'customer_lookup.first_name as first_name',
			'last_name'  => 'customer_lookup.last_name as last_name',
			'email'      => 'customer_lookup.email as email',
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
			'type'     => 'dashboard',
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

			$this->normalize_subscription_activations( $subscriptions_data );
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
	 * Add the number of subscription active or cancelled to results.
	 *
	 * @param array $subscriptions_data Data.
	 */
	public function normalize_subscription_activations( &$subscriptions_data ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'yith_ywsbs_stats';
		$query      = "SELECT
				customer_id,
				SUM(CASE WHEN {$table_name}.status IN ('cancelled','expired') THEN 0 ELSE 1 END ) AS active,
				SUM(CASE WHEN {$table_name}.status IN ('cancelled','expired') THEN 1 ELSE 0 END) AS cancelled
			FROM
				{$table_name}
				GROUP BY
					customer_id";
		$results    = $wpdb->get_results( $query, ARRAY_A ); // phpcs:ignore
		if ( $results ) {
			$mapped_results = $this->map_array_by_key( $results, 'customer_id' );
			foreach ( $subscriptions_data as $key => $data ) {
				if ( isset( $mapped_results[ $data['customer_id'] ] ) ) {
					$subscriptions_data[ $key ]['subscription_active']    = $mapped_results[ $data['customer_id'] ]['active'];
					$subscriptions_data[ $key ]['subscription_cancelled'] = $mapped_results[ $data['customer_id'] ]['cancelled'];
				}
			}
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
	 * Initialize query objects.
	 */
	protected function initialize_queries() {
		$this->clear_all_clauses();
		global $wpdb;
		$customer_lookup_table = $wpdb->prefix . 'wc_customer_lookup';
		$table_name            = self::get_db_table_name();
		$join_clause           = "INNER JOIN {$customer_lookup_table} as customer_lookup ON customer_lookup.user_id = {$table_name}.customer_id";

		$this->subquery = new SqlQuery( $this->context . '_subquery' );
		$this->subquery->add_sql_clause( 'select', self::get_db_table_name() . '.customer_id' );
		$this->subquery->add_sql_clause( 'join', $join_clause );
		$this->subquery->add_sql_clause( 'from', self::get_db_table_name() );
		$this->subquery->add_sql_clause( 'group_by', 'customer_lookup.user_id' );
	}

}
