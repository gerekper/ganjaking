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

namespace SkyVerge\WooCommerce\CSV_Export\Automations;

use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * A factory for the Automation object.
 *
 * @since 5.0.0
 */
class Automation_Factory {


	/**
	 * Returns an array of all automations, optionally filtered by argument values.
	 *
	 * @see Automation::$data for a list of possible keys to filter by
	 *
	 * @since 5.0.0
	 *
	 * @param array $args (Optional) filter by automation data keys
	 * @return Automation[]
	 */
	public static function get_automations( $args = [] ) {

		$args        = is_array( $args ) ? $args : [];
		$data_store  = new Automation_Data_Store_Options();
		$automations = [];

		foreach ( $data_store->get_automations_data() as $automation_data ) {

			foreach ( $args as $key => $value ) {

				if ( ! isset( $automation_data[ $key ] ) || $value !== $automation_data[ $key ] ) {
					continue 2;
				}
			}

			$automations[ $automation_data['id'] ] = new Automation( $automation_data );
		}

		return $automations;
	}


	/**
	 * Returns an array of Automations for the given export type.
	 *
	 * @since 5.0.0
	 *
	 * @param string $export_type
	 * @return Automation[]
	 */
	public static function get_automations_by_export_type( $export_type ) {

		return self::get_automations( [ 'export_type' => $export_type ] );
	}


	/**
	 * Gets an automation by its ID.
	 *
	 * @since 5.0.0
	 *
	 * @param string $automation_id the ID of the automation
	 * @return Automation|null Automation instance or null if not found
	 */
	public static function get_automation( $automation_id ) {

		$automation = null;

		try {
			$automation = new Automation( $automation_id );
		} catch ( Framework\SV_WC_Plugin_Exception $e ) {}

		return $automation;
	}


}
