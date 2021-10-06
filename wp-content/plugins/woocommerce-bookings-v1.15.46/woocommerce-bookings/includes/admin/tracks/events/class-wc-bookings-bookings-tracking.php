<?php
/**
 * WooCommerce Bookings Bookings Tracking.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * This class adds actions to track usage of WooCommerce Bookings Bookings.
 */
class WC_Bookings_Bookings_Tracking {
	/**
	 * Init tracking.
	 */
	public function init() {
		add_action( 'save_post', array( $this, 'on_save_post' ) );
		add_action( 'woocommerce_bookings_created_manual_booking', array( $this, 'create_manual_booking' ) );
		add_action( 'current_screen', array( $this, 'current_screen' ) );
	}

	/**
	 * When booking product is saved.
	 *
	 * @since 1.15.0
	 * @param int $post_id The ID of the post.
	 */
	public function on_save_post( $post_id ) {
		if ( empty( $post_id ) ) {
			return;
		}

		// When a booking is saved.
		if ( 'wc_booking' === get_post_type( $post_id ) ) {
			$this->record_saved_booking( $post_id );
		}
	}

	/**
	 * When booking is saved.
	 *
	 * @since 1.15.0
	 * @param int $post_id The ID of the booking.
	 */
	public function record_saved_booking( $post_id ) {
		$booking = new WC_Booking( $post_id );

		if ( ! is_object( $booking ) || ! is_a( $booking, 'WC_Booking' ) ) {
			return;
		}

		WC_Bookings_Tracks::record_event( 'edit_detail_booking' );
	}

	/**
	 * When create manual booking is triggered.
	 *
	 * @since 1.15.0
	 * @param object $booking The booking object created.
	 */
	public function create_manual_booking( $booking ) {
		if ( ! is_object( $booking ) ) {
			return;
		}

		if ( ! is_a( $booking, 'WC_Booking' ) ) {
			return;
		}

		WC_Bookings_Tracks::record_event( 'manual_booking_add' );
	}

	/**
	 * Before the page has rendered to the screen.
	 *
	 * @since 1.15.0
	 * @param object $screen Current screen.
	 */
	public function current_screen( $screen ) {
		$current_screen = $screen;

		// View all bookings screen.
		if ( 'wc_booking' === $current_screen->post_type && 'edit-wc_booking' === $current_screen->id ) {
				WC_Bookings_Tracks::record_event( 'view_all_bookings' );
		}

		// Add booking screen.
		if ( 'wc_booking' === $current_screen->post_type && 'wc_booking_page_create_booking' === $current_screen->id ) {
				WC_Bookings_Tracks::record_event( 'manual_booking_view' );
		}
	}
}
