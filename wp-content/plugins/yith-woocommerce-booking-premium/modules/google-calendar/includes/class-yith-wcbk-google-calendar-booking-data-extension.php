<?php
/**
 * Class YITH_WCBK_Google_Calendar_Booking_Data_Extension
 * Handle booking data for the Google Calendar module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\GoogleCalendar
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Google_Calendar_Booking_Data_Extension' ) ) {
	/**
	 * YITH_WCBK_Resources_Google_Calendar_Data_Extension class.
	 */
	class YITH_WCBK_Google_Calendar_Booking_Data_Extension extends YITH_WCBK_Booking_Data_Extension {

		/**
		 * Get settings.
		 *
		 * @return array
		 */
		protected function get_settings(): array {
			return array(
				'meta_keys_to_props' => array(
					'_google_calendar_last_update' => 'google_calendar_last_update',
				),
				'internal_meta_keys' => array(
					'_google_calendar_last_update',
				),
			);
		}

		/**
		 * Read extra data.
		 *
		 * @param YITH_WCBK_Booking $booking       The booking.
		 * @param array             $updated_props The updated props.
		 */
		public function handle_updated_props( YITH_WCBK_Booking $booking, array $updated_props ) {
			$unuseful_props       = array( 'google_calendar_last_update' );
			$useful_updated_props = array_diff( $updated_props, $unuseful_props );
			if ( ! ! $useful_updated_props ) {
				// Triggered only if some props is updated, excluding the 'google_calendar_last_update' (to prevent infinite loops).
				/**
				 * DO_ACTION: yith_wcbk_google_calendar_booking_sync_on_update
				 * Used to synchronize the Google Calendar event when the booking is updated.
				 *
				 * @param YITH_WCBK_Booking $booking The booking.
				 */
				do_action( 'yith_wcbk_google_calendar_booking_sync_on_update', $booking );
			}
		}
	}
}
