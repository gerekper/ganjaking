<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce API Manager Format Class
 *
 * @since       2.0
 *
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @package     WooCommerce API Manager/Format
 */
class WC_AM_Format {

	/**
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * @static
	 * @return \WC_AM_Format
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	private function __construct() { }

	/**
	 * Display a human friendly time diff for a given timestamp, e.g. "In 12 hours" or "12 hours ago".
	 *
	 * @since 2.0
	 *
	 * @param int $timestamp_gmt
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function get_human_time_diff( $timestamp_gmt ) {
		$timestamp_gmt = (int) $timestamp_gmt;
		$current_time  = WC_AM_ORDER_DATA_STORE()->get_current_time_stamp();
		$time_diff     = $timestamp_gmt - $current_time;

		if ( $time_diff > 0 && $time_diff < WEEK_IN_SECONDS ) {
			// translators: placeholder is human time diff (e.g. "3 weeks")
			$date_to_display = sprintf( __( 'In %s', 'woocommerce-api-manager' ), human_time_diff( $current_time, $timestamp_gmt ) );
		} elseif ( $time_diff < 0 && absint( $time_diff ) < WEEK_IN_SECONDS ) {
			// translators: placeholder is human time diff (e.g. "3 weeks")
			$date_to_display = sprintf( __( '%s ago', 'woocommerce-api-manager' ), human_time_diff( $current_time, $timestamp_gmt ) );
		} else {
			$date_to_display = $this->unix_timestamp_to_date( $timestamp_gmt );
		}

		return $date_to_display;
	}

	/**
	 * Determines the difference between two timestamps.
	 *
	 * The difference is returned in seconds.
	 *
	 * @since 2.4.4
	 *
	 * @param int $from Unix timestamp from which the difference begins.
	 * @param int $to   Optional. Unix timestamp to end the time difference. Default becomes time() if not set.
	 *
	 * @return string Number of seconds of time difference.
	 */
	public function find_time_diff( $from, $to = 0 ) {
		if ( empty( $to ) ) {
			$to = time();
		}

		$diff = (int) abs( $to - $from );

		if ( $diff < MINUTE_IN_SECONDS ) {
			$secs = $diff;
			if ( $secs <= 1 ) {
				$secs = 1;
			}

			return $secs . ' secs';
		} elseif ( $diff < HOUR_IN_SECONDS && $diff >= MINUTE_IN_SECONDS ) {
			$mins = $diff / MINUTE_IN_SECONDS;
			if ( $mins <= 1 ) {
				$mins = 1;
			}

			return $mins * MINUTE_IN_SECONDS . ' mins';
		} elseif ( $diff < DAY_IN_SECONDS && $diff >= HOUR_IN_SECONDS ) {
			$hours = $diff / HOUR_IN_SECONDS;
			if ( $hours <= 1 ) {
				$hours = 1;
			}

			return $hours * HOUR_IN_SECONDS . ' hours';
		} elseif ( $diff < WEEK_IN_SECONDS && $diff >= DAY_IN_SECONDS ) {
			$days = $diff / DAY_IN_SECONDS;
			if ( $days <= 1 ) {
				$days = 1;
			}

			return $days * DAY_IN_SECONDS . ' days';
		} elseif ( $diff < MONTH_IN_SECONDS && $diff >= WEEK_IN_SECONDS ) {
			$weeks = $diff / WEEK_IN_SECONDS;
			if ( $weeks <= 1 ) {
				$weeks = 1;
			}

			return $weeks * WEEK_IN_SECONDS . ' weeks';
		} elseif ( $diff < YEAR_IN_SECONDS && $diff >= MONTH_IN_SECONDS ) {
			$months = $diff / MONTH_IN_SECONDS;
			if ( $months <= 1 ) {
				$months = 1;
			}

			return $months * MONTH_IN_SECONDS . ' months';
		} elseif ( $diff >= YEAR_IN_SECONDS ) {
			$years = $diff / YEAR_IN_SECONDS;
			if ( $years <= 1 ) {
				$years = 1;
			}

			return $years * YEAR_IN_SECONDS . ' years';
		}

		return 0;
	}

