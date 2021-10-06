<?php
/**
 * PHP Generator Functions
 *
 * PHP 5.5+ only.
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cartesian product based on generator.
 *
 * @param  array  $vectors
 * @return array
 */
function wc_cp_cartesian( $vectors ) {

	if ( $vectors ) {

		$vector_keys     = array_keys( $vectors );
		$last_vector_key = end( $vector_keys );

		if ( $last_vector_values = array_pop( $vectors ) ) {

			foreach ( wc_cp_cartesian( $vectors ) as $p ) {
				foreach ( $last_vector_values as $value ) {
					yield $p + array( $last_vector_key => $value );
				}
			}
		}

	} else {
		yield array();
	}
}
