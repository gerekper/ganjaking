<?php
/**
 * WC_CP_AutomateWoo_Compatibility class
 *
 * @package  WooCommerce Composite Products
 * @since    8.7.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds hooks for AutomateWoo Compatibility.
 *
 * @version  8.7.4
 */
class WC_CP_AutomateWoo_Compatibility {

	public static function init() {

		add_filter( 'automatewoo/cart/get_items', array( __CLASS__, 'filter_get_items' ), 10, 1 );

	}

	/**
	 * Filter out components from the abandoned cart and keep the composite products (parent).
	 *
	 * @since  8.7.4
	 *
	 * @param  array  $items
	 * @return \AutomateWoo\Cart_Item[]
	 */
	public static function filter_get_items( $items ) {

		if ( ! is_array( $items ) ) {
			return $items;
		}

		foreach ( $items as $key => $item ) {
			$data = $item->get_data();

			// AutomateWoo adds the components to the abandoned cart.
			// We need to remove them, as the parent will add them automatically when it's added to the cart.
			// This means that composite children keys will not have the same keys as they had in the abandoned cart.
			if ( isset( $data[ 'composite_parent' ] ) ) {
				unset( $items[ $key ] );
			}

			// AutomateWoo adds the parents in the composite_children array.
			// We need to remove them to avoid conflicts.
			if ( isset( $data[ 'composite_children' ] ) ) {
				// Search composite_children for the parent and unset it.
				$index = array_search( $key, $data[ 'composite_children' ], true );
				if ( false !== $index ) {
					unset( $data[ 'composite_children' ][ $index ] );

					// Recreate AutomateWoo\Cart_Item object.
					$items[ $key ] = new \AutomateWoo\Cart_Item( $key, $data );
				}

			}
		}

		return $items;

	}

}

WC_CP_AutomateWoo_Compatibility::init();
