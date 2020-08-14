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

if ( ! class_exists( 'YWAF_Blacklist' ) ) {

	/**
	 * Blacklist rules class
	 *
	 * @class   YWAF_Blacklist
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWAF_Blacklist extends YWAF_Rules {

		private $blacklist = array();

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$value           = get_option( 'ywaf_email_blacklist_list' );
			$this->blacklist = ( $value != '' ) ? explode( ',', $value ) : array();

			$message = __( 'The email address is blacklisted!', 'yith-woocommerce-anti-fraud' );
			$points  = 100;

			parent::__construct( $message, $points );

		}

		/**
		 * Check if email is in blacklist.
		 *
		 * @since   1.0.0
		 *
		 * @param   $order WC_Order
		 *
		 * @return  boolean
		 * @author  Alberto Ruggiero
		 */
		public function get_fraud_risk( $order ) {

			$fraud_risk = false;

			if ( in_array( $order->get_billing_email(), $this->blacklist ) ) {
				$fraud_risk = true;
			}

			return apply_filters( 'ywaf_get_fraud_risk', $fraud_risk, $order );

		}

		/**
		 * Add email to blacklist.
		 *
		 * @since   1.0.0
		 *
		 * @param   $order WC_Order
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function add_to_blacklist( $order ) {

			$this->blacklist[] = $order->get_billing_email();
			update_option( 'ywaf_email_blacklist_list', implode( ',', $this->blacklist ) );

		}

	}

}