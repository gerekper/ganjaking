<?php
/**
 * WooCommerce Yoast SEO plugin file.
 *
 * @package WPSEO/WooCommerce
 */

/**
 * Represents the product's brand.
 */
class WPSEO_WooCommerce_Product_Brand_Presenter extends WPSEO_WooCommerce_Abstract_Product_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'product:brand';

	/**
	 * Gets the raw value of a presentation.
	 *
	 * @return string The raw value.
	 */
	public function get() {
		$schema_brand = $this->helpers->options->get( 'woo_schema_brand' );
		if ( $schema_brand !== '' ) {
			$brand = $this->get_brand_term_name( $schema_brand, $this->product );
			if ( ! empty( $brand ) ) {
				return $brand;
			}
		}

		return '';
	}

	/**
	 * Retrieve the primary and if that doesn't exist first term for the brand taxonomy.
	 *
	 * @param string      $schema_brand The taxonomy the site uses for brands.
	 * @param \WC_Product $product      The product we're finding the brand for.
	 *
	 * @return bool|string The brand name or false on failure.
	 */
	private function get_brand_term_name( $schema_brand, $product ) {
		$primary_term = WPSEO_WooCommerce_Utils::search_primary_term( [ $schema_brand ], $product );
		if ( ! empty( $primary_term ) ) {
			return $primary_term;
		}
		$terms = get_the_terms( get_the_ID(), $schema_brand );
		if ( is_array( $terms ) && count( $terms ) > 0 ) {
			$term_values = array_values( $terms );
			$term        = array_shift( $term_values );

			return $term->name;
		}

		return false;
	}
}
