<?php
/**
 * WC_Product_Addons_Product_Export class
 *
 * @package  Product Add-Ons
 * @since    5.0.3
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce core Product Exporter support.
 *
 * @class    WC_Product_Addons_Product_Export
 * @version  5.0.3
 */
class WC_Product_Addons_Product_Export {

	/**
	 * Hook in.
	 */
	public static function init() {

		// Export Product Add-Ons.
		add_filter( 'woocommerce_product_export_meta_value', array( __CLASS__, 'export_product_addons' ), 10, 4 );
	}

	/**
	 * Export Product Add-Ons..
	 *
	 * @param  mixed         $meta_value
	 * @param  WC_Meta_Data  $meta
	 * @param  WC_Product    $product
	 * @param  array         $row
	 * @return string        $meta_value
	 */
	public static function export_product_addons( $meta_value, $meta, $product, $row ) {

		if ( '_product_addons' === $meta->key ) {
			if ( ! empty( $meta_value ) ) {
				$meta_value = json_encode( maybe_unserialize( $meta_value ) );
			}
		}

		return $meta_value;
	}
}

WC_Product_Addons_Product_Export::init();
