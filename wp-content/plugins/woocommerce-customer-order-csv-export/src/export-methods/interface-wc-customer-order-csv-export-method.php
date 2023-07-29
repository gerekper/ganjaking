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
 * Customer/Order CSV Export Method Interface
 *
 * Defines a simple interface that export export-methods must implement to provide an export action
 *
 * @since 3.0.0
 */
interface WC_Customer_Order_CSV_Export_Method {


	/**
	 * This method should perform the export action, e.g. sending the file via email or
	 * uploading via FTP to a remote server
	 *
	 * @since 3.0.0
	 *
	 * @param \WC_Customer_Order_CSV_Export_Export|string $export the export object or a path to an export file
	 */
	public function perform_action( $export );


}
