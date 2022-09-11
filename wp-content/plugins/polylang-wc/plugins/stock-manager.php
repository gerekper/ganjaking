<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the compatibility with:
 *
 * @see https://wordpress.org/plugins/woocommerce-stock-manager/ WooCommerce Stock Manager, version tested: 1.2.6.
 * 
 * @see https://woocommerce.com/products/bulk-stock-management/ WooCommerce Bulk Stock Management, version tested: 2.2.9.
 *
 * @since 0.5
 */
class PLLWC_Stock_Manager {

	/**
	 * Constructor.
	 *
	 * @since 0.5
	 */
	public function __construct() {
		add_action( 'parse_query', array( $this, 'parse_query' ), 20 );
	}

	/**
	 * Returns true if the query is filtered by a language
	 * or includes a translated taxonomy.
	 *
	 * @since 0.9
	 *
	 * @param array $qvars Query vars.
	 * @return bool
	 */
	public function is_language_in_query( $qvars ) {
		if ( isset( $qvars['tax_query'] ) ) {
			foreach ( $qvars['tax_query'] as $tax_query ) {
				if ( isset( $tax_query['taxonomy'] ) && ( 'language' === $tax_query['taxonomy'] || pll_is_translated_taxonomy( $tax_query['taxonomy'] ) ) ) {
					return true;
				}
			}
		}

		if ( ! empty( $qvars['lang'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Returns true if the query includes the product post type.
	 *
	 * @since 0.9
	 *
	 * @param array $qvars Query vars.
	 * @return bool
	 */
	public function is_product_in_query( $qvars ) {
		$product_types = array( 'product', 'product_variation' );
		return isset( $qvars['post_type'] ) && ( in_array( $qvars['post_type'], $product_types ) || ( is_array( $qvars['post_type'] ) && array_intersect( $qvars['post_type'], $product_types ) ) );
	}

	/**
	 * Make sure that the products are displayed in only one language (even when the admin languages filter requests all languages)
	 * to avoid conflicts if inconsistent information would be provided for the products translations.
	 *
	 * @since 0.3.2
	 *
	 * @param WP_Query $query WP_Query object.
	 * @return void
	 */
	public function parse_query( $query ) {
		$qvars = $query->query_vars;

		if ( ! $this->is_language_in_query( $qvars ) && $this->is_product_in_query( $qvars ) ) {
			$query->query_vars['lang'] = PLLWC_Admin::get_preferred_language();
		}
	}
}
