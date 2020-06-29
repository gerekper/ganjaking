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

if ( ! class_exists( 'YWSN_Bulk_SMS_SA' ) ) {

	/**
	 * Implements Bulk SMS (SA) API for YWSN plugin
	 *
	 * @class   YWSN_Bulk_SMS_SA
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWSN_Bulk_SMS_SA extends YWSN_SMS_Gateway {

		/** @var string bulk_sms_sa user */
		private $_bulk_sms_sa_user;

		/** @var string bulk_sms_sa pass */
		private $_bulk_sms_sa_pass;

		/** @var string bulk_sms_sa sender */
		private $_bulk_sms_sa_sender;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$this->_bulk_sms_sa_user   = get_option( 'ywsn_bulk_sms_sa_user' );
			$this->_bulk_sms_sa_pass   = get_option( 'ywsn_bulk_sms_sa_pass' );
			$this->_bulk_sms_sa_sender = get_option( 'ywsn_bulk_sms_sa_sender' );

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
				'comm'    => 'sendsms',
				'user'    => $this->_bulk_sms_sa_user,
				'pass'    => $this->_bulk_sms_sa_pass,
				'to'      => $to_phone,
				'message' => urlencode( $message ),
				'sender'  => $this->_bulk_sms_sa_sender,
			);

			$endpoint = 'http://www.bulksms-sa.info/api.php';

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

			$code = explode( ':', $response['body'] );

			if ( 1 !== (int) $code[0] ) {
				/* translators: %s error message */
				throw new Exception( sprintf( esc_html__( 'An error has occurred: %s', 'yith-woocommerce-sms-notifications' ), $code[0] ) );
			}

			return;

		}

	}

}
