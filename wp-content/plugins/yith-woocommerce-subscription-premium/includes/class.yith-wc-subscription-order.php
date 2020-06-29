<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements YWSBS_Subscription_Order Class
 *
 * @class   YWSBS_Subscription_Order
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YWSBS_Subscription_Order' ) ) {

	/**
	 * Class YWSBS_Subscription_Order
	 */
	class YWSBS_Subscription_Order {

		/**
		 * Single instance of the class
		 *
		 * @var \YWSBS_Subscription_Order
		 */
		protected static $instance;

		/**
		 * Array with the new subscription details
		 *
		 * @var array
		 * @since 1.0.0
		 */
		private $subscriptions_info = array();
		/**
		 * @var array
		 */
		private $cart_item_order_item = array();

		/**
		 * @var WC_Cart
		 */
		private $actual_cart;
		/**
		 * @var int
		 */
		private $current_order_id;

		/**
		 * @var boolean
		 */
		private $payment_done = array();

		/**
		 * Returns single instance of the class
		 *
		 * @return \YWSBS_Subscription_Order
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
		 */
		public function __construct() {

			// Save details of subscription
			add_action( 'woocommerce_new_order_item', array( $this, 'add_subscription_order_item_meta' ), 20, 3 );
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'get_extra_subscription_meta' ), 10, 2 );

			// Add subscriptions from orders
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'check_order_for_subscription' ), 100, 2 );
			add_action( 'woocommerce_resume_order', array( $this, 'remove_subscription_from_order' ) );

			// Start subscription after payment received
			add_action( 'woocommerce_payment_complete', array( $this, 'payment_complete' ) );
			add_action( 'woocommerce_order_status_completed', array( $this, 'payment_complete' ) );
			add_action( 'woocommerce_order_status_processing', array( $this, 'payment_complete' ) );

			if ( get_option( 'ywsbs_delete_subscription_order_cancelled' ) == 'yes' ) {
				add_action( 'woocommerce_order_status_cancelled', array( $this, 'delete_subscriptions' ), 10 );
				add_action( 'before_delete_post', array( $this, 'delete_subscriptions' ), 10 );
			}

			//On refund of the main order cancel the subscription
			add_action( 'woocommerce_order_fully_refunded', array( $this, 'order_refunded' ), 10, 2 );
			add_action( 'woocommerce_order_status_refunded', array( $this, 'order_refunded' ), 10 );
			add_action( 'woocommerce_order_status_on-hold_to_completed', array( $this, 'reactive_subscription' ), 10 );
			add_action( 'woocommerce_order_status_on-hold_to_processing', array( $this, 'reactive_subscription' ), 10 );

			// If there's a subscription inside the order, even if the order total is $0, it still needs payment
			add_filter( 'woocommerce_order_needs_payment', array( $this, 'order_need_payment' ), 10, 3 );


			//@since 1.2.6
			add_filter( 'woocommerce_can_reduce_order_stock', array( $this, 'can_reduce_order_stock' ), 10, 2 );

			//renew_manually from my account > orders
			if ( 'yes' == get_option( 'ywsbs_renew_now_on_my_account', 'no' ) ) {
				add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, 'add_renew_subscription_manually' ), 10, 2 );
				add_action( 'wp', array( $this, 'pay_renew_order_now' ) );
			}

			// make renew order payable
			// @since 1.6.1
			add_filter( 'woocommerce_order_needs_payment', array( $this, 'renew_needs_payment' ), 10, 3 );


		}

		/**
		 * Add the action Renew now on order list
		 *
		 * @param $actions
		 * @param $order WC_Order
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @return mixed
		 */
		public function add_renew_subscription_manually( $actions, $order ) {
			if ( apply_filters( 'ywsbs_renew_now_order_action', ( 'yes' == $order->get_meta( 'is_a_renew' ) && ywsbs_check_renew_order_before_pay( $order ) && $order->get_meta( 'failed_attemps' ) > 0 ), $order ) ) {
				$actions['renew_now'] = array(
					'url'  => add_query_arg( array( 'renew_order' => $order->get_id() ), wc_get_page_permalink( 'myaccount' ) ),
					'name' => __( 'Renew Now', 'woocommerce' )
				);
			}

			return $actions;

		}

		/**
		 * Pay the renew order from my account page.
		 *
		 * @return bool
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function pay_renew_order_now() {

			$order_id = isset( $_GET['renew_order'] ) ? $_GET['renew_order'] : false;

			if ( $order_id ) {
				$is_manual_renew = true;
				$order           = wc_get_order( $order_id );
				$order = apply_filters( 'ywsbs_check_order_before_pay_renew_order', $order );
				if ( $order && get_current_user_id() == $order->get_customer_id() && ywsbs_check_renew_order_before_pay( $order ) ) {
					do_action( 'ywsbs_pay_renew_order_with_' . $order->get_payment_method(), $order, $is_manual_renew );
				}
			} else {
				return false;
			}

			wp_safe_redirect( wc_get_endpoint_url( 'orders' ) );
			die();
		}


		/**
		 * Save some info if a subscription is in the cart
		 *
		 * @access public
		 *
		 * @param  $order_id int
		 * @param  $posted   array
		 *
		 * @throws Exception
		 */
		public function get_extra_subscription_meta( $order_id, $posted ) {

			if ( ! YITH_WC_Subscription()->cart_has_subscriptions() || isset( $_REQUEST['cancel_order'] ) ) {
				return;
			}
			$this->actual_cart = WC()->session->get( 'cart' );

			add_filter( 'ywsbs_price_check', '__return_false' );
			remove_action( 'woocommerce_before_calculate_totals', array( YWSBS_Subscription_Cart(), 'add_change_prices_filter' ), 10 );

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

				$product            = $cart_item['data'];
				$id                 = $product->get_id();
				$main_product_id    = $product->get_parent_id() ? $product->get_parent_id() : $id;
				$main_product       = ( $main_product_id != $id ) ? wc_get_product( $main_product_id ) : $product;
				$one_time_shippable = $main_product->get_meta( '_ywsbs_one_time_shipping' );
				if ( $one_time_shippable == 'yes' ) {
					add_filter( 'woocommerce_cart_needs_shipping', '__return_false' );
				}

				if ( YITH_WC_Subscription()->is_subscription( $product ) ) {
					if ( YITH_WC_Subscription()->debug_active ) {
						YITH_WC_Subscription()->debug->add( 'ywsbs', 'find a subscription at create_order : ' . $order_id . ' product_id : ' . $id );
					}

					$new_cart = new WC_Cart();

					$subscription_info = array(
						'shipping' => array(),
						'taxes'    => array(),
					);

					if ( isset( $cart_item['variation'] ) ) {
						$subscription_info['variation'] = $cart_item['variation'];
					}
					do_action( 'ywsbs_before_add_to_cart_subscription', $cart_item );
					$new_cart_item_key = $new_cart->add_to_cart(
						$cart_item['product_id'],
						$cart_item['quantity'],
						( isset( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : '' ),
						( isset( $cart_item['variation'] ) ? $cart_item['variation'] : '' ),
						$cart_item
					);
					do_action( 'ywsbs_after_add_to_cart_subscription', $cart_item );

					$new_cart = apply_filters( 'ywsbs_add_cart_item_data', $new_cart, $new_cart_item_key, $cart_item );

					$new_cart_item_keys = array_keys( $new_cart->cart_contents );

					$applied_coupons = WC()->cart->get_applied_coupons();

					foreach ( $new_cart_item_keys as $new_cart_item_key ) {
						//shipping
						if ( $new_cart->needs_shipping() && $product->needs_shipping() ) {
							if ( method_exists( WC()->shipping, 'get_packages' ) ) {
								$packages = WC()->shipping->get_packages();

								foreach ( $packages as $key => $package ) {
									if ( isset( $package['rates'][ $posted['shipping_method'][ $key ] ] ) ) {
										if ( isset( $package['contents'][ $cart_item_key ] ) || isset( $package['contents'][ $new_cart_item_key ] ) ) {
											// This shipping method has the current subscription
											$shipping['method']      = $posted['shipping_method'][ $key ];
											$shipping['destination'] = $package['destination'];

											break;
										}
									}
								}


								if ( isset( $shipping ) ) {
									// Get packages based on renewal order details
									$new_packages = apply_filters( 'woocommerce_cart_shipping_packages', array(
										0 => array(
											'contents'        => $new_cart->get_cart(),
											'contents_cost'   => isset( $new_cart->cart_contents[ $new_cart_item_key ]['line_total'] ) ? $new_cart->cart_contents[ $new_cart_item_key ]['line_total'] : 0,
											'applied_coupons' => $new_cart->applied_coupons,
											'destination'     => $shipping['destination'],
										),
									) );

									//subscription_shipping_method_temp
									$save_temp_session_values = array(
										'shipping_method_counts'  => WC()->session->get( 'shipping_method_counts' ),
										'chosen_shipping_methods' => WC()->session->get( 'chosen_shipping_methods' ),
									);

									WC()->session->set( 'chosen_shipping_methods', array( $shipping['method'] ) );

									add_filter( 'woocommerce_shipping_chosen_method', array( $this, 'change_shipping_chosen_method_temp' ) );
									$this->subscription_shipping_method_temp = $shipping['method'];

									WC()->shipping->calculate_shipping( $new_packages );

									remove_filter( 'woocommerce_shipping_chosen_method', array( $this, 'change_shipping_chosen_method_temp' ) );

									unset( $this->subscription_shipping_method_temp );
								}
							}

						}

						foreach ( $applied_coupons as $coupon_code ) {
							$coupon        = new WC_Coupon( $coupon_code );
							$coupon_type   = $coupon->get_discount_type();
							$coupon_amount = $coupon->get_amount();
							$valid         = ywsbs_coupon_is_valid( $coupon, WC()->cart );
							if ( $valid && in_array( $coupon_type, array( 'recurring_percent', 'recurring_fixed' ) ) ) {

								$price     = $new_cart->cart_contents[ $new_cart_item_key ]['line_subtotal'];
								$price_tax = $new_cart->cart_contents[ $new_cart_item_key ]['line_subtotal_tax'];

								switch ( $coupon_type ) {
									case 'recurring_percent':
										$discount_amount     = round( ( $price / 100 ) * $coupon_amount, WC()->cart->dp );
										$discount_amount_tax = round( ( $price_tax / 100 ) * $coupon_amount, WC()->cart->dp );
										break;
									case 'recurring_fixed':
										$discount_amount     = ( $price < $coupon_amount ) ? $price : $coupon_type;
										$discount_amount_tax = 0;
										break;
								}

								$subscription_info['coupons'][] = array(
									'coupon_code'         => $coupon_code,
									'discount_amount'     => $discount_amount * $cart_item['quantity'],
									'discount_amount_tax' => $discount_amount_tax * $cart_item['quantity']
								);

								$new_cart->applied_coupons[]   = $coupon_code;
								$new_cart->coupon_subscription = true;

							}
						}

						if ( ! empty( $new_cart->applied_coupons ) ) {
							WC()->cart->discount_cart       = 0;
							WC()->cart->discount_cart_tax   = 0;
							WC()->cart->subscription_coupon = 1;
						}

						$new_cart->calculate_totals();

						// Recalculate totals
						//save some order settings
						$subscription_info['order_shipping']     = wc_format_decimal( $new_cart->shipping_total );
						$subscription_info['order_shipping_tax'] = wc_format_decimal( $new_cart->shipping_tax_total );
						$subscription_info['cart_discount']      = wc_format_decimal( $new_cart->get_cart_discount_total() );
						$subscription_info['cart_discount_tax']  = wc_format_decimal( $new_cart->get_cart_discount_tax_total() );
						$subscription_info['order_discount']     = $new_cart->get_total_discount();
						$subscription_info['order_tax']          = wc_format_decimal( $new_cart->tax_total );
						$subscription_info['order_subtotal']     = wc_format_decimal( $new_cart->subtotal, get_option( 'woocommerce_price_num_decimals' ) );
						$subscription_info['order_total']        = wc_format_decimal( $new_cart->total, get_option( 'woocommerce_price_num_decimals' ) );
						$subscription_info['line_subtotal']      = wc_format_decimal( $new_cart->cart_contents[ $new_cart_item_key ]['line_subtotal'] );
						$subscription_info['line_subtotal_tax']  = wc_format_decimal( $new_cart->cart_contents[ $new_cart_item_key ]['line_subtotal_tax'] );
						$subscription_info['line_total']         = wc_format_decimal( $new_cart->cart_contents[ $new_cart_item_key ]['line_total'] );
						$subscription_info['line_tax']           = wc_format_decimal( $new_cart->cart_contents[ $new_cart_item_key ]['line_tax'] );
						$subscription_info['line_tax_data']      = $new_cart->cart_contents[ $new_cart_item_key ]['line_tax_data'];
					}

					// Get shipping details
					if ( $product->needs_shipping() && $one_time_shippable != 'yes' ) {
						if ( isset( $shipping['method'] ) ) {
							$method = null;
							foreach ( WC()->shipping->packages as $i => $package ) {
								if ( isset( $package['rates'][ $shipping['method'] ] ) ) {
									$method = $package['rates'][ $shipping['method'] ];
									break;
								}
							}

							if ( ! is_null( $method ) ) {
								$subscription_info['shipping'] = array(
									'name'      => $method->label,
									'method_id' => $method->id,
									'cost'      => wc_format_decimal( $method->cost ),
									'taxes'     => $method->taxes,
								);

								// Set session variables to original values and recalculate shipping for original order which is being processed now
								WC()->session->set( 'shipping_method_counts', $save_temp_session_values['shipping_method_counts'] );
								WC()->session->set( 'chosen_shipping_methods', $save_temp_session_values['chosen_shipping_methods'] );
								WC()->shipping->calculate_shipping( WC()->shipping->packages );
							}
						}
					}

					//CALCULATE TAXES
					$taxes          = version_compare( WC()->version, '3.2.0', '>=' ) ? $new_cart->get_cart_contents_taxes() : array();
					$shipping_taxes = version_compare( WC()->version, '3.2.0', '>=' ) ? $new_cart->get_shipping_taxes() : array();

					foreach ( $new_cart->get_tax_totals() as $rate_key => $rate ) {

						$rate_args = array(
							'name'     => $rate_key,
							'rate_id'  => $rate->tax_rate_id,
							'label'    => $rate->label,
							'compound' => absint( $rate->is_compound ? 1 : 0 ),

						);

						if ( version_compare( WC()->version, '3.2.0', '>=' ) ) {
							$rate_args['tax_amount']          = wc_format_decimal( isset( $taxes[ $rate->tax_rate_id ] ) ? $taxes[ $rate->tax_rate_id ] : 0 );
							$rate_args['shipping_tax_amount'] = wc_format_decimal( isset( $shipping_taxes[ $rate->tax_rate_id ] ) ? $shipping_taxes[ $rate->tax_rate_id ] : 0 );
						} else {
							$rate_args['tax_amount']          = wc_format_decimal( isset( $new_cart->taxes[ $rate->tax_rate_id ] ) ? $new_cart->taxes[ $rate->tax_rate_id ] : 0 );
							$rate_args['shipping_tax_amount'] = wc_format_decimal( isset( $new_cart->shipping_taxes[ $rate->tax_rate_id ] ) ? $new_cart->shipping_taxes[ $rate->tax_rate_id ] : 0 );
						}

						$subscription_info['taxes'][] = $rate_args;
					}

					$subscription_info['payment_method']       = '';
					$subscription_info['payment_method_title'] = '';
					if ( isset( $posted['payment_method'] ) && $posted['payment_method'] ) {
						$enabled_gateways = WC()->payment_gateways->get_available_payment_gateways();

						if ( isset( $enabled_gateways[ $posted['payment_method'] ] ) ) {
							$payment_method = $enabled_gateways[ $posted['payment_method'] ];
							$payment_method->validate_fields();
							$subscription_info['payment_method']       = $payment_method->id;
							$subscription_info['payment_method_title'] = $payment_method->get_title();
						}
					}

					if ( isset( $this->cart_item_order_item[ $cart_item_key ] ) ) {
						$order_item_id                                       = $this->cart_item_order_item[ $cart_item_key ];
						$this->subscriptions_info['order'][ $order_item_id ] = $subscription_info;
						wc_add_order_item_meta( $order_item_id, '_subscription_info', $subscription_info, true );
					}


					$new_cart->empty_cart( true );
					WC()->cart->empty_cart( true );
					WC()->session->set( 'cart', $this->actual_cart );
					WC()->cart->get_cart_from_session();
					WC()->cart->set_session();
				}
			}
		}


		/**
		 *
		 */
		public function revert_cart_after_checkout() {
			if ( isset( $this->order ) ) {
				$cart = get_post_meta( $this->order, 'saved_cart', true );
				WC()->cart->empty_cart( true );
				WC()->session->set( 'cart', $cart );
				WC()->cart->get_cart_from_session();
				WC()->cart->set_session();
			}
		}


		/**
		 * Save the options of subscription in an array with order item id
		 *
		 * @access   public
		 *
		 * @param  $item_id
		 * @param  $item WC_Order_Item_Product
		 * @param  $order_id
		 *
		 * @return string
		 * @internal param int $cart_item_key
		 *
		 * @internal param int $item_id
		 * @internal param array $values
		 */
		public function add_subscription_order_item_meta( $item_id, $item, $order_id ) {
			if ( isset( $item->legacy_cart_item_key ) ) {
				$this->cart_item_order_item[ $item->legacy_cart_item_key ] = $item_id;
			}
		}

		/**
		 * Overwrite chosen shipping method temp for calculate the subscription shippings
		 *
		 * @access public
		 *
		 * @param $method
		 *
		 * @return string
		 */
		public function change_shipping_chosen_method_temp( $method ) {
			return isset( $this->subscription_shipping_method_temp ) ? $this->subscription_shipping_method_temp : $method;
		}

		/**
		 * Check in the order if there's a subscription and create it
		 *
		 * @access public
		 *
		 * @param  $order_id int
		 * @param  $posted   array
		 *
		 * @return void
		 * @throws Exception
		 */
		public function check_order_for_subscription( $order_id, $posted ) {

			$order          = wc_get_order( $order_id );
			$order_items    = $order->get_items();
			$order_args     = array();
			$user_id        = $order->get_customer_id();
			$order_currency = $order->get_currency();

			//check id the the subscriptions are created
			$subscriptions = $order->get_meta( 'subscriptions' );

			if ( empty( $order_items ) || ! empty( $subscriptions ) ) {
				return;
			}

			$subscriptions = is_array( $subscriptions ) ? $subscriptions : array();

			$order_has_subscription = apply_filters( 'ywsbs_force_order_has_subscriptions', false );

			foreach ( $order_items as $key => $order_item ) {

				/** @var  $_product WC_Product */
				$_product = $order_item->get_product();


				if ( $_product == false ) {
					continue;
				}

				$id = $_product->get_id();

				$args = array();

				if ( YITH_WC_Subscription()->is_subscription( $id ) ) {

					if ( YITH_WC_Subscription()->debug_active ) {
						YITH_WC_Subscription()->debug->add( 'ywsbs', 'find a subscription at woocommerce_checkout_order_processed : ' . $order_id . ' product_id : ' . $id );
					}

					if ( ! isset( $this->subscriptions_info['order'][ $key ] ) ) {
						continue;
					}

					$order_has_subscription = true;
					$subscription_info      = $this->subscriptions_info['order'][ $key ];

					$max_length        = yit_get_prop( $_product, '_ywsbs_max_length' );
					$price_is_per      = yit_get_prop( $_product, '_ywsbs_price_is_per' );
					$price_time_option = yit_get_prop( $_product, '_ywsbs_price_time_option' );
					$fee               = yit_get_prop( $_product, '_ywsbs_fee' );
					$duration          = ( empty( $max_length ) ) ? '' : ywsbs_get_timestamp_from_option( 0, $max_length, $price_time_option );

					// DOWNGRADE PROCESS
					// Set a trial period for the new downgrade subscription so the next payment will be due at the expiration date of the previous subscription
					if ( get_user_meta( get_current_user_id(), 'ywsbs_trial_' . $id, true ) != '' ) {
						$trial_info        = get_user_meta( get_current_user_id(), 'ywsbs_trial_' . $id, true );
						$trial_period      = $trial_info['trial_days'];
						$trial_time_option = 'days';
					} else {
						$trial_period      = yit_get_prop( $_product, '_ywsbs_trial_per' );
						$trial_time_option = yit_get_prop( $_product, '_ywsbs_trial_time_option' );
					}

					//if this subscription is a downgrade the old subscription will be cancelled
					$subscription_to_update_id = get_user_meta( get_current_user_id(), 'ywsbs_downgrade_' . $id, true );
					if ( $subscription_to_update_id != '' ) {
						$args_cancel_subscription = array(
							'subscription_to_cancel' => $subscription_to_update_id,
							'process_type'           => 'downgrade',
							'product_id'             => $id,
							'user_id'                => get_current_user_id()
						);

						$order_args['_ywsbs_subscritpion_to_cancel'] = $args_cancel_subscription;
					}

					/****************/

					// UPGRADE PROCESS
					// if the we are in the upgrade process and the prorate must be done
					$subscription_old_id       = $pay_gap = '';
					$prorate_length            = yit_get_prop( $_product, '_ywsbs_prorate_length' );
					$gap_payment               = yit_get_prop( $_product, '_ywsbs_gap_payment' );
					$subscription_upgrade_info = get_user_meta( get_current_user_id(), 'ywsbs_upgrade_' . $id, true );

					if ( ! empty( $subscription_upgrade_info ) ) {
						$subscription_old_id = $subscription_upgrade_info['subscription_id'];
						$pay_gap             = $subscription_upgrade_info['pay_gap'];
						$trial_period        = '';

						//if this subscription is an upgrade the old subscription will be cancelled
						if ( $subscription_old_id != '' ) {
							$args_cancel_subscription = array(
								'subscription_to_cancel' => $subscription_old_id,
								'process_type'           => 'upgrade',
								'product_id'             => $id,
								'user_id'                => get_current_user_id()
							);

							$order_args['_ywsbs_subscritpion_to_cancel'] = $args_cancel_subscription;
						}

					}

					if ( $prorate_length == 'yes' && ! empty( $max_length ) && $subscription_old_id != '' ) {

						$old_sub         = ywsbs_get_subscription( $subscription_old_id );
						$activity_period = $old_sub->get_activity_period();

						if ( $price_time_option == $old_sub->price_time_option ) {
							$new_max_length = $max_length - ceil( $activity_period / ywsbs_get_timestamp_from_option( 0, 1, $old_sub->price_time_option ) );
						} else {
							$new_duration   = ywsbs_get_days( $duration - $activity_period );
							$new_max_length = $new_duration / ywsbs_get_timestamp_from_option( 0, 1, $price_time_option );
						}

						$max_length = abs( $new_max_length );
					}

					if ( $gap_payment == 'yes' && $pay_gap > 0 ) {
						//change the fee of the subscription adding the total amount of the previous rates
						$fee = $pay_gap;
					}


					if ( $fee ) {
						$order_item->add_meta_data( '_fee', $fee );
					}

					/****************/

					// fill the array for subscription creation
					$args = array(
						'product_id'              => $order_item['product_id'],
						'variation_id'            => $order_item['variation_id'],
						'variation'               => ( isset( $subscription_info['variation'] ) ? $subscription_info['variation'] : '' ),
						'product_name'            => $order_item['name'],

						//order details
						'order_id'                => $order_id,
						'order_item_id'           => $key,
						'order_ids'               => array( $order_id ),
						'line_subtotal'           => $subscription_info['line_subtotal'],
						'line_total'              => $subscription_info['line_total'],
						'line_subtotal_tax'       => $subscription_info['line_subtotal_tax'],
						'line_tax'                => $subscription_info['line_tax'],
						'line_tax_data'           => $subscription_info['line_tax_data'],
						'cart_discount'           => $subscription_info['cart_discount'],
						'cart_discount_tax'       => $subscription_info['cart_discount_tax'],
						'coupons'                 => ( isset( $subscription_info['coupons'] ) ) ? $subscription_info['coupons'] : '',
						'order_total'             => $subscription_info['order_total'],
						'subscription_total'      => $subscription_info['order_total'],
						'order_tax'               => $subscription_info['order_tax'],
						'order_subtotal'          => $subscription_info['order_subtotal'],
						'order_discount'          => $subscription_info['order_discount'],
						'order_shipping'          => $subscription_info['order_shipping'],
						'order_shipping_tax'      => $subscription_info['order_shipping_tax'],
						'subscriptions_shippings' => $subscription_info['shipping'],
						'payment_method'          => $subscription_info['payment_method'],
						'payment_method_title'    => $subscription_info['payment_method_title'],
						'order_currency'          => $order_currency,
						'prices_include_tax'      => $order->get_meta( 'prices_include_tax' ),
						//user details
						'quantity'                => $order_item['qty'],
						'user_id'                 => $user_id,
						'customer_ip_address'     => $order->get_customer_ip_address(),
						'customer_user_agent'     => $order->get_customer_user_agent(),
						//item subscription detail
						'price_is_per'            => $price_is_per,
						'price_time_option'       => $price_time_option,
						'max_length'              => $max_length,
						'trial_per'               => $trial_period,
						'trial_time_option'       => $trial_time_option,
						'fee'                     => $fee,
						'num_of_rates'            => ( $max_length && $price_is_per ) ? $max_length / $price_is_per : ''
					);

					$subscription = new YWSBS_Subscription( '', $args );

					//save the version of plugin in the order
					$order_args['_ywsbs_order_version'] = YITH_YWSBS_VERSION;

					if ( $subscription->id ) {
						$subscriptions[]             = $subscription->id;
						$order_args['subscriptions'] = $subscriptions;
						$order->add_order_note( sprintf( __( 'A new subscription <a href="%s">#%s</a> has been created from this order', 'yith-woocommerce-subscription' ), admin_url( 'post.php?post=' . $subscription->id . '&action=edit' ), $subscription->id ) );

						wc_add_order_item_meta( $key, '_subscription_id', $subscription->id, true );

						if ( YITH_WC_Subscription()->debug_active ) {
							YITH_WC_Subscription()->debug->add( 'ywsbs', 'Created a new subscription ' . $subscription->id . ' for order: ' . $order_id );
						}

						$product_id = ( $subscription->variation_id ) ? $subscription->variation_id : $subscription->product_id;
						delete_user_meta( $subscription->user_id, 'ywsbs_trial_' . $product_id );
					}
				}
			}

			if ( ! empty( $order_args ) ) {
				foreach ( $order_args as $key => $value ) {
					$order->update_meta_data( $key, $value );
				}
				$order->save();

				if( apply_filters( 'ywsbs_calculate_order_totals_condition', true) ){
					$order->calculate_totals();
				}

				WC()->session->set('ywsbs_order_args', $order_args );
			}

			if ( $order_has_subscription ) {
				do_action( 'ywcsb_after_calculate_totals', $order );
			}

		}

		/**
		 * @param $order_id
		 */
		public function remove_subscription_from_order( $order_id ) {
			$order         = wc_get_order( $order_id );
			$subscriptions = $order->get_meta( 'subscriptions' );
			if ( ! $subscriptions ) {
				return;
			}

			foreach ( $subscriptions as $subscription_id ) {
				$subscription = ywsbs_get_subscription( $subscription_id );
				$subscription->delete();
				$order->add_order_note( sprintf( __( 'The subscription %s created from this orders has been cancelled because the order item was cancelled', 'yith-woocommerce-subscription' ), $subscription_id ) );
			}

			$order->delete_meta_data( 'subscriptions' );
			$order->save();
		}

		/**
		 * Actives a subscription after a payment is done
		 *
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 *
		 * @param $order_id
		 *
		 * @return void
		 */
		public function payment_complete( $order_id ) {

			if ( isset( $this->payment_done[ $order_id ] ) ) {
				return;
			}

			$order_id = apply_filters( 'ywsbs_order_id_on_payment_complete', $order_id );


			$order         = wc_get_order( $order_id );
			$subscriptions = $order->get_meta( 'subscriptions' );

			if ( $subscriptions ) {

				foreach ( $subscriptions as $subscription_id ) {
					$subscription = ywsbs_get_subscription( $subscription_id );

					//check if the subscription exists
					if ( is_null( $subscription->post ) ) {
						continue;
					}

					$payed_order = is_array( $subscription->payed_order_list ) ? $subscription->payed_order_list : array();

					if ( ! in_array( $order_id, $payed_order ) ) {
						if ( $subscription->renew_order != 0 && $subscription->renew_order == $order_id ) {
							$subscription->update( $order_id );
						} elseif ( $subscription->renew_order == 0 ) {
							$subscription->start( $order_id );
						}
					}

					$product_id = ( $subscription->variation_id ) ? $subscription->variation_id : $subscription->product_id;
					delete_user_meta( $subscription->customer_user, 'ywsbs_trial_' . $product_id );

					do_action( 'ywsbs_subscription_payment_complete', $subscription, $order );
				}
			}

			$this->payment_done[ $order_id ] = true;
		}

		/**
		 * @param null $subscription
		 *
		 * @return string
		 */
		public function get_renew_order_status( $subscription = null ) {

			$new_status = 'on-hold';

			if ( ! is_null( $subscription ) && $subscription->payment_method == 'bacs' ) {
				$new_status = 'pending';
			}

			//the status must be register as wc status
			$status = apply_filters( 'ywsbs_renew_order_status', $new_status, $subscription );

			return $status;
		}

		/**
		 * Create a new order for next payments of a subscription
		 *
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 *
		 * @param $subscription_id
		 *
		 * @return int
		 * @throws Exception
		 */
		public function renew_order( $subscription_id ) {

			yith_subscription_log( 'Creating renew order for the subscription #'.$subscription_id, 'subscription_payment' );

			$subscription   = ywsbs_get_subscription( $subscription_id );
			$parent_order   = $subscription->get_order();
			$status         = $this->get_renew_order_status( $subscription );
			$renew_order_id = $subscription->can_be_create_a_renew_order();

			$indentation = '----';
			$message = $indentation.' Original order id '.$parent_order->get_id();
			yith_subscription_log( 'Here the subscription data:' , 'subscription_payment' );
			yith_subscription_log( $message , 'subscription_payment' );

			$message = $indentation.' the renew order must have the status '.$status;
			yith_subscription_log( $message , 'subscription_payment' );

			if( $renew_order_id ) {
				$message = $indentation . ' the renew order not exist, create!';
				yith_subscription_log( $message , 'subscription_payment' );
			}else{
				$message = $indentation . ' the renew order exist and is '.$renew_order_id;
				yith_subscription_log( $message , 'subscription_payment' );

				$renew_order = wc_get_order( $renew_order_id );
				if ( $renew_order ) {
					$message .= $indentation . $indentation . ' ' . $renew_order->get_formatted_billing_address() . '\n';
				}
			}
			if ( $renew_order_id && is_int( $renew_order_id ) ) {
				return $renew_order_id;
			} elseif ( $renew_order_id === false ) {
				return false;
			}

			if( ! $parent_order ){
				$message = $indentation . ' the renew order cannot created because the parent order not exist';
				yith_subscription_log(  $message , 'subscription_payment' );

				$subscription->cancel();
				return false;
			}

			if ( apply_filters( 'ywsbs_skip_create_renew_order', false, $subscription ) ) {
				$message = $indentation . ' the renew order cannot created because is added filter to skip';
				yith_subscription_log(  $message , 'subscription_payment' );
				return false;
			}

			$order = wc_create_order( $args = array(
				'status'      => 'renew',
				'customer_id' => $subscription->get( 'user_id' )
			) );

			$message = $indentation.'the customer with subscription #'.$subscription->id.' has this user id #'.$subscription->get( 'user_id' );
			yith_subscription_log( $message , 'subscription_payment' );
			$message = $indentation . ' the renew order created is #'.$order->get_id().' the customer';
			yith_subscription_log( $message , 'subscription_payment' );

			$args = array(
				'subscriptions'        => array( $subscription_id ),
				'payment_method'       => $subscription->get( 'payment_method' ),
				'payment_method_title' => $subscription->get( 'payment_method_title' ),
				'currency'             => $subscription->get( 'order_currency' ),
				'failed_attemps'       => 0,
				'next_payment_attempt' => 0
			);


			$customer_note = $parent_order->get_customer_note();
			$customer_note && $args['customer_note'] = $customer_note;

			$message = $indentation . 'Check the billing an shipping info' ;
			yith_subscription_log( $message , 'subscription_payment' );

			// get billing
			$billing_fields = $subscription->get_address_fields( 'billing' );
			// get shipping
			$shipping_fields = $subscription->get_address_fields( 'shipping' );

			$args = array_merge( $args, $shipping_fields, $billing_fields );

			foreach ( $args as $key => $field ) {
				$set = 'set_' . $key;
				$message = $indentation.' '.$set.'\n';
				yith_subscription_log( $message , 'subscription_payment' );
				if ( method_exists( $order, $set ) ) {
					$message= $indentation.$indentation.' '.$field;
					yith_subscription_log( $message , 'subscription_payment' );
					$order->$set( $field );
				} else {
					$key = in_array( $key, array( 'billing_vat', 'billing_ssn' ) ) ? '_' . $key : $key;
					$order->update_meta_data( $key, $field );
					$message = $indentation.$indentation.' '.$key.' '.print_r( $field, true );
					yith_subscription_log( $message , 'subscription_payment' );
				}
			}

			$order_id = $order->get_id();
			$_product = $subscription->get_product();

			$item_id = $order->add_product( $_product, $subscription->get( 'quantity' ), array(
				'variation' => array(),
				'totals'    => array(
					'subtotal'     => $subscription->get( 'line_subtotal' ),
					'subtotal_tax' => $subscription->get( 'line_subtotal_tax' ),
					'total'        => $subscription->get( 'line_total' ),
					'tax'          => $subscription->get( 'line_tax' ),
					'tax_data'     => $subscription->get( 'line_tax_data' )
				)
			) );


			if ( ! $item_id ) {
				throw new Exception( sprintf( __( 'Error %d: unable to create the order. Please try again.', 'yith-woocommerce-subscription' ), 402 ) );
			} else {

				$metadata = get_metadata( 'order_item', $subscription->get( 'order_item_id' ) );

				if ( $metadata ) {
					foreach ( $metadata as $key => $value ) {
						if ( apply_filters( 'ywsbs_renew_order_item_meta_data', is_array( $value ) && count( $value ) == 1 && '_fee' != $key, $subscription->get( 'order_item_id' ), $key, $value ) ) {
							add_metadata( 'order_item', $item_id, $key, maybe_unserialize( $value[0] ), true );
						}
					}
				}

			}

			$shipping_cost = 0;


			//Shipping
			if ( apply_filters( 'ywsbs_add_shipping_cost_order_renew', ! empty( $subscription->subscriptions_shippings ) ) ) {


				$shipping_item_id = wc_add_order_item( $order_id, array(
					'order_item_name' => $subscription->subscriptions_shippings['name'],
					'order_item_type' => 'shipping',
				) );

				$shipping_cost     = isset( $subscription->subscriptions_shippings['cost'] ) ? $subscription->subscriptions_shippings['cost'] : 0;
				$shipping_cost_tax = 0;

				if ( isset( $subscription->subscriptions_shippings['method_id'] ) ) {
					wc_add_order_item_meta( $shipping_item_id, 'method_id', $subscription->subscriptions_shippings['method_id'] );
				}

				wc_add_order_item_meta( $shipping_item_id, 'cost', wc_format_decimal( $shipping_cost ) );
				if ( isset( $subscription->subscriptions_shippings['taxes'] ) ) {
					wc_add_order_item_meta( $shipping_item_id, 'taxes', $subscription->subscriptions_shippings['taxes'] );
				}

				if ( ! empty( $subscription->subscriptions_shippings['taxes'] ) ) {
					foreach ( $subscription->subscriptions_shippings['taxes'] as $tax_cost ) {
						$shipping_cost_tax += $tax_cost;
					}
				}

				$order->set_shipping_total( $shipping_cost );
				$order->set_shipping_tax( $subscription->subscriptions_shippings['taxes'] );
				$order->save();

			} else {
				do_action( 'ywsbs_add_custom_shipping_costs', $order, $subscription );
			}

			$cart_discount_total     = 0;
			$cart_discount_total_tax = 0;

			//coupons
			if ( ! empty( $subscription->coupons ) ) {
				foreach ( $subscription->coupons as $coupon ) {
					$order->add_coupon( $coupon['coupon_code'], $coupon['discount_amount'], $coupon['discount_amount_tax'] );
					$cart_discount_total     += $coupon['discount_amount'];
					$cart_discount_total_tax += $coupon['discount_amount_tax'];
				}
			}

			$order->set_discount_total( $cart_discount_total );

			if ( isset( $subscription->subscriptions_shippings['taxes'] ) && $subscription->subscriptions_shippings['taxes'] ) {
				/**
				 * this fix the shipping taxes removed form WC settings
				 * if in a previous tax there was the taxes this will be forced
				 * even if they are disabled for the shipping
				 */
				add_action( 'woocommerce_find_rates', array( $this, 'add_shipping_tax' ), 10 );
			}

			$order->update_meta_data( 'is_a_renew', 'yes' );
			$order->set_discount_total( $cart_discount_total );
			$order->update_taxes();
			$order->calculate_totals();
			$order->set_status( $status );
			$order->save();

			$order->add_order_note( sprintf( __( 'This order has been created to renew subscription <a href="%s">#%s</a>', 'yith-woocommerce-subscription' ), admin_url( 'post.php?post=' . $subscription->id . '&action=edit' ), $subscription->id ) );

			//attach the new order to the subscription
			$orders = $subscription->get( 'order_ids' );
			array_push( $orders, $order_id );
			$subscription->set( 'order_ids', $orders );

			YITH_WC_Activity()->add_activity( $subscription->id, 'renew-order', 'success', $order_id, sprintf( __( 'The order %d has been created for the subscription', 'yith-woocommerce-subscription' ), $order_id ) );

			$subscription->set( 'renew_order', $order_id );

			do_action( 'ywsbs_renew_subscription', $order_id, $subscription_id );

			return $order_id;

		}

		/**
		 * this fix the shipping taxes removed form WC settings
		 * if in a previous tax there was the taxes this will be forced
		 * even if they are disabled for the shipping
		 *
		 * @param $shipping_taxes
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_shipping_tax( $shipping_taxes ) {

			foreach ( $shipping_taxes as &$shipping_tax ) {
				$shipping_tax['shipping'] = 'yes';

			}

			return $shipping_taxes;
		}

		/**
		 * Cancel the subscription if the order is refunded
		 *
		 *
		 * @since    1.0.1
		 * @author   Emanuela Castorina
		 *
		 * @param $order_id
		 * @param $refund_id
		 *
		 * @return bool
		 * @internal param $subscription_id
		 *
		 */
		public function order_refunded( $order_id, $refund_id = 0 ) {

			$order         = wc_get_order( $order_id );
			$subscriptions = $order->get_meta( 'subscriptions' );

			if ( $subscriptions ) {
				foreach ( $subscriptions as $subscription_id ) {
					$subscription = ywsbs_get_subscription( $subscription_id );

					if ( is_null( $subscription ) ) {
						continue;
					}

					if ( $subscription->status == 'cancelled' ) {
						$subscription->set( 'end_date', current_time( 'timestamp' ) );
						do_action( 'ywsbs_refund_subscription', $subscription );
					} else {
						// filter added to gateway payments
						if ( ! apply_filters( 'ywsbs_cancel_recurring_payment', true, $subscription ) ) {
							$order->add_order_note( __( 'The subscription cannot be cancelled', 'yith-woocommerce-subscription' ) );

							return false;
						}
						$subscription->update_status( 'cancel-now', 'refund' );
					}
				}
			}
		}


		/**
		 * @param $order_id
		 */
		public function reactive_subscription( $order_id ) {

			$order         = wc_get_order( $order_id );
			$subscriptions = $order->get_meta( 'subscriptions' );

			if ( $subscriptions ) {
				foreach ( $subscriptions as $subscription_id ) {
					$subscription = ywsbs_get_subscription( $subscription_id );
					if ( is_null( $subscription ) ) {
						continue;
					}

					if ( $subscription->status != 'cancelled' && $subscription->status != 'trial' ) {
						$subscription->update_status( 'active', 'resumed' );
					}

				}
			}
		}


		/**
		 * Check if the new order have subscriptions
		 *
		 * @access public
		 *
		 * @return bool
		 *
		 * @since  1.0.0
		 */
		public function the_order_have_subscriptions() {
			if ( isset( WC()->cart ) ) {
				foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
					$id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];
					if ( YITH_WC_Subscription()->is_subscription( $id ) ) {
						return true;
					}
				}
			}

			return false;
		}

		/**
		 * If there's a subscription inside the order, even if the order total is $0, it still needs payment
		 *
		 * @param $needs_payment        bool
		 * @param $order                WC_Order
		 * @param $valid_order_statuses array
		 *
		 * @return bool
		 *
		 * @since  1.0.0
		 */
		public function order_need_payment( $needs_payment, $order, $valid_order_statuses ) {

			if ( ! $needs_payment && $this->the_order_have_subscriptions() && in_array( $order->get_status(), $valid_order_statuses ) && 0 == $order->get_total() ) {
				return true;
			}

			return $needs_payment;

		}


		/**
		 * Delete all subscription if an order change the status to cancelled
		 *
		 * @param $order_id
		 */
		public function delete_subscriptions( $order_id ) {
			if ( in_array( get_post_type( $order_id ), wc_get_order_types(), true ) ) {

				$order = wc_get_order( $order_id );

				if ( ! $order ) {
					return;
				}


				$is_a_renew    = $order->get_meta( 'is_a_renew' );
				$subscriptions = $order->get_meta( 'subscriptions' );

				if ( empty( $subscriptions ) || $is_a_renew == 'yes' ) {
					return;
				}

				foreach ( $subscriptions as $subscription_id ) {
					$subscription = ywsbs_get_subscription( $subscription_id );
					//check if the subscription exists
					if ( is_null( $subscription->post ) ) {
						continue;
					}

					$subscription->delete();
				}
			}

		}

		/**
		 * Return false if the option reduce order stock is disabled for the renew order
		 *
		 * @param $result
		 * @param $order
		 *
		 * @return bool
		 * @since  1.2.6
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function can_reduce_order_stock( $result, $order ) {
			$is_a_renew = $order->get_meta( 'is_a_renew' );

			if ( 'yes' == get_option( 'ywsbs_disable_the_reduction_of_order_stock_in_renew' ) && 'yes' == $is_a_renew ) {
				$result = false;
			}

			return $result;
		}

		/**
		 * Filters needs_payment for the order, to make renew payable when user try to manually pay for it
		 *
		 * @param $needs_payment bool Whether order needs payment
		 * @param $order \WC_Order Order
		 * @param $valid_statuses array Array of valid order statuses for payment
		 * @return bool Filtered version of needs payment
		 */
		function renew_needs_payment( $needs_payment, $order, $valid_statuses ){
			/**
			 * @var $order \WC_Order
			 */
			if( 'yes' != $order->get_meta( 'is_a_renew', true ) ){
				return $needs_payment;
			}

			if( ! is_checkout_pay_page() ){
				return $needs_payment;
			}

			if( ! isset( $_GET['ywsbs_manual_renew'] ) || ! wp_verify_nonce( $_GET['ywsbs_manual_renew'], 'ywsbs_manual_renew' ) ){
				return $needs_payment;
			}

			if( ! $order->has_status( YWSBS_Subscription_Order()->get_renew_order_status() ) ){
				return $needs_payment;
			}

			return true;
		}
	}
}

/**
 * Unique access to instance of YWSBS_Subscription_Order class
 *
 * @return \YWSBS_Subscription_Order
 */
function YWSBS_Subscription_Order() {
	return YWSBS_Subscription_Order::get_instance();
}
