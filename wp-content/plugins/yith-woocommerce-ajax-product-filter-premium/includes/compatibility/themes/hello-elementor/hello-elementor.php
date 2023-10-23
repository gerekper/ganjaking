<?php
/**
 * Hello Elementor theme compatibility
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Ajax Product FIlter
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'yith_wcan_hello_elementor_content_selector' ) ) {
	/**
	 * Filters content selector, returning correct value for Hello Elementor theme
	 *
	 * @param string $selector Current selector.
	 * @return string Content selector.
	 */
	function yith_wcan_hello_elementor_content_selector( $selector ) {
		$selector = '#main';

		return $selector;
	}

	add_filter( 'yith_wcan_content_selector', 'yith_wcan_hello_elementor_content_selector' );
}