	/**
	 * Convert a date string into a timestamp without ever adding or deducting time.
	 *
	 * strtotime() would be handy for this purpose, but alas, if other code running on the server
	 * is calling date_default_timezone_set() to change the timezone, strtotime() will assume the
	 * date is in that timezone unless the timezone is specific on the string (which it isn't for
	 * any MySQL formatted date) and attempt to convert it to UTC time by adding or deducting the
	 * GMT/UTC offset for that timezone, so for example, when 3rd party code has set the servers
	 * timezone using date_default_timezone_set( 'America/Los_Angeles' ) doing something like
	 * gmdate( "Y-m-d H:i:s", strtotime( gmdate( "Y-m-d H:i:s" ) ) ) will actually add 7 hours to
	 * the date even though it is a date in UTC timezone because the timezone wasn't specified.
	 *
	 * This makes sure the date is never converted.
	 *
	 * wc_string_to_timestamp() - no timezone offset.
	 * date_to_time() - timezone offset.
	 *
	 * @since 2.0
	 *
	 * @param string $date        A date string formatted in MySQl or similar format that will map correctly when instantiating an instance of
	 *                            DateTime().
	 *
	 * @return int Unix timestamp representation of the timestamp passed in without any changes for timezones
	 */
	public function date_to_time( $date, $offset = true ) {
		if ( $date == 0 ) {
			return 0;
		}

		try {
			$date_time = new WC_DateTime( $date, new DateTimeZone( 'UTC' ) );

			return ( $offset ) ? intval( $date_time->getOffsetTimestamp() ) : intval( $date_time->getTimestamp() );
		} catch ( Exception $e ) {
			return 0;
		}
	}

	/**
	 * WooCommerce API Manager Date Format - Allows the date format to be changed for everything in WooCommerce API Manager.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function date_format() {
		return apply_filters( 'wc_am_date_format', wc_date_format() );
	}

	/**
	 * WooCommerce API Manager Time Format - Allows the time format to be changed for everything in WooCommerce API Manager.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function time_format() {
		return apply_filters( 'wc_am_time_format', wc_time_format() );
	}

	/**
	 * Returns the timezone of the site as a string.
	 *
	 * Uses the `timezone_string` option to get a proper timezone name if available,
	 * otherwise falls back to a manual UTC ± offset.
	 *
	 * Example return values:
	 *
	 *  - 'Europe/Rome'
	 *  - 'America/North_Dakota/New_Salem'
	 *  - 'UTC'
	 *  - '-06:30'
	 *  - '+00:00'
	 *  - '+08:45'
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function timezone_string() {
		return wp_timezone_string();
	}

	/**
	 * Retrieves the timezone of the site as a `DateTimeZone` object.
	 *
	 * Timezone can be based on a PHP timezone string or a ±HH:MM offset.
	 *
	 * @since 2.5
	 *
	 * @return DateTimeZone Timezone object.
	 */
	public function timezone() {
		return wp_timezone();
	}

	/**
	 * Take a date and convert it into an epoch/unix timestamp without the timezone offset.
	 *
	 * @since   2.5
	 *
	 * @param string $datetime
	 *
	 * @return int
	 */
	public function date_to_unix_timestamp_with_no_timezone_offset( $datetime ) {
		return $this->date_to_time( $datetime, false );
	}

	/**
	 * Take a date and convert it into an epoch/unix timestamp with the correctly localized timezone offset.
	 *
	 * @since 2.4.4
	 *
	 * @param string $datetime
	 *
	 * @return int
	 */
	public function date_to_unix_timestamp_with_timezone_offset( $datetime ) {
		return $this->date_to_time( $datetime );
	}

