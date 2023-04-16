<?php
/**
 * Gets bookings
 *
 * @package WooCommerce Bookings
 */

/**
 * WC_Bookings_Controller class.
 */
class WC_Bookings_Controller {

	/**
	 * Return all bookings for a product in a given range
	 *
	 * @param integer $start_date Start date.
	 * @param integer $end_date End date.
	 * @param mixed   $product_or_resource_ids Product or resource IDs.
	 * @param bool    $check_in_cart Check in cart.
	 *
	 * @return array Array of bookings.
	 */
	public static function get_bookings_in_date_range( $start_date, $end_date, $product_or_resource_ids = 0, $check_in_cart = true ) {
		wc_deprecated_function( __METHOD__, '1.15.0', 'WC_Booking_Data_Store::get_bookings_in_date_range' );
		return WC_Booking_Data_Store::get_bookings_in_date_range( $start_date, $end_date, $product_or_resource_ids, $check_in_cart );
	}

	/**
	 * Return all bookings and blocked availability for a product in a given range.
	 *
	 * @param integer $start_date              Start date.
	 * @param integer $end_date                End date.
	 * @param mixed   $product_or_resource_ids Product or resource IDs.
	 * @param bool    $check_in_cart           Flag to Check whether include in-cart bookings in query.
	 *
	 * @return array
	 */
	public static function get_events_in_date_range( $start_date, $end_date, $product_or_resource_ids = 0, $check_in_cart = true ) {
		wc_deprecated_function( __METHOD__, '1.15.0', 'WC_Global_Availability_Data_Store::get_events_in_date_range' );
		return WC_Global_Availability_Data_Store::get_events_in_date_range( $start_date, $end_date, $product_or_resource_ids, $check_in_cart );
	}

	/**
	 * Return an array global_availability_rules
	 *
	 * @since 1.13.0
	 *
	 * @param  int $start_date Start date.
	 * @param  int $end_date End date.
	 *
	 * @return array Days that are buffer days and therefor should be un-bookable
	 */
	public static function get_global_availability_in_date_range( $start_date, $end_date ) {
		wc_deprecated_function( __METHOD__, '1.15.0', 'WC_Global_Availability_Data_Store::get_global_availability_in_date_range' );
		return WC_Global_Availability_Data_Store::get_global_availability_in_date_range( $start_date, $end_date );
	}

	/**
	 * Return an array of un-bookable buffer days
	 *
	 * @since 1.9.13
	 *
	 * @param WC_Product_Booking|int $bookable_product Product or product ID.
	 *
	 * @return array Days that are buffer days and therefor should be un-bookable
	 * @throws Exception When product is not a bookable product.
	 */
	public static function find_buffer_day_blocks( $bookable_product ) {
		if ( is_int( $bookable_product ) ) {
			$bookable_product = wc_get_product( $bookable_product );
		}
		if ( ! is_a( $bookable_product, 'WC_Product_Booking' ) ) {
			return array();
		}
		$booked = self::find_booked_day_blocks( $bookable_product );
		return self::get_buffer_day_blocks_for_booked_days( $bookable_product, $booked['fully_booked_days'] );
	}

