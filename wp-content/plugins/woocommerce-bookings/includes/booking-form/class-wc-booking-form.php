<?php
/**
 * Booking form class
 */
class WC_Booking_Form {

	/**
	 * Booking product data.
	 * @var WC_Product_Booking
	 */
	public $product;

	/**
	 * Booking fields.
	 * @var array
	 */
	private $fields;

	/**
	 * Constructor
	 * @param $product WC_Product_Booking
	 */
	public function __construct( $product ) {
		$this->product = $product;
	}

	/**
	 * Booking form scripts
	 */
	public function scripts() {
		global $wp_locale;

		$wc_bookings_booking_form_args = array(
			'closeText'                  => __( 'Close', 'woocommerce-bookings' ),
			'currentText'                => __( 'Today', 'woocommerce-bookings' ),
			'prevText'                   => __( 'Previous', 'woocommerce-bookings' ),
			'nextText'                   => __( 'Next', 'woocommerce-bookings' ),
			'monthNames'                 => array_values( $wp_locale->month ),
			'monthNamesShort'            => array_values( $wp_locale->month_abbrev ),
			'dayNames'                   => array_values( $wp_locale->weekday ),
			'dayNamesShort'              => array_values( $wp_locale->weekday_abbrev ),
			'dayNamesMin'                => array_values( $wp_locale->weekday_initial ),
			'firstDay'                   => get_option( 'start_of_week' ),
			'current_time'               => date( 'Ymd', current_time( 'timestamp' ) ),
			'check_availability_against' => $this->product->get_check_start_block_only() ? 'start' : '',
			'duration_type'              => $this->product->get_duration_type(),
			'duration_unit'              => $this->product->get_duration_unit(),
			'resources_assignment'       => ! $this->product->has_resources() ? 'customer' : $this->product->get_resources_assignment(),
			'isRTL'                      => is_rtl(),
			'product_id'                 => $this->product->get_id(),
			'default_availability'       => $this->product->get_default_availability(),
		);

		$wc_bookings_date_picker_args = array(
			'ajax_url'                   => WC_Ajax_Compat::get_endpoint( 'wc_bookings_find_booked_day_blocks' ),
		);

		if ( in_array( $this->product->get_duration_unit(), array( 'minute', 'hour' ) ) ) {
			$wc_bookings_booking_form_args['booking_duration'] = 1;
		} else {
			$wc_bookings_booking_form_args['booking_duration']         = $this->product->get_duration();
			$wc_bookings_booking_form_args['booking_duration_type']    = $this->product->get_duration_type();

			if ( 'customer' == $wc_bookings_booking_form_args['booking_duration_type'] ) {
				$wc_bookings_booking_form_args['booking_min_duration'] = $this->product->get_min_duration();
				$wc_bookings_booking_form_args['booking_max_duration'] = $this->product->get_max_duration();
			} else {
				$wc_bookings_booking_form_args['booking_min_duration'] = $wc_bookings_booking_form_args['booking_duration'];
				$wc_bookings_booking_form_args['booking_max_duration'] = $wc_bookings_booking_form_args['booking_duration'];
			}
		}

		wp_enqueue_script( 'wc-bookings-booking-form', WC_BOOKINGS_PLUGIN_URL . '/dist/frontend.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-datepicker', 'underscore' ), WC_BOOKINGS_VERSION, true );
		wp_localize_script( 'wc-bookings-booking-form', 'wc_bookings_booking_form', $wc_bookings_booking_form_args );
		wp_localize_script( 'wc-bookings-booking-form', 'wc_bookings_date_picker_args', $wc_bookings_date_picker_args );
		wp_enqueue_script( 'wc-bookings-moment', WC_BOOKINGS_PLUGIN_URL . '/dist/js/lib/moment-with-locales.js', array(), WC_BOOKINGS_VERSION, true );
		wp_enqueue_script( 'wc-bookings-moment-timezone', WC_BOOKINGS_PLUGIN_URL . '/dist/js/lib/moment-timezone-with-data.js', array(), WC_BOOKINGS_VERSION, true );

