<?php
/**
 * WC_CP_Shipwire_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.8.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shipwire Integration.
 *
 * @version  3.8.0
 */
class WC_CP_Shipwire_Compatibility {

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

		add_filter( 'woocommerce_order_get_items', array( WC_CP()->order, 'get_order_items' ), 10, 2 );
		add_filter( 'woocommerce_get_product_from_item', array( WC_CP()->order, 'get_product_from_item' ), 10, 3 );
	}

	/**
	 * Remove filters.
	 */
	public static function remove_filters() {

		remove_filter( 'woocommerce_order_get_items', array( WC_CP()->order, 'get_order_items' ), 10, 2 );
		remove_filter( 'woocommerce_get_product_from_item', array( WC_CP()->order, 'get_product_from_item' ), 10, 3 );
	}
}

WC_CP_Shipwire_Compatibility::init();
