<?php
/**
 * Shipping & delivery functions
 *
 * @package WC_OD/Functions
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the days by the specified property and value.
 *
 * @since 1.0.0
 * @param array $days      The days data.
 * @param string $property The day property to filter.
 * @param mixed $value     The property value to search.
 * @return array The filtered days.
 */
function wc_od_get_days_by( $days, $property, $value ) {
	$filtered_days = array();
	foreach ( $days as $index => $day ) {
		if ( isset( $day[ $property ] ) && $value === $day[ $property ] ) {
			$filtered_days[ $index ] = $day;
		}
	}

	return $filtered_days;
}

/**
 * Gets the events.
 *
 * @since 1.0.0
 * @param array $filters The filters for retrieve the events.
 * @return array The filtered events.
 */
function wc_od_get_events( $filters = array() ) {
	$event_type = ( isset( $filters['type'] ) ? $filters['type'] : 'event' ) ;
	$event_class = 'WC_OD_Event';
	if ( 'delivery' === $event_type ) {
		$event_class = 'WC_OD_Event_Delivery';
	}

	$event_filters = array_diff_key( $filters, array_flip( array( 'timezone', 'start', 'end', 'type' ) ) );
	$event_filters['start'] = wc_od_parse_datetime( $filters['start'] );
	$event_filters['end'] = wc_od_parse_datetime( $filters['end'] );

	// Parse the timezone parameter if it is present.
	$timezone = null;
	if ( isset( $filters['timezone'] ) && $filters['timezone'] ) {
		$timezone = new DateTimeZone( $filters['timezone'] );
	}

	$setting_name = $event_type . '_events';
	$events = WC_OD()->settings()->get_setting( $setting_name, array() );
	$filtered_events = array();
	foreach ( $events as $eventData ) {
		$event = new $event_class( $eventData, $timezone );
		if ( $event->is_valid( $event_filters ) ) {
			$filtered_events[] = $event->to_array();
		}
	}

	/**
	 * Filter the events.
	 *
	 * @since 1.1.0
	 *
	 * @param array $events  An array with the events.
	 * @param array $filters An array with the filters used to get the events.
	 */
	return apply_filters( 'wc_od_get_events', $filtered_events, $filters );
}

/**
 * Gets the disabled days for the specified arguments.
 *
 * NOTE: Since 1.2.0 the dates are always in the ISO 8601 format.
 *
 * @since 1.1.0
 *
 * @param array  $args    Optional. The arguments.
 * @param string $context Optional. The context.
 * @return array An array with the disabled days.
 */
function wc_od_get_disabled_days( $args = array(), $context = '' ) {
	$today = wc_od_get_local_date();
	$max_delivery_days = ( WC_OD()->settings()->get_setting( 'max_delivery_days' ) + 1 ); // Non-inclusive.

	$defaults = array(
		'type'  => 'shipping',
		'start' => date( 'Y-m-d', $today ),
		'end'   => date( 'Y-m-d', strtotime( "+ {$max_delivery_days} days", $today ) ),
	);

	/**
	 * Filter the arguments used to calculate the disabled days.
	 *
	 * @since 1.1.0
	 *
	 * @param array  $args    The arguments.
	 * @param string $context The context.
	 */
	$args = apply_filters( 'wc_od_get_disabled_days_args', wp_parse_args( $args, $defaults ), $context );

	$disabled_days = array();
	$events        = wc_od_get_events( $args );

	foreach ( $events as $event ) {
		$start_timestamp = wc_od_get_timestamp( $event['start'] );
		$disabled_days[] = date( 'Y-m-d', $start_timestamp );

		if ( isset( $event['end'] ) ) {
			$end_timestamp = wc_od_get_timestamp( $event['end'] );

			while ( $start_timestamp < $end_timestamp ) {
				$start_timestamp = strtotime( '+1 day', $start_timestamp );
				$disabled_days[] = date( 'Y-m-d', $start_timestamp );
			}
		}
	}

	// Remove duplicated values and re-index the values to make sure the 'disabledDates' parameter is an array.
	$disabled_days = array_values( array_unique( $disabled_days ) );

	/**
	 * Filter the disabled days.
	 *
	 * @since 1.1.0
	 *
	 * @param array  $days    An array with the disabled dates.
	 * @param array  $args    The arguments used to disable the days.
	 * @param string $context The context.
	 */
	return apply_filters( 'wc_od_get_disabled_days', $disabled_days, $args, $context );
}

