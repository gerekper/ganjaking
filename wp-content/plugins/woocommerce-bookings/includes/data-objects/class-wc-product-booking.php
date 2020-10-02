<?php

if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
	class WC_Product_Booking_Compatibility extends Legacy_WC_Product_Booking {}
} else {
	class WC_Product_Booking_Compatibility extends WC_Product {}
}

/**
 * Class for the booking product type.
 */
class WC_Product_Booking extends WC_Product_Booking_Compatibility {
	/**
	 * Stores product data.
	 *
	 * @var array
	 */
	protected $defaults = array(
		'apply_adjacent_buffer'      => false,
		'availability'               => array(),
		'block_cost'                 => 0,
		'buffer_period'              => 0,
		'calendar_display_mode'      => 'always_visible',
		'cancel_limit_unit'          => 'month',
		'cancel_limit'               => 1,
		'check_start_block_only'     => false,
		'cost'                       => 0,
		'default_date_availability'  => '',
		'display_cost'               => '',
		'duration_type'              => 'fixed',
		'duration_unit'              => 'day',
		'duration'                   => 1,
		'enable_range_picker'        => false,
		'first_block_time'           => '',
		'has_person_cost_multiplier' => false,
		'has_person_qty_multiplier'  => false,
		'has_person_types'           => false,
		'has_persons'                => false,
		'has_resources'              => false,
		'has_restricted_days'        => false,
		'max_date_unit'              => 'month',
		'max_date_value'             => 12,
		'max_duration'               => 1,
		'max_persons'                => 1,
		'min_date_unit'              => 'day',
		'min_date_value'             => 0,
		'min_duration'               => 1,
		'min_persons'                => 1,
		'person_types'               => array(),
		'pricing'                    => array(),
		'qty'                        => 1,
		'requires_confirmation'      => false,
		'resource_label'              => '',
		'resource_base_costs'        => array(),
		'resource_block_costs'       => array(),
		'resource_ids'               => array(),
		'resources_assignment'       => '',
		'restricted_days'            => '',
		'user_can_cancel'            => false,
	);

	/**
	 * Stores availability rules once loaded.
	 *
	 * @var array
	 */
	public $availability_rules = array();

	/**
	 * Merges booking product data into the parent object.
	 *
	 * @param int|WC_Product|object $product Product to init.
	 */
	public function __construct( $product = 0 ) {
		$this->data = array_merge( $this->data, $this->defaults );
		parent::__construct( $product );
	}

	/**
	 * Get the add to cart button text for the single page
	 *
	 * @return string
	 */
	public function single_add_to_cart_text() {
		return $this->get_requires_confirmation() ? apply_filters( 'woocommerce_booking_single_check_availability_text', __( 'Check Availability', 'woocommerce-bookings' ), $this ) : apply_filters( 'woocommerce_booking_single_add_to_cart_text', __( 'Book now', 'woocommerce-bookings' ), $this );
	}

	/**
	 * Get product price.
	 *
	 * @param string $context
	 * @param bool   $filters
	 * @return string
	 */
	public function get_price( $context = 'view' ) {
		$price = get_post_meta( $this->get_id(), '_price', '' );

		return $price ? parent::get_price( $context ) : WC_Bookings_Cost_Calculation::calculated_base_cost( $this );
	}

