<?php
/**
 * Legacy Functions & hooks
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if( ! function_exists( 'YITH_WCWL_Admin_Init' ) ){
	/**
	 * Deprecated function that used to return admin class single instance
	 *
	 * @return YITH_WCWL_Admin
	 * @since 2.0.0
	 */
	function YITH_WCWL_Admin_Init(){
		_deprecated_function( __FUNCTION__, '3.0.0', 'YITH_WCWL_Admin' );
		return YITH_WCWL_Admin();
	}
}

if( ! function_exists( 'YITH_WCWL_Init' ) ){
	/**
	 * Deprecated function that used to return init class single instance
	 *
	 * @return YITH_WCWL_Frontend
	 * @since 2.0.0
	 */
	function YITH_WCWL_Init(){
		_deprecated_function( __FUNCTION__, '3.0.0', 'YITH_WCWL_Frontend' );
		return YITH_WCWL_Frontend();
	}
}