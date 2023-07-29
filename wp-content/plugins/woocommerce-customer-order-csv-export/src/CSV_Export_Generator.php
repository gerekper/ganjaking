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

/**
 * Customer/Order CSV Export Generator
 *
 * Converts customer/order data into CSV
 *
 * @since 5.0.0
 */
class CSV_Export_Generator extends Export_Generator {


	/** @var string CSV delimiter */
	public $delimiter;

	/** @var string CSV enclosure */
	public $enclosure;


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
	public function __construct( $export_type, $ids = null, $format_key = 'default' ) {

		$this->output_type = WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV;

		parent::__construct( $export_type, $ids, $format_key );

		/**
		 * CSV Delimiter.
		 *
		 * Filter the delimiter used for the CSV file
		 *
		 * @since 5.0.0
		 *
		 * @param string $delimiter, defaults to comma (,)
		 * @param CSV_Export_Generator $this, generator instance
		 */
		$this->delimiter = apply_filters( 'wc_customer_order_export_csv_delimiter', $this->format_definition['delimiter'], $this );

		/**
		 * CSV Enclosure.
		 *
		 * Filter the enclosure used for the CSV file
		 *
		 * @since 5.0.0
		 *
		 * @param string $enclosure, defaults to double quote (")
		 * @param CSV_Export_Generator $this, generator instance
		 */
		$this->enclosure = apply_filters( 'wc_customer_order_export_csv_enclosure', $this->format_definition['enclosure'], $this );
	}


	/**
	 * Gets the CSV for orders.
	 *
	 * In 5.0.0, moved from WC_Customer_Order_CSV_Export_Generator::get_orders_csv
	 *
	 * @since 3.0.0
	 * @param array $ids Order ID(s) to export
	 * @param bool $include_headers Optional. Whether to include CSV column headers in the output or not. Defaults to false
	 * @return string CSV data
	 */
	public function get_orders_output( $ids, $include_headers = false ) {

		$stream  = fopen( 'php://output', 'w' );
		$headers = $this->get_orders_csv_headers();

		ob_start();

		$this->write_header( $include_headers, $stream );

		$order_data = [];

		// iterate through order IDs
		foreach ( $ids as $order_id ) {

			// get data for each order
			$data = $this->get_orders_csv_row_data( $order_id );

			// skip order/data wasn't found
			if ( empty( $data ) ) {
				continue;
			}

			$order_data[] = $data;

			// write data to stream as single row or multiple rows
			$this->write_data( $data, $headers, $stream );
		}

		fclose( $stream );

		$csv = ob_get_clean();

		/**
		 * Filter the generated orders CSV.
		 *
		 * @since 5.0.0
		 *
		 * @param string $csv_data The CSV data
		 * @param array $order_data An array of the order data to write to to the CSV
		 * @param array $order_ids The order ids.
		 * @param string $export_format The order export format.
		 * @param string $custom_export_format The order custom export format, if any.
		 */
		return apply_filters( 'wc_customer_order_export_get_orders_csv_output', $csv, $order_data, $ids, $this->export_format, $this->custom_export_format );
	}


	/**
	 * Gets the column headers for the orders CSV.
	 *
	 * Note that the headers are keyed in column_key => column_name format so that plugins can control the output
	 * format using only the column headers and row data is not required to be in the exact same order, as the row data
	 * is matched on the column key
	 *
	 * In 5.0.0, moved from WC_Customer_Order_CSV_Export_Generator
	 *
	 * @since 3.0.0
	 * @return array column headers in column_key => column_name format
	 */
	private function get_orders_csv_headers() {

		$column_headers = $this->format_definition['columns'];

		if ( 'custom' !== $this->export_format ) {

			$vat_headers = [ 'vat_number' => 'vat_number' ];

			if ( isset( $column_headers['billing_company'] ) ) {
				$column_headers = Framework\SV_WC_Helper::array_insert_after( $column_headers, 'billing_company', $vat_headers );
			} else {
				$column_headers = array_merge( $column_headers, $vat_headers );
			}
		}

		/**
		 * CSV Order Export Column Headers.
		 *
		 * Filter the column headers for the order export
		 *
		 * @since 5.0.0
		 *
		 * @param array $column_headers {
		 *     column headers in key => name format
		 *     to modify the column headers, ensure the keys match these and set your own values
		 * }
		 * @param CSV_Export_Generator $this, generator instance
		 */
		return apply_filters( 'wc_customer_order_export_csv_order_headers', $column_headers, $this );
	}


