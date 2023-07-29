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
 * Customer/Order Export Generator abstract class
 *
 * @since 5.0.0
 */
abstract class Export_Generator {


	/** @var string export type */
	public $export_type;

	/** @var string output type */
	public $output_type;

	/** @var string export format ("custom" for all custom formats) */
	public $export_format;

	/** @var string custom export format (selected custom format key, if a custom format is selected) */
	public $custom_export_format;

	/** @var array format definition */
	public $format_definition;

	/** @var array order IDs or customer IDs */
	public $ids;


	/**
	 * Initialize the generator
	 *
	 * In 4.0.0 replaced $ids param with $export_type param
	 * In 4.0.3 added back $ids as a second, optional param for backwards compatibility
	 *
	 * @since 3.0.0
	 * @param string $export_type export type, one of `orders` or `customers`
	 * @param array $ids optional. Object IDs associated with the export. Provide for export formats that
	 *                   modify headers based on the objects being exported (such as orders legacy import format)
	 * @param string $format_key export format key
	 */
	public function __construct( $export_type, $ids = null, $format_key = 'default' ) {

		$this->export_type = $export_type;

		// if this is a custom format, set $this->export_format to 'custom' for backwards compatibility
		// applies to both new custom formats prefixed with `custom-`, as well as legacy that are simply `custom`
		if ( Framework\SV_WC_Helper::str_starts_with( $format_key, 'custom' ) ) {

			$this->custom_export_format = $format_key;
			$format_key                 = 'custom';
		}

		/**
		 * Filters the {output type} export format.
		 *
		 * Can be used to target a specific output type.
		 *
		 * @since 5.0.0
		 *
		 * @param string $format export format key
		 * @param Export_Generator $generator generator instance
		 */
		$format_key = apply_filters( "wc_customer_order_export_{$this->output_type}_format", $format_key, $this );

		/**
		 * Filters the configured export format.
		 *
		 * @since 5.0.0
		 *
		 * @param string $format export format key
		 * @param Export_Generator $generator generator instance
		 */
		$format_key = apply_filters( 'wc_customer_order_export_format', $format_key, $this );

		$this->export_format = $format_key;

		// set the format key back to its original custom format key if necessary
		if ( 'custom' === $format_key && ! empty( $this->custom_export_format ) ) {
			$format_key = $this->custom_export_format;
		}

		// get format definition
		$this->format_definition = wc_customer_order_csv_export()->get_formats_instance()->get_format( $export_type, $format_key, $this->output_type );

		if ( ! empty( $ids ) ) {
			$this->ids = $ids;
		}
	}


	/**
	 * Gets output for the provided export type.
	 *
	 * @since 5.0.0
	 *
	 * @param array $ids
	 * @return string
	 */
	public function get_output( $ids ) {

		switch ( $this->export_type ) {

			case WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS:
				return $this->get_orders_output( $ids );

			case WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS:
				return $this->get_customers_output( $ids );

			case WC_Customer_Order_CSV_Export::EXPORT_TYPE_COUPONS:
				return $this->get_coupons_output( $ids );

			default:

				/**
				 * Allow actors to provide output for custom export types
				 *
				 * @since 5.0.0
				 *
				 * @param string $output defaults to empty string
				 * @param array $ids object IDs to export
				 * @param string $export_format export format, if any
				 * @param string $custom_export_format custom export format, if any
				 */
				return apply_filters( 'wc_customer_order_export_get_' . $this->export_type . '_' . $this->output_type, '', $ids, $this->export_format, $this->custom_export_format );
		}
	}


	/**
	 * Gets header.
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	abstract public function get_header();


	/**
	 * Gets footer.
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	abstract public function get_footer();


	/**
	 * Gets the output for orders.
	 *
	 * @since 5.0.0
	 *
	 * @param array $ids Order ID(s) to export
	 * @param bool $include_headers Optional. Whether to include headers in the output or not. Defaults to false
	 * @return string output data
	 */
	abstract public function get_orders_output( $ids, $include_headers = false );


	/**
	 * Gets the output for customers.
	 *
	 * @since 5.0.0
	 *
	 * @param array $ids customer IDs to export. also accepts an array of arrays with billing email and
	 *                   order Ids, for guest customers: [ $user_id, [ $billing_email, $order_id ] ]
	 * @param bool $include_headers optional. Whether to include headers in the output or not. Defaults to false
	 * @return string output
	 */
	abstract public function get_customers_output( $ids, $include_headers = false );


	/**
	 * Gets the output for coupons.
	 *
	 * @since 5.0.0
	 *
	 * @param array $ids coupon IDs to export
	 * @param bool $include_headers optional. Whether to include headers in the output or not. Defaults to false
	 * @return string output
	 */
	abstract public function get_coupons_output( $ids, $include_headers = false );


