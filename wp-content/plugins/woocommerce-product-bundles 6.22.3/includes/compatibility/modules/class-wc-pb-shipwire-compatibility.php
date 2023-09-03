<?php
/**
 * WC_PB_Shipwire_Compatibility class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shipwire Integration.
 *
 * @version  5.1.0
 */
class WC_PB_Shipwire_Compatibility {

	public static function init() {

		// Add filters.
		add_action( 'wc_shipwire_before_order_export', array( __CLASS__, 'add_filters' ) );
		add_action( 'wc_shipwire_before_order_items_update', array( __CLASS__, 'add_filters' ) );

		// Remove filters.
		add_action( 'wc_shipwire_after_order_export', array( __CLASS__, 'remove_filters' ) );
		add_action( 'wc_shipwire_after_order_items_update', array( __CLASS__, 'remove_filters' ) );
	}

	/**
	 * Modify the returned order items and products to return the correct items/weights/values for shipping.
	 */
	public static function add_filters() {

		add_filter( 'woocommerce_order_get_items', array( WC_PB()->order, 'get_order_items' ), 10, 2 );
		add_filter( 'woocommerce_get_product_from_item', array( WC_PB()->order, 'get_product_from_item' ), 10, 3 );
	}

	/**
	 * Remove filters.
	 */
	public static function remove_filters() {

		remove_filter( 'woocommerce_order_get_items', array( WC_PB()->order, 'get_order_items' ), 10, 2 );
		remove_filter( 'woocommerce_get_product_from_item', array( WC_PB()->order, 'get_product_from_item' ), 10, 3 );
	}
}

WC_PB_Shipwire_Compatibility::init();