	/**
	 * Gets the order data for a single CSV row.
	 *
	 * Note items are keyed according to the column header keys above so these can be modified using
	 * the provider filter without needing to worry about the array order.
	 *
	 * In 5.0.0, moved from WC_Customer_Order_CSV_Export_Generator
	 * In 4.0.0 renamed from 'get_orders_csv_row' to 'get_orders_csv_row_data'
	 *
	 * @since 3.0.0
	 * @param int $order_id the WC_Order ID
	 * @return array|false order data in the format key => content, or false on failure
	 */
	private function get_orders_csv_row_data( $order_id ) {

		$order = wc_get_order( $order_id );

		// skip if invalid order
		if ( ! $order instanceof \WC_Order ) {
			return false;
		}

		$is_json = 'json' === $this->format_definition['items_format'];

		// get line items
		$line_items = $this->get_line_items( $order, $is_json );

		// get shipping items
		$shipping_items = $this->get_shipping_items( $order, $is_json );

		// get fee items & total
		list( $fee_items, $fee_total, $fee_tax_total ) = $this->get_fee_items( $order, $is_json );

		// get tax items
		$tax_items = $this->get_tax_items( $order, $is_json );

		// add coupons
		$coupon_items = $this->get_coupon_items( $order, $is_json );

		// add refunds
		$refunds = $this->get_refunds( $order, $is_json );

		// encode fee items if available
		$fee_items_data = '';

		if ( $is_json && $fee_items ) {
			$fee_items_data = json_encode( $fee_items );
		} elseif ( is_array( $fee_items ) ) {
			$fee_items_data = implode( ';', $fee_items );
		}

		// grant download permissions if the order both permits and contains a downloadable item
		$download_permissions_granted = ( $order->is_download_permitted() && $order->has_downloadable_item() );

		$order_date = is_callable( [ $order->get_date_created(), 'date' ] ) ? $order->get_date_created()->date( 'Y-m-d H:i:s' ) : null;

		$order_data = [
			'order_id'               => $order->get_id(),
			'order_number_formatted' => $order->get_meta( '_order_number_formatted', true, 'edit' ),
			'order_number'           => $order->get_meta( '_order_number', true, 'edit' ),
			'order_date'             => $this->format_date( $order_date ),
			'status'                 => $order->get_status(),
			'shipping_total'         => $this->format_decimal( $order->get_shipping_total(), 2 ),
			'shipping_tax_total'     => $this->format_decimal( $order->get_shipping_tax(), 2 ),
			'fee_total'              => $this->format_decimal( $fee_total, 2 ),
			'fee_tax_total'          => $this->format_decimal( $fee_tax_total, 2 ),
			'tax_total'              => $this->format_decimal( $order->get_total_tax(), 2 ),
			'discount_total'         => $this->format_decimal( $order->get_total_discount(), 2 ),
			'order_total'            => $this->format_decimal( $order->get_total(), 2 ),
			'refunded_total'         => $this->format_decimal( $order->get_total_refunded(), 2 ),
			'order_currency'         => $order->get_currency( 'view' ),
			'payment_method'         => $order->get_payment_method( 'edit' ),
			'shipping_method'        => $order->get_shipping_method(),
			'customer_id'            => $order->get_user_id(),
			'billing_first_name'     => $order->get_billing_first_name( 'edit' ),
			'billing_last_name'      => $order->get_billing_last_name( 'edit' ),
			'billing_full_name'      => $order->get_formatted_billing_full_name(),
			'billing_company'        => $order->get_billing_company( 'edit' ),
			'billing_email'          => $order->get_billing_email( 'edit' ),
			'billing_phone'          => $order->get_billing_phone( 'edit' ),
			'billing_address_1'      => $order->get_billing_address_1( 'edit' ),
			'billing_address_2'      => $order->get_billing_address_2( 'edit' ),
			'billing_postcode'       => $order->get_billing_postcode( 'edit' ),
			'billing_city'           => $order->get_billing_city( 'edit' ),
			'billing_state'          => $this->get_localized_state( $order->get_billing_country( 'edit' ), $order->get_billing_state( 'edit' ) ),
			'billing_state_code'     => $order->get_billing_state( 'edit' ),
			'billing_country'        => $order->get_billing_country( 'edit' ),
			'shipping_first_name'    => $order->get_shipping_first_name( 'edit' ),
			'shipping_last_name'     => $order->get_shipping_last_name( 'edit' ),
			'shipping_full_name'     => $order->get_formatted_shipping_full_name(),
			'shipping_company'       => $order->get_shipping_company( 'edit' ),
			'shipping_address_1'     => $order->get_shipping_address_1( 'edit' ),
			'shipping_address_2'     => $order->get_shipping_address_2( 'edit' ),
			'shipping_postcode'      => $order->get_shipping_postcode( 'edit' ),
			'shipping_city'          => $order->get_shipping_city( 'edit' ),
			'shipping_state'         => $this->get_localized_state( $order->get_shipping_country( 'edit' ), $order->get_shipping_state( 'edit' ) ),
			'shipping_state_code'    => $order->get_shipping_state( 'edit' ),
			'shipping_country'       => $order->get_shipping_country( 'edit' ),
			'customer_note'          => $order->get_customer_note( 'edit' ),
			'shipping_items'         => $is_json && ! empty( $shipping_items ) ? json_encode( $shipping_items ) : implode( ';', $shipping_items ),
			'fee_items'              => $fee_items_data,
			'tax_items'              => $is_json && ! empty( $tax_items ) ? json_encode( $tax_items ) : implode( ';', $tax_items ),
			'coupon_items'           => $is_json && ! empty( $coupon_items ) ? json_encode( $coupon_items ) : implode( ';', $coupon_items ),
			'refunds'                => $is_json && ! empty( $refunds ) ? json_encode( $refunds ) : implode( ';', $refunds ),
			'order_notes'            => implode( '|', $this->get_order_notes( $order ) ),
			'download_permissions'   => $download_permissions_granted ? 1 : 0,
		];

		// support custom order numbers beyond Sequential Order Numbers Free / Pro - but since we can't
		// distinguish between the underlying number and the formatted number, only set the formatted number
		if ( ! $order_data['order_number_formatted'] ) {
			$order_data['order_number_formatted'] = $order->get_order_number();
		}

		$vat_data = [ 'vat_number' => $this->get_vat_number( $order ) ];

		if ( 'item' === $this->format_definition['row_type'] ) {

			$new_order_data = [];

			foreach ( $line_items as $item ) {
				$item_price = $this->format_decimal( $item['quantity'] && is_numeric( $item['quantity'] ) ? (float) $item['subtotal'] / (float) $item['quantity'] : 0 );

				$order_data['item_id']           = $item['id'];
				$order_data['item_name']         = $item['name'];
				$order_data['item_product_id']   = $item['product_id'];
				$order_data['item_sku']          = $item['sku'];
				$order_data['item_price']        = $item_price;
				$order_data['item_quantity']     = $item['quantity'];
				$order_data['item_subtotal']     = $item['subtotal'];
				$order_data['item_subtotal_tax'] = $item['subtotal_tax'];
				$order_data['item_total']        = $item['total'];
				$order_data['item_total_tax']    = $item['total_tax'];
				$order_data['item_refunded']     = $item['refunded'];
				$order_data['item_refunded_qty'] = $item['refunded_qty'];
				$order_data['item_meta']         = $item['meta'];

				/**
				 * CSV Order Export Row for One Row per Item.
				 *
				 * Filter the individual row data for the order export
				 *
				 * @since 3.3.0
				 *
				 * @param array $order_data {
				 *     order data in key => value format
				 *     to modify the row data, ensure the key matches any of the header keys and set your own value
				 * }
				 * @param array $item
				 * @param \WC_Order $order WC Order object
				 * @param CSV_Export_Generator $this, generator instance
				 */
				$new_order_data[] = apply_filters( 'wc_customer_order_export_csv_order_row_one_row_per_item', array_merge( $order_data, $vat_data ), $item, $order, $this );
			}

			$order_data = $new_order_data;

		} else {

			$order_data = array_merge( $order_data, $vat_data );

			$order_data['line_items'] = $is_json ? json_encode( $line_items ) : implode( ';', $line_items );
		}

		/**
		 * CSV Order Export Row.
		 *
		 * Filter the individual row data for the order export
		 *
		 * @since 3.0.0
		 *
		 * @param array $order_data {
		 *     order data in key => value format
		 *     to modify the row data, ensure the key matches any of the header keys and set your own value
		 * }
		 * @param \WC_Order $order WC Order object
		 * @param CSV_Export_Generator $this, generator instance
		 */
		return apply_filters( 'wc_customer_order_export_csv_order_row', $order_data, $order, $this );
	}


