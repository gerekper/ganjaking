<?php
/**
 * WooCommerce Customer/Order/Coupon Export
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon Export to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon Export for your
 * needs please refer to http://docs.woocommerce.com/document/ordercustomer-csv-exporter/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\CSV_Export;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;
use WC_Customer_Order_CSV_Export;
use WC_Order;

/**
 * Customer/Order XML Export Generator
 *
 * Converts customer/order data into XML
 *
 * @since 5.0.0
 */
class XML_Export_Generator extends Export_Generator {


	/** @var bool whether to indent XML or not */
	public $indent;

	/** @var string XML version */
	public $xml_version;

	/** @var string XML encoding */
	public $xml_encoding;

	/** @var string XML standalone */
	public $xml_standalone;

	/** @var string XML root element */
	public $root_element;

	/** @var \XMLWriter instance */
	private $writer;


	/**
	 * Initializes the generator.
	 *
	 * @since 5.0.0
	 *
	 * @param string $export_type export type, one of `orders` or `customers`
	 * @param array $ids optional. Object IDs associated with the export. Provide for export formats that
	 *                   modify headers based on the objects being exported (such as orders legacy import format)
	 * @param string $format_key export format key
	 */
	public function __construct( $export_type, $ids = null, $format_key = 'default') {

		$this->output_type = WC_Customer_Order_CSV_Export::OUTPUT_TYPE_XML;

		parent::__construct( $export_type, $ids, $format_key );

		/**
		 * Toggles XML file indentation on/off.
		 *
		 * @since 5.0.0
		 *
		 * @param bool $indent
		 * @param XML_Export_Generator $this, generator instance
		 */
		$this->indent = apply_filters( 'wc_customer_order_export_xml_indent', $this->format_definition['indent'], $this );

		/**
		 * Sets the XML version declaration.
		 *
		 * @since 5.0.0
		 *
		 * @param string $xml_version
		 * @param XML_Export_Generator $this, generator instance
		 */
		$this->xml_version = apply_filters( 'wc_customer_order_export_xml_version', $this->format_definition['xml_version'], $this );

		/**
		 * Sets the XML encoding declaration.
		 *
		 * @since 5.0.0
		 *
		 * @param string $xml_encoding
		 * @param XML_Export_Generator $this, generator instance
		 */
		$this->xml_encoding = apply_filters( 'wc_customer_order_export_xml_encoding', $this->format_definition['xml_encoding'], $this );

		/**
		 * Sets the XML standalone declaration.
		 *
		 * @since 5.0.0
		 *
		 * @param string $xml_standalone
		 * @param XML_Export_Generator $this, generator instance
		 */
		$this->xml_standalone = apply_filters( 'wc_customer_order_export_xml_standalone', $this->format_definition['xml_standalone'], $this );

		/**
		 * Sets the XML root element.
		 *
		 * @since 5.0.0
		 *
		 * @param string $root_element defaults to the export type ('orders', 'customers' or 'coupons')
		 * @param XML_Export_Generator $this, generator instance
		 */
		$this->root_element = apply_filters( 'wc_customer_order_export_xml_root_element', ucfirst( $export_type ), $this );

		$this->writer = new \XMLWriter();
	}


	/**
	 * Builds XML for orders in the default format.
	 *
	 * @since 5.0.0
	 *
	 * @param array $ids order IDs to export
	 * @param bool $include_headers unused
	 * @return string
	 */
	public function get_orders_output( $ids, $include_headers = false ) {

		$orders = $this->get_orders( $ids );

		/**
		 * Allows actors to change the XML array format for orders.
		 *
		 * @since 5.0.0
		 *
		 * @param array XML data array
		 * @param array $orders
		 */
		$xml_array = apply_filters( 'wc_customer_order_export_xml_get_orders_xml_data', [ 'Order' => $orders ], $orders );

		/**
		 * Filters the generated orders XML.
		 *
		 * @since 5.0.0
		 *
		 * @param string $xml XML string
		 * @param array $xml_array XML data as array
		 * @param array $orders An array of the order data to write to to the XML
		 * @param array $order_ids The order ids.
		 * @param string $export_format The customer export format.
		 */
		return apply_filters( 'wc_customer_order_export_xml_get_orders_output', $this->get_xml( $xml_array ), $xml_array, $orders, $ids, $this->export_format );
	}


