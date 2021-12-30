<?php
/**
 * WC_PB_WC_Services_Compatibility class
 *
 * @package  WooCommerce Product Bundles
 * @since    6.3.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Services Integration.
 *
 * @version  6.3.4
 */
class WC_PB_WC_Services_Compatibility {

	public static function init() {

		// Legacy compatibility.
		add_filter( 'rest_dispatch_request', array( __CLASS__, 'add_rest_filters' ), 10, 3 );

		// Adds the necessary filters at the beginning of the edit-order postboxes created by WC Services.
		add_filter( 'postbox_classes_shop_order_woocommerce-order-shipment-tracking', array( __CLASS__, 'add_postbox_filters' ), 10 );
		add_filter( 'postbox_classes_shop_order_woocommerce-order-label', array( __CLASS__, 'add_postbox_filters' ), 10 );

		// Removes filters added at the beginning of the edit-order postboxes created by WC Services.
		add_filter( 'wc_connect_meta_box_payload', array( __CLASS__, 'remove_postbox_filters' ), 10 );
	}

	/**
	 * Adds the necessary filters when processing requests on the `connect/label' endpoint.
	 */
	public static function add_rest_filters( $dispatch_result, $request, $route ) {
		if ( strpos( $route, 'connect/label' ) !== false ) {
			self::add_filters();
		}
		return $dispatch_result;
	}

	/**
	 * Adds the necessary filters at the beginning of the edit-order postboxes created by WC Services.
	 */
	public static function add_postbox_filters( $classes ) {
		self::add_filters();
		return $classes;
	}

	/**
	 * Adds the necessary filters at the beginning of the edit-order postboxes created by WC Services.
	 */
	public static function remove_postbox_filters( $payload ) {
		self::remove_filters();
		return $payload;
	}

	/**
	 * Modify the returned order items and products to return the correct items/weights/values for shipping.
	 */
	public static function add_filters() {
		add_filter( 'woocommerce_order_get_items', array( WC_PB()->order, 'get_order_items' ), 10, 2 );
		add_filter( 'woocommerce_order_item_product', array( WC_PB()->order, 'get_product_from_item' ), 10, 2 );
	}

	/**
	 * Modify the returned order items and products to return the correct items/weights/values for shipping.
	 */
	public static function remove_filters() {
		remove_filter( 'woocommerce_order_get_items', array( WC_PB()->order, 'get_order_items' ), 10 );
		remove_filter( 'woocommerce_order_item_product', array( WC_PB()->order, 'get_product_from_item' ), 10 );
	}
}

WC_PB_WC_Services_Compatibility::init();
