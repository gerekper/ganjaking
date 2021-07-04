<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_AF_Rule_Country extends WC_AF_Rule {
	private $is_enabled  = null;
	private $rule_weight = 0;
	private $unsafe_countries;
	/**
	 * The constructor
	 */
	public function __construct() {
		$this->unsafe_countries = get_option('wc_settings_anti_fraud_define_unsafe_countries_list');
		$this->is_enabled  = get_option('wc_af_unsafe_countries');
		$this->rule_weight = get_option('wc_settings_anti_fraud_unsafe_countries_weight');
		parent::__construct( 'country', 'Ordered from a risk country', $this->rule_weight );
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
		Af_Logger::debug('Checking country rule');
		// Orders from these countries are considered a risk unless the shop is located in the same country
		//$risk_countries = apply_filters( 'wc_af_rule_countries', $this->unsafe_countries );
		
		$risk_countries = $this->unsafe_countries;

		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			$billing_country = $order->billing_country;
			$shipping_country = $order->shipping_country;
		} else {
			$billing_country = $order->get_billing_country();
			$shipping_country = $order->get_shipping_country();
		}

		// Default risk is false
		$risk = false;
		// Check if the billing or shipping country is considered a risk country
		
		if (!empty($risk_countries)) {

			if ( ( true === in_array( $billing_country, $risk_countries ) ) || ( true === in_array( $shipping_country, $risk_countries ) ) ) {
				$risk = true;
				Af_Logger::debug('billing country ' .$billing_country. ' is at risk');
				if ( !empty($shipping_country) ) {
					Af_Logger::debug('shipping country ' .$shipping_country. ' is at risk');
				}
			}
		}

		if ( true === $risk ) {

			// Get store country
			$store_country = WC()->countries->get_base_country();

			// There is no risk if the billing and shipping country are equal to the store country
			if ( $store_country == $billing_country && $store_country == $shipping_country ) {
				
				$risk = false;
				Af_Logger::debug('billing country ' .$billing_country. ' is base country.');
				if ( !empty($shipping_country) ) {
					Af_Logger::debug('shipping country ' .$shipping_country. ' is base country.');
				}
			}
		}
		Af_Logger::debug('country rule risk : '. ( $risk===true ? 'true' : 'false' ));
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