	/**
	 * Creates array of order line items.
	 *
	 * In 5.0.0, moved from WC_Customer_Order_CSV_Export_Generator::get_orders_csv_row_data
	 *
	 * @since 5.0.0
	 *
	 * @param object $order
	 * @param bool $is_json Items' column format (true for JSON, false for pipe-delimited)
	 * @return array|null
	 */
	private function get_line_items( $order, $is_json ) {

		$line_items = [];

		/**
		 * @var int $item_id order item ID
		 * @var \WC_Order_Item_Product $item order item object
		 */
		foreach ( $order->get_items() as $item_id => $item ) {

			if ( $is_json ) {

				$meta           = [];
				$meta_formatted = Framework\SV_WC_Order_Compatibility::get_item_formatted_meta_data( $item, '_' );

				foreach ( $meta_formatted as $meta_key => $formatted_meta ) {
					// we need to encode quotes as escaping them will break CSV cells in JSON format
					$meta[ $formatted_meta['label'] ] = wp_strip_all_tags( str_replace( '"', '&quot;', $formatted_meta['value'] ) );
				}

			} else {

				// make sure we filter really late as to not interfere with other plugins, such as
				// Product Add-Ons
				add_filter( 'woocommerce_attribute_label',               [ $this, 'escape_reserved_meta_chars' ], 9999 );
				add_filter( 'woocommerce_order_item_display_meta_value', [ $this, 'escape_reserved_meta_chars' ], 9999 );

				$meta_data    = $item->get_formatted_meta_data( '_', true );
				$display_meta = [];

				foreach ( $meta_data as $meta ) {
					$display_meta[] = "{$meta->display_key}: {$meta->display_value}";
				}

				$meta = implode( '', $display_meta );

				remove_filter( 'woocommerce_attribute_label',               [ $this, 'escape_reserved_meta_chars' ], 9999 );
				remove_filter( 'woocommerce_order_item_display_meta_value', [ $this, 'escape_reserved_meta_chars' ], 9999 );

				if ( $meta ) {

					// replace key-value separator (': ') with our own - equals sign (=)
					$meta = str_replace( ': ', '=', wp_strip_all_tags( $meta ) );

					// remove any newlines generated by WC_Order_Item_Meta::display()
					$meta = str_replace( [ ", \r\n", ", \r", ", \n", "\r\n", "\r", "\n" ], ',', $meta );

					// remove any html entities
					$meta = preg_replace( '/\&(?:[a-z,A-Z,0-9]+|#\d+|#x[0-9a-f]+);/', '', $meta );

					// re-insert colons and newlines
					$meta = str_replace( [ '[INSERT_COLON_HERE]', '[INSERT_NEWLINE_HERE]' ], [ ':', "\n" ], $meta );
				}
			}

			// Give the product ID and SKU initial values in case they're not overwritten.
			// this means the product doesn't exist, so it could have been deleted, BUT
			// we should set the SKU to a value so CSV import could allow these orders to be imported
			$product     = $item->get_product();
			$product_id  = 0;
			$product_sku = 'unknown_product';

			// Check if the product exists.
			if ( $product instanceof \WC_Product ) {
				$product_id  = $product->get_id();
				$product_sku = $product->get_sku();
			}

			$line_item = [
				// fields following WC API conventions (except refunded and refunded_qty), see WC_API_Orders
				'id'           => $item_id,
				// we need to encode quotes as escaping them will break CSV cells in JSON format
				'name'         => $is_json ? str_replace( '"', '&quot;', $item['name'] ) : $item['name'],
				'product_id'   => $product_id,
				'sku'          => $product_sku,
				'quantity'     => (int) $item['qty'],
				'subtotal'     => $this->format_decimal( $order->get_line_subtotal( $item ), 2 ),
				'subtotal_tax' => $this->format_decimal( $item['line_subtotal_tax'], 2 ),
				'total'        => $this->format_decimal( $order->get_line_total( $item ), 2 ),
				'total_tax'    => $this->format_decimal( $order->get_line_tax( $item ), 2 ),
				'refunded'     => $this->format_decimal( $order->get_total_refunded_for_item( $item_id ), 2 ),
				'refunded_qty' => $order->get_qty_refunded_for_item( $item_id ),
				'meta'         => $meta,
			];

			// tax data is only supported in JSON-based formats, as encoding/escaping it reliably in
			// a pipe-delimited format is quite messy
			if ( $is_json ) {
				$line_item['tax_data'] = isset( $item['line_tax_data'] ) ? maybe_unserialize( $item['line_tax_data'] ) : '';
			}

			/**
			 * CSV Order Export Line Item.
			 *
			 * Filter the individual line item entry
			 *
			 * @since 5.0.0
			 *
			 * @param array $line_item {
			 *     line item data in key => value format
			 *     the keys are for convenience and not necessarily used for exporting. Make
			 *     sure to prefix the values with the desired line item entry name
			 * }
			 *
			 * @param array $item WC order item data
			 * @param \WC_Product $product the product. May be boolean false if the product for line item doesn't exist anymore.
			 * @param \WC_Order $order the order
			 * @param CSV_Export_Generator $this, generator instance
			 */
			$line_item = apply_filters( 'wc_customer_order_export_csv_order_line_item', $line_item, $item, $product, $order, $this );

			if ( ! empty( $line_item ) ) {
				$row_type     = $this->format_definition['row_type'];
				$line_items[] = $is_json || 'item' === $row_type ? $line_item : $this->pipe_delimit_item( $line_item );
			}
		}

		return $line_items;
	}


	/**
	 * Creates array of shipping items.
	 *
	 * In 5.0.0, moved from WC_Customer_Order_CSV_Export_Generator::get_orders_csv_row_data
	 *
	 * @since 5.0.0
	 *
	 * @param object $order
	 * @param bool $is_json Items' column format (true for JSON, false for pipe-delimited)
	 * @return array|null
	 */
	private function get_shipping_items( $order, $is_json ) {

		$shipping_items = [];

		foreach ( $order->get_shipping_methods() as $shipping_item_id => $shipping ) {

			$shipping_item = [
				'id'           => $shipping_item_id,
				'method_id'    => $shipping['method_id'],
				'method_title' => $shipping['name'],
				'total'        => $this->format_decimal( $shipping['cost'], 2 ),
			];

			// tax data is only supported in JSON-based formats, as encoding/escaping it reliably in
			// a pipe-delimited format is quite messy
			if ( $is_json ) {
				$shipping_item['taxes'] = isset( $shipping['taxes'] ) ? maybe_unserialize( $shipping['taxes'] ) : '';
			}

			/**
			 * CSV Order Export Shipping Line Item.
			 *
			 * Filter the individual shipping line item entry
			 *
			 * @since 4.0.0
			 *
			 * @param array $shipping_item {
			 *     line item data in key => value format
			 *     the keys are for convenience and not necessarily used for exporting. Make
			 *     sure to prefix the values with the desired shipping line item entry name
			 * }
			 *
			 * @param array $shipping WC order shipping item data
			 * @param \WC_Order $order the order
			 * @param CSV_Export_Generator $this, generator instance
			 */
			$shipping_item = apply_filters( 'wc_customer_order_export_csv_order_shipping_item', $shipping_item, $shipping, $order, $this );

			$shipping_items[] = $is_json ? $shipping_item : $this->pipe_delimit_item( $shipping_item );
		}

		return $shipping_items;
	}


