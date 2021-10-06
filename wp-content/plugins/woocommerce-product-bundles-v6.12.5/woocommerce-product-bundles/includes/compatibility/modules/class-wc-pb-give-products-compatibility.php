<?php
/**
 * WC_PB_Give_Products_Compatibility class
 *
 * @author   Rodrigo Primo <rodrigo@automattic.com>
 * @package  WooCommerce Product Bundles
 * @since    5.1.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Give Products Integration.
 *
 * @version 5.5.0
 */
class WC_PB_Give_Products_Compatibility {

	public static function init() {
		// Whenever a product bundle is given to an user make sure all the bundled items are included in the order.
		add_action( 'woocommerce_order_given', array( __CLASS__, 'add_bundle_product_to_order' ) );
	}

	/**
	 * Loop through the items of an order that was given to an user and re-add bundles to the order using WC_PB_Order::add_bundle_to_order().
	 * Without this code the order will contain only the "container" item without any of the bundled items.
	 *
	 * Important: Only works reliably with static bundles. WC_PB_Order::add_bundle_to_order() normally expects a configuration array when a bundle has configurable content.
	 *
	 * @param  int  $order_id
	 */
	public static function add_bundle_product_to_order( $order_id ) {

		$order                 = wc_get_order( $order_id );
		$items_to_remove       = array();
		$order_contains_bundle = false;

		foreach ( $order->get_items( 'line_item' ) as $order_item_id => $order_item ) {

			$product_id  = $order_item->get_product_id();
			$product_qty = $order_item->get_quantity();
			$product     = wc_get_product( $product_id );

			if ( $product && $product->is_type( 'bundle' ) ) {

				$items_to_remove[] = $order_item;

				// Re-add the product bundle to the order this time adding the bundled items.
				WC_PB()->order->add_bundle_to_order( $product, $order, $product_qty );

				// Remove the original product bundle "container" item as it has no bundled items associated to it.
				wc_delete_order_item( $order_item_id );

				$order_contains_bundle = true;
			}
		}

		if ( ! empty( $items_to_remove ) ) {

			$order->save();

			foreach ( $items_to_remove as $remove_item ) {
				$order->remove_item( $remove_item->get_id() );
				$remove_item->delete();
			}

			$order->save();
		}

		if ( $order_contains_bundle ) {
			self::regenerate_download_permissions( $order_id );
		}
	}

	/**
	 * Regenerate download permissions for this order to include the permissions for the bundled items if any.
	 *
	 * @param  int  $order_id
	 */
	public static function regenerate_download_permissions( $order_id ) {
		$data_store = WC_Data_Store::load( 'customer-download' );
		$data_store->delete_by_order_id( $order_id );
		wc_downloadable_product_permissions( $order_id, true );
	}
}

WC_PB_Give_Products_Compatibility::init();
