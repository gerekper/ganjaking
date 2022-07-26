<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_AF_Rule_High_Value extends WC_AF_Rule {
	private $is_enabled  = false;
	private $rule_weight = 0;
	/**
	 * The constructor
	 */
	public function __construct() {
		$this->is_enabled  =  get_option('wc_af_order_avg_amount_check');
		$this->rule_weight = get_option('wc_settings_anti_fraud_order_avg_amount_weight');
		
		parent::__construct( 
			'high_value', 
			sprintf( 
				'Order has a total higher than %s times the average order.', 
				/**
				 * Get value multiplier risk in shop
				 *
				 * @since  1.0.0
				 */
				apply_filters( 'wc_af_high_value_multiplier', $this->rule_weight ) 
			), 15 );
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
		
		Af_Logger::debug('Checking high value rule');
		global $wpdb;

		// Default risk is false
		$risk = false;

		/**
		 * Status values for query
		 *
		 * @since  1.0.0
		 */
		$statuses = "'wc-" . implode("','wc-", apply_filters( 'wc_af_high_value_value_order_statuses', array('completed', 'processing', 'on-hold') )) . "'";

		Af_Logger::debug('high value status ' . print_r($statuses, true));

		// Get the average order total
		$avg_order_total = round( $wpdb->get_var( "SELECT AVG(PM.`meta_value`)
 			FROM $wpdb->postmeta PM
 			INNER JOIN $wpdb->posts P ON P.`ID` = PM.`post_id`
 			WHERE PM.`meta_key` = '_order_total' AND PM.`meta_value` > 0 AND P.`post_type` = 'shop_order'
		AND P.`post_status` IN  ( '%s' ) ", $statuses ) );

		Af_Logger::debug('Average order total : ' . print_r($avg_order_total, true));

		/** 
		 * Check if the order total is higher than 2 times the average order total
		 *
		 * @since  1.0.0
		 * 
		 */
		if ( ( $avg_order_total > 0 ) && $order->get_total() > ( $avg_order_total * apply_filters( 'wc_af_high_value_multiplier', get_option('wc_settings_anti_fraud_avg_amount_multiplier') ) ) ) {
			$risk = true;
		}

		Af_Logger::debug('high value rule risk : ' . ( true === $risk ? 'true' : 'false' ));
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
