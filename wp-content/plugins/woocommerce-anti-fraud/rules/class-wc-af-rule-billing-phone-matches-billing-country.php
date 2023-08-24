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
			$this->is_enabled  = get_option( 'wc_af_billing_phone_number_order' );
			$this->rule_weight = get_option( 'wc_settings_anti_fraud_billing_phone_number_order_weight' );
			parent::__construct( 'Billing_Phone_Matches_Billing_Country', 'Billing phone number does not match with Billing country', $this->rule_weight );
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

			Af_Logger::debug( 'Checking billing phone matches billing country rule' );
			// Default risk is false
			$risk = false;

			// Get store country
			$store_country = WC()->countries->get_base_country();
			$billing_country = $order->get_billing_country();
			$billing_phone = $order->get_billing_phone();
			$num = trim( $billing_phone );
			$new_num = str_replace( ' ', '', $num );
			$phone = preg_replace( '/[^A-Za-z0-9\-]/', '', $new_num );
			$phones[] = $phone;
				// get your list of country codes

			require_once( plugin_dir_path( __FILE__ ) . 'country-code-list.php' );

			$ccodes = countrycodelist( @$countryArray );

			krsort( $ccodes );

			foreach ( $phones as $pn ) {
				foreach ( $ccodes as $key => $value ) {
					if ( substr( $pn, 0, strlen( $key ) ) == $key ) {
						// match
						$country[ $pn ] = $value;
						break;
					}
				}
			}
			$billing_phone_code = array_search( $value, $ccodes, true );
			$billing_phone_code = '+' . $billing_phone_code;
			$calling_code = '';

			if ( $billing_country ) {

				$calling_code = WC()->countries->get_country_calling_code( $billing_country );
				$calling_code = is_array( $calling_code ) ? $calling_code[0] : $calling_code;
			}
			if ( $calling_code == $billing_phone_code ) {

				$risk = false;

			} else {

				$risk = true;
			}

			Af_Logger::debug( 'billing phone matches billing country rule risk : ' . ( true === $risk ? 'true' : 'false' ) );
			return $risk;
		}

		// Enable rule check
		public function is_enabled() {
			if ( 'yes' == $this->is_enabled ) {
				return true;
			}
			return false;
		}
	}
}
