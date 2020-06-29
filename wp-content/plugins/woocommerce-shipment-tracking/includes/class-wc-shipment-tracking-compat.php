<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Shipment Tracking compats handler.
 *
 * @since 1.6.0
 */
class WC_Shipment_Tracking_Compat {

	/**
	 * Load compat classes and instantiate it.
	 */
	public function load_compats() {
		// Load built-in compat classes.
		require_once( 'compats/class-wc-shipment-tracking-pip-compat.php' );
		require_once( 'compats/class-wc-shipment-tracking-order-xml-export-compat.php' );
		require_once( 'compats/class-wc-shipment-tracking-order-csv-import-compat.php' );
		require_once( 'compats/class-wc-shipment-tracking-order-csv-export-compat.php' );

		$compats = array(
			'WC_Shipment_Tracking_PIP_Compat',
			'WC_Shipment_Tracking_XML_Export_Compat',
			'WC_Shipment_Tracking_Order_CSV_Import_Compat',
			'WC_Shipment_Tracking_Order_CSV_Export_Compat',
		);

		/**
		 * Filters the shipment tracking compats.
		 *
		 * @since 1.6.0
		 *
		 * @param array $compats List of class names that provide compatibilities
		 *                       with WooCommerce Shipment Tracking
		 */
		$compats = apply_filters( 'wc_shipment_tracking_compats', $compats );

		foreach ( $compats as $compat ) {
			if ( class_exists( $compat ) ) {
				new $compat();
			}
		}
	}
}
