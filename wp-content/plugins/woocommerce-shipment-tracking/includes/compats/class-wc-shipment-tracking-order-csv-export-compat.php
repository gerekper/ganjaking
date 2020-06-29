<?php
/**
 * Compat class for Order CSV Export.
 *
 * @package WC_Shipment_Tracking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Shipment Tracking compatibility with Order CSV Export.
 *
 * @since 1.6.2
 */
class WC_Shipment_Tracking_Order_CSV_Export_Compat {

	/**
	 * Constructor
	 *
	 * Set callbacks to WP hooks.
	 */
	public function __construct() {
		// Customer / Order CSV Export column mapper UI.
		add_filter( 'wc_customer_order_csv_export_format_column_data_options', array( $this, 'add_custom_mapping_options' ), 10, 2 );

		// Order CSV Export column headers/data + line item data.
		add_filter( 'wc_customer_order_csv_export_order_headers', array( $this, 'add_tracking_info_to_csv_export_column_headers' ), 15, 2 );
		add_filter( 'wc_customer_order_csv_export_order_row', array( $this, 'add_tracking_info_to_csv_export_column_data' ), 10, 3 );
	}

	/**
	 * Add custom mapping options.
	 *
	 * @param array  $options     Mapping options.
	 * @param string $export_type Export type.
	 *
	 * @return array Mapping options.
	 */
	public function add_custom_mapping_options( $options, $export_type ) {
		if ( 'orders' === $export_type ) {
			$options[] = 'shipment_tracking';
		}
		return $options;
	}

	/**
	 * Adds support for Customer/Order CSV Export by adding appropriate column headers.
	 *
	 * @param array  $headers       Existing array of header key/names for the CSV export.
	 * @param object $csv_generator WC_CSV_Export_Generator instance.
	 *
	 * @return array Column headers.
	 */
	public function add_tracking_info_to_csv_export_column_headers( $headers, $csv_generator ) {
		if ( 'custom' === $csv_generator->export_format ) {
			return $headers;
		}

		$headers['shipment_tracking'] = 'shipment_tracking';
		return $headers;
	}

	/**
	 * Adds support for Customer/Order CSV Export by adding data for the column headers.
	 *
	 * @param array    $order_data    Generated order data matching the column keys in the header.
	 * @param WC_Order $order         Order being exported.
	 * @param object   $csv_generator WC_CSV_Export_Generator instance.
	 *
	 * @return array Column data.
	 */
	public function add_tracking_info_to_csv_export_column_data( $order_data, $order, $csv_generator ) {
		$order_id                     = is_callable( array( $order, 'get_id' ) ) ? $order->get_id() : $order->id;
		$tracking_items               = wc_shipment_tracking()->actions->get_tracking_items( $order_id, true );
		$new_order_data               = array();
		$one_row_per_item             = false;
		$shipment_tracking_csv_output = '';

		if ( count( $tracking_items ) > 0 ) {
			foreach ( $tracking_items as $item ) {
				$pipe = null;
				foreach ( $item as $key => $value ) {
					if ( 'date_shipped' === $key ) {
						$value = date( 'Y-m-d', $value );
					}

					$shipment_tracking_csv_output .= "$pipe$key:$value";

					if ( ! $pipe ) {
						$pipe = '|';
					}
				}

				$shipment_tracking_csv_output .= ';';
			}
		}

		if ( version_compare( wc_customer_order_csv_export()->get_version(), '4.0.0', '<' ) ) {
			$one_row_per_item = ( 'default_one_row_per_item' === $csv_generator->order_format || 'legacy_one_row_per_item' === $csv_generator->order_format );
		} elseif ( isset( $csv_generator->format_definition ) ) {
			$one_row_per_item = 'item' === $csv_generator->format_definition['row_type'];
		}

		if ( $one_row_per_item ) {
			foreach ( $order_data as $data ) {
				$new_order_data[] = array_merge( (array) $data, array( 'shipment_tracking' => $shipment_tracking_csv_output ) );
			}
		} else {
			$new_order_data = array_merge( $order_data, array( 'shipment_tracking' => $shipment_tracking_csv_output ) );
		}

		return $new_order_data;
	}
}