		// Variables for JS scripts
		$booking_form_params = array(
			'cache_ajax_requests'        => 'false',
			'nonce'                      => array(
				'get_end_time_html' => wp_create_nonce( 'get_end_time_html' ),
			),
			'ajax_url'                   => admin_url( 'admin-ajax.php' ),
			'i18n_date_unavailable'      => __( 'This date is unavailable', 'woocommerce-bookings' ),
			'i18n_date_fully_booked'     => __( 'This date is fully booked and unavailable', 'woocommerce-bookings' ),
			'i18n_date_partially_booked' => __( 'This date is partially booked - but bookings still remain', 'woocommerce-bookings' ),
			'i18n_date_available'        => __( 'This date is available', 'woocommerce-bookings' ),
			'i18n_start_date'            => __( 'Choose a Start Date', 'woocommerce-bookings' ),
			'i18n_end_date'              => __( 'Choose an End Date', 'woocommerce-bookings' ),
			'i18n_dates'                 => __( 'Dates', 'woocommerce-bookings' ),
			'i18n_choose_options'        => __( 'Please select the options for your booking and make sure duration rules apply.', 'woocommerce-bookings' ),
			'i18n_clear_date_selection'  => __( 'To clear selection, pick a new start date', 'woocommerce-bookings' ),
			'pao_pre_30'                 => ( defined( 'WC_PRODUCT_ADDONS_VERSION' ) && version_compare( WC_PRODUCT_ADDONS_VERSION, '3.0', '<' ) ) ? 'true' : 'false',
			'pao_active'                 => class_exists( 'WC_Product_Addons' ),
			'timezone_conversion'        => wc_should_convert_timezone(),
			'client_firstday'            => 'yes' === WC_Bookings_Timezone_Settings::get( 'use_client_firstday' ),
			'server_timezone'            => wc_booking_get_timezone_string(),
			'server_time_format'         => wc_bookings_convert_to_moment_format( wc_bookings_time_format() ),
			'i18n_store_server_time'     => esc_js( __( 'Store server time: ', 'woocommerce-bookings' ) ),
		);

