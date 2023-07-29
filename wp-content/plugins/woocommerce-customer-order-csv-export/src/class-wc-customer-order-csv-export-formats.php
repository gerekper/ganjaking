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

defined( 'ABSPATH' ) or exit;

use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore;
use SkyVerge\WooCommerce\CSV_Export\Export_Formats;
use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

/**
 * Customer/Order CSV Export Formats
 *
 * Defines different export formats and provides a base set of
 * data sources for the formats.
 *
 * @since 4.0.0
 */
class WC_Customer_Order_CSV_Export_Formats {


	/** @var array $formats export formats container **/
	private $formats;

	/** @var array $all_meta_keys container for export types **/
	private $all_meta_keys = [];


	/**
	 * Gets an export format definition object.
	 *
	 * @since 4.7.0
	 *
	 * @param string $export_type Export type, such as `orders` or `customers`
	 * @param string $format Export format key, such as `default` or `import`
	 * @param string $output_type Output type, either `csv` or `xml`
	 * @return Export_Formats\Export_Format_Definition|null format definition object, or null if not found
	 */
	public function get_format_definition( $export_type, $format, $output_type = WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV ) {

		$object = null;

		// on first call, load the built-in & custom formats
		if ( null === $this->formats ) {
			$this->load_formats();
		}

		// Find the requested format
		if ( isset( $this->formats[ $output_type ][ $export_type ][ $format ] ) ) {
			$object = $this->formats[ $output_type ][ $export_type ][ $format ];
		}

		/**
		 * Allows actors to change the format definition object.
		 *
		 * @since 4.7.0
		 *
		 * @param Export_Formats\Export_Format_Definition|null $object Format definition object, or null if not found
		 * @param string $export_type Export type, such as `orders` or `customers`
		 * @param string $format Export format, such as `default` or `import`
		 */
		return apply_filters( 'wc_customer_order_export_format_definition_object', $object, $export_type, $format );
	}


	/**
	 * Gets an export format.
	 *
	 * @since 4.0.0
	 *
	 * @param string $export_type Export type, such as `orders` or `customers`
	 * @param string $format Export format, such as `default` or `import`
	 * @param string $output_type Output type, either `csv` or `xml`
	 * @return array|null format definition, or null if not found
	 */
	public function get_format( $export_type, $format, $output_type = WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV ) {

		$definition = null;

		if ( $object = $this->get_format_definition( $export_type, $format, $output_type ) ) {
			$definition = $object->to_array();
		}

		// If this is a custom format, pass 'custom' as format key for backwards compatibility
		$format = Framework\SV_WC_Helper::str_starts_with( $format, 'custom-' ) ? 'custom' : $format;

		/**
		 * Filters the format definition before exporting.
		 *
		 * @since 5.0.0
		 *
		 * @param array|null $definition Format definition, or null if not found
		 * @param string $export_type Export type, such as `orders` or `customers`
		 * @param string $format Export format, such as `default` or `import`
		 */
		$definition = apply_filters( "wc_customer_order_export_{$output_type}_format_definition", $definition, $export_type, $format );

		/**
		 * Allow actors to change the format definition
		 *
		 * @since 5.0.0
		 *
		 * @param array|null $definition Format definition, or null if not found
		 * @param string $export_type Export type, such as `orders` or `customers`
		 * @param string $format Export format, such as `default` or `import`
		 * @param string $output_type Export type, such as `csv` or `xml`
		 */
		return apply_filters( 'wc_customer_order_export_format_definition', $definition, $export_type, $format, $output_type );
	}


	/**
	 * Gets export formats for the given export and output type.
	 *
	 * @since 4.0.0
	 *
	 * @param string $export_type export type, such as `orders` or `customers`
	 * @param string $output_type output type, such as csv or xml
	 * @return array
	 */
	public function get_formats( $export_type, $output_type = WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV ) {

		if ( null === $this->formats ) {
			$this->load_formats();
		}

		$formats = [];

		if ( ! empty( $this->formats[ $output_type ][ $export_type ] ) ) {

			foreach ( array_keys( $this->formats[ $output_type ][ $export_type ] ) as $format_key ) {
				$formats[ $format_key ] = $this->get_format( $export_type, $format_key, $output_type );
			}
		}

		return $formats;
	}


	/**
	 * Gets a custom format definition.
	 *
	 * @since 5.0.0
	 *
	 * @param string $export_type export type, like orders or customers
	 * @param string $key format key
	 * @return Export_Formats\Export_Format_Definition|null
	 */
	public function get_custom_format_definition( $export_type, $key ) {

		$key = sanitize_title( $key );

		$formats = $this->get_custom_format_definitions( $export_type );

		return ! empty( $formats[ $key ] ) ? $formats[ $key ] : null;
	}


