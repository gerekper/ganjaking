<?php

namespace YITH\POS\RestApi\Reports\Cashiers;

defined( 'ABSPATH' ) || exit;

use \Automattic\WooCommerce\Admin\API\Reports\DataStore as ReportsDataStore;
use \Automattic\WooCommerce\Admin\API\Reports\DataStoreInterface;
use \Automattic\WooCommerce\Admin\API\Reports\TimeInterval;
use \Automattic\WooCommerce\Admin\API\Reports\SqlQuery;

use function YITH\POS\RestApi\get_sql_clauses_for_filters;


class DataStore extends ReportsDataStore implements DataStoreInterface {

    /**
     * Table used to get the data.
     *
     * @var string
     */
    protected static $table_name = 'wc_order_stats';

    /**
     * Cache identifier.
     *
     * @var string
     */
    protected $cache_key = 'cashiers';

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
     * Mapping columns to data type to return correct response types.
     *
     * @var array
     */
    protected $column_types = array(
        'orders_count'            => 'intval',
        'num_items_sold'          => 'intval',
        'total_sales'             => 'floatval',
        'taxes'                   => 'floatval',
        'shipping'                => 'floatval',
        'net_revenue'             => 'floatval',
        'avg_items_per_order'     => 'intval',
        'avg_order_value'         => 'floatval',
        'num_returning_customers' => 'intval',
        'num_new_customers'       => 'intval',
    );

    /**
     * Data store context used to pass to filters.
     *
     * @var string
     */
    protected $context = 'cashiers';

    /**
     * Assign report columns once full table name has been assigned.
     */
    protected function assign_report_columns() {
        $table_name = self::get_db_table_name();

        $this->report_columns = array(
            'orders_count'            => "SUM( CASE WHEN {$table_name}.parent_id = 0 THEN 1 ELSE 0 END ) as orders_count",
            'num_items_sold'          => "SUM({$table_name}.num_items_sold) as num_items_sold",
            'total_sales'             => "SUM({$table_name}.total_sales) AS total_sales",
            'taxes'                   => "SUM({$table_name}.tax_total) AS taxes",
            'shipping'                => "SUM({$table_name}.shipping_total) AS shipping",
            'net_revenue'             => "SUM({$table_name}.net_total) AS net_revenue",
            'avg_items_per_order'     => "SUM( {$table_name}.num_items_sold ) / SUM( CASE WHEN {$table_name}.parent_id = 0 THEN 1 ELSE 0 END ) AS avg_items_per_order",
            'avg_order_value'         => "SUM( {$table_name}.net_total ) / SUM( CASE WHEN {$table_name}.parent_id = 0 THEN 1 ELSE 0 END ) AS avg_order_value",
            'num_returning_customers' => "( COUNT( DISTINCT( {$table_name}.customer_id ) ) -  COUNT( DISTINCT( CASE WHEN {$table_name}.returning_customer = 0 THEN {$table_name}.customer_id END ) ) ) AS num_returning_customers",
            'num_new_customers'       => "COUNT( DISTINCT( CASE WHEN {$table_name}.returning_customer = 0 THEN {$table_name}.customer_id END ) ) AS num_new_customers",
        );
    }