	/**
	 * Creates array of given orders in standard format.
	 *
	 * Filter in get_order_export_xml() allow modification of parent array
	 * Filter in method allows modification of individual order array format
	 *
	 * @since 5.0.0
	 *
	 * @param array $order_ids order IDs to generate array from
	 * @return array orders in array format required by array_to_xml()
	 */
	private function get_orders( $order_ids ) {

		$orders = [];

		// loop through each order
		foreach ( $order_ids as $order_id ) {

			// instantiate WC_Order object
			$order = wc_get_order( $order_id );

			// skip invalid orders
			if ( ! $order instanceof WC_Order ) {
				continue;
			}

			list( $shipping_items, $shipping_methods, $shipping_methods_ids ) = $this->get_shipping_items( $order );
			list( $fee_items, $fee_total, $fee_tax_total )                    = $this->get_fee_items( $order );

			$download_permissions_granted = $order->get_meta( '_download_permissions_granted', true, 'edit' );

			$order_date     = is_callable( [ $order->get_date_created(), 'date' ] ) ? $order->get_date_created()->date( 'Y-m-d H:i:s' ) : null;
			$completed_date = is_callable( [ $order->get_date_completed(), 'date' ] ) ? $order->get_date_completed()->date( 'Y-m-d H:i:s' ) : null;

			$data = [
				'OrderId'                    => $order->get_id(),
				'OrderNumber'                => $order->get_meta( '_order_number', true, 'edit' ),
				'OrderNumberFormatted'       => $order->get_meta( '_order_number_formatted', true, 'edit' ),
				'OrderDate'                  => $this->format_date( $order_date ),
				'OrderStatus'                => $order->get_status(),
				'OrderCurrency'              => $order->get_currency( 'view' ),
				'BillingFirstName'           => $order->get_billing_first_name( 'edit' ),
				'BillingLastName'            => $order->get_billing_last_name( 'edit' ),
				'BillingFullName'            => $order->get_formatted_billing_full_name(),
				'BillingCompany'             => $order->get_billing_company( 'edit' ),
				'BillingAddress1'            => $order->get_billing_address_1( 'edit' ),
				'BillingAddress2'            => $order->get_billing_address_2( 'edit' ),
				'BillingCity'                => $order->get_billing_city( 'edit' ),
				'BillingState'               => $this->get_localized_state( $order->get_billing_country( 'edit' ), $order->get_billing_state( 'edit' ) ),
				'BillingStateCode'           => $order->get_billing_state( 'edit' ),
				'BillingPostcode'            => $order->get_billing_postcode( 'edit' ),
				'BillingCountry'             => $order->get_billing_country( 'edit' ),
				'BillingPhone'               => $order->get_billing_phone( 'edit' ),
				'BillingEmail'               => $order->get_billing_email( 'edit' ),
				'ShippingFirstName'          => $order->get_shipping_first_name( 'edit' ),
				'ShippingLastName'           => $order->get_shipping_last_name( 'edit' ),
				'ShippingFullName'           => $order->get_formatted_shipping_full_name(),
				'ShippingCompany'            => $order->get_shipping_company( 'edit' ),
				'ShippingAddress1'           => $order->get_shipping_address_1( 'edit' ),
				'ShippingAddress2'           => $order->get_shipping_address_2( 'edit' ),
				'ShippingCity'               => $order->get_shipping_city( 'edit' ),
				'ShippingState'              => $this->get_localized_state( $order->get_shipping_country( 'edit' ), $order->get_shipping_state( 'edit' ) ),
				'ShippingStateCode'          => $order->get_shipping_state( 'edit' ),
				'ShippingPostcode'           => $order->get_shipping_postcode( 'edit' ),
				'ShippingCountry'            => $order->get_shipping_country( 'edit' ),
				'ShippingMethodId'           => implode( ',', $shipping_methods_ids ),
				'ShippingMethod'             => implode( ', ', $shipping_methods ),
				'PaymentMethodId'            => $order->get_payment_method( 'edit' ),
				'PaymentMethod'              => $order->get_payment_method_title( 'edit' ),
				'DiscountTotal'              => $order->get_total_discount(),
				'ShippingTotal'              => $this->format_decimal( $order->get_shipping_total() ),
				'ShippingTaxTotal'           => $this->format_decimal( $order->get_shipping_tax(), 2 ),
				'OrderTotal'                 => $this->format_decimal( $order->get_total() ),
				'FeeTotal'                   => $fee_total,
				'FeeTaxTotal'                => $this->format_decimal( $fee_tax_total, 2 ),
				'TaxTotal'                   => $this->format_decimal( $order->get_total_tax(), 2 ),
				'RefundedTotal'              => $order->get_total_refunded(),
				'CompletedDate'              => $this->format_date( $completed_date ),
				'CustomerNote'               => $order->get_customer_note( 'edit' ),
				'CustomerId'                 => $order->get_user_id(),
				'OrderLineItems'             => $this->get_line_items( $order ),
				'FeeItems'                   => $fee_items,
				'ShippingItems'              => $shipping_items,
				'CouponItems'                => $this->get_coupon_items( $order ),
				'TaxItems'                   => $this->get_tax_items( $order ),
				'Refunds'                    => $this->get_refunds( $order ),
				'OrderNotes'                 => $this->get_formatted_order_notes( $order ),
				'DownloadPermissionsGranted' => $download_permissions_granted ? 1 : 0,
			];

			// support custom order numbers beyond Sequential Order Numbers Free / Pro - but since we can't
			// distinguish between the underlying number and the formatted number, only set the formatted number
			if ( ! $data['OrderNumberFormatted'] ) {
				$data['OrderNumberFormatted'] = $order->get_order_number();
			}

			$order_data = [];

			if ( ! empty( $this->format_definition ) && ! empty( $this->format_definition['fields'] ) ) {

				foreach ( $this->format_definition['fields'] as $key => $field ) {
					$order_data[ $field ] = isset( $data[ $key ] ) ? $data[ $key ] : '';
				}

				if ( 'custom' === $this->export_format ) {
					$order_data = $this->get_order_custom_data( $order_data, $order );
				}

			} else {

				$order_data = $data;
			}

			// Adjust output for legacy formats
			if ( 'legacy' === $this->export_format ) {

				// ensure order number output is unchanged
				if ( ! $data['OrderNumber'] ) {
					$order_data['OrderNumber'] = $order->get_order_number();
				}

				// OrderLineItems were not wrapped in OrderLineItem pre 2.0.0
				$order_data['OrderLineItems'] = $order_data['OrderLineItems']['OrderLineItem'];
			}

			if ( is_array( $order_data ) ) {

				$vat_number = $this->get_vat_number( $order );

				// only add tax data to custom formats if set in the format builder
				if ( 'custom' === $this->export_format ) {

					// the data here can use a renamed version of our VAT data, so we need to get format definition first to find out the new name
					$format_definition = $this->format_definition;
					$vat_key           = $format_definition['fields']['VATNumber'] ?? null;

					if ( $vat_key && isset( $data[ $vat_key ] ) ) {
						$order_data[ $vat_key ] = $vat_number;
					}

				// otherwise, automatically add order tax data to the export file
				} else {

					$vat_data = [ 'VATNumber' => $vat_number ];

					if ( isset( $data['BillingPhone'] ) ) {
						$order_data = Framework\SV_WC_Helper::array_insert_after( $order_data, 'BillingPhone', $vat_data );
					} else {
						$order_data = array_merge( $order_data, $vat_data );
					}
				}
			}

			/**
			 * Filters the order data for XML.
			 *
			 * @since 5.0.0
			 *
			 * @param array $order_data order data
			 * @param WC_Order $order order object
			 * @param XML_Export_Generator $generator the generator object
			 */
			$orders[] = apply_filters( 'wc_customer_order_export_xml_order_data', $order_data, $order, $this );
		}

		return $orders;
	}


