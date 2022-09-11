<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the coupons in the backend.
 *
 * @since 0.3.6
 */
class PLLWC_Admin_Coupons extends PLLWC_Coupons {

	/**
	 * Constructor.
	 *
	 * @since 0.3.6
	 */
	public function __construct() {
		parent::__construct();
		add_filter( 'get_terms_args', array( $this, 'get_terms_args' ), 10, 2 );
	}

	/**
	 * Filters the product category per language.
	 *
	 * @since 0.3.6
	 *
	 * @param array    $args       Arguments passed to WP_Term_Query.
	 * @param string[] $taxonomies Taxonomies passed to WP_Term_Query.
	 * @return array Modified arguments.
	 */
	public function get_terms_args( $args, $taxonomies ) {
		if ( isset( $GLOBALS['post_type'] ) && 'shop_coupon' === $GLOBALS['post_type'] && in_array( 'product_cat', $taxonomies ) ) {
			$args['lang'] = PLLWC_Admin::get_preferred_language();
		}
		return $args;
	}
}
