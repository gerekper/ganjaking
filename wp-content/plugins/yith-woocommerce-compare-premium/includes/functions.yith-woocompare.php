<?php
/**
 * Functions
 *
 * @author Your Inspiration Themes
 * @package YITH Woocommerce Compare
 * @version 1.1.4
 */

if ( !defined( 'YITH_WOOCOMPARE' ) ) { exit; } // Exit if accessed directly

if( ! function_exists( 'yith_woocompare_user_style' ) ) {
	/**
	 * Return custom style based on user options
	 *
	 * @since 2.1.0
	 * @author Francesco Licandro
	 * @return string
	 */
	function yith_woocompare_user_style() {

		$custom_css = "
				#yith-woocompare-cat-nav h3 {
                    color: " . yith_woocompare_get_proteo_default( 'yith-woocompare-categories-filter-title-color', '#333333' ) . ";
                }
                #yith-woocompare-cat-nav li a {
                    color: " . get_option( 'yith-woocompare-categories-filter-link-color', '#777777' ) . ";
                }
                #yith-woocompare-cat-nav li a:hover, #yith-woocompare-cat-nav li .active {
                    color: " . get_option( 'yith-woocompare-categories-filter-link-hover-color', '#333333' ) . ";
                }
                table.compare-list .remove a {
                    color: " . get_option( 'yith-woocompare-table-remove-color', '#777777' ) . ";
                }
                table.compare-list .remove a:hover {
                    color: " . get_option( 'yith-woocompare-table-remove-color-hover', '#333333' ) . ";
                }
                a.button.yith_woocompare_clear, table.compare-list .product_info .button, table.compare-list .add-to-cart .button, table.compare-list .added_to_cart {
                    color: " . yith_woocompare_get_proteo_default( 'yith-woocompare-table-button-text-color', '#ffffff' ) . ";
                    background-color: " . yith_woocompare_get_proteo_default( 'yith-woocompare-table-button-color', '#b2b2b2' ) . ";
                }
               	a.button.yith_woocompare_clear:hover, table.compare-list .product_info .button:hover, table.compare-list .add-to-cart .button:hover, table.compare-list .added_to_cart:hover {
                    color: " . yith_woocompare_get_proteo_default( 'yith-woocompare-table-button-text-color-hover', '#ffffff' ) . ";
                    background-color: " . yith_woocompare_get_proteo_default( 'yith-woocompare-table-button-color-hover', '#303030' ) . ";
                }
                table.compare-list .rating .star-rating {
                    color: " . get_option( 'yith-woocompare-table-star', '#303030' ) . ";
                }
                #yith-woocompare-related .yith-woocompare-related-title {
                    color: " . yith_woocompare_get_proteo_default( 'yith-woocompare-related-title-color', '#333333' ) . ";
                }
                #yith-woocompare-related .related-products .button {
                    color: " . yith_woocompare_get_proteo_default( 'yith-woocompare-related-button-text-color', '#ffffff' ) . ";
                    background-color: " . yith_woocompare_get_proteo_default( 'yith-woocompare-related-button-color', '#b2b2b2' ) . ";
                }
                #yith-woocompare-related .related-products .button:hover {
                    color: " . yith_woocompare_get_proteo_default( 'yith-woocompare-related-button-text-color-hover', '#ffffff' ) . ";
                    background-color: " . yith_woocompare_get_proteo_default( 'yith-woocompare-related-button-color-hover', '#303030' ) . ";
                }
                #yith-woocompare-share h3 {
                    color: " . yith_woocompare_get_proteo_default( 'yith-woocompare-share-title-color', '#333333' ) . ";
                }
                table.compare-list tr.different, table.compare-list tr.different th {
                	background-color: " . get_option( 'yith-woocompare-highlights-color', '#e4e4e4' ) . " !important;
                }";

		return apply_filters( 'yith_woocompare_user_style_value', $custom_css );
	}
}

if( ! function_exists( 'yith_woocompare_get_vendor_name' ) ) {
	/**
	 * Print vendor name under product name in Compare Table. Needs YITH WooCommerce Multi Vendor active
	 *
	 * @since 2.2.0
	 * @author Francesco Licandro
	 *
	 * @param string $product_id The product ID
	 * @return string
	 */
	function yith_woocompare_get_vendor_name( $product ) {

		if( ! function_exists('yith_get_vendor') || empty( $product ) || ! is_object( $product ) ) {
			return '';
		}
		
		$vendor = yith_get_vendor( $product, 'product' );

		if ( $vendor->is_valid() ) {
			$args          = array(
				'vendor' => $vendor,
				'label_color' => 'color: ' . get_option( 'yith_vendors_color_name' )
			);

			$template_info = array(
				'name'    => 'vendor-name-title',
				'args'    => $args,
				'section' => 'woocommerce/loop',
			);

			$template_info = apply_filters( 'yith_woocommerce_vendor_name_template_info', $template_info );

			extract( $template_info );

			ob_start();
			yith_wcpv_get_template( $name, $args, $section );
			return ob_get_clean();
		}
		
		return '';
	}
}

if ( ! function_exists( 'yith_woocompare_get_proteo_default' ) ) {
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
	function yith_woocompare_get_proteo_default( $key, $default = '', $force_default = false ) {

		// get value from DB if requested and return if not empty
		! $force_default && $value = get_option( $key, $default );

		if ( ! empty( $value ) ) {
			return $value;
		}

		if ( ! defined( 'YITH_PROTEO_VERSION' ) ) {
			return $default;
		}


		switch ( $key ) {
			case 'yith-woocompare-related-title-color':
			case 'yith-woocompare-share-title-color':
			case 'yith-woocompare-categories-filter-title-color':
				$default = get_theme_mod( 'yith_proteo_base_font_color', '#404040' );
				break;
			case 'yith-woocompare-related-button-color':
			case 'yith-woocompare-table-button-color':
				$default = get_theme_mod( 'yith_proteo_button_style_1_bg_color', '#448a85' );
				break;
			case 'yith-woocompare-related-button-color-hover':
			case 'yith-woocompare-table-button-color-hover':
				$default = get_theme_mod( 'yith_proteo_button_style_1_bg_color_hover', yith_proteo_adjust_brightness( get_theme_mod( 'yith_proteo_main_color_shade', '#448a85' ), 0.2 ) );
				break;
			case 'yith-woocompare-related-button-text-color':
			case 'yith-woocompare-table-button-text-color':
				$default = get_theme_mod( 'yith_proteo_button_style_1_text_color', '#ffffff' );
				break;
			case 'yith-woocompare-related-button-text-color-hover':
			case 'yith-woocompare-table-button-text-color-hover':
				$default = get_theme_mod( 'yith_proteo_button_style_1_text_color_hover', '#ffffff' );
				break;

		}

		return $default;
	}
}