	/**
	 * Helper to localize state names for countries with numeric state codes
	 *
	 * @since 4.1.0
	 * @param string $country the country for the current customer
	 * @param string $state the state code for the current customer
	 * @return string the localized state name
	 */
	protected function get_localized_state( $country, $state ) {

		// countries that have numeric state codes
		$countries_with_numeric_states = [
			'JP',
			'BG',
			'CN',
			'TH',
			'TR',
		];

		// only proceed if we need to replace a numeric state code
		if ( ! in_array( $country, $countries_with_numeric_states, true ) ) {
			return $state;
		}

		$state_name = $state;

		// get a state list for states the store sells to
		$states = WC()->countries->get_states();

		if ( ! empty ( $states[ $country ] ) && isset( $states[ $country ][ $state ] ) ) {
			$state_name = $states[ $country ][ $state ];
		}

		return $state_name;
	}


	/**
	 * Helper to run all dates through a formatting filter for easy format changes.
	 *
	 * @since 4.3.4
	 *
	 * @param string $date the current date output
	 * @return string the formatted date output
	 */
	protected function format_date( $date ) {

		/**
		 * Filters the format of all dates in the export file.
		 *
		 * Can be used to target a specific output type.
		 *
		 * @since 5.0.0
		 *
		 * @param string $date the formatted date
		 * @param Export_Generator $generator generator instance
		 */
		$date = apply_filters( "wc_customer_order_export_{$this->output_type}_format_date", $date, $this );

		/**
		 * Filters the format of all dates in the export file.
		 *
		 * @since 5.0.0
		 *
		 * @param string $date the formatted date
		 * @param Export_Generator $generator generator instance
		 */
		return apply_filters( 'wc_customer_order_export_format_date', $date, $this );
	}


	/**
	 * Helper to run all decimals through formatting rules for easy format changes.
	 *
	 * @see wc_format_decimal
	 *
	 * @since 5.2.0
	 *
	 * @param float|string $number
	 * @param mixed $decimal_points number of decimal points to use, false to avoid all rounding
	 * @return string the formatted number output
	 */
	protected function format_decimal( $number, $decimal_points = false ) {

		$decimal_points = false === $decimal_points ? wc_get_price_decimals() : $decimal_points;

		/**
		 * Filters the decimal separator of all output types.
		 *
		 * @since 5.2.0
		 *
		 * @param string $separator the default decimal separator
		 * @param string $output_type the file output type (csv, xml)
		 * @param float|string $number the number to be formatted
		 * @param Export_Generator $generator generator instance
		 */
		$decimal_separator = apply_filters( "wc_customer_order_export_decimal_separator", '.', $this->output_type, $number, $this );

		/**
		 * Filters the thousands separator of all output types.
		 *
		 * @since 5.2.0
		 *
		 * @param string $separator the default thousands separator
		 * @param string $output_type the file output type (csv, xml)
		 * @param float|string $number the number to be formatted
		 * @param Export_Generator $generator generator instance
		 */
		$thousands_separator = apply_filters( "wc_customer_order_export_thousands_separator", '', $this->output_type, $number, $this );

		return is_numeric( $number ) ? number_format( $number, $decimal_points, $decimal_separator, $thousands_separator ) : $number;
	}


	/**
	 * Gets user from order.
	 *
	 * @since 5.0.0
	 *
	 * @param \WC_Order $order
	 * @return \stdClass
	 */
	protected function get_user_from_order( $order ) {

		// create blank user
		$user = new \stdClass();

		if ( $order ) {

			$order_date = is_callable( [
				$order->get_date_created(),
				'date'
			] ) ? $order->get_date_created()->date( 'Y-m-d H:i:s' ) : null;

			// set properties on user
			$user->ID         = 0;
			$user->first_name = $order->get_billing_first_name( 'edit' );
			$user->last_name  = $order->get_billing_last_name( 'edit' );
			$user->user_login = '';
			$user->user_email = $order->get_billing_email( 'edit' );
			$user->user_pass  = '';
			// don't format this date, it will be formatted later
			$user->user_registered     = $order_date;
			$user->billing_first_name  = $order->get_billing_first_name( 'edit' );
			$user->billing_last_name   = $order->get_billing_last_name( 'edit' );
			$user->billing_full_name   = $order->get_formatted_billing_full_name();
			$user->billing_company     = $order->get_billing_company( 'edit' );
			$user->billing_email       = $order->get_billing_email( 'edit' );
			$user->billing_phone       = $order->get_billing_phone( 'edit' );
			$user->billing_address_1   = $order->get_billing_address_1( 'edit' );
			$user->billing_address_2   = $order->get_billing_address_2( 'edit' );
			$user->billing_postcode    = $order->get_billing_postcode( 'edit' );
			$user->billing_city        = $order->get_billing_city( 'edit' );
			$user->billing_state       = $this->get_localized_state( $order->get_billing_country( 'edit' ), $order->get_billing_state( 'edit' ) );
			$user->billing_state_code  = $order->get_billing_state( 'edit' );
			$user->billing_country     = $order->get_billing_country( 'edit' );
			$user->shipping_first_name = $order->get_shipping_first_name( 'edit' );
			$user->shipping_last_name  = $order->get_shipping_last_name( 'edit' );
			$user->shipping_full_name  = $order->get_formatted_shipping_full_name();
			$user->shipping_company    = $order->get_shipping_company( 'edit' );
			$user->shipping_address_1  = $order->get_shipping_address_1( 'edit' );
			$user->shipping_address_2  = $order->get_shipping_address_2( 'edit' );
			$user->shipping_postcode   = $order->get_shipping_postcode( 'edit' );
			$user->shipping_city       = $order->get_shipping_city( 'edit' );
			$user->shipping_state      = $this->get_localized_state( $order->get_shipping_country( 'edit' ), $order->get_shipping_state( 'edit' ) );
			$user->shipping_state_code = $order->get_shipping_state( 'edit' );
			$user->shipping_country    = $order->get_shipping_country( 'edit' );
		}

		return $user;
	}