	/**
	 * Return an array of un-bookable buffer days
	 *
	 * @since 1.9.13
	 *
	 * @param WC_Product_Booking|int $bookable_product  Product or product ID.
	 * @param array                  $fully_booked_days Array of fully booked days.
	 *
	 * @return array Days that are buffer days and therefor should be un-bookable
	 */
	public static function get_buffer_day_blocks_for_booked_days( $bookable_product, $fully_booked_days ) {
		if ( is_int( $bookable_product ) ) {
			$bookable_product = wc_get_product( $bookable_product );
		}
		if ( ! is_a( $bookable_product, 'WC_Product_Booking' ) ) {
			return array();
		}

		$buffer_period = $bookable_product->get_buffer_period();
		$buffer_days   = array();

		foreach ( $fully_booked_days as $date => $data ) {
			// Loop through each resource.
			foreach ( $data as $resource_id => $booked ) {
				$next_day = strtotime( '+1 day', strtotime( $date ) );

				// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
				if ( array_key_exists( date( 'Y-n-j', $next_day ), $fully_booked_days ) ) {
					continue;
				}

				// x days after.
				for ( $i = 1; $i < $buffer_period + 1; $i ++ ) {
					// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
					$buffer_day = date( 'Y-n-j', strtotime( "+{$i} day", strtotime( $date ) ) );

					$buffer_days[ $buffer_day ][ $resource_id ] = 1;
				}
			}
		}

		if ( $bookable_product->get_apply_adjacent_buffer() ) {
			foreach ( $fully_booked_days as $date => $data ) {
				// Loop through each resource.
				foreach ( $data as $resource_id => $booked ) {
					$previous_day = strtotime( '-1 day', strtotime( $date ) );

					// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
					if ( array_key_exists( date( 'Y-n-j', $previous_day ), $fully_booked_days ) ) {
						continue;
					}

					// x days before.
					for ( $i = 1; $i < $buffer_period + 1; $i ++ ) {
						// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
						$buffer_day = date( 'Y-n-j', strtotime( "-{$i} day", strtotime( $date ) ) );

						$buffer_days[ $buffer_day ][ $resource_id ] = 1;
					}
				}
			}
		}

		return $buffer_days;
	}

	/**
	 * Finds existing bookings for a product and its tied resources.
	 *
	 * @param WC_Product_Booking $bookable_product Bookable product.
	 * @param int                $min_date         Min date.
	 * @param int                $max_date         Max date.
	 *
	 * @return array Array of existing bookings.
	 */
	public static function get_all_existing_bookings( $bookable_product, $min_date = 0, $max_date = 0 ) {
		wc_deprecated_function( __METHOD__, '1.15.0', 'WC_Booking_Data_Store::get_all_existing_bookings()' );

		return WC_Booking_Data_Store::get_all_existing_bookings( $bookable_product, $min_date, $max_date );
	}

	/**
	 * For hour bookings types check that the booking is past midnight and before start time.
	 * This only works for the very next day after booking start.
	 *
	 * @since 1.10.7
	 *
	 * @param WC_Booking         $booking    Booking object.
	 * @param WC_Product_Booking $product    Bookable product object.
	 * @param string             $check_date Date to check.
	 *
	 * @return boolean True if booking is past midnight and before start time.
	 */
	private static function is_booking_past_midnight_and_before_start_time( $booking, $product, $check_date ) {
		// This handles bookings overlapping midnight when slots only start
		// from a specific hour.
		$start_time = $product->get_first_block_time();

		return (
			'hour' === $product->get_duration_unit()
			&& ! empty( $start_time )
			// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
			&& date( 'md', $booking['end'] ) === ( date( 'md', $check_date ) )
			// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
			&& (int) str_replace( ':', '', $start_time ) > (int) date( 'Hi', $booking['end'] )
		);
	}

	/**
	 * Finds months which are fully booked.
	 *
	 * @param WC_Product_Booking|int $bookable_product Bookable product.
	 *
	 * @return array( 'fully_booked_months' ) Array of months which are fully booked.
	 * @throws Exception When the product is not a bookable product.
	 */
	public static function find_booked_month_blocks( $bookable_product ) {
		$booked_day_blocks = self::find_booked_day_blocks( $bookable_product, 0, 0, 'Y-n' );

		$booked_month_blocks = array(
			'fully_booked_months' => $booked_day_blocks['fully_booked_days'],
		);

		/**
		 * Filter the booked month blocks calculated per project.
		 *
		 * @since 1.11
		 *
		 * @param array $booked_month_blocks {
		 *  @type array $fully_booked_months
		 * }
		 * @param WC_Product $bookable_product
		 */
		return apply_filters( 'woocommerce_bookings_booked_month_blocks', $booked_month_blocks, $bookable_product );
	}

