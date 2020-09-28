<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_AF_Rule_First_Order_Processing extends WC_AF_Rule {
	
	private $is_enabled  = false;
    private $rule_weight = 0;
	/**
	 * The constructor
	 */
	public function __construct() {
		$this->is_enabled  = get_option('wc_af_first_order_custom');
		$this->rule_weight = get_option('wc_settings_anti_fraud_first_order_custom_weight');
		parent::__construct( 'first_order', "This is the customer&apos;s first order.", $this->rule_weight );
	}

	/**
	 * Do the required check in this method. The method must return a boolean.
	 * Check if this is user's first order.
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
		$risk = false;
        $order_amount =  $wpdb->get_var($wpdb->prepare( "SELECT COUNT(P.ID)
 			FROM $wpdb->postmeta PM
 			INNER JOIN $wpdb->posts P ON P.ID = PM.post_id
 			WHERE PM.meta_key = '_billing_email' AND PM.meta_value = %s AND P.post_type = 'shop_order'
			AND P.post_status IN ( 'wc-" . implode( "','wc-", apply_filters( 'wc_af_high_value_value_order_statuses', array( 'completed','processing' ) ) ) . "' ) ;",  $order->get_billing_email() )); 
			
		$order_count =  $wpdb->get_var($wpdb->prepare( "SELECT COUNT(P.ID)
 			FROM $wpdb->postmeta PM
 			INNER JOIN $wpdb->posts P ON P.ID = PM.post_id
 			WHERE PM.meta_key = '_billing_email' AND PM.meta_value = %s AND P.post_type = 'shop_order'
			AND P.post_status IN ( 'wc-" . implode( "','wc-", apply_filters( 'wc_af_high_value_value_order_statuses', array( 'completed','processing','pending','on-hold' ) ) ) . "' ) ;",  $order->get_billing_email() )); 
			
			
		// Risk is true if order amount is smaller than 2
		if ( ($order_amount < 1) && ($order_count == 1)) {

			$risk = true;

		} elseif(($order_amount < 1) && ($order_count > 1)) {

			parent::__construct( 'first_order', "Customer has ordered before, but has never completed their order", $this->rule_weight );
			$risk = true;
			
		}

		return $risk;
	
	}

	public function is_enabled(){
		if('yes' == $this->is_enabled){
			return true;
		}
		return false;
	}

}
