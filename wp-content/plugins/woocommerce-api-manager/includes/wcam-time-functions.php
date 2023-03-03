<?php

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce API Manager Time Functions
 *
 * Note: Functions must be called using the plugins_loaded action hook.
 *
 * @package     WooCommerce API Manager/includes/Time Functions
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @version     2.5
 */

/**
 * Display a human friendly time diff for a given timestamp, e.g. "In 12 hours" or "12 hours ago".
 *
 * Wrapper for get_human_time_diff().
 *
 * @since 2.5
 *
 * @param int $timestamp_gmt
 *
 * @return string
 * @throws \Exception
 */
function wc_am_get_human_time_diff( $timestamp_gmt ) {
	return WC_AM_FORMAT()->get_human_time_diff( $timestamp_gmt );
}

/**
 * Convert a date string into a timestamp without ever adding or deducting time.
 *
 * Wrapper for date_to_time().
 *
 * strtotime() would be handy for this purpose, but alas, if other code running on the server
 * is calling date_default_timezone_set() to change the timezone, strtotime() will assume the
 * date is in that timezone unless the timezone is specific on the string (which it isn't for
 * any MySQL formatted date) and attempt to convert it to UTC time by adding or deducting the
 * GMT/UTC offset for that timezone, so for example, when 3rd party code has set the servers
 * timezone using date_default_timezone_set( 'America/Los_Angeles' ) doing something like
 * gmdate( "Y-m-d H:i:s", strtotime( gmdate( "Y-m-d H:i:s" ) ) ) will actually add 7 hours to
 * the date even though it is a date in UTC timezone because the timezone wasn't specificed.
 *
 * This makes sure the date is never converted.
 *
 * wc_string_to_timestamp() - no timezone offset.
 * date_to_time() - timezone offset.
 *
 * @since 2.5
 *
 * @param string $date        A date string formatted in MySQl or similar format that will map correctly when instantiating an instance of
 *                            DateTime().
 *
 * @return int Unix timestamp representation of the timestamp passed in without any changes for timezones
 * @throws \Exception
 */
function wc_am_date_to_time( $date, $offset = true ) {
	return WC_AM_FORMAT()->date_to_time( $date, $offset );
}

/**
 * Take a date and convert it into an epoch/unix timestamp without the timezone offset.
 *
 * Wrapper for date_to_unix_timestamp_with_no_timezone_offset().
 *
 * @since   2.5
 *
 * @param string $datetime
 *
 * @return int
 */
function wc_am_date_to_unix_timestamp_with_no_timezone_offset( $datetime ) {
	return WC_AM_FORMAT()->date_to_unix_timestamp_with_no_timezone_offset( $datetime );
}

/**
 * Take a date and convert it into an epoch/unix timestamp with the correctly locallized timezone offset.
 *
 * Wrapper for date_to_unix_timestamp_with_timezone_offset().
 *
 * @since 2.5
 *
 * @param string $datetime
 *
 * @return int
 */
function wc_am_date_to_unix_timestamp_with_timezone_offset( $datetime ) {
	return WC_AM_FORMAT()->date_to_unix_timestamp_with_timezone_offset( $datetime );
}

/**
 * Takes an Epoch/Unix timestamp and converts it into a localized string formated date and time.
 *
 * Wrapper for unix_timestamp_to_date().
 *
 * @since 2.5
 *
 * @param int $timestamp
 *
 * @return string
 */
function wc_am_unix_timestamp_to_date( $timestamp ) {
	return WC_AM_FORMAT()->unix_timestamp_to_date( $timestamp );
}

/**
 * Takes an Epoch/Unix timestamp and converts it into a localized string formated date for a calendar.
 *
 * Wrapper for unix_timestamp_to_calendar_date().
 *
 * @since 2.5
 *
 * @param int $timestamp
 *
 * @return string
 */
function wc_am_unix_timestamp_to_calendar_date( $timestamp ) {
	return WC_AM_FORMAT()->unix_timestamp_to_calendar_date( $timestamp );
}