/**
 * Gets if the specified day is disabled or not.
 *
 * @since 1.1.0
 *
 * @param string|int $date    The date string or timestamp.
 * @param array      $args    Optional. The optional arguments.
 * @param string     $context Optional. The context.
 * @return bool|null True if the date is disabled. False otherwise. Null on failure.
 */
function wc_od_is_disabled_day( $date, $args = array(), $context = '' ) {
	$timestamp = wc_od_get_timestamp( $date );
	if ( ! $timestamp ) {
		return null;
	}

	$args['start'] = date( 'Y-m-d', $timestamp );
	$args['end']   = date( 'Y-m-d', strtotime( '+1 day', $timestamp ) );

	$days = wc_od_get_disabled_days( $args, $context );

	/**
	 * Filter if the specified day is disabled or not.
	 *
	 * @since 1.1.0
	 *
	 * @param bool   $disabled True if the day is disabled. False otherwise.
	 * @param int    $date     A timestamp representing the day to check.
	 * @param array  $args     The optional arguments.
	 * @param string $context  The context.
	 */
	return apply_filters( 'wc_od_is_disabled_day', ( ! empty( $days ) ), $timestamp, $args, $context );
}

/**
 * Filters if the current day is disabled for delivery.
 *
 * @since 1.6.2
 *
 * @param bool   $disabled  True if the day is disabled. False otherwise.
 * @param int    $timestamp A timestamp representing the day to check.
 * @param array  $args      The optional arguments.
 * @param string $context   The context.
 * @return bool
 */
function wc_od_is_current_day_disabled( $disabled, $timestamp, $args, $context ) {
	if (
		! $disabled && // Not disabled yet.
		( isset( $args['type'] ) ) && 'delivery' === $args['type'] && // Only for delivery.
		in_array( $context, array( 'checkout', 'checkout-auto' ), true ) && // Checkout context.
		date( 'Y-m-d', $timestamp ) === wc_od_get_local_date( false ) // It's the current date.
	) {
		$delivery_day = wc_od_get_delivery_day( date( 'w', $timestamp ) );

		// The delivery day has time frames defined.
		if ( $delivery_day->has_time_frames() ) {
			$checkout = WC_OD()->checkout();

			$time_frames = wc_od_get_time_frames_for_date(
				$timestamp,
				array(
					'shipping_method' => $checkout->get_shipping_method(),
				),
				$context
			);

			// But there is no time frames to select. So, disable the current date.
			if ( $time_frames->is_empty() ) {
				$disabled = true;
			}
		}
	}

	return $disabled;
}
add_filter( 'wc_od_is_disabled_day', 'wc_od_is_current_day_disabled', 10, 4 );

/**
 * Gets if the specified delivery date is valid or not.
 *
 * @since 1.1.0
 * @since 1.5.0 Added `shipping_method` parameter to `$args`.
 *
 * @param string|int $date    The delivery date string or timestamp.
 * @param array      $args    Optional. The optional arguments.
 * @param string     $context Optional. The context.
 * @return bool True if the delivery date is a valid date. False otherwise.
 */
