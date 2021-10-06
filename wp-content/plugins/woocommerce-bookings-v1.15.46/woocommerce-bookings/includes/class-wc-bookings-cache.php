<?php
/**
 * WC_Bookings_Cache class.
 *
 * @package WooCommerce-Bookings/Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Helper cache class.
 *
 * @since 1.15.0
 */
class WC_Bookings_Cache {
	/**
	 * Constructor.
	 *
	 * @since 1.15.0
	 */
	public function __construct() {
		add_action( 'woocommerce_booking_cancelled', array( __CLASS__, 'clear_cache' ) );
		add_action( 'before_delete_post', array( __CLASS__, 'clear_cache' ) );
		add_action( 'wp_trash_post', array( __CLASS__, 'clear_cache' ) );
		add_action( 'untrash_post', array( __CLASS__, 'clear_cache' ) );
		add_action( 'save_post', array( __CLASS__, 'clear_cache_on_save_post' ) );
		add_action( 'woocommerce_order_status_changed', array( __CLASS__, 'clear_cache' ) );
		add_action( 'woocommerce_pre_payment_complete', array( __CLASS__, 'clear_cache' ) );

		// Scheduled events.
		add_action( 'delete_booking_transients', array( __CLASS__, 'clear_cache' ) );
		add_action( 'delete_booking_dr_transients', array( __CLASS__, 'clear_cache' ) );
		add_action( 'delete_booking_ress_transients', array( __CLASS__, 'clear_cache' ) );
		add_action( 'delete_booking_res_transients', array( __CLASS__, 'clear_cache' ) );
		add_action( 'delete_booking_res_ids_transients', array( __CLASS__, 'clear_cache' ) );
	}

	/**
	 * Determines if debug mode is enabled. Used to
	 * get around stale cache when testing.
	 *
	 * @since 1.15.0
	 * @return bool
	 */
	public static function is_debug_mode() {
		return true === WC_BOOKINGS_DEBUG;
	}

	/**
	 * Gets the cache transient from db.
	 *
	 * @since 1.15.0
	 * @param string $name Name of the cache.
	 * @return mixed $data
	 */
	public static function get( $name = '' ) {
		if ( empty( $name ) || self::is_debug_mode() ) {
			return false;
		}

		return get_transient( $name );
	}

	/**
	 * Sets the cache transient to db.
	 *
	 * @since 1.15.0
	 * @param string $name Name of the cache.
	 * @param mixed  $data The data to be cached.
	 * @param int $expiration When to expire the cache.
	 * @return void
	 */
	public static function set( $name = '', $data = null, $expiration = YEAR_IN_SECONDS ) {
		set_transient( $name, $data, $expiration );
	}

	/**
	 * Deletes the cache transient from db.
	 *
	 * @since 1.15.0
	 * @param string $name Name of the cache.
	 * @return void
	 */
	public static function delete( $name = '' ) {
		delete_transient( $name );
	}

	public static function clear_cache() {
		WC_Cache_Helper::get_transient_version( 'bookings', true );

		// It only makes sense to delete transients from the DB if we're not using an external cache.
		if ( ! wp_using_ext_object_cache() ) {
			self::delete_booking_transients();
			self::delete_booking_dr_transients();
			self::delete_booking_ress_transients();
			self::delete_booking_res_transients();
			self::delete_booking_res_ids_transients();
		}
	}

	/**
	 * Clears the transients when booking is edited.
	 *
	 * @param int $post_id
	 * @return int $post_id
	 */
	public static function clear_cache_on_save_post( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		$post = get_post( $post_id );

		if ( 'wc_booking' !== $post->post_type && 'product' !== $post->post_type ) {
			return $post_id;
		}

		self::clear_cache();
	}

	/**
	 * Delete Booking Related Transients
	 */
	public static function delete_booking_transients() {
		global $wpdb;
		$limit = 1000;

		$affected_timeouts   = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s LIMIT %d;", '_transient_timeout_book_fo_%', $limit ) );
		$affected_transients = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s LIMIT %d;", '_transient_book_fo_%', $limit ) );

		// If affected rows is equal to limit, there are more rows to delete. Delete in 10 secs.
		if ( $affected_transients === $limit ) {
			wp_schedule_single_event( time() + 10, 'delete_booking_transients', array( time() ) );
		}
	}

	/**
	 * Delete Booking Date Range Related Transients
	 */
	public static function delete_booking_dr_transients() {
		global $wpdb;
		$limit = 1000;

		$affected_timeouts   = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s LIMIT %d;", '_transient_timeout_book_dr_%', $limit ) );
		$affected_transients = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s LIMIT %d;", '_transient_book_dr_%', $limit ) );

		// If affected rows is equal to limit, there are more rows to delete. Delete in 10 secs.
		if ( $affected_transients === $limit ) {
			wp_schedule_single_event( time() + 10, 'delete_booking_dr_transients', array( time() ) );
		}
	}

