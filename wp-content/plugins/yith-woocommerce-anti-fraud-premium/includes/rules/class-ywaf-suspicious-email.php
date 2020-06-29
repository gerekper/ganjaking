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

if ( ! class_exists( 'YWAF_Suspicious_Email' ) ) {

	/**
	 * Suspicious email rules class
	 *
	 * @class   YWAF_Suspicious_Email
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWAF_Suspicious_Email extends YWAF_Rules {

		private $defined_domains = array();

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->defined_domains = explode( ',', get_option( 'ywaf_rules_suspicious_email_domains' ) );

			$message = __( 'The billing email address used for the order has a suspicious domain.', 'yith-woocommerce-anti-fraud' );
			$points  = get_option( 'ywaf_rules_suspicious_email_weight', 10 );

			parent::__construct( $message, $points );

		}

		/**
		 * Check if order billing has a suspicious email.
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
			$domains    = $this->defined_domains;
			$regex      = preg_match( "`@([a-zA-z0-9\-\_]+(?:\.[a-zA-Z]{0,5}){0,2})$`", $order->get_billing_email(), $domain_to_check );

			if ( $regex === 1 ) {

				if ( in_array( $domain_to_check[1], $domains ) ) {
					$fraud_risk = true;
				}

			}

			return $fraud_risk;

		}

	}
}