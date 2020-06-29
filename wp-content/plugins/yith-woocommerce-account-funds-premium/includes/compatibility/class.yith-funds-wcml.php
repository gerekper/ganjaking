<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_FUNDS_WCML' ) ) {

	class YITH_FUNDS_WCML {

		public function __construct() {

			add_filter( 'yith_show_available_funds', array(
				$this,
				'convert_base_currency_amount_to_user_currency'
			), 10 );
			add_filter( 'yith_min_deposit', array( $this, 'convert_base_currency_amount_to_user_currency' ), 10 );
			add_filter( 'yith_max_deposit', array( $this, 'convert_base_currency_amount_to_user_currency' ), 10 );

			add_filter( 'yith_show_used_funds', array( $this, 'convert_base_currency_amount_to_user_currency' ), 10 );
			add_filter( 'yith_discount_value', array( $this, 'convert_discount_value' ), 10, 2 );
			add_filter( 'yith_fund_deposit_amount_for_session', array( $this, 'convert_deposit_amount' ), 10, 2 );

			add_filter('yith_account_funds_deposit_item_data', array( $this, 'add_account_funds_item_data'), 10, 1 );

			add_filter( 'yith_admin_deposit_funds', array( $this, 'convert_amount_to_base_currency' ), 10, 2 );
			add_filter( 'yith_admin_order_total', array( $this, 'convert_amount_to_base_currency' ), 10, 2 );


			add_filter( 'yith_admin_order_totals_user_available', array(
				$this,
				'admin_order_totals_user_available'
			), 10, 2 );
			add_filter( 'yith_show_funds_used_into_order_currency', array(
				$this,
				'admin_order_totals_user_available'
			), 10, 2 );
			add_filter( 'yith_refund_amount_base_currency', array( $this, 'convert_amount_to_base_currency' ), 10, 2 );
			add_filter( 'yith_how_refund_base_currency', array( $this, 'convert_amount_to_base_currency' ), 10, 2 );

			add_filter( 'yith_fund_into_customer_email', array( $this, 'convert_price_amount' ), 10, 2 );
			add_filter( 'yith_funds_refund_total', array( $this, 'convert_price_amount' ), 10, 2 );
		}


		public function add_account_funds_item_data( $cart_item_data ){
			if ( ! empty( $cart_item_data['amount_deposit'] ) ) {
				// Keep track of the original amount entered by the customer, as well
				// as the original currency. These elements will make it possible to
				// convert the amount to any target currency
				$cart_item_data['original_amount_deposit'] = $cart_item_data['amount_deposit'];
				$cart_item_data['original_currency'] = get_woocommerce_currency();
			}

			return $cart_item_data;
		}

		/**
		 * @author Salvatore Strano
		 * @since 1.0.25
		 *
		 * @param float $funds
		 *
		 * @return float
		 */
		public function convert_base_currency_amount_to_user_currency( $funds ) {

			if( !empty( $funds ) ) {
				return apply_filters( 'wcml_raw_price_amount', $funds );
			}
			return $funds;
		}

		/**
		 * @param $amount
		 * @param array $cart_item_data
		 *
		 * @return float
		 */
		public function convert_deposit_amount( $amount, $cart_item_data ) {


			if( isset( $cart_item_data['original_amount_deposit'] ) && isset( $cart_item_data['original_currency'] ) ) {
				$current_currency = get_woocommerce_currency();
				$admin_currency = get_option( 'woocommerce_currency' );
				$customer_currency = $cart_item_data['original_currency'];
				$amount = $cart_item_data['original_amount_deposit'];


				if ( $current_currency !== $customer_currency ) {

					if( $admin_currency == $customer_currency ){
						$amount = $this->convert_base_currency_amount_to_user_currency( $amount);
					}else {
						$amount = $this->unconvert_price_amount( $amount, $customer_currency );
					}
				}
			}

			return $amount;
		}

		/**
		 * @param int|float $discount
		 * @param string $type
		 *
		 * @return float
		 */
		public function convert_discount_value( $discount, $type ) {

			if ( $type == 'fixed_cart' ) {

				$discount = $this->convert_base_currency_amount_to_user_currency( $discount );
			}

			return $discount;
		}


		/**
		 * @author Salvatore Strano
		 * @since 1.0.25
		 *
		 * @param float $value
		 * @param int $order_id
		 *
		 * @return  float
		 */
		public function convert_amount_to_base_currency( $value, $order_id ) {

			$order = wc_get_order( $order_id );

			$order_currency = $order->get_currency();

			$value = $this->unconvert_price_amount( $value, $order_currency );

			return $value;
		}

		/**
		 * @author Salvatore Stranp
		 * @since 1.0.25
		 *
		 * @param float $value
		 * @param int $order_id
		 *
		 * @return float
		 */
		public function admin_order_totals_user_available( $value, $order_id ) {

			$order = wc_get_order( $order_id );

			$order_currency = $order->get_currency();

			$value = $this->convert_price_amount( $value, $order_currency );

			return $value;
		}

		/**
		 * convert amount to base currency
		 * @author Salvatore Strano
		 *
		 * @param float|int $value
		 * @param bool|string $order_currency
		 *
		 * @return float|int
		 */
		public function unconvert_price_amount( $value, $order_currency = false ) {

			global $woocommerce_wpml;

			if ( !is_null( $woocommerce_wpml ) && !is_null( $woocommerce_wpml->multi_currency ) && ! is_null( $woocommerce_wpml->multi_currency->prices ) ) {
				$value = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $value, $order_currency );
			}

			return $value;
		}

		/**
		 * convert amount into customer currency
		 * @author Salvatore Strano
		 *
		 * @param float $value
		 * @param string $order_currency
		 *
		 * @return float|int
		 */
		public function convert_price_amount( $value, $order_currency ) {
			global $woocommerce_wpml;

			if ( !is_null( $woocommerce_wpml ) && !is_null( $woocommerce_wpml->multi_currency ) && ! is_null( $woocommerce_wpml->multi_currency->prices ) ) {
				$value = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $value, $order_currency );
			}

			return $value;

		}
	}

}

new YITH_FUNDS_WCML();