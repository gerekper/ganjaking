<?php
/**
 * Product Add-ons display
 *
 * @package WC_Product_Addons/Classes/Legacy/Display
 * @since   2.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product_Addon_Display_Legacy class.
 */
class Product_Addon_Display_Legacy extends Product_Addon_Display {

	/**
	 * totals function.
	 *
	 * @access public
	 * @return void
	 */
	public function totals( $post_id ) {
		global $product;

		if ( ! isset( $product ) || $product->id != $post_id ) {
			$the_product = wc_get_product( $post_id );
		} else {
			$the_product = $product;
		}

		if ( is_object( $the_product ) ) {
			$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
			$display_price    = $tax_display_mode == 'incl' ? $the_product->get_price_including_tax() : $the_product->get_price_excluding_tax();
		} else {
			$display_price    = '';
			$raw_price        = 0;
		}

		if ( get_option( 'woocommerce_prices_include_tax' ) === 'no' ) {
			$tax_mode = 'excl';
			$raw_price = $the_product->get_price_excluding_tax();
		} else {
			$tax_mode = 'incl';
			$raw_price = $the_product->get_price_including_tax();
		}

		echo '<div id="product-addons-total" data-show-sub-total="' . ( apply_filters( 'woocommerce_product_addons_show_grand_total', true, $the_product ) ? 1 : 0 ) . '" data-type="' . esc_attr( $the_product->product_type ) . '" data-tax-mode="' . esc_attr( $tax_mode ) . '" data-tax-display-mode="' . esc_attr( $tax_display_mode ) . '" data-price="' . esc_attr( $display_price )  . '" data-raw-price="' . esc_attr( $raw_price ) . '" data-product-id="' . esc_attr( $post_id ) . '"></div>';
	}


	/**
	 * add_to_cart_text function.
	 *
	 * @access public
	 * @param mixed $text
	 * @return void
	 */
	public function add_to_cart_text( $text, $product = null ) {
		global $product;

		if ( ! is_single( $product->id ) ) {
			if ( $this->check_required_addons( $product->id ) ) {
				if ( version_compare( WOOCOMMERCE_VERSION, '2.5.0', '<' ) ) {
					$product->product_type = 'addons';
				}
				$text = apply_filters( 'addons_add_to_cart_text', __( 'Select options', 'woocommerce-product-addons' ) );
			}
		}

		return $text;
	}

	/**
	 * Removes ajax-add-to-cart functionality in WC 2.5 when a product has required add-ons.
	 *
	 * @access public
	 * @param  boolean $supports
	 * @param  string  $feature
	 * @param  object  $product
	 * @return boolean
	 */
	public function ajax_add_to_cart_supports( $supports, $feature, $product ) {

		if ( 'ajax_add_to_cart' === $feature && $this->check_required_addons( $product->id ) ) {
			$supports = false;
		}

		return $supports;
	}

	/**
	 * add_to_cart_url function.
	 *
	 * @access public
	 * @param mixed $url
	 * @return void
	 */
	public function add_to_cart_url( $url, $product = null ) {
		global $product;

		if ( ! is_single( $product->id ) && in_array( $product->product_type, apply_filters( 'woocommerce_product_addons_add_to_cart_product_types', array( 'subscription', 'simple' ) ) ) && ( ! isset( $_GET['wc-api'] ) || $_GET['wc-api'] !== 'WC_Quick_View' ) ) {
			if ( $this->check_required_addons( $product->id ) ) {
				if ( version_compare( WOOCOMMERCE_VERSION, '2.5.0', '<' ) ) {
					$product->product_type = 'addons';
				}
				$url = apply_filters( 'addons_add_to_cart_url', get_permalink( $product->id ) );
			}
		}

		return $url;
	}

	/**
	 * Don't let products with required addons be added to cart when viewing grouped products.
	 * @param  bool $purchasable
	 * @param  object $product
	 * @return bool
	 */
	public function prevent_purchase_at_grouped_level( $purchasable, $product ) {
		if ( $product && $product->get_parent() && is_single( $product->get_parent() ) && $this->check_required_addons( $product->id ) ) {
			$purchasable = false;
		}
		return $purchasable;
	}
}
