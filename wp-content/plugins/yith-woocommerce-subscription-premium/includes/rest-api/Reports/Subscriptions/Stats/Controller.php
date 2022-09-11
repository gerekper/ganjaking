<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Controller class
 *
 * @class   \YITH\Subscription\RestApi\Reports\Subscriptions\Stats\Controller
 * @package YITH WooCommerce Subscription
 * @since   2.3.0
 * @author  YITH
 */


namespace YITH\Subscription\RestApi\Reports\Subscriptions\Stats;

defined( 'ABSPATH' ) || exit;

use \Automattic\WooCommerce\Admin\API\Reports\Controller as ReportsController;
use \Automattic\WooCommerce\Admin\API\Reports\ExportableInterface;
use WP_Error;
use YITH\Subscription\RestApi\Reports\Subscriptions\Stats\Query;

/**
 * Class Controller
 */
class Controller extends ReportsController implements ExportableInterface {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'yith-ywsbs';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'reports/subscriptions/stats';


	/**
	 * Maps query arguments from the REST request.
	 *
	 * @param array $request Request array.
	 * @return array
	 */
	protected function prepare_reports_query( $request ) {
		$args             = array();
		$args['before']   = $request['before'];
		$args['after']    = $request['after'];
		$args['interval'] = $request['interval'];
		$args['page']     = $request['page'];
		$args['per_page'] = $request['per_page'];
		$args['orderby']  = $request['orderby'];
		$args['order']    = $request['order'];
		$args['fields']   = $request['fields'];
		$args['match']    = $request['match'];

		return $args;
	}

	/**
	 * Get the column names for export.
	 *
	 * @return array Key value pair of Column ID => Label.
	 */
	public function get_export_columns() {
		return array(
			'subscription' => __( 'Subscription', 'woocommerce-admin' ),
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
			'subscription' => 'Subscription',
		);
	}

	/**
	 * Get all reports.
	 *
	 * @param WP_REST_Request $request Request data.
	 * @return WP_Error|array
	 */
	public function get_items( $request ) {
		$query_args         = $this->prepare_reports_query( $request );
		$subscription_query = new Query( $query_args );
		try {
			$report_data = $subscription_query->get_data();
		} catch ( ParameterException $e ) {
			return new \WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}

		$out_data = array(
			'totals'    => get_object_vars( $report_data->totals ),
			'intervals' => array(),
		);

		foreach ( $report_data->intervals as $interval_data ) {
			$item                    = $this->prepare_item_for_response( $interval_data, $request );
			$out_data['intervals'][] = $this->prepare_response_for_collection( $item );
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
	 * Get the Report's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {

		$totals = array(
			'subscriptions_count' => array(
				'description' => __( 'Subscription Count.', 'yith-woocommerce-subscription' ),
				'type'        => 'number',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'net_revenue'         => array(
				'description' => __( 'Net Sales.', 'yith-woocommerce-subscription' ),
				'type'        => 'number',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
				'format'      => 'currency',
			),
			'mrr'                 => array(
				'description' => __( 'MRR ( Monthly Recurring Revenue)', 'yith-woocommerce-subscription' ),
				'type'        => 'number',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
				'format'      => 'currency',
			),
			'arr'                 => array(
				'description' => __( 'ARR ( Annual Recurring Revenue)', 'yith-woocommerce-subscription' ),
				'type'        => 'number',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
				'format'      => 'currency',
			),
			'trial'               => array(
				'description' => __( 'New Trials', 'yith-woocommerce-subscription' ),
				'type'        => 'number',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
		);

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'report_products_stats',
			'type'       => 'object',
			'properties' => array(
				'totals' => array(
					'description' => __( 'Totals data.', 'yith-woocommerce-subscription' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
					'properties'  => $totals,
				),

			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Prepare a report object for serialization.
	 *
	 * @param Array           $report  Report data.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function prepare_item_for_response( $report, $request ) {
		$data = $report;

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );

		$data = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		/**
		 * Filter a report returned from the API.
		 *
		 * Allows modification of the report data right before it is returned.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param object           $report   The original report object.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'woocommerce_rest_prepare_subscriptions_stats', $response, $report, $request );
	}



}