	/**
	 * Finds days which are partially booked & fully booked already.
	 *
	 * This function will get a general min/max Booking date, which initially is [today, today + 1 year]
	 * Based on the Bookings retrieved from that date, it will shrink the range to the [Bookings_min, Bookings_max]
	 * For the newly generated range, it will determine availability of dates by calling `wc_bookings_get_time_slots` on it.
	 *
	 * Depending on the data returned from it we set:
	 * Fully booked days     - for those dates that there are no more slot available
	 * Partially booked days - for those dates that there are some slots available
	 *
	 * @param WC_Product_Booking|int $bookable_product    Bookable product.
	 * @param int                    $min_date            Min date to check for bookings.
	 * @param int                    $max_date            Max date to check for bookings.
	 * @param string                 $default_date_format Date format to use for the returned array.
	 * @param int                    $timezone_offset     Timezone offset in hours.
	 * @param array                  $resource_ids        Array of resource ids.
	 *
	 * @return void|array( 'partially_booked_days', 'fully_booked_days' ) Array of booked days.
	 * @throws Exception When the product is not a bookable product.
	 */
	public static function find_booked_day_blocks( $bookable_product, $min_date = 0, $max_date = 0, $default_date_format = 'Y-n-j', $timezone_offset = 0, $resource_ids = array() ) {
		$booked_day_blocks = array(
			'partially_booked_days' => array(),
			'fully_booked_days'     => array(),
			'unavailable_days'      => array(),
		);

		$timezone_offset = $timezone_offset * HOUR_IN_SECONDS;

		if ( is_int( $bookable_product ) ) {
			$bookable_product = wc_get_product( $bookable_product );
		}

		if ( ! is_a( $bookable_product, 'WC_Product_Booking' ) ) {
			return $booked_day_blocks;
		}

		// Check if the first and the last days are available or not.
		// The first day may have some minimum bookable time in the future,
		// and the time is passed enough, leaving no more blocks available
		// for that day, In that case, already considered available first
		// day should be marked as unavailable. Same applies to the last day.
		$first_day_end   = strtotime( 'tomorrow', $min_date ) - 1;
		$last_day_starts = strtotime( 'midnight', $max_date );

		// For products without resources, pass 0 in $resource_ids to check product-level availability.
		$resource_ids_loop = count( $resource_ids ) ? $resource_ids : array( 0 );
		foreach ( $resource_ids_loop as $resource_id ) {
			// Get the blocks available after checking existing rules.
			$first_day_blocks = $bookable_product->get_blocks_in_range( $min_date, $first_day_end, array(), $resource_id );
			// Get the blocks available after checking existing bookings.
			$first_day_blocks = wc_bookings_get_time_slots( $bookable_product, $first_day_blocks, array(), $resource_id, $min_date, $first_day_end );

			// If no blocks available for the minimum bookable (first) day, mark it unavailable.
			if ( 0 === count( $first_day_blocks ) ) {
				// phpcs:disable WordPress.DateTime.RestrictedFunctions.date_date
				$min_date_format = date( $default_date_format, $min_date );

				$booked_day_blocks['unavailable_days'][ $min_date_format ][0] = 1;
				if ( $bookable_product->has_resources() ) {
					foreach ( $bookable_product->get_resources() as $resource ) {
						$booked_day_blocks['unavailable_days'][ $min_date_format ][ $resource->ID ] = 1;
					}
				}
			}

			// If $max_date and $last_day_starts are same, it means the $max_date
			// is reset and coming from the Calendar instead of the setting.
			// See WC_Bookings_WC_Ajax::find_booked_day_blocks for more information.
			// In this case, no need to check the last day availability.
			if ( $max_date === $last_day_starts ) {
				continue;
			}

			// Get the blocks available after checking existing rules.
			$last_day_blocks = $bookable_product->get_blocks_in_range( $last_day_starts, $max_date, array(), $resource_id );
			// Get the blocks available after checking existing bookings.
			$last_day_blocks = wc_bookings_get_time_slots( $bookable_product, $last_day_blocks, array(), $resource_id, $last_day_starts, $max_date );

			// If no blocks available for the maximum bookable (last) day, mark it unavailable.
			if ( 0 === count( $last_day_blocks ) ) {
				// phpcs:disable WordPress.DateTime.RestrictedFunctions.date_date
				$max_date_format = date( $default_date_format, $max_date );

				$booked_day_blocks['unavailable_days'][ $max_date_format ][0] = 1;
				if ( $bookable_product->has_resources() ) {
					foreach ( $bookable_product->get_resources() as $resource ) {
						$booked_day_blocks['unavailable_days'][ $max_date_format ][ $resource->ID ] = 1;
					}
				}
			}
		}

		// Get existing bookings and go through them to set partial/fully booked days.
		$existing_bookings = WC_Booking_Data_Store::get_all_existing_bookings( $bookable_product, $min_date, $max_date );

		if ( empty( $existing_bookings ) ) {
			return $booked_day_blocks;
		}

		// phpcs:disable WordPress.DateTime.CurrentTimeTimestamp.Requested
		$min_booking_date = strtotime( '+100 years', current_time( 'timestamp' ) );
		// phpcs:disable WordPress.DateTime.CurrentTimeTimestamp.Requested
		$max_booking_date = strtotime( '-100 years', current_time( 'timestamp' ) );
		$bookings         = array();
		$day_format       = 1 === (int) $bookable_product->get_qty() ? 'unavailable_days' : 'partially_booked_days';

		// Find the minimum and maximum booking dates and store the booking data in an array for further processing.
		foreach ( $existing_bookings as $existing_booking ) {
			if ( ! is_a( $existing_booking, 'WC_Booking' ) ) {
				continue;
			}
			$check_date    = strtotime( 'midnight', $existing_booking->get_start() + $timezone_offset );
			$check_date_to = strtotime( 'midnight', $existing_booking->get_end() + $timezone_offset );
			$resource_id   = $existing_booking->get_resource_id();

			if ( ! empty( $resource_ids ) && ! in_array( $resource_id, $resource_ids, true ) ) {
				continue;
			}

			// If it's a booking on the same day, move it before the end of the current day.
			if ( $check_date_to === $check_date ) {
				$check_date_to = strtotime( '+1 day', $check_date ) - 1;
			}

			$min_booking_date = min( $min_booking_date, $check_date );
			$max_booking_date = max( $max_booking_date, $check_date_to );

			// If the booking duration is day, make sure we add the (duration) days to unavailable days.
			// This will mark them as white on the calendar, since they are not fully booked, but rather
			// unavailable. The difference is that a booking extending to those days is allowed.
			if ( 1 < $bookable_product->get_duration() && 'day' === $bookable_product->get_duration_unit() ) {

				$amount_of_buffer_days = $bookable_product->get_buffer_period();

				if ( $bookable_product->get_apply_adjacent_buffer() ) {
					$amount_of_buffer_days *= 2;
				}

				$duration_with_buffer = $bookable_product->get_duration() + $amount_of_buffer_days;

				// This buffer only gets applied from the left hand side, the buffer on the right hand side will get processed elsewhere.
				$check_new_date = strtotime( '-' . ( $duration_with_buffer - 1 ) . ' days', $check_date );

				// Mark the days between the fake booking and the actual booking as unavailable.
				while ( $check_new_date < $check_date ) {
					// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
					$date_format = date( $default_date_format, $check_new_date );
					$booked_day_blocks[ $day_format ][ $date_format ][ $resource_id ] = 1;
					$check_new_date = strtotime( '+1 day', $check_new_date );
				}
			}

			$bookings[] = array(
				'start' => $check_date,
				'end'   => $check_date_to,
				'res'   => $resource_id,
			);
		}

		$max_booking_date = strtotime( '+1 day', $max_booking_date );

		/*
		 * Changing the booking timestamps according to the local timezone.
		 * Fix for https://github.com/woocommerce/woocommerce-bookings/issues/3069
		 */
		$min_booking_date = strtotime( 'midnight', $min_booking_date - $timezone_offset );
		$max_booking_date = strtotime( 'midnight', $max_booking_date - $timezone_offset );

		// phpcs:ignore PHPCompatibility.FunctionUse.ArgumentFunctionsReportCurrentValue.Changed -- Reason: value is unchanged.
		$arg_list = func_get_args();
		$source   = $arg_list[6] ?? 'user';

		// If the seventh argument is `action-scheduler-helper`, just schedule an event and return void.
		if ( 'action-scheduler-helper' === $source ) {
			$product_id = $bookable_product->get_id();

			// Create action scheduler event only if it's not already created
			// OR it's not running right now (i.e. 'cache_update_started' meta does not exist).
			$clear_cache_event = as_has_scheduled_action( 'wc-booking-scheduled-update-availability', array( $product_id, $min_booking_date, $max_booking_date, $timezone_offset ) );

			// Append unique transient name in metas to support multiple event creations.
			$transient_name       = 'book_ts_' . md5( http_build_query( array( $product_id, 0, $min_booking_date, $max_booking_date, false ) ) );
			$cache_update_started = get_post_meta( $product_id, 'cache_update_started-' . $transient_name, true );

			if ( ! $clear_cache_event && ! $cache_update_started ) {

				// Schedule event.
				as_schedule_single_action( time(), 'wc-booking-scheduled-update-availability', array( $product_id, $min_booking_date, $max_booking_date, $timezone_offset ) );

				// Preserve previous availability to serve to users until new one is generated.
				$available_slots = WC_Bookings_Cache::get( $transient_name );
				if ( $available_slots ) {
					WC_Bookings_Cache::set( 'prev-availability-' . $transient_name, $available_slots, 5 * MINUTE_IN_SECONDS );
				}

				// Track that the event is scheduled via a meta, in order to show users the old availability until it runs.
				update_post_meta( $product_id, 'cache_update_scheduled-' . $transient_name, true );
			}

			return;
		}

		// Call these for the whole chunk range for the bookings since they're expensive.
		$blocks = $bookable_product->get_blocks_in_range( $min_booking_date, $max_booking_date );

		// The following loop is needed when:
		// - The product is not available by default.
		// - The product has no availability and the availability is provided by the resources.
		// We need to loop trough the resources to get the blocs in range that would be missing from the product.
		// We are limiting it to products with customer selected resources because it is expensive and there are no requests for automatically selected resources.
		if ( ! $bookable_product->get_default_availability() && $bookable_product->has_resources() && ! $bookable_product->is_resource_assignment_type( 'automatic' ) ) {
			foreach ( $bookable_product->get_resources() as $resource ) {
				$resource_id     = $resource->get_id();
				$resource_blocks = $bookable_product->get_blocks_in_range( $min_booking_date, $max_booking_date, array(), $resource_id );

				$blocks = array_unique( array_merge( $blocks, $resource_blocks ) );
				sort( $blocks );
			}
		}

		// Passing seventh and eight arguments to know whether an event is scheduled or not.
		$available_blocks = wc_bookings_get_time_slots( $bookable_product, $blocks, array(), 0, $min_booking_date, $max_booking_date, false, $timezone_offset, $source );

		$booked_day_blocks['old_availability'] = isset( $available_blocks['old_availability'] ) && true === $available_blocks['old_availability'];
		unset( $available_blocks['old_availability'] );

		$available_slots = array();

		// Get local timezone from existing booking object.
		$local_timezone = $existing_booking->data['local_timezone'] ?? '';

		// Update existing available blocks availability according to the customer's local timezone.
		$available_blocks = self::update_booking_availability_for_customers_timezone( $available_blocks, $local_timezone );

		foreach ( $available_blocks as $block => $quantity ) {
			foreach ( $quantity['resources'] as $resource_id => $availability ) {
				if ( ! empty( $resource_ids ) && ! in_array( $resource_id, $resource_ids, true ) ) {
					continue;
				}

				if ( $availability > 0 ) {
					// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
					$available_slots[ $resource_id ][] = date( $default_date_format, $block );
				}
			}
		}
		// Go through [start, end] of each of the bookings by chunking it in days: [start, start + 1d, start + 2d, ..., end]
		// For each of the chunk check the available slots. If there are no slots, it is fully booked, otherwise partially booked.
		$booking_type = '';
		foreach ( $bookings as $booking ) {
			$check_date = $booking['start'];
			while ( $check_date <= $booking['end'] ) {
				if ( self::is_booking_past_midnight_and_before_start_time( $booking, $bookable_product, $check_date ) ) {
					$check_date = strtotime( '+1 day', $check_date );
					continue;
				}

				// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
				$date_format = date( $default_date_format, $check_date );

				if (
					isset( $available_slots[ $booking['res'] ] ) &&
					in_array( $date_format, $available_slots[ $booking['res'] ], true )
				) {
					$booking_type = 'partially_booked_days';
				} elseif (
					! empty( $available_slots ) &&
					! array_key_exists( $booking['res'], $available_slots ) &&
					$bookable_product->get_resources()
				) {
					/**
					 * Previous booking was made with the resource out of
					 * currently assigned resources. This might happen if
					 * a resource was added after previous booking was made.
					 */
					$booking_type = 'partially_booked_days';
				} else {
					$booking_type = 'fully_booked_days';
				}

				$booked_day_blocks[ $booking_type ][ $date_format ][ $booking['res'] ] = 1;

				$check_date = strtotime( '+1 day', $check_date );
			}
		}

		/**
		 * Filter the booked day blocks calculated per project.
		 *
		 * @since 1.9.13
		 *
		 * @param array $booked_day_blocks {
		 *  @type array $partially_booked_days
		 *  @type array $fully_booked_days
		 * }
		 * @param WC_Product $bookable_product
		 */
		return apply_filters( 'woocommerce_bookings_booked_day_blocks', $booked_day_blocks, $bookable_product );
	}

