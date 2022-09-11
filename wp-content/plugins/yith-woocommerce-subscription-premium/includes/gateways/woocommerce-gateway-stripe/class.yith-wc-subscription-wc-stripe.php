<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YITH_WC_Subscription_WC_Stripe integration with WooCommerce Stripe Plugin
 *
 * @class   YITH_WC_Subscription_WC_Stripe
 * @since   1.0.0
 * @author  YITH
 * @package YITH WooCommerce Subscription
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Compatibility class for WooCommerce Gateway Stripe.
 *
 * @extends WC_Gateway_Stripe
 */
class YITH_WC_Subscription_WC_Stripe extends WC_Gateway_Stripe {


	/**
	 * Instance of YITH_WC_Subscription_WC_Stripe
	 *
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * Stripe gateway id
	 *
	 * @since 1.0
	 * @var   string ID of specific gateway
	 */
	public static $gateway_id = 'stripe';

	/**
	 * Return the instance of Gateway
	 *
	 * @return YITH_Subscription_WC_Stripe
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		$this->supports = array(
			'products',
			'refunds',
			'tokenization',
			'yith_subscriptions',
			'yith_subscriptions_scheduling',
			'yith_subscriptions_pause',
			'yith_subscriptions_multiple',
			'yith_subscriptions_payment_date',
			'yith_subscriptions_recurring_amount',
		);

		add_action( 'ywsbs_subscription_payment_complete', array( $this, 'add_payment_meta_data_to_subscription' ), 10, 2 );

		// Pay the renew orders.
		add_action( 'ywsbs_pay_renew_order_with_' . $this->id, array( $this, 'pay_renew_order' ), 10, 2 );
	}

	/**
	 * Process the payment based on type.
	 *
	 * @param int   $order_id          Reference.
	 * @param bool  $retry             Should we retry on fail.
	 * @param bool  $force_save_source Force save the payment source.
	 * @param mixed $previous_error    Any error message from previous request.
	 * @param bool  $use_order_source  Whether to use the source, which should already be attached to the order.
	 *
	 * @return array
	 * @throws Exception Trigger an error.
	 */
	public function process_payment( $order_id, $retry = true, $force_save_source = false, $previous_error = false, $use_order_source = false ) {
		$sbs = YWSBS_Subscription_Order()->get_subscription_items_inside_the_order( $order_id );

		if ( YWSBS_Subscription_Cart::cart_has_subscriptions() || ! empty( $sbs ) ) {
			return parent::process_payment( $order_id, $retry, true, $previous_error );
		}

		return parent::process_payment( $order_id, $retry, $force_save_source, $previous_error );
	}

	/**
	 * Register the payment information on subscription meta.
	 *
	 * @param YWSBS_Subscription $subscription Subscription.
	 * @param WC_Order           $order        Order.
	 */
	public function add_payment_meta_data_to_subscription( $subscription, $order ) {

		if ( ! $subscription || ! $order || $subscription->get_order_id() !== $order->get_id() ) {
			return;
		}

		if ( $this->id === $order->get_payment_method() ) {
			$subscription->set( '_stripe_customer_id', $order->get_meta( '_stripe_customer_id' ) );
			$subscription->set( '_stripe_source_id', $order->get_meta( '_stripe_source_id' ) );
		}

	}