function wc_od_validate_delivery_date( $date, $args = array(), $context = '' ) {
	$delivery_timestamp = wc_od_get_timestamp( $date );

	if ( ! $delivery_timestamp ) {
		return false;
	}

	$defaults = array(
		'shipping_method'    => false,
		'start_date'         => false,
		'end_date'           => false, // The maximum date (Non-inclusive).
		'delivery_days'      => WC_OD()->settings()->get_setting( 'delivery_days' ),
		'disabled_days'      => null, // Use these disabled days if not null.
		'disabled_days_args' => array( // Arguments used by the wc_od_disabled_days() function.
			'type'    => 'delivery',
			'country' => '', // Events for all countries.
		),
	);

	/**
	 * Filter the arguments used to validate the delivery date.
	 *
	 * @since 1.1.0
	 *
	 * @param array  $args    The arguments.
	 * @param string $context The context.
	 */
	$args = apply_filters( 'wc_od_validate_delivery_date_args', wp_parse_args( $args, $defaults ), $context );

	$valid = true;

	// Validate start_date.
	if ( $args['start_date'] ) {
		$start_timestamp = wc_od_get_timestamp( $args['start_date'] );

		// Out of range.
		if ( ! $start_timestamp || $delivery_timestamp < $start_timestamp ) {
			$valid = false;
		}
	}

	// Validate end_date.
	if ( $valid && $args['end_date'] ) {
		$end_timestamp = wc_od_get_timestamp( $args['end_date'] );

		// Out of range.
		if ( ! $end_timestamp || $delivery_timestamp >= $end_timestamp ) {
			$valid = false;
		}
	}

	// Validate delivery day status.
	if ( $valid ) {
		$wday         = date( 'w', $delivery_timestamp );
		$delivery_day = $args['delivery_days'][ $wday ];

		// Calculate the status only for the default delivery days.
		if ( $args['delivery_days'] === $defaults['delivery_days'] ) {
			$status = wc_od_get_delivery_day_status(
				$delivery_day,
				array(
					'shipping_method' => $args['shipping_method'],
				),
				$context
			);
		} else {
			$status = $delivery_day['enabled'];
		}

		$valid = wc_string_to_bool( $status );
	}

	// Validate disabled days.
	if ( $valid ) {
		$delivery_date = date( 'Y-m-d', $delivery_timestamp );

		if ( ( $args['disabled_days'] && in_array( $delivery_date, $args['disabled_days'], true ) ) ||
			wc_od_is_disabled_day( $delivery_date, $args['disabled_days_args'], $context ) ) {
			$valid = false;
		}
	}

	/**
	 * Filters the delivery date validation.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added `$args`and `$context` parameters.
	 *
	 * @param bool   $valid              Is it a valid delivery date?.
	 * @param int    $delivery_timestamp The delivery date timestamp.
	 * @param array  $args               A array with the arguments used to validate the date.
	 * @param string $context            The context.
	 */
	return apply_filters( 'wc_od_validate_delivery_date', $valid, $delivery_timestamp, $args, $context );
}

/**
 * Gets the first day to ship the orders.
 *
 * @since 1.1.0
 * @since 1.5.4 The default value of the `start_date` parameter is the current timestamp.
 *              Check the shipping time limit for the `start_date` parameter instead of the current date.
 *              Deprecated `days_for_shipping` parameter.
 *
 * @param array  $args    Optional. The arguments used to calculate the date.
 * @param string $context Optional. The context.
 *
 * @return int A timestamp representing the first allowed date to ship the orders. False on failure.
 */
