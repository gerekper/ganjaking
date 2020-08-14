<?php
/**
 * YWCM_Aelia_Integration Class
 *
 * @class   YWCM_Cart_Message
 * @package YITH
 * @since   1.0.0
 * @author  Your Inspiration Themes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YWCM_Aelia_Integration' ) ) {
	/**
	 * Class YWCM_Aelia_Integration
	 */
	class  YWCM_Aelia_Integration {

		/**
		 * YWCM_Aelia_Integration constructor.
		 */
		public function __construct() {

			add_filter( 'ywcm_message_minimum_amount', array( $this, 'get_amount_in_currency' ), 10, 1 );
			add_filter( 'ywcm_minimum_amount_threshold_amount', array( $this, 'get_amount_in_currency' ), 10, 1 );
		}


		/**
		 * Return the amount converted.
		 *
		 * @param float $amount Amount.
		 * @param mixed $to_currency To currency.
		 * @param mixed $from_currency From currency.
		 * @return string|float
		 */
		public function get_amount_in_currency( $amount, $to_currency = null, $from_currency = null ) {

			if ( '' !== $amount ) {

				if ( empty( $from_currency ) ) {
					$from_currency = get_option( 'woocommerce_currency' );
				}

				if ( empty( $to_currency ) ) {
					$to_currency = get_woocommerce_currency();
				}

				return apply_filters( 'wc_aelia_cs_convert', $amount, $from_currency, $to_currency );
			}

			return $amount;
		}
	}
}

if ( ! function_exists( 'YWCM_Aelia_Integration' ) ) {
	/**
	 * Return YWCM_Aelia_Integration instance.
	 *
	 * @return YWCM_Aelia_Integration
	 */
	function YWCM_Aelia_Integration() {  // phpcs:ignored WordPress.NamingConventions
		return new YWCM_Aelia_Integration();
	}
}
