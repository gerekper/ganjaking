<?php
/**
 * WCS_ATT_Product_Export class
 *
 * @package  All Products for WooCommerce Subscriptions
 * @since    2.2.5
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce core Product Exporter support.
 *
 * @class    WCS_ATT_Product_Export
 * @version  4.0.0
 */
class WCS_ATT_Product_Export {

	/**
	 * Hook in.
	 */
	public static function init() {

		// Export Subscription schemes.
		add_filter( 'woocommerce_product_export_meta_value', array( __CLASS__, 'export_subscription_schemes' ), 10, 4 );
	}

	/**
	 * Export Subscription schemes.
	 *
	 * @param  mixed         $meta_value
	 * @param  WC_Meta_Data  $meta
	 * @param  WC_Product    $product
	 * @param  array         $row
	 * @return string        $meta_value
	 */
	public static function export_subscription_schemes( $meta_value, $meta, $product, $row ) {

		if ( '_wcsatt_schemes' === $meta->key ) {
			if ( ! empty( $meta_value ) ) {
				$meta_value = json_encode( maybe_unserialize( $meta_value ) );
			}
		}

		return $meta_value;
	}
}

WCS_ATT_Product_Export::init();
