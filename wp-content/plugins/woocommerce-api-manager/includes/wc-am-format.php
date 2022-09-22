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
	function get_human_time_diff( $timestamp_gmt ) {
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
			$date_to_display = $this->unix_timestamp_to_date_i18n( $timestamp_gmt );
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
	function find_time_diff( $from, $to = 0 ) {
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
	 * the date even though it is a date in UTC timezone because the timezone wasn't specificed.
	 *
	 * This makes sure the date is never converted.
	 *
	 * @since 2.0
	 *
	 * @param string $date_string A date string formatted in MySQl or similar format that will map correctly when instantiating an instance of
	 *                            DateTime().
	 *
	 * @return int Unix timestamp representation of the timestamp passed in without any changes for timezones
	 * @throws \Exception
	 */
	function date_to_time( $date_string ) {
		if ( $date_string == 0 ) {
			return 0;
		}

		$date_time = new WC_DateTime( $date_string, new DateTimeZone( 'UTC' ) );

		return intval( $date_time->getOffsetTimestamp() );
	}

	/**
	 * WooCommerce API Manager Date Format - Allows to change date format for everything WooCommerce.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	function date_format() {
		return apply_filters( 'wc_am_date_format', get_option( 'date_format' ) );
	}

	/**
	 * WooCommerce API Manager Time Format - Allows to change time format for everything WooCommerce.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	function time_format() {
		return apply_filters( 'wc_am_time_format', get_option( 'time_format' ) );
	}

	/**
	 * Take a date and convert it into an epoch/unix timestamp with the correctly locallized timezone offset.
	 *
	 * @since 2.4.4
	 *
	 * @param $datetime_string
	 *
	 * @return int
	 * @throws \Exception
	 */
	function date_to_unix_timestamp_with_timezone_offset( $datetime_string ) {
		return wc_string_to_timestamp( $datetime_string );

		//try {
		//	$datetime = new DateTime( $datetime_string, new DateTimeZone( wc_timezone_string() ) );
		//
		//	return (int) $datetime->format( 'U' );
		//} catch ( Exception $e ) {
		//	WC_AM_LOG()->log_error( esc_html__( 'Details from date_to_unix_timestamp_with_timezone_offset() method.', 'woocommerce-api-manager' ) . PHP_EOL . $e, 'date_to_unix_timestamp_with_timezone_offset' );
		//}
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
	 * Takes an Epoch/Unix timestamp and converts it into a localized string formated date and time.
	 *
	 * @since 2.0.6
	 *
	 * @param int $timestamp
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function unix_timestamp_to_date_i18n( $timestamp ) {
		$timestamp_site  = $this->date_to_time( gmdate( 'Y-m-d H:i:s', $timestamp ) );
		$date_to_display = date_i18n( $this->date_format(), $timestamp_site ) . ' ' . date_i18n( $this->time_format(), $timestamp_site );

		return $date_to_display;
	}

	/**
	 * Takes an Epoch/Unix timestamp and converts it into a localized string formated date for a calendar.
	 *
	 * @since 2.4
	 *
	 * @param int $timestamp
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function unix_timestamp_to_calendar_date_i18n( $timestamp ) {
		$timestamp_site  = $this->date_to_time( gmdate( 'Y-m-d', $timestamp ) );
		$date_to_display = date_i18n( 'Y-m-d', $timestamp_site );

		return $date_to_display;
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
	 * Returns FALSE if var exists and has a non-empty, non-zero value. Otherwise returns TRUE.
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

}