	/**
	 * Creates array of order shipping items in format required for xml_to_array()
	 *
	 * Filter in method allows modification of individual shipping item array format
	 *
	 * @since 5.0.0
	 *
	 * @param WC_Order $order
	 * @return array - shipping items, methods and method ids. Values will be null if order has no shipping methods.
	 */
	private function get_shipping_items( $order ) {

		$shipping_items = $shipping_methods = $shipping_methods_ids = [];

		foreach ( $order->get_shipping_methods() as $shipping_item_id => $shipping ) {

			$shipping_methods[]     = $shipping['name'];
			$shipping_methods_ids[] = $shipping['method_id'];

			$shipping_item = [
				'Id'         => $shipping_item_id,
				'MethodId'   => $shipping['method_id'],
				'MethodName' => $shipping['name'],
				'Total'      => $this->format_decimal( $shipping['cost'], 2 ),
				'Taxes'      => $this->get_tax_details( $shipping ),
			];

			/**
			 * XML Order Export Shipping Line Item.
			 *
			 * Filter the individual shipping line item entry
			 *
			 * @since 5.0.0
			 *
			 * @param array $shipping_item {
			 *     line item data in key => value format
			 * }
			 *
			 * @param array $shipping WC order shipping item data
			 * @param WC_Order $order the order
			 */
			$shipping_items['ShippingItem'][] = apply_filters( 'wc_customer_order_export_xml_order_shipping_item', $shipping_item, $shipping, $order );
		}

		return [ ( ! empty( $shipping_items ) ? $shipping_items : null ), $shipping_methods, $shipping_methods_ids ];
	}


	/**
	 * Creates array of order line items in format required for xml_to_array()
	 *
	 * Filter in method allows modification of individual line items array format
	 *
	 * @since 5.0.0
	 *
	 * @param object $order
	 * @return array|null - line items in array format required by array_to_xml(), or null if no line items
	 */
	private function get_line_items( $order ) {

		$items = [];

		/** @var \WC_Order_Item_Product $line_items */
		$line_items = $order->get_items( 'line_item' );

		// loop through each item in order
		foreach ( $line_items as $item_id => $item ) {

			// get the product
			/** @var \WC_Product $product */
			$product = $item->get_product();

			// instantiate line item meta
			$meta_data    = $item->get_formatted_meta_data( '_', true );
			$display_meta = [];

			foreach ( $meta_data as $meta ) {
				$display_meta[] = "{$meta->display_key}: {$meta->display_value}";
			}

			$item_meta = implode( ', ', $display_meta );

			// remove all HTML
			$item_meta = wp_strip_all_tags( $item_meta );

			// strip HTML in legacy format - note: in modern formats,
			// SV_WC_Helper::array_to_xml will automatically escape HTML and newlines by wrapping
			// the contents of the tag in CDATA when necessary
			if ( 'legacy' === $this->export_format ) {
				// remove control characters
				$item_meta = str_replace( [ "\r", "\n", "\t" ], '', $item_meta );
			}

			// remove any html entities
			$item_meta = preg_replace( '/\&(?:[a-z,A-Z,0-9]+|#\d+|#x[0-9a-f]+);/', '', $item_meta );

			$item_data = [];

			$item_data['Id']               = $item_id;
			$item_data['Name']             = html_entity_decode( $product ? $product->get_title() : $item['name'], ENT_NOQUOTES, 'UTF-8' );
			$item_data['ProductId']        = $product ? $product->get_id() : '';  // handling for permanently deleted product
			$item_data['SKU']              = $product ? $product->get_sku() : '';  // handling for permanently deleted product
			$item_data['Quantity']         = $item['qty'];
			$item_data['Price']            = $this->format_decimal( $order->get_item_total( $item ), 2 );
			$item_data['Subtotal']         = $this->format_decimal( $order->get_line_subtotal( $item ), 2 );
			$item_data['SubtotalTax']      = $this->format_decimal( $item['line_subtotal_tax'], 2 );
			$item_data['Total']            = $this->format_decimal( $order->get_line_total( $item ), 2 );
			$item_data['TotalTax']         = $this->format_decimal( $order->get_line_tax( $item ), 2 );
			$item_data['Refunded']         = $this->format_decimal( $order->get_total_refunded_for_item( $item ), 2 );
			$item_data['RefundedQuantity'] = $order->get_qty_refunded_for_item( $item_id );

			if ( 'yes' === get_option( 'woocommerce_calc_taxes' ) && 'yes' === get_option( 'woocommerce_prices_include_tax' ) ) {
				$item_data['PriceInclTax'] = $this->format_decimal( $order->get_item_total( $item, true ), 2 );
				$item_data['TotalInclTax'] = $this->format_decimal( $order->get_line_total( $item, true ), 2 );
			}

			$item_data['Meta'] = $item_meta;

			$item_data['Taxes'] = $this->get_tax_details( $item );

			// Keep order items backwards-compatible with legacy version
			if ( 'legacy' === $this->export_format ) {

				// rename fields to be compatible with pre 2.0.0
				$item_data['ItemName']  = $item_data['Name'];
				$item_data['LineTotal'] = $item_data['Total'];

				if ( 'yes' === get_option( 'woocommerce_calc_taxes' ) && 'yes' === get_option( 'woocommerce_prices_include_tax' ) ) {
					$item_data['LineTotalInclTax'] = $item_data['TotalInclTax'];
				}

				// remove data that wasn't present pre 2.0.0
				unset( $item_data['Id'], $item_data['Name'], $item_data['ProductId'], $item_data['Subtotal'], $item_data['SubtotalTax'], $item_data['Total'], $item_data['TotalTax'], $item_data['Refunded'], $item_data['RefundedQuantity'], $item_data['TotalInclTax'], $item_data['Taxes'] );
			}

			/**
			 * Allow actors to modify the line item data / format
			 *
			 * In 2.0.0 renamed from `wc_customer_order_xml_export_suite_order_export_line_item_format`
			 * to `wc_customer_order_xml_export_suite_order_line_item`
			 *
			 * @since 5.0.0
			 *
			 * @param array $item_data
			 * @param WC_Order $order Related order
			 * @param array $item Order line item
			 */
			$items['OrderLineItem'][] = apply_filters( 'wc_customer_order_export_xml_order_line_item', $item_data, $order, $item );
		}

		return ! empty( $items ) ? $items : null;
	}