	/**
	 * Creates array of fee items.
	 *
	 * In 5.0.0, moved from WC_Customer_Order_CSV_Export_Generator::get_orders_csv_row_data
	 *
	 * @since 5.0.0
	 *
	 * @param object $order
	 * @param bool $is_json Items' column format (true for JSON, false for pipe-delimited)
	 * @return array - fee items, total and tax total. Values will be null or 0 if order has no fees.
	 */
	private function get_fee_items( $order, $is_json ) {

		$fee_items = [];

		$fee_total = 0;
		$fee_tax_total = 0;

		foreach ( $order->get_fees() as $fee_item_id => $fee ) {

			$fee_item = [
				'id'        => $fee_item_id,
				'title'     => $fee['name'],
				'tax_class' => ( ! empty( $fee['tax_class'] ) ) ? $fee['tax_class'] : null,
				'total'     => $this->format_decimal( $order->get_line_total( $fee ), 2 ),
				'total_tax' => $this->format_decimal( $order->get_line_tax( $fee ), 2 ),
			];

			// tax data is only supported in JSON-based formats, as encoding/escaping it reliably in
			// a pipe-delimited format is quite messy
			if ( $is_json ) {
				$fee_item['tax_data'] = isset( $fee['line_tax_data'] ) ? maybe_unserialize( $fee['line_tax_data'] ) : '';
			}

			$fee_item['taxable'] = null !== $fee_item['tax_class'];

			/**
			 * CSV Order Export Fee Line Item.
			 *
			 * Filter the individual fee line item entry
			 *
			 * @since 4.0.0
			 *
			 * @param array $fee_item {
			 *     line item data in key => value format
			 *     the keys are for convenience and not necessarily used for exporting. Make
			 *     sure to prefix the values with the desired fee line item entry name
			 * }
			 *
			 * @param array $fee WC order fee item data
			 * @param \WC_Order $order the order
			 * @param CSV_Export_Generator $this, generator instance
			 */
			$fee_item = apply_filters( 'wc_customer_order_export_csv_order_fee_item', $fee_item, $fee, $order, $this );

			$fee_items[] = $is_json ? $fee_item : $this->pipe_delimit_item( $fee_item );

			$fee_total     += $fee['line_total'];
			$fee_tax_total += $fee['line_tax'];
		}

		return [ ( ! empty( $fee_items ) ? $fee_items : null ), $fee_total, $fee_tax_total ];
	}


	/**
	 * Creates array of tax items.
	 *
	 * In 5.0.0, moved from WC_Customer_Order_CSV_Export_Generator::get_orders_csv_row_data
	 *
	 * @since 5.0.0
	 *
	 * @param object $order
	 * @param bool $is_json Items' column format (true for JSON, false for pipe-delimited)
	 * @return array|null
	 */
	private function get_tax_items( $order, $is_json ) {

		$tax_items = [];

		foreach ( $order->get_tax_totals() as $tax_code => $tax ) {

			$tax_item = [
				'id'       => $tax->id,
				'rate_id'  => $tax->rate_id,
				'code'     => $tax_code,
				'title'    => $tax->label,
				'total'    => $this->format_decimal( $tax->amount, 2 ),
				'compound' => (bool) $tax->is_compound,
			];

			/**
			 * CSV Order Export Tax Line Item.
			 *
			 * Filter the individual tax line item entry
			 *
			 * @since 4.0.0
			 *
			 * @param array $tax_item {
			 *     line item data in key => value format
			 *     the keys are for convenience and not necessarily used for exporting. Make
			 *     sure to prefix the values with the desired tax line item entry name
			 * }
			 *
			 * @param object $tax WC order tax item
			 * @param \WC_Order $order the order
			 * @param CSV_Export_Generator $this, generator instance
			 */
			$tax_item = apply_filters( 'wc_customer_order_export_csv_order_tax_item', $tax_item, $tax, $order, $this );

			$tax_items[] = $is_json ? $tax_item : $this->pipe_delimit_item( $tax_item );
		}

		return $tax_items;
	}


	/**
	 * Creates array of coupon items.
	 *
	 * In 5.0.0, moved from WC_Customer_Order_CSV_Export_Generator::get_orders_csv_row_data
	 *
	 * @since 5.0.0
	 *
	 * @param object $order
	 * @param bool $is_json Items' column format (true for JSON, false for pipe-delimited)
	 * @return array|null
	 */
	private function get_coupon_items( $order, $is_json ) {

		$coupon_items = [];

		foreach ( $order->get_items( 'coupon' ) as $coupon_item_id => $coupon ) {

			$_coupon     = new \WC_Coupon( $coupon['name'] );
			$coupon_post = get_post( $_coupon->get_id() );

			$coupon_item = [
				'id'          => $coupon_item_id,
				'code'        => $coupon['name'],
				'amount'      => $this->format_decimal( $coupon['discount_amount'], 2 ),
				'description' => is_object( $coupon_post ) ? $coupon_post->post_excerpt : '',
			];

			/**
			 * CSV Order Export Coupon Line Item.
			 *
			 * Filter the individual coupon line item entry
			 *
			 * @since 4.0.0
			 *
			 * @param array $coupon_item {
			 *     line item data in key => value format
			 *     the keys are for convenience and not necessarily used for exporting. Make
			 *     sure to prefix the values with the desired refund line item entry name
			 * }
			 *
			 * @param array $coupon WC order coupon item
			 * @param \WC_Order $order the order
			 * @param CSV_Export_Generator $this, generator instance
			 */
			$coupon_item = apply_filters( 'wc_customer_order_export_csv_order_coupon_item', $coupon_item, $coupon, $order, $this );

			$coupon_items[] = $is_json ? $coupon_item : $this->pipe_delimit_item( $coupon_item );
		}

		return $coupon_items;
	}


