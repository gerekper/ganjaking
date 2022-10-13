<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_AF_Rule_Geo_Location extends WC_AF_Rule {
	//exit();
	private $is_enabled  = false;
	private $rule_weight = 0;

	/**
	 * The constructor
	 */
	public function __construct() {
		$this->is_enabled  =  get_option('wc_af_geolocation_order');
		$this->rule_weight = get_option('wc_settings_anti_fraud_geolocation_order_weight');
		
		parent::__construct( 'geo_location', 'Customer order geo location mismatches billing/shipping address.', $this->rule_weight );
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
		
		Af_Logger::debug('Checking Geo address rule');
		
		// Default risk is false
		$risk = false;

		if ($this->is_enabled) {

			if ( $order->get_billing_address_1() != $order->get_shipping_address_1() ) {
				$customer_city = strtolower($order->get_billing_city());
				$customer_state = $order->get_billing_state();
				
			} else {
				$customer_city = strtolower($order->get_shipping_city());
				$customer_state = $order->get_shipping_state();
			}

			$country = strtolower($order->get_billing_country());
				
			$customer_state = $order->get_billing_state();
			$c_state = strtolower(WC()->countries->get_states( $country )[$customer_state]);
			

			$html_geo_loc_state = get_option('html_geo_loc_state');
			$html_geo_loc_city = get_option('html_geo_loc_city');
			$html_geo_loc_cntry = get_option('html_geo_loc_cntry');


			if (( !empty($html_geo_loc_city) && !empty($customer_city) ) && ( $customer_city == $html_geo_loc_city )) {
				Af_Logger::debug('Geo Location is matches billing/shipping City.');
				$risk = false;

			} elseif (( !empty($c_state) && !empty($c_state) ) && ( $c_state == $html_geo_loc_state )) {
				Af_Logger::debug('Geo Location is matches billing/shipping State.');
				$risk = false;

			} elseif (( !empty($html_geo_loc_cntry) && !empty($country) ) && ( $country == $html_geo_loc_cntry )) {
				Af_Logger::debug('Geo Location is matches billing/shipping country.');
				$risk = false;
			} else {
				Af_Logger::debug('Geo Location is not matches billing/shipping address.');
				$risk = true;
			}
			
		} else {
			$risk = false;
			Af_Logger::debug('Geo Location rule is disabled.');
		}
		fclose($fp);
		Af_Logger::debug('Geo Location rule risk : ' . ( true === $risk ? 'true' : 'false' ));
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
