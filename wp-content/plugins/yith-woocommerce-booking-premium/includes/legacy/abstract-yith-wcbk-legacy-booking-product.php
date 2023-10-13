<?php
/**
 * Class YITH_WCBK_Legacy_Booking_Product
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;


if ( ! class_exists( 'YITH_WCBK_Legacy_Booking_Product' ) ) {
	/**
	 * Class YITH_WCBK_Legacy_Booking_Product
	 */
	abstract class YITH_WCBK_Legacy_Booking_Product extends WC_Product {

		/**
		 * Legacy prop name map.
		 *
		 * @var string[]
		 */
		protected $legacy_prop_name_map = array(
			'all_day'                          => 'full_day',
			'max_per_block'                    => 'max_bookings_per_unit',
			'request_confirmation'             => 'confirmation_required',
			'can_be_cancelled'                 => 'cancellation_available',
			'cancelled_duration'               => 'cancellation_available_up_to',
			'cancelled_unit'                   => 'cancellation_available_up_to_unit',
			'checkin'                          => 'check_in',
			'checkout'                         => 'check_out',
			'time_increment_based_on_duration' => 'time_increment_based_on_duration',
			'time_increment_including_buffer'  => 'time_increment_including_buffer',
			'allow_after'                      => 'minimum_advance_reservation',
			'allow_after_unit'                 => 'minimum_advance_reservation_unit',
			'allow_until'                      => 'maximum_advance_reservation',
			'allow_until_unit'                 => 'maximum_advance_reservation_unit',
			'availability_range'               => 'availability_rules',
			'block_cost'                       => 'base_price',
			'multiply_costs_by_persons'        => 'multiply_base_price_by_number_of_people',
			'base_cost'                        => 'fixed_base_fee',
			'costs_range'                      => 'price_rules',
			'has_persons'                      => 'enable_people',
			'min_persons'                      => 'minimum_number_of_people',
			'max_persons'                      => 'maximum_number_of_people',
			'count_persons_as_bookings'        => 'count_people_as_separate_bookings',
			'enable_person_types'              => 'enable_people_types',
			'person_types'                     => 'people_types',
			'location_lat'                     => 'location_latitude',
			'location_lng'                     => 'location_longitude',
		);

		/**
		 * Retrieve the correct prop name (for backward compatibility)
		 *
		 * @param string $prop_name Prop name.
		 *
		 * @return string
		 */
		private function get_legacy_prop_name( $prop_name ) {
			return array_key_exists( $prop_name, $this->legacy_prop_name_map ) ? $this->legacy_prop_name_map[ $prop_name ] : $prop_name;
		}

		/**
		 * Get a booking property
		 *
		 * @param string $prop_name  Prop name.
		 * @param string $deprecated Deprecated.
		 *
		 * @return mixed
		 * @since      1.1.0
		 * @deprecated since 2.1 | use the CRUD methods instead
		 */
		public function get_booking_prop( $prop_name, $deprecated = '' ) {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::get_booking_prop', '2.1' );

			yith_wcbk_deprecated_filter( 'yith_wcbk_booking_product_get_booking_prop', '2.1', null, 'Use specific hooks in CRUD methods' );
			$value = apply_filters( 'yith_wcbk_booking_product_get_booking_prop', null, $prop_name, $this );

			if ( is_null( $value ) ) {
				$prop_name = $this->get_legacy_prop_name( $prop_name );
				if ( is_callable( array( $this, "get_{$prop_name}" ) ) ) {
					$value = $this->{"get_$prop_name"}();
				}
			}

			return $value;
		}

		/**
		 * Set a booking property
		 *
		 * @param string $prop_name Prop name.
		 * @param mixed  $value     The value.
		 * @param bool   $save      Save flag.
		 *
		 * @since      1.1.0
		 * @deprecated since 2.1 | use the CRUD methods instead
		 */
		public function set_booking_prop( $prop_name, $value, $save = false ) {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::set_booking_prop', '2.1' );

			$prop_name = $this->get_legacy_prop_name( $prop_name );
			if ( is_callable( array( $this, "set_{$prop_name}" ) ) ) {
				$this->{"set_$prop_name"}( $value );
			}
			if ( $save ) {
				$this->save();
			}
		}

		/**
		 * Save booking properties
		 *
		 * @since      2.0.0
		 * @deprecated since 2.1 | use WC_Product::save()
		 */
		public function save_booking_props() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::save_booking_props', '2.1', 'WC_Product::save' );

			$this->save();
		}

		/**
		 * Get the availability range array
		 *
		 * @return YITH_WCBK_Availability_Rule[]
		 * @deprecated since 2.1 | WC_Product_Booking::use get_availability_rules() instead
		 */
		public function get_availability_ranges() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::get_availability_ranges', '2.1', 'WC_Product_Booking::get_availability_rules' );

			return $this->get_availability_rules();
		}

		/**
		 * Get the costs range array
		 *
		 * @return array
		 * @deprecated since 2.1 | use WC_Product_Booking::get_price_rules() instead
		 */
		public function get_costs_ranges() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::get_costs_ranges', '2.1', 'WC_Product_Booking::get_price_rules' );

			return $this->get_price_rules();
		}

		/**
		 * Returns the product's base cost,
		 * calculated by (base cost + booking cost) * persons .
		 *
		 * @return string price
		 */
		public function get_base_cost() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::get_base_cost', '2.1' );

			return '';
		}

		/**
		 * Check for the correct external calendars sync key
		 *
		 * @param string $key The key.
		 *
		 * @return bool
		 * @since      2.0.0
		 * @deprecated since 2.1 | Use WC_Product_Booking::is_valid_external_calendars_key instead
		 */
		public function check_external_calendars_key( $key ) {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::check_external_calendars_key', '2.1', 'WC_Product_Booking::is_valid_external_calendars_key' );

			return $this->is_valid_external_calendars_key( $key );
		}

		/**
		 * Get the maximum duration
		 *
		 * @return int
		 * @deprecated since 2.1 | Use WC_Product_Booking::get_maximum_duration instead
		 */
		public function get_max_duration() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::get_max_duration', '2.1', 'WC_Product_Booking::get_maximum_duration' );

			return $this->get_maximum_duration();
		}

		/**
		 * Get the maximum duration time object
		 *
		 * @return object
		 * @deprecated since 2.1 | use CRUD functions instead
		 */
		public function get_max_duration_time() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::get_max_duration_time', '2.1' );

			return (object) array(
				'duration' => $this->get_maximum_duration() * $this->get_duration(),
				'unit'     => $this->get_duration_unit(),
			);
		}

		/**
		 * Get the minimum duration
		 *
		 * @return int
		 * @deprecated since 2.1 | Use WC_Product_Booking::get_minimum_duration instead
		 */
		public function get_min_duration() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::get_max_duration', '2.1', 'WC_Product_Booking::get_minimum_duration' );

			return $this->get_minimum_duration();
		}

		/**
		 * Get the minimum duration time object
		 *
		 * @return object
		 * @deprecated since 2.1 | use CRUD functions instead
		 */
		public function get_min_duration_time() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::get_min_duration_time', '2.1' );

			return (object) array(
				'duration' => $this->get_minimum_duration() * $this->get_duration(),
				'unit'     => $this->get_duration_unit(),
			);
		}

		/**
		 * Get the minimum number of persons
		 *
		 * @return int
		 * @deprecated since 2.1 | Use WC_Product_Booking::get_minimum_number_of_people
		 */
		public function get_min_persons() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::get_min_persons', '2.1', 'WC_Product_Booking::get_minimum_number_of_people' );

			return $this->get_minimum_number_of_people();
		}

		/**
		 * Get the max number of persons
		 *
		 * @return int
		 * @since      2.0.8
		 * @deprecated since 2.1 | Use WC_Product_Booking::get_maximum_number_of_people
		 */
		public function get_max_persons() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::get_max_persons', '2.1', 'WC_Product_Booking::get_maximum_number_of_people' );

			return $this->get_maximum_number_of_people();
		}

		/**
		 * Checks if a product has multiply costs by persons enabled.
		 *
		 * @return bool
		 * @deprecated since 2.1 | Use WC_Product_Booking::has_multiply_base_price_by_number_of_people and WC_Product_Booking::has_multiply_fixed_base_fee_by_number_of_people instead
		 */
		public function has_multiply_costs_by_persons_enabled() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::has_multiply_costs_by_persons_enabled', '2.1' );

			return $this->has_multiply_base_price_by_number_of_people();
		}

		/**
		 * Checks if a product has count persons as bookings enabled.
		 *
		 * @return bool
		 * @deprecated since 2.1 | Use WC_Product_Booking::has_count_people_as_separate_bookings_enabled instead
		 */
		public function has_count_persons_as_bookings_enabled() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::has_count_persons_as_bookings_enabled', '2.1', 'WC_Product_Booking::has_count_people_as_separate_bookings_enabled' );

			return $this->has_count_people_as_separate_bookings_enabled();
		}

		/**
		 * Check if has persons enabled.
		 *
		 * @return boolean
		 * @deprecated since 2.1 | Use WC_Product_Booking::has_people instead
		 */
		public function has_persons() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::has_persons', '2.1', 'WC_Product_Booking::has_people' );

			return $this->has_people();
		}

		/**
		 * Check if has person types enabled.
		 *
		 * @return boolean
		 * @deprecated since 2.1 | Use WC_Product_Booking:has_people_types_enabled instead
		 */
		public function has_person_types() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::has_person_types', '2.1', 'WC_Product_Booking::has_people_types_enabled' );

			return $this->has_people_types_enabled();
		}

		/**
		 * Get the enabled person types
		 *
		 * @return array
		 * @deprecated since 2.1 | Use WC_Product_Booking::get_enabled_people_types instead
		 */
		public function get_person_types() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::get_person_types', '2.1', 'WC_Product_Booking::get_enabled_people_types' );

			return $this->get_enabled_people_types();
		}

		/**
		 * Get the services
		 *
		 * @param array $args Arguments passed to wp_get_object_terms.
		 *
		 * @return array
		 * @deprecated since 2.1 | Use WC_Product_Booking::get_service_ids instead
		 */
		public function get_services( $args = array() ) {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::get_services', '2.1', 'WC_Product_Booking::get_service_ids' );

			return $this->get_service_ids();
		}

		/**
		 * Get the max bookings per unit
		 *
		 * @return int
		 * @deprecated since 2.1 | Use WC_Product_Booking::get_max_bookings_per_unit instead
		 */
		public function get_max_per_block() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::get_max_per_block', '2.1', 'WC_Product_Booking::get_max_bookings_per_unit' );

			return $this->get_max_bookings_per_unit();
		}

		/**
		 * Get allow after
		 *
		 * @return int
		 * @since      2.0.0
		 * @deprecated since 2.1 | Use WC_Product_Booking::get_minimum_advance_reservation instead
		 */
		public function get_allow_after() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::get_allow_after', '2.1', 'WC_Product_Booking::get_minimum_advance_reservation' );

			return $this->get_minimum_advance_reservation();
		}

		/**
		 * Get allow after unit
		 *
		 * @return string
		 * @since      2.0.0
		 * @deprecated since 2.1 | Use WC_Product_Booking::get_minimum_advance_reservation_unit instead
		 */
		public function get_allow_after_unit() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::get_allow_after_unit', '2.1', 'WC_Product_Booking::get_minimum_advance_reservation_unit' );

			return $this->get_minimum_advance_reservation_unit();
		}

		/**
		 * Get allow until
		 *
		 * @return int
		 * @since      2.0.0
		 * @deprecated since 2.1 | Use WC_Product_Booking::get_maximum_advance_reservation instead
		 */
		public function get_allow_until() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::get_allow_until', '2.1', 'WC_Product_Booking::get_maximum_advance_reservation' );

			return $this->get_maximum_advance_reservation();
		}

		/**
		 * Get allow until unit
		 *
		 * @return string
		 * @since      2.0.0
		 * @deprecated since 2.1 | Use WC_Product_Booking::get_maximum_advance_reservation_unit instead
		 */
		public function get_allow_until_unit() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::get_allow_until_unit', '2.1', 'WC_Product_Booking::get_maximum_advance_reservation_unit' );

			return $this->get_maximum_advance_reservation_unit();
		}

		/**
		 * Check if Admin has to confirm before purchase booking
		 *
		 * @return boolean
		 * @deprecated since 2.1 | Use WC_Product_Booking::is_confirmation_required instead
		 */
		public function is_requested_confirmation() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::is_requested_confirmation', '2.1', 'WC_Product_Booking::is_confirmation_required' );

			return $this->is_confirmation_required();
		}


		/**
		 * Check if the booking is all day
		 *
		 * @return boolean
		 * @deprecated since 2.1 | Use WC_Product_Booking::is_full_day instead
		 */
		public function is_all_day() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::is_all_day', '2.1', 'WC_Product_Booking::is_full_day' );

			return $this->is_full_day();
		}

		/**
		 * Sync product price for sorting
		 *
		 * @since      2.0.5
		 * @deprecated since 2.1 | use yith_wcbk_product_price_sync function instead
		 */
		public function sync_price() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::sync_price', '2.1', 'yith_wcbk_product_price_sync' );

			yith_wcbk_product_price_sync( $this );
		}

		/**
		 * Return the service cost in base of duration(number of blocks), person type and person number
		 *
		 * @param int   $duration                   Duration.
		 * @param int   $person_type_id             Person type ID.
		 * @param int   $person_number              Number.
		 * @param array $optional_services_selected Optional services selected.
		 *
		 * @return int|string
		 * @deprecated since 2.1
		 */
		public function calculate_service_cost( $duration, $person_type_id, $person_number, $optional_services_selected = array() ) {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::calculate_service_cost', '2.1' );

			return '';
		}

		/**
		 * Delete the external calendars sync expiration
		 *
		 * @since      2.0
		 * @deprecated since 2.1 | Use yith_wcbk_product_delete_external_calendars_last_sync instead
		 */
		public function delete_external_calendars_last_sync() {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::delete_external_calendars_last_sync', '2.1', 'yith_wcbk_product_delete_external_calendars_last_sync' );

			yith_wcbk_product_delete_external_calendars_last_sync( $this );
		}

		/**
		 * Get non available dates
		 *
		 * @param int    $from_year              From year.
		 * @param int    $from_month             From month.
		 * @param int    $to_year                To year.
		 * @param int    $to_month               To month.
		 * @param string $range                  Range.
		 * @param bool   $exclude_booked         Exclude booked flag.
		 * @param bool   $check_start_date       Check start date flag.
		 * @param bool   $check_min_max_duration Check min-max duration flag.
		 *
		 * @return array
		 * @deprecated 4.0.0 | use WC_Product_Booking::get_non_available_dates instead.
		 */
		public function get_not_available_dates( $from_year, $from_month, $to_year, $to_month, $range = 'day', $exclude_booked = false, $check_start_date = false, $check_min_max_duration = true ) {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::get_not_available_dates', '4.0.0', 'WC_Product_Booking::get_non_available_dates' );

			$args = compact( 'range', 'exclude_booked', 'check_start_date', 'check_min_max_duration' );

			return $this->get_non_available_dates( $from_year, $from_month, $to_year, $to_month, $args );
		}

		/**
		 * Create availability calendar
		 *
		 * @param int    $from_year              From year.
		 * @param int    $from_month             From month.
		 * @param int    $to_year                To year.
		 * @param int    $to_month               To month.
		 * @param string $return                 Return.
		 * @param string $range                  Range.
		 * @param bool   $exclude_booked         Exclude booked flag.
		 * @param bool   $check_start_date       Check start date flag.
		 * @param bool   $check_min_max_duration Check min-max duration flag.
		 *
		 * @return array
		 * @deprecated 4.0.0
		 */
		public function create_availability_calendar( $from_year, $from_month, $to_year, $to_month, $return = 'all', $range = 'day', $exclude_booked = false, $check_start_date = true, $check_min_max_duration = true ) {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::create_availability_calendar', '4.0.0' );

			$args = compact( 'return', 'range', 'exclude_booked', 'check_start_date', 'check_min_max_duration' );

			return $this->generate_availability_calendar( $from_year, $from_month, $to_year, $to_month, $args );
		}

		/**
		 * Create availability month calendar.
		 *
		 * @param int    $year                   Year.
		 * @param int    $month                  Month.
		 * @param string $return                 Return.
		 * @param string $range                  Range.
		 * @param bool   $exclude_booked         Exclude booked flag.
		 * @param bool   $check_start_date       Check start date flag.
		 * @param bool   $check_min_max_duration Check min-max duration flag.
		 *
		 * @return array
		 * @deprecated 4.0.0
		 */
		public function create_availability_month_calendar( $year = 0, $month = 0, $return = 'all', $range = 'day', $exclude_booked = false, $check_start_date = true, $check_min_max_duration = true ) {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::create_availability_month_calendar', '4.0.0' );

			$args = compact( 'return', 'range', 'exclude_booked', 'check_start_date', 'check_min_max_duration' );

			return $this->generate_availability_month_calendar( $year, $month, $args );
		}

		/**
		 * Create availability year calendar
		 *
		 * @param int    $year                   Year.
		 * @param int    $from_month             From month.
		 * @param int    $to_month               To month.
		 * @param string $return                 Return.
		 * @param string $range                  Range.
		 * @param bool   $exclude_booked         Exclude booked flag.
		 * @param bool   $check_start_date       Check start date flag.
		 * @param bool   $check_min_max_duration Check min-max duration flag.
		 *
		 * @return array
		 * @deprecated 4.0.0
		 */
		public function create_availability_year_calendar( $year = 0, $from_month = 1, $to_month = 12, $return = 'all', $range = 'day', $exclude_booked = false, $check_start_date = true, $check_min_max_duration = true ) {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::create_availability_year_calendar', '4.0.0' );

			$args = compact( 'return', 'range', 'exclude_booked', 'check_start_date', 'check_min_max_duration' );

			return $this->generate_availability_year_calendar( $year, $from_month, $to_month, $args );
		}

		/**
		 * Calculate the total service costs
		 *
		 * @param array $args Arguments.
		 *
		 * @return float
		 * @deprecated 4.0.0
		 */
		public function calculate_service_costs( $args = array() ) {
			yith_wcbk_deprecated_function( 'WC_Product_Booking::calculate_service_costs', '4.0.0' );

			return 0;
		}
	}
}
