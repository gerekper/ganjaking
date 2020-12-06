<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WC_AF_Rule_Billing_Matches_Shipping' ) ) {
	class WC_AF_Rule_Billing_Matches_Shipping extends WC_AF_Rule {
		private $is_enabled  = false;
		private $rule_weight = 0;	
		/**
		 * The constructor
		 */
		public function __construct() {
			$this->is_enabled  =  get_option('wc_af_bca_order');
			$this->rule_weight = get_option('wc_settings_anti_fraud_bca_order_weight');

			parent::__construct( 'billing_matches_shipping', 'Billing address does not match shipping address', $this->rule_weight );
		}

		/**
		 * Do the required check in this method. The method must return a boolean.
		 *
		 * @param WC_Order $order
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return bool
		 */
		public function is_risk( WC_Order $order ) {

			// Default risk is false
			$risk = false;

			// Check if the billing address does not match shipping address
			if ( $this->has_shipping_address( $order ) && $order->get_formatted_billing_address() != $order->get_formatted_shipping_address() ) {
				$risk = true;
			}

			return $risk;
		}

		/**
		 * Check if an order has a shipping address.
		 *
		 * @param WC_Order $order
		 * @return bool
		 */
		protected function has_shipping_address( $order ) {
			if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
				return $order->shipping_address_1 || $order->shipping_address_2;
			}

			return $order->has_shipping_address();
		}
		//Enable rule check
		public function is_enabled() {
			if ('yes' == $this->is_enabled) {
				return true;
			}
			return false;
		}
	}
}