	/**
	 * Updates existing available blocks according to the customer's local timezone.
	 *
	 * @param array  $available_blocks Existing available block.
	 * @param string $timezone         Local timezone.
	 *
	 * @return array Updated available blocks.
	 * @throws Exception When timezone is not set.
	 */
	public static function update_booking_availability_for_customers_timezone( $available_blocks, $timezone = '' ) {

		if ( empty( $timezone ) ) {
			if ( get_option( 'timezone_string' ) ) {
				$timezone = wc_timezone_string();
			} else {
				// Return if timezone is set as UTC time.
				return $available_blocks;
			}
		}

		$local_timezone           = new DateTimeZone( $timezone );
		$server_timezone          = wc_booking_get_timezone_string();
		$updated_available_blocks = array();

		foreach ( $available_blocks as $timestamp => $block ) {
			// Create DateTime in server's timezone (otherwise UTC is assumed).
			// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
			$dt = new DateTime( date( 'Y-m-d\TH:i:s', $timestamp ), new DateTimeZone( $server_timezone ) );
			$dt->setTimezone( $local_timezone );

			// Calling simply `getTimestamp` will not calculate the timezone.
			$new_timestamp                              = strtotime( $dt->format( 'Y-m-d H:i:s' ) );
			$updated_available_blocks[ $new_timestamp ] = $block;
		}

		return $updated_available_blocks;
	}