	/**
	 * Creates array of order fee items in format required for xml_to_array()
	 *
	 * Filter in method allows modification of individual fee item array format
	 *
	 * @since 5.0.0
	 *
	 * @param object $order
	 * @return array - fee items, total and tax total. Values will be null or 0 if order has no fees.
	 */
	private function get_fee_items( $order ) {

		$fee_items = [];
		$fee_total = $fee_tax_total = 0;

		foreach ( $order->get_fees() as $fee_item_id => $fee ) {

			$fee_item = [
				'Id'       => $fee_item_id,
				'Title'    => $fee['name'],
				'TaxClass' => ( ! empty( $fee['tax_class'] ) ) ? $fee['tax_class'] : null,
				'Total'    => $this->format_decimal( $order->get_line_total( $fee ), 2 ),
				'TaxTotal' => $this->format_decimal( $order->get_line_tax( $fee ), 2 ),
				'Taxes'    => $this->get_tax_details( $fee ),
			];

			$fee_item['Taxable'] = null !== $fee_item['TaxClass'];

			/**
			 * XML Export Fee Line Item.
			 *
			 * Filter the individual fee line item entry
			 *
			 * @since 5.0.0
			 *
			 * @param array $fee_item {
			 *     line item data in key => value format
			 * }
			 *
			 * @param array $fee WC order fee item data
			 * @param WC_Order $order the order
			 */
			$fee_items['FeeItem'][] = apply_filters( 'wc_customer_order_export_xml_order_fee_item', $fee_item, $fee, $order );

			$fee_total     += $fee['line_total'];
			$fee_tax_total += $fee['line_tax'];
		}

		return [ ( ! empty( $fee_items ) ? $fee_items : null ), $fee_total, $fee_tax_total ];
	}


	/**
	 * Creates array of order tax items in format required for xml_to_array()
	 *
	 * Filter in method allows modification of individual tax item array format
	 *
	 * @since 5.0.0
	 *
	 * @param object $order
	 * @return array|null - tax items in array format required by array_to_xml(), or null if no taxes
	 */
	private function get_tax_items( $order ) {

		$tax_items = [];

		foreach ( $order->get_tax_totals() as $tax_code => $tax ) {

			$tax_item = [
				'Id'       => $tax->id,
				'RateId'   => $tax->rate_id,
				'Code'     => $tax_code,
				'Title'    => $tax->label,
				'Total'    => $this->format_decimal( $tax->amount, 2 ),
				'Compound' => (bool) $tax->is_compound,
			];

			/**
			 * XML Order Export Tax Line Item.
			 *
			 * Filter the individual tax line item entry
			 *
			 * @since 5.0.0
			 *
			 * @param array $tax_item {
			 *     line item data in key => value format
			 * }
			 *
			 * @param object $tax WC order tax item
			 * @param WC_Order $order the order
			 */
			$tax_items['TaxItem'][] = apply_filters( 'wc_customer_order_export_xml_order_tax_item', $tax_item, $tax, $order );
		}

		return ! empty( $tax_items ) ? $tax_items : null;
	}


	/**
	 * Creates array of order coupons in format required for xml_to_array()
	 *
	 * Filter in method allows modification of individual coupons array format
	 *
	 * @since 5.0.0
	 *
	 * @param WC_Order $order
	 * @return array|null - coupons in array format required by array_to_xml(), or null if no coupons
	 */
	private function get_coupon_items( $order ) {

		$coupon_items = [];

		foreach ( $order->get_items( 'coupon' ) as $coupon_item_id => $coupon ) {

			$_coupon     = new \WC_Coupon( $coupon['name'] );
			$coupon_post = get_post( $_coupon->get_id() );

			$coupon_item = [
				'Id'          => $coupon_item_id,
				'Code'        => $coupon['name'],
				'Amount'      => $this->format_decimal( $coupon['discount_amount'], 2 ),
				'Description' => is_object( $coupon_post ) ? $coupon_post->post_excerpt : '',
			];

			/**
			 * XML Order Export Coupon Line Item.
			 *
			 * Filter the individual coupon line item entry
			 *
			 * @since 5.0.0
			 *
			 * @param array $coupon_item {
			 *     line item data in key => value format
			 *     the keys are for convenience and not necessarily used for exporting. Make
			 *     sure to prefix the values with the desired refund line item entry name
			 * }
			 *
			 * @param array $coupon WC order coupon item
			 * @param WC_Order $order the order
			 */
			$coupon_items['CouponItem'][] = apply_filters( 'wc_customer_order_export_xml_order_coupon_item', $coupon_item, $coupon, $order );
		}

		return ! empty( $coupon_items ) ? $coupon_items : null;
	}


	/**
	 * Creates array of order refunds in format required for xml_to_array()
	 *
	 * Filter in method allows modification of individual refunds array format
	 *
	 * @since 5.0.0
	 *
	 * @param WC_Order $order
	 * @return array|null - refunds in array format required by array_to_xml(), or null if no refunds
	 */
	private function get_refunds( $order ) {

		$refunds = [];

		/** @var \WC_Order_Refund $refund */
		foreach ( $order->get_refunds() as $refund ) {

			$refund_data = [
				'Id'     => $refund->get_id(),
				'Date'   => $this->format_date( $refund->get_date_created()->date( 'Y-m-d H:i:s' ) ),
				'Amount' => $this->format_decimal( $refund->get_amount(), 2 ),
				'Reason' => $refund->get_reason(),
			];

			$refunded_items = [];

			// add line items
			foreach ( $refund->get_items( [ 'line_item', 'fee', 'shipping' ] ) as $item_id => $item ) {

				$refund_amount = abs( isset( $item['line_total'] ) ? $item['line_total'] : ( isset( $item['cost'] ) ? $item['cost'] : null ) );

				// skip empty refund lines
				if ( ! $refund_amount ) {
					continue;
				}

				$refunded_item = [
					'RefundedItemId' => $item['refunded_item_id'],
					'RefundedTotal'  => $refund_amount,
					'RefundedTaxes'  => $this->get_tax_details( $item, 'RefundedTax' ),
				];

				if ( isset( $item['qty'] ) ) {
					$refunded_item['Quantity'] = abs( $item['qty'] );
				}

				$refunded_items[] = $refunded_item;
			}

			$refund_data['RefundedItems'] = ! empty( $refunded_items ) ? [ 'RefundedItem' => $refunded_items ] : null;

			/**
			 * XML Order Export Refund.
			 *
			 * Filter the individual refund entry
			 *
			 * @since 5.0.0
			 *
			 * @param array $refund {
			 *     line item data in key => value format
			 *     the keys are for convenience and not necessarily used for exporting. Make
			 *     sure to prefix the values with the desired refund entry name
			 * }
			 *
			 * @param \WC_Order_Refund $refund WC order refund instance
			 * @param WC_Order $order the order
			 */
			$refunds['Refund'][] = apply_filters( 'wc_customer_order_export_xml_order_refund', $refund_data, $refund, $order );
		}

		return ! empty( $refunds ) ? $refunds : null;
	}


