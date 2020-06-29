<?php
/**
 * General Function
 *
 * @author  YITH
 * @package YITH WooCommerce Added to cart popup
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WACP' ) ) {
	exit;
} // Exit if accessed directly


if ( ! function_exists( 'yith_wacp_get_style_options' ) ) {
	/**
	 * Get style options from Plugin Options
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro
	 * @return string
	 */
	function yith_wacp_get_style_options() {

		$inline_css = '';

		$size       = get_option(
			'yith-wacp-box-size',
			array(
				'width'  => '700',
				'height' => '700',
			)
		);
		$inline_css .= '#yith-wacp-popup .yith-wacp-wrapper{max-width:' . $size['width'] . 'px;max-height:' . $size['height'] . 'px;}';

		$icon               = get_option( 'yith-wacp-message-icon', YITH_WACP_ASSETS_URL . '/images/message-icon.png' );
		$close_color        = get_option( 'yith-wacp-close-color', array( 'normal' => '#ffffff', 'hover' => '#c0c0c0' ) );
		$product_name_color = yith_wacp_get_proteo_option( 'yith-wacp-product-name-color', array( 'normal' => '#000000', 'hover' => '#565656' ) );

		if ( $icon ) {
			$inline_css .= '#yith-wacp-popup .yith-wacp-message:before{min-width: 30px; min-height:30px;background: url(' . $icon . ') no-repeat center center;}';
		}

		if ( get_option( 'yith-wacp-mini-cart-hide-empty', 'no' ) === 'yes' ) {
			$inline_css .= '#yith-wacp-mini-cart.empty{ visibility:hidden!important; }';
		}

		$inline_css .= '#yith-wacp-popup .yith-wacp-main{background-color: ' . get_option( 'yith-wacp-popup-background', '#ffffff' ) . ';}
			#yith-wacp-popup .yith-wacp-overlay{background-color: ' . get_option( 'yith-wacp-overlay-color', '#000000' ) . ';}
			#yith-wacp-popup.open .yith-wacp-overlay{opacity: ' . get_option( 'yith-wacp-overlay-opacity', '0.8' ) . ';}
			#yith-wacp-popup .yith-wacp-close{color: ' . $close_color['normal'] . ';}
			#yith-wacp-popup .yith-wacp-close:hover{color: ' . $close_color['hover'] . ';}
			#yith-wacp-popup .yith-wacp-message{color: ' . yith_wacp_get_proteo_option( 'yith-wacp-message-text-color', '#000000' ) . ';background-color: ' . get_option( 'yith-wacp-message-background-color', '#e6ffc5' ) . ';}
			.yith-wacp-content .cart-info > div{color: ' . yith_wacp_get_proteo_option( 'yith-wacp-cart-info-label-color', '#565656' ) . ';}
			.yith-wacp-content .cart-info > div span{color: ' . get_option( 'yith-wacp-cart-info-amount-color', '#000000' ) . ';}
			.yith-wacp-content table.cart-list td.item-info .item-name:hover,.yith-wacp-content h3.product-title:hover{color: ' . $product_name_color['hover'] . ';}
			.yith-wacp-content table.cart-list td.item-info .item-name,.yith-wacp-content table.cart-list td.item-info dl,.yith-wacp-content h3.product-title{color: ' . $product_name_color['normal'] . ';}
			.yith-wacp-content table.cart-list td.item-info .item-price,.yith-wacp-content .product-price,.yith-wacp-content ul.products li.product .price,.yith-wacp-content ul.products li.product .price ins {color: ' . yith_wacp_get_proteo_option( 'yith-wacp-product-price-color', '#565656' ) . ';}';

		return $inline_css;
	}
}

if ( ! function_exists( 'get_array_column' ) ) {
	/**
	 * Get column of last names from a record set
	 *
	 * @since  1.0.0
	 * @author Alessio Torrisi
	 * @param array  $array        The array to process.
	 * @param string $array_column The array column key.
	 * @return array
	 */
	function get_array_column( $array, $array_column ) {
		if ( function_exists( 'array_column' ) ) {
			return array_column( $array, $array_column );
		}

		$return = array();
		foreach ( $array as $row ) {
			if ( isset( $row[ $array_column ] ) ) {
				$return[] = $row[ $array_column ];
			}
		}

		return $return;
	}
}

if ( ! function_exists( 'yith_wacp_gb_pixel_plugin' ) ) {
	/**
	 * Compatibility with FB Pixel Plugin
	 *
	 * @author Francesco Licandro
	 * @param integer $product_id The product ID.
	 * @return void
	 */
	function yith_wacp_gb_pixel_plugin( $product_id ) {

		if ( ! function_exists( 'pys_add_event' ) ) {
			return;
		}

		if ( pys_get_option( 'woo', 'variation_id' ) === 'variation' && isset( $_REQUEST['variation_id'] ) ) {
			$product_id = $_REQUEST['variation_id'];
		}

		$params = function_exists( 'pys_get_woo_product_addtocart_params' ) ? pys_get_woo_product_addtocart_params( $product_id ) : pys_get_woo_ajax_addtocart_params( $product_id );

		pys_add_event( 'AddToCart', $params );
	}
}

add_action( 'woocommerce_ajax_added_to_cart', 'yith_wacp_gb_pixel_plugin', 99, 1 );

if ( ! function_exists( 'yith_wacp_get_cart_info' ) ) {
	/**
	 * Get cart info for popup
	 *
	 * @since  1.1.0
	 * @author Francesco Licandro
	 * @return array
	 */
	function yith_wacp_get_cart_info() {

		// First of all define cart constant for cart calculation.
		if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
			define( 'WOOCOMMERCE_CART', true );
		}

		$cart_info = array();

		// Calculate totals.
		WC()->cart->calculate_totals();

		// Build info array.
		if ( WC()->cart->calculate_shipping() && WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) {
			$cart_info['shipping'] = WC()->cart->get_cart_shipping_total();
		}

		if ( wc_tax_enabled() ) {
			$cart_info['tax'] = WC()->cart->get_cart_tax();
		}

		$cart_info['discount'] = WC()->cart->get_discount_total() + WC()->cart->get_discount_tax() > 0 ? wc_price( WC()->cart->get_discount_total() + WC()->cart->get_discount_tax() ) : null;
		$cart_info['total']    = WC()->cart->get_total();

		return apply_filters( 'yith_wacp_popup_cart_info', $cart_info );
	}
}

if ( ! function_exists( 'yith_wacp_get_cart_remove_url' ) ) {
	/**
	 * Get cart item remove url
	 *
	 * @since  1.3.0
	 * @author Francesco Licandro
	 * @param string $item_key The item key.
	 * @return string
	 */
	function yith_wacp_get_cart_remove_url( $item_key ) {
		return function_exists( 'wc_get_cart_remove_url' ) ? wc_get_cart_remove_url( $item_key ) : WC()->cart->get_remove_url( $item_key );
	}
}

if ( ! function_exists( 'yith_wacp_get_formatted_cart_item_data' ) ) {
	/**
	 * Get formatted cart item data
	 *
	 * @since  1.3.0
	 * @author Francesco Licandro
	 * @param array $item Cart item object.
	 * @return string
	 */
	function yith_wacp_get_formatted_cart_item_data( $item ) {
		return function_exists( 'wc_get_formatted_cart_item_data' ) ? wc_get_formatted_cart_item_data( $item ) : WC()->cart->get_item_data( $item );
	}
}

if ( ! function_exists( 'yith_wacp_get_proteo_default' ) ) {
	/**
	 * Filter option default value if Proteo theme is active
	 *
	 * @since  1.5.1
	 * @author Francesco Licandro
	 * @param string  $key
	 * @param mixed   $default
	 * @param boolean $force_default
	 * @return string
	 */
	function yith_wacp_get_proteo_option( $key, $default = '', $force_default = false ) {

		// get value from DB if requested and return if not empty
		! $force_default && $value = get_option( $key, $default );

		if ( ! empty( $value ) ) {
			return $value;
		}

		if ( ! defined( 'YITH_PROTEO_VERSION' ) ) {
			return $default;
		}


		switch ( $key ) {
			case 'yith-wacp-message-text-color':
			case 'yith-wacp-product-name-color_normal':
			case 'yith-wacp-product-name-color_hover':
			case 'yith-wacp-product-price-color':
			case 'yith-wacp-related-title-color':
			case 'yith-wacp-cart-info-label-color':
				$default = get_theme_mod( 'yith_proteo_base_font_color', '#404040' );
				break;
			case 'yith-wacp-button-background_normal':
				$default = get_theme_mod( 'yith_proteo_button_style_1_bg_color', '#448a85' );
				break;
			case 'yith-wacp-button-background_hover':
				$default = get_theme_mod( 'yith_proteo_button_style_1_bg_color_hover', yith_proteo_adjust_brightness( get_theme_mod( 'yith_proteo_main_color_shade', '#448a85' ), 0.2 ) );
				break;
			case 'yith-wacp-product-name-color':
				$default = array(
					'normal' => yith_wacp_get_proteo_option( 'yith-wacp-product-name-color_normal', $default, true ),
					'hover'  => yith_wacp_get_proteo_option( 'yith-wacp-product-name-color_hover', $default, true ),
				);
				break;
			case 'yith-wacp-button-background':
				$default = array(
					'normal' => yith_wacp_get_proteo_option( 'yith-wacp-button-background_normal', $default, true ),
					'hover'  => yith_wacp_get_proteo_option( 'yith-wacp-button-background_hover', $default, true ),
				);
				break;
			case 'yith-wacp-button-text_normal':
				$default = get_theme_mod( 'yith_proteo_button_style_1_text_color', '#ffffff' );
				break;
			case 'yith-wacp-button-text_hover':
				$default = get_theme_mod( 'yith_proteo_button_style_1_text_color_hover', '#ffffff' );
				break;
			case 'yith_welrp_button_lb_color':
				$default = array(
					'normal' => yith_wacp_get_proteo_option( 'yith-wacp-button-text_normal', $default, true ),
					'hover'  => yith_wacp_get_proteo_option( 'yith-wacp-button-text_hover', $default, true ),
				);
				break;
		}

		return $default;
	}
}