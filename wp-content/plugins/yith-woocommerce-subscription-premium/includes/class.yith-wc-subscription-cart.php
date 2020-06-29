<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements YWSBS_Subscription Cart Class
 *
 * @class   YWSBS_Subscription_Cart
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YWSBS_Subscription_Cart' ) ) {

	class YWSBS_Subscription_Cart {

		/**
		 * Single instance of the class
		 *
		 * @var \YWSBS_Subscription_Cart
		 */
		protected static $instance;

		/**
		 * @var bool
		 */
		protected $prices_updates = false;


		/**
		 * Returns single instance of the class
		 *
		 * @return \YWSBS_Subscription_Cart
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			$this->prices_updates = false;

			// change prices in calculation totals to add the fee amount
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'add_change_prices_filter' ), 10 );
			add_action( 'woocommerce_calculate_totals', array( $this, 'remove_change_prices_filter' ), 10 );
			add_action( 'woocommerce_after_calculate_totals', array( $this, 'remove_change_prices_filter' ), 10 );

			// Change prices and totals in cart
			// add_action( 'woocommerce_add_to_cart', array( $this, 'change_prices_to_single_item' ), 99 );
			add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'change_subtotal_price_in_cart_html' ), 99, 3 );
			add_filter( 'woocommerce_cart_item_price', array( $this, 'change_price_in_cart_html' ), 99, 3 );
			add_filter( 'woocommerce_cart_needs_payment', array( $this, 'cart_needs_payment' ), 10, 2 );
			add_filter( 'ywsbs_signup_fee_in_cart', array( $this, 'change_signup_fee_in_cart' ), 10, 2 );
			add_filter( 'ywsbs_trial_in_cart', array( $this, 'change_trial_in_cart' ), 10, 2 );

		}

		/**
		 * Add change prices filter.
		 *
		 * @since 1.4.6
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_change_prices_filter() {
			add_filter( 'woocommerce_product_get_price', array( $this, 'change_prices_for_calculation' ), 100, 2 );
			add_filter( 'woocommerce_product_variation_get_price', array( $this, 'change_prices_for_calculation' ), 100, 2 );
		}

		/**
		 * Remove the change price filter.
		 *
		 * @since 1.4.6
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function remove_change_prices_filter() {
			remove_filter( 'woocommerce_product_get_price', array( $this, 'change_prices_for_calculation' ), 100 );
			remove_filter( 'woocommerce_product_variation_get_price', array( $this, 'change_prices_for_calculation' ), 100 );
		}


		/**
		 * Change price
		 *
		 * @param $price
		 * @param $product WC_Product
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @return mixed
		 */
		public function change_prices_for_calculation( $price, $product ) {

			$is_raq = $product->get_meta( 'ywraq_product' );

			if ( ! YITH_WC_Subscription()->is_subscription( $product ) || $is_raq ) {

				return $price;
			}

			$signup_fee   = yit_get_prop( $product, '_ywsbs_fee', true );
			$trial_period = yit_get_prop( $product, '_ywsbs_trial_per', true );

			if ( $trial_period != '' && $trial_period != 0 ) {
				$price = 0;
			}

			if ( $signup_fee != '' ) {
				$price = floatval( $signup_fee ) + $price;
			}

			return $price;

		}

		/**
		 * @param $cart_item_key
		 *
		 * @internal param $product_id
		 * @internal param $quantity
		 * @internal param $variation_id
		 * @internal param $variation
		 * @internal param $cart_item_data
		 *
		 * @deprecated
		 */
		public function change_prices_to_single_item( $cart_item_key ) {
			$this->change_prices( WC()->cart );
			WC()->cart->calculate_totals();
		}


		/**
		 * @deprecated
		 * @param $cart WC_Cart
		 */
		public function change_prices( $cart ) {

			$cart_t = $cart->get_cart();
			if ( empty( $cart_t ) ) {
				return;
			}

			foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
				/** @var WC_Product $product */
				$product = $cart_item['data'];
				$id      = $product->get_id();

				if ( YITH_WC_Subscription()->is_subscription( $id ) ) {

					$price = apply_filters( 'ywsbs_change_prices', $product->get_price(), $product );

					$signup_fee   = apply_filters( 'ywsbs_signup_fee_in_cart', yit_get_prop( $product, '_ywsbs_fee', true ), $cart_item );
					$trial_period = apply_filters( 'ywsbs_trial_in_cart', yit_get_prop( $product, '_ywsbs_trial_per', true ), $cart_item );

					if ( $trial_period != '' && $trial_period != 0 ) {
						$price = 0;
					}

					if ( $signup_fee != '' ) {
						$price = floatval( $signup_fee ) + $price;
					}

					if ( apply_filters( 'ywsbs_price_check', true, $product, $price ) ) {

						if ( isset( WC()->cart->cart_contents[ $cart_item_key ] ) ) {
							if ( version_compare( WC()->version, '2.7.0', '>=' ) ) {

								yit_set_prop( WC()->cart->cart_contents[ $cart_item_key ]['data'], 'recurring_price', $product->get_price() );
								WC()->cart->cart_contents[ $cart_item_key ]['data']->set_price( $price );
								WC()->cart->cart_contents[ $cart_item_key ]['data']->recurring_price = $product->get_price();
							} else {
								WC()->cart->cart_contents[ $cart_item_key ]['data']->price           = $price;
								WC()->cart->cart_contents[ $cart_item_key ]['data']->recurring_price = $product->get_price();
							}
						}
					}
				}

				$this->prices_updates = true;
			}
		}

		/**
		 * @param $price_html
		 * @param $cart_item
		 * @param $cart_item_key
		 *
		 * @return mixed|void
		 */
		public function change_price_in_cart_html( $price_html, $cart_item, $cart_item_key ) {

			$product_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];

			if ( YITH_WC_Subscription()->is_subscription( $product_id ) && isset( $cart_item['data'] ) ) {
				$product       = $cart_item['data'];
				$price         = apply_filters( 'ywsbs_change_price_in_cart_html', $cart_item['data']->get_price(), $cart_item['data'] );
				$price_current = apply_filters( 'ywsbs_change_price_current_in_cart_html', $product->get_price(), $product );
				$product->set_price( $price );
				$price_html = apply_filters( 'ywsbs_get_price_html', $product->get_price_html(), $cart_item, $product_id );
				$product->set_price( $price_current );
			}

			return $price_html;

		}

		/**
		 * @param $price_html
		 * @param $cart_item
		 * @param $cart_item_key
		 *
		 * @return string
		 */
		public function change_subtotal_price_in_cart_html( $price_html, $cart_item, $cart_item_key ) {

			$product_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];
			if ( YITH_WC_Subscription()->is_subscription( $product_id ) && isset( $cart_item['data'] ) ) {
				$product       = $cart_item['data'];
				$price         = apply_filters( 'ywsbs_change_subtotal_price_in_cart_html', $cart_item['data']->get_price(), $cart_item['data'] );
				$price_current = apply_filters( 'ywsbs_change_subtotal_price_current_in_cart_html', $product->get_price(), $product );

				$product->set_price( $price );
				$price_html = YITH_WC_Subscription()->change_general_price_html( $product, $cart_item['quantity'] );
				$product->set_price( $price_current );

				if ( is_checkout() && 'yes' == get_option( 'ywsbs_subscription_total_amount', 'no' ) ) {
					$max_lenght = $product->get_meta( '_ywsbs_max_length' );
					$price_time = $product->get_meta( '_ywsbs_price_time_option' );
					$price_per  = $product->get_meta( '_ywsbs_price_is_per' );
					$fee        = $product->get_meta( '_ywsbs_fee' );
					$fee        = ! empty( $fee ) ? $fee : 0;
					$max_lenght = ! empty( $max_lenght ) ? $max_lenght : 1;
					$duration   = substr( ywsbs_get_price_per_string( $max_lenght, $price_time ), 2 );

					$price = ! empty( $price_per ) && $price_per > 0 ? ( ( $price / $price_per ) * $max_lenght + $fee ) : ( $price + $fee );
					$price = $price * $cart_item['quantity'];

					if ( $max_lenght && $max_lenght > 1 ) {

						$price_html .= apply_filters( 'ywsbs_checkout_subscription_total_amount', '<br><small>' . sprintf( __( '(Subscription total for %1$s %2$s: %3$s)', 'yith-woocommerce-subscription' ), $max_lenght, $duration, wc_price( $price ), $price_time ) . '</small>' );
					}
				}
				return apply_filters( 'ywsbs_subscription_subtotal_html', $price_html, $cart_item['data'], $cart_item );
			}

			return $price_html;
		}

		/**
		 * @param     $product_id
		 * @param     $price
		 * @param int        $quantity
		 *
		 * @return float
		 */
		public function get_price( $product_id, $price, $quantity = 1 ) {
			// Load product object
			$product = wc_get_product( $product_id );

			$price = $product->get_regular_price();

			// Get correct price
			if ( get_option( 'woocommerce_tax_display_cart' ) ) {
				$price = yit_get_price_including_tax( $product, $quantity, $price );
			} else {
				$price = yit_get_price_excluding_tax( $product, $quantity, $price );
			}

			return (float) $price;
		}


		/**
		 * @return bool
		 */
		public function cart_has_subscription_with_signup() {
			foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
				/** @var WC_Product $product */
				$product = $cart_item['data'];
				$id      = $product->get_id();

				if ( YITH_WC_Subscription()->is_subscription( $id ) ) {
					$fee = yit_get_prop( $product, '_ywsbs_fee', true );
					if ( ! empty( $fee ) && $fee > 0 ) {
						return true;
					}
				}
			}

			return false;
		}

		/**
		 * Check whether the cart needs payment even if the order total is $0
		 *
		 * @param bool    $needs_payment
		 * @param      WC_Cart
		 *
		 * @return bool
		 */
		public static function cart_needs_payment( $needs_payment, $cart ) {

			if ( false === $needs_payment && YITH_WC_Subscription()->cart_has_subscriptions() && $cart->total == 0 ) {
				return true;
			}

			return $needs_payment;
		}

		/**
		 * Check if there are subscription upgrade in progress and change the fee
		 *
		 * @param $fee       float
		 * @param $cart_item array
		 *
		 * @param WC_Cart
		 *
		 * @return bool
		 */
		public function change_signup_fee_in_cart( $fee, $cart_item ) {

			$signup_fee = $fee;

			/*
			 UPGRADE PROCESS */
			// add fee is gap payment is available and choosed b user
			$product = $cart_item['data'];
			$id      = $product->get_id();

			$subscription_info = get_user_meta( get_current_user_id(), 'ywsbs_upgrade_' . $id, true );
			$gap_payment       = yit_get_prop( $product, '_ywsbs_gap_payment' );
			$pay_gap           = 0;

			if ( ! empty( $subscription_info ) && isset( $subscription_info['pay_gap'] ) ) {
				$pay_gap = $subscription_info['pay_gap'];
			}

			if ( $gap_payment == 'yes' && $pay_gap > 0 ) {
				// change the fee of the subscription adding the total amount of the previous rates
				$signup_fee += $pay_gap;
			}

			return $signup_fee;

		}

		/**
		 * Check if there are subscription upgrade in progress and change the trial options
		 * During the upgrade or downgrade the trial period will be nulled
		 *
		 * @param int   $trial
		 * @param array $cart_item
		 *
		 * @return int | string
		 */
		public function change_trial_in_cart( $trial, $cart_item ) {

			$new_trial = $trial;

			$product = $cart_item['data'];
			$id      = $product->get_id();

			/* UPGRADE PROCESS */
			$subscription_upgrade_info = get_user_meta( get_current_user_id(), 'ywsbs_upgrade_' . $id, true );
			if ( ! empty( $subscription_upgrade_info ) ) {
				return '';
			}

			/* DOWNGRADE PROCESS */
			$subscription_downgrade_info = get_user_meta( get_current_user_id(), 'ywsbs_trial_' . $id, true );
			if ( ! empty( $subscription_downgrade_info ) ) {
				$new_trial = $subscription_downgrade_info['trial_days'];
			}

			return $new_trial;

		}
	}


}

/**
 * Unique access to instance of YWSBS_Subscription_Cart class
 *
 * @return \YWSBS_Subscription_Cart
 */
function YWSBS_Subscription_Cart() {
	return YWSBS_Subscription_Cart::get_instance();
}
