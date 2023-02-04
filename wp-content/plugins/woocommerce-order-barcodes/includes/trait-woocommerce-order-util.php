<?php
/**
 * Class WooCommerce\OrderBarcodes\Order_Util file.
 *
 * @package WooCommerce\OrderBarcodes
 */

namespace WooCommerce\OrderBarcodes;

use Automattic\WooCommerce\Utilities\OrderUtil;

defined( 'ABSPATH' ) || exit;

/**
 * Trait Order_Util
 *
 * A proxy-style trait that will help keep our code more stable and cleaner during the
 * transition to WC Custom Order Tables.
 */
trait Order_Util {
	/**
	 * Constant variable for OrderUtil class.
	 *
	 * @var wc_order_util_class.
	 */
	public static $wc_order_util_class = 'Automattic\WooCommerce\Utilities\OrderUtil';

	/**
	 * Constant variable for admin screen name.
	 *
	 * @var legacy_order_admin_screen.
	 */
	public static $legacy_order_admin_screen = 'shop_order';

	/**
	 * Checks whether the OrderUtil class exists
	 *
	 * @return bool
	 */
	public function wc_order_util_class_exists() {
		return class_exists( self::$wc_order_util_class );
	}

	/**
	 * Checks whether the OrderUtil class and the given method exist
	 *
	 * @param String $method_name Class method name.
	 *
	 * @return bool
	 */
	public function wc_order_util_method_exists( $method_name ) {
		if ( ! $this->wc_order_util_class_exists() ) {
			return false;
		}

		if ( ! method_exists( self::$wc_order_util_class, $method_name ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks whether we are using custom order tables.
	 *
	 * @return bool
	 */
	public function custom_orders_table_usage_is_enabled() {
		if ( ! $this->wc_order_util_method_exists( 'custom_orders_table_usage_is_enabled' ) ) {
			return false;
		}

		return OrderUtil::custom_orders_table_usage_is_enabled();
	}

	/**
	 * Returns the relevant order screen depending on whether
	 * custom order tables are being used.
	 *
	 * @return string
	 */
	public function get_order_admin_screen() {
		if ( ! $this->wc_order_util_method_exists( 'get_order_admin_screen' ) ) {
			return self::$legacy_order_admin_screen;
		}

		return OrderUtil::get_order_admin_screen();
	}

	/**
	 * Check if the object is WP_Post object.
	 *
	 * @param Mixed $object Either Post object or Order object.
	 *
	 * @return Boolean
	 */
	public function is_wp_post( $object ) {
		return is_a( $object, 'WP_Post' );
	}

	/**
	 * Check if the object is WC_Order object.
	 *
	 * @param Mixed $object Either Post object or Order object.
	 *
	 * @return Boolean
	 */
	public function is_wc_order( $object ) {
		return is_a( $object, 'WC_Order' );
	}

	/**
	 * Check if the object is either WP_Post or WC_Order object.
	 *
	 * @param Mixed $object Either Post object or Order object.
	 *
	 * @return Boolean
	 */
	public function is_order_or_post( $object ) {
		return $this->is_wp_post( $object ) || $this->is_wc_order( $object );
	}

	/**
	 * Returns the WC_Order object from the object passed to
	 * the add_meta_box callback function.
	 *
	 * @param WC_Order|WP_Post $post_or_order_object Either Post object or Order object.
	 *
	 * @return \WC_Order
	 */
	public function init_theorder_object( $post_or_order_object ) {
		if ( ! $this->wc_order_util_method_exists( 'init_theorder_object' ) ) {
			return wc_get_order( $post_or_order_object->ID );
		}

		return OrderUtil::init_theorder_object( $post_or_order_object );
	}

	/**
	 * Get order status with 'wc-' prefix from order object.
	 *
	 * @param WC_Order $order Order object.
	 *
	 * @return Boolean|String.
	 */
	public function get_order_status( $order ) {
		if ( ! method_exists( $order, 'get_status' ) ) {
			return false;
		}

		return ( false === strpos( $order->get_status(), 'wc-' ) ) ? 'wc-' . $order->get_status() : $order->get_status();
	}

	/**
	 * Return error text for unready barcode.
	 *
	 * @return String.
	 */
	public function barcode_not_ready_error_text() {
		return esc_html__( 'Barcode will be generated after Order is created!', 'woocommerce-order-barcodes' );
	}
}
