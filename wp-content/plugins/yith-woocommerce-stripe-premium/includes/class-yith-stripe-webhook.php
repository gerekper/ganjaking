<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Stripe
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCSTRIPE' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCStripe_Webhook' ) ) {
	/**
	 * Manage webhooks of stripe
	 *
	 * @since 1.0.0
	 */
	class YITH_WCStripe_Webhook {

		/** @var object|\Stripe\Event */
		protected static $event = null;

		/** @var YITH_WCStripe_Gateway|YITH_WCStripe_Gateway_Advanced|YITH_WCStripe_Gateway_Addons */
		protected static $gateway = null;

		/** @var bool Avoid performing a webhook if already runned */
		protected static $running = false;

		/**
		 * Constructor.
		 *
		 * Route the webhook to the own method
		 *
		 * @return \YITH_WCStripe_Webhook
		 * @since 1.0.0
		 */
		public static function route() {
			if ( self::$running ) {
				return;
			}

			self::$running = true;

			$body        = @file_get_contents( 'php://input' );
			self::$event = json_decode( $body );

			// retrieve the callback to use fo this event
			$callback = isset( self::$event->type ) ? str_replace( '.', '_', self::$event->type ) : '';

			if ( ! $callback || ! method_exists( __CLASS__, $callback ) ) {
				self::_sendSuccess( __( 'No action to perform with this event (method invoked is: ' . $callback . '.', 'yith-woocommerce-stripe' ) );
			}

			if ( ! self::$gateway = YITH_WCStripe()->get_gateway() ) {
				self::_sendSuccess( __( 'No gateway.', 'yith-woocommerce-stripe' ) );
			}

			self::$gateway->init_stripe_sdk();

			try {
				// call the method event
				call_user_func( array( __CLASS__, $callback ) );
				self::_sendSuccess( __( 'Webhook performed without error.', 'yith-woocommerce-stripe' ) );
			} catch ( Stripe\Exception\ApiErrorException $e ) {
				self::$gateway->log( 'Charge updated: ' . $e->getMessage() );
				self::_sendError( var_export( $e->getJsonBody(), true ) . "\n\n" . $e->getTraceAsString() );
			} catch ( Exception $e ) {
				self::$gateway->log( 'Charge updated: ' . $e->getMessage() );
				self::_sendError( $e->getCode() . ': ' . $e->getMessage() . "\n\n" . $e->getTraceAsString() );
			}

		}

		/**
		 * Handle the captured charge
		 *
		 * @var $charge \Stripe\Charge
		 * @since 1.0.0
		 */
		protected static function charge_captured() {
			$charge  = self::$event->data->object;
			$gateway = self::$gateway;

			// check the domain
			if ( ! isset( $charge->metadata->instance ) || $charge->metadata->instance != $gateway->instance ) {
				self::_sendSuccess( 'Instance does not match -> ' . $charge->metadata->instance . ' : ' . $gateway->instance );
			}

			// get order
			if ( ! isset( $charge->metadata->order_id ) || empty( $charge->metadata->order_id ) ) {
				self::_sendSuccess( 'No order ID set' );
			}

			$order_id = $charge->metadata->order_id;
			$order    = wc_get_order( $charge->metadata->order_id );

			if ( false === $order ) {
				self::_sendSuccess( 'No order for this event' );
			}

			yit_save_prop( $order, '_captured', 'yes' );

			// check if refunds
			if ( $charge->refunds->total_count > 0 ) {
				$refunds         = $charge->refunds->data;
				$amount_captured = YITH_WCStripe::get_original_amount( $charge->amount - $charge->amount_refunded );

				/**
				 * @var $refund \Stripe\Refund
				 */
				foreach ( $refunds as $refund ) {
					$amount_refunded = YITH_WCStripe::get_original_amount( $refund->amount, $refund->currency );

					// add refund to order
					$order_refund = wc_create_refund(
						array(
							'amount'   => $amount_refunded,
							'reason'   => sprintf( __( 'Captured only %s via Stripe.', 'yith-woocommerce-stripe' ), strip_tags( wc_price( $amount_captured ) ) ) . ( ! empty( $refund->reason ) ? '<br />' . $refund->reason : '' ),
							'order_id' => $order_id,
						)
					);

					// set metadata
					yit_save_prop( $order_refund, '_refund_stripe_id', $refund->id );
				}
			}

			// complete order
			$order->update_status( 'completed', __( 'Charge captured via Stripe account.', 'yith-woocommerce-stripe' ) . '<br />' );
		}

		/**
		 * Handle the refunded charge
		 *
		 * @since 1.0.0
		 */
		protected static function charge_refunded() {
			$charge  = self::$event->data->object;
			$gateway = self::$gateway;

			// check the domain
			if ( ! isset( $charge->metadata->instance ) || $charge->metadata->instance != $gateway->instance ) {
				self::_sendSuccess( 'Instance does not match -> ' . $charge->metadata->instance . ' : ' . $gateway->instance );
			}

			//get order
			if ( ! isset( $charge->metadata->order_id ) || empty( $charge->metadata->order_id ) ) {
				self::_sendSuccess( 'No order ID set' );
			}

			$order    = wc_get_order( $charge->metadata->order_id );
			$order_id = $charge->metadata->order_id;

			if ( false === $order ) {
				self::_sendSuccess( 'No order for this event' );
			}

			// If already captured, set as refund
			if ( $charge->captured ) {
				yit_save_prop( $order, '_captured', 'yes' );

				// check if refunds
				if ( $charge->refunds->total_count > 0 ) {
					$refunds = $charge->refunds->data;

					/**
					 * @var $refund \Stripe\Refund
					 */
					foreach ( $refunds as $refund ) {
						$amount_refunded = YITH_WCStripe::get_original_amount( $refund->amount, $refund->currency );

						// check if already exists
						foreach ( $order->get_refunds() as $the ) {

							/**
							 * Retrieve Stripe refund ids for current refund object.
							 * Since one single refund on WC may cause many different refunds on Stripe, this meta is now
							 * considered to be an array; if it is not, it is casted to array for backward compatibility
							 */
							$refund_stripe_ids = yit_get_prop( $the, '_refund_stripe_id' );
							$refund_stripe_ids = ! is_array( $refund_stripe_ids ) ? (array) $refund_stripe_ids : $refund_stripe_ids;

							if ( in_array( $refund->id, $refund_stripe_ids ) ) {
								continue 2;
							}
						}

						// add refund to order
						$order_refund = wc_create_refund(
							array(
								'amount'   => $amount_refunded,
								'reason'   => __( 'Refunded via Stripe.', 'yith-woocommerce-stripe' ) . ( ! empty( $refund->reason ) ? '<br />' . $refund->reason : '' ),
								'order_id' => $order_id,
							)
						);

						$order->add_order_note( sprintf( __( 'Refunded %1$s - Refund ID: %2$s', 'woocommerce' ), $amount_refunded, $refund->id ) );

						// set metadata
						yit_save_prop( $order_refund, '_refund_stripe_id', $refund->id );
					}

					// refund order if is fully refunded
					if ( $charge->amount == $charge->amount_refunded ) {
						$order->update_status( 'refunded' );
					}
				}
			} // if isn't captured yet, set as cancelled
			else {
				yit_save_prop( $order, '_captured', 'no' );

				// set cancelled
				$order->update_status( 'cancelled', __( 'Authorization released via Stripe.', 'yith-woocommerce-stripe' ) . '<br />' );
			}
		}

		/**
		 * Handle dispute created event
		 *
		 * @since 1.6.0
		 */
		protected static function charge_dispute_created() {
			global $wpdb;

			$dispute = self::$event->data->object;
			$gateway = self::$gateway;

			$charge_id = $dispute->charge;

			if ( empty( $charge_id ) ) {
				self::_sendSuccess( 'No charge ID in the request' );
			}

			try {
				$charge = $gateway->api->get_charge( $charge_id );
			} catch ( Exception $e ) {
				self::_sendSuccess( 'Could not retrieve charge ' . $charge_id );
			}

			// check the domain
			if ( ! isset( $charge->metadata->instance ) || $charge->metadata->instance != $gateway->instance ) {
				self::_sendSuccess( 'Instance does not match -> ' . $charge->metadata->instance . ' : ' . $gateway->instance );
			}

			$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s", '_transaction_id', $charge_id ) );

			if ( ! $order_id ) {
				self::_sendSuccess( 'No order ID found for the charge ID' );
			}

			$order = wc_get_order( $order_id );

			if ( ! $order ) {
				self::_sendSuccess( 'No order ID for this event' );
			}

			$order->update_status( 'on-hold', __( 'Payment reversed via Stripe', 'yith-woocommerce-stripe' ) );
		}

		/**
		 * Handle dispute closed event
		 *
		 * @since 1.6.0
		 */
		protected static function charge_dispute_closed() {
			global $wpdb;

			$dispute = self::$event->data->object;
			$gateway = self::$gateway;

			$charge_id = $dispute->charge;
			$status    = $dispute->status;

			try {
				$charge = $gateway->api->get_charge( $charge_id );
			} catch ( Exception $e ) {
				self::_sendSuccess( 'Could not retrieve charge ' . $charge_id );
			}

			// check the domain
			if ( ! isset( $charge->metadata->instance ) || $charge->metadata->instance != $gateway->instance ) {
				self::_sendSuccess( 'Instance does not match -> ' . $charge->metadata->instance . ' : ' . $gateway->instance );
			}

			if ( ! in_array( $status, array( 'won', 'lost' ) ) ) {
				self::_sendSuccess( 'No processable dispute status in the request' );;
			}

			if ( empty( $charge_id ) ) {
				self::_sendSuccess( 'No charge ID in the request' );;
			}

			$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s", '_transaction_id', $charge_id ) );

			if ( ! $order_id ) {
				self::_sendSuccess( 'No order ID found for the charge ID' );
			}

			$order = wc_get_order( $order_id );

			if ( ! $order ) {
				self::_sendSuccess( 'No order ID for this event' );
			}

			if ( $status == 'won' ) {
				$order->update_status( 'completed', __( 'Payment completed after winning dispute', 'yith-woocommerce-stripe' ) );
			} elseif ( $status == 'lost' ) {
				$order->update_status( 'refunded', __( 'Payment refunded after losing dispute', 'yith-woocommerce-stripe' ) );
			}
		}

		/**
		 * Handle the change of customer data
		 *
		 * @since 1.0.0
		 */
		protected static function customer_updated() {
			$customer = self::$event->data->object;

			self::_updateCustomer( $customer );
		}

		/**
		 * Handle the change of customer data
		 *
		 * @since 1.0.0
		 */
		protected static function customer_source_created() {
			$card = self::$event->data->object;

			self::_updateCustomer( $card->customer );
		}

		/**
		 * Handle the change of customer data
		 *
		 * @since 1.0.0
		 */
		protected static function customer_source_updated() {
			$card = self::$event->data->object;

			self::_updateCustomer( $card->customer );
		}

		/**
		 * Handle the change of customer data
		 *
		 * @since 1.0.0
		 */
		protected static function customer_source_deleted() {
			$card = self::$event->data->object;

			self::_updateCustomer( $card->customer );
		}

		/**
		 * Subscription recurring payed success
		 */
		protected static function invoice_payment_succeeded( $invoice = false ) {
			/** @var \Stripe\Invoice $invoice */
			$manual  = (bool) $invoice;
			$invoice = $invoice ? $invoice : self::$event->data->object;
			$gateway = self::$gateway;

			if ( ! $gateway instanceof YITH_WCStripe_Gateway_Addons ) {
				self::_sendSuccess( 'Subscriptions disabled' );
			}

			// get subscription line from invoice
			foreach ( $invoice->lines->data as $line ) {
				if ( 'subscription' == $line->type ) {
					$stripe_subscription_line_obj = $line;
					break;
				}
			}

			if ( empty( $stripe_subscription_line_obj ) ) {
				self::_sendSuccess( 'No subscriptions for this event.' );
			}

			// amount_due == 0 to avoid duplication on
			if ( ! $manual && ( $invoice->amount_due == 0 || ! property_exists( $invoice, 'subscription' ) || ! property_exists( $invoice, 'paid' ) || $invoice->paid !== true || ! property_exists( $invoice, 'charge' ) ) ) {
				self::_sendSuccess( 'Duplication' );
			}

			$stripe_subscription_id = $invoice->subscription;
			$subscription_id        = $gateway->get_subscription_id( $stripe_subscription_id );

			if ( empty( $subscription_id ) ) {
				self::_sendSuccess( 'No subscription ID on website' );
			}

			$subscription       = ywsbs_get_subscription( $subscription_id );
			$invoices_processed = isset( $subscription->stripe_invoices_processed ) ? $subscription->stripe_invoices_processed : array();

			if ( in_array( $invoice->id, $invoices_processed ) ) {
				self::_sendSuccess( 'Invoice already processed.' );
			}

			$order       = wc_get_order( $subscription->order_id );
			$customer_id = $invoice->customer;
			$user        = $order->get_user();

			if ( $subscription->status == 'cancelled' ) {
				$msg = 'YSBS - Webhook stripe subscription payment error #' . $subscription_id . ' is cancelled';
				$gateway->log( $msg );
				self::_sendSuccess( $msg );
			}

			$pending_order = $subscription->renew_order;
			$last_order    = $pending_order ? wc_get_order( intval( $pending_order ) ) : false;

			if ( $last_order ) {
				$order_id      = yit_get_order_id( $last_order );
				$order         = wc_get_order( $order_id );
				$order_to_save = $last_order;

			} else {

				//if the renew_order is not created try to create it
				$new_order_id  = YWSBS_Subscription_Order()->renew_order( $subscription->id );
				$order_to_save = wc_get_order( $new_order_id );
				$order_id      = $new_order_id;
				$order         = wc_get_order( $order_id );

				yit_save_prop( $order, 'software_processed', 1 );
				$subscription->set( 'renew_order', $order_id );
			}

			$metadata = array(
				'metadata'    => array(
					'order_id' => $order_id,
					'instance' => $gateway->instance
				),
				'description' => apply_filters( 'yith_wcstripe_charge_description', sprintf( __( '%s - Order %s', 'yith-woocommerce-stripe' ), esc_html( get_bloginfo( 'name' ) ), $order_id ), esc_html( get_bloginfo( 'name' ) ), $order_to_save->get_order_number() ),
			);
			$charge   = $gateway->api->update_charge( $invoice->charge, $metadata );

			if ( isset( $charge->payment_intent ) ) {
				$gateway->api->update_intent( $charge->payment_intent, $metadata );
			}

			// check if it will be expired on next renew
			if ( $subscription->expired_date != '' && ( $invoice->period_end >= $subscription->expired_date || $invoice->period_end + DAY_IN_SECONDS > $subscription->expired_date ) ) {
				$gateway->api->cancel_subscription( $customer_id, $stripe_subscription_id );
			}

			yit_save_prop( $order, array_merge( array(
				'Subscriber ID'           => $customer_id,
				'Subscriber payment type' => $gateway->id,
				'Stripe Subscribtion ID'  => $stripe_subscription_id,
				'_captured'               => 'yes',
				'next_payment_attempt'    => $invoice->next_payment_attempt,
			),
				$user ? array(
					'Subscriber address'    => $user->billing_email,
					'Subscriber first name' => $user->first_name,
					'Subscriber last name'  => $user->last_name,
				) : array()
			) );

			// filter to increase performance during "payment_complete" action
			add_filter( 'woocommerce_delete_version_transients_limit', 'yith_wcstripe_return_10' );

			$invoices_processed[] = $invoice->id;
			$subscription->set( 'stripe_invoices_processed', $invoices_processed );
			$subscription->set( 'stripe_charge_id', $invoice->charge );
			$subscription->set( 'payment_method', $gateway->id );
			$subscription->set( 'payment_method_title', $gateway->get_title() );

			// remove the invoice from failed invoices list, if it exists
			$failed_invoices = get_user_meta( $order->get_user_id(), 'failed_invoices', true );
			if ( isset( $failed_invoices[ $subscription->id ] ) ) {
				unset( $failed_invoices[ $subscription->id ] );
				update_user_meta( $order->get_user_id(), 'failed_invoices', $failed_invoices );
			}

			// add a user meta to show him a success notice for renew
			add_user_meta( $order->get_user_id(), 'invoice_charged', true );

			$order_to_save->add_order_note( __( 'Stripe subscription payment completed.', 'yith-woocommerce-stripe' ) );
			$order_to_save->set_payment_method( $gateway );
			$order_to_save->payment_complete( $invoice->charge );

			// must be after payment_complete, because subscription plugin add the period to payment_due_date, on payment_complete
			$subscription->set( 'payment_due_date', $stripe_subscription_line_obj->period->end );

			method_exists( $subscription, 'save' ) && $subscription->save();
		}

		/**
		 * Subscription recurring payed failed
		 */
		protected static function invoice_payment_failed() {
			/** @var \Stripe\Invoice $invoice */
			$invoice = self::$event->data->object;
			$gateway = self::$gateway;

			if ( ! $gateway instanceof YITH_WCStripe_Gateway_Addons ) {
				self::_sendSuccess( 'Subscriptions disabled' );
			}

			$stripe_subscription_id = $invoice->subscription;
			$subscription_id        = $gateway->get_subscription_id( $stripe_subscription_id );

			if ( empty( $subscription_id ) ) {
				self::_sendSuccess( 'No subscription ID on website' );
			}

			$subscription = ywsbs_get_subscription( $subscription_id );

			if ( empty( $subscription ) ) {
				self::_sendSuccess( 'No subscription on website' );
			}

			$order_id       = $subscription->order_id;
			$order          = wc_get_order( $order_id );
			$renew_order_id = $subscription->renew_order;
			$renew_order    = wc_get_order( $renew_order_id );
			$customer_id    = $order->get_customer_id();
			$retry_renew    = 'yes' == $gateway->get_option( 'retry_with_other_cards' );

			// if we cannot find any renew order, subscription was already processed
			if ( ! $renew_order ) {
				self::_sendSuccess( 'No renew order; subscription was already processed' );
			}

			// check if we're currently processing other cards (in this case should ignore failure webhooks)
			$cards_to_test = get_post_meta( $renew_order_id, '_cards_to_test', true );

			// before registering fail, try to pay with other registered cards
			if ( ! is_array( $cards_to_test ) && $customer_id && $retry_renew ) {
				$cards_to_test = array();

				$customer_tokens = WC_Payment_Tokens::get_customer_tokens( $customer_id, YITH_WCStripe::$gateway_id );

				$current_year  = date( 'Y' );
				$current_month = date( 'm' );

				if ( count( $customer_tokens ) > 1 ) {
					foreach ( $customer_tokens as $customer_token ) {
						/**
						 * @var $customer_token \WC_Payment_Token_CC
						 */
						$card_id   = $customer_token->get_token();
						$exp_year  = $customer_token->get_expiry_year();
						$exp_month = $customer_token->get_expiry_month();

						if ( ! $card_id ) {
							continue;
						}

						if ( $exp_year < $current_year || ( $exp_year == $current_year && $exp_month < $current_month ) ) {
							continue;
						}

						$cards_to_test[] = $card_id;
					}
				}

				if ( ! empty( $cards_to_test ) ) {
					update_post_meta( $renew_order_id, '_cards_to_test', $cards_to_test );
				}
			}

			// backward compatibility before YITH Subscription  1.1.3
			if ( method_exists( $subscription, 'register_failed_attemp' ) ) {
				$subscription->register_failed_attempt( $invoice->attempt_count );
			} else {
				yit_save_prop( $order, 'failed_attemps', $invoice->attempt_count );
				YITH_WC_Activity()->add_activity( $subscription_id, 'failed-payment', 'success', $order_id, sprintf( __( 'Failed payment for order %d', 'yith-woocommerce-stripe' ), $order_id ) );
			}

			// suspend subscription
			if ( get_option( 'ywsbs_suspend_for_failed_recurring_payment' ) == 'yes' ) {
				$subscription->update_status( 'suspended', 'yith-stripe' );
			}

			// register next attempt date
			yit_save_prop( $order, 'next_payment_attempt', $invoice->next_payment_attempt );

			if ( $renew_order ) {
				$renew_order->add_order_note( __( 'YSBS - Webhook Failed payment', 'yith-woocommerce-stripe' ) );
			} else {
				$order->add_order_note( __( 'YSBS - Webhook Failed payment', 'yith-woocommerce-stripe' ) );
			}

			// save a user meta to notify the failure on my account page
			$failed_invoices = get_user_meta( $order->get_user_id(), 'failed_invoices', true );
			$failed_invoices = is_array( $failed_invoices ) ? $failed_invoices : array();

			if ( ! isset( $failed_invoices[ $subscription->id ] ) ) {
				$failed_invoices[ $subscription->id ] = $invoice->id;
				update_user_meta( $order->get_user_id(), 'failed_invoices', $failed_invoices );
			}

			// Subscription Cancellation Completed
			$gateway->log( 'YSBS - Webhook stripe subscription failed payment ' . $order_id );

			// retry payment using other customer cards; failure or success will be handled by subsequent webhooks sent from Stripe.
			if ( is_array( $cards_to_test ) ) {
				if ( empty( $cards_to_test ) ) {
					delete_post_meta( $renew_order_id, '_cards_to_test' );
				} else {
					$card_id = array_shift( $cards_to_test );

					try {
						$invoice = $gateway->api->get_invoice( $invoice->id );
					} catch ( Exception $e ) {
						self::_sendSuccess( 'Invoice failed.' );
					}

					// remove card from queue.
					update_post_meta( $renew_order_id, '_cards_to_test', $cards_to_test );

					try {
						$result = $invoice->pay(
							array(
								'off_session' => true,
								'source'      => $card_id,
							)
						);

						if ( $result->amount_due == $result->amount_paid ) {
							delete_post_meta( $renew_order_id, '_cards_to_test' );
							self::_sendSuccess( "Invoice failed. New payment attempted with card {$card_id}: success!" );
						}
					} catch ( Exception $e ) {
						self::_sendSuccess( "Invoice failed. New payment attempted with card {$card_id}: fail!" );
					}
				}
			} else {
				self::_sendSuccess( 'Invoice failed.' );
			}
		}

		/**
		 * Subscription deleted
		 */
		protected static function customer_subscription_deleted() {
			$stripe_subscription = self::$event->data->object;
			$gateway             = self::$gateway;

			if ( ! $gateway instanceof YITH_WCStripe_Gateway_Addons ) {
				self::_sendSuccess( 'Subscriptions disabled' );
			}

			// remove subscription on wordpress site
			$subscription_id = $gateway->get_subscription_id( $stripe_subscription->id );

			if ( empty( $subscription_id ) ) {
				return;
			}

			$subscription = ywsbs_get_subscription( $subscription_id );
			if ( 'cancelled' != $subscription->status ) {
				$subscription->cancel();
			}

			// remove the invoice from failed invoices list, if it exists
			$order           = wc_get_order( $subscription->order_id );
			$failed_invoices = get_user_meta( $order->get_user_id(), 'failed_invoices', true );
			if ( isset( $failed_invoices[ $subscription->id ] ) ) {
				unset( $failed_invoices[ $subscription->id ] );
				update_user_meta( $order->get_user_id(), 'failed_invoices', $failed_invoices );
			}
		}

		/**
		 * Util method for customer update.
		 *
		 * Get profile data from stripe and update in the database
		 *
		 * @param $customer mixed The ID of customer or customer object
		 *
		 * @since 1.0.0
		 */
		protected static function _updateCustomer( $customer ) {
			$gateway = self::$gateway;

			// retrieve customer from stripe profile
			$gateway->init_stripe_sdk();

			if ( is_string( $customer ) ) {
				$customer = $gateway->api->get_customer( $customer );
			}

			// exit if there is an user_id linked
			if ( ! isset( $customer->metadata->instance ) || $customer->metadata->instance != $gateway->instance || ! isset( $customer->metadata->user_id ) || empty( $customer->metadata->user_id ) ) {
				self::_sendSuccess( 'Instance does not match -> ' . $customer->metadata->instance . ' : ' . $gateway->instance );
			}

			// update tokens
			if ( method_exists( $gateway, 'sync_tokens' ) ) {
				$gateway->sync_tokens( $customer->metadata->user_id, $customer );
			}

			// back-compatibility
			YITH_WCStripe()->get_customer()->update_usermeta_info( $customer->metadata->user_id, array(
				'id'             => $customer->id,
				'cards'          => $customer->sources->data,
				'default_source' => $customer->default_source
			) );
		}

		/**
		 * Return success
		 *
		 * @param string $msg
		 *
		 * @since 1.2.6
		 */
		protected static function _sendSuccess( $msg = '' ) {
			status_header( 200 );
			header( 'Content-Type: text/plain' );

			if ( ! empty( $msg ) ) {
				echo $msg;
			}

			self::$running = false;

			exit( 0 );
		}

		/**
		 * Return error
		 *
		 * @param string|Exception $msg
		 *
		 * @since 1.2.6
		 */
		protected static function _sendError( $msg = '' ) {
			header( 'Content-Type: plain/text' );
			status_header( 500 );

			if ( ! empty( $msg ) ) {
				echo $msg;
			}

			self::$running = false;

			exit( 0 );
		}
	}
}