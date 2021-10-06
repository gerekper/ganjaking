<?php

/**
 * Cron job handler.
 */
class WC_Booking_Cron_Manager {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wc-booking-reminder', array( $this, 'send_booking_reminder' ) );
		add_action( 'wc-booking-complete', array( $this, 'maybe_mark_booking_complete' ) );
		add_action( 'wc-booking-remove-inactive-cart', array( $this, 'remove_inactive_booking_from_cart' ) );
	}

	/**
	 * Send booking reminder email
	 */
	public function send_booking_reminder( $booking_id ) {
		$booking = get_wc_booking( $booking_id );
		if ( ! is_a( $booking, 'WC_Booking' ) || ! $booking->is_active() ) {
			return;
		}

		$mailer   = WC()->mailer();
		$reminder = $mailer->emails['WC_Email_Booking_Reminder'];
		$reminder->trigger( $booking_id );
	}

	/**
	 * Change the booking status if it wasn't previously cancelled
	 */
	public function maybe_mark_booking_complete( $booking_id ) {
		$booking = get_wc_booking( $booking_id );

		//Don't procede if id is not of a valid booking
		if ( ! is_a( $booking, 'WC_Booking' ) ) {
			return;
		}

		if ( 'cancelled' === get_post_status( $booking_id ) ) {
			$booking->schedule_events();
		} else {
			$this->mark_booking_complete( $booking );
		}
	}

	/**
	 * Change the booking status to complete
	 */
	public function mark_booking_complete( $booking ) {
		$booking->update_status( 'complete' );
	}

	/**
	 * Remove inactive booking
	 */
	public function remove_inactive_booking_from_cart( $booking_id ) {
		$booking = $booking_id ? get_wc_booking( $booking_id ) : false;
		if ( $booking_id && $booking && $booking->has_status( 'in-cart' ) ) {
			wp_delete_post( $booking_id );
		}
	}
}
