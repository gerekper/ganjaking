<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements YWSBS_Subscription_Coupons Class
 *
 * @class   YWSBS_Subscription_Coupons
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YWSBS_Subscription_Coupons' ) ) {

	class YWSBS_Subscription_Coupons {

		/**
		 * Single instance of the class
		 *
		 * @var \YWSBS_Subscription_Coupons
		 */
		protected static $instance;

		protected $coupon_types = array();
		protected $coupon_error = '';


		/**
		 * Returns single instance of the class
		 *
		 * @return \YWSBS_Subscription_Coupons
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

			$this->coupon_types = array( 'signup_percent', 'signup_fixed', 'recurring_percent', 'recurring_fixed' );
			// Add new coupons type to administrator
			add_filter( 'woocommerce_coupon_discount_types', array( $this, 'add_coupon_discount_types' ) );
			add_filter( 'woocommerce_product_coupon_types', array( $this, 'add_coupon_discount_types_list' ) );

			// Apply discounts to a product and get the discounted price (before tax is applied).
			add_filter( 'woocommerce_get_discounted_price', array( $this, 'get_discounted_price' ), 10, 3 );
			add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'coupon_get_discount_amount' ), 10, 5 );

			// Validate coupons
			add_filter( 'woocommerce_coupon_is_valid', array( $this, 'validate_coupon' ), 10, 2 );

		}

		/**
		 * Add discount types on coupon system
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 *
		 * @param $coupons_type
		 *
		 * @return mixed
		 */
		public function add_coupon_discount_types( $coupons_type ) {

			$coupons_type['signup_percent']    = __( 'Subscription Signup % Discount', 'yith-woocommerce-subscription' );
			$coupons_type['signup_fixed']      = __( 'Subscription Signup Discount', 'yith-woocommerce-subscription' );
			$coupons_type['recurring_percent'] = __( 'Subscription Recurring % Discount', 'yith-woocommerce-subscription' );
			$coupons_type['recurring_fixed']   = __( 'Subscription Recurring Discount', 'yith-woocommerce-subscription' );

			return $coupons_type;
		}


		/**
		 * @param $coupons_type
		 *
		 * @return array
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_coupon_discount_types_list( $coupons_type ) {
			return array_merge(
				$coupons_type,
				array(
					'signup_percent',
					'signup_fixed',
					'recurring_percent',
					'recurring_fixed',
				)
			);
		}


		/**
		 * @param $discount
		 * @param $discounting_amount
		 * @param $cart_item
		 * @param $single
		 * @param $coupon
		 *
		 * @return float|int|mixed
		 * @throws Exception
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function coupon_get_discount_amount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {

			$product = $cart_item['data'];
			$id      = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];
			if ( ! YITH_WC_Subscription()->is_subscription( $id ) ) {
				return $discount;
			}
			$fee             = yit_get_prop( $product, '_ywsbs_fee' );
			$trial_per       = yit_get_prop( $product, '_ywsbs_trial_per' );
			$recurring_price = $product->get_price();

			$valid = ywsbs_coupon_is_valid( $coupon, WC()->cart );

			if ( ! empty( $coupon ) && $valid ) {

				$coupon_type   = method_exists( $coupon, 'get_discount_type' ) ? $coupon->get_discount_type() : $coupon->type;
				$coupon_amount = method_exists( $coupon, 'get_amount' ) ? $coupon->get_amount() : $coupon->amount;

				switch ( $coupon_type ) {
					case 'signup_percent':
						if ( ! empty( $fee ) && $fee != 0 ) {
							$discount = round( ( $fee / 100 ) * $coupon_amount, WC()->cart->dp );
						}
						break;
					case 'recurring_percent':
						if ( empty( $trial_per ) || isset( WC()->cart->subscription_coupon ) ) {
							$discount = round( ( $recurring_price / 100 ) * $coupon_amount, WC()->cart->dp );
						}
						break;
					case 'signup_fixed':
						if ( ! empty( $fee ) && $fee != 0 ) {
							$discount = ( $fee < $coupon_amount ) ? $fee : $coupon_amount;
						}
						break;
					case 'recurring_fixed':
						if ( empty( $trial_per ) || isset( WC()->cart->subscription_coupon ) ) {
							$discount = ( $recurring_price < $coupon_amount ) ? $recurring_price : $coupon_amount;
						}
						break;
					default:
				}
			}

			return $discount;

		}

		/**
		 * @param $price
		 * @param $cart_item
		 * @param $cart
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function get_discounted_price( $price, $cart_item, $cart ) {

			$id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];
			if ( ! $price || ! YITH_WC_Subscription()->is_subscription( $id ) ) {
				return $price;
			}

			$undiscounted_price = $price;
			$applied_coupons    = ywsbs_get_applied_coupons( $cart );

			if ( ! empty( $applied_coupons ) ) {

				$product = $cart_item['data'];
				foreach ( $applied_coupons as $code => $coupon ) {
					$valid = ywsbs_coupon_is_valid( $coupon, WC()->cart );

					if ( $valid ) {
						$discount_amount = (float) $coupon->get_discount_amount( 'yes' === get_option( 'woocommerce_calc_discounts_sequentially', 'no' ) ? $price : $undiscounted_price, $cart_item, true );

						// $discount_amount = min( $price, $discount_amount );
						// $price           = max( $price - $discount_amount, 0 );

						// Store the totals for DISPLAY in the cart.

						$total_discount     = $discount_amount * $cart_item['quantity'];
						$total_discount_tax = 0;

						if ( wc_tax_enabled() && $product->is_taxable() ) {
							$tax_rates                = WC_Tax::get_rates( $product->get_tax_class() );
							$taxes                    = WC_Tax::calc_tax( $discount_amount, $tax_rates, $cart->prices_include_tax );
							$total_discount_tax       = WC_Tax::get_tax_total( $taxes ) * $cart_item['quantity'];
							$total_discount           = $cart->prices_include_tax ? $total_discount - $total_discount_tax : $total_discount;
							$cart->discount_cart_tax += $total_discount_tax;
						}

						$cart->discount_cart += $total_discount;
						$this->increase_coupon_discount_amount( $code, $total_discount, $total_discount_tax, $cart );
						$this->increase_coupon_applied_count( $code, $cart_item['quantity'], $cart );

					}

					// If the price is 0, we can stop going through coupons because there is nothing more to discount for this product.
					if ( 0 >= $price ) {
						break;
					}
				}
			}

			return $price;
		}


		/**
		 * Check if coupon is valid
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 *
		 * @param $is_valid
		 * @param $coupon
		 *
		 * @return bool
		 */
		public function validate_coupon( $is_valid, $coupon ) {

			$this->coupon_error = '';
			$coupon_type        = method_exists( $coupon, 'get_discount_type' ) ? $coupon->get_discount_type() : $coupon->type;
			if ( ! in_array( $coupon_type, $this->coupon_types ) && ! YITH_WC_Subscription()->cart_has_subscriptions() ) {
				return $is_valid;
			}

			// ignore non-subscription coupons
			if ( ! YITH_WC_Subscription()->cart_has_subscriptions() ) {
				$this->coupon_error = __( 'Sorry, this coupon can be used only if there is a subscription in the cart', 'yith-woocommerce-subscription' );
			} else {

				if ( in_array(
					$coupon_type,
					array(
						'signup_percent',
						'signup_fixed',
					)
				) && ! YWSBS_Subscription_Cart()->cart_has_subscription_with_signup() ) {
					$this->coupon_error = __( 'Sorry, this coupon can be used only if there is a subscription with signup fees', 'yith-woocommerce-subscription' );
				}
			}

			if ( ! empty( $this->coupon_error ) ) {
				$is_valid = false;
				add_filter( 'woocommerce_coupon_error', array( $this, 'add_coupon_error' ), 10 );
			}

			return $is_valid;
		}


		/**
		 * Add coupon error if the coupon is not valid
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 *
		 * @param $error
		 *
		 * @return string
		 */
		public function add_coupon_error( $error ) {
			if ( ! empty( $this->coupon_error ) ) {
				$errors = $this->coupon_error;
			}

			return $errors;
		}


		/**
		 * Total of coupon discounts
		 *
		 * @param mixed              $coupon_code
		 * @param mixed              $amount
		 * @param $total_discount_tax
		 * @param $cart
		 *
		 * @return void
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function increase_coupon_discount_amount( $coupon_code, $amount, $total_discount_tax, $cart ) {
			$cart->coupon_discount_amounts[ $coupon_code ]     = isset( $cart->coupon_discount_amounts[ $coupon_code ] ) ? $cart->coupon_discount_amounts[ $coupon_code ] + $amount : $amount;
			$cart->coupon_discount_tax_amounts[ $coupon_code ] = isset( $cart->coupon_discount_tax_amounts[ $coupon_code ] ) ? $cart->coupon_discount_tax_amounts[ $coupon_code ] + $total_discount_tax : $total_discount_tax;
		}

		/**
		 * Increase coupon applied count
		 *
		 * @param $code
		 * @param int  $count
		 *
		 * @param $cart
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function increase_coupon_applied_count( $code, $count = 1, $cart ) {
			if ( empty( $cart->coupon_applied_count[ $code ] ) ) {
				$cart->coupon_applied_count[ $code ] = 0;
			}
			$cart->coupon_applied_count[ $code ] += $count;
		}
	}
}

/**
 * Unique access to instance of YWSBS_Subscription_Coupons class
 *
 * @return \YWSBS_Subscription_Coupons
 */
function YWSBS_Subscription_Coupons() {
	return YWSBS_Subscription_Coupons::get_instance();
}