	/**
	 * Gets bookings for product ids and resource ids
	 *
	 * @param array   $ids       Array of booking IDs.
	 * @param array   $status    Array of booking statuses.
	 * @param integer $date_from Timestamp.
	 * @param integer $date_to   Timestamp.
	 *
	 * @return array of WC_Booking objects.
	 */
	public static function get_bookings_for_objects( $ids = array(), $status = array(), $date_from = 0, $date_to = 0 ) {
		wc_deprecated_function( __METHOD__, '1.15.0', 'WC_Booking_Data_Store::get_bookings_for_objects()' );
		return WC_Booking_Data_Store::get_bookings_for_objects( $ids, $status, $date_from, $date_to );
	}

	/**
	 * Gets bookings for product ids and resource ids
	 *
	 * @param array   $ids       Array of booking IDs.
	 * @param array   $status    Array of booking statuses.
	 * @param integer $date_from Timestamp.
	 * @param integer $date_to   Timestamp.
	 *
	 * @return array of WC_Booking objects.
	 */
	public static function get_bookings_for_objects_query( $ids, $status, $date_from = 0, $date_to = 0 ) {
		wc_deprecated_function( __METHOD__, '1.15.0', 'WC_Booking_Data_Store::get_bookings_for_objects_query()' );
		return WC_Booking_Data_Store::get_bookings_for_objects_query( $ids, $status, $date_from, $date_to );
	}

