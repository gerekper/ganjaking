<?php
/**
 * Admin AJAX hooks and functions
 *
 * @package WC_OD
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/** Calendar AJAX functions ***************************************************/

/**
 * Fetchs the calendar events.
 *
 * @since 1.0.0
 */
function wc_od_calendar_fetch_events() {
	$filters = $_POST['filters'];
	if ( isset( $filters['timezone'] ) && 'false' === $filters['timezone'] ) {
		$filters['timezone'] = false;
	}

	$events = wc_od_get_events( $filters );

	wp_send_json( $events );
}
add_action( 'wp_ajax_wc_od_calendar_fetch_events', 'wc_od_calendar_fetch_events' );

/**
 * Adds an event to the correct list depending on the event type.
 *
 * @since 1.0.0
 */
function wc_od_calendar_add_event() {
	$event          = $_POST['event'];
	$event_type     = $event['type'];
	$index_setting  = $event_type . '_events_index';
	$events_setting = $event_type . '_events';
	$event_index    = WC_OD()->settings()->get_setting( $index_setting );
	$events         = WC_OD()->settings()->get_setting( $events_setting );

	if ( false !== $event_index && false !== $events ) {
		$event['id'] = $event_index;
		$event       = wc_od_parse_event( $event );

		$events[ $event_index ] = $event;
		$event_index++;

		// Updates the settings.
		WC_OD()->settings()->update_setting( $index_setting, $event_index );
		WC_OD()->settings()->update_setting( $events_setting, $events );

		$response = array(
			'status' => 'success',
			'event'  => $event,
		);
	} else {
		$response = array(
			'status'  => 'error',
			'message' => __( 'Invalid event type.', 'woocommerce-order-delivery' ),
		);
	}

	wp_send_json( $response );
}
add_action( 'wp_ajax_wc_od_calendar_add_event', 'wc_od_calendar_add_event' );

/**
 * Updates an event.
 *
 * @since 1.0.0
 */
function wc_od_calendar_update_event() {
	$event          = $_POST['event'];
	$event_type     = $event['type'];
	$events_setting = $event_type . '_events';
	$events         = WC_OD()->settings()->get_setting( $events_setting );

	if ( false !== $events ) {
		if ( isset( $event['id'] ) && isset( $events[ $event['id'] ] ) ) {
			$event = wc_od_parse_event( $event );
			$events[ $event['id'] ] = $event;

			WC_OD()->settings()->update_setting( $events_setting, $events );

			$response = array(
				'status' => 'success',
				'event'  => $event,
			);
		} else {
			$response = array(
				'status'  => 'error',
				'message' => __( 'Invalid event ID.', 'woocommerce-order-delivery' ),
			);
		}
	} else {
		$response = array(
			'status'  => 'error',
			'message' => __( 'Invalid event type.', 'woocommerce-order-delivery' ),
		);
	}

	wp_send_json( $response );
}
add_action( 'wp_ajax_wc_od_calendar_update_event', 'wc_od_calendar_update_event' );

/**
 * Deletes an event.
 *
 * @since 1.0.0
 */
function wc_od_calendar_delete_event() {
	$event          = $_POST['event'];
	$event_type     = $event['type'];
	$events_setting = $event_type . '_events';
	$events         = WC_OD()->settings()->get_setting( $events_setting );

	if ( false !== $events ) {
		if ( isset( $event['id'] ) && isset( $events[ $event['id'] ] ) ) {
			unset( $events[ $event['id'] ] );

			WC_OD()->settings()->update_setting( $events_setting, $events );

			$response = array(
				'status' => 'success',
				'event'  => $event,
			);
		} else {
			$response = array(
				'status'  => 'error',
				'message' => __( 'Invalid event ID.', 'woocommerce-order-delivery' ),
			);
		}
	} else {
		$response = array(
			'status'  => 'error',
			'message' => __( 'Invalid event type.', 'woocommerce-order-delivery' ),
		);
	}

	wp_send_json( $response );
}
add_action( 'wp_ajax_wc_od_calendar_delete_event', 'wc_od_calendar_delete_event' );

/**
 * Parse the event parameters.
 *
 * @since 1.0.0
 * @param array $event The event data.
 * @return array The parsed event data.
 */
function wc_od_parse_event( $event ) {
	// Remove end date if it is empty or equal to the start date.
	if ( '' === $event['end'] || $event['end'] === $event['start'] ) {
		unset( $event['end'] );
	}

	// Remove empty country parameter.
	if ( isset( $event['country'] ) && ! $event['country'] ) {
		unset( $event['country'] );
	}

	// Remove empty states parameter.
	if ( isset( $event['states'] ) && ! $event['states'] ) {
		unset( $event['states'] );
	}

	// Remove event type.
	if ( isset( $event['type'] ) ) {
		unset( $event['type'] );
	}

	return $event;
}
