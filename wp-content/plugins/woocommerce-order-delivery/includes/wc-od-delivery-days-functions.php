<?php
/**
 * Delivery days functions
 *
 * @package WC_OD/Functions
 * @since   1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the delivery days collection.
 *
 * @since 1.6.0
 *
 * @param mixed $delivery_days Optional. Delivery days collection or data.
 * @return WC_OD_Collection_Delivery_Days
 */
function wc_od_get_delivery_days( $delivery_days = null ) {
	if ( empty( $delivery_days ) ) {
		$delivery_days = WC_OD()->settings()->get_setting( 'delivery_days' );
	}

	if ( ! $delivery_days instanceof WC_OD_Collection_Delivery_Days ) {
		$delivery_days = new WC_OD_Collection_Delivery_Days( $delivery_days );
	}

	return $delivery_days;
}

/**
 * Gets the specified delivery day.
 *
 * @since 1.5.0
 * @since 1.6.0 Accepts a WC_OD_Delivery_Day object as the parameter. Returns a WC_OD_Delivery_Day object.
 *
 * @param mixed $delivery_day WC_OD_Delivery_Day object, an array with the delivery day data or the weekday number.
 * @return WC_OD_Delivery_Day|null The delivery date object. Null on failure.
 */
function wc_od_get_delivery_day( $delivery_day ) {
	if ( is_numeric( $delivery_day ) ) {
		$delivery_days = wc_od_get_delivery_days();
		$delivery_day  = $delivery_days->get( intval( $delivery_day ) );
	} elseif ( is_array( $delivery_day ) ) {
		$delivery_day = new WC_OD_Delivery_Day( $delivery_day );
	}

	return $delivery_day;
}

/**
 * Gets the status (enabled or disabled) of the delivery day for the specified arguments.
 *
 * @since 1.5.0
 * @since 1.6.0 Accepts a WC_OD_Delivery_Day object as the parameter.
 *
 * @param mixed  $delivery_day WC_OD_Delivery_Day object, an array with the delivery day data or the weekday number.
 * @param array  $args         Optional. The arguments.
 * @param string $context      Optional. The context.
 * @return mixed The delivery day status ('yes' or 'no'). Null on failure.
 */
function wc_od_get_delivery_day_status( $delivery_day, $args = array(), $context = '' ) {
	$delivery_day = wc_od_get_delivery_day( $delivery_day );

	if ( ! $delivery_day ) {
		return null;
	}

	$defaults = array(
		'shipping_method' => false,
	);

	/**
	 * Filters the arguments used to calculate the status of the delivery day.
	 *
	 * @since 1.5.0
	 * @since 1.6.0 The `$delivery_day` parameter is a WC_OD_Delivery_Day object.
	 *
	 * @param array              $args         The arguments.
	 * @param WC_OD_Delivery_Day $delivery_day The delivery day.
	 * @param string             $context      The context.
	 */
	$args = apply_filters( 'wc_od_get_delivery_day_status_args', wp_parse_args( $args, $defaults ), $delivery_day, $context );

	$status = $delivery_day->get_enabled();

	if ( $delivery_day->is_enabled() && $args['shipping_method'] ) {
		$status = wc_bool_to_string( $delivery_day->validate_shipping_method( $args['shipping_method'] ) );
	}

	/**
	 * Filters the status of the delivery day.
	 *
	 * @since 1.5.0
	 * @since 1.6.0 The `$delivery_day` parameter is a WC_OD_Delivery_Day object.
	 *
	 * @param string             $status       The delivery day status. Accepts 'yes', 'no'.
	 * @param WC_OD_Delivery_Day $delivery_day The delivery day.
	 * @param array              $args         The arguments.
	 * @param string             $context      The context.
	 */
	return apply_filters( 'wc_od_get_delivery_day_status', $status, $delivery_day, $args, $context );
}

/**
 * Gets the status (enabled or disabled) of the delivery days for the specified arguments.
 *
 * @since 1.5.0
 * @since 1.6.0 The parameter `$delivery_days` also accepts a WC_OD_Collection_Delivery_Days object.
 *
 * @param mixed  $delivery_days Optional. WC_OD_Collection_Delivery_Days object or an array with the delivery days data.
 * @param array  $args          Optional. The arguments.
 * @param string $context       Optional. The context.
 * @return array
 */
function wc_od_get_delivery_days_status( $delivery_days = array(), $args = array(), $context = '' ) {
	$delivery_days = wc_od_get_delivery_days( $delivery_days );
	$statuses      = array();

	foreach ( $delivery_days as $index => $delivery_day ) {
		$statuses[ $index ] = wc_od_get_delivery_day_status( $delivery_day, $args, $context );
	}

	/**
	 * Filters the status of the delivery days.
	 *
	 * @since 1.5.0
	 * @since 1.6.0 The `$delivery_days` parameter is a WC_OD_Collection_Delivery_Days object.
	 *
	 * @param array                          $statuses      An array with the status of each delivery day.
	 * @param WC_OD_Collection_Delivery_Days $delivery_days The delivery days.
	 * @param array                          $args          The arguments.
	 * @param string                         $args          The context.
	 */
	return apply_filters( 'wc_od_get_delivery_days_status', $statuses, $delivery_days, $args, $context );
}
