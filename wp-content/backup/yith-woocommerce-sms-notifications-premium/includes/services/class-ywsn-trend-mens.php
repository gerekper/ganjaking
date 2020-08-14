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

if ( ! class_exists( 'YWSN_Trend_Mens' ) ) {

	/**
	 * Implements Trend Mens API for YWSN plugin
	 *
	 * @class   YWSN_Trend_Mens
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWSN_Trend_Mens extends YWSN_SMS_Gateway {

		/** @var string trend_mens key */
		private $_trend_mens_key;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$this->_trend_mens_key = get_option( 'ywsn_trend_mens_key' );

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

			$args = json_encode(
				array(
					'to'      => $to_phone,
					'message' => $message,
				)
			);

			$wp_remote_http_args = array(
				'method'  => 'POST',
				'body'    => $args,
				'headers' => array(
					'api-key'      => $this->_trend_mens_key,
					'content-type' => 'application/json',
				),
			);

			$endpoint = 'http://www.trendmens.com.br/api/send_sms/';

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

			if ( isset( $response['response']['code'] ) ) {
				if ( 200 !== (int) $response['response']['code'] ) {
					/* translators: %s error message */
					throw new Exception( sprintf( esc_html__( 'An error has occurred: %s', 'yith-woocommerce-sms-notifications' ), $response['response']['message'] ) );
				}
			} else {
				throw new Exception( esc_html__( 'No answer code', 'yith-woocommerce-sms-notifications' ) );
			}

			return;

		}

	}

}