	/**
	 * Creates array of order refunds.
	 *
	 * In 5.0.0, moved from WC_Customer_Order_CSV_Export_Generator::get_orders_csv_row_data
	 *
	 * @since 5.0.0
	 *
	 * @param object $order
	 * @param bool $is_json Items' column format (true for JSON, false for pipe-delimited)
	 * @return array|null
	 */
	private function get_refunds( $order, $is_json ) {

		$refunds = [];

		/** @var \WC_Order_Refund $refund */
		foreach ( $order->get_refunds() as $refund ) {

			$refund_data = [
				'id'         => $refund->get_id(),
				'date'       => $refund->get_date_created() ? $this->format_date( $refund->get_date_created()->date( 'Y-m-d H:i:s' ) ) : '',
				'amount'     => $this->format_decimal( $refund->get_amount(), 2 ),
				'reason'     => $refund->get_reason(),
			];

			// line items data for refunds is only supported in JSON-based formats, as encoding/escaping it reliably in
			// a pipe-delimited format is quite messy
			if ( $is_json ) {

				$refunded_items = [];

				// add line items
				foreach ( $refund->get_items( [ 'line_item', 'fee', 'shipping' ] ) as $item_id => $item ) {

					$refund_amount = abs( isset( $item['line_total'] ) ? $item['line_total'] : ( isset( $item['cost'] ) ? $item['cost'] : null ) );

					// skip empty refund lines
					if ( ! $refund_amount ) {
						continue;
					}

					$refunded_item = [
						'refunded_item_id' => $item['refunded_item_id'],
						'refund_total'     => $refund_amount,
					];

					// tax data is only supported in JSON-based formats, as encoding/escaping it reliably in
					// a pipe-delimited format is quite messy
					if ( $is_json ) {

						if ( isset( $item['taxes'] ) ) {

							// shipping items use `taxes`, with no distinction between total/subtotal
							$tax_data = maybe_unserialize( $item['taxes'] );
							$refunded_item['refund_tax'] = $tax_data['total'];

						} elseif ( isset( $item['line_tax_data'] ) ) {

							// line & fee items use `line_tax_data`, with both total and subtotal tax details
							// however, we are only interested in total tax details, as this is what is needed
							// by wc_create_refund.
							$tax_data = maybe_unserialize( $item['line_tax_data'] );
							$refunded_item['refund_tax'] = $tax_data['total'];
						}
					}

					if ( isset( $item['qty'] ) ) {
						$refunded_item['qty'] = $item['qty'];
					}

					$refunded_items[] = $refunded_item;
				}

				$refund_data['line_items'] = $refunded_items;
			}

			/**
			 * CSV Order Export Refund.
			 *
			 * Filter the individual refund entry
			 *
			 * @since 4.0.0
			 *
			 * @param array $refund {
			 *     line item data in key => value format
			 *     the keys are for convenience and not necessarily used for exporting. Make
			 *     sure to prefix the values with the desired refund entry name
			 * }
			 *
			 * @param \WC_Order_Refund $refund WC order refund instance
			 * @param \WC_Order $order the order
			 * @param CSV_Export_Generator $this, generator instance
			 */
			$refund_data = apply_filters( 'wc_customer_order_export_csv_order_refund_data', $refund_data, $refund, $order, $this );

			$refunds[] = $is_json ? $refund_data : $this->pipe_delimit_item( $refund_data );
		}

		return $refunds;
	}


	/**
	 * Formats an item (shipping, fee, line item) as a pipe-delimited string.
	 *
	 * In 5.0.0, moved from WC_Customer_Order_CSV_Export_Generator
	 *
	 * @since 4.0.0
	 * @param array $item
	 * @return string
	 */
	private function pipe_delimit_item( $item ) {

		$result = [];

		foreach ( $item as $key => $value ) {

			if ( is_array( $value ) ) {
				$value = ! empty( $array ) ? maybe_serialize( $value ) : '';
			}

			$result[] = $this->escape_reserved_item_chars( $key ) . ':' . $this->escape_reserved_item_chars( $value );
		}

		return implode( '|', $result );
	}


	/**
	 * Gets the order notes for given order.
	 *
	 * In 5.0.0, moved from WC_Customer_Order_CSV_Export_Generator
	 *
	 * @since 3.0.0
	 * @param \WC_Order $order
	 * @return array order notes
	 */
	private function get_order_notes( $order ) {

		$callback = [ 'WC_Comments', 'exclude_order_comments' ];

		$args = [
			'post_id' => $order->get_id(),
			'approve' => 'approve',
			'type'    => 'order_note',
		];

		remove_filter( 'comments_clauses', $callback );

		$notes = get_comments( $args );

		add_filter( 'comments_clauses', $callback );

		$order_notes = [];

		foreach ( $notes as $note ) {

			$order_notes[] = str_replace( [ "\r", "\n" ], ' ', $note->comment_content );
		}

		return $order_notes;
	}


	/**
	 * Gets the CSV for customers.
	 *
	 * In 5.0.0, moved from WC_Customer_Order_CSV_Export_Generator::get_customers_csv
	 *
	 * @since 3.0.0
	 * @param array $ids customer IDs to export. also accepts an array of arrays with billing email and
	 *                   order Ids, for guest customers: [ $user_id, [ $billing_email, $order_id ] ]
	 * @param bool $include_headers optional. Whether to include CSV column headers in the output or not. Defaults to false
	 * @return string CSV data
	 */
	public function get_customers_output( $ids, $include_headers = false ) {

		$stream  = fopen( 'php://output', 'w' );
		$headers = $this->get_customers_csv_headers();

		ob_start();

		$this->write_header( $include_headers, $stream );

		$customer_data = [];

		// iterate through customers
		foreach ( $ids as $customer_id ) {

			$order_id = null;

			if ( is_array( $customer_id ) ) {
				list( $customer_id, $order_id ) = $customer_id;
			}

			// get data for each customer
			$data = $this->get_customers_csv_row_data( $customer_id, $order_id );

			// skip if customer/data wasn't found
			if ( empty( $data ) ) {
				continue;
			}

			$customer_data[] = $data;

			// write data to stream as single row or multiple rows
			$this->write_data( $data, $headers, $stream );
		}

		fclose( $stream );

		$csv = ob_get_clean();

		/**
		 * Filter the generated customers CSV
		 *
		 * In 4.0.0 removed the $customers param
		 *
		 * @since 3.8.0
		 * @param string $csv_data The CSV data
		 * @param array $customer_data An array of the customer data to write to to the CSV
		 * @param array $customer_ids The customer ids.
		 */
		return apply_filters( 'wc_customer_order_export_get_customers_csv_output', $csv, $customer_data, $ids );
	}


