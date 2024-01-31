<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;


/**
 * Adds months to a timestamp.
 *
 * Workaround the last day of month quirk in PHP's strtotime function.
 *
 * Adding +1 month to the last day of the month can yield unexpected results with strtotime()
 * For example:
 * - 30 Jan 2013 + 1 month = 3rd March 2013
 * - 28 Feb 2013 + 1 month = 28th March 2013
 *
 * What humans usually want is for the charge to continue on the last day of the month.
 *
 * Copied from WooCommerce Subscriptions.
 *
 * @since 1.6.0
 *
 * @param int $from_timestamp original timestamp to add months to
 * @param int $months_to_add number of months to add to the timestamp
 * @return int corrected timestamp
 */
function wc_memberships_add_months_to_timestamp( $from_timestamp, $months_to_add ) {

	// bail out if there aren't months to add or is a non positive integer
	if ( ! is_numeric( $months_to_add ) || (int) $months_to_add <= 0 ) {
		return $from_timestamp;
	}

	$first_day_of_month = date( 'Y-m', $from_timestamp ) . '-1';
	$days_in_next_month = date( 't', strtotime( "+ {$months_to_add} month", strtotime( $first_day_of_month ) ) );
	$next_timestamp     = 0;

	// if it's the last day of the month
	// OR
	// the number of days in the next month are less than the the day of this month
	// (i.e. current date is 30th January, next date can't be 30th February)
	if ( date( 'd', $from_timestamp ) > $days_in_next_month || date( 'd m Y', $from_timestamp ) === date( 't m Y', $from_timestamp ) ) {

		for ( $i = 1; $i <= $months_to_add; $i++ ) {

			// add 3 days to make sure we get to the next month,
			// even when it's the 29th day of a month with 31 days:
			$next_month     = strtotime( '+ 3 days', $from_timestamp );
			// note the "t" in date format to get last day of next month:
			$next_timestamp = $from_timestamp = strtotime( date( 'Y-m-t H:i:s', $next_month ) );
		}

	// it's otherwise safe to just add a month
	} else {

		$next_timestamp = strtotime( "+ {$months_to_add} month", $from_timestamp );
	}

	return $next_timestamp;
}


/**
 * Loosely parses a date for Memberships date usages.
 *
 * This is not an absolutely fool-proof check due to PHP 5.2 compatibility constraints.
 *
 * @since 1.7.0
 *
 * @param string|int $date a date in timestamp or string format
 * @param string $format optional, the format to validate: either 'mysql' (default) or 'timestamp'
 * @return false|string|int the date parsed in the chosen format or false if not a valid date
 */
function wc_memberships_parse_date( $date, $format = 'mysql' ) {

	$parsed_date  = false;
	$is_timestamp = 'timestamp' === $format;

	if ( $is_timestamp && is_numeric( $date ) ) {
		$parsed_date = (int) $date;
	} elseif ( ! $is_timestamp && is_string( $date ) && ( $time = strtotime( $date ) ) ) {
		$format      = 'mysql' === $format ? 'Y-m-d H:i:s' : $format;
		$parsed_date = date( $format, $time );
	}

	return $parsed_date;
}


/**
 * Parses a temporal period length.
 *
 * For example: 1 month, 3 weeks, 5 days, 1 year, etc. which should be a string that can be successfully passed to `strtotime()`.
 *
 * @since 1.7.0
 *
 * @param string $length the period length
 * @param string $return optional, part of period length to return (amount, period or, default, the whole parsed length)
 * @return int|string empty string if length is not valid, int for amount, string for period or parsed length
 */
function wc_memberships_parse_period_length( $length, $return = '' ) {

	if ( ! is_string( $length ) ) {
		return '';
	}

	$pieces = explode( ' ', trim( $length ) );
	$amount = isset( $pieces[0], $pieces[1] ) && is_numeric( $pieces[0] ) ? (int) $pieces[0] : '';
	$period = isset( $pieces[0], $pieces[1] ) && is_numeric( $pieces[0] ) ? $pieces[1]       : '';

	if ( ! empty( $amount ) && ! empty( $period ) ) {

		$periods = wc_memberships()->get_plans_instance()->get_membership_plans_access_length_periods();

		if ( in_array( $period, $periods, true ) ) {

			switch ( $return ) {
				case 'amount' :
					return $amount;
				case 'period' :
					return $period;
				default :
					return $amount . ' ' . $period;
			}
		}
	}

	return '';
}


/**
 * Formats a date in a requested format.
 *
 * @since 1.7.0
 *
 * @param string|int $date date string, in 'mysql' format, or timestamp
 * @param string $format optional, format to use: 'mysql' (default), 'timestamp' or valid PHP date format
 * @return string|int formatted date as a timestamp or MySQL format
 */
function wc_memberships_format_date( $date, $format = 'mysql' ) {

	switch ( $format ) {
		case 'mysql':
			return is_numeric( $date ) ? date( 'Y-m-d H:i:s', $date ) : $date;
		case 'timestamp':
			return is_numeric( $date ) ? (int) $date : strtotime( $date );
		default:
			return date( $format, is_numeric( $date ) ? (int) $date : strtotime( $date ) );
	}
}


/**
 * Adjusts dates in UTC format.
 *
 * Converts a UTC date to the corresponding date in another timezone.
 *
 * @since 1.6.0
 *
 * @param int|string $date date in string or timestamp format
 * @param string $format format to use in output
 * @param string $timezone timezone to convert from
 * @return int|string
 */
function wc_memberships_adjust_date_by_timezone( $date, $format = 'mysql', $timezone = 'UTC' ) {

	if ( is_numeric( $date ) ) {
		$src_date = date( 'Y-m-d H:i:s', (int) $date );
	} else {
		$src_date = $date;
	}

	if ( 'mysql' === $format ) {
		$format = 'Y-m-d H:i:s';
	}

	if ( 'UTC' === $timezone ) {
		$from_timezone = 'UTC';
		$to_timezone   = wc_timezone_string();
	} else {
		$from_timezone = $timezone;
		$to_timezone   = 'UTC';
	}

	try {

		$from_date = new \DateTime( $src_date, new \DateTimeZone( $from_timezone ) );
		$to_date   = new \DateTimeZone( $to_timezone );
		$offset    = $to_date->getOffset( $from_date );

		// getTimestamp method not used here for PHP 5.2 compatibility
		$timestamp = (int) $from_date->format( 'U' );

	} catch ( \Exception $e ) {

		// in case of DateTime errors, just return the date as is but issue an error
		trigger_error( sprintf( 'Failed to parse date "%1$s" to get timezone offset: %2$s.', $date, $e->getMessage() ), E_USER_WARNING );

		$timestamp = is_numeric( $date ) ? (int) $date : strtotime( $date );
		$offset    = 0;
	}

	return 'timestamp' === $format ? $timestamp + $offset : date( $format, $timestamp + $offset );
}
