<?php
/**
 * REST API Reports bundles controller
 *
 * @package  WooCommerce Product Bundles
 * @since    6.9.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Admin\API\Reports\ExportableInterface;

/**
 * REST API Reports bundles controller class.
 *
 * @version 6.9.0
 */
class WC_PB_Analytics_Stock_REST_Controller extends WC_REST_Reports_Controller implements ExportableInterface {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-analytics';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'reports/bundles/stock';

	/**
	 * Mapping between external parameter name and name used in query class.
	 *
	 * @var array
	 */
	protected $param_mapping = array(
		'products' => 'product_includes'
	);

	/**
	 * Get items.
	 *
	 * @param WP_REST_Request $request Request data.
	 *
	 * @return array|WP_Error
	 */
	public function get_items( $request ) {
		$args       = array();
		$registered = array_keys( $this->get_collection_params() );
		foreach ( $registered as $param_name ) {
			if ( isset( $request[ $param_name ] ) ) {
				if ( isset( $this->param_mapping[ $param_name ] ) ) {
					$args[ $this->param_mapping[ $param_name ] ] = $request[ $param_name ];
				} else {
					$args[ $param_name ] = $request[ $param_name ];
				}
			}
		}

		$reports       = new WC_PB_Analytics_Stock_Query( $args );
		$products_data = $reports->get_data();
		$data          = array();

		// Prepare and sanitize characters for response.
		foreach ( $products_data->data as $product_data ) {
			$item   = $this->prepare_item_for_response( $product_data, $request );
			$data[] = $this->prepare_response_for_collection( $item );
		}

		$response = rest_ensure_response( $data );
		$response->header( 'X-WP-Total', (int) $products_data->total );
		$response->header( 'X-WP-TotalPages', (int) $products_data->pages );

		$page      = $products_data->page_no;
		$max_pages = $products_data->pages;
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
	 * @param Array           $report  Report data.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function prepare_item_for_response( $report, $request ) {
		$data = $report;

		$context = ! empty( $request[ 'context' ] ) ? $request[ 'context' ] : 'view';
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
		 * @param object           $report   The original report object.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'woocommerce_rest_prepare_report_bundles', $response, $report, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param Array $object Object data.
	 * @return array        Links for the given post.
	 */
	protected function prepare_links( $object ) {
		$links = array(
			'bundle' => array(
				'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, 'products', $object[ 'bundle_id' ] ) ),
			),
			'product' => array(
				'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, 'products', $object[ 'product_id' ] ) ),
			),
		);

		return $links;
	}

	/**
	 * Get the Report's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'report_bundles',
			'type'       => 'object',
			'properties' => array(
				'bundle_id'     => array(
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'description' => __( 'Bundle ID.', 'woocommerce-product-bundles' ),
					'readonly'    => true,
				),
				'product_id'    => array(
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'description' => __( 'Bundled Product ID.', 'woocommerce-product-bundles' ),
					'readonly'    => true,
				),
				'bundled_item_id'     => array(
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'description' => __( 'Bundled Item ID.', 'woocommerce-product-bundles' ),
					'readonly'    => true,
				),
				'extended_info' => array(
					'bundle_name'      => array(
						'type'        => 'string',
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'Bundle name.', 'woocommerce-product-bundles' ),
						'readonly'    => true,
					),
					'name'        => array(
						'type'        => 'string',
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'Bundled Product name.', 'woocommerce-product-bundles' ),
						'readonly'    => true,
					),
					'permalink'        => array(
						'type'        => 'string',
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'Bundled Product link.', 'woocommerce-product-bundles' ),
						'readonly'    => true,
					),
					'stock_status'     => array(
						'type'        => 'string',
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'Bundled Product inventory status.', 'woocommerce-product-bundles' ),
						'readonly'    => true,
					),
					'stock_quantity'   => array(
						'description' => __( 'Stock quantity.', 'woocommerce-product-bundles' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit' ),
						'readonly'    => true,
					),
					'manage_stock'     => array(
						'description' => __( 'Manage stock.', 'woocommerce-product-bundles' ),
						'type'        => 'boolean',
						'context'     => array( 'view', 'edit' ),
						'readonly'    => true,
					),
					'sku'              => array(
						'type'        => 'string',
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'Bundled Product SKU.', 'woocommerce-product-bundles' ),
						'readonly'    => true,
					),
					'units_required'   => array(
						'description' => __( 'Quantity required.', 'woocommerce-product-bundles' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit' ),
						'readonly'    => true,
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
		$params                 = array();
		$params[ 'context' ]    = $this->get_context_param( array( 'default' => 'view' ) );
		$params[ 'page' ]       = array(
			'description'       => __( 'Current page of the collection.', 'woocommerce-product-bundles' ),
			'type'              => 'integer',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
			'minimum'           => 1,
		);
		$params[ 'per_page' ]   = array(
			'description'       => __( 'Maximum number of items to be returned in result set.', 'woocommerce-product-bundles' ),
			'type'              => 'integer',
			'default'           => 10,
			'minimum'           => 1,
			'maximum'           => 100,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params[ 'order' ]      = array(
			'description'       => __( 'Order sort attribute ascending or descending.', 'woocommerce-product-bundles' ),
			'type'              => 'string',
			'default'           => 'desc',
			'enum'              => array( 'asc', 'desc' ),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params[ 'orderby' ]    = array(
			'description'       => __( 'Sort collection by object attribute.', 'woocommerce-product-bundles' ),
			'type'              => 'string',
			'default'           => 'product_name',
			'enum'              => array(
				'product_name',
				'bundle_name',
				'units_required',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params[ 'products' ]   = array(
			'description'       => __( 'Limit result to items with specified product ids.', 'woocommerce-product-bundles' ),
			'type'              => 'array',
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
			'items'             => array(
				'type' => 'integer',
			),
		);
		$params[ 'extended_info' ] = array(
			'description'       => __( 'Add additional piece of info about each product to the report.', 'woocommerce-product-bundles' ),
			'type'              => 'boolean',
			'default'           => false,
			'sanitize_callback' => 'wc_string_to_bool',
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $params;
	}

	/**
	 * Get stock status column export value.
	 *
	 * @param array $status Stock status from report row.
	 * @return string
	 */
	protected function _get_stock_status( $status ) {
		$statuses = wc_get_product_stock_status_options();
		return isset( $statuses[ $status ] ) ? $statuses[ $status ] : '';
	}

	/**
	 * Get the column names for export.
	 *
	 * @return array Key value pair of Column ID => Label.
	 */
	public function get_export_columns() {
		$export_columns = array(
			'product_name'         => __( 'Bundle Title', 'woocommerce-product-bundles' ),
			'bundled_product_name' => __( 'Bundled Product Title', 'woocommerce-product-bundles' ),
			'units_required'       => __( 'Units Required', 'woocommerce-product-bundles' ),
		);

		if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {
			$export_columns[ 'stock_status' ] = __( 'Status', 'woocommerce-product-bundles' );
		}

		/**
		 * Filter to add or remove column names from the products report for
		 * export.
		 *
		 */
		return apply_filters(
			'woocommerce_report_bundles_stock_export_columns',
			$export_columns
		);
	}

	/**
	 * Get the column values for export.
	 *
	 * @param array $item Single report item/row.
	 * @return array Key value pair of Column ID => Row Value.
	 */
	public function prepare_item_for_export( $item ) {
		$export_item = array(
			'product_name'         => $item[ 'bundle_title' ],
			'bundled_product_name' => $item[ 'bundled_product_title' ],
			'units_required'       => $item[ 'units_required' ],
		);

		if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {
			$export_item[ 'stock_status' ] = $this->_get_stock_status( $item[ 'stock_status' ] );
		}

		/**
		 * Filter to prepare extra columns in the export item for the products
		 * report.
		 *
		 */
		return apply_filters(
			'woocommerce_report_bundles_stock_prepare_export_item',
			$export_item,
			$item
		);
	}
}
