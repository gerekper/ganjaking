<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWGC_WPML' ) ) {

	/**
	 *
	 * @class   YWGC_WPML
	 *
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YWGC_WPML {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;


		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {

			/**
			 * Convert gift card amounts shown on product page according to current WPML currency
			 */
			add_filter( 'yith_ywgc_gift_card_amounts', array(
				$this,
				'get_wpml_multi_currency'
			), 10, 2 );

			add_filter( 'yith_ywgc_set_cart_item_price', array(
				$this,
				'cart_convert_to_user_currency'
			), 10, 2 );



			add_filter( 'yith_ywgc_submitting_manual_amount', array(
				$this,
				'convert_to_base_currency'
			) );

			/**
			 * Retrieve the array data key for the subtotal in the current currency
			 */
			add_filter( 'yith_ywgc_line_subtotal', array(
				$this,
				'line_subtotal'
			), 10, 2 );

			/**
			 * Retrieve the array data key for the subtotal tax in the  current currency
			 */
			add_filter( 'yith_ywgc_line_subtotal_tax', array(
				$this,
				'line_subtotal_tax'
			), 10, 2 );

			/**
			 * Set the amount from customer currency to base currency
			 */
			add_filter( 'yith_ywgc_gift_card_amount_before_deduct',
				array(
					$this,
					'convert_to_base_currency'
				) );

			/**
			 * Show the amount of the gift card using the user currency
			 */
			add_filter( 'yith_ywgc_gift_card_template_amount', array(
				$this,
				'get_amount_in_gift_card_currency'
			), 10, 3 );

			add_filter( 'yith_ywgc_gift_card_coupon_amount', array(
				$this,
				'convert_to_user_currency'
			), 10, 2 );

		}

		/**
		 * Convert gift card amounts shown on product page according to current WPML currency
		 *
		 * @param array                $amounts amounts to be shown
		 * @param WC_Product_Gift_Card $product the gift card product
		 *
		 * @return array
		 */
		public function get_wpml_multi_currency( $amounts, $product ) {
			if ( $amounts ) {
				$multi_currency_amounts = array();
				foreach ( $amounts as $amount ) {
					$multi_currency_amounts[] = apply_filters( 'wcml_raw_price_amount', $amount );
				}

				return $multi_currency_amounts;
			}

			return $amounts;
		}

		/**
		 * @param YWGC_Gift_Card_Premium $gift_card
		 *
		 * @return float
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function get_amount_in_gift_card_currency( $price, $gift_card, $amount ) {

			if ( $gift_card->currency ) {

				$price = wc_price( apply_filters( 'wcml_raw_price_amount', $amount, $gift_card->currency ),
					array( 'currency' => $gift_card->currency ) );
			}

			return $price;
		}

		public function convert_to_user_currency( $price, $values ) {
			/** @var woocommerce_wpml $woocommerce_wpml */
			global $woocommerce_wpml;
			if ( $woocommerce_wpml->multi_currency ) {

				$currency = $woocommerce_wpml->multi_currency->get_client_currency();
				if ( $currency != get_option( 'woocommerce_currency' ) ) {
					$price = apply_filters( 'wcml_raw_price_amount', $price );
				}
			}

			return $price;

		}

		public function cart_convert_to_user_currency( $price, $values ) {
			/** @var woocommerce_wpml $woocommerce_wpml */
			global $woocommerce_wpml;
			if ( $woocommerce_wpml->multi_currency ) {
				$price = apply_filters( 'wcml_raw_price_amount', $values['ywgc_default_currency_amount'] );
			}

			return $price;

		}

		public function convert_to_base_currency( $price ) {
			/** @var woocommerce_wpml $woocommerce_wpml */
			global $woocommerce_wpml;

			if (  $woocommerce_wpml->multi_currency ) {
				$price = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $price );
			}

			return $price;
		}

		/**
		 * Retrieve the array data key for the subtotal in the current currency
		 */
		public function line_subtotal( $amount, $order_item_data ) {
			return $this->convert_to_base_currency( $amount );
		}

		/**
		 * Retrieve the array data key for the subtotal in the current currency
		 */
		public function line_subtotal_tax( $amount, $order_item_data ) {
			return $this->convert_to_base_currency( $amount );
		}
	}
}

YWGC_WPML::get_instance();
