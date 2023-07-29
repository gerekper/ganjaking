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

namespace SkyVerge\WooCommerce\CSV_Export\Export_Formats\CSV;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\CSV_Export\Export_Formats\Export_Format_Definition;
use WC_Customer_Order_CSV_Export;
use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

/**
 * CSV Export Format Definition abstract class
 *
 * @since 5.0.0
 */
class CSV_Export_Format_Definition extends Export_Format_Definition {

	/** @var string delimiter */
	protected $delimiter;

	/** @var string enclosure */
	protected $enclosure;

	/** @var array columns */
	protected $columns;


	/**
	 * Initializes the CSV export format definition.
	 *
	 * @since 5.0.0
	 *
	 * @param array $args {
	 *     An array of arguments.
	 *
	 *     @type string $key format key
	 *     @type string $name format name
	 *     @type string $export_type export type
	 *     @type array $mapping column mapping (custom formats)
	 *     @type bool $include_all_meta include all meta as columns (custom formats)
	 *     @type string $delimiter column delimiter optional
	 *     @type string $enclosure column enclosure optional
	 *     @type array $columns columns (predefined formats)
	 * }
	 */
	public function __construct( $args ) {

		$args['output_type'] = WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV;

		parent::__construct( $args );

		$this->delimiter = ! empty( $args['delimiter'] ) ? $args['delimiter'] : ',';
		$this->enclosure = ! empty( $args['enclosure'] ) ? $args['enclosure'] : '"';
		$this->columns   = ! empty( $args['columns'] ) ? $args['columns'] : [];
	}


	/**
	 * Gets the CSV delimiter.
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	public function get_delimiter() {
		return $this->delimiter;
	}


	/**
	 * Gets the enclosure.
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	public function get_enclosure() {
		return $this->enclosure;
	}


	/**
	 * Gets columns. For custom formats, gets columns from mapping and include_all_meta.
	 *
	 * @since 5.0.0
	 *
	 * @return array
	 */
	public function get_columns() {

		if ( ! empty( $this->columns ) ) {
			return $this->columns;
		}

		$columns = [];

		if ( ! empty( $this->mapping ) ) {

			foreach ( $this->mapping as $column ) {

				if ( empty( $column['source'] ) ) {
					continue;
				}

				$key = $column['source'];

				if ( 'meta' === $column['source'] ) {
					$key .= ':' . $column['meta_key'];
				} elseif ( 'static' === $column['source'] ) {
					$key = $column['name'];
				}

				$columns[ $key ] = $column['name'];
			}
		}

		// Include all meta
		if ( $this->include_all_meta ) {

			$all_meta = wc_customer_order_csv_export()->get_formats_instance()->get_all_meta_keys( $this->export_type );

			if ( ! empty( $all_meta ) ) {

				foreach ( $all_meta as $meta_key ) {

					// make sure this meta has not already been manually set
					foreach ( $this->mapping as $column ) {

						if ( ! empty( $column['source'] ) && 'meta' === $column['source'] && $meta_key === $column['meta_key'] ) {
							continue 2;
						}
					}

					$columns[ 'meta:' . $meta_key ] = 'meta:' . $meta_key;
				}
			}
		}

		$this->columns = $columns;

		return $this->columns;
	}


	/**
	 * Determines if the given meta key has a dedicated source for this format.
	 *
	 * @since 5.0.0
	 *
	 * @param string $meta_key the meta key to check
	 * @param string $export_type such as orders or customers
	 * @return bool
	 */
	public static function meta_has_dedicated_source( $meta_key, $export_type ) {

		/**
		 * Filters the known data sources for the CSV format.
		 *
		 * @since 5.0.0
		 *
		 * @param array $sources data sources
		 * @param string $export_type such as orders or customers
		 */
		$sources = apply_filters( 'wc_customer_order_export_csv_format_data_sources', static::get_data_sources(), $export_type );

		$meta_key = Framework\SV_WC_Helper::str_starts_with( $meta_key, '_' ) ? substr( $meta_key, 1 ) : $meta_key;

		return ! empty( $sources ) && in_array( $meta_key, $sources, true );
	}


