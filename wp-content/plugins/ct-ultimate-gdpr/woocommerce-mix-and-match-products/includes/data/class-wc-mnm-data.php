<?php
/**
 * Mix and Match Register Data Store
 *
 * @package  WooCommerce Mix and Match Products/Data
 * @since    1.2.0
 * @version  2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_Data Class.
 *
 * MnM Data filters and includes.
 */
class WC_MNM_Data {

	public static function init() {

		// MNM Child Item Data Store class.
		// @since 2.0.0
		require_once( 'class-wc-mnm-child-item-data-store.php' );

		// Mix and Match product custom post type data store.
		require_once( 'class-wc-product-mix-and-match-data-store-cpt.php' );

		// Register the Mix and Match custom post type data store.
		add_filter( 'woocommerce_data_stores', array( __CLASS__, 'register_mnm_type_data_store' ), 10 );

	}

	/**
	 * Registers the Mix and Match product custom post type data store.
	 *
	 * @param  array  $stores
	 * @return array
	 */
	public static function register_mnm_type_data_store( $stores ) {

		$stores['product-mix-and-match'] = 'WC_Product_MNM_Data_Store_CPT';
		$stores['wc-mnm-child-item'] = 'WC_MNM_Child_Item_Data_Store';

		return $stores;
	}
}

WC_MNM_Data::init();
