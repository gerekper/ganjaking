<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Shipment Tracking compatibility with Customer / Coupon / Order CSV Import
 *
 * @since 1.6.2
 */
class WC_Shipment_Tracking_Order_CSV_Import_Compat {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Add Shipment Tracking as a recognized mapping option to the import column mapper
		add_action( 'wc_csv_import_suite_column_mapping_options', array( $this, 'add_column_mapping_options' ), 10, 2 );

		// Parse Shipment Tracking data from the import to set it properly for an order
		add_filter( 'wc_csv_import_suite_parsed_order_data', array( $this, 'add_parsed_order_data' ), 10, 2 );
		add_action( 'wc_csv_import_suite_update_order_data', array( $this, 'save_shipment_tracking_order_data' ), 10, 2 );
	}


	/**
	 * Add Shipment Tracking as a recognized import column
	 *
	 * @param array $options associative array of options for import mapping
	 * @param string $importer Importer type
	 * @return array updated options
	 */
	public function add_column_mapping_options( $options, $importer ) {
		if ( 'woocommerce_order_csv' === $importer ) {
			$shipment_tracking_group = __( 'Order data', 'woocommerce-shipment-tracking' );

			$new_options = array(
				'shipment_tracking' => __( 'Shipment Tracking', 'woocommerce-shipment-tracking' ),
			);

			$options[ $shipment_tracking_group ] = $options[ $shipment_tracking_group ] + $new_options;
		}

		return $options;
	}


	/**
	 * Add Shipment Tracking data to the parsed raw order data in CSV Import Suite.
	 *
	 * @since 1.6.2
	 * @param array $order_data Parsed order data
	 * @param array $item raw order data
	 * @return array
	 */
	public function add_parsed_order_data( $order_data, $item ) {
		$value         = ! empty( $item['shipment_tracking'] ) ? $item['shipment_tracking'] : '';
		$tracking_data = array();

		if ( ! empty( $value ) ) {
			// let's get an array of packages first
			$packages_raw  = explode( ';', $value );

			// format each package as an array of data
			foreach ( $packages_raw as $package_raw ) {
				if ( empty( $package_raw ) ) {
					continue;
				}

				$package_data = array();
				$package      = explode( '|', $package_raw );

				// now give us an associative array of 'tracking_key' => 'value' for the package
				// we don't use list() as we want to leverage the explode() 'limit' param
				// since tracking URLs can have : chars within the value
				foreach ( $package as $tracking_value ) {

					$tracking                     = explode( ':', $tracking_value, 2 );
					$package_data[ $tracking[0] ] = $tracking[1];
				}

				// use a timestamp, Shipment Tracking expects one
				$package_data['date_shipped'] = strtotime( $package_data['date_shipped'] );
				$tracking_data[]              = $package_data;
			}
		}

		$order_data['shipment_tracking'] = $tracking_data;
		return $order_data;
	}

	/**
	 * Update Shipment Tracking data when orders are imported by CSV Import Suite.
	 *
	 * @since 1.6.2
	 * @param int $id Order ID
	 * @param array $data Order data
	 */
	public function save_shipment_tracking_order_data( $order_id, $order_data ) {
		$order = wc_get_order( $order_id );
		$order->update_meta_data( '_wc_shipment_tracking_items', $order_data['shipment_tracking'] );
		$order->save_meta_data();
	}

}
