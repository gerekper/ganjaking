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

if ( ! class_exists( 'YWSN_SMSAPI' ) ) {

	/**
	 * Implements SMSAPI API for YWSN plugin
	 *
	 * @class   YWSN_SMSAPI
	 * @since   1.4.5
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWSN_SMSAPI extends YWSN_SMS_Gateway {

		/** @var string smsapi api key */
		private $_smsapi_token;

		/** @var string smsapi sender */
		private $_smsapi_sender;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.1.8
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$this->_smsapi_token  = get_option( 'ywsn_smsapi_token' );
			$this->_smsapi_sender = get_option( 'ywsn_smsapi_sender' );

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
		 * @since   1.1.8
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function send( $to_phone, $message, $country_code ) {

			$args = array(
				'to'      => $to_phone,
				'from'    => $this->_smsapi_sender,
				'message' => $message,
				'format'  => 'json',
			);

			$endpoint = 'https://api.smsapi.com/sms.do';

			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $endpoint );
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $args );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array( "Authorization: Bearer $this->_smsapi_token" ) );

			$response = curl_exec( $ch );

			// WP HTTP API error like network timeout, etc
			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			}

			$this->_log[] = $response;

			// Check for proper body
			if ( '' === $response ) {
				throw new Exception( esc_html__( 'No answer', 'yith-woocommerce-sms-notifications' ) );
			}

			$result = json_decode( $response, true );

			if ( isset( $result['error'] ) ) {
				/* translators: %s error message */
				throw new Exception( sprintf( esc_html__( 'An error has occurred: %s', 'yith-woocommerce-sms-notifications' ), $result['message'] ) );
			}

			return;

		}

	}

}
