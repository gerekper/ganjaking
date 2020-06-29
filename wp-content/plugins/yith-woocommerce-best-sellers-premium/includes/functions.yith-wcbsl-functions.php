<?php

/**
 * Functions
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCBSL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'yith_wcbsl_rss_text_limit' ) ) {
	function yith_wcbsl_rss_text_limit( $string, $length, $replacer = '...' ) {
		$string = strip_tags( $string );
		if ( strlen( $string ) > $length ) {
			return ( preg_match( '/^(.*)\W.*$/', substr( $string, 0, $length + 1 ), $matches ) ? $matches[1] : substr( $string, 0, $length ) ) . $replacer;
		}

		return $string;
	}
}

if ( ! function_exists( 'yith_wcbsl_get_terms' ) ) {
	function yith_wcbsl_get_terms( $args = array() ) {
		global $wp_version;

		if ( version_compare( '4.5.0', $wp_version, '>=' ) ) {
			return get_terms( $args );
		} else {
			$taxonomy = isset( $args['taxonomy'] ) ? $args['taxonomy'] : '';
			if ( isset( $args['taxonomy'] ) ) {
				unset( $args['taxonomy'] );
			}

			return get_terms( $taxonomy, $args );
		}
	}
}

if ( ! function_exists( 'yith_wcbsl_is_doing_query' ) ) {
	/**
	 * @return bool
	 * @since 1.1.17
	 */
	function yith_wcbsl_is_doing_query() {
		return YITH_WCBSL_Reports_Premium::is_doing_query();
	}
}