<?php

namespace YITH\POS\RestApi\Reports\Cashiers;

defined( 'ABSPATH' ) || exit;

use \Automattic\WooCommerce\Admin\API\Reports\Controller as ReportsController;
use \Automattic\WooCommerce\Admin\API\Reports\ExportableInterface;
use WP_Error;

class Controller extends ReportsController implements ExportableInterface {

    /**
     * Endpoint namespace.
     *
     * @var string
     */
    protected $namespace = 'yith-pos';

    /**
     * Route base.
     *
     * @var string
     */
    protected $rest_base = 'reports/cashiers';

    /**
     * Maps query arguments from the REST request.
     *
     * @param array $request Request array.
     * @return array
     */
    protected function prepare_reports_query( $request ) {
        $args                    = array();
        $args[ 'register' ]      = $request[ 'register' ];
        $args[ 'store' ]         = $request[ 'store' ];
        $args[ 'before' ]        = $request[ 'before' ];
        $args[ 'after' ]         = $request[ 'after' ];
        $args[ 'interval' ]      = $request[ 'interval' ];
        $args[ 'page' ]          = $request[ 'page' ];
        $args[ 'per_page' ]      = $request[ 'per_page' ];
        $args[ 'orderby' ]       = $request[ 'orderby' ];
        $args[ 'order' ]         = $request[ 'order' ];
        $args[ 'extended_info' ] = $request[ 'extended_info' ];
        $args[ 'cashiers' ]      = (array) $request[ 'cashiers' ];
        $args[ 'status_is' ]     = (array) $request[ 'status_is' ];
        $args[ 'status_is_not' ] = (array) $request[ 'status_is_not' ];

        return $args;
    }

    /**
     * Get all reports.
     *
     * @param WP_REST_Request $request Request data.
     * @return WP_Error|array
     */
    public function get_items( $request ) {
        $query_args     = $this->prepare_reports_query( $request );
        $cashiers_query = new Query( $query_args );
        $report_data    = $cashiers_query->get_data();

        if ( is_wp_error( $report_data ) ) {
            return $report_data;
        }

        if ( !isset( $report_data->data ) || !isset( $report_data->page_no ) || !isset( $report_data->pages ) ) {
            return new WP_Error( 'yith_pos_rest_reports_cashiers_invalid_response', __( 'Invalid response from data store.', 'yith-point-of-sale-for-woocommerce' ), array( 'status' => 500 ) );
        }

        $out_data = array();

        foreach ( $report_data->data as $datum ) {
            $item       = $this->prepare_item_for_response( $datum, $request );
            $out_data[] = $this->prepare_response_for_collection( $item );
        }

        $response = rest_ensure_response( $out_data );
        $response->header( 'X-WP-Total', (int) $report_data->total );
        $response->header( 'X-WP-TotalPages', (int) $report_data->pages );

        $page      = $report_data->page_no;
        $max_pages = $report_data->pages;
        $base      = add_query_arg( $request->get_query_params(), rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );
        if ( $page > 1 ) {
            $prev_page = $page - 1;
            if ( $prev_page > $max_pages ) {
                $prev_page = $max_pages;
            }
            $prev_link = add_query_arg( 'page', $prev_page, $base );
            $response->link_header( 'prev', $prev_link );
        }
        if ( $max_pages > $page ) {
            $next_page = $page + 1;
            $next_link = add_query_arg( 'page', $next_page, $base );
            $response->link_header( 'next', $next_link );
        }

        return $response;
    }

