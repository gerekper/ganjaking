<?php
/**
 * Backward compatibility functions
 *
 * @package WC_Instagram/Functions
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the terms in a given taxonomy or list of taxonomies.
 *
 * @since 3.0.0
 *
 * @param array $args The arguments.
 * @return array
 */
function wc_instagram_get_terms( $args ) {
	$terms = get_terms( $args );

	if ( is_wp_error( $terms ) ) {
		$terms = array();
	}

	return $terms;
}
