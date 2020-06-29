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

if ( ! class_exists( 'YWAF_High_Amount_Fixed' ) ) {

	/**
	 * High amount rules class
	 *
	 * @class   YWAF_High_Amount_Fixed
	 * @package Yithemes
	 * @since   1.0.5
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWAF_High_Amount_Fixed extends YWAF_Rules {

		private $amount_limit = null;

		/**
		 * Constructor
		 *
		 * @since   1.0.5
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->amount_limit = get_option( 'ywaf_rules_high_amount_fixed_limit', 0 );

			$message = sprintf( __( 'This order amount is bigger than %s.', 'yith-woocommerce-anti-fraud' ), wc_price( $this->amount_limit ) );
			$points  = get_option( 'ywaf_rules_high_amount_fixed_weight', 10 );

			parent::__construct( $message, $points );

		}

		/**
		 * Check if order is higher than the amount limit.
		 *
		 * @since   1.0.5
		 *
		 * @param   $order WC_Order
		 *
		 * @return  boolean
		 * @author  Alberto Ruggiero
		 */
		public function get_fraud_risk( $order ) {

			$fraud_risk = false;

			if ( ( $this->amount_limit > 0 ) && $order->get_total() > ( $this->amount_limit ) ) {
				$fraud_risk = true;
			}

			return $fraud_risk;

		}

	}

}