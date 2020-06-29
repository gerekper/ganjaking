<?php
/**
 * WooCommerce Customer/Order/Coupon CSV Import Suite
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon CSV Import Suite to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon CSV Import Suite for your
 * needs please refer to http://docs.woocommerce.com/document/customer-order-csv-import-suite/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

if ( ! class_exists( 'WP_Importer' ) ) return;

defined( 'ABSPATH' ) or exit;

/**
 * WooCommerce CSV Import Suite Importers loader
 *
 * @since 3.0.0
 */
class WC_CSV_Import_Suite_Importers {


	/** @var array Array of importer instances */
	private $importers = array();


	/**
	 * Get an importer Instance
	 *
	 * Loads the importer class and creates an instance of it.
	 * This function will cache instances, so calling it for the
	 * same type of importer will only create one instance and
	 * return the same instance on following calls.
	 *
	 * @since 3.0.0
	 * @param string $type Importer type.
	 * @return mixed Instance of the importer class or null on failure
	 */
	public function get_importer( $type ) {

		if ( ! $type ) {
			return null;
		}

		// if importer instance is not in the cache already, load & create it
		if ( ! isset( $this->importers[ $type ] ) ) {

			$this->load_wp_importer_api();

			require_once( wc_csv_import_suite()->get_plugin_path() . '/includes/class-wc-csv-import-suite-import-exception.php' );
			require_once( wc_csv_import_suite()->get_plugin_path() . '/includes/class-wc-csv-import-suite-parser.php' );
			require_once( wc_csv_import_suite()->get_plugin_path() . '/includes/class-wc-csv-import-suite-importer.php' );

			$importer = null;

			switch ( $type ) {

				case 'woocommerce_customer_csv':

					require_once( wc_csv_import_suite()->get_plugin_path() . '/includes/class-wc-csv-import-suite-customer-import.php' );
					$importer = 'WC_CSV_Import_Suite_Customer_Import';

				break;

				case 'woocommerce_coupon_csv':

					require_once( wc_csv_import_suite()->get_plugin_path() . '/includes/class-wc-csv-import-suite-coupon-import.php' );
					$importer = 'WC_CSV_Import_Suite_Coupon_Import';

				break;

				case 'woocommerce_order_csv':

					require_once( wc_csv_import_suite()->get_plugin_path() . '/includes/class-wc-csv-import-suite-order-import.php' );
					$importer = 'WC_CSV_Import_Suite_Order_Import';

				break;

			}

			/**
			 * Filter the loaded importer instance
			 *
			 * Allows 3rd parties to load their importer instances in a streamlined manner
			 *
			 * @since 3.0.0
			 * @param mixed $importer Importer instance, class name or null (if no matching importer was found)
			 * @param string $type Importer type, such as `woocommerce_customer_csv`
			 */
			$importer = apply_filters( 'wc_csv_import_suite_importer_class', $importer, $type );


			// create new importer instance
			if ( is_string( $importer ) ) {
				$importer = new $importer();
			}

			// cache the instance
			$this->importers[ $type ] = $importer;
		}

		return $this->importers[ $type ];
	}


	/**
	 * Load WP_Importer class
	 *
	 * @since 3.0.0
	 */
	private function load_wp_importer_api() {

		require_once ABSPATH . 'wp-admin/includes/import.php';

		if ( ! class_exists( 'WP_Importer' ) ) {

			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

			if ( is_readable( $class_wp_importer ) ) {
				require( $class_wp_importer );
			}
		}
	}


}
