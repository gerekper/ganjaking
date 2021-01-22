<?php
/**
 * A class to manage a range of delivery dates.
 *
 * @package WC_OD/Classes
 * @since   1.8.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_OD_Delivery_Dates
 */
class WC_OD_Delivery_Dates {

	/**
	 * Gets the available dates for delivery.
	 *
	 * @since 1.8.0
	 * @since 1.8.5 Added `$format` parameter.
	 *
	 * @param array $args {
	 *      Associative array with arguments.
	 *
	 *      @type string $start_date
	 *      @type string $end_date
	 *      @type array  $disabled_dates
	 * }.
	 *
	 * @param string $format The date format. Default: empty (timestamp).
	 * @return array
	 */
	public static function get_disabled_dates( $args, $format = '' ) {
		$args = wp_parse_args(
			$args,
			array(
				'start_date'     => strtotime( wc_od_get_first_shipping_date( array() ) ),
				'end_date'       => strtotime( ( WC_OD()->settings()->get_setting( 'max_delivery_days' ) + 1 ) . ' days', wc_od_get_local_date() ), // The maximum date (Non-inclusive) to look for a valid date.
				'disabled_dates' => array(),
				'delivery_days'  => WC_OD()->settings()->get_setting( 'delivery_days' ),
			)
		);

		$disabled_dates = array();
		$index          = 0;

		do {
			$timestamp     = strtotime( "+{$index} days", $args['start_date'] );
			$weekday       = date( 'w', $timestamp );
			$delivery_day  = new WC_OD_Delivery_Day( $args['delivery_days'][ $weekday ], $weekday );
			$delivery_date = new WC_OD_Delivery_Date( $timestamp, $delivery_day );

			if (
				in_array( date( 'Y-m-d', $timestamp ), $args['disabled_dates'], true ) ||
				! $delivery_date->is_valid()
			) {
				$disabled_dates[] = ( $format ? date( $format, $timestamp ) : $timestamp );
			}

			$index++;
		} while ( $timestamp < $args['end_date'] );

		return apply_filters( 'wc_od_get_disabled_dates', $disabled_dates );
	}
}
