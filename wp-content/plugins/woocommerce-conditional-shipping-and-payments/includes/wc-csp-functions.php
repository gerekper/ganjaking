<?php
/**
 * Conditional Shipping and Payments Functions
 *
 * @package  WooCommerce Conditional Shipping and Payments
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds terms tree of a flatten terms array.
 *
 * @since  1.8.1
 *
 * @param  array  $terms Array of WP_Term objects.
 * @param  int    $parent_id
 * @return array
 */
function wc_csp_build_taxonomy_tree( $terms, $parent_id = 0 ) {

	if ( empty( $terms ) ) {
		return array();
	}

	// Build.
	$tree = array();
	foreach ( $terms as $index => $term ) {
		if ( $term->parent === $parent_id && ! isset( $tree[ $term->term_id ] ) ) {
			$tree[ $term->term_id ]           = $term;
			$tree[ $term->term_id ]->children = wc_csp_build_taxonomy_tree( $terms, $term->term_id );
		}
	}

	return $tree;
}

/**
 * Prints <option/> elements for a given terms tree.
 *
 * @since  1.8.1
 *
 * @param  array  $terms Array of WP_Term objects.
 * @param  array  $selected_ids
 * @param  string $prefix_html
 * @param  array  $args
 * @return void
 */
function wc_csp_print_taxonomy_tree_options( $terms, $selected_ids = array(), $args = array() ) {

	$args = wp_parse_args( $args, array(
		'prefix_html'   => '',
		'seperator'     => _x( '%1$s&nbsp;&gt;&nbsp;%2$s', 'term separator', 'woocommerce-conditional-shipping-and-payments' ),
		'shorten_text'  => true,
		'shorten_level' => 3,
		'term_path'     => array()
	) );

	$term_path = $args[ 'term_path' ];

	foreach ( $terms as $term ) {

		$term_path[] = $term->name;
		$option_text = $term->name;

		if ( ! empty( $args[ 'prefix_html' ] ) ) {
			$option_text = sprintf( $args[ 'seperator' ], $args[ 'prefix_html' ], $option_text );
		}

		// Print option element.
		echo '<option value="' . esc_attr( $term->term_id ) . '" ' . selected( in_array( $term->term_id, $selected_ids ), true, false ) . '>';

		if ( $args[ 'shorten_text' ] && count( $term_path ) > $args[ 'shorten_level' ] ) {
			echo esc_html( sprintf( _x( '%1$s&nbsp;&gt;&nbsp;&hellip;&nbsp;&gt;&nbsp;%2$s', 'many terms separator', 'woocommerce-conditional-shipping-and-payments' ), $term_path[ 0 ], $term_path[ count( $term_path ) - 1 ] ) );
		} else {
			echo esc_html( $option_text );
		}

		echo '</option>';

		// Recursive call to print children.
		if ( ! empty( $term->children ) ) {

			// Reset `prefix_html` argument to recursive mode.
			$reset_args                  = $args;
			$reset_args[ 'prefix_html' ] = $option_text;
			$reset_args[ 'term_path' ]   = $term_path;

			wc_csp_print_taxonomy_tree_options( $term->children, $selected_ids, $reset_args );
		}

		$term_path = $args[ 'term_path' ];
	}
}

/**
 * Get debug status.
 *
 * @since  1.11.0
 *
 * @return bool
 */
function wc_csp_debug_enabled() {

	$debug = ( 'yes' === get_option( 'wccsp_debug_enabled', 'no' ) );

	/**
	 * 'woocommerce_csp_debug_enabled' filter.
	 */
	return apply_filters( 'woocommerce_csp_debug_enabled', $debug );
}
