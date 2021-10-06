<?php

/**
 * Handle frontend forms
 */
class WC_Booking_Form_Handler {

	/**
	 * Hook in methods
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'cancel_booking' ), 20 );
	}

	/**
	 * Cancel a booking.
	 */
	public static function cancel_booking() {
		if ( isset( $_GET['cancel_booking'] ) && isset( $_GET['booking_id'] ) ) {

			$booking_id         = absint( $_GET['booking_id'] );
			$booking            = get_wc_booking( $booking_id );
			$booking_can_cancel = $booking->has_status( get_wc_booking_statuses( 'cancel' ) );
			$redirect           = $_GET['redirect'];

			if ( $booking->has_status( 'cancelled' ) ) {
				// Already cancelled - take no action
			} elseif ( $booking_can_cancel && $booking->get_id() == $booking_id && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'woocommerce-bookings-cancel_booking' ) ) {
				// Cancel the booking
				$booking->update_status( 'cancelled' );
				WC_Cache_Helper::get_transient_version( 'bookings', true );

				// Message
				wc_add_notice( apply_filters( 'woocommerce_booking_cancelled_notice', __( 'Your booking was cancelled.', 'woocommerce-bookings' ) ), apply_filters( 'woocommerce_booking_cancelled_notice_type', 'notice' ) );

				do_action( 'woocommerce_bookings_cancelled_booking', $booking->get_id() );
			} elseif ( ! $booking_can_cancel ) {
				wc_add_notice( __( 'Your booking can no longer be cancelled. Please contact us if you need assistance.', 'woocommerce-bookings' ), 'error' );
			} else {
				wc_add_notice( __( 'Invalid booking.', 'woocommerce-bookings' ), 'error' );
			}

			if ( $redirect ) {
				wp_safe_redirect( $redirect );
				exit;
			}
		}
	}
}

