<?php

/**
 * Mock certain calls that pretend that key WooCommerce settings are in
 * existence.
 */
class WoocommerceGpfWcMocks {

	public static function setupMocks() {
		$taxonomies = [
			'product_type',
			'product_cat',
			'product_tag',
			'product_shipping_class',
			'pa_colour',
		];

		/**
		 * Mock a set of basic taxonomies being attached to the product post type,
		 * including from WooCommerce core, and a user-defined attribute set.
		 *
		 * Mocks get_object_taxonomies( 'product' );
		 */
		\WP_Mock::userFunction( 'get_object_taxonomies', array(
			'args'   => [ 'product' ],
			'return' => $taxonomies,
		) );

		/**
		 * Mock the individual taxonomy information.
		 *
		 * Mocks get_taxonomy($slug);
		 */
		foreach ( $taxonomies as $slug ) {
			\WP_Mock::userFunction( 'get_taxonomy', array(
				'args'   => [ $slug ],
				'return' => new MockWpTaxonomy( $slug ),
			) );
		}

		\WP_Mock::userFunction( 'wc_get_dimension', array(
			'args'    => [ Mockery::any(), Mockery::any() ],
			'return'  => function ( $dimension, $to ) {
				return $dimension;
			},
		) );
		\WP_Mock::userFunction( 'wc_get_weight', array(
			'args'    => [ '2', 'g' ],
			'return'  => function ( $dimension, $to ) {
				return $dimension * 1.5;
			},
		) );
		\WP_Mock::userFunction( 'wc_get_weight', array(
			'args'    => [ Mockery::any(), Mockery::any() ],
			'return'  => function ( $dimension, $to ) {
				return $dimension;
			},
		) );

	}
}
