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
 * CSV Customers Export Format Definition class
 *
 * @since 5.0.0
 */
class Customers_Export_Format_Definition extends CSV_Export_Format_Definition {


	/**
	 * Initializes the CSV customers export format definition.
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
	 * }
	 */
	public function __construct( $args ) {

		$args['export_type'] = WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS;
		parent::__construct( $args );
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
			'customer_id',
			'first_name',
			'last_name',
			'user_login',
			'email',
			'user_pass',
			'date_registered',
			'billing_first_name',
			'billing_last_name',
			'billing_full_name',
			'billing_company',
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
			'shipping_company',
			'shipping_address_1',
			'shipping_address_2',
			'shipping_postcode',
			'shipping_city',
			'shipping_state',
			'shipping_state_code',
			'shipping_country',
			'total_spent',
			'order_count',
		];
	}


}
