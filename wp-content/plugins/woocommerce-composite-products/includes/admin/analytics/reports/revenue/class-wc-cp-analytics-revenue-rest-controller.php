<?php
/**
 * REST API Reports composites controller.
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    8.3.0-dev
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Admin\API\Reports\ExportableInterface;

/**
 * WC_CP_Analytics_Revenue_REST_Controller class.
 *
 * @version 8.3.0-dev
 */
class WC_CP_Analytics_Revenue_REST_Controller extends WC_REST_Reports_Controller implements ExportableInterface {

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
	protected $rest_base = 'reports/composites';

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

		$reports       = new WC_CP_Analytics_Revenue_Query( $args );
		$products_data = $reports->get_data();

		$data = array();

		// Prepare and sanitize characters for response.
		foreach ( $products_data->data as $product_data ) {
			$item = $this->prepare_item_for_response( $product_data, $request );
			if ( isset( $item->data[ 'extended_info' ][ 'name' ] ) ) {
				$item->data[ 'extended_info' ][ 'name' ] = wp_strip_all_tags( $item->data[ 'extended_info' ][ 'name' ] );
			}
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
		return apply_filters( 'woocommerce_rest_prepare_report_composites', $response, $report, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param Array $object Object data.
	 * @return array        Links for the given post.
	 */
	protected function prepare_links( $object ) {
		$links = array(
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
			'title'      => 'report_composites',
			'type'       => 'object',
			'properties' => array(
				'product_id'            => array(
					'type'        => 'integer',
					'readonly'    => true,
					'context'     => array( 'view', 'edit' ),
					'description' => __( 'Product ID.', 'woocommerce-composite-products' ),
				),
				'items_sold'            => array(
					'type'        => 'integer',
					'readonly'    => true,
					'context'     => array( 'view', 'edit' ),
					'description' => __( 'Number of items sold.', 'woocommerce-composite-products' ),
				),
				'composited_items_sold' => array(
					'type'        => 'integer',
					'readonly'    => true,
					'context'     => array( 'view', 'edit' ),
					'description' => __( 'Number of composited items sold.', 'woocommerce-composite-products' ),
				),
				'net_revenue'           => array(
					'type'        => 'number',
					'readonly'    => true,
					'context'     => array( 'view', 'edit' ),
					'description' => __( 'Total Net Sales of all items sold.', 'woocommerce-composite-products' ),
				),
				'orders_count'          => array(
					'type'        => 'integer',
					'readonly'    => true,
					'context'     => array( 'view', 'edit' ),
					'description' => __( 'Number of orders product appeared in.', 'woocommerce-composite-products' ),
				),
				'extended_info'         => array(
					'name'         => array(
						'type'        => 'string',
						'readonly'    => true,
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'Product name.', 'woocommerce-composite-products' ),
					),
					'price'        => array(
						'type'        => 'number',
						'readonly'    => true,
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'Product price.', 'woocommerce-composite-products' ),
					),
					'image'        => array(
						'type'        => 'string',
						'readonly'    => true,
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'Product image.', 'woocommerce-composite-products' ),
					),
					'permalink'    => array(
						'type'        => 'string',
						'readonly'    => true,
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'Product link.', 'woocommerce-composite-products' ),
					),
					'sku'          => array(
						'type'        => 'string',
						'readonly'    => true,
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'Product SKU.', 'woocommerce-composite-products' ),
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
			'description'       => __( 'Current page of the collection.', 'woocommerce-composite-products' ),
			'type'              => 'integer',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
			'minimum'           => 1,
		);
		$params[ 'per_page' ]   = array(
			'description'       => __( 'Maximum number of items to be returned in result set.', 'woocommerce-composite-products' ),
			'type'              => 'integer',
			'default'           => 10,
			'minimum'           => 1,
			'maximum'           => 100,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params[ 'after' ]      = array(
			'description'       => __( 'Limit response to resources published after a given ISO8601 compliant date.', 'woocommerce-composite-products' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params[ 'before' ]     = array(
			'description'       => __( 'Limit response to resources published before a given ISO8601 compliant date.', 'woocommerce-composite-products' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params[ 'order' ]      = array(
			'description'       => __( 'Order sort attribute ascending or descending.', 'woocommerce-composite-products' ),
			'type'              => 'string',
			'default'           => 'desc',
			'enum'              => array( 'asc', 'desc' ),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params[ 'orderby' ]    = array(
			'description'       => __( 'Sort collection by object attribute.', 'woocommerce-composite-products' ),
			'type'              => 'string',
			'default'           => 'date',
			'enum'              => array(
				'date',
				'net_revenue',
				'orders_count',
				'items_sold',
				'composited_items_sold',
				'product_name',
				'sku',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params[ 'match' ]      = array(
			'description'       => __( 'Indicates whether all the conditions should be true for the resulting set, or if any one of them is sufficient. Match affects the following parameters: status_is, status_is_not, product_includes, product_excludes, coupon_includes, coupon_excludes, customer, categories', 'woocommerce-composite-products' ),
			'type'              => 'string',
			'default'           => 'all',
			'enum'              => array(
				'all',
				'any',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params[ 'products' ]   = array(
			'description'       => __( 'Limit result to items with specified product ids.', 'woocommerce-composite-products' ),
			'type'              => 'array',
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
			'items'             => array(
				'type' => 'integer',
			),

		);
		$params[ 'extended_info' ] = array(
			'description'       => __( 'Add additional piece of info about each product to the report.', 'woocommerce-composite-products' ),
			'type'              => 'boolean',
			'default'           => false,
			'sanitize_callback' => 'wc_string_to_bool',
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $params;
	}

	/**
	 * Get the column names for export.
	 *
	 * @return array Key value pair of Column ID => Label.
	 */
	public function get_export_columns() {
		$export_columns = array(
			'product_name'          => __( 'Product Title', 'woocommerce-composite-products' ),
			'sku'                   => __( 'SKU', 'woocommerce-composite-products' ),
			'items_sold'            => __( 'Items Sold', 'woocommerce-composite-products' ),
			'composited_items_sold' => __( 'Composited Items Sold', 'woocommerce-composite-products' ),
			'net_revenue'           => __( 'N. Revenue', 'woocommerce-composite-products' ),
			'orders_count'          => __( 'Orders', 'woocommerce-composite-products' ),
		);

		/**
		 * `woocommerce_report_composites_export_columns` filter.
		 *
		 * Filter to add or remove column names from the products report for
		 * export.
		 */
		return apply_filters(
			'woocommerce_report_composites_export_columns',
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
			'product_name'          => $item[ 'extended_info' ][ 'name' ],
			'sku'                   => $item[ 'extended_info' ][ 'sku' ],
			'items_sold'            => $item[ 'items_sold' ],
			'composited_items_sold' => $item[ 'composited_items_sold' ],
			'net_revenue'           => $item[ 'net_revenue' ],
			'orders_count'          => $item[ 'orders_count' ]
		);

		/**
		 * `woocommerce_report_composites_prepare_export_item` filter.
		 *
		 * Filter to prepare extra columns in the export item for the products
		 * report.
		 *
		 */
		return apply_filters(
			'woocommerce_report_composites_prepare_export_item',
			$export_item,
			$item
		);
	}
}