	/**
	 * Gets custom export formats for the given export type.
	 *
	 * @since 4.7.0
	 *
	 * @param string $export_type Export type, such as `orders` or `customers`
	 * @param string $output_type Output type, either `csv` or `xml`
	 * @return Export_Formats\Export_Format_Definition[]
	 */
	public function get_custom_format_definitions( $export_type, $output_type = '' ) {

		$custom_format_definitions = [];

		$custom_formats = get_option( 'wc_customer_order_export_' . $export_type . '_custom_formats', [] );

		foreach ( $custom_formats as $custom_format_key => $custom_format_data ) {

			// if a specific output type is desired, skip others
			if ( $output_type && $output_type !== $custom_format_data['output_type'] ) {
				continue;
			}

			if ( $format_class = $this->get_format_definition_class( $custom_format_data['output_type'], $export_type ) ) {
				$custom_format_definitions[ $custom_format_key ] = new $format_class( $custom_format_data );
			}
		}

		return $custom_format_definitions;
	}


	/**
	 * Creates or updates a custom format in the database.
	 *
	 * @since 4.7.0
	 *
	 * @param string $export_type Export type, such as `orders` or `customers`
	 * @param Export_Formats\Export_Format_Definition $custom_format Custom format object
	 * @return bool
	 */
	public function save_custom_format( $export_type, $custom_format ) {

		$option_name        = 'wc_customer_order_export_' . $export_type . '_custom_formats';
		$all_custom_formats = get_option( $option_name, [] );

		$custom_format_key = sanitize_title( $custom_format->get_key() );

		$all_custom_formats[ $custom_format_key ] = $custom_format->to_array();

		$updated = update_option( $option_name, $all_custom_formats );

		// reload formats
		$this->load_formats();

		return $updated;
	}


	/**
	 * Deletes a custom format from the database.
	 *
	 * @since 4.7.0
	 *
	 * @param string $export_type export type, such as `orders` or `customers`
	 * @param string $custom_format_key custom format key
	 * @return bool
	 */
	public function delete_custom_format( $export_type, $custom_format_key ) {

		$option_name = 'wc_customer_order_export_' . $export_type . '_custom_formats';

		$all_custom_formats = get_option( $option_name, [] );

		unset( $all_custom_formats[ sanitize_title( $custom_format_key ) ] );

		$updated = update_option( $option_name, $all_custom_formats );

		// reload formats
		$this->load_formats();

		return $updated;
	}


	/**
	 * Initializes the built-in formats and loads the custom
	 * formats to memory
	 *
	 * @since 4.0.0
	 */
	private function load_formats() {

		$output_types = wc_customer_order_csv_export()->get_output_types();
		$export_types = wc_customer_order_csv_export()->get_export_types();

		foreach ( $output_types as $output_type => $name ) {

			if ( $class = $this->get_format_definition_class( $output_type ) ) {
				$built_in_formats = $class::get_predefined_formats();
			} else {
				$built_in_formats = [];
			}

			$this->formats[ $output_type ] = $built_in_formats;

			foreach ( $export_types as $export_type => $formats ) {

				$custom_formats = $this->get_custom_format_definitions( $export_type, $output_type );

				$this->formats[ $output_type ][ $export_type ] = ! empty( $this->formats[ $output_type ][ $export_type ] ) ? array_merge( $this->formats[ $output_type ][ $export_type ], $custom_formats ) : $custom_formats;
			}
		}
	}


	/**
	 * Gets all meta keys for the given export type.
	 *
	 * @since 4.0.0
	 *
	 * @param string $export_type
	 * @param string $output_type
	 * @return array
	 */
	public function get_all_meta_keys( $export_type, $output_type = \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV ) {

		if ( ! isset( $this->all_meta_keys[ $export_type ] ) ) {

			$meta_keys = [];

			if ( WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS === $export_type ) {
				$meta_keys = $this->get_user_meta_keys();
			} elseif ( WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS === $export_type ) {
				$meta_keys = $this->get_post_meta_keys( 'shop_order' );
			} elseif ( WC_Customer_Order_CSV_Export::EXPORT_TYPE_COUPONS === $export_type ) {
				$meta_keys = $this->get_post_meta_keys( 'shop_coupon' );
			}

			if ( $format_class = $this->get_format_definition_class( $output_type, $export_type ) ) {

				// exclude meta with dedicated columns from all meta
				foreach ( $meta_keys as $key => $meta_key ) {

					/**
					 * Filters whether the meta has a dedicated source.
					 *
					 * @since 5.0.0
					 *
					 * @param bool $has_dedicated_source
					 * @param string $meta_key meta key to check
					 * @param string $export_type export type, such as orders or customers
					 */
					$has_dedicated_source = apply_filters( 'wc_customer_order_export_meta_has_dedicated_source', $format_class::meta_has_dedicated_source( $meta_key, $export_type ), $meta_key, $export_type );

					if ( $has_dedicated_source ) {
						unset( $meta_keys[ $key ] );
					}
				}
			}

			/**
			 * Allow actors to adjust the returned meta keys for an export type
			 *
			 * This filter is useful for providing meta keys for custom export types.
			 *
			 * @since 4.0.0
			 * @param array $meta_keys
			 * @param string $export_type
			 */
			$this->all_meta_keys[ $export_type ] = apply_filters( 'wc_customer_order_export_all_meta_keys', $meta_keys, $export_type );
		}

		return $this->all_meta_keys[ $export_type ];
	}


