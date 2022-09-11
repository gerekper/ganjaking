<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Controller class
 *
 * @class   \YITH\Subscription\RestApi\Reports\Customers\Controller
 * @package YITH WooCommerce Subscription
 * @since   2.3.0
 * @author  YITH
 */

namespace YITH\Subscription\RestApi\Reports\Customers;

defined( 'ABSPATH' ) || exit;

use \Automattic\WooCommerce\Admin\API\Reports\Controller as ReportsController;
use \Automattic\WooCommerce\Admin\API\Reports\ExportableInterface;
use WP_Error;
use YITH\Subscription\RestApi\Reports\Customers\Query;

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
	protected $rest_base = 'reports/customers';


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
		$args['page']     = $request['page'];
		$args['per_page'] = $request['per_page'];
		$args['orderby']  = $request['orderby'];
		$args['order']    = $request['order'];
		$args['type']     = $request['type'];
		return $args;
	}

	/**
	 * Get the column names for export.
	 *
	 * @return array Key value pair of Column ID => Label.
	 */
	public function get_export_columns() {
		$export_columns = array(
			'customer'               => __( 'Customer', 'yith-woocommerce-subscription' ),
			'total_paid'             => __( 'Total paid', 'yith-woocommerce-subscription' ),
			'username'               => __( 'Username', 'yith-woocommerce-subscription' ),
			'first_name'             => __( 'First Name', 'yith-woocommerce-subscription' ),
			'last_name'              => __( 'Last Name', 'yith-woocommerce-subscription' ),
			'email'                  => __( 'Email', 'yith-woocommerce-subscription' ),
			'subscription_active'    => __( 'Active subscriptions', 'yith-woocommerce-subscription' ),
			'subscription_cancelled' => __( 'Cancelled subscriptions', 'yith-woocommerce-subscription' ),
		);

		/**
		 * Filter to add or remove column names from the product report for
		 * export.
		 *
		 * @since 2.3.0
		 */
		return apply_filters( 'ywsbs_report_customer_export_columns', $export_columns );
	}

	/**
	 * Prepare a report object for serialization.
	 *
	 * @param stdClass        $report Report data.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function prepare_item_for_response( $report, $request ) {
		$data = $report;

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		/**
		 * Filter a report returned from the API.
		 *
		 * Allows modification of the report data right before it is returned.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param object           $report The original report object.
		 * @param WP_REST_Request  $request Request used to generate the response.
		 */
		return apply_filters( 'ywsbs_report_subscription_prepare_report_customers', $response, $report, $request );
	}

	/**
	 * Get the column values for export.
	 *
	 * @param array $item Single report item/row.
	 * @return array Key value pair of Column ID => Row Value.
	 */
	public function prepare_item_for_export( $item ) {
		$export_item = array(
			'customer'               => $item['customer'],
			'total_paid'             => $item['total_paid'],
			'username'               => $item['username'],
			'first_name'             => $item['first_name'],
			'last_name'              => $item['last_name'],
			'email'                  => $item['email'],
			'subscription_active'    => $item['subscription_active'],
			'subscription_cancelled' => $item['subscription_cancelled'],
		);

		/**
		 * Filter to prepare extra columns in the export item for the subscription
		 * report.
		 *
		 * @since 2.3.0
		 */
		return apply_filters(
			'ywsbs_report_customer_prepare_export_item',
			$export_item,
			$item
		);
	}

	/**
	 * Get all reports.
	 *
	 * @param WP_REST_Request $request Request data.
	 * @return array|WP_Error
	 */
	public function get_items( $request ) {
		$query_args      = $this->prepare_reports_query( $request );
		$customers_query = new \YITH\Subscription\RestApi\Reports\Customers\Query( $query_args );
		$report_data     = $customers_query->get_data();

		$data = array();

		foreach ( $report_data->data as $customer_data ) {
			$item   = $this->prepare_item_for_response( $customer_data, $request );
			$data[] = $this->prepare_response_for_collection( $item );
		}

		$response = rest_ensure_response( $data );
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
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'report_customers',
			'type'       => 'object',
			'properties' => array(
				'customer'   => array(
					'description' => __( 'Customer', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'total_paid' => array(
					'description' => __( 'Total paid', 'yith-woocommerce-subscription' ),
					'type'        => 'float',
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
		$params             = array();
		$params['context']  = $this->get_context_param( array( 'default' => 'view' ) );
		$params['page']     = array(
			'description'       => __( 'Current page of the collection.', 'yith-woocommerce-subscription' ),
			'type'              => 'integer',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
			'minimum'           => 1,
		);
		$params['per_page'] = array(
			'description'       => __( 'Maximum number of items to be returned in result set.', 'yith-woocommerce-subscription' ),
			'type'              => 'integer',
			'default'           => 10,
			'minimum'           => 0,
			'maximum'           => 100,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['after']    = array(
			'description'       => __( 'Limit response to resources published after a given ISO8601 compliant date.', 'yith-woocommerce-subscription' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['before']   = array(
			'description'       => __( 'Limit response to resources published before a given ISO8601 compliant date.', 'yith-woocommerce-subscription' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['order']    = array(
			'description'       => __( 'Order sort attribute ascending or descending.', 'yith-woocommerce-subscription' ),
			'type'              => 'string',
			'default'           => 'desc',
			'enum'              => array( 'asc', 'desc' ),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['orderby']  = array(
			'description'       => __( 'Sort collection by object attribute.', 'yith-woocommerce-subscription' ),
			'type'              => 'string',
			'default'           => 'total_paid',
			'enum'              => array(
				'total_paid',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['type']     = array(
			'description'       => __( 'Type of request', 'yith-woocommerce-subscription' ),
			'type'              => 'string',
			'default'           => 'dashboard',
			'enum'              => array(
				'dashboard',
				'leaderboard',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		return $params;
	}

}
