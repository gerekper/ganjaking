<?php
/**
 * WC_Deposits_COT_Compatibility class
 *
 * @package  WooCommerce Deposits
 * @since    1.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for WC COT compatibility.
 *
 * @class    WC_Deposits_COT_Compatibility
 * @version  1.6.2
 */
class WC_Deposits_COT_Compatibility {

	/**
	 * Init.
	 */
	public static function init() {
		// Save current rest request. Is there a better way to get it?
		add_filter( 'woocommerce_order_list_table_prepare_items_query_args', array( __CLASS__, 'modify_query_args_for_parent_order' ) );
	}

	/**
	 * Helper function to get whether custom order tables are enabled or not.
	 *
	 * @return bool
	 */
	public static function is_cot_enabled() {
		if ( class_exists( 'Automattic\WooCommerce\Utilities\OrderUtil' ) ) {
			return Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
		}
		return false;
	}

	/**
	 * Modify the query arguments used in the (Custom Order Table-powered) order list table.
	 *
	 * @param array $order_query_args Arguments to be passed to `wc_get_orders()`.
	 * @return array Modified Arguments.
	 */
	public static function modify_query_args_for_parent_order( $order_query_args ) {
		if ( self::is_cot_enabled() && isset( $_GET['status'] ) && isset( $_GET['parent'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$parent_order = sanitize_text_field( wp_unslash( $_GET['parent'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( ! empty( $parent_order ) && is_numeric( $parent_order ) ) {
				$order_query_args['parent'] = $parent_order;
			}
		}
		return $order_query_args;
	}

	/**
	 * Get scheduled payments URL for given order.
	 *
	 * @param int $order_id Order ID.
	 * @return string scheduled payments URL.
	 */
	public static function get_scheduled_payments_url( $order_id ) {
		if ( self::is_cot_enabled() ) {
			return admin_url( 'admin.php?page=wc-orders&status=wc-scheduled-payment&parent=' . $order_id );
		}
		return admin_url( 'edit.php?post_status=wc-scheduled-payment&post_type=shop_order&post_parent=' . $order_id );
	}

	/**
	 * Untrash HPOS order.
	 *
	 * @param WC_Order $order Order.
	 */
	public static function untrash_order( $order ) {
		if ( self::is_cot_enabled() && class_exists( 'Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore' ) ) {
			$orders_store = wc_get_container()->get( Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore::class );
			$orders_store->untrash_order( $order );
		}
	}
}

WC_Deposits_COT_Compatibility::init();
