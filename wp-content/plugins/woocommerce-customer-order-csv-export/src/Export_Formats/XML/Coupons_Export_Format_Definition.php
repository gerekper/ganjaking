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

namespace SkyVerge\WooCommerce\CSV_Export\Export_Formats\XML;

use WC_Customer_Order_CSV_Export;

defined( 'ABSPATH' ) or exit;

/**
 * XML Coupons Export Format Definition class
 *
 * @since 5.0.0
 */
class Coupons_Export_Format_Definition extends XML_Export_Format_Definition {


	/**
	 * Initializes the XML coupons export format definition.
	 *
	 * @since 5.0.0
	 *
	 * @param array $args {
	 *     An array of arguments.
	 *
	 *     @type string $key format key
	 *     @type string $name format name
	 *     @type bool $indent whether to indent the resulting XML optional
	 *     @type string $xml_version XML version optional
	 *     @type string $xml_encoding XML encoding optional
	 *     @type string $xml_standalone XML standalone optional and unused
	 *     @type array $fields mapping of the data fields
	 * }
	 */
	public function __construct( $args ) {

		$args['export_type'] = WC_Customer_Order_CSV_Export::EXPORT_TYPE_COUPONS;
		parent::__construct( $args );
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

		$dedicated_sources = [ 'coupon_amount', 'date_expires', 'discount_type', 'free_shipping' ];

		return in_array( $meta_key, $dedicated_sources, true ) || parent::meta_has_dedicated_source( $meta_key, $export_type ) ;
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
			'Code',
			'Type',
			'Description',
			'Amount',
			'ExpiryDate',
			'EnableFreeShipping',
			'MinimumAmount',
			'MaximumAmount',
			'IndividualUse',
			'ExcludeSaleItems',
			'Products',
			'ExcludeProducts',
			'ProductCategories',
			'ExcludeProductCategories',
			'CustomerEmails',
			'UsageLimit',
			'LimitUsageToXItems',
			'UsageLimitPerUser',
			'UsageCount',
			'ProductIDs',
			'ExcludeProductIDs',
			'UsedBy',
		];
	}


}
