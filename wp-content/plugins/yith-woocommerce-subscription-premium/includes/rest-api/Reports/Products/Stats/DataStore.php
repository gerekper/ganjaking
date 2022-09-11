<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

// phpcs:disable  WordPress.DB.DirectDatabaseQuery.NoCaching
// phpcs:disable  WordPress.DateTime.CurrentTimeTimestamp.Requested
// phpcs:disable  WordPress.DB.PreparedSQL.InterpolatedNotPrepared
// phpcs:disable  WordPress.DB.DirectDatabaseQuery.DirectQuery
// phpcs:disable  WordPress.DB.PreparedSQL.NotPrepared

/**
 * DataStore class
 *
 * @class   \YITH\Subscription\RestApi\Reports\Products\Stats\DataStore
 * @package YITH WooCommerce Subscription
 * @since   2.3.0
 * @author  YITH
 */


namespace YITH\Subscription\RestApi\Reports\Products\Stats;

defined( 'ABSPATH' ) || exit;

use \YITH\Subscription\RestApi\Reports\Subscriptions\Stats\DataStore as SubscriptionStatsDataStore;
use \Automattic\WooCommerce\Admin\API\Reports\DataStoreInterface;
use \Automattic\WooCommerce\Admin\API\Reports\TimeInterval;
use \Automattic\WooCommerce\Admin\API\Reports\SqlQuery;

use \DateTime;

/**
 * Class DataStore
 */
class DataStore extends SubscriptionStatsDataStore implements DataStoreInterface {


	/**
	 * Cache identifier.
	 *
	 * @var string
	 */
	protected $cache_key = 'products_stats';

	/**
	 * Data store context used to pass to filters.
	 *
	 * @var string
	 */
	protected $context = 'products_stats';

	/**
	 * Query args.
	 *
	 * @var array
	 */
	protected $query_args = array();

	/**
	 * Single instance of the class
	 *
	 * @var DataStore
	 */
	protected static $instance;

	/**
	 * Singleton implementation
	 *
	 * @return DataStore
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * Assign report columns once full table name has been assigned.
	 */
	protected function assign_report_columns() {

		$table_name = self::get_db_table_name();

		$this->report_columns = array(
			'subscriptions_count' => 'SUM( 1 ) as subscriptions_count',
			'net_revenue'         => "SUM(CASE WHEN  ( {$table_name}.trial = 1 OR {$table_name}.status IN ('pending', 'trial' ) ) THEN 0 ELSE {$table_name}.net_total END) AS net_revenue",
			'mrr'                 => "SUM({$table_name}.mrr) AS mrr",
			'arr'                 => "SUM({$table_name}.arr) AS arr",
			'trial'               => "SUM(CASE WHEN {$table_name}.trial = 1 THEN 1 ELSE 0 END) AS trial",
			'conversions'         => "SUM(CASE WHEN {$table_name}.conversion_date NOT LIKE NULL THEN 1 ELSE 0 END) AS conversions",
		);
	}


	/**
	 * Maps ordering specified by the user to columns in the database/fields in the data.
	 *
	 * @param string $order_by Sorting criterion.
	 * @return string
	 */
	protected function normalize_order_by( $order_by ) {
		if ( 'date' === $order_by ) {
			return 'time_interval';
		}
		return $order_by;
	}

	/**
	 * Returns the page of data according to page number and items per page.
	 *
	 * @param array   $data Data to paginate.
	 * @param integer $page_no Page number.
	 * @param integer $items_per_page Number of items per page.
	 * @return array
	 */
	protected function page_records( $data, $page_no, $items_per_page ) {
		$offset = ( $page_no - 1 ) * $items_per_page;
		return array_slice( $data, $offset, $items_per_page );
	}


