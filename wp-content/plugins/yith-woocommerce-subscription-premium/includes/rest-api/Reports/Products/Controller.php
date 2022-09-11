<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Controller class
 *
 * @class   \YITH\Subscription\RestApi\Reports\Products\Controller
 * @package YITH WooCommerce Subscription
 * @since   2.3.0
 * @author  YITH
 */

namespace YITH\Subscription\RestApi\Reports\Products;

defined( 'ABSPATH' ) || exit;

use \Automattic\WooCommerce\Admin\API\Reports\Controller as ReportsController;
use \Automattic\WooCommerce\Admin\API\Reports\ExportableInterface;
use WP_Error;
use YITH\Subscription\RestApi\Reports\Products\Query;

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
	protected $rest_base = 'reports/products';


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
		$args['products'] = $request['products'];
		return $args;
	}

	/**
	 * Get the column names for export.
	 *
	 * @return array Key value pair of Column ID => Label.
	 */
	public function get_export_columns() {
		$export_columns = array(
			'product_name' => __( 'Product Name', 'yith-woocommerce-subscription' ),
			'subscribers'  => __( 'Subscribers', 'yith-woocommerce-subscription' ),
			'mrr'          => __( 'MRR', 'yith-woocommerce-subscription' ),
		);

		/**
		 * Filter to add or remove column names from the product report for
		 * export.
		 *
		 * @since 2.3.0
		 */
		return apply_filters( 'ywsbs_report_product_export_columns', $export_columns );
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
		return apply_filters( 'ywsbs_report_subscription_prepare_report_products', $response, $report, $request );
	}

	/**
	 * Get the column values for export.
	 *
	 * @param array $item Single report item/row.
	 * @return array Key value pair of Column ID => Row Value.
	 */
	public function prepare_item_for_export( $item ) {
		$export_item = array(
			'product_name' => $item['product_name'],
			'subscribers'  => $item['subscribers'],
			'mrr'          => $item['mrr'],
		);

		/**
		 * Filter to prepare extra columns in the export item for the subscription
		 * report.
		 *
		 * @since 2.3.0
		 */
		return apply_filters(
			'ywsbs_report_product_prepare_export_item',
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
		$query_args     = $this->prepare_reports_query( $request );
		$products_query = new \YITH\Subscription\RestApi\Reports\Products\Query( $query_args );
		$report_data    = $products_query->get_data();

		$data = array();

		foreach ( $report_data->data as $product_data ) {
			$item   = $this->prepare_item_for_response( $product_data, $request );
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
				'product_name' => array(
					'description' => __( 'Product Name', 'yith-woocommerce-subscription' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'subscribers'  => array(
					'description' => __( 'Subscribers', 'yith-woocommerce-subscription' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'mrr'          => array(
					'description' => __( 'MRR', 'yith-woocommerce-subscription' ),
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
			'default'           => 'subscribers',
			'enum'              => array(
				'product_name',
				'subscribers',
				'mrr',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['products'] = array(
			'description'       => __( 'Limit result to items with specified product ids.', 'yith-woocommerce-subscription' ),
			'type'              => 'array',
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
			'items'             => array(
				'type' => 'integer',
			),
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

		return $params;
	}

}