	/**
	 * Gets all category names to match against coupon category IDs.
	 *
	 * @since 5.0.0
	 *
	 * @return array
	 */
	private function get_category_names() {

		$categories     = get_categories( [ 'taxonomy' => 'product_cat' ] );
		$category_names = [];

		foreach ( $categories as $category ) {
			$category_names[ $category->term_id ] = $category->category_nicename;
		}

		return $category_names;
	}


	/**
	 * Creates array of product SKUs allowed by a coupon.
	 *
	 * @since 5.0.0
	 *
	 * @param \WC_Coupon $coupon
	 * @return array
	 */
	protected function get_allowed_products_skus( $coupon ) {

		$products = [];

		// get this coupon's included products
		$product_ids = $coupon->get_product_ids();

		foreach ( $product_ids as $product_id ) {

			$product = wc_get_product( $product_id );

			// this might be the case of a product that has been deleted
			if ( ! $product instanceof \WC_Product ) {
				continue;
			}

			// only include products with a valid SKU
			// all products will appear in 'ProductIDs'
			if ( ! empty( $product->get_sku() ) ) {
				$products[] = $product->get_sku();
			}
		}

		return $products;
	}


	/**
	 * Creates array of product SKUs excluded by a coupon.
	 *
	 * @since 5.0.0
	 *
	 * @param \WC_Coupon $coupon
	 * @return array
	 */
	protected function get_excluded_products_skus( $coupon ) {

		$excluded_products = [];

		// get this coupon's excluded products
		$excluded_product_ids = $coupon->get_excluded_product_ids();

		foreach ( $excluded_product_ids as $excluded_product_id ) {

			$product = wc_get_product( $excluded_product_id );

			// this might be the case of a product that has been deleted
			if ( ! $product instanceof \WC_Product ) {
				continue;
			}

			// only include products with a valid SKU
			// all products will appear in 'ProductIDs'
			if ( ! empty( $product->get_sku() ) ) {
				$excluded_products[] = $product->get_sku();
			}
		}

		return $excluded_products;
	}


	/**
	 * Creates array of product category names allowed by a coupon.
	 *
	 * @since 5.0.0
	 *
	 * @param \WC_Coupon $coupon
	 * @return array
	 */
	protected function get_allowed_product_category_names( $coupon ) {

		$product_categories = [];

		// get this coupon's included product categories
		$product_category_ids = $coupon->get_product_categories();
		$category_names       = $this->get_category_names();

		foreach ( $product_category_ids as $product_category_id ) {
			$product_categories[] = $category_names[ $product_category_id ];
		}

		return $product_categories;
	}


	/**
	 * Creates array of product category names excluded by a coupon.
	 *
	 * @since 5.0.0
	 *
	 * @param \WC_Coupon $coupon
	 * @return array
	 */
	protected function get_excluded_product_category_names( $coupon ) {

		$excluded_product_categories = [];

		// get this coupon's excluded product categories
		$excluded_product_category_ids = $coupon->get_excluded_product_categories();
		$category_names                = $this->get_category_names();

		foreach ( $excluded_product_category_ids as $excluded_product_category_id ) {
			$excluded_product_categories[] = $category_names[ $excluded_product_category_id ];
		}

		return $excluded_product_categories;
	}


	/**
	 * Gets VAT meta data from an order.
	 *
	 * @since 5.3.0
	 *
	 * @param \WC_Order $order
	 * @return string
	 */
	protected function get_vat_number( \WC_Order $order ) : string {

		$vat_meta = '';

		// find VAT number if one exists for the order
		$vat_number_meta_keys = [
			'_vat_number',         // EU VAT number (legacy?)
			'_billing_vat_number', // EU VAT number
			'VAT Number',          // Legacy EU VAT number
			'vat_number',          // Taxamo
		];

		foreach ( $vat_number_meta_keys as $meta_key ) {

			if ( $order->meta_exists( $meta_key ) ) {

				$vat_meta = $order->get_meta( $meta_key );
				break;
			}
		}

		return is_string( $vat_meta ) ? $vat_meta : '';
	}


}

class_alias( Export_Generator::class, 'WC_Customer_Order_CSV_Export_Generator' );
