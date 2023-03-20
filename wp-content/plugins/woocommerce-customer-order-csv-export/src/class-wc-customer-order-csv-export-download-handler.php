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
 * Customer/Order CSV Export Download Handler
 *
 * Based on WC_Download_Handler
 *
 * @since 4.0.0
 */
class WC_Customer_Order_CSV_Export_Download_Handler {


	/**
	 * Initialize the download handler class
	 *
	 * @since 4.0.0
	 */
	public function __construct() {

		if ( isset( $_GET['download_exported_file'] ) && isset( $_GET['export_id'] ) ) {
			add_action( 'init', [ $this, 'download_exported_file' ] );
		}
	}


	/**
	 * Download an exported file
	 *
	 * @since 4.0.0
	 */
	public function download_exported_file() {

		check_admin_referer( 'download-export' );

		if ( ! current_user_can( 'manage_woocommerce_csv_exports' ) ) {
			wp_die( __( 'You do not have the proper permissions to download this file.', 'woocommerce-customer-order-csv-export' ) );
		}

		$export = wc_customer_order_csv_export_get_export( wc_clean( $_GET['export_id'] ) );

		if ( ! $export ) {
			$this->download_error( __( 'Export not found', 'woocommerce-customer-order-csv-export' ) );
		}

		$output_type = $export->get_output_type();

		$filename = $export->get_filename();

		/**
		 * Filters the export file name before download for the given output type.
		 *
		 * @since 5.0.0
		 *
		 * @param string $filename
		 * @param string $export_id
		 */
		$filename = apply_filters( "wc_customer_order_export_{$output_type}_file_download_filename", $filename, $export->get_id() );

		/**
		 * Filters the export file name before download.
		 *
		 * @since 5.0.0
		 *
		 * @param string $filename
		 * @param string $export_id
		 */
		$filename = apply_filters( 'wc_customer_order_export_file_download_filename', $filename, $export->get_id() );

		// we are intentionally using text/xml here to prevent a console warning
		$content_type = WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV === $output_type ? 'text/csv' : 'text/xml';

		header( 'Content-type: ' . $content_type );
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Pragma: public' );

		$file_size = $export->get_file_size();

		if ( $file_size && 0 < $file_size ) {
			header( 'Content-Length: ' . $file_size );
		}

		$output_resource = fopen( 'php://output', 'w' );

		$export->stream_output_to_resource( $output_resource );

		fclose( $output_resource );

		exit();
	}


	/**
	 * Die with an error message if the download fails.
	 *
	 * @since 4.0.0
	 * @param  string $message
	 * @param  string  $title
	 * @param  integer $status
	 */
	private function download_error( $message, $title = '', $status = 404 ) {
		wp_die( $message, $title, [ 'response' => $status ] );
	}

}
