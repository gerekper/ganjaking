<?php

/**
 * Class for extending WooCommerce Core Bookable Product Exports
 */
class WC_Booking_Products_Export {
	/**
	 * The data properties we'd like to include in the export.
	 *
	 * @var array
	 */
	protected $properties = array();

	/**
	 * Merges booking product data into the parent object.
	 *
	 * @param int|WC_Product|object $product Product to init.
	 */
	public function __construct( $product = 0 ) {
		$this->set_default_properties();
		add_filter( 'woocommerce_product_export_row_data', array( $this, 'add_person_types' ), 20, 2 );
		add_filter( 'woocommerce_product_export_product_default_columns', array( $this, 'add_column_names' ), 20, 2 );
	}
	/**
 	 * Setup the bookable properties that should also be exported.
 	 *
 	 * @since 1.11.0
 	 */
	public function set_default_properties(){
		$this->properties = array(
			'apply_adjacent_buffer'      => __( 'Apply_adjacent_buffer', 'woocommerce-bookings' ),
			'availability'               => __( 'Availability', 'woocommerce-bookings' ),
			'block_cost'                 => __( 'Block_cost', 'woocommerce-bookings' ),
			'buffer_period'              => __( 'Buffer_period', 'woocommerce-bookings' ),
			'calendar_display_mode'      => __( 'Calendar_display_mode', 'woocommerce-bookings' ),
			'cancel_limit_unit'          => __( 'Cancel_limit_unit', 'woocommerce-bookings' ),
			'cancel_limit'               => __( 'Cancel_limit', 'woocommerce-bookings' ),
			'check_start_block_only'     => __( 'Check_start_block_only', 'woocommerce-bookings' ),
			'cost'                       => __( 'Cost', 'woocommerce-bookings' ),
			'default_date_availability'  => __( 'Default_date_availability', 'woocommerce-bookings' ),
			'display_cost'               => __( 'Display_cost', 'woocommerce-bookings' ),
			'duration_type'              => __( 'Duration_type', 'woocommerce-bookings' ),
			'duration_unit'              => __( 'Duration_unit', 'woocommerce-bookings' ),
			'duration'                   => __( 'Duration', 'woocommerce-bookings' ),
			'enable_range_picker'        => __( 'Enable_range_picker', 'woocommerce-bookings' ),
			'first_block_time'           => __( 'First_block_time', 'woocommerce-bookings' ),
			'has_person_cost_multiplier' => __( 'Has_person_cost_multiplier', 'woocommerce-bookings' ),
			'has_person_qty_multiplier'  => __( 'Has_person_qty_multiplier', 'woocommerce-bookings' ),
			'has_person_types'           => __( 'Has_person_types', 'woocommerce-bookings' ),
			'has_persons'                => __( 'Has_persons', 'woocommerce-bookings' ),
			'has_resources'              => __( 'Has_resources', 'woocommerce-bookings' ),
			'has_restricted_days'        => __( 'Has_restricted_days', 'woocommerce-bookings' ),
			'max_date_unit'              => __( 'Max_date_unit', 'woocommerce-bookings' ),
			'max_date_value'             => __( 'Max_date_value', 'woocommerce-bookings' ),
			'max_duration'               => __( 'Max_duration', 'woocommerce-bookings' ),
			'max_persons'                => __( 'Max_persons', 'woocommerce-bookings' ),
			'min_date_unit'              => __( 'Min_date_unit', 'woocommerce-bookings' ),
			'min_date_value'             => __( 'Min_date_value', 'woocommerce-bookings' ),
			'min_duration'               => __( 'Min_duration', 'woocommerce-bookings' ),
			'min_persons'                => __( 'Min_persons', 'woocommerce-bookings' ),
			'person_types'               => __( 'Person_types', 'woocommerce-bookings' ),
			'pricing'                    => __( 'Pricing', 'woocommerce-bookings' ),
			'qty'                        => __( 'Qty', 'woocommerce-bookings' ),
			'requires_confirmation'      => __( 'Requires_confirmation', 'woocommerce-bookings' ),
			'resource_label'             => __( 'Resource_label', 'woocommerce-bookings' ),
			'resource_base_costs'        => __( 'Resource_base_costs', 'woocommerce-bookings' ),
			'resource_block_costs'       => __( 'Resource_block_costs', 'woocommerce-bookings' ),
			'resource_ids'               => __( 'Resource_ids', 'woocommerce-bookings' ),
			'resources_assignment'       => __( 'Resources_assignment', 'woocommerce-bookings' ),
			'restricted_days'            => __( 'Restricted_days', 'woocommerce-bookings' ),
			'user_can_cancel'            => __( 'User_can_cancel', 'woocommerce-bookings' ),
		);
	}

	/**
	 * When exporting  WC core uses columns to decided which properties to export.
	 * Add the bookings properties  to this list to ensure that we export those values.
	 *
	 * @since 1.11.0
	 *
	 * @param array $default_columns
	 *
	 * @return array
	 */
	public function add_column_names( $default_columns ) {
		return $default_columns + $this->properties;
	}

	/**
	 * Add person types to the booking export.
	 *
	 * @since 1.11.0
	 *
	 * @param array $row
	 * @param WC_Product
	 *
	 * @return array
	 */
	public function add_person_types( $row, $product ) {
		if ( 'booking' !== $product->get_type() ){
			return $row;
		}
		$types = $product->get_person_types();
		$types_to_export = $this->convert_types_for_export( $types );
		$row[ 'person_types' ] = $types_to_export;
		return $row;
	}

	/**
	 * Convert array of type WC_Product_Booking_Person_Type to array of array
	 * representing the data from the given objects. This ensures that don't export
	 * objects.
	 *
	 * @since 1.11.0
	 *
	 * @param array $types
	 *
	 * @return array
	 */
	public function convert_types_for_export( $types = array() ) {
		$converted_types = array();
		foreach ( $types as $person_type ){
			$converted_type = array(
				'block_cost'  => $person_type->get_block_cost(),
				'cost'        => $person_type->get_cost(),
				'description' => $person_type->get_description(),
				'max'         => $person_type->get_max(),
				'min'         => $person_type->get_min(),
				'name'        => $person_type->get_name(),
				'parent_id'   => $person_type->get_parent_id(),
				'sort_order'  => $person_type->get_sort_order(),
		   	);
			$converted_types[] = $converted_type;
		}
		return serialize( $converted_types );
	}
}