	/**
	 * Gets bookings for a product by ID
	 *
	 * @param int   $product_id The id of the product that we want bookings for.
	 * @param array $status The status of the bookings we want to get.
	 *
	 * @return array of WC_Booking objects.
	 */
	public static function get_bookings_for_product( $product_id, $status = array( 'confirmed', 'paid' ) ) {
		wc_deprecated_function( __METHOD__, '1.15.0', 'WC_Booking_Data_Store::get_bookings_for_product()' );
		return WC_Booking_Data_Store::get_bookings_for_product( $product_id, $status );
	}

	/**
	 * Get latest bookings
	 *
	 * @param int $number_of_items Number of objects returned (default to unlimited).
	 * @param int $offset          The number of objects to skip (as a query offset).
	 *
	 * @return array of WC_Booking objects.
	 */
	public static function get_latest_bookings( $number_of_items = 10, $offset = 0 ) {
		wc_deprecated_function( __METHOD__, '1.15.0' );

		$booking_ids = get_posts(
			array(
				'numberposts' => $number_of_items,
				'offset'      => $offset,
				'orderby'     => 'post_date',
				'order'       => 'DESC',
				'post_type'   => 'wc_booking',
				'post_status' => get_wc_booking_statuses(),
				'fields'      => 'ids',
			)
		);

		return array_map( 'get_wc_booking', $booking_ids );
	}

