<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_AF_Rule_Billing_Method extends WC_AF_Rule {
	private $is_enabled  = null;
	private $rule_weight = 0;
	private $unsafe_countries;
	/**
	 * The constructor
	 */
	public function __construct() {
		$this->unsafe_countries = get_option('wc_settings_anti_fraud_define_safe_billing_method_list');
		$this->is_enabled  = get_option('wc_af_safe_billing_method');
		$this->rule_weight = get_option('wc_settings_anti_fraud_unsafe_billing_method_weight');
		parent::__construct( 'billing_method', 'Ordered from a risk billing_method', $this->rule_weight );
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
		
		Af_Logger::debug('Checking billing method rule');
		$risk = false;

		$pre_wc_30  = version_compare( WC_VERSION, '3.0', '<' );
		$order_id   = $pre_wc_30 ? $order->id : $order->get_id();

		if (get_option( 'wc_af_enable_whitelist_payment_method' ) == 'yes') {

			if (get_option('wc_settings_anti_fraud_whitelist_payment_method') && null != get_option('wc_settings_anti_fraud_whitelist_payment_method')) {

				$whitelist_payment_method = get_option('wc_settings_anti_fraud_whitelist_payment_method');
				$payment_method = get_post_meta( $order_id, '_payment_method', true );

				if ( !in_array( $payment_method, $whitelist_payment_method ) ) {
					$risk = true;
				}
			}
		}
		if (get_option( 'wc_af_enable_bypass_payment_method' ) == 'yes') {

			if (get_option('wc_settings_anti_fraud_bypass_payment_method') && null != get_option('wc_settings_anti_fraud_bypass_payment_method')) {

				$bypass_payment_method = get_option('wc_settings_anti_fraud_bypass_payment_method');
				$payment_method_bypass = get_post_meta( $order_id, '_payment_method', true );
				$bypass_payment_method = explode( ',', $bypass_payment_method );

				if ( !in_array( $payment_method_bypass, $bypass_payment_method ) ) {
					$risk = true;
				}
			}
		} 
		Af_Logger::debug('billing method rule risk : ' . ( true === $risk ? 'true' : 'false' )); 
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
