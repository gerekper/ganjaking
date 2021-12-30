<?php
/**
 * WC_PB_Data class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Bundles Data class.
 *
 * Product Bundles Data filters and includes.
 *
 * @class    WC_PB_Data
 * @version  5.5.0
 */
class WC_PB_Data {

	public static function init() {

		// DB API for custom PB tables.
		require_once( WC_PB_ABSPATH . 'includes/data/class-wc-pb-db.php' );

		// Bundled Item Data CRUD class.
		require_once( WC_PB_ABSPATH . 'includes/data/class-wc-bundled-item-data.php' );

		// Product Bundle CPT data store.
		require_once( WC_PB_ABSPATH . 'includes/data/class-wc-product-bundle-data-store-cpt.php' );

		// Register the Product Bundle Custom Post Type data store.
		add_filter( 'woocommerce_data_stores', array( __CLASS__, 'register_bundle_type_data_store' ), 10 );
	}

	/**
	 * Registers the Product Bundle Custom Post Type data store.
	 *
	 * @param  array  $stores
	 * @return array
	 */
	public static function register_bundle_type_data_store( $stores ) {

		$stores[ 'product-bundle' ] = 'WC_Product_Bundle_Data_Store_CPT';

		return $stores;
	}
}

WC_PB_Data::init();
