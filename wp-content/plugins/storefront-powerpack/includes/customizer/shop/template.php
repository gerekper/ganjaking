<?php
/**
 * Storefront Powerpack template functions.
 *
 * @package Storefront_Powerpack
 */

if ( ! function_exists( 'sp_loop_product_description' ) ) {
	/**
	 * Display the product short description
	 *
	 * @return void
	 */
	function sp_loop_product_description() {
		global $product;

		$wc_product = wc_get_product( $product );

		if ( ! $wc_product ) {
			return false;
		}

		$short_description = $wc_product->get_short_description();

		if ( '' !== $short_description ) {
			echo '<div itemprop="description">' . wp_kses_post( $short_description ) . '</div>';
		}
	}
}

if ( ! function_exists( 'sp_scroll_wrapper' ) ) {
	/**
	 * Used to provide a unique wrapper for the inifinte scroll script to interact with.
	 *
	 * @return void
	 */
	function sp_scroll_wrapper() {
		$infinite_scroll = get_theme_mod( 'sp_infinite_scroll', false );

		if ( true === $infinite_scroll ) {
			echo '<div class="scroll-wrap">';
		}
	}
}

if ( ! function_exists( 'sp_scroll_wrapper_close' ) ) {
	/**
	 * Close the inifinite scroll wrapper.
	 *
	 * @return void
	 */
	function sp_scroll_wrapper_close() {
		$infinite_scroll = get_theme_mod( 'sp_infinite_scroll', false );

		if ( true === $infinite_scroll ) {
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'sp_product_loop_wrap' ) ) {
	/**
	 * Used to wrap instances of the product loop and specify how many columns products should be arranged in to.
	 *
	 * @return void
	 */
	function sp_product_loop_wrap() {
		$columns = get_theme_mod( 'sp_product_columns' );

		if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) {
			echo '<div class="columns-' . esc_attr( $columns ) . '">';
		}
	}
}

if ( ! function_exists( 'sp_product_loop_wrap_close' ) ) {
	/**
	 * Closes the product loop wrap.
	 *
	 * @return void
	 */
	function sp_product_loop_wrap_close() {
		if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) {
			echo '</div>';
		}
	}
}