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
 * @global string $wp_version The WordPress version.
 *
 * @param array $args The arguments.
 * @return array
 */
function wc_instagram_get_terms( $args ) {
	global $wp_version;

	if ( version_compare( $wp_version, '4.5', '>=' ) ) {
		$terms = get_terms( $args );
	} else {
		$terms = get_terms( $args['taxonomy'], $args );
	}

	if ( is_wp_error( $terms ) ) {
		$terms = array();
	}

	return $terms;
}
