<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_AF_Rule_Detect_Proxy extends WC_AF_Rule {
	private $is_enabled  = false;
    private $rule_weight = 0;
	/**
	 * The constructor
	 */
	public function __construct() {
		$this->is_enabled  = get_option('wc_af_proxy_order');
		$this->rule_weight = get_option('wc_settings_anti_fraud_proxy_order_weight');
		parent::__construct( 'detect_proxy', 'Customer ordered from behind a proxy.', $this->rule_weight );
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
		global $wpdb;
		//$ip = WC_AF_Score_Helper::get_ip_address();
		$data = $order->get_id();
		$ip = get_post_meta( $data, '_customer_ip_address', true );
		/*if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
			$ip = $_SERVER['REMOTE_ADDR'];
			}*/
		//$ip = '195.181.161.229';

		// Default risk is false
		$risk = false;
		$contents = @file_get_contents("https://ip.teoh.io/api/vpn/$ip");
				 
		if ( $contents !== false ) {

		    $res = @json_decode($contents);
			
		    if(json_last_error() === JSON_ERROR_NONE) {
				
				$array_data = (array)$res;

				if(array_key_exists('vpn_or_proxy', $array_data)) {

					if($res->vpn_or_proxy == 'yes'){

						$risk = true;
					}
				}
				
				// Here we can create a log entry in future, whenever required. We can write the complete $res object in that log.	
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