    /**
     * Return the database query with parameters used for Categories report: time span and order status.
     *
     * @param array $query_args Query arguments supplied by the user.
     */
    protected function add_sql_query_params( $query_args ) {
        global $wpdb;
        $order_stats_lookup_table = self::get_db_table_name();
        $post_meta                = $wpdb->postmeta;

        $this->add_time_period_sql_params( $query_args, $order_stats_lookup_table );

        // join wp_order_product_lookup_table with relationships
        $this->subquery->add_sql_clause( 'left_join', "LEFT JOIN {$post_meta} ON {$order_stats_lookup_table}.order_id = {$post_meta}.post_id" );
        $this->subquery->add_sql_clause( 'where', " AND {$post_meta}.meta_key = '_yith_pos_cashier'  AND {$post_meta}.meta_value IS NOT NULL" );

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
     * @param array   $data           Data to paginate.
     * @param integer $page_no        Page number.
     * @param integer $items_per_page Number of items per page.
     * @return array
     */
    protected function page_records( $data, $page_no, $items_per_page ) {
        $offset = ( $page_no - 1 ) * $items_per_page;
        return array_slice( $data, $offset, $items_per_page );
    }

    /**
     * Enriches the category data.
     *
     * @param array $cashiers_data Categories data.
     * @param array $query_args    Query parameters.
     */
    protected function include_extended_info( &$cashiers_data, $query_args ) {
        foreach ( $cashiers_data as $key => $cashier_data ) {
            $extended_info = new \ArrayObject();
            if ( $query_args[ 'extended_info' ] ) {
                $extended_info[ 'name' ] = yith_pos_get_employee_name( $cashier_data[ 'cashier_id' ] );
            }
            $cashiers_data[ $key ][ 'extended_info' ] = $extended_info;
        }
    }

    /**
     * Returns the report data based on parameters supplied by the user.
     *
     * @param array $query_args Query parameters.
     * @return stdClass|WP_Error Data.
     */
    public function get_data( $query_args ) {
        global $wpdb;

        // These defaults are only partially applied when used via REST API, as that has its own defaults.
        $defaults   = array(
            'per_page'      => get_option( 'posts_per_page' ),
            'page'          => 1,
            'order'         => 'desc',
            'orderby'       => 'net_revenue',
            'before'        => TimeInterval::default_before(),
            'after'         => TimeInterval::default_after(),
            'fields'        => '*',
            'extended_info' => false,
        );
        $query_args = wp_parse_args( $query_args, $defaults );
        $this->normalize_timezones( $query_args, $defaults );

        /*
         * We need to get the cache key here because
         * parent::update_intervals_sql_params() modifies $query_args.
         */
        $cache_key = $this->get_cache_key( $query_args );
        $data      = $this->get_cached_data( $cache_key );

        if ( false === $data || ( defined( 'YITH_POS_REPORTS_DEBUG' ) && YITH_POS_REPORTS_DEBUG ) ) {
            $this->initialize_queries();

            $data = (object) array(
                'data'    => array(),
                'total'   => 0,
                'pages'   => 0,
                'page_no' => 0,
            );

            $this->subquery->add_sql_clause( 'select', $this->selected_columns( $query_args ) );
            $this->add_sql_query_params( $query_args );

            $order_status_filter = $this->get_status_subquery( $query_args );
            if ( $order_status_filter ) {
                $this->subquery->add_sql_clause( 'where', "AND ( {$order_status_filter} )" );
            }


            $clauses = get_sql_clauses_for_filters( $query_args, self::get_db_table_name(), false );

            if ( $clauses->from && $clauses->where ) {
                $this->subquery->add_sql_clause( 'join', $clauses->from );
                $this->subquery->add_sql_clause( 'where', "AND ( $clauses->where )" );
            }

            $order    = $query_args[ 'order' ];
            $order_by = $query_args[ 'orderby' ];


            $this->subquery->add_sql_clause( 'order_by', "{$order_by} {$order}" );

            $query         = $this->subquery->get_query_statement();
            $cashiers_data = $wpdb->get_results(
                $query,
                ARRAY_A
            ); // WPCS: cache ok, DB call ok, unprepared SQL ok.

            if ( null === $cashiers_data ) {
                return new \WP_Error( 'yith_pos_reports_cashiers_result_failed', __( 'Sorry, fetching data failed.', 'yith-point-of-sale-for-woocommerce' ), array( 'status' => 500 ) );
            }

            $record_count = count( $cashiers_data );
            $total_pages  = (int) ceil( $record_count / $query_args[ 'per_page' ] );
            if ( $query_args[ 'page' ] < 1 || $query_args[ 'page' ] > $total_pages ) {
                return $data;
            }

            $cashiers_data = $this->page_records( $cashiers_data, $query_args[ 'page' ], $query_args[ 'per_page' ] );
            $this->include_extended_info( $cashiers_data, $query_args );
            $cashiers_data = array_map( array( $this, 'cast_numbers' ), $cashiers_data );
            $data          = (object) array(
                'data'    => $cashiers_data,
                'total'   => $record_count,
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
        global $wpdb;
        $post_meta      = $wpdb->postmeta;
        $this->subquery = new SqlQuery( $this->context . '_subquery' );
        $this->subquery->add_sql_clause( 'select', "{$post_meta}.meta_value as cashier_id," );
        $this->subquery->add_sql_clause( 'from', self::get_db_table_name() );
        $this->subquery->add_sql_clause( 'group_by', "{$post_meta}.meta_value" );
    }
}
