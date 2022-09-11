<?php
/**
 * YWSBS_Amazon_Pay integration class for WooCommerce Amazon Pay Plugin
 *
 * @class   YWSBS_Amazon_Pay
 * @package YITH/Subscription/Gateways
 * @since   2.3.1
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Compatibility class for WooCommerce Amazon Pay.
 */
class YWSBS_Amazon_Pay {


	/**
	 * Instance of YWSBS_Amazon_Pay
	 *
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * Return the instance of class
	 *
	 * @return null|YWSBS_Amazon_Pay
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * Constructor
	 */
	public function __construct() {

		add_filter( 'ywsbs_from_list', array( $this, 'add_from_list' ) );
		add_filter( 'woocommerce_amazon_pa_supports', array( $this, 'add_subscription_support' ) );

		add_action( 'wp_loaded', array( $this, 'init_handlers' ), 12 );
		add_action( 'ywsbs_renew_order_saved', array( $this, 'save_amazon_payment_meta_on_renew_order' ), 10, 2 );

	}

	/**
	 * Initialize Handlers For subscriptions
	 */
	public function init_handlers() {
		$id = wc_apa()->get_gateway()->id;
		add_filter( 'woocommerce_amazon_pa_create_checkout_session_params', array( $this, 'recurring_checkout_session' ) );
		add_filter( 'woocommerce_amazon_pa_update_checkout_session_payload', array( $this, 'recurring_checkout_session_update' ), 10, 3 );
		add_filter( 'woocommerce_amazon_pa_update_complete_checkout_session_payload', array( $this, 'recurring_complete_checkout_session_update' ), 10, 3 );
		add_filter( 'woocommerce_amazon_pa_processed_order', array( $this, 'copy_meta_to_sub' ), 10, 2 );

		add_action( 'ywsbs_pay_renew_order_with_' . $id, array( $this, 'pay_renew_order' ), 10, 2 );
	}