	/**
	 * Returns an array definition, for compatibility.
	 *
	 * @since 5.0.0
	 *
	 * @return array
	 */
	public function to_array() {

		return array_merge( parent::to_array(), [
			'delimiter'        => $this->get_delimiter(),
			'enclosure'        => $this->get_enclosure(),
			'columns'          => $this->get_columns(),
		] );
	}


	/**
	 * Gets the built-in formats.
	 *
	 * @since 5.0.0
	 *
	 * @returns CSV_Export_Format_Definition[]
	 */
	public static function get_predefined_formats() {

		$formats = [
			WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS    => [],
			WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS => [],
			WC_Customer_Order_CSV_Export::EXPORT_TYPE_COUPONS   => [],
		];

		// 'default' format
		$formats[WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS]['default'] = new Orders_Export_Format_Definition(
			[
				'key'     => 'default',
				'name'    => __( 'Default', 'woocommerce-customer-order-csv-export' ),
				'columns' => [
					'order_id'               => 'order_id',
					'order_number'           => 'order_number',
					'order_number_formatted' => 'order_number_formatted',
					'order_date'             => 'date',
					'status'                 => 'status',
					'shipping_total'         => 'shipping_total',
					'shipping_tax_total'     => 'shipping_tax_total',
					'fee_total'              => 'fee_total',
					'fee_tax_total'          => 'fee_tax_total',
					'tax_total'              => 'tax_total',
					'discount_total'         => 'discount_total',
					'order_total'            => 'order_total',
					'refunded_total'         => 'refunded_total',
					'order_currency'         => 'order_currency',
					'payment_method'         => 'payment_method',
					'shipping_method'        => 'shipping_method',
					'customer_id'            => 'customer_id',
					'billing_first_name'     => 'billing_first_name',
					'billing_last_name'      => 'billing_last_name',
					'billing_company'        => 'billing_company',
					'billing_email'          => 'billing_email',
					'billing_phone'          => 'billing_phone',
					'billing_address_1'      => 'billing_address_1',
					'billing_address_2'      => 'billing_address_2',
					'billing_postcode'       => 'billing_postcode',
					'billing_city'           => 'billing_city',
					'billing_state'          => 'billing_state',
					'billing_country'        => 'billing_country',
					'shipping_first_name'    => 'shipping_first_name',
					'shipping_last_name'     => 'shipping_last_name',
					'shipping_address_1'     => 'shipping_address_1',
					'shipping_address_2'     => 'shipping_address_2',
					'shipping_postcode'      => 'shipping_postcode',
					'shipping_city'          => 'shipping_city',
					'shipping_state'         => 'shipping_state',
					'shipping_country'       => 'shipping_country',
					'shipping_company'       => 'shipping_company',
					'customer_note'          => 'customer_note',
					'line_items'             => 'line_items',
					'shipping_items'         => 'shipping_items',
					'fee_items'              => 'fee_items',
					'tax_items'              => 'tax_items',
					'coupon_items'           => 'coupon_items',
					'refunds'                => 'refunds',
					'order_notes'            => 'order_notes',
					'download_permissions'   => 'download_permissions_granted',
				],
			]
		);

		// 'default_one_row_per_item' format
		$formats[WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS]['default_one_row_per_item'] = new Orders_Export_Format_Definition(
			[
				'key'      => 'default_one_row_per_item',
				'name'     => __( 'Default - One Row per Item', 'woocommerce-customer-order-csv-export' ),
				'row_type' => 'item',
				'columns'  => [
					'order_id'             => 'order_id',
					'order_number'         => 'order_number',
					'order_date'           => 'date',
					'status'               => 'status',
					'shipping_total'       => 'shipping_total',
					'shipping_tax_total'   => 'shipping_tax_total',
					'fee_total'            => 'fee_total',
					'fee_tax_total'        => 'fee_tax_total',
					'tax_total'            => 'tax_total',
					'discount_total'       => 'discount_total',
					'order_total'          => 'order_total',
					'refunded_total'       => 'refunded_total',
					'order_currency'       => 'order_currency',
					'payment_method'       => 'payment_method',
					'shipping_method'      => 'shipping_method',
					'customer_id'          => 'customer_id',
					'billing_first_name'   => 'billing_first_name',
					'billing_last_name'    => 'billing_last_name',
					'billing_company'      => 'billing_company',
					'billing_email'        => 'billing_email',
					'billing_phone'        => 'billing_phone',
					'billing_address_1'    => 'billing_address_1',
					'billing_address_2'    => 'billing_address_2',
					'billing_postcode'     => 'billing_postcode',
					'billing_city'         => 'billing_city',
					'billing_state'        => 'billing_state',
					'billing_country'      => 'billing_country',
					'shipping_first_name'  => 'shipping_first_name',
					'shipping_last_name'   => 'shipping_last_name',
					'shipping_address_1'   => 'shipping_address_1',
					'shipping_address_2'   => 'shipping_address_2',
					'shipping_postcode'    => 'shipping_postcode',
					'shipping_city'        => 'shipping_city',
					'shipping_state'       => 'shipping_state',
					'shipping_country'     => 'shipping_country',
					'shipping_company'     => 'shipping_company',
					'customer_note'        => 'customer_note',
					'item_id'              => 'item_id',
					'item_product_id'      => 'item_product_id',
					'item_name'            => 'item_name',
					'item_sku'             => 'item_sku',
					'item_price'           => 'item_price',
					'item_quantity'        => 'item_quantity',
					'item_subtotal'        => 'item_subtotal',
					'item_subtotal_tax'    => 'item_subtotal_tax',
					'item_total'           => 'item_total',
					'item_total_tax'       => 'item_total_tax',
					'item_refunded'        => 'item_refunded',
					'item_refunded_qty'    => 'item_refunded_qty',
					'item_meta'            => 'item_meta',
					'shipping_items'       => 'shipping_items',
					'fee_items'            => 'fee_items',
					'tax_items'            => 'tax_items',
					'coupon_items'         => 'coupon_items',
					'order_notes'          => 'order_notes',
					'download_permissions' => 'download_permissions_granted',
				],
			]
		);

		// 'import' format, based on 'default'
		$formats[WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS]['import'] = new Orders_Export_Format_Definition(
			[
				'key'          => 'import',
				'name'         => __( 'CSV Import', 'woocommerce-customer-order-csv-export' ),
				'items_format' => 'json',
				'columns'      => [
					'order_id'               => 'id',
					'order_number'           => 'order_number',
					'order_number_formatted' => 'order_number_formatted',
					'order_date'             => 'created_at',
					'status'                 => 'status',
					'order_total'            => 'total',
					'shipping_total'         => 'total_shipping',
					'tax_total'              => 'cart_tax',
					'shipping_tax_total'     => 'shipping_tax',
					'discount_total'         => 'total_discount',
					'refunded_total'         => 'total_refunded',
					'payment_method'         => 'payment_method',
					'order_currency'         => 'currency',
					'customer_id'            => 'customer_user',
					'billing_first_name'     => 'billing_first_name',
					'billing_last_name'      => 'billing_last_name',
					'billing_email'          => 'billing_email',
					'billing_phone'          => 'billing_phone',
					'billing_address_1'      => 'billing_address_1',
					'billing_address_2'      => 'billing_address_2',
					'billing_postcode'       => 'billing_postcode',
					'billing_city'           => 'billing_city',
					'billing_state'          => 'billing_state',
					'billing_country'        => 'billing_country',
					'billing_company'        => 'billing_company',
					'shipping_first_name'    => 'shipping_first_name',
					'shipping_last_name'     => 'shipping_last_name',
					'shipping_address_1'     => 'shipping_address_1',
					'shipping_address_2'     => 'shipping_address_2',
					'shipping_postcode'      => 'shipping_postcode',
					'shipping_city'          => 'shipping_city',
					'shipping_state'         => 'shipping_state',
					'shipping_country'       => 'shipping_country',
					'shipping_company'       => 'shipping_company',
					'customer_note'          => 'note',
					'line_items'             => 'line_items',
					'shipping_items'         => 'shipping_lines',
					'tax_items'              => 'tax_lines',
					'fee_items'              => 'fee_lines',
					'coupon_items'           => 'coupon_lines',
					'refunds'                => 'refunds',
					'order_notes'            => 'order_notes',
					'download_permissions'   => 'download_permissions_granted',
				],
			]
		);

		// 'legacy_import', also based on 'default'
		$formats[WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS]['legacy_import'] = new Orders_Export_Format_Definition(
			[
				'key'     => 'legacy_import',
				'name'    => __( 'Legacy CSV Import', 'woocommerce-customer-order-csv-export' ),
				'columns' => [
					'order_id'               => 'order_id',
					'order_number_formatted' => 'order_number_formatted',
					'order_number'           => 'order_number',
					'order_date'             => 'date',
					'status'                 => 'status',
					'shipping_total'         => 'order_shipping',
					'shipping_tax_total'     => 'order_shipping_tax',
					'fee_total'              => 'order_fees',
					'fee_tax_total'          => 'order_fee_tax',
					'tax_total'              => 'order_tax',
					'discount_total'         => 'discount_total',
					'order_total'            => 'order_total',
					'payment_method'         => 'payment_method',
					'shipping_method'        => 'shipping_method',
					'customer_id'            => 'customer_user',
					'billing_first_name'     => 'billing_first_name',
					'billing_last_name'      => 'billing_last_name',
					'billing_email'          => 'billing_email',
					'billing_phone'          => 'billing_phone',
					'billing_address_1'      => 'billing_address_1',
					'billing_address_2'      => 'billing_address_2',
					'billing_postcode'       => 'billing_postcode',
					'billing_city'           => 'billing_city',
					'billing_state'          => 'billing_state',
					'billing_country'        => 'billing_country',
					'billing_company'        => 'billing_company',
					'shipping_first_name'    => 'shipping_first_name',
					'shipping_last_name'     => 'shipping_last_name',
					'shipping_address_1'     => 'shipping_address_1',
					'shipping_address_2'     => 'shipping_address_2',
					'shipping_postcode'      => 'shipping_postcode',
					'shipping_city'          => 'shipping_city',
					'shipping_state'         => 'shipping_state',
					'shipping_country'       => 'shipping_country',
					'shipping_company'       => 'shipping_company',
					'customer_note'          => 'customer_note',
					'order_item_[i]'         => 'order_item_[i]',
					// will be replaced with order_item_1, order_item_2 etc
					'order_notes'            => 'order_notes',
					'download_permissions'   => 'download_permissions_granted',
					'shipping_method_[i]'    => 'shipping_method_[i]',
					// will be replaced with shipping_method, shipping_method_2 etc
				],
			]
		);

		// 'legacy_single_column' format
		$formats[WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS]['legacy_single_column'] = new Orders_Export_Format_Definition(
			[
				'key'     => 'legacy_single_column',
				'name'    => __( 'Legacy - Single Column for all Items', 'woocommerce-customer-order-csv-export' ),
				'columns' => [
					'order_id'             => __( 'Order ID', 'woocommerce-customer-order-csv-export' ),
					'order_date'           => __( 'Date', 'woocommerce-customer-order-csv-export' ),
					'status'               => __( 'Order Status', 'woocommerce-customer-order-csv-export' ),
					'shipping_total'       => __( 'Shipping', 'woocommerce-customer-order-csv-export' ),
					'shipping_tax_total'   => __( 'Shipping Tax', 'woocommerce-customer-order-csv-export' ),
					'fee_total'            => __( 'Fees', 'woocommerce-customer-order-csv-export' ),
					'fee_tax_total'        => __( 'Fee Tax', 'woocommerce-customer-order-csv-export' ),
					'tax_total'            => __( 'Tax', 'woocommerce-customer-order-csv-export' ),
					'discount_total'       => __( 'Discount Total', 'woocommerce-customer-order-csv-export' ),
					'order_total'          => __( 'Order Total', 'woocommerce-customer-order-csv-export' ),
					'payment_method'       => __( 'Payment Method', 'woocommerce-customer-order-csv-export' ),
					'shipping_method'      => __( 'Shipping Method', 'woocommerce-customer-order-csv-export' ),
					'billing_first_name'   => __( 'Billing First Name', 'woocommerce-customer-order-csv-export' ),
					'billing_last_name'    => __( 'Billing Last Name', 'woocommerce-customer-order-csv-export' ),
					'billing_email'        => __( 'Billing Email', 'woocommerce-customer-order-csv-export' ),
					'billing_phone'        => __( 'Billing Phone', 'woocommerce-customer-order-csv-export' ),
					'billing_address_1'    => __( 'Billing Address 1', 'woocommerce-customer-order-csv-export' ),
					'billing_address_2'    => __( 'Billing Address 2', 'woocommerce-customer-order-csv-export' ),
					'billing_postcode'     => __( 'Billing Post code', 'woocommerce-customer-order-csv-export' ),
					'billing_city'         => __( 'Billing City', 'woocommerce-customer-order-csv-export' ),
					'billing_state'        => __( 'Billing State', 'woocommerce-customer-order-csv-export' ),
					'billing_country'      => __( 'Billing Country', 'woocommerce-customer-order-csv-export' ),
					'billing_company'      => __( 'Billing Company', 'woocommerce-customer-order-csv-export' ),
					'shipping_first_name'  => __( 'Shipping First Name', 'woocommerce-customer-order-csv-export' ),
					'shipping_last_name'   => __( 'Shipping Last Name', 'woocommerce-customer-order-csv-export' ),
					'shipping_address_1'   => __( 'Shipping Address 1', 'woocommerce-customer-order-csv-export' ),
					'shipping_address_2'   => __( 'Shipping Address 2', 'woocommerce-customer-order-csv-export' ),
					'shipping_postcode'    => __( 'Shipping Post code', 'woocommerce-customer-order-csv-export' ),
					'shipping_city'        => __( 'Shipping City', 'woocommerce-customer-order-csv-export' ),
					'shipping_state'       => __( 'Shipping State', 'woocommerce-customer-order-csv-export' ),
					'shipping_country'     => __( 'Shipping Country', 'woocommerce-customer-order-csv-export' ),
					'shipping_company'     => __( 'Shipping Company', 'woocommerce-customer-order-csv-export' ),
					'customer_note'        => __( 'Customer Note', 'woocommerce-customer-order-csv-export' ),
					'order_items'          => __( 'Order Items', 'woocommerce-customer-order-csv-export' ),
					'download_permissions' => __( 'Download Permissions Granted', 'woocommerce-customer-order-csv-export' ),
					'order_notes'          => __( 'Order Notes', 'woocommerce-customer-order-csv-export' ),
					'coupon_items'         => __( 'Coupons', 'woocommerce-customer-order-csv-export' ),
				],
			]
		);

		// 'legacy_one_row_per_item' format, based on legacy format
		$formats[WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS]['legacy_one_row_per_item'] = new Orders_Export_Format_Definition(
			[
				'key'      => 'legacy_one_row_per_item',
				'name'     => __( 'Legacy - One Row per Item', 'woocommerce-customer-order-csv-export' ),
				'row_type' => 'item',
				'columns'  => [
					'order_id'             => __( 'Order ID', 'woocommerce-customer-order-csv-export' ),
					'order_date'           => __( 'Date', 'woocommerce-customer-order-csv-export' ),
					'status'               => __( 'Order Status', 'woocommerce-customer-order-csv-export' ),
					'shipping_total'       => __( 'Shipping', 'woocommerce-customer-order-csv-export' ),
					'shipping_tax_total'   => __( 'Shipping Tax', 'woocommerce-customer-order-csv-export' ),
					'fee_total'            => __( 'Fees', 'woocommerce-customer-order-csv-export' ),
					'fee_tax_total'        => __( 'Fee Tax', 'woocommerce-customer-order-csv-export' ),
					'tax_total'            => __( 'Tax', 'woocommerce-customer-order-csv-export' ),
					'discount_total'       => __( 'Discount Total', 'woocommerce-customer-order-csv-export' ),
					'order_total'          => __( 'Order Total', 'woocommerce-customer-order-csv-export' ),
					'payment_method'       => __( 'Payment Method', 'woocommerce-customer-order-csv-export' ),
					'shipping_method'      => __( 'Shipping Method', 'woocommerce-customer-order-csv-export' ),
					'billing_first_name'   => __( 'Billing First Name', 'woocommerce-customer-order-csv-export' ),
					'billing_last_name'    => __( 'Billing Last Name', 'woocommerce-customer-order-csv-export' ),
					'billing_email'        => __( 'Billing Email', 'woocommerce-customer-order-csv-export' ),
					'billing_phone'        => __( 'Billing Phone', 'woocommerce-customer-order-csv-export' ),
					'billing_address_1'    => __( 'Billing Address 1', 'woocommerce-customer-order-csv-export' ),
					'billing_address_2'    => __( 'Billing Address 2', 'woocommerce-customer-order-csv-export' ),
					'billing_postcode'     => __( 'Billing Post code', 'woocommerce-customer-order-csv-export' ),
					'billing_city'         => __( 'Billing City', 'woocommerce-customer-order-csv-export' ),
					'billing_state'        => __( 'Billing State', 'woocommerce-customer-order-csv-export' ),
					'billing_country'      => __( 'Billing Country', 'woocommerce-customer-order-csv-export' ),
					'billing_company'      => __( 'Billing Company', 'woocommerce-customer-order-csv-export' ),
					'shipping_first_name'  => __( 'Shipping First Name', 'woocommerce-customer-order-csv-export' ),
					'shipping_last_name'   => __( 'Shipping Last Name', 'woocommerce-customer-order-csv-export' ),
					'shipping_address_1'   => __( 'Shipping Address 1', 'woocommerce-customer-order-csv-export' ),
					'shipping_address_2'   => __( 'Shipping Address 2', 'woocommerce-customer-order-csv-export' ),
					'shipping_postcode'    => __( 'Shipping Post code', 'woocommerce-customer-order-csv-export' ),
					'shipping_city'        => __( 'Shipping City', 'woocommerce-customer-order-csv-export' ),
					'shipping_state'       => __( 'Shipping State', 'woocommerce-customer-order-csv-export' ),
					'shipping_country'     => __( 'Shipping Country', 'woocommerce-customer-order-csv-export' ),
					'shipping_company'     => __( 'Shipping Company', 'woocommerce-customer-order-csv-export' ),
					'customer_note'        => __( 'Customer Note', 'woocommerce-customer-order-csv-export' ),
					'line_item_sku'        => __( 'Item SKU', 'woocommerce-customer-order-csv-export' ),
					'line_item_name'       => __( 'Item Name', 'woocommerce-customer-order-csv-export' ),
					'line_item_variation'  => __( 'Item Variation', 'woocommerce-customer-order-csv-export' ),
					'line_item_amount'     => __( 'Item Amount', 'woocommerce-customer-order-csv-export' ),
					'line_item_price'      => __( 'Row Price', 'woocommerce-customer-order-csv-export' ),
					'download_permissions' => __( 'Download Permissions Granted', 'woocommerce-customer-order-csv-export' ),
					'order_notes'          => __( 'Order Notes', 'woocommerce-customer-order-csv-export' ),
					'coupon_items'         => __( 'Coupons', 'woocommerce-customer-order-csv-export' ),
				],
			]
		);

		// Define customers export formats

		$formats[WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS]['default'] = new Customers_Export_Format_Definition(
			[
				'key'     => 'default',
				'name'    => __( 'Default', 'woocommerce-customer-order-csv-export' ),
				'columns' => [
					'customer_id'         => 'customer_id',
					'first_name'          => 'first_name',
					'last_name'           => 'last_name',
					'email'               => 'email',
					'date_registered'     => 'date_registered',
					'billing_first_name'  => 'billing_first_name',
					'billing_last_name'   => 'billing_last_name',
					'billing_company'     => 'billing_company',
					'billing_email'       => 'billing_email',
					'billing_phone'       => 'billing_phone',
					'billing_address_1'   => 'billing_address_1',
					'billing_address_2'   => 'billing_address_2',
					'billing_postcode'    => 'billing_postcode',
					'billing_city'        => 'billing_city',
					'billing_state'       => 'billing_state',
					'billing_country'     => 'billing_country',
					'shipping_first_name' => 'shipping_first_name',
					'shipping_last_name'  => 'shipping_last_name',
					'shipping_company'    => 'shipping_company',
					'shipping_address_1'  => 'shipping_address_1',
					'shipping_address_2'  => 'shipping_address_2',
					'shipping_postcode'   => 'shipping_postcode',
					'shipping_city'       => 'shipping_city',
					'shipping_state'      => 'shipping_state',
					'shipping_country'    => 'shipping_country',
				],
			]
		);

		$formats[WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS]['import'] = new Customers_Export_Format_Definition(
			[
				'key'     => 'import',
				'name'    => __( 'CSV Import', 'woocommerce-customer-order-csv-export' ),
				'columns' => [
					'user_login'          => 'username',
					'email'               => 'email',
					'user_pass'           => 'password',
					'date_registered'     => 'date_registered',
					'billing_first_name'  => 'billing_first_name',
					'billing_last_name'   => 'billing_last_name',
					'billing_company'     => 'billing_company',
					'billing_address_1'   => 'billing_address_1',
					'billing_address_2'   => 'billing_address_2',
					'billing_city'        => 'billing_city',
					'billing_state'       => 'billing_state',
					'billing_postcode'    => 'billing_postcode',
					'billing_country'     => 'billing_country',
					'billing_email'       => 'billing_email',
					'billing_phone'       => 'billing_phone',
					'shipping_first_name' => 'shipping_first_name',
					'shipping_last_name'  => 'shipping_last_name',
					'shipping_company'    => 'shipping_company',
					'shipping_address_1'  => 'shipping_address_1',
					'shipping_address_2'  => 'shipping_address_2',
					'shipping_city'       => 'shipping_city',
					'shipping_state'      => 'shipping_state',
					'shipping_postcode'   => 'shipping_postcode',
					'shipping_country'    => 'shipping_country',
				],
			]
		);

		$formats[WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS]['legacy'] = new Customers_Export_Format_Definition(
			[
				'key'     => 'legacy',
				'name'    => __( 'Legacy', 'woocommerce-customer-order-csv-export' ),
				'columns' => [
					'customer_id'        => __( 'ID', 'woocommerce-customer-order-csv-export' ),
					'billing_first_name' => __( 'First Name', 'woocommerce-customer-order-csv-export' ),
					'billing_last_name'  => __( 'Last Name', 'woocommerce-customer-order-csv-export' ),
					'billing_email'      => __( 'Email', 'woocommerce-customer-order-csv-export' ),
					'billing_phone'      => __( 'Phone', 'woocommerce-customer-order-csv-export' ),
					'billing_address_1'  => __( 'Address', 'woocommerce-customer-order-csv-export' ),
					'billing_address_2'  => __( 'Address 2', 'woocommerce-customer-order-csv-export' ),
					'billing_postcode'   => __( 'Post code', 'woocommerce-customer-order-csv-export' ),
					'billing_city'       => __( 'City', 'woocommerce-customer-order-csv-export' ),
					'billing_state'      => __( 'State', 'woocommerce-customer-order-csv-export' ),
					'billing_country'    => __( 'Country', 'woocommerce-customer-order-csv-export' ),
					'billing_company'    => __( 'Company', 'woocommerce-customer-order-csv-export' ),
				],
			]
		);

		// Define coupons export formats

		$formats[WC_Customer_Order_CSV_Export::EXPORT_TYPE_COUPONS]['default'] = new Coupons_Export_Format_Definition(
			[
				'key'     => 'default',
				'name'    => __( 'Default', 'woocommerce-customer-order-csv-export' ),
				'columns' => [
					'code'                       => 'code',
					'type'                       => 'type',
					'description'                => 'description',
					'amount'                     => 'amount',
					'expiry_date'                => 'expiry_date',
					'enable_free_shipping'       => 'enable_free_shipping',
					'minimum_amount'             => 'minimum_amount',
					'maximum_amount'             => 'maximum_amount',
					'individual_use'             => 'individual_use',
					'exclude_sale_items'         => 'exclude_sale_items',
					'products'                   => 'products',
					'exclude_products'           => 'exclude_products',
					'product_categories'         => 'product_categories',
					'exclude_product_categories' => 'exclude_product_categories',
					'customer_emails'            => 'customer_emails',
					'usage_limit'                => 'usage_limit',
					'limit_usage_to_x_items'     => 'limit_usage_to_x_items',
					'usage_limit_per_user'       => 'usage_limit_per_user',
					'usage_count'                => 'usage_count',
					'product_ids'                => 'product_ids',
					'exclude_product_ids'        => 'exclude_product_ids',
					'used_by'                    => 'used_by',
				],
			]
		);

		return $formats;
	}


}
