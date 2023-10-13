<?php
/**
 * Class YITH_WCBK_Background_Process_Google_Calendar_Sync
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Background_Process_Google_Calendar_Sync' ) ) {
	/**
	 * Class YITH_WCBK_Background_Process_Google_Calendar_Sync
	 * handle Google Calendar Sync in background
	 */
	class YITH_WCBK_Background_Process_Google_Calendar_Sync extends YITH_WCBK_Background_Process {
		/**
		 * Retrieve the action type
		 *
		 * @return string
		 */
		public function get_action_type() {
			return 'google_calendar_sync';
		}

		/**
		 * Return a list of notices to show
		 *
		 * @return array
		 */
		public function get_notices() {
			return array(
				'start'    => __( 'Google Calendar Sync - your bookable products are being synchronized in the background.', 'yith-booking-for-woocommerce' ),
				'running'  => __( 'Google Calendar Sync - your bookable products are being synchronized in the background.', 'yith-booking-for-woocommerce' ),
				'complete' => __( 'Google Calendar Sync - synchronization completed!', 'yith-booking-for-woocommerce' ),
			);
		}
	}
}