	/**
	 * Pay the renew order.
	 *
	 * It is triggered by ywsbs_pay_renew_order_with_{gateway_id} action.
	 *
	 * @param WC_Order $renewal_order Order to renew.
	 * @param bool     $manually      Check if this is a manual renew.
	 *
	 * @return array|bool|WP_Error
	 * @throws WC_Stripe_Exception Trigger an error.
	 * @since  1.1.0
	 */
	public function pay_renew_order( $renewal_order = null, $manually = false ) {

		if ( is_null( $renewal_order ) ) {
			return false;
		}

		$is_a_renew      = $renewal_order->get_meta( 'is_a_renew' );
		$subscriptions   = $renewal_order->get_meta( 'subscriptions' );
		$subscription_id = $subscriptions ? $subscriptions[0] : false;
		$order_id        = $renewal_order->get_id();

		if ( ! $subscription_id ) {
			// translators: placeholder order id.
			WC_Stripe_Logger::log( sprintf( __( 'Sorry, any subscription is found for this order: %s', 'yith-woocommerce-subscription' ), $order_id ) );
			// translators: placeholder order id.
			yith_subscription_log( sprintf( __( 'Sorry, any subscription is found for this order: %s', 'yith-woocommerce-subscription' ), $order_id ), 'subscription_payment' );

			return false;
		}

		foreach ( $subscriptions as $subscription_id ) {

			$subscription   = ywsbs_get_subscription( $subscription_id );
			$has_source     = $subscription->get( '_stripe_source_id' );
			$has_customer   = $subscription->get( '_stripe_customer_id' );
			$previous_error = false;

			if ( 'yes' !== $is_a_renew || empty( $has_source ) || empty( $has_customer ) ) {
				yith_subscription_log( 'Cannot pay order for the subscription ' . $subscription->id . ' stripe_customer_id=' . $has_customer . ' stripe_source_id=' . $has_source, 'subscription_payment' );
				ywsbs_register_failed_payment( $renewal_order, __( 'Error: Stripe customer and source info are missing.', 'yith-woocommerce-subscription' ) );

				return false;
			}

			$amount = $renewal_order->get_total();
			if ( $amount <= 0 ) {
				$renewal_order->payment_complete();

				return true;
			}

			try {

				if ( $amount * 100 < WC_Stripe_Helper::get_minimum_amount() ) {
					/* translators: minimum amount */
					$message = sprintf( __( 'Sorry, the minimum allowed order total is %1$s to use this payment method.', 'woocommerce-gateway-stripe' ), wc_price( WC_Stripe_Helper::get_minimum_amount() / 100 ) );
					ywsbs_register_failed_payment( $renewal_order, $message );

					return new WP_Error( 'stripe_error', $message );
				}

				$order_id = $renewal_order->get_id();

				// Get source from order.
				$prepared_source = $this->prepare_order_source( $renewal_order );

				if ( ! $prepared_source ) {
					throw new WC_Stripe_Exception( WC_Stripe_Helper::get_localized_messages()['missing'] );
				}

				$source_object = $prepared_source->source_object;

				if ( ! $prepared_source->customer ) {
					throw new WC_Stripe_Exception(
						'Failed to process renewal for order ' . $renewal_order->get_id() . '. Stripe customer id is missing in the order',
						__( 'Customer not found', 'woocommerce-gateway-stripe' )
					);
				}

				WC_Stripe_Logger::log( "Info: Begin processing subscription payment for order {$order_id} for the amount of {$amount}" );

				/*
				 * If we're doing a retry and source is chargeable, we need to pass
				 * a different idempotency key and retry for success.
				 */
				if ( is_object( $source_object ) && empty( $source_object->error ) && $this->need_update_idempotency_key( $source_object, $previous_error ) ) {
					add_filter( 'wc_stripe_idempotency_key', array( $this, 'change_idempotency_key' ), 10, 2 );
				}

				if ( ( $this->is_no_such_source_error( $previous_error ) || $this->is_no_linked_source_error( $previous_error ) ) && apply_filters( 'wc_stripe_use_default_customer_source', true ) ) {
					// Passing empty source will charge customer default.
					$prepared_source->source = '';
				}

				if ( $this->lock_order_payment( $renewal_order ) ) {
					return false;
				}

				$response                   = $this->create_and_confirm_intent_for_off_session( $renewal_order, $prepared_source, $amount );
				$is_authentication_required = $this->is_authentication_required_for_payment( $response );

				if ( ! empty( $response->error ) && ! $is_authentication_required ) {
					$localized_message = __( 'Sorry, we are unable to process your payment at this time. Please retry later.', 'woocommerce-gateway-stripe' );
					$renewal_order->add_order_note( $localized_message );
					throw new WC_Stripe_Exception( print_r( $response, true ), $localized_message );
				}

				if ( $is_authentication_required ) {
					do_action( 'wc_gateway_stripe_process_payment_authentication_required', $renewal_order, $response );

					$error_message = __( 'This transaction requires authentication.', 'woocommerce-gateway-stripe' );
					$renewal_order->add_order_note( $error_message );

					$charge = end( $response->error->payment_intent->charges->data );
					$id     = $charge->id;
					$renewal_order->set_transaction_id( $id );
					/* translators: %s is the charge Id */
					$renewal_order->update_status( 'failed', sprintf( __( 'Stripe charge awaiting authentication by user: %s.', 'woocommerce-gateway-stripe' ), $id ) );
					$renewal_order->save();
				} else {
					do_action( 'wc_gateway_stripe_process_payment', $response, $renewal_order );

					// Use the last charge within the intent or the full response body in case of SEPA.
					$this->process_response( isset( $response->charges ) ? end( $response->charges->data ) : $response, $renewal_order );
				}
			} catch ( WC_Stripe_Exception $e ) {
				WC_Stripe_Logger::log( 'Error: ' . $e->getMessage() );
				ywsbs_register_failed_payment( $renewal_order, 'Error: ' . $e->getMessage() );
				do_action( 'wc_gateway_stripe_process_payment_error', $e, $renewal_order );
			}
		}

	}

