<?php
/**
 * WC_Product_Vendors_COT_Compatibility class
 *
 * @package  WC_Product_Vendors
 * @since    2.1.66
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for WC COT compatibility.
 *
 * @class    WC_Product_Vendors_COT_Compatibility
 * @version  2.1.66
 */
class WC_Product_Vendors_COT_Compatibility {

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
	 * Helper function to get whether given id is order or not.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return bool Whether given id is order or not.
	 */
	public static function is_order( $order_id ) {
		if ( self::is_cot_enabled() ) {
			return Automattic\WooCommerce\Utilities\OrderUtil::is_order( $order_id, wc_get_order_types() );
		}
		return in_array( get_post_type( $order_id ), wc_get_order_types(), true );
	}
}
