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
 * Export FTP over Implicit SSL Class
 *
 * Wrapper for cURL functions to transfer a file over FTP with implicit SSL
 *
 * @since 3.0.0
 */
class WC_Customer_Order_CSV_Export_Method_FTP_Implicit_SSL extends WC_Customer_Order_CSV_Export_Method_File_Transfer {


	/** @var resource cURL resource handle */
	private $curl_handle;

	/** @var string cURL URL for upload */
	private $url;


	/**
	 * Connect to FTP server over Implicit SSL/TLS
	 *
	 * @since 3.0.0
	 * @throws Framework\SV_WC_Plugin_Exception
	 * @param array $args
	 */
	public function __construct( $args ) {

		parent::__construct( $args );

		// set host/initial path
		$this->url = "ftps://{$this->server}/{$this->path}";

		// setup connection
		$this->curl_handle = curl_init();

		// check for successful connection
		if ( ! $this->curl_handle ) {

			throw new Framework\SV_WC_Plugin_Exception( __( 'Could not initialize cURL.', 'woocommerce-customer-order-csv-export' ) );
		}

		// connection options
		$options = [
			CURLOPT_USERPWD        => $this->username . ':' . $this->password,
			CURLOPT_SSL_VERIFYPEER => false, // don't verify SSL
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_FTP_SSL        => CURLFTPSSL_ALL, // require SSL For both control and data connections
			CURLOPT_FTPSSLAUTH     => CURLFTPAUTH_DEFAULT, // let cURL choose the FTP authentication method (either SSL or TLS)
			CURLOPT_UPLOAD         => true,
			CURLOPT_PORT           => $this->port,
			CURLOPT_TIMEOUT        => $this->timeout,
		];

		// cURL FTP enables passive mode by default, so disable it by enabling the
		// PORT command
		if ( ! $this->passive_mode ) {

			$options[ CURLOPT_FTPPORT ] = '-';
		}

		/**
		 * Filter FTP over Implicit SSL cURL options
		 *
		 * @since 3.0.0
		 * @param array $options
		 * @param \WC_Customer_Order_CSV_Export_Method_FTP_Implicit_SSL instance
		 */
		$options = apply_filters( 'wc_customer_order_export_ftp_over_implicit_curl_options', $options, $this );

		// set connection options, use foreach so useful errors can be caught
		// instead of a generic "cannot set options" error with curl_setopt_array()
		foreach ( $options as $option_name => $option_value ) {

			if ( ! curl_setopt( $this->curl_handle, $option_name, $option_value ) ) {

				/* translators: Placeholders: %s - option name */
				throw new Framework\SV_WC_Plugin_Exception( sprintf( __( 'Could not set cURL option: %s', 'woocommerce-customer-order-csv-export' ), $option_name ) );
			}
		}
	}


	/**
	 * Uploads the file to the remote target.
	 *
	 * @since 3.0.0
	 *
	 * @param \WC_Customer_Order_CSV_Export_Export|string $export the export object or a path to an export file
	 * @return bool whether the upload was successful or not
	 * @throws Framework\SV_WC_Plugin_Exception Open remote file failure or write data failure
	 */
	public function perform_action( $export ) {

		if ( ! $export ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'Unable to find export for transfer', 'woocommerce-customer-order-csv-export' ) );
		}

		if ( is_string( $export ) && is_readable( $export ) ) {

			$file_path = $export;
			$stream    = fopen( $file_path, 'r' );
			$filename  = basename( $file_path );

		} else {

			$filename = $export->get_filename();
			$stream   = $export->get_file_stream();
		}

		// set file name
		if ( ! curl_setopt( $this->curl_handle, CURLOPT_URL, $this->url . $filename ) ) {

			/* translators: Placeholders: %s - name of file to be uploaded */
			throw new Framework\SV_WC_Plugin_Exception( sprintf( __( 'Could not set cURL file name: %s', 'woocommerce-customer-order-csv-export' ), $filename ) );
		}

		// check for valid stream handle
		if ( ! $stream ) {

			/* translators: Placeholders: %s - file path */
			throw new Framework\SV_WC_Plugin_Exception( sprintf( __( 'Could not open %s for reading.', 'woocommerce-customer-order-csv-export' ), $filename ) );
		}

		// set the file to be uploaded
		if ( ! curl_setopt( $this->curl_handle, CURLOPT_INFILE, $stream ) ) {

			/* translators: Placeholders: %s - name of file to be updloaded */
			throw new Framework\SV_WC_Plugin_Exception( sprintf( __( 'Could not load file %s', 'woocommerce-customer-order-csv-export' ), $filename ) );
		}

		// upload file
		if ( ! curl_exec( $this->curl_handle ) ) {

			/* translators: Placeholders: %1$s - cURL error number, %2$s - cURL error message */
			throw new Framework\SV_WC_Plugin_Exception( sprintf( __( 'Could not upload file. cURL Error: [%1$s] - %2$s', 'woocommerce-customer-order-csv-export' ), curl_errno( $this->curl_handle ), curl_error( $this->curl_handle ) ) );
		}

		// close the stream handle
		fclose( $stream );

		return true;
	}


	/**
	 * Attempt to close cURL handle
	 *
	 * @since 3.0.0
	 */
	public function __destruct() {

		// errors suppressed here as they are not useful
		@curl_close( $this->curl_handle );
	}


}
