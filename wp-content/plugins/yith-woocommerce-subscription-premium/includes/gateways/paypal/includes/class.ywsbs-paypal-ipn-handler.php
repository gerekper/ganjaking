<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements YWSBS_PayPal_IPN_Handler Class
 *
 * @class   YWSBS_PayPal_IPN_Handler
 * @package YITH WooCommerce Subscription
 * @since   1.0.1
 * @author  YITH
 */
if ( ! class_exists( 'YWSBS_PayPal_IPN_Handler' ) ) {

	class YWSBS_PayPal_IPN_Handler extends WC_Gateway_Paypal_IPN_Handler {


		protected $transaction_types = array(
			'subscr_signup',
			'subscr_payment',
			'subscr_modify',
			'subscr_failed',
			'subscr_eot',
			'subscr_cancel',
			'recurring_payment_suspended_due_to_max_failed_payment',
			'recurring_payment_skipped',
			'recurring_payment_outstanding_payment',
		);

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 *
		 * @param bool   $sandbox
		 * @param string $receiver_email
		 */
		public function __construct( $sandbox = false, $receiver_email = '' ) {

			add_action( 'valid-paypal-standard-ipn-request', array( $this, 'valid_response' ), 0 );
			$this->receiver_email = $receiver_email;
		}

		/**
		 * There was a valid response
		 *
		 * @param  array $posted Post data after wp_unslash
		 *
		 * @throws Exception
		 */
		public function valid_response( $posted ) {

			if ( ! empty( $posted['custom'] ) ) {

				$order = $this->get_paypal_order( $posted['custom'] );

			} elseif ( ! empty( $posted['invoice'] ) ) {

				$order = $this->get_paypal_order_from_invoice( $posted['invoice'] );
			}

			if ( $order ) {

				$order_id = yit_get_prop( $order, 'id' );
				WC_Gateway_Paypal::log( 'PayPal IPN: ' . print_r( $posted, true ) );
				WC_Gateway_Paypal::log( 'YWSBS - Found order #' . $order_id );

				if ( isset( $posted['payment_status'] ) ) {
					$posted['payment_status'] = strtolower( $posted['payment_status'] );

					if ( 'refunded' == $posted['payment_status'] ) {
						$this->check_subscription_child_refunds( $order, $posted );
					}
				}

				WC_Gateway_Paypal::log( 'YWSBS - Txn Type: ' . $posted['txn_type'] );

				add_filter( 'update_post_metadata', array( $this, 'check_metadata' ), 10, 5 );

				$this->process_paypal_request( $order, $posted );

			} else {
				WC_Gateway_Paypal::log( 'YWSBS - 404 Order Not Found.' );
			}
		}


		public function get_paypal_order_from_invoice( $invoice ) {
			$extract      = explode( '-', $invoice );
			$order_number = false;
			$order        = false;

			if ( is_array( $extract ) ) {
				$order_number = end( $extract );
			}

			if ( empty( $order_number ) ) {
				return false;
			}

			$query_args = array(
				'numberposts' => 1,
				'meta_key'    => '_order_number',
				'meta_value'  => $order_number,
				'post_type'   => 'shop_order',
				'post_status' => 'any',
				'fields'      => 'ids',
			);

			$posts            = get_posts( $query_args );
			list( $order_id ) = ! empty( $posts ) ? $posts : null;

			// order was found
			if ( $order_id !== null ) {
				$order = wc_get_order( $order_id );
			}

			return $order;

		}


		/**
		 * Handle a completed payment
		 *
		 * @param  WC_Order $order
		 *
		 * @param $posted
		 *
		 * @throws Exception
		 */
		protected function process_paypal_request( $order, $posted ) {

			if ( isset( $posted['txn_type'] ) && ! $this->validate_transaction_type( $posted['txn_type'] ) ) {
				return;
			}

			if ( isset( $posted['mc_currency'] ) ) {
				$this->validate_currency( $order, $posted['mc_currency'] );
			}

			WC_Gateway_Paypal::log( 'YWSBS - Validate currency OK' );

			if ( isset( $posted['receiver_email'] ) ) {
				$this->validate_receiver_email( $order, $posted['receiver_email'] );
			}

			WC_Gateway_Paypal::log( 'YWSBS - Receiver Email OK' );

			$this->save_paypal_meta_data( $order, $posted );

			$this->paypal_ipn_request( $posted );
		}


		protected function get_order_info( $args ) {
			if ( isset( $args['custom'] ) ) {
				$order_info = json_decode( $args['custom'], true );
			}
			return $order_info;
		}

		/**
		 * Catch the paypal ipn request for subscription
		 *
		 * @param array $ipn_args
		 *
		 * @throws Exception
		 */
		protected function paypal_ipn_request( $ipn_args ) {

			WC_Gateway_Paypal::log( 'YSBS - Paypal IPN Request Start' );

			// check if the order has the same order_key
			$order_info = $this->get_order_info( $ipn_args );
			$order      = wc_get_order( $order_info['order_id'] );
			$order_id   = $order_info['order_id'];

			if ( $order->get_order_key() != $order_info['order_key'] ) {
				WC_Gateway_Paypal::log( 'YSBS - Order keys do not match' );
				return;
			}

			// check if the transaction has been processed
			$order_transaction_ids = yit_get_prop( $order, '_paypal_transaction_ids' );
			$order_transactions    = $this->is_a_valid_transaction( $order_transaction_ids, $ipn_args );
			if ( $order_transactions ) {
				update_post_meta( $order_id, '_paypal_transaction_ids', $order_transactions );
			} else {
				WC_Gateway_Paypal::log( 'YSBS - Transaction ID already processed' );
				return;
			}

			// get the subscriptions of the order
			$subscriptions = yit_get_prop( $order, 'subscriptions' );

			if ( empty( $subscriptions ) ) {
				WC_Gateway_Paypal::log( 'YSBS - IPN subscription payment error - ' . $order_id . ' haven\'t subscriptions' );
				return;
			} else {
				WC_Gateway_Paypal::log( 'YSBS - Subscription ' . print_r( $subscriptions, true ) );
			}

			$valid_order_statuses = array( 'on-hold', 'pending', 'failed', 'cancelled' );

			switch ( $ipn_args['txn_type'] ) {
				case 'subscr_signup':
					$args = array(
						'Subscriber ID'         => $ipn_args['subscr_id'],
						'Subscriber first name' => $ipn_args['first_name'],
						'Subscriber last name'  => $ipn_args['last_name'],
						'Subscriber address'    => $ipn_args['payer_email'],
						'Payment type'          => isset( $ipn_args['payment_type'] ) ? $ipn_args['payment_type'] : '',
					);

					$order->add_order_note( __( 'IPN subscription started', 'yith-woocommerce-subscription' ) );
					$txn_id = isset( $ipn_args['txn_id'] ) ? $ipn_args['txn_id'] : '';

					foreach ( $subscriptions as $subscription_id ) {
						$subscription = new YWSBS_Subscription( $subscription_id );
						$subscription->set( 'paypal_transaction_id', $txn_id );
						$subscription->set( 'paypal_subscriber_id', $ipn_args['subscr_id'] );
					}

					yit_save_prop( $order, $args );

					if ( isset( $ipn_args['mc_amount1'] ) && $ipn_args['mc_amount1'] == 0 ) {
						$order->payment_complete( $txn_id );
						exit;
					}

					break;
				case 'recurring_payment_outstanding_payment':
				case 'subscr_payment':
					if ( 'completed' == strtolower( $ipn_args['payment_status'] ) ) {

						foreach ( $subscriptions as $subscription_id ) {
							$subscription = new YWSBS_Subscription( $subscription_id );

							$transaction_ids = get_post_meta( $subscription_id, '_paypal_transaction_ids', true );
							$transactions    = $this->is_a_valid_transaction( $transaction_ids, $ipn_args );
							if ( $transactions ) {
								update_post_meta( $subscription_id, '_paypal_transaction_ids', $transactions );
							} else {
								break;
							}

							$last_order    = false;
							$pending_order = $subscription->renew_order;

							if ( intval( $pending_order ) ) {
								$last_order = wc_get_order( $pending_order );
							}

							if ( isset( $ipn_args['mc_gross'] ) ) {
								if ( $last_order ) {
									$this->validate_amount( $last_order, $ipn_args['mc_gross'] );
								} elseif ( $order->has_status( $valid_order_statuses ) ) {
									$this->validate_amount( $order, $ipn_args['mc_gross'] );
								}
							}

							if ( isset( $ipn_args['subscr_id'] ) ) {
								$sub_id = $ipn_args['subscr_id'];
							} elseif ( isset( $ipn_args['recurring_payment_id'] ) ) {
								$sub_id = $ipn_args['recurring_payment_id'];
							}

							isset( $ipn_args['txn_id'] ) && $subscription->set( 'paypal_transaction_id', $ipn_args['txn_id'] );
							isset( $ipn_args['subscr_id'] ) && $subscription->set( 'paypal_subscriber_id', $ipn_args['subscr_id'] );
							$subscription->set( 'payment_method', 'paypal' );
							$subscription->set( 'payment_method_title', 'PayPal' );

							if ( $subscription->status == 'pending' || ( ! $last_order && $order->has_status( $valid_order_statuses ) ) ) {

								$args = array(
									'Subscriber ID'        => $sub_id,
									'Subscriber first name' => $ipn_args['first_name'],
									'Subscriber last name' => $ipn_args['last_name'],
									'Subscriber address'   => $ipn_args['payer_email'],
									'Subscriber payment type' => wc_clean( $ipn_args['payment_type'] ),
									'Payment type'         => wc_clean( $ipn_args['payment_type'] ),
								);

								yit_save_prop( $order, $args );
								$order->add_order_note( __( 'IPN subscription payment completed.', 'yith-woocommerce-subscription' ) );
								$order->payment_complete( $ipn_args['txn_id'] );
								exit;

							} elseif ( $last_order ) {

								$args = array(
									'Subscriber ID'        => $sub_id,
									'Subscriber first name' => $ipn_args['first_name'],
									'Subscriber last name' => $ipn_args['last_name'],
									'Subscriber address'   => $ipn_args['payer_email'],
									'Subscriber payment type' => wc_clean( $ipn_args['payment_type'] ),
									'Payment type'         => wc_clean( $ipn_args['payment_type'] ),
								);

								yit_save_prop( $last_order, $args, false, true );

								$last_order->add_order_note( __( 'IPN subscription payment completed.', 'yith-woocommerce-subscription' ) );
								$last_order->payment_complete( $ipn_args['txn_id'] );
								exit;

							} else {

								// if the renew_order is not created try to create it
								$new_order_id = YWSBS_Subscription_Order()->renew_order( $subscription->id );

								if ( ! $new_order_id ) {
									YITH_WC_Activity()->add_activity( $subscription_id, 'renew-order', 'failed', $order_id, __( 'Renew order creation failed', 'yith-woocommerce-subscription' ) );
									return;
								}

								$new_order = wc_get_order( $new_order_id );

								if ( isset( $ipn_args['mc_gross'] ) ) {
									$this->validate_amount( $new_order, $ipn_args['mc_gross'] );
								}

								$subscription->set( 'renew_order', $new_order_id );

								$args = array(
									'Subscriber ID'        => $sub_id,
									'Subscriber first name' => $ipn_args['first_name'],
									'Subscriber last name' => $ipn_args['last_name'],
									'Subscriber address'   => $ipn_args['payer_email'],
									'Subscriber payment type' => wc_clean( $ipn_args['payment_type'] ),
									'Payment type'         => wc_clean( $ipn_args['payment_type'] ),
								);

								yit_save_prop( $new_order_id, $args, false, true );

								$new_order->add_order_note( __( 'IPN subscription payment completed.', 'yith-woocommerce-subscription' ) );
								$new_order->payment_complete( $ipn_args['txn_id'] );
								exit;

							}
						}
					}

					if ( isset( $ipn_args['subscr_id'] ) && 'pending' == strtolower( $ipn_args['payment_status'] ) && 'echeck' == strtolower( $ipn_args['payment_type'] ) ) {

						foreach ( $subscriptions as $subscription_id ) {
							$subscription = new YWSBS_Subscription( $subscription_id );

							$transaction_ids = get_post_meta( $subscription_id, '_paypal_transaction_ids', true );
							$transactions    = $this->is_a_valid_transaction( $transaction_ids, $ipn_args );
							if ( $transactions ) {
								update_post_meta( $subscription_id, '_paypal_transaction_ids', $transactions );
							} else {
								break;
							}

							/**check if is a renewal */
							$last_order    = false;
							$pending_order = $subscription->renew_order;

							if ( intval( $pending_order ) ) {
								$last_order = wc_get_order( $pending_order );
							}

							isset( $ipn_args['txn_id'] ) && $subscription->set( 'paypal_transaction_id', $ipn_args['txn_id'] );
							isset( $ipn_args['subscr_id'] ) && $subscription->set( 'paypal_subscriber_id', $ipn_args['subscr_id'] );
							$subscription->set( 'payment_method', 'paypal' );
							$subscription->set( 'payment_method_title', 'PayPal' );

							if ( $subscription->status == 'pending' || ( ! $last_order && $order->has_status( $valid_order_statuses ) ) ) {
								// first payment
								update_post_meta( $subscription_id, 'start_date', current_time( 'timestamp' ) );
								update_post_meta( $subscription_id, 'payment_type', $ipn_args['payment_type'] );
								// in this case change the status of order in on-hold waiting the paypal payment
								$order->update_status( 'on-hold', __( 'Paypal echeck payment', 'yith-woocommerce-subscription' ) );
								yit_save_prop( $order, 'Payment type', $ipn_args['payment_type'], false, true );

								wc_reduce_stock_levels( $order_id );
								WC()->cart->empty_cart();

							} elseif ( $last_order ) {
								// renew order
								$last_order->add_order_note( __( 'YSBS - IPN Pending payment for echeck payment type', 'yith-woocommerce-subscription' ) );
								// if the renewal is payed with echeck and it is in pending, the subscription is suspended
								$subscription->update_status( 'suspended', 'paypal' );
								YITH_WC_Activity()->add_activity( $subscription_id, 'suspended', 'success', $order_id, __( 'Subscription has been suspendend because in pending payment for echeck payment type', 'yith-woocommerce-subscription' ) );

								$last_order->add_order_note( __( 'YSBS - Subscription has been suspendend because in pending payment for echeck payment type', 'yith-woocommerce-subscription' ) );

							} else {
								// if the renew_order is not created try to create it
								$new_order_id = YWSBS_Subscription_Order()->renew_order( $subscription->id );
								if ( ! $new_order_id ) {
									YITH_WC_Activity()->add_activity( $subscription_id, 'renew-order', 'failed', $order_id, __( 'Renew order creation failed', 'yith-woocommerce-subscription' ) );
									return;
								}
								$new_order = wc_get_order( $new_order_id );
								$new_order->add_order_note( __( 'YSBS - IPN Pending payment for echeck payment type', 'yith-woocommerce-subscription' ) );
							}
						}
					}
					if ( isset( $ipn_args['subscr_id'] ) && 'failed' == strtolower( $ipn_args['payment_status'] ) ) {
						if ( isset( $ipn_args['subscr_id'] ) ) {
							$paypal_sub_id = $ipn_args['subscr_id'];
							$order_sub_id  = yit_get_prop( $order, 'Subscriber ID' );

							if ( $paypal_sub_id != $order_sub_id ) {
								WC_Gateway_Paypal::log( 'YSBS - IPN subscription failed request ignored - new PayPal Profile ID linked to this subscription, for order ' . $order_id );
							} else {
								$subscriptions = yit_get_prop( $order, 'subscriptions' );

								if ( empty( $subscriptions ) ) {
									WC_Gateway_Paypal::log( 'YSBS - IPN subscription failed payment request ignored - order ' . $order_id . ' doesn\'t not subscriptions' );
								}

								// let's remove woocommerce default IPN handling, that would switch parent order to Failed
								remove_all_actions( 'valid-paypal-standard-ipn-request', 10 );

								foreach ( $subscriptions as $subscription_id ) {

									$subscription = ywsbs_get_subscription( $subscription_id );

									$transaction_ids = get_post_meta( $subscription_id, '_paypal_transaction_ids', true );
									$transactions    = $this->is_a_valid_transaction( $transaction_ids, $ipn_args );
									if ( $transactions ) {
										update_post_meta( $subscription_id, '_paypal_transaction_ids', $transactions );
									} else {
										break;
									}

									isset( $ipn_args['txn_id'] ) && $subscription->set( 'paypal_transaction_id', $ipn_args['txn_id'] );
									isset( $ipn_args['subscr_id'] ) && $subscription->set( 'paypal_subscriber_id', $ipn_args['subscr_id'] );
									$subscription->set( 'payment_method', 'paypal' );
									$subscription->set( 'payment_method_title', 'PayPal' );

									$last_order    = false;
									$pending_order = $subscription->renew_order;

									if ( intval( $pending_order ) ) {
										$last_order = wc_get_order( $pending_order );
									}

									if ( $subscription->status == 'pending' || ( ! $last_order && $order->has_status( $valid_order_statuses ) ) ) {
										continue;
									} elseif ( $last_order ) {
										$last_order->add_order_note( __( 'YSBS - IPN Failed payment', 'yith-woocommerce-subscription' ) );
									} else {
										// if the renew_order is not created try to create it
										$new_order_id = YWSBS_Subscription_Order()->renew_order( $subscription->id );
										if ( ! $new_order_id ) {
											YITH_WC_Activity()->add_activity( $subscription_id, 'renew-order', 'failed', $order_id, __( 'Renew order creation failed', 'yith-woocommerce-subscription' ) );
											return;
										}
										$new_order = wc_get_order( $new_order_id );
										$new_order->add_order_note( __( 'YSBS - IPN Failed payment', 'yith-woocommerce-subscription' ) );
									}

									// update the number of failed attemp
									$subscription->register_failed_attemp();

									if ( isset( $ipn_args['retry_at'] ) ) {
										yit_save_prop( $order, 'next_payment_attempt', strtotime( $ipn_args['retry_at'] ), false, true );
									}

									if ( get_option( 'ywsbs_suspend_for_failed_recurring_payment' ) == 'yes' ) {

										$subscription->update_status( 'suspended', 'paypal' );

										YITH_WC_Activity()->add_activity( $subscription_id, 'suspended', 'success', $order_id, __( 'Subscription has been suspendend for failed payment', 'yith-woocommerce-subscription' ) );

									}

									$order->add_order_note( __( 'YSBS - IPN Failed payment', 'yith-woocommerce-subscription' ) );

									// Subscription Cancellation Completed

									WC_Gateway_Paypal::log( 'YSBS -IPN Failed payment' . $order_id );

								}
							}
						}
					}
					break;

				case 'subscr_modify':
					break;

				case 'subscr_failed':
					if ( isset( $ipn_args['subscr_id'] ) ) {
						$paypal_sub_id = $ipn_args['subscr_id'];
						$order_sub_id  = yit_get_prop( $order, 'Subscriber ID' );

						if ( $paypal_sub_id != $order_sub_id ) {
							WC_Gateway_Paypal::log( 'YSBS - IPN subscription failed request ignored - new PayPal Profile ID linked to this subscription, for order ' . $order_id );
						} else {
							$subscriptions = yit_get_prop( $order, 'subscriptions' );

							if ( empty( $subscriptions ) ) {
								WC_Gateway_Paypal::log( 'YSBS - IPN subscription failed payment request ignored - order ' . $order_id . ' doesn\'t not subscriptions' );
							}

							foreach ( $subscriptions as $subscription_id ) {

								$subscription = ywsbs_get_subscription( $subscription_id );

								$transaction_ids = get_post_meta( $subscription_id, '_paypal_transaction_ids', true );
								$transactions    = $this->is_a_valid_transaction( $transaction_ids, $ipn_args );
								if ( $transactions ) {
									update_post_meta( $subscription_id, '_paypal_transaction_ids', $transactions );
								} elseif ( isset( $ipn_args['retry_at'] ) ) {
									$retry_at_meta = get_post_meta( $subscription_id, '_retry_at', true );
									if ( $retry_at_meta == $ipn_args['retry_at'] ) {
										break;
									} else {
										update_post_meta( $subscription_id, '_retry_at', $ipn_args['retry_at'] );
									}
								} else {
									break;
								}

								isset( $ipn_args['txn_id'] ) && $subscription->set( 'paypal_transaction_id', $ipn_args['txn_id'] );
								isset( $ipn_args['subscr_id'] ) && $subscription->set( 'paypal_subscriber_id', $ipn_args['subscr_id'] );
								$subscription->set( 'payment_method', 'paypal' );
								$subscription->set( 'payment_method_title', 'PayPal' );

								$last_order    = false;
								$pending_order = $subscription->renew_order;

								if ( intval( $pending_order ) ) {
									$last_order = wc_get_order( $pending_order );
								}

								if ( $subscription->status == 'pending' || ( ! $last_order && $order->has_status( $valid_order_statuses ) ) ) {
									continue;
								} elseif ( $last_order ) {
									$last_order->add_order_note( __( 'YSBS - IPN Failed payment', 'yith-woocommerce-subscription' ) );
								} else {
									// if the renew_order is not created try to create it
									$new_order_id = YWSBS_Subscription_Order()->renew_order( $subscription->id );
									if ( ! $new_order_id ) {
										YITH_WC_Activity()->add_activity( $subscription_id, 'renew-order', 'failed', $order_id, __( 'Renew order creation failed', 'yith-woocommerce-subscription' ) );

										return;
									}
									$new_order = wc_get_order( $new_order_id );
									$new_order->add_order_note( __( 'YSBS - IPN Failed payment', 'yith-woocommerce-subscription' ) );
								}

								// update the number of failed attemp
								$subscription->register_failed_attemp();
								if ( isset( $ipn_args['retry_at'] ) ) {
									yit_save_prop( $order, 'next_payment_attempt', strtotime( $ipn_args['retry_at'] ), false, true );
								}

								if ( get_option( 'ywsbs_suspend_for_failed_recurring_payment' ) == 'yes' ) {
									$subscription->update_status( 'suspended', 'paypal' );
									YITH_WC_Activity()->add_activity( $subscription_id, 'suspended', 'success', $order_id, __( 'Subscription has been suspendend for failed payment', 'yith-woocommerce-subscription' ) );
								}

								$order->add_order_note( __( 'YSBS - IPN Failed payment', 'yith-woocommerce-subscription' ) );

								// Subscription Cancellation Completed
								WC_Gateway_Paypal::log( 'YSBS -IPN Failed payment' . $order_id );
							}
						}
					}
					break;
				case 'recurring_payment_skipped':
					if ( isset( $ipn_args['recurring_payment_id'] ) ) {

						$paypal_sub_id = $ipn_args['recurring_payment_id'];
						$order_sub_id  = yit_get_prop( $order, 'Subscriber ID' );

						if ( $paypal_sub_id != $order_sub_id ) {
							WC_Gateway_Paypal::log( 'YSBS - IPN subscription failed payment - new PayPal Profile ID linked to this subscription, for order ' . $order_id );
						} else {
							$subscriptions = yit_get_prop( $order, 'subscriptions' );

							if ( empty( $subscriptions ) ) {
								WC_Gateway_Paypal::log( 'YSBS - IPN subscription failed payment request ignored - order ' . $order_id . ' doesn\'t not subscriptions' );
							}

							foreach ( $subscriptions as $subscription_id ) {

								$subscription = ywsbs_get_subscription( $subscription_id );

								$transaction_ids = get_post_meta( $subscription_id, '_paypal_transaction_ids', true );
								$transactions    = $this->is_a_valid_transaction( $transaction_ids, $ipn_args );
								if ( $transactions ) {
									update_post_meta( $subscription_id, '_paypal_transaction_ids', $transactions );
								} else {
									break;
								}

								$last_order    = false;
								$pending_order = $subscription->renew_order;

								if ( intval( $pending_order ) ) {
									$last_order = wc_get_order( $pending_order );
								}

								if ( $subscription->status == 'pending' || ( ! $last_order && $order->has_status( $valid_order_statuses ) ) ) {
									continue;
								} elseif ( $last_order ) {
									$last_order->add_order_note( __( 'YSBS - IPN Failed payment', 'yith-woocommerce-subscription' ) );
								} else {
									// if the renew_order is not created try to create it
									$new_order_id = YWSBS_Subscription_Order()->renew_order( $subscription->id );
									if ( ! $new_order_id ) {
										YITH_WC_Activity()->add_activity( $subscription_id, 'renew-order', 'failed', $order_id, __( 'Renew order creation failed', 'yith-woocommerce-subscription' ) );
										return;
									}
									$new_order = wc_get_order( $new_order_id );
									$new_order->add_order_note( __( 'YSBS - IPN Failed payment', 'yith-woocommerce-subscription' ) );
								}

								// update the number of failed attemp
								$subscription->register_failed_attemp();
								if ( isset( $ipn_args['retry_at'] ) ) {
									yit_save_prop( $order, 'next_payment_attempt', strtotime( $ipn_args['retry_at'] ), false, true );
								}

								if ( get_option( 'ywsbs_suspend_for_failed_recurring_payment' ) == 'yes' ) {
									$subscription->update_status( 'suspended', 'paypal' );

									YITH_WC_Activity()->add_activity( $subscription_id, 'suspended', 'success', $order_id, __( 'Subscription has been suspendend for failed payment', 'yith-woocommerce-subscription' ) );

								}

								$order->add_order_note( __( 'YSBS - IPN Failed payment', 'yith-woocommerce-subscription' ) );

								// Subscription Cancellation Completed
								WC_Gateway_Paypal::log( 'YSBS -IPN Failed payment' . $order_id );

							}
						}
					}
					break;

				case 'subscr_eot':
					/*subscription expired*/
					break;

				case 'recurring_payment_suspended_due_to_max_failed_payment':
					if ( isset( $ipn_args['recurring_payment_id'] ) ) {
						$paypal_sub_id = $ipn_args['recurring_payment_id'];
						$order_sub_id  = yit_get_prop( $order, 'Subscriber ID' );

						if ( $paypal_sub_id != $order_sub_id ) {
							WC_Gateway_Paypal::log( 'YSBS - IPN subscription failed request ignored - new PayPal Profile ID linked to this subscription, for order ' . $order_id );
						} else {
							$subscriptions = yit_get_prop( $order, 'subscriptions' );

							if ( empty( $subscriptions ) ) {
								WC_Gateway_Paypal::log( 'YSBS - IPN subscription cancellation for failed request ignored - order ' . $order_id . ' doesn\'t not subscriptions' );
							}

							foreach ( $subscriptions as $subscription_id ) {

								$transaction_ids = get_post_meta( $subscription_id, '_paypal_transaction_ids', true );
								$transactions    = $this->is_a_valid_transaction( $transaction_ids, $ipn_args );
								if ( $transactions ) {
									update_post_meta( $subscription_id, '_paypal_transaction_ids', $transactions );
								} else {
									break;
								}

								$subscription = ywsbs_get_subscription( $subscription_id );

								// check if the subscription has max num of attemps
								$failed_attemp       = yit_get_prop( $order, 'failed_attemps' );
								$max_failed_attempts = ywsbs_get_max_failed_attempts_by_gateway( 'paypal' );

								if ( $failed_attemp >= $max_failed_attempts - 1 ) {
									$subscription->cancel( false );
									YITH_WC_Activity()->add_activity( $subscription->id, 'cancelled', 'success', $order_id, __( 'Subscription cancelled max failed attemps: recurring_payment_suspended_due_to_max_failed_payment', 'yith-woocommerce-subscription' ) );
									$order->add_order_note( __( 'YSBS - Subscription cancelled max failed attemps: recurring_payment_suspended_due_to_max_failed_payment', 'yith-woocommerce-subscription' ) );
									// Subscription Cancellation Completed
									WC_Gateway_Paypal::log( 'YSBS -Subscription cancelled max failed attempts: recurring_payment_suspended_due_to_max_failed_payment' . $order_id );
								} else {
									$subscription->update_status( 'suspended', 'paypal' );
									YITH_WC_Activity()->add_activity( $subscription_id, 'suspended', 'success', $order_id, __( 'Subscription has been suspendend because received PayPal IPN message: recurring_payment_suspended_due_to_max_failed_payment', 'yith-woocommerce-subscription' ) );

									$last_order    = false;
									$pending_order = $subscription->renew_order;

									if ( intval( $pending_order ) ) {
										$last_order = wc_get_order( $pending_order );
									}

									if ( $last_order ) {
										$last_order->add_order_note( __( 'YSBS - IPN message: recurring_payment_suspended_due_to_max_failed_payment', 'yith-woocommerce-subscription' ) );
									} else {
										$order->add_order_note( __( 'YSBS - IPN message: recurring_payment_suspended_due_to_max_failed_payment', 'yith-woocommerce-subscription' ) );
									}
								}
							}
						}
					}
					break;

				case 'subscr_cancel':
					/*subscription cancelled*/
					$paypal_sub_id = $ipn_args['subscr_id'];
					$order_sub_id  = yit_get_prop( $order, 'Subscriber ID' );

					if ( $paypal_sub_id != $order_sub_id ) {
						WC_Gateway_Paypal::log( 'YSBS - IPN subscription cancellation request ignored - new PayPal Profile ID linked to this subscription, for order ' . $order_id );
					} else {
						$subscriptions = yit_get_prop( $order, 'subscriptions' );

						if ( empty( $subscriptions ) ) {
							WC_Gateway_Paypal::log( 'YSBS - IPN subscription cancellation request ignored - order ' . $order_id . ' doesn\'t not subscriptions' );
						}

						foreach ( $subscriptions as $subscription_id ) {

							$transaction_ids = get_post_meta( $subscription_id, '_paypal_transaction_ids', true );
							$transactions    = $this->is_a_valid_transaction( $transaction_ids, $ipn_args );
							if ( $transactions ) {
								update_post_meta( $subscription_id, '_paypal_transaction_ids', $transactions );
							} else {
								break;
							}

							$subscription = new YWSBS_Subscription( $subscription_id );
							$subscription->cancel( false );
							YITH_WC_Activity()->add_activity( $subscription->id, 'cancelled', 'success', $order_id, __( 'Subscription cancelled by gateway', 'yith-woocommerce-subscription' ) );

							$order->add_order_note( __( 'YSBS - IPN subscription cancelled for the order.', 'yith-woocommerce-subscription' ) );

							// Subscription Cancellation Completed
							WC_Gateway_Paypal::log( 'YSBS -IPN subscription cancelled for order ' . $order_id );

						}
					}

					break;
				default:
			}

		}

		protected function is_a_valid_transaction( $transaction_ids, $ipn_args ) {

			$transaction_ids = empty( $transaction_ids ) ? array() : $transaction_ids;
			// check if the ipn request as been processed
			if ( isset( $ipn_args['txn_id'] ) ) {
				$transaction_id = $ipn_args['txn_id'] . '-' . $ipn_args['txn_type'];

				if ( isset( $ipn_args['payment_status'] ) ) {
					$transaction_id .= '-' . $ipn_args['payment_status'];
				}
				if ( empty( $transaction_ids ) || ! in_array( $transaction_id, $transaction_ids ) ) {
					$transaction_ids[] = $transaction_id;
				} else {
					if ( $this->debug ) {
						$this->wclog->add( 'paypal', 'YSBS - Subscription IPN Error: IPN ' . $transaction_id . ' message has already been correctly handled.' );
					}

					return false;
				}
			} elseif ( isset( $ipn_args['ipn_track_id'] ) ) {
				$track_id = $ipn_args['txn_type'] . '-' . $ipn_args['ipn_track_id'];
				if ( empty( $transaction_ids ) || ! in_array( $track_id, $transaction_ids ) ) {
					$transaction_ids[] = $track_id;
				} else {
					if ( $this->debug ) {
						$this->wclog->add( 'paypal', 'YSBS - Subscription IPN Error: IPN ' . $track_id . ' message has already been correctly handled.' );
					}
					return false;
				}
			}

			return $transaction_ids;

		}
		/**
		 * Check for a valid transaction type
		 *
		 * @param  string $txn_type
		 *
		 * @return bool|void
		 */
		protected function validate_transaction_type( $txn_type ) {

			if ( ! in_array( strtolower( $txn_type ), $this->transaction_types ) ) {
				// WC_Gateway_Paypal::log( 'Aborting, Invalid type:' . $txn_type );
				return false;
			}

			return true;
		}

		/**
		 * Check payment amount from IPN matches the order.
		 *
		 * @param WC_Order $order
		 * @param int      $amount
		 */
		protected function validate_amount( $order, $amount ) {
			if ( number_format( $order->get_total(), 2, '.', '' ) != number_format( $amount, 2, '.', '' ) ) {
				WC_Gateway_Paypal::log( 'Payment error: Amounts do not match (gross ' . $amount . ')' );

				// Put this order on-hold for manual checking.
				$order->update_status( 'on-hold', sprintf( __( 'Validation error: PayPal amounts do not match (gross %s).', 'yith-woocommerce-subscription' ), $amount ) );
				exit;
			}
		}


		/**
		 * Avoid the change of _transaction_id of a parent order
		 *
		 * @param $result
		 * @param $object_id
		 * @param $meta_key
		 * @param $meta_value
		 * @param $prev_value
		 *
		 * @return bool
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function check_metadata( $result, $object_id, $meta_key, $meta_value, $prev_value ) {
			if ( $meta_key == '_transaction_id' && get_post_meta( $object_id, $meta_key, true ) ) {
				$order = wc_get_order( $object_id );
				if ( $order ) {
					$is_a_renew       = $order->get_meta( 'is_a_renew' );
					$has_subscription = $order->get_meta( 'subscriptions' );
					if ( $order->has_status( 'completed' ) && 'yes' != $is_a_renew && is_array( $has_subscription ) ) {
						$result = true;
					}
				}
			}

			return $result;
		}

		/**
		 * Check if the refund is of a renew order.
		 *
		 * @param $order WC_Order
		 * @param $posted
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		private function check_subscription_child_refunds( $order, $posted ) {

			global $wpdb;

			$order_id = $order->get_id();

			$subscriptions = get_post_meta( $order_id, 'subscriptions', true );

			if ( ! $subscriptions ) {
				return;
			}

			$parent_txn_id = isset( $posted['parent_txn_id'] ) ? $posted['parent_txn_id'] : false;

			$child_order_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s", '_transaction_id', $parent_txn_id ) );

			if ( ! $child_order_id ) {
				return;
			}

			$child_order = wc_get_order( $child_order_id );

			if ( ! $child_order ) {
				return;
			}

			remove_all_actions( 'valid-paypal-standard-ipn-request', 10 );

			$this->payment_status_refunded( $child_order, $posted );
		}


		/**
		 * Handle a refunded order.
		 *
		 * @param WC_Order $order  Order object.
		 * @param array    $posted Posted data.
		 */
		protected function payment_status_refunded( $order, $posted ) {

			// Only handle full refunds, not partial.
			if ( floatval( $order->get_total() ) == $posted['mc_gross'] * -1 ) {
				/* translators: %s: payment status. */
				$order->update_status( 'refunded', sprintf( __( 'Payment %s via IPN.', 'yith-woocommerce-subscription' ), strtolower( $posted['payment_status'] ) ) );

				$this->send_ipn_email_notification(
				/* translators: %s: order link. */
					sprintf( __( 'Payment for order %s refunded', 'yith-woocommerce-subscription' ), '<a class="link" href="' . esc_url( $order->get_edit_order_url() ) . '">' . $order->get_order_number() . '</a>' ),
					/* translators: %1$s: order ID, %2$s: reason code. */
					sprintf( __( 'Order #%1$s has been marked as refunded - PayPal reason code: %2$s', 'yith-woocommerce-subscription' ), $order->get_order_number(), $posted['reason_code'] )
				);
			}
		}

	}

}
