<?php
/**
 * WooCommerce Bookings Timezone Tracking.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * This class adds actions to track usage of WooCommerce Bookings Timezone.
 */
class WC_Bookings_Timezone_Tracking {
	/**
	 * Init tracking.
	 */
	public function init() {
		add_action( 'wc_bookings_timezone_settings_on_save', array( $this, 'timezone_settings_on_save' ) );
	}

	/**
	 * When timezone settings page is saved.
	 *
	 * @since 1.15.0
	 * @param object $timezone_settings The timezone_settings object.
	 */
	public function timezone_settings_on_save( $timezone_settings ) {
		if ( ! is_object( $timezone_settings ) ) {
			return;
		}

		$properties = array(
			'enable_timezone_calculation' => $timezone_settings->get( 'use_server_timezone_for_actions' ),
			'timezone'                    => $timezone_settings->get( 'use_client_timezone' ),
			'display_timezone'            => $timezone_settings->get( 'display_timezone' ),
			'calendar_first_day'          => $timezone_settings->get( 'use_client_firstday' ),
		);

		WC_Bookings_Tracks::record_event( 'timezone_settings', $properties );
	}
}
