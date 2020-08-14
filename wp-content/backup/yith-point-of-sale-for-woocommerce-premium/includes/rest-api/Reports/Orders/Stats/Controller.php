<?php

namespace YITH\POS\RestApi\Reports\Orders\Stats;

defined( 'ABSPATH' ) || exit;

/**
 * REST API Reports orders stats controller class.
 */
class Controller extends \Automattic\WooCommerce\Admin\API\Reports\Orders\Stats\Controller {

    protected $namespace = 'yith-pos';

    protected function prepare_reports_query( $request ) {
        $args = parent::prepare_reports_query( $request );

        $args[ 'store' ]    = isset( $request[ 'store' ] ) ? absint( $request[ 'store' ] ) : 0;
        $args[ 'register' ] = isset( $request[ 'register' ] ) ? absint( $request[ 'register' ] ) : 0;

        return $args;
    }

    /**
     * Get all reports.
     *
     * @param WP_REST_Request $request Request data.
     * @return array|WP_Error
     */
    public function get_items( $request ) {
        $query_args   = $this->prepare_reports_query( $request );
        $orders_query = new Query( $query_args );
        try {
            $report_data = $orders_query->get_data();
        } catch ( ParameterException $e ) {
            return new \WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
        }

        $out_data = array(
            'totals'    => get_object_vars( $report_data->totals ),
            'intervals' => array(),
        );

        foreach ( $report_data->intervals as $interval_data ) {
            $item                      = $this->prepare_item_for_response( $interval_data, $request );
            $out_data[ 'intervals' ][] = $this->prepare_response_for_collection( $item );
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
}