	/**
	 * Gets tax details for an order item.
	 *
	 * @since 5.0.0
	 *
	 * @param \WC_Order_Item $item
	 * @param string $field optional. Tag name to wrap tax details in. Defaults to `Tax`
	 * @return array|null
	 */
	private function get_tax_details( $item, $field = 'Tax' ) {

		if ( empty( $item ) ) {
			return null;
		}

		$taxes = [];

		if ( isset( $item['taxes'] ) ) {

			$taxes = maybe_unserialize( $item['taxes'] );

		} elseif ( isset( $item['line_tax_data'] ) ) {

			$tax_data = maybe_unserialize( $item['line_tax_data'] );
			$taxes    = $tax_data['total'];
		}

		if ( empty( $taxes ) ) {
			return null;
		}

		$tax_details = [];

		foreach ( $taxes as $rate_id => $amount ) {

			// refunds have negative amounts, but we want them - absolutely, positively - we do
			$tax_data = [ 'RateId' => $rate_id, 'Amount' => is_numeric( $amount ) ? abs( $amount ) : 0 ];

			/**
			 * Allows actors to modify the tax details data/format
			 *
			 * @since 5.0.0
			 *
			 * @param array $tax_data
			 * @param array $item related order item (line item, fee item or shipping item)
			 */
			$tax_details[ $field ][] = apply_filters( 'wc_customer_order_export_xml_order_item_tax_data', $tax_data, $item );
		}

		return $tax_details;
	}


	/**
	 * Creates array of order notes in format required for xml_to_array()
	 *
	 * Filter in method allows modification of individual order notes array format
	 *
	 * @since 5.0.0
	 *
	 * @param WC_Order $order
	 * @return array|null - order notes in array format required by array_to_xml() or null if not notes
	 */
	private function get_formatted_order_notes( $order ) {

		$order_notes = $this->get_order_notes( $order );

		$order_note = [];

		if ( ! empty( $order_notes ) ) {

			foreach ( $order_notes as $note ) {

				$note_content = $note->comment_content;

				// strip newlines in legacy format - note: in modern formats,
				// SV_WC_Helper::array_to_xml will automatically escape HTML and newlines by wrapping
				// the contents of the tag in CDATA when necessary
				if ( 'legacy' === $this->export_format ) {
					$note_content = str_replace( [ "\r", "\n" ], ' ', $note_content );
				}

				/**
				 * Filters the format of order notes in the order XML export
				 *
				 * @since 5.0.0
				 *
				 * @param array - the data included for each order note
				 * @param WC_Order $order
				 * @param object $note the order note comment object
				 */
				$order_note['OrderNote'][] = apply_filters( 'wc_customer_order_export_xml_order_note', [
					'Date'    => $this->format_date( $note->comment_date ),
					'Author'  => $note->comment_author,
					'Content' => $note_content,
				], $note, $order );
			}

		}

		return ! empty( $order_note ) ? $order_note : null;
	}


	/**
	 * Function to get an array of order note comment objects
	 *
	 * @since 5.0.0
	 *
	 * @param WC_Order $order
	 * @return array - order notes as array of comment objects
	 */
	private function get_order_notes( $order ) {

		$callback = [ 'WC_Comments', 'exclude_order_comments' ];

		$args = [
			'post_id' => $order->get_id(),
			'approve' => 'approve',
			'type'    => 'order_note',
		];

		remove_filter( 'comments_clauses', $callback );

		$order_notes = get_comments( $args );

		add_filter( 'comments_clauses', $callback );

		return $order_notes;
	}


	/**
	 * Gets meta keys that should be included in the custom export format
	 *
	 * @since 5.0.0
	 *
	 * @param string $export_type
	 * @return array
	 */
	private function get_custom_format_meta_keys( $export_type ) {

		$meta = [];

		// Include all meta
		if ( ! empty( $this->format_definition ) && ! empty ( $this->format_definition['include_all_meta'] ) ) {

			$all_meta = wc_customer_order_csv_export()->get_formats_instance()->get_all_meta_keys( $export_type, $this->output_type );

			if ( ! empty( $all_meta ) ) {

				foreach ( $all_meta as $meta_key ) {

					$meta[ $meta_key ] = $meta_key;
				}
			}

		// Include some meta only, if defined
		} elseif ( ! empty( $this->format_definition ) ) {

			$column_mapping = (array) $this->format_definition['mapping'];

			foreach ( $column_mapping as $column ) {

				if ( isset( $column['source'] ) && 'meta' === $column['source'] ) {
					$meta[ $column['meta_key'] ] = $column['name'];
				}
			}

		}

		return $meta;
	}


	/**
	 * Returns static columns that should be included in the custom export format.
	 *
	 * @since 5.0.0
	 *
	 * @param string $export_type
	 * @return array
	 */
	private function get_custom_format_static_fields( $export_type ) {

		$statics = [];

		$column_mapping = ! empty( $this->format_definition ) ? (array) $this->format_definition['mapping'] : [];

		foreach ( $column_mapping as $column ) {

			if ( isset( $column['source'] ) && 'static' === $column['source'] ) {
				$statics[ $column['name'] ] = $column['static_value'];
			}
		}

		return $statics;
	}


