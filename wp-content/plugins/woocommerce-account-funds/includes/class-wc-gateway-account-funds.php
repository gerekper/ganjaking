<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Gateway_Account_Funds class.
 *
 * @extends WC_Payment_Gateway
 */
class WC_Gateway_Account_Funds extends WC_Payment_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id                 = 'accountfunds';
		$this->method_title       = __( 'Account Funds', 'woocommerce-account-funds' );
		$this->method_description = __( 'This gateway takes full payment using a logged in user\'s account funds.', 'woocommerce-account-funds' );
		$this->supports           = array(
			'products',
			'subscriptions',
			'subscription_cancellation',
			'subscription_reactivation',
			'subscription_suspension',
			'subscription_amount_changes',
			'subscription_date_changes',
			'subscription_payment_method_change',
			'subscription_payment_method_change_customer',
			'subscription_payment_method_change_admin',
		);

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		$this->title = $this->settings['title'];

		$description = sprintf( __( "Available balance: %s", 'woocommerce-account-funds'), WC_Account_Funds::get_account_funds() );

		if ( 'yes' === get_option( 'account_funds_give_discount' ) ) {
			$amount      = floatval( get_option( 'account_funds_discount_amount' ) );
			$amount      = 'fixed' === get_option( 'account_funds_discount_type' ) ? wc_price( $amount ) : $amount . '%';
			$description .= '<br/><em>' . sprintf( __( 'Use your account funds and get a %s discount on your order.', 'woocommerce-account-funds' ), $amount ) . '</em>';
		}

		$this->description = $description;

		// Subscriptions.
		add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, array( $this, 'scheduled_subscription_payment' ), 10, 2 );
		add_filter( 'woocommerce_my_subscriptions_recurring_payment_method', array( $this, 'subscription_payment_method_name' ), 10, 3 );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_subscriptions_paid_for_failed_renewal_order', array( $this, 'failed_renewal_order_paid' ), 5, 2 );
		add_action( 'subscriptions_activated_for_order', array( $this, 'subscriptions_activated_for_order' ), 5 );

		// Make sure this class is loaded before using any methods that depend on it.
		include_once( __DIR__ . '/class-wc-account-funds-cart-manager.php' );
	}

	/**
	 * Gets the gateway's description.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_description() {
		if ( is_checkout() && WC_Account_Funds_Cart_Manager::using_funds() ) {
			$this->description = sprintf(
				__( "Remaining balance: %s", 'woocommerce-account-funds'),
				wc_price( WC_Account_Funds_Cart_Manager::get_remaining_balance() )
			);
		}

		return parent::get_description();
	}

	/**
	 * Gets the order total in checkout and pay_for_order.
	 *
	 * @since 2.3.6
	 *
	 * @return float
	 */
	protected function get_order_total() {
		/*
		 * Use the subscription total on the subscription details page.
		 * This allows showing/hiding the action "Add payment/Change payment" when "Account Funds" is
		 * the unique available payment gateway for subscriptions.
		 */
		if ( function_exists( 'wcs_get_subscription' ) ) {
			$subscription_id = absint( get_query_var( 'view-subscription' ) );

			if ( ! $subscription_id ) {
				$subscription_id = absint( get_query_var( 'subscription-payment-method' ) );
			}

			if ( $subscription_id > 0 ) {
				$subscription = wcs_get_subscription( $subscription_id );

				return (float) $subscription->get_total();
			}
		}

		return parent::get_order_total();
	}

	/**
	 * Check if the gateway is available for use
	 *
	 * @return bool
	 */
	public function is_available() {
		if ( ! parent::is_available() || ! is_user_logged_in() ) {
			return false;
		}

		if ( WC()->cart ) {
			if ( WC_Account_Funds_Cart_Manager::cart_contains_deposit() ) {
				return false;
			}

			$order_total = $this->get_order_total();
			$funds       = WC_Account_Funds::get_account_funds( get_current_user_id(), false );
			$using_funds = WC_Account_Funds_Cart_Manager::using_funds();

			// Not enough funds.
			if (
				( $using_funds && $order_total > 0 ) ||
				( ! $using_funds && $funds < $order_total )
			) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Settings
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-account-funds' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable', 'woocommerce-account-funds' ),
				'default' => 'yes'
			),
			'title' => array(
				'title'       => __( 'Title', 'woocommerce-account-funds' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-account-funds' ),
				'default'     => __( 'Account Funds', 'woocommerce-account-funds' )
			)
		);
	}

	/**
	 * Process Payment.
	 *
	 * @param int $order_id Order ID.
	 * @return array
	 */
	public function process_payment( $order_id ) {
		if ( ! is_user_logged_in() ) {
			wc_add_notice( __( 'Payment error:', 'woocommerce-account-funds' ) . ' ' . __( 'You must be logged in to use this payment method', 'woocommerce-account-funds' ), 'error' );
			return array( 'result' => 'error' );
		}

		$order = wc_get_order( $order_id );

		// Changing the subscription's payment method.
		if ( $order instanceof WC_Subscription ) {
			return array(
				'result'    => 'success',
				'redirect'  => $this->get_return_url( $order )
			);
		}

		$available_funds = WC_Account_Funds::get_account_funds( $order->get_user_id(), false, $order_id );
		$funds_used      = WC()->session->get( 'used-account-funds', 0 );

		if ( $order->get_total() > 0 || $available_funds < $funds_used ) {
			wc_add_notice( __( 'Payment error:', 'woocommerce-account-funds' ) . ' ' . __( 'Insufficient account balance', 'woocommerce-account-funds' ), 'error' );
			return array( 'result' => 'error' );
		}

		// Update account funds.
		WC_Account_Funds::remove_funds( $order->get_user_id(), $funds_used );
		update_post_meta( $order_id, '_funds_used', $funds_used );
		update_post_meta( $order_id, '_funds_removed', 1 );

		// Payment complete.
		$order->payment_complete();

		// Remove cart.
		WC()->cart->empty_cart();

		// Return thankyou redirect.
		return array(
			'result'    => 'success',
			'redirect'  => $this->get_return_url( $order )
		);
	}

	/**
	 * Process scheduled subscription payment.
	 *
	 * @since 1.0.0
	 * @version 2.1.7
	 *
	 * @param float    $amount Renewal order amount.
	 * @param WC_Order $order  Renewal order.
	 * @return bool|WP_Error True on success. WP_Error on failure.
	 */
	public function scheduled_subscription_payment( $amount, $order ) {
		try {
			$user_id = $order->get_user_id();

			if ( ! $user_id ) {
				throw new Exception( __( 'Customer not found.', 'woocommerce-account-funds' ) );
			}

			$funds = WC_Account_Funds::get_account_funds( $user_id, false );

			if ( $amount > $funds ) {
				throw new Exception( sprintf( __( 'Insufficient funds (amount to pay = %s; available funds = %s).', 'woocommerce-account-funds' ), wc_price( $amount ), wc_price( $funds ) ) );
			}

			$order_id = ( method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id );

			WC_Account_Funds::remove_funds( $user_id, $amount );

			update_post_meta( $order_id, '_funds_used', $amount );
			update_post_meta( $order_id, '_funds_removed', 1 );
			update_post_meta( $order_id, '_order_total', 0 );

			$order->add_order_note( sprintf( __( 'Account funds payment applied: %s', 'woocommerce-account-funds' ), $amount ) );
			$order->payment_complete();
		} catch ( Exception $e ) {
			$order->add_order_note( $e->getMessage() );
			$this->payment_failed_for_subscriptions_on_order( $order );

			return new WP_Error( 'accountfunds', $e->getMessage() );
		}

		return true;
	}

	/**
	 * Complete subscriptions payments in a given order.
	 *
	 * @since 2.1.7
	 * @deprecated 2.3.9
	 *
	 * @param int|WC_Order $order Order ID or order object.
	 */
	protected function complete_payment_for_subscriptions_on_order( $order ) {
		foreach ( $this->get_subscriptions_for_order( $order ) as $subscription ) {
			$subscription->payment_complete();
		}
		do_action( 'processed_subscription_payments_for_order', $order );
	}

	/**
	 * Failed payment for subscriptions in a given order.
	 *
	 * @since 2.1.7
	 * @version 2.1.7
	 *
	 * @param int|WC_Order $order Order ID or order object.
	 */
	protected function payment_failed_for_subscriptions_on_order( $order ) {
		foreach ( $this->get_subscriptions_for_order( $order ) as $subscription ) {
			/*
			 * If Account Funds is the unique payment gateway that support subscriptions, no payment gateways will be
			 * available during checkout. So, we set the subscription to manual renewal.
			 */
			if ( ! $subscription->is_manual() ) {
				$subscription->set_requires_manual_renewal( true );
				$subscription->add_meta_data( '_restore_auto_renewal', 'yes', true );
				$subscription->save();
			}

			$subscription->payment_failed();
		}
		do_action( 'processed_subscription_payment_failure_for_order', $order );
	}

	/**
	 * Get subscriptions from a given order.
	 *
	 * @since 2.1.7
	 * @version 2.1.7
	 *
	 * @param int|WC_Order $order Order ID or order object.
	 *
	 * @return array List of subscriptions.
	 */
	protected function get_subscriptions_for_order( $order ) {
		return wcs_get_subscriptions_for_order(
			$order,
			array(
				'order_type' => array( 'parent', 'renewal' ),
			)
		);
	}

	/**
	 * Set renewal order meta.
	 *
	 * Set the total to zero as it will be replaced by `_funds_used`.
	 *
	 * @deprecated 2.3.5
	 *
	 * @param WC_Order $renewal_order Order from renewal payment
	 */
	public function set_renewal_order_meta( $renewal_order ) {
		_deprecated_function( __FUNCTION__, '2.3.5' );

		// Use total from post meta directly to avoid filter in total amount.
		// The _order_total meta is already calculated for total subscription
		// to pay of given order.
		$renewal_order_id = version_compare( WC_VERSION, '3.0', '<' ) ? $renewal_order->id : $renewal_order->get_id();

		update_post_meta( $renewal_order_id, '_funds_used', get_post_meta( $renewal_order_id, '_order_total', true ) );

		$renewal_order->set_total( 0 );
		$renewal_order->add_order_note( __( 'Account Funds subscription payment completed', 'woocommerce-account-funds' ) );
	}

	/**
	 * Payment method name
	 */
	public function subscription_payment_method_name( $payment_method_to_display, $subscription_details, $order ) {
		$customer_user = version_compare( WC_VERSION, '3.0', '<' ) ? $order->customer_user : $order->get_customer_id();
		$order_id = version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id();
		if ( $this->id !== get_post_meta( $order_id, '_recurring_payment_method', true ) || ! $customer_user ) {
			return $payment_method_to_display;
		}
		return sprintf( __( 'Via %s', 'woocommerce-account-funds' ), $this->method_title );
	}

	/**
	 * Processes a subscription after its failed renewal order has been paid.
	 *
	 * @since 2.3.8
	 *
	 * @param WC_Order        $order        Renewal order successfully paid.
	 * @param WC_Subscription $subscription Subscription related to the renewed order.
	 */
	public function failed_renewal_order_paid( $order, $subscription ) {
		$this->restore_auto_renewal( $subscription );
	}

	/**
	 * Processes subscriptions after being activated due to the payment of a renewal order.
	 *
	 * @since 2.3.8
	 *
	 * @param int $order_id Order ID.
	 */
	public function subscriptions_activated_for_order( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		$subscriptions = $this->get_subscriptions_for_order( $order );

		foreach ( $subscriptions as $subscription ) {
			$this->restore_auto_renewal( $subscription );
		}
	}

	/**
	 * Restores the subscription auto-renew previously deactivated when the payment with funds failed.
	 *
	 * @since 2.3.8
	 *
	 * @param WC_Subscription $subscription Subscription object.
	 */
	protected function restore_auto_renewal( $subscription ) {
		if ( ! $subscription->get_meta( '_restore_auto_renewal' ) ) {
			return;
		}

		$subscription->set_requires_manual_renewal( false );
		$subscription->delete_meta_data( '_restore_auto_renewal' );
		$subscription->save();
	}
}
