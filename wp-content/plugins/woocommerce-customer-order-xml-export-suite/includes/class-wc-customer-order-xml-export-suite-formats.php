<?php
/**
 * WooCommerce Customer/Order XML Export Suite
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order XML Export Suite to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order XML Export Suite for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-customer-order-xml-export-suite/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2019, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Customer/Order XML Export Suite Formats
 *
 * Defines different export formats and provides a base set of
 * data sources for the formats.
 *
 * @since 2.0.0
 */
class WC_Customer_Order_XML_Export_Suite_Formats {


	/** @var array $formats export formats container **/
	private $formats;

	/** @var array $all_meta_keys container for export types **/
	private $all_meta_keys = array();


	/**
	 * Get field data options for the export type. This method is useful
	 * for getting a base list of field data options for an export format,
	 * for example to be used in the field mapper UI.
	 *
	 * @since 2.0.0
	 * @param string $export_type Export type
	 * @return array
	 */
	public function get_field_data_options( $export_type ) {

		$options = array();

		if ( 'orders' === $export_type ) {

			$options = array(
				'OrderId',
				'OrderNumber',
				'OrderNumberFormatted',
				'OrderDate',
				'OrderStatus',
				'OrderCurrency',
				'BillingFirstName',
				'BillingLastName',
				'BillingFullName',
				'BillingCompany',
				'BillingAddress1',
				'BillingAddress2',
				'BillingCity',
				'BillingState',
				'BillingStateCode',
				'BillingPostcode',
				'BillingCountry',
				'BillingEmail',
				'BillingPhone',
				'ShippingFirstName',
				'ShippingLastName',
				'ShippingFullName',
				'ShippingCompany',
				'ShippingAddress1',
				'ShippingAddress2',
				'ShippingCity',
				'ShippingState',
				'ShippingStateCode',
				'ShippingPostcode',
				'ShippingCountry',
				'ShippingMethodId',
				'ShippingMethod',
				'PaymentMethodId',
				'PaymentMethod',
				'DiscountTotal',
				'ShippingTotal',
				'ShippingTaxTotal',
				'OrderTotal',
				'FeeTotal',
				'FeeTaxTotal',
				'TaxTotal',
				'RefundedTotal',
				'CompletedDate',
				'CustomerNote',
				'CustomerId',
				'OrderLineItems',
				'OrderNotes',
				'ShippingItems',
				'FeeItems',
				'TaxItems',
				'CouponItems',
				'Refunds',
				'DownloadPermissionsGranted',
			);

		} elseif ( 'customers' === $export_type ) {

			$options = array(
				'CustomerId',
				'FirstName',
				'LastName',
				'Username',
				'Email',
				'Password',
				'DateRegistered',
				'BillingFirstName',
				'BillingLastName',
				'BillingFullName',
				'BillingCompany',
				'BillingAddress1',
				'BillingAddress2',
				'BillingCity',
				'BillingState',
				'BillingStateCode',
				'BillingPostcode',
				'BillingCountry',
				'BillingEmail',
				'BillingPhone',
				'ShippingFirstName',
				'ShippingLastName',
				'ShippingFullName',
				'ShippingCompany',
				'ShippingAddress1',
				'ShippingAddress2',
				'ShippingCity',
				'ShippingState',
				'ShippingStateCode',
				'ShippingPostcode',
				'ShippingCountry',
				'TotalSpent',
				'OrderCount',
			);

		} elseif ( 'coupons' === $export_type ) {

			$options = array(
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
			);
		}

		/**
		 * Allow actors to adjust the available export field data options
		 *
		 * This filter is especially useful for providing options for custom export types
		 *
		 * @since 2.0.0
		 * @param array $options field data options
		 * @param string $export_type export type
		 */
		return apply_filters( 'wc_customer_order_xml_export_suite_format_field_data_options', $options, $export_type );
	}


	/**
	 * Get an export format
	 *
	 * @since 2.0.0
	 * @param string $export_type Export type, such as `orders` or `customers`
	 * @param string $format Export format, such as `default` or `import`
	 * @return array|null format definition, or null if not found
	 */
	public function get_format( $export_type, $format ) {

		$definition = null;

		// On first call, load the built-in & custom formats
		if ( ! isset( $this->formats ) ) {
			$this->load_formats();
		}

		// Find the requested format
		if ( isset( $this->formats[ $export_type ] ) && isset( $this->formats[ $export_type ][ $format ] ) ) {
			$definition = $this->formats[ $export_type ][ $format ];
		}

		/**
		 * Allow actors to change the format definition
		 *
		 * @since 2.0.0
		 * @param array|null $definition Format definition, or null if not found
		 * @param string $export_type Export type, such as `orders` or `customers`
		 * @param string $format Export format, such as `default` or `import`
		 */
		return apply_filters( 'wc_customer_order_xml_export_suite_format_definition', $definition, $export_type, $format );
	}


