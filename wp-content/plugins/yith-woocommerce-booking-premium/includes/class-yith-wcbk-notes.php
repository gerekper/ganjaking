<?php
/**
 * Class YITH_WCBK_Notes
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Notes' ) ) {
	/**
	 * Class YITH_WCBK_Notes
	 * handle Booking notes
	 */
	class YITH_WCBK_Notes {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * DB table name
		 *
		 * @var string
		 */
		public $table_name = '';

		/**
		 * YITH_WCBK_Notes constructor.
		 */
		protected function __construct() {
			global $wpdb;
			$this->table_name = $wpdb->yith_wcbk_booking_notes;
		}

		/**
		 * Add booking note
		 *
		 * @param int    $booking_id Booking ID.
		 * @param string $type       Note type.
		 * @param string $note       The note.
		 *
		 * @return false|int
		 */
		public function add_booking_note( $booking_id, $type, $note = '' ) {
			global $wpdb;

			$is_customer_note = 'customer' === $type;
			if ( $is_customer_note ) {
				WC()->mailer();
				do_action(
					'yith_wcbk_new_customer_note',
					array(
						'booking_id' => $booking_id,
						'note'       => $note,
					)
				);
			}

			return $wpdb->insert(
				$wpdb->yith_wcbk_booking_notes,
				array(
					'booking_id'  => $booking_id,
					'type'        => $type,
					'description' => $note,
					'note_date'   => current_time( 'mysql', true ),

				)
			);
		}

		/**
		 * Get booking notes
		 *
		 * @param int $booking_id The booking ID.
		 *
		 * @return array|null|object
		 */
		public function get_booking_notes( $booking_id ) {
			global $wpdb;

			return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->yith_wcbk_booking_notes} WHERE booking_id = %d ORDER by note_date DESC, id DESC", $booking_id ) );
		}

		/**
		 * Delete booking note
		 *
		 * @param int $note_id Note ID.
		 *
		 * @return false|int
		 */
		public function delete_booking_note( $note_id ) {
			global $wpdb;

			$note_id = absint( $note_id );

			return $wpdb->delete( $wpdb->yith_wcbk_booking_notes, array( 'id' => $note_id ), array( '%d' ) );
		}
	}
}


/**
 * Unique access to instance of YITH_WCBK_Notes class
 *
 * @return YITH_WCBK_Notes
 */
function yith_wcbk_notes() {
	return YITH_WCBK_Notes::get_instance();
}

if ( ! function_exists( 'yith_wcbk_delete_booking_note' ) ) {
	/**
	 * Delete booking note
	 *
	 * @param int $note_id Note ID.
	 *
	 * @return false|int
	 */
	function yith_wcbk_delete_booking_note( $note_id ) {
		return yith_wcbk_notes()->delete_booking_note( $note_id );
	}
}
