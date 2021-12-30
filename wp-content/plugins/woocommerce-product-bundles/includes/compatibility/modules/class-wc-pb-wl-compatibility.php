<?php
/**
 * WC_PB_Wishlists_Compatibility class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.10.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Wishlists Compatibility.
 *
 * @version  5.10.0
 */
class WC_PB_Wishlists_Compatibility {

	public static function init() {

		// Modifies wishlist bundle item price - the precise sum cannot be displayed reliably unless the item is added to the cart.
		add_filter( 'woocommerce_wishlist_list_item_price', array( __CLASS__, 'wishlist_list_item_price' ), 10, 3 );

		// Inserts bundle contents after main wishlist bundle item is displayed.
		add_action( 'woocommerce_wishlist_after_list_item_name', array( __CLASS__, 'wishlist_after_list_item_name' ), 10, 2 );

		// Displays bundled items within a Composite Product.
		add_filter( 'woocommerce_composite_wishlist_item_contents', array( __CLASS__, 'wishlist_display_bundled_items' ), 10, 3 );
	}

	/**
	 * Displays bundled items within a Composite Product.
	 *
	 * @param  string      $default_markup
	 * @param  WC_Product  $composited_product
	 * @param  array       $composited_item_data
	 * @return string
	 */
	public static function wishlist_display_bundled_items( $default_markup, $composited_product, $composited_item_data ) {

		$markup = $default_markup . '<br>';

		if ( $composited_product->is_type( 'bundle' ) && ! empty( $composited_item_data[ 'stamp' ] ) ) {

			if ( false === WC_Product_Bundle::group_mode_has( $composited_product->get_group_mode(), 'parent_item' ) ) {
				$markup = '';
			}

			foreach ( $composited_item_data[ 'stamp' ] as $bundled_item_id => $bundled_item_data ) {

				$bundled_product = wc_get_product( $bundled_item_data[ 'product_id' ] );

				if ( empty( $bundled_product ) ) {
					continue;
				}

				if ( isset( $bundled_item_data[ 'optional_selected' ] ) && ( 'no' === $bundled_item_data[ 'optional_selected' ] ) ) {
					continue;
				}

				$markup .= '<span class="bundled_item_meta">' . $bundled_product->get_title() . ' <strong class="bundled_quantity_meta wishlist_bundled_quantity_meta product-quantity">&times; ' . $bundled_item_data[ 'quantity' ] . '</strong></span><br>';

				if ( ! empty ( $bundled_item_data[ 'attributes' ] ) ) {

					$attributes = '';

					foreach ( $bundled_item_data[ 'attributes' ] as $attribute_name => $attribute_value ) {

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

							$attribute_value    = apply_filters( 'woocommerce_variation_option_name', $attribute_value );
							$product_attributes = $bundled_product->get_attributes();

							if ( isset( $product_attributes[ str_replace( 'attribute_', '', $attribute_name ) ] ) ) {
								$label = wc_attribute_label( $product_attributes[ str_replace( 'attribute_', '', $attribute_name ) ][ 'name' ] );
							} else {
								$label = $attribute_name;
							}
						}

						$attributes = $attributes . $label . ': ' . $attribute_value . ', ';
					}
					$markup .= rtrim( $attributes, ', ' );
				}
			}
		}
		return $markup;
	}

	/**
	 * Inserts bundle contents after main wishlist bundle item is displayed.
	 *
	 * @param  array  $item
	 * @param  array  $wishlist
	 * @return void
	 */
	public static function wishlist_after_list_item_name( $item, $wishlist ) {

		if ( $item[ 'data' ]->is_type( 'bundle' ) && ! empty( $item[ 'stamp' ] ) ) {

			echo '<dl>';

			foreach ( $item[ 'stamp' ] as $bundled_item_id => $bundled_item_data ) {

				$bundled_product = wc_get_product( $bundled_item_data[ 'product_id' ] );

				if ( empty( $bundled_product ) ) {
					continue;
				}

				if ( isset( $bundled_item_data[ 'optional_selected' ] ) && ( 'no' === $bundled_item_data[ 'optional_selected' ] ) ) {
					continue;
				}

				echo '<dt class="bundled_title_meta wishlist_bundled_title_meta">' . $bundled_product->get_title() . ' <strong class="bundled_quantity_meta wishlist_bundled_quantity_meta product-quantity">&times; ' . $bundled_item_data[ 'quantity' ] . '</strong></dt>';

				if ( ! empty ( $bundled_item_data[ 'attributes' ] ) ) {

					$attributes = '';

					foreach ( $bundled_item_data[ 'attributes' ] as $attribute_name => $attribute_value ) {

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

							$attribute_value    = apply_filters( 'woocommerce_variation_option_name', $attribute_value );
							$product_attributes = $bundled_product->get_attributes();

							if ( isset( $product_attributes[ str_replace( 'attribute_', '', $attribute_name ) ] ) ) {
								$label = wc_attribute_label( $product_attributes[ str_replace( 'attribute_', '', $attribute_name ) ][ 'name' ] );
							} else {
								$label = $attribute_name;
							}
						}

						$attributes = $attributes . $label . ': ' . $attribute_value . ', ';
					}
					echo '<dd class="bundled_attribute_meta wishlist_bundled_attribute_meta">' . rtrim( $attributes, ', ' ) . '</dd>';
				}
			}
			echo '</dl>';
			echo '<p class="bundled_notice wishlist_component_notice">' . __( '*', 'woocommerce-product-bundles' ) . '&nbsp;&nbsp;<em>' . __( 'For up-to-date pricing details, please add the product to your cart.', 'woocommerce-product-bundles' ) . '</em></p>';
		}
	}

	/**
	 * Modifies wishlist bundle item price - the precise sum cannot be displayed reliably unless the item is added to the cart.
	 *
	 * @param  double  $price
	 * @param  array   $item
	 * @param  array   $wishlist
	 * @return string  $price
	 */
	public static function wishlist_list_item_price( $price, $item, $wishlist ) {

		if ( $item[ 'data' ]->is_type( 'bundle' ) && ! empty( $item[ 'stamp' ] ) )
			$price = __( '*', 'woocommerce-product-bundles' );

		return $price;
	}

}

WC_PB_Wishlists_Compatibility::init();