	/**
	 * Get export formats for the given export type
	 *
	 * @since 2.0.0
	 * @param string $export_type Export type, such as `orders` or `customers`
	 * @return array
	 */
	public function get_formats( $export_type ) {

		if ( ! isset( $this->formats ) ) {
			$this->load_formats();
		}

		$formats = array();

		// make sure each format is filtered
		if ( ! empty( $this->formats[ $export_type ] ) ) {

			foreach ( array_keys( $this->formats[ $export_type ] ) as $format_key ) {

				$formats[ $format_key ] = $this->get_format( $export_type, $format_key );
			}
		}

		return $formats;
	}


	/**
	 * Constructor
	 *
	 * Initializes the built-in formats and loads the custom
	 * format to memory
	 *
	 * @since 2.0.0
	 */
	private function load_formats() {

		$this->formats = array(
			'orders'    => array(),
			'customers' => array(),
			'coupons'   => array(),
		);

		// 'default' format

		$this->formats['orders']['default'] = array(
			'indent'         => false,
			'xml_version'    => '1.0',
			'xml_encoding'   => 'UTF-8',
			'xml_standalone' => 'no',
			'fields'         => array(
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
			),
		);

		// 'legacy' format (pre 2.0.0)

		$this->formats['orders']['legacy'] = array(
			'indent'         => true,
			'xml_version'    => '1.0',
			'xml_encoding'   => 'UTF-8',
			'xml_standalone' => 'no',
			'fields'         => array(
				'OrderId'                    => 'OrderId',
				'OrderNumberFormatted'       => 'OrderNumber',
				'OrderDate'                  => 'OrderDate',
				'OrderStatus'                => 'OrderStatus',
				'BillingFirstName'           => 'BillingFirstName',
				'BillingLastName'            => 'BillingLastName',
				'BillingFullName'            => 'BillingFullName',
				'BillingCompany'             => 'BillingCompany',
				'BillingAddress1'            => 'BillingAddress1',
				'BillingAddress2'            => 'BillingAddress2',
				'BillingCity'                => 'BillingCity',
				'BillingState'               => 'BillingState',
				'BillingPostcode'            => 'BillingPostCode',
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
				'ShippingPostcode'           => 'ShippingPostCode',
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
				'TaxTotal'                   => 'TaxTotal',
				'CompletedDate'              => 'CompletedDate',
				'CustomerNote'               => 'CustomerNote',
				'CustomerId'                 => 'CustomerId',
				'OrderLineItems'             => 'OrderLineItems',
				'OrderNotes'                 => 'OrderNotes',
			),
		);


		// 'custom' order format

		$this->formats['orders']['custom'] = array(
			'indent'         => 'yes' === get_option( 'wc_customer_order_xml_export_suite_orders_custom_format_indent', 'no' ),
			'xml_version'    => '1.0',
			'xml_encoding'   => 'UTF-8',
			'xml_standalone' => 'no',
			'fields'         => $this->get_custom_field_mapping( 'orders' ),
		);



		// Define customers export formats

		// `default` format

		$this->formats['customers']['default'] = array(
			'indent'         => false,
			'xml_version'    => '1.0',
			'xml_encoding'   => 'UTF-8',
			'xml_standalone' => 'no',
			'fields'         => array(
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
			),
		);

		// `legacy` format (pre 2.0.0)

		$this->formats['customers']['legacy'] = array(
			'indent'         => false,
			'xml_version'    => '1.0',
			'xml_encoding'   => 'UTF-8',
			'xml_standalone' => 'no',
			'fields'         => array(
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
			),
		);

		$this->formats['customers']['custom'] = array(
			'indent'         => 'yes' === get_option( 'wc_customer_order_xml_export_suite_customers_custom_format_indent', 'no' ),
			'xml_version'    => '1.0',
			'xml_encoding'   => 'UTF-8',
			'xml_standalone' => 'no',
			'fields'         => $this->get_custom_field_mapping( 'customers' ),
		);

		// define coupons export formats

		// 'default' format

		$this->formats['coupons']['default'] = array(
			'indent'         => false,
			'xml_version'    => '1.0',
			'xml_encoding'   => 'UTF-8',
			'xml_standalone' => 'no',
			'fields'         => array(
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
			),
		);

		// 'custom' format
		$this->formats['coupons']['custom'] = array(
			'indent'         => 'yes' === get_option( 'wc_customer_order_xml_export_suite_coupons_custom_format_indent', 'no' ),
			'xml_version'    => '1.0',
			'xml_encoding'   => 'UTF-8',
			'xml_standalone' => 'no',
			'fields'         => $this->get_custom_field_mapping( 'coupons' ),
		);
	}


	/**
	 * Get custom export type field mapping
	 *
	 * @since 2.0.0
	 * @param string $export_type
	 * @return array
	 */
	private function get_custom_field_mapping( $export_type ) {

		$fields = array();

		$mapping = get_option( 'wc_customer_order_xml_export_suite_' . $export_type . '_custom_format_mapping' );

		if ( ! empty( $mapping ) ) {
			foreach ( $mapping as $field ) {

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

				$fields[ $key ] = $field['name'];
			}
		}

		// Include all meta
		if ( 'yes' === get_option( 'wc_customer_order_xml_export_suite_' . $export_type . '_custom_format_include_all_meta' ) ) {

			$all_meta = $this->get_all_meta_keys( $export_type );

			if ( ! empty( $all_meta ) ) {

				foreach ( $all_meta as $meta_key ) {

					// make sure this meta has not already been manually set
					foreach ( $mapping as $field  ) {

						if ( ! empty( $field['source'] ) && 'meta' === $field['source'] && $meta_key === $field['meta_key'] ) {
							continue 2;
						}
					}

					$fields[ 'Meta-' . $meta_key ] = 'Meta-' . $meta_key;
				}
			}
		}

		return $fields;
	}


