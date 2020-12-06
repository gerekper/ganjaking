<?php
/**
 * WC_CP_Data class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.9.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Composite Products Data class.
 *
 * Composite Products Data filters and includes.
 *
 * @class    WC_CP_Data
 * @version  3.9.0
 */
class WC_CP_Data {

	public static function init() {

		// Composite Product CPT data store.
		require_once( WC_CP_ABSPATH . 'includes/data/class-wc-product-composite-data-store-cpt.php' );

		// Register the Composite Product Custom Post Type data store.
		add_filter( 'woocommerce_data_stores', array( __CLASS__, 'register_composite_type_data_store' ), 10 );
	}

	/**
	 * Registers the Composite Product Custom Post Type data store.
	 *
	 * @param  array  $stores
	 * @return array
	 */
	public static function register_composite_type_data_store( $stores ) {

		$stores[ 'product-composite' ] = 'WC_Product_Composite_Data_Store_CPT';

		return $stores;
	}
}

WC_CP_Data::init();
