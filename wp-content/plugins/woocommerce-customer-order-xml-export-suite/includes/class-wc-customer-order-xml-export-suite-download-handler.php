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
 * Customer/Order XML Export Suite Download Handler
 *
 * Based on \WC_Download_Handler
 *
 * @since 2.0.0
 */
class WC_Customer_Order_XML_Export_Suite_Download_Handler {


	/**
	 * Initialize the download handler class
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		if ( isset( $_GET['download_exported_xml_file'] ) && isset( $_GET['export_id'] ) ) {
			add_action( 'init', array( $this, 'download_exported_file' ) );
		}

		if ( isset( $_GET['view_exported_xml_file'] ) && isset( $_GET['export_id'] ) ) {
			add_action( 'init', array( $this, 'view_exported_xml_file' ) );
		}
	}


	/**
	 * Downloads an exported file.
	 *
	 * @since 2.0.0
	 */
	public function download_exported_file() {

		check_admin_referer( 'download-export' );

		if ( ! current_user_can( 'manage_woocommerce_xml_exports' ) ) {
			wp_die( __( 'You do not have the proper permissions to download this file.', 'woocommerce-customer-order-xml-export-suite' ) );
		}

		$export = wc_customer_order_xml_export_suite_get_export( wc_clean( $_GET['export_id'] ) );

		if ( ! $export ) {
			$this->download_error( __( 'Export not found', 'woocommerce-customer-order-xml-export-suite' ) );
		}

		/**
		 * Allow actors to change the export filename before download
		 *
		 * @since 2.0.0
		 * @param string $filename
		 * @param string $export_id
		 */
		$filename = apply_filters( 'wc_customer_order_xml_export_suite_file_download_filename', $export->get_filename(), $export->get_id() );

		header( 'Content-type: text/xml' );
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
	 * Outputs the contents of an exported XML file to screen.
	 *
	 * @since 2.0.0
	 */
	public function view_exported_xml_file() {

		check_admin_referer( 'view-export' );

		if ( ! current_user_can( 'manage_woocommerce_xml_exports' ) ) {
			wp_die( __( 'You do not have the proper permissions to view this file.', 'woocommerce-customer-order-xml-export-suite' ) );
		}

		$export = wc_customer_order_xml_export_suite_get_export( $_GET['export_id'] );

		if ( ! $export ) {

			$this->download_error( __( 'Export not found', 'woocommerce-customer-order-xml-export-suite' ) );
		}

		header('Content-type: application/xml');

		echo $export->get_output();

		exit;
	}


	/**
	 * Die with an error message if the download fails.
	 *
	 * @since 2.0.0
	 * @param  string $message
	 * @param  string  $title
	 * @param  integer $status
	 */
	private function download_error( $message, $title = '', $status = 404 ) {
		wp_die( $message, $title, array( 'response' => $status ) );
	}

}