	/**
	 * Gets order data for the custom format.
	 *
	 * @since 5.0.0
	 *
	 * @param array $order_data an array of order data for the given order
	 * @param WC_Order $order the WC_Order object
	 * @return array modified order data
	 */
	private function get_order_custom_data( array $order_data, WC_Order $order ) : array {

		$meta = $this->get_custom_format_meta_keys( 'orders' );

		// Fetch meta
		if ( ! empty( $meta ) ) {

			foreach ( $meta as $meta_key => $column_name ) {

				$order_data[ $column_name ] = maybe_serialize( $order->get_meta( $meta_key ) );

				$this->add_meta_key_index_prefix( $order_data, $column_name );
			}
		}

		return array_merge( $order_data, $this->get_custom_format_static_fields( 'orders' ) );
	}


	/**
	 * Gets customer data for the custom format.
	 *
	 * @since 5.0.0
	 *
	 * @param array $customer_data an array of customer data for the given customer
	 * @param \WP_User $user the WP_User user
	 * @return array modified customer data
	 */
	private function get_customer_custom_data( $customer_data, $user ) {

		$meta = $this->get_custom_format_meta_keys( 'customers' );

		// Fetch meta
		if ( ! empty( $meta ) && $user instanceof \WP_User ) {

			foreach ( $meta as $meta_key => $column_name ) {

				$customer_data[ $column_name ] = maybe_serialize( get_user_meta( $user->ID, $meta_key, true ) );

				$this->add_meta_key_index_prefix( $customer_data, $column_name );
			}
		}

		$customer_data = array_merge( $customer_data, $this->get_custom_format_static_fields( 'customers' ) );

		return $customer_data;
	}


	/**
	 * Gets coupon data for the custom format.
	 *
	 * @since 2.5.0
	 *
	 * @param array $coupon_data an array of coupon data for the given coupon
	 * @param \WC_Coupon $coupon the WC_Coupon object
	 * @return array modified coupon data
	 */
	private function get_coupon_custom_data( $coupon_data, $coupon ) {

		$meta = $this->get_custom_format_meta_keys( 'coupons' );

		// fetch meta
		if ( ! empty( $meta ) ) {

			foreach ( $meta as $meta_key => $column_name ) {

				$coupon_data[ $column_name ] = maybe_serialize( get_post_meta( $coupon->get_id(), $meta_key, true ) );

				$this->add_meta_key_index_prefix( $coupon_data, $column_name );
			}
		}

		$coupon_data = array_merge( $coupon_data, $this->get_custom_format_static_fields( 'coupons' ) );

		return $coupon_data;
	}


	/**
	 * Gets the XML for customers.
	 *
	 * @since 5.0.0
	 * @param array $ids customer IDs to export. also accepts an array of arrays with billing email and
	 *                   order Ids, for guest customers: array( $user_id, array( $billing_email, $order_id ) )
	 * @param bool $include_headers optional. Whether to include CSV column headers in the output or not. Defaults to false
	 * @return string XML data
	 */
	public function get_customers_output( $ids, $include_headers = false ) {

		$customers = $this->get_customers( $ids );

		/**
		 * Allows actors to change the XML array format for customers.
		 *
		 * @since 5.0.0
		 *
		 * @param array XML data array
		 * @param array $customers
		 */
		$xml_array = apply_filters( 'wc_customer_order_export_xml_customers_xml_data', [ 'Customer' => $customers ], $customers );

		/**
		 * Filters the generated customers XML.
		 *
		 * @since 5.0.0
		 *
		 * @param string $xml XML string
		 * @param array $xml_array XML data as array
		 * @param array $customers An array of the customers data to write to to the XML
		 * @param array $customer_id The customer ids.
		 * @param string $export_format The customer export format.
		 */
		return apply_filters( 'wc_customer_order_export_xml_customers_xml', $this->get_xml( $xml_array ), $xml_array, $customers, $ids, $this->export_format );
	}


