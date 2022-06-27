<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_AF_Rule_International_Order extends WC_AF_Rule {
	private $is_enabled  = false;
	private $rule_weight = 0;
	/**
	 * The constructor
	 */
	public function __construct() {
		$this->is_enabled  =  get_option('wc_af_international_order');
		$this->rule_weight = get_option('wc_settings_anti_fraud_international_order_weight');
		
		parent::__construct( 'international_order', 'Order is an international order.', $this->rule_weight );
	}

	/**
	 * Do the required check in this method. The method must return a boolean.
	 *
	 * @param WC_Order $order
	 *
	 * @since  1.0.0
	 *
	 * @return bool
	 */
	public function is_risk( WC_Order $order ) {
		
		Af_Logger::debug('Checking international order rule');
		// Default risk is false
		$risk = false;

		// Get store country
		$store_country = WC()->countries->get_base_country();

		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			$billing_country = $order->billing_country;
			$shipping_country = $order->shipping_country;
		} else {
			$billing_country = $order->get_billing_country();
			$shipping_country = $order->get_shipping_country();
		}

		// Check if store country differs from billing or shipping country
		if ( ( $store_country != $billing_country && !empty($billing_country) )  || ( $store_country != $shipping_country && !empty($shipping_country) ) ) {
			$risk = true;
		}
		
		Af_Logger::debug('international order rule risk : ' . ( true === $risk ? 'true' : 'false' ));
		return $risk;
	}
	//Enable rule check
	public function is_enabled() {
		if ('yes' == $this->is_enabled) {
			return true;
		}
		return false;
	}
}