	/**
	 * Gets the column headers for the customers CSV.
	 *
	 * Note that the headers are keyed in column_key => column_name format so that plugins can control the output
	 * format using only the column headers and row data is not required to be in the exact same order, as the row data
	 * is matched on the column key
	 *
	 * In 5.0.0, moved from WC_Customer_Order_CSV_Export_Generator
	 *
	 * @since 3.0.0
	 * @return array column headers in column_key => column_name format
	 */
	public function get_customers_csv_headers() {

		$column_headers = $this->format_definition['columns'];

		/**
		 * CSV Customer Export Column Headers.
		 *
		 * Filter the column headers for the customer export
		 *
		 * @since 3.0.0
		 *
		 * @param array $column_headers {
		 *     column headers in key => name format
		 *     to modify the column headers, ensure the keys match these and set your own values
		 * }
		 * @param CSV_Export_Generator $this, generator instance
		 */
		return apply_filters( 'wc_customer_order_export_csv_customer_headers', $column_headers, $this );
	}


	/**
	 * Gets the customer data for a single CSV row.
	 *
	 * Note items are keyed according to the column header keys above so these can be modified using
	 * the provider filter without needing to worry about the array order.
	 *
	 * In 5.0.0, moved from WC_Customer_Order_CSV_Export_Generator
	 * In 4.0.0 renamed from 'get_customers_csv_row' to 'get_customers_csv_row_data'
	 *
	 * @since 3.0.0
	 * @param int|string $id customer id or email
	 * @param int $order_id optional, a valid order ID for the customer, if available.
	 * @return array|false customer data in the format key => content, or false on failure
	 */
	private function get_customers_csv_row_data( $id, $order_id = null ) {

		$user = is_numeric( $id ) ? get_user_by( 'id', $id ) : get_user_by( 'email', $id );

		// guest, get info from order
		if ( ! $user && is_numeric( $order_id ) ) {

			$order = wc_get_order( $order_id );
			$user  = $this->get_user_from_order( $order );
		}

		// user not found, skip - this can occur when an invalid customer id or email was passed in
		if ( ! $user ) {
			return false;
		}

		// fallbacks for full name are needed if we're pulling this information from user meta instead of the order data
		$customer_data = [
			'customer_id'         => $user->ID,
			'first_name'          => $user->first_name,
			'last_name'           => $user->last_name,
			'user_login'          => $user->user_login,
			'email'               => $user->user_email,
			'user_pass'           => $user->user_pass,
			'date_registered'     => $this->format_date( $user->user_registered ),
			'billing_first_name'  => $user->billing_first_name,
			'billing_last_name'   => $user->billing_last_name,
			/* translators: Placeholders: %1$s - first name; %2$s - last name */
			'billing_full_name'   => ! empty( $user->billing_full_name ) ? $user->billing_full_name : sprintf( _x( '%1$s %2$s', 'full name', 'woocommerce-customer-order-csv-export' ), $user->billing_first_name, $user->billing_last_name ),
			'billing_company'     => $user->billing_company,
			'billing_email'       => $user->billing_email,
			'billing_phone'       => $user->billing_phone,
			'billing_address_1'   => $user->billing_address_1,
			'billing_address_2'   => $user->billing_address_2,
			'billing_postcode'    => $user->billing_postcode,
			'billing_city'        => $user->billing_city,
			'billing_state'       => $this->get_localized_state( $user->billing_country, $user->billing_state ),
			'billing_state_code'  => $user->billing_state_code,
			'billing_country'     => $user->billing_country,
			'shipping_first_name' => $user->shipping_first_name,
			'shipping_last_name'  => $user->shipping_last_name,
			/* translators: Placeholders: %1$s - first name; %2$s - last name / surname */
			'shipping_full_name'  => ! empty( $user->shipping_full_name ) ? $user->shipping_full_name : sprintf( _x( '%1$s %2$s', 'full name', 'woocommerce-customer-order-csv-export' ), $user->shipping_first_name, $user->shipping_last_name ),
			'shipping_company'    => $user->shipping_company,
			'shipping_address_1'  => $user->shipping_address_1,
			'shipping_address_2'  => $user->shipping_address_2,
			'shipping_postcode'   => $user->shipping_postcode,
			'shipping_city'       => $user->shipping_city,
			'shipping_state'      => $this->get_localized_state( $user->shipping_country, $user->shipping_state ),
			'shipping_state_code' => $user->shipping_state_code,
			'shipping_country'    => $user->shipping_country,
			'total_spent'         => $this->format_decimal( wc_get_customer_total_spent( $user->ID ), 2 ),
			'order_count'         => wc_get_customer_order_count( $user->ID ),
		];

		/**
		 * CSV Customer Export Row.
		 *
		 * Filter the individual row data for the customer export
		 *
		 * @since 3.0.0
		 *
		 * @param array $customer_data {
		 *     order data in key => value format
		 *     to modify the row data, ensure the key matches any of the header keys and set your own value
		 * }
		 * @param \WP_User|object $user WP User object, if available, an object with guest customer data otherwise
		 * @param int $order_id an order ID for the customer, if available
		 * @param CSV_Export_Generator $this, generator instance
		 */
		return apply_filters( 'wc_customer_order_export_csv_customer_row', $customer_data, $user, $order_id, $this );
	}


	/**
	 * Gets the CSV for coupons.
	 *
	 * In 5.0.0, moved from WC_Customer_Order_CSV_Export_Generator::get_coupons_csv
	 *
	 * @since 4.6.0
	 *
	 * @param array $ids coupon IDs to export
	 * @param bool $include_headers optional. Whether to include CSV column headers in the output or not. Defaults to false
	 * @return string CSV data
	 */
	public function get_coupons_output( $ids, $include_headers = false ) {

		$stream  = fopen( 'php://output', 'w' );
		$headers = $this->get_coupons_csv_headers();

		ob_start();

		$this->write_header( $include_headers, $stream );

		$coupon_data = [];

		// iterate through coupons
		foreach ( $ids as $coupon_id ) {

			// get data for each coupon
			$data = $this->get_coupons_csv_row_data( $coupon_id );

			// skip if coupon data wasn't found
			if ( empty( $data ) ) {
				continue;
			}

			$coupon_data[] = $data;

			// write data to stream as single row or multiple rows
			$this->write_data( $data, $headers, $stream );
		}

		fclose( $stream );

		$csv = ob_get_clean();

		/**
		 * Filter the generated coupons CSV.
		 *
		 * @since 4.6.0
		 *
		 * @param string $csv_data The CSV data
		 * @param array $coupon_data An array of the coupon data to write to to the CSV
		 * @param array $coupon_ids The coupon ids
		 */
		return apply_filters( 'wc_customer_order_export_get_coupons_csv_output', $csv, $coupon_data, $ids );
	}