function wc_od_get_first_shipping_date( $args = array(), $context = '' ) {
	$defaults = array(
		'min_working_days'   => WC_OD()->settings()->get_setting( 'min_working_days' ),
		'shipping_days'      => WC_OD()->settings()->get_setting( 'shipping_days' ),
		'days_for_shipping'  => 0, // Deprecated. We keep it for backward compatibility with hooks.
		'start_date'         => current_time( 'timestamp' ), // Accept strings or timestamps.
		'end_date'           => false, // The maximum date (Non-inclusive) to look for a valid date.
		'disabled_days_args' => array( // Arguments passed to the wc_od_disabled_days() function.
			'type' => 'shipping',
		),
	);

	/**
	 * Filters the arguments used to calculate the first shipping date.
	 *
	 * @since 1.1.0
	 *
	 * @param array  $args    The arguments.
	 * @param string $context The context.
	 */
	$args = apply_filters( 'wc_od_first_shipping_date_args', wp_parse_args( $args, $defaults ), $context );

	/**
	 * Before executing any calculation, it forces a shipping date if the returned value by the filter is not false.
	 *
	 * @since 1.1.0
	 *
	 * @param int|false $timestamp A timestamp representing the first shipping date.
	 * @param array     $args      The arguments.
	 * @param string    $context   The context.
	 */
	$first_shipping_date = apply_filters( 'wc_od_pre_get_first_shipping_date', false, $args, $context );

	if ( $first_shipping_date ) {
		return $first_shipping_date;
	}

	$start_timestamp = wc_od_get_timestamp( $args['start_date'] );

	if ( ! $start_timestamp ) {
		return false;
	}

	$initial  = strtotime( date( 'Y-m-d', $start_timestamp ) ); // Strip the time from the 'start_date' parameter.
	$deadline = wc_od_get_timestamp( $args['end_date'] );
	$wday     = date( 'w', $start_timestamp );

	// Don't modify the original values.
	$days_for_shipping = intval( $args['days_for_shipping'] );
	$min_working_days  = intval( $args['min_working_days'] );

	do {
		$timestamp = strtotime( "{$days_for_shipping} days", $initial );

		// The day is available for shipping.
		if ( wc_string_to_bool( $args['shipping_days'][ $wday ]['enabled'] ) && ! wc_od_is_disabled_day( $timestamp, $args['disabled_days_args'], $context ) ) {
			// Decrease the minimum working days by default.
			$min_working_days--;

			// Check the time limit in the initial date.
			if ( $initial === $timestamp && ! empty( $args['shipping_days'][ $wday ]['time'] ) ) {
				$timestamp_limit = strtotime( date( 'Y-m-d', $initial ) . " {$args['shipping_days'][ $wday ]['time']}" );

				// We cannot start processing the order in the initial date.
				if ( $start_timestamp > $timestamp_limit ) {
					// Increase the minimum working days.
					$min_working_days++;
				}
			}

			if ( 0 > $min_working_days ) {
				$first_shipping_date = $timestamp;
			}
		}

		$days_for_shipping++;
		$wday = ( ( $wday + 1 ) % 7 );
	} while ( ! $first_shipping_date && ( ! $deadline || $timestamp < $deadline ) );

	/**
	 * Filters the first shipping date.
	 *
	 * @since 1.1.0
	 *
	 * @param int    $timestamp A timestamp representing the first shipping date.
	 * @param array  $args      A array with the arguments used to calculate the date.
	 * @param string $context   The context.
	 */
	return apply_filters( 'wc_od_get_first_shipping_date', $first_shipping_date, $args, $context );
}

/**
 * Gets the last day to ship the orders to receive them on the specified delivery date.
 *
 * @since 1.4.0
 * @since 1.5.0 Added `shipping_method` parameter to `$args`.
 * @since 1.7.0 Deprecated the parameter `delivery_range` from `$args`.
 *
 * @param array  $args    Optional. The arguments used to calculate the date.
 * @param string $context Optional. The context.
 * @return false|int A timestamp representing the last date to ship the orders. False on failure.
 */
