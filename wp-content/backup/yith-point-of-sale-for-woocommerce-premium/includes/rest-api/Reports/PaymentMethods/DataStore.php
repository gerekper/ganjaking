<?php

namespace YITH\POS\RestApi\Reports\PaymentMethods;

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
    protected $cache_key = 'payment-methods';

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
        'orders_count' => 'intval',
        'amount'  => 'floatval',
    );

    /**
     * Data store context used to pass to filters.
     *
     * @var string
     */
    protected $context = 'payment-methods';

    protected static $post_meta_label       = 'pm_pm';
    protected static $payment_method_prefix = '_yith_pos_gateway_';

    /**
     * Assign report columns once full table name has been assigned.
     */
    protected function assign_report_columns() {
        $table_name      = self::get_db_table_name();
        $post_meta_label = self::$post_meta_label;

        $this->report_columns = array(
            'orders_count' => "SUM( CASE WHEN {$table_name}.parent_id = 0 THEN 1 ELSE 0 END ) as orders_count",
            'amount'  => "SUM({$post_meta_label}.meta_value) AS amount",
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
        $post_meta_label          = self::$post_meta_label;

        $this->add_time_period_sql_params( $query_args, $order_stats_lookup_table );

        $payment_methods        = yith_pos_get_enabled_gateways_option();
        $payment_methods_values = array_map( array( $this, 'add_prefix_to_payment_method' ), $payment_methods );
        $payment_methods_values = "('" . implode( "', '", $payment_methods_values ) . "')";

        // join wp_order_product_lookup_table with relationships
        $this->subquery->add_sql_clause( 'left_join', "LEFT JOIN {$post_meta} as {$post_meta_label} ON {$order_stats_lookup_table}.order_id = {$post_meta_label}.post_id" );
        $this->subquery->add_sql_clause( 'where', " AND {$post_meta_label}.meta_key IN {$payment_methods_values}  AND {$post_meta_label}.meta_value IS NOT NULL" );

    }

    /**
     * add the prefix to the payment method
     *
     * @param string $payment_method
     * @return string
     */
    public function add_prefix_to_payment_method( $payment_method ) {
        return self::$payment_method_prefix . $payment_method;
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
     * Enriches the data.
     *
     * @param array $data       data.
     * @param array $query_args Query parameters.
     */
    protected function add_info( &$data, $query_args ) {
        if ( WC()->payment_gateways() ) {
            $payment_gateways = WC()->payment_gateways->payment_gateways();
        } else {
            $payment_gateways = array();
        }

        $found_payment_methods = array();

        foreach ( $data as $key => $_data ) {
            if ( $_data[ 'payment_method' ] ) {
                $payment_method                        = $_data[ 'payment_method' ];
                $found_payment_methods[]               = $payment_method;
                $data[ $key ][ 'payment_method_name' ] = isset( $payment_gateways[ $payment_method ] ) ? $payment_gateways[ $payment_method ]->get_title() : $payment_method;
            }
        }

        if ( $query_args[ 'include_empty' ] ) {
            $enabled_gateways      = yith_pos_get_enabled_gateways_option();
            $empty_payment_methods = array_diff( $enabled_gateways, $found_payment_methods );
            foreach ( $empty_payment_methods as $payment_method ) {
                $data[] = array(
                    'payment_method'      => $payment_method,
                    'payment_method_name' => isset( $payment_gateways[ $payment_method ] ) ? $payment_gateways[ $payment_method ]->get_title() : $payment_method,
                    'orders_count'        => 0,
                    'amount'         => 0,
                );
            }
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
            'orderby'       => 'amount',
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

        $debug_enabled = ( defined( 'YITH_POS_REPORTS_DEBUG' ) && YITH_POS_REPORTS_DEBUG );

        if ( false === $data || $debug_enabled ) {
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

            $query        = $this->subquery->get_query_statement();
            $results_data = $wpdb->get_results(
                $query,
                ARRAY_A
            ); // WPCS: cache ok, DB call ok, unprepared SQL ok.
            if ( $debug_enabled ) {
                $data->debug = $query;
            }

            if ( null === $results_data ) {
                return new \WP_Error( 'yith_pos_reports_payment_methods_result_failed', __( 'Sorry, fetching data failed.', 'yith-point-of-sale-for-woocommerce' ), array( 'status' => 500 ) );
            }

            $record_count = count( $results_data );
            $total_pages  = (int) ceil( $record_count / $query_args[ 'per_page' ] );
            if ( $query_args[ 'page' ] < 1 || $query_args[ 'page' ] > $total_pages ) {
                return $data;
            }

            $results_data = $this->page_records( $results_data, $query_args[ 'page' ], $query_args[ 'per_page' ] );
            $this->add_info( $results_data, $query_args );
            $results_data = array_map( array( $this, 'cast_numbers' ), $results_data );
            $data         = (object) array(
                'data'    => $results_data,
                'debug'   => $debug_enabled ? $query : '',
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
        $post_meta_label = self::$post_meta_label;

        $this->subquery = new SqlQuery( $this->context . '_subquery' );

        $prefix                = self::$payment_method_prefix;
        $select_payment_method = "REPLACE({$post_meta_label}.meta_key, '{$prefix}', '')";
        $this->subquery->add_sql_clause( 'select', "{$select_payment_method} as payment_method," );

        $this->subquery->add_sql_clause( 'from', self::get_db_table_name() );
        $this->subquery->add_sql_clause( 'group_by', "payment_method" );
    }
}