	/**
	 * Process a scheduled subscription payment.
	 *
	 * @param WC_Order $renewal_order Order object.
	 * @param bool     $manually If the payment is manually or not.
	 */
	public function pay_renew_order( $renewal_order = null, $manually = false ) {
		if ( is_null( $renewal_order ) ) {
			return false;
		}

		$is_a_renew      = $renewal_order->get_meta( 'is_a_renew' );
		$subscriptions   = $renewal_order->get_meta( 'subscriptions' );
		$subscription_id = $subscriptions ? $subscriptions[0] : false;
		$order_id        = $renewal_order->get_id();

		if ( ! $subscription_id || 'yes' !== $is_a_renew ) {
			// translators: placeholder order id.
			yith_subscription_log( sprintf( __( 'Sorry, any subscription is found for this order: %s', 'yith-woocommerce-subscription' ), $order_id ), 'subscription_payment' );
			return false;
		}

		$charge_permission_id = $renewal_order->get_meta( 'amazon_charge_permission_id' );
		$can_do_async         = ( 'async' === WC_Amazon_Payments_Advanced_API::get_settings( 'authorization_mode' ) );

		$currency = wc_apa_get_order_prop( $renewal_order, 'order_currency' );

		$response = WC_Amazon_Payments_Advanced_API::create_charge(
			$charge_permission_id,
			array(
				'merchantMetadata'              => WC_Amazon_Payments_Advanced_API::get_merchant_metadata( $order_id ),
				'captureNow'                    => true,
				'canHandlePendingAuthorization' => $can_do_async,
				'chargeAmount'                  => array(
					'amount'       => $renewal_order->get_total(),
					'currencyCode' => $currency,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			wc_apa()->log( "Error processing payment for renewal order #{$order_id}. Charge Permission ID: {$charge_permission_id}", $response );
			// translators: placeholder is a gateway error message.
			$renewal_order->add_order_note( sprintf( __( 'Amazon Pay subscription renewal failed - %s', 'yith-woocommerce-subscription' ), $response->get_error_message() ) );
			wc_apa()->get_gateway()->log_charge_permission_status_change( $renewal_order );
			ywsbs_register_failed_payment( $renewal_order, 'Error: ' . $response->get_error_message() );
		}

		wc_apa()->get_gateway()->log_charge_permission_status_change( $renewal_order );
		wc_apa()->get_gateway()->log_charge_status_change( $renewal_order, $response );
	}

	/**
	 * Filter the payload to add recurring data to the checkout session creation object.
	 *
	 * @param array $payload Payload to create checkout session (JS button).
	 * @return array
	 */
	public function recurring_checkout_session( $payload ) {
		if ( ! class_exists( 'YWSBS_Subscription_Cart' ) ) {
			return $payload;
		}

		$cart_contains_subscription = YWSBS_Subscription_Cart::cart_has_subscriptions();

		if ( ! $cart_contains_subscription ) {
			return $payload;
		}

		if ( ! is_wc_endpoint_url( 'order-pay' ) && $cart_contains_subscription ) {

			if ( count( $cart_contains_subscription ) > 0 ) {
				$amount                          = 0;
				$payload['chargePermissionType'] = 'Recurring';
				$frequency                       = '';
				foreach ( $cart_contains_subscription as $cart_item_key ) {
					$cart_item = WC()->cart->get_cart_item( $cart_item_key );
					$sbs_info  = isset( $cart_item['ywsbs-subscription-info'] ) ? $cart_item['ywsbs-subscription-info'] : array();
					if ( ! empty( $sbs_info ) ) {
						$amount   += (float) $sbs_info['recurring_price'];
						$frequency = $this->get_recurring_frequency( $cart_item );
					}
				}

				$payload['recurringMetadata'] = array(
					'frequency' => $frequency,
					'amount'    => array(
						'amount'       => $amount,
						'currencyCode' => get_woocommerce_currency(),
					),

				);
			}
		}

		return $payload;
	}


	/**
	 * Filter the payload to add recurring data to the checkout session update object.
	 *
	 * @param array    $payload Payload to send to the API before proceeding to checkout.
	 * @param string   $checkout_session_id Checkout Session Id.
	 * @param WC_Order $order Order object.
	 * @return array
	 */
	public function recurring_checkout_session_update( $payload, $checkout_session_id, $order ) {

		$total                      = $order->get_total();
		$cart_contains_subscription = YWSBS_Subscription_Cart::cart_has_subscriptions();

		if ( ! $cart_contains_subscription ) {
			return $payload;
		}

		if ( count( $cart_contains_subscription ) > 0 ) {
			$amount    = 0;
			$frequency = '';
			foreach ( $cart_contains_subscription as $cart_item_key ) {
				$cart_item = WC()->cart->get_cart_item( $cart_item_key );
				$sbs_info  = isset( $cart_item['ywsbs-subscription-info'] ) ? $cart_item['ywsbs-subscription-info'] : array();
				if ( ! empty( $sbs_info ) ) {
					$amount   += (float) $sbs_info['recurring_price'];
					$frequency = $this->get_recurring_frequency( $cart_item );
				}
			}

			$payload['recurringMetadata'] = array(
				'frequency' => $frequency,
				'amount'    => array(
					'amount'       => $amount,
					'currencyCode' => get_woocommerce_currency(),
				),
			);

			if ( 0 >= $total ) {
				$payload['paymentDetails']['paymentIntent'] = 'Confirm';
				unset( $payload['paymentDetails']['canHandlePendingAuthorization'] );

				$payload['paymentDetails']['chargeAmount']['amount'] = $amount;
			}
		}

		return $payload;
	}

	/**
	 * Filter payload to complete recurring checkout session
	 *
	 * @param array $payload Payload for the complete checkout session API call.
	 * @return array
	 */
	public function recurring_complete_checkout_session_update( $payload ) {

		$cart_contains_subscription = YWSBS_Subscription_Cart::cart_has_subscriptions();

		if ( ! $cart_contains_subscription ) {
			return $payload;
		}

		$amount = (float) $payload['chargeAmount']['amount'];
		if ( 0 < $amount ) {
			return $payload;
		}

		if ( count( $cart_contains_subscription ) > 0 ) {
			$amount = 0;
			foreach ( $cart_contains_subscription as $cart_item_key ) {
				$cart_item = WC()->cart->get_cart_item( $cart_item_key );
				$sbs_info  = isset( $cart_item['ywsbs-subscription-info'] ) ? $cart_item['ywsbs-subscription-info'] : array();
				if ( ! empty( $sbs_info ) ) {
					$amount += (float) $sbs_info['recurring_price'];
				}
			}

			$payload['chargeAmount']['amount'] = $amount;
		}

		return $payload;

	}

	/**
	 * Get recurring frequency from the cart
	 *
	 * @param array $item Subscription item in cart.
	 * @return array Standard data object to be used in API calls.
	 */
	private function get_recurring_frequency( array $item ) : array {
		$apa_period    = null;
		$apa_interval  = null;
		$apa_timeframe = PHP_INT_MAX;

		$sbs_info = isset( $item['ywsbs-subscription-info'] ) ? $item['ywsbs-subscription-info'] : array();

		if ( ! empty( $sbs_info ) ) {
			$interval       = $item['ywsbs-subscription-info']['price_is_per'];
			$period         = $item['ywsbs-subscription-info']['price_time_option'];
			$this_timeframe = PHP_INT_MAX;
			switch ( strtolower( $period ) ) {
				case 'years':
					$this_timeframe = YEAR_IN_SECONDS * $interval;
					break;
				case 'months':
					$this_timeframe = MONTH_IN_SECONDS * $interval;
					break;
				case 'weeks':
					$this_timeframe = WEEK_IN_SECONDS * $interval;
					break;
				case 'days':
					$this_timeframe = DAY_IN_SECONDS * $interval;
					break;
			}

			if ( $this_timeframe < $apa_timeframe ) {
				$apa_period   = $period;
				$apa_interval = $interval;
			}
		}

		return $this->parse_interval_to_apa_frequency( $apa_period, $apa_interval );
	}


	/**
	 * Add subscription support to the gateway
	 *
	 * @param array $supports List of supported features.
	 * @return array
	 */
	public function add_subscription_support( $supports ) {
		$supports = array_merge(
			$supports,
			array(
				'yith_subscriptions',
				'yith_subscriptions_scheduling',
				'yith_subscriptions_pause',
				'yith_subscriptions_multiple',
				'yith_subscriptions_payment_date',
				'yith_subscriptions_recurring_amount',
			)
		);

		return $supports;
	}


	/**
	 * Add this gateway in the list "from" to understand from where the
	 * update status is requested.
	 *
	 * @param array $list List of gateways.
	 *
	 * @return mixed
	 */
	public function add_from_list( $list ) {
		$list[] = wc_apa()->get_gateway()->get_method_title();
		return $list;
	}

	/**
	 * Parse WC interval into Amazon Pay frequency object.
	 *
	 * @param string     $apa_period WC Period.
	 * @param int|string $apa_interval WC Interval.
	 * @return array
	 */
	public function parse_interval_to_apa_frequency( $apa_period = null, $apa_interval = null ) {
		switch ( strtolower( $apa_period ) ) {
			case 'years':
			case 'months':
			case 'weeks':
			case 'days':
				$apa_period = substr( $apa_period, 0, -1 );
				$apa_period = ucfirst( strtolower( $apa_period ) );
				break;
			default:
				$apa_period   = 'Variable';
				$apa_interval = '0';
				break;
		}

		if ( is_null( $apa_interval ) ) {
			$apa_interval = '1';
		}

		return array(
			'unit'  => $apa_period,
			'value' => $apa_interval,
		);
	}

	/**
	 * Copy meta from order to the relevant subscriptions
	 *
	 * @param WC_Order $order Order object.
	 * @param object   $response Response from the API.
	 */
	public function copy_meta_to_sub( $order, $response ) {

		$subscriptions = $order->get_meta( 'subscriptions' );

		if ( empty( $subscriptions ) ) {
			return;
		}

		$meta_keys_to_copy = array(
			'amazon_charge_permission_id',
			'amazon_charge_permission_status',
			'amazon_payment_advanced_version',
			'woocommerce_version',
		);

		$perm_status = wc_apa()->get_gateway()->get_cached_charge_permission_status( $order, true );

		if ( isset( $perm_status->status ) && 'Chargeable' !== $perm_status->status ) {
			// remove the recurring payment on Amazon.
			$this->cancelled_subscription( $order );
		}

		wc_apa()->get_gateway()->log_charge_permission_status_change( $order, $response->chargePermissionId ); // phpcs:ignore WordPress.NamingConventions

		foreach ( $subscriptions as $subscription ) {
			$subscription = ywsbs_get_subscription( $subscription );

			foreach ( $meta_keys_to_copy as $key ) {
				$value = $order->get_meta( $key );
				if ( empty( $value ) ) {
					continue;
				}
				$subscription->set( $key, $value );
			}
		}

	}

	/**
	 * Copy the amazon payment data inside the new order.
	 *
	 * @param WC_Order           $order Renew order.
	 * @param YWSBS_Subscription $subscription Subscription.
	 */
	public function save_amazon_payment_meta_on_renew_order( $order, $subscription ) {
		$payment_method = $subscription->get_payment_method();
		if ( wc_apa()->get_gateway()->id === $payment_method ) {
			$order->update_meta_data( 'amazon_charge_permission_id', $subscription->get( 'amazon_charge_permission_id' ) );
			$order->update_meta_data( 'amazon_charge_permission_status', $subscription->get( 'amazon_charge_permission_status' ) );
			$order->update_meta_data( 'amazon_payment_advanced_version', $subscription->get( 'amazon_payment_advanced_version' ) );
			$order->update_meta_data( 'woocommerce_version', $subscription->get( 'woocommerce_version' ) );
			$order->save();
		}
	}

	/**
	 * Cancelled subscription hook
	 *
	 * @param WC_Order $order Main Order.
	 */
	public function cancelled_subscription( $order ) {

		$order_id = $order->get_id();

		$charge_permission_id = $order->get_meta( 'amazon_charge_permission_id' );

		if ( empty( $charge_permission_id ) ) {
			return;
		}

		$response = WC_Amazon_Payments_Advanced_API::close_charge_permission( $charge_permission_id, 'Subscription Cancelled' );

		if ( is_wp_error( $response ) ) {
			wc_apa()->log( "Error processing cancellation for subscription main order #{$order_id}. Charge Permission ID: {$charge_permission_id}", $response );
			return;
		}

		wc_apa()->get_gateway()->log_charge_permission_status_change( $order );
	}

}
