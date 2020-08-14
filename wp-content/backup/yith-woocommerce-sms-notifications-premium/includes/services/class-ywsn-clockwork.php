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

if ( ! class_exists( 'YWSN_Clockwork' ) ) {

	/**
	 * Implements Clockwork API for YWSN plugin
	 *
	 * @class   YWSN_Clockwork
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWSN_Clockwork extends YWSN_SMS_Gateway {

		/** @var string clockwork API Key */
		private $_clockwork_api_key;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$this->_clockwork_api_key = get_option( 'ywsn_clockwork_api_key' );

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

			require_once( YWSN_DIR . 'includes/services/clockwork/class-Clockwork.php' );

			try {

				if ( '' !== $this->_from_asid ) {
					$from = $this->_from_asid;
				} else {
					$from = $this->_from_number;
				}

				$clockwork    = new Clockwork( $this->_clockwork_api_key );
				$message      = array(
					'from'    => $from,
					'to'      => $to_phone,
					'message' => $message,
				);
				$response     = $clockwork->send( $message );
				$this->_log[] = $response;

				if ( $response['success'] ) {
					return;
				} else {
					/* translators: %1$s error code, %2$s error message */
					throw new Exception( sprintf( esc_html__( 'An error has occurred: %1$s %2$s', 'yith-woocommerce-sms-notifications' ), $response['error_code'], $response['error_message'] ) );
				}
			} catch ( ClockworkException $e ) {
				/* translators: %s error message */
				throw new Exception( sprintf( esc_html__( 'An error has occurred: %s', 'yith-woocommerce-sms-notifications' ), $e->getMessage() ) );
			}

		}

	}

}

