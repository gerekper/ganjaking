<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_AF_Rule_High_Amount extends WC_AF_Rule {
	private $is_enabled  = false;
    private $rule_weight = 0;
	/**
	 * The constructor
	 */
	public function __construct() {
		$this->is_enabled  =  get_option('wc_af_order_amount_check');
		$this->rule_weight = get_option('wc_settings_anti_fraud_order_amount_weight');
		$this->amount      = get_option('wc_settings_anti_fraud_amount_limit'); 
		parent::__construct( 'high_amount', sprintf( 'Order has more amount as specified.' ),$this->rule_weight );
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

		// Default risk is false
		$risk = false;

		// Get the COUNT of total products
		$order_amount = $order->get_total();
	
		// Check if the order total is higher than 2 times the average order total
		if ( $order_amount > $this->amount ) {
			$risk = true;
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