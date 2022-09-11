<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the compatibility with WooCommerce Free Gift Coupons.
 * Version tested: 2.4.3.
 *
 * Translates the cart when the language is switched.
 *
 * @since 1.4
 */
class PLLWC_Free_Gift_Coupons {
	/**
	 * Constructor.
	 * Setups filters.
	 *
	 * @since 1.4
	 */
	public function __construct() {
		// Translate the cart.
		add_filter( 'pllwc_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );
		add_filter( 'pllwc_translate_cart_item', array( $this, 'translate_cart_item' ) );
	}

	/**
	 * Adds Free Gift Coupons informations to the cart item data when translated.
	 * Hooked to the filter 'pllwc_add_cart_item_data'.
	 *
	 * @since 1.4
	 *
	 * @param array $cart_item_data Cart item data.
	 * @param array $item           Cart item.
	 * @return array
	 */
	public function add_cart_item_data( $cart_item_data, $item ) {
		if ( isset( $item['free_gift'], $item['fgc_quantity'] ) ) {
			$cart_item_data = array_merge(
				$cart_item_data,
				array(
					'free_gift'    => $item['free_gift'],
					'fgc_quantity' => $item['fgc_quantity'],
				)
			);
		}
		return $cart_item_data;
	}

	/**
	 * Changes the price on the gift item to be zero.
	 * Hooked to the filter 'pllwc_translate_cart_item'.
	 *
	 * @since 1.4
	 *
	 * @param array $item Cart item.
	 * @return array
	 */
	public function translate_cart_item( $item ) {
		return empty( $item['data'] ) ? $item : WC_Free_Gift_Coupons::add_cart_item( $item );
	}
}


