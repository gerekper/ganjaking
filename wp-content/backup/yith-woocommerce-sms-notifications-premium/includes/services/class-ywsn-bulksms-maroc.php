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

if ( ! class_exists( 'YWSN_BulkSMS_Maroc' ) ) {

	/**
	 * Implements BulkSMS Maroc API for YWSN plugin
	 *
	 * @class   YWSN_BulkSMS_Maroc
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWSN_BulkSMS_Maroc extends YWSN_SMS_Gateway {

		/** @var string BulkSMS Maroc api key */
		private $_bulksms_maroc_key;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$this->_bulksms_maroc_key = get_option( 'ywsn_bulksms_maroc_key' );

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

			$args = array(
				'token'   => $this->_bulksms_maroc_key,
				'message' => urlencode( $message ),
				'tel'     => $to_phone,
			);

			if ( '' !== $this->_from_asid ) {
				$args['shortcode'] = $this->_from_asid;
			}

			$endpoint = 'https://bulksms.ma/developer/sms/send';

			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, add_query_arg( $args, $endpoint ) );
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
			curl_setopt( $ch, CURLOPT_TIMEOUT, 100 );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
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

			if ( ! isset( $code['success'] ) ) {
				/* translators: %s error message */
				throw new Exception( sprintf( esc_html__( 'An error has occurred: %s', 'yith-woocommerce-sms-notifications' ), $code['error'] ) );
			}

			return;

		}

	}

}
