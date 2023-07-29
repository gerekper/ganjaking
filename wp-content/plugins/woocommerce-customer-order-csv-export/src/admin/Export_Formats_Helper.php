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

namespace SkyVerge\WooCommerce\CSV_Export\Admin;

use SkyVerge\WooCommerce\CSV_Export\Export_Formats\Export_Format_Definition;
use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Admin Export Formats helper class.
 *
 * @since 5.0.0
 */
class Export_Formats_Helper {


	/**
	 * Gets an array of export formats options.
	 *
	 * @since 5.0.0
	 *
	 * @param string $output_type output type, such as csv or xml
	 * @param string $export_type export type, such as orders or customers
	 * @param bool $use_legacy whether to make legacy formats available
	 * @return array
	 */
	public static function get_export_formats( $output_type, $export_type, $use_legacy = false ) {

		$predefined_optgroup_label = __( 'Predefined', 'woocommerce-customer-order-csv-export' );
		$custom_optgroup_label     = __( 'Custom', 'woocommerce-customer-order-csv-export' );
		$legacy_optgroup_label     = __( 'Legacy', 'woocommerce-customer-order-csv-export' );

		/** @var Export_Format_Definition[] $legacy_formats */
		$legacy_formats = [];

		$export_options = [
			$predefined_optgroup_label => [],
			$custom_optgroup_label     => [],
		];

		if ( $use_legacy ) {
			$export_options[ $legacy_optgroup_label ] = [];
		}

		/** @var Export_Format_Definition $formats_class */
		if ( $formats_class = wc_customer_order_csv_export()->get_formats_instance()->get_format_definition_class( $output_type ) ) {

			$predefined_formats = $formats_class::get_predefined_formats();

			if ( ! empty( $predefined_formats[ $export_type ] ) ) {

				/** @var Export_Format_Definition $format */
				foreach ( $predefined_formats[ $export_type ] as $format ) {

					// if a legacy format, save for later
					if ( Framework\SV_WC_Helper::str_starts_with( $format->get_key(), 'legacy' ) ) {
						$legacy_formats[] = $format;
						continue;
					}

					$export_options[ $predefined_optgroup_label ][ $format->get_key() ] = $format->get_name();
				}
			}
		}

		// custom formats
		$custom_formats = wc_customer_order_csv_export()->get_formats_instance()->get_custom_format_definitions( $export_type, $output_type );

		foreach ( $custom_formats as $custom_format_key => $custom_format ) {
			$export_options[ $custom_optgroup_label ][ $custom_format_key ] = $custom_format->get_name();
		}

		// since v4.6.4 only legacy installations will support legacy formats
		if ( $use_legacy ) {

			foreach ( $legacy_formats as $legacy_format ) {
				$export_options[ $legacy_optgroup_label ][ $legacy_format->get_key() ] = $legacy_format->get_name();
			}
		}

		// remove groups without options
		$export_options = array_filter( $export_options );

		return $export_options;
	}


}
