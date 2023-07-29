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

namespace SkyVerge\WooCommerce\CSV_Export\Export_Formats;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

/**
 * Export Format Definition abstract class
 *
 * @since 4.7.0
 */
abstract class Export_Format_Definition {


	/** @var string key */
	protected $key;

	/** @var string name */
	protected $name;

	/** @var string export type, one of `orders`, `customers` or `coupons` */
	protected $export_type;

	/** @var string output type, one of `csv` or `xml` */
	protected $output_type;

	/** @var array column mapping (for custom formats) */
	protected $mapping;

	/** @var bool include all meta as columns (for custom formats) */
	protected $include_all_meta;


	/**
	 * Initializes the export format definition.
	 *
	 * @since 4.7.0
	 *
	 * @param array $args {
	 *     An array of arguments.
	 *
	 *     @type string $key format key
	 *     @type string $name format name
	 *     @type string $export_type export type
	 *     @type string $output_type output type
	 *     @type array $mapping column mapping (custom formats)
	 *     @type bool $include_all_meta include all meta as columns (custom formats)
	 * }
	 */
	public function __construct( $args ) {

		$args = wp_parse_args( $args, [
			'export_type'      => '',
			'output_type'      => '',
			'name'             => '',
			'key'              => '',
			'mapping'          => [],
			'include_all_meta' => false,
		] );

		$this->export_type      = $args['export_type'];
		$this->output_type      = $args['output_type'];
		$this->name             = stripslashes( $args['name'] );
		$this->key              = $args['key'];
		$this->include_all_meta = (bool) $args['include_all_meta'];

		$this->set_mapping( $args['mapping'] );
	}


	/**
	 * Gets the format key.
	 *
	 * @since 4.7.0
	 *
	 * @return string
	 */
	public function get_key() {
		return $this->key;
	}


	/**
	 * Gets the format name.
	 *
	 * @since 4.7.0
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}


	/**
	 * Gets the export type (`orders`, `customers` or `coupons`).
	 *
	 * @since 4.7.0
	 *
	 * @return string
	 */
	public function get_export_type() {
		return $this->export_type;
	}


	/**
	 * Gets the output type (`csv` or `xml`).
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	public function get_output_type() {
		return $this->output_type;
	}


	/**
	 * Sets column mapping (for custom formats).
	 *
	 * @since 5.0.14
	 *
	 * @param $mapping
	 */
	public function set_mapping( $mapping ) {

		$this->mapping = is_array( $mapping ) ? $mapping : [];

		foreach ( array_keys( $this->mapping ) as $i ) {
			if ( array_key_exists( 'name', $this->mapping[ $i ] ) ) {
				$this->mapping[ $i ]['name'] = stripslashes( $this->mapping[ $i ]['name'] );
			}
		}
	}


	/**
	 * Gets column mapping (for custom formats).
	 *
	 * @since 5.0.0
	 *
	 * @return array
	 */
	public function get_mapping() {

		return $this->mapping;
	}


	/**
	 * Gets include all meta (for custom formats).
	 *
	 * @since 5.0.0
	 *
	 * @return bool
	 */
	public function get_include_all_meta() {
		return $this->include_all_meta;
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

		return false;
	}


	/**
	 * Gets the format's available data sources.
	 *
	 * @since 5.0.0
	 *
	 * @return array
	 */
	public static function get_data_sources() {

		return [];
	}


	/**
	 * Gets the format's predefined data, if any.
	 *
	 * @since 5.0.0
	 *
	 * @return array
	 */
	public static function get_predefined_formats() {

		return [];
	}


	/**
	 * Returns an array definition, for compatibility.
	 *
	 * @since 4.7.0
	 *
	 * @return array
	 */
	public function to_array() {

		return [
			'key'              => $this->get_key(),
			'name'             => $this->get_name(),
			'export_type'      => $this->get_export_type(),
			'output_type'      => $this->get_output_type(),
			'mapping'          => $this->get_mapping(),
			'include_all_meta' => $this->get_include_all_meta(),
		];
	}


	/**
	 * Generates a unique key, based on the format name.
	 *
	 * @since 4.7.0
	 *
	 * @param string $export_type
	 * @param string $format_name
	 * @return string
	 */
	public static function generate_unique_format_key( $export_type, $format_name ) {

		$possible_key = 'custom-' . sanitize_title( $format_name );

		// check if the key is already used and increment as needed
		while ( null !== wc_customer_order_csv_export()->get_formats_instance()->get_format_definition( $export_type, $possible_key, \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV ) ||
		        null !== wc_customer_order_csv_export()->get_formats_instance()->get_format_definition( $export_type, $possible_key, \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_XML ) ) {
			$possible_key = self::increment_format_key( $possible_key );
		}

		return $possible_key;
	}


	/**
	 * Increments format key.
	 *
	 * @since 4.7.0
	 *
	 * @param string key
	 * @return string
	 */
	private static function increment_format_key( $key ) {

		$i = 1;

		// check if the key already ends in "-number"
		$position = strrpos( $key, '-' );

		if ( $position !== false ) {
			$suffix = substr( $key, $position + 1 );

			if ( is_numeric( $suffix ) ) {
				// remove suffix from key
				$key = substr( $key, 0, $position );
				// get suffix numeric value
				$i = $suffix + 1;
			}
		}

		return $key . '-' . $i;
	}


}