	/**
	 * Gets the column headers for the coupons CSV.
	 *
	 * Note that the headers are keyed in column_key => column_name format so that plugins can control the output
	 * format using only the column headers and row data is not required to be in the exact same order, as the row data
	 * is matched on the column key.
	 *
	 * In 5.0.0, moved from WC_Customer_Order_CSV_Export_Generator
	 *
	 * @since 4.6.0
	 *
	 * @return array column headers in column_key => column_name format
	 */
	public function get_coupons_csv_headers() {

		$column_headers = $this->format_definition['columns'];

		/**
		 * CSV Coupon Export Column Headers.
		 *
		 * Filter the column headers for the coupon export
		 *
		 * @since 4.6.0
		 *
		 * @param array $column_headers {
		 *     column headers in key => name format
		 *     to modify the column headers, ensure the keys match these and set your own values
		 * }
		 * @param CSV_Export_Generator $this, generator instance
		 */
		return apply_filters( 'wc_customer_order_export_csv_coupon_headers', $column_headers, $this );
	}


	/**
	 * Gets the coupon data for a single CSV row.
	 *
	 * Note items are keyed according to the column header keys above so these can be modified using
	 * the provider filter without needing to worry about the array order.
	 *
	 * In 5.0.0, moved from WC_Customer_Order_CSV_Export_Generator
	 *
	 * @since 4.6.0
	 *
	 * @param int|string $id coupon id
	 * @return array|false coupon data in the format key => content, or false on failure
	 */
	private function get_coupons_csv_row_data( $id ) {

		// load the coupon
		$coupon = new \WC_Coupon( $id );

		// skip this row if the coupon can't be loaded
		if ( ! $coupon ) {
			return false;
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
		$formatted_expiry_date = ( !empty( $expiry_date ) ) ? $this->format_date( $expiry_date->date( 'Y-m-d' ) ) : '';

		// create array of email addresses of customers who have used this coupon
		$used_by_customer_emails = [];

		$used_by = $coupon->get_used_by();

		foreach ( $used_by as $user_id ) {
			// this value may be a user ID or an email address
			if ( is_email( $user_id ) ) {
				$used_by_customer_emails[] = $user_id;
			} elseif ( $user = get_user_by( 'id', $user_id ) ) {
				$user = get_user_by( 'id', $user_id );
				$used_by_customer_emails[] = $user->user_email;
			}
		}

		$coupon_data = [
			'code'                       => $coupon->get_code(),
			'type'                       => $coupon->get_discount_type(),
			'description'                => $coupon->get_description(),
			'amount'                     => $this->format_decimal( $coupon->get_amount() ),
			'expiry_date'                => $formatted_expiry_date,
			'enable_free_shipping'       => $coupon->get_free_shipping() ? 'yes' : 'no',
			'minimum_amount'             => $this->format_decimal( $coupon->get_minimum_amount() ),
			'maximum_amount'             => $this->format_decimal( $coupon->get_maximum_amount() ),
			'individual_use'             => $coupon->get_individual_use() ? 'yes' : 'no',
			'exclude_sale_items'         => $coupon->get_exclude_sale_items() ? 'yes' : 'no',
			'products'                   => implode( ', ', $products ),
			'exclude_products'           => implode( ', ', $excluded_products ),
			'product_categories'         => implode( ', ', $product_categories ),
			'exclude_product_categories' => implode( ', ', $excluded_product_categories ),
			'customer_emails'            => implode( ', ', $coupon->get_email_restrictions() ),
			'usage_limit'                => $coupon->get_usage_limit(),
			'limit_usage_to_x_items'     => $coupon->get_limit_usage_to_x_items(),
			'usage_limit_per_user'       => $coupon->get_usage_limit_per_user(),
			'usage_count'                => $coupon->get_usage_count(),
			'product_ids'                => implode( ', ', $product_ids ),
			'exclude_product_ids'        => implode( ', ', $excluded_product_ids ),
			'used_by'                    => implode( ', ', $used_by_customer_emails ),
		];

		/**
		 * CSV Coupon Export Row.
		 *
		 * Filter the individual row data for the coupon export
		 *
		 * @since 4.6.0
		 *
		 * @param array $coupon_data {
		 *     order data in key => value format
		 *     to modify the row data, ensure the key matches any of the header keys and set your own value
		 * }
		 * @param \WC_Coupon $coupon the coupon for this row
		 * @param CSV_Export_Generator $this, generator instance
		 */
		return apply_filters( 'wc_customer_order_export_csv_coupon_row', $coupon_data, $coupon, $this );
	}


	/**
	 * Gets the CSV row for the given row data.
	 *
	 * This is abstracted so the provided data can be matched to the CSV headers
	 * set and the CSV delimiter and enclosure can be controlled from a single method
	 *
	 * In 5.0.0, moved from WC_Customer_Order_CSV_Export_Generator
	 *
	 * @since 3.11.3
	 * @param array $row_data Row data
	 * @param array $headers CSV column headers
	 * @return string generated CSV row
	 */
	private function get_row_csv( $row_data, $headers ) {

		if ( empty( $row_data ) ) {
			return '';
		}

		$data = [];

		foreach ( $headers as $header_key => $_ ) {

			if ( ! isset( $row_data[ $header_key ] ) ) {
				$row_data[ $header_key ] = '';
			}

			$value = $row_data[ $header_key ];
			$value = is_string( $value ) ? self::escape_cell_formulas( $value ) : $value;

			$data[] = $value;
		}

		/**
		 * Allow actors to change the generated CSV row
		 *
		 * Actors may return null to remove the generated row from the final
		 * output completely. In other cases, careful attention must be paid to
		 * not remove the line ending characters from the generated CSV.
		 *
		 * @since 4.0.0
		 *
		 * @param string $csv Generated CSV for the object (customer, order)
		 * @param array $data Input data used to generate the CSV
		 * @param CSV_Export_Generator $this - generator class instance
		 */
		return apply_filters( 'wc_customer_order_export_generated_csv_row', $this->array_to_csv_row( $data ), $data, $this );
	}


	/**
	 * Takes an array of data and return it as a CSV-formatted string.
	 *
	 * In 5.0.0, moved from WC_Customer_Order_CSV_Export_Generator
	 *
	 * @since 4.0.0
	 * @param array $data
	 * @return string
	 */
	private function array_to_csv_row( $data ) {

		ob_start();

		$stream = fopen( 'php://output', 'w' );

		fputcsv( $stream, $data, $this->delimiter, $this->enclosure );

		fclose( $stream );

		return ob_get_clean();
	}


	/**
	 * Gets CSV header row.
	 *
	 * In 5.0.0, moved from WC_Customer_Order_CSV_Export_Generator
	 *
	 * @since 4.0.0
	 * @return string
	 */
	public function get_header() {

		$header = '';

		switch ( $this->export_type ) {

			case WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS:
				$headers = $this->get_orders_csv_headers();
			break;

			case WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS:
				$headers = $this->get_customers_csv_headers();
			break;

			case WC_Customer_Order_CSV_Export::EXPORT_TYPE_COUPONS:
				$headers = $this->get_coupons_csv_headers();
				break;

			default:

				/**
				 * Allow actors to provide header data for unknown export types
				 *
				 * @since 4.0.0
				 * @param array $headers
				 */
				$headers = apply_filters( 'wc_customer_order_export_csv_' . $this->export_type . '_headers', [] );

			break;
		}

		/**
		 * CSV BOM (Byte order mark).
		 *
		 * Enable adding a BOM to the exported CSV
		 *
		 * In 4.0.0 added $export_type param, moved from __construct() to get_header()
		 *
		 * @since 3.0.0
		 * @param bool $enable_bom true to add the BOM, false otherwise. Defaults to false.
		 * @param string $export_type Export type, either `orders`, `customers` or a custom type
		 */
		if ( apply_filters( 'wc_customer_order_export_csv_enable_bom', false, $this, $this->export_type ) ) {

			$header .= ( chr(0xEF) . chr(0xBB) . chr(0xBF) );
		}

		if ( empty( $headers ) ) {
			return $header;
		}

		return $header . $this->get_row_csv( $headers, $headers );
	}


	/**
	 * Gets CSV footer row.
	 *
	 * @since 5.0.0
	 * @return string
	 */
	public function get_footer() {

		return '';
	}


	/**
	 * Writes data to stream.
	 *
	 * Data can be a single array (representing a single row)
	 * or an array of arrays when each line item is it's own row
	 *
	 * @since 5.0.0
	 *
	 * @param array $data
	 * @param array $headers
	 * @param resource $stream
	 */
	private function write_data( $data, $headers, $stream ) {

		// data can be an array of arrays when each line item is it's own row
		$first_element = reset( $data );

		if ( is_array( $first_element ) ) {

			// iterate through each line item row and write it
			foreach ( $data as $row ) {

				$csv_row = $this->get_row_csv( $row, $headers );

				if ( ! empty( $csv_row ) ) {
					fwrite( $stream, $csv_row );
				}
			}

		} else {

			// otherwise simply write the single order row
			$csv_row = $this->get_row_csv( $data, $headers );

			if ( ! empty( $csv_row ) ) {
				fwrite( $stream, $csv_row );
			}
		}
	}


	/**
	 * Writes header to stream.
	 *
	 * @since 5.0.0
	 *
	 * @param bool $include_headers
	 * @param resource $stream
	 */
	private function write_header( $include_headers, $stream ) {

		if ( $include_headers ) {

			$header = $this->get_header();

			if ( null !== $header ) {
				fwrite( $stream, $header );
			}
		}
	}


	/**
	 * Escape reserved meta chars in a string (commas and equals signs)
	 *
	 * Will also replace colons and newlines with a placeholder, which should
	 * be replaced later with actual characters.
	 *
	 * @since 3.12.0
	 * @param string $input Input string
	 * @return string
	 */
	public function escape_reserved_meta_chars( $input ) {

		// commas delimit meta fields, equals sign delimits key-value pairs,
		// colons need to be replaced with a placeholder so that we can safely
		// replace the key-value separator (colon + space) with our own (equals sign)
		$input = str_replace(
			[ '=',  ',',  ':' ],
			[ '\=', '\,', '[INSERT_COLON_HERE]' ],
			$input
		);

		// newlines are legal in CSV, but we want to remove the newlines generated
		// by WC_Order_Item_Meta::display(), so we replace them with a placeholder temporarily
		return str_replace( [ "\r\n", "\r", "\n" ], '[INSERT_NEWLINE_HERE]', $input );
	}


	/**
	 * Escape reserved item chars in a string (semicolons, colons and pipes)
	 *
	 * @since 3.12.0
	 * @param string $input Input string
	 * @return string
	 */
	public static function escape_reserved_item_chars( $input ) {

		// colons separate key-value pairs, pipes separate fields/properties,
		// and semicolons separate line items themselves
		return str_replace(
			[ ':',  '|',  ';' ],
			[ '\:', '\|', '\;' ],
			$input
		);
	}


	/**
	 * Escape leading equals, plus, minus and @ signs with a single quote to
	 * prevent CSV injections
	 *
	 * @since 4.0.0
	 * @see http://www.contextis.com/resources/blog/comma-separated-vulnerabilities/
	 * @param string $value Potentially unsafe value
	 * @return string Value with any leading special characters escaped
	 */
	public static function escape_cell_formulas( $value ) {

		$untrusted = Framework\SV_WC_Helper::str_starts_with( $value, '=' ) ||
		             Framework\SV_WC_Helper::str_starts_with( $value, '+' ) ||
		             Framework\SV_WC_Helper::str_starts_with( $value, '-' ) ||
		             Framework\SV_WC_Helper::str_starts_with( $value, '@' );

		if ( $untrusted ) {
			$value = "'" . $value;
		}

		return $value;
	}


	/** Deprecated methods ********************************************************************************************/


	/**
	 * Get the CSV for orders
	 *
	 * @since 3.0.0
	 * @deprecated 5.0.0
	 *
	 * @param array $ids Order ID(s) to export
	 * @param bool $include_headers Optional. Whether to include CSV column headers in the output or not. Defaults to false
	 * @return string
	 */
	public function get_orders_csv( $ids, $include_headers = false ) {

		wc_deprecated_function( __METHOD__, '5.0.0', get_class( $this ) . '::get_orders_output()' );

		return $this->get_orders_output( $ids, $include_headers );
	}


	/**
	 * Get the CSV for customers
	 *
	 * @since 3.0.0
	 * @deprecated 5.0.0
	 *
	 * @param array $ids customer IDs to export. also accepts an array of arrays with billing email and
	 *                   order Ids, for guest customers: [ $user_id, [ $billing_email, $order_id ] ]
	 * @param bool $include_headers optional. Whether to include CSV column headers in the output or not. Defaults to false
	 * @return string
	 */
	public function get_customers_csv( $ids, $include_headers = false ) {

		wc_deprecated_function( __METHOD__, '5.0.0', get_class( $this ) . '::get_customers_output()' );

		return $this->get_customers_output( $ids, $include_headers );
	}


	/**
	 * Get the CSV for coupons.
	 *
	 * @since 4.6.0
	 * @deprecated 5.0.0
	 *
	 * @param array $ids coupon IDs to export
	 * @param bool $include_headers optional. Whether to include CSV column headers in the output or not. Defaults to false
	 * @return string CSV data
	 */
	public function get_coupons_csv( $ids, $include_headers = false ) {

		wc_deprecated_function( __METHOD__, '5.0.0', get_class( $this ) . '::get_coupons_output()' );

		return $this->get_coupons_output( $ids, $include_headers );
	}


}
