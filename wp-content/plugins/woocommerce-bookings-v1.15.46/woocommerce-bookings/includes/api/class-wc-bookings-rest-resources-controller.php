<?php
/**
 * REST API controller for resource objects.
 *
 * Handles requests to the /resources endpoint.
 *
 * @package WooCommerce\Bookings\Rest\Controller
 */

/**
 * REST API Products controller class.
 */
class WC_Bookings_REST_Resources_Controller extends WC_Bookings_REST_CRUD_Controller {

	use WC_Bookings_Rest_Permission_Check;

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'resources';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'bookable_resource';


	/**
	 * Get object.
	 *
	 * @param int $id Object ID.
	 *
	 * @return WC_Product_Booking_Resource
	 */
	protected function get_object( $id ) {
		return new WC_Product_Booking_Resource( $id );
	}

	/**
	 * Prepare a single product output for response.
	 *
	 * @param WC_Product_Booking_Resource $object  Object data.
	 * @param WP_REST_Request             $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function prepare_object_for_response( $object, $request ) {

		parent::prepare_object_for_response( $object, $request );
		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = array(
			'id'           => $object->get_id(),
			'availability' => $object->get_availability( $context ),
			'base_cost'    => $object->get_base_cost( $context ),
			'block_cost'   => $object->get_block_cost( $context ),
			'name'         => $object->get_name( $context ),
			'parent_id'    => $object->get_parent_id( $context ),
			'qty'          => $object->get_qty( $context ),
			'sort_order'   => $object->get_sort_order( $context ),
		);

		$data     = $this->add_additional_fields_to_object( $data, $request );
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $object, $request ) );

		/**
		 * Filter the data for a response.
		 *
		 * The dynamic portion of the hook name, $this->post_type,
		 * refers to object type being prepared for the response.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param WC_Data          $object   Object data.
		 * @param WP_REST_Request  $request  Request object.
		 */
		return apply_filters( "woocommerce_rest_prepare_{$this->post_type}_object", $response, $object, $request );
	}

	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'resource',
			'type'       => 'object',
			'properties' => array(
				'availability' => array(
					'type' => array(
						'description' => __( 'Availability date/time range type string.', 'woocommerce-bookings' ),
						'type'        => 'string',
						'readonly'    => true,
						'context'     => array( 'view' ),
					),
					'bookable' => array(
						'description' => __( 'Whether or not the resource is bookable during the time range.', 'woocommerce-bookings' ),
						'type'        => 'string',
						'readonly'    => true,
						'context'     => array( 'view' ),
					),
					'priority' => array(
						'description' => __( 'Priority for how availability rule is applied.', 'woocommerce-bookings' ),
						'type'        => 'integer',
						'readonly'    => true,
						'context'     => array( 'view' ),
					),
					'from' => array(
						'description' => __( 'From time for availability range.', 'woocommerce-bookings' ),
						'type'        => 'string',
						'readonly'    => true,
						'context'     => array( 'view' ),
					),
					'to' => array(
						'description' => __( 'To time for availability range.', 'woocommerce-bookings' ),
						'type'        => 'string',
						'readonly'    => true,
						'context'     => array( 'view' ),
					),
					'from_date' => array(
						'description' => __( 'From date for availability range.', 'woocommerce-bookings' ),
						'type'        => 'integer',
						'readonly'    => true,
						'context'     => array( 'view' ),
					),
					'to_date' => array(
						'description' => __( 'To date for availability range.', 'woocommerce-bookings' ),
						'type'        => 'string',
						'readonly'    => true,
						'context'     => array( 'view' ),
					),
				),
				'base_cost' => array(
					'description' => __( 'Base cost for resource.', 'woocommerce-bookings' ),
					'type'        => 'number',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'block_cost' => array(
					'description' => __( 'Cost per block of resource.', 'woocommerce-bookings' ),
					'type'        => 'number',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'name' => array(
					'description' => __( 'Name of resource.', 'woocommerce-bookings' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'parent_id' => array(
					'description' => __( 'ID of parent bookable product.', 'woocommerce-bookings' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'qty' => array(
					'description' => __( 'Quantity of resource.', 'woocommerce-bookings' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'sort_order' => array(
					'description' => __( 'Sorting order.', 'woocommerce-bookings' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
			),
		);
		return $this->add_additional_fields_schema( $schema );
	}
}