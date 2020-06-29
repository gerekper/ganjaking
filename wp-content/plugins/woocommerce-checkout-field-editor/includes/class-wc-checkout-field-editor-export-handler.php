<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Checkout Field Editor Export Handler
 *
 * Adds support for:
 *
 * + Customer / Order CSV Export
 * + Customer / Order XML Export
 *
 * @since 1.2.5
 */
class WC_Checkout_Field_Editor_Export_Handler {

	/** @var checkout fields */
	private $fields;


	/**
	 * Setup class
	 *
	 * @since 1.2.5
	 */
	public function __construct() {

		$this->fields = $this->set_fields();

		// Customer / Order CSV Export column headers/data
		add_filter( 'wc_customer_order_csv_export_order_headers', array( $this, 'add_fields_to_csv_export_column_headers' ), 10, 2 );
		add_filter( 'wc_customer_order_csv_export_order_row',     array( $this, 'add_fields_to_csv_export_column_data' ), 10, 4 );

		// Customer / Order XML Export fields / data
		if ( function_exists( 'wc_customer_order_xml_export_suite' ) && version_compare( wc_customer_order_xml_export_suite()->get_version(), '2.0.0', '<' ) ) {
			add_filter( 'wc_customer_order_xml_export_suite_order_export_order_list_format', array( $this, 'add_fields_to_xml_export_order_list_format' ), 10, 2 );
		} else {
			add_filter( 'wc_customer_order_xml_export_suite_order_data', array( $this, 'add_fields_to_xml_export_order_list_format' ), 10, 2 );
		}
	}


	/** Customer / Order CSV Export ****************************************/


	/**
	 * Adds support for Customer/Order CSV Export by adding column headers for each field.
	 *
	 * @since 1.2.5
	 * @param array $headers existing array of header key/names for the CSV export
	 * @return array
	 */
	public function add_fields_to_csv_export_column_headers( $headers, $csv_generator ) {

		$field_headers = array();
		$fields        = $this->get_fields();
		$export_format = get_option( 'wc_customer_order_csv_export_orders_format' );

		// only add custom options when using a custom CSV order format
		if ( 'custom' === $export_format ) {

			foreach ( $fields as $name => $options ) {
				if ( $options['custom'] ) {
					$field_headers[ $name ] = $name;
				}
			}

		// add all fields for non-custom exports
		} else {
			foreach ( $fields as $name => $options ) {
				$field_headers[ $name ] = $name;
			}
		}

		return array_merge( $headers, $field_headers );
	}


	/**
	 * Adds support for Customer/Order CSV Export by adding checkout editor field data.
	 *
	 * @since 1.2.5
	 * @param array $order_data generated order data matching the column keys in the header
	 * @param WC_Order $order order being exported
	 * @param \WC_Customer_Order_CSV_Export_Generator $csv_generator instance
	 * @return array
	 */
	public function add_fields_to_csv_export_column_data( $order_data, $order, $csv_generator ) {

		$field_data       = array();
		$new_order_data   = array();
		$one_row_per_item = $this->is_one_row_per_item( $csv_generator );

		foreach ( $this->fields as $name => $options ) {
			$field_data[ $name ] = get_post_meta( version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id(), $name, true );
		}

		if ( $one_row_per_item ) {

			foreach ( $order_data as $data ) {
				$new_order_data[] = array_merge( $field_data, (array) $data );
			}

		} else {

			$new_order_data = array_merge( $field_data, $order_data );
		}

		return $new_order_data;
	}


	/**
	 * Determine if the CSV Export format/format definition are set to export
	 * one row per item.
	 *
	 * @since 1.5.3
	 * @param \WC_Customer_Order_CSV_Export_Generator $csv_generator instance
	 * @return bool
	 */
	private function is_one_row_per_item( $csv_generator ) {

		// sanity check - bail if CSV Export is not active, or if the provided parameter is not as expected
		if ( ! function_exists( 'wc_customer_order_csv_export' ) || ! $csv_generator instanceof WC_Customer_Order_CSV_Export_Generator ) {
			return false;
		}

		$one_row_per_item = false;

		// determine if the selected format is "one row per item"
		if ( version_compare( wc_customer_order_csv_export()->get_version(), '4.0.0', '<' ) ) {

			$one_row_per_item = ( 'default_one_row_per_item' === $csv_generator->order_format || 'legacy_one_row_per_item' === $csv_generator->order_format );

		// v4.0.0 - 4.0.2
		} elseif ( ! isset( $csv_generator->format_definition ) ) {

			// get the CSV Export format definition
			$format_definition = wc_customer_order_csv_export()->get_formats_instance()->get_format( $csv_generator->export_type, $csv_generator->export_format );

			$one_row_per_item = isset( $format_definition['row_type'] ) && 'item' === $format_definition['row_type'];

		// v4.0.3+
		} else {

			$one_row_per_item = 'item' === $csv_generator->format_definition['row_type'];
		}

		return $one_row_per_item;
	}


	/** Customer / Order XML Export ****************************************/


	/**
	 * Adds support for Customer/Order XML Export Suite by adding checkout editor field data.
	 *
	 * @since 1.4.6
	 * @param array $order_format array of order data to be exported
	 * @param WC_Order $order order being exported
	 * @return array modified array of order data to be exported
	 */
	public function add_fields_to_xml_export_order_list_format( $order_format, $order ) {
		$order_id = version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id();
		$export_format = get_option( 'wc_customer_order_xml_export_suite_orders_format', 'legacy' );

		// add a <CheckoutFields> tag for new formats to hold all custom fields
		if ( 'legacy' !== $export_format ) {
			$order_format['CheckoutFields'] = array();
		}

		foreach ( $this->get_fields() as $name => $options ) {

			if ( 'legacy' === $export_format ) {

				// add all fields as tags for backwards compat
				$order_format[ $name ] = get_post_meta( $order_id, $name, true );

			} else {

				// only add custom fields for new formats
				if ( $options['custom'] ) {
					$order_format['CheckoutFields'][ $name ] = get_post_meta( $order_id, $name, true );
				}

			}
		}

		return $order_format;
	}


	/**
	 * Set all registered fields.
	 *
	 * @since 1.2.5
	 * @return array
	 */
	private function set_fields() {

		$fields = array();

		$temp_fields = get_option( 'wc_fields_billing' );

		if ( $temp_fields !== false ) {
			$fields = array_merge( $fields, $temp_fields );
		}

		$temp_fields = get_option( 'wc_fields_shipping' );

		if ( $temp_fields !== false ) {
			$fields = array_merge( $fields, $temp_fields );
		}

		$temp_fields = get_option( 'wc_fields_additional' );

		if ( $temp_fields !== false ) {
			$fields = array_merge( $fields, $temp_fields );
		}

		return $fields;
	}

	/**
	 * Get all registered fields.
	 *
	 * @since 1.4.9
	 * @return array
	 */
	public function get_fields() {
		return $this->fields;
	}

} // end WC_Checkout_Field_Editor_Export_Handler
