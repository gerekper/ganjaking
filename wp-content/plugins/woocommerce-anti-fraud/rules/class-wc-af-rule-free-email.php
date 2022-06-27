<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_AF_Rule_Free_Email extends WC_AF_Rule {
	private $is_enabled  = false;
	private $rule_weight = 0;
	private $free_email;
	/**
	 * The constructor
	 */
	public function __construct() {
		$this->free_email  = get_option('wc_settings_anti_fraud_suspecious_email_domains'); 

		$this->is_enabled  = get_option('wc_af_suspecius_email');
		$this->rule_weight = get_option('wc_settings_anti_fraud_suspecious_email_weight');
		
		parent::__construct( 'free_email', 'Email is a known free email address.', $this->rule_weight );
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
		
		Af_Logger::debug('Checking free email rule');
		$domains = explode( ',', $this->free_email );
		$free_email_domains = apply_filters( 'wc_af_temporary_email_domains', $domains );
		
		// Default risk is false
		$risk = false;

		// Do the regex
		$regex_result = preg_match( '`@([a-zA-z0-9\-\_]+)(?:\.[a-zA-Z]{0,5}){0,2}$`', ( version_compare( WC_VERSION, '3.0', '<' ) ? $order->billing_email : $order->get_billing_email() ), $email_domain );

		// Check if we've got a result
		if ( 1 === $regex_result ) {

			// Check if domain is in free domain array
			if ( in_array( $email_domain[1], $free_email_domains ) ) {
				$risk = true;
			}

		}
		Af_Logger::debug('free email rule risk : ' . ( true === $risk ? 'true' : 'false' ));
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
