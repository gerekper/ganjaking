<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_AF_Rule_Ip_Location extends WC_AF_Rule {
	private $is_enabled  = false;
	private $rule_weight = 0;
	private $is_maxmind_auth = false;
	/**
	 * The constructor
	 */
	public function __construct() {
		$this->is_enabled  =  get_option('wc_af_ip_geolocation_order');
		$this->rule_weight = get_option('wc_settings_anti_fraud_ip_geolocation_order_weight');
		$this->is_maxmind_auth = get_option('wc_af_maxmind_authentication');
		
		parent::__construct( 'ip_location', 'Customer IP address did not match given billing country.', $this->rule_weight );
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
		Af_Logger::debug('Checking ip address rule');
		global $wpdb;
		// Default risk is false
		$risk = false;

		if ($this->is_maxmind_auth) {

			// Set IP address in var
			$ip_address = $order->get_customer_ip_address();
			$billing_country = $order->get_billing_country();
			$billing_city = $order->get_billing_city();
			$maxmind_user = get_option( 'wc_af_maxmind_user' );
			$maxmind_license_key = get_option( 'wc_af_maxmind_license_key' );
			$authkey = 'Basic ' . base64_encode( $maxmind_user . ':' . $maxmind_license_key );
			
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://geoip.maxmind.com/geoip/v2.1/insights/'.$ip_address,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERAGENT => 'AnTiFrAuDOPMC',
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_HTTPHEADER => array(
				'authorization:' . $authkey,
				'cache-control: no-cache',
				'content-type: application/json',
				),
			));

			$response = curl_exec($curl);
			curl_close($curl);
			$response = json_decode( $response, true );
			// Af_Logger::debug(print_r($response,true));
			Af_Logger::debug('ip city : ' . $response['city']['names']['en']);
			Af_Logger::debug('ip country : ' . $response['country']['iso_code']);
			
			if ( !empty( $response ) && !isset( $response['error'] ) ) {
				if ( isset( $response['country']['iso_code'] ) && !empty( $response['country']['iso_code'] ) ) {
					if ( ( $billing_country == $response['country']['iso_code'] ) ) {
						$risk = false;
						Af_Logger::debug('Customer IP address matched given billing country '.$billing_country.' and city '.$billing_city);
					} else {
						$risk = true;
						Af_Logger::debug('Customer IP address not matched given billing country '.$billing_country.' and city '.$billing_city);
					}
				} else {
					$risk = true;
					Af_Logger::debug('Customer IP address not matched given billing country '.$billing_country.' and city '.$billing_city);
				}
			} else {
				$risk = true;
				Af_Logger::debug('Customer IP address not matched given billing country '.$billing_country.' and city '.$billing_city);
			}
		} else {
			$risk = false;
			Af_Logger::debug('Maxmind creds not authenticated. IP Address rule is disabled.');
		}
		Af_Logger::debug('ip address rule risk : ' . ( $risk===true ? 'true' : 'false' ));
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
