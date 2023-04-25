<?php
/**
 * Datetime functions.
 *
 * @package WC_OD/Functions
 * @since   2.3.0
 */

defined( 'ABSPATH' ) || exit;


/**
 * Parses a string into a DateTime object, optionally forced into the given timezone.
 *
 * @since 1.0.0
 *
 * @param string       $string    A string representing a datetime.
 * @param DateTimeZone $timezone  Optional. The timezone.
 * @return DateTime  The DataTime object.
 */
function wc_od_parse_datetime( $string, $timezone = null ) {
	if ( ! $timezone ) {
		$timezone = new DateTimeZone( 'UTC' );
	}

	$date = new DateTime( $string, $timezone );
	$date->setTimezone( $timezone );

	return $date;
}

/**
 * Takes the year-month-day values of the given DateTime and converts them to a new UTC DateTime.
 *
 * @since 1.0.0
 *
 * @param DateTime $datetime The datetime.
 * @return DateTime The DataTime object.
 */
function wc_od_strip_time( $datetime ) {
	return new DateTime( $datetime->format( 'Y-m-d' ) );
}

/**
 * Parses a string into a DateTime object.
 *
 * @since 1.0.0
 *
 * @param string $string      A string representing a time.
 * @param string $time_format The time format.
 * @return string The sanitized time.
 */
function wc_od_sanitize_time( $string, $time_format = 'H:i' ) {
	if ( ! $string ) {
		return '';
	}

	$timestamp = strtotime( $string );
	if ( false === $timestamp ) {
		return '';
	}

	return date( $time_format, $timestamp );
}

/**
 * Gets the localized date with the date format.
 *
 * @since 1.0.0
 *
 * @param string|int $date   The date to localize.
 * @param string     $format Optional. The date format. If null use the general WordPress date format.
 * @return string|null The localized date string. Null if the date is not valid.
 */
function wc_od_localize_date( $date, $format = null ) {
	if ( ! $date ) {
		return null;
	}

	if ( ! $format ) {
		$format = wc_od_get_date_format( 'php' );
	}

	if ( wc_od_is_timestamp( $date ) ) {
		// Assume a WP timestamp (UNIX timestamp + offset).
		$date_i18n = date_i18n( $format, $date );
	} else {
		try {
			$datetime  = new WC_DateTime( $date, new DateTimeZone( wc_timezone_string() ) );
			$date_i18n = $datetime->date_i18n( $format );
		} catch ( Exception $e ) {
			$date_i18n = null;
		}
	}

	return $date_i18n;
}

/**
 * Gets the localized time with the specified format.
 *
 * @since 1.5.0
 * @since 1.6.0 Returns an empty string instead of false on failure.
 *
 * @param string|int $time   The time to localize.
 * @param string     $format Optional. The time format. WC format by default.
 * @return string The localized time string. Empty string on failure.
 */
function wc_od_localize_time( $time, $format = null ) {
	if ( ! $time ) {
		return '';
	}

	if ( ! $format ) {
		$format = wc_time_format();
	}

	if ( wc_od_is_timestamp( $time ) ) {
		// Assume a WP timestamp (UNIX timestamp + offset).
		$time_i18n = date_i18n( $format, $time );
	} else {
		try {
			$datetime  = new WC_DateTime( $time, new DateTimeZone( wc_timezone_string() ) );
			$time_i18n = $datetime->date_i18n( $format );
		} catch ( Exception $e ) {
			$time_i18n = '';
		}
	}

	return $time_i18n;
}

/**
 * Checks if it's a valid timestamp.
 *
 * @since 1.1.0
 *
 * @param string|int $timestamp Timestamp to validate.
 *
 * @return bool True if the parameter is a timestamp. False otherwise.
 */
function wc_od_is_timestamp( $timestamp ) {
	return ( is_numeric( $timestamp ) && (int) $timestamp == $timestamp );
}

/**
 * Gets the timestamp value for the date string.
 *
 * If $date is already a timestamp (integer or string), only it's parsed to integer.
 *
 * @since 1.1.0
 *
 * @param string|int $date The date to process.
 * @return false|int The timestamp value. False for invalid values.
 */
