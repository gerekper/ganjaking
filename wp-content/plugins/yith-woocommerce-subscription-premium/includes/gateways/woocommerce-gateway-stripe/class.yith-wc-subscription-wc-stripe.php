<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
	protected static $_instance = null;

	/**
	 * Stripe gateway id
	 *
	 * @var string ID of specific gateway
	 * @since 1.0
	 */
	public static $gateway_id = 'stripe';

	/**
	 * Return the instance of Gateway
	 *
	 * @return null|YITH_Subscription_WC_Stripe
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		$this->supports = array(
			'products',
			'tokenization',
			'yith_subscriptions',
			'yith_subscriptions_scheduling',
			'yith_subscriptions_pause',
			'yith_subscriptions_multiple',
			'yith_subscriptions_payment_date',
			'yith_subscriptions_recurring_amount',
		);

		add_action( 'ywsbs_subscription_payment_complete', array( $this, 'add_payment_meta_data_to_subscription' ), 10, 2 );
		// Pay the renew orders
		add_action( 'ywsbs_pay_renew_order_with_' . $this->id, array( $this, 'pay_renew_order' ), 10, 2 );
	}

	/**
	 * Process the payment based on type.
	 *
	 * @param  int $order_id
	 *
	 * @return array
	 * @throws Exception
	 */
	public function process_payment( $order_id, $retry = true, $force_save_source = false, $previous_error = false, $use_order_source = false ) {
		if ( YITH_WC_Subscription()->cart_has_subscriptions() ) {
			return parent::process_payment( $order_id, $retry, true, $previous_error );
		}

		return parent::process_payment( $order_id, $retry, $force_save_source, $previous_error );
	}

	/**
	 * Register the payment information on subscription meta.
	 *
	 * @param $subscription YWSBS_Subscription
	 * @param $order WC_Order
	 */
	public function add_payment_meta_data_to_subscription( $subscription, $order ) {

		if ( ! $subscription || ! $order || $subscription->get( 'order_id' ) != $order->get_id() || $subscription->get_payment_method() != $this->id ) {
			return;
		}

		update_post_meta( $subscription->id, '_stripe_customer_id', $order->get_meta( '_stripe_customer_id' ) );
		update_post_meta( $subscription->id, '_stripe_source_id', $order->get_meta( '_stripe_source_id' ) );

	}


	/**
	 * Pay the renew order.
	 *
	 * It is triggered by ywsbs_pay_renew_order_with_{gateway_id} action
	 *
	 * @param WC_Order $renewal_order
	 *
	 * @return array|bool|WP_Error
	 * @throws Error\Api
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 * @since 1.1.0
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
			WC_Stripe_Logger::log( sprintf( __( 'Sorry, any subscription is found for this order: %s', 'yith-woocommerce-subscription' ), $order_id ) );
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

				return false;
			}

			$amount = $renewal_order->get_total();

			try {
				if ( $amount * 100 < WC_Stripe_Helper::get_minimum_amount() ) {
					/* translators: minimum amount */
					$message = sprintf( __( 'Sorry, the minimum allowed order total is %1$s to use this payment method.', 'woocommerce-gateway-stripe' ), wc_price( WC_Stripe_Helper::get_minimum_amount() / 100 ) );
					ywsbs_register_failed_payment( $renewal_order, $message );
					return new WP_Error( 'stripe_error', $message );
				}

				$order_id = $renewal_order->get_id();

				// Get source from order
				$prepared_source = $this->prepare_order_source( $renewal_order );

				if ( ! $prepared_source->customer ) {
					return new WP_Error( 'stripe_error', __( 'Customer not found', 'woocommerce-gateway-stripe' ) );
				}

				WC_Stripe_Logger::log( "Info: Begin processing subscription payment for order {$order_id} for the amount of {$amount}" );

				if ( ( $this->is_no_such_source_error( $previous_error ) || $this->is_no_linked_source_error( $previous_error ) ) && apply_filters( 'wc_stripe_use_default_customer_source', true ) ) {
					// Passing empty source will charge customer default.
					$prepared_source->source = '';
				}

				$request            = $this->generate_payment_request( $renewal_order, $prepared_source );
				$request['capture'] = 'true';
				$request['amount']  = WC_Stripe_Helper::get_stripe_amount( $amount, $request['currency'] );
				$response           = WC_Stripe_API::request( $request );
				if ( ! empty( $response->error ) ) {
					$localized_messages = WC_Stripe_Helper::get_localized_messages();

					if ( 'card_error' === $response->error->type ) {
						$localized_message = isset( $localized_messages[ $response->error->code ] ) ? $localized_messages[ $response->error->code ] : $response->error->message;
					} else {
						$localized_message = isset( $localized_messages[ $response->error->type ] ) ? $localized_messages[ $response->error->type ] : $response->error->message;
					}

					$renewal_order->add_order_note( $localized_message );
					throw new WC_Stripe_Exception( print_r( $response, true ), $localized_message );
				}

				do_action( 'wc_gateway_stripe_process_payment', $response, $renewal_order );

				$this->process_response( $response, $renewal_order );
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
	 * @since 3.1.0
	 * @version 4.0.0
	 * @param object $order
	 * @return object
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
}
