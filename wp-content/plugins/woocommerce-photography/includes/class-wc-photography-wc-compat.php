<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooCommerce compatibility methods.
 */
class WC_Photography_WC_Compat {

	/**
	 *
	 * Updates a term meta. Compatibility function for WC 3.6.
	 *
	 * @since 1.0.17
	 *
	 * @param int    $term_id    Term ID.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 * @return bool
	 */
	public static function update_term_meta( $term_id, $meta_key, $meta_value ) {
		if ( version_compare( WC_VERSION, '3.6', 'ge' ) ) {
			return update_term_meta( $term_id, $meta_key, $meta_value );
		} else {
			return update_woocommerce_term_meta( $term_id, $meta_key, $meta_value );
		}
	}

	/**
	 * Gets a term meta. Compatibility function for WC 3.6.
	 *
	 * @since 1.0.17
	 *
	 * @param int    $term_id Term ID.
	 * @param string $key     Meta key.
	 * @param bool   $single  Whether to return a single value. (default: true).
	 * @return mixed
	 */
	public static function get_term_meta( $term_id, $key, $single = true ) {
		if ( version_compare( WC_VERSION, '3.6', 'ge' ) ) {
			return get_term_meta( $term_id, $key, $single );
		} else {
			return get_woocommerce_term_meta( $term_id, $key, $single );
		}
	}

}
