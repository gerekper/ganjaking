<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the compatibility with Yith WooCommerce Ajax Search.
 * Version tested: 1.5.3.
 *
 * @since 0.9
 */
class PLLWC_Yith_WCAS {

	/**
	 * Constructor.
	 *
	 * @since 0.9
	 */
	public function __construct() {
		add_filter( 'do_shortcode_tag', array( $this, 'filter_shortcode' ), 99, 2 );
	}

	/**
	 * Filters the home url in the search form outputed by the shortcode.
	 *
	 * @since 0.9
	 *
	 * @param string $output Shortcode output.
	 * @param string $tag    Shortcode tag.
	 * @return string Modified output.
	 */
	public function filter_shortcode( $output, $tag ) {
		if ( 'yith_woocommerce_ajax_search' === $tag ) {
			$output = PLL()->filters_search->get_search_form( $output );
		}
		return $output;
	}
}
