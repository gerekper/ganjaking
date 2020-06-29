<?php
/**
 * Cost of Goods Compatibility
 *
 * @author   SomewhereWarm
 * @category Compatibility
 * @package  WooCommerce Mix and Match Products/Compatibility
 * @since    1.0.5
 * @version  1.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_COG_Compatibility Class.
 *
 * Adds compatibility with WooCommerce Cost of Goods.
 */
class WC_MNM_COG_Compatibility {

	public static function init() {

		// Cost of Goods support.
		add_filter( 'wc_cost_of_goods_set_order_item_cost_meta_item_cost', array( __CLASS__, 'cost_of_goods_set_order_item_bundled_item_cost' ), 10, 3 );
	}

	/**
	 * Cost of goods compatibility: Zero order item cost for child products that belong to statically priced bundles.
	 *
	 * @param  double    $cost
	 * @param  array     $item
	 * @param  WC_Order  $order
	 * @return double
	 */
	public static function cost_of_goods_set_order_item_bundled_item_cost( $cost, $item, $order ) {

		if ( $parent_item = wc_mnm_get_order_item_container( $item, $order ) ) {

			$parent_obj = wc_get_product( $parent_item[ 'product_id' ] );

			$child_item_priced_individually = isset( $parent_item[ 'per_product_pricing' ] ) ? $parent_item[ 'per_product_pricing' ] : $parent_obj->is_priced_per_product();

			if ( 'no' === $child_item_priced_individually ) {
				return 0;
			}
		}

		return $cost;
	}
}

WC_MNM_COG_Compatibility::init();