    /**
     * Prepare a report object for serialization.
     *
     * @param stdClass        $report  Report data.
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function prepare_item_for_response( $report, $request ) {
        $data = $report;

        $context = !empty( $request[ 'context' ] ) ? $request[ 'context' ] : 'view';
        $data    = $this->add_additional_fields_to_object( $data, $request );
        $data    = $this->filter_response_by_context( $data, $context );

        // Wrap the data in a response object.
        $response = rest_ensure_response( $data );

        /**
         * Filter a report returned from the API.
         * Allows modification of the report data right before it is returned.
         *
         * @param WP_REST_Response $response The response object.
         * @param object           $report   The original report object.
         * @param WP_REST_Request  $request  Request used to generate the response.
         */
        return apply_filters( 'yith_pos_rest_prepare_report_cashiers', $response, $report, $request );
    }

    /**
     * Get the Report's schema, conforming to JSON Schema.
     *
     * @return array
     */
    public function get_item_schema() {
        $schema = array(
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => 'report_cashiers',
            'type'       => 'object',
            'properties' => array(
                'cashier_id'     => array(
                    'description' => __( 'Cashier ID.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'        => 'integer',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'items_sold'     => array(
                    'description' => __( 'Amount of items sold.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'        => 'integer',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'net_revenue'    => array(
                    'description' => __( 'Total Sales.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'        => 'number',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'orders_count'   => array(
                    'description' => __( 'Number of orders.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'        => 'integer',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'products_count' => array(
                    'description' => __( 'Number of products.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'        => 'integer',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'extended_info'  => array(
                    'name' => array(
                        'type'        => 'string',
                        'readonly'    => true,
                        'context'     => array( 'view', 'edit' ),
                        'description' => __( 'Cashier\'s name.', 'yith-point-of-sale-for-woocommerce' ),
                    ),
                ),
            ),
        );

        return $this->add_additional_fields_schema( $schema );
    }

    /**
     * Get the query params for collections.
     *
     * @return array
     */
    public function get_collection_params() {
        $params                    = array();
        $params[ 'context' ]       = $this->get_context_param( array( 'default' => 'view' ) );
        $params[ 'page' ]          = array(
            'description'       => __( 'Current page of the collection.', 'yith-point-of-sale-for-woocommerce' ),
            'type'              => 'integer',
            'default'           => 1,
            'sanitize_callback' => 'absint',
            'validate_callback' => 'rest_validate_request_arg',
            'minimum'           => 1,
        );
        $params[ 'per_page' ]      = array(
            'description'       => __( 'Maximum number of items to be returned in result set.', 'yith-point-of-sale-for-woocommerce' ),
            'type'              => 'integer',
            'default'           => 10,
            'minimum'           => 1,
            'maximum'           => 100,
            'sanitize_callback' => 'absint',
            'validate_callback' => 'rest_validate_request_arg',
        );
        $params[ 'after' ]         = array(
            'description'       => __( 'Limit response to resources published after a given ISO8601 compliant date.', 'yith-point-of-sale-for-woocommerce' ),
            'type'              => 'string',
            'format'            => 'date-time',
            'validate_callback' => 'rest_validate_request_arg',
        );
        $params[ 'before' ]        = array(
            'description'       => __( 'Limit response to resources published before a given ISO8601 compliant date.', 'yith-point-of-sale-for-woocommerce' ),
            'type'              => 'string',
            'format'            => 'date-time',
            'validate_callback' => 'rest_validate_request_arg',
        );
        $params[ 'order' ]         = array(
            'description'       => __( 'Order sort attribute ascending or descending.', 'yith-point-of-sale-for-woocommerce' ),
            'type'              => 'string',
            'default'           => 'desc',
            'enum'              => array( 'asc', 'desc' ),
            'validate_callback' => 'rest_validate_request_arg',
        );
        $params[ 'orderby' ]       = array(
            'description'       => __( 'Sort collection by object attribute.', 'yith-point-of-sale-for-woocommerce' ),
            'type'              => 'string',
            'default'           => 'net_revenue',
            'enum'              => array(
                'cashier_id',
                'orders_count',
                'num_items_sold',
                'total_sales',
                'taxes',
                'shipping',
                'net_revenue',
                'avg_items_per_order',
                'avg_order_value',
                'num_returning_customers',
                'num_new_customers',
            ),
            'validate_callback' => 'rest_validate_request_arg',
        );
        $params[ 'interval' ]      = array(
            'description'       => __( 'Time interval to use for buckets in the returned data.', 'yith-point-of-sale-for-woocommerce' ),
            'type'              => 'string',
            'default'           => 'week',
            'enum'              => array(
                'hour',
                'day',
                'week',
                'month',
                'quarter',
                'year',
            ),
            'validate_callback' => 'rest_validate_request_arg',
        );
        $params[ 'status_is' ]     = array(
            'description'       => __( 'Limit result set to items that have the specified order status.', 'yith-point-of-sale-for-woocommerce' ),
            'type'              => 'array',
            'sanitize_callback' => 'wp_parse_slug_list',
            'validate_callback' => 'rest_validate_request_arg',
            'items'             => array(
                'enum' => $this->get_order_statuses(),
                'type' => 'string',
            ),
        );
        $params[ 'status_is_not' ] = array(
            'description'       => __( 'Limit result set to items that don\'t have the specified order status.', 'yith-point-of-sale-for-woocommerce' ),
            'type'              => 'array',
            'sanitize_callback' => 'wp_parse_slug_list',
            'validate_callback' => 'rest_validate_request_arg',
            'items'             => array(
                'enum' => $this->get_order_statuses(),
                'type' => 'string',
            ),
        );
        $params[ 'cashiers' ]      = array(
            'description'       => __( 'Limit result set to all items that have the specified cashier.', 'yith-point-of-sale-for-woocommerce' ),
            'type'              => 'array',
            'sanitize_callback' => 'wp_parse_id_list',
            'validate_callback' => 'rest_validate_request_arg',
            'items'             => array(
                'type' => 'integer',
            ),
        );
        $params[ 'extended_info' ] = array(
            'description'       => __( 'Add additional info about each cashier to the report.', 'yith-point-of-sale-for-woocommerce' ),
            'type'              => 'boolean',
            'default'           => false,
            'sanitize_callback' => 'wc_string_to_bool',
            'validate_callback' => 'rest_validate_request_arg',
        );

        $params[ 'register' ] = array(
            'description'       => __( 'The Register.', 'yith-point-of-sale-for-woocommerce' ),
            'type'              => 'integer',
            'default'           => 0,
            'sanitize_callback' => 'absint',
        );

        $params[ 'store' ] = array(
            'description'       => __( 'The Store.', 'yith-point-of-sale-for-woocommerce' ),
            'type'              => 'integer',
            'default'           => 0,
            'sanitize_callback' => 'absint',
        );

        return $params;
    }

    /**
     * Get the column names for export.
     *
     * @return array Key value pair of Column ID => Label.
     */
    public function get_export_columns() {
        return array(
            'cashier'        => __( 'Cashier', 'woocommerce-admin' ),
            'items_sold'     => __( 'Items Sold', 'woocommerce-admin' ),
            'net_revenue'    => __( 'Net Revenue', 'woocommerce-admin' ),
            'products_count' => __( 'Products', 'woocommerce-admin' ),
            'orders_count'   => __( 'Orders', 'woocommerce-admin' ),
        );
    }

    /**
     * Get the column values for export.
     *
     * @param array $item Single report item/row.
     * @return array Key value pair of Column ID => Row Value.
     */
    public function prepare_item_for_export( $item ) {
        return array(
            'cashier'        => $item[ 'extended_info' ][ 'name' ],
            'items_sold'     => $item[ 'items_sold' ],
            'net_revenue'    => $item[ 'net_revenue' ],
            'products_count' => $item[ 'products_count' ],
            'orders_count'   => $item[ 'orders_count' ],
        );
    }
}
