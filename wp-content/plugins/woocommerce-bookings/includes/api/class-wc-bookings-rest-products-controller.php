<?php
/**
 * REST API Products controller customized for Bookenberg.
 *
 * Handles requests to the /products endpoint.
 *
 * @package WooCommerce\Bookings\Rest\Controller
 */

/**
 * REST API Products controller class.
 */
class WC_Bookings_REST_Products_Controller extends WC_REST_Products_Controller {

	use WC_Bookings_Rest_Permission_Check;

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = WC_Bookings_REST_API::V1_NAMESPACE;

	/**
	 * Add support for filtering by resource.
	 *
	 * @param WP_REST_Request $request Request data.
	 *
	 * @return array
	 */
	protected function prepare_objects_query( $request ) {

		$args = parent::prepare_objects_query( $request );

		if ( ! empty( $request['resource'] ) ) {
			$args['wc_bookings_resource'] = $request['resource'];

			add_filter( 'posts_join', array( $this, 'add_resource_filter' ), 10, 2 );
		}
		
		$args = apply_filters( 'woocommerce_bookings_product_rest_endpoint', $args );

		return $args;
	}

	/**
	 * Get objects.
	 *
	 * @param array $query_args Query args.
	 *
	 * @return array
	 */
	protected function get_objects( $query_args ) {
		$objects = parent::get_objects( $query_args );

		if ( ! empty( $query_args['wc_bookings_resource'] ) ) {
			remove_filter( 'posts_join', array( $this, 'add_resource_filter' ) );
		}

		return $objects;
	}

	/**
	 * Filters products by associated resource id(s).
	 *
	 * @param string   $join Current join clauses.
	 * @param WP_Query $wp_query Current query object.
	 *
	 * @return string
	 */
	public function add_resource_filter( $join, $wp_query ) {
		global $wpdb;
		if ( ! empty( $wp_query->query['wc_bookings_resource'] ) ) {
			$resource_id_in = implode( ',', array_map( 'absint', (array) $wp_query->query['wc_bookings_resource'] ) );
			$join          .= " INNER JOIN 
			{$wpdb->prefix}wc_booking_relationships ON 
			({$wpdb->posts}.ID = {$wpdb->prefix}wc_booking_relationships.product_id AND 
			{$wpdb->prefix}wc_booking_relationships.resource_id IN ({$resource_id_in}))";
		}
		return $join;
	}

	/**
	 * Get product data.
	 *
	 * @param  WC_Product_Booking $product Product instance.
	 * @param  string             $context Request context.
	 *                                     Options: 'view' and 'edit'.
	 * @return array
	 */
	protected function get_product_data( $product, $context = 'view' ) {
		$is_vaild_rest_type = 'booking' === $product->get_type();
		$is_vaild_rest_type = apply_filters( "woocommerce_bookings_product_type_rest_check", $is_vaild_rest_type, $product );
		if ( ! $is_vaild_rest_type ) {
			wp_send_json( __( 'Not a bookable product', 'woocommerce-bookings' ), 400 );
		}

		$data = parent::get_product_data( $product, $context );

		$bookable_data = array(
			'apply_adjacent_buffer'      => $product->get_apply_adjacent_buffer( $context ),
			'availability'               => $product->get_availability( $context ),
			'block_cost'                 => $product->get_block_cost( $context ),
			'buffer_period'              => $product->get_buffer_period( $context ),
			'calendar_display_mode'      => $product->get_calendar_display_mode( $context ),
			'cancel_limit_unit'          => $product->get_cancel_limit_unit( $context ),
			'cancel_limit'               => $product->get_cancel_limit( $context ),
			'check_start_block_only'     => $product->get_check_start_block_only( $context ),
			'cost'                       => $product->get_cost( $context ),
			'default_date_availability'  => $product->get_default_date_availability( $context ),
			'display_cost'               => $product->get_display_cost( $context ),
			'duration_type'              => $product->get_duration_type( $context ),
			'duration_unit'              => $product->get_duration_unit( $context ),
			'duration'                   => $product->get_duration( $context ),
			'enable_range_picker'        => $product->get_enable_range_picker( $context ),
			'first_block_time'           => $product->get_first_block_time( $context ),
			'has_person_cost_multiplier' => $product->get_has_person_cost_multiplier( $context ),
			'has_person_qty_multiplier'  => $product->get_has_person_qty_multiplier( $context ),
			'has_person_types'           => $product->get_has_person_types( $context ),
			'has_persons'                => $product->get_has_persons( $context ),
			'has_resources'              => $product->get_has_resources( $context ),
			'has_restricted_days'        => $product->get_has_restricted_days( $context ),
			'max_date'                   => $product->get_max_date(),
			'max_date_value'             => $product->get_max_date_value( $context ),
			'max_date_unit'              => $product->get_max_date_unit( $context ),
			'max_duration'               => $product->get_max_duration( $context ),
			'max_persons'                => $product->get_max_persons( $context ),
			'min_date'                   => $product->get_min_date(),
			'min_date_value'             => $product->get_min_date_value( $context ),
			'min_date_unit'              => $product->get_min_date_unit( $context ),
			'min_duration'               => $product->get_min_duration( $context ),
			'min_persons'                => $product->get_min_persons( $context ),
			'person_types'               => $product->get_person_types( $context ),
			'pricing'                    => $product->get_pricing( $context ),
			'qty'                        => $product->get_qty( $context ),
			'requires_confirmation'      => $product->requires_confirmation(),
			'resource_label'             => $product->get_resource_label( $context ),
			'resource_base_costs'        => $product->get_resource_base_costs( $context ),
			'resource_block_costs'       => $product->get_resource_block_costs( $context ),
			'resource_ids'               => $product->get_resource_ids( $context ),
			'resources_assignment'       => $product->get_resources_assignment( $context ),
			'restricted_days'            => $product->get_restricted_days( $context ),
			'can_be_cancelled'           => $product->can_be_cancelled(),
			'user_can_cancel'            => $product->get_user_can_cancel( $context ),
		);

		return array_merge( $data, $bookable_data );
	}

