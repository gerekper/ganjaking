<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore;

/**
 * Compatibility class for WooCommerce Orders
 */
class WC_Booking_Order_Compat {

	/**
	 * @var \Automattic\WooCommerce\Utilities\OrderUtil object.
	 */
	public static $order_util;

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! function_exists( 'wc_get_container' ) ) {
			return;
		}

		try {
			self::$order_util = wc_get_container()->get( Automattic\WooCommerce\Utilities\OrderUtil::class );
		} catch ( Exception $e ) {
			self::$order_util = false;
		}
	}

	/**
	 * Helper function to get whether custom order tables are enabled or not.
	 *
	 * @return bool
	 */
	public static function is_cot_enabled() {
		return self::$order_util && self::$order_util::custom_orders_table_usage_is_enabled();
	}

	/**
	 * Returns type of passed id, post or order object.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return bool Type of the order.
	 */
	public static function is_shop_order( $order_id ) {
		return self::$order_util && self::$order_util::is_order( $order_id )
		       || 'shop_order' === get_post_type( $order_id );
	}

	/**
	 * Un-trash the order.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return bool Type of the order.
	 */
	public static function untrash_post( $order_id ) {
		$orders_store = wc_get_container()->get( OrdersTableDataStore::class );
		$orders_store->untrash_order( wc_get_order( $order_id ) );
	}
}
