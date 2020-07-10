<?php
/**
 * WooCommerce Bookings Resources Tracking.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * This class adds actions to track usage of WooCommerce Booking Resources.
 */
class WC_Bookings_Resources_Tracking {
	/**
	 * Init tracking.
	 */
	public function init() {
		add_action( 'save_post', array( $this, 'on_save_post' ) );
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

		// When a booking resource is saved.
		if ( 'bookable_resource' === get_post_type( $post_id ) ) {
			$this->record_saved_resource( $post_id );
		}
	}

	/**
	 * When resource is saved.
	 *
	 * @since 1.15.0
	 * @param int $post_id The ID of the resource.
	 */
	public function record_saved_resource( $post_id ) {
		$resource = get_post( $post_id );

		if ( ! is_object( $resource ) ) {
			return;
		}

		WC_Bookings_Tracks::record_event( 'edit_detail_resource' );
	}

	/**
	 * Before the page has rendered to the screen.
	 *
	 * @since 1.15.0
	 * @param object $screen Current screen.
	 */
	public function current_screen( $screen ) {
		$current_screen = $screen;

		// View resources screen.
		if ( 'bookable_resource' === $current_screen->post_type && 'edit-bookable_resource' === $current_screen->id ) {
			WC_Bookings_Tracks::record_event( 'view_all_resources' );
		}
	}
}
