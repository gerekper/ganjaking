<?php

/**
 * REST API shipment tracking controller.
 *
 * Handles requests to /orders/shipment-tracking endpoint.
 *
 * @since 1.5.0
 */

class WC_Shipment_Tracking_Order_REST_API_Controller extends WC_REST_Controller {

	/**
	 * Endpoint namespace.
	 * This should not be in wc/* because shippment tracking does not need to follow WC core apis.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-shipment-tracking/v3';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'orders/(?P<order_id>[\d]+)/shipment-trackings';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'shop_order';

	/**
	 * @param $namespace
	 *
	 * @return WC_Shipment_Tracking_Order_REST_API_Controller
	 */
	public function set_namespace( $namespace ) {
		$this->namespace = $namespace;
		return $this;
	}

	/**
	 * Register the routes for trackings.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => $this->get_collection_params(),
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_item' ),
				'permission_callback' => array( $this, 'create_item_permissions_check' ),
				'args'                => array_merge( $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ), array(
					'tracking_number' => array(
						'required' => true,
					),
				) ),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/providers', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_providers' ),
			),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[a-fA-F0-9]{0,32})', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => array(
					'context' => $this->get_context_param( array( 'default' => 'view' ) ),
				),
			),
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_item' ),
				'permission_callback' => array( $this, 'delete_item_permissions_check' ),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );
	}

	/**
	 * Check whether a given request has permission to read order shipment-trackings.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! wc_rest_check_post_permissions( $this->post_type, 'read' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'woocommerce-shipment-tracking' ), array( 'status' => rest_authorization_required_code() ) );
		}
		return true;
	}

	/**
	 * Check if a given request has access create order shipment-tracking.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return boolean
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! wc_rest_check_post_permissions( $this->post_type, 'create' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_create', __( 'Sorry, you are not allowed to create resources.', 'woocommerce-shipment-tracking' ), array( 'status' => rest_authorization_required_code() ) );
		}
		return true;
	}

	/**
	 * Check if a given request has access to read a order shipment-tracking.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {
		if ( ! wc_rest_check_post_permissions( $this->post_type, 'read', (int) $request['order_id'] ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot view this resource.', 'woocommerce-shipment-tracking' ), array( 'status' => rest_authorization_required_code() ) );
		}
		return true;
	}

	/**
	 * Check if a given request has access delete a order shipment-tracking.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return boolean
	 */
	public function delete_item_permissions_check( $request ) {
		if ( ! wc_rest_check_post_permissions( $this->post_type, 'delete', (int) $request['order_id'] ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_delete', __( 'Sorry, you are not allowed to delete this resource.', 'woocommerce-shipment-tracking' ), array( 'status' => rest_authorization_required_code() ) );
		}
		return true;
	}

	/**
	 * Checks if an order ID is a valid order.
	 *
	 * @param int $order_id
	 * @return bool
	 * @since 1.6.4
	 */
	public function is_valid_order_id( $order_id ) {
		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			$order = get_post( $order_id );
			if ( empty( $order->post_type ) || $this->post_type !== $order->post_type ) {
				return false;
			}
		} else {
			$order = wc_get_order( $order_id );
			// in 3.0 the order factor will return false if the order class
			// throws an exception or the class doesn't exist.
			if ( false === $order ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Get shipment-trackings from an order.
	 *
	 * @param WP_REST_Request $request
	 * @return array
	 */
	public function get_items( $request ) {
		$order_id = (int) $request['order_id'];
		if ( ! $this->is_valid_order_id( $order_id ) ) {
			return new WP_Error( 'woocommerce_rest_order_invalid_id', __( 'Invalid order ID.', 'woocommerce-shipment-tracking' ), array( 'status' => 404 ) );
		}

		$st             = WC_Shipment_Tracking_Actions::get_instance();
		$tracking_items = $st->get_tracking_items( $order_id, true );

		$data = array();
		foreach ( $tracking_items as $tracking_item ) {
			$tracking_item['order_id'] = $order_id;

			$tracking_item = $this->prepare_item_for_response( $tracking_item, $request );
			$tracking_item = $this->prepare_response_for_collection( $tracking_item );
			$data[]        = $tracking_item;
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Get shipment-tracking providers.
	 *
	 * @param WP_REST_Request $request
	 * @return array
	 */
	public function get_providers( $request ) {
		wc_deprecated_argument( 'order_id', '1.6.20', 'providers endpoint does not require a variation_id anymore.' );
		$st = WC_Shipment_Tracking_Actions::get_instance();
		return rest_ensure_response( $st->get_providers() );
	}

	/**
	 * Create a single order shipment-tracking.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['tracking_id'] ) ) {
			return new WP_Error( 'woocommerce_rest_shop_order_shipment_tracking_exists', __( 'Cannot create existing order shipment tracking.', 'woocommerce-shipment-tracking' ), array( 'status' => 400 ) );
		}

		$order_id = (int) $request['order_id'];
		if ( ! $this->is_valid_order_id( $order_id ) ) {
			return new WP_Error( 'woocommerce_rest_order_invalid_id', __( 'Invalid order ID.', 'woocommerce-shipment-tracking' ), array( 'status' => 404 ) );
		}

		$args = array(
			'tracking_provider'        => wc_clean( sanitize_title( $request['tracking_provider'] ) ),
			'custom_tracking_provider' => wc_clean( $request['custom_tracking_provider'] ),
			'custom_tracking_link'     => wc_clean( $request['custom_tracking_link'] ),
			'tracking_number'          => wc_clean( $request['tracking_number'] ),
			'date_shipped'             => wc_clean( $request['date_shipped'] ),
		);

		$st                        = WC_Shipment_Tracking_Actions::get_instance();
		$tracking_item             = $st->add_tracking_item( $order_id, $args );
		$tracking_item['order_id'] = $order_id;
		$formatted                 = $st->get_formatted_tracking_item( $order_id, $tracking_item );
		$tracking_item             = array_merge( $tracking_item, $formatted );

		$request->set_param( 'context', 'edit' );

		$response = $this->prepare_item_for_response( $tracking_item, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, str_replace( '(?P<order_id>[\d]+)', $order_id, $this->rest_base ), $tracking_item['tracking_id'] ) ) );

		return $response;
	}

	/**
	 * Get a single order shipment-tracking.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( $request ) {
		$tracking_id = $request['id'];

		$order_id = (int) $request['order_id'];
		if ( ! $this->is_valid_order_id( $order_id ) ) {
			return new WP_Error( 'woocommerce_rest_order_invalid_id', __( 'Invalid order ID.', 'woocommerce-shipment-tracking' ), array( 'status' => 404 ) );
		}

		$st            = WC_Shipment_Tracking_Actions::get_instance();
		$tracking_item = $st->get_tracking_item( $order_id, $tracking_id, true );

		if ( ! $tracking_item ) {
			return new WP_Error( 'woocommerce_rest_order_shipment_tracking_invalid_id', __( 'Invalid shipment tracking ID.', 'woocommerce-shipment-tracking' ), array( 'status' => 404 ) );
		}

		$tracking_item['order_id'] = $order_id;
		$tracking_item             = $this->prepare_item_for_response( $tracking_item, $request );
		$response                  = rest_ensure_response( $tracking_item );

		return $response;
	}

	/**
	 * Delete a single order shipment-tracking.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function delete_item( $request ) {
		$tracking_id = $request['id'];

		$order_id = (int) $request['order_id'];
		if ( ! $this->is_valid_order_id( $order_id ) ) {
			return new WP_Error( 'woocommerce_rest_order_invalid_id', __( 'Invalid order ID.', 'woocommerce-shipment-tracking' ), array( 'status' => 404 ) );
		}

		$st            = WC_Shipment_Tracking_Actions::get_instance();
		$tracking_item = $st->get_tracking_item( $order_id, $tracking_id, true );

		if ( ! $tracking_item ) {
			return new WP_Error( 'woocommerce_rest_order_shipment_tracking_invalid_id', __( 'Invalid shipment tracking ID.', 'woocommerce-shipment-tracking' ), array( 'status' => 404 ) );
		}

		$tracking_item['order_id'] = $order_id;
		$tracking_item             = $this->prepare_item_for_response( $tracking_item, $request );
		$response                  = rest_ensure_response( $tracking_item );

		$result = $st->delete_tracking_item( $order_id, $tracking_id );
		if ( ! $result ) {
			return new WP_Error( 'woocommerce_rest_cannot_delete_order_shipment_tracking', __( 'The shipment tracking cannot be deleted.', 'woocommerce-shipment-tracking' ), array( 'status' => 500 ) );
		}

		return $response;
	}

	/**
	 * Prepare a single order shipment-note output for response.
	 *
	 * @param array           $tracking_item Shipment tracking item
	 * @param WP_REST_Request $request       Request object
	 *
	 * @return WP_REST_Response $response Response data
	 */
	public function prepare_item_for_response( $tracking_item, $request ) {
		$data = array(
			'tracking_id'       => $tracking_item['tracking_id'],
			'tracking_provider' => $tracking_item['formatted_tracking_provider'],
			'tracking_link'     => $tracking_item['formatted_tracking_link'],
			'tracking_number'   => $tracking_item['tracking_number'],
			'date_shipped'      => date( 'Y-m-d', $tracking_item['date_shipped'] ),
		);

		$order_id = $tracking_item['order_id'];

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		$response->add_links( $this->prepare_links( $order_id, $tracking_item ) );

		/**
		 * Filter order shipment-tracking object returned from the REST API.
		 *
		 * @param WP_REST_Response $response      The response object.
		 * @param array            $tracking_item Order tracking item used to create response.
		 * @param WP_REST_Request  $request       Request object.
		 */
		return apply_filters( 'woocommerce_rest_prepare_order_shipment_tracking', $response, $tracking_item, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param int   $order_id          Order ID
	 * @param array $shipment_tracking Shipment tracking item
	 *
	 * @return array Links for the given order shipment-tracking.
	 */
	protected function prepare_links( $order_id, $tracking_item ) {
		$order_id = (int) $order_id;
		$base     = str_replace( '(?P<order_id>[\d]+)', $order_id, $this->rest_base );
		$links    = array(
			'self' => array(
				'href' => rest_url( sprintf( '/%s/%s/%s', $this->namespace, $base, $tracking_item['tracking_id'] ) ),
			),
			'collection' => array(
				'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $base ) ),
			),
			'up' => array(
				'href' => rest_url( sprintf( '/%s/orders/%d', $this->namespace, $order_id ) ),
			),
		);
		return $links;
	}

	/**
	 * Get the Order Notes schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'tax',
			'type'       => 'shipment_tracking',
			'properties' => array(
				'tracking_id' => array(
					'description' => __( 'Unique identifier for shipment tracking.', 'woocommerce-shipment-tracking' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'tracking_provider' => array(
					'description' => __( 'Tracking provider name.', 'woocommerce-shipment-tracking' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => false,
				),
				'custom_tracking_provider' => array(
					'description' => __( 'Custom tracking provider name.', 'woocommerce-shipment-tracking' ),
					'type'        => 'string',
					'context'     => array( 'edit' ),
					'readonly'    => false,
				),
				'custom_tracking_link' => array(
					'description' => __( 'Custom tracking provider link.', 'woocommerce-shipment-tracking' ),
					'type'        => 'url',
					'context'     => array( 'edit' ),
					'readonly'    => false,
				),
				'tracking_number' => array(
					'description' => __( 'Tracking number.', 'woocommerce-shipment-tracking' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => false,
				),
				'date_shipped' => array(
					'description' => __( 'Date when package was shipped.', 'woocommerce-shipment-tracking' ),
					'type'        => 'date',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => false,
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
		return array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);
	}
}
