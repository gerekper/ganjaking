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

if ( ! class_exists( 'YWSN_IntelliSMS' ) ) {

	/**
	 * Implements IntelliSMS API for YWSN plugin
	 *
	 * @class   YWSN_IntelliSMS
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWSN_IntelliSMS extends YWSN_SMS_Gateway {

		/** @var string intellisms mobile */
		private $_intellisms_user;

		/** @var string intellisms password */
		private $_intellisms_pass;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$this->_intellisms_user = get_option( 'ywsn_intellisms_user' );
			$this->_intellisms_pass = get_option( 'ywsn_intellisms_pass' );

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
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function send( $to_phone, $message, $country_code ) {

			$is_unicode = ! empty( apply_filters( 'ywsn_additional_charsets', get_option( 'ywsn_active_charsets', array() ) ) );

			$from = '' !== $this->_from_asid ? $this->_from_asid : $this->_from_number;

			//APPLY_FILTERS: ywsn_max_concat_messages: maximum numbers of concatenated messages
			$max_messages = apply_filters( 'ywsn_max_concat_messages', 5 );

			$args = array(
				'username'  => $this->_intellisms_user,
				'password'  => $this->_intellisms_pass,
				'to'        => $to_phone,
				'from'      => $from,
				'text'      => $message,
				'maxconcat' => $max_messages,
			);

			if ( $is_unicode ) {
				unset( $args['message'] );
				$args['type'] = 2;
				$args['hex']  = $this->convert_unicode_hex( $message );
			}

			$args = http_build_query( $args );

			$wp_remote_http_args = array(
				'method' => 'POST',
				'body'   => $args,
				'header' => 'Content-type: application/x-www-form-urlencoded' . "\r\n" . 'Content-Length: ' . strlen( $args ) . "\r\n",
			);

			$endpoint = 'https://www.intellisoftware.co.uk/smsgateway/sendmsg.aspx';

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

			$code = explode( ':', $response['body'] );

			if ( 'ID' !== $code[0] ) {
				/* translators: %s error message */
				throw new Exception( sprintf( esc_html__( 'An error has occurred: %s', 'yith-woocommerce-sms-notifications' ), $code[1] ) );
			}

			return;

		}

		/**
		 * Convert string to HEX unicode
		 *
		 * @param $str string
		 *
		 * @return string
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function convert_unicode_hex( $str ) {
			$ucs2    = '';
			$results = '';
			$str     = bin2hex( $str );
			for ( $i = 0; $i < strlen( $str ); $i += 2 ) {
				$char1hex = $str[ $i ] . $str[ $i + 1 ];

				$char1dec = hexdec( $char1hex );
				if ( $char1dec < 128 ) {
					$results = $char1hex;
				} elseif ( $char1dec < 224 ) {
					$char2hex = $str[ $i + 2 ] . $str[ $i + 3 ];
					$results  = dechex( ( ( hexdec( $char1hex ) - 192 ) * 64 ) + ( hexdec( $char2hex ) - 128 ) );

					$i += 2;
				} elseif ( $char1dec < 240 ) {
					$char2hex = $str[ $i + 2 ] . $str[ $i + 3 ];
					$char3hex = $str[ $i + 4 ] . $str[ $i + 5 ];
					$results  = dechex( ( ( hexdec( $char1hex ) - 224 ) * 4096 ) + ( ( hexdec( $char2hex ) - 128 ) * 64 ) + ( hexdec( $char3hex ) - 128 ) );

					$i += 4;
				} else {
					//Not supported: UCS-2 only
					$i += 6;
				}

				while ( strlen( $results ) < 4 ) {
					$results = '0' . $results;
				}

				$ucs2 .= $results;
			}

			return $ucs2;
		}

	}

}
