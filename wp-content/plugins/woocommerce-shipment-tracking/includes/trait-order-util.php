<?php

namespace WooCommerce\ShipmentTracking;

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
	 * The OrderUtil class provided by WooCommerce in version 6.5
	 * to help with the transition to Custom Order Tables
	 *
	 * @var string
	 */
	public static $wc_order_util_class = 'Automattic\WooCommerce\Utilities\OrderUtil';
	/**
	 * The legacy screen name/id for the WC Order post type
	 *
	 * @var string
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
	 * @param string $method
	 *
	 * @return bool
	 */
	public function wc_order_util_method_exists( $method ) {
		if ( ! $this->wc_order_util_class_exists() ) {
			return false;
		}

		if ( ! method_exists( self::$wc_order_util_class, $method ) ) {
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
	 * Returns the WC_Order object from the object passed to
	 * the add_meta_box callback function.
	 *
	 * @param $post_or_order_object
	 *
	 * @return \WC_Order
	 */
	public function init_theorder_object( $post_or_order_object ) {
		if ( ! $this->wc_order_util_method_exists( 'init_theorder_object' ) ) {
			return wc_get_order( $post_or_order_object->ID );
		}

		return OrderUtil::init_theorder_object( $post_or_order_object );
	}

}