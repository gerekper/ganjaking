<?php
/**
 * Wishlists Compatibility
 *
 * @author   Kathy Darling
 * @category Compatibility
 * @package  WooCommerce Mix and Match Products/Compatibility
 * @since    1.0.5
 * @version  1.0.5
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_WL_Compatibility Class.
 *
 * Adds compatibility with WooCommerce Wishlists.
 */
class WC_MNM_WL_Compatibility {

	public static function init() {

		add_action( 'woocommerce_wishlist_after_list_item_name', array( __CLASS__, 'wishlist_after_list_item_name' ), 10, 2 );
		add_filter( 'woocommerce_wishlist_list_item_price', array( __CLASS__, 'wishlist_list_item_price' ), 10, 3 );
	}

	/**
	 * Inserts bundle contents after main wishlist bundle item is displayed.
	 *
	 * @param  array    $item       Wishlist item
	 * @param  array    $wishlist   Wishlist
	 */
	public static function wishlist_after_list_item_name( $item, $wishlist ) {

		if ( ! empty( $item[ 'mnm_config' ] ) ) {

			echo '<div class="wishlist_mnm_items">';

			foreach ( $item[ 'mnm_config' ] as $mnm_item => $mnm_item_data ) {

				$mnm_product = wc_get_product ( $mnm_item );

				if ( ! $mnm_product->is_visible() ) {
					// translators: %d child quantity in configuration.
					echo apply_filters( 'woocommerce_cart_item_name', $mnm_product->get_title(), $mnm_item_data, false ) . ' ' . sprintf( __( '&times; %d', 'woocommerce-mix-and-match-products' ), $mnm_item_data['quantity'] );
				} else {
					// translators: %d child quantity in configuration.
					echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', $mnm_product->get_permalink(), $mnm_product->get_title() ), $mnm_item_data, false ) . ' ' . sprintf( __( '&times; %d', 'woocommerce-mix-and-match-products' ), $mnm_item_data[ 'quantity' ] );
				}

				// Variation Data.
				if( $mnm_product->is_type( 'variation' ) ){
					echo wc_get_formatted_variation( $mnm_product->get_variation_attributes() );
				}

			}

			$mnm_container = wc_get_product( $item['product_id'] );

			if( $item[ 'data' ]->is_priced_per_product() ){
				echo '<p class="wishlist_mnm_notice">' . __( '*', 'woocommerce-mix-and-match-products' ) . '&nbsp;&nbsp;<em>' . __( 'Accurate pricing info available in cart.', 'woocommerce-mix-and-match-products' ) . '</em></p>';
			}

			echo '</div>';
		}
	}

	/**
	 * Modifies wishlist bundle item price - the precise sum cannot be displayed reliably unless the item is added to the cart.
	 *
	 * @param  double   $price      Item price
	 * @param  array    $item       Wishlist item
	 * @param  array    $wishlist   Wishlist
	 * @return string   $price
	 */
	public static function wishlist_list_item_price( $price, $item, $wishlist ) {

		if ( ! empty( $item[ 'mnm_config' ] ) && $item[ 'data' ]->is_priced_per_product() ){
			$price = __( '*', 'woocommerce-mix-and-match-products' );
		}

		return $price;
	}
}

WC_MNM_WL_Compatibility::init();