	/**
	 * Get a list of all the meta keys for a post type. This includes all public, private,
	 * used, no-longer used etc. They will be sorted once fetched.
	 *
	 * @since 4.0.0
	 * @param string $post_type Optional. Defaults to `shop_order`
	 * @return array
	 */
	public function get_post_meta_keys( string $post_type = 'shop_order' ): array {

		global $wpdb;

		if ( 'shop_order' === $post_type && Framework\SV_WC_Plugin_Compatibility::is_hpos_enabled()) {

			$orders_table = OrdersTableDataStore::get_orders_table_name();
			$meta_table   = OrdersTableDataStore::get_meta_table_name();

			$meta = $wpdb->get_col( "
				SELECT DISTINCT om.meta_key
				FROM {$meta_table} AS om
				LEFT JOIN {$orders_table} AS o ON o.id = om.order_id
			" );
		} else {

			$meta = $wpdb->get_col( $wpdb->prepare( "
				SELECT DISTINCT pm.meta_key
				FROM {$wpdb->postmeta} AS pm
				LEFT JOIN {$wpdb->posts} AS p ON p.ID = pm.post_id
				WHERE p.post_type = %s
			", $post_type ) );
		}

		sort( $meta );

		return $meta;
	}


	/**
	 * Get a list of all the meta keys for users. They will be sorted once fetched.
	 *
	 * @since 4.0.0
	 * @return array
	 */
	public function get_user_meta_keys() {

		global $wpdb;

		$meta = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->usermeta}" );

		sort( $meta );

		return $meta;
	}


	/**
	 * Gets the a format definition class name for the given export and output type.
	 *
	 * @since 5.0.0
	 *
	 * @param string $export_type export type
	 * @param string $output_type output type
	 * @return string
	 */
	public function get_format_definition_class( $output_type, $export_type = '' ) {

		switch ( $output_type ) {

			case \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV:
				$namespace = '\\SkyVerge\\WooCommerce\\CSV_Export\\Export_Formats\\CSV';
			break;

			case \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_XML:
				$namespace = '\\SkyVerge\\WooCommerce\\CSV_Export\\Export_Formats\\XML';
			break;

			default:
				$namespace = '';
		}

		$class_name = '';

		switch ( $export_type ) {

			case \WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS:
				$class_name = 'Orders_Export_Format_Definition';
			break;

			case \WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS:
				$class_name = 'Customers_Export_Format_Definition';
			break;

			case \WC_Customer_Order_CSV_Export::EXPORT_TYPE_COUPONS:
				$class_name = 'Coupons_Export_Format_Definition';
			break;

			default:

				if ( \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV === $output_type ) {
					$class_name = 'CSV_Export_Format_Definition';
				} elseif ( \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_XML === $output_type ) {
					$class_name = 'XML_Export_Format_Definition';
				}
		}

		$class_name = $namespace . '\\' . $class_name;

		$class_name = apply_filters( 'wc_customer_order_coupon_export_export_format_class_name', $class_name, $export_type, $output_type );

		return class_exists( $class_name ) && is_subclass_of( $class_name, Export_Formats\Export_Format_Definition::class ) ? $class_name : false;
	}


	/** Deprecated methods ********************************************************************************************/


	/**
	 * Get column data options for the export type. This method is useful
	 * for getting a base list of columns/column data options for
	 * an export format, for example to be used in the column mapper UI.
	 *
	 * @since 4.0.0
	 * @deprecated 5.0.0
	 *
	 * @param string $export_type Export type
	 * @return array
	 */
	public function get_column_data_options( $export_type ) {

		wc_deprecated_function( __METHOD__, '5.0.0' );

		$options = [];

		if ( WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS === $export_type ) {
			$options = Export_Formats\CSV\Orders_Export_Format_Definition::get_data_sources();
		} elseif ( WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS === $export_type ) {
			$options = Export_Formats\CSV\Customers_Export_Format_Definition::get_data_sources();
		} elseif ( WC_Customer_Order_CSV_Export::EXPORT_TYPE_COUPONS === $export_type ) {
			$options = Export_Formats\CSV\Coupons_Export_Format_Definition::get_data_sources();
		}

		/**
		 * Allow actors to adjust the available export column data options
		 *
		 * This filter is especially useful for providing options for custom export types
		 *
		 * @since 4.0.0
		 * @deprecated 5.0.0
		 *
		 * @param array $options column data options
		 * @param string $export_type export type
		 */
		return apply_filters( 'wc_customer_order_export_format_column_data_options', $options, $export_type );
	}


}
