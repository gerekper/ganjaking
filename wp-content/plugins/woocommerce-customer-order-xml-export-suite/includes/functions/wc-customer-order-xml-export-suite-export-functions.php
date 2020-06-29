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
 * Gets an export object for the given export ID or stdClass job object.
 *
 * @since 2.4.0
 *
 * @param string|object $export the export object ID or stdClass background job object
 * @return \WC_Customer_Order_XML_Export_Suite_Export|null the export object or null if not found
 */
function wc_customer_order_xml_export_suite_get_export( $export ) {

	// sanity check
	if ( $export instanceof WC_Customer_Order_XML_Export_Suite_Export ) {
		return $export;
	}

	if ( is_string( $export ) || $export instanceof stdClass ) {

		try {

			return new WC_Customer_Order_XML_Export_Suite_Export( $export );

		} catch( SV_WC_Plugin_Exception $e ) {}

	}

	return null;
}
