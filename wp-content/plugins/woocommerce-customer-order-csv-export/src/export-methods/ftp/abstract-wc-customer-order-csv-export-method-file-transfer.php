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
 * Export File Transfer Class
 *
 * Simple abstract class that handles getting FTP credentials and connection information for
 * all of the FTP methods (FTP, FTPS, FTP over implicit SSL, SFTP)
 *
 * @since 3.0.0
 */
abstract class WC_Customer_Order_CSV_Export_Method_File_Transfer implements WC_Customer_Order_CSV_Export_Method {


	/** @var string the FTP server address */
	protected $server;

	/** @var string the FTP username */
	protected $username;

	/** @var string the FTP user password*/
	protected $password;

	/** @var string the FTP server port */
	protected $port;

	/** @var string the path to change to after connecting */
	protected $path;

	/** @var string the FTP security type, either `none`, `ftps`, `ftp-ssl`, `sftp` */
	protected $security;

	/** @var bool true to enable passive mode for the FTP connection, false otherwise */
	protected $passive_mode;

	/** @var int the timeout for the FTP connection in seconds */
	protected $timeout;


	/**
	 * Setup FTP information and check for any invalid/missing info
	 *
	 * @since 3.0.0
	 * @throws Framework\SV_WC_Plugin_Exception on missing configuration variables
	 * @param array $args {
	 *     An array of arguments.
	 *
	 *     @type string $ftp_server FTP server address
	 *     @type string $ftp_username FTP username
	 *     @type string $ftp_password FTP password
	 *     @type string $ftp_port FTP port
	 *     @type string $ftp_path Path on the FTP server to upload files to
	 *     @type string $ftp_security FTP security type
	 *     @type string $ftp_passive_mode Whether to use FTP passive mode or not, either `yes` or `no`
	 * }
	 */
	 public function __construct( $args ) {

		$args = wp_parse_args( $args, [
			'ftp_server'       => '',
			'ftp_username'     => '',
			'ftp_password'     => '',
			'ftp_port'         => '',
			'ftp_path'         => '',
			'ftp_security'     => '',
			'ftp_passive_mode' => 'no',
		] );

		// set connection info
		$this->server       = $args['ftp_server'];
		$this->username     = $args['ftp_username'];
		$this->password     = $args['ftp_password'];
		$this->port         = $args['ftp_port'];
		$this->path         = $args['ftp_path'];
		$this->security     = $args['ftp_security'];
		$this->passive_mode = wc_string_to_bool( $args['ftp_passive_mode'] ); // make sure 'yes' and 'no' also result in boolean

		/**
		 * Allow actors to adjust the FTP timeout
		 *
		 * @since 3.0.0
		 * @param int $timeout Timeout in seconds
		 */
		$this->timeout = apply_filters( 'wc_customer_order_export_ftp_timeout', 30 );

		// check for blank username
		if ( ! $this->username ) {

			throw new Framework\SV_WC_Plugin_Exception( __( 'FTP Username is blank.', 'woocommerce-customer-order-csv-export' ) );
		}

		/* allow blank passwords */

		// check for blank server
		if ( ! $this->server ) {

			throw new Framework\SV_WC_Plugin_Exception( __( 'FTP Server is blank.', 'woocommerce-customer-order-csv-export' ) );
		}

		// check for blank port
		if ( ! $this->port ) {

			throw new Framework\SV_WC_Plugin_Exception ( __( 'FTP Port is blank.', 'woocommerce-customer-order-csv-export' ) );
		}
	}

}
