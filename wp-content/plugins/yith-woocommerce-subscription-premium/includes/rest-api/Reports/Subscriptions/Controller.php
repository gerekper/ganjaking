<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Controller class
 *
 * @class   \YITH\Subscription\RestApi\Reports\Subscriptions\Controller
 * @package YITH WooCommerce Subscription
 * @since   2.3.0
 * @author  YITH
 */

namespace YITH\Subscription\RestApi\Reports\Subscriptions;

defined( 'ABSPATH' ) || exit;

use \Automattic\WooCommerce\Admin\API\Reports\Controller as ReportsController;
use \Automattic\WooCommerce\Admin\API\Reports\ExportableInterface;
use WP_Error;
use YITH\Subscription\RestApi\Reports\Subscriptions\Query;

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
	protected $rest_base = 'reports/subscriptions';


	/**
	 * Maps query arguments from the REST request.
	 *
	 * @param array $request Request array.
	 * @return array
	 */
	protected function prepare_reports_query( $request ) {
		$args                = array();
		$args['before']      = $request['before'];
		$args['after']       = $request['after'];
		$args['page']        = $request['page'];
		$args['per_page']    = $request['per_page'];
		$args['orderby']     = $request['orderby'];
		$args['order']       = $request['order'];
		$args['status_is']   = (array) $request['status_is'];
		$args['conversions'] = $request['conversions'];
		$args['renews']      = $request['renews'];

		return $args;
	}

	/**
	 * Get the column names for export.
	 *
	 * @return array Key value pair of Column ID => Label.
	 */
	public function get_export_columns() {
		$export_columns = array(
			'date_created'    => __( 'Date', 'yith-woocommerce-subscription' ),
			'subscription_id' => __( 'Subscription #', 'yith-woocommerce-subscription' ),
			'status'          => __( 'Status', 'yith-woocommerce-subscription' ),
			'customer_id'     => __( 'Customer', 'yith-woocommerce-subscription' ),
			'product_name'    => __( 'Product Name', 'yith-woocommerce-subscription' ),
			'net_total'       => __( 'Net Total', 'yith-woocommerce-subscription' ),
			'conversion_date' => __( 'Conversion Date', 'yith-woocommerce-subscription' ),
		);

		/**
		 * Filter to add or remove column names from the orders report for
		 * export.
		 *
		 * @since 1.6.0
		 */
		return apply_filters(
			'ywsbs_report_subscription_export_columns',
			$export_columns
		);
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
		$response->add_links( $this->prepare_links( $report ) );

		/**
		 * Filter a report returned from the API.
		 *
		 * Allows modification of the report data right before it is returned.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param object           $report The original report object.
		 * @param WP_REST_Request  $request Request used to generate the response.
		 */
		return apply_filters( 'ywsbs_report_subscription_prepare_report_subscriptions', $response, $report, $request );
	}


	/**
	 * Get the column values for export.
	 *
	 * @param array $item Single report item/row.
	 * @return array Key value pair of Column ID => Row Value.
	 */
	public function prepare_item_for_export( $item ) {
		$export_item = array(
			'date_created'    => $item['date_created'],
			'subscription_id' => $item['subscription_id'],
			'status'          => $item['status'],
			'customer_id'     => $item['customer_id'],
			'product_name'    => $item['product_name'],
			'net_total'       => $item['net_total'],
			'conversion_date' => $item['conversion_date'],
		);

		/**
		 * Filter to prepare extra columns in the export item for the subscription
		 * report.
		 *
		 * @since 1.6.0
		 */
		return apply_filters(
			'ywsbs_report_subscription_prepare_export_item',
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
		$query_args   = $this->prepare_reports_query( $request );
		$orders_query = new Query( $query_args );
		$report_data  = $orders_query->get_data();

		$data = array();

		foreach ( $report_data->data as $orders_data ) {
			$item   = $this->prepare_item_for_response( $orders_data, $request );
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
			'title'      => 'report_subscriptions',
			'type'       => 'object',
			'properties' => array(
				'subscription_id'  => array(
					'description' => __( 'Subscription ID.', 'yith-woocommerce-subscription' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'customer_id'      => array(
					'description' => __( 'Customer ID', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_created'     => array(
					'description' => __( "Date the order was created, in the site's timezone.", 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_created_gmt' => array(
					'description' => __( 'Date the order was created, on GMT.', 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),

				'product_name'     => array(
					'description' => __( 'Product Name', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'net_total'        => array(
					'description' => __( 'Net total revenue.', 'yith-woocommerce-subscription' ),
					'type'        => 'float',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'extended_info'    => array(
					'customer' => array(
						'type'        => 'object',
						'readonly'    => true,
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'Subscription customer information.', 'yith-woocommerce-subscription' ),
					),
				),
				'conversion_date'  => array(
					'description' => __( 'Date the subscription has been converted.', 'yith-woocommerce-subscription' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param WC_Reports_Query $object Object data.
	 * @return array
	 */
	protected function prepare_links( $object ) {
		$links = array(
			'subscription' => array(
				'href' => rest_url( sprintf( '/%s/subscription/%d', $this->namespace, $object['subscription_id'] ) ),
			),
		);

		return $links;
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
			'default'           => 'date_created',
			'enum'              => array(
				'date_created',
				'subscription_id',
				'status',
				'product_name',
				'net_total',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['interval'] = array(
			'description'       => __( 'Time interval to use for buckets in the returned data.', 'yith-woocommerce-subscription' ),
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

		$status                  = ywsbs_get_status();
		$params['status_is']     = array(
			'description'       => __( 'Limit result set to items that have the specified order status.', 'yith-woocommerce-subscription' ),
			'type'              => 'array',
			'sanitize_callback' => 'wp_parse_slug_list',
			'validate_callback' => 'rest_validate_request_arg',
			'items'             => array(
				'enum' => array_keys( $status ),
				'type' => 'string',
			),
		);
		$params['extended_info'] = array(
			'description'       => __( 'Add additional info about each subscription to the report.', 'yith-woocommerce-subscription' ),
			'type'              => 'boolean',
			'default'           => false,
			'sanitize_callback' => 'wc_string_to_bool',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['conversions'] = array(
			'description'       => __( 'Retrieves the converted subscriptions during the period selected', 'yith-woocommerce-subscription' ),
			'type'              => 'boolean',
			'default'           => false,
			'sanitize_callback' => 'wc_string_to_bool',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['renews'] = array(
			'description'       => __( 'Retrieves subscriptions that have been renewed in the period selected', 'yith-woocommerce-subscription' ),
			'type'              => 'boolean',
			'default'           => false,
			'sanitize_callback' => 'wc_string_to_bool',
			'validate_callback' => 'rest_validate_request_arg',
		);
		return $params;
	}


}
