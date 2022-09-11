<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * DataStore class
 *
 * @class   \YITH\Subscription\RestApi\Reports\Subscriptions\DataStore
 * @package YITH WooCommerce Subscription
 * @since   2.3.0
 * @author  YITH
 */

namespace YITH\Subscription\RestApi\Reports\Subscriptions;

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
	 * Order by setting used for sorting categories data.
	 *
	 * @var string
	 */
	private $order_by = '';

	/**
	 * Order setting used for sorting categories data.
	 *
	 * @var string
	 */
	private $order = '';

	/**
	 * Cache identifier.
	 *
	 * @var string
	 */
	protected $cache_key = 'subscriptions';


	/**
	 * Mapping columns to data type to return correct response types.
	 *
	 * @var array
	 */
	protected $column_types = array(
		'subscription_id'   => 'intval',
		'customer_id'       => 'intval',
		'status'            => 'strval',
		'date_created'      => 'strval',
		'date_created_gmt'  => 'strval',
		'product_id'        => 'intval',
		'product_name'      => 'strval',
		'variation_id'      => 'intval',
		'currency'          => 'strval',
		'quantity'          => 'intval',
		'total'             => 'floatval',
		'tax'               => 'floatval',
		'shipping'          => 'floatval',
		'net_revenue'       => 'floatval',
		'next_payment_date' => 'strval',
		'orders_paid'       => 'intval',
		'mrr'               => 'floatval',
		'arr'               => 'floatval',
		'trial'             => 'intval',
		'conversion_date'   => 'strval',
		'cancelled_date'    => 'strval',
	);

	/**
	 * Data store context used to pass to filters.
	 *
	 * @var string
	 */
	protected $context = 'subscriptions_stats';

	/**
	 * Assign report columns once full table name has been assigned.
	 */
	protected function assign_report_columns() {
		$table_name = self::get_db_table_name();

		$this->report_columns = array(
			'subscription_id'   => "{$table_name}.subscription_id",
			'status'            => "{$table_name}.status",
			'date_created'      => "{$table_name}.date_created",
			'date_created_gmt'  => "{$table_name}.date_created_gmt",
			'currency'          => "{$table_name}.currency",
			'quantity'          => "{$table_name}.quantity",
			'product_name'      => "{$table_name}.product_name",
			'product_id'        => "{$table_name}.product_id",
			'total'             => "{$table_name}.total",
			'tax'               => "{$table_name}.tax_total",
			'shipping'          => "{$table_name}.shipping_total",
			'net_revenue'       => "{$table_name}.net_total",
			'next_payment_date' => "{$table_name}.next_payment_due_date",
			'orders_paid'       => "{$table_name}.orders_paid",
			'customer_id'       => "{$table_name}.customer_id",
			'mrr'               => "{$table_name}.mrr",
			'arr'               => "{$table_name}.arr",
			'trial'             => "{$table_name}.trial",
			'conversion_date'   => "{$table_name}.conversion_date",
			'cancelled_date'    => "{$table_name}.cancelled_date",
		);
	}


	/**
	 * Initialize query objects.
	 */
	protected function initialize_queries() {
		$this->clear_all_clauses();
		$this->subquery = new SqlQuery( $this->context . '_subquery' );
		$this->subquery->add_sql_clause( 'select', self::get_db_table_name() . '.subscription_id' );
		$this->subquery->add_sql_clause( 'from', self::get_db_table_name() );
	}

	/**
	 * Normalizes order_by clause to match to SQL query.
	 *
	 * @param string $order_by Order by option request by user.
	 * @return string
	 */
	protected function normalize_order_by( $order_by ) {
		if ( 'date' === $order_by ) {
			return 'date_created';
		}

		return $order_by;
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
		$defaults   = array(
			'per_page'      => get_option( 'posts_per_page' ),
			'page'          => 1,
			'order'         => 'DESC',
			'orderby'       => 'date_created',
			'before'        => '',
			'after'         => '',
			'fields'        => '*',
			'status_is'     => array(),
			'extended_info' => true,
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

			$renews_subquery = $this->get_renews_subquery( $query_args );
			if ( $renews_subquery ) {
				$this->subquery->add_sql_clause( 'where', "AND {$renews_subquery}" );
			}

			$conversions_subquery = $this->get_conversions_subquery( $query_args );
			if ( $conversions_subquery ) {
				$this->subquery->add_sql_clause( 'where', "AND {$conversions_subquery}" );
			}

			$status_subquery = $this->get_status_subquery( $query_args );
			if ( $status_subquery ) {
				$this->subquery->add_sql_clause( 'where', "AND {$status_subquery}" );
			}

			if ( empty( $renews_subquery ) && empty( $conversions_subquery ) ) {
				$this->add_intervals_sql_params( $query_args, $table_name );
				$this->add_time_period_sql_params( $query_args, $table_name );
			}

			$db_records_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM ( {$this->subquery->get_query_statement()} ) AS tt" ); //phpcs:ignore.

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

			$subscriptions_data = $wpdb->get_results( $this->subquery->get_query_statement(), ARRAY_A ); // //phpcs:ignore

			if ( null === $subscriptions_data ) {
				return $data;
			}

			if ( $query_args['extended_info'] ) {
				$this->include_extended_info( $subscriptions_data, $query_args );
			}

			$subscriptions_data = array_map( array( $this, 'cast_numbers' ), $subscriptions_data );

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
	 * Fills WHERE clause of SQL request with date-related constraints.
	 *
	 * @param array  $query_args Parameters supplied by the user.
	 * @param string $table_name Name of the db table relevant for the date constraint.
	 */
	protected function add_time_period_sql_params( $query_args, $table_name ) {
		$this->clear_sql_clause( array( 'from', 'where_time', 'where' ) );
		if ( isset( $this->subquery ) ) {
			$this->subquery->clear_sql_clause( 'where_time' );
		}

		$column = 'date_created';
		if ( isset( $query_args['status_is'] ) && in_array( 'cancelled', $query_args['status_is'], true ) ) {
			$column = 'cancelled_date';
		}
		if ( isset( $query_args['before'] ) && '' !== $query_args['before'] ) {
			if ( is_a( $query_args['before'], 'WC_DateTime' ) ) {
				$datetime_str = $query_args['before']->date( TimeInterval::$sql_datetime_format );
			} else {
				$datetime_str = $query_args['before']->format( TimeInterval::$sql_datetime_format );
			}
			if ( isset( $this->subquery ) ) {
				$this->subquery->add_sql_clause( 'where_time', "AND {$table_name}.{$column} <= '$datetime_str'" );
			} else {
				$this->add_sql_clause( 'where_time', "AND {$table_name}.{$column} <= '$datetime_str'" );
			}
		}

		if ( isset( $query_args['after'] ) && '' !== $query_args['after'] ) {
			if ( is_a( $query_args['after'], 'WC_DateTime' ) ) {
				$datetime_str = $query_args['after']->date( TimeInterval::$sql_datetime_format );
			} else {
				$datetime_str = $query_args['after']->format( TimeInterval::$sql_datetime_format );
			}
			if ( isset( $this->subquery ) ) {
				$this->subquery->add_sql_clause( 'where_time', "AND {$table_name}.{$column} >= '$datetime_str'" );
			} else {
				$this->add_sql_clause( 'where_time', "AND {$table_name}.{$column} >= '$datetime_str'" );
			}
		}
	}

	/**
	 * Enriches the subscription data.
	 *
	 * @param array $subscriptions_data Orders data.
	 * @param array $query_args Query parameters.
	 */
	protected function include_extended_info( &$subscriptions_data, $query_args ) {

		$customers        = $this->get_customers_by_subscriptions( $subscriptions_data );
		$mapped_customers = $this->map_array_by_key( $customers, 'user_id' );
		$mapped_data      = array();

		foreach ( $subscriptions_data as $key => $subscription_data ) {
			$defaults                                    = array(
				'customer' => array(),
			);
			$subscriptions_data[ $key ]['extended_info'] = isset( $mapped_data[ $subscription_data['subscription_id'] ] ) ? array_merge( $defaults, $mapped_data[ $subscription_data['subscription_id'] ] ) : $defaults;

			if ( $subscription_data['customer_id'] && isset( $mapped_customers[ $subscription_data['customer_id'] ] ) ) {
				$subscriptions_data[ $key ]['extended_info']['customer'] = $mapped_customers[ $subscription_data['customer_id'] ];
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
	 * Get customer data from Subscription data.
	 *
	 * @param array $subscriptions Array of subscription data.
	 * @return array
	 */
	protected function get_customers_by_subscriptions( $subscriptions ) {
		global $wpdb;

		$customer_lookup_table = $wpdb->prefix . 'wc_customer_lookup';
		$customer_ids          = array();

		foreach ( $subscriptions as $subscription ) {
			if ( $subscription['customer_id'] ) {
				$customer_ids[] = intval( $subscription['customer_id'] );
			}
		}

		if ( empty( $customer_ids ) ) {
			return array();
		}

		$customer_ids = implode( ',', $customer_ids );
		$customers    = $wpdb->get_results( "SELECT * FROM {$customer_lookup_table} WHERE user_id IN ({$customer_ids})", ARRAY_A ); //phpcs:ignore

		return $customers;
	}

	/**
	 * Returns subscription status subquery to be used in WHERE SQL query, based on query arguments from the user.
	 *
	 * @param array  $query_args Parameters supplied by the user.
	 * @param string $operator AND or OR, based on match query argument.
	 * @return string
	 */
	protected function get_status_subquery( $query_args, $operator = 'AND' ) {

		$table_name = self::get_db_table_name();
		$subqueries = array();

		if ( isset( $query_args['status_is'] ) && is_array( $query_args['status_is'] ) && count( $query_args['status_is'] ) > 0 ) {
			$allowed_statuses = $query_args['status_is'];
			if ( $allowed_statuses ) {
				$subqueries[] = "{$table_name}.status IN ( '" . implode( "','", $allowed_statuses ) . "' )";
			}
		}

		return implode( " $operator ", $subqueries );
	}

	/**
	 * Returns subscription status subquery to be used in WHERE SQL query, based on query arguments from the user.
	 *
	 * @param array  $query_args Parameters supplied by the user.
	 * @param string $operator AND or OR, based on match query argument.
	 * @return string
	 */
	protected function get_conversions_subquery( $query_args, $operator = 'AND' ) {
		$subqueries = array();

		if ( isset( $query_args['conversions'] ) && false != $query_args['conversions'] ) { //phpcs:ignore
			$table_name = self::get_db_table_name();
			$start_date = $query_args['after'];
			$end_date   = $query_args['before'];
			$start_date = $start_date->format( 'Y-m-d H:i:s' );
			$end_date   = $end_date->format( 'Y-m-d H:i:s' );

			$subqueries[] = "{$table_name}.conversion_date >= '{$start_date}' AND {$table_name}.conversion_date <= '{$end_date}'";
		}
		return implode( " $operator ", $subqueries );
	}

	/**
	 * Returns subscription renews subquery, select the subscriptions that have been renewed in the period
	 *
	 * @param array  $query_args Parameters supplied by the user.
	 * @param string $operator AND or OR, based on match query argument.
	 * @return string
	 */
	protected function get_renews_subquery( $query_args, $operator = 'AND' ) {
		$subqueries = array();

		if ( isset( $query_args['renews'] ) && false != $query_args['renews'] ) { //phpcs:ignore
			global $wpdb;
			$table_name = $wpdb->prefix . 'yith_ywsbs_order_lookup';
			$start_date = $query_args['after'];
			$end_date   = $query_args['before'];
			$start_date = $start_date->format( 'Y-m-d H:i:s' );
			$end_date   = $end_date->format( 'Y-m-d H:i:s' );

			$subscriptions = $wpdb->get_col( $wpdb->prepare( "Select subscription_id from {$table_name} where renew = 1 and date_paid >= %s and date_paid <= %s group by subscription_id order by subscription_id", $start_date, $end_date ) ); //phpcs:ignore
			if ( ! empty( $subscriptions ) ) {

				$table_name        = self::get_db_table_name();
				$subscription_list = "('" . implode( "', '", $subscriptions ) . "')";
				$subqueries[]      = "{$table_name}.subscription_id IN {$subscription_list} ";
			}
		}
		return implode( " $operator ", $subqueries );
	}
}