	/**
	 * Takes an Epoch/Unix timestamp and converts it into a localized string formatted date and time.
	 *
	 * @since     2.0.6
	 * @depecated 2.5
	 *
	 * @param int $timestamp
	 *
	 * @return string
	 */
	public function unix_timestamp_to_date_i18n( $timestamp ) {
		_deprecated_function( 'WC_AM_FORMAT()->unix_timestamp_to_date_i18n()', '2.5', 'WC_AM_FORMAT()->unix_timestamp_to_date()' );

		return $this->unix_timestamp_to_date( $timestamp );
	}

	/**
	 * Takes an Epoch/Unix timestamp and converts it into a localized string formatted date and time.
	 *
	 * @since   2.0.6
	 * @updated 2.8 Added $date_only.
	 *
	 * @param int  $timestamp
	 * @param bool $date_only
	 *
	 * @return string
	 */
	public function unix_timestamp_to_date( $timestamp, $date_only = false ) {
		if ( $date_only ) {
			return date_i18n( $this->date_format(), $this->localized_datetime_timestamp( $timestamp ) );
		}

		return date_i18n( $this->date_format() . ' ' . $this->time_format(), $this->localized_datetime_timestamp( $timestamp ) );
	}

	/**
	 * Takes an Epoch/Unix timestamp and converts it into a localized string formatted date for a calendar.
	 *
	 * @since     2.4
	 * @depecated 2.5
	 *
	 * @param int $timestamp
	 *
	 * @return string
	 */
	public function unix_timestamp_to_calendar_date_i18n( $timestamp ) {
		_deprecated_function( 'WC_AM_FORMAT()->unix_timestamp_to_calendar_date_i18n()', '2.5', 'WC_AM_FORMAT()->unix_timestamp_to_calendar_date()' );

		return $this->unix_timestamp_to_calendar_date( $timestamp );
	}

	/**
	 * Takes an Epoch/Unix timestamp and converts it into a localized string formatted date for a calendar.
	 *
	 * @since 2.4
	 *
	 * @param int $timestamp
	 *
	 * @return string
	 */
	public function unix_timestamp_to_calendar_date( $timestamp ) {
		$timestamp_localized = $this->date_to_time( gmdate( $this->date_format(), $timestamp ) );

		return date_i18n( 'Y-m-d', $timestamp_localized );
	}

	/**
	 * Take a date in the form of a timestamp, MySQL date/time string or DateTime object (or perhaps
	 * a WC_Datetime object when WC > 3.0 is active) and create a WC_DateTime object.
	 *
	 * @since  2.6
	 *
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if there is no date.
	 *
	 * @return null|WC_DateTime in site's timezone
	 */
	public function get_datetime_from( $variable_date_type ) {

		try {
			if ( empty( $variable_date_type ) ) {
				$datetime = null;
			} elseif ( is_a( $variable_date_type, 'WC_DateTime' ) ) {
				$datetime = $variable_date_type;
			} elseif ( is_numeric( $variable_date_type ) ) {
				$datetime = new WC_DateTime( "@{$variable_date_type}", new DateTimeZone( 'UTC' ) );
				$datetime->setTimezone( new DateTimeZone( wc_timezone_string() ) );
			} else {
				$datetime = new WC_DateTime( $variable_date_type, new DateTimeZone( wc_timezone_string() ) );
			}
		} catch ( Exception $e ) {
			$datetime = null;
		}

		return $datetime;
	}

	/**
	 * Return timestamp formatted with the localized date and time.
	 *
	 * @since 2.6.5
	 *
	 * @param int $timestamp
	 *
	 * @return int
	 */
	public function localized_datetime_timestamp( $timestamp ) {
		return $this->date_to_time( get_date_from_gmt( gmdate( $this->date_format() . ' ' . $this->time_format(), $timestamp ) ) );
	}