	/**
	 * Returns the report data based on parameters supplied by the user.
	 *
	 * @param array $query_args Query parameters.
	 * @return stdClass|WP_Error Data.
	 */
	public function get_data( $query_args ) {
		global $wpdb;
		unset( $query_args['orderby'] );

		// These defaults are only partially applied when used via REST API, as that has its own defaults.
		$defaults   = array(
			'per_page'      => get_option( 'posts_per_page' ),
			'page'          => 1,
			'order'         => 'asc',
			'orderby'       => 'time_interval',
			'before'        => TimeInterval::default_before(),
			'after'         => TimeInterval::default_after(),
			'fields'        => '*',
			'extended_info' => false,
			'interval'      => 'week',
			'products'      => '',
		);
		$query_args = wp_parse_args( $query_args, $defaults );

		$this->normalize_timezones( $query_args, $defaults );
		$cache_key  = $this->get_cache_key( $query_args );
		$data       = $this->get_cached_data( $cache_key );
		$table_name = self::get_db_table_name();

		if ( false === $data || ( defined( 'YITH_YWSBS_TEST_ON' ) && YITH_YWSBS_TEST_ON ) ) {
			$this->initialize_queries();

			$data = (object) array(
				'totals'    => (object) array(),
				'intervals' => (object) array(),
				'total'     => 0,
				'pages'     => 0,
				'page_no'   => 0,
			);

			$selections = $this->selected_columns( $query_args );
			$this->add_time_period_sql_params( $query_args, $table_name );
			$this->add_intervals_sql_params( $query_args, $table_name );
			$this->add_order_by_sql_params( $query_args );
			$where_time = $this->get_sql_clause( 'where_time' );
			$params     = $this->get_limit_sql_params( $query_args );
			$this->total_query->add_sql_clause( 'select', $selections );
			$this->total_query->add_sql_clause( 'where_time', $where_time );

			$products_subquery = $this->get_include_products_subquery( $query_args );
			if ( $products_subquery ) {
				$this->total_query->add_sql_clause( 'where', "AND {$products_subquery}" );
			}

			$totals = $wpdb->get_results(
				$this->total_query->get_query_statement(),
				ARRAY_A
			);

			if ( null === $totals ) {
				return new \WP_Error( 'woocommerce_analytics_revenue_result_failed', __( 'Sorry, fetching revenue data failed.', 'woocommerce' ) );
			}

			$totals = (object) $this->cast_numbers( $totals[0] );
			$this->interval_query->add_sql_clause( 'select', $this->get_sql_clause( 'select' ) . ' AS time_interval' );
			$this->interval_query->add_sql_clause( 'where_time', $where_time );

			$db_intervals = $wpdb->get_col(
				$this->interval_query->get_query_statement()
			); // phpcs:ignore cache ok, DB call ok, , unprepared SQL ok.

			$db_interval_count       = count( $db_intervals );
			$expected_interval_count = TimeInterval::intervals_between( $query_args['after'], $query_args['before'], $query_args['interval'] );

			$total_pages = (int) ceil( $expected_interval_count / $params['per_page'] );

			if ( $products_subquery ) {
				$this->interval_query->add_sql_clause( 'where', "AND {$products_subquery}" );
			}

			if ( $query_args['page'] < 1 || $query_args['page'] > $total_pages ) {
				return $data;
			}

			$this->update_intervals_sql_params( $query_args, $db_interval_count, $expected_interval_count, $table_name );

			$this->interval_query->add_sql_clause( 'order_by', $this->get_sql_clause( 'order_by' ) );
			$this->interval_query->add_sql_clause( 'limit', $this->get_sql_clause( 'limit' ) );
			$this->interval_query->add_sql_clause( 'select', ", MAX(${table_name}.date_created) AS datetime_anchor" );

			if ( '' !== $selections ) {
				$this->interval_query->add_sql_clause( 'select', ', ' . $selections );
			}

			$intervals = $wpdb->get_results(
				$this->interval_query->get_query_statement(),
				ARRAY_A
			); // phpcs:ignore cache ok, DB call ok, unprepared SQL ok.

			if ( null === $intervals ) {
				return new \WP_Error( 'woocommerce_analytics_revenue_result_failed', __( 'Sorry, fetching revenue data failed.', 'woocommerce' ) );
			}

			$after            = ! isset( $query_args['adj_after'] ) ? $query_args['after'] : $query_args['adj_after'];
			$before           = ! isset( $query_args['adj_before'] ) ? $query_args['before'] : $query_args['adj_before'];
			$this->query_args = $query_args;
			$this->revenues   = $this->get_revenues( $before );

			$totals->arr = array_sum( array_column( $this->revenues, 'arr' ) );
			$totals->mrr = array_sum( array_column( $this->revenues, 'mrr' ) );
			$this->get_conversions( $after, $before );
			$this->get_renews( $after, $before );
			$this->get_cancelled_subscriptions( $after, $before );

			$data = (object) array(
				'totals'    => $totals,
				'intervals' => $intervals,
				'total'     => $expected_interval_count,
				'pages'     => $total_pages,
				'page_no'   => (int) $query_args['page'],
			);
			$this->fill_in_missing_intervals( $db_intervals, $after, $before, $query_args['interval'], $data );

			if ( TimeInterval::intervals_missing( $expected_interval_count, $db_interval_count, $params['per_page'], $query_args['page'], $query_args['order'], $query_args['orderby'], count( $intervals ) ) ) {
				$this->fill_in_missing_intervals( $db_intervals, $after, $before, $query_args['interval'], $data );
				$this->sort_intervals( $data, $query_args['orderby'], $query_args['order'] );
				$this->remove_extra_records( $data, $query_args['page'], $params['per_page'], $db_interval_count, $expected_interval_count, $query_args['orderby'], $query_args['order'] );
			} else {
				$this->update_interval_boundary_dates( $query_args['after'], $query_args['before'], $query_args['interval'], $data->intervals );
			}

			$this->create_interval_subtotals( $data->intervals );

			$this->normalize_totals( $data );

			$this->set_cached_data( $cache_key, $data );

		}

		return $data;
	}

