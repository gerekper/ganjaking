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

if ( ! class_exists( 'YWSN_MessageBird' ) ) {

	/**
	 * Implements MessageBird API for YWSN plugin
	 *
	 * @class   YWSN_MessageBird
	 * @since   1.0.4
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWSN_MessageBird extends YWSN_SMS_Gateway {

		/** @var string MessageBird API Key */
		private $_messagebird_api_key;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$this->_messagebird_api_key = get_option( 'ywsn_messagebird_api_key' );

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
		 * @since   1.0.4
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function send( $to_phone, $message, $country_code ) {

			require_once( YWSN_DIR . 'includes/services/messagebird/autoload.php' );

			$to_phone = ( '+' !== substr( $to_phone, 0, 1 ) ? '+' . $to_phone : $to_phone );

			try {

				if ( '' !== $this->_from_asid ) {
					$from = $this->_from_asid;
				} else {
					$from = ( '+' !== substr( $this->_from_number, 0, 1 ) ? '+' . $this->_from_number : $this->_from_number );
				}

				$message_bird = new \MessageBird\Client( $this->_messagebird_api_key );

				$message_obj             = new \MessageBird\Objects\Message();
				$message_obj->originator = $from;
				$message_obj->recipients = array( $to_phone );
				$message_obj->body       = $message;
				$message_obj->datacoding = 'auto';

				$response = $message_bird->messages->create( $message_obj );

				$this->_log[] = $response;

				if ( $response->type ) {
					return;
				} else {
					/* translators: %s error message */
					throw new Exception( sprintf( esc_html__( 'An error has occurred: %s', 'yith-woocommerce-sms-notifications' ), $response[0] ) );
				}
			} catch ( Exception $e ) {
				/* translators: %s error message */
				throw new Exception( sprintf( esc_html__( 'An error has occurred: %s', 'yith-woocommerce-sms-notifications' ), $e->getMessage() ) );
			}

		}

	}

}