	/**
	 * Get all meta keys for the given export type
	 *
	 * @since 2.0.0
	 * @param string $export_type
	 * @return array
	 */
	public function get_all_meta_keys( $export_type ) {

		if ( ! isset( $this->all_meta_keys[ $export_type ] ) ) {

			$meta_keys = array();

			if ( 'customers' === $export_type ) {
				$meta_keys = $this->get_user_meta_keys();
			} elseif ( 'orders' === $export_type ) {
				$meta_keys = $this->get_post_meta_keys( 'shop_order' );
			} elseif ( 'coupons' === $export_type ) {
				$meta_keys = $this->get_post_meta_keys( 'shop_coupon' );
			}

			// exclude meta with dedicated fields from all meta
			foreach ( $meta_keys as $key => $meta_key ) {

				if ( $this->meta_has_dedicated_field( $meta_key, $export_type ) ) {
					unset( $meta_keys[ $key ] );
				}
			}

			/**
			 * Allow actors to adjust the returned meta keys for an export type
			 *
			 * This filter is useful for providing meta keys for custom export types.
			 *
			 * @since 2.0.0
			 * @param array $meta_keys
			 * @param string $export_type
			 */
			$this->all_meta_keys[ $export_type ] = apply_filters( 'wc_customer_order_xml_export_suite_all_meta_keys', $meta_keys, $export_type );
		}

		return $this->all_meta_keys[ $export_type ];
	}


	/**
	 * Get a list of all the meta keys for a post type. This includes all public, private,
	 * used, no-longer used etc. They will be sorted once fetched.
	 *
	 * @since 2.0.0
	 * @param string $post_type Optional. Defaults to `shop_order`
	 * @return array
	 */
	public function get_post_meta_keys( $post_type = 'shop_order' ) {

		global $wpdb;

		$meta = $wpdb->get_col( $wpdb->prepare( "
			SELECT DISTINCT pm.meta_key
			FROM {$wpdb->postmeta} AS pm
			LEFT JOIN {$wpdb->posts} AS p ON p.ID = pm.post_id
			WHERE p.post_type = %s
		", $post_type ) );

		sort( $meta );

		return $meta;
	}


	/**
	 * Get a list of all the meta keys for users. They will be sorted once fetched.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function get_user_meta_keys() {

		global $wpdb;

		$meta = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->usermeta}" );

		sort( $meta );

		return $meta;
	}


	/**
	 * Check if a meta key for an export type has a dedicated field
	 *
	 * @since 2.0.0
	 * @param string $meta_key
	 * @param string $export_type
	 * @return bool
	 */
	private function meta_has_dedicated_field( $meta_key, $export_type ) {

		$has_dedicated_field = false;

		if ( 'orders' === $export_type ) {

			$dedicated_order_fields = array( '_customer_user', '_order_shipping', '_order_shipping_tax', '_download_permissions_granted' );
			$has_dedicated_field    = in_array( $meta_key, $dedicated_order_fields, true );
		}

		if ( 'coupons' === $export_type ) {

			$dedicated_coupon_fields = array( 'coupon_amount', 'date_expires', 'discount_type', 'free_shipping' );
			$has_dedicated_field     = in_array( $meta_key, $dedicated_coupon_fields, true );
		}

		if ( ! $has_dedicated_field ) {

			$fields            = $this->get_field_data_options( $export_type );
			$_meta_key         = $this->get_string_pascalized( SV_WC_Helper::str_starts_with( $meta_key, '_' ) ? substr( $meta_key, 1 ) : $meta_key );
			$has_dedicated_field = ! empty( $fields ) && in_array( $_meta_key, $fields, true );
		}

		/**
		 * Allow actors to adjust whether a meta key has a dedicated field or not
		 *
		 * This affects whether the meta key is included in custom export formats
		 * with `include all meta` checked or not. Meta keys having dedicated fields
		 * are excluded from the export, as the value will be present in the dedicated field.
		 *
		 * @since 2.0.0
		 * @param bool $has_dedicated_field
		 * @param string $meta_key
		 * @param string $export_type
		 */
		return apply_filters( 'wc_customer_order_xml_export_suite_meta_has_dedicated_field', $has_dedicated_field, $meta_key, $export_type );
	}


	/**
	 * Convert an underscored_string to PascalCase
	 *
	 * @since 2.0.0
	 * @param string $string
	 * @return string
	 */
	private function get_string_pascalized( $string ) {

		return str_replace( ' ', '', ucwords( str_replace( '_', ' ', $string ) ) );
	}


}
