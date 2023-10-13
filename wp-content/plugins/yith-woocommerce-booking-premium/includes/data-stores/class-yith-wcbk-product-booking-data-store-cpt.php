<?php
/**
 * Class YITH_WCBK_Product_Booking_Data_Store_CPT
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * YITH Booking Product Data Store: Stored in CPT.
 *
 * @since  2.1
 */
class YITH_WCBK_Product_Booking_Data_Store_CPT extends WC_Product_Data_Store_CPT implements WC_Object_Data_Store_Interface {
	// phpcs:disable WordPress.Arrays.MultipleStatementAlignment

	/**
	 * Meta keys and how they transfer to CRUD props.
	 *
	 * @var array
	 */
	protected $booking_meta_key_to_props = array(
		// ------ Booking Settings --------------------------------------------------
		'_yith_booking_duration_type'                    => 'duration_type',
		'_yith_booking_duration'                         => 'duration',
		'_yith_booking_duration_unit'                    => 'duration_unit',
		'_yith_booking_enable_calendar_range_picker'     => 'enable_calendar_range_picker',
		'_yith_booking_default_start_date'               => 'default_start_date',
		'_yith_booking_default_start_date_custom'        => 'default_start_date_custom',
		'_yith_booking_default_start_time'               => 'default_start_time',
		'_yith_booking_all_day'                          => 'full_day',
		// ------ Booking Availability --------------------------------------------------
		'_yith_booking_max_per_block'                    => 'max_bookings_per_unit',
		'_yith_booking_minimum_duration'                 => 'minimum_duration',
		'_yith_booking_maximum_duration'                 => 'maximum_duration',
		'_yith_booking_request_confirmation'             => 'confirmation_required',
		'_yith_booking_can_be_cancelled'                 => 'cancellation_available',
		'_yith_booking_cancelled_duration'               => 'cancellation_available_up_to',
		'_yith_booking_cancelled_unit'                   => 'cancellation_available_up_to_unit',
		'_yith_booking_checkin'                          => 'check_in',
		'_yith_booking_checkout'                         => 'check_out',
		'_yith_booking_allowed_start_days'               => 'allowed_start_days',
		'_yith_booking_daily_start_time'                 => 'daily_start_time',
		'_yith_booking_buffer'                           => 'buffer',
		'_yith_booking_time_increment_based_on_duration' => 'time_increment_based_on_duration',
		'_yith_booking_time_increment_including_buffer'  => 'time_increment_including_buffer',
		'_yith_booking_allow_after'                      => 'minimum_advance_reservation',
		'_yith_booking_allow_after_unit'                 => 'minimum_advance_reservation_unit',
		'_yith_booking_allow_until'                      => 'maximum_advance_reservation',
		'_yith_booking_allow_until_unit'                 => 'maximum_advance_reservation_unit',
		'_yith_booking_availability_range'               => 'availability_rules',
		'_yith_booking_default_availabilities'           => 'default_availabilities',
		// ------ Booking Prices --------------------------------------------------
		'_yith_booking_block_cost'                       => 'base_price',
		'_yith_booking_base_cost'                        => 'fixed_base_fee',
		'_yith_booking_costs_range'                      => 'price_rules',
	);

	// phpcs:enable

	/**
	 * Meta keys and how they transfer to CRUD props.
	 *
	 * @var array
	 */
	protected $booking_internal_meta_keys = array(
		'_yith_booking_duration_type',
		'_yith_booking_duration',
		'_yith_booking_duration_unit',
		'_yith_booking_enable_calendar_range_picker',
		'_yith_booking_default_start_date',
		'_yith_booking_default_start_date_custom',
		'_yith_booking_default_start_time',
		'_yith_booking_all_day',
		'_yith_booking_max_per_block',
		'_yith_booking_minimum_duration',
		'_yith_booking_maximum_duration',
		'_yith_booking_request_confirmation',
		'_yith_booking_can_be_cancelled',
		'_yith_booking_cancelled_duration',
		'_yith_booking_cancelled_unit',
		'_yith_booking_checkin',
		'_yith_booking_checkout',
		'_yith_booking_allowed_start_days',
		'_yith_booking_daily_start_time',
		'_yith_booking_buffer',
		'_yith_booking_time_increment_based_on_duration',
		'_yith_booking_time_increment_including_buffer',
		'_yith_booking_allow_after',
		'_yith_booking_allow_after_unit',
		'_yith_booking_allow_until',
		'_yith_booking_allow_until_unit',
		'_yith_booking_availability_range',
		'_yith_booking_default_availabilities',
		'_yith_booking_block_cost',
		'_yith_booking_base_cost',
		'_yith_booking_costs_range',
	);

