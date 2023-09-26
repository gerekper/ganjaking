<?php
/**
 * WC_CSP_Product_Import_Export class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.8.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce core Product Importer/Exporter support.
 *
 * @class    WC_CSP_Product_Import_Export
 * @version  1.8.10
 */
class WC_CSP_Product_Import_Export {

	/**
	 * Hook in.
	 */
	public static function init() {

		// Export product-level restrictions as formatted meta data.
		add_filter( 'woocommerce_product_export_meta_value', array( __CLASS__, 'export_product_level_restrictions' ), 10, 2 );

		// Parse and import product-level restrictions
		add_filter( 'woocommerce_product_importer_parsed_data', array( __CLASS__, 'import_product_level_restrictions' ), 10, 1 );
	}

	/**
	 * Add CSV columns for exporting bundle data.
	 *
	 * @param  string        $meta_value
	 * @param  WC_Meta_Data  $meta
	 * @return string        $meta_value
	 */
	public static function export_product_level_restrictions( $meta_value, $meta ) {

		if ( '_wccsp_restrictions' === $meta->key ){
			$meta_value = json_encode( maybe_unserialize( $meta_value ) );
		}

		return $meta_value;
	}

	/**
	 * Bundle data column content.
	 *
	 * @param  array  $parsed_data
	 * @return array  $parsed_data
	 */
	public static function import_product_level_restrictions( $parsed_data ) {

		if ( empty( $parsed_data[ 'meta_data' ] ) ) {
			return $parsed_data;
		}

		foreach ( $parsed_data[ 'meta_data' ] as $index => $meta_data ) {

			if ( '_wccsp_restrictions' === $meta_data[ 'key' ] ) {

				if ( ! empty( $meta_data[ 'value' ] ) ) {

					$meta_data[ 'value' ]                 = json_decode( $meta_data[ 'value' ], true );
					$parsed_data[ 'meta_data' ][ $index ] = $meta_data;
				}
			}
		}

		return $parsed_data;
	}
}

WC_CSP_Product_Import_Export::init();
