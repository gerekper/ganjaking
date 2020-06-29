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

if ( ! class_exists( 'YWSN_SMS_Office' ) ) {

	/**
	 * Implements SMS Office API for YWSN plugin
	 *
	 * @class   YWSN_SMS_Office
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWSN_SMS_Office extends YWSN_SMS_Gateway {

		/** @var string sms_office mobile */
		private $_sms_office_key;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$this->_sms_office_key = get_option( 'ywsn_sms_office_key' );

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

			if ( '' !== $this->_from_asid ) {
				$from = $this->_from_asid;
			} else {
				$from = ( '+' !== substr( $this->_from_number, 0, 1 ) ? '+' . $this->_from_number : $this->_from_number );
			}

			$args = array(
				'key'         => $this->_sms_office_key,
				'sender'      => $from,
				'destination' => $to_phone,
				'content'     => urlencode( $message ),

			);

			$endpoint = 'http://smsoffice.ge/api/v2/send';

			// perform HTTP request with endpoint / args
			$response = wp_remote_get( add_query_arg( $args, $endpoint ) );

			// WP HTTP API error like network timeout, etc
			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			}

			$this->_log[] = $response;

			// Check for proper response / body
			if ( ! isset( $response['body'] ) ) {
				throw new Exception( esc_html__( 'No answer', 'yith-woocommerce-sms-notifications' ) );
			}

			$result = json_decode( $response['body'] );

			if ( ! $result->Success ) { //phpcs:ignore
				/* translators: %1$s error code, %2$s error message */
				throw new Exception( sprintf( esc_html__( 'An error has occurred: %1$s %2$s', 'yith-woocommerce-sms-notifications' ), $result->ErrorCode, $result->Message ) );//phpcs:ignore
			}

			return;

		}

	}

}
