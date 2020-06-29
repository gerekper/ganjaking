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

if ( ! class_exists( 'YWAF_International_Order' ) ) {

	/**
	 * International order rules class
	 *
	 * @class   YWAF_International_Order
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWAF_International_Order extends YWAF_Rules {

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$message = __( 'This is an international order.', 'yith-woocommerce-anti-fraud' );
			$points  = get_option( 'ywaf_rules_international_order_weight', 10 );

			parent::__construct( $message, $points );

		}

		/**
		 * Check if this is an international order.
		 *
		 * @since   1.0.0
		 *
		 * @param   $order WC_Order
		 *
		 * @return  boolean
		 * @author  Alberto Ruggiero
		 */
		public function get_fraud_risk( $order ) {

			$fraud_risk       = false;
			$shop_country     = WC()->countries->get_base_country();
			$billing_country  = $order->get_billing_country();
			$shipping_country = $order->get_shipping_country();

			if ( ( $shop_country != $billing_country && $billing_country != '' ) || ( $shop_country != $shipping_country && $shipping_country != '' ) ) {
				$fraud_risk = true;
			}

			return $fraud_risk;

		}

	}

}