function wc_od_get_last_shipping_date( $args = array(), $context = '' ) {
	$defaults = array(
		'shipping_method'             => false,
		'shipping_days'               => WC_OD()->settings()->get_setting( 'shipping_days' ),
		'delivery_days'               => WC_OD()->settings()->get_setting( 'delivery_days' ),
		'delivery_range'              => array(),
		'delivery_date'               => wc_od_get_local_date( false ), // Accept strings or timestamps.
		'end_date'                    => wc_od_get_local_date( false ), // The minimum date to look for a valid date. Today as default.
		'disabled_shipping_days_args' => array( // Arguments used by the wc_od_disabled_days() function.
			'type' => 'shipping',
		),
		'disabled_delivery_days_args' => array( // Arguments used by the wc_od_disabled_days() function.
			'type'    => 'delivery',
			'country' => '', // Events for all countries.
		),
	);

	/**
	 * Filter the arguments used to calculate the last shipping date.
	 *
	 * @since 1.4.0
	 *
	 * @param array  $args    The arguments.
	 * @param string $context The context.
	 */
	$args = apply_filters( 'wc_od_last_shipping_date_args', wp_parse_args( $args, $defaults ), $context );

	/**
	 * Before executing any calculation, it forces a shipping date if the returned value by the filter is not false.
	 *
	 * @since 1.4.0
	 *
	 * @param int|false $timestamp A timestamp representing the last shipping date.
	 * @param array     $args      The arguments.
	 * @param string    $context   The context.
	 */
	$last_shipping_date = apply_filters( 'wc_od_pre_get_last_shipping_date', false, $args, $context );

	if ( $last_shipping_date ) {
		return $last_shipping_date;
	}

	$delivery_timestamp = wc_od_get_timestamp( $args['delivery_date'] );

	if ( ! $delivery_timestamp ) {
		return false;
	}

	if ( ! empty( $args['delivery_range'] ) ) {
		wc_doing_it_wrong( __FUNCTION__, 'The parameter "delivery_range" is deprecated.', '1.7.0' );

		$delivery_range = new WC_OD_Delivery_Range();
		$delivery_range->set_props(
			array(
				'from' => $args['delivery_range']['min'],
				'to'   => $args['delivery_range']['max'],
			)
		);
	} else {
		$delivery_range = WC_OD_Delivery_Ranges::get_range_matching_shipping_method( $args['shipping_method'] );
	}

	$days_for_delivery = 0;
	$wday              = intval( date( 'w', $delivery_timestamp ) );
	$min_delivery_days = $delivery_range->get_from();
	$deadline          = wc_od_get_timestamp( $args['end_date'] );

	// Calculate the statuses only for the default delivery days.
	if ( $args['delivery_days'] === $defaults['delivery_days'] ) {
		$delivery_days_status = wc_od_get_delivery_days_status(
			$args['delivery_days'],
			array(
				'shipping_method' => $args['shipping_method'],
			),
			$context
		);
	} else {
		$delivery_days_status = wp_list_pluck( $args['delivery_days'], 'enabled' );
	}

	do {
		$timestamp = strtotime( "{$days_for_delivery} days", $delivery_timestamp );

		// Need to reduce the minimum delivery days to zero before check the shipping date.
		if ( 0 < $min_delivery_days ) {
			if ( wc_string_to_bool( $delivery_days_status[ $wday ] ) && ! wc_od_is_disabled_day( $timestamp, $args['disabled_delivery_days_args'], $context ) ) {
				$min_delivery_days--;
			}
			// Check shipping day availability.
		} elseif ( wc_string_to_bool( $args['shipping_days'][ $wday ]['enabled'] ) && ! wc_od_is_disabled_day( $timestamp, $args['disabled_shipping_days_args'], $context ) ) {
			$last_shipping_date = $timestamp;
		}

		$days_for_delivery--;
		$wday = ( 0 === $wday ? 6 : $wday - 1 ); // The module operator doesn't work with negative numbers.
	} while ( ! $last_shipping_date && ( ! $deadline || $timestamp > $deadline ) );

	/**
	 * Filter the last shipping date.
	 *
	 * @since 1.1.0
	 *
	 * @param int    $timestamp A timestamp representing the last shipping date.
	 * @param array  $args      A array with the arguments used to calculate the date.
	 * @param string $context   The context.
	 */
	return apply_filters( 'wc_od_get_last_shipping_date', $last_shipping_date, $args, $context );
}

/**
 * Gets the first day to deliver the orders.
 *
 * @since 1.1.0
 * @since 1.5.0 Added `shipping_method` parameter to `$args`. Set the default value `end_date` to the setting value `max_delivery_days`.
 * @since 1.7.0 Deprecated the parameter `delivery_range` from `$args`.
 *
 * @param array  $args    Optional. The arguments used to calculate the date.
 * @param string $context Optional. The context.
 * @return false|int A timestamp representing the first allowed date to deliver the orders. False on failure.
 */
