<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

// phpcs:disable  WordPress.DB.DirectDatabaseQuery.NoCaching
// phpcs:disable  WordPress.DateTime.CurrentTimeTimestamp.Requested
// phpcs:disable  WordPress.DB.PreparedSQL.InterpolatedNotPrepared
// phpcs:disable  WordPress.DB.DirectDatabaseQuery.DirectQuery
// phpcs:disable  WordPress.DB.PreparedSQL.NotPrepared

/**
 * DataStore class
 *
 * @class   \YITH\Subscription\RestApi\Reports\Subscriptions\Stats\DataStore
 * @package YITH WooCommerce Subscription
 * @since   2.3.0
 * @author  YITH
 */


namespace YITH\Subscription\RestApi\Reports\Subscriptions\Stats;

defined( 'ABSPATH' ) || exit;

use \Automattic\WooCommerce\Admin\API\Reports\DataStore as ReportsDataStore;
use \Automattic\WooCommerce\Admin\API\Reports\DataStoreInterface;
use \Automattic\WooCommerce\Admin\API\Reports\TimeInterval;
use \Automattic\WooCommerce\Admin\API\Reports\SqlQuery;

use function YITH\Subscription\RestApi\get_sql_clauses_for_filters;
use \DateTime;
use YITH\Subscription\RestApi\Schedulers\Scheduler;

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
	 * Table used to get the data.
	 *
	 * @var string
	 */
	protected static $table_order_name = 'yith_ywsbs_order_lookup';

	/**
	 * Table used to get the data.
	 *
	 * @var string
	 */
	protected static $table_revenue_name = 'yith_ywsbs_revenue_lookup';

	/**
	 * Table used to get the data.
	 *
	 * @var string
	 */
	protected $revenues = array();


	/**
	 * Subscription conversions.
	 *
	 * @var bool|array
	 */
	protected $conversions = false;


	/**
	 * Table used to get the data.
	 *
	 * @var bool|array
	 */
	protected $renews = false;

	/**
	 * Table used to get the data.
	 *
	 * @var bool|array
	 */
	protected $cancelled_subscriptions = false;

	/**
	 * Query args.
	 *
	 * @var array
	 */
	protected $query_args = array();


	/**
	 * Mapping columns to data type to return correct response types.
	 *
	 * @var array
	 */
	protected $column_types = array(
		'subscriptions_count'     => 'intval',
		'net_revenue'             => 'floatval',
		'mrr'                     => 'floatval',
		'arr'                     => 'floatval',
		'trial'                   => 'intval',
		'conversions'             => 'intval',
		'total_sales'             => 'floatval',
		'renews_count'            => 'intval',
		'renews_net_revenue'      => 'floatval',
		'cancelled_subscriptions' => 'intval',
	);

	/**
	 * Cache identifier.
	 *
	 * @var string
	 */
	protected $cache_key = 'subscriptions_stats';

	/**
	 * Data store context used to pass to filters.
	 *
	 * @var string
	 */
	protected $context = 'subscriptions_stats';

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
		global $wpdb;
		$table_name         = self::get_db_table_name();
		$table_revenue_name = $wpdb->prefix . self::$table_revenue_name;

		$this->report_columns = apply_filters(
			'ywpar_report_columns_subscription',
			array(
				'subscriptions_count' => "SUM( 1 ) as subscriptions_count",
				'net_revenue'         => "SUM(CASE WHEN {$table_name}.trial = 1 OR {$table_name}.status LIKE 'pending' THEN 0 ELSE {$table_name}.net_total END) AS net_revenue",
				'mrr'                 => "SUM({$table_name}.mrr) AS mrr",
				'arr'                 => "SUM({$table_name}.arr) AS arr",
				'trial'               => "SUM(CASE WHEN {$table_name}.trial = 1 THEN 1 ELSE 0 END) AS trial",
				'conversions'         => "SUM(CASE WHEN {$table_name}.conversion_date NOT LIKE NULL THEN 1 ELSE 0 END) AS conversions",
				'total_revenue'       => "SUM(CASE WHEN {$table_name}.trial = 1 OR {$table_name}.status LIKE 'pending' THEN 0 ELSE {$table_name}.net_total END) AS net_revenue",
			), $table_name
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

			$this->revenues = $this->get_revenues( $before );
			$totals->arr    = array_sum( array_column( $this->revenues, 'arr' ) );
			$totals->mrr    = array_sum( array_column( $this->revenues, 'mrr' ) );

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
	 * Normalize the totals
	 *
	 * @param mixed $data Data.
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
		$data->totals->cancelled_subscriptions = count( $this->cancelled_subscriptions );

		$renew_total          = 0;
		$subscription_renewed = array();
		foreach ( $this->renews as $renew ) {
			$renew_total += $renew['net_total'];
			if ( ! in_array( $renew['subscription_id'], $subscription_renewed ) ) { //phpcs:ignore
				array_push( $subscription_renewed, $renew['subscription_id'] );
			}
		}
		$data->totals->renews_count       = count( $subscription_renewed );
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
				$cancelled    = count( $this->get_cancelled_subscriptions( $start_datetime, $end_datetime ) );
				$mrr          = $this->calculate_mrr( $end_datetime );
				$arr          = $this->calculate_arr( $end_datetime );

			} else {
				$prev_end_timestamp = (int) $next_start->format( 'U' ) - 1;
				$prev_end           = new \DateTime();
				$prev_end->setTimestamp( $prev_end_timestamp );
				$prev_end->setTimezone( $local_tz );
				$interval_end = $prev_end->format( 'Y-m-d H:i:s' );
				$conversions  = count( $this->get_conversions( $start_datetime, $prev_end ) );
				$renews       = $this->get_renews( $start_datetime, $prev_end );
				$cancelled    = count( $this->get_cancelled_subscriptions( $start_datetime, $prev_end ) );
				$mrr          = $this->calculate_mrr( $prev_end );
				$arr          = $this->calculate_arr( $prev_end );
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
				$record['cancelled_subscriptions'] = $cancelled;
				$record['total_revenue']           = $renew_net_revenue + $record['net_revenue'];
			} elseif ( ! array_key_exists( $time_id, $db_intervals ) ) {
				$record_arr                            = array();
				$record_arr['time_interval']           = $time_id;
				$record_arr['date_start']              = $start_datetime->format( 'Y-m-d H:i:s' );
				$record_arr['date_end']                = $interval_end;
				$totals_arr['mrr']                     = $mrr;
				$totals_arr['arr']                     = $arr;
				$totals_arr['conversions']             = $conversions;
				$totals_arr['renews_count']            = $renews_count;
				$totals_arr['renews_net_revenue']      = $renew_net_revenue;
				$totals_arr['cancelled_subscriptions'] = $cancelled;
				$totals_arr['total_revenue']           = $renew_net_revenue + $totals_arr['net_revenue'];

				$data->intervals[] = array_merge( $record_arr, $totals_arr );
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
	 * Update the database with stats data.
	 *
	 * @param YWSBS_Subscription $subscription Subscription object.
	 * @param int|string         $order_id Order Id.
	 *
	 * @return int|bool Returns -1 if order won't be processed, or a boolean indicating processing success.
	 */
	public function update( $subscription, $order_id ) {
		global $wpdb;
		$table_name = self::get_db_table_name();

		if ( ! $subscription->get_id() ) {
			return -1;
		}

		/**
		 * Filters subscription stats data.
		 *
		 * @param array              $data Data written to subscription stats lookup table.
		 * @param YWSBS_Subscription $subscription Subscription object.
		 */
		$date_created    = get_the_date( 'Y-m-d H:i:s', $subscription->get_id() );
		$subscription_id = $subscription->get_id();

		$table_revenue_name = $wpdb->prefix . self::$table_revenue_name;
		$old_entry          = $wpdb->get_row(
			"SELECT * FROM {$table_revenue_name}
			WHERE subscription_id = {$subscription_id} order by update_date DESC LIMIT 1",
			ARRAY_A
		); // phpcs:ignore unprepared SQL ok.

		$was_trial       = (int) $subscription->get( 'trial_per' ) > 0;
		$conversion_date = $subscription->get_conversion_date();

		if ( ! empty( $conversion_date ) ) {
			$dt              = new DateTime();
			$conversion_date = $dt->setTimestamp( $conversion_date );
			$conversion_date = $conversion_date->format( 'Y-m-d H:i:s' );
		}
		$cancelled_date = '';
		if ( in_array( $subscription->get_status(), array( 'cancelled', 'expired' ), true ) ) {

			if ( 'expired' === $subscription->get_status() ) {
				$cancelled_date = $subscription->get( 'expired_date' );
			} else {
				$cancelled_date = $subscription->get( 'cancelled_date' );
			}

			if ( $cancelled_date ) {
				$cancelled_datetime = new DateTime();
				$cancelled_datetime = $cancelled_datetime->setTimestamp( $cancelled_date );

				$cancelled_date = $cancelled_datetime->format( 'Y-m-d H:i:s' );
			}
		}

		$data   = apply_filters(
			'ywsbs_analytics_update_subscription_stats_data',
			array(
				'subscription_id'       => $subscription->get_id(),
				'status'                => $subscription->get_status(),
				'customer_id'           => $subscription->get_user_id(),
				'date_created'          => $date_created,
				'date_created_gmt'      => gmdate( 'Y-m-d H:i:s', strtotime( $date_created ) ),
				'product_id'            => $subscription->get_product_id(),
				'variation_id'          => $subscription->get_variation_id(),
				'product_name'          => $subscription->get_product_name(),
				'currency'              => $subscription->get_order_currency(),
				'quantity'              => $subscription->get_quantity(),
				'total'                 => $subscription->get_subscription_total(),
				'tax_total'             => $subscription->get_order_shipping_tax() + $subscription->get_order_tax(),
				'shipping_total'        => $subscription->get_order_shipping(),
				'net_total'             => abs( $subscription->get_subscription_total() - ( $subscription->get_order_shipping_tax() + $subscription->get_order_tax() + $subscription->get_order_shipping() ) ),
				'fee'                   => $subscription->get_fee(),
				'mrr'                   => $subscription->get_mrr(),
				'arr'                   => $subscription->get_arr(),
				'next_payment_due_date' => $subscription->get_payment_due_date(),
				'orders_paid'           => $subscription->get( 'rates_payed' ),
				'trial'                 => (int) $was_trial,
				'conversion_date'       => $conversion_date,
				'cancelled_date'        => $cancelled_date,
			),
			$subscription
		);
		$format = array(
			'%d',  // subscription_id.
			'%s',  // status.
			'%d',  // customer_id.
			'%s',  // date_created.
			'%s',  // date_created_gmt.
			'%d',  // product_id.
			'%d',  // variation_id.
			'%s',  // product_name.
			'%s',  // currency.
			'%d',  // quantity.
			'%f',  // total.
			'%f',  // tax_total.
			'%f',  // shipping_total.
			'%f',  // net_total.
			'%f',  // fee.
			'%f',  // mrr.
			'%f',  // arr.
			'%s',  // next_payment_due_date.
			'%d',  // orders_paid.
			'%d',  // trial.
			'%s',  // conversion_date.
			'%s',  // cancelled_date.
		);

		// Update or add the information to the DB.
		$result = $wpdb->replace( $table_name, $data, $format );

		if ( $old_entry ) {
			if ( $data['mrr'] != $old_entry['mrr'] || $data['arr'] != $old_entry['arr'] ) { //phpcs:ignore
				$this->update_revenue_lookup( $data, 1 === $result );
			}
		} else {
			$this->update_revenue_lookup( $data, 1 === $result );
		}

		/**
		 * Fires when subscription's stats reports are updated.
		 *
		 * @param int $subscription_id Subscription ID.
		 */
		do_action( 'ywsbs_analytics_update_subscription_stats', $subscription->get_id(), $order_id );
		if ( ! empty( $order_id ) ) {
			$this->update_order_lookup( $subscription, $order_id );
		}

		// Check the rows affected for success. Using REPLACE can affect 2 rows if the row already exists.
		return ( 1 === $result || 2 === $result );
	}


	/**
	 * Update the database with stats data.
	 *
	 * @param YWSBS_Subscription $subscription Subscription object.
	 * @param int|string         $order_id Order Id.
	 *
	 * @return int|bool Returns -1 if order won't be processed, or a boolean indicating processing success.
	 */
	public function update_order_lookup( $subscription, $order_id ) {
		global $wpdb;

		$table_name      = $wpdb->prefix . self::$table_order_name;
		$subscription_id = $subscription->get_id();

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return -1;
		}

		$order_item = $order->get_item( $subscription->get( 'order_item_id' ) );
		$line_total = (float) $order_item->get_total();
		$line_tax   = (float) $order_item->get_total_tax();

		$date_paid = ! is_null( $order->get_date_paid() ) ? $order->get_date_paid() : $order->get_date_created();
		$data      = apply_filters(
			'ywsbs_analytics_update_order_stats_data',
			array(
				'order_id'        => $order_id,
				'subscription_id' => $subscription->get_id(),
				'status'          => self::normalize_order_status( $order->get_status() ),
				'customer_id'     => $order->get_customer_id(),
				'date_created'    => $order->get_date_created()->date( 'Y-m-d H:i:s' ),
				'date_paid'       => $date_paid->date( 'Y-m-d H:i:s' ),
				'total'           => $line_total + $line_tax + (float) $order->get_shipping_total() + (float) $order->get_shipping_tax(),
				'tax_total'       => $line_tax + (float) $order->get_shipping_tax(),
				'shipping_total'  => (float) $order->get_shipping_total(),
				'net_total'       => $line_total,
				'renew'           => 'yes' === $order->get_meta( 'is_a_renew' ),
			),
			$order
		);

		$format = array(
			'%d',
			'%d',
			'%s',
			'%d',
			'%s',
			'%s',
			'%f',
			'%f',
			'%f',
			'%f',
			'%s',
		);

		// Update or add the information to the DB.
		$result = $wpdb->replace( $table_name, $data, $format );

		do_action( 'ywsbs_analytics_update_order_stats', $order_id, $subscription_id );

		// Check the rows affected for success. Using REPLACE can affect 2 rows if the row already exists.
		return ( 1 === $result || 2 === $result );
	}


	/**
	 * Update revenue lookup
	 *
	 * @param array $data Data.
	 * @param bool  $is_new This is necessary to set the update date.
	 *
	 * @return bool
	 */
	public function update_revenue_lookup( $data, $is_new ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_revenue_name;

		$now         = new DateTime();
		$update_date = $is_new ? substr( $data['date_created'], 0, 10 ) : $now->format( 'Y-m-d' );

		$data = apply_filters(
			'ywsbs_analytics_update_revenue_stats_data',
			array(
				'subscription_id' => $data['subscription_id'],
				'update_date'     => $update_date . ' 00:00:00',
				'mrr'             => $data['mrr'],
				'arr'             => $data['arr'],
			),
			$data
		);

		$format = array(
			'%d',
			'%s',
			'%f',
			'%f',
		);

		// Update or add the information to the DB.
		$result = $wpdb->replace( $table_name, $data, $format );

		do_action( 'ywsbs_analytics_update_revenue_stats', $data );

		// Check the rows affected for success. Using REPLACE can affect 2 rows if the row already exists.
		return ( 1 === $result || 2 === $result );
	}

	/**
	 * Get all the revenues from the revenue table
	 *
	 * @param Datetime $end_date End date.
	 * @return array
	 */
	public function get_revenues( $end_date ) {
		global $wpdb;

		$table_revenue_name = $wpdb->prefix . self::$table_revenue_name;
		$query_args         = $this->query_args;

		$end_date = $end_date->format( 'Y-m-d H:i:s' );
		if ( isset( $query_args['products'] ) && ! empty( $query_args['products'] ) ) {
			global $wpdb;
			$table_name      = self::get_db_table_name();
			$search_products = $query_args['products'];

			$revenues = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT table_revenue.subscription_id, table_revenue.update_date, table_revenue.mrr, table_revenue.arr from {$table_revenue_name} as table_revenue
    INNER JOIN {$table_name} ON table_revenue.subscription_id = {$table_name}.subscription_id where update_date < %s AND product_id IN ( '" . implode( "','", $search_products ) . "' ) and {$table_name}.status NOT LIKE 'pending' order by update_date DESC",
					$end_date
				),
				ARRAY_A
			);
		} else {
			$revenues = $wpdb->get_results(
				$wpdb->prepare( "SELECT subscription_id, update_date, mrr, arr from {$table_revenue_name} where update_date < %s order by update_date DESC", $end_date ),
				ARRAY_A
			);
		}

		return $revenues;
	}

	/**
	 * Get all the revenues from the revenue table from start to end date.
	 *
	 * @param Datetime $start_date Start date.
	 * @param Datetime $end_date End date.
	 * @return array
	 */
	public function get_conversions( $start_date, $end_date ) {

		$start_date  = $start_date->format( 'Y-m-d H:i:s' );
		$end_date    = $end_date->format( 'Y-m-d H:i:s' );
		$query_args  = $this->query_args;
		$conversions = array();

		if ( false === $this->conversions ) {
			global $wpdb;
			$table_name = self::get_db_table_name();
			if ( isset( $query_args['products'] ) && ! empty( $query_args['products'] ) ) {
				$search_products = $query_args['products'];
				$conversions     = $wpdb->get_results( $wpdb->prepare( "SELECT subscription_id, conversion_date from {$table_name} where conversion_date <= %s and  conversion_date >= %s and product_id IN ( '" . implode( "','", $search_products ) . "' ) order by conversion_date ASC", $end_date, $start_date ), ARRAY_A ); //phpcs:ignore
			} else {
				$conversions = $wpdb->get_results(
					$wpdb->prepare( "SELECT subscription_id, conversion_date from {$table_name} where conversion_date <= %s and  conversion_date >= %s order by conversion_date ASC", $end_date, $start_date ),
					ARRAY_A
				);
			}

			$this->conversions = $conversions;

		} else {

			foreach ( $this->conversions as $conversion ) {
				if ( $conversion['conversion_date'] >= $start_date && $conversion['conversion_date'] <= $end_date ) {
					array_push( $conversions, $conversion );
				}
			}
		}

		return $conversions;
	}

	/**
	 * Get all the cancelled subscriptions from subscription table from start to end date.
	 *
	 * @param Datetime $start_date Start date.
	 * @param Datetime $end_date End date.
	 * @return array
	 */
	public function get_cancelled_subscriptions( $start_date, $end_date ) {

		$start_date              = $start_date->format( 'Y-m-d H:i:s' );
		$end_date                = $end_date->format( 'Y-m-d H:i:s' );
		$query_args              = $this->query_args;
		$cancelled_subscriptions = array();
		if ( false === $this->cancelled_subscriptions ) {
			global $wpdb;
			$table_name = self::get_db_table_name();
			if ( isset( $query_args['products'] ) && ! empty( $query_args['products'] ) ) {
				global $wpdb;

				$search_products = $query_args['products'];

				$cancelled_subscriptions = $wpdb->get_results( $wpdb->prepare( "SELECT * from {$table_name}  where product_id IN ( '" . implode( "','", $search_products ) . "' ) AND cancelled_date >= %s and cancelled_date <= %s order by cancelled_date ASC ", $start_date, $end_date ), ARRAY_A ); //phpcs:ignore

			} else {
				$cancelled_subscriptions = $wpdb->get_results( $wpdb->prepare( "SELECT * from {$table_name} where cancelled_date >= %s and cancelled_date <= %s order by cancelled_date ASC ", $start_date, $end_date ), ARRAY_A ); //phpcs:ignore
			}

			$this->cancelled_subscriptions = $cancelled_subscriptions;

		} else {

			foreach ( $this->cancelled_subscriptions as $cancelled_subscription ) {
				if ( $cancelled_subscription['cancelled_date'] >= $start_date && $cancelled_subscription['cancelled_date'] <= $end_date ) {
					array_push( $cancelled_subscriptions, $cancelled_subscription );
				}
			}
		}

		return $cancelled_subscriptions;
	}


	/**
	 * Get all the revenues from the revenue table from start to end date.
	 *
	 * @param Datetime $start_date Start date.
	 * @param Datetime $end_date End date.
	 * @return array
	 */
	public function get_renews( $start_date, $end_date ) {

		$start_date = $start_date->format( 'Y-m-d H:i:s' );
		$end_date   = $end_date->format( 'Y-m-d H:i:s' );
		$query_args = $this->query_args;
		$renews     = array();
		if ( false === $this->renews ) {
			global $wpdb;
			$table_name = $wpdb->prefix . self::$table_order_name;
			$s_tb       = self::get_db_table_name();
			if ( isset( $query_args['products'] ) && ! empty( $query_args['products'] ) ) {
				global $wpdb;
				$search_products = $query_args['products'];
				$renews = $wpdb->get_results( $wpdb->prepare( "SELECT * from {$table_name} LEFT JOIN {$s_tb} as tb ON tb.subscription_id = {$table_name}.subscription_id where tb.product_id IN ( '" . implode( "','", $search_products ) . "' ) AND renew = 1 AND {$table_name}.status IN ('wc-processing','wc-completed') AND  date_paid >= %s and date_paid <= %s order by date_paid ASC ", $start_date, $end_date ), ARRAY_A ); //phpcs:ignore
			} else {
				$renews = $wpdb->get_results(
					$wpdb->prepare( "SELECT * from {$table_name} where renew = 1 AND {$table_name}.status IN ('wc-processing','wc-completed') and   date_paid >= %s and date_paid <= %s order by date_paid ASC ", $start_date, $end_date ),
					ARRAY_A
				);
			}

			$this->renews = $renews;

		} else {

			foreach ( $this->renews as $renew ) {
				if ( $renew['date_paid'] >= $start_date && $renew['date_paid'] <= $end_date ) {
					array_push( $renews, $renew );
				}
			}
		}

		return $renews;
	}


	/**
	 * Calculate MRR for a period
	 *
	 * @param Datetime $end_date Start date.
	 *
	 * @return int|mixed|string
	 */
	public function calculate_mrr( $end_date ) {
		$mrr     = 0;
		$sbs_ids = array();
		foreach ( $this->revenues as $revenue ) {
			if ( $revenue['update_date'] <= $end_date->format( 'Y-m-d H:m:i' ) && ! in_array( $revenue['subscription_id'], $sbs_ids ) ) { //phpcs:ignore
				$mrr      += $revenue['mrr'];
				$sbs_ids[] = $revenue['subscription_id'];
			}
		}

		return $mrr;
	}

	/**
	 * Calculate ARR for a period
	 *
	 * @param Datetime $end_date End date.
	 *
	 * @return int|mixed|string
	 */
	public function calculate_arr( $end_date ) {
		$arr     = 0;
		$sbs_ids = array();
		foreach ( $this->revenues as $revenue ) {
			if ( $revenue['update_date'] <= $end_date->format( 'Y-m-d H:m:i' ) && ! in_array( $revenue['subscription_id'], $sbs_ids ) ) { //phpcs:ignore
				$arr      += $revenue['arr'];
				$sbs_ids[] = $revenue['subscription_id'];
			}
		}

		return $arr;
	}

}
