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

if ( ! class_exists( 'YWSN_Sabeq_Alarabia' ) ) {

	/**
	 * Implements Sabeq Alarabia API for YWSN plugin
	 *
	 * @class   YWSN_Sabeq_Alarabia
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWSN_Sabeq_Alarabia extends YWSN_SMS_Gateway {

		/** @var string sabeq_alarabia user */
		private $_sabeq_alarabia_user;

		/** @var string sabeq_alarabia pass */
		private $_sabeq_alarabia_pass;

		/** @var string sabeq_alarabia sender */
		private $_sabeq_alarabia_sender;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$this->_sabeq_alarabia_user   = get_option( 'ywsn_sabeq_alarabia_user' );
			$this->_sabeq_alarabia_pass   = get_option( 'ywsn_sabeq_alarabia_pass' );
			$this->_sabeq_alarabia_sender = get_option( 'ywsn_sabeq_alarabia_sender' );

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

			$args = http_build_query(
				array(
					'username'     => $this->_sabeq_alarabia_user,
					'password'     => $this->_sabeq_alarabia_pass,
					'numbers'      => $to_phone,
					'message'      => $message,
					'sender'       => $this->_sabeq_alarabia_sender,
					'unicode'      => 'e',
					'Rmduplicated' => '1',
					'return'       => 'json',
				)
			);

			$wp_remote_http_args = array(
				'method' => 'POST',
				'body'   => $args,
				'header' => 'Content-type: application/x-www-form-urlencoded' . "\r\n" . 'Content-Length: ' . strlen( $args ) . "\r\n",
			);

			$endpoint = 'http://www.sabeq-alarabia.net/api/sendsms.php';

			// perform HTTP request with endpoint / args
			$response = wp_safe_remote_request( esc_url_raw( $endpoint ), $wp_remote_http_args );

			// WP HTTP API error like network timeout, etc
			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			}

			$this->_log[] = $response;

			// Check for proper response / body
			if ( ! isset( $response['body'] ) ) {
				throw new Exception( esc_html__( 'No answer', 'yith-woocommerce-sms-notifications' ) );
			}

			$result = json_decode( $response['body'], true );

			if ( '100' !== $result['Code'] ) {
				/* translators: %s error message */
				throw new Exception( sprintf( esc_html__( 'An error has occurred: %s', 'yith-woocommerce-sms-notifications' ), $result['MessageIs'] ) );
			}

			return;

		}

	}

}
