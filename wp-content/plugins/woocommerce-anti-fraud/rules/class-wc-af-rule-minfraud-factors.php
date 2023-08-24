<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_AF_Rule_MinFraud_Factors extends WC_AF_Rule {
	private $is_enabled  = false;
	private $rule_weight = 0;
	private $minimum_minfraud_score;
	/**
	 * The constructor
	 */
	public function __construct() {
		$this->minimum_minfraud_score   = get_option( 'wc_settings_anti_fraud_minfraud_factors_risk_score' );
		$this->is_enabled  = get_option( 'wc_af_maxmind_factors' );
		$this->rule_weight = get_option( 'wc_settings_anti_fraud_minfraud_factors_order_weight' );
		parent::__construct( 'minfraud', 'Score returned by Minfraud Factors exceeds the allowed value.', $this->rule_weight );
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
		Af_Logger::debug( 'Checking minfraud factors rule' );
		global $wpdb;

		$minfraud_score = $this->call_maxmind_api_on_order_place( $order );
		// Default risk is false
		$risk = false;

		if ( $this->minimum_minfraud_score < $minfraud_score ) {

			$risk = true;
		}
		Af_Logger::debug( 'minfraud factors rule risk : ' . ( true === $risk ? 'true' : 'false' ) );
		return $risk;
	}

	public function call_maxmind_api_on_order_place( $order ) {

		$order = wc_get_order( $order ); // getting order Object

		if ( false === $order ) {
			return false;
		}

		/*
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			//check ip from share internet
			$ip_address = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			//to check ip is pass from proxy
			$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip_address = $_SERVER['REMOTE_ADDR'];
		} */

		$maxmind_user = get_option( 'wc_af_maxmind_user' );
		$maxmind_license_key = get_option( 'wc_af_maxmind_license_key' );
		$authkey = 'Basic ' . base64_encode( $maxmind_user . ':' . $maxmind_license_key );
		$ip_address = $order->get_customer_ip_address();
		$agent = $order->get_customer_user_agent();
		$maxmind_user = get_option( 'wc_af_maxmind_user' );
		$maxmind_license_key = get_option( 'wc_af_maxmind_license_key' );
		$authkey = 'Basic ' . base64_encode( $maxmind_user . ':' . $maxmind_license_key );
		// $agent = $_SERVER["HTTP_USER_AGENT"];
		$order_items = $order->get_items();
		$currency_symbol = get_woocommerce_currency();
		$shipping_total = $order->get_total();
		$payment_title = $order->get_payment_method_title();
		// Iterating through each item in the order
		foreach ( $order_items as $item_id => $item_data ) {

			$product_name = $item_data['name'];
			$item_quantity = wc_get_order_item_meta( $item_id, '_qty', true );
			$product_id = $item_data['product_id'];
			// $product_cat = get_the_terms( $product_id, 'product_cat', true );
			$price = opmc_hpos_get_post_meta( $product_id, '_regular_price', true );
		}
		// $agent = $_SERVER["HTTP_USER_AGENT"];

		$data = array(
			'device' => array(
				'ip_address' => $ip_address,
				'user_agent' => $agent,
				'accept_language' => 'en-US,en;q=0.8',
			),
			'shipping' => array(
				'first_name' => $order->get_shipping_first_name(),
				'last_name' => $order->get_shipping_last_name(),
				'company' => $order->get_shipping_company(),
				'address' => $order->get_shipping_address_1(),
				'address_2' => $order->get_shipping_address_2(),
				'city' => $order->get_shipping_city(),
				'region' => $order->get_shipping_state(),
				'country' => $order->get_shipping_country(),
				'postal' => $order->get_shipping_postcode(),
				'phone_number' => $order->get_billing_phone(),
			),
			'billing' => array(
				'first_name' => $order->get_billing_first_name(),
				'last_name' => $order->get_billing_last_name(),
				'company' => $order->get_billing_company(),
				'address' => $order->get_billing_address_1(),
				'address_2' => $order->get_billing_address_2(),
				'city' => $order->get_billing_city(),
				'region' => $order->get_billing_state(),
				'country' => $order->get_billing_country(),
				'postal' => $order->get_billing_postcode(),
				'phone_number' => $order->get_billing_phone(),
			),
			'payment' => array(
				'processor' => $payment_title,
				'was_authorized' => false,
			),
			'order' => array(
				'amount' => $shipping_total,
				'currency' => $currency_symbol,
			),
			'shopping_cart' => array(
				array(
					'item_id' => $product_id,
					'quantity' => $item_quantity,
					'price' => $price,
				),
			),
		);

		$body_data = json_encode( $data );
		$curl = curl_init();

		curl_setopt_array(
			$curl,
			array(
				CURLOPT_URL => 'https://minfraud.maxmind.com/minfraud/v2.0/factors',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_USERAGENT => 'AnTiFrAuDOPMC',
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $body_data,
				CURLOPT_HTTPHEADER => array(
					'authorization:' . $authkey,
					'cache-control: no-cache',
					'content-type: application/json',
				),
			)
		);

		$response = curl_exec( $curl );
		curl_close( $curl );
		$score = json_decode( $response, true );  // echo 'Factors'; echo '<pre>'; print_r($score);
		$error = @$score['code'];
		if ( 'AUTHORIZATION_INVALID' === $error ) {
			Af_Logger::debug( 'minfraud factors score  ' . $error );
			return;

		} else {

			$minmraud_score = @$score['risk_score'];
			Af_Logger::debug( 'minfraud factors score  ' . $minmraud_score );
			return $minmraud_score;
		}
	}


	// Enable rule check
	public function is_enabled() {
		if ( 'yes' == $this->is_enabled ) {
			return true;
		}
		return false;
	}
}
