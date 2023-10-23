<?php
/**
 * Bricks theme compatibility
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Ajax Product FIlter
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'yith_wcan_bricks_custom_css' ) ) {
	/**
	 * Filters custom plugin CSS, to append a couple of custom rules dedicated to this theme
	 *
	 * @param string $css Current CSS.
	 * @return string Filtered CSS.
	 */
	function yith_wcan_bricks_custom_css( $css ) {
		$css .= '
			.yith-wcan-filters .filter-content input[type=checkbox],
			.yith-wcan-filters .filter-content input[type=radio] {
				display: inline;
				width: auto;
			}
		';

		return $css;
	}

	add_filter( 'yith_wcan_custom_css', 'yith_wcan_bricks_custom_css' );
}

if ( ! function_exists( 'yith_wcan_bricks_content_selector' ) ) {
	/**
	 * Filters content selector, returning correct value for Bricks theme
	 *
	 * @param string $selector Current selector.
	 * @return string Content selector.
	 */
	function yith_wcan_bricks_content_selector( $selector ) {
		$selector = 'main';

		return $selector;
	}

	add_filter( 'yith_wcan_content_selector', 'yith_wcan_bricks_content_selector' );
}

if ( ! function_exists( 'yith_wcan_bricks_lazy_load_support' ) ) {
	/**
	 * Force system to re-init Bricks's lazy load after filtering
	 *
	 * @return void
	 */
	function yith_wcan_bricks_lazy_load_support() {
		$js = 'jQuery( ( $ ) => {
		   $( document ).on( "yith-wcan-ajax-filtered", bricksLazyLoad );
		} );';
		wp_add_inline_script( 'bricks-woocommerce', $js );
	}

	add_filter( 'wp_enqueue_scripts', 'yith_wcan_bricks_lazy_load_support', 99 );
}
