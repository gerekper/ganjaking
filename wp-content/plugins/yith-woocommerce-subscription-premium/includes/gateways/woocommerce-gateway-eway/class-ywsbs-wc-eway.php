<?php
/**
 * YWSBS_WC_EWAY integration with WooCommerce eWAY Payment Gateway
 *
 * @class   YWSBS_WC_EWAY
 * @package YITH/Subscription/Gateways
 * @since   2.4.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Compatibility class for WooCommerce eWAY Payment Gateway
 *
 * @extends WC_Gateway_EWAY
 */
class YWSBS_WC_EWAY extends WC_Gateway_EWAY {

	/**
	 * Instance of YWSBS_WC_EWAY
	 *
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * Gateway id
	 *
	 * @var   string ID of specific gateway
	 * @since 1.0
	 */
	public static $gateway_id = 'YWSBS_WC_EWAY';

	/**
	 * Return the instance of Gateway
	 *
	 * @return YWSBS_WC_EWAY
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct();

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

		add_action( 'ywsbs_renew_order_saved', array( $this, 'save_eway_on_renew_order' ), 10, 2 );
		add_action( 'ywsbs_subscription_payment_complete', array( $this, 'add_payment_meta_data_to_subscription' ), 10, 2 );
		add_action( 'ywsbs_pay_renew_order_with_' . $this->id, array( $this, 'pay_renew_order' ), 10, 2 );
	}

	/**
	 * Save additional payment information inside the subscription.
	 *
	 * @param YWSBS_Subscription $subscription Subscription Object.
	 */
	public function add_payment_meta_data_to_subscription( $subscription ) {
		$payment_method = $subscription->get_payment_method();
		if ( $payment_method === $this->id ) {
			$order      = $subscription->get_order();
			$eway_token = $order->get_meta( '_eway_token_customer_id' );
			$subscription->set( '_eway_token_customer_id', $eway_token );
		}
	}

	/**
	 * API call to get Eway access call
	 *
	 * @param WC_Order $order Order to pay.
	 *
	 * @return array|mixed|object
	 * @throws Exception Throws an exception.
	 */
	protected function request_access_code( $order ) {

		// Check if order is for a subscription, if it is check for fee and charge that.
		if ( ywsbs_is_an_order_with_subscription( $order ) ) {

			$method = ( 0 == $order->get_total() ) ? 'CreateTokenCustomer' : 'TokenPayment'; //phpcs:ignore

			$order_total = $order->get_total() * 100;

			$result = json_decode( $this->get_api()->request_access_code( $order, $method, 'Recurring', $order_total ) );

			if ( isset( $result->Errors ) && ! is_null( $result->Errors ) ) { //phpcs:ignore
				throw new Exception( $this->response_message_lookup( $result->Errors ) ); //phpcs:ignore
			}

			return $result;

		} else {

			return parent::request_access_code( $order );

		}

	}

	/**
	 * Pay the renew order.
	 *
	 * It is triggered by ywsbs_pay_renew_order_with_{gateway_id} action.
	 *
	 * @param WC_Order $renewal_order Order to renew.
	 * @param bool     $manually Check if this is a manual renew.
	 *
	 * @return array|bool|WP_Error
	 * @throws Exception Trigger an error.
	 * @since  1.1.0
	 */
	public function pay_renew_order( $renewal_order = null, $manually = false ) {

		$is_a_renew      = $renewal_order->get_meta( 'is_a_renew' );
		$subscriptions   = $renewal_order->get_meta( 'subscriptions' );
		$subscription_id = $subscriptions ? $subscriptions[0] : false;
		$order_id        = $renewal_order->get_id();

		if ( ! $subscription_id || 'yes' !== $is_a_renew ) {
			// translators: placeholder order id.
			yith_subscription_log( sprintf( __( 'Sorry, any subscription is found for this order: %s', 'yith-woocommerce-subscription' ), $order_id ), 'subscription_payment' );
			return false;
		}

		$eway_token_customer_id = $renewal_order->get_meta( '_eway_token_customer_id' );

		if ( ! $eway_token_customer_id ) {
			// translators: placeholder order id.
			yith_subscription_log( sprintf( __( 'EWay token customer ID not found: %s', 'yith-woocommerce-subscription' ), $order_id ), 'subscription_payment' );
			// translators: placeholder subscription id.
			$renewal_order->add_order_note( sprintf( __( 'There is no saved payment token. Subscription renewal failed - %s', 'yith-woocommerce-subscription' ), $subscription_id ) );
			// translators: placeholder subscription id.
			ywsbs_register_failed_payment( $renewal_order, 'Error ' . sprintf( __( 'There is no saved payment token. Subscription renewal failed - %s', 'yith-woocommerce-subscription' ), $subscription_id ) );
			return;
		}

		// Charge the customer.
		try {
			return $this->process_payment_request( $renewal_order, $renewal_order->get_total(), $eway_token_customer_id );
		} catch ( Exception $e ) {
			// translators: placeholder subscription id.
			yith_subscription_log( sprintf( __( 'Error processing subscription renewal #: %s', 'yith-woocommerce-subscription' ), $subscription_id ), 'subscription_payment' );
			// translators: placeholder order id.
			$renewal_order->add_order_note( sprintf( __( 'Error processing order - %s', 'yith-woocommerce-subscription' ), $order_id ) );
			// translators: placeholder error message.
			ywsbs_register_failed_payment( $renewal_order, sprintf( __( 'Subscription renewal failed - %1$s : %2$s', 'yith-woocommerce-subscription' ), $subscription_id, $e->getMessage() ) );
		}

	}

	/**
	 * Copy the eway payment data inside the new order.
	 *
	 * @param WC_Order           $order Renew order.
	 * @param YWSBS_Subscription $subscription Subscription.
	 */
	public function save_eway_on_renew_order( $order, $subscription ) {
		$payment_method = $subscription->get_payment_method();
		if ( $payment_method === $this->id ) {
			$order->update_meta_data( '_eway_token_customer_id', $subscription->get( '_eway_token_customer_id' ) );
			$order->save();
		}
	}


}
