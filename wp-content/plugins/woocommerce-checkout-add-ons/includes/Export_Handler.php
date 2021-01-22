<?php
/**
 * WooCommerce Checkout Add-Ons
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Checkout Add-Ons to newer
 * versions in the future. If you wish to customize WooCommerce Checkout Add-Ons for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-checkout-add-ons/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Checkout_Add_Ons;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On_Factory;

defined( 'ABSPATH' ) or exit;

/**
 * Checkout Add-Ons Import/Export Handler
 *
 * Adds support for:
 *
 * + Customer / Order CSV Export
 *
 * @since 1.1.0
 */
class Export_Handler {


	/**
	 * Setup class
	 *
	 * @since 1.1.0
	 */
	public function __construct() {

		// Customer / Order / Coupon Export compatibility

		// add column headers/data
		add_filter( 'wc_customer_order_csv_export_order_headers', [ $this, 'add_checkout_add_ons_to_csv_export_column_headers' ], 10, 2 );
		add_filter( 'wc_customer_order_csv_export_order_row',     [ $this, 'add_checkout_add_ons_to_csv_export_column_data' ], 10, 3 );

		// add custom format builder support, v4.0+
		add_filter( 'wc_customer_order_csv_export_format_column_data_options', [ $this, 'add_fields_to_export_orders_custom_mapping_options' ], 10, 2 );

		// add fee item data for XML exports
		add_filter( 'wc_customer_order_export_xml_order_data', [ $this, 'add_checkout_add_ons_to_xml_export_data' ], 10, 3 );

		// Customer / Order XML Export Suite compatibility

		// add fee item data
		add_filter( 'wc_customer_order_xml_export_suite_order_data', [ $this, 'add_checkout_add_ons_to_xml_export_data_legacy' ], 10, 2 );

		// add custom format builder support
		add_filter( 'wc_customer_order_xml_export_suite_format_field_data_options', [ $this, 'add_fields_to_export_orders_custom_mapping_options' ], 10, 2 );
	}


	/**
	 * Adds support for Customer/Order CSV Export by adding a column header for each checkout add-on.
	 *
	 * @since 1.1.0
	 *
	 * @param array $headers existing array of header key/names for the CSV export
	 * @param \WC_Customer_Order_CSV_Export_Generator $csv_generator instance
	 * @return array
	 */
	public function add_checkout_add_ons_to_csv_export_column_headers( $headers, $csv_generator ) {

		$export_format = version_compare( wc_customer_order_csv_export()->get_version(), '4.0.0', '<' ) ? $csv_generator->order_format : $csv_generator->export_format;

		// don't automatically add headers for custom formats
		if ( 'custom' === $export_format ) {

			return $headers;
		}

		foreach ( Add_On_Factory::get_add_ons() as $add_on ) {

			$headers[ "checkout_add_on_{$add_on->get_id()}" ]       = 'checkout_add_on:' . str_replace( '-', '_', sanitize_title( $add_on->get_name() ) ) . '_' . $add_on->get_id();
			$headers[ "checkout_add_on_total_{$add_on->get_id()}" ] = 'checkout_add_on_total:' . str_replace( '-', '_', sanitize_title( $add_on->get_name() ) ) . '_' . $add_on->get_id();
		}

		return $headers;
	}


