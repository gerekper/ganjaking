<?php
/**
 * Functions
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Zoom Magnifier
 * @version 1.1.2
 */

if ( ! defined( 'YITH_WCMG' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'yith_wcmg_is_enabled' ) ) {
	/**
	 * Check if the plugin is enabled for the current context
	 *
	 * @param string $path
	 * @param array  $var
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	function yith_wcmg_is_enabled() {

		if ( wp_is_mobile() ) {
			return ( 'yes' == get_option( 'yith_wcmg_enable_mobile' ) );

		}

		return get_option( 'yith_wcmg_enable_plugin' ) == 'yes';
	}
}

if ( ! function_exists( 'yit_shop_single_w' ) ) {
	/**
	 * Return the shop_single image width
	 *
	 * @return integer
	 * @since 1.0.0
	 */
	function yit_shop_single_w() {
		global $woocommerce;
		$size = yit_get_image_size( 'shop_single' );

		return $size['width'];
	}
}

if ( ! function_exists( 'yit_shop_thumbnail_w' ) ) {
	/**
	 * Return the shop_thumbnail image width
	 *
	 * @return integer
	 * @since 1.0.0
	 */
	function yit_shop_thumbnail_w() {
		global $woocommerce;
		$size = yit_get_image_size( 'shop_thumbnail' );

		return $size['width'];
	}
}

/* FIX TO WOOCOMMERCE 2.1 */
if ( ! function_exists( 'yit_get_image_size' ) ) {
	function yit_get_image_size( $size ) {
		if ( function_exists( 'wc_get_image_size' ) ) {
			return wc_get_image_size( $size );
		} else {
			global $woocommerce;

			return $woocommerce->get_image_size( $size );
		}
	}
}