	/**
	 * Update the collection params.
	 *
	 * Adds new options for 'orderby', and new parameters 'cat_operator', 'attr_operator'.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params             = parent::get_collection_params();
		$params['resource'] = array(
			'description'       => __( 'Limit result set to products assigned a specific resource ID.', 'woocommerce-bookings' ),
			'type'              => 'array',
			'items'             => array(
				'type'          => 'integer',
			),
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['status']['default'] = 'publish';
		$params['type']['default']   = 'booking';
		$params['type']['enum']      = array( 'booking' );

		return $params;
	}

	/**
	 * @param WP_REST_Request $request
	 * @param bool $creating
	 *
	 * @return WC_Data|WP_Error
	 */
	protected function prepare_object_for_database( $request, $creating = false ) {
		$request['type'] = 'booking';

		$product =  parent::prepare_object_for_database( $request, $creating );

		if ( ! $product instanceof WC_Product_Booking ) {
			wp_send_json( __( 'Not a bookable product', 'woocommerce-bookings' ), 400 );
		}

		foreach ( array_keys( $this->get_booking_product_properties() ) as $prop ) {
			$method = 'set_' . $prop;
			if ( isset( $request[ $prop ] ) && method_exists( $product, $method ) ) {
				$product->$method( $request[ $prop ] );
			}
		}

		if ( isset( $request['can_be_cancelled'] ) ) {
			$product->set_user_can_cancel( $request['can_be_cancelled'] );
		}

		return $product;
	}

