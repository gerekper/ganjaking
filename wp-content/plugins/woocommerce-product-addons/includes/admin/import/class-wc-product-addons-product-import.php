<?php
/**
 * WC_Product_Addons_Product_Import class
 *
 * @package  Product Add-Ons
 * @since    5.0.3
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce core Product Importer support.
 *
 * @class    WC_Product_Addons_Product_Import
 * @version  5.0.3
 */
class WC_Product_Addons_Product_Import {

	/**
	 * Hook in.
	 */
	public static function init() {

		// Parse Product Add-Ons.
		add_filter( 'woocommerce_product_importer_parsed_data', array( __CLASS__, 'import_product_addons' ), 10, 2 );
	}

	/**
	 * Parse Product Add-Ons..
	 *
	 * @param  array                    $parsed_data
	 * @param  WC_Product_CSV_Importer  $importer
	 * @return array                    $parsed_data
	 */
	public static function import_product_addons( $parsed_data, $importer ) {

		if ( empty( $parsed_data[ 'meta_data' ] ) ) {
			return $parsed_data;
		}

		foreach ( $parsed_data[ 'meta_data' ] as $meta_data_index => $meta_data ) {
			if ( '_product_addons' === $meta_data[ 'key' ] ) {
				if ( ! empty( $meta_data[ 'value' ] ) ) {
					$meta_data[ 'value' ]                           = json_decode( $meta_data[ 'value' ], true );
					$parsed_data[ 'meta_data' ][ $meta_data_index ] = $meta_data;
				}
			}
		}

		return $parsed_data;
	}
}

WC_Product_Addons_Product_Import::init();