	/**
	 * Delete Booking Product Resources Related Transients
	 */
	public static function delete_booking_ress_transients() {
		global $wpdb;
		$limit = 1000;

		$affected_timeouts   = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s LIMIT %d;", '_transient_timeout_book_ress_%', $limit ) );
		$affected_transients = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s LIMIT %d;", '_transient_book_ress_%', $limit ) );

		// If affected rows is equal to limit, there are more rows to delete. Delete in 10 secs.
		if ( $affected_transients === $limit ) {
			wp_schedule_single_event( time() + 10, 'delete_booking_ress_transients', array( time() ) );
		}
	}

	/**
	 * Delete Booking Product Resource Related Transients
	 */
	public static function delete_booking_res_transients() {
		global $wpdb;
		$limit = 1000;

		$affected_timeouts   = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s LIMIT %d;", '_transient_timeout_book_res_%', $limit ) );
		$affected_transients = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s LIMIT %d;", '_transient_book_res_%', $limit ) );

		// If affected rows is equal to limit, there are more rows to delete. Delete in 10 secs.
		if ( $affected_transients === $limit ) {
			wp_schedule_single_event( time() + 10, 'delete_booking_res_transients', array( time() ) );
		}
	}
	/**
	 * Delete Booking Product Resource Related Transients
	 *
	 * @return void
	 * @since 1.15.17
	 */
	public static function delete_booking_res_ids_transients() {
		global $wpdb;
		$limit = 1000;

		$affected_timeouts   = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s LIMIT %d;", '_transient_timeout_book_res_ids_%', $limit ) );
		$affected_transients = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s LIMIT %d;", '_transient_book_res_ids_%', $limit ) );

		// If affected rows is equal to limit, there are more rows to delete. Delete in 10 secs.
		if ( $affected_transients === $limit ) {
			wp_schedule_single_event( time() + 10, 'delete_booking_res_ids_transients', array( time() ) );
		}
	}
	/**
	 * Clear booking list of resources transient.
	 *
	 * @param  int|null $bookable_product_id
	 * @return void
	 * @since  1.15.17
	 */
	public static function delete_booking_resources_transient( $bookable_product_id = null ) {
		$transient_name = 'book_res_ids_' . md5( http_build_query( array( $bookable_product_id, WC_Cache_Helper::get_transient_version( 'bookings' ) ) ) );
		self::delete( $transient_name );
	}

	/**
	 * Clear booking slots transient.
	 * If there are resources find connected products and clear their transients.
	 *
	 * @param  WC_Booking $booking
	 * @since  1.15.18
	 */
	public static function flush_all_booking_connected_transients( $booking ) {
		if ( 0 !== $booking->get_resource_id() ) {
			// We have a resource. Other booking products may be affected.
			$resource = $booking->get_resource();
			$resource->flush_resource_transients();
			return;
		}

		// No resource. Just flush for this booking product.
		$bookable_product_id = $booking->get_product_id();
		self::delete_booking_slots_transient( $bookable_product_id );
	}

	/**
	 * Clear booking slots transient.
	 *
	 * In contexts where we have a product id, it will only delete the specific ones.
	 * However, not all contexts will have a product id, e.g. Global Availability.
	 *
	 * @param  int|null $bookable_product_id
	 * @since  1.13.12
	 */
	public static function delete_booking_slots_transient( $bookable_product_id = null ) {
		$booking_slots_transient_keys = array_filter( (array) self::get( 'booking_slots_transient_keys' ) );

		if ( is_int( $bookable_product_id ) ) {
			if ( ! isset( $booking_slots_transient_keys[ $bookable_product_id ] ) ) {
				return;
			}

			// Get a list of flushed transients
			$flushed_transients = array_map( function( $transient_name ) {
				self::delete( $transient_name );
				return $transient_name;
			}, $booking_slots_transient_keys[ $bookable_product_id ] );

			// Remove the flushed transients referenced from other product ids (if there's such a cross-reference)
			array_walk( $booking_slots_transient_keys, function( &$transients, $bookable_product_id ) use ( $flushed_transients ) {
				$transients = array_values( array_diff( $transients, $flushed_transients ) );
			} );

			$booking_slots_transient_keys = array_filter( $booking_slots_transient_keys );

			unset( $booking_slots_transient_keys[ $bookable_product_id ] );
			self::set( 'booking_slots_transient_keys', $booking_slots_transient_keys, YEAR_IN_SECONDS );
		} else {
			$transients = array_unique( array_reduce( $booking_slots_transient_keys, function( $result, $item ) {
				return array_merge( $result, $item );
			}, array() ) );

			foreach ( $transients as $transient_key ) {
				self::delete( $transient_key );
			}

			self::delete( 'booking_slots_transient_keys' );
		}
	}
}
