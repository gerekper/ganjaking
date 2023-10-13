<?php
/**
 * Date functions
 *
 * @package YITH\Booking\Functions
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! function_exists( 'yith_wcbk_get_weeks_array' ) ) {
	/**
	 * Return the weeks array.
	 *
	 * @return array
	 */
	function yith_wcbk_get_weeks_array() {
		$weeks_array = array();
		for ( $i = 1; $i < 53; $i ++ ) {
			$index = $i . '';
			// translators: %d is the number of the week: ex. Week 15.
			$weeks_array[ $index ] = sprintf( __( 'Week %d', 'yith-booking-for-woocommerce' ), $index );
		}

		return $weeks_array;
	}
}

if ( ! function_exists( 'yith_wcbk_get_months_array' ) ) {
	/**
	 * Return the months array.
	 *
	 * @param bool $localized Set true to use localized strings.
	 *
	 * @return array|string[]
	 */
	function yith_wcbk_get_months_array( $localized = true ) {
		if ( $localized ) {
			$months_array = array(
				1  => __( 'January', 'yith-booking-for-woocommerce' ),
				2  => __( 'February', 'yith-booking-for-woocommerce' ),
				3  => __( 'March', 'yith-booking-for-woocommerce' ),
				4  => __( 'April', 'yith-booking-for-woocommerce' ),
				5  => __( 'May', 'yith-booking-for-woocommerce' ),
				6  => __( 'June', 'yith-booking-for-woocommerce' ),
				7  => __( 'July', 'yith-booking-for-woocommerce' ),
				8  => __( 'August', 'yith-booking-for-woocommerce' ),
				9  => __( 'September', 'yith-booking-for-woocommerce' ),
				10 => __( 'October', 'yith-booking-for-woocommerce' ),
				11 => __( 'November', 'yith-booking-for-woocommerce' ),
				12 => __( 'December', 'yith-booking-for-woocommerce' ),
			);
		} else {
			$months_array = array(
				1  => 'January',
				2  => 'February',
				3  => 'March',
				4  => 'April',
				5  => 'May',
				6  => 'June',
				7  => 'July',
				8  => 'August',
				9  => 'September',
				10 => 'October',
				11 => 'November',
				12 => 'December',
			);
		}

		return $months_array;
	}
}

if ( ! function_exists( 'yith_wcbk_get_days_array' ) ) {
	/**
	 * Return the days array.
	 *
	 * @param bool $localized Set true to use localized strings.
	 * @param bool $short     Set true to use short day names.
	 *
	 * @return array|string[]
	 */
	function yith_wcbk_get_days_array( $localized = true, $short = false ) {
		if ( $localized ) {
			if ( $short ) {
				$days_array = array(
					1 => _x( 'Mon', 'Short day name', 'yith-booking-for-woocommerce' ),
					2 => _x( 'Tue', 'Short day name', 'yith-booking-for-woocommerce' ),
					3 => _x( 'Wed', 'Short day name', 'yith-booking-for-woocommerce' ),
					4 => _x( 'Thu', 'Short day name', 'yith-booking-for-woocommerce' ),
					5 => _x( 'Fri', 'Short day name', 'yith-booking-for-woocommerce' ),
					6 => _x( 'Sat', 'Short day name', 'yith-booking-for-woocommerce' ),
					7 => _x( 'Sun', 'Short day name', 'yith-booking-for-woocommerce' ),
				);
			} else {
				$days_array = array(
					1 => __( 'Monday', 'yith-booking-for-woocommerce' ),
					2 => __( 'Tuesday', 'yith-booking-for-woocommerce' ),
					3 => __( 'Wednesday', 'yith-booking-for-woocommerce' ),
					4 => __( 'Thursday', 'yith-booking-for-woocommerce' ),
					5 => __( 'Friday', 'yith-booking-for-woocommerce' ),
					6 => __( 'Saturday', 'yith-booking-for-woocommerce' ),
					7 => __( 'Sunday', 'yith-booking-for-woocommerce' ),
				);
			}
		} else {
			$days_array = array(
				1 => 'Monday',
				2 => 'Tuesday',
				3 => 'Wednesday',
				4 => 'Thursday',
				5 => 'Friday',
				6 => 'Saturday',
				7 => 'Sunday',
			);
			if ( $short ) {
				foreach ( $days_array as $key => $value ) {
					$days_array[ $key ] = substr( $value, 0, 3 );
				}
			}
		}

		return $days_array;
	}
}

