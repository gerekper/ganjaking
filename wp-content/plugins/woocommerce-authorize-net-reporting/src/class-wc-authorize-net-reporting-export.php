<?php
/**
 * WooCommerce Authorize.Net Reporting
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Authorize.Net Reporting to newer
 * versions in the future. If you wish to customize WooCommerce Authorize.Net Reporting for your
 * needs please refer to http://www.skyverge.com/contact/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_5 as Framework;

/**
 * Export Class.
 *
 * Handles exporting data.
 *
 * @since 1.0
 */
class WC_Authorize_Net_Reporting_Export {


	/** @var string the export filename */
	private $filename;


	/**
	 * Sets start/end dates.
	 *
	 * @since 1.0
	 *
	 * @param null|string $start_date
	 * @param null|string $end_date
	 */
	public function __construct( $start_date = null, $end_date = null ) {

		$this->start_date = $start_date;

		$this->end_date = $end_date;

		if ( ! empty( $start_date ) && ! empty( $end_date ) ) {
			$date_range = date( 'Y_m_d', strtotime( $start_date ) ) . '_' . date( 'Y_m_d', strtotime( $end_date ) );
		} else {
			$date_range = date( 'Y_m_d', strtotime( 'yesterday' ) );
		}

		$this->filename = apply_filters( 'wc_authorize_net_reporting_export_filename', sprintf( 'transaction_details_%s.csv', $date_range ) );
	}


	/**
	 * Downloads the exported transaction details as a CSV.
	 *
	 * @since 1.0
	 */
	public function download() {

		$transaction_details = $this->get_transaction_details();

		// if no transactions returned, add a message and redirect
		if ( 0 === count( $transaction_details ) ) {

			wc_authorize_net_reporting()->get_admin_instance()->message_handler->add_message( __( 'No transactions found for that date range', 'woocommerce-authorize-net-reporting' ) );

			wp_redirect( wp_get_referer() );
		}

		// try to set unlimited script timeout
		@set_time_limit( 0 );

		// set headers for download
		header( apply_filters( 'wc_authorize_net_reporting_download_content_type', 'Content-Type: text/csv; charset=UTF-8' ) );
		header( sprintf( 'Content-Disposition: attachment; filename="%s"', $this->filename ) );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		// allow plugins to add additional headers
		do_action( 'wc_authorize_net_reporting_download_after_headers' );

		// clear the output buffer
		@ini_set( 'zlib.output_compression', 'Off' );
		@ini_set( 'output_buffering', 'Off' );
		@ini_set( 'output_handler', '' );

		// open the output buffer for writing
		$fp = fopen( 'php://output', 'w' );

		// write header
		// @see WC_Authorize_Net_Reporting_API_Response::get_transaction_fields() to filter headers
		fputcsv( $fp, \WC_Authorize_Net_Reporting_API_Response::get_transaction_fields() );

		// write each transaction
		foreach ( $transaction_details as $transaction_detail ) {
			fputcsv( $fp, $transaction_detail );
		}

		// close the output buffer
		fclose( $fp );

		exit;
	}


	/**
	 * Emails yesterday's transaction details CSV to the specified recipients.
	 *
	 * @since 1.0
	 *
	 * @param string|array $recipients
	 *
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function email( $recipients ) {

		$transaction_details = $this->get_transaction_details();

		// if no transactions returned, exit
		if ( 0 === count( $transaction_details ) ) {
			return;
		}

		$previous_day = date( 'Y-m-d', strtotime( 'yesterday' ) );

		$subject = sprintf( __( 'Authorize.Net Transaction Report for %s', 'woocommerce-authorize-net-reporting' ), $previous_day );

		$message = sprintf( __( 'Attached is your daily Authorize.Net Transaction Report for %s in CSV format.', 'woocommerce-authorize-net-reporting' ), $previous_day );

		// set the attachment filename
		$filename = $this->filename;

		// prepend the temp directory
		$filename = get_temp_dir() . $filename;

		// create the file
		touch( $filename );

		// open the file
		$handle = fopen( $filename, 'w+' );

		// write header
		fputcsv( $handle, \WC_Authorize_Net_Reporting_API_Response::get_transaction_fields() );

		// write each transaction
		foreach ( $transaction_details as $transaction_detail ) {
			fputcsv( $handle, $transaction_detail );
		}

		// close the file
		fclose( $handle );

		// make sure the temp file is removed after the email is sent
		wc_authorize_net_reporting()->temp_export_file = $filename;
		register_shutdown_function( function() {
			@unlink( wc_authorize_net_reporting()->temp_export_file );
		} );

		wp_mail( $recipients, $subject, $message, '', $filename );
	}


	/**
	 * Fetches the transaction details for the provided date range as a simple associative array.
	 *
	 * @since 1.0
	 *
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function get_transaction_details() {

		try {

			$api = wc_authorize_net_reporting()->get_api();

			if ( ! $api ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'You must enter your Authorize.Net API credentials before downloading an export.', 'woocommerce-authorize-net-reporting' ) );
			}

			return $api->get_transaction_details_by_date_range( $this->start_date, $this->end_date );

		} catch ( Framework\SV_WC_Plugin_Exception $e ) {

			// add error message as an admin notice
			if ( is_admin() ) {
				wc_authorize_net_reporting()->get_admin_instance()->message_handler->add_error( $e->getMessage() );
			}

			wp_redirect( wp_get_referer() );
			exit;
		}
	}


}
