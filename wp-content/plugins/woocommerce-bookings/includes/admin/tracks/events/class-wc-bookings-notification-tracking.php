<?php
/**
 * WooCommerce Bookings Notification Tracking.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * This class adds actions to track usage of WooCommerce Bookings Notification.
 */
class WC_Bookings_Notification_Tracking {
	/**
	 * Init tracking.
	 */
	public function init() {
		add_action( 'wc_bookings_notification_sent', array( $this, 'on_notification_sent' ) );
		add_action( 'current_screen', array( $this, 'current_screen' ) );
	}

	/**
	 * When manual booking notification is sent.
	 *
	 * @since 1.15.0
	 */
	public function on_notification_sent() {
		WC_Bookings_Tracks::record_event( 'notification_sent' );
	}

	/**
	 * Before the page has rendered to the screen.
	 *
	 * @since 1.15.0
	 * @param object $screen Current screen.
	 */
	public function current_screen( $screen ) {
		$current_screen = $screen;

		// Send notification screen.
		if ( 'wc_booking' === $current_screen->post_type && 'wc_booking_page_booking_notification' === $current_screen->id ) {
				WC_Bookings_Tracks::record_event( 'notification_view' );
		}
	}
}