	/**
	 * Gets the customer data.
	 *
	 * @since 5.0.0
	 *
	 * @param array $ids customer IDs to export. also accepts an array of arrays with billing email and
	 *                   order Ids, for guest customers: array( $user_id, array( $billing_email, $order_id ) )
	 * @return array customer data in the format key => content
	 */
	private function get_customers( $ids ) {

		$customers = [];

		foreach ( $ids as $customer_id ) {

			$order_id = null;

			if ( is_array( $customer_id ) ) {
				list( $customer_id, $order_id ) = $customer_id;
			}

			$user = is_numeric( $customer_id ) ? get_user_by( 'id', $customer_id ) : get_user_by( 'email', $customer_id );

			// guest, get info from order
			if ( ! $user && is_numeric( $order_id ) ) {

				$order = wc_get_order( $order_id );
				$user  = $this->get_user_from_order( $order );
			}

			// user not found, skip - this can occur when an invalid customer id or email was passed in
			if ( ! $user ) {
				continue;
			}

			$data = [
				'CustomerId'        => $user->ID,
				'FirstName'         => $user->first_name,
				'LastName'          => $user->last_name,
				'Username'          => $user->user_login,
				'Email'             => $user->user_email,
				'Password'          => $user->user_pass,
				'DateRegistered'    => $this->format_date( $user->user_registered ),
				'BillingFirstName'  => $user->billing_first_name,
				'BillingLastName'   => $user->billing_last_name,
				/* translators: Placeholders: %1$s - first name; %2$s - last name / surname */
				'BillingFullName'   => ! empty( $user->billing_full_name ) ? $user->billing_full_name : sprintf( _x( '%1$s %2$s', 'full name', 'woocommerce-customer-order-xml-export-suite' ), $user->billing_first_name, $user->billing_last_name ),
				'BillingCompany'    => $user->billing_company,
				'BillingEmail'      => $user->billing_email,
				'BillingPhone'      => $user->billing_phone,
				'BillingAddress1'   => $user->billing_address_1,
				'BillingAddress2'   => $user->billing_address_2,
				'BillingPostcode'   => $user->billing_postcode,
				'BillingCity'       => $user->billing_city,
				'BillingState'      => $this->get_localized_state( $user->billing_country, $user->billing_state ),
				'BillingStateCode'  => $user->billing_state,
				'BillingCountry'    => $user->billing_country,
				'ShippingFirstName' => $user->shipping_first_name,
				'ShippingLastName'  => $user->shipping_last_name,
				/* translators: Placeholders: %1$s - first name; %2$s - last name / surname */
				'ShippingFullName'  => ! empty( $user->shipping_full_name ) ? $user->shipping_full_name : sprintf( _x( '%1$s %2$s', 'full name', 'woocommerce-customer-order-xml-export-suite' ), $user->shipping_first_name, $user->shipping_last_name ),
				'ShippingCompany'   => $user->shipping_company,
				'ShippingAddress1'  => $user->shipping_address_1,
				'ShippingAddress2'  => $user->shipping_address_2,
				'ShippingPostcode'  => $user->shipping_postcode,
				'ShippingCity'      => $user->shipping_city,
				'ShippingState'     => $this->get_localized_state( $user->shipping_country, $user->shipping_state ),
				'ShippingStateCode' => $user->shipping_state,
				'ShippingCountry'   => $user->shipping_country,
				'TotalSpent'        => $this->format_decimal( wc_get_customer_total_spent( $user->ID ), 2 ),
				'OrderCount'        => wc_get_customer_order_count( $user->ID ),
			];

			if ( ! empty( $this->format_definition ) && ! empty( $this->format_definition['fields'] ) ) {

				foreach ( $this->format_definition['fields'] as $key => $field ) {
					$customer_data[ $field ] = isset( $data[ $key ] ) ? $data[ $key ] : '';
				}

				if ( 'custom' === $this->export_format ) {
					$customer_data = $this->get_customer_custom_data( $customer_data, $user );
				}

			} else {
				$customer_data = $data;
			}

			/**
			 * XML Export Customer Data
			 *
			 * Filters the individual customer data
			 *
			 * @since 5.0.0
			 *
			 * @param array $customer_data
			 * @param \WP_User $user WP User object
			 * @param int|null $order_id an order ID for the customer. Null if registered customer.
			 * @param XML_Export_Generator $this, generator instance
			 */
			$customers[] = apply_filters( 'wc_customer_order_export_xml_customer_export_data', $customer_data, $user, $order_id, $this );
		}

		return $customers;
	}


	/**
	 * Gets the XML for coupons.
	 *
	 * @since 5.0.0
	 *
	 * @param array $ids coupon IDs to export
	 * @param bool $include_headers optional. Whether to include CSV column headers in the output or not. Defaults to false
	 *
	 * @return string XML data
	 */
	public function get_coupons_output( $ids, $include_headers = false ) {

		$coupons = $this->get_coupons( $ids );

		/**
		 * Allows actors to change the XML array format for coupons.
		 *
		 * @since 5.0.0
		 *
		 * @param array XML data array
		 * @param array $coupons
		 */
		$xml_array = apply_filters( 'wc_customer_order_export_xml_coupons_xml_data', [ 'Coupon' => $coupons ], $coupons );

		/**
		 * Filters the generated coupons XML.
		 *
		 * @since 5.0.0
		 *
		 * @param string $xml XML string
		 * @param array $xml_array XML data as array
		 * @param array $coupons An array of the customers data to write to to the XML
		 * @param array $coupon_id The coupon IDs
		 * @param string $export_format The coupon export format
		 */
		return apply_filters( 'wc_customer_order_export_xml_coupons_xml', $this->get_xml( $xml_array ), $xml_array, $coupons, $ids, $this->export_format );
	}


	/**
	 * Gets the coupon data.
	 *
	 * @since 5.0.0
	 *
	 * @param array $ids coupon IDs to export
	 *
	 * @return array coupon data in the format key => content
	 */
	private function get_coupons( $ids ) {

		$coupons = [];

		foreach ( $ids as $coupon_id ) {

			// load the coupon
			$coupon = new \WC_Coupon( $coupon_id );

			// skip if the coupon can't be loaded
			if ( ! $coupon ) {
				continue;
			}

			// get this coupon's included and excluded products
			$product_ids          = $coupon->get_product_ids();
			$excluded_product_ids = $coupon->get_excluded_product_ids();

			// create array of product SKUs allowed by this coupon
			$products = $this->get_allowed_products_skus( $coupon );

			// create array of product SKUs excluded by this coupon
			$excluded_products = $this->get_excluded_products_skus( $coupon );

			// create array of product category names allowed by this coupon
			$product_categories = $this->get_allowed_product_category_names( $coupon );

			// create array of product category names excluded by this coupon
			$excluded_product_categories = $this->get_excluded_product_category_names( $coupon );

			$expiry_date           = $coupon->get_date_expires();
			$formatted_expiry_date = ( ! empty( $expiry_date ) ) ? $this->format_date( $expiry_date->date( 'Y-m-d' ) ) : '';

			// create array of email addresses of customers who have used this coupon
			$used_by_customer_emails = [];

			$used_by = $coupon->get_used_by();

			foreach ( $used_by as $user_id ) {

				// this value may be a user ID or an email address
				if ( is_email( $user_id ) ) {

					$used_by_customer_emails['User'][] = [ 'Email' => $user_id ];
				} elseif ( $user = get_user_by( 'id', $user_id ) ) {

					$user = get_user_by( 'id', $user_id );
					$used_by_customer_emails['User'][] = [ 'Email' => $user->user_email ];
				}
			}

			$data = [
				'Code'                     => $coupon->get_code(),
				'Type'                     => $coupon->get_discount_type(),
				'Description'              => $coupon->get_description(),
				'Amount'                   => $coupon->get_amount(),
				'ExpiryDate'               => $formatted_expiry_date,
				'EnableFreeShipping'       => $coupon->get_free_shipping() ? 'yes' : 'no',
				'MinimumAmount'            => $this->format_decimal( $coupon->get_minimum_amount() ),
				'MaximumAmount'            => $this->format_decimal( $coupon->get_maximum_amount() ),
				'IndividualUse'            => $coupon->get_individual_use() ? 'yes' : 'no',
				'ExcludeSaleItems'         => $coupon->get_exclude_sale_items() ? 'yes' : 'no',
				'Products'                 => implode( ', ', $products ),
				'ExcludeProducts'          => implode( ', ', $excluded_products ),
				'ProductCategories'        => implode( ', ', $product_categories ),
				'ExcludeProductCategories' => implode( ', ', $excluded_product_categories ),
				'CustomerEmails'           => implode( ', ', $coupon->get_email_restrictions() ),
				'UsageLimit'               => $coupon->get_usage_limit(),
				'LimitUsageToXItems'       => $coupon->get_limit_usage_to_x_items(),
				'UsageLimitPerUser'        => $coupon->get_usage_limit_per_user(),
				'UsageCount'               => $coupon->get_usage_count(),
				'ProductIDs'               => implode( ', ', $product_ids ),
				'ExcludeProductIDs'        => implode( ', ', $excluded_product_ids ),
				'UsedBy'                   => $used_by_customer_emails,
			];

			if ( ! empty( $this->format_definition ) && ! empty( $this->format_definition['fields'] ) ) {

				foreach ( $this->format_definition['fields'] as $key => $field ) {
					$coupon_data[ $field ] = isset( $data[ $key ] ) ? $data[ $key ] : '';
				}

				if ( 'custom' === $this->export_format ) {
					$coupon_data = $this->get_coupon_custom_data( $coupon_data, $coupon );
				}

			} else {
				$coupon_data = $data;
			}

			/**
			 * XML Export Coupon Data.
			 *
			 * Filters the individual coupon data
			 *
			 * @since 5.0.0
			 *
			 * @param array $coupon_data
			 * @param XML_Export_Generator $this, generator instance
			 */
			$coupons[] = apply_filters( 'wc_customer_order_export_xml_coupon_export_data', $coupon_data, $this );
		}

		return $coupons;
	}


