<?php
/**
 * REST API Global Availability Rules controller
 *
 * @package YITH\Booking\RestApi
 */

defined( 'ABSPATH' ) || exit;

/**
 * REST API Global Availability Rules controller class.
 *
 * @package YITH\Booking\RestApi
 */
class YITH_WCBK_REST_Global_Availability_Rules_Controller extends WP_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'yith-booking/v1';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'global-availability-rules';

	/**
	 * Register the routes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
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
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				'args'   => array(
					'id' => array(
						'type' => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/priorities',
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_priorities' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
				),
			)
		);
	}

	/**
	 * Retrieves order stats.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$args                   = array();
		$args['items_per_page'] = $request['per_page'];
		$args['page']           = $request['page'];
		$args['order']          = $request['order'];
		$args['order_by']       = $request['order_by'];

		$extra_args = array( 'enabled' );

		foreach ( $extra_args as $extra_arg ) {
			if ( isset( $request[ $extra_arg ] ) ) {
				$args[ $extra_arg ] = $request[ $extra_arg ];
			}
		}
		$args['return']   = 'ids';
		$args['paginate'] = true;

		$query = yith_wcbk_get_global_availability_rules( $args );
		$items = array();
		foreach ( $query->items as $item ) {
			$data    = $this->prepare_item_for_response( $item, $request );
			$items[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $items );
		$response->header( 'X-TRS-Total', $query->total );
		$response->header( 'X-TRS-TotalPages', (int) $query->max_num_pages );

		return $response;
	}

	/**
	 * Creates one item from the collection.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function create_item( $request ) {
		$rule = new YITH_WCBK_Global_Availability_Rule();

		$props = array(
			'name'                 => $request['name'] ?? null,
			'type'                 => $request['type'] ?? null,
			'enabled'              => $request['enabled'] ?? null,
			'date_ranges'          => $request['date_ranges'] ?? null,
			'availabilities'       => $request['availabilities'] ?? null,
			'priority'             => $request['priority'] ?? ( YITH_WCBK_Global_Availability_Rule_Data_Store::get_greatest_priority() + 1 ),
			'exclude_products'     => $request['exclude_products'] ?? null,
			'excluded_product_ids' => $request['excluded_product_ids'] ?? null,
		);

		$rule->set_props( $props );
		$rule->save();

		return $this->prepare_item_for_response( $rule, $request );
	}

	/**
	 * Update item
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function update_item( $request ) {
		$id   = $request['id'];
		$rule = yith_wcbk_get_global_availability_rule( $id );
		if ( $rule ) {
			$props = array(
				'name'                 => $request['name'] ?? null,
				'type'                 => $request['type'] ?? null,
				'enabled'              => $request['enabled'] ?? null,
				'date_ranges'          => $request['date_ranges'] ?? null,
				'availabilities'       => $request['availabilities'] ?? null,
				'priority'             => $request['priority'] ?? null,
				'exclude_products'     => $request['exclude_products'] ?? null,
				'excluded_product_ids' => $request['excluded_product_ids'] ?? null,
			);

			$rule->set_props( $props );
			$rule->save();

			return $this->prepare_item_for_response( $rule, $request );
		} else {
			return new WP_Error( 'yith_wcbk_global_availability_rule_not_found', __( 'Rule not found!', 'yith-booking-for-woocommerce' ), array( 'status' => 404 ) );
		}
	}

	/**
	 * Delete item.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function delete_item( $request ) {
		$id   = $request['id'];
		$rule = yith_wcbk_get_global_availability_rule( $id );
		if ( $rule ) {
			$response = $this->prepare_item_for_response( $rule, $request );

			$rule->delete();

			return $response;
		} else {
			return new WP_Error( 'yith_wcbk_global_availability_rule_not_found', __( 'Rule not found!', 'yith-booking-for-woocommerce' ), array( 'status' => 404 ) );
		}
	}

	/**
	 * Sort items by priorities.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function update_priorities( $request ) {
		$priorities = $request['priorities'] ?? false;
		if ( $priorities ) {
			$response = YITH_WCBK_Global_Availability_Rule_Data_Store::update_priorities( $priorities );

			return rest_ensure_response( $response );
		} else {
			$required = array( 'priorities' );
			$message  = sprintf(
			// translators: %s is the list of missing params.
				__( 'The following parameters are required: %.', 'yith-booking-for-woocommerce' ),
				'"' . implode( '", "', $required ) . '"'
			);

			return new WP_Error( 'yith_wcbk_rest_missing_params', $message, array( 'status' => 400 ) );
		}
	}

	/**
	 * Prepare a single register output for response.
	 *
	 * @param int|YITH_WCBK_Global_Availability_Rule $rule    The ID or the rule object.
	 * @param WP_REST_Request                        $request Request object.
	 *
	 * @return WP_REST_Response $data
	 */
	public function prepare_item_for_response( $rule, $request ) {
		$rule = yith_wcbk_get_global_availability_rule( $rule );
		if ( $rule ) {
			$data = array(
				'id'                   => $rule->get_id(),
				'name'                 => $rule->get_name(),
				'type'                 => $rule->get_type(),
				'enabled'              => 'yes' === $rule->get_enabled(),
				'date_ranges'          => $rule->get_date_ranges(),
				'availabilities'       => $rule->get_availabilities(),
				'priority'             => $rule->get_priority(),
				'exclude_products'     => 'yes' === $rule->get_exclude_products(),
				'excluded_product_ids' => $rule->get_excluded_product_ids(),
			);

			return rest_ensure_response( $data );
		} else {
			return new WP_Error( 'yith_wcbk_global_availability_rule_not_found', __( 'Rule not found!', 'yith-booking-for-woocommerce' ), array( 'status' => 404 ) );
		}
	}

	/**
	 * Checks if a given request has access to manage items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return boolean
	 * @deprecated 5.3.1 | Use specific checks: create_item_permissions_check, get_items_permissions_check, and so on...
	 */
	public function manage_items_permissions_check( $request ) {
		yith_wcbk_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '5.3.1', 'Use specific checks: create_item_permissions_check, get_items_permissions_check, and so on...' );

		return current_user_can( 'manage_options' );
	}

	/**
	 * Checks if a given request has access to create items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! yith_wcbk_rest_check_manager_permissions( 'global_availability_rules', 'create' ) ) {
			return new WP_Error( 'yith_booking_rest_cannot_create_global_availability_rules', __( 'Sorry, you cannot create this resource.', 'yith-booking-for-woocommerce' ), array( 'status' => 403 ) );
		}

		return true;
	}

	/**
	 * Checks if a given request has access to read items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! yith_wcbk_rest_check_manager_permissions( 'global_availability_rules', 'read' ) ) {
			return new WP_Error( 'yith_booking_rest_cannot_view_global_availability_rules', __( 'Sorry, you cannot view this resource.', 'yith-booking-for-woocommerce' ), array( 'status' => 403 ) );
		}

		return true;
	}

	/**
	 * Checks if a given request has access to update items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		if ( ! yith_wcbk_rest_check_manager_permissions( 'global_availability_rules', 'update' ) ) {
			return new WP_Error( 'yith_booking_rest_cannot_update_global_availability_rules', __( 'Sorry, you cannot update this resource.', 'yith-booking-for-woocommerce' ), array( 'status' => 403 ) );
		}

		return true;
	}

	/**
	 * Checks if a given request has access to delete items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) {
		if ( ! yith_wcbk_rest_check_manager_permissions( 'global_availability_rules', 'delete' ) ) {
			return new WP_Error( 'yith_booking_rest_cannot_delete_global_availability_rules', __( 'Sorry, you cannot delete this resource.', 'yith-booking-for-woocommerce' ), array( 'status' => 403 ) );
		}

		return true;
	}

	/**
	 * Get the schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'yith-booking-global-availability-rule',
			'type'       => 'object',
			'properties' => array(
				'id'                   => array(
					'type'     => 'integer',
					'context'  => array( 'view', 'edit' ),
					'readonly' => true,
				),
				'name'                 => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
				'type'                 => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
					'enum'    => array( 'generic', 'specific' ),
				),
				'enabled'              => array(
					'type'    => 'boolean',
					'context' => array( 'view', 'edit' ),
				),
				'date_ranges'          => array(
					'type'    => 'array',
					'context' => array( 'view', 'edit' ),
				),
				'availabilities'       => array(
					'type'    => 'array',
					'context' => array( 'view', 'edit' ),
				),
				'priority'             => array(
					'type'    => 'numeric',
					'context' => array( 'view', 'edit' ),
				),
				'exclude_products'     => array(
					'type'    => 'boolean',
					'context' => array( 'view', 'edit' ),
				),
				'excluded_product_ids' => array(
					'type'    => 'array',
					'context' => array( 'view', 'edit' ),
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
		$params = parent::get_collection_params();

		$params['per_page']['minimum']           = - 1;
		$params['per_page']['sanitize_callback'] = function ( $number ) {
			return intval( $number );
		};

		$params['order']    = array(
			'type'              => 'string',
			'default'           => 'asc',
			'enum'              => array( 'asc', 'desc' ),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['order_by'] = array(
			'type'              => 'string',
			'default'           => 'priority',
			'enum'              => array( 'id', 'name', 'priority' ),
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $params;
	}
}