	/**
	 * YITH_WCBK_Product_Booking_Data_Store_CPT constructor.
	 */
	public function __construct() {
		if ( is_callable( array( parent::class, '__construct' ) ) ) {
			parent::__construct();
		}

		$this->booking_internal_meta_keys = apply_filters( 'yith_wcbk_product_data_store_internal_meta_keys', $this->booking_internal_meta_keys, $this );

		$this->internal_meta_keys = array_merge( $this->internal_meta_keys, array_keys( $this->booking_internal_meta_keys ) );
	}

	/**
	 * Force meta values on save.
	 *
	 * @param WC_Product_Booking $product The booking product.
	 */
	protected function force_meta_values( &$product ) {
		$product->set_regular_price( '' );
		$product->set_sale_price( '' );
		$product->set_manage_stock( false );
		$product->set_stock_status( 'instock' );

		$this->sync_booking_price( $product );
	}

	/**
	 * Method to create a new product in the database.
	 *
	 * @param WC_Product_Booking $product The booking product.
	 */
	public function create( &$product ) {
		parent::create( $product );
		$this->force_meta_values( $product );
	}

	/**
	 * Method to update a product in the database.
	 *
	 * @param WC_Product_Booking $product The booking product.
	 */
	public function update( &$product ) {
		parent::update( $product );
		$this->force_meta_values( $product );
	}

	/**
	 * Helper method that updates all the post meta for a product based on it's settings in the WC_Product class.
	 *
	 * @param WC_Product $product The booking product.
	 * @param bool       $force   Force all props to be written even if not changed. This is used during creation.
	 *
	 * @since 3.0.0
	 */
	public function update_post_meta( &$product, $force = false ) {
		parent::update_post_meta( $product, $force );

		$props_to_update = $force ? $this->get_booking_meta_key_to_props() : $this->get_props_to_update( $product, $this->get_booking_meta_key_to_props() );

		foreach ( $props_to_update as $meta_key => $prop ) {
			if ( is_callable( array( $product, "get_$prop" ) ) ) {
				$value = $product->{"get_$prop"}( 'edit' );

				switch ( $prop ) {
					case 'enable_calendar_range_picker':
					case 'full_day':
					case 'confirmation_required':
					case 'cancellation_available':
					case 'time_increment_based_on_duration':
					case 'time_increment_including_buffer':
						$value = wc_bool_to_string( $value );
						break;
					case 'availability_rules':
					case 'price_rules':
					case 'default_availabilities':
						$value = yith_wcbk_simple_objects_to_array( $value );
						break;
				}

				$updated = update_post_meta( $product->get_id(), $meta_key, $value );

				if ( $updated ) {
					$this->updated_props[] = $prop;
				}
			}
		}

		/**
		 * This filter allows third-party plugins (and plugin modules) to update custom props.
		 * Important: you MUST add the props you updated to the first param.
		 */
		$extra_updated_props = apply_filters( 'yith_wcbk_product_data_store_update_props', array(), $product, $force, $this );
		if ( $extra_updated_props ) {
			$this->updated_props = array_merge( $this->updated_props, $extra_updated_props );
		}
	}

	/**
	 * Read product data. Can be overridden by child classes to load other props.
	 *
	 * @param WC_Product $product The booking product.
	 */
	public function read_product_data( &$product ) {
		parent::read_product_data( $product );

		// Convert "multiply costs by persons" in two different fields.
		if ( metadata_exists( 'post', $product->get_id(), '_yith_booking_multiply_costs_by_persons' ) ) {
			$value = get_post_meta( $product->get_id(), '_yith_booking_multiply_costs_by_persons', true );
			update_post_meta( $product->get_id(), '_yith_booking_multiply_base_price_by_number_of_people', $value );
			update_post_meta( $product->get_id(), '_yith_booking_multiply_fixed_base_fee_by_number_of_people', $value );
			delete_post_meta( $product->get_id(), '_yith_booking_multiply_costs_by_persons' );
		}

		$post_meta_values = get_post_meta( $product->get_id() );
		$props_to_set     = array();

		foreach ( $this->get_booking_meta_key_to_props() as $meta_key => $prop ) {
			$meta_value            = $post_meta_values[ $meta_key ][0] ?? null;
			$props_to_set[ $prop ] = maybe_unserialize( $meta_value ); // get_post_meta only unserializes single values.
		}

		$product->set_props( $props_to_set );

		do_action( 'yith_wcbk_product_data_store_read_data', $product, $this );
	}