	/**
	 * Adds support for Customer/Order CSV Export by adding data for each checkout add-on column header.
	 *
	 * @since 1.1.0
	 *
	 * @param array $order_data generated order data matching the column keys in the header
	 * @param \WC_Order $order order being exported
	 * @param \WC_Customer_Order_CSV_Export_Generator $csv_generator instance
	 * @return array
	 */
	public function add_checkout_add_ons_to_csv_export_column_data( $order_data, $order, $csv_generator ) {

		$order_add_ons  = wc_checkout_add_ons()->get_order_add_ons( $order->get_id() );
		$new_order_data = $add_on_data = array();

		foreach ( Add_On_Factory::get_add_ons() as $add_on ) {

			$value = '';
			$total = '';

			if ( isset( $order_add_ons[ $add_on->get_id() ] ) ) {

				switch ( $add_on->get_type() ) {

					case 'file':
						$value = wp_get_attachment_url( $order_add_ons[ $add_on->get_id() ]['value'] );
					break;

					case 'checkbox':
						$value = '1' === $order_add_ons[ $add_on->get_id() ]['value'] ? 'yes' : 'no';
					break;

					case 'textarea':
						$value = $order_add_ons[ $add_on->get_id() ]['value'];
					break;

					default:
						$value = $add_on->normalize_value( $order_add_ons[ $add_on->get_id() ]['normalized_value'], true );
				}

				$total = wc_format_decimal( $order_add_ons[ $add_on->get_id() ]['total'], 2 );
			}

			$add_on_data[ "checkout_add_on_{$add_on->get_id()}" ]       = wp_strip_all_tags( $value );
			$add_on_data[ "checkout_add_on_total_{$add_on->get_id()}" ] = $total;
		}

		$one_row_per_item = $this->is_one_row_per_item( $csv_generator );

		if ( $one_row_per_item ) {

			foreach ( $order_data as $data ) {
				$new_order_data[] = array_merge( (array) $data, $add_on_data );
			}

		} else {

			$new_order_data = array_merge( $order_data, $add_on_data );
		}

		return $new_order_data;
	}


