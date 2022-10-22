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
			'refunds',
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

		$description = sprintf( __( 'Available balance: %s', 'woocommerce-account-funds' ), WC_Account_Funds::get_account_funds() );

		if ( 'yes' === get_option( 'account_funds_give_discount' ) ) {
			$amount       = get_option( 'account_funds_discount_amount', 0 );
			$amount       = 'fixed' === get_option( 'account_funds_discount_type' ) ? wc_price( $amount ) : $amount . '%';
			$description .= '<br/><em>' . sprintf( __( 'Use your account funds and get a %s discount on your order.', 'woocommerce-account-funds' ), $amount ) . '</em>';
		}

		$this->description = $description;

		// Subscriptions.
		add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, array( $this, 'process_subscription_payment' ), 10, 2 );
		add_filter( 'woocommerce_my_subscriptions_recurring_payment_method', array( $this, 'subscription_payment_method_name' ), 10, 3 );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_subscriptions_paid_for_failed_renewal_order', array( $this, 'failed_renewal_order_paid' ), 5, 2 );
		add_action( 'subscriptions_activated_for_order', array( $this, 'subscriptions_activated_for_order' ), 5 );

		// Make sure this class is loaded before using any methods that depend on it.
		include_once __DIR__ . '/class-wc-account-funds-cart-manager.php';
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

		if (
			WC()->cart && (
				WC_Account_Funds_Cart_Manager::using_funds() ||
				WC_Account_Funds_Cart_Manager::cart_contains_deposit() ||
				WC_Account_Funds::get_account_funds( null, false ) < $this->get_order_total()
			)
		) {
			return false;
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
				'default' => 'yes',
			),
			'title'   => array(
				'title'       => __( 'Title', 'woocommerce-account-funds' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-account-funds' ),
				'default'     => __( 'Account Funds', 'woocommerce-account-funds' ),
			),
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
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order ),
			);
		}

		$order_total     = $this->get_order_total();
		$available_funds = WC_Account_Funds::get_account_funds( $order->get_customer_id(), false, $order_id );

		if ( $available_funds < $order_total ) {
			wc_add_notice( __( 'Payment error:', 'woocommerce-account-funds' ) . ' ' . __( 'Insufficient account balance', 'woocommerce-account-funds' ), 'error' );
			return array( 'result' => 'error' );
		}

		// Update account funds.
		WC_Account_Funds_Manager::decrease_user_funds( $order->get_customer_id(), $order_total );

		$order->update_meta_data( '_funds_used', $order_total );
		$order->update_meta_data( '_funds_removed', 1 );
		$order->update_meta_data( '_funds_version', WC_ACCOUNT_FUNDS_VERSION );
		$order->save_meta_data();

		// Payment complete.
		$order->payment_complete();

		// Remove cart.
		WC()->cart->empty_cart();

		// Return thankyou redirect.
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}

	/**
	 * Process scheduled subscription payment.
	 *
	 * @since 2.4.0
	 *
	 * @param float    $order_total Renewal order total.
	 * @param WC_Order $order       Renewal order.
	 */
	public function process_subscription_payment( $order_total, $order ) {
		try {
			$user_id = $order->get_customer_id();

			if ( ! $user_id ) {
				throw new Exception( __( 'Customer not found.', 'woocommerce-account-funds' ) );
			}

			$funds = WC_Account_Funds::get_account_funds( $user_id, false );

			if ( $order_total > $funds ) {
				throw new Exception(
					sprintf(
						__( 'Insufficient funds (amount to pay = %1$s; available funds = %2$s).', 'woocommerce-account-funds' ),
						wc_account_funds_format_order_price( $order, $order_total ),
						wc_account_funds_format_order_price( $order, $funds )
					)
				);
			}

			WC_Account_Funds_Manager::decrease_user_funds( $user_id, $order_total );

			$order->update_meta_data( '_funds_used', $order_total );
			$order->update_meta_data( '_funds_removed', 1 );
			$order->update_meta_data( '_funds_version', WC_ACCOUNT_FUNDS_VERSION );
			$order->save_meta_data();

			/* translators: %s: funds used */
			$order->add_order_note( sprintf( __( 'Account funds payment applied: %s', 'woocommerce-account-funds' ), wc_account_funds_format_order_price( $order, $order_total ) ) );
			$order->payment_complete();
		} catch ( Exception $e ) {
			$order->add_order_note( $e->getMessage() );
			$this->payment_failed_for_subscriptions_on_order( $order );
		}
	}

	/**
	 * Process refund.
	 *
	 * @since 2.4.0
	 *
	 * @param int        $order_id Order ID.
	 * @param float|null $amount Refund amount.
	 * @param string     $reason Refund reason.
	 * @return bool|WP_Error
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		$order = wc_get_order( $order_id );

		if ( ! $order || 0 >= $amount ) {
			return false;
		}

		WC_Account_Funds_Manager::increase_user_funds( $order->get_customer_id(), $amount );

		$funds_refunded = (float) $order->get_meta( '_funds_refunded' );

		$order->update_meta_data( '_funds_refunded', ( $funds_refunded + $amount ) );
		$order->save_meta_data();

		$order->add_order_note(
			sprintf(
				/* translators: 1: Refund amount, 2: Payment gateway title */
				__( 'Refunded %1$s via %2$s.', 'woocommerce-account-funds' ),
				wc_account_funds_format_order_price( $order, $amount ),
				$this->method_title
			)
		);

		return true;
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
	 * Payment method name
	 */
	public function subscription_payment_method_name( $payment_method_to_display, $subscription_details, $order ) {
		if ( ! $order->get_customer_id() || $this->id !== $order->get_meta( '_recurring_payment_method' ) ) {
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
		wc_deprecated_function( __FUNCTION__, '2.3.5' );
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
		wc_deprecated_function( __FUNCTION__, '2.3.9' );
	}

	/**
	 * Process scheduled subscription payment.
	 *
	 * @since 1.0.0
	 * @deprecated 2.4.0 Use WC_Gateway_Account_Funds->process_subscription_payment()
	 *
	 * @see WC_Gateway_Account_Funds->process_subscription_payment()
	 *
	 * @param float    $amount Renewal order amount.
	 * @param WC_Order $order  Renewal order.
	 */
	public function scheduled_subscription_payment( $amount, $order ) {
		wc_deprecated_function( __FUNCTION__, '2.4.0', 'WC_Gateway_Account_Funds->process_subscription_payment()' );

		$this->process_subscription_payment( $amount, $order );
	}
}
