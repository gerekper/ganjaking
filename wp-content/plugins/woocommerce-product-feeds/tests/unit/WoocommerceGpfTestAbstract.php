<?php

class WoocommerceGpfTestAbstract extends \PHPUnit\Framework\TestCase {

	public function setUp() {
		\WP_Mock::setUp();
	}

	public function tearDown() {
		\WP_Mock::tearDown();
	}

	/**
	 * This product has the following properties:
	 *     - Its ID is 1
	 *     - It is a simple product, called "Simple product"
	 *     - It has a SKU of TESTSKUVALUE, regular price of 10, sale price of 9
	 *     - It is a category called 'Mock term #1', whose term ID is 1
	 *         - The category has custom_label_{1,3,4} set to "Category-level setting"
	 *     - It has a shipping class called 'Standard parcel', with term ID 2
	 *     - It has custom_label{2,4} set to "Product-level setting"
	 *     - It has brand set to "Product-level setting"
	 */
	protected function setup_simple_product( $product_gpf_config = [], $category_gpf_config = [] ) {
		$category = new MockWpTerm( 1, 'Mock term #1' );
		$category->mock_set_gpf_config( array_merge(
			[
				'brand'          => '',
				'custom_label_1' => 'Category-level setting',
				'custom_label_3' => 'Category-level setting',
				'custom_label_4' => 'Category-level setting',
			],
			$category_gpf_config
		) );
		$shipping_class = new MockWpTerm( 2, 'Standard parcel' );
		$product        = new MockWcProduct(
			1,
			'simple',
			array_merge(
				[
					'title'             => 'Simple product',
					'description'       => 'Simple product description',
					'short_description' => 'Simple product short description',
					'sku'               => 'TESTSKUVALUE',
					'regular_price'     => 12.00,
					'sale_price'        => 9.00,
					'price'             => 9.00,
					'weight'            => 1,
					'width'             => 5,
					'height'            => 5,
					'length'            => 5,
				],
				$product_gpf_config
			)
		);
		$product->mock_set_categories( [ $category ] );
		$product->mock_set_shipping_classes( [ $shipping_class ] );
		$product->mock_set_gpf_config( [
			'custom_label_2' => 'Product-level setting',
			'custom_label_4' => 'Product-level setting',
			'brand'          => 'Product-level setting',
		] );
		\WP_Mock::userFunction( 'wc_get_product', array(
			'args'   => [ 1 ],
			'return' => $product,
		) );
		\WP_Mock::userFunction( 'get_post_meta', array(
			'args'   => [ 1, 'test_meta', false ],
			'return' => [ 'Test meta value' ],
		) );

		return $product;
	}

	/**
	 * Function that emulates a product with corrupt (non-array) gpf config.
	 */
	protected function setup_simple_product_corrupt_gpf_config() {
		$category = new MockWpTerm( 1, 'Mock term #1' );
		$category->mock_set_gpf_config( [
			'brand'          => '',
			'custom_label_1' => 'Category-level setting',
			'custom_label_3' => 'Category-level setting',
			'custom_label_4' => 'Category-level setting',
		] );
		$shipping_class = new MockWpTerm( 2, 'Standard parcel' );
		$product        = new MockWcProduct(
			1,
			'simple',
			[
				'title'             => 'Simple product',
				'description'       => 'Simple product description',
				'short_description' => 'Simple product short description',
				'sku'               => 'TESTSKUVALUE',
				'regular_price'     => 12.00,
				'sale_price'        => 9.00,
				'price'             => 9.00,
				'weight'            => 1,
				'width'             => 5,
				'height'            => 5,
				'length'            => 5,
			]
		);
		$product->mock_set_categories( [ $category ] );
		$product->mock_set_shipping_classes( [ $shipping_class ] );
		$product->mock_set_gpf_config( '12345678' );
		\WP_Mock::userFunction( 'wc_get_product', array(
			'args'   => [ 1 ],
			'return' => $product,
		) );
		\WP_Mock::userFunction( 'get_post_meta', array(
			'args'   => [ 1, 'test_meta', false ],
			'return' => [ 'Test meta value' ],
		) );

		return $product;
	}

	/**
	 * Mocks calls to various functions to pretend that a product exists.
	 *
	 * This product has the following properties:
	 *     - Its ID is 2
	 *     - It is a variable product, called "Variable product #2"
	 *     - It has a SKU of TESTMAINSKUVALUE
	 *     - It is a category called 'Mock term #2', whose term ID is 2
	 *         - The category has custom_label_{1,3,4} set to "Category-level setting"
	 *     - It has a shipping class called 'Standard parcel', with term ID 2
	 *     - It has custom_label{2,4} set to "Product-level setting"
	 *     - It has brand set to "Product-level setting"
	 *     - It has a single variation with product ID 3, called "Variation product #3"
	 *     - The variation has a SKU of TESTVARIATIONSKUVALUE
	 *     - The variation has a shipping class called 'Large parcel', with term ID 3
	 *     - The variation has its brand set to "Variation-level setting"
	 */
	protected function setup_variable_product() {
		$category = new MockWpTerm( 2, 'Mock category #2' );
		$category->mock_set_gpf_config( [
			'custom_label_1' => 'Category-level setting',
			'custom_label_3' => 'Category-level setting',
			'custom_label_4' => 'Category-level setting',
		] );
		$standard_shipping_class = new MockWpTerm( 2, 'Standard parcel' );
		$large_shipping_class    = new MockWpTerm( 3, 'Large parcel' );
		$variable_product        = new MockWcProduct(
			2,
			'variable',
			[
				'title'             => 'Variable product #2',
				'sku'               => 'TESTMAINSKUVALUE',
				'description'       => 'Variable description #2',
				'short_description' => 'Variable short description #2',
				'regular_price'     => 10.00,
				'sale_price'        => 9.00,
				'price'             => 9.00,
			]
		);
		$variable_product->mock_set_categories( [ $category ] );
		$variable_product->mock_set_shipping_classes( [ $standard_shipping_class ] );
		$variable_product->mock_set_gpf_config( [
			'custom_label_2' => 'Product-level setting',
			'custom_label_4' => 'Product-level setting',
			'brand'          => 'Product-level setting',
		] );
		\WP_Mock::userFunction( 'wc_get_product', array(
			'args'   => [ 2 ],
			'return' => $variable_product
		) );
		\WP_Mock::userFunction( 'get_post_meta', array(
			'args'   => [ 2, 'test_meta', false ],
			'return' => [ 'Test meta value' ],
		) );
		$variation_product = new MockWcProduct(
			3,
			'variation',
			[
				'title'         => 'Variation product #3',
				'sku'           => 'TESTVARIATIONSKUVALUE',
				'description'   => 'Variation description #3',
				'regular_price' => 10.00,
				'sale_price'    => 9.00,
				'price'         => 9.00,
			]
		);
		$variation_product->mock_set_shipping_classes( [ $large_shipping_class ] );
		$variation_product->mock_set_gpf_config( [
			'brand' => 'Variation-level setting',
			'gtin'  => 'variation-gtin',
		] );
		\WP_Mock::userFunction( 'wc_get_product', array(
			'args'   => [ 3 ],
			'return' => $variation_product
		) );
		\WP_Mock::userFunction( 'get_post_meta', array(
			'args'   => [ 3, 'test_meta', false ],
			'return' => [ 'Test meta value' ],
		) );

		return $variable_product;
	}

}
