<?php
/**
 * WooCommerce Bookings Products Tracking.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * This class adds actions to track usage of WooCommerce Booking Products.
 */
class WC_Bookings_Products_Tracking {
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

		// When bookabled product is saved.
		if ( 'product' === get_post_type( $post_id ) ) {
			$this->record_saved_product( $post_id );
		}
	}

	/**
	 * Record event for when bookable product is saved/updated.
	 *
	 * @since 1.15.0
	 * @param int $post_id The ID of the post.
	 */
	public function record_saved_product( $post_id ) {
		$product = wc_get_product( $post_id );

		if ( ! is_object( $product ) || ! $product->is_type( 'booking' ) ) {
			return;
		}

		$product_availability = $product->get_availability();

		$properties = array(
			'product_id'                  => $product->get_id(),
			'virtual'                     => $product->is_virtual(),
			'has_persons'                 => $product->get_has_persons(),
			'has_resources'               => $product->get_has_resources(),
			'duration_type'               => $product->get_duration_type(),
			'duration'                    => $product->get_duration(),
			'duration_unit'               => $product->get_duration_unit(),
			'min_duration'                => $product->get_min_duration(),
			'max_duration'                => $product->get_max_duration(),
			'calendar_display_mode'       => $product->get_calendar_display_mode(),
			'requires_confirmation'       => $product->get_requires_confirmation(),
			'user_can_cancel'             => $product->get_user_can_cancel(),
			'max_bookings_per_block'      => $product->get_qty(),
			'minimum_block_bookable'      => $product->get_min_date_value(),
			'minimum_block_bookable_unit' => $product->get_min_date_unit(),
			'maximum_block_bookable'      => $product->get_max_date_value(),
			'maximum_block_bookable_unit' => $product->get_max_date_unit(),
			'buffer_period'               => $product->get_buffer_period(),
			'adjacent_buffer'             => $product->get_apply_adjacent_buffer(),
			'default_date_availability'   => $product->get_default_date_availability(),
			'check_rules_against'         => $product->get_check_start_block_only(),
			'first_block_time'            => $product->get_first_block_time(),
			'has_restricted_days'         => $product->get_has_restricted_days(),
			'display_cost'                => $product->get_display_cost(),

		);

		if ( $product->get_has_persons() ) {
			$properties['min_persons']                = $product->get_min_persons();
			$properties['max_persons']                = $product->get_max_persons();
			$properties['has_person_cost_multiplier'] = $product->get_has_person_cost_multiplier();
			$properties['has_person_qty_multiplier']  = $product->get_has_person_qty_multiplier();
			$properties['has_person_types']           = $product->get_has_person_types();
		}

		if ( $product->get_has_resources() ) {
			$properties['resources_assignment'] = $product->get_resources_assignment();
		}

		WC_Bookings_Tracks::record_event( 'product_settings', $properties );
	}

	/**
	 * Before the page has rendered to the screen.
	 *
	 * @since 1.15.0
	 * @param object $screen Current screen.
	 */
	public function current_screen( $screen ) {
		$current_screen = $screen;

		// View bookable product screen.
		if ( 'product' === $current_screen->post_type && 'product' === $current_screen->id ) {
			if ( ! isset( $_GET['post'] ) ) {
				return;
			}

			$post_id = intval( $_GET['post'] );

			if ( 'product' !== get_post_type( $post_id ) ) {
				return;
			}

			$product = wc_get_product( $post_id );

			if ( ! is_object( $product ) || ! $product->is_type( 'booking' ) ) {
				return;
			}

			WC_Bookings_Tracks::record_event( 'booking_product_view' );
		}
	}
}