		wp_localize_script( 'wc-bookings-booking-form', 'booking_form_params', apply_filters( 'booking_form_params', $booking_form_params ) );
	}

	/**
	 * Attempt to convert a date formatting string from PHP to Moment
	 *
	 * @deprecated 1.15.0
	 * @param string $format
	 * @return string
	 */
	protected function convert_to_moment_format( $format ) {
		wc_deprecated_function( __METHOD__, '1.15.0', 'wc_bookings_convert_to_moment_format' );

		return wc_bookings_convert_to_moment_format( $format );
	}

	/**
	 * Prepare fields for the booking form
	 */
	public function prepare_fields() {
		// Destroy existing fields
		$this->reset_fields();

		// Add fields in order
		$this->duration_field();
		$this->persons_field();
		$this->resources_field();
		$this->date_field();

		$this->fields = apply_filters( 'booking_form_fields', $this->fields );
	}

	/**
	 * Reset fields array
	 */
	public function reset_fields() {
		$this->fields = array();
	}

	/**
	 * Add duration field to the form
	 */
	private function duration_field() {
		// Customer defined bookings
		if ( 'customer' === $this->product->get_duration_type() ) {
			$after = '';
			switch ( $this->product->get_duration_unit() ) {
				case 'month':
					if ( $this->product->get_duration() > 1 ) {
						/* translators: %s: product duration in months */
						$after = sprintf( __( '&times; %s Months', 'woocommerce-bookings' ), $this->product->get_duration() );
					} else {
						$after = __( 'Month(s)', 'woocommerce-bookings' );
					}
					break;
				case 'week':
					if ( $this->product->get_duration() > 1 ) {
						/* translators: %s: product duration in weeks */
						$after = sprintf( __( '&times; %s weeks', 'woocommerce-bookings' ), $this->product->get_duration() );
					} else {
						$after = __( 'Week(s)', 'woocommerce-bookings' );
					}
					break;
				case 'day':
					if ( $this->product->get_duration() % 7 ) {
						if ( $this->product->get_duration() > 1 ) {
							/* translators: %s product duration in days */
							$after = sprintf( __( '&times; %s days', 'woocommerce-bookings' ), $this->product->get_duration() );
						} else {
							$after = __( 'Day(s)', 'woocommerce-bookings' );
						}
					} else {
						if ( 1 == ( $this->product->get_duration() / 7 ) ) {
							$after = __( 'Week(s)', 'woocommerce-bookings' );
						} else {
							/* translators: %s: product duration in weeks */
							$after = sprintf( __( '&times; %s weeks', 'woocommerce-bookings' ), $this->product->get_duration() / 7 );
						}
					}
					break;
				case 'night':
					if ( $this->product->get_duration() > 1 ) {
						/* translators: %s: product duration in nights */
						$after = sprintf( __( '&times; %s nights', 'woocommerce-bookings' ), $this->product->get_duration() );
					} else {
						$after = __( 'Night(s)', 'woocommerce-bookings' );
					}
					break;
				case 'hour':
					if ( $this->product->get_duration() > 1 ) {
						/* translators: %s: product duration in hours */
						$after = sprintf( __( '&times; %s hours', 'woocommerce-bookings' ), $this->product->get_duration() );
					} else {
						$after = __( 'Hour(s)', 'woocommerce-bookings' );
					}
					break;
				case 'minute':
					if ( $this->product->get_duration() > 1 ) {
						/* translators: %s: product duration in minutes */
						$after = sprintf( __( '&times; %s minutes', 'woocommerce-bookings' ), $this->product->get_duration() );
					} else {
						$after = __( 'Minute(s)', 'woocommerce-bookings' );
					}
					break;
			}

			$this->add_field( array(
				'type'  => 'number',
				'name'  => 'duration',
				'label' => __( 'Duration', 'woocommerce-bookings' ),
				'after' => $after,
				'min'   => $this->product->get_min_duration(),
				'max'   => $this->product->get_max_duration(),
				'step'  => 1,
			) );
		}
	}

	/**
	 * Add persons field
	 */
	private function persons_field() {
		// Persons field
		if ( $this->product->has_persons() ) {

			// Get the max persons now to use for all person types
			$max_persons = $this->product->get_max_persons() ? $this->product->get_max_persons() : '';

			if ( $this->product->has_person_types() ) {
				$person_types = $this->product->get_person_types();

				foreach ( $person_types as $person_type ) {
					$min_person_type_persons = $person_type->get_min();
					$max_person_type_persons = $person_type->get_max();

					$this->add_field( array(
						'type'  => 'number',
						'step'  => 1,
						'min'   => is_numeric( $min_person_type_persons ) ? $min_person_type_persons : 0,
						'max'   => ! empty( $max_person_type_persons ) ? absint( $max_person_type_persons ) : $max_persons,
						'name'  => 'persons_' . $person_type->get_id(),
						'label' => $person_type->get_name(),
						'after' => $person_type->get_description(),
					) );
				}
			} else {
				$this->add_field( array(
					'type'  => 'number',
					'step'  => 1,
					'min'   => $this->product->get_min_persons(),
					'max'   => $max_persons,
					'name'  => 'persons',
					'label' => __( 'Persons', 'woocommerce-bookings' ),
				) );
			}
		}
	}

	/**
	 * Add resources field
	 */
	private function resources_field() {
		// Resources field
		if ( ! $this->product->has_resources() || ! $this->product->is_resource_assignment_type( 'customer' ) ) {
			return;
		}

		$resources          = $this->product->get_resources();
		$resource_options   = array();

		foreach ( $resources as $resource ) {
			$cost_plus_base  = $resource->get_base_cost() + $this->product->get_block_cost() + $this->product->get_cost();
			$additional_cost = array();

			if ( $resource->get_base_cost() && $this->product->get_block_cost() < $cost_plus_base ) {
				// if display cost price is set, don't calculate the difference
				if ( '' !== $this->product->get_display_cost() ) {
					$additional_cost[] = '+' . wp_strip_all_tags( wc_price( $cost_plus_base ) );
				} else {
					$additional_cost[] = '+' . wp_strip_all_tags( wc_price( (float) $resource->get_base_cost() ) );
				}
			}

			if ( $resource->get_block_cost() && ! $this->product->get_display_cost() ) {
				$duration      = $this->product->get_duration();
				$duration_unit = $this->product->get_duration_unit();
				
				if ( in_array( $duration_unit, array( 'minute', 'hour' ) ) ) {
					$duration_unit = _n( 'block', 'blocks', $duration, 'woocommerce-bookings' );
				} else if ( in_array( $duration_unit, array( 'day') ) ) {
					$duration_unit = _n( 'day', 'days', $duration, 'woocommerce-bookings' );
				}

				// Check for singular display.
				if ( 1 == $duration ) {
					$duration_display = sprintf( '%s', $duration_unit );
				} else {
					// Plural.
					$duration_display = sprintf( '%d %s', $duration, $duration_unit );
				}

				$duration_display = apply_filters( 'woocommerce_bookings_resource_duration_display_string', $duration_display, $this->product );

				/* translators: 1: block cost 2: duration unit */
				$additional_cost[] = sprintf( __( '+%1$1s per %2$2s', 'woocommerce-bookings' ), wp_strip_all_tags( wc_price( $resource->get_block_cost() ) ), $duration_display );
			}

			if ( $additional_cost ) {
				$additional_cost_string = ' (' . implode( ', ', $additional_cost ) . ')';
			} else {
				$additional_cost_string = '';
			}

			$resource_options[ $resource->ID ] = $resource->post_title . apply_filters( 'woocommerce_bookings_resource_additional_cost_string', $additional_cost_string, $resource );
		}

		$label = $this->product->get_resource_label() ? $this->product->get_resource_label() : __( 'Type', 'woocommerce-bookings' );
		$this->add_field( array(
			'type'    => 'select',
			'name'    => 'resource',
			'label'   => $label,
			'class'   => array( 'wc_booking_field_' . sanitize_title( $this->product->get_resource_label() ) ),
			'options' => $resource_options,
		) );
	}

	/**
	 * Add the date field to the booking form
	 */
	private function date_field() {
		$picker = null;

		// Get date picker specific to the duration unit for this product
		switch ( $this->product->get_duration_unit() ) {
			case 'month':
				include_once 'class-wc-booking-form-month-picker.php';
				$picker = new WC_Booking_Form_Month_Picker( $this );
				break;
			case 'day':
			case 'night':
				include_once 'class-wc-booking-form-date-picker.php';
				$picker = new WC_Booking_Form_Date_Picker( $this );
				break;
			case 'minute':
			case 'hour':
				include_once 'class-wc-booking-form-datetime-picker.php';
				$picker = new WC_Booking_Form_Datetime_Picker( $this );
				break;
			default:
				break;
		}

		if ( ! is_null( $picker ) ) {
			$this->add_field( $picker->get_args() );
		}
	}

	/**
	 * Add Field
	 * @param  array $field
	 * @return void
	 */
	public function add_field( $field ) {
		$default = array(
			'name'  => '',
			'class' => array(),
			'label' => '',
			'type'  => 'text',
		);

		$field = wp_parse_args( $field, $default );

		if ( ! $field['name'] || ! $field['type'] ) {
			return;
		}

		$nicename = 'wc_bookings_field_' . sanitize_title( $field['name'] );

		$field['name']    = $nicename;
		$field['class'][] = $nicename;

		$this->fields[ sanitize_title( $field['name'] ) ] = $field;
	}

	/**
	 * Output the form - called from the add to cart templates
	 */
	public function output() {
		$this->scripts();
		$this->prepare_fields();

		foreach ( $this->fields as $key => $field ) {
			if ( ( 'hour' === $this->product->get_duration_unit() || 'minute' === $this->product->get_duration_unit() ) && 'wc_bookings_field_duration' === $field['name'] ) {
				continue;
			}

			wc_get_template( 'booking-form/' . $field['type'] . '.php', array( 'field' => $field, 'product' => $this->product ), 'woocommerce-bookings', WC_BOOKINGS_TEMPLATE_PATH );
		}
	}

	/**
	 * Get posted form data into a neat array
	 * @param  array $posted
	 * @return array
	 */
	public function get_posted_data( $posted = array() ) {
		wc_deprecated_function( __METHOD__, '1.15.0', 'wc_bookings_get_posted_data()' );

		return wc_bookings_get_posted_data( $posted, $this->product );
	}

	/**
	 * Checks booking data is correctly set, and that the chosen blocks are indeed available.
	 *
	 * @deprecated 1.15.0
	 * @param  array $data
	 * @return bool|WP_Error on failure, true on success
	 */
	public function is_bookable( $data ) {
		wc_deprecated_function( __METHOD__, '1.15.0', 'WC_Product_Booking::is_bookable()' );

		return $this->product->is_bookable( $data );
	}

	/**
	 * Get an array of formatted time values.
	 *
	 * @deprecated  1.15.0
	 * @param  string $timestamp
	 * @return array
	 */
	public function get_formatted_times( $timestamp ) {
		wc_deprecated_function( __METHOD__, '1.15.0', 'wc_bookings_get_formatted_times()' );

		return wc_bookings_get_formatted_times( $timestamp );
	}

	/**
	 * Calculate costs from posted values.
	 *
	 * @deprecated 1.15.0
	 * @param  array $posted
	 * @return string cost
	 */
	public function calculate_booking_cost( $posted ) {
		wc_deprecated_function( __METHOD__, '1.15.0', 'WC_Bookings_Cost_Calculation::calculate_booking_cost()' );

		$data = wc_bookings_get_posted_data( $posted, $this->product );

		return apply_filters( 'booking_form_calculated_booking_cost', WC_Bookings_Cost_Calculation::calculate_booking_cost( $data, $this->product ), $this, $posted );
	}

	/**
	 * Builds the HTML to display the start time for hours/minutes.
	 *
	 * @since 1.13.0
	 * @param  array  $blocks
	 * @param  array  $intervals
	 * @param  integer $resource_id
	 * @param  integer $from The starting date for the set of blocks
	 * @param  integer $to
	 * @param  array $available_blocks
	 * @return string
	 *
	 */
	public function get_start_time_html( $blocks, $intervals = array(), $resource_id = 0, $from = 0, $to = 0 ) {
		$transient_name   = 'book_st_' . md5( http_build_query( array( $from, $to, $this->product->get_id(), $resource_id ) ) );
		$st_block_html    = WC_Bookings_Cache::get( $transient_name );
		$available_blocks = wc_bookings_get_time_slots( $this->product, $blocks, $intervals, $resource_id, $from, $to );
		$escaped_blocks   = function_exists( 'wc_esc_json' ) ? wc_esc_json( wp_json_encode( $blocks ) ) : _wp_specialchars( wp_json_encode( $blocks ), ENT_QUOTES, 'UTF-8', true );
		$block_html       = '';
		$block_html      .= '<div class="wc-bookings-start-time-container" data-product-id="' . esc_attr( $this->product->get_id() ) . '" data-blocks="' . $escaped_blocks . '">';
		$block_html      .= '<label for="wc-bookings-form-start-time">' . esc_html__( 'Starts', 'woocommerce-bookings' ) . '</label>';
		$block_html      .= '<select id="wc-bookings-form-start-time" name="start_time">';
		$block_html      .= '<option value="0">' . esc_html__( 'Start time', 'woocommerce-bookings' ) . '</option>';

		$booking_slots_transient_keys = array_filter( (array) WC_Bookings_Cache::get( 'booking_slots_transient_keys' ) );

		if ( ! isset( $booking_slots_transient_keys[ $this->product->get_id() ] ) ) {
			$booking_slots_transient_keys[ $this->product->get_id() ] = array();
		}

		$booking_slots_transient_keys[ $this->product->get_id() ][] = $transient_name;

		// Give array of keys a long ttl because if it expires we won't be able to flush the keys when needed.
		// We can't use 0 to never expire because then WordPress will autoload the option on every page.
		WC_Bookings_Cache::set( 'booking_slots_transient_keys', $booking_slots_transient_keys, YEAR_IN_SECONDS );

		if ( false === $st_block_html ) {
			$st_block_html = '';

			foreach ( $available_blocks as $block => $quantity ) {
				if ( $quantity['available'] > 0 ) {
					$data = $this->get_end_times( $blocks, get_time_as_iso8601( $block ), $intervals, $resource_id, $from, $to, true );

					// If this block does not have any end times, skip rendering the time
					if ( empty( $data ) ) {
						continue;
					}

					if ( $quantity['booked'] ) {
						/* translators: 1: quantity available */
						$st_block_html .= '<option data-block="' . esc_attr( date( 'Hi', $block ) ) . '" data-remaining="' . sprintf( _n( '%d left', '%d left', $quantity['available'], 'woocommerce-bookings' ), absint( $quantity['available'] ) ) . '" value="' . esc_attr( get_time_as_iso8601( $block ) ) . '">' . date_i18n( wc_bookings_time_format(), $block ) . ' (' . sprintf( _n( '%d left', '%d left', $quantity['available'], 'woocommerce-bookings' ), absint( $quantity['available'] ) ) . ')</option>';
					} else {
						$st_block_html .= '<option data-block="' . esc_attr( date( 'Hi', $block ) ) . '" value="' . esc_attr( get_time_as_iso8601( $block ) ) . '">' . date_i18n( wc_bookings_time_format(), $block ) . '</option>';
					}
				}
			}

			WC_Bookings_Cache::set( $transient_name, $st_block_html );
		}

		$block_html .= $st_block_html;
		$block_html .= '</select></div>&nbsp;&nbsp;';

		return $block_html;
	}

	/**
	 * Builds the data to display the end time for hours/minutes.
	 *
	 * @since 1.13.0
	 * @param  array  $blocks
	 * @param  string $start_date_time Date of the start time.
	 * @param  array  $intervals
	 * @param  integer $resource_id
	 * @param  integer $from The starting date for the set of blocks
	 * @param  integer $to
	 * @param  bool    $check Whether to just check if there's any data at all.
	 * @return array
	 *
	 */
	public function get_end_times( $blocks, $start_date_time = '', $intervals = array(), $resource_id = 0, $from = 0, $to = 0, $check = false ) {
		$min_duration     = ! empty( $this->product->get_min_duration() ) ? $this->product->get_min_duration() : 1;
		$max_duration     = ! empty( $this->product->get_max_duration() ) ? $this->product->get_max_duration() : 1;
		$product_duration = ! empty( $this->product->get_duration() ) ? $this->product->get_duration() : 1;
		$start_time       = ! empty( $start_date_time ) ? strtotime( substr( $start_date_time, 0, 19 ) ) : '';
		$data             = array();

		if ( empty( $start_time ) ) {
			return $data;
		}

		$first_duration_multiple = intval( $product_duration ) * intval( $min_duration );
		$first_time_slot         = strtotime( '+ ' . $first_duration_multiple . ' ' . $this->product->get_duration_unit(), $start_time );

		if ( ! in_array( $start_time, $blocks ) ) {
			return $data;
		}

		$calc_avail    = true;
		$base_interval = $product_duration * ( 'hour' === $this->product->get_duration_unit() ? 60 : 1 );

		if ( $check ) {
			$intervals        = array( $min_duration * $base_interval, $base_interval );
			$available_blocks = wc_bookings_get_total_available_bookings_for_range( $this->product, $start_time, $first_time_slot, $resource_id, 1, $intervals );

			return ! is_wp_error( $available_blocks ) && $available_blocks && in_array( $start_time, $blocks );
		}

		for ( $duration_index = $max_duration; $duration_index >= $min_duration; $duration_index-- ) {
			$end_time = strtotime( '+ ' . $duration_index * $product_duration . ' ' . $this->product->get_duration_unit(), $start_time );

			// Check if $end_time is bookable by rules.
			if ( ! WC_Product_Booking_Rule_Manager::check_availability_rules_against_time( $start_time, $end_time, $resource_id, $this->product ) ) {
				continue;
			}

			// Just need to calculate availability for max duration. If that is available, anything below it will also be.
			if ( $calc_avail ) {
				$intervals        = array( $duration_index * $base_interval, $base_interval );
				$available_blocks = wc_bookings_get_total_available_bookings_for_range( $this->product, $start_time, $end_time, $resource_id, 1, $intervals );

				// If there are no available blocks, skip this block
				if ( is_wp_error( $available_blocks ) || ! $available_blocks ) {
					continue;
				}

				$calc_avail = false;
			}

			$duration_units = ( $end_time - $start_time ) / 60;
			/* translators: %d: booking duration in minutes */
			$display = ' (' . sprintf( _n( '%d Minute', '%d Minutes', $duration_units, 'woocommerce-bookings' ), $duration_units ) . ')';
			if ( 'hour' === $this->product->get_duration_unit() ) {
				$duration_units /= 60;
				/* translators: %d: booking duration in hours */
				$display = ' (' . sprintf( _n( '%d Hour', '%d Hours', $duration_units, 'woocommerce-bookings' ), $duration_units ) . ')';
			}

			$data[] = array(
				'display'  => $display,
				'end_time' => $end_time,
				'duration' => $duration_units / $this->product->get_duration(),
			);
		}

		return array_reverse( $data );
	}

	/**
	 * Renders the HTML to display the end time for hours/minutes.
	 *
	 * @since 1.13.0
	 * @param  array  $blocks
	 * @param  string $start_date_time Date of the start time.
	 * @param  array  $intervals
	 * @param  integer $resource_id
	 * @param  integer $from The starting date for the set of blocks
	 * @param  integer $to
	 * @return string
	 *
	 */
	public function get_end_time_html( $blocks, $start_date_time = '', $intervals = array(), $resource_id = 0, $from = 0, $to = 0 ) {
		$block_html  = '';
		$block_html .= '<div class="wc-bookings-end-time-container">';
		$block_html .= '<label for="wc-bookings-form-end-time">' . esc_html__( 'Ends', 'woocommerce-bookings' ) . '</label>';
		$block_html .= '<select id="wc-bookings-form-end-time" name="end_time">';
		$block_html .= '<option value="0">' . esc_html__( 'End time', 'woocommerce-bookings' ) . '</option>';

		$data = $this->get_end_times( $blocks, $start_date_time, $intervals, $resource_id, $from, $to );

		foreach ( $data as $booking_data ) {
			$display  = $booking_data['display'];
			$end_time = $booking_data['end_time'];
			$duration = $booking_data['duration'];

			$block_html .= '<option data-duration-display="' . esc_attr( $display ) . '" data-value="' . get_time_as_iso8601( $end_time ) . '" value="' . esc_attr( $duration ) . '">' . date_i18n( wc_bookings_time_format(), $end_time ) . $display . '</option>';
		}

		$block_html .= '</select></div>';

		return $block_html;
	}

	/**
	 * Find available blocks and return HTML for the user to choose a block. Used in class-wc-bookings-ajax.php.
	 *
	 * @param  array  $blocks
	 * @param  array  $intervals
	 * @param  integer $resource_id
	 * @param  integer $from The starting date for the set of blocks
	 * @param  integer $to
	 * @return string
	 *
	 * @version  1.10.7
	 */
	public function get_time_slots_html( $blocks, $intervals = array(), $resource_id = 0, $from = 0, $to = 0 ) {
		$block_html       = '';
		$available_blocks = wc_bookings_get_time_slots( $this->product, $blocks, $intervals, $resource_id, $from, $to );

		// If customer defined, we show two dropdowns start/end time.
		if ( 'customer' === $this->product->get_duration_type() ) {
			$block_html .= $this->get_start_time_html( $blocks, $intervals, $resource_id, $from, $to, $available_blocks );
			$block_html .= $this->get_end_time_html( $blocks, '', $intervals, $resource_id, $from, $to );
		} else {
			foreach ( $available_blocks as $block => $quantity ) {
				if ( $quantity['available'] > 0 ) {
					if ( $quantity['booked'] ) {
						/* translators: 1: quantity available */
						$block_html .= '<li class="block" data-block="' . esc_attr( date( 'Hi', $block ) ) . '" data-remaining="' . esc_attr( $quantity['available'] ) . '" ><a href="#" data-value="' . get_time_as_iso8601( $block ) . '">' . date_i18n( wc_bookings_time_format(), $block ) . ' <small class="booking-spaces-left">(' . sprintf( _n( '%d left', '%d left', $quantity['available'], 'woocommerce-bookings' ), absint( $quantity['available'] ) ) . ')</small></a></li>';
					} else {
						$block_html .= '<li class="block" data-block="' . esc_attr( date( 'Hi', $block ) ) . '"><a href="#" data-value="' . get_time_as_iso8601( $block ) . '">' . date_i18n( wc_bookings_time_format(), $block ) . '</a></li>';
					}
				}
			}
		}

		return apply_filters( 'wc_bookings_get_time_slots_html', $block_html, $available_blocks, $blocks );
	}
}