if ( ! function_exists( 'yith_wcbk_intersect_dates' ) ) {
	/**
	 * Check if one date range intersects other date range
	 *
	 * @param int $start1 The start timestamp of the first range.
	 * @param int $end1   The end timestamp of the first range.
	 * @param int $start2 The start timestamp of the second range.
	 * @param int $end2   The end timestamp of the second range.
	 *
	 * @return bool
	 */
	function yith_wcbk_intersect_dates( $start1, $end1, $start2, $end2 ) {
		return yith_wcbk_date_helper()->check_intersect_dates( $start1, $end1, $start2, $end2 );
	}
}

if ( ! function_exists( 'yith_wcbk_get_timezone' ) ) {
	/**
	 * Get the WordPress timezone
	 *
	 * @param string $type The return type (default or human).
	 */
	function yith_wcbk_get_timezone( $type = 'default' ) {
		$timezone_string = get_option( 'timezone_string' );
		if ( $timezone_string ) {
			$timezone = $timezone_string;
		} else {
			$gmt_offset = get_option( 'gmt_offset' );
			if ( 'human' === $type ) {
				$timezone = sprintf( 'UTC%s%s', $gmt_offset >= 0 ? '+' : '-', absint( $gmt_offset ) );
			} else {
				$timezone = sprintf( '%s0%s00', $gmt_offset >= 0 ? '+' : '-', absint( $gmt_offset ) );
			}
		}

		return apply_filters( 'yith_wcbk_get_timezone', $timezone );
	}
}

if ( ! function_exists( 'yith_wcbk_time_slot_validate' ) ) {
	/**
	 * Validate a time-slot
	 *
	 * @param string $slot The time-slot.
	 *
	 * @return bool
	 * @since 2.1.0
	 */
	function yith_wcbk_time_slot_validate( $slot ) {
		if ( strpos( $slot, ':' ) ) {
			list( $h, $m ) = explode( ':', $slot, 2 );

			return is_numeric( $h ) && is_numeric( $m ) && $h < 25 && $m < 60;
		}

		return false;
	}
}

if ( ! function_exists( 'yith_wcbk_time_slot' ) ) {
	/**
	 * Return a valid time-slot.
	 *
	 * @param string $slot The time-slot.
	 *
	 * @return string
	 * @since 2.1.0
	 */
	function yith_wcbk_time_slot( $slot ) {
		return yith_wcbk_time_slot_validate( $slot ) ? $slot : '00:00';
	}
}

if ( ! function_exists( 'yith_wcbk_string_to_time_slot' ) ) {
	/**
	 * Parse a string to return a valid time-slot
	 *
	 * @param string $string The string to be parsed.
	 *
	 * @return string
	 * @since 2.1.3
	 */
	function yith_wcbk_string_to_time_slot( $string ) {
		if ( preg_match( '/[0-9][0-9]:[0-9][0-9][aApP][mM]/', $string, $matches ) ) {
			$time_slot = current( $matches );
			$time      = substr( $time_slot, 0, 5 );
			if ( preg_match( '/[pP][mM]/', $time_slot ) ) {
				list( $h, $m ) = explode( ':', $time, 2 );

				$time = ( $h + 12 ) . ':' . $m;
			}
		} elseif ( preg_match( '/[0-9][0-9]:[0-9][0-9]/', $string, $matches ) ) {
			$time = current( $matches );
		} else {
			$time = false;
		}

		return $time && yith_wcbk_time_slot_validate( $time ) ? $time : false;
	}
}

if ( ! function_exists( 'yith_wcbk_date' ) ) {
	/**
	 * Format a date.
	 *
	 * @param int $timestamp The timestamp to be formatted.
	 *
	 * @return string
	 */
	function yith_wcbk_date( $timestamp ) {
		return date_i18n( wc_date_format(), $timestamp );
	}
}

if ( ! function_exists( 'yith_wcbk_datetime' ) ) {
	/**
	 * Format a date with time.
	 *
	 * @param int $timestamp The timestamp to be formatted.
	 *
	 * @return string
	 */
	function yith_wcbk_datetime( $timestamp ) {
		$format = sprintf( '%s %s', wc_date_format(), wc_time_format() );

		return date_i18n( $format, $timestamp );
	}
}

if ( ! function_exists( 'yith_wcbk_get_local_timezone_timestamp' ) ) {
	/**
	 * Return a timestamp including the local timezone offset.
	 *
	 * @return int
	 */
	function yith_wcbk_get_local_timezone_timestamp() {
		return time() + (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
	}
}

if ( ! function_exists( 'yith_wcbk_number_of_days_by_month' ) ) {
	/**
	 * Return the number of days for each month.
	 *
	 * @return array
	 * @since 3.0.0
	 */
	function yith_wcbk_number_of_days_by_month() {
		return array(
			1  => 31,
			2  => 29,
			3  => 31,
			4  => 30,
			5  => 31,
			6  => 30,
			7  => 31,
			8  => 31,
			9  => 30,
			10 => 31,
			11 => 30,
			12 => 31,
		);
	}
}
