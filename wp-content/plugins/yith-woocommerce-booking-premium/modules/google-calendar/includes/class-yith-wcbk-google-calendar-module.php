<?php
/**
 * Class YITH_WCBK_Google_Calendar_Module
 * Handle the Google Calendar module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\GoogleCalendar
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Google_Calendar_Module' ) ) {
	/**
	 * YITH_WCBK_Google_Calendar_Module class.
	 *
	 * @since   4.0
	 */
	class YITH_WCBK_Google_Calendar_Module extends YITH_WCBK_Module {

		const KEY = 'google-calendar';

		/**
		 * On load.
		 */
		public function on_load() {
			YITH_WCBK_Google_Calendar_Sync::get_instance();
			YITH_WCBK_Google_Calendar_Bookings::get_instance();
		}
	}
}
