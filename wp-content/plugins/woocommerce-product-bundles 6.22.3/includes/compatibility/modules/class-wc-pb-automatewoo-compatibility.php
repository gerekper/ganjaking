<?php
/**
 * WC_PB_AutomateWoo_Compatibility class
 *
 * @package  WooCommerce Product Bundles
 * @since    6.18.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds hooks for AutomateWoo Compatibility.
 *
 * @version  6.18.2
 */
class WC_PB_AutomateWoo_Compatibility {

	public static function init() {

		add_filter( 'automatewoo/cart/get_items', array( __CLASS__, 'filter_get_items' ), 10, 1 );

	}

	/**
	 * Filter out bundled items from the abandoned cart and keep the bundled products (parent).
	 *
	 * @since  6.18.2
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

			// AutomateWoo adds the bundled items to the abandoned cart.
			// We need to remove them, as the parent will add them automatically when it's added to the cart.
			// This means that bundled item keys will not have the same keys as they had in the abandoned cart.
			if ( isset( $data[ 'bundled_by' ] ) ) {
				unset( $items[ $key ] );
			}

			// AutomateWoo adds the parents in the bundled_items array.
			// We need to remove them to avoid conflicts.
			if ( isset( $data[ 'bundled_items' ] ) ) {
				// Search composite_children for the parent and unset it.
				$index = array_search( $key, $data[ 'bundled_items' ], true );
				if ( false !== $index ) {
					unset( $data[ 'bundled_items' ][ $index ] );

					// Recreate AutomateWoo\Cart_Item object.
					$items[ $key ] = new \AutomateWoo\Cart_Item( $key, $data );
				}

			}
		}

		return $items;

	}

}

WC_PB_AutomateWoo_Compatibility::init();
