<?php
/**
 * WC_CP_COG_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cost of Goods Compatibility.
 *
 * @version  4.1.0
 */
class WC_CP_COG_Compatibility {

	public static function init() {

		// Filter parent/child cost meta.
		add_filter( 'wc_cost_of_goods_set_order_item_cost_meta_item_cost', array( __CLASS__, 'set_composited_order_item_cost' ), 10, 3 );

		// Update composited item cost meta when calling 'WC_CP_Order::add_composite_to_order'.
		add_filter( 'woocommerce_composite_added_to_order', array( __CLASS__, 'set_composite_added_to_order_item_cost' ), 10, 2 );
	}

	/**
	 * Update composited item cost meta when calling 'WC_CP_Order::add_composite_to_order'.
	 *
	 * @since  4.1.0
	 *
	 * @param  WC_Order_Item  $container_order_item
	 * @param  WC_Order       $order
	 * @return void
	 */
	public static function set_composite_added_to_order_item_cost( $container_order_item, $order ) {
		wc_cog()->set_order_cost_meta( $order->get_id(), true );
	}

	/**
	 * Cost of goods compatibility: Zero order item cost for composited products that belong to statically priced composites.
	 *
	 * @param  double    $cost
	 * @param  array     $item
	 * @param  WC_Order  $order
	 * @return double
	 */
	public static function set_composited_order_item_cost( $cost, $item, $order ) {

		if ( $composite_container_item = wc_cp_get_composited_order_item_container( $item, $order ) ) {

			$item_priced_individually = isset( $item[ 'component_priced_individually' ] ) ? 'yes' === $item[ 'component_priced_individually' ] : null;

			// Back-compat.
			if ( null === $item_priced_individually ) {
				if ( isset( $composite_container_item[ 'per_product_pricing' ] ) ) {
					$item_priced_individually = 'yes' === $composite_container_item[ 'per_product_pricing' ];
				} elseif ( isset( $item[ 'composite_item' ] ) ) {
					if ( $composite = wc_get_product( $composite_container_item[ 'product_id' ] ) ) {
						$product_id               = $item[ 'product_id' ];
						$component_id             = $item[ 'composite_item' ];
						$component_option         = $composite->get_component_option( $component_id, $product_id );
						$item_priced_individually = $component_option instanceof WC_CP_Product ? $component_option->is_priced_individually() : false;
					}
				}
			}

			if ( false === $item_priced_individually ) {
				$cost = 0;
			}
		}

		return $cost;
	}
}

WC_CP_COG_Compatibility::init();
