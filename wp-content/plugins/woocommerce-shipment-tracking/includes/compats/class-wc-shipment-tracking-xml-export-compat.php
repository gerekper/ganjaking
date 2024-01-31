<?php
/**
 * WC_Shipment_Tracking_XML_Export_Compat file.
 *
 * @package WC_Shipment_Tracking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Shipment Tracking compatibility with Customer / Order XML Export
 *
 * @since 1.7.0
 */
class WC_Shipment_Tracking_XML_Export_Compat {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'wc_customer_order_xml_export_suite_order_data', array( $this, 'add_fields_to_xml_export_order_format' ), 10, 2 );
	}

	/**
	 * Adds fields to the order XML Export for shipment tracking information.
	 *
	 * @param array    $format Fields in the order XML output.
	 * @param WC_Order $order The order object being exported.
	 *
	 * @return array - the updated fields
	 */
	public function add_fields_to_xml_export_order_format( $format, $order ) {
		$order_id                   = is_callable( array( $order, 'get_id' ) ) ? $order->get_id() : $order->id;
		$tracking_items             = wc_shipment_tracking()->actions->get_tracking_items( $order_id, true );
		$format['ShipmentTracking'] = array();

		// Bail if we have no tracking items.
		if ( 0 === count( $tracking_items ) ) {
			return $format;
		}

		foreach ( $tracking_items as $key => $values ) {

			// Format timestamps for humans.
			$values['date_shipped'] = gmdate( 'Y-m-d', $values['date_shipped'] );

			// Add the values for each tracking item into a <Package> tag.
			$format['ShipmentTracking']['Package'][] = $values;
		}

		return $format;
	}
}
