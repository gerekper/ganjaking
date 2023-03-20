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

/**
 * Customer/Order CSV Export Data Store Factory
 *
 * Creates and return data store instances.
 *
 * @since 4.5.0
 */
class WC_Customer_Order_CSV_Export_Data_Store_Factory {


	/**
	 * Includes all necessary data store files.
	 *
	 * @since 4.5.0
	 *
	 * @param string $data_store_slug data store to load required files for
	 */
	public static function includes( $data_store_slug ) {

		$plugin_path = wc_customer_order_csv_export()->get_plugin_path();

		$includes = [

			'database' => [
				$plugin_path . '/src/data-stores/database/class-wc-customer-order-csv-export-data-store-database.php',
				$plugin_path . '/src/data-stores/database/class-wc-customer-order-csv-export-database-stream-iterator.php',
				$plugin_path . '/src/data-stores/database/class-wc-customer-order-csv-export-database-stream-wrapper.php',
			],

			'filesystem' => [
				$plugin_path . '/src/data-stores/filesystem/class-wc-customer-order-csv-export-data-store-filesystem.php',
			],
		];

		if ( isset( $includes[ $data_store_slug ] ) ) {

			foreach( $includes[ $data_store_slug ] as $file_path ) {

				if ( is_readable( $file_path ) ) {

					require_once( $file_path );
				}
			}
		}
	}


	/**
	 * Initializes and returns a data store.
	 *
	 * @since 4.5.0
	 *
	 * @param string $type data store type slug
	 * @param string $output_type such as csv or xml
	 * @return \WC_Customer_Order_CSV_Export_Data_Store|null instance of the data store
	 */
	public static function create( $type, $output_type ) {

		self::includes( $type );

		switch ( $type ) {

			case 'database':
				return new WC_Customer_Order_CSV_Export_Data_Store_Database();
			break;

			case 'filesystem':
				return new WC_Customer_Order_CSV_Export_Data_Store_Filesystem();
			break;

			default:

				/**
				 * Filters the data store to allow actors to instantiate and return a custom data store.
				 *
				 * @since 5.0.0
				 *
				 * @param string $type data store type slug
				 */
				$data_store = apply_filters( "wc_customer_order_export_{$output_type}_custom_data_store", $type );

				/**
				 * Filters the data store to allow actors to instantiate and return a custom data store.
				 *
				 * @since 5.0.0
				 *
				 * @param string $type data store type slug
				 * @param string $output_type such as csv or xml
				 */
				$data_store = apply_filters( 'wc_customer_order_export_custom_data_store', $data_store, $output_type );

				return $data_store instanceof WC_Customer_Order_CSV_Export_Data_Store ? $data_store : null;

			break;
		}
	}


}
