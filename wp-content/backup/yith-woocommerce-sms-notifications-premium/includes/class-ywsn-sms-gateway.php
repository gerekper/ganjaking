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

if ( ! class_exists( 'YWSN_SMS_Gateway' ) ) {

	/**
	 * SMS Gateway abstract class
	 *
	 * @class   YWSN_SMS_Gateway
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	abstract class YWSN_SMS_Gateway {

		/**
		 * @var string the number SMS messages will be sent from
		 */
		protected $_from_number;

		/**
		 * @var string using Alphanumeric Sender ID
		 */
		protected $_from_asid;

		/**
		 * @var array the response of the SMS service
		 */
		protected $_log;

		/**
		 * @var array the response of the SMS service
		 */
		protected $_logger;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$this->_from_asid   = substr( get_option( 'ywsn_from_asid' ), 0, 11 );
			$this->_from_number = preg_replace( '[\D]', '', get_option( 'ywsn_from_number' ) );
			$this->_logger      = wc_get_logger();

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

			die( 'function YWSN_SMS_Gateway->send() must be over-ridden in a sub-class.' );

		}

		/**
		 * Print send log
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function print_log() {

			error_log( print_r( $this->_log, true ) );
			update_option( 'ywsn_debug_log', print_r( $this->_log, true ) );

		}

		/**
		 * Write send log
		 *
		 * @param   $args array
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function write_log( $args ) {

			$context   = array( 'source' => 'ywsn' );
			$object_id = '';

			if ( $args['object'] instanceof WC_Order ) {
				$object_id = 'Order #' . $args['object']->get_id() . ' - ';
			} elseif ( $args['object'] instanceof YITH_WCBK_Booking ) {
				$object_id = 'Booking #' . $args['object']->get_id() . ' - ';
			}

			$log[] = strtoupper( $object_id . $args['type'] . ' MESSAGE' );
			$log[] = 'Status: ' . ( $args['success'] ? 'SUCCESS' : 'FAILED - ' . $args['status_message'] );
			$log[] = 'Phone: ' . $args['phone'];
			$log[] = 'Message: ' . $args['message'];

			$log = implode( "\r\n", $log );

			if ( $args['success'] ) {
				$this->_logger->info( $log, $context );
			} else {
				$this->_logger->error( $log, $context );
			}

		}

	}

}
