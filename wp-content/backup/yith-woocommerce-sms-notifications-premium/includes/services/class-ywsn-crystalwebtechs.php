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

if ( ! class_exists( 'YWSN_Crystalwebtechs' ) ) {

	/**
	 * Implements Crystalwebtechs API for YWSN plugin
	 *
	 * @class   YWSN_Crystalwebtechs
	 * @since   1.1.1
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWSN_Crystalwebtechs extends YWSN_SMS_Gateway {

		/** @var string crystalwebtechs Username */
		private $_crystalwebtechs_username;

		/** @var string crystalwebtechs password */
		private $_crystalwebtechs_password;

		/** @var string crystalwebtechs sender */
		private $_crystalwebtechs_sender;

		/** @var string crystalwebtechs channel */
		private $_crystalwebtechs_channel;

		/** @var string crystalwebtechs route id */
		private $_crystalwebtechs_route_id;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.1.1
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$this->_crystalwebtechs_username = get_option( 'ywsn_crystalwebtechs_username' );
			$this->_crystalwebtechs_password = get_option( 'ywsn_crystalwebtechs_pass' );
			$this->_crystalwebtechs_sender   = get_option( 'ywsn_crystalwebtechs_sender' );
			$this->_crystalwebtechs_channel  = get_option( 'ywsn_crystalwebtechs_sender_channel_type' );
			$this->_crystalwebtechs_route_id = get_option( 'ywsn_crystalwebtechs_route_id' );

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
		 * @since   1.1.1
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function send( $to_phone, $message, $country_code ) {

			$dcs = empty( apply_filters( 'ywsn_additional_charsets', get_option( 'ywsn_active_charsets', array() ) ) ) ? '0' : '8';

			$args = array(
				'user'     => $this->_crystalwebtechs_username,
				'password' => $this->_crystalwebtechs_password,
				'senderid' => $this->_crystalwebtechs_sender,
				'channel'  => $this->_crystalwebtechs_channel,
				'DCS'      => $dcs,
				'flashsms' => '0',
				'number'   => $to_phone,
				'text'     => urlencode( $message ),
				'route'    => $this->_crystalwebtechs_route_id,
			);

			$endpoint = 'http://websms.mysmsshop.com/api/mt/SendSMS';

			$url = add_query_arg( $args, $endpoint );

			// perform HTTP request with endpoint / args
			$response = wp_remote_get( $url );

			// WP HTTP API error like network timeout, etc
			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			}

			$this->_log[] = $response;

			// Check for proper body
			if ( ! isset( $response['body'] ) ) {
				throw new Exception( esc_html__( 'No answer', 'yith-woocommerce-sms-notifications' ) );
			}

			$result = json_decode( $response['body'], true );

			if ( '0' !== $result['ErrorCode'] ) {
				/* translators: %s error message */
				throw new Exception( sprintf( esc_html__( 'An error has occurred: %s', 'yith-woocommerce-sms-notifications' ), $result['ErrorMessage'] ) );
			}

			return;

		}

	}

}