	/**
	 * Get payment source from an order.
	 *
	 * Not using 2.6 tokens for this part since we need a customer AND a card
	 * token, and not just one.
	 *
	 * @param WC_Order $order Order.
	 *
	 * @return  boolean|object
	 * @since   3.1.0
	 * @version 4.0.0
	 */
	public function prepare_order_source( $order = null ) {
		$stripe_customer = new WC_Stripe_Customer();
		$stripe_source   = false;
		$token_id        = false;
		$source_object   = false;

		if ( $order ) {
			$order_id      = $order->get_id();
			$subscriptions = $order->get_meta( 'subscriptions' );

			if ( empty( $subscriptions ) ) {
				return false;
			}

			foreach ( $subscriptions as $subscription_id ) {
				$subscription       = ywsbs_get_subscription( $subscription_id );
				$stripe_customer_id = $subscription->get( '_stripe_customer_id' );

				if ( $stripe_customer_id ) {
					$stripe_customer->set_id( $stripe_customer_id );
				}

				$source_id = $subscription->get( '_stripe_source_id' );
				if ( $source_id ) {
					$stripe_source = $source_id;
					$source_object = WC_Stripe_API::retrieve( 'sources/' . $source_id );

					if ( ( empty( $source_object ) || ( ! empty( $source_object ) && isset( $source_object->status ) && 'consumed' === $source_object->status ) ) && apply_filters( 'ywsbs_wc_stripe_get_alternative_sources', true ) ) {
						/**
						 * If the source status is "Consumed" this means that the customer has removed it from its account.
						 * So we search for the default source ID.
						 * If this ID is empty, this means that the customer has no credit card saved on the account so the payment will fail.
						 */
						$customer       = WC_Stripe_API::retrieve( "customers/$stripe_customer_id" );
						$default_source = $customer->default_source;
						if ( $default_source ) {
							$stripe_source = $default_source;
							$source_object = WC_Stripe_API::retrieve( 'sources/' . $default_source );
						} else {
							return false;
						}
					}
				} elseif ( apply_filters( 'wc_stripe_use_default_customer_source', true ) ) {
					/*
					* We can attempt to charge the customer's default source
					* by sending empty source id.
					*/
					$stripe_source = '';
				}
			}

			return (object) array(
				'token_id'      => $token_id,
				'customer'      => $stripe_customer ? $stripe_customer->get_id() : false,
				'source'        => $stripe_source,
				'source_object' => $source_object,
			);
		}

		return false;

	}

	/**
	 * Get the source id.
	 *
	 * @param YWSBS_Subscription $subscription Subscription.
	 *
	 * @return string;
	 */
	protected function get_source_id( $subscription ) {
		$source_id = $subscription->get( '_stripe_source_id' );

		$wc_token = WC_Payment_Tokens::get( $source_id );
		$user_id  = $subscription->get_user_id();

		if ( empty( $wc_token ) ) {
			$default = WC_Payment_Tokens::get_customer_default_token( $user_id );

			if ( $default && self::$gateway_id === $default->gateway_id ) {
				$source_id = $default->get_token();
			} else {
				$tokens = WC_Payment_Tokens::get_customer_tokens( $user_id, self::$gateway_id );
				if ( $tokens ) {
					foreach ( $tokens as $token ) {
						$source_id = $token->get_token();
						break;
					}
				}
			}
			$source_id = $subscription->get( '_stripe_source_id' );
			if ( $source_id ) {
				$source_id = '';
			} else {
				$subscription->set( '_stripe_source_id', $source_id );
			}
		}

		return $source_id;
	}
}
