<?php
/**
 * WC_CP_Wishlists_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.15.3
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Wishlists Compatibility.
 *
 * @version  3.15.3
 */
class WC_CP_Wishlists_Compatibility {

	public static function init() {

		// Modifies wishlist composited item price - the precise sum cannot be displayed reliably unless the item is added to the cart.
		add_filter( 'woocommerce_wishlist_list_item_price', array( __CLASS__, 'wishlist_list_item_price' ), 10, 3 );

		// Inserts composite contents after main wishlist composited item is displayed.
		add_action( 'woocommerce_wishlist_after_list_item_name', array( __CLASS__, 'wishlist_after_list_item_name' ), 10, 2 );
	}

	/**
	 * Inserts composite contents after main wishlist composited item is displayed.
	 *
	 * @param  array  $item
	 * @param  array  $wishlist
	 * @return void
	 */
	public static function wishlist_after_list_item_name( $item, $wishlist ) {

		if ( ! empty( $item[ 'composite_data' ] ) ) {
			echo '<dl>';
			foreach ( $item[ 'composite_data' ] as $composited_item => $composited_item_data ) {

				$composited_product = wc_get_product( $composited_item_data[ 'product_id' ] );

				if ( ! $composited_product ) {
					continue;
				}

				echo '<dt class="component_title_meta wishlist_component_title_meta">' . $composited_item_data[ 'title' ] . ':</dt>';

				$default_markup = $composited_product->get_title() . ' <strong class="component_quantity_meta wishlist_component_quantity_meta product-quantity">&times; ' . $composited_item_data[ 'quantity' ] . '</strong>';

				echo '<dd class="component_option_meta wishlist_component_option_meta">' . apply_filters( 'woocommerce_composite_wishlist_item_contents', $default_markup, $composited_product, $composited_item_data ) . '</dd>';

				if ( ! empty ( $composited_item_data[ 'attributes' ] ) ) {

					$attributes = '';

					foreach ( $composited_item_data[ 'attributes' ] as $attribute_name => $attribute_value ) {

						$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $attribute_name ) ) );

						// If this is a term slug, get the term's nice name.
						if ( taxonomy_exists( $taxonomy ) ) {

							$term = get_term_by( 'slug', $attribute_value, $taxonomy );

							if ( ! is_wp_error( $term ) && $term && $term->name ) {
								$attribute_value = $term->name;
							}

							$label = wc_attribute_label( $taxonomy );

						// If this is a custom option slug, get the options name.
						} else {

							$product_attributes = $composited_product->get_attributes();
							$attribute_value    = apply_filters( 'woocommerce_variation_option_name', $attribute_value );

							if ( isset( $product_attributes[ str_replace( 'attribute_', '', $attribute_name ) ] ) ) {
								$label = wc_attribute_label( $product_attributes[ str_replace( 'attribute_', '', $attribute_name ) ][ 'name' ] );
							} else {
								$label = $attribute_name;
							}
						}

						$attributes = $attributes . $label . ': ' . $attribute_value . ', ';
					}
					echo '<dd class="component_attribute_meta wishlist_component_attribute_meta">' . rtrim( $attributes, ', ' ) . '</dd>';
				}
			}
			echo '</dl>';
			echo '<p class="component_notice wishlist_component_notice">' . __( '*', 'woocommerce-composite-products' ) . '&nbsp;&nbsp;<em>' . __( 'For up-to-date pricing details, please add the product to your cart.', 'woocommerce-composite-products' ) . '</em></p>';
		}
	}

	/**
	 * Modifies wishlist composited item price - the precise sum cannot be displayed reliably unless the item is added to the cart.
	 *
	 * @param  double  $price
	 * @param  array   $item
	 * @param  array   $wishlist
	 * @return string  $price
	 */
	public static function wishlist_list_item_price( $price, $item, $wishlist ) {

		if ( ! empty( $item[ 'composite_data' ] ) ) {
			$price = __( '*', 'woocommerce-composite-products' );
		}

		return $price;

	}
}

WC_CP_Wishlists_Compatibility::init();
