<?php
/**
 * A class to manage date and time frames availability.
 *
 * @package WC_OD/Classes
 * @since 1.8.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_OD_Delivery_Cache' ) ) {
	return;
}

/**
 * Class WC_OD_Delivery_Cache
 */
class WC_OD_Delivery_Cache extends WC_OD_Singleton {

	const CACHE_EXPIRES      = 3600;
	const ORDER_CACHE_PREFIX = 'wc-od_order_';

	/**
	 * WC_OD_Delivery_Cache constructor.
	 */
	public function __construct() {
		parent::__construct();

		add_action( 'woocommerce_update_order', array( $this, 'on_order_updated' ), 10, 1 );
		add_action( 'wp_trash_post', array( $this, 'on_order_deleted' ) );
	}

	/**
	 * Removes the cache for the day and time frame when an order is updated.
	 *
	 * This action is called always when an order is created or updated, so is the best place to remove the cache.
	 * Also, it's called with the old and the new data, so it will remove both cache keys (day and time frame before and
	 * after updating).
	 *
	 * @since 1.8.0
	 *
	 * @param int $order_id The order ID.
	 */
	public function on_order_updated( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		$cache_key = $this->get_order_cache_key( $order );
		$this->delete( $cache_key );
	}

	/**
	 * Removes the cache key when a 'shop_order' post is trashed.
	 *
	 * @param int $id The post ID.
	 */
	public function on_order_deleted( $id ) {
		$type = get_post_type( $id );

		if ( 'shop_order' !== $type ) {
			return;
		}

		$this->on_order_updated( $id );
	}

	/**
	 * Retrieves the cache key where the order delivery data can be store. It's formed by
	 * the delivery day and the time frames.
	 *
	 * @param WC_Order $order The order object.
	 *
	 * @return string
	 */
	public function get_order_cache_key( $order ) {
		$delivery_date       = $order->get_meta( '_delivery_date' );
		$delivery_time_frame = $order->get_meta( '_delivery_time_frame' );

		$args = array( $delivery_date );

		if ( is_array( $delivery_time_frame ) ) {
			$args = array_merge( $args, $delivery_time_frame );
		}

		return $this->build_cache_key( self::ORDER_CACHE_PREFIX, $args );
	}

	/**
	 * Removes all the cached transients related to the orders cache.
	 *
	 * @return bool|int
	 */
	public function remove_order_cache() {
		global $wpdb;

		$sql    = 'DELETE FROM ' . $wpdb->options . ' WHERE option_name LIKE "%' .self::ORDER_CACHE_PREFIX . '%"' ;
		$result = $wpdb->query( $sql );

		return $result;
	}

	/**
	 * Returns a string formed by the $prefix and all the $args.
	 *
	 * @param string $prefix The prefix to add.
	 * @param array  $args Array of string to form the key.
	 *
	 * @return string
	 */
	public function build_cache_key( $prefix, $args = array() ) {
		if ( ! is_array( $args ) ) {
			return $prefix;
		}

		$args = array_filter( $args, 'is_string' );

		return $prefix . implode( '_', $args );
	}

	/**
	 * Adds a record to cache.
	 *
	 * @param int|string $key The cache key to use for retrieval later.
	 * @param mixed      $value The data to add to the cache.
	 * @return bool True if the value was set, false otherwise.
	 */
	public function write( $key, $value ) {
		return set_transient( $key, $value, self::CACHE_EXPIRES );
	}

	/**
	 * Reads a record from cache.
	 *
	 * @param int|string $key The key under which the cache contents are stored.
	 * @return mixed Value of transient.
	 */
	public function read( $key ) {
		return get_transient( $key );
	}

	/**
	 * Deletes a record from cache.
	 *
	 * @param int|string $key   The key under which the cache contents are stored.
	 * @return bool True on successful removal, false on failure.
	 */
	public function delete( $key ) {
		return delete_transient( $key );
	}
}

