<?php
/**
 * @package Polylang-WC
 */

if ( ! function_exists( 'pll_remove_anonymous_object_filter' ) ) {
	/**
	 * Remove an anonymous object filter.
	 * Thanks to toscho.
	 *
	 * @see http://wordpress.stackexchange.com/questions/57079/how-to-remove-a-filter-that-is-an-anonymous-object/57088#57088
	 *
	 * @since 0.1
	 *
	 * @param string $tag      Hook name.
	 * @param array  $method   [0] => class name, [1] => method name.
	 * @param int    $priority Hook priority, defaults to 10.
	 * @return void
	 */
	function pll_remove_anonymous_object_filter( $tag, $method, $priority = 10 ) {
		if ( ! empty( $GLOBALS['wp_filter'][ $tag ][ $priority ] ) ) {
			foreach ( $GLOBALS['wp_filter'][ $tag ][ $priority ] as $function ) {
				if ( is_array( $function ) && is_array( $function['function'] ) && is_a( $function['function'][0], $method[0] ) && $method[1] === $function['function'][1] ) {
					remove_filter( $tag, array( $function['function'][0], $method[1] ), $priority );
				}
			}
		}
	}
}

if ( ! function_exists( 'pll_get_anonymous_object_from_filter' ) ) {
	/**
	 * Get an anonymous object from one of its known filter.
	 *
	 * @see pll_remove_anonymous_object_filter()
	 *
	 * @since 0.1
	 *
	 * @param string $tag      Hook name.
	 * @param array  $method   [0] => class name, [1] => method name.
	 * @param int    $priority Hook priority, defaults to 10.
	 * @return object|null
	 */
	function pll_get_anonymous_object_from_filter( $tag, $method, $priority = 10 ) {
		if ( ! empty( $GLOBALS['wp_filter'][ $tag ][ $priority ] ) ) {
			foreach ( $GLOBALS['wp_filter'][ $tag ][ $priority ] as $function ) {
				if ( is_array( $function ) && is_array( $function['function'] ) && is_a( $function['function'][0], $method[0] ) && $method[1] === $function['function'][1] ) {
					return $function['function'][0];
				}
			}
		}
		return null;
	}
}

if ( ! function_exists( 'PLLWC' ) ) {
	/**
	 * Returns the Polylang for WooCommerce instance.
	 *
	 * @since 0.1
	 *
	 * @return Polylang_Woocommerce
	 */
	function PLLWC() { // PHPCS:ignore WordPress.NamingConventions.ValidFunctionName
		return Polylang_Woocommerce::instance();
	}
}