	/**
	 * Gets the XML output for an array.
	 *
	 * @since 5.0.0
	 *
	 * @param array $xml_array
	 * @return string
	 */
	private function get_xml( $xml_array ) {

		$this->writer->openMemory();
		$this->writer->setIndent( $this->indent );

		// generate xml starting with the root element and recursively generating child elements
		Framework\SV_WC_Helper::array_to_xml( $this->writer, $this->root_element, $xml_array );

		$replace = [
			"<{$this->root_element}>\r\n",
			"<{$this->root_element}>\r",
			"<{$this->root_element}>\n",
			"<{$this->root_element}>",
			"</{$this->root_element}>\r\n",
			"</{$this->root_element}>\r",
			"</{$this->root_element}>\n",
			"</{$this->root_element}>",
		];

		return str_replace( $replace, '', $this->writer->outputMemory() );
	}


	/**
	 * Gets XML headers.
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	public function get_header() {

		$this->writer->openMemory();
		$this->writer->setIndent( $this->indent );

		$this->writer->startDocument( $this->xml_version, $this->xml_encoding, $this->xml_standalone );

		$this->writer->text( "<$this->root_element>" . ( $this->indent ? "\n" : "" ) );

		/**
		 * Allows actors to modify XML header.
		 *
		 * @since 5.0.0
		 *
		 * @param string $header
		 */
		$header = apply_filters( 'wc_customer_order_export_xml_' . $this->export_type . '_header', $this->writer->outputMemory() );

		return $header;
	}


	/**
	 * Gets XML footer.
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	public function get_footer() {

		$this->writer->openMemory();
		$this->writer->setIndent( $this->indent );

		$this->writer->text( "</$this->root_element>" . ( $this->indent ? "\n" : "" ) );

		$this->writer->endDocument();

		/**
		 * Allows actors to modify XML footer.
		 *
		 * @since 5.0.0
		 * @param string $footer
		 */
		$footer = apply_filters( 'wc_customer_order_export_xml_' . $this->export_type . '_footer', $this->writer->outputMemory() );

		return $footer;
	}


	/**
	 * Adds a prefix to a meta column name.
	 *
	 * This method performs an array manipulation to simulate a key rename action.
	 *
	 * @since 5.2.0
	 *
	 * @param array $order_data order data to be exported
	 * @param $meta_column_name meta column name to be renamed
	 */
	private function add_meta_key_index_prefix( &$order_data, $meta_column_name ) {

		// extracts all column names
		$keys = array_keys( $order_data );

		// replaces the meta column name
		$keys[ array_search( $meta_column_name, $keys, false ) ] = "Meta-{$meta_column_name}";

		// combines the keys and data to have an array with a renamed column
		$order_data = array_combine( $keys, $order_data );
	}


	/** Deprecated methods ********************************************************************************************/


	/**
	 * Build XML for orders in the default format.
	 *
	 * In 2.0.0 added $ids param
	 *
	 * @since 1.0.0
	 * @deprecated 5.0.0
	 *
	 * @param array $ids order IDs to export
	 * @return string
	 */
	public function get_orders_xml( $ids ) {

		wc_deprecated_function( __METHOD__, '5.0.0', __CLASS__ . '::get_orders_output( $ids )' );

		return $this->get_orders_output( $ids );
	}


	/**
	 * Get the XML for customers
	 *
	 * In 2.0.0 added $ids param
	 *
	 * @since 1.1.0
	 * @param array $ids customer IDs to export. also accepts an array of arrays with billing email and
	 *                   order Ids, for guest customers: array( $user_id, array( $billing_email, $order_id ) )
	 * @return string XML data
	 */
	public function get_customers_xml( $ids ) {

		wc_deprecated_function( __METHOD__, '5.0.0', __CLASS__ . '::get_customers_output( $ids )' );

		return $this->get_customers_output( $ids );
	}


	/**
	 * Gets the XML for coupons.
	 *
	 * @since 2.5.0
	 * @deprecated 5.0.0
	 *
	 * @param array $ids coupon IDs to export
	 *
	 * @return string XML data
	 */
	public function get_coupons_xml( $ids ) {

		wc_deprecated_function( __METHOD__, '5.0.0', __CLASS__ . '::get_coupons_output( $ids )' );

		return $this->get_coupons_output( $ids );
	}


}
