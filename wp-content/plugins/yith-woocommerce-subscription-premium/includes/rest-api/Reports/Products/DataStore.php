<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

// phpcs:disable  WordPress.DB.DirectDatabaseQuery.NoCaching
// phpcs:disable  WordPress.DateTime.CurrentTimeTimestamp.Requested
// phpcs:disable  WordPress.DB.PreparedSQL.InterpolatedNotPrepared
// phpcs:disable  WordPress.DB.DirectDatabaseQuery.DirectQuery
// phpcs:disable  WordPress.DB.PreparedSQL.NotPrepared

/**
 * DataStore class
 *
 * @class   \YITH\Subscription\RestApi\Reports\Products\DataStore
 * @package YITH WooCommerce Subscription
 * @since   2.3.0
 * @author  YITH
 */


namespace YITH\Subscription\RestApi\Reports\Products;

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
	protected static $table_name = 'yith_ywsbs_stats';

	/**
	 * Cache identifier.
	 *
	 * @var string
	 */
	protected $cache_key = 'products';

	/**
	 * Mapping columns to data type to return correct response types.
	 *
	 * @var array
	 */
	protected $column_types = array(
		'product_name' => 'strval',
		'product_id'   => 'intval',
		'subscribers'  => 'intval',
		'mrr'          => 'floatval',
		'net_total'    => 'floatval',
		'total'        => 'floatval',
	);

	/**
	 * Data store context used to pass to filters.
	 *
	 * @var string
	 */
	protected $context = 'products';

	/**
	 * Assign report columns once full table name has been assigned.
	 */
	protected function assign_report_columns() {
		$table_name = self::get_db_table_name();

		$this->report_columns = array(
			'product_name' => "{$table_name}.product_id as product_id",
			'subscribers'  => "SUM(  {$table_name}.quantity ) as subscribers",
			'mrr'          => "SUM(  {$table_name}.mrr ) as mrr",
			'net_total'    => "SUM(  {$table_name}.net_total ) as net_total",
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
			'products' => '',
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

			$data = (object)array(
				'data'    => array(),
				'total'   => 0,
				'pages'   => 0,
				'page_no' => 0,
			);

			$selections = $this->selected_columns( $query_args );
			$params     = $this->get_limit_params( $query_args );
			$this->add_order_by_sql_params( $query_args );
			$this->add_intervals_sql_params( $query_args, $table_name );
			$this->add_time_period_sql_params( $query_args, $table_name );

			$this->add_from_sql_params( $query_args, 'orderby', "{$table_name}.product_id" );

			$products_subquery = $this->get_include_products_subquery( $query_args );
			if ( $products_subquery ) {
				$this->subquery->add_sql_clause( 'where', "AND {$products_subquery}" );
			}

			$db_records_count = (int)$wpdb->get_var(
				"SELECT COUNT(*) FROM (
					{$this->subquery->get_query_statement()}
				) AS tt"
			); //phpcs:ignore.

			if ( 0 === $params['per_page'] ) {
				$total_pages = 0;
			} else {
				$total_pages = (int)ceil( $db_records_count / $params['per_page'] );
			}

			if ( $query_args['page'] < 1 || $query_args['page'] > $total_pages ) {
				$data = (object)array(
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
			);

			if ( null === $subscriptions_data ) {
				return $data;
			}

			$subscriptions_data = array_map( array( $this, 'cast_numbers' ), $subscriptions_data );
			$this->normalize_product_name( $subscriptions_data );
			$this->normalize_total( $subscriptions_data );

			$data = (object)array(
				'data'    => $subscriptions_data,
				'total'   => $db_records_count,
				'pages'   => $total_pages,
				'page_no' => (int)$query_args['page'],
			);

			$this->set_cached_data( $cache_key, $data );
		}

		return $data;

	}

	/**
	 * Add the product names of products to the results
	 *
	 * @param array $subscriptions_data Result data.
	 */
	protected function normalize_product_name( &$subscriptions_data ) {
		foreach ( $subscriptions_data as $key => $subscription ) {
			$product = wc_get_product( $subscription['product_id'] );
			if ( $product ) {
				$subscriptions_data[ $key ]['product_name'] = $product->get_formatted_name();
			}
		}
	}

	/**
	 * Add the product names of products to the results
	 *
	 * @param array $subscriptions_data Result data.
	 */
	protected function normalize_total( &$subscriptions_data ) {
		global $wpdb;
		$table_name  = self::get_db_table_name();
		$order_table = $wpdb->prefix . 'yith_ywsbs_order_lookup';

		foreach ( $subscriptions_data as $key => $subscription ) {
			$product_id = $subscriptions_data[ $key ]['product_id'];

			$result = $wpdb->get_row(
				"SELECT SUM( o.net_total ) as renew_total from {$order_table} as o LEFT JOIN {$table_name} as s ON ( o.subscription_id = s.subscription_id ) 
WHERE s.product_id = {$product_id} AND o.status IN ('wc-processing','wc-completed') AND s.status NOT IN ('pending', 'trial') "
			);
			if ( $result ) {
				$subscriptions_data[ $key ]['total'] = $result->renew_total;
			} else {
				$subscriptions_data[ $key ]['total'] = $subscriptions_data[ $key ]['net_total'];
			}
		}
	}

	/**
	 * Fills FROM clause of SQL request based on user supplied parameters.
	 *
	 * @param array  $query_args Parameters supplied by the user.
	 * @param string $arg_name Target of the JOIN sql param.
	 * @param string $id_cell ID cell identifier, like `table_name.id_column_name`.
	 */
	protected function add_from_sql_params( $query_args, $arg_name, $id_cell ) {
		global $wpdb;

		$type = 'join';

		// Order by product name requires extra JOIN.
		switch ( $query_args['orderby'] ) {
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
			$this->subquery->add_sql_clause( $type, $join );
		}
	}

	/**
	 * Initialize query objects.
	 */
	protected function initialize_queries() {
		$this->clear_all_clauses();
		$this->subquery = new SqlQuery( $this->context . '_subquery' );
		$this->subquery->add_sql_clause( 'select', self::get_db_table_name() . '.subscription_id' );
		$this->subquery->add_sql_clause( 'from', self::get_db_table_name() );
		$this->subquery->add_sql_clause( 'group_by', 'product_id' );
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
