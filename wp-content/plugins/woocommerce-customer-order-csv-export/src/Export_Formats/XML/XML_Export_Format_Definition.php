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

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\CSV_Export\Export_Formats\Export_Format_Definition;
use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

/**
 * XML Export Format Definition abstract class
 *
 * @since 5.0.0
 */
class XML_Export_Format_Definition extends Export_Format_Definition {

	/** @var bool whether to indent the resulting XML or not */
	protected $indent;

	/** @var string the XML version */
	protected $xml_version;

	/** @var string the XML encoding */
	protected $xml_encoding;

	/** @var string 'no' or 'yes' (unused, always set to 'no', kept for compatibility) */
	protected $xml_standalone;

	/** @var array mapping of the data fields that should appear in the document */
	protected $fields;


	/**
	 * Initializes the XML export format definition.
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
	 *     @type bool $indent whether to indent the resulting XML optional
	 *     @type string $xml_version XML version optional
	 *     @type string $xml_encoding XML encoding optional
	 *     @type string $xml_standalone XML standalone optional and unused
	 *     @type array $fields mapping of the data fields
	 * }
	 */
	public function __construct( $args ) {

		$args['output_type'] = \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_XML;

		parent::__construct( $args );

		$this->indent         = isset( $args['indent'] ) ? $args['indent'] : false;
		$this->xml_version    = ! empty( $args['xml_version'] ) ? $args['xml_version'] : '1.0';
		$this->xml_encoding   = ! empty( $args['xml_encoding'] ) ? $args['xml_encoding'] : 'UTF-8';
		$this->xml_standalone = ! empty( $args['xml_standalone'] ) ? $args['xml_standalone'] : 'no';
		$this->fields         = ! empty( $args['fields'] ) ? $args['fields'] : [];
	}


	/**
	 * Gets the indent.
	 *
	 * @since 5.0.0
	 *
	 * @return bool
	 */
	public function get_indent() {
		return $this->indent;
	}


	/**
	 * Gets the XML version.
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	public function get_xml_version() {
		return $this->xml_version;
	}


	/**
	 * Gets the XML encoding.
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	public function get_xml_encoding() {
		return $this->xml_encoding;
	}


	/**
	 * Gets the XML standalone.
	 *
	 * @since 5.0.0
	 *
	 * @return string ('yes' or 'no')
	 */
	public function get_xml_standalone() {
		return $this->xml_standalone;
	}


