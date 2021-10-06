<?php
/**
 * WooCommerce Bookings Calendar Tracking.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * This class adds actions to track usage of WooCommerce Bookings Calendar.
 */
class WC_Bookings_Calendar_Tracking {
	/**
	 * Init tracking.
	 */
	public function init() {
		add_action( 'wc_bookings_calendar_settings_on_save', array( $this, 'calendar_settings_on_save' ) );
		add_action( 'current_screen', array( $this, 'current_screen' ) );
	}

	/**
	 * When calendar settings page is saved.
	 *
	 * @since 1.15.0
	 * @param object $calendar The calendar object.
	 */
	public function calendar_settings_on_save( $calendar ) {
		if ( ! is_object( $calendar ) ) {
			return;
		}

		$properties = array(
			'sync_preference' => $calendar->settings['sync_preference'],
			'debug'           => $calendar->settings['debug'],
			'calendar_id'     => '',
		);

		if ( '' !== $calendar->settings['calendar_id'] ) {
			$properties['calendar_id'] = 'set';
		}

		WC_Bookings_Tracks::record_event( 'calendar_settings', $properties );
	}

	/**
	 * Before the page has rendered to the screen.
	 *
	 * @since 1.15.0
	 * @param object $screen Current screen.
	 */
	public function current_screen( $screen ) {
		$current_screen = $screen;
		// View booking calendar screen.
		if ( 'wc_booking' === $current_screen->post_type && 'wc_booking_page_booking_calendar' === $current_screen->id ) {
			if ( isset( $_GET['view'] ) ) {
				switch ( $_GET['view'] ) {
					case 'schedule':
						WC_Bookings_Tracks::record_event( 'calendar_view_schedule' );
						break;
					case 'month':
						WC_Bookings_Tracks::record_event( 'calendar_view_month' );
						break;
					default:
						WC_Bookings_Tracks::record_event( 'calendar_view_day' );
						break;
				}
			} else {
				WC_Bookings_Tracks::record_event( 'calendar_view_day' );
			}

			if ( ! empty( $_GET['filter_bookings_product'] ) ) {
				WC_Bookings_Tracks::record_event( 'calendar_filtered_products' );
			}

			if ( ! empty( $_GET['filter_bookings_resource'] ) ) {
				WC_Bookings_Tracks::record_event( 'calendar_filtered_resources' );
			}
		}
	}
}
