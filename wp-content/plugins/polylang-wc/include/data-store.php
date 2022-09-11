<?php
/**
 * @package Polylang-WC
 */

/**
 * Data store factory.
 *
 * As our language data stores don't implement the WC_Object_Data_Store_Interface
 * interface, it appears risky to use WC_Data_Store directly, so it has been thought
 * to be better to create our own class which can be used in a similar way.
 *
 * @since 1.0
 *
 * @template TKey
 * @template TValue
 */
class PLLWC_Data_Store {

	/**
	 * Array of data stores.
	 *
	 * @var array<TKey, class-string<TValue>>
	 */
	private static $stores = array(
		'order_language'   => 'PLLWC_Order_Language_CPT',
		'product_language' => 'PLLWC_Product_Language_CPT',
	);

	/**
	 * Loads a data store.
	 *
	 * @since 1.0
	 *
	 * @throws Exception If the data store doesn't exist.
	 *
	 * @param TKey $object_type Identifier for the data store, typically 'order_language' or 'product_language'.
	 * @return TValue
	 */
	public static function load( $object_type ) {
		/**
		 * Filters the list of available data stores.
		 *
		 * @since 1.0
		 *
		 * @param array<TKey, class-string<TValue>> $stores Available data stores.
		 */
		self::$stores = apply_filters( 'pllwc_data_stores', self::$stores );

		/** @var class-string<TValue> */
		$store = self::$stores[ $object_type ];

		if ( class_exists( $store ) ) {
			return new $store();
		}

		throw new Exception( 'Invalid data store.' );
	}
}
