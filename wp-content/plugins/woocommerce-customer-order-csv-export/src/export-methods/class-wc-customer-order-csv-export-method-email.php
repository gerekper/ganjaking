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
 * Export Email Class
 *
 * Helper class for emailing exported file
 *
 * @since 3.1.0
 */
class WC_Customer_Order_CSV_Export_Method_Email implements WC_Customer_Order_CSV_Export_Method {


	/** @var string temporary filename to be deleted */
	private $temp_filename;

	/** @var string email recipients */
	private $email_recipients;

	/** @var string email subject */
	private $email_subject;

	/** @var string email message */
	private $email_message;

	/** @var string email id */
	private $email_id;


	/**
	 * Initialize the export method
	 *
	 * @since 4.0.0
	 * @param array $args {
	 *     An array of arguments.
	 *
	 *     @type string $email_recipients Email recipients
	 *     @type string $email_subject Email subject
	 *     @type string $email_message Email message
	 *     @type string $email_id Email ID
	 * }
	 */
	 public function __construct( $args ) {

		$this->args             = $args;
		$this->email_recipients = $args['email_recipients'];
		$this->email_subject    = $args['email_subject'];
		$this->email_message    = $args['email_message'];
		$this->email_id         = $args['email_id'];
	}


	/**
	 * Emails the admin with the exported file as an attachment.
	 *
	 * @since 3.1.0

	 * @param \WC_Customer_Order_CSV_Export_Export|string $export the export object or a path to an export file
	 * @return bool whether the mail was sent successfully or not
	 * @throws Framework\SV_WC_Plugin_Exception wp_mail errors
	 */
	public function perform_action( $export ) {

		if ( ! $export ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'Unable to find export for transfer', 'woocommerce-customer-order-csv-export' ) );
		}

		if ( is_string( $export ) && is_readable( $export ) ) {

			$file_path = $export;

		} else {

			$file_path = $export->get_temporary_file_path();
		}

		// init email args
		$mailer  = WC()->mailer();
		$to      = ( $email = $this->email_recipients ) ? $email : get_option( 'admin_email' );
		$subject = $this->email_subject;
		$message = $this->email_message;

		/** WooCommerce core filter hook: {@see \WC_Email::get_headers()} */
		$headers     = apply_filters( 'woocommerce_email_headers', "Content-Type: text/plain\r\n", $this->email_id, $this->args, $this );
		$attachments = [ $file_path ];

		// hook into `wp_mail_failed` and throw errors as exceptions
		add_action( 'wp_mail_failed', [ $this, 'handle_wp_mail_error' ] );

		// send email
		$result = $mailer->send( $to, $subject, $message, $headers, $attachments );

		// unhook from wp_mail_failed
		remove_action( 'wp_mail_failed', [ $this, 'handle_wp_mail_error' ] );

		return $result;
	}


	/**
	 * Handle wp_mail_failed errors, by throwing them as exceptions
	 *
	 * @since 4.0.0
	 * @param \WP_Error $error
	 * @throws \Exception
	 */
	public function handle_wp_mail_error( WP_Error $error ) {

		// unhook from wp_mail_failed
		remove_action( 'wp_mail_failed', [ $this, 'handle_wp_mail_error' ] );

		throw new Framework\SV_WC_Plugin_Exception( $error->get_error_message() );
	}

}
