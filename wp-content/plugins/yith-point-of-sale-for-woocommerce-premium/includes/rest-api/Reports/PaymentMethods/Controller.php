<?php

namespace YITH\POS\RestApi\Reports\PaymentMethods;

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
    protected $rest_base = 'reports/payment-methods';

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
        $args[ 'include_empty' ] = $request[ 'include_empty' ];
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
        $query_args  = $this->prepare_reports_query( $request );
        $query       = new Query( $query_args );
        $report_data = $query->get_data();

        if ( is_wp_error( $report_data ) ) {
            return $report_data;
        }

        if ( !isset( $report_data->data ) || !isset( $report_data->page_no ) || !isset( $report_data->pages ) ) {
            return new WP_Error( 'yith_pos_rest_reports_payment_methods_invalid_response', __( 'Invalid response from data store.', 'yith-point-of-sale-for-woocommerce' ), array( 'status' => 500 ) );
        }

        $out_data = array();

        foreach ( $report_data->data as $datum ) {
            $item       = $this->prepare_item_for_response( $datum, $request );
            $out_data[] = $this->prepare_response_for_collection( $item );
        }

        $response = rest_ensure_response( $out_data );
        $response->header( 'X-WP-Total', (int) $report_data->total );
        $response->header( 'X-WP-TotalPages', (int) $report_data->pages );

	    if ( isset( $report_data->debug ) ) {
		    $response->header( 'X-WP-Debug', $report_data->debug );
	    }

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
        return apply_filters( 'yith_pos_rest_prepare_report_payment_methods', $response, $report, $request );
    }

    /**
     * Get the Report's schema, conforming to JSON Schema.
     *
     * @return array
     */
    public function get_item_schema() {
        $schema = array(
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => 'report_payment_methods',
            'type'       => 'object',
            'properties' => array(
                'payment_method'      => array(
                    'description' => __( 'Payment Method.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'payment_method_name' => array(
                    'description' => __( 'Payment Method Name.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'orders_count'        => array(
                    'description' => __( 'Number of orders.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'        => 'integer',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'amount'         => array(
                    'description' => __( 'Amount.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'        => 'number',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
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
            'default'           => 'amount',
            'enum'              => array(
                'orders_count',
                'amount',
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

        $params[ 'include_empty' ] = array(
            'description'       => __( 'Include payment methods with no sale.', 'yith-point-of-sale-for-woocommerce' ),
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
            'payment_method_name' => __( 'Payment Method Name', 'woocommerce-admin' ),
            'orders_count'        => __( 'Orders', 'woocommerce-admin' ),
            'amount'         => __( 'Amount', 'woocommerce-admin' ),
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
            'payment_method_name' => $item[ 'payment_method_name' ],
            'orders_count'        => $item[ 'orders_count' ],
            'amount'         => $item[ 'amount' ],
        );
    }
}
