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

if ( ! class_exists( 'YWSN_Burst_SMS' ) ) {

	/**
	 * Implements Burst SMS API for YWSN plugin
	 *
	 * @class   YWSN_Burst_SMS
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWSN_Burst_SMS extends YWSN_SMS_Gateway {

		/** @var string burst api key */
		private $_burst_sms_api_key;

		/** @var string burst api secret */
		private $_burst_sms_api_secret;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$this->_burst_sms_api_key    = get_option( 'ywsn_burst_sms_api_key' );
			$this->_burst_sms_api_secret = get_option( 'ywsn_burst_sms_api_secret' );

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

			$args = array(
				'from'    => $from,
				'to'      => $to_phone,
				'message' => $message,
			);

			$endpoint = 'https://api.transmitsms.com/send-sms.json';
			$headers  = array(
				'Content-Type:application/json',
				'Authorization:Basic ' . base64_encode( "$this->_burst_sms_api_key:$this->_burst_sms_api_secret" ),
			);

			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch, CURLOPT_URL, add_query_arg( $args, $endpoint ) );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
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

			if ( ! isset( $code['message_id'] ) ) {
				/* translators: %1$s error code, %2$s error message */
				throw new Exception( sprintf( esc_html__( 'An error has occurred: %1$s %2$s', 'yith-woocommerce-sms-notifications' ), $code['error']['code'], $code['error']['description'] ) );
			}

			return;

		}

	}

}