function wc_od_get_first_delivery_date( $args = array(), $context = '' ) {
	$defaults = array(
		'shipping_date'      => '', // Accept strings or timestamps.
		'shipping_method'    => false,
		'delivery_days'      => WC_OD()->settings()->get_setting( 'delivery_days' ),
		'delivery_range'     => array(), // Backward compatibility.
		'end_date'           => strtotime( ( WC_OD()->settings()->get_setting( 'max_delivery_days' ) + 1 ) . ' days', wc_od_get_local_date() ), // The maximum date (Non-inclusive) to look for a valid date.
		'disabled_days_args' => array( // Arguments used by the wc_od_disabled_days() function.
			'type'    => 'delivery',
			'country' => '', // Events for all countries.
		),
	);

	$args = wp_parse_args( $args, $defaults );

	// Avoid to calculate the default shipping date value if this will be overridden in the wp_parse_args() function.
	// We use an empty string instead of 'false' to avoid conflict with an invalid timestamp.
	if ( '' === $args['shipping_date'] ) {
		$args['shipping_date'] = wc_od_get_first_shipping_date( array(), $context );
	}

	/**
	 * Filter the arguments used to calculate the first delivery date.
	 *
	 * @since 1.1.0
	 *
	 * @param array  $args    The arguments.
	 * @param string $context The context.
	 */
	$args = apply_filters( 'wc_od_first_delivery_date_args', $args, $context );

	/**
	 * Before executing any calculation, it forces a delivery date if the returned value by the filter is not false.
	 *
	 * @since 1.1.0
	 *
	 * @param int|false $timestamp A timestamp representing the first delivery date.
	 * @param array     $args      The arguments.
	 * @param string    $context   The context.
	 */
	$first_delivery_date = apply_filters( 'wc_od_pre_get_first_delivery_date', false, $args, $context );

	if ( $first_delivery_date ) {
		return $first_delivery_date;
	}

	$shipping_timestamp = wc_od_get_timestamp( $args['shipping_date'] );

	if ( ! $shipping_timestamp ) {
		return false;
	}

	if ( ! empty( $args['delivery_range'] ) ) {
		wc_doing_it_wrong( __FUNCTION__, 'The parameter "delivery_range" is deprecated.', '1.7.0' );

		$delivery_range = new WC_OD_Delivery_Range();
		$delivery_range->set_props(
			array(
				'from' => $args['delivery_range']['min'],
				'to'   => $args['delivery_range']['max'],
			)
		);
	} else {
		$delivery_range = WC_OD_Delivery_Ranges::get_range_matching_shipping_method( $args['shipping_method'] );
	}

	$deadline = wc_od_get_timestamp( $args['end_date'] );
	$wday     = (int) date( 'w', $shipping_timestamp );

	$days_for_delivery = 0;
	$min_delivery_days = $delivery_range->get_from();

	// Calculate the statuses only for the default delivery days.
	if ( $args['delivery_days'] === $defaults['delivery_days'] ) {
		$delivery_days_status = wc_od_get_delivery_days_status(
			$args['delivery_days'],
			array(
				'shipping_method' => $args['shipping_method'],
			),
			$context
		);
	} else {
		$delivery_days_status = wp_list_pluck( $args['delivery_days'], 'enabled' );
	}

	$delivery_days = wc_od_get_delivery_days();

	do {
		$timestamp     = strtotime( "{$days_for_delivery} days", $shipping_timestamp );
		$delivery_date = new WC_OD_Delivery_Date( $timestamp, $delivery_days->get( $wday ) );

		/*
		 * Special Case: The current date is the shipping date and the minimum delivery days is higher than zero.
		 * We do not deliver this day because it is disabled. But it is a working day for the shipping carrier.
		 */
		if (
			( wc_string_to_bool( $delivery_days_status[ $wday ] ) || ( $shipping_timestamp === $timestamp && 0 < $min_delivery_days ) ) &&
			! wc_od_is_disabled_day( $timestamp, $args['disabled_days_args'], $context ) && // The day isn't disabled for delivery.
			$delivery_date->is_valid() // The date is available for delivery.
		) {
			// Decrease the minimum delivery days.
			$min_delivery_days--;

			if ( 0 > $min_delivery_days ) {
				$first_delivery_date = $timestamp;
			}
		}

		$days_for_delivery++;
		$wday = ( ( $wday + 1 ) % 7 );
	} while ( ! $first_delivery_date && ( ! $deadline || $timestamp < $deadline ) );

	/**
	 * Filter the first delivery date.
	 *
	 * @since 1.1.0
	 *
	 * @param int    $timestamp A timestamp representing the first delivery date.
	 * @param array  $args      A array with the arguments used to calculate the date.
	 * @param string $context   The context.
	 */
	return apply_filters( 'wc_od_get_first_delivery_date', $first_delivery_date, $args, $context );
}

