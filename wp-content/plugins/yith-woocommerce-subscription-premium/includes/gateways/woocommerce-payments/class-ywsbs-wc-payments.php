<?php
/**
 * YWSBS_WC_Payments integration with WooCommerce Payments Plugin
 *
 * @class   YWSBS_WC_Payments
 * @since   2.4.0
 * @author  YITH
 * @package YITH/Subscription/Gateways
 */

use WCPay\Constants\Payment_Initiated_By;
use WCPay\Constants\Payment_Type;
use WCPay\Exceptions\API_Exception;
use WCPay\Logger;
use WCPay\Payment_Information;

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Compatibility class for WooCommerce Payments.
 *
 * @extends WC_Payment_Gateway_WCPay
 */
class YWSBS_WC_Payments extends WC_Payment_Gateway_WCPay {

	const PAYMENT_METHOD_META_TABLE = 'wc_order_tokens';
	const PAYMENT_METHOD_META_KEY   = 'token';

	/**
	 * Instance of YWSBS_WC_Payments
	 *
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * Gateway id
	 *
	 * @since 1.0
	 * @var   string ID of specific gateway
	 */
	public static $gateway_id = 'ywsbs_wc_payments';

	/**
	 * Return the instance of Gateway
	 *
	 * @return YWSBS_WC_Payments
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * Constructor
	 *
	 * @param WC_Payments_API_Client               $payments_api_client             - WooCommerce Payments API client.
	 * @param WC_Payments_Account                  $account                         - Account class instance.
	 * @param WC_Payments_Customer_Service         $customer_service                - Customer class instance.
	 * @param WC_Payments_Token_Service            $token_service                   - Token class instance.
	 * @param WC_Payments_Action_Scheduler_Service $action_scheduler_service        - Action Scheduler service instance.
	 * @param WCPay\Session_Rate_Limiter           $failed_transaction_rate_limiter - Rate Limiter for failed transactions.
	 * @param WC_Payments_Order_Service            $order_service                   - Order Service.
	 */
	public function __construct( $payments_api_client, $account, $customer_service, $token_service, $action_scheduler_service, $failed_transaction_rate_limiter, $order_service ) {

		parent::__construct( $payments_api_client, $account, $customer_service, $token_service, $action_scheduler_service, $failed_transaction_rate_limiter, $order_service );

		$this->supports = array_merge(
			$this->supports,
			array(
				'yith_subscriptions',
				'yith_subscriptions_scheduling',
				'yith_subscriptions_pause',
				'yith_subscriptions_multiple',
				'yith_subscriptions_payment_date',
				'yith_subscriptions_recurring_amount',
			)
		);

		add_filter( 'wc_payments_display_save_payment_method_checkbox', array( $this, 'display_save_payment_method_checkbox' ), 10 );
		add_action( 'ywsbs_renew_order_saved', array( $this, 'save_wc_payment_meta_on_renew_order' ), 10, 2 );
		add_action( 'ywsbs_subscription_payment_complete', array( $this, 'add_payment_meta_data_to_subscription' ), 10, 2 );
		add_action( 'ywsbs_pay_renew_order_with_' . $this->id, array( $this, 'pay_renew_order' ), 10, 2 );

		apply_filters( 'wcpay_force_network_saved_cards', '__return_true' );
	}

	/**
	 * Prepares the payment information object.
	 *
	 * @param WC_Order $order The order whose payment will be processed.
	 *
	 * @return Payment_Information An object, which describes the payment.
	 */
	protected function prepare_payment_information( $order ) {

		$payment_information = parent::prepare_payment_information( $order );

		$payment_information->set_payment_type( Payment_Type::RECURRING() );
		if ( is_callable( array( $payment_information, 'must_save_payment_method_to_store' ) ) ) {
			$payment_information->must_save_payment_method_to_store();
		} elseif ( is_callable( array( $payment_information, 'must_save_payment_method' ) ) ) {
			$payment_information->must_save_payment_method();
		}

		return $payment_information;
	}

	/**
	 * Returns a boolean value indicating whether the save payment checkbox should be
	 * displayed during checkout.
	 *
	 * @param bool $display Show or not the save payment_method checkbox.
	 *
	 * @return bool
	 */
	public function display_save_payment_method_checkbox( $display ) {

		if ( ! class_exists( 'YWSBS_Subscription_Cart' ) ) {
			return $display;
		}

		$cart_contains_subscription = YWSBS_Subscription_Cart::cart_has_subscriptions();

		if ( $cart_contains_subscription ) {
			return false;
		}

		return $display;
	}