	/**
	 * Get price HTML
	 * @param string $price
	 * @return string
	 */
	public function get_price_html( $price = '' ) {
		$base_price = WC_Bookings_Cost_Calculation::calculated_base_cost( $this );

		if ( 'incl' === get_option( 'woocommerce_tax_display_shop' ) ) {
			if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
				$display_price = wc_get_price_including_tax( $this, array(
					'qty' => 1,
					'price' => $base_price,
				) );
			} else {
				$display_price = $this->get_price_including_tax( 1, $base_price );
			}
		} else {
			if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
				$display_price = wc_get_price_excluding_tax( $this, array(
					'qty' => 1,
					'price' => $base_price,
				) );
			} else {
				$display_price = $this->get_price_excluding_tax( 1, $base_price );
			}
		}

		$display_price_suffix  = wc_price( apply_filters( 'woocommerce_product_get_price', $display_price, $this ) ) . $this->get_price_suffix();
		$original_price_suffix = wc_price( $display_price ) . $this->get_price_suffix();

		if ( $original_price_suffix !== $display_price_suffix ) {
			$price_html = "<del>{$original_price_suffix}</del><ins>{$display_price_suffix}</ins>";
		} elseif ( $display_price ) {
			if ( $this->has_additional_costs() || $this->get_display_cost() ) {
				/* translators: 1: display price */
				$price_html = sprintf( __( 'From: %s', 'woocommerce-bookings' ), wc_price( $display_price ) ) . $this->get_price_suffix();
			} else {
				$price_html = wc_price( $display_price ) . $this->get_price_suffix();
			}
		} elseif ( ! $this->has_additional_costs() ) {
			$price_html = __( 'Free', 'woocommerce-bookings' );
		} else {
			$price_html = '';
		}

		return apply_filters( 'woocommerce_get_price_html', $price_html, $this );
	}

	/**
	 * Get internal type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'booking';
	}

	/*
	|--------------------------------------------------------------------------
	| WC 3.0 Product Type Options functions
	|
	| WooCommerce 3.0 will call this function when determining if
	| the product type boxes are checked.
	|
	| This ensures forward compatibility if the data source is no longer post-meta
	|--------------------------------------------------------------------------
	*/
	/**
	 * @since 1.10.0
	 * @return bool
	 */
	public function is_wc_booking_has_persons() {
		return $this->has_persons();
	}

	/**
	 * @since 1.10.0
	 * @return bool
	 */
	public function is_wc_booking_has_resources() {
		return $this->has_resources();
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD Getters and setters.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get the qty available to book per block.
	 *
	 * @param  string $context
	 * @return integer
	 */
	public function get_qty( $context = 'view' ) {
		return $this->get_prop( 'qty', $context );
	}

	/**
	 * Set qty.
	 *
	 * @param integer $value
	 */
	public function set_qty( $value ) {
		$this->set_prop( 'qty', absint( $value ) );
	}

	/**
	 * Get has_persons.
	 *
	 * @param  string $context
	 * @return boolean
	 */
	public function get_has_persons( $context = 'view' ) {
		return $this->get_prop( 'has_persons', $context );
	}

	/**
	 * Set has_persons.
	 * @param boolean $value
	 */
	public function set_has_persons( $value ) {
		$this->set_prop( 'has_persons', $value );
	}

	/**
	 * Get has_person_types.
	 *
	 * @param  string $context
	 * @return boolean
	 */
	public function get_has_person_types( $context = 'view' ) {
		return $this->get_prop( 'has_person_types', $context );
	}

	/**
	 * Set has_person_types.
	 *
	 * @param boolean $value
	 */
	public function set_has_person_types( $value ) {
		$this->set_prop( 'has_person_types', wc_bookings_string_to_bool( $value ) );
	}

	/**
	 * Get has_person_qty_multiplier.
	 *
	 * @param  string $context
	 * @return boolean
	 */
	public function get_has_person_qty_multiplier( $context = 'view' ) {
		return $this->get_prop( 'has_person_qty_multiplier', $context );
	}

	/**
	 * Set has_person_qty_multiplier.
	 *
	 * @param boolean $value
	 */
	public function set_has_person_qty_multiplier( $value ) {
		$this->set_prop( 'has_person_qty_multiplier', wc_bookings_string_to_bool( $value ) );
	}

	/**
	 * Get min_persons.
	 *
	 * @param  string $context
	 * @return integer
	 */
	public function get_min_persons( $context = 'view' ) {
		return $this->get_prop( 'min_persons', $context );
	}

	/**
	 * Set min_persons.
	 *
	 * @param integer $value
	 */
	public function set_min_persons( $value ) {
		$this->set_prop( 'min_persons', absint( $value ) );
	}

	/**
	 * Get max_persons.
	 *
	 * @param  string $context
	 * @return integer
	 */
	public function get_max_persons( $context = 'view' ) {
		return $this->get_prop( 'max_persons', $context );
	}

	/**
	 * Set max_persons.
	 *
	 * @param integer $value
	 */
	public function set_max_persons( $value ) {
		$this->set_prop( 'max_persons', absint( $value ) );
	}

	/**
	 * Get has_resources.
	 *
	 * @param  string $context
	 * @return boolean
	 */
	public function get_has_resources( $context = 'view' ) {
		return $this->get_prop( 'has_resources', $context );
	}

	/**
	 * Set has_resources.
	 *
	 * @param boolean $value
	 */
	public function set_has_resources( $value ) {
		$this->set_prop( 'has_resources', $value );
	}

	/**
	 * Get duration.
	 *
	 * @param  string $context
	 * @return integer
	 */
	public function get_duration( $context = 'view' ) {
		return $this->get_prop( 'duration', $context );
	}

	/**
	 * Set duration.
	 *
	 * @param integer $value
	 */
	public function set_duration( $value ) {
		$this->set_prop( 'duration', absint( $value ) );
	}

	/**
	 * Get duration_unit.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_duration_unit( $context = 'view' ) {
		$value = $this->get_prop( 'duration_unit', $context );

		if ( 'view' === $context ) {
			$value = apply_filters( 'woocommerce_bookings_get_duration_unit', $value, $this );
		}
		return $value;
	}

	/**
	 * Set duration_unit.
	 *
	 * @param string $value
	 */
	public function set_duration_unit( $value ) {
		$this->set_prop( 'duration_unit', (string) $value );
	}

	/**
	 * Get duration_type.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_duration_type( $context = 'view' ) {
		return $this->get_prop( 'duration_type', $context );
	}

	/**
	 * Set duration_type.
	 *
	 * @param string $value
	 */
	public function set_duration_type( $value ) {
		$this->set_prop( 'duration_type', (string) $value );
	}

	/**
	 * Get min_duration.
	 * Returns 1 if duration is fixed on view context.
	 *
	 * @param  string $context
	 * @return integer
	 */
	public function get_min_duration( $context = 'view' ) {
		if ( 'view' === $context && 'fixed' === $this->get_duration_type() ) {
			return 1;
		}
		return $this->get_prop( 'min_duration', $context );
	}

	/**
	 * Set min_duration.
	 *
	 * @param integer $value
	 */
	public function set_min_duration( $value ) {
		$this->set_prop( 'min_duration', absint( $value ) );
	}

	/**
	 * Get max_duration.
	 * Returns 1 if duration is fixed on view context.
	 *
	 * @param  string $context
	 * @return integer
	 */
	public function get_max_duration( $context = 'view' ) {
		if ( 'view' === $context && 'fixed' === $this->get_duration_type() ) {
			return 1;
		}
		return $this->get_prop( 'max_duration', $context );
	}

	/**
	 * Set max_duration.
	 *
	 * @param integer $value
	 */
	public function set_max_duration( $value ) {
		$this->set_prop( 'max_duration', absint( $value ) );
	}

	/**
	 * Get enable_range_picker.
	 *
	 * @param  string $context
	 * @return boolean
	 */
	public function get_enable_range_picker( $context = 'view' ) {
		return $this->get_prop( 'enable_range_picker', $context );
	}

	/**
	 * Set enable_range_picker.
	 *
	 * @param boolean $value
	 */
	public function set_enable_range_picker( $value ) {
		$this->set_prop( 'enable_range_picker', wc_bookings_string_to_bool( $value ) );
	}

	/**
	 * Get display_cost.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_display_cost( $context = 'view' ) {
		return $this->get_prop( 'display_cost', $context );
	}

	/**
	 * Set display_cost.
	 *
	 * @param string $value
	 */
	public function set_display_cost( $value ) {
		$this->set_prop( 'display_cost', (string) $value );
	}

	/**
	 * Get base_cost.
	 *
	 * @param  string $context
	 * @return float
	 * @deprecated since 1.10.9
	 */
	public function get_base_cost( $context = 'view' ) {
		return $this->get_block_cost( $context );
	}

	/**
	 * Set base_cost.
	 *
	 * @param float $value
	 * @deprecated since 1.10.9
	 */
	public function set_base_cost( $value ) {
		$this->set_block_cost( $value );
	}

	/**
	 * Get block_cost.
	 *
	 * @param  string $context
	 * @return float
	 */
	public function get_block_cost( $context = 'view' ) {
		return (float) $this->get_prop( 'block_cost', $context );
	}

	/**
	 * Set block_cost.
	 *
	 * @param float $value
	 */
	public function set_block_cost( $value ) {
		$this->set_prop( 'block_cost', wc_format_decimal( $value ) );
	}

	/**
	 * Get cost.
	 *
	 * @param  string $context
	 * @return float
	 */
	public function get_cost( $context = 'view' ) {
		return (float) $this->get_prop( 'cost', $context );
	}

	/**
	 * Set cost.
	 *
	 * @param float $value
	 */
	public function set_cost( $value ) {
		$this->set_prop( 'cost', wc_format_decimal( $value ) );
	}

	/**
	 * Get has_person_cost_multiplier.
	 *
	 * @param  string $context
	 * @return boolean
	 */
	public function get_has_person_cost_multiplier( $context = 'view' ) {
		return $this->get_prop( 'has_person_cost_multiplier', $context );
	}

	/**
	 * Set has_person_cost_multiplier.
	 *
	 * @param boolean $value
	 */
	public function set_has_person_cost_multiplier( $value ) {
		$this->set_prop( 'has_person_cost_multiplier', wc_bookings_string_to_bool( $value ) );
	}

	/**
	 * Get has_additional_costs.
	 *
	 * @param  string $context
	 * @return boolean
	 */
	public function get_has_additional_costs( $context = 'view' ) {
		return $this->get_prop( 'has_additional_costs', $context );
	}

	/**
	 * Set has_additional_costs.
	 *
	 * @param boolean $value
	 */
	public function set_has_additional_costs( $value ) {
		$this->set_prop( 'has_additional_costs', wc_bookings_string_to_bool( $value ) );
	}

	/**
	 * Get min_date_value.
	 *
	 * @param  string $context
	 * @return integer
	 */
	public function get_min_date_value( $context = 'view' ) {
		return $this->get_prop( 'min_date_value', $context );
	}

	/**
	 * Set min_date_value.
	 *
	 * @param integer $value
	 */
	public function set_min_date_value( $value ) {
		$this->set_prop( 'min_date_value', absint( $value ) );
	}

	/**
	 * Get min_date_unit.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_min_date_unit( $context = 'view' ) {
		return $this->get_prop( 'min_date_unit', $context );
	}

	/**
	 * Set min_date_unit.
	 *
	 * @param string $value
	 */
	public function set_min_date_unit( $value ) {
		$this->set_prop( 'min_date_unit', (string) $value );
	}

	/**
	 * Get max_date_value.
	 *
	 * @param  string $context
	 * @return integer
	 */
	public function get_max_date_value( $context = 'view' ) {
		return $this->get_prop( 'max_date_value', $context );
	}

	/**
	 * Set max_date_value.
	 *
	 * @param integer $value
	 */
	public function set_max_date_value( $value ) {
		$this->set_prop( 'max_date_value', absint( $value ) );
	}

	/**
	 * Get max_date_unit.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_max_date_unit( $context = 'view' ) {
		return $this->get_prop( 'max_date_unit', $context );
	}

	/**
	 * Set max_date_unit.
	 *
	 * @param string $value
	 */
	public function set_max_date_unit( $value ) {
		$this->set_prop( 'max_date_unit', (string) $value );
	}

	/**
	 * Get resources_assignment.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_resources_assignment( $context = 'view' ) {
		return $this->get_prop( 'resources_assignment', $context );
	}

	/**
	 * Set resources_assignment.
	 *
	 * @param string $value
	 */
	public function set_resources_assignment( $value ) {
		$this->set_prop( 'resources_assignment', (string) $value );
	}

	/**
	 * Get default_date_availability.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_default_date_availability( $context = 'view' ) {
		return $this->get_prop( 'default_date_availability', $context );
	}

	/**
	 * Set default_date_availability.
	 *
	 * @param string $value
	 */
	public function set_default_date_availability( $value ) {
		$this->set_prop( 'default_date_availability', (string) $value );
	}

	/**
	 * Get requires_confirmation.
	 *
	 * @param  string $context
	 * @return boolean
	 */
	public function get_requires_confirmation( $context = 'view' ) {
		return $this->get_prop( 'requires_confirmation', $context );
	}

	/**
	 * Set requires_confirmation.
	 *
	 * @param boolean $value
	 */
	public function set_requires_confirmation( $value ) {
		$this->set_prop( 'requires_confirmation', wc_bookings_string_to_bool( $value ) );
	}

	/**
	 * Get user_can_cancel.
	 *
	 * @param  string $context
	 * @return boolean
	 */
	public function get_user_can_cancel( $context = 'view' ) {
		return $this->get_prop( 'user_can_cancel', $context );
	}

	/**
	 * Set user_can_cancel.
	 *
	 * @param boolean $value
	 */
	public function set_user_can_cancel( $value ) {
		$this->set_prop( 'user_can_cancel', wc_bookings_string_to_bool( $value ) );
	}

	/**
	 * Get buffer_period.
	 *
	 * @param  string $context
	 * @return integer
	 */
	public function get_buffer_period( $context = 'view' ) {
		return $this->get_prop( 'buffer_period', $context );
	}

	/**
	 * Set buffer_period.
	 *
	 * @param integer $value
	 */
	public function set_buffer_period( $value ) {
		$this->set_prop( 'buffer_period', absint( $value ) );
	}

	/**
	 * Get check_start_block_only.
	 *
	 * @param  string $context
	 * @return bool
	 */
	public function get_check_start_block_only( $context = 'view' ) {
		return $this->get_prop( 'check_start_block_only', $context );
	}

	/**
	 * Set check_start_block_only.
	 *
	 * @param bool $value
	 */
	public function set_check_start_block_only( $value ) {
		$this->set_prop( 'check_start_block_only', wc_bookings_string_to_bool( $value ) );
	}

	/**
	 * Get calendar_display_mode.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_calendar_display_mode( $context = 'view' ) {
		return $this->get_prop( 'calendar_display_mode', $context );
	}

	/**
	 * Set calendar_display_mode.
	 *
	 * @param string $value
	 */
	public function set_calendar_display_mode( $value ) {
		$value = in_array( $value, array( '', 'always_visible' ) ) ? $value : '';
		$this->set_prop( 'calendar_display_mode', $value );
	}

	/**
	 * Get cancel_limit.
	 *
	 * @param  string $context
	 * @return integer
	 */
	public function get_cancel_limit( $context = 'view' ) {
		return $this->get_prop( 'cancel_limit', $context );
	}

	/**
	 * Set cancel_limit.
	 *
	 * @param integer $value
	 */
	public function set_cancel_limit( $value ) {
		$this->set_prop( 'cancel_limit', max( 1, absint( $value ) ) );
	}

	/**
	 * Get cancel_limit_unit.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_cancel_limit_unit( $context = 'view' ) {
		return $this->get_prop( 'cancel_limit_unit', $context );
	}

	/**
	 * Set cancel_limit_unit.
	 *
	 * @param string $value
	 */
	public function set_cancel_limit_unit( $value ) {
		$value = in_array( $value, array( 'month', 'day', 'hour', 'minute' ) ) ? $value : 'month';
		$this->set_prop( 'cancel_limit_unit', $value );
	}

	/**
	 * Get first_block_time.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_first_block_time( $context = 'view' ) {
		return $this->get_prop( 'first_block_time', $context );
	}

	/**
	 * Set first_block_time.
	 *
	 * @param string $value
	 */
	public function set_first_block_time( $value ) {
		$this->set_prop( 'first_block_time', $value );
	}

	/**
	 * Get resource_label.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_resource_label( $context = 'view' ) {
		return $this->get_prop( 'resource_label', $context );
	}

	/**
	 * Set resource_label.
	 *
	 * @param string $value
	 */
	public function set_resource_label( $value ) {
		$this->set_prop( 'resource_label', $value );
	}

	/**
	 * Get apply_adjacent_buffer.
	 *
	 * @param  string $context
	 * @return bool
	 */
	public function get_apply_adjacent_buffer( $context = 'view' ) {
		return $this->get_prop( 'apply_adjacent_buffer', $context );
	}

	/**
	 * Set apply_adjacent_buffer.
	 *
	 * @param bool $value
	 */
	public function set_apply_adjacent_buffer( $value ) {
		$this->set_prop( 'apply_adjacent_buffer', wc_bookings_string_to_bool( $value ) );
	}

	/**
	 * Get availability.
	 *
	 * @param  string $context
	 * @return array
	 */
	public function get_availability( $context = 'view' ) {
		return $this->get_prop( 'availability', $context );
	}

	/**
	 * Set availability.
	 *
	 * @param array $value
	 */
	public function set_availability( $value ) {
		$this->set_prop( 'availability', (array) $value );
	}

	/**
	 * Get pricing_rules.
	 *
	 * @param  string $context
	 * @return array
	 */
	public function get_pricing( $context = 'view' ) {
		return $this->get_prop( 'pricing', $context );
	}

	/**
	 * Set pricing_rules.
	 *
	 * @param array $value
	 */
	public function set_pricing( $value ) {
		$this->set_prop( 'pricing', (array) $value );
	}

	/**
	 * Get person_types.
	 *
	 * @param  string $context
	 * @return array
	 */
	public function get_person_types( $context = 'view' ) {
		return $this->get_prop( 'person_types', $context );
	}

	/**
	 * Set person_types.
	 *
	 * @param array $value
	 */
	public function set_person_types( $value ) {
		$this->set_prop( 'person_types', (array) $value );
	}
	/**
	 * Add a Person_Type ID to the existing list of ids.
	 * @param WC_Product_Booking_Person_Type $person_type
	 */
	public function add_person_type( $person_type ) {
		$person_types = $this->get_person_types();
		$person_types[] = $person_type;
		$this->set_person_types( $person_types );
	}

	/**
	 * Get resource_ids.
	 *
	 * @param  string $context
	 * @return array
	 */
	public function get_resource_ids( $context = 'view' ) {
		return $this->get_prop( 'resource_ids', $context );
	}

	/**
	 * Set resource_ids.
	 *
	 * @param array $value
	 */
	public function set_resource_ids( $value ) {
		$this->set_prop( 'resource_ids', wp_parse_id_list( (array) $value ) );
	}

	/**
	 * Add resource ID to the existing list of ids.
	 * @param int $id
	 */
	public function add_resource_id( $id ) {
		$ids = $this->get_resource_ids();
		$ids[] = $id;
		$this->set_resource_ids( $ids );
	}

	/**
	 * Get resource_base_costs.
	 *
	 * @param  string $context
	 * @return array
	 */
	public function get_resource_base_costs( $context = 'view' ) {
		return $this->get_prop( 'resource_base_costs', $context );
	}

	/**
	 * Set resource_base_costs.
	 *
	 * @param array $value
	 */
	public function set_resource_base_costs( $value ) {
		$this->set_prop( 'resource_base_costs', (array) $value );
	}

	/**
	 * Get resource_block_costs.
	 *
	 * @param  string $context
	 * @return array
	 */
	public function get_resource_block_costs( $context = 'view' ) {
		return $this->get_prop( 'resource_block_costs', $context );
	}

	/**
	 * Set resource_block_costs.
	 *
	 * @param array $value
	 */
	public function set_resource_block_costs( $value ) {
		$this->set_prop( 'resource_block_costs', (array) $value );
	}

	/**
	 * Get has_restricted_days.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_has_restricted_days( $context = 'view' ) {
		return $this->get_prop( 'has_restricted_days', $context );
	}

	/**
	 * Set has_restricted_days.
	 *
	 * @param string $value
	 */
	public function set_has_restricted_days( $value ) {
		$this->set_prop( 'has_restricted_days', $value );
	}

	/**
	 * Get restricted_days.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_restricted_days( $context = 'view' ) {
		return $this->get_prop( 'restricted_days', $context );
	}

	/**
	 * Set restricted_days.
	 *
	 * @param string $value
	 */
	public function set_restricted_days( $value ) {
		$this->set_prop( 'restricted_days', $value );
	}


	/*
	|--------------------------------------------------------------------------
	| Conditionals
	|--------------------------------------------------------------------------
	|
	| Conditionals functions which return true or false.
	*/

	/**
	 * If this product class is a skeleton/place holder class (used for booking addons).
	 *
	 * @return boolean
	 */
	public function is_skeleton() {
		return false;
	}

	/**
	 * If this product class is an addon for bookings.
	 *
	 * @return boolean
	 */
	public function is_bookings_addon() {
		return false;
	}

	/**
	 * Extension/plugin/add-on name for the booking addon this product refers to.
	 *
	 * @return string
	 */
	public function bookings_addon_title() {
		return '';
	}

	/**
	 * We want to sell bookings one at a time.
	 *
	 * @return boolean
	 */
	public function is_sold_individually() {
		return true;
	}

	/**
	 * Bookings can always be purchased regardless of price.
	 *
	 * @return boolean
	 */
	public function is_purchasable() {
		$status = is_callable( array( $this, 'get_status' ) ) ? $this->get_status() : $this->post->post_status;
		return apply_filters( 'woocommerce_is_purchasable', $this->exists() && ( 'publish' === $status || current_user_can( 'edit_post', $this->get_id() ) ), $this );
	}

	/**
	 * See if this booking product has persons enabled.
	 *
	 * @return boolean
	 */
	public function has_persons() {
		return $this->get_has_persons();
	}

	/**
	 * See if this booking product has person types enabled.
	 *
	 * @return boolean
	 */
	public function has_person_types() {
		return $this->get_has_person_types();
	}

	/**
	 * See if persons affect the booked qty.
	 *
	 * @return boolean
	 */
	public function has_person_qty_multiplier() {
		return $this->get_has_persons() && $this->get_has_person_qty_multiplier();
	}

	/**
	 * See if this booking product has resources enabled.
	 *
	 * @return boolean
	 */
	public function has_resources() {
		return $this->get_has_resources();
	}

	/**
	 * Test duration type.
	 *
	 * @param string $type
	 * @return boolean
	 */
	public function is_duration_type( $type ) {
		return $this->get_duration_type() === $type;
	}

	/**
	 * is_range_picker_enabled.
	 *
	 * @return bool
	 */
	public function is_range_picker_enabled() {
		return $this->get_enable_range_picker() && in_array( $this->get_duration_unit(), array( 'day', 'month' ) ) && $this->is_duration_type( 'customer' ) && 1 === $this->get_duration();
	}

	/**
	 * Return if booking has extra costs.
	 *
	 * @return bool
	 */
	public function has_additional_costs() {
		if ( $this->get_has_additional_costs() ) {
			return true;
		}

		if ( $this->has_persons() && $this->get_has_person_cost_multiplier() ) {
			return true;
		}

		if ( $this->get_has_person_types() ) {
			$person_types = $this->get_person_types();
			foreach ( $person_types as $person_type ) {
				if ( $person_type->get_cost() || $person_type->get_block_cost() ) {
					return true;
				}
			}
		}

		if ( $this->has_resources() ) {
			$resources = $this->get_resources();
			foreach ( $resources as $resource ) {
				if ( $resource->get_base_cost() || $resource->get_block_cost() ) {
					return true;
				}
			}
		}

		if ( $this->get_min_duration() > 1 && $this->get_block_cost() ) {
			return true;
		}

		if ( $this->is_duration_type( 'customer' ) ) {
			return true;
		}

		$costs = $this->get_costs();
		if ( ! empty( $costs ) ) {
			return true;
		}

		return false;
	}

	/**
	 * How resources are assigned.
	 *
	 * @param string $type
	 * @return boolean customer or automatic
	 */
	public function is_resource_assignment_type( $type ) {
		return $this->get_resources_assignment() === $type;
	}

	/**
	 * Checks if a product requires confirmation.
	 *
	 * @return bool
	 */
	public function requires_confirmation() {
		return apply_filters( 'woocommerce_booking_requires_confirmation', $this->get_requires_confirmation(), $this );
	}

	/**
	 * See if the booking can be cancelled.
	 *
	 * @return boolean
	 */
	public function can_be_cancelled() {
		return apply_filters( 'woocommerce_booking_user_can_cancel', $this->get_user_can_cancel(), $this );
	}

	/**
	 * See if dates are by default bookable.
	 *
	 * @return bool
	 */
	public function get_default_availability() {
		return 'available' === $this->get_default_date_availability();
	}

	/**
	 * See if this booking product has restricted days.
	 *
	 * @return boolean
	 */
	public function has_restricted_days() {
		return $this->get_has_restricted_days();
	}


	/*
	|--------------------------------------------------------------------------
	| Non-CRUD getters
	|--------------------------------------------------------------------------
	*/
	/**
	 * Gets all formatted cost rules.
	 *
	 * @return array
	 */
	public function get_costs() {
		return WC_Product_Booking_Rule_Manager::process_cost_rules( $this->get_pricing() );
	}

	/**
	 * Get Min date.
	 *
	 * @return array|bool
	 */
	public function get_min_date() {
		$min_date['value'] = apply_filters( 'woocommerce_bookings_min_date_value', $this->get_min_date_value(), $this->get_id() );
		$min_date['unit']  = $this->get_min_date_unit() ? apply_filters( 'woocommerce_bookings_min_date_unit', $this->get_min_date_unit(), $this->get_id() ) : 'month';
		return $min_date;
	}

	/**
	 * Get max date.
	 *
	 * @return array
	 */
	public function get_max_date() {
		$max_date['value'] = $this->get_max_date_value() ? apply_filters( 'woocommerce_bookings_max_date_value', $this->get_max_date_value(), $this->get_id() ) : 1;
		$max_date['unit']  = $this->get_max_date_unit() ? apply_filters( 'woocommerce_bookings_max_date_unit', $this->get_max_date_unit(), $this->get_id() ) : 'month';
		return $max_date;
	}

	/**
	 * Get the Product buffer period setting.
	 *
	 * @since 1.9.13 introduced.
	 * @return mixed $buffer_period
	 */
	public function get_buffer_period_minutes() {
		$buffer_period = $this->get_buffer_period();

		// If exists always treat booking_period in minutes.
		if ( ! empty( $buffer_period ) && 'hour' === $this->get_duration_unit() ) {
			$buffer_period = $buffer_period * 60;
		}

		return $buffer_period;
	}

	/**
	 * Get available quantity.
	 *
	 * @since 1.9.13 introduced.
	 * @param $resource_id
	 * @return bool|int
	 */
	public function get_available_quantity( $resource_id = '' ) {
		$booking_resource      = $resource_id ? $this->get_resource( $resource_id ) : null;
		$available_qty         = $this->has_resources() && $booking_resource && $booking_resource->has_qty() ? $booking_resource->get_qty() : $this->get_qty();
		return apply_filters( 'woocommerce_bookings_get_available_quantity', $available_qty, $this, $booking_resource );
	}

	/**
	 * Get person type by ID.
	 *
	 * @param  int $id
	 * @return WP_POST object
	 */
	public function get_person( $id ) {
		$id      = absint( $id );
		$persons = $this->get_person_types();

		if ( isset( $persons[ $id ] ) ) {
			return $persons[ $id ];
		}
		return false;
	}

	/**
	 * Get resource by ID.
	 *
	 * @param  int $id
	 * @return WC_Product_Booking_Resource object
	 */
	public function get_resource( $id ) {
		global $wpdb;

		if ( is_a( $id, 'WC_Product_Booking_Resource' ) ) {
			return $id;
		}

		$id = absint( $id );

		if ( $id ) {
			$transient_name = 'book_res_' . md5( http_build_query( array( $id, $this->get_id(), WC_Cache_Helper::get_transient_version( 'bookings' ) ) ) );
			$relationship_id = WC_Bookings_Cache::get( $transient_name );

			if ( false === $relationship_id ) {
				$relationship_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}wc_booking_relationships WHERE product_id = %d AND resource_id = %d", $this->get_id(), $id ) );
				WC_Bookings_Cache::set( $transient_name, $relationship_id, DAY_IN_SECONDS * 30 );
			}

			$resource = get_post( $id );

			if ( is_object( $resource ) && 'bookable_resource' == $resource->post_type && 0 < $relationship_id ) {
				return new WC_Product_Booking_Resource( $resource, $this->get_id() );
			}
		}

		return false;
	}

	/**
	 * Get resources objects.
	 *
	 * @param WC_Product
	 *
	 * @return array(
	 *   type WC_Product_Booking_Resource
	 * )
	 */
	public function get_resources() {
		$product_resources = array();

		foreach ( $this->get_resource_ids() as $resource_id ) {
			$status = get_post_status( $resource_id );
			if ( 'publish' !== $status ) {
				continue;
			}

			$product_resources[] = new WC_Product_Booking_Resource( $resource_id, $this->get_id() );
		}

		return $product_resources;
	}

	/**
	 * Get rules in order of `override power`. The higher the index the higher the override power. Element at index 4 will
	 * override element at index 2.
	 *
	 * Within priority the rules will be ordered top to bottom.
	 *
	 * @return array  availability_rules {
	 *    @type $resource_id => array {
	 *
	 *       The $order_index depicts the levels override. `0` Is the lowest. `1` overrides `0` and `2` overrides `1`.
	 *       e.g. If monday is set to available in `1` and not available in `2` the results should be that Monday is
	 *       NOT available because `2` overrides `1`.
	 *       $order_index corresponds to override power. The higher the element index the higher the override power.
	 *       @type $order_index => array {
	 *          @type string $type   The type of range selected in admin.
	 *          @type string $range  Depending on the type this depicts what range and if available or not.
	 *          @type integer $priority
	 *          @type string $level Global, Product or Resource
	 *          @type integer $order The index for the order set in admin.
	 *      }
	 * }
	 */
	public function get_availability_rules( $for_resource = 0 ) {
		if ( ! isset( $this->availability_rules[ $for_resource ] ) ) {
			$this->availability_rules[ $for_resource ] = array();

			// Rule types
			$all_resource_rules = array();
			$product_rules      = $this->get_availability();
			$global_rules       = WC_Data_Store::load( 'booking-global-availability' )->get_all_as_array();

			// Get availability of each resource - no resource has been chosen yet
			if ( $this->has_resources() && ! $for_resource && $this->is_resource_assignment_type( 'automatic' ) ) {
				$resources = $this->get_resources();

				// If all blocks are available by default, we should not hide days if we don't know which resource is going to be used.
				if ( ! $this->get_default_availability() ) {

					foreach ( $resources as $resource ) {
						$temp_resource_rules = $resource->get_availability();
						$resource_rules      = array();
						// add resource id to each rule
						foreach ( $temp_resource_rules as $index => $rule ) {
							$resource_rules[ $index ]                = $rule;
							$resource_rules[ $index ]['resource_id'] = $resource->get_id();
						}
						$all_resource_rules = array_merge( $all_resource_rules, $resource_rules );
					}
				}
			} elseif ( $for_resource ) {
				$resource            = new WC_Product_Booking_Resource( $for_resource );
				$temp_resource_rules = $resource->get_availability();
				// add resource to each rule
				foreach ( $temp_resource_rules as $index => $rule ) {
					$all_resource_rules[ $index ]                = $rule;
					$all_resource_rules[ $index ]['resource_id'] = $for_resource;
				}
			}

			// The order that these rules are put into the array are important due to the way that
			// the rules as processed for overrides.
			$availability_rules = array_filter(
				array_merge(
					WC_Product_Booking_Rule_Manager::process_availability_rules( $all_resource_rules, 'resource' ),
					WC_Product_Booking_Rule_Manager::process_availability_rules( $product_rules, 'product' ),
					WC_Product_Booking_Rule_Manager::process_availability_rules( $global_rules, 'global' )
				)
			);

			usort( $availability_rules, array( 'WC_Product_Booking_Rule_Manager', 'sort_rules_callback' ) );

			$this->availability_rules[ $for_resource ] = $availability_rules;
		}

		return apply_filters( 'woocommerce_booking_get_availability_rules', $this->availability_rules[ $for_resource ], $for_resource, $this );
	}

	/*
	|--------------------------------------------------------------------------
	| Block calculation functions. @todo move to own manager class
	|--------------------------------------------------------------------------
	*/

	/**
	 * Check the resources availability against all the blocks.
	 *
	 * @param  string $start_date
	 * @param  string $end_date
	 * @param  int    $qty
	 * @param  WC_Product_Booking_Resource|null $booking_resource
	 * @param  array  $intervals
	 * @return string|WP_Error
	 */
	public function get_blocks_availability( $start_date, $end_date, $qty, $booking_resource = null, $intervals = array() ) {

		$resource_id = isset( $booking_resource ) ? $booking_resource->get_id() : 0;
		$interval    = 'hour' === $this->get_duration_unit() ? $this->get_duration() * 60 : $this->get_duration();

		$bookings_end_date = $end_date - 1;

		// If we have an adjacent buffer then look for bookings till the end time + buffer time.
		if ( $this->get_apply_adjacent_buffer() ) {
			$bookings_end_date += $this->get_buffer_period_minutes() * 60;
		}

		$existing_bookings = WC_Booking_Data_Store::get_bookings_in_date_range( $start_date + 1, $bookings_end_date, $this->has_resources() && $resource_id ? $resource_id : $this->get_id() );
		$blocks            = $this->get_blocks_in_range( $start_date, $end_date, $intervals, $resource_id );

		if ( empty( $blocks ) || ! in_array( $start_date, $blocks ) ) {
			return false;
		}

		$blocks = array_unique( array_merge( array_map( function( $booking ) {
			return $booking->get_start();
		}, $existing_bookings ), $blocks ) );

		$original_available_qty = $this->get_available_quantity( $booking_resource );

		// Check all blocks availability
		$available_qtys    = array();
		foreach ( $blocks as $block ) {
			// Initially, for every minute block, there will be 0 bookings.
			$block_minutes_array = wc_bookings_get_block_minutes_array( $block, $interval );
			$qty_booked_in_block = $block_minutes_array;

			foreach ( $existing_bookings as $existing_booking ) {

				if ( ! $existing_booking->is_within_block( $block, strtotime( "+{$interval} minutes", $block ) ) ) {
					continue;
				}
				$existing_booking_product    = $existing_booking->get_product();
				$qty_to_add                  = $existing_booking_product->has_person_qty_multiplier() ? $existing_booking->get_persons_total() : 1;
				// The call to `is_within_block` above will ensure we have cached data.
				$existing_booking_block      = $existing_booking->get_start_cached();
				/*
				 * We use ceil to round up intervals that are 1 second shorter then a full minute.
				 * This happens for example for a day interval where one day is basically 24h -1s.
				 * That one second is subtracted in order not to overlap in calculations with the other interval.
				 */
				$existing_booking_interval   = ceil( ( $existing_booking->get_end_cached() - $existing_booking_block ) / MINUTE_IN_SECONDS );
				$booking_block_minutes_array = wc_bookings_get_block_minutes_array( $existing_booking_block, $existing_booking_interval );
				if ( $this->has_resources() ) {
					if ( $existing_booking->get_resource_id() === absint( $resource_id ) || ( ! $booking_resource->has_qty() && $existing_booking->get_resource() && ! $existing_booking->get_resource()->has_qty() ) ) {
						$qty_booked_in_block = wc_bookings_add_at_intersection( $qty_booked_in_block, $booking_block_minutes_array, $qty_to_add );
					}
				} else {
					$qty_booked_in_block = wc_bookings_add_at_intersection( $qty_booked_in_block, $booking_block_minutes_array, $qty_to_add );
				}
			}
			$available_qty = $original_available_qty - max( $qty_booked_in_block );

			// Remaining places are less than requested qty, return an error.
			if ( $available_qty < $qty ) {
				$display_available_qty = $available_qty > 0 ? $available_qty : 0;

				if ( in_array( $this->get_duration_unit(), array( 'hour', 'minute' ) ) ) {
					return new WP_Error( 'Error', sprintf(
						/* translators: 1: available quantity */
						_n( 'There is a maximum of %d place remaining', 'There are a maximum of %d places remaining', $display_available_qty , 'woocommerce-bookings' ),
						$display_available_qty
					) );
				} elseif ( ! $available_qty ) {
					return new WP_Error( 'Error', sprintf(
						/* translators: 1: available quantity 2: booking block date */
						_n( 'There is a maximum of %1$d place remaining on %2$s', 'There are a maximum of %1$d places remaining on %2$s', $display_available_qty , 'woocommerce-bookings' ),
						$display_available_qty,
						date_i18n( wc_bookings_date_format(), $block )
					) );
				} else {
					return new WP_Error( 'Error', sprintf(
						/* translators: 1: available quantity 2: booking block date */
						_n( 'There is a maximum of %1$d place remaining on %2$s', 'There are a maximum of %1$d places remaining on %2$s', $display_available_qty , 'woocommerce-bookings' ),
						$display_available_qty,
						date_i18n( wc_bookings_date_format(), $block )
					) );
				}
			}

			$available_qtys[] = $available_qty;
		}

		return min( $available_qtys );
	}

	/**
	 * Get an array of blocks within in a specified date range - might be days, might be blocks within days, depending on settings.
	 *
	 * @param       $start_date
	 * @param       $end_date
	 * @param array $intervals
	 * @param int   $resource_id
	 * @param array $booked
	 * @param bool  $get_past_times
	 *
	 * @return array
	 */
	public function get_blocks_in_range( $start_date, $end_date, $intervals = array(), $resource_id = 0, $booked = array(), $get_past_times = false ) {

		$default_interval = 'hour' === $this->get_duration_unit() ? $this->get_duration() * 60 : $this->get_duration();

		if ( empty( $intervals ) ) {
			$intervals = array( $default_interval, $default_interval );
		}

		// if we're only checking against the first block the first interval
		// should be equal to the standard slot size.
		if ( $this->get_check_start_block_only() ) {
			$intervals[0] = $default_interval;
		}

		if ( 'day' === $this->get_duration_unit() ) {
			$blocks_in_range = $this->get_blocks_in_range_for_day( $start_date, $end_date, $resource_id, $booked );
		} elseif ( 'month' === $this->get_duration_unit() ) {
			$blocks_in_range = $this->get_blocks_in_range_for_month( $start_date, $end_date, $resource_id );
		} else {
			$blocks_in_range = $this->get_blocks_in_range_for_hour_or_minutes( $start_date, $end_date, $intervals, $resource_id, $booked, $get_past_times );
		}

		return array_unique( $blocks_in_range );
	}

	/**
	 * Get blocks/day blocks in range for day duration unit.
	 *
	 * @param $start_date
	 * @param $end_date
	 * @param integer $resource_id
	 * @param array $bookings { $booking[0] start and $booking[1] end }
	 *
	 * @return array
	 */
	public function get_blocks_in_range_for_day( $start_date, $end_date, $resource_id, $bookings ) {
		$blocks = array();
		$available_qty    = $this->get_available_quantity( $resource_id );

		// get booked days with a counter to specify how many bookings on that date
		$booked_days_with_count = array();
		foreach ( $bookings as $booking ) {
			$booking_start = $booking[0];
			$booking_end   = $booking[1];
			$current_booking_day = $booking_start;

			// < because booking end depicts an end of a day and not a start for a new day.
			while ( $current_booking_day < $booking_end ) {
				$date = date( 'Y-m-d', $current_booking_day );

				if ( isset( $booked_days_with_count[ $date  ] ) ) {
					$booked_days_with_count[ $date ]++;
				} else {
					$booked_days_with_count[ $date ] = 1;
				}

				$current_booking_day = strtotime( '+1 day', $current_booking_day );
			}
		}

		// If exists always treat booking_period in minutes.
		$check_date = $start_date;

		$end_date = $this->get_max_allowed_date_into_the_future( $end_date );

		while ( $check_date <= $end_date ) {
			if ( WC_Product_Booking_Rule_Manager::check_availability_rules_against_date( $this, $resource_id, $check_date ) ) {

				$date = date( 'Y-m-d', $check_date );
				if ( ! isset( $booked_days_with_count[ $date ] ) || $booked_days_with_count[ $date ] < $available_qty ) {
					$blocks[] = $check_date;
				}
			}

			// move to next day
			$check_date = strtotime( '+1 day', $check_date );
		}

		return $blocks;
	}

	/**
	 * For months, loop each month in the range to find blocks.
	 *
	 * @param $start_date
	 * @param $end_date
	 * @param integer $resource_id
	 *
	 * @return array
	 */
	public function get_blocks_in_range_for_month( $start_date, $end_date, $resource_id ) {

		$blocks = array();

		if ( 'month' !== $this->get_duration_unit() ) {
			return $blocks;
		}

		$end_date = $this->get_max_allowed_date_into_the_future( $end_date );

		// Generate a range of blocks for months
		$from       = strtotime( date( 'Y-m-01', $start_date ) );
		$to         = strtotime( date( 'Y-m-t', $end_date ) );
		$month_diff = 0;
		$month_from = strtotime( '+1 MONTH', $from );

		while ( $month_from <= $to ) {
			$month_from = strtotime( '+1 MONTH', $month_from );
			$month_diff ++;
		}

		for ( $i = 0; $i <= $month_diff; $i ++ ) {
			$year  = date( 'Y', ( $i ? strtotime( "+ {$i} month", $from ) : $from ) );
			$month = date( 'n', ( $i ? strtotime( "+ {$i} month", $from ) : $from ) );

			if ( ! WC_Product_Booking_Rule_Manager::check_availability_rules_against_date( $this, $resource_id, strtotime( "{$year}-{$month}-01" ) ) ) {
				continue;
			}

			$blocks[] = strtotime( "+ {$i} month", $from );
		}
		return $blocks;
	}

	/**
	 * Get blocks in range for hour or minute duration unit.
	 * For minutes and hours find valid blocks within THIS DAY ($check_date)
	 *
	 * @param $start_date
	 * @param $end_date
	 * @param $intervals
	 * @param integer $resource_id
	 * @param $booked
	 * @param $get_past_times
	 *
	 * @return array
	 */
	public function get_blocks_in_range_for_hour_or_minutes( $start_date, $end_date, $intervals, $resource_id, $booked, $get_past_times = false ) {
		$block_start_times_in_range = array();
		$interval                   = $intervals[0];
		$check_date                 = $start_date;

		// Setup.
		$first_block_time_minute  = $this->get_first_block_time() ? ( date( 'H', strtotime( $this->get_first_block_time() ) ) * 60 ) + date( 'i', strtotime( $this->get_first_block_time() ) ) : 0;
		$default_bookable_minutes = $this->get_default_availability() ? range( $first_block_time_minute, ( 1440 + $interval ) ) : array();
		$sorted_rules             = $this->get_availability_rules( $resource_id ); // Work out what minutes are actually bookable on this day
		$end_date                 = $this->get_max_allowed_date_into_the_future( $end_date );

		// Get available slot start times.
		$minutes_booked     = $this->get_unavailable_minutes( $booked );

		// Looping day by day look for available blocks
		// using `<=` instead of `<` because https://github.com/woothemes/woocommerce-bookings/issues/325
		while ( $check_date <= $end_date ) {
			$bookable_minutes_for_date  = array_merge( $default_bookable_minutes, WC_Product_Booking_Rule_Manager::get_minutes_from_rules( $sorted_rules, $check_date ) );

			if ( ! $this->get_default_availability() ) {
				// From an array of minutes for a day, remove all minutes before first block time.
				$bookable_minutes_for_date = array_filter(
					$bookable_minutes_for_date,
					function( $minute ) use ( $first_block_time_minute ) {
						return $first_block_time_minute <= $minute;
					}
				);
			}

			$bookable_start_and_end     = $this->get_bookable_minute_start_and_end( $bookable_minutes_for_date );
			$blocks                     = $this->get_bookable_minute_blocks_for_date( $check_date, $start_date, $end_date, $bookable_start_and_end, $intervals, $resource_id, $minutes_booked, $get_past_times );

			$block_start_times_in_range = array_merge( $blocks, $block_start_times_in_range );
			$check_date                 = strtotime( '+1 day', $check_date );// Move to the next day
		}
		return $block_start_times_in_range;
	}

	/**
	 * From an array of minutes for a day remove all minutes before first block time.
	 * @since 1.10.0
	 *
	 * @param array $bookable_minutes
	 * @param int $first_block_minutes
	 *
	 * @return array $minutes
	 */
	public function apply_first_block_time( $bookable_minutes, $first_block_minutes ) {
		_deprecated_function( __METHOD__, '1.12.2' );

		$minutes = array();
		foreach ( $bookable_minutes as $minute ) {
			if ( $first_block_minutes <= $minute ) {
				$minutes[] = $minute;
			}
		}
		return $minutes;
	}

	/**
	 * @param array $bookable_minutes
	 *
	 * @return array
	 */
	public function get_bookable_minute_start_and_end( $bookable_minutes ) {

		// Break bookable minutes into sequences - bookings cannot have breaks
		$bookable_minute_blocks     = array();
		$bookable_minute_block_from = current( $bookable_minutes );

		foreach ( $bookable_minutes as $key => $minute ) {
			if ( isset( $bookable_minutes[ $key + 1 ] ) ) {
				// check if there is a break in the sequence
				if ( $bookable_minutes[ $key + 1 ] - 1 !== $minute ) {
					$bookable_minute_blocks[]   = array( $bookable_minute_block_from, $minute );
					$bookable_minute_block_from = $bookable_minutes[ $key + 1 ];
				}
			} else {
				// We're at the end of the bookable minutes
				$bookable_minute_blocks[] = array( $bookable_minute_block_from, $minute );
			}
		}

		// Find blocks that don't span any amount of time (same start + end)
		foreach ( $bookable_minute_blocks as $key => $bookable_minute_block ) {
			if ( $bookable_minute_block[0] === $bookable_minute_block[1] ) {
				$keys_to_remove[] = $key; // track which blocks need removed
			}
		}
		// Remove all of our blocks
		if ( ! empty( $keys_to_remove ) ) {
			foreach ( $keys_to_remove as $key ) {
				unset( $bookable_minute_blocks[ $key ] );
			}
		}

		return $bookable_minute_blocks;
	}

	/**
	 * Return an array of that is not available for booking.
	 *
	 * @since 1.9.13 introduced.
	 *
	 * @param array $booked. Pairs of booked slot start and end times.
	 * @return array $booked_minutes
	 */
	public function get_unavailable_minutes( $booked ) {
		$minutes_not_available = array();
		$buffer = ( $this->get_buffer_period_minutes() ?: 0 ) * 60;
		$has_adjacent_buffer = $this->get_apply_adjacent_buffer();

		foreach ( $booked as $booked_block ) {
			$start = $booked_block[0];
			$end   = $booked_block[1] + $buffer + ( $has_adjacent_buffer ? $buffer : 0 );
			for ( $i = $start; $i < $end; $i += 60 ) {
				array_push( $minutes_not_available, $i );
			}
		}
		$minutes_not_available = array_count_values( $minutes_not_available );
		return $minutes_not_available;
	}

	/**
	 * @deprecated sine 1.9.15
	 * @param $booked
	 * @return mixed
	 */
	public function get_unavailable_mintues( $booked ) {
		_deprecated_function( 'WooCommerce Bookings, use get_unavailable_minutes instead. ', '1.9.15' );
		return $this->get_unavailable_minutes( $booked );
	}

	/**
	 * Returns blocks/time slots from a given start and end minute blocks.
	 *
	 * This function take varied inputs but always returns a block array of available slots.
	 * Sometimes it gets the minutes and see if all is available some times it needs to make up the
	 * minutes based on what is booked.
	 *
	 * It uses start and end date to figure things out.
	 *
	 * @since 1.9.13 introduced.
	 *
	 * @param $check_date
	 * @param $start_date
	 * @param $end_date
	 * @param $bookable_ranges
	 * @param $intervals
	 * @param integer $resource_id
	 * @param $minutes_not_available
	 * @param $get_past_times
	 *
	 * @return array
	 */
	protected function get_bookable_minute_blocks_for_date( $check_date, $start_date, $end_date, $bookable_ranges, $intervals, $resource_id, $minutes_not_available, $get_past_times = false ) {

		// blocks as in an array of slots. $slot_start_times
		$blocks = array();

		// boring interval stuff
		$interval              = $intervals[0];
		$base_interval         = $intervals[1];

		// get a time stamp to check from
		// and get a time stamp to check to
		$product_min_date = $this->get_min_date();
		$product_max_date = $this->get_max_date();

		$min_check_from     = strtotime( "+{$product_min_date['value']} {$product_min_date['unit']}", current_time( 'timestamp' ) );
		$max_check_to       = strtotime( "+{$product_max_date['value']} {$product_max_date['unit']}", current_time( 'timestamp' ) );
		$min_date           = wc_bookings_get_min_timestamp_for_day( $start_date, $product_min_date['value'], $product_min_date['unit'] );

		$available_qty      = $this->get_available_quantity( $resource_id );
		$current_time_stamp = current_time( 'timestamp' );
		$availability       = $this->get_default_availability();

		// if we have a buffer, we will shift all times accordingly by changing the from_interval
		// e.g. 60 min buffer shifts [ 480, 600, 720 ] into [ 480, 660, 840 ]
		$buffer = $this->get_buffer_period_minutes() ?: 0;

		// if adjacency is enabled, multiply the buffer by 2 (see https://docs.woocommerce.com/document/creating-a-bookable-product/#section-8)
		if ( $this->get_apply_adjacent_buffer() ) {
			$buffer *= 2;
		}

		// Loop ranges looking for slots within
		foreach ( $bookable_ranges as $minutes ) {
			$range_start = $minutes[0];
			$range_end   = $minutes[1];
			if ( 'hour' === $this->get_duration_unit() ) {
				// Adding 1 minute to round up to a full hour.
				$range_end  += 1;
			}

			$range_start_time        = strtotime( "midnight +{$range_start} minutes", $check_date );
			$range_end_time          = strtotime( "midnight +{$range_end} minutes", $check_date );
			$minutes_for_range       = $range_end - $range_start;
			$base_intervals_in_block = floor( $minutes_for_range / $base_interval );

			for ( $i = 0; $i <= $base_intervals_in_block; $i ++ ) {
				$from_interval         = $i * ( $base_interval + $buffer );
				$to_interval           = $from_interval + $interval + $buffer;
				$to_interval_no_buffer = $from_interval + $interval;
				$start_time            = strtotime( "+{$from_interval} minutes", $range_start_time );
				$end_time              = strtotime( "+{$to_interval} minutes", $range_start_time );
				$end_time_no_buffer    = strtotime( "+{$to_interval_no_buffer} minutes", $range_start_time );

				// Break if start time is after the end date being calculated.
				if ( $start_time > $end_date ) {
					break 2;
				}

				// Must be in the future
				if ( ( $start_time < $min_date || $start_time <= $current_time_stamp ) && ! $get_past_times ) {
					continue;
				}

				$interval_not_bookable = false;
				// Check if any minute of block is not within not available minutes.
				for ( $t = $start_time; $t < $end_time; $t += 60 ) {
					if ( isset( $minutes_not_available[ $t ] ) && $minutes_not_available[ $t ] >= $available_qty ) {
						$interval_not_bookable = true;
						break;
					}
				}

				if ( $interval_not_bookable ) {
					continue;
				}

				// Make sure minute & hour blocks are not past minimum & max booking settings.
				if ( ( $end_time < $min_check_from || $start_time > $max_check_to ) && ! $get_past_times ) {
					continue;
				}

				/*
				 * We check only if the booking time will fit.
				 * We should check with buffer time added but unfortunatelly doing this will cause a regression.
				 * Time slots at the end of the day with buffers going over the day end will not be present.
				 */
				if ( $end_time_no_buffer > $range_end_time ) {
					continue;
				}

				// Make sure slot doesn't start after the end date.
				if ( $start_time > $end_date ) {
					continue;
				}

				/*
				 * If default availability is NO/False then it means the minutes we're looking at has already been
				 * generated by using the rules so there's no need to test availability again.
				 */
				if ( $availability
					&& ! WC_Product_Booking_Rule_Manager::check_availability_rules_against_time( $start_time, $end_time, $resource_id, $this, $availability ) ) {
					continue;
				}

				if ( ! in_array( $start_time, $blocks ) ) {
					$blocks[] = $start_time;
				}
			}
		}

		return  $blocks;
	}

	/**
	 * Find available and booked blocks for specific resources (if any) and return them as array.
	 *
	 * @param  array  $blocks
	 * @param  array  $intervals
	 * @param  integer $resource_id
	 * @param  integer $from The starting date for the set of blocks
	 * @param  integer $to
	 * @return array
	 */
	public function get_time_slots( $blocks, $resource_id = 0, $from = 0, $to = 0, $include_sold_out = false ) {

		$default_interval = 'hour' === $this->get_duration_unit() ? $this->get_duration() * 60 : $this->get_duration();
		$intervals        = array( $default_interval, $default_interval );

		if( in_array( $this->get_duration_unit(), array( 'minute', 'hour' ) ) ) {
			return wc_bookings_get_time_slots( $this, $blocks, $intervals, $resource_id, $from, $to, $include_sold_out );
		}

		/*
		* Days and months case.
		*/
		return wc_bookings_get_time_slots( $this, $blocks, $intervals, $resource_id, $from, $to, $include_sold_out );
	}

	/**
	 * Returns available blocks from a range of blocks by looking at existing bookings.
	 *
	 * @param  array $args
	 *     @option  array   $blocks      The blocks we'll be checking availability for.
	 *     @option  array   $intervals   Array containing 2 items; the interval of the block (maybe user set), and the base interval for the block/product.
	 *     @option  integer $resource_id Resource we're getting blocks for. Falls backs to product as a whole if 0.
	 *     @option  integer $from        The starting date for the set of blocks
	 *     @option  integer $to          Ending date for the set of blocks
	 *
	 * @return array The available blocks array
	 */
	public function get_available_blocks( $args ) {
		$args = wp_parse_args( $args, array(
			'blocks'      => array(),
			'intervals'   => array(),
			'resource_id' => 0,
			'from'        => 0,
			'to'          => 0,
		) );

		$blocks      = $args['blocks'];
		$intervals   = $args['intervals'];
		$resource_id = $args['resource_id'];
		$from        = $args['from'];
		$to          = $args['to'];

		if ( empty( $intervals ) ) {
			$default_interval = 'hour' === $this->get_duration_unit() ? $this->get_duration() * 60 : $this->get_duration();
			$intervals        = array( $default_interval, $default_interval );
		}

		$interval      = absint( $intervals[0] );
		$base_interval = absint( $intervals[1] );

		$available_times = array();

		$start_date = $from;
		if ( empty( $start_date ) ) {
			$start_date = reset( $blocks );
		}

		$end_date = $to;
		if ( empty( $end_date ) ) {
			$end_date = absint( end( $blocks ) );
		}

		if ( ! empty( $blocks ) ) {
			/**
			 * Grab all existing bookings for the date range.
			 * Extend end_date to end of last block. Note: base_interval is in minutes.
			 * @var array
			 */
			$existing_bookings = WC_Booking_Data_Store::get_bookings_in_date_range( $start_date, $end_date + ( $base_interval * 60 ), $this->has_resources() && $resource_id ? $resource_id : $this->get_id() );

			// Resources booked array. Resource can be a "resource" but also just a booking if it has no resources
			$resources_booked = array( 0 => array() );

			// Loop all existing bookings
			foreach ( $existing_bookings as $booking ) {
				$booking_resource_id = $booking->get_resource_id();

				// prepare resource array for resource id
				$resources_booked[ $booking_resource_id ] = isset( $resources_booked[ $booking_resource_id ] ) ? $resources_booked[ $booking_resource_id ] : array();

				// if person multiplier is on we should disable stuff where nothing is available
				$repeat = max( 1, $this->has_person_qty_multiplier() && $booking->has_persons() ? $booking->get_persons_total() : 1 );

				for ( $i = 0; $i < $repeat; $i++ ) {
					array_push( $resources_booked[ $booking_resource_id ], array( $booking->get_start(), $booking->get_end() ) );
				}
			}

			// Generate arrays that contain information about what blocks to unset
			if ( $this->has_resources() && ! $resource_id ) {

				$resources       = $this->get_resources();

				// Loop all resources
				foreach ( $resources as $resource ) {
					$times           = $this->get_blocks_in_range( $start_date, $end_date, array( $interval, $base_interval ), $resource->ID, isset( $resources_booked[ $resource->ID ] ) ? $resources_booked[ $resource->ID ] : array() );
					$available_times = array_merge( $available_times, $times );
				}
			} else {
				$bookings = isset( $resources_booked[ $resource_id ] ) ? $resources_booked[ $resource_id ] : $resources_booked[0];
				$available_times = array_merge( $available_times, $this->get_blocks_in_range( $start_date, $end_date, array( $interval, $base_interval ), $resource_id, $bookings ) );
			}
		}

		$available_times = array_unique( $available_times );
		sort( $available_times );

		/**
		 * Filter the available blocks for a product within a given range
		 *
		 * @since 1.9.8 introduced
		 *
		 * @param array $available_blocks
		 * @param WC_Product $bookings_product
		 * @param array $raw_range passed into this function.
		 * @param array $intervals
		 * @param integer $resource_id
		 */
		return apply_filters( 'wc_bookings_product_get_available_blocks', $available_times, $this, $blocks, $intervals, $resource_id );
	}

	/**
	 * Get the availability of all resources
	 *
	 * @param string $start_date
	 * @param string $end_date
	 * @param integer $qty
	 * @return array| WP_Error
	 */
	public function get_all_resources_availability( $start_date, $end_date, $qty ) {
		$resources           = $this->get_resources();
		$available_resources = array();

		foreach ( $resources as $resource ) {
			$availability = wc_bookings_get_total_available_bookings_for_range( $this, $start_date, $end_date, $resource->ID, $qty );

			if ( $availability && ! is_wp_error( $availability ) ) {
				$available_resources[ $resource->ID ] = $availability;
			}
		}

		if ( empty( $available_resources ) ) {
			return new WP_Error( 'Error', __( 'This block cannot be booked.', 'woocommerce-bookings' ) );
		}

		return $available_resources;
	}


	/*
	|--------------------------------------------------------------------------
	| Deprecated Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get the minutes that should be available based on the rules and the date to check.
	 *
	 * The minutes are returned in a range from the start incrementing minutes right up to the last available minute.
	 *
	 * @deprecated since 1.9.14
	 * @param array $rules
	 * @param int $check_date
	 *
	 * @return array $bookable_minutes
	 */
	public function get_minutes_from_rules( $rules, $check_date ) {
		return WC_Product_Booking_Rule_Manager::get_minutes_from_rules( $rules, $check_date );
	}

	/**
	 * Find the minimum block's timestamp based on settings.
	 *
	 * @deprecated Replaced with wc_bookings_get_min_timestamp_for_day
	 * @return int
	 */
	public function get_min_timestamp_for_date( $start_date ) {
		$min = $this->get_min_date();
		return wc_bookings_get_min_timestamp_for_day( $start_date, $min['value'], $min['unit'] );
	}

	/**
	 * Sort rules.
	 *
	 * @deprecated Replaced with WC_Product_Booking_Rule_Manager::sort_rules_callback
	 */
	public function rule_override_power_sort( $rule1, $rule2 ) {
		return WC_Product_Booking_Rule_Manager::sort_rules_callback( $rule1, $rule2 );
	}

	/**
	 * Return an array of resources which can be booked for a defined start/end date
	 *
	 * @deprecated Replaced with wc_bookings_get_block_availability_for_range
	 * @param  string $start_date
	 * @param  string $end_date
	 * @param  string $resource_id
	 * @param  integer $qty being booked
	 * @return bool|WP_ERROR if no blocks available, or int count of bookings that can be made, or array of available resources
	 */
	public function get_available_bookings( $start_date, $end_date, $resource_id = '', $qty = 1 ) {
		return wc_bookings_get_total_available_bookings_for_range( $this, $start_date, $end_date, $resource_id, $qty );
	}

	/**
	 * Get existing bookings in a given date range
	 *
	 * @param integer $start_date
	 * @param integer $end_date
	 * @param int    $resource_id
	 * @return array
	 */
	public function get_bookings_in_date_range( $start_date, $end_date, $resource_id = null ) {
		return WC_Booking_Data_Store::get_bookings_in_date_range( $start_date, $end_date, $this->has_resources() && $resource_id ? $resource_id : $this->get_id() );
	}

	/**
	 * Check a time against the time specific availability rules
	 *
	 * @param  string       $block_start_time timestamp to check
	 * @param  string $block_end_time   timestamp to check
	 * @return bool available or not
	 */
	public function check_availability_rules_against_time( $block_start_time, $block_end_time, $resource_id ) {
		return WC_Product_Booking_Rule_Manager::check_availability_rules_against_time( $block_start_time, $block_end_time, $resource_id, $this );
	}

	/**
	 * Check a date against the availability rules
	 *
	 *
	 * @param  string $check_date date to check
	 *
	 * @return bool available or not
	 */
	public function check_availability_rules_against_date( $check_date, $resource_id ) {
		return WC_Product_Booking_Rule_Manager::check_availability_rules_against_date( $this, $resource_id, $check_date );
	}

	/**
	 * Find available blocks and return HTML for the user to choose a block. Used in class-wc-bookings-ajax.php
	 * @param  array  $blocks
	 * @param  array  $intervals
	 * @param  integer $resource_id
	 * @param  string  $from The starting date for the set of blocks
	 * @return string
	 */
	public function get_available_blocks_html( $blocks, $intervals = array(), $resource_id = 0, $from = '' ) {
		_deprecated_function( __METHOD__, '1.12.2' );

		return wc_bookings_available_blocks_html( $this, $blocks, $intervals, $resource_id, $from );
	}

	/**
	 * Checks booking data is correctly set, and that the chosen blocks are indeed available.
	 *
	 * @since 1.15.0
	 * @param  array $data
	 * @return bool|WP_Error on failure, true on success
	 */
	public function is_bookable( $data ) {
		// Validate resources are set
		if ( $this->has_resources() && $this->is_resource_assignment_type( 'customer' ) ) {
			if ( empty( $data['_resource_id'] ) ) {
				return new WP_Error( 'Error', __( 'Please choose a resource type', 'woocommerce-bookings' ) );
			}
		} elseif ( $this->has_resources() && $this->is_resource_assignment_type( 'automatic' ) ) {
			$data['_resource_id'] = 0;
		} else {
			$data['_resource_id'] = '';
		}

		// Validate customer set durations
		if ( $this->is_duration_type( 'customer' ) ) {
			if ( empty( $data['_duration'] ) ) {
				return new WP_Error( 'Error', __( 'Duration is required - please enter a duration greater than zero', 'woocommerce-bookings' ) );
			}
			if ( $data['_duration'] > $this->get_max_duration() ) {
				/* translators: 1: maximum duration */
				return new WP_Error( 'Error', sprintf( __( 'The maximum duration is %d', 'woocommerce-bookings' ), $this->get_max_duration() ) );
			}
			if ( $data['_duration'] < $this->get_min_duration() ) {
				/* translators: 1: minimum duration */
				return new WP_Error( 'Error', sprintf( __( 'The minimum duration is %d', 'woocommerce-bookings' ), $this->get_min_duration() ) );
			}
		}

		// Validate date and time
		if ( empty( $data['date'] ) ) {
			return new WP_Error( 'Error', __( 'Date is required - please choose one above', 'woocommerce-bookings' ) );
		}
		if ( in_array( $this->get_duration_unit(), array( 'minute', 'hour' ) ) && empty( $data['time'] ) ) {
			return new WP_Error( 'Error', __( 'Time is required - please choose one above', 'woocommerce-bookings' ) );
		}
		if ( $data['_date'] && date( 'Ymd', strtotime( $data['_date'] ) ) < date( 'Ymd', current_time( 'timestamp' ) ) ) {
			return new WP_Error( 'Error', __( 'You must choose a future date and time.', 'woocommerce-bookings' ) );
		}
		if ( $data['_date'] && ! empty( $data['_time'] ) && date( 'YmdHi', strtotime( $data['_date'] . ' ' . $data['_time'] ) ) < date( 'YmdHi', current_time( 'timestamp' ) ) ) {
			return new WP_Error( 'Error', __( 'You must choose a future date and time.', 'woocommerce-bookings' ) );
		}

		// Validate min date and max date
		if ( in_array( $this->get_duration_unit(), array( 'minute', 'hour' ) ) ) {
			$now = current_time( 'timestamp' );
		} elseif ( 'month' === $this->get_duration_unit() ) {
			$now = strtotime( 'midnight first day of this month', current_time( 'timestamp' ) );
		} else {
			$now = strtotime( 'midnight', current_time( 'timestamp' ) );
		}
		$min = $this->get_min_date();

		if ( $min ) {
			$min_date = wc_bookings_get_min_timestamp_for_day( strtotime( $data['_date'] ), $min['value'], $min['unit'] );

			if ( strtotime( $data['_date'] . ' ' . $data['_time'] ) < $min_date ) {
				/* translators: 1: minimum date */
				return new WP_Error( 'Error', sprintf( __( 'The earliest booking possible is currently %s.', 'woocommerce-bookings' ), date_i18n( wc_bookings_date_format() . ' ' . wc_bookings_time_format(), $min_date ) ) );
			}
		}
		$max = $this->get_max_date();

		if ( $max ) {
			$max_date = strtotime( "+{$max['value']} {$max['unit']}", $now );
			if ( strtotime( $data['_date'] . ' ' . $data['_time'] ) > $max_date ) {
				/* translators: 1: maximum date */
				return new WP_Error( 'Error', sprintf( __( 'The latest booking possible is currently %s.', 'woocommerce-bookings' ), date_i18n( wc_bookings_date_format() . ' ' . wc_bookings_time_format(), $max_date ) ) );
			}
		}

		// Check that the day of the week is not restricted.
		if ( $this->has_restricted_days() ) {
			$restricted_days = $this->get_restricted_days();

			if ( ! in_array( date( 'w', $data['_start_date'] ), $restricted_days ) ) {
				return new WP_Error( 'Error', __( 'Sorry, bookings cannot start on this day.', 'woocommerce-bookings' ) );
			}
		}

		// Validate persons
		if ( $this->has_persons() ) {
			$persons = array_sum( $data['_persons'] );

			if ( $this->get_max_persons() && $persons > $this->get_max_persons() ) {
				/* translators: 1: maximum persons */
				return new WP_Error( 'Error', sprintf( __( 'The maximum persons per group is %d', 'woocommerce-bookings' ), $this->get_max_persons() ) );
			}
			if ( $persons < $this->get_min_persons() ) {
				/* translators: 1: minimum persons */
				return new WP_Error( 'Error', sprintf( __( 'The minimum persons per group is %d', 'woocommerce-bookings' ), $this->get_min_persons() ) );
			}

			if ( $this->has_person_types() ) {
				$person_types = $this->get_person_types();
				foreach ( $person_types as $person ) {
					$person_max = $person->get_max();
					if ( is_numeric( $person_max ) && isset( $data['_persons'][ $person->get_id() ] ) && $data['_persons'][ $person->get_id() ] > $person_max ) {
						/* translators: 1: person name 2: maximum persons */
						return new WP_Error( 'Error', sprintf( __( 'The maximum %1$s per group is %2$d', 'woocommerce-bookings' ), $person->post_title, $person_max ) );
					}

					$person_min = $person->get_min();
					if ( is_numeric( $person_min ) && isset( $data['_persons'][ $person->get_id() ] ) && $data['_persons'][ $person->get_id() ] < $person_min ) {
						/* translators: 1: person name 2: minimum persons */
						return new WP_Error( 'Error', sprintf( __( 'The minimum %1$s per group is %2$d', 'woocommerce-bookings' ), $person->post_title, $person_min ) );
					}
				}
			}
		}

		$base_interval = 'hour' === $this->get_duration_unit() ? $this->get_duration() * 60 : $this->get_duration();
		$interval      = $base_interval;
		if ( ! empty( $data['_duration'] ) ) {
			$interval = $base_interval * absint( $data['_duration'] );
		}
		$intervals = array( $interval, $base_interval );

		// Get availability for the dates
		$available_bookings = wc_bookings_get_total_available_bookings_for_range( $this, $data['_start_date'], $data['_end_date'], $data['_resource_id'], $data['_qty'], $intervals );

		if ( is_wp_error( $available_bookings ) ) {
			return $available_bookings;
		} elseif ( ! $available_bookings ) {
			return new WP_Error( 'Error', __( 'Sorry, the selected block is not available', 'woocommerce-bookings' ) );
		}

		return true;
	}

	/**
	 * Compares the provided date with the booking max allowed date and returns
	 * the earliest.
	 *
	 * @since 1.15.6
	 * @param  date $end_date Date to compare.
	 * @return date
	 */
	private function get_max_allowed_date_into_the_future( $end_date ) {
		$product_max_date = $this->get_max_date();

		if ( $product_max_date ) {
			return $end_date;
		}

		$max_end_date     = strtotime( "+{$product_max_date['value']} {$product_max_date['unit']}", current_time( 'timestamp' ) );
		return $end_date > $max_end_date ? $max_end_date : $end_date;
	}

}