	/**
	 * Initialize query objects.
	 */
	protected function initialize_queries() {
		$sbs_stats = self::get_db_table_name();

		$this->clear_all_clauses();
		unset( $this->subquery );
		$this->total_query = new SqlQuery( $this->context . '_total' );
		$this->total_query->add_sql_clause( 'from', $sbs_stats );

		$this->interval_query = new SqlQuery( $this->context . '_interval' );
		$this->interval_query->add_sql_clause( 'from', $sbs_stats );
		$this->interval_query->add_sql_clause( 'group_by', 'time_interval' );
	}

	/**
	 * Normalize total
	 *
	 * @param object $data Data to normalize.
	 */
	protected function normalize_totals( &$data ) {
		$intervals    = $data->intervals;
		$mrr_average  = 0;
		$arr_average  = 0;
		$num_interval = count( $intervals );
		foreach ( $intervals as $interval ) {
			$mrr_average += $interval['subtotals']->mrr;
			$arr_average += $interval['subtotals']->arr;
		}

		$data->totals->mrr                     = $mrr_average / $num_interval;
		$data->totals->arr                     = $arr_average / $num_interval;
		$data->totals->conversions             = count( $this->conversions );
		$data->totals->renews_count            = count( $this->renews );
		$data->totals->cancelled_subscriptions = count( $this->cancelled_subscriptions );

		$renew_total          = 0;
		$subscription_renewed = array();
		foreach ( $this->renews as $renew ) {
			$renew_total += $renew['net_total'];
			if ( ! in_array( $renew['subscription_id'], $subscription_renewed ) ) { //phpcs:ignore
				array_push( $subscription_renewed, $renew['subscription_id'] );
			}
		}

		$data->totals->renews_net_revenue = $renew_total;

		$data->totals->total_revenue = $data->totals->renews_net_revenue + $data->totals->net_revenue;
	}

