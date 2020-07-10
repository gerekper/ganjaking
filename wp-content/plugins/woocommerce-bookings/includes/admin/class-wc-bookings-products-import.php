<?php

/**
 * Class for extending WooCommerce Core Bookable Product Imports
 */
class WC_Booking_Products_Import {
	/**
	 * The data properties we'd like to include in the import.
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
		add_filter( 'woocommerce_csv_product_import_mapping_options', array( $this, 'add_mapped_fields' ), 20, 2 );
		add_filter( 'woocommerce_product_import_pre_insert_product_object', array( $this, 'convert_person_types' ), 20, 2 );
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
	 * When importing WC core uses maps to link data to properties.
	 * Add the bookings mappings to this list.
	 *
	 * @since 1.11.0
	 *
	 * @param array $mappings
	 *
	 * @return array
	 */
	public function add_mapped_fields( $mappings ) {
		return $mappings + array_flip( $this->properties );
	}

	/**
	 * We store the person type data as a serialized array on export.
	 * This function converts it from a serialized array to the required
	 * objects before we save the product.
	 *
	 * @since 1.11.0
	 *
	 * @param WC_Product $product Could be bookable or standard product.
	 * @param array $data Raw import data added tot the WC_Product.
	 *
	 * @return array
	 */
	public function convert_person_types( $product, $data ) {
		if( ! is_callable( array( $product, 'has_person_types' ) )
			|| ! $product->has_person_types() ) {
			return $product;
		}

		// when set the string is cast to array so we need to unwrap it to get
		// the serialized string.
		$import_types = $product->get_person_types();
		if( is_array( $import_types ) ) {
			$import_types = $import_types[0];
		}
		$import_types = maybe_unserialize( $import_types );

		$person_types = array();
		foreach ( $import_types as $person_type_data ){
			$person_type = new WC_Product_Booking_Person_Type();
			$person_type->set_block_cost( $person_type_data['block_cost'] );
			$person_type->set_cost( $person_type_data['cost'] );
			$person_type->set_description( $person_type_data['description'] );
			$person_type->set_max( $person_type_data['max'] );
			$person_type->set_min( $person_type_data['min'] );
			$person_type->set_name( $person_type_data['name'] );
			$person_type->set_parent_id( $product->get_id() );
			$person_type->set_sort_order( $person_type_data['sort_order'] );
			$person_type->save();

			$person_types[] = $person_type;
		}

		$product->set_person_types( $person_types );
		return $product;
	}
}