	/**
	 * Pay the renew order.
	 *
	 * It is triggered by ywsbs_pay_renew_order_with_{gateway_id} action.
	 *
	 * @param WC_Order $renewal_order Order to renew.
	 * @param bool     $manually      Check if this is a manual renew.
	 *
	 * @return array|bool|WP_Error|void
	 * @throws WC_Stripe_Exception Trigger an error.
	 * @since  1.1.0
	 */
	public function pay_renew_order( $renewal_order = null, $manually = false ) {

		$is_a_renew      = $renewal_order->get_meta( 'is_a_renew' );
		$subscriptions   = $renewal_order->get_meta( 'subscriptions' );
		$subscription_id = $subscriptions ? $subscriptions[0] : false;
		$subscription    = ywsbs_get_subscription( $subscription_id );
		$order_id        = $renewal_order->get_id();

		if ( ! $subscription_id || 'yes' !== $is_a_renew ) {
			// translators: placeholder order id.
			yith_subscription_log( sprintf( __( 'Sorry, any subscription is found for this order: %s', 'yith-woocommerce-subscription' ), $order_id ), 'subscription_payment' );

			return false;
		}

		$token = $this->get_payment_token( $renewal_order );

		if ( is_null( $token ) ) {
			$default = WC_Payment_Tokens::get_customer_default_token( $renewal_order->get_customer_id() );

			if ( $default && $this->id === $default->get_gateway_id() ) {
				$token = $default;
				$key   = $default->get_id();
				$renewal_order->update_meta_data( '_payment_tokens', array( $key ) );
				$subscription->set( '_payment_tokens', array( $key ) );
			} else {
				$tokens = WC_Payment_Tokens::get_customer_tokens( $renewal_order->get_customer_id(), $this->id );

				if ( $tokens ) {
					foreach ( $tokens as $key => $current_token ) {
						$token = $current_token;
						$subscription->set( '_payment_tokens', array( $key ) );
						$renewal_order->update_meta_data( '_payment_tokens', array( $key ) );
						break;
					}
				}
			}
		}

		if ( is_null( $token ) && ! WC_Payments::is_network_saved_cards_enabled() ) {
			// translators: placeholder order id.
			yith_subscription_log( sprintf( __( 'There is no saved payment token for order #%s', 'yith-woocommerce-subscription' ), $order_id ), 'subscription_payment' );
			// translators: placeholder subscription id.
			$renewal_order->add_order_note( sprintf( __( 'There is no saved payment token. Subscription renewal failed - %s', 'yith-woocommerce-subscription' ), $subscription_id ) );
			// translators: placeholder subscription id.
			ywsbs_register_failed_payment( $renewal_order, 'Error ' . sprintf( __( 'There is no saved payment token. Subscription renewal failed - %s', 'yith-woocommerce-subscription' ), $subscription_id ) );

			return;
		}

		try {
			$payment_information = new Payment_Information( '', $renewal_order, Payment_Type::RECURRING(), $token, Payment_Initiated_By::MERCHANT() );
			$renewal_order->save();
			$this->process_payment_for_order( null, $payment_information );
		} catch ( API_Exception $e ) {
			Logger::error( 'Error processing subscription renewal: ' . $e->getMessage() );

			// translators: placeholder subscription id.
			yith_subscription_log( sprintf( __( 'Error processing subscription renewal #: %s', 'yith-woocommerce-subscription' ), $subscription_id ), 'subscription_payment' );
			// translators: placeholder order id.
			$renewal_order->add_order_note( sprintf( __( 'Error processing order - %s', 'yith-woocommerce-subscription' ), $order_id ) );
			// translators: placeholder error message.
			ywsbs_register_failed_payment( $renewal_order, sprintf( __( 'Subscription renewal failed - %1$s : %2$s', 'yith-woocommerce-subscription' ), $subscription_id, $e->getMessage() ) );

			if ( ! empty( $payment_information ) ) {
				$note = sprintf(
					WC_Payments_Utils::esc_interpolated_html(
					/* translators: %1: the failed payment amount, %2: error message  */
						__(
							'A payment of %1$s <strong>failed</strong> to complete with the following message: <code>%2$s</code>.',
							'woocommerce-payments'
						),
						array(
							'strong' => '<strong>',
							'code'   => '<code>',
						)
					),
					wc_price( $renewal_order->get_total(), array( 'currency' => WC_Payments_Utils::get_order_intent_currency( $renewal_order ) ) ),
					esc_html( rtrim( $e->getMessage(), '.' ) )
				);
				$renewal_order->add_order_note( $note );
			}
		}
	}

	/**
	 * Save additional payment information inside the subscription.
	 *
	 * @param YWSBS_Subscription $subscription Subscription Object.
	 */
	public function add_payment_meta_data_to_subscription( $subscription ) {
		$payment_method = $subscription->get_payment_method();
		if ( $payment_method === $this->id ) {
			$order             = $subscription->get_order();
			$order_tokens      = $order->get_payment_tokens();
			$customer_id       = $order->get_meta( '_stripe_customer_id' );
			$payment_method_id = $order->get_meta( '_payment_method_id' );
			$subscription->set( '_payment_tokens', $order_tokens );
			$subscription->set( '_stripe_customer_id', $customer_id );
			$subscription->set( '_payment_method_id', $payment_method_id );
		}
	}

	/**
	 * Copy the WooCommerce Payment data inside the new order.
	 *
	 * @param WC_Order           $order        Renew order.
	 * @param YWSBS_Subscription $subscription Subscription.
	 */
	public function save_wc_payment_meta_on_renew_order( $order, $subscription ) {
		$payment_method = $subscription->get_payment_method();
		if ( $payment_method === $this->id ) {
			$order->update_meta_data( '_payment_tokens', $subscription->get( '_payment_tokens' ) );
			$order->update_meta_data( '_stripe_customer_id', $subscription->get( '_stripe_customer_id' ) );
			$order->update_meta_data( '_payment_method_id', $subscription->get( '_payment_method_id' ) );
			$order->save();
		}
	}


}