	/**
	 * Handle updated meta props after updating meta data.
	 *
	 * @param WC_Product_Booking $product Product Object.
	 *
	 * @since 3.0.0
	 */
	protected function handle_updated_props( &$product ) {
		if ( in_array( 'duration_type', $this->updated_props, true ) && 'fixed' === $product->get_duration_type() ) {
			update_post_meta( $product->get_id(), '_yith_booking_maximum_duration', 1 );
			$product->set_maximum_duration( 1 );
		}

		do_action( 'yith_wcbk_product_data_store_updated_props', $product, $this->updated_props );

		parent::handle_updated_props( $product );
	}

	/**
	 * Check if a prop is boolean.
	 *
	 * @param string $prop The property.
	 *
	 * @return bool
	 * @deprecated 4.0.0
	 */
	public function is_boolean_prop( $prop ) {
		yith_wcbk_deprecated_function( 'YITH_WCBK_Product_Booking_Data_Store_CPT::is_boolean_prop', '4.0.0' );

		$boolean_props = array(
			'enable_calendar_range_picker',
			'full_day',
			'confirmation_required',
			'cancellation_available',
			'time_increment_based_on_duration',
			'time_increment_including_buffer',
			'multiply_base_price_by_number_of_people',
			'multiply_fixed_base_fee_by_number_of_people',
			'enable_people',
			'count_people_as_separate_bookings',
			'enable_people_types',
			'enable_resources',
			'resource_is_required',
		);

		return in_array( $prop, $boolean_props, true );
	}

	/**
	 * Check if a prop is an array.
	 *
	 * @param string $prop The property.
	 *
	 * @return bool
	 * @deprecated 4.0.0
	 */
	public function is_array_prop( $prop ) {
		yith_wcbk_deprecated_function( 'YITH_WCBK_Product_Booking_Data_Store_CPT::is_array_prop', '4.0.0' );

		$array_props = array(
			'allowed_start_days',
			'availability_rules',
			'people_types',
			'price_rules',
			'external_calendars',
			'extra_costs',
			'default_availabilities',
		);

		return in_array( $prop, $array_props, true );
	}

	/**
	 * Check if a prop is an array of simple objects
	 *
	 * @param string $prop The property.
	 *
	 * @return bool
	 * @deprecated 4.0.0
	 */
	public function is_simple_object_array_prop( $prop ) {
		yith_wcbk_deprecated_function( 'YITH_WCBK_Product_Booking_Data_Store_CPT::is_simple_object_array_prop', '4.0.0' );
		$simple_object_array_props = array(
			'availability_rules',
			'price_rules',
			'extra_costs',
			'default_availabilities',
		);

		return in_array( $prop, $simple_object_array_props, true );
	}

	/**
	 * Update the last sync for external calendars
	 *
	 * @param WC_Product_Booking $product   The booking product.
	 * @param int|null           $last_sync The last sync timestamp. Set null for current timestamp.
	 *
	 * @return bool|int
	 */
	public function update_external_calendars_last_sync( $product, $last_sync = null ) {
		$last_sync = ! is_null( $last_sync ) ? $last_sync : time();

		if ( $last_sync ) {
			$success = update_post_meta( $product->get_id(), '_yith_booking_external_calendars_last_sync', $last_sync );
		} else {
			$success = delete_post_meta( $product->get_id(), '_yith_booking_external_calendars_last_sync' );
		}

		return ! ! $success ? $last_sync : false;
	}

	/**
	 * Sync Booking product price
	 *
	 * @param int|WC_Product_Booking $product The booking product.
	 *
	 * @return bool
	 */
	public function sync_booking_price( $product ) {
		$product = wc_get_product( $product );
		if ( $product && $product->is_type( 'booking' ) ) {
			/**
			 * The Booking product
			 *
			 * @var WC_Product_Booking $product
			 */
			do_action( 'yith_wcbk_product_sync_price_before', $product );
			delete_post_meta( $product->get_id(), '_price' );
			$price = $product->get_price_to_store();
			if ( $price ) {
				update_post_meta( $product->get_id(), '_price', $price );
			}

			if ( is_callable( array( $this, 'update_lookup_table' ) ) ) {
				$this->update_lookup_table( $product->get_id(), 'wc_product_meta_lookup' );
			}

			yith_wcbk_maybe_debug( sprintf( 'Sync Product Price #%s', $product->get_id() ) );
			do_action( 'yith_wcbk_product_sync_price_after', $product );
		}

		return false;
	}

	/**
	 * Get booking meta key to props.
	 *
	 * @return array
	 */
	public function get_booking_meta_key_to_props() {
		return $this->booking_meta_key_to_props;
	}
}
