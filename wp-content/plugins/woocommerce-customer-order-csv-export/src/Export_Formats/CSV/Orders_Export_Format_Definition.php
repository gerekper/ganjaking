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

use WC_Customer_Order_CSV_Export;

defined( 'ABSPATH' ) or exit;

/**
 * CSV Orders Export Format Definition class
 *
 * @since 5.0.0
 */
class Orders_Export_Format_Definition extends CSV_Export_Format_Definition {


	/** @var string row type, one of `order` or `item` */
	private $row_type;

	/** @var string items format, one of `pipe_delimited` or `json` */
	private $items_format;


	/**
	 * Initializes the CSV orders export format definition.
	 *
	 * @since 5.0.0
	 *
	 * @param array $args {
	 *     An array of arguments.
	 *
	 *     @type string $key format key
	 *     @type string $name format name
	 *     @type string $delimiter column delimiter optional
	 *     @type string $enclosure column enclosure optional
	 *     @type array $columns columns (predefined formats)
	 *     @type array $mapping column mapping (custom formats)
	 *     @type boolean $include_all_meta include all meta as columns (custom formats)
	 *     @type string $row_type row type, one of one of `order` or `item` optional
	 *     @type string $items_format items format, one of one of `pipe_delimited` or `json` optional
	 * }
	 */
	public function __construct( $args ) {

		$args['export_type'] = WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS;

		parent::__construct( $args );

		$this->row_type     = ! empty( $args['row_type'] ) ? $args['row_type'] : 'order';
		$this->items_format = ! empty( $args['items_format'] ) ? $args['items_format'] : 'pipe_delimited';
	}


	/**
	 * Gets the row type (`order` or `item`).
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	public function get_row_type() {
		return $this->row_type;
	}


	/**
	 * Gets the items format (`pipe_delimited` or `json`).
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	public function get_items_format() {
		return $this->items_format;
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
			'row_type'     => $this->get_row_type(),
			'items_format' => $this->get_items_format(),
		] );
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

		$dedicated_order_sources = [ '_customer_user', '_order_shipping', '_order_shipping_tax', '_download_permissions_granted' ];

		return in_array( $meta_key, $dedicated_order_sources, true ) || parent::meta_has_dedicated_source( $meta_key, $export_type ) ;
	}


	/**
	 * Gets the format's available data sources.
	 *
	 * @since 5.0.0
	 *
	 * @return array
	 */
	public static function get_data_sources() {

		return [
			'order_id',
			'order_number',
			'order_number_formatted',
			'order_date',
			'status',
			'shipping_total',
			'shipping_tax_total',
			'fee_total',
			'fee_tax_total',
			'tax_total',
			'discount_total',
			'order_total',
			'refunded_total',
			'order_currency',
			'payment_method',
			'shipping_method',
			'customer_id',
			'billing_first_name',
			'billing_last_name',
			'billing_full_name',
			'billing_company',
			'vat_number',
			'billing_email',
			'billing_phone',
			'billing_address_1',
			'billing_address_2',
			'billing_postcode',
			'billing_city',
			'billing_state',
			'billing_state_code',
			'billing_country',
			'shipping_first_name',
			'shipping_last_name',
			'shipping_full_name',
			'shipping_address_1',
			'shipping_address_2',
			'shipping_postcode',
			'shipping_city',
			'shipping_state',
			'shipping_state_code',
			'shipping_country',
			'shipping_company',
			'customer_note',
			'item_id',           // order item id
			'item_product_id',
			'item_name',
			'item_sku',
			'item_quantity',
			'item_subtotal',
			'item_subtotal_tax',
			'item_total',
			'item_total_tax',
			'item_refunded',
			'item_refunded_qty',
			'item_meta',
			'item_price',
			'line_items',
			'shipping_items',
			'fee_items',
			'tax_items',
			'coupon_items',
			'refunds',
			'order_notes',
			'download_permissions',
		];
	}


}
