<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWSN_Bulk_SMS' ) ) {

	/**
	 * Implements Bulk SMS API for YWSN plugin
	 *
	 * @class   YWSN_Bulk_SMS
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWSN_Bulk_SMS extends YWSN_SMS_Gateway {

		/** @var string bulk_sms user */
		private $_bulk_sms_user;

		/** @var string bulk_sms pass */
		private $_bulk_sms_pass;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$this->_bulk_sms_user = get_option( 'ywsn_bulk_sms_user' );
			$this->_bulk_sms_pass = get_option( 'ywsn_bulk_sms_pass' );

			parent::__construct();

		}

		/**
		 * Send SMS
		 *
		 * @param   $to_phone     string
		 * @param   $message      string
		 * @param   $country_code string
		 *
		 * @return  void
		 * @throws  Exception for WP HTTP API error, no response, HTTP status code is not 201 or if HTTP status code not set
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function send( $to_phone, $message, $country_code ) {

			$to_phone = ( '+' !== substr( $to_phone, 0, 1 ) ? '+' . $to_phone : $to_phone );

			$messages = array(
				array(
					'to'   => $to_phone,
					'body' => $message,
				),
			);

			$args = array(
				'auto-unicode'        => 'true',
				'longMessageMaxParts' => '30',
			);

			$endpoint = 'https://api.bulksms.com/v1/messages';

			$ch      = curl_init();
			$headers = array(
				'Content-Type:application/json',
				'Authorization:Basic ' . base64_encode( "$this->_bulk_sms_user:$this->_bulk_sms_pass" ),
			);
			curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch, CURLOPT_URL, add_query_arg( $args, $endpoint ) );
			curl_setopt( $ch, CURLOPT_POST, 1 );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $messages ) );
			curl_setopt( $ch, CURLOPT_TIMEOUT, 20 );
			curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
			$response = curl_exec( $ch );
			curl_close( $ch );

			// WP HTTP API error like network timeout, etc
			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			}

			$this->_log[] = $response;

			// Check for proper body
			if ( '' === $response ) {
				throw new Exception( esc_html__( 'No answer', 'yith-woocommerce-sms-notifications' ) );
			}

			$code = json_decode( $response, true );

			if ( ! isset( $code[0] ) ) {
				/* translators: %1$s error status, %2$s error detail */
				throw new Exception( sprintf( esc_html__( 'An error has occurred: %1$s %2$s', 'yith-woocommerce-sms-notifications' ), $code['status'], $code['detail'] ) );
			}

			return;

		}

	}

}