	/**
	 * Returns number of elements in an array or zero.
	 * Wrapper for count() to fix PHP 7.2 requirement that parameter must be validated as array, object, or collection that implements Countable Interface.
	 *
	 * @since 2.0
	 *
	 * @param array|object $collection
	 *
	 * @return int
	 */
	public function count( $collection ) {
		return is_array( $collection ) || is_object( $collection ) ? count( $collection ) : 0;
	}

	/**
	 * Wrapper for wp_json_encode() if WP version is < 4.1.
	 *
	 * @since 2.1.2
	 *
	 * @param array $data Data to be encoded
	 *
	 * @return false|mixed|string|void
	 */
	public function json_encode( $data ) {
		if ( function_exists( 'wp_json_encode' ) ) {
			return wp_json_encode( $data );
		}

		return json_encode( $data );
	}

	/**
	 * Returns FALSE if var exists and has a non-empty, non-zero value.
	 * Works with Objects, which the core empty() PHP function does not.
	 *
	 * @since   2.2.1
	 * @updated 2.4.1
	 * @updated 2.4.4
	 *
	 * @param $var
	 *
	 * @return bool
	 */
	public function empty( $var ) {
		/*
		 * @since 2.2.1
		 * Why the @? Ironically, an empty object will cause the warning:
		 * json_decode() expects parameter 1 to be string, object given in ...
		 *
		 * // return is_object( $var ) ? empty( @json_decode( $var, true ) ) : empty( $var );
		 *
		 * @updated 2.4.1
		 * PHP 8.1 started throwing error
		 * CRITICAL Uncaught TypeError: json_decode(): Argument #1 ($json) must be of type string, stdClass given in ...
		 * return is_object( $var ) ? empty( @json_decode( $var, true ) ) : empty( $var );
		 *
		 * If is_object == true then cast $var to (array) before checking with empty() function.
		 *
		 * @updated 2.4.4
		 * Error: Array to string conversion.
		 * // return is_object( $var ) ? empty( (array) $var ) : empty( $var );
		 */
		if ( is_array( $var ) || is_string( $var ) ) {
			return empty( $var );
		}

		return is_object( $var ) ? empty( json_decode( json_encode( $var ), true ) ) : empty( $var );
	}

	/**
	 * Returns false if either type String compared is null to prevent PHP 8.2 deprecated notice.
	 * PHP notice: Deprecated: strcmp(): Passing null to parameter #1 ($string1) of type string is deprecated ...
	 *
	 * @since 2.5
	 * @since 2.7 updated strcmp() to === 0 as it was before 2.5, since it should be zero if both strings are identical.
	 *
	 * @param String $str1
	 * @param String $str2
	 *
	 * @return bool
	 */
	public function strcmp( $str1, $str2 ) {
		return ! is_null( $str1 ) && ! is_null( $str2 ) && strcmp( $str1, $str2 ) === 0;
	}

	/**
	 * Extracts the digits from a string and returns the version. Excludes non-digits. Includes periods.
	 * Examples: 1.2, 1.3.3
	 *
	 * @since 2.6.16
	 *
	 * @param $string
	 *
	 * @return float
	 */
	public function string_to_version( $string ) {
		return is_numeric( $string ) ? $string : preg_replace( '/[^0-9,.]/', '', $string );
	}

	/**
	 * Extracts the digits from a string and returns the absolute value float. Excludes non-digits.
	 *
	 * @since 2.6.16
	 *
	 * @param $string
	 *
	 * @return float
	 */
	public function string_to_float( $string ) {
		return is_numeric( $string ) ? abs( (float) $string ) : abs( (float) filter_var( $string, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) ); // 5.00
	}

	/**
	 * Extracts the digits from a string and returns the absolute value integer. Excludes non-digits.
	 *
	 * @since 2.6.16
	 *
	 * @param $string
	 *
	 * @return int
	 */
	public function string_to_integer( $string ) {
		return is_numeric( $string ) ? absint( $string ) : absint( filter_var( $string, FILTER_SANITIZE_NUMBER_INT ) );
	}
}