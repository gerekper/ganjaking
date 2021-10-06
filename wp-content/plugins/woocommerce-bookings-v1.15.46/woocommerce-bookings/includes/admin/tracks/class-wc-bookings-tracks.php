<?php
/**
 * Bookings-specific Tracks implementation.
 */
class WC_Bookings_Tracks {
	/**
	 * Tracks event name prefix.
	 */
	const PREFIX = 'bookings_';

	/**
	 * Constructor.
	 *
	 * @since 1.15.0
	 */
	public function __construct() {
		$tracking_classes = array(
			'WC_Bookings_Products_Tracking',
			'WC_Bookings_Bookings_Tracking',
			'WC_Bookings_Global_Availability_Tracking',
			'WC_Bookings_Timezone_Tracking',
			'WC_Bookings_Calendar_Tracking',
			'WC_Bookings_Notification_Tracking',
			'WC_Bookings_Resources_Tracking',
		);

		foreach ( $tracking_classes as $tracking_class ) {
			$tracker_instance    = new $tracking_class();
			$tracker_init_method = array( $tracker_instance, 'init' );

			if ( is_callable( $tracker_init_method ) ) {
				call_user_func( $tracker_init_method );
			}
		}
	}

	/**
	 * Record an event in Tracks - this is the preferred way to record events from PHP.
	 *
	 * @param string $event_name The name of the event.
	 * @param array  $properties Custom properties to send with the event.
	 */
	public static function record_event( $event_name, $properties = array() ) {
		// Include base properties.
		$base_properties = array(
			'bookings_version'    => WC_BOOKINGS_VERSION,
			'bookings_db_version' => WC_BOOKINGS_DB_VERSION,
		);

		$properties = array_merge( $base_properties, $properties );

		$full_event_name = self::PREFIX . $event_name;

		if ( class_exists( 'WC_Tracks' ) && WC_Site_Tracking::is_tracking_enabled() ) {
			WC_Tracks::record_event( $full_event_name, $properties );
		}
	}
}
