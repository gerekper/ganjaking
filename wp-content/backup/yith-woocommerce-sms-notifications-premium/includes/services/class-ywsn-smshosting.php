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

if ( ! class_exists( 'YWSN_Smshosting' ) ) {

	/**
	 * Implements Smshosting API for YWSN plugin
	 *
	 * @class   YWSN_Smshosting
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWSN_Smshosting extends YWSN_SMS_Gateway {

		/** @var string smshosting authkey */
		private $_smshosting_authkey;

		/** @var string smshosting password */
		private $_smshosting_authsecret;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$this->_smshosting_authkey    = get_option( 'ywsn_smshosting_authkey' );
			$this->_smshosting_authsecret = get_option( 'ywsn_smshosting_authsecret' );

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

			$from = '' !== $this->_from_asid ? $this->_from_asid : $this->_from_number;

			$type = empty( apply_filters( 'ywsn_additional_charsets', get_option( 'ywsn_active_charsets', array() ) ) ) ? '7BIT' : 'UCS2';

			$args = http_build_query(
				array(
					'authKey'    => $this->_smshosting_authkey,
					'authSecret' => $this->_smshosting_authsecret,
					'from'       => $from,
					'to'         => $to_phone,
					'text'       => $message,
					'encoding'   => $type,
				)
			);

			$wp_remote_http_args = array(
				'method' => 'POST',
				'body'   => $args,
				'header' => 'Content-type: application/x-www-form-urlencoded' . "\r\n" . 'Content-Length: ' . strlen( $args ) . "\r\n",
			);

			$endpoint = 'https://api.smshosting.it/rest/api/smart/sms/send';

			// perform HTTP request with endpoint / args
			$response = wp_safe_remote_request( esc_url_raw( $endpoint ), $wp_remote_http_args );

			// WP HTTP API error like network timeout, etc
			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			}

			$this->_log[] = $response;

			// Check for proper response / body
			if ( ! isset( $response['response'] ) || ! isset( $response['body'] ) ) {
				throw new Exception( esc_html__( 'No answer', 'yith-woocommerce-sms-notifications' ) );
			}

			if ( 200 !== (int) $response['response']['code'] ) {
				$error = json_decode( $response['body'], true );
				/* translators: %s error message */
				throw new Exception( sprintf( esc_html__( 'An error has occurred: %s', 'yith-woocommerce-sms-notifications' ), $error['errorMsg'] ) );
			}

			return;

		}

	}

}
