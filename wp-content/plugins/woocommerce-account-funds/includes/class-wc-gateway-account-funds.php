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
	 * Check if the gateway is available for use
	 *
	 * @return bool
	 */
	public function is_available() {
		if ( ! parent::is_available() ) {
			return false;
		}

		if ( WC()->cart ) {
			if ( WC_Account_Funds_Cart_Manager::cart_contains_deposit() ) {
				return false;
			}

			// There are no enough funds to pay for the whole order.
			if ( WC_Account_Funds_Cart_Manager::using_funds() && $this->get_order_total() > 0 ) {
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
			return array();
		}

		$order           = wc_get_order( $order_id );
		$available_funds = WC_Account_Funds::get_account_funds( $order->get_user_id(), false, $order_id );
		$funds_used      = WC()->session->get( 'used-account-funds', 0 );

		if ( $order->get_total() > 0 || $available_funds < $funds_used ) {
			wc_add_notice( __( 'Payment error:', 'woocommerce-account-funds' ) . ' ' . __( 'Insufficient account balance', 'woocommerce-account-funds' ), 'error' );
			return array();
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
	 *
	 * @return bool|WP_Error
	 */
	public function scheduled_subscription_payment( $amount, $order ) {
		$ret = true;

		// The WC_Subscriptions_Manager will generates order for the renewal.
		// However, the total will not be cleared and replaced with amount of
		// funds used. The set_renewal_order_meta will fix that.
		add_action( 'woocommerce_subscriptions_renewal_order_created', array( $this, 'set_renewal_order_meta' ), 10, 2 );

		try {
			$user_id = $order->get_user_id();
			if ( ! $user_id ) {
				throw new Exception( __( 'Customer not found.', 'woocommerce-account-funds' ) );
			}

			$funds = WC_Account_Funds::get_account_funds( $user_id, false );
			if ( $amount > $funds ) {
				throw new Exception( sprintf( __( 'Insufficient funds (amount to pay = %s; available funds = %s).', 'woocommerce-account-funds' ), wc_price( $amount ), wc_price( $funds ) ) );
			}

			WC_Account_Funds::remove_funds( $order->get_user_id(), $amount );

			$this->complete_payment_for_subscriptions_on_order( $order );

			$order->add_order_note( sprintf( __( 'Account funds payment applied: %s', 'woocommerce-account-funds' ), $amount ) );

		} catch ( Exception $e ) {

			$order->add_order_note( $e->getMessage() );
			$this->payment_failed_for_subscriptions_on_order( $order );

			$ret = new WP_Error( 'accountfunds', $e->getMessage() );
		}

		remove_action( 'woocommerce_subscriptions_renewal_order_created', array( $this, 'set_renewal_order_meta' ), 10, 2 );

		return $ret;
	}

	/**
	 * Complete subscriptions payments in a given order.
	 *
	 * @since 2.1.7
	 * @version 2.1.7
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
	 * @param WC_Order $renewal_order Order from renewal payment
	 *
	 * @return void
	 */
	public function set_renewal_order_meta( $renewal_order ) {
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
}