/**
 * Gets the next day to deliver the orders from a valid delivery date.
 *
 * @since 1.3.0
 * @since 1.5.0 Added `shipping_method` parameter to `$args`.
 *
 * @param array  $args    Optional. The arguments used to calculate the date.
 * @param string $context Optional. The context.
 * @return false|int A timestamp representing the next allowed date to deliver the orders. False on failure.
 */
function wc_od_get_next_delivery_date( $args = array(), $context = '' ) {
	$defaults = array(
		'shipping_method'    => false,
		'delivery_days'      => WC_OD()->settings()->get_setting( 'delivery_days' ),
		'delivery_date'      => current_time( 'Y-m-d' ), // Accept strings or timestamps.
		'end_date'           => false, // The maximum date (Non-inclusive) to look for a valid date.
		'disabled_days_args' => array( // Arguments used by the wc_od_disabled_days() function.
			'type'    => 'delivery',
			'country' => '', // Events for all countries.
		),
	);

	/**
	 * Filter the arguments used to calculate the next delivery date.
	 *
	 * @since 1.3.0
	 *
	 * @param array  $args    The arguments.
	 * @param string $context The context.
	 */
	$args = apply_filters( 'wc_od_next_delivery_date_args', wp_parse_args( $args, $defaults ), $context );

	/**
	 * Before executing any calculation, it forces a delivery date if the returned value by the filter is not false.
	 *
	 * @since 1.3.0
	 *
	 * @param int|false $timestamp A timestamp representing the next delivery date.
	 * @param array     $args      The arguments.
	 * @param string    $context   The context.
	 */
	$next_delivery_date = apply_filters( 'wc_od_pre_get_next_delivery_date', false, $args, $context );

	if ( $next_delivery_date ) {
		return $next_delivery_date;
	}

	$delivery_timestamp = wc_od_get_timestamp( $args['delivery_date'] );

	if ( ! $delivery_timestamp ) {
		return false;
	}

	$deadline  = wc_od_get_timestamp( $args['end_date'] );
	$next_days = 1;

	// Calculate the statuses only for the default delivery days.
	if ( $args['delivery_days'] === $defaults['delivery_days'] ) {
		$delivery_days_status = wc_od_get_delivery_days_status(
			$args['delivery_days'],
			array(
				'shipping_method' => $args['shipping_method'],
			),
			$context
		);
	} else {
		$delivery_days_status = wp_list_pluck( $args['delivery_days'], 'enabled' );
	}

	do {
		$timestamp = strtotime( "{$next_days} days", $delivery_timestamp );
		$wday      = date( 'w', $timestamp );

		if ( wc_string_to_bool( $delivery_days_status[ $wday ] ) && ! wc_od_is_disabled_day( $timestamp, $args['disabled_days_args'], $context ) ) {
			$next_delivery_date = $timestamp;
		}

		$next_days++;
	} while ( ! $next_delivery_date && ( ! $deadline || $timestamp < $deadline ) );

	/**
	 * Filter the next delivery date.
	 *
	 * @since 1.3.0
	 *
	 * @param int    $timestamp A timestamp representing the next delivery date.
	 * @param array  $args      A array with the arguments used to calculate the date.
	 * @param string $context   The context.
	 */
	return apply_filters( 'wc_od_get_next_delivery_date', $next_delivery_date, $args, $context );
}
