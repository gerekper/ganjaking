<?php
/**
 * Formatting functions
 *
 * @package WC_Instagram/Functions
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Convert a timestamp to a WC_DateTime.
 *
 * @since 4.0.0
 *
 * @param int $timestamp The UNIX timestamp.
 * @return WC_DateTime|false
 */
function wc_instagram_timestamp_to_datetime( $timestamp ) {
	if ( ! is_numeric( $timestamp ) ) {
		return false;
	}

	try {
		$datetime = new WC_DateTime( "@{$timestamp}", new DateTimeZone( 'UTC' ) );

		// Set local timezone or offset.
		if ( get_option( 'timezone_string' ) ) {
			$datetime->setTimezone( new DateTimeZone( wc_timezone_string() ) );
		} else {
			$datetime->set_utc_offset( wc_timezone_offset() );
		}
	} catch ( Exception $e ) {
		$datetime = false;
	}

	return $datetime;
}

/**
 * Converts a datetime to a human-readable format.
 *
 * @since 4.0.0
 *
 * @param WC_DateTime $datetime The datetime.
 * @return string
 */
function wc_instagram_format_datetime( $datetime ) {
	if ( ! $datetime instanceof WC_DateTime ) {
		return '';
	}

	return sprintf(
		/* translators: 1: formatted date 2: formatted time */
		__( '%1$s at %2$s', 'woocommerce' ), // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
		$datetime->date_i18n( wc_date_format() ),
		$datetime->date_i18n( wc_time_format() )
	);
}
