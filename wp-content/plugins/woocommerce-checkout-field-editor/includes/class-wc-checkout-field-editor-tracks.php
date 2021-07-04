<?php
/**
 * WC_Checkout_Field_Editor_Tracks
 */
class WC_Checkout_Field_Editor_Tracks {
	/**
	 * Tracks event name prefix.
	 */
	const PREFIX = 'checkout_field_editor_';

	/**
	 * Record an event in Tracks - this is the preferred way to record events from PHP.
	 *
	 * @param string $event_name The name of the event.
	 * @param array  $properties Custom properties to send with the event.
	 * @return bool|WP_Error True for success or WP_Error if the event pixel could not be fired.
	 */
	public static function record_event( $event_name, $properties = array() ) {
		$full_event_name = self::PREFIX . $event_name;

		if ( class_exists( 'WC_Tracks' ) && WC_Site_Tracking::is_tracking_enabled() ) {
			WC_Tracks::record_event( $full_event_name, $properties );
		}
	}
}