	/**
	 * Fills in interval gaps from DB with 0-filled objects.
	 *
	 * @param array    $db_intervals Array of all intervals present in the db.
	 * @param DateTime $start_datetime Start date.
	 * @param DateTime $end_datetime End date.
	 * @param string   $time_interval Time interval, e.g. day, week, month.
	 * @param stdClass $data Data with SQL extracted intervals.
	 * @return stdClass
	 */
	protected function fill_in_missing_intervals( $db_intervals, $start_datetime, $end_datetime, $time_interval, &$data ) {
		$local_tz = new \DateTimeZone( wc_timezone_string() );

		// At this point, we don't know when we can stop iterating, as the ordering can be based on any value.
		$time_ids     = array_flip( wp_list_pluck( $data->intervals, 'time_interval' ) );
		$db_intervals = array_flip( $db_intervals );

		// Totals object used to get all needed properties.
		$totals_arr = get_object_vars( $data->totals );

		foreach ( $totals_arr as $key => $val ) {
			$totals_arr[ $key ] = 0;
		}

		while ( $start_datetime <= $end_datetime ) {

			$next_start = TimeInterval::iterate( $start_datetime, $time_interval );
			$time_id    = TimeInterval::time_interval_id( $time_interval, $start_datetime );

			// Either create fill-zero interval or use data from db.
			if ( $next_start > $end_datetime ) {
				$interval_end = $end_datetime->format( 'Y-m-d H:i:s' );
				$conversions  = count( $this->get_conversions( $start_datetime, $end_datetime ) );
				$renews       = $this->get_renews( $start_datetime, $end_datetime );
				$mrr          = $this->calculate_mrr( $end_datetime );
				$arr          = $this->calculate_arr( $end_datetime );
				$cancelled    = count( $this->get_cancelled_subscriptions( $start_datetime, $end_datetime ) );

			} else {
				$prev_end_timestamp = (int) $next_start->format( 'U' ) - 1;
				$prev_end           = new \DateTime();
				$prev_end->setTimestamp( $prev_end_timestamp );
				$prev_end->setTimezone( $local_tz );
				$interval_end = $prev_end->format( 'Y-m-d H:i:s' );
				$conversions  = count( $this->get_conversions( $start_datetime, $prev_end ) );
				$renews       = $this->get_renews( $start_datetime, $prev_end );
				$mrr          = $this->calculate_mrr( $prev_end );
				$arr          = $this->calculate_arr( $prev_end );
				$cancelled    = count( $this->get_cancelled_subscriptions( $start_datetime, $prev_end ) );
			}

			$renews_count      = count( $renews );
			$renew_net_revenue = 0;
			foreach ( $renews as $renew ) {
				$renew_net_revenue += $renew['net_total'];
			}

			if ( array_key_exists( $time_id, $time_ids ) ) {
				// For interval present in the db for this time frame, just fill in dates.
				$record               = &$data->intervals[ $time_ids[ $time_id ] ];
				$record['date_start'] = $start_datetime->format( 'Y-m-d H:i:s' );
				$record['date_end']   = $interval_end;

				$record['mrr']                     = $mrr;
				$record['arr']                     = $arr;
				$record['conversions']             = $conversions;
				$record['renews_count']            = $renews_count;
				$record['renews_net_revenue']      = $renew_net_revenue;
				$record['total_revenue']           = $renew_net_revenue + $record['net_revenue'];
				$record['cancelled_subscriptions'] = $cancelled;

			} else {
				$record_arr                            = array();
				$record_arr['time_interval']           = $time_id;
				$record_arr['date_start']              = $start_datetime->format( 'Y-m-d H:i:s' );
				$record_arr['date_end']                = $interval_end;
				$totals_arr['mrr']                     = $mrr;
				$totals_arr['arr']                     = $arr;
				$totals_arr['conversions']             = $conversions;
				$totals_arr['renews_count']            = $renews_count;
				$totals_arr['renews_net_revenue']      = $renew_net_revenue;
				$totals_arr['total_revenue']           = $renew_net_revenue + $totals_arr['net_revenue'];
				$totals_arr['cancelled_subscriptions'] = $cancelled;
				$data->intervals[]                     = array_merge( $record_arr, $totals_arr );

			}
			$start_datetime = $next_start;
		}

		return $data;
	}

	/**
	 * Add subscription information to the lookup table when a subscription is created or modified.
	 *
	 * @param int        $subscription_id Post ID.
	 * @param int|string $order_id Order Id.
	 *
	 * @return int|bool Returns -1 if order won't be processed, or a boolean indicating processing success.
	 */
	public function sync_subscription( $subscription_id, $order_id = '' ) {
		if ( YITH_YWSBS_POST_TYPE !== get_post_type( $subscription_id ) ) {
			return -1;
		}

		$subscription = ywsbs_get_subscription( $subscription_id );
		if ( ! $subscription ) {
			return -1;
		}

		return $this->update( $subscription, $order_id );
	}


	/**
	 * Returns subscription status subquery to be used in WHERE SQL query, based on query arguments from the user.
	 *
	 * @param array  $query_args Parameters supplied by the user.
	 * @param string $operator AND or OR, based on match query argument.
	 * @return string
	 */
	protected function get_include_products_subquery( $query_args, $operator = 'AND' ) {

		$table_name = self::get_db_table_name();
		$subqueries = array();

		if ( isset( $query_args['products'] ) && is_array( $query_args['products'] ) && count( $query_args['products'] ) > 0 ) {
			$search_products = $query_args['products'];
			if ( $search_products ) {
				$subqueries[] = "{$table_name}.product_id IN ( '" . implode( "','", $search_products ) . "' )";
			}
		}

		return implode( " $operator ", $subqueries );
	}

}
