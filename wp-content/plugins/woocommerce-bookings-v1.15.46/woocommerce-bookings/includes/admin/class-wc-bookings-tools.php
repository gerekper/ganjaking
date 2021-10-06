<?php

/**
 * Booking tools
 */
class WC_Bookings_Tools {
	public static $id = 'bookings-tools';

	/*
	 * Clean person types tool.
	 */
	public static function clean_person_types() {
		global $wpdb;

		$unused_types = WC_Product_Booking_Data_Store_CPT::get_person_types_ids();
		$booking_products = WC_Bookings_Admin::get_booking_products();

		if ( class_exists( 'WC_Logger' ) ) {
			$logger = new WC_Logger();
		} else {
			$logger = WC()->logger();

		}

		$logger->add( self::$id, 'Called clean_person_types tool.' );

		foreach ( $booking_products as $product ) {
			$bookings = WC_Booking_Data_Store::get_bookings_for_product( $product->get_id(), array() );

			$used_types = array();

			// get the person types from the product
			$used_types = array_merge( $used_types, array_keys( $product->get_person_types() ) );

			// get the person types from all the bookings related to the product
			foreach ( $bookings as $booking ) {
				$used_types = array_unique( array_merge( $used_types, array_keys( $booking->get_person_counts() ) ) );
			}

			$unused_types = array_diff( $unused_types, $used_types );
		}

		$logger->add( self::$id, 'Found ' . count( $unused_types ) . ' unused person types. Removing them from DB.' );

		foreach ( $unused_types as $unused_id ) {
			$wpdb->delete(
				$wpdb->posts,
				array(
					'ID' => $unused_id,
					'post_type' => 'bookable_person',
				)
			);
		}
	}

	/**
	 * Removes In Cart bookings
	 *
	 * @param  string  $remove  If set to 'all' it will remove all In Cart, otherwise, just expired ones. 
	 *
	 * @return string  Message being returned after the system tool is run.
	 */
	public static function remove_in_cart_bookings( $remove = 'expired' ) {
		$minutes = apply_filters( 'woocommerce_bookings_remove_inactive_cart_time', 60 );
		$minutes = empty( $minutes ) ? 60 : $minutes;

		$args = array(
			'post_type'   => 'wc_booking',
			'post_status' => 'in-cart',
			'date_query'  => array(
				array(
					'column' => 'post_date_gmt',
					'before' => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) - ( $minutes * 60 ) ),
				),
			),
			'posts_per_page' => -1,
		);

		// We set this, then remove it if there's a match because we don't always want to remove all. 
		if ( 'all' === $remove ) {
			unset( $args['date_query'] );
		}

		$results = new WP_Query( $args );

		foreach ( $results->posts as $post ) {
			wp_delete_post( $post->ID );
		}
		/* translators: %s: Number of posts that have expired */
		return sprintf( __( 'Removed %s expired In Cart booking(s).', 'woocommerce-bookings' ) , $results->found_posts );
	}

	/**
	 * Removes the relationship between the resource and bookable product.
	 *
	 * @since 1.14.0
	 * @param int $product_id The product ID to unlink.
	 * @return bool
	 */
	public static function unlink_resource( $product_id = null ) {
		if ( is_null( $product_id ) ) {
			return false;
		}

		global $wpdb;

		$wpdb->delete( $wpdb->prefix . 'wc_booking_relationships', array( 'product_id' => $product_id ), array( '%d' ) );
	}
}