	protected function get_booking_product_properties() {
		return array(
			'apply_adjacent_buffer'      => array(
				'description' => __( 'Apply adjacent buffers.', 'woocommerce-bookings' ),
				'type'        => 'boolean',
				'context'     => array( 'view', 'edit' ),
			),
			'availability'               => array(
				'description' => __( 'Availability rules defined on product level.', 'woocommerce-bookings' ),
				'type'        => 'array',
				'context'     => array( 'view', 'edit' ),
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'type'      => array(
							'description' => __( 'Availability type.', 'woocommerce-bookings' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'from'      => array(
							'description' => __( 'Starting month/day/week inclusive.', 'woocommerce-bookings' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'to'        => array(
							'description' => __( 'Ending month/day/week inclusive.', 'woocommerce-bookings' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'from_date' => array(
							'description' => __( 'Starting day if \'from\' is a time.,', 'woocommerce-bookings' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'to_date'   => array(
							'description' => __( 'Ending day if \'to\' is a time.', 'woocommerce-bookings' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'bookable'  => array(
							'description' => __( 'Rule marks things as bookable or not, \'yes\' or \'no\'.', 'woocommerce-bookings' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'priority'  => array(
							'description' => __( 'Priority of rule.', 'woocommerce-bookings' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' ),
						),
					),
				),
			),
			'block_cost'                 => array(
				'description' => __( 'Base cost of each block.', 'woocommerce-bookings' ),
				'type'        => 'number',
				'context'     => array( 'view', 'edit' ),
			),
			'buffer_period'              => array(
				'description' => __( 'Required buffer Period between bookings.', 'woocommerce-bookings' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
			),
			'calendar_display_mode'      => array(
				'description' => __( 'How the calendar will display on the product page. Valid values are \'always_visible\' or \'\'.', 'woocommerce-bookings' ),
				'type'        => 'string',
				'enum'        => array( '', 'always_visible'),
				'context'     => array( 'view', 'edit' ),
			),
			'cancel_limit_unit'          => array(
				'description' => __( 'The unit limit is defined in. Valid values are \'month\', \'day\', \'hour\', and \'minute\'.', 'woocommerce-bookings' ),
				'type'        => 'string',
				'enum'        => array( 'month', 'day', 'hour', 'minute' ),
				'context'     => array( 'view', 'edit' ),
			),
			'cancel_limit'               => array(
				'description' => __( 'How many limit units in advance users are allowed to cancel bookings.', 'woocommerce-bookings' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
			),
			'check_start_block_only'     => array(
				'description' => __( 'If true only the first block in checked for availability.', 'woocommerce-bookings' ),
				'type'        => 'boolean',
				'context'     => array( 'view', 'edit' ),
			),
			'cost'                       => array(
				'description' => __( 'Product cost.', 'woocommerce-bookings' ),
				'type'        => 'number',
				'context'     => array( 'view', 'edit' ),
			),
			'default_date_availability'  => array(
				'description' => __( 'If \'available\' product is bookable unless made unbookable by availability rules.', 'woocommerce-bookings' ),
				'type'        => 'string',
				'enum'        => array( '', 'available' ),
				'context'     => array( 'view', 'edit' ),
			),
			'display_cost'               => array(
				'description' => __( 'Product cost displayed.', 'woocommerce-bookings' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
			),
			'duration_type'              => array(
				'description' => __( 'How duration is defined.', 'woocommerce-bookings' ),
				'type'        => 'string',
				'enum'        => array( 'customer', 'fixed' ),
				'context'     => array( 'view', 'edit' ),
			),
			'duration_unit'              => array(
				'description' => __( 'Unit duration is defined in.', 'woocommerce-bookings' ),
				'type'        => 'string',
				'enum'        => array( 'month', 'day', 'hour', 'minute' ),
				'context'     => array( 'view', 'edit' ),
			),
			'duration'                   => array(
				'description' => __( 'Size in duration units of each block.', 'woocommerce-bookings' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
			),
			'enable_range_picker'        => array(
				'description' => __( 'Customer can pick a range of days on calendar.', 'woocommerce-bookings' ),
				'type'        => 'boolean',
				'context'     => array( 'view', 'edit' ),
			),
			'first_block_time'           => array(
				'description' => __( 'Time of day first block starts.', 'woocommerce-bookings' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
			),
			'has_person_cost_multiplier' => array(
				'description' => __( 'Will multiply cost by number of persons.', 'woocommerce-bookings' ),
				'type'        => 'boolean',
				'context'     => array( 'view', 'edit' ),
			),
			'has_person_qty_multiplier'  => array(
				'description' => __( 'Each person counts as a booking.', 'woocommerce-bookings' ),
				'type'        => 'boolean',
				'context'     => array( 'view', 'edit' ),
			),
			'has_person_types'           => array(
				'description' => __( 'Product has different types of persons.', 'woocommerce-bookings' ),
				'type'        => 'boolean',
				'context'     => array( 'view', 'edit' ),
			),
			'has_persons'                => array(
				'description' => __( 'Product has persons defined.', 'woocommerce-bookings' ),
				'type'        => 'boolean',
				'context'     => array( 'view', 'edit' ),
			),
			'has_resources'              => array(
				'description' => __( 'Product has resources defined.', 'woocommerce-bookings' ),
				'type'        => 'boolean',
				'context'     => array( 'view', 'edit' ),
			),
			'has_restricted_days'        => array(
				'description' => __( 'Product has restricted days.', 'woocommerce-bookings' ),
				'type'        => 'boolean',
				'context'     => array( 'view', 'edit' ),
			),
			'max_date'                   => array(
				'description' => __( 'Max date value combined with max date unit.', 'woocommerce-bookings' ),
				'type'        => 'string',
				'context'     => array( 'view' ),
			),
			'max_date_value'             => array(
				'description' => __( 'Max amount af max_date_units into the future a block is bookable.', 'woocommerce-bookings' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
			),
			'max_date_unit'              => array(
				'description' => __( 'Units for max_date_value.', 'woocommerce-bookings' ),
				'type'        => 'string',
				'enum'        => array( 'month', 'day', 'hour', 'week' ),
				'context'     => array( 'view', 'edit' ),
			),
			'min_date'                   => array(
				'description' => __( 'Min date value combined with min date unit.', 'woocommerce-bookings' ),
				'type'        => 'string',
				'context'     => array( 'view' ),
			),
			'min_date_value'             => array(
				'description' => __( 'Min amount af min_date_units into the future a block is bookable.', 'woocommerce-bookings' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
			),
			'min_date_unit'              => array(
				'description' => __( 'Units for min_date_value.', 'woocommerce-bookings' ),
				'type'        => 'string',
				'enum'        => array( 'month', 'day', 'hour', 'week' ),
				'context'     => array( 'view', 'edit' ),
			),
			'max_duration'               => array(
				'description' => __( 'Max duration of units a booking can be.', 'woocommerce-bookings' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
			),
			'min_duration'               => array(
				'description' => __( 'Min duration of units a booking can be.', 'woocommerce-bookings' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
			),
			'max_persons'                => array(
				'description' => __( 'Max persons which can be booked per booking.', 'woocommerce-bookings' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
			),
			'min_persons'                => array(
				'description' => __( 'Min persons which can be booked per booking.', 'woocommerce-bookings' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
			),
			'pricing'                    => array(
				'description' => __( 'Pricing rules.', 'woocommerce-bookings' ),
				'type'        => 'array',
				'context'     => array( 'view', 'edit' ),
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'type'               => array(
							'description' => __( 'Date range type.', 'woocommerce-bookings' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'from'               => array(
							'description' => __( 'Starting month/day/week inclusive.', 'woocommerce-bookings' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'to'                 => array(
							'description' => __( 'Ending month/day/week inclusive.', 'woocommerce-bookings' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'from_date'          => array(
							'description' => __( 'Starting day if \'from\' is a time.,', 'woocommerce-bookings' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'to_date'            => array(
							'description' => __( 'Ending day if \'to\' is a time.', 'woocommerce-bookings' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'modifier'      => array(
							'description' => __( 'How the block cost should be modified.', 'woocommerce-bookings' ),
							'type'      => 'string',
							'enum'        => array( '+', 'minus', 'times', 'divide', 'equals' ),
							'context'     => array( 'view', 'edit' ),
						),
						'cost'               => array(
							'description' => __( 'Block cost.', 'woocommerce-bookings' ),
							'type'      => 'number',
							'context'     => array( 'view', 'edit' ),
						),
						'base_modifier' => array(
							'description' => __( 'How the base cost should be modified.', 'woocommerce-bookings' ),
							'type'      => 'string',
							'enum'        => array( '+', 'minus', 'times', 'divide', 'equals' ),
							'context'     => array( 'view', 'edit' ),
						),
						'base_cost'          => array(
							'description' => __( 'Base cost.', 'woocommerce-bookings' ),
							'type'      => 'number',
							'context'     => array( 'view', 'edit' ),
						),
						'priority'           => array(
							'description' => __( 'Priority of rule.', 'woocommerce-bookings' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' ),
						),
					),
				),
			),
			'qty'                        => array(
				'description' => __( 'Max bookings per block.', 'woocommerce-bookings' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
			),
			'requires_confirmation'      => array(
				'description' => __( 'Booking require confirmation.', 'woocommerce-bookings' ),
				'type'        => 'boolean',
				'context'     => array( 'view', 'edit' ),
			),
			'restricted_days'            => array(
				'description' => __( 'Days days of week bookings cannot start. Array of numeric day indexes with 0 being Sunday.', 'woocommerce-bookings' ),
				'type'        => 'array',
				'items'       => array(
					'type' => 'integer',
					'enum' => array( 0, 1, 2, 3, 4, 5, 6 ),
				),
				'context'     => array( 'view', 'edit' ),
			),
			'can_be_cancelled'           => array(
				'description' => __( 'Booking can be cancelled by customer.', 'woocommerce-bookings' ),
				'type'        => 'boolean',
				'context'     => array( 'view', 'edit' ),
			),

		);
	}

	/**
	 * Get the Product's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = parent::get_item_schema();

		$schema['properties'] = array_merge(
			$schema['properties'],
			$this->get_booking_product_properties()
		);

		return $schema;
	}
}
