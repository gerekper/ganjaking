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

use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

/**
 * The export batch handler class.
 *
 * @since 4.4.0
 */
class WC_Customer_Order_CSV_Export_Batch_Export_Handler extends Framework\SV_WP_Job_Batch_Handler {


	/**
	 * Handles an export job after processing a batch.
	 *
	 * Adds extra job properties specific to exports.
	 *
	 * @since 4.4.0
	 *
	 * @param object $job export object
	 * @return object $job export object
	 *
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	protected function process_job_status( $job ) {

		$job = parent::process_job_status( $job );

		if ( 'failed' === $job->status ) {
			throw new Framework\SV_WC_Plugin_Exception( $this->get_job_handler()->get_export_status_message( 'failed' ) );
		}

		$job->export_id = $job->id;

		if ( 'completed' === $job->status ) {

			if ( 'failed' === $job->transfer_status ) {
				throw new Framework\SV_WC_Plugin_Exception( $this->get_job_handler()->get_export_status_message( 'transfer-failed' ) );
			}

			$download_url = wp_nonce_url( admin_url(), 'download-export' );

			// return the download url for the exported file
			$job->download_url = add_query_arg( [
				'download_exported_file' => 1,
				'export_id'              => $job->export_id,
			], $download_url );
		}

		return $job;
	}


}
