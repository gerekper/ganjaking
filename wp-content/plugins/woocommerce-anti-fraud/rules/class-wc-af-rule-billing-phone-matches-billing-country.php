<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WC_AF_Rule_Billing_Phone_Matches_Billing_Country' ) ) {
	class WC_AF_Rule_Billing_Phone_Matches_Billing_Country extends WC_AF_Rule {
		private $is_enabled  = false;
    	private $rule_weight = 0;	
		/**
		 * The constructor
		 */
		public function __construct() {
			$this->is_enabled  =  get_option('wc_af_billing_phone_number_order');
			$this->rule_weight = get_option('wc_settings_anti_fraud_billing_phone_number_order_weight');
			parent::__construct( 'Billing_Phone_Matches_Billing_Country', 'Billing phone number does not match with Billing country',$this->rule_weight );
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

			// Get store country
			$store_country = WC()->countries->get_base_country();
			$billing_country = $order->get_billing_country();
			$billing_phone = $order->get_billing_phone();
			$trim_billing_phone = trim($billing_phone);
			$billing_phone = preg_match( '!\(([^\)]+)\)!', $trim_billing_phone, $match );
			$billing_phone_code = @$match[1];
			$billing_phone_code = preg_replace("/\s+/", "", $billing_phone_code);
			$calling_code = '';

	    	if( $billing_country ) {

	        	$calling_code = WC()->countries->get_country_calling_code( $billing_country );
	        	$calling_code = is_array( $calling_code ) ? $calling_code[0] : $calling_code;
	   	 	}
	   	 	
	   	 	if ($calling_code == $billing_phone_code) {
				
				$risk = false;

	   	 	} else {
	   	 	
		   	 	if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
					$billing_country = $order->billing_country;
					$shipping_country = $order->shipping_country;
				} else {
					$billing_country = $order->get_billing_country();
					$shipping_country = $order->get_shipping_country();
				}

				// Check if store country differs from billing or shipping country
				if ( ($store_country != $billing_country && !empty($billing_country))  || ($store_country != $shipping_country && !empty($shipping_country) ) ) {
					
					$risk = true;
				}
	   	 	}
			return $risk;
		}

		//Enable rule check
		public function is_enabled(){
			if('yes' == $this->is_enabled){
				return true;
			}
			return false;
		}
	}
}