	/**
	 * Gets bookings for a user by ID
	 *
	 * @param int   $user_id    The id of the user that we want bookings for.
	 * @param array $query_args The query arguments used to get booking IDs.
	 *
	 * @return array             Array of WC_Booking objects.
	 */
	public static function get_bookings_for_user( $user_id, $query_args = array() ) {
		wc_deprecated_function( __METHOD__, '1.15.0', 'WC_Booking_Data_Store::get_bookings_for_user()' );
		return WC_Booking_Data_Store::get_bookings_for_user( $user_id, $query_args );
	}

	/*
	|--------------------------------------------------------------------------
	| Deprecated Methods
	|--------------------------------------------------------------------------
	*/
	/**
	 * Get the start and end times for and array of bookings
	 *
	 * @since       1.10.0
	 *
	 * @deprecated  should be removed after other parts of the is optimised to use an array of bookings objects
	 *
	 * @param WC_Booking[] $bookings_objects Array of bookings objects.
	 * @param int          $resource_id      Whether to filter on a specific resource.
	 *
	 * @return array Array of start and end times
	 */
	public static function get_bookings_star_and_end_times( $bookings_objects, $resource_id = 0 ) {
		wc_deprecated_function( __METHOD__, '1.12.2' );

		$bookings_start_and_end = array();
		foreach ( $bookings_objects as $booking ) {
			if ( ! empty( $resource_id ) && $booking->get_resource_id() !== $resource_id ) {
				continue;
			}

			$bookings_start_and_end[] = array( $booking->get_start(), $booking->get_end() );
		}
		return $bookings_start_and_end;
	}

	/**
	 * Gets bookings for a resource.
	 *
	 * @param int   $resource_id Resource ID.
	 * @param array $status      Status.
	 *
	 * @return array array of WC_Booking objects.
	 */
	public static function get_bookings_for_resource( $resource_id, $status = array( 'confirmed', 'paid' ) ) {
		wc_deprecated_function( __METHOD__, '1.12.2' );

		$booking_ids = WC_Booking_Data_Store::get_booking_ids_by(
			array(
				'object_id'   => $resource_id,
				'object_type' => 'resource',
				'status'      => $status,
			)
		);
		return array_map( 'get_wc_booking', $booking_ids );
	}

	/**
	 * Loop through given bookings to find those that are on or over lap the given date.
	 *
	 * @since 1.9.14
	 *
	 * @param array  $bookings array of WC_Booking objects.
	 * @param string $date     Date in string format.
	 *
	 * @return array of booking ids
	 */
	public static function filter_bookings_on_date( $bookings, $date ) {
		wc_deprecated_function( __METHOD__, '1.12.2' );

		$bookings_on_date = array();
		$date_start       = strtotime( 'midnight', $date ); // Midnight today.
		$date_end         = strtotime( 'tomorrow', $date ); // Midnight next day.

		foreach ( $bookings as $booking ) {
			// does the date we want to check fall on one of the days in the booking?
			if ( $booking->get_start() < $date_end && $booking->get_end() > $date_start ) {
				$bookings_on_date[] = $booking;
			}
		}
		return $bookings_on_date;
	}
}
