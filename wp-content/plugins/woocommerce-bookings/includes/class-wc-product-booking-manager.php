<?php

/**
 * Handles product booking transitions
 */
class WC_Product_Booking_Manager {

	/**
	 * Constructor sets up actions
	 */
	public function __construct() {
		add_action( 'wp_trash_post', array( __CLASS__, 'pre_trash_delete_handler' ), 10, 1 );
		add_action( 'before_delete_post', array( __CLASS__, 'pre_trash_delete_handler' ), 10, 1 );
	}

	/**
	 * Filters whether a bookable product deletion should take place.
	 * If there are Bookings linked to it, do not allow deletion.
	 *
	 * @since 1.10.9
	 *
	 * @param int $post_id  Post ID.
	 */
	public static function pre_trash_delete_handler( $post_id ) {
		if ( ! $post_id ) {
			return;
		}

		$post_type = get_post_type( $post_id );

		if ( 'product' === $post_type ) {
			$product = wc_get_product( $post_id );

			// TODO: Figure the most performant way.
			if ( 'booking' === $product->get_type() ) {
				$bookings = WC_Booking_Data_Store::get_bookings_for_objects( $post_id );

				if ( 0 !== count( $bookings ) ) {
					$message  = __( 'You cannot trash/delete a bookable product that has Bookings associated with it.', 'woocommerce-bookings' );

					$message .= '<br/>';

					$message .= '<a href="https://docs.woocommerce.com/document/bookings-faq/">';
					$message .= __( 'Please visit our Bookings FAQs for more information', 'woocommerce-bookings' );
					$message .= '</a>.';
					wp_die( wp_kses_post( $message ) );
				}
			}
		}

		if ( 'bookable_resource' === $post_type ) {
			$resources = WC_Booking_Data_Store::get_bookings_for_objects( $post_id );

			if ( 0 !== count( $resources ) ) {
				$message  = __( 'You cannot trash/delete a resource that has Bookings associated with it.', 'woocommerce-bookings' );

				$message .= '<br/>';

				$message .= '<a href="https://docs.woocommerce.com/document/bookings-faq/">';
				$message .= __( 'Please visit our Bookings FAQs for more information', 'woocommerce-bookings' );
				$message .= '</a>.';
				wp_die( wp_kses_post( $message ) );
			}
		}

		WC_Bookings_Cache::delete_booking_slots_transient( $post_id );
	}
}
