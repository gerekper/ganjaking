<?php

/**
 * Set up mocks for GPF plugin / test specific functions.
 */
class WoocommerceGpfMocks {

	public static function setupMocks() {
		self::setupConfigSettings();
	}

	/**
	 * @return array
	 */
	public static function getMockConfigSettings() {
		return [
			'product_fields'      => [
				'availability'            => 'on',
				'mpn'                     => 'on',
				'product_type'            => 'on',
				'google_product_category' => 'on',
				'size_system'             => 'on',
				'bing_category'           => 'on',
				'delivery_label'          => 'on',
				'material'                => 'on',

				// Store-level setting only
				'custom_label_0'          => 'on',

				// Category-level setting only
				'custom_label_1'          => 'on',

				// Product-level setting only
				'custom_label_2'          => 'on',

				// Variation-level setting only
				'gtin'                    => 'on',

				// Store-level setting, overridden at category level
				'custom_label_3'          => 'on',

				// Store-level setting, overridden at category and product_level
				'custom_label_4'          => 'on',

				// Store-level setting, overridden just at product-level.
				'brand'                   => 'on',

			],
			'product_defaults'    => [
				'bing_category'           => 'Software',
				'google_product_category' => 'Software',
				'custom_label_0'          => 'Store-level setting',
				'custom_label_3'          => 'Store-level setting',
				'custom_label_4'          => 'Store-level setting',
				'brand'                   => 'Store-level setting',
			],
			'product_prepopulate' => [
				'description'    => 'description:fullvar',
				'delivery_label' => 'tax:product_shipping_class',
				'mpn'            => 'field:sku',
				'material'       => 'meta:test_meta',
			],
		];
	}

	/**
	 * Provide a set of config settings that we use for the tests.
	 *
	 * Mocks get_option( 'woocommerce_gpf_config' );
	 *
	 * @return array
	 */
	private static function setupConfigSettings() {
		// get_option( 'woocommerce-gpf_config' );
		\WP_Mock::userFunction( 'get_option' )
			->with( 'woocommerce_gpf_config' )
			->andReturn( self::getMockConfigSettings() );
	}

}
