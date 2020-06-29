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
 * Export File Transfer Class
 *
 * Simple abstract class that handles getting FTP credentials and connection information for
 * all of the FTP methods (FTP, FTPS, FTP over implicit SSL, SFTP)
 *
 * @since 1.1.0
 */
abstract class WC_Customer_Order_XML_Export_Suite_Method_File_Transfer implements WC_Customer_Order_XML_Export_Suite_Method {


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
	 * @since 1.1.0
	 * @throws SV_WC_Plugin_Exception on missing configuration variables
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

		$args = wp_parse_args( $args, array(
			'ftp_server'       => '',
			'ftp_username'     => '',
			'ftp_password'     => '',
			'ftp_port'         => '',
			'ftp_path'         => '',
			'ftp_security'     => '',
			'ftp_passive_mode' => 'no',
		) );

		// set connection info
		$this->server       = $args['ftp_server'];
		$this->username     = $args['ftp_username'];
		$this->password     = $args['ftp_password'];
		$this->port         = $args['ftp_port'];
		$this->path         = $args['ftp_path'];
		$this->security     = $args['ftp_security'];
		$this->passive_mode = 'yes' === $args['ftp_passive_mode'];

		/**
		 * Allow actors to adjust the FTP timeout
		 *
		 * @since 1.1.0
		 * @param int $timeout Timeout in seconds
		 */
		$this->timeout = apply_filters( 'wc_customer_order_xml_export_suite_ftp_timeout', 30 );

		// check for blank username
		if ( ! $this->username ) {

			throw new SV_WC_Plugin_Exception( __( 'FTP Username is blank.', 'woocommerce-customer-order-xml-export-suite' ) );
		}

		/* allow blank passwords */

		// check for blank server
		if ( ! $this->server ) {

			throw new SV_WC_Plugin_Exception( __( 'FTP Server is blank.', 'woocommerce-customer-order-xml-export-suite' ) );
		}

		// check for blank port
		if ( ! $this->port ) {

			throw new SV_WC_Plugin_Exception ( __( 'FTP Port is blank.', 'woocommerce-customer-order-xml-export-suite' ) );
		}
	}

}
