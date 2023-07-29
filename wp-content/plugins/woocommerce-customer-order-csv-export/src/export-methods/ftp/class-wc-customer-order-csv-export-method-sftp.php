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
 * Export SFTP Class
 *
 * Simple wrapper for ssh2_* functions to upload an exported file to a remote
 * server via FTP over SSH
 *
 * @since 3.0.0
 */
class WC_Customer_Order_CSV_Export_Method_SFTP extends WC_Customer_Order_CSV_Export_Method_File_Transfer {


	/** @var resource sftp connection resource */
	private $sftp_link;

	/** @var resource ssh connection resource */
	private $ssh_link;


	/**
	 * Connect to SSH server, authenticate via password, and set up SFTP link
	 *
	 * @since 3.0.0
	 * @see WC_Customer_Order_CSV_Export_Method_File_Transfer::__construct()
	 * @throws Framework\SV_WC_Plugin_Exception - ssh2 extension not installed, failed SSH / SFTP connection, failed authentication
	 * @param array $args
	 */
	public function __construct( $args ) {

		parent::__construct( $args );

		// Handle errors from ssh2_* functions that throw warnings for things like
		// failed connections, etc
		set_error_handler( [ $this, 'handle_errors' ] );

		// check if ssh2 extension is installed
		if ( ! function_exists( 'ssh2_connect' ) ) {

			throw new Framework\SV_WC_Plugin_Exception( __( 'SSH2 Extension is not installed, cannot connect via SFTP.', 'woocommerce-customer-order-csv-export' ) );
		}

		// setup connection
		$this->ssh_link = ssh2_connect( $this->server, $this->port );

		// check for successful connection
		if ( ! $this->ssh_link ) {

			/* translators: Placeholders: %1$s - server address, %2$s - server port. */
			throw new Framework\SV_WC_Plugin_Exception( sprintf( __( 'Could not connect via SSH to %1$s on port %2$s, check server address and port.', 'woocommerce-customer-order-csv-export' ), $this->server, $this->port ) );
		}

		// authenticate via password and check for successful authentication
		if ( ! ssh2_auth_password( $this->ssh_link, $this->username, $this->password ) ) {

			/* translators: Placeholders: %s - username */
			throw new Framework\SV_WC_Plugin_Exception( sprintf( __( 'Could not authenticate via SSH with username %s and password. Check username and password.', 'woocommerce-customer-order-csv-export' ), $this->username ) );
		}

		// setup SFTP link
		$this->sftp_link = ssh2_sftp( $this->ssh_link );

		// check for successful SFTP link
		if ( ! $this->sftp_link ) {

			throw new Framework\SV_WC_Plugin_Exception( __( 'Could not setup SFTP link', 'woocommerce-customer-order-csv-export' ) );
		}
	}


	/**
	 * Opens the remote file and writes exported data into it.
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
			$source    = fopen( $file_path, 'r' );
			$filename  = basename( $file_path );

		} else {

			$filename = $export->get_filename();
			$source   = $export->get_file_stream();
		}

		// keep this! see https://bugs.php.net/bug.php?id=73597
		$sftp_link   = intval( $this->sftp_link );
		$remote_path = "ssh2.sftp://{$sftp_link}/{$this->path}";
		$remote_file = "{$remote_path}{$filename}";

		// open a file on the remote system for writing
		$remote_stream = fopen( $remote_file, 'w+' );

		// set chunk size to 1Mb to optimize for transfer speed
		$chunk_size = 1024 * 1024;
		stream_set_chunk_size( $remote_stream, $chunk_size );

		// check for fopen failure
		if ( ! $remote_stream ) {

			/* translators: Placeholders: %s - file path */
			throw new Framework\SV_WC_Plugin_Exception( sprintf( __( 'Could not open remote file: %s.', 'woocommerce-customer-order-csv-export' ), $remote_file ) );
		}

		while ( $buffer = fread( $source, $chunk_size ) ) {

			fwrite( $remote_stream, $buffer );
		}

		// close file
		fclose( $remote_stream );
		fclose( $source );

		return true;
	}


	/**
	 * Handle PHP errors during the upload process -- some ssh2_* functions throw E_WARNINGS in addition to returning false
	 * when encountering incorrect passwords, etc. Using a custom error handler serves to return helpful messages instead
	 * of "cannot connect" or similar.
	 *
	 * @since 4.0.5
	 * @param int $error_no unused
	 * @param string $error_string PHP error string
	 * @param string $error_file PHP file where error occurred
	 * @param int $error_line line number of error
	 * @return boolean false
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function handle_errors( $error_no, $error_string, $error_file, $error_line ) {

		// only handle errors for our own files
		if ( false === strpos( $error_file, __FILE__ ) ) {

			return false;
		}

		/* translators: Placeholders: %s - error message */
		throw new Framework\SV_WC_Plugin_Exception( sprintf( __( 'SFTP error: %s', 'woocommerce-customer-order-csv-export' ), $error_string ) );
	}


	/**
	 * Restore error handler and close SSH connction
	 *
	 * @since 4.0.5
	 */
	public function __destruct() {

		if ( isset( $this->ssh_link ) ) {

			unset( $this->ssh_link );
		}

		// give error handling back to PHP
		restore_error_handler();
	}


}
