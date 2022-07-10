<?php
/**
 * REST API for bookings objects.
 *
 * Handles requests to the /bookings endpoint.
 *
 * @package WooCommerce\Bookings\Rest\Controller
 */

/**
 * REST API Products controller class.
 */
class WC_Bookings_REST_Booking_Controller extends WC_Bookings_REST_CRUD_Controller {

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'bookings';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'wc_booking';

	/**
	 * Get object.
	 *
	 * @param int $booking_id Object ID.
	 *
	 * @return WC_Booking|false
	 */
	protected function get_object( $booking_id ) {
		$post_object = $booking_id ? get_post( $booking_id ) : false;

		if ( ! $post_object || $this->post_type !== $post_object->post_type ) {
			return false;
		}

		return new WC_Booking( $booking_id );
	}

	/**
	 * Get objects (i.e. Bookings).
	 *
	 * @param array $query_args Query args.
	 *
	 * @return array Bookings data.
	 */
	protected function get_objects( $query_args ) {
		/**
		 * Get all public post statuses list and include a few.
		 * This is done to include `wc-partial-payment` for now.
		 *
		 * Fix https://github.com/woocommerce/woocommerce-bookings/issues/3082
		 */
		if ( ! isset( $query_args['post_status'] ) && empty( $query_args['post_status'] ) ) {
			$post_statuses             = array_values( get_post_stati( array( 'public' => true ) ) );
			$include_statuses          = array( 'wc-partial-payment' );
			$query_args['post_status'] = array_merge( $post_statuses, $include_statuses );
		}

		return parent::get_objects( $query_args );
	}

	/**
	 * Prepare a single product output for response.
	 *
	 * @param WC_Booking      $object  Object data.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function prepare_object_for_response( $object, $request ) {
		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';

		$data = array(
			'id'                       => $object->get_id(),
			'all_day'                  => $object->get_all_day( $context ),
			'cost'                     => $object->get_cost( $context ),
			'customer_id'              => $object->get_customer_id( $context ),
			'date_created'             => $object->get_date_created( $context ),
			'date_modified'            => $object->get_date_modified( $context ),
			'end'                      => $object->get_end( $context ),
			'google_calendar_event_id' => $object->get_google_calendar_event_id( $context ),
			'order_id'                 => $object->get_order_id( $context ),
			'order_item_id'            => $object->get_order_item_id( $context ),
			'parent_id'                => $object->get_parent_id( $context ),
			'person_counts'            => $object->get_person_counts( $context ),
			'product_id'               => $object->get_product_id( $context ),
			'resource_id'              => $object->get_resource_id( $context ),
			'start'                    => $object->get_start( $context ),
			'status'                   => $object->get_status( $context ),
			'local_timezone'           => $object->get_local_timezone( $context ),
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

	/**
	 * Prepare a single product for create or update.
	 *
	 * @param  WP_REST_Request $request Request object.
	 * @param  bool            $creating If is creating a new object.
	 * @return WP_Error|WC_Data
	 */
	protected function prepare_object_for_database( $request, $creating = false ) {
		$id = isset( $request['id'] ) ? absint( $request['id'] ) : 0;

		$booking = new WC_Booking( $id );

		if ( isset( $request['resource_id'] ) ) {
			$booking->set_resource_id( absint( $request['resource_id'] ) );
		}

		// TODO: Update other fields here.

		// Allow set meta_data.
		if ( is_array( $request['meta_data'] ) ) {
			foreach ( $request['meta_data'] as $meta ) {
				$booking->update_meta_data( $meta['key'], $meta['value'], isset( $meta['id'] ) ? $meta['id'] : '' );
			}
		}

		/**
		 * Filters an object before it is inserted via the REST API.
		 *
		 * The dynamic portion of the hook name, `$this->post_type`,
		 * refers to the object type slug.
		 *
		 * @param WC_Data         $booking  Object object.
		 * @param WP_REST_Request $request  Request object.
		 * @param bool            $creating If is creating a new object.
		 */
		return apply_filters( "woocommerce_rest_pre_insert_{$this->post_type}_object", $booking, $request, $creating );
	}

	public function get_item_schema() {
		// TODO: Implement auto documentation here.
		return parent::get_item_schema();
	}
}
