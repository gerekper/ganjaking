<?php
/**
 * Templates tracking.
 *
 * @package WooCommerce Bookings
 */

/**
 * Integrates Tracks with Bookings product templates.
 *
 * @since 2.0.0
 */
class WC_Bookings_Templates_Tracking {

	/**
	 * Initialize tracking.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'wc_booking_page_wc_bookings_product_templates', array( $this, 'track_add_product_screen_view' ) );
		add_filter( 'woocommerce_tracks_event_properties', array( $this, 'track_template_data' ), 10, 2 );

		add_action( 'woocommerce_new_product', array( $this, 'track_product_from_template_created' ) );
		add_action( 'wp_after_insert_post', array( $this, 'track_product_from_template_published' ), 10, 4 );
		add_action( 'wp_trash_post', array( $this, 'track_product_from_template_trashed_or_deleted' ), 10 );
		add_action( 'before_delete_post', array( $this, 'track_product_from_template_trashed_or_deleted' ), 10 );
	}

	/**
	 * Tracks when the Bookings > Add Product page is shown.
	 */
	public function track_add_product_screen_view() {
		WC_Bookings_Tracks::record_event( 'templates_page_view' );
	}

	/**
	 * Augments 'bookings_product_settings' and 'product_add_publish' track events with template information.
	 *
	 * @param array  $event_properties Tracks event properties.
	 * @param string $event_name       Tracks event name.
	 * @return array
	 */
	public function track_template_data( $event_properties, $event_name ) {
		if ( ! in_array( $event_name, array( 'wcadmin_bookings_product_settings', 'wcadmin_product_add_publish' ), true )
			|| ! isset( $event_properties['product_id'] ) ) {
				return $event_properties;
		}

		$product = wc_get_product( $event_properties['product_id'] );
		if ( ! $product || ! $product->is_type( 'booking' ) ) {
			return $event_properties;
		}

		$product_template = $product->get_meta( WC_Bookings_Templates::SOURCE_TEMPLATE_META_KEY, true, 'edit' );
		if ( ! $product_template ) {
			return $event_properties;
		}

		$prefix = ( 'wcadmin_product_add_publish' === $event_name ) ? 'bookings_' : '';
		$event_properties[ $prefix . 'uses_product_template' ] = true;
		$event_properties[ $prefix . 'product_template' ]      = $product_template;

		return $event_properties;
	}

	/**
	 * Tracks when a bookable product is created from a template.
	 *
	 * @param int $product_id
	 */
	public function track_product_from_template_created( $product_id ) {
		$product          = wc_get_product( $product_id );
		$product_template = $product->get_meta( WC_Bookings_Templates::SOURCE_TEMPLATE_META_KEY, true, 'edit' );

		if ( ! $product->is_type( 'booking' ) || ! $product_template ) {
			return;
		}

		WC_Bookings_Tracks::record_event(
			'product_add_from_template',
			array(
				'product_id'       => $product->get_id(),
				'product_template' => $product_template,
			)
		);
	}

	/**
	 * Tracks when a bookable product that was created from a template is published.
	 *
	 * @param int          $post_id
	 * @param object       $post
	 * @param bool         $update
	 * @param null|WP_Post $post_before
	 */
	public function track_product_from_template_published( $post_id, $post, $update, $post_before ) {
		if ( 'product' !== $post->post_type || 'publish' !== $post->post_status || ( $post_before && 'publish' === $post_before->post_status ) ) {
			return;
		}

		$product = wc_get_product( $post_id );
		if ( ! $product || ! $product->is_type( 'booking' ) ) {
			return;
		}

		$product_template = $product->get_meta( WC_Bookings_Templates::SOURCE_TEMPLATE_META_KEY, true, 'edit' );
		if ( ! $product_template ) {
			return;
		}

		WC_Bookings_Tracks::record_event(
			'product_publish_from_template',
			array(
				'product_id'       => $product->get_id(),
				'product_template' => $product_template,
			)
		);
	}

	/**
	 * Tracks when a bookable product that was created from a template is trashed or deleted.
	 *
	 * @param int $post_id
	 */
	public function track_product_from_template_trashed_or_deleted( $post_id ) {
		if ( 'product' !== get_post_type( $post_id ) ) {
			return;
		}

		$product = wc_get_product( $post_id );
		if ( ! $product || ! $product->is_type( 'booking' ) ) {
			return;
		}

		$product_template = $product->get_meta( WC_Bookings_Templates::SOURCE_TEMPLATE_META_KEY, true, 'edit' );
		if ( ! $product_template ) {
			return;
		}

		WC_Bookings_Tracks::record_event(
			( current_action() === 'wp_trash_post' ) ? 'product_trash_from_template' : 'product_delete_from_template',
			array(
				'product_id'       => $product->get_id(),
				'product_template' => $product_template,
			)
		);
	}

}