function wc_od_get_timestamp( $date ) {
	if ( wc_od_is_timestamp( $date ) ) {
		return (int) $date;
	}

	// Disambiguate the m/d/Y and d/m/Y formats. (DateTime::createFromFormat was added on PHP 5.3).
	if ( 'd/m/Y' === wc_od_get_date_format( 'php' ) ) {
		$date = str_replace( '/', '-', $date );
	}

	return strtotime( $date );
}

/**
 * Gets the date representing the current day in the site's timezone.
 *
 * @since 1.1.0
 *
 * @param bool   $timestamp Optional. True to return a timestamp. False for a date string.
 * @param string $format    Optional. The date format.
 * @return mixed The current date string or timestamp. False on failure.
 */
function wc_od_get_local_date( $timestamp = true, $format = 'Y-m-d' ) {
	$date = current_time( $format );

	return ( $timestamp ? strtotime( $date ) : $date );
}

/**
 * Gets the date format for the specified context.
 *
 * Added 'admin' context in version 1.2.0.
 *
 * The format can be translated for each language. It uses the ISO 8601 as the default date format.
 * It is recommended to use this method only for display purposes. To make date operations is better to use the standard ISO 8601.
 *
 * @since 1.1.0
 *
 * @param string $context Optional. The context [php, js, admin].
 * @return string The date format.
 */
function wc_od_get_date_format( $context = 'php' ) {
	$use_wp_format = _x( 'yes', "Use the WordPress date format for this language? Set to 'no' to use a custom format", 'woocommerce-order-delivery' );
	$date_format   = get_option( 'date_format' );

	// Use the translated date format.
	if ( 'yes' !== $use_wp_format ) {
		$date_format = _x( 'Y-m-d', 'Custom PHP date format for this language', 'woocommerce-order-delivery' );
	}

	if ( 'js' === $context ) {
		// Convert the date format from PHP to JS. Keep this order to avoid the double conversion of some characters.
		$format_conversion = array(
			'd' => 'dd',
			'j' => 'd',
			'l' => 'DD',
			'F' => 'MM',
			'm' => 'mm',
			'n' => 'm',
			'y' => 'yy',
			'Y' => 'yyyy',
		);

		$date_format = str_replace( array_keys( $format_conversion ), array_values( $format_conversion ), $date_format );
	} elseif ( 'admin' === $context ) {
		// Use the same format as the 'date' column.
		$format = __( 'M j, Y', 'woocommerce' ); // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch

		/** This filter is documented in woocommerce/includes/admin/list-tables/class-wc-admin-list-table-orders.php */
		$date_format = apply_filters( 'woocommerce_admin_order_date_format', $format );
	}

	/**
	 * Filter the date format.
	 *
	 * @since 1.2.0
	 *
	 * @param string $date_format The date format.
	 * @param string $context     The context [php, js, admin].
	 */
	return apply_filters( 'wc_od_get_date_format', $date_format, $context );
}

/**
 * Converts a timestamp to a WC_DateTime object.
 *
 * @since 2.3.0
 *
 * @param int $timestamp The timestamp value.
 * @return WC_DateTime|false
 */
function wc_od_timestamp_to_datetime( $timestamp ) {
	try {
		$datetime = new WC_DateTime( "@{$timestamp}", new DateTimeZone( 'UTC' ) );
	} catch ( Exception $e ) {
		return false;
	}

	// Set local timezone or offset.
	if ( get_option( 'timezone_string' ) ) {
		$datetime->setTimezone( new DateTimeZone( wc_timezone_string() ) );
	} else {
		$datetime->set_utc_offset( wc_timezone_offset() );
	}

	return $datetime;
}

/**
 * Converts a date string to a WC_DateTime.
 *
 * @since 2.3.0
 *
 * @param string $date_string    Date string.
 * @param int    $from_timestamp Optional. Timestamp to convert from. Default null.
 * @return WC_DateTime
 */
function wc_od_string_to_datetime( $date_string, $from_timestamp = null ) {
	if ( ! $from_timestamp ) {
		return wc_string_to_datetime( $date_string );
	}

	$timestamp = wc_string_to_timestamp( $date_string, $from_timestamp );

	return wc_od_timestamp_to_datetime( $timestamp );
}
