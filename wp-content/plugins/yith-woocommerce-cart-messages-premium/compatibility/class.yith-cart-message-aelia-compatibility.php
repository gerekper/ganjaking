<?php
if(!defined('ABSPATH')){
	exit;
}

if( !class_exists( 'YWCM_Aelia_Integration')){

	class  YWCM_Aelia_Integration{

		public function __construct() {

			add_filter( 'ywcm_message_minimum_amount', array( $this, 'get_amount_in_currency' ), 10 ,1 );
			add_filter( 'ywcm_minimum_amount_threshold_amount', array( $this, 'get_amount_in_currency' ), 10 ,1 );
		}


		/**
		 * @param $amount
		 * @param null $to_currency
		 * @param null $from_currency
		 * @return string|float
		 */
		public function get_amount_in_currency( $amount, $to_currency = null, $from_currency = null ){

			if( '' !== $amount ){

				if( empty( $from_currency ) ){
					$from_currency = get_option( 'woocommerce_currency' );
				}

				if(empty($to_currency)) {
					$to_currency = get_woocommerce_currency();
				}

				return   apply_filters('wc_aelia_cs_convert', $amount, $from_currency, $to_currency);
			}

			return $amount;
		}
	}
}

if( !function_exists('YWCM_Aelia_Integration')){
	function YWCM_Aelia_Integration(){
		return new YWCM_Aelia_Integration();
	}
}