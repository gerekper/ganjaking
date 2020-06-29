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
 * Export FTP Class
 *
 * Simple wrapper for ftp_* functions to upload an exported file to a remote
 * server via FTP/FTPS (explicit)
 *
 * @since 1.1.0
 */
class WC_Customer_Order_XML_Export_Suite_Method_FTP extends WC_Customer_Order_XML_Export_Suite_Method_File_Transfer {


	/** @var resource FTP connection resource */
	private $link;


	/**
	 * Connect to FTP server and authenticate via password
	 *
	 * @since 1.1.0
	 * @throws SV_WC_Plugin_Exception
	 * @param array $args
	 */
	public function __construct( $args ) {

		parent::__construct( $args );

		// Handle errors from ftp_* functions that throw warnings for things like
		// invalid username / password, failed directory changes, and failed data connections
		set_error_handler( array( $this, 'handle_errors' ) );

		// setup connection
		$this->link = null;

		if ( 'ftps' === $this->security && function_exists( 'ftp_ssl_connect' ) ) {

			$this->link = ftp_ssl_connect( $this->server, $this->port, $this->timeout );

		} elseif ( 'ftps' !== $this->security ) {

			$this->link = ftp_connect( $this->server, $this->port, $this->timeout );
		}

		// check for successful connection
		if ( ! $this->link ) {

			/* translators: Placeholders: %1$s - server address, %2$s - server port. */
			throw new SV_WC_Plugin_Exception( sprintf( __( 'Could not connect via FTP to %1$s on port %2$s, check server address and port.', 'woocommerce-customer-order-xml-export-suite' ), $this->server, $this->port ) );
		}

		// attempt to login, note that incorrect credentials throws an E_WARNING PHP error
		if ( ! ftp_login( $this->link, $this->username, $this->password ) ) {

			/* translators: Placeholders: %s - username */
			throw new SV_WC_Plugin_Exception( sprintf( __( "Could not authenticate via FTP with username %s and password. Check username and password.", 'woocommerce-customer-order-xml-export-suite' ), $this->username ) );
		}

		// set passive mode if enabled
		if ( $this->passive_mode ) {

			// check for success
			if ( ! ftp_pasv( $this->link, true ) ) {

				throw new SV_WC_Plugin_Exception( __( 'Could not set passive mode', 'woocommerce-customer-order-xml-export-suite' ) );
			}
		}

		// change directories if initial path is populated, note that failing to change directory throws an E_WARNING PHP error
		if ( $this->path ) {

			// check for success
			if ( ! ftp_chdir( $this->link, '/' . $this->path ) ) {

				/* translators: Placeholders: %s - directory path */
				throw new SV_WC_Plugin_Exception( sprintf( __( 'Could not change directory to %s - check path exists.', 'woocommerce-customer-order-xml-export-suite' ), $this->path ) );
			}
		}
	}


	/**
	 * Uploads the exported file to the remote location.
	 *
	 * @since 1.1.0
	 *
	 * @param \WC_Customer_Order_XML_Export_Suite_Export|string $export the export object or a path to an export file
	 * @return bool whether the upload was successful or not
	 * @throws \SV_WC_Plugin_Exception Open remote file failure or write data failure
	 */
	public function perform_action( $export ) {

		if ( ! $export ) {
			throw new SV_WC_Plugin_Exception( __( 'Unable to find export for transfer', 'woocommerce-customer-order-xml-export-suite' ) );
		}

		if ( is_string( $export ) && is_readable( $export ) ) {

			$file_path = $export;
			$stream    = fopen( $file_path, 'r' );
			$filename  = basename( $file_path );

		} else {

			$filename = $export->get_filename();
			$stream   = $export->get_file_stream();
		}

		// check for valid stream handle
		if ( ! $stream ) {

			/* translators: Placeholders: %s - file path */
			throw new SV_WC_Plugin_Exception( sprintf( __( 'Could not open file %s for reading.', 'woocommerce-customer-order-xml-export-suite' ), $filename ) );
		}

		// upload the stream
		if ( ! ftp_fput( $this->link, $filename, $stream, FTP_ASCII ) ) {

			/* translators: Placeholders: %s - file name */
			throw new SV_WC_Plugin_Exception( sprintf( __( 'Could not upload file: %s - check permissions.', 'woocommerce-customer-order-xml-export-suite' ), $filename ) );
		}

		// close the stream handle
		fclose( $stream );

		return true;
	}


	/**
	 * Handle PHP errors during the upload process -- some ftp_* functions throw E_WARNINGS in addition to returning false
	 * when encountering incorrect passwords, etc. Using a custom error handler serves to return helpful messages instead
	 * of "cannot connect" or similar.
	 *
	 * @since 1.1.0
	 * @param int $error_no unused
	 * @param string $error_string PHP error string
	 * @param string $error_file PHP file where error occurred
	 * @param int $error_line line number of error
	 * @return boolean false
	 * @throws SV_WC_Plugin_Exception
	 */
	public function handle_errors( $error_no, $error_string, $error_file, $error_line ) {

		// only handle errors for our own files
		if ( false === strpos( $error_file, __FILE__ ) ) {

			return false;
		}

		/* translators: Placeholders: %s - error message */
		throw new SV_WC_Plugin_Exception( sprintf( __( 'FTP error: %s', 'woocommerce-customer-order-xml-export-suite' ), $error_string ) );
	}


	/**
	 * Attempt to close FTP link
	 *
	 * @since 1.1.0
	 */
	public function __destruct() {

		if ( $this->link ) {

			// errors suppressed here as they are not useful
			@ftp_close( $this->link );
		}

		// give error handling back to PHP
		restore_error_handler();
	}


}
