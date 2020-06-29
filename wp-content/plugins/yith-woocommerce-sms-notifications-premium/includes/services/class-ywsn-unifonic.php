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

if ( ! class_exists( 'YWSN_Unifonic' ) ) {

	/**
	 * Implements Unifonic API for YWSN plugin
	 *
	 * @class   YWSN_Unifonic
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWSN_Unifonic extends YWSN_SMS_Gateway {

		/** @var string unifonic api key */
		private $_unifonic_apikey;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$this->_unifonic_apikey = get_option( 'ywsn_unifonic_apikey' );

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

			$charset = apply_filters( 'ywsn_additional_charsets', get_option( 'ywsn_active_charsets', array() ) );

			if ( '' !== $this->_from_asid ) {
				$from = $this->_from_asid;
			} else {
				$from = ( '+' !== substr( $this->_from_number, 0, 1 ) ? '+' . $this->_from_number : $this->_from_number );
			}
			$args = array(
				'AppSid'    => $this->_unifonic_apikey,
				'Recipient' => $to_phone,
				'Body'      => $message,
				'SenderID'  => $from,
			);

			if ( ! empty( $charset ) ) {
				$args['encoding'] = 'UTF8';
			}

			$query_args = http_build_query( $args );

			$wp_remote_http_args = array(
				'method' => 'POST',
				'body'   => $query_args,
				'header' => 'Content-type: application/x-www-form-urlencoded' . "\r\n" . 'Content-Length: ' . strlen( $query_args ) . "\r\n",
			);

			$endpoint = 'https://api.unifonic.com/rest/Messages/Send';

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

			if ( 'false' === $result['success'] ) {
				/* translators: %1$s error code, %2$s error message */
				throw new Exception( sprintf( esc_html__( 'An error has occurred: %1$s %2$s', 'yith-woocommerce-sms-notifications' ), $result['errorCode'], $result['message'] ) );
			}

			return;

		}

	}

}
