<?php
/**
 * Class YITH_WCBK_Google_Calendar_Bookings
 * Handle booking for the Google Calendar module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\GoogleCalendar
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Google_Calendar_Bookings' ) ) {
	/**
	 * YITH_WCBK_Google_Calendar_Bookings class.
	 */
	class YITH_WCBK_Google_Calendar_Bookings {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * The constructor.
		 */
		protected function __construct() {
			YITH_WCBK_Google_Calendar_Booking_Data_Extension::get_instance();
		}
	}
}
