<?php

if ( ! function_exists( 'yith_wcqv_get_custom_style' ) ) {
	/**
	 * Get custom style from plugin options panel
	 *
	 * @since  1.2.0
	 * @author Francesco Licandro
	 * @return string
	 */
	function yith_wcqv_get_custom_style() {
		$content_w                = get_option( 'yith-quick-view-modal-width', '1000' );
		$image_w                  = get_option( 'yith-quick-view-product-image-width', '500' );
		$background_modal = get_option( 'yith-wcqv-background-modal' );
		$button_text_color = yith_wcqv_get_proteo_default( 'yith-wcqv-button-quick-view-text-color', '#ffffff' );
		$button_text_color_hover = yith_wcqv_get_proteo_default( 'yith-wcqv-button-quick-view-text-color-hover', '#ffffff' );
		$button_color = yith_wcqv_get_proteo_default( 'yith-wcqv-button-quick-view-color', '#222222' );
		$button_color_hover = yith_wcqv_get_proteo_default( 'yith-wcqv-button-quick-view-color-hover', '#ababab' );
		$main_text_color = yith_wcqv_get_proteo_default( 'yith-wcqv-main-text-color', '#222222' );
		$star_color = get_option( 'yith-wcqv-star-color', '#f7c104' );
		$cart_color = yith_wcqv_get_proteo_default( 'yith-wcqv-button-cart-color', '#a46497' );
		$cart_color_hover = yith_wcqv_get_proteo_default( 'yith-wcqv-button-cart-color-hover', '#935386' );
		$cart_text_color = yith_wcqv_get_proteo_default( 'yith-wcqv-button-cart-text-color', '#ffffff' );
		$cart_text_color_hover = yith_wcqv_get_proteo_default( 'yith-wcqv-button-cart-text-color-hover', '#ffffff' );
		$details_color = yith_wcqv_get_proteo_default( 'yith-wcqv-button-details-color', '#ebe9eb' );
		$details_color_hover = yith_wcqv_get_proteo_default( 'yith-wcqv-button-details-color-hover', '#dad8da' );
		$details_text_color = yith_wcqv_get_proteo_default( 'yith-wcqv-button-details-text-color', '#515151' );
		$details_text_color_hover = yith_wcqv_get_proteo_default( 'yith-wcqv-button-details-text-color-hover', '#515151' );

		$image_w   = ( 100 * $image_w ) / $content_w;
		$summary_w = 100 - $image_w;

		$inline_css = ".yith-quick-view.yith-modal .yith-wcqv-main{background:{$background_modal};}
			.yith-wcqv-button.inside-thumb span, .yith-wcqv-button.button{color:{$button_text_color};background:{$button_color};}
			.yith-wcqv-button.inside-thumb:hover span, .yith-wcqv-button.button:hover{color:{$button_text_color_hover};background:{$button_color_hover};}
			.yith-quick-view-content.woocommerce div.summary h1,.yith-quick-view-content.woocommerce div.summary div[itemprop=\"description\"],.yith-quick-view-content.woocommerce div.summary .product_meta,.yith-quick-view-content.woocommerce div.summary .price,.yith-quick-view-content.woocommerce div.summary .price ins {color: {$main_text_color};}
			.yith-quick-view-content.woocommerce div.summary .woocommerce-product-rating .star-rating,.yith-quick-view-content.woocommerce div.summary .woocommerce-product-rating .star-rating:before {color: {$star_color};}
			.yith-quick-view-content.woocommerce div.summary button.button.alt{background: {$cart_color};color: {$cart_text_color};}
			.yith-quick-view-content.woocommerce div.summary button.button.alt:hover{background: {$cart_color_hover};color: {$cart_text_color_hover};}
			.yith-quick-view-content.woocommerce div.summary .yith-wcqv-view-details{background: {$details_color};color: {$details_text_color};}
			.yith-quick-view-content.woocommerce div.summary .yith-wcqv-view-details:hover{background: {$details_color_hover};color: {$details_text_color_hover};}
			@media (min-width: 481px){.yith-quick-view.yith-modal .yith-quick-view-content div.images{width:{$image_w}% !important;}
			.yith-quick-view.yith-modal .yith-quick-view-content div.summary{width:{$summary_w}% !important;}}";

		return $inline_css;
	}
}

if ( ! function_exists( 'yith_wcqv_get_proteo_default' ) ) {
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
	function yith_wcqv_get_proteo_default( $key, $default = '', $force_default = false ) {

		// get value from DB if requested and return if not empty
		! $force_default && $value = get_option( $key, $default );

		if ( ! empty( $value ) ) {
			return $value;
		}

		if ( ! defined( 'YITH_PROTEO_VERSION' ) ) {
			return $default;
		}


		switch ( $key ) {
			case 'yith-wcqv-main-text-color':
				$default = get_theme_mod( 'yith_proteo_base_font_color', '#404040' );
				break;
			case 'yith-wcqv-button-quick-view-color':
			case 'yith-wcqv-button-cart-color':
			case 'yith-wcqv-button-details-color':
				$default = get_theme_mod( 'yith_proteo_button_style_1_bg_color', '#448a85' );
				break;
			case 'yith-wcqv-button-quick-view-color-hover':
			case 'yith-wcqv-button-cart-color-hover':
			case 'yith-wcqv-button-details-color-hover':
				$default = get_theme_mod( 'yith_proteo_button_style_1_bg_color_hover', yith_proteo_adjust_brightness( get_theme_mod( 'yith_proteo_main_color_shade', '#448a85' ), 0.2 ) );
				break;
			case 'yith-wcqv-button-quick-view-text-color':
			case 'yith-wcqv-button-cart-text-color':
			case 'yith-wcqv-button-details-text-color':
				$default = get_theme_mod( 'yith_proteo_button_style_1_text_color', '#ffffff' );
				break;
			case 'yith-wcqv-button-quick-view-text-color-hover':
			case 'yith-wcqv-button-cart-text-color-hover':
			case 'yith-wcqv-button-details-text-color-hover':
				$default = get_theme_mod( 'yith_proteo_button_style_1_text_color_hover', '#ffffff' );
				break;
		}

		return $default;
	}
}

if( ! function_exists( 'yith_wcqv_is_flatsome' ) ) {
	/**
	 * Check if current installed theme is Flatsome or a child
	 *
	 * @since 1.5.1
	 * @author Francesco Licandro
	 * @return boolean
	 */
	function yith_wcqv_is_flatsome() {

		if( is_child_theme() ) {
			$temp_obj	= wp_get_theme();
			$theme_obj 	= wp_get_theme( $temp_obj->get('Template') );
		} else {
			$theme_obj 	= wp_get_theme();
		}

		return 'Flatsome' === $theme_obj->get('Name');
	}
}