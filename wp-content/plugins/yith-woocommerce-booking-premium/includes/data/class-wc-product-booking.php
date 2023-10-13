<?php
/**
 * Class WC_Product_Booking
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Legacy product contains all deprecated methods for this class and can be removed in the future.
 */
require_once YITH_WCBK_DIR . 'includes/legacy/abstract-yith-wcbk-legacy-booking-product.php';

if ( ! class_exists( 'WC_Product_Booking' ) ) {
	/**
	 * Class WC_Product_Booking
	 * the Booking Product
	 */
	class WC_Product_Booking extends YITH_WCBK_Legacy_Booking_Product {

		/**
		 * The availability handler.
		 *
		 * @var YITH_WCBK_Product_Availability_Handler
		 */
		protected $availability_handler;

		/**
		 * Booking data defaults.
		 *
		 * @var array
		 */
		protected $booking_data_defaults = array(
			'duration_type'                               => 'customer',
			'duration'                                    => 1,
			'duration_unit'                               => 'day',
			'enable_calendar_range_picker'                => false,
			'default_start_date'                          => '',
			'default_start_date_custom'                   => '',
			'default_start_time'                          => '',
			'full_day'                                    => false,
			'location'                                    => '',
			'location_latitude'                           => '',
			'location_longitude'                          => '',
			'max_bookings_per_unit'                       => 1,
			'minimum_duration'                            => 1,
			'maximum_duration'                            => 0,
			'confirmation_required'                       => false,
			'cancellation_available'                      => false,
			'cancellation_available_up_to'                => 0,
			'cancellation_available_up_to_unit'           => 'day',
			'check_in'                                    => '',
			'check_out'                                   => '',
			'allowed_start_days'                          => array(),
			'daily_start_time'                            => '00:00',
			'buffer'                                      => 0,
			'time_increment_based_on_duration'            => false,
			'time_increment_including_buffer'             => false,
			'minimum_advance_reservation'                 => 0,
			'minimum_advance_reservation_unit'            => 'day',
			'maximum_advance_reservation'                 => 1,
			'maximum_advance_reservation_unit'            => 'year',
			'availability_rules'                          => array(),
			'base_price'                                  => '',
			'multiply_base_price_by_number_of_people'     => false,
			'extra_price_per_person'                      => '',
			'extra_price_per_person_greater_than'         => 0,
			'weekly_discount'                             => 0,
			'monthly_discount'                            => 0,
			'last_minute_discount'                        => 0,
			'last_minute_discount_days_before_arrival'    => 0,
			'fixed_base_fee'                              => '',
			'multiply_fixed_base_fee_by_number_of_people' => false,
			'price_rules'                                 => array(),
			'enable_people'                               => false,
			'minimum_number_of_people'                    => 1,
			'maximum_number_of_people'                    => 0,
			'count_people_as_separate_bookings'           => false,
			'enable_people_types'                         => false,
			'people_types'                                => array(),
			'service_ids'                                 => array(),
			'external_calendars'                          => array(),
			'external_calendars_key'                      => '',
			'external_calendars_last_sync'                => 0,
			'extra_costs'                                 => array(),
			'default_availabilities'                      => array(),
			'enable_resources'                            => false,
			'resource_assignment'                         => 'customer-select-one',
			'resources_layout'                            => 'default',
			'resources_label'                             => '',
			'resources_field_label'                       => '',
			'resources_field_placeholder'                 => '',
			'resource_is_required'                        => true,
			'resources_data'                              => array(),
		);


		/**
		 * Merges booking product data into the parent object.
		 *
		 * @param int|WC_Product|object $product Product to init.
		 */
		public function __construct( $product = 0 ) {
			$this->data = array_merge( $this->data, $this->booking_data_defaults );
			parent::__construct( $product );

			$this->availability_handler = new YITH_WCBK_Product_Availability_Handler();
		}

		/**
		 * Get internal type.
		 *
		 * @return string
		 */
		public function get_type() {
			return 'booking';
		}

		/**
		 * Returns true if the product has additional options that need to be selected before adding to cart.
		 * Useful, for example, to avoid direct add-to-cart for bookable products
		 * when using WooCommerce 'All Products' Gutenberg block.
		 *
		 * @return boolean
		 * @since  3.0.0
		 */
		public function has_options() {
			return true;
		}

		/*
		|--------------------------------------------------------------------------
		| Getters
		|--------------------------------------------------------------------------
		|
		| Methods for getting data from the product object.
		*/

		/**
		 * Get product duration type.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_duration_type( $context = 'view' ) {
			return $this->get_prop( 'duration_type', $context );
		}

		/**
		 * Get product duration.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_duration( $context = 'view' ) {
			return $this->get_prop( 'duration', $context );
		}

		/**
		 * Get product duration unit.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_duration_unit( $context = 'view' ) {
			return $this->get_prop( 'duration_unit', $context );
		}

		/**
		 * Get enable calendar range picker
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return boolean
		 * @since 2.1
		 */
		public function get_enable_calendar_range_picker( $context = 'view' ) {
			return $this->get_prop( 'enable_calendar_range_picker', $context );
		}

		/**
		 * Get default start date
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_default_start_date( $context = 'view' ) {
			return $this->get_prop( 'default_start_date', $context );
		}

		/**
		 * Get default start date custom
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_default_start_date_custom( $context = 'view' ) {
			return $this->get_prop( 'default_start_date_custom', $context );
		}

		/**
		 * Get default start time
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_default_start_time( $context = 'view' ) {
			return $this->get_prop( 'default_start_time', $context );
		}

		/**
		 * Get full day
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return boolean
		 * @since 2.1
		 */
		public function get_full_day( $context = 'view' ) {
			return $this->get_prop( 'full_day', $context );
		}

		/**
		 * Get location
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_location( $context = 'view' ) {
			return $this->get_prop( 'location', $context );
		}

		/**
		 * Get location latitude
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_location_latitude( $context = 'view' ) {
			return $this->get_prop( 'location_latitude', $context );
		}

		/**
		 * Get location longitude
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_location_longitude( $context = 'view' ) {
			return $this->get_prop( 'location_longitude', $context );
		}

		/**
		 * Get max bookings per unit
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_max_bookings_per_unit( $context = 'view' ) {
			return $this->get_prop( 'max_bookings_per_unit', $context );
		}

		/**
		 * Get minimum duration
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_minimum_duration( $context = 'view' ) {
			return 'view' === $context && $this->is_type_fixed_blocks() ? 1 : $this->get_prop( 'minimum_duration', $context );
		}

		/**
		 * Get maximum duration
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_maximum_duration( $context = 'view' ) {
			return 'view' === $context && $this->is_type_fixed_blocks() ? 1 : $this->get_prop( 'maximum_duration', $context );
		}

		/**
		 * Get confirmation required
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return boolean
		 * @since 2.1
		 */
		public function get_confirmation_required( $context = 'view' ) {
			return $this->get_prop( 'confirmation_required', $context );
		}

		/**
		 * Get cancellation available
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return boolean
		 * @since 2.1
		 */
		public function get_cancellation_available( $context = 'view' ) {
			return $this->get_prop( 'cancellation_available', $context );
		}

		/**
		 * Get cancellation available up to
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_cancellation_available_up_to( $context = 'view' ) {
			return $this->get_prop( 'cancellation_available_up_to', $context );
		}

		/**
		 * Get cancellation available up to unit
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_cancellation_available_up_to_unit( $context = 'view' ) {
			return $this->get_prop( 'cancellation_available_up_to_unit', $context );
		}

		/**
		 * Get check-in
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_check_in( $context = 'view' ) {
			return $this->get_prop( 'check_in', $context );
		}

		/**
		 * Get check-out
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_check_out( $context = 'view' ) {
			return $this->get_prop( 'check_out', $context );
		}

		/**
		 * Get allowed start days
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int[]
		 * @since 2.1
		 */
		public function get_allowed_start_days( $context = 'view' ) {
			return $this->get_prop( 'allowed_start_days', $context );
		}

		/**
		 * Get daily start time
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since      2.1
		 * @todo       To remove, since this field is no longer available since 3.0.0 (see WC_Product_Booking::get_daily_time_slot_ranges).
		 * @deprecated 3.0.0 | use get_daily_time_slots instead.
		 */
		public function get_daily_start_time( $context = 'view' ) {
			return $this->get_prop( 'daily_start_time', $context );
		}

		/**
		 * Get buffer
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_buffer( $context = 'view' ) {
			return $this->get_prop( 'buffer', $context );
		}

		/**
		 * Get time increment based on duration
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return boolean
		 * @since 2.1
		 */
		public function get_time_increment_based_on_duration( $context = 'view' ) {
			return $this->get_prop( 'time_increment_based_on_duration', $context );
		}

		/**
		 * Get time increment including buffer
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return boolean
		 * @since 2.1
		 */
		public function get_time_increment_including_buffer( $context = 'view' ) {
			return $this->get_prop( 'time_increment_including_buffer', $context );
		}

		/**
		 * Get minimum advance reservation
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_minimum_advance_reservation( $context = 'view' ) {
			return $this->get_prop( 'minimum_advance_reservation', $context );
		}

		/**
		 * Get minimum advance reservation unit
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_minimum_advance_reservation_unit( $context = 'view' ) {
			return $this->get_prop( 'minimum_advance_reservation_unit', $context );
		}

		/**
		 * Get maximum advance reservation
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_maximum_advance_reservation( $context = 'view' ) {
			return $this->get_prop( 'maximum_advance_reservation', $context );
		}

		/**
		 * Get maximum advance reservation unit
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_maximum_advance_reservation_unit( $context = 'view' ) {
			return $this->get_prop( 'maximum_advance_reservation_unit', $context );
		}

		/**
		 * Get availability rules
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return YITH_WCBK_Availability_Rule[]
		 * @since 2.1
		 */
		public function get_availability_rules( $context = 'view' ) {
			return $this->get_prop( 'availability_rules', $context );
		}

		/**
		 * Get base price
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_base_price( $context = 'view' ) {
			return $this->get_prop( 'base_price', $context );
		}

		/**
		 * Get multiply base price by number of people
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function get_multiply_base_price_by_number_of_people( $context = 'view' ) {
			return $this->get_prop( 'multiply_base_price_by_number_of_people', $context );
		}

		/**
		 * Get extra price per person
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function get_extra_price_per_person( $context = 'view' ) {
			return $this->get_prop( 'extra_price_per_person', $context );
		}

		/**
		 * Get extra price per person greater than
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_extra_price_per_person_greater_than( $context = 'view' ) {
			return $this->get_prop( 'extra_price_per_person_greater_than', $context );
		}

		/**
		 * Get weekly discount
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_weekly_discount( $context = 'view' ) {
			return $this->get_prop( 'weekly_discount', $context );
		}

		/**
		 * Get monthly discount
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_monthly_discount( $context = 'view' ) {
			return $this->get_prop( 'monthly_discount', $context );
		}

		/**
		 * Get last minute discount
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_last_minute_discount( $context = 'view' ) {
			return $this->get_prop( 'last_minute_discount', $context );
		}

		/**
		 * Get last minute discount - days before arrival
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function get_last_minute_discount_days_before_arrival( $context = 'view' ) {
			return $this->get_prop( 'last_minute_discount_days_before_arrival', $context );
		}

		/**
		 * Get multiply fixed base fee by number of people
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function get_multiply_fixed_base_fee_by_number_of_people( $context = 'view' ) {
			return $this->get_prop( 'multiply_fixed_base_fee_by_number_of_people', $context );
		}

		/**
		 * Get fixed base fee
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_fixed_base_fee( $context = 'view' ) {
			return $this->get_prop( 'fixed_base_fee', $context );
		}

		/**
		 * Returns the product's active price.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string price
		 */
		public function get_price( $context = 'view' ) {
			$price = parent::get_price( 'edit' );

			$price = 'view' === $context ? apply_filters( 'yith_wcbk_booking_product_get_price', $price, $this ) : $price;

			return 'view' === $context ? apply_filters( 'woocommerce_product_get_price', $price, $this ) : $price;
		}

		/**
		 * Returns the price in html format.
		 *
		 * @param string $deprecated Deprecated param.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_price_html( $deprecated = '' ) {
			if ( '' === $this->get_price() ) {
				$price_html = apply_filters( 'woocommerce_empty_price_html', '', $this );
			} else {
				$price_html = wc_price( wc_get_price_to_display( $this ) ) . $this->get_price_suffix();
			}

			$is_price_changed = array_key_exists( 'price', $this->changes );

			// If the price is not the default one, we need to show the one set as price.
			if ( ! $is_price_changed ) {
				/**
				 * Allow pre-filtering the HTML price.
				 *
				 * @see YITH_WCBK_Premium_Products::filter_pre_get_price_html
				 */
				$pre_price_html = apply_filters( 'yith_wcbk_booking_product_pre_get_price_html', null, $this );
				if ( ! is_null( $pre_price_html ) ) {
					$price_html = $pre_price_html;
				} else {
					$price_html = wc_price( $this->calculate_price() );
				}
			}

			$price_html = apply_filters( 'woocommerce_get_price_html', $price_html, $this );

			return apply_filters( 'yith_wcbk_booking_product_get_price_html', $price_html, $this );
		}

		/**
		 * Returns the product's regular price.
		 * In case of Booking Product the regular price is ''
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string price
		 */
		public function get_regular_price( $context = 'view' ) {
			return '';
		}

		/**
		 * Get price rules
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return YITH_WCBK_Price_Rule[]
		 * @since 2.1
		 */
		public function get_price_rules( $context = 'view' ) {
			return $this->get_prop( 'price_rules', $context );
		}

		/**
		 * Get enable people
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function get_enable_people( $context = 'view' ) {
			return $this->get_prop( 'enable_people', $context );
		}

		/**
		 * Get minimum number of people
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_minimum_number_of_people( $context = 'view' ) {
			return $this->get_prop( 'minimum_number_of_people', $context );
		}

		/**
		 * Get maximum number of people
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_maximum_number_of_people( $context = 'view' ) {
			return $this->get_prop( 'maximum_number_of_people', $context );
		}

		/**
		 * Get count people as separate bookings
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function get_count_people_as_separate_bookings( $context = 'view' ) {
			return $this->get_prop( 'count_people_as_separate_bookings', $context );
		}

		/**
		 * Get enable people types
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function get_enable_people_types( $context = 'view' ) {
			return $this->get_prop( 'enable_people_types', $context );
		}

		/**
		 * Get people types
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 * @since 2.1
		 */
		public function get_people_types( $context = 'view' ) {
			return $this->get_prop( 'people_types', $context );
		}

		/**
		 * Get service ids
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int[]
		 * @since 2.1
		 */
		public function get_service_ids( $context = 'view' ) {
			return $this->get_prop( 'service_ids', $context );
		}

		/**
		 * Get external calendars
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 * @since 2.1
		 */
		public function get_external_calendars( $context = 'view' ) {
			return $this->get_prop( 'external_calendars', $context );
		}

		/**
		 * Get external calendars key
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_external_calendars_key( $context = 'view' ) {
			return $this->get_prop( 'external_calendars_key', $context );
		}

		/**
		 * Get external calendars last sync
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_external_calendars_last_sync( $context = 'view' ) {
			return $this->get_prop( 'external_calendars_last_sync', $context );
		}


		/**
		 * Get extra costs
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return YITH_WCBK_Product_Extra_Cost[]
		 * @since 2.1
		 */
		public function get_extra_costs( $context = 'view' ) {
			return $this->get_prop( 'extra_costs', $context );
		}

		/**
		 * Get default availabilities
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return YITH_WCBK_Availability[]
		 * @since 3.0.0
		 */
		public function get_default_availabilities( $context = 'view' ) {
			return $this->get_prop( 'default_availabilities', $context );
		}

		/**
		 * Get enable_resources.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 * @since 4.0.0
		 */
		public function get_enable_resources( $context = 'view' ) {
			return $this->get_prop( 'enable_resources', $context );
		}

		/**
		 * Get resource_assignment.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 4.0.0
		 */
		public function get_resource_assignment( $context = 'view' ) {
			return $this->get_prop( 'resource_assignment', $context );
		}

		/**
		 * Get resources_layout.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since feature/resources-layout
		 */
		public function get_resources_layout( $context = 'view' ) {
			return $this->get_prop( 'resources_layout', $context );
		}

		/**
		 * Get resources_label.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 4.0.0
		 */
		public function get_resources_label( $context = 'view' ) {
			return $this->get_prop( 'resources_label', $context );
		}

		/**
		 * Get resources_field_label.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 4.0.0
		 */
		public function get_resources_field_label( $context = 'view' ) {
			return $this->get_prop( 'resources_field_label', $context );
		}

		/**
		 * Get resources_field_placeholder.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 4.0.0
		 */
		public function get_resources_field_placeholder( $context = 'view' ) {
			return $this->get_prop( 'resources_field_placeholder', $context );
		}

		/**
		 * Get resource_is_required.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 * @since 4.0.0
		 */
		public function get_resource_is_required( $context = 'view' ) {
			return $this->get_prop( 'resource_is_required', $context );
		}

		/**
		 * Get resources_data.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return YITH_WCBK_Resource_Data[]
		 * @since 4.0.0
		 */
		public function get_resources_data( $context = 'view' ) {
			return $this->get_prop( 'resources_data', $context );
		}

		/*
		|--------------------------------------------------------------------------
		| Setters
		|--------------------------------------------------------------------------
		|
		| Functions for setting product data. These should not update anything in the
		| database itself and should only change what is stored in the class
		| object.
		*/

		/**
		 * Set product duration type.
		 *
		 * @param string $duration_type Product duration type.
		 *
		 * @since 2.1
		 */
		public function set_duration_type( $duration_type ) {
			$this->set_prop( 'duration_type', $duration_type );
		}

		/**
		 * Set product duration.
		 *
		 * @param int $duration Product duration.
		 *
		 * @since 2.1
		 */
		public function set_duration( $duration ) {
			$this->set_prop( 'duration', max( 1, absint( $duration ) ) );
		}

		/**
		 * Set product duration unit.
		 *
		 * @param string $duration_unit Product duration unit.
		 *
		 * @since 2.1
		 */
		public function set_duration_unit( $duration_unit ) {
			$this->set_prop( 'duration_unit', $duration_unit );
		}

		/**
		 * Set enable calendar range picker
		 *
		 * @param bool|string $enabled Whether the calendar range picker is enabled or not.
		 *
		 * @since 2.1
		 */
		public function set_enable_calendar_range_picker( $enabled ) {
			$this->set_prop( 'enable_calendar_range_picker', wc_string_to_bool( $enabled ) );
		}

		/**
		 * Set default start date
		 *
		 * @param string $default_start_date Product default start date.
		 *
		 * @since 2.1
		 */
		public function set_default_start_date( $default_start_date ) {
			$this->set_prop( 'default_start_date', $default_start_date );
		}

		/**
		 * Set default start date custom
		 *
		 * @param string $default_start_date_custom Product default start date custom.
		 *
		 * @since 2.1
		 */
		public function set_default_start_date_custom( $default_start_date_custom ) {
			$this->set_prop( 'default_start_date_custom', $default_start_date_custom );
		}

		/**
		 * Set default start time
		 *
		 * @param string $default_start_time Product default start time.
		 *
		 * @since 2.1
		 */
		public function set_default_start_time( $default_start_time ) {
			$this->set_prop( 'default_start_time', $default_start_time );
		}

		/**
		 * Set full day
		 *
		 * @param bool|string $full_day Whether the product is full day or not.
		 *
		 * @since 2.1
		 */
		public function set_full_day( $full_day ) {
			$this->set_prop( 'full_day', wc_string_to_bool( $full_day ) );
		}

		/**
		 * Set location
		 *
		 * @param string $location Product location.
		 *
		 * @since 2.1
		 */
		public function set_location( $location ) {
			if ( yith_wcbk_is_google_maps_module_active() ) {
				$this->set_prop( 'location', $location );
			}
		}

		/**
		 * Set location latitude
		 *
		 * @param string $latitude Product location latitude.
		 *
		 * @since 2.1
		 */
		public function set_location_latitude( $latitude ) {
			if ( yith_wcbk_is_google_maps_module_active() ) {
				$this->set_prop( 'location_latitude', $latitude );
			}
		}

		/**
		 * Set location longitude
		 *
		 * @param string $location_longitude Product location longitude.
		 *
		 * @since 2.1
		 */
		public function set_location_longitude( $location_longitude ) {
			if ( yith_wcbk_is_google_maps_module_active() ) {
				$this->set_prop( 'location_longitude', $location_longitude );
			}
		}

		/**
		 * Set max bookings per unit
		 *
		 * @param int $max_bookings_per_unit Product max bookings per unit.
		 *
		 * @since 2.1
		 */
		public function set_max_bookings_per_unit( $max_bookings_per_unit ) {
			$this->set_prop( 'max_bookings_per_unit', absint( $max_bookings_per_unit ) );
		}

		/**
		 * Set minimum duration
		 *
		 * @param int $minimum_duration Product minimum duration.
		 *
		 * @since 2.1
		 */
		public function set_minimum_duration( $minimum_duration ) {
			$this->set_prop( 'minimum_duration', max( 1, absint( $minimum_duration ) ) );
		}

		/**
		 * Set maximum duration
		 *
		 * @param int $maximum_duration Product maximum duration.
		 *
		 * @since 2.1
		 */
		public function set_maximum_duration( $maximum_duration ) {
			$maximum_duration = $this->is_type_fixed_blocks() ? 1 : absint( $maximum_duration );
			$this->set_prop( 'maximum_duration', absint( $maximum_duration ) );
		}

		/**
		 * Set confirmation required
		 *
		 * @param bool|string $confirmation_required Whether the product requires confirmation or not.
		 *
		 * @since 2.1
		 */
		public function set_confirmation_required( $confirmation_required ) {
			$this->set_prop( 'confirmation_required', wc_string_to_bool( $confirmation_required ) );
		}

		/**
		 * Get cancellation available
		 *
		 * @param bool|string $cancellation_available Whether the booking cancellation is available or not.
		 *
		 * @since 2.1
		 */
		public function set_cancellation_available( $cancellation_available ) {
			$this->set_prop( 'cancellation_available', wc_string_to_bool( $cancellation_available ) );
		}

		/**
		 * Set cancellation available up to
		 *
		 * @param int $cancellation_available_up_to Product cancellation available up to.
		 *
		 * @since 2.1
		 */
		public function set_cancellation_available_up_to( $cancellation_available_up_to ) {
			$this->set_prop( 'cancellation_available_up_to', absint( $cancellation_available_up_to ) );
		}

		/**
		 * Get cancellation available up to unit
		 *
		 * @param string $cancellation_available_up_to_unit Product cancellation available up to unit.
		 *
		 * @since 2.1
		 */
		public function set_cancellation_available_up_to_unit( $cancellation_available_up_to_unit ) {
			$this->set_prop( 'cancellation_available_up_to_unit', $cancellation_available_up_to_unit );
		}

		/**
		 * Set check-in
		 *
		 * @param string $check_in Product check-in.
		 *
		 * @since 2.1
		 */
		public function set_check_in( $check_in ) {
			$this->set_prop( 'check_in', $check_in );
		}

		/**
		 * Set check-out
		 *
		 * @param string $check_out Product check-out.
		 *
		 * @since 2.1
		 */
		public function set_check_out( $check_out ) {
			$this->set_prop( 'check_out', $check_out );
		}

		/**
		 * Set allowed start days
		 *
		 * @param int[] $allowed_start_days Product allowed start days.
		 *
		 * @since 2.1
		 */
		public function set_allowed_start_days( $allowed_start_days ) {
			$this->set_prop( 'allowed_start_days', is_array( $allowed_start_days ) ? array_map( 'absint', $allowed_start_days ) : array() );
		}

		/**
		 * Set daily start time
		 *
		 * @param string $daily_start_time Product daily start time.
		 *
		 * @since      2.1
		 * @todo       To remove, since this field is no longer available since 3.0.0 (see WC_Product_Booking::get_daily_time_slot_ranges).
		 * @deprecated 3.0.0
		 */
		public function set_daily_start_time( $daily_start_time ) {
			$this->set_prop( 'daily_start_time', yith_wcbk_time_slot( $daily_start_time ) );
		}

		/**
		 * Set buffer
		 *
		 * @param int $buffer Product buffer.
		 *
		 * @since 2.1
		 */
		public function set_buffer( $buffer ) {
			$this->set_prop( 'buffer', apply_filters( 'yith_wcbk_set_buffer', absint( $buffer ), $buffer ) );
		}

		/**
		 * Set time increment based on duration
		 *
		 * @param bool|string $time_increment_based_on_duration Whether the time increment is based on duration or not.
		 *
		 * @since 2.1
		 */
		public function set_time_increment_based_on_duration( $time_increment_based_on_duration ) {
			$this->set_prop( 'time_increment_based_on_duration', wc_string_to_bool( $time_increment_based_on_duration ) );
		}

		/**
		 * Set time increment including buffer
		 *
		 * @param bool|string $time_increment_including_buffer Whether the time increment includes buffer or not.
		 *
		 * @since 2.1
		 */
		public function set_time_increment_including_buffer( $time_increment_including_buffer ) {
			$this->set_prop( 'time_increment_including_buffer', wc_string_to_bool( $time_increment_including_buffer ) );
		}

		/**
		 * Set minimum advance reservation
		 *
		 * @param int $minimum_advance_reservation Product minimum advance reservation.
		 *
		 * @since 2.1
		 */
		public function set_minimum_advance_reservation( $minimum_advance_reservation ) {
			$this->set_prop( 'minimum_advance_reservation', absint( $minimum_advance_reservation ) );
		}

		/**
		 * Set minimum advance reservation unit
		 *
		 * @param string $minimum_advance_reservation_unit Product minimum advance reservation unit.
		 *
		 * @since 2.1
		 */
		public function set_minimum_advance_reservation_unit( $minimum_advance_reservation_unit ) {
			$this->set_prop( 'minimum_advance_reservation_unit', in_array( $minimum_advance_reservation_unit, array( 'month', 'day', 'hour' ), true ) ? $minimum_advance_reservation_unit : 'day' );
		}

		/**
		 * Set maximum advance reservation
		 *
		 * @param int $maximum_advance_reservation Product maximum advance reservation.
		 *
		 * @since 2.1
		 */
		public function set_maximum_advance_reservation( $maximum_advance_reservation ) {
			$this->set_prop( 'maximum_advance_reservation', max( 1, absint( $maximum_advance_reservation ) ) );
		}

		/**
		 * Set maximum advance reservation unit
		 *
		 * @param string $value Product maximum advance reservation unit.
		 *
		 * @since 2.1
		 */
		public function set_maximum_advance_reservation_unit( $value ) {
			$allowed_values = array( 'year', 'month', 'day' );
			$this->set_prop( 'maximum_advance_reservation_unit', in_array( $value, $allowed_values, true ) ? $value : 'year' );
		}

		/**
		 * Set availability rules
		 *
		 * @param array|YITH_WCBK_Availability_Rule[] $availability_rules Product availability rules.
		 *
		 * @since 2.1
		 */
		public function set_availability_rules( $availability_rules ) {
			if ( ! ! $availability_rules && is_array( $availability_rules ) ) {
				$availability_rules = array_map( 'yith_wcbk_availability_rule', $availability_rules );
			} else {
				$availability_rules = array();
			}

			$remove_time_slots = ! $this->has_time();

			/**
			 * Availability Rules
			 *
			 * @var YITH_WCBK_Availability_Rule[] $availability_rules
			 */
			$availability_rules = array_map(
				function ( $rule ) use ( $remove_time_slots ) {
					$availabilities = $rule->get_availabilities( 'edit' );
					$availabilities = yith_wcbk_validate_availabilities(
						$availabilities,
						array(
							'remove_time_slots'   => $remove_time_slots,
							'force_first_all_day' => false,
						)
					);
					$rule->set_availabilities( $availabilities );

					return $rule;
				},
				$availability_rules
			);

			$this->set_prop( 'availability_rules', $availability_rules );
		}

		/**
		 * Set base price
		 *
		 * @param string $base_price Product base price.
		 *
		 * @since 2.1
		 */
		public function set_base_price( $base_price ) {
			$this->set_prop( 'base_price', wc_format_decimal( $base_price ) );
		}

		/**
		 * Set multiply base price by number of people
		 *
		 * @param bool|string $multiply Whether the cost are multiplied by the number of people or not.
		 *
		 * @since 2.1
		 */
		public function set_multiply_base_price_by_number_of_people( $multiply ) {
			if ( yith_wcbk_is_people_module_active() ) {
				$this->set_prop( 'multiply_base_price_by_number_of_people', wc_string_to_bool( $multiply ) );
			}
		}

		/**
		 * Set extra price per person
		 *
		 * @param string $price Product extra price per person.
		 *
		 * @since 2.1
		 */
		public function set_extra_price_per_person( $price ) {
			if ( yith_wcbk_is_people_module_active() ) {
				$this->set_prop( 'extra_price_per_person', wc_format_decimal( $price ) );
			}
		}

		/**
		 * Set extra price per person greater than
		 *
		 * @param string $price Product extra price per person.
		 *
		 * @since 2.1
		 */
		public function set_extra_price_per_person_greater_than( $price ) {
			if ( yith_wcbk_is_people_module_active() ) {
				$this->set_prop( 'extra_price_per_person_greater_than', absint( $price ) );
			}
		}

		/**
		 * Set weekly discount
		 *
		 * @param int|float|string $discount The discount to apply.
		 *
		 * @since 2.1
		 */
		public function set_weekly_discount( $discount ) {
			if ( yith_wcbk_is_costs_module_active() ) {
				$this->set_prop( 'weekly_discount', wc_format_decimal( min( max( 0, $discount ), 100 ) ) );
			}
		}

		/**
		 * Set monthly discount
		 *
		 * @param int|float|string $discount The discount to apply.
		 *
		 * @since 2.1
		 */
		public function set_monthly_discount( $discount ) {
			if ( yith_wcbk_is_costs_module_active() ) {
				$this->set_prop( 'monthly_discount', wc_format_decimal( min( max( 0, $discount ), 100 ) ) );
			}
		}

		/**
		 * Set last minute discount
		 *
		 * @param int|float|string $discount The discount to apply.
		 *
		 * @since 2.1
		 */
		public function set_last_minute_discount( $discount ) {
			if ( yith_wcbk_is_costs_module_active() ) {
				$this->set_prop( 'last_minute_discount', wc_format_decimal( min( max( 0, $discount ), 100 ) ) );
			}
		}

		/**
		 * Set last minute discount - days before arrival
		 *
		 * @param int $days the days before arrival.
		 *
		 * @since 2.1
		 */
		public function set_last_minute_discount_days_before_arrival( $days ) {
			if ( yith_wcbk_is_costs_module_active() ) {
				$this->set_prop( 'last_minute_discount_days_before_arrival', absint( $days ) );
			}
		}

		/**
		 * Set multiply fixed base fee by number of people
		 *
		 * @param bool|string $multiply Whether the cost are multiplied by the number of people or not.
		 *
		 * @since 2.1
		 */
		public function set_multiply_fixed_base_fee_by_number_of_people( $multiply ) {
			if ( yith_wcbk_is_people_module_active() ) {
				$this->set_prop( 'multiply_fixed_base_fee_by_number_of_people', wc_string_to_bool( $multiply ) );
			}
		}

		/**
		 * Set fixed base fee
		 *
		 * @param string $fixed_base_fee Product fixed base fee.
		 *
		 * @since 2.1
		 */
		public function set_fixed_base_fee( $fixed_base_fee ) {
			$this->set_prop( 'fixed_base_fee', wc_format_decimal( $fixed_base_fee ) );
		}

		/**
		 * Set price rules
		 *
		 * @param array|YITH_WCBK_Price_Rule[] $price_rules Product price rules.
		 *
		 * @since 2.1
		 */
		public function set_price_rules( $price_rules ) {
			if ( ! ! $price_rules && is_array( $price_rules ) ) {
				$price_rules = array_map( 'yith_wcbk_price_rule', $price_rules );
			} else {
				$price_rules = array();
			}
			$this->set_prop( 'price_rules', $price_rules );
		}

		/**
		 * Set enable people
		 *
		 * @param bool|string $enable_people Whether the people are enabled or not.
		 *
		 * @since 2.1
		 */
		public function set_enable_people( $enable_people ) {
			if ( yith_wcbk_is_people_module_active() ) {
				$this->set_prop( 'enable_people', wc_string_to_bool( $enable_people ) );
			}
		}

		/**
		 * Set minimum number of people
		 *
		 * @param int $minimum_number_of_people Product minimum number of people.
		 *
		 * @since 2.1
		 */
		public function set_minimum_number_of_people( $minimum_number_of_people ) {
			if ( yith_wcbk_is_people_module_active() ) {
				$this->set_prop( 'minimum_number_of_people', max( 1, absint( $minimum_number_of_people ) ) );
			}
		}

		/**
		 * Set maximum number of people
		 *
		 * @param int $maximum_number_of_people Product maximum number of people.
		 *
		 * @since 2.1
		 */
		public function set_maximum_number_of_people( $maximum_number_of_people ) {
			if ( yith_wcbk_is_people_module_active() ) {
				$this->set_prop( 'maximum_number_of_people', absint( $maximum_number_of_people ) );
			}
		}

		/**
		 * Set count people as separate bookings
		 *
		 * @param bool|string $count_people_as_separate_bookings Whether the people are counted as separate bookings or not.
		 *
		 * @since 2.1
		 */
		public function set_count_people_as_separate_bookings( $count_people_as_separate_bookings ) {
			if ( yith_wcbk_is_people_module_active() ) {
				$this->set_prop( 'count_people_as_separate_bookings', wc_string_to_bool( $count_people_as_separate_bookings ) );
			}
		}

		/**
		 * Set enable people types
		 *
		 * @param bool|string $enable_people_types Whether the people types are enabled or not.
		 *
		 * @since 2.1
		 */
		public function set_enable_people_types( $enable_people_types ) {
			if ( yith_wcbk_is_people_module_active() ) {
				$this->set_prop( 'enable_people_types', wc_string_to_bool( $enable_people_types ) );
			}
		}

		/**
		 * Set people types
		 *
		 * @param array $people_types Product people types.
		 *
		 * @since 2.1
		 */
		public function set_people_types( $people_types ) {
			if ( yith_wcbk_is_people_module_active() ) {
				$people_types = is_array( $people_types ) ? $people_types : array();
				foreach ( $people_types as $_key => $_value ) {
					$id = $_value['id'] ?? $_key;
					if ( $id && 'publish' === get_post_status( $id ) ) {
						if ( isset( $_value['base_cost'] ) ) {
							$people_types[ $_key ]['base_cost'] = wc_format_decimal( $_value['base_cost'] );
						}
						if ( isset( $_value['block_cost'] ) ) {
							$people_types[ $_key ]['block_cost'] = wc_format_decimal( $_value['block_cost'] );
						}
					} else {
						unset( $people_types[ $_key ] );
					}
				}
				$this->set_prop( 'people_types', $people_types );
			}
		}

		/**
		 * Set service ids
		 *
		 * @param int[] $service_ids Product service ids.
		 *
		 * @since 2.1
		 */
		public function set_service_ids( $service_ids ) {
			if ( yith_wcbk_is_services_module_active() ) {
				$this->set_prop( 'service_ids', array_filter( array_map( 'absint', $service_ids ) ) );
			}
		}

		/**
		 * Set external calendars
		 *
		 * @param array $external_calendars Product external calendars.
		 *
		 * @since 2.1
		 */
		public function set_external_calendars( $external_calendars ) {
			if ( yith_wcbk_is_external_sync_module_active() ) {
				if ( is_array( $external_calendars ) ) {
					foreach ( $external_calendars as $key => $calendar ) {
						if ( empty( $calendar['url'] ) ) {
							unset( $external_calendars[ $key ] );
						}
					}
				}
				$this->set_prop( 'external_calendars', is_array( $external_calendars ) ? $external_calendars : array() );
			}
		}

		/**
		 * Set external calendars key
		 *
		 * @param string $external_calendars_key Product external calendars key.
		 *
		 * @since 2.1
		 */
		public function set_external_calendars_key( $external_calendars_key ) {
			if ( yith_wcbk_is_external_sync_module_active() ) {
				if ( ! $external_calendars_key ) {
					$external_calendars_key = yith_wcbk_generate_external_calendars_key();
				}
				$this->set_prop( 'external_calendars_key', $external_calendars_key );
			}
		}

		/**
		 * Set external calendars last sync
		 *
		 * @param int $external_calendars_last_sync Product external calendars last sync.
		 *
		 * @since 2.1
		 */
		public function set_external_calendars_last_sync( $external_calendars_last_sync ) {
			if ( yith_wcbk_is_external_sync_module_active() ) {
				$this->set_prop( 'external_calendars_last_sync', absint( $external_calendars_last_sync ) );
			}
		}

		/**
		 * Set extra costs.
		 *
		 * @param array $extra_costs The product extra costs.
		 *
		 * @since 2.1
		 */
		public function set_extra_costs( $extra_costs ) {
			if ( yith_wcbk_is_costs_module_active() ) {
				if ( ! ! $extra_costs && is_array( $extra_costs ) ) {
					$extra_costs = array_map( 'yith_wcbk_product_extra_cost', $extra_costs );
					$extra_costs = array_reduce( $extra_costs, 'yith_wcbk_product_extra_costs_array_reduce' );
				} else {
					$extra_costs = array();
				}

				$this->set_prop( 'extra_costs', $extra_costs );
			}
		}

		/**
		 * Set default availabilities
		 *
		 * @param array|YITH_WCBK_Availability[] $availabilities The default availabilities.
		 *
		 * @since 3.0.0
		 */
		public function set_default_availabilities( $availabilities ) {
			$availabilities = ! ! $availabilities && is_array( $availabilities ) ? $availabilities : array();
			$availabilities = array_map( 'yith_wcbk_availability', $availabilities );
			$availabilities = yith_wcbk_validate_availabilities(
				$availabilities,
				array(
					'remove_time_slots'   => ! $this->has_time(),
					'force_first_all_day' => true,
				)
			);

			$this->set_prop( 'default_availabilities', $availabilities );
		}

		/**
		 * Set enable_resources
		 *
		 * @param bool|string $value The value to set.
		 *
		 * @since 4.0.0
		 */
		public function set_enable_resources( $value ) {
			if ( yith_wcbk_is_resources_module_active() ) {
				$this->set_prop( 'enable_resources', wc_string_to_bool( $value ) );
			}
		}

		/**
		 * Set resource_assignment
		 *
		 * @param string $value The value to set.
		 *
		 * @since 4.0.0
		 */
		public function set_resource_assignment( $value ) {
			if ( yith_wcbk_is_resources_module_active() ) {
				$this->set_prop( 'resource_assignment', $value );
			}
		}

		/**
		 * Set resources_layout
		 *
		 * @param string $value The value to set.
		 *
		 * @since feature/resources-layout
		 */
		public function set_resources_layout( $value ) {
			if ( yith_wcbk_is_resources_module_active() ) {
				$allowed = array( 'default', 'dropdown', 'list' );
				$value   = in_array( $value, $allowed, true ) ? $value : 'default';
				$this->set_prop( 'resources_layout', $value );
			}
		}

		/**
		 * Set resources_label
		 *
		 * @param string $value The value to set.
		 *
		 * @since 4.0.0
		 */
		public function set_resources_label( $value ) {
			if ( yith_wcbk_is_resources_module_active() ) {
				$this->set_prop( 'resources_label', $value );
			}
		}

		/**
		 * Set resources_field_label
		 *
		 * @param string $value The value to set.
		 *
		 * @since 4.0.0
		 */
		public function set_resources_field_label( $value ) {
			if ( yith_wcbk_is_resources_module_active() ) {
				$this->set_prop( 'resources_field_label', $value );
			}
		}

		/**
		 * Set resources_field_placeholder
		 *
		 * @param string $value The value to set.
		 *
		 * @since 4.0.0
		 */
		public function set_resources_field_placeholder( $value ) {
			if ( yith_wcbk_is_resources_module_active() ) {
				$this->set_prop( 'resources_field_placeholder', $value );
			}
		}

		/**
		 * Set resource_is_required
		 *
		 * @param bool|string $value The value to set.
		 *
		 * @since 4.0.0
		 */
		public function set_resource_is_required( $value ) {
			if ( yith_wcbk_is_resources_module_active() ) {
				$this->set_prop( 'resource_is_required', wc_string_to_bool( $value ) );
			}
		}

		/**
		 * Set resources_data
		 *
		 * @param array $value The value to set.
		 *
		 * @since 4.0.0
		 */
		public function set_resources_data( $value ) {
			if ( yith_wcbk_is_resources_module_active() ) {
				$value = ! ! $value && is_array( $value ) ? $value : array();

				/**
				 * The resource data.
				 *
				 * @var YITH_WCBK_Resource_Data[] $value
				 */
				$value = array_filter( array_map( 'yith_wcbk_resource_data', $value ) );

				$new_value = array();
				foreach ( $value as $resource_data ) {
					$resource_data->set_product_id( $this->get_id() );
					$new_value[ $resource_data->get_resource_id( 'edit' ) ] = $resource_data;
				}

				$this->set_prop( 'resources_data', $new_value );
			}
		}

		/*
		|--------------------------------------------------------------------------
		| Conditionals
		|--------------------------------------------------------------------------
		*/

		/**
		 * Return true if it's possible showing availability of the current product in calendar.
		 *
		 * @param string $step The step.
		 *
		 * @return bool
		 * @since 2.0.3
		 */
		public function can_show_availability( $step = '' ) {
			$show = $this->get_max_bookings_per_unit() > 1;
			if ( $show && $step ) {
				switch ( $step ) {
					case 'day':
						$show = 'day' === $this->get_duration_unit();
						break;
					case 'h':
					case 'hour':
					case 'hours':
						$show = $this->has_time();
						break;
					case 'm':
					case 'minute':
					case 'minutes':
						$show = 'minute' === $this->get_duration_unit();
						break;
				}
			}

			return $show;
		}

		/**
		 * Check for the product availability
		 *
		 * @param int  $from                 The 'from' timestamp.
		 * @param int  $to                   The 'to' timestamp.
		 * @param bool $exclude_time         Set to true to exclude time.
		 * @param bool $default_availability Default availability.
		 *
		 * @return bool
		 */
		public function check_availability( int $from, int $to, bool $exclude_time = false, bool $default_availability = true ): bool {
			return $this->availability_handler()->check_availability_rules( $from, $to, $exclude_time, $default_availability );
		}

		/**
		 * Check if the duration type is "Fixed blocks"
		 *
		 * @return boolean
		 */
		public function is_type_fixed_blocks() {
			return 'fixed' === $this->get_duration_type();
		}

		/**
		 * Return true if duration unit is hour or minute
		 *
		 * @return bool
		 * @since 2.0.0
		 */
		public function has_time() {
			return in_array( $this->get_duration_unit(), array( 'hour', 'minute' ), true );
		}

		/**
		 * Checks if a product has the calendar picker enabled
		 *
		 * @return bool
		 */
		public function has_calendar_picker_enabled() {
			return $this->get_enable_calendar_range_picker() && 'customer' === $this->get_duration_type() && 'day' === $this->get_duration_unit() && 1 === $this->get_duration();
		}

		/**
		 * Check if has people enabled.
		 *
		 * @return boolean
		 * @since 2.1
		 */
		public function has_people() {
			return $this->get_enable_people() && yith_wcbk_is_people_module_active();
		}

		/**
		 * Check if this booking has services
		 *
		 * @return bool
		 */
		public function has_services() {
			return ! ! $this->get_service_ids() && yith_wcbk_is_services_module_active();
		}

		/**
		 * Return true if the product has resources
		 *
		 * @return bool
		 * @since 4.0.0
		 */
		public function has_resources(): bool {
			return ! ! $this->get_enable_resources() && ! ! $this->get_resources_data() && yith_wcbk_is_resources_module_active();
		}

		/**
		 * Return true if the weekly discount is enabled
		 *
		 * @return bool
		 */
		public function is_weekly_discount_enabled() {
			return $this->get_weekly_discount() && 'customer' === $this->get_duration_type() && 'day' === $this->get_duration_unit() && 1 === $this->get_duration() && yith_wcbk_is_costs_module_active();
		}

		/**
		 * Return true if the monthly discount is enabled
		 *
		 * @return bool
		 */
		public function is_monthly_discount_enabled() {
			return $this->get_monthly_discount() && 'customer' === $this->get_duration_type() && 'day' === $this->get_duration_unit() && 1 === $this->get_duration() && yith_wcbk_is_costs_module_active();
		}

		/**
		 * Return true if the last minute discount is allowed from the start date
		 *
		 * @param int|string $start The start date of the booking.
		 *
		 * @return bool
		 */
		public function is_last_minute_discount_allowed( $start ) {
			if ( ! yith_wcbk_is_costs_module_active() ) {
				return false;
			}
			$start = ! is_numeric( $start ) ? strtotime( $start ) : $start;
			$now   = time();

			if ( ! $this->has_time() ) {
				$start = strtotime( 'midnight', $start );
				$now   = strtotime( 'midnight', $now );
			}

			return apply_filters( 'yith_wcbk_is_last_minute_discount_allowed', $this->get_last_minute_discount() && ( $now >= $start - $this->get_last_minute_discount_days_before_arrival() * DAY_IN_SECONDS ), $this, $now, $start );
		}

		/**
		 * Return true if time increment based on duration is enabled
		 *
		 * @return bool
		 * @since 2.0.0
		 */
		public function is_time_increment_based_on_duration() {
			return $this->get_time_increment_based_on_duration();
		}

		/**
		 * Return true if time increment based on duration is enabled
		 *
		 * @return bool
		 * @since 2.0.7
		 */
		public function is_time_increment_including_buffer() {
			return $this->is_type_fixed_blocks() && $this->has_time() && $this->get_time_increment_including_buffer();
		}

		/**
		 * Checks if a product has multiply costs by persons enabled.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function has_multiply_base_price_by_number_of_people() {
			return $this->has_people() && $this->get_multiply_base_price_by_number_of_people() && yith_wcbk_is_people_module_active();
		}

		/**
		 * Checks if a product has multiply costs by persons enabled.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function has_multiply_fixed_base_fee_by_number_of_people() {
			return $this->has_people() && $this->get_multiply_fixed_base_fee_by_number_of_people() && yith_wcbk_is_people_module_active();
		}

		/**
		 * Checks if a product has count persons as bookings enabled.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function has_count_people_as_separate_bookings_enabled() {
			return $this->has_people() && $this->get_count_people_as_separate_bookings() && yith_wcbk_is_people_module_active();
		}

		/**
		 * Check if has people types enabled.
		 *
		 * @return boolean
		 * @since 2.1
		 */
		public function has_people_types_enabled() {
			return $this->has_people() && $this->get_enable_people_types() && ! ! $this->get_enable_people_types() && yith_wcbk_is_people_module_active();
		}

		/**
		 * Check for the default availability.
		 *
		 * @param int   $from The from timestamp.
		 * @param int   $to   The to timestamp.
		 * @param array $args Arguments.
		 *
		 * @return bool
		 * @since 3.0.0
		 */
		protected function check_default_availability( int $from, int $to, array $args = array() ): bool {
			return $this->availability_handler()->check_default_availability( $from, $to, $args );
		}

		/**
		 * Check if this booking is available
		 *
		 * @param array $args Arguments.
		 *
		 * @return bool|array
		 */
		public function is_available( $args = array() ) {
			do_action( 'yith_wcbk_booking_before_is_available', $args, $this );
			$args                             = apply_filters( 'yith_wcbk_booking_product_is_available_args', $args, $this );
			$date_helper                      = yith_wcbk_date_helper();
			$now                              = time();
			$minimum_advance_reservation      = $this->get_minimum_advance_reservation();
			$minimum_advance_reservation_unit = $this->get_minimum_advance_reservation_unit();
			$unit                             = $this->get_duration_unit();
			$relative_maximum_duration        = $this->get_maximum_duration() * $this->get_duration();
			$relative_minimum_duration        = $this->get_minimum_duration() * $this->get_duration();

			$from                              = $args['from'] ?? $now;
			$to                                = ! empty( $args['to'] ) ? $args['to'] : false;
			$exclude_time                      = $args['exclude_time'] ?? false;
			$check_start_date                  = $args['check_start_date'] ?? true;
			$check_min_max_duration            = $args['check_min_max_duration'] ?? true;
			$check_non_available_in_past       = $args['check_non_available_in_past'] ?? true;
			$check_maximum_advance_reservation = $args['check_maximum_advance_reservation'] ?? true;

			$return                = $args['return'] ?? 'bool';
			$include_reasons       = 'array' === $return;
			$non_available_reasons = array();
			$local_timestamp       = yith_wcbk_get_local_timezone_timestamp();
			$allowed_start_days    = $this->get_allowed_start_days();

			if ( ! $to ) {
				$_duration = $check_min_max_duration ? $relative_minimum_duration : 1;
				$to        = $date_helper->get_time_sum( $from, $_duration, $unit );
				if ( $this->is_full_day() ) {
					$to = $date_helper->get_time_sum( $to, - 1, 'day' );
				}
			}

			if ( $this->is_full_day() ) {
				$to = strtotime( '00:00:00', $to );
				$to = $date_helper->get_time_sum( $to, 1, 'day' );
			}

			$args['parsed_from'] = $from;
			$args['parsed_to']   = $to;

			$available = true;

			// Not available in past for Time bookings.
			if ( ( $available || $include_reasons ) && isset( $args['from'] ) && ! $exclude_time && $check_non_available_in_past && $this->has_time() ) {
				if ( $from < $local_timestamp ) {
					$available                                = false;
					$non_available_reasons['start-date-past'] = __( 'The selected start date has already passed', 'yith-booking-for-woocommerce' );
				}
			}

			// Not available in past (based on 'Allow after' | default 'today midnight'); take into account the current time.
			$min_date_midnight  = in_array( $minimum_advance_reservation_unit, array( 'month', 'day' ), true ) ? 'midnight' : '';
			$min_date_timestamp = strtotime( "+{$minimum_advance_reservation} {$minimum_advance_reservation_unit}s {$min_date_midnight}", $local_timestamp );
			if ( ( $available || $include_reasons ) && $check_non_available_in_past && $from < $min_date_timestamp ) {
				$available = false;
				$_format   = $this->has_time() ? ( wc_date_format() . ' ' . wc_time_format() ) : wc_date_format();
				$_min_date = date_i18n( $_format, $min_date_timestamp );

				// translators: %s is the minimum date.
				$non_available_reasons['start-non-allowed-after'] = sprintf( __( 'The selected start date is not allowed; you cannot book it before %s', 'yith-booking-for-woocommerce' ), $_min_date );
			}

			if ( ( $available || $include_reasons ) && $check_start_date && $allowed_start_days ) {
				$from_day = absint( gmdate( 'N', $from ) );
				if ( ! in_array( $from_day, $allowed_start_days, true ) ) {
					$available                                      = false;
					$non_available_reasons['start-day-non-allowed'] = __( 'The selected start day is not allowed', 'yith-booking-for-woocommerce' );
				}
			}

			if ( ( $available || $include_reasons ) && $check_min_max_duration ) {
				$min_to = $date_helper->get_time_sum( $from, $relative_minimum_duration, $unit, true );

				if ( $to < $min_to ) {
					$available          = false;
					$_min_duration_html = yith_wcbk_format_duration( $relative_minimum_duration, $unit );

					// translators: %s is the minimum duration.
					$non_available_reasons['min-duration'] = sprintf( __( 'Min duration: %s', 'yith-booking-for-woocommerce' ), $_min_duration_html );
				}

				if ( $relative_maximum_duration > 0 ) {
					$max_to = $date_helper->get_time_sum( $from, $relative_maximum_duration, $unit, true );

					if ( $this->is_full_day() ) {
						$max_to = $date_helper->get_time_sum( $max_to, 1, 'day' ) - 1;
					}

					if ( $to > $max_to ) {
						$available          = false;
						$_max_duration_html = yith_wcbk_format_duration( $relative_maximum_duration, $unit );

						// translators: %s is the maximum duration.
						$non_available_reasons['max-duration'] = sprintf( __( 'Max duration: %s', 'yith-booking-for-woocommerce' ), $_max_duration_html );
					}
				}

				if ( $this->get_duration() > 1 ) {
					$_duration = $date_helper->get_time_diff( $from, $to, $unit );
					if ( $_duration % $this->get_duration() !== 0 ) {
						$available                                      = false;
						$non_available_reasons['duration-non-multiple'] = __( 'The selected duration is not allowed', 'yith-booking-for-woocommerce' );
					}
				}
			}

			if ( $check_maximum_advance_reservation && ( $available || $include_reasons ) ) {
				$maximum_advance_reservation      = apply_filters( 'yith_wcbk_get_maximum_advance_reservation', $this->get_maximum_advance_reservation(), $this );
				$maximum_advance_reservation_unit = apply_filters( 'yith_wcbk_get_maximum_advance_reservation_unit', $this->get_maximum_advance_reservation_unit(), $this );
				// Not available in future (based on 'Maximum advance reservation' | default '+1 year').
				$max_date_timestamp = strtotime( "+{$maximum_advance_reservation} {$maximum_advance_reservation_unit}s midnight", $now );
				if ( $to > $max_date_timestamp ) {
					$available                            = false;
					$non_available_reasons['allow-until'] = __( 'The end date is beyond available ones', 'yith-booking-for-woocommerce' );
				}
			}

			$available_data        = apply_filters( 'yith_wcbk_booking_is_available_data_static', compact( 'available', 'non_available_reasons' ), $args, $this );
			$available             = ! ! ( $available_data['available'] ?? false );
			$non_available_reasons = $available_data['non_available_reasons'] ?? array();
			$non_available_reasons = ! $available ? $non_available_reasons : array();

			$_remained = '';

			if ( $available ) {
				$handler                       = $this->availability_handler();
				$available                     = $handler->is_available( $from, $to, $args );
				$current_non_available_reasons = $handler->get_non_available_reason_messages();
				if ( ! $available ) {
					$non_available_reasons = array_merge( $non_available_reasons, $current_non_available_reasons );
				}
			}

			$available             = apply_filters( 'yith_wcbk_booking_is_available', $available, $args, $this );
			$non_available_reasons = apply_filters( 'yith_wcbk_booking_is_available_non_available_reasons', $non_available_reasons, $args, $this, $_remained );

			$available_data        = apply_filters( 'yith_wcbk_booking_is_available_data', compact( 'available', 'non_available_reasons' ), $args, $this );
			$available             = ! ! ( $available_data['available'] ?? false );
			$non_available_reasons = $available_data['non_available_reasons'] ?? array();
			$non_available_reasons = ! $available ? $non_available_reasons : array();

			if ( 'array' === $return ) {
				$available = compact( 'available', 'non_available_reasons' );
			}

			return $available;
		}

		/**
		 * Check if the confirmation is required
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function is_confirmation_required() {
			return $this->get_confirmation_required();
		}

		/**
		 * Check if the product is full day
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function is_full_day() {
			return $this->get_full_day() && 'day' === $this->get_duration_unit();
		}

		/**
		 * Check if the cancellation is available
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function is_cancellation_available() {
			return $this->get_cancellation_available();
		}


		/**
		 * Returns false if the product cannot be bought.
		 *
		 * @return bool
		 */
		public function is_purchasable() {
			return apply_filters( 'woocommerce_is_purchasable', $this->exists() && ( 'publish' === $this->get_status() || current_user_can( 'edit_post', $this->get_id() ) ), $this );
		}

		/**
		 * The booking product is sold individually
		 *
		 * @return boolean
		 */
		public function is_sold_individually() {
			return true;
		}

		/**
		 * Checks if a product is virtual (has no shipping).
		 *
		 * @return bool
		 */
		public function is_virtual() {
			return apply_filters( 'yith_wcbk_booking_product_is_virtual', parent::is_virtual(), $this );
		}

		/**
		 * Has at least one time slot available.
		 *
		 * @param string|int $date           The date.
		 * @param bool       $exclude_booked Exclude booked flag.
		 *
		 * @return bool
		 * @since 2.0.8
		 */
		public function has_at_least_one_time_slot_available_on( $date, $exclude_booked = false ) {
			if ( ! is_numeric( $date ) ) {
				$date = strtotime( $date );
			}
			$current_day = strtotime( 'midnight', $date );
			$next_day    = strtotime( 'tomorrow', $current_day );
			$available   = true;

			if ( $this->has_time() ) {
				$check = true;
				if ( apply_filters( 'yith_wcbk_product_has_at_least_one_time_slot_available_on_check_only_if_bookings_exist', false ) ) {
					$_count_args = array(
						'product_id'        => $this->get_id(),
						'from'              => $current_day,
						'to'                => $next_day,
						'include_externals' => true,
					);
					$check       = $exclude_booked ? 1 : yith_wcbk_booking_helper()->count_booked_bookings_in_period( $_count_args );
				}

				if ( $check ) {
					$available = ! ! $this->create_availability_time_array( $current_day );
				}
			} else {
				$available = $this->is_available(
					array(
						'from' => $current_day,
						'to'   => $next_day,
					)
				);
			}

			return $available;
		}

		/**
		 * Return true if has external calendars
		 *
		 * @return bool
		 * @since 2.0.0
		 */
		public function has_external_calendars() {
			return ! ! $this->get_external_calendars() && yith_wcbk_is_external_sync_module_active();
		}

		/**
		 * Return true if externals has already loaded (and not expired) for this product
		 *
		 * @return bool
		 * @since 2.0
		 */
		public function has_externals_synchronized() {
			if ( ! yith_wcbk_is_external_sync_module_active() ) {
				return false;
			}
			$expiring_time = get_option( 'yith-wcbk-external-calendars-sync-expiration', 6 * HOUR_IN_SECONDS );
			$now           = time();
			$last_loaded   = $this->get_external_calendars_last_sync();

			return ! ! $last_loaded && ( $now - $last_loaded < $expiring_time );
		}

		/**
		 * Is this a valid external calendars key?
		 *
		 * @param string $key The key.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function is_valid_external_calendars_key( $key ) {
			return $key === $this->get_external_calendars_key();
		}

		/*
		|--------------------------------------------------------------------------
		| Non-CRUD Getters
		|--------------------------------------------------------------------------
		*/

		/**
		 * Get and initialize the availability handler.
		 *
		 * @return YITH_WCBK_Product_Availability_Handler
		 * @since 4.0.0
		 */
		public function availability_handler(): YITH_WCBK_Product_Availability_Handler {
			$this->availability_handler->init( $this );

			return $this->availability_handler;
		}

		/**
		 * Get the add to cart button text
		 *
		 * @access public
		 * @return string
		 */
		public function add_to_cart_text() {
			return apply_filters( 'woocommerce_product_add_to_cart_text', yith_wcbk_get_label( 'read-more' ), $this );
		}

		/**
		 * Get the add to cart button text for the single page.
		 *
		 * @return string
		 */
		public function single_add_to_cart_text() {
			$text = ! $this->is_confirmation_required() ? yith_wcbk_get_label( 'add-to-cart' ) : yith_wcbk_get_label( 'request-confirmation' );

			return apply_filters( 'woocommerce_product_single_add_to_cart_text', $text, $this );
		}

		/**
		 * Calculate costs (block or base) for a single timestamp
		 *
		 * @param int    $timestamp Timestamp.
		 * @param string $type      The type.
		 * @param array  $args      Arguments.
		 *
		 * @return float
		 */
		public function calculate_cost( $timestamp, $type = 'base_price', $args = array() ) {
			do_action( 'yith_wcbk_booking_before_calculate_cost', $timestamp, $type, $this );

			// Backward compatibility.
			if ( 'block' === $type ) {
				$type = 'base_price';
			} elseif ( 'base' === $type ) {
				$type = 'fixed_base_fee';
			}

			$allowed_types = array( 'base_price', 'fixed_base_fee' );
			$type          = in_array( $type, $allowed_types, true ) ? $type : 'base_price';
			$cost          = 'base_price' === $type ? (float) $this->get_base_price() : (float) $this->get_fixed_base_fee();

			$price_rules        = $this->get_price_rules();
			$global_price_rules = yith_wcbk()->settings->get_global_price_rules( array( 'product_id' => $this->get_id() ) );
			$price_rules        = array_merge( $price_rules, $global_price_rules );

			$person_type_id = false;
			$person_number  = 0;

			if ( isset( $args['person_type'] ) ) {
				$current_person_type = $args['person_type'];
				$person_types        = $this->get_enabled_people_types();
				if ( 0 !== $current_person_type['id'] && isset( $person_types[ $current_person_type['id'] ] ) ) {
					$person_type_id = $current_person_type['id'];
					$person_number  = absint( $current_person_type['number'] );

					$product_person_type   = $person_types[ $current_person_type['id'] ];
					$person_type_cost_type = 'base_price' === $type ? 'block_cost' : 'base_cost';
					if ( '' !== $product_person_type[ $person_type_cost_type ] ) {
						$cost = (float) $product_person_type[ $person_type_cost_type ];

						$cost = apply_filters( "yith_wcbk_booking_product_single_person_type_{$type}", $cost, $person_type_id, $this );
					}
				}
			}

			$date_helper = yith_wcbk_date_helper();

			$persons  = isset( $args['persons'] ) ? absint( $args['persons'] ) : $this->get_minimum_number_of_people();
			$duration = isset( $args['duration'] ) ? absint( $args['duration'] ) : 1;

			$variables = array(
				'persons'   => $persons,
				'duration'  => $duration,
				'qty'       => 1,
				'extra_qty' => 1,
			);

			foreach ( $price_rules as $price_rule ) {
				if ( $price_rule->is_enabled() ) {
					$date_from         = $timestamp;
					$date_to           = $timestamp;
					$conditions        = $price_rule->get_conditions();
					$current_variables = $variables;
					$check             = ! ! $conditions;

					foreach ( $conditions as $condition ) {
						$condition_type = $condition['type'];
						$condition_from = $condition['from'];
						$condition_to   = $condition['to'];
						$intersect      = false;

						$is_date_range = ! in_array( $condition_type, array( 'person', 'block' ), true ) && 0 !== strpos( $condition_type, 'person-type-' );

						if ( $is_date_range ) {
							$condition_check = $date_helper->check_date_inclusion_in_range( $condition_type, $condition_from, $condition_to, $date_from, $date_to, $intersect );
						} else {
							$condition_check = false;
							$condition_from  = absint( $condition_from );
							$condition_to    = absint( $condition_to );

							if ( 'person' === $condition_type && $this->has_people() ) {
								$current_variables['qty']       = $persons;
								$current_variables['extra_qty'] = $persons - $condition_from + 1;
								if ( ( ! $condition_to || $persons <= $condition_to ) && $persons >= $condition_from ) {
									$condition_check = true;
								}
							} elseif ( 'block' === $condition_type ) {
								$current_variables['qty']       = $duration;
								$current_variables['extra_qty'] = $duration - $condition_from + 1;
								if ( ( ! $condition_to || $duration <= $condition_to ) && $duration >= $condition_from ) {
									$condition_check = true;
								}
							} elseif ( strpos( $condition_type, 'person-type-' ) === 0 && $this->has_people() && $this->has_people_types_enabled() ) {
								$range_person_type_id         = absint( str_replace( 'person-type-', '', $condition_type ) );
								$multiply_by_number_of_people = 'base_price' === $type ? $this->has_multiply_base_price_by_number_of_people() : $this->has_multiply_fixed_base_fee_by_number_of_people();
								$apply_to_all_people          = ! $multiply_by_number_of_people;
								$apply_to_all_people          = apply_filters( 'yith_wcbk_booking_calculate_cost_apply_person_type_rule_to_all_people', $apply_to_all_people, $timestamp, $type, $args, $this );

								if ( ! empty( $args['person_types'] ) && $apply_to_all_people ) {
									foreach ( $args['person_types'] as $_current_person_type ) {
										if ( absint( $_current_person_type['id'] ) === absint( $range_person_type_id ) ) {
											$_person_number                 = absint( $_current_person_type['number'] );
											$current_variables['qty']       = $_person_number;
											$current_variables['extra_qty'] = $_person_number - $condition_from + 1;
											if ( ( ! $condition_to || $_person_number <= $condition_to ) && $_person_number >= $condition_from ) {
												$condition_check = true;
											}
											break;
										}
									}
								} else {
									if ( $person_type_id && absint( $person_type_id ) === absint( $range_person_type_id ) ) {
										$current_variables['qty']       = $person_number;
										$current_variables['extra_qty'] = $person_number - $condition_from + 1;
										if ( ( ! $condition_to || $person_number <= $condition_to ) && $person_number >= $condition_from ) {
											$condition_check = true;
										}
									}
								}
							}
						}

						$check = $check && $condition_check;
					}

					if ( ! empty( $args['person_types'] ) ) {
						foreach ( $args['person_types'] as $_current_person_type ) {
							$_person_number = absint( $_current_person_type['number'] );

							$current_variables[ 'person_' . $_current_person_type['id'] ] = $_person_number;
						}
					}

					$variable_alias = array(
						'extra_qty' => array( 'qty_diff' ),
					);

					foreach ( $variable_alias as $key => $alias_array ) {
						foreach ( $alias_array as $alias ) {
							$current_variables[ $alias ] = $current_variables[ $key ];
						}
					}

					$check = apply_filters( 'yith_wcbk_booking_calculate_cost_check_is_in_range', $check, $price_rule, $timestamp, $type, $this );

					if ( $check ) {
						$this_cost     = 'base_price' === $type ? $price_rule->get_base_price() : $price_rule->get_base_fee();
						$this_operator = 'base_price' === $type ? $price_rule->get_base_price_operator() : $price_rule->get_base_fee_operator();

						if ( strpos( $this_cost, '*' ) ) {
							list( $this_cost, $variable ) = explode( '*', $this_cost, 2 );
							// The $current_variables[ $variable ] is an INTEGER: for this reason it should be > 1.
							if ( isset( $current_variables[ $variable ] ) && $current_variables[ $variable ] > 1 ) {
								$this_cost *= $current_variables[ $variable ];
							} elseif ( 'person_' === substr( $variable, 0, 7 ) && empty( $current_variables[ $variable ] ) ) {
								$this_cost = 0;
							}
						} elseif ( strpos( $this_cost, '/' ) ) {
							list( $this_cost, $variable ) = explode( '/', $this_cost, 2 );
							// The $current_variables[ $variable ] is an INTEGER: for this reason it should be > 1.
							if ( ! empty( $current_variables[ $variable ] ) && $current_variables[ $variable ] > 1 ) {
								$this_cost /= $current_variables[ $variable ];
							} elseif ( 'person_' === substr( $variable, 0, 7 ) && empty( $current_variables[ $variable ] ) ) {
								$this_cost = 0;
							}
						}

						$this_cost = (float) $this_cost;

						switch ( $this_operator ) {
							case 'add':
								$cost = $cost + $this_cost;
								break;
							case 'sub':
								$cost = $cost - $this_cost;
								break;
							case 'mul':
								$cost = $cost * $this_cost;
								break;
							case 'div':
								if ( ! ! $this_cost ) {
									$cost = $cost / $this_cost;
								}
								break;
							case 'set-to':
								$cost = $this_cost;
								break;
							case 'add-percentage':
								$cost = $cost * ( 1 + $this_cost / 100 );
								break;
							case 'sub-percentage':
								$cost = $cost * ( 1 - $this_cost / 100 );
								break;
						}
					}
				}
			}

			$cost = apply_filters( 'yith_wcbk_booking_calculate_cost', $cost, $timestamp, $type, $this );

			return (float) $cost;

		}

		/**
		 * Calculate the extra price per person.
		 *
		 * @param array $args Arguments.
		 *
		 * @return float|int
		 * @since 2.1
		 */
		public function calculate_extra_price_per_person( $args = array() ) {
			$extra_price_per_person = 0;
			if ( $this->has_people() && ! $this->has_multiply_base_price_by_number_of_people() && $this->get_extra_price_per_person() ) {
				$people_number = isset( $args['persons'] ) ? absint( $args['persons'] ) : $this->get_minimum_number_of_people();
				$people_types  = $this->get_enabled_people_types();
				if ( isset( $args['person_types'] ) ) {
					foreach ( $args['person_types'] as $people_type ) {
						$person_type_id     = absint( $people_type['id'] );
						$person_type_number = absint( $people_type['number'] );

						if ( isset( $people_types[ $person_type_id ] ) && isset( $people_types[ $person_type_id ]['block_cost'] ) && '0' === $people_types[ $person_type_id ]['block_cost'] ) {
							$people_number -= $person_type_number;
						}
					}
				}

				$extra_people = $people_number - $this->get_extra_price_per_person_greater_than();
				if ( $extra_people > 0 ) {
					$extra_price_per_person = $extra_people * $this->get_extra_price_per_person();
				}
			}

			return $extra_price_per_person;
		}

		/**
		 * Parse booking data args.
		 *
		 * @param array $data Booking data.
		 *
		 * @return array
		 * @since 3.0.0
		 */
		public function parse_booking_data_args( $data = array() ) {
			$data['from'] = $data['from'] ?? time();
			$data['from'] = ! is_numeric( $data['from'] ) ? strtotime( $data['from'] ) : $data['from'];

			$data['persons']      = $data['persons'] ?? $this->get_minimum_number_of_people();
			$data['person_types'] = isset( $data['person_types'] ) && is_array( $data['person_types'] ) ? yith_wcbk_booking_person_types_to_id_number_array( $data['person_types'] ) : array();

			if ( ! empty( $data['person_types'] ) && $this->has_people() ) {
				$data['persons'] = 0;
				foreach ( $data['person_types'] as $person_id => $person_number ) {
					$data['persons'] += absint( $person_number );
				}

				if ( 0 === $data['persons'] ) {
					// If counting person_types return zero, unset person_types to use only 'persons' for price calculation (to allow using minimum-people value).
					unset( $data['person_types'] );
				}
			}

			// The minimum number of persons is 1, also for bookings without people (to allow correct price calculation).
			$data['persons'] = max( 1, $data['persons'] );

			if ( $this->has_people() ) {
				$data['persons'] = max( $this->get_minimum_number_of_people(), $data['persons'] );
			}

			// TODO: add a check to keep only person types allowed in the product.

			if ( isset( $data['person_types'] ) && ! $this->has_people_types_enabled() ) {
				unset( $data['person_types'] );
			}

			return $data;
		}

		/**
		 * Parse args before calculating price
		 *
		 * @param array $args Arguments.
		 *
		 * @return array
		 * @since 2.1
		 */
		public function parse_price_args( $args = array() ) {
			$args = $this->parse_booking_data_args( $args );
			if ( isset( $args['person_types'] ) ) {
				$args['person_types'] = yith_wcbk_booking_person_types_to_list( $args['person_types'] );
			}

			if ( isset( $args['to'] ) ) {
				$to = ! is_numeric( $args['to'] ) ? strtotime( $args['to'] ) : $args['to'];

				if ( $this->is_full_day() ) {
					$to = yith_wcbk_date_helper()->get_time_sum( $to, 1, 'day' );
				}

				$args['duration'] = yith_wcbk_date_helper()->get_time_diff( $args['from'], $to, $this->get_duration_unit() ) / $this->get_duration();
				$args['duration'] = max( $args['duration'], $this->get_minimum_duration() );
				unset( $args['to'] ); // 'To' is not needed, it's enough having 'from' and 'duration'.
			} elseif ( ! isset( $args['duration'] ) ) {
				$args['duration'] = $this->get_minimum_duration();
			}

			return apply_filters( 'yith_wcbk_booking_product_parse_price_args', $args, $this );
		}

		/**
		 * Retrieve an array with Totals
		 *
		 * @param array $args      Arguments.
		 * @param bool  $formatted If true format each price in the 'display' parameter.
		 *
		 * @return array
		 * @since 2.1
		 */
		public function calculate_totals( $args = array(), $formatted = false ) {
			$args          = $this->parse_price_args( $args );
			$from          = $args['from'];
			$duration      = $args['duration'];
			$people_number = $args['persons'];

			$totals = array(
				'base_price_and_extra_price_per_person' => array( 'value' => 0 ),
				'base_price'                            => array(
					'label' => __( 'Base Price', 'yith-booking-for-woocommerce' ),
					'value' => 0,
				),
				'fixed_base_fee'                        => array(
					'label' => __( 'Fixed base fee', 'yith-booking-for-woocommerce' ),
					'value' => 0,
				),
				'extra_price_per_person'                => array(
					'label' => __( 'Extra price for additional people', 'yith-booking-for-woocommerce' ),
					'value' => 0,
				),
				'weekly_discount'                       => array(
					'label' => __( 'Weekly discount', 'yith-booking-for-woocommerce' ),
					'value' => 0,
				),
				'monthly_discount'                      => array(
					'label' => __( 'Monthly discount', 'yith-booking-for-woocommerce' ),
					'value' => 0,
				),
				'last_minute_discount'                  => array(
					'label' => __( 'Last minute discount', 'yith-booking-for-woocommerce' ),
					'value' => 0,
				),
			);

			$totals = apply_filters( 'yith_wcbk_totals', $totals );

			$default_people_types = array(
				array(
					'id'     => 0,
					'number' => $people_number,
				),
			);

			// Fixed Base Fee.
			$people_types = ! empty( $args['person_types'] ) && $this->has_multiply_fixed_base_fee_by_number_of_people() ? $args['person_types'] : $default_people_types;

			foreach ( $people_types as $people_type ) {
				// The fixed base fee depends on start-date only.
				$calculate_cost_args                = $args;
				$calculate_cost_args['person_type'] = $people_type;
				$person_type_number                 = absint( $people_type['number'] );
				$fixed_base_fee                     = $this->calculate_cost( $from, 'fixed_base_fee', $calculate_cost_args );

				if ( $this->has_multiply_fixed_base_fee_by_number_of_people() ) {
					$fixed_base_fee = $fixed_base_fee * $person_type_number;
				}

				$totals['fixed_base_fee']['value'] += $fixed_base_fee;
			}

			// Base Price.
			$people_types = ! empty( $args['person_types'] ) && $this->has_multiply_base_price_by_number_of_people() ? $args['person_types'] : $default_people_types;

			foreach ( $people_types as $people_type ) {
				$calculate_cost_args                = $args;
				$calculate_cost_args['person_type'] = $people_type;
				$person_type_number                 = absint( $people_type['number'] );
				$unit                               = $this->get_duration_unit();
				$single_block_duration              = $this->get_duration();
				$unit_cost                          = 0;
				$weekly_discount                    = 0;
				$monthly_discount                   = 0;
				$actual_week_cost                   = 0;
				$actual_month_cost                  = 0;

				// increase the block cost for every block in base of settings.
				for ( $i = 0; $i < $duration; $i ++ ) {
					$referring_date      = yith_wcbk_date_helper()->get_time_sum( $from, $single_block_duration * $i, $unit, true );
					$_current_block_cost = $this->calculate_cost( $referring_date, 'base_price', $calculate_cost_args );

					$unit_cost += $_current_block_cost;

					if ( yith_wcbk_is_costs_module_active() ) {
						$actual_week_cost  += $_current_block_cost;
						$actual_month_cost += $_current_block_cost;

						$check_for_weekly_discount  = ( $i + 1 ) % 7 === 0;
						$check_for_monthly_discount = apply_filters( 'yith_wcbk_check_for_monthly_discount', ( $i + 1 ) % 30 === 0, $i );

						if ( apply_filters( 'yith_wcbk_apply_weekly_discount', true, $duration ) && $check_for_weekly_discount && $this->is_weekly_discount_enabled() ) {
							$_current_discount = $this->get_weekly_discount() / 100 * $actual_week_cost;

							$weekly_discount   += $_current_discount;
							$actual_month_cost -= $_current_discount;

							$actual_week_cost = 0;
						}

						if ( $check_for_monthly_discount && $this->is_monthly_discount_enabled() ) {
							$monthly_discount += $this->get_monthly_discount() / 100 * $actual_month_cost;

							$actual_month_cost = 0;
						}
					}
				}

				if ( $this->has_multiply_base_price_by_number_of_people() ) {
					$unit_cost        = $unit_cost * $person_type_number;
					$weekly_discount  = $weekly_discount * $person_type_number;
					$monthly_discount = $monthly_discount * $person_type_number;
				}

				$totals['base_price']['value'] += $unit_cost;
				if ( yith_wcbk_is_costs_module_active() ) {
					$totals['weekly_discount']['value']  -= $weekly_discount;
					$totals['monthly_discount']['value'] -= $monthly_discount;
				}
			}

			// Extra Price Per Person.
			$single_extra_price_per_person             = $this->calculate_extra_price_per_person( $args );
			$totals['extra_price_per_person']['value'] = $single_extra_price_per_person * $duration;

			if ( yith_wcbk_is_costs_module_active() ) {
				$extra_price_per_person_weekly_discount  = 0;
				$extra_price_per_person_monthly_discount = 0;
				if ( $this->is_weekly_discount_enabled() && $duration >= 7 ) {
					$extra_price_per_person_weekly_discount = $single_extra_price_per_person * ( absint( $duration / 7 ) ) * $this->get_weekly_discount() / 100;
				}

				if ( $this->is_monthly_discount_enabled() && $duration >= 30 ) {
					$extra_price_per_person_monthly_discount = ( $single_extra_price_per_person - $extra_price_per_person_weekly_discount ) * ( absint( $duration / 30 ) ) * $this->get_monthly_discount() / 100;
				}

				$totals['weekly_discount']['value']  -= $extra_price_per_person_weekly_discount;
				$totals['monthly_discount']['value'] -= $extra_price_per_person_monthly_discount;

				if ( $this->is_weekly_discount_enabled() ) {
					// translators: %s is the discount percentage.
					$totals['weekly_discount']['label'] = sprintf( __( '%s%% weekly discount', 'yith-booking-for-woocommerce' ), yith_wcbk_number( $this->get_weekly_discount() ) );
				}

				if ( $this->is_monthly_discount_enabled() ) {
					// translators: %s is the discount percentage.
					$totals['monthly_discount']['label'] = sprintf( __( '%s%% monthly discount', 'yith-booking-for-woocommerce' ), yith_wcbk_number( $this->get_monthly_discount() ) );
				}

				// Extra Costs.
				foreach ( $this->get_extra_costs() as $extra_cost ) {
					if ( $extra_cost->is_valid() ) {
						$extra_cost_total_key            = "extra_cost_{$extra_cost->get_identifier()}";
						$totals[ $extra_cost_total_key ] = array(
							'label' => $extra_cost->get_name(),
							'value' => $extra_cost->calculate_cost( $duration, $people_number ),
						);
					}
				}

				// Last Minute Discount.
				if ( $this->is_last_minute_discount_allowed( $from ) ) {
					$totals_for_last_minute_discount = apply_filters( 'yith_wcbk_booking_product_last_minute_discount_applied_on', array( 'fixed_base_fee', 'base_price', 'extra_price_per_person', 'weekly_discount', 'monthly_discount' ), $args, $this );
					$total_to_discount               = 0;
					foreach ( $totals_for_last_minute_discount as $_key ) {
						if ( isset( $totals[ $_key ] ) && isset( $totals[ $_key ]['value'] ) ) {
							$total_to_discount += $totals[ $_key ]['value'];
						}
					}

					$totals['last_minute_discount']['value'] = - ( $total_to_discount * $this->get_last_minute_discount() / 100 );
				}
			}

			$totals = apply_filters( 'yith_wcbk_booking_product_calculated_price_totals', $totals, $args, $formatted, $this );

			if ( $formatted ) {
				// Merge base price and extra price per person.
				if ( apply_filters( 'yith_wcbk_booking_product_merge_base_price_and_extra_price_per_person_in_totals', true, $this ) ) {
					$base_price_and_extra_price_per_person = (float) $totals['base_price']['value'] + $totals['extra_price_per_person']['value'];
					$price_per_unit_average                = $base_price_and_extra_price_per_person / $duration;
					if ( 1 === $this->get_duration() ) {
						$_label = sprintf( '%s x %s', yith_wcbk_get_formatted_price_to_display( $this, $price_per_unit_average ), yith_wcbk_format_duration( $duration, $this->get_duration_unit() ) );
					} else {
						$_label = $totals['base_price']['label'];
					}
					$totals['base_price_and_extra_price_per_person'] = array(
						'label' => $_label,
						'value' => $base_price_and_extra_price_per_person,
					);
					unset( $totals['base_price'] );
					unset( $totals['extra_price_per_person'] );
				} else {
					if ( 1 === $this->get_duration() ) {
						$price_per_unit_average        = (float) $totals['base_price']['value'] / $duration;
						$totals['base_price']['label'] = sprintf( '%s x %s', wc_price( $price_per_unit_average ), yith_wcbk_format_duration( $duration, $this->get_duration_unit() ) );
					}
				}

				foreach ( $totals as $total_key => $total ) {
					if ( ! empty( $total['value'] ) ) {
						$totals[ $total_key ]['display'] = yith_wcbk_get_formatted_price_to_display( $this, $total['value'] );
					}
				}

				$totals = apply_filters( 'yith_wcbk_booking_product_calculated_price_totals_formatted', $totals, $args, $this );
			}

			return array_filter(
				$totals,
				function ( $total ) {
					return ! empty( $total['value'] );
				}
			);
		}

		/**
		 * Calculate the total price from totals array.
		 *
		 * @param array $totals Totals.
		 *
		 * @return float|int
		 * @since 2.1
		 */
		public function calculate_price_from_totals( $totals = array() ) {
			return array_sum( wp_list_pluck( $totals, 'value' ) );
		}

		/**
		 * Calculate price for Booking product
		 *
		 * @param array $args Arguments.
		 *
		 * @return float price
		 */
		public function calculate_price( $args = array() ) {
			$totals = $this->calculate_totals( $args, false );
			$price  = $this->calculate_price_from_totals( $totals );

			return apply_filters( 'yith_wcbk_booking_product_calculated_price', $price, $args, $this );
		}

		/**
		 * Calculate a partial price by including only some prices.
		 *
		 * @param array|false $included Array of costs to include (or false to include all costs).
		 *
		 * @return float price
		 * @since 3.0.0
		 */
		public function calculate_partial_price( $included = false ) {
			$totals = $this->calculate_totals( array(), false );

			if ( false !== $included ) {
				$to_unset = array();

				if ( ! in_array( 'base-price', $included, true ) || ! in_array( 'fixed-base-fee', $included, true ) ) {
					// Unset discounts if "base price" or "fixed base fee" are not included in calculation.
					$to_unset = array( 'weekly_discount', 'monthly_discount', 'last_minute_discount' );
				}

				if ( ! in_array( 'base-price', $included, true ) ) {
					$base_price_keys = array( 'base_price_and_extra_price_per_person', 'base_price', 'extra_price_per_person' );
					$to_unset        = array_merge( $to_unset, $base_price_keys );
				}

				if ( ! in_array( 'fixed-base-fee', $included, true ) ) {
					$to_unset[] = 'fixed_base_fee';
				}

				if ( ! in_array( 'services', $included, true ) ) {
					$to_unset[] = 'services';
				}

				if ( ! in_array( 'extra-costs', $included, true ) ) {
					$totals_keys      = array_keys( $totals );
					$extra_costs_keys = array_filter(
						$totals_keys,
						function ( $key ) {
							return 0 === strpos( $key, 'extra_cost_' );
						}
					);
					$to_unset         = array_merge( $to_unset, $extra_costs_keys );
				}

				foreach ( $to_unset as $key ) {
					if ( isset( $totals[ $key ] ) ) {
						unset( $totals[ $key ] );
					}
				}
			}

			$price = $this->calculate_price_from_totals( $totals );

			return apply_filters( 'yith_wcbk_booking_product_calculated_partial_price', $price, $included, $this );
		}

		/**
		 * Calculate booking product price to be stored.
		 *
		 * @return float price
		 * @since 3.0.0
		 */
		public function get_price_to_store() {
			/**
			 * Allow pre-filtering the price to store.
			 *
			 * @see YITH_WCBK_Premium_Products::filter_pre_get_price_to_store
			 */
			$price = apply_filters( 'yith_wcbk_booking_product_pre_get_price_to_store', null, $this );
			if ( ! is_null( $price ) ) {
				$price = $this->calculate_price();
			}

			return apply_filters( 'yith_wcbk_booking_product_get_price_to_store', $price, $this );
		}

		/**
		 * Get daily ranges for a specific timestamp.
		 *
		 * @param int $timestamp Timestamp of the day to be checked.
		 *
		 * @return array|string[][]
		 * @since 3.0.0
		 */
		public function get_daily_time_slot_ranges( int $timestamp ): array {
			$availabilities = $this->get_default_availabilities();
			$ranges         = array();
			if ( $this->has_time() ) {

				/**
				 * Handle backward compatibility, since 3.0.0
				 * "daily start time" field was removed and handled by DB update.
				 * This is intended to use the previously set "daily start time", during the DB update.
				 * Note: if empty, the default availability is automatically filled with a generic bookable
				 * availability in WC_Product_Booking::set_default_availabilities.
				 *
				 * @todo To remove when removing 'get_daily_start_time' method, since the DB will be already updated.
				 */
				$backward_compatibility_hack_3_0_0 = ! $availabilities || ( 1 === count( $availabilities ) && current( $availabilities )->is_full_day() );
				if ( $backward_compatibility_hack_3_0_0 ) {
					$ranges = array(
						array(
							'from' => $this->get_daily_start_time(),
							'to'   => '00:00',
						),
					);
				} else {
					$from = strtotime( 'midnight', $timestamp );
					$end  = strtotime( 'tomorrow midnight', $from );

					$available_from_tmp = false;

					while ( $from < $end ) {
						$to        = $from + ( 15 * MINUTE_IN_SECONDS );
						$available = $this->check_default_availability( $from, $to, array( 'include_time' => true ) );

						if ( ! $available_from_tmp && $available ) {
							$available_from_tmp = gmdate( 'H:i', $from );
						}

						if ( $available_from_tmp && ! $available ) {
							$ranges[]           = array(
								'from' => $available_from_tmp,
								'to'   => gmdate( 'H:i', $from ),
							);
							$available_from_tmp = false;
						}

						$from = $to;
					}

					if ( $available_from_tmp ) {
						$ranges[] = array(
							'from' => $available_from_tmp,
							'to'   => '00:00',
						);
					}
				}
			}

			// Fix the 'to' value to 24:00 if it's set to 00:00.
			$ranges = array_map(
				function ( $range ) {
					$range['to'] = '00:00' === $range['to'] ? '24:00' : $range['to'];

					return $range;
				},
				$ranges
			);

			return $ranges;
		}

		/**
		 * Create an array of available time slots.
		 *
		 * @param int|string $from              The initial date.
		 * @param int        $duration          The duration.
		 * @param array      $availability_args The availability args.
		 *
		 * @return array
		 * @since 2.0.0
		 */
		public function create_availability_time_array( $from = '', $duration = 0, $availability_args = array() ) {
			$function     = __FUNCTION__;
			$cached_key   = array_merge( compact( 'function', 'from', 'duration' ), $availability_args );
			$cached_value = yith_wcbk_cache()->get_product_data( $this->get_id(), $cached_key );

			$from = ! ! $from ? $from : time();
			$from = is_numeric( $from ) ? $from : strtotime( $from );

			$is_today = strtotime( 'midnight', $from ) === strtotime( 'midnight', time() );

			if ( ! $is_today && ! is_null( $cached_value ) ) {
				$times = $cached_value;
			} else {
				$times = array();
				$unit  = $this->get_duration_unit();
				if ( in_array( $unit, array( 'hour', 'minute' ), true ) ) {
					$date_helper      = yith_wcbk_date_helper();
					$booking_duration = $this->get_duration();
					$duration         = ! ! $duration ? $duration : $this->get_minimum_duration();

					if ( $this->is_time_increment_based_on_duration() ) {
						$unit_increment = $booking_duration;
					} else {
						$unit_increment = 'hour' === $unit ? 1 : yith_wcbk_get_minimum_minute_increment();
					}

					if ( $this->is_time_increment_including_buffer() && $this->get_buffer() ) {
						$unit_increment += $this->get_buffer();
					}

					$unit_increment = apply_filters( 'yith_wcbk_booking_product_create_availability_time_array_unit_increment', $unit_increment, $this, $from, $duration );

					/**
					 * Filter yith_wcbk_booking_product_create_availability_time_array_custom_time_slots
					 *
					 * @deprecated 3.0.0 | use default availability to set time slots.
					 */
					$custom_time_slots = apply_filters( 'yith_wcbk_booking_product_create_availability_time_array_custom_time_slots', array(), $this, $from, $duration );
					if ( $custom_time_slots ) {
						foreach ( $custom_time_slots as $time_slot ) {
							$current_time = strtotime( gmdate( 'Y-m-d', $from ) . ' ' . $time_slot );
							$_duration    = absint( $duration ) * $booking_duration;
							$_to          = $date_helper->get_time_sum( $current_time, $_duration, $unit );
							$is_available = $this->is_available(
								array(
									'from' => $current_time,
									'to'   => $_to,
								)
							);
							if ( $is_available ) {
								$time_to_add = gmdate( 'H:i', $current_time );
								$times[]     = $time_to_add;
							}
						}
					} else {
						$time_slot_ranges = $this->get_daily_time_slot_ranges( $from );

						foreach ( $time_slot_ranges as $range ) {
							$current_time = strtotime( gmdate( 'Y-m-d', $from ) . ' ' . $range['from'] );
							$end          = strtotime( gmdate( 'Y-m-d', $from ) . ' ' . $range['to'] );

							while ( $current_time < $end ) {
								$_duration    = absint( $duration ) * $booking_duration;
								$_to          = $date_helper->get_time_sum( $current_time, $_duration, $unit );
								$is_available = $this->is_available(
									array_merge(
										$availability_args,
										array(
											'from' => $current_time,
											'to'   => $_to,
										)
									)
								);
								if ( $is_available ) {
									$time_to_add = gmdate( 'H:i', $current_time );
									$times[]     = $time_to_add;
								}
								$current_time = $date_helper->get_time_sum( $current_time, $unit_increment, $unit );
							}
						}
					}
				}
				if ( ! $is_today ) {
					yith_wcbk_cache()->set_product_data( $this->get_id(), $cached_key, $times );
				}
			}

			return apply_filters( 'yith_wcbk_booking_product_create_availability_time_array', $times, $from, $duration, $this );
		}

		/**
		 * Get the block duration html
		 */
		public function get_block_duration_html() {
			return yith_wcbk_format_duration( $this->get_duration(), $this->get_duration_unit() );
		}

		/**
		 * Retrieve an array containing the booking data
		 *
		 * @return array
		 */
		public function get_booking_data() {
			$booking_data = array(
				'minimum_number_of_people' => $this->get_minimum_number_of_people(),
				'maximum_number_of_people' => $this->get_maximum_number_of_people(),
				'duration'                 => $this->get_duration(),
				'duration_unit'            => $this->get_duration_unit(),
				'minimum_duration'         => $this->get_minimum_duration(),
				'maximum_duration'         => $this->get_maximum_duration(),
				'full_day'                 => wc_bool_to_string( $this->get_full_day() ),
			);

			$old_booking_data = array(
				'min_persons' => $this->get_minimum_number_of_people(),
				'max_persons' => $this->get_maximum_number_of_people(),
				'all_day'     => $this->is_full_day() ? 'yes' : 'no',
			);

			$booking_data = array_merge( $booking_data, $old_booking_data );

			return apply_filters( 'yith_wcbk_get_booking_data', $booking_data, $this );
		}

		/**
		 * Get the calculated price html
		 *
		 * @param string $price The price.
		 *
		 * @return string
		 */
		public function get_calculated_price_html( $price = false ) {
			if ( false === $price || is_array( $price ) ) {
				// Backward compatibility, since the 1st params was an array before 2.1.
				$args  = is_array( $price ) ? $price : array();
				$price = $this->calculate_price( $args );
			}
			$_price = yith_wcbk_get_price_to_display( $this, $price );
			$_price = apply_filters( 'yith_wcbk_get_calculated_price_html_price', $_price, $price, $this );

			if ( ! $_price ) {
				$price_html = apply_filters( 'yith_wcbk_booking_product_free_price_html', __( 'Free!', 'woocommerce' ), $this );
			} else {
				$price_html = wc_price( $_price ) . $this->get_price_suffix();
			}

			return apply_filters( 'yith_wcbk_booking_product_get_calculated_price_html', $price_html, $price, $this );
		}

		/**
		 * Return the calculated default start date
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_calculated_default_start_date() {
			$date               = '';
			$default_start_date = $this->get_default_start_date();
			$offset             = (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
			$today_utc          = strtotime( 'now midnight' );
			$local_timestamp    = yith_wcbk_get_local_timezone_timestamp();

			if ( in_array( $default_start_date, array( 'today', 'tomorrow' ), true ) ) {
				$minimum_advance_reservation = $this->get_minimum_advance_reservation();
				$timestamp                   = strtotime( $default_start_date ) + $offset;

				if ( $minimum_advance_reservation ) {
					$minimum_advance_reservation_unit = $this->get_minimum_advance_reservation_unit();
					$first_available_timestamp        = yith_wcbk_date_helper()->get_time_sum( $today_utc, $minimum_advance_reservation, $minimum_advance_reservation_unit, true ) + $offset;
					if ( $timestamp < $first_available_timestamp ) {
						$timestamp = $first_available_timestamp;
					}
				}

				$date = gmdate( 'Y-m-d', $timestamp );

			} elseif ( 'first-available' === $default_start_date ) {
				$current_day                 = $local_timestamp;
				$minimum_advance_reservation = $this->get_minimum_advance_reservation();
				if ( $minimum_advance_reservation ) {
					$date_helper                      = yith_wcbk_date_helper();
					$minimum_advance_reservation_unit = $this->get_minimum_advance_reservation_unit();
					$current_day                      = $date_helper->get_time_sum( $current_day, $minimum_advance_reservation, $minimum_advance_reservation_unit, true );
				}
				$date_info           = yith_wcbk_get_booking_form_date_info(
					$this,
					array(
						'include_default_start_date' => false,
						'include_default_end_date'   => false,
					)
				);
				$last_date           = strtotime( $date_info['next_year'] . '-' . $date_info['next_month'] . '-1' ); // The last month is not included in non-available dates.
				$not_available_dates = $this->get_non_available_dates( $date_info['current_year'], $date_info['current_month'], $date_info['next_year'], $date_info['next_month'] );
				$allowed_start_days  = $this->get_allowed_start_days();

				do {
					$current_date = gmdate( 'Y-m-d', $current_day );
					if ( ! in_array( $current_date, $not_available_dates, true ) && ( ! $allowed_start_days || in_array( absint( gmdate( 'N', $current_day ) ), $allowed_start_days, true ) ) ) {
						$date = $current_date;
						break;
					} else {
						$current_day = strtotime( '+1 day', $current_day );
					}
				} while ( $current_day < $last_date );

			} elseif ( 'custom' === $default_start_date ) {
				$date = $this->get_default_start_date_custom();
			}

			return apply_filters( 'yith_wcbk_booking_product_get_default_start_date', $date, $this );

		}

		/**
		 * Get the admin calendar Url
		 *
		 * @return string
		 * @since 2.0.3
		 */
		public function get_admin_calendar_url() {
			$url = yith_wcbk_get_admin_calendar_url( $this->get_id() );

			return apply_filters( 'yith_wcbk_product_get_admin_calendar_url', $url, $this );
		}

		/**
		 * Get the enabled people types
		 *
		 * @return array
		 */
		public function get_enabled_people_types() {
			return array_filter(
				$this->get_people_types(),
				function ( $people_type ) {
					return isset( $people_type['enabled'] ) && 'yes' === $people_type['enabled'];
				}
			);
		}

		/**
		 * Return an array of bookings loaded from external calendars
		 *
		 * @param bool $force_loading Force loading flag.
		 *
		 * @return YITH_WCBK_Booking_External[]
		 * @since 2.0
		 */
		public function get_externals( $force_loading = false ) {
			$calendars = $this->get_external_calendars();
			$externals = array();
			if ( $calendars && yith_wcbk_is_external_sync_module_active() ) {
				$load = $force_loading || ! $this->has_externals_synchronized();

				if ( $load ) {
					yith_wcbk_booking_externals()->delete_externals_from_product_id( $this->get_id() );
					$externals = array();

					foreach ( $calendars as $calendar ) {
						$name = htmlspecialchars( $calendar['name'] );
						$url  = $calendar['url'];

						$timeout  = apply_filters( 'yith_wcbk_booking_product_get_externals_timeout', 15 );
						$response = wp_remote_get( $url, array( 'timeout' => $timeout ) );

						if ( ! is_wp_error( $response ) && 200 === absint( $response['response']['code'] ) && 'OK' === $response['response']['message'] ) {
							$body = $response['body'];
							try {
								$ics_parser = new YITH_WCBK_ICS_Parser(
									$body,
									array(
										'product_id'    => $this->get_id(),
										'calendar_name' => $name,
									)
								);

								$externals = array_merge( $externals, $ics_parser->get_events() );

							} catch ( Exception $e ) {
								$message = sprintf(
									'Error while parsing ICS externals for product #%s - %s - %s',
									$this->get_id(),
									$e->getMessage(),
									print_r( compact( 'name', 'url', 'body' ), true ) // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
								);

								yith_wcbk_add_log( $message, YITH_WCBK_Logger_Types::ERROR, YITH_WCBK_Logger_Groups::GENERAL );
							}
						} else {
							$message = sprintf(
								'Error while retrieving externals for product #%s - %s',
								$this->get_id(),
								print_r( compact( 'name', 'url', 'response' ), true ) // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
							);

							yith_wcbk_add_log( $message, YITH_WCBK_Logger_Types::ERROR, YITH_WCBK_Logger_Groups::GENERAL );
						}
					}

					$externals = apply_filters( 'yith_wcbk_product_retrieved_externals', $externals, $this );

					// remove completed externals.
					$externals = array_filter(
						$externals,
						function ( $external ) {
							/**
							 * External booking.
							 *
							 * @var YITH_WCBK_Booking_External $external
							 */
							return ! $external->is_completed();
						}
					);

					yith_wcbk_booking_externals()->add_externals( $externals, false );
					yith_wcbk_product_update_external_calendars_last_sync( $this );

				} else {
					$externals = yith_wcbk_booking_externals()->get_externals_from_product_id( $this->get_id() );
				}
			}

			return $externals;
		}

		/**
		 * Get the location coordinates
		 *
		 * @return array|bool
		 */
		public function get_location_coordinates() {
			$coordinates = false;

			if ( $this->get_location() && yith_wcbk_is_google_maps_module_active() ) {
				$latitude  = $this->get_location_latitude();
				$longitude = $this->get_location_longitude();

				if ( '' !== $latitude && '' !== $longitude ) {
					$coordinates = array(
						'lat' => $latitude,
						'lng' => $longitude,
					);
				} else {
					$this->update_location_coordinates();

					$latitude  = $this->get_location_latitude();
					$longitude = $this->get_location_longitude();

					if ( '' !== $latitude && '' !== $longitude ) {
						$coordinates = array(
							'lat' => $latitude,
							'lng' => $longitude,
						);
					}
				}
			}

			return $coordinates;
		}

		/**
		 * Get non available months
		 *
		 * @param int $from_year  From year.
		 * @param int $from_month From month.
		 * @param int $to_year    To year.
		 * @param int $to_month   To month.
		 *
		 * @return array
		 */
		public function get_not_available_months( $from_year, $from_month, $to_year, $to_month ) {
			$dates           = $this->get_non_available_dates( $from_year, $from_month, $to_year, $to_month, array( 'range' => 'month' ) );
			$number_of_dates = count( $dates );
			if ( $number_of_dates < 1 ) {
				return array();
			}

			$zero_array  = array_fill( 0, $number_of_dates, 0 );
			$seven_array = array_fill( 0, $number_of_dates, 7 );
			$dates       = array_map( 'substr', $dates, $zero_array, $seven_array );

			return $dates;
		}

		/**
		 * Get the permalink by adding query args based on passed array
		 *
		 * @param array $booking_data Booking data.
		 *
		 * @return string
		 * @since 2.0.0
		 */
		public function get_permalink_with_data( $booking_data = array() ) {
			$booking_data_array = array();
			foreach ( $booking_data as $id => $value ) {
				switch ( $id ) {
					case 'booking_services':
						if ( is_array( $value ) && ! ! $value ) {
							$booking_data_array[ $id ] = implode( ',', $value );
						} else {
							$booking_data_array[ $id ] = $value;
						}
						break;
					case 'person_types':
						if ( is_array( $value ) && ! ! $value ) {
							foreach ( $value as $child_id => $child_value ) {
								$current_id                        = 'person_type_' . absint( $child_id );
								$booking_data_array[ $current_id ] = $child_value;
							}
						}
						break;
					default:
						if ( is_scalar( $value ) ) {
							$booking_data_array[ $id ] = $value;
						}
						break;
				}
			}

			return add_query_arg( $booking_data_array, $this->get_permalink() );
		}

		/**
		 * Get non available dates
		 *
		 * @param int   $from_year  From year.
		 * @param int   $from_month From month.
		 * @param int   $to_year    To year.
		 * @param int   $to_month   To month.
		 * @param array $args       Arguments.
		 *
		 * @return array
		 * @since 4.0.0
		 */
		public function get_non_available_dates( int $from_year, int $from_month, int $to_year, int $to_month, array $args = array() ): array {
			$default_args = array(
				'range'                  => 'day',
				'exclude_booked'         => false,
				'check_start_date'       => false,
				'check_min_max_duration' => false,
			);
			$args         = wp_parse_args( $args, $default_args );

			$all_args = array_merge( compact( 'from_year', 'from_month', 'to_year', 'to_month' ), $args );
			$dates    = apply_filters( 'yith_wcbk_product_get_not_available_dates_before', null, $all_args, $this );
			$no_cache = apply_filters( 'yith_wcbk_product_get_not_available_dates_force_no_cache', false );
			if ( ! is_null( $dates ) ) {
				return $dates;
			}
			$cached_key = array_merge( array( 'function' => __FUNCTION__ ), $all_args );

			if ( $no_cache || ( $this->has_external_calendars() && ! $this->has_externals_synchronized() ) ) {
				$cached_value = null; // not use cache to consider new data for external calendars.
			} else {
				$cached_value = yith_wcbk_cache()->get_product_data( $this->get_id(), $cached_key );
			}

			if ( ! is_null( $cached_value ) ) {
				$dates = $cached_value;
			} else {
				$args['return'] = 'non_bookable';
				$calendar       = $this->generate_availability_calendar( $from_year, $from_month, $to_year, $to_month, $args );
				$dates          = array();
				foreach ( $calendar as $year => $months ) {
					foreach ( $months as $month => $days ) {
						if ( $month < 10 ) {
							$month = '0' . $month;
						}
						foreach ( $days as $day => $bookable ) {
							if ( $day < 10 ) {
								$day = '0' . $day;
							}
							$dates[] = $year . '-' . $month . '-' . $day;
						}
					}
				}

				// Set data if cache is enabled.
				$no_cache || yith_wcbk_cache()->set_product_data( $this->get_id(), $cached_key, $dates );
			}

			return apply_filters( 'yith_wcbk_product_get_not_available_dates', $dates, $all_args, $this );
		}

		/**
		 * Create availability calendar
		 *
		 * @param int   $from_year  From year.
		 * @param int   $from_month From month.
		 * @param int   $to_year    To year.
		 * @param int   $to_month   To month.
		 * @param array $args       Arguments.
		 *
		 * @return array
		 * @since 4.0.0
		 */
		protected function generate_availability_calendar( int $from_year, int $from_month, int $to_year, int $to_month, array $args = array() ): array {
			$default_args = array(
				'return'                 => 'all',
				'range'                  => 'day',
				'exclude_booked'         => false,
				'check_start_date'       => false,
				'check_min_max_duration' => false,
			);
			$args         = wp_parse_args( $args, $default_args );

			$calendar = array();

			$from_year  = absint( $from_year );
			$from_month = absint( $from_month );
			$to_year    = absint( $to_year );
			$to_month   = absint( $to_month );

			for ( $year = $from_year; $year <= $to_year; $year ++ ) {
				$first_month        = $year === $from_year ? $from_month : 1;
				$last_month         = $year === $to_year ? ( $to_month - 1 ) : 12; // last month is not included.
				$this_year_calendar = $this->generate_availability_year_calendar( $year, $first_month, $last_month, $args );
				if ( ! empty( $this_year_calendar ) ) {
					$calendar[ $year ] = $this_year_calendar;
				}
			}

			return $calendar;
		}

		/**
		 * Create availability year calendar
		 *
		 * @param int   $year       Year.
		 * @param int   $from_month From month.
		 * @param int   $to_month   To month.
		 * @param array $args       Arguments.
		 *
		 * @return array
		 * @since 4.0.0
		 */
		protected function generate_availability_year_calendar( int $year = 0, int $from_month = 1, int $to_month = 12, array $args = array() ): array {
			$default_args = array(
				'return'                 => 'all',
				'range'                  => 'day',
				'exclude_booked'         => false,
				'check_start_date'       => false,
				'check_min_max_duration' => false,
			);
			$args         = wp_parse_args( $args, $default_args );

			$year_calendar = array();
			for ( $i = $from_month; $i <= $to_month; $i ++ ) {
				$this_month_calendar = $this->generate_availability_month_calendar( $year, $i, $args );
				if ( ! empty( $this_month_calendar ) ) {
					$year_calendar[ $i ] = $this_month_calendar;
				}
			}

			return $year_calendar;
		}

		/**
		 * Create availability month calendar.
		 *
		 * @param int   $year  Year.
		 * @param int   $month Month.
		 * @param array $args  Arguments.
		 *
		 * @return array
		 * @since 4.0.0
		 */
		protected function generate_availability_month_calendar( int $year = 0, int $month = 0, array $args = array() ) {
			$default_args = array(
				'return'                 => 'all',
				'range'                  => 'day',
				'exclude_booked'         => false,
				'check_start_date'       => false,
				'check_min_max_duration' => false,
			);
			$args         = wp_parse_args( $args, $default_args );
			$range        = $args['range'] ?? 'day';
			$return       = $args['return'] ?? 'all';

			$is_available_args = $args;
			$internal_args     = array( 'return', 'range' );
			foreach ( $internal_args as $arg ) {
				unset( $is_available_args[ $arg ] );
			}

			$exclude_booked_param                             = $is_available_args['exclude_booked'];
			$is_available_args['exclude_booked']              = $is_available_args['exclude_booked'] || $this->has_time(); // Force excluding booked for time-bookings.
			$is_available_args['check_non_available_in_past'] = ! $this->has_time(); // Exclude checking in past for time-bookings.

			$disable_day_if_no_time = yith_wcbk()->settings->get( 'disable-day-if-no-time-available', 'no' ) === 'yes';

			$year  = absint( $year );
			$month = absint( $month );

			// Default for year and month.
			$year  = ! $year ? gmdate( 'Y', time() ) : $year;
			$month = ! $month ? gmdate( 'm', time() ) : $month;

			$month_calendar = array();

			$first_day_of_month = strtotime( $year . '-' . $month . '-01' );
			if ( $this->has_time() ) {
				$first_day_of_month = strtotime( $year . '-' . $month . '-01', yith_wcbk_get_local_timezone_timestamp() );
			}
			$first_day_of_next_month = strtotime( ' + 1 month', $first_day_of_month );

			$current_day = $first_day_of_month;
			while ( $current_day < $first_day_of_next_month ) {
				$number_of_day = gmdate( 'j', $current_day );
				switch ( $range ) {
					case 'month':
						$next_day = $first_day_of_next_month;
						break;
					case 'day':
					default:
						$next_day = strtotime( ' + 1 day', $current_day );
				}

				$is_available = $this->is_available(
					array_merge(
						$is_available_args,
						array(
							'from'         => $current_day,
							'exclude_time' => true,
						)
					)
				);

				if ( $disable_day_if_no_time && $this->has_time() ) {
					$check = true;

					if ( apply_filters( 'yith_wcbk_disable_day_if_no_time_available_check_only_if_bookings_exist', true ) && ! $exclude_booked_param ) {
						$count_args      = array(
							'product_id'        => $this->get_id(),
							'from'              => $current_day,
							'to'                => $next_day,
							'include_externals' => true,
							'exclude_booked'    => false,
						);
						$booked_bookings = yith_wcbk_booking_helper()->count_booked_bookings_in_period( $count_args );

						$count_args_for_filter = array_merge(
							$is_available_args,
							array(
								'from' => $current_day,
								'to'   => $next_day,
							)
						);
						$booked_bookings       = apply_filters( 'yith_wcbk_disable_day_if_no_time_available_bookings_count', $booked_bookings, $count_args_for_filter, $this );

						$check = ! ! $booked_bookings;
					}

					if ( $check ) {
						$this_is_available_args                   = $is_available_args;
						$this_is_available_args['exclude_booked'] = $exclude_booked_param;
						$is_available                             = $is_available && $this->create_availability_time_array( $current_day, 0, $this_is_available_args );
					}
				}

				switch ( $return ) {
					case 'bookable':
						if ( $is_available ) {
							$month_calendar[ $number_of_day ] = $is_available;
						}
						break;
					case 'not_bookable':
					case 'non_bookable':
						if ( ! $is_available ) {
							$month_calendar[ $number_of_day ] = $is_available;
						}
						break;
					default:
						$month_calendar[ $number_of_day ] = $is_available;

				}
				$current_day = $next_day;
			}

			return $month_calendar;
		}

		/*
		|--------------------------------------------------------------------------
		| Other Methods
		|--------------------------------------------------------------------------
		*/

		/**
		 * Load external calendars if not already loaded
		 *
		 * @since 2.0.0
		 */
		public function maybe_load_externals() {
			if ( $this->has_external_calendars() && ! $this->has_externals_synchronized() && yith_wcbk_is_external_sync_module_active() ) {
				$this->get_externals();
			}
		}

		/**
		 * Regenerate product data
		 *
		 * @param array $data Data.
		 */
		public function regenerate_data( $data = array() ) {
			$time_debug_key = __FUNCTION__ . '_' . $this->get_id();
			yith_wcbk_time_debug_start( $time_debug_key );
			if ( ! $data ) {
				$data = array( 'externals', 'not-available-dates' );
			}

			$data_debug = PHP_EOL . 'Data regenerated for ' . implode( ', ', $data );

			if ( in_array( 'externals', $data, true ) ) {
				$this->maybe_load_externals();
			}

			if ( in_array( 'not-available-dates', $data, true ) ) {
				$date_info           = yith_wcbk_get_booking_form_date_info( $this );
				$non_available_dates = $this->get_non_available_dates( $date_info['current_year'], $date_info['current_month'], $date_info['next_year'], $date_info['next_month'] );

				$data_debug .= PHP_EOL . 'Non-available dates: ' . print_r( $non_available_dates, true ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}

			$seconds = yith_wcbk_time_debug_end( $time_debug_key );
			yith_wcbk_maybe_debug( sprintf( 'Product Data regenerated for product #%s (%s seconds taken) %s', $this->get_id(), $seconds, $data_debug ) );

			do_action( 'yith_wcbk_booking_product_after_regenerating_data', $data, $this );
		}

		/*
		|--------------------------------------------------------------------------
		| Updaters and Deleters
		|--------------------------------------------------------------------------
		*/

		/**
		 * Update location coordinates based on product location
		 */
		public function update_location_coordinates() {
			$location  = $this->get_location();
			$latitude  = '';
			$longitude = '';
			$maps      = yith_wcbk()->maps();

			if ( $maps ) {
				if ( $location ) {
					$coordinates = $maps->get_location_by_address( $location );
					if ( isset( $coordinates['lat'] ) && isset( $coordinates['lng'] ) ) {
						$latitude  = $coordinates['lat'];
						$longitude = $coordinates['lng'];
					}
				}

				// save changes only if needed.
				if ( $this->get_location_latitude( 'edit' ) !== $latitude || $this->get_location_longitude( 'edit' ) !== $longitude ) {
					$this->set_location_latitude( $latitude );
					$this->set_location_longitude( $longitude );

					/**
					 * Cloned product to store changes directly to DB
					 *
					 * @var WC_Product_Booking $clone_product
					 */
					$clone_product = wc_get_product( $this );
					if ( $clone_product ) {
						$clone_product->set_location_latitude( $latitude );
						$clone_product->set_location_longitude( $longitude );
						$clone_product->save();
					}
				}
			}
		}
	}
}
