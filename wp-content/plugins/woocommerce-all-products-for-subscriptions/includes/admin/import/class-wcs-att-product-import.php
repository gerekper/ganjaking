<?php
/**
 * WCS_ATT_Product_Import class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  All Products for WooCommerce Subscriptions
 * @since    2.2.5
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce core Product Importer support.
 *
 * @class    WCS_ATT_Product_Import
 * @version  2.2.5
 */
class WCS_ATT_Product_Import {

	/**
	 * Hook in.
	 */
	public static function init() {

		// Parse Subscription schemes.
		add_filter( 'woocommerce_product_importer_parsed_data', array( __CLASS__, 'parse_subscription_schemes' ), 10, 2 );
	}

	/**
	 * Parse Subscription schemes.
	 *
	 * @param  array                    $parsed_data
	 * @param  WC_Product_CSV_Importer  $importer
	 * @return array                    $parsed_data
	 */
	public static function parse_subscription_schemes( $parsed_data, $importer ) {

		foreach ( $parsed_data[ 'meta_data' ] as $meta_data_index => $meta_data ) {
			if ( '_wcsatt_schemes' === $meta_data[ 'key' ] ) {
				if ( ! empty( $meta_data[ 'value' ] ) ) {
					$meta_data[ 'value' ]                           = maybe_unserialize( $meta_data[ 'value' ] );
					$parsed_data[ 'meta_data' ][ $meta_data_index ] = $meta_data;
				}
			}
		}

		return $parsed_data;
	}
}

WCS_ATT_Product_Import::init();
