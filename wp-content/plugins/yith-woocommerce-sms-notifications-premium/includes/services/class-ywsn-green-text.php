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

if ( ! class_exists( 'YWSN_Green_Text' ) ) {

	/**
	 * Implements Green Text API for YWSN plugin
	 *
	 * @class   YWSN_Green_Text
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWSN_Green_Text extends YWSN_SMS_Gateway {

		/** @var string green_text mobile */
		private $_green_text_user;

		/** @var string green_text password */
		private $_green_text_pass;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$this->_green_text_user = get_option( 'ywsn_green_text_user' );
			$this->_green_text_pass = get_option( 'ywsn_green_text_pass' );

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

			$to_phone       = ( '+' !== substr( $to_phone, 0, 1 ) ? '+' . $to_phone : $to_phone );
			$charactersetid = empty( apply_filters( 'ywsn_additional_charsets', get_option( 'ywsn_active_charsets', array() ) ) ) ? 2 : 1;

			$from         = '' !== $this->_from_asid ? $this->_from_asid : $this->_from_number;
			$reply_method = '' !== $this->_from_asid ? 1 : 4;

			$args = array(
				'method'                 => 'sendsms',
				'returncsvstring'        => 'true',
				'externallogin'          => $this->_green_text_user,
				'password'               => $this->_green_text_pass,
				'clientbillingreference' => '0',
				'clientmessagereference' => '0',
				'destinations'           => urlencode( $to_phone ),
				'originator'             => $from,
				'body'                   => urlencode( $message ),
				'charactersetid'         => $charactersetid,
				'replymethodid'          => $reply_method,
				'replydata'              => '',
				'statusnotificationurl'  => '',
				'validity'               => '72',
			);

			$endpoint = 'http://www.textapp.net/webservice/httpservice.aspx';

			// perform HTTP request with endpoint / args
			$url = add_query_arg( $args, $endpoint );

			// perform HTTP request with endpoint / args
			$response = wp_remote_get( $url );

			// WP HTTP API error like network timeout, etc
			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			}

			$this->_log[] = $response;

			// Check for proper response / body
			if ( ! isset( $response['body'] ) ) {
				throw new Exception( esc_html__( 'No answer', 'yith-woocommerce-sms-notifications' ) );
			}

			$code = substr( $response['body'], 1, strpos( $response['body'], '#', 1 ) - 1 );

			if ( 1 !== (int) $code ) {
				/* translators: %s error message */
				throw new Exception( sprintf( esc_html__( 'An error has occurred: %s', 'yith-woocommerce-sms-notifications' ), $response['body'] ) );
			}

			return;

		}

	}

}