	/**
	 * Determine if the CSV Export format is one row per item or not
	 *
	 * @since 1.11.0
	 *
	 * @param \WC_Customer_Order_CSV_Export_Generator $csv_generator instance
	 * @return bool true if one row per item
	 */
	private function is_one_row_per_item( $csv_generator ) {

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


	/**
	 * Adds support for Customer / Order / Coupon Export XML exports by adding a dedicated <CheckoutAddOns> tag.
	 *
	 * @internal
	 *
	 * @since 2.2.3
	 *
	 * @param array $order_data order data for the XML output
	 * @param \WC_Order $order order object
	 * @param \SkyVerge\WooCommerce\CSV_Export\XML_Export_Generator $generator export generator
	 * @return array
	 */
	public function add_checkout_add_ons_to_xml_export_data( $order_data, $order, $generator = null ) {

		// only add custom order field data to custom formats if set
		if ( $generator && 'custom' === $generator->export_format ) {

			// the data here can use a renamed version of the Checkout Add-Ons data, so we need to get format definition first to find out the new name
			$format_definition = $generator->format_definition;
			$custom_fields_key = isset( $format_definition['fields']['CheckoutAddOns'] ) ? $format_definition['fields']['CheckoutAddOns'] : null;

			if ( $custom_fields_key && isset( $order_data[ $custom_fields_key ] ) ) {

				$order_data[ $custom_fields_key ] = $this->get_checkout_add_ons_formatted( $order );
			}

		} else {

			$order_data['CheckoutAddOns'] = $this->get_checkout_add_ons_formatted( $order );
		}

		return $order_data;
	}


	/**
	 * Adds support for Customer / Order XML Export by adding a dedicated <CheckoutAddOns> tag.
	 *
	 * TODO: remove when XML Export is no longer supported {CW 2019-12-11}
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 * @deprecated 2.2.3
	 *
	 * @param array $order_data order data for the XML output
	 * @param \WC_Order $order order object
	 * @return array updated order data
	 */
	public function add_checkout_add_ons_to_xml_export_data_legacy( $order_data, $order ) {

		// only add custom order field data to custom formats if set in the format builder with v2.0+
		if ( 'custom' === get_option( 'wc_customer_order_xml_export_suite_orders_format', 'default' ) ) {

			// the data here can use a renamed version of the ACOF data, so we need to get format definition first to find out the new name
			$format_definition = wc_customer_order_xml_export_suite()->get_formats_instance()->get_format( 'orders', 'custom' );
			$custom_fields_key = isset( $format_definition['fields']['CheckoutAddOns'] ) ? $format_definition['fields']['CheckoutAddOns'] : null;

			if ( $custom_fields_key && isset( $order_data[ $custom_fields_key ] ) ) {

				$order_data[ $custom_fields_key ] = $this->get_checkout_add_ons_formatted( $order );
			}

		} else {

			$order_data['CheckoutAddOns'] = $this->get_checkout_add_ons_formatted( $order );
		}

		return $order_data;
	}


	/**
	 * Creates array of checkout add-ons data.
	 *
	 * @see xml_to_array() for required format
	 *
	 * @since 1.9.0
	 *
	 * @param \WC_Order $order order object
	 * @return array|null add-ons in array format required by array_to_xml() or null if no add-ons
	 */
	protected function get_checkout_add_ons_formatted( $order ) {

		$add_ons       = [];
		$order_add_ons = wc_checkout_add_ons()->get_order_add_ons( $order->get_id() );

		foreach( Add_On_Factory::get_add_ons() as $id => $add_on ) {

			$add_on_data = [];

			if ( isset( $order_add_ons[ $add_on->get_id() ] ) ) {

				switch( $add_on->get_type() ) {

					case 'file':
						$add_on_value = wp_get_attachment_url( $order_add_ons[ $add_on->get_id() ]['value'] );
					break;

					case 'checkbox':
						$add_on_value = '1' === $order_add_ons[ $add_on->get_id() ]['value'] ? 'yes' : 'no';
					break;

					default:
						$add_on_value = is_array( $order_add_ons[ $add_on->get_id() ]['normalized_value'] ) ? implode( ', ', $order_add_ons[ $add_on->get_id() ]['normalized_value'] ) : $order_add_ons[ $add_on->get_id() ]['normalized_value'];
					break;
				}

				$add_on_data['ID']    = $id;
				$add_on_data['Name']  = $order_add_ons[ $add_on->get_id() ]['name'];
				$add_on_data['Value'] = $add_on_value;
				$add_on_data['Cost']  = wc_format_decimal( $order_add_ons[ $add_on->get_id() ]['total'], 2 );
			}

			/**
			 * Filters the individual add-ons array format.
			 *
			 * @since 1.9.0
			 *
			 * @param array $add_on_data the add-on data for the array_to_xml() format
			 * @param \WC_Order $order
			 * @param array $add_on the raw add-on data for the order
			 */
			$addon_data = (array) apply_filters( 'wc_checkout_add_ons_xml_add_on_data', $add_on_data, $order, $add_on );

			if ( ! empty( $addon_data ) ) {
				$add_ons['CheckoutAddOn'][] = $addon_data;
			}
		}

		return ! empty( $add_ons ) ? $add_ons : null;
	}


	/**
	 * Filters the custom format building options to allow adding Cost of Goods headers.
	 *
	 * @since 1.11.0
	 *
	 * @param string[] $options the custom format building options
	 * @param string $export_type the export type, 'customers' or 'orders'
	 *
	 * @return string[] updated custom format options
	 */
	public function add_fields_to_export_orders_custom_mapping_options( $options, $export_type ) {

		if ( 'orders' === $export_type ) {

			$add_ons = Add_On_Factory::get_add_ons();

			if ( ! empty( $add_ons ) ) {

				$export_options      = current_filter();
				$custom_option_added = false;

				foreach ( $add_ons as $add_on ) {

					if ( 'wc_customer_order_csv_export_format_column_data_options' === $export_options ) {

						$options[] = "checkout_add_on_{$add_on->get_id()}";
						$options[] = "checkout_add_on_total_{$add_on->get_id()}";

					} elseif ( 'wc_customer_order_xml_export_suite_format_field_data_options' === $export_options && ! $custom_option_added ) {

						$options[]           = 'CheckoutAddOns';
						$custom_option_added = true;
					}
				}
			}
		}

		return $options;
	}


}