	/**
	 * Gets fields.
	 *
	 * @since 5.0.0
	 *
	 * @return array
	 */
	public function get_fields() {

		if ( empty( $this->fields ) && ! empty( $this->mapping ) ) {

			foreach ( $this->mapping as $field ) {

				// skip fields with no source key
				if ( empty( $field['source'] ) ) {
					continue;
				}

				$key = $field['source'];

				if ( 'meta' === $field['source'] ) {
					$key = 'Meta-' . $field['meta_key'];
				}

				elseif ( 'static' === $field['source'] ) {
					$key = $field['name'];
				}

				$this->fields[ $key ] = $field['name'];
			}
		}

		return $this->fields;
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
		 * Filters the known data sources for the XML format.
		 *
		 * @since 5.0.0
		 *
		 * @param array $sources data sources
		 * @param string $export_type such as orders or customers
		 */
		$sources = apply_filters( 'wc_customer_order_export_xml_format_data_sources', static::get_data_sources(), $export_type );

		$meta_key = Framework\SV_WC_Helper::str_starts_with( $meta_key, '_' ) ? substr( $meta_key, 1 ) : $meta_key;
		$meta_key = str_replace( ' ', '', ucwords( str_replace( '_', ' ', $meta_key ) ) );

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
			'indent'         => $this->get_indent(),
			'xml_version'    => $this->get_xml_version(),
			'xml_encoding'   => $this->get_xml_encoding(),
			'xml_standalone' => $this->get_xml_standalone(),
			'fields'         => $this->get_fields(),
		] );
	}


	/**
	 * Gets the built-in formats.
	 *
	 * @since 5.0.0
	 *
	 * @return XML_Export_Format_Definition[]
	 */
	public static function get_predefined_formats() {

		$formats = [
			\WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS    => [],
			\WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS => [],
			\WC_Customer_Order_CSV_Export::EXPORT_TYPE_COUPONS   => [],
		];

		// 'default' format

		$formats[ \WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS ]['default'] = new Orders_Export_Format_Definition( [
			'key'    => 'default',
			'name'   => __( 'Default', 'woocommerce-customer-order-csv-export' ),
			'fields' => [
				'OrderId'                    => 'OrderId',
				'OrderNumber'                => 'OrderNumber',
				'OrderNumberFormatted'       => 'OrderNumberFormatted',
				'OrderDate'                  => 'OrderDate',
				'OrderStatus'                => 'OrderStatus',
				'OrderCurrency'              => 'OrderCurrency',
				'BillingFirstName'           => 'BillingFirstName',
				'BillingLastName'            => 'BillingLastName',
				'BillingFullName'            => 'BillingFullName',
				'BillingCompany'             => 'BillingCompany',
				'BillingAddress1'            => 'BillingAddress1',
				'BillingAddress2'            => 'BillingAddress2',
				'BillingCity'                => 'BillingCity',
				'BillingState'               => 'BillingState',
				'BillingPostcode'            => 'BillingPostcode',
				'BillingCountry'             => 'BillingCountry',
				'BillingEmail'               => 'BillingEmail',
				'BillingPhone'               => 'BillingPhone',
				'ShippingFirstName'          => 'ShippingFirstName',
				'ShippingLastName'           => 'ShippingLastName',
				'ShippingFullName'           => 'ShippingFullName',
				'ShippingCompany'            => 'ShippingCompany',
				'ShippingAddress1'           => 'ShippingAddress1',
				'ShippingAddress2'           => 'ShippingAddress2',
				'ShippingCity'               => 'ShippingCity',
				'ShippingState'              => 'ShippingState',
				'ShippingPostcode'           => 'ShippingPostcode',
				'ShippingCountry'            => 'ShippingCountry',
				'ShippingMethodId'           => 'ShippingMethodId',
				'ShippingMethod'             => 'ShippingMethod',
				'PaymentMethodId'            => 'PaymentMethodId',
				'PaymentMethod'              => 'PaymentMethod',
				'DiscountTotal'              => 'DiscountTotal',
				'ShippingTotal'              => 'ShippingTotal',
				'ShippingTaxTotal'           => 'ShippingTaxTotal',
				'OrderTotal'                 => 'OrderTotal',
				'FeeTotal'                   => 'FeeTotal',
				'FeeTaxTotal'                => 'FeeTaxTotal',
				'TaxTotal'                   => 'TaxTotal',
				'RefundedTotal'              => 'RefundedTotal',
				'CompletedDate'              => 'CompletedDate',
				'CustomerNote'               => 'CustomerNote',
				'CustomerId'                 => 'CustomerId',
				'OrderLineItems'             => 'OrderLineItems',
				'OrderNotes'                 => 'OrderNotes',
				'ShippingItems'              => 'ShippingItems',
				'FeeItems'                   => 'FeeItems',
				'TaxItems'                   => 'TaxItems',
				'CouponItems'                => 'CouponItems',
				'Refunds'                    => 'Refunds',
				'DownloadPermissionsGranted' => 'DownloadPermissionsGranted',
			],
		] );

		// 'legacy' format (pre 2.0.0)

		$formats[ \WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS ]['legacy'] = new Orders_Export_Format_Definition( [
			'key'    => 'legacy',
			'name'   => __( 'Legacy', 'woocommerce-customer-order-csv-export' ),
			'indent' => true,
			'fields' => [
				'OrderId'              => 'OrderId',
				'OrderNumberFormatted' => 'OrderNumber',
				'OrderDate'            => 'OrderDate',
				'OrderStatus'          => 'OrderStatus',
				'BillingFirstName'     => 'BillingFirstName',
				'BillingLastName'      => 'BillingLastName',
				'BillingFullName'      => 'BillingFullName',
				'BillingCompany'       => 'BillingCompany',
				'BillingAddress1'      => 'BillingAddress1',
				'BillingAddress2'      => 'BillingAddress2',
				'BillingCity'          => 'BillingCity',
				'BillingState'         => 'BillingState',
				'BillingPostcode'      => 'BillingPostCode',
				'BillingCountry'       => 'BillingCountry',
				'BillingEmail'         => 'BillingEmail',
				'BillingPhone'         => 'BillingPhone',
				'ShippingFirstName'    => 'ShippingFirstName',
				'ShippingLastName'     => 'ShippingLastName',
				'ShippingFullName'     => 'ShippingFullName',
				'ShippingCompany'      => 'ShippingCompany',
				'ShippingAddress1'     => 'ShippingAddress1',
				'ShippingAddress2'     => 'ShippingAddress2',
				'ShippingCity'         => 'ShippingCity',
				'ShippingState'        => 'ShippingState',
				'ShippingPostcode'     => 'ShippingPostCode',
				'ShippingCountry'      => 'ShippingCountry',
				'ShippingMethodId'     => 'ShippingMethodId',
				'ShippingMethod'       => 'ShippingMethod',
				'PaymentMethodId'      => 'PaymentMethodId',
				'PaymentMethod'        => 'PaymentMethod',
				'DiscountTotal'        => 'DiscountTotal',
				'ShippingTotal'        => 'ShippingTotal',
				'ShippingTaxTotal'     => 'ShippingTaxTotal',
				'OrderTotal'           => 'OrderTotal',
				'FeeTotal'             => 'FeeTotal',
				'TaxTotal'             => 'TaxTotal',
				'CompletedDate'        => 'CompletedDate',
				'CustomerNote'         => 'CustomerNote',
				'CustomerId'           => 'CustomerId',
				'OrderLineItems'       => 'OrderLineItems',
				'OrderNotes'           => 'OrderNotes',
			],
		] );

		// Define customers export formats

		// `default` format

		$formats[ \WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS ]['default'] = new Customers_Export_Format_Definition( [
			'key'    => 'default',
			'name'   => __( 'Default', 'woocommerce-customer-order-csv-export' ),
			'fields' => [
				'CustomerId'        => 'CustomerId',
				'FirstName'         => 'FirstName',
				'LastName'          => 'LastName',
				'Username'          => 'Username',
				'Email'             => 'Email',
				'Password'          => 'Password',
				'DateRegistered'    => 'DateRegistered',
				'BillingFirstName'  => 'BillingFirstName',
				'BillingLastName'   => 'BillingLastName',
				'BillingCompany'    => 'BillingCompany',
				'BillingAddress1'   => 'BillingAddress1',
				'BillingAddress2'   => 'BillingAddress2',
				'BillingCity'       => 'BillingCity',
				'BillingState'      => 'BillingState',
				'BillingPostcode'   => 'BillingPostcode',
				'BillingCountry'    => 'BillingCountry',
				'BillingEmail'      => 'BillingEmail',
				'BillingPhone'      => 'BillingPhone',
				'ShippingFirstName' => 'ShippingFirstName',
				'ShippingLastName'  => 'ShippingLastName',
				'ShippingCompany'   => 'ShippingCompany',
				'ShippingAddress1'  => 'ShippingAddress1',
				'ShippingAddress2'  => 'ShippingAddress2',
				'ShippingCity'      => 'ShippingCity',
				'ShippingState'     => 'ShippingState',
				'ShippingPostcode'  => 'ShippingPostcode',
				'ShippingCountry'   => 'ShippingCountry',
			],
		] );

		// `legacy` format (pre 2.0.0)

		$formats[ \WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS ]['legacy'] = new Customers_Export_Format_Definition( [
			'key'    => 'legacy',
			'name'   => __( 'Legacy', 'woocommerce-customer-order-csv-export' ),
			'fields' => [
				'CustomerId'        => 'CustomerId',
				'FirstName'         => 'FirstName',
				'LastName'          => 'LastName',
				'Email'             => 'Email',
				'DateRegistered'    => 'DateRegistered',
				'BillingFirstName'  => 'BillingFirstName',
				'BillingLastName'   => 'BillingLastName',
				'BillingCompany'    => 'BillingCompany',
				'Billingaddress1'   => 'Billingaddress1',
				'Billingaddress2'   => 'Billingaddress2',
				'Billingcity'       => 'Billingcity',
				'Billingstate'      => 'Billingstate',
				'Billingpostcode'   => 'Billingpostcode',
				'Billingcountry'    => 'Billingcountry',
				'BillingEmail'      => 'BillingEmail',
				'BillingPhone'      => 'BillingPhone',
				'ShippingFirstName' => 'ShippingFirstName',
				'ShippingLastName'  => 'ShippingLastName',
				'ShippingCompany'   => 'ShippingCompany',
				'ShippingAddress1'  => 'ShippingAddress1',
				'ShippingAddress2'  => 'ShippingAddress2',
				'ShippingCity'      => 'ShippingCity',
				'ShippingState'     => 'ShippingState',
				'ShippingPostcode'  => 'ShippingPostcode',
				'ShippingCountry'   => 'ShippingCountry',
			],
		] );

		// define coupons export formats

		// 'default' format

		$formats[ \WC_Customer_Order_CSV_Export::EXPORT_TYPE_COUPONS ]['default'] = new Coupons_Export_Format_Definition( [
			'key'    => 'default',
			'name'   => __( 'Default', 'woocommerce-customer-order-csv-export' ),
			'fields' => [
				'Code'                     => 'Code',
				'Type'                     => 'Type',
				'Description'              => 'Description',
				'Amount'                   => 'Amount',
				'ExpiryDate'               => 'ExpiryDate',
				'EnableFreeShipping'       => 'EnableFreeShipping',
				'MinimumAmount'            => 'MinimumAmount',
				'MaximumAmount'            => 'MaximumAmount',
				'IndividualUse'            => 'IndividualUse',
				'ExcludeSaleItems'         => 'ExcludeSaleItems',
				'Products'                 => 'Products',
				'ExcludeProducts'          => 'ExcludeProducts',
				'ProductCategories'        => 'ProductCategories',
				'ExcludeProductCategories' => 'ExcludeProductCategories',
				'CustomerEmails'           => 'CustomerEmails',
				'UsageLimit'               => 'UsageLimit',
				'LimitUsageToXItems'       => 'LimitUsageToXItems',
				'UsageLimitPerUser'        => 'UsageLimitPerUser',
				'UsageCount'               => 'UsageCount',
				'ProductIDs'               => 'ProductIDs',
				'ExcludeProductIDs'        => 'ExcludeProductIDs',
				'UsedBy'                   => 'UsedBy',
			],
		] );

		return $formats;
	}


}
