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

if ( ! class_exists( 'YWAF_Addresses_Matching' ) ) {

	/**
	 * Addresses matching rules class
	 *
	 * @class   YWAF_Addresses_Matching
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWAF_Addresses_Matching extends YWAF_Rules {

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$message = __( 'Billing address and shipping address do not match.', 'yith-woocommerce-anti-fraud' );
			$points  = get_option( 'ywaf_rules_addresses_matching_weight', 10 );

			parent::__construct( $message, $points );

		}

		/**
		 * Check if billing address matches shipping address.
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

			if ( $order->get_formatted_billing_address() != $order->get_formatted_shipping_address() ) {

				$fraud_risk = true;

			}

			return $fraud_risk;

		}

	}

}