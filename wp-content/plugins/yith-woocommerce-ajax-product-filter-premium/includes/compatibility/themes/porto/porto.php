<?php
/**
 * Porto theme compatibility
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Ajax Product FIlter
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

add_filter( 'yith_wcan_use_wp_the_query_object', '__return_true' );

if ( ! function_exists( 'yith_wcan_porto_content_selector' ) ) {
	/**
	 * Filters content selector, returning correct value for Porto theme
	 *
	 * @param string $selector Current selector.
	 * @return string Content selector.
	 */
	function yith_wcan_porto_content_selector( $selector ) {
		$selector = '#main';

		return $selector;
	}

	add_filter( 'yith_wcan_content_selector', 'yith_wcan_porto_content_selector' );
}

if ( ! function_exists( 'yith_wcan_porto_lazy_load_support' ) ) {
	/**
	 * Force system to reinit filters after Porto's skeleton lazy load
	 *
	 * @return void
	 */
	function yith_wcan_porto_lazy_load_support() {
		$js = 'jQuery( function($){
		   $(document).on("skeleton-loaded", function(){$(document).trigger("yith_wcan_init_shortcodes")});
		} );';
		wp_add_inline_script( 'yith-wcan-shortcodes', $js );
	}

	add_filter( 'wp_enqueue_scripts', 'yith_wcan_porto_lazy_load_support', 99 );
}
