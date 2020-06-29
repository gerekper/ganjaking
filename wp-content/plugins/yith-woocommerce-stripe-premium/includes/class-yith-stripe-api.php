<?php
/**
 * Created by PhpStorm.
 * User: YourInspiration
 * Date: 20/02/2015
 * Time: 16:54
 */

use \Stripe\Stripe;
use \Stripe\Charge;
use \Stripe\Refund;
use \Stripe\Customer;
use \Stripe\Plan;
use Stripe\StripeObject;
use \Stripe\Subscription;
use \Stripe\Invoice;
use \Stripe\InvoiceItem;
use \Stripe\Event;
use \Stripe\Product;
use \Stripe\BalanceTransaction;
use \Stripe\WebhookEndpoint;
use \Stripe\PaymentIntent;
use \Stripe\PaymentMethod;
use \Stripe\SetupIntent;
use \Stripe\Checkout\Session;

class YITH_Stripe_API {

	protected $private_key = '';

	/**
	 * Set the Stripe library
	 *
	 * @param $key
	 *
	 * @since 1.0.0
	 */
	public function __construct( $key ) {
		if ( ! class_exists( 'Stripe' ) ) {
			include_once( dirname( dirname( __FILE__ ) ) . '/vendor/autoload.php' );
		}

		$this->private_key = $key;
		Stripe::setAppInfo( 'YITH WooCommerce Stripe', YITH_WCSTRIPE_VERSION, 'https://yithemes.com' );
		Stripe::setApiVersion( YITH_WCSTRIPE_API_VERSION );
		Stripe::setApiKey( $this->private_key );
	}

	/**
	 * Returns Stripe's Private Key
	 *
	 * @return string
	 * @since 1.6.0
	 */
	public function get_private_key() {
		return apply_filters( 'yith_wcstripe_private_key', $this->private_key );
	}

	/* === CHARGES METHODS === */

	/**
	 * Create the charge
	 *
	 * @param $params
	 *
	 * @return Charge
	 * @throws \Stripe\Exception\ApiErrorException
	 * @since 1.0.0
	 */
	public function charge( $params ) {
		return Charge::create( $params, array(
			'idempotency_key' => self::generateRandomString(),
		) );
	}

	/**
	 * Retrieve the charge
	 *
	 * @param $transaction_id
	 *
	 * @return Charge
	 * @throws \Stripe\Exception\ApiErrorException
	 * @since 1.0.0
	 */
	public function get_charge( $transaction_id ) {
		return Charge::retrieve( $transaction_id );
	}

	/**
	 * Capture a charge
	 *
	 * @param $transaction_id
	 *
	 * @return Charge
	 * @throws \Stripe\Exception\ApiErrorException
	 * @since 1.0.0
	 */
	public function capture_charge( $transaction_id ) {
		$charge = $this->get_charge( $transaction_id );

		// exist if already captured
		if ( ! $charge->captured ) {
			$charge->capture();
		}

		return $charge;
	}

	/**
	 * Change a charge
	 *
	 * @param string $transaction_id Charge id
	 * @param array  $params
	 *
	 * @return Charge
	 * @throws \Stripe\Exception\ApiErrorException
	 * @since 1.0.0
	 */
	public function update_charge( $transaction_id, $params = array() ) {
		$charge           = $this->get_charge( $transaction_id );
		$valid_properties = array(
			'description',
			'metadata',
			'receipt_email',
			'fraud_details',
			'shipping'
		);

		foreach ( $params as $param => $value ) {
			if ( in_array( $param, $valid_properties ) ) {
				$charge->{$param} = $value;
			}
		}

		$charge->save();

		return $charge;
	}

	/**
	 * Retrieve Balance Transaction
	 *
	 * @param $transaction_id string Transaction unique id
	 * @param $params         array Additional parameters to be sent within the request
	 *
	 * @return BalanceTransaction Balance object
	 * @throws \Stripe\Exception\ApiErrorException
	 */
	public function get_balance_transaction( $transaction_id, $params = array() ) {
		return BalanceTransaction::retrieve( $transaction_id, $params );
	}

	/**
	 * Perform a refund
	 *
	 * @param $transaction_id
	 * @param $params
	 *
	 * @return Refund
	 * @throws \Stripe\Exception\ApiErrorException
	 * @since 1.0.0
	 */
	public function refund( $transaction_id, $params ) {
		return Refund::create( array_merge( array( 'charge' => $transaction_id ), $params ) );
	}

	/* === CUSTOMER METHODS === */

	/**
	 * New customer
	 *
	 * @param $params
	 *
	 * @return Customer
	 * @throws \Stripe\Exception\ApiErrorException
	 * @since 1.0.0
	 */
	public function create_customer( $params ) {
		return Customer::create( $params );
	}

	/**
	 * Retrieve customer
	 *
	 * @param $customer Customer|string Customer object or ID
	 *
	 * @return Customer
	 * @throws \Stripe\Exception\ApiErrorException
	 * @since 1.0.0
	 */
	public function get_customer( $customer ) {
		if ( is_a( $customer, '\Stripe\Customer' ) ) {
			return $customer;
		}

		return Customer::retrieve( $customer );
	}

	/**
	 * Update customer
	 *
	 * @param $customer Customer object or ID
	 * @param $params
	 *
	 * @return Customer
	 * @throws \Stripe\Exception\ApiErrorException
	 * @since 1.0.0
	 */
	public function update_customer( $customer, $params ) {
		$customer = $this->get_customer( $customer );

		// edit
		foreach ( $params as $key => $value ) {
			$customer->{$key} = $value;
		}

		// save
		$customer->save();

		return $customer;
	}

	/* === CARDS METHODS === */

	/**
	 * Create a card
	 *
	 * @param $customer Customer object or ID
	 * @param $token
	 *
	 * @return Customer
	 *
	 * @throws \Stripe\Exception\ApiErrorException
	 * @since 1.0.0
	 */
	public function create_card( $customer, $token ) {
		$customer = $this->get_customer( $customer );

		$result = $customer->sources->create(
			array(
				'card' => $token
			)
		);

		do_action( 'yith_wcstripe_card_created', $customer, $token );

		return $result;
	}

	/**
	 * Update card object
	 *
	 * @param $customer int|\Stripe\Customer Customer object
	 * @param $card_id  string Card id
	 * @param $args     array Parameter to update
	 *
	 * @return \Stripe\Customer Customer
	 * @throws \Stripe\Exception\ApiErrorException
	 */
	public function update_card( $customer, $card_id, $args = array() ) {
		$customer    = $this->get_customer( $customer );
		$customer_id = $customer->id;

		// update card
		$card = $customer->sources->retrieve( $card_id );

		if ( ! $card ) {
			return $customer;
		}

		if ( ! empty( $args ) ) {
			foreach ( $args as $key => $value ) {
				if ( ! isset( $card->$key ) ) {
					continue;
				}
				$card->$key = $value;
			}
		}

		$card->save();

		return $this->get_customer( $customer_id );
	}

	/**
	 * Create a card
	 *
	 * @param $customer Customer object or ID
	 * @param $card_id
	 *
	 * @return Customer
	 *
	 * @throws \Stripe\Exception\ApiErrorException
	 * @since 1.0.0
	 */
	public function delete_card( $customer, $card_id ) {
		$customer    = $this->get_customer( $customer );
		$customer_id = $customer->id;

		// delete card
		$customer->sources->retrieve( $card_id )->delete();

		do_action( 'yith_wcstripe_card_deleted', $customer, $card_id );

		return $this->get_customer( $customer_id );
	}

	/**
	 * Se the default card for the customer
	 *
	 * @param $customer Customer object or ID
	 * @param $card_id
	 *
	 * @return Customer
	 *
	 * @throws \Stripe\Exception\ApiErrorException
	 * @since 1.0.0
	 */
	public function set_default_card( $customer, $card_id ) {
		$result = $this->update_customer( $customer, array(
			'default_source' => $card_id
		) );

		do_action( 'yith_wcstripe_card_set_default', $customer, $card_id );

		return $result;
	}

	/**
	 * Delete a card
	 *
	 * @param $customer
	 * @param $params
	 *
	 * @return Customer
	 *
	 * @throws \Stripe\Exception\ApiErrorException
	 * @since 1.0.0
	 */
	public function get_cards( $customer, $params = array( 'limit' => 100 ) ) {
		$customer = $this->get_customer( $customer );

		return $customer->sources->all( $params )->data;
	}

	/**
	 * Retrieve a card object for the customer
	 *
	 * @param $customer Customer object or ID
	 * @param $card_id
	 *
	 * @return Customer
	 *
	 * @throws \Stripe\Exception\ApiErrorException
	 * @since 1.0.0
	 */
	public function get_card( $customer, $card_id, $params = array() ) {
		$customer = $this->get_customer( $customer );

		$card = $customer->sources->retrieve( $card_id, $params );

		return $card;
	}

	/* === BILLING METHODS === */

	/**
	 * Retrieve product
	 *
	 * @param $product Product|string Product object or ID
	 *
	 * @return Product
	 * @throws \Stripe\Exception\ApiErrorException
	 * @since 1.5.1
	 */
	public function get_product( $product ) {
		if ( is_a( $product, '\Stripe\Product' ) ) {
			return $product;
		}

		return Product::retrieve( $product );
	}

	/**
	 * Create a plan
	 *
	 * @param array $params
	 *
	 * @return Plan
	 * @throws \Stripe\Exception\ApiErrorException
	 */
	public function create_plan( $params = array() ) {
		return Plan::create( $params );
	}

	/**
	 * Create a plan
	 *
	 * @param $plan_id
	 *
	 * @return Plan
	 *
	 * @throws \Stripe\Exception\ApiErrorException
	 */
	public function delete_plan( $plan_id ) {
		$plan = $this->get_plan( $plan_id );

		return $plan->delete();
	}

	/**
	 * Get a plan
	 *
	 * @param $plan_id
	 *
	 * @return Plan|bool
	 */
	public function get_plan( $plan_id ) {
		try {
			return Plan::retrieve( $plan_id );
		} catch ( \Stripe\Exception\ApiErrorException $e ) {
			return false;
		}
	}

	/**
	 * Create an invoice
	 *
	 * @param $customer Customer Customer object
	 * @param $params   array Array of parameters
	 *
	 * @return \Stripe\Invoice
	 * @throws \Stripe\Exception\ApiErrorException
	 */
	public function create_invoice( $customer, $params = array() ) {
		$customer = $this->get_customer( $customer );

		return Invoice::create( array_merge( array( 'customer' => $customer->id ), $params ) );
	}

	/**
	 * Create an invoice item
	 *
	 * @param $customer Customer Customer object
	 * @param $params   array Array of parameters
	 *
	 * @return \Stripe\InvoiceItem
	 * @throws \Stripe\Exception\ApiErrorException
	 */
	public function create_invoice_item( $customer, $params = array() ) {
		$customer = $this->get_customer( $customer );

		return InvoiceItem::create( array_merge( array( 'customer' => $customer->id ), $params ) );
	}

	/**
	 * Create a subscription
	 *
	 * @param $customer
	 * @param $plan_id
	 *
	 * @return Subscription
	 * @throws \Stripe\Exception\ApiErrorException
	 */
	public function create_subscription( $customer, $plan_id, $params = array() ) {
		$customer = $this->get_customer( $customer );

		return $customer->subscriptions->create( array_merge( array( "plan" => $plan_id ), $params ) );
	}

	/**
	 * Create a subscription
	 *
	 * @param $customer
	 * @param $subscription_id
	 *
	 * @return Subscription
	 * @throws \Stripe\Exception\ApiErrorException
	 */
	public function get_subscription( $customer, $subscription_id ) {
		$customer = $this->get_customer( $customer );

		return $customer->subscriptions->retrieve( $subscription_id );
	}

	/**
	 * Retrieves subscriptions for a specific customer
	 *
	 * @param $customer
	 * @param $param
	 *
	 * @return \Stripe\Collection
	 * @throws \Stripe\Exception\ApiErrorException
	 */
	public function get_subscriptions( $customer, $params = array() ) {
		$params = array_merge(
			$params,
			array( 'customer' => $customer )
		);

		return Subscription::all( $params );
	}

	/**
	 * Modify a subscription on stripe
	 *
	 * @param $customer
	 * @param $subscription_id
	 *
	 * @return Subscription
	 * @throws \Stripe\Exception\ApiErrorException
	 */
	public function update_subscription( $customer, $subscription_id, $params = array() ) {
		$subscription = $this->get_subscription( $customer, $subscription_id );

		foreach ( $params as $param => $value ) {
			// TODO find a better way to check for valid properties to set within subscription
			if ( in_array( $param, array(
				'id',
				'object',
				'application_fee_percent',
				'billing',
				'billing_cycle_anchor',
				'cancel_at_period_end',
				'canceled_at',
				'created',
				'urrent_period_end',
				'urrent_period_start',
				'customer',
				'days_until_due',
				'discount',
				'ended_at',
				'items',
				'livemode',
				'metadata',
				'plan',
				'quantity',
				'start',
				'status',
				'tax_percent',
				'trial_end',
				'trial_start '
			) ) ) {
				$subscription->{$param} = $value;
			}
		}

		$subscription->save();

		return $subscription;
	}

	/**
	 * Cancel a subscription
	 *
	 * @param $customer
	 * @param $subscription_id
	 * @param $params
	 *
	 * @return Subscription
	 * @throws \Stripe\Exception\ApiErrorException
	 */
	public function cancel_subscription( $customer, $subscription_id, $params = array() ) {
		$subscription = $this->get_subscription( $customer, $subscription_id );

		return $subscription->cancel( $params );
	}

	/**
	 * Get an invoice for subscription
	 *
	 * @param $invoice_id
	 *
	 * @return Invoice
	 * @throws \Stripe\Exception\ApiErrorException
	 * @since 1.0.0
	 */
	public function get_invoice( $invoice_id ) {
		return Invoice::retrieve( $invoice_id );
	}

	/**
	 * Pay an invoice for subscription
	 *
	 * @param $invoice_id
	 *
	 * @return Invoice
	 * @throws \Stripe\Exception\ApiErrorException
	 * @since 1.0.0
	 */
	public function pay_invoice( $invoice_id ) {
		$invoice = $this->get_invoice( $invoice_id );
		$invoice->pay();

		return $invoice;
	}

	/* === PAYMENT INTENTS METHODS === */

	/**
	 * Retrieve a payment intent object on stripe, using id passed as argument
	 *
	 * @param $payment_intent_id int Payment intent id
	 *
	 * @return \Stripe\StripeObject|bool Payment intent or false
	 */
	public function get_intent( $payment_intent_id ) {
		try {
			return PaymentIntent::retrieve( $payment_intent_id );
		} catch ( \Stripe\Exception\ApiErrorException $e ) {
			return false;
		}
	}

	/**
	 * Create a payment intent object on stripe, using parameters passed as argument
	 *
	 * @param $params array Array of parameters used to create Payment intent
	 *
	 * @return \Stripe\StripeObject|bool Brand new payment intent or false on failure
	 * @throws \Stripe\Exception\ApiErrorException
	 */
	public function create_intent( $params ) {
		return PaymentIntent::create( $params, array(
			'idempotency_key' => self::generateRandomString()
		) );
	}

	/**
	 * Update a payment intent object on stripe, using parameters passed as argument
	 *
	 * @param $params array Array of parameters used to update Payment intent
	 *
	 * @return \Stripe\StripeObject|bool Updated payment intent or false on failure
	 */
	public function update_intent( $payment_intent_id, $params ) {
		try {
			return PaymentIntent::update( $payment_intent_id, $params );
		} catch ( \Stripe\Exception\ApiErrorException $e ) {
			return false;
		}
	}

	/**
	 * Return all payments method for a csutomer
	 *
	 * @param $customer
	 *
	 * @return \Stripe\Collection|bool
	 */
	public function get_payment_methods( $customer ) {
		try {
			$customer = $this->get_customer( $customer );

			return \Stripe\PaymentMethod::all( array(
				'customer' => $customer->id,
				'type'     => 'card'
			) )->data;
		} catch ( Exception $e ) {
			return false;
		}

	}

	/**
	 * Retrieve a payment method object on stripe, using id passed as argument
	 *
	 * @param $payment_method_id int Payment method id
	 *
	 * @return \Stripe\StripeObject|bool Payment intent or false
	 */
	public function get_payment_method( $payment_method_id ) {
		try {
			return PaymentMethod::retrieve( $payment_method_id );
		} catch ( \Stripe\Exception\ApiErrorException $e ) {
			return false;
		}
	}

	/**
	 * Detach a payment method from the customer
	 *
	 * @param $payment_method_id string Payment method id
	 *
	 * @return StripeObject|bool Detached payment method, or false on failure
	 */
	public function delete_payment_method( $payment_method_id ) {
		try {
			return PaymentMethod::retrieve( $payment_method_id )->detach();
		} catch ( \Stripe\Exception\ApiErrorException $e ) {
			return false;
		}
	}

	/**
	 * Retrieve a setup intent object on stripe, using id passed as argument
	 *
	 * @param $payment_intent_id int Setup intent id
	 *
	 * @return \Stripe\StripeObject|bool Setup intent or false
	 */
	public function get_setup_intent( $setup_intent_id ) {
		try {
			return SetupIntent::retrieve( $setup_intent_id );
		} catch ( \Stripe\Exception\ApiErrorException $e ) {
			return false;
		}
	}

	/**
	 * Create a payment intent object on stripe, using parameters passed as argument
	 *
	 * @param $params array Array of parameters used to create Payment intent
	 *
	 * @return \Stripe\StripeObject|bool Brand new payment intent or false on failure
	 */
	public function create_setup_intent( $params ) {
		try {
			return SetupIntent::create( $params, array(
				'idempotency_key' => self::generateRandomString()
			) );
		} catch ( \Stripe\Exception\ApiErrorException $e ) {
			return false;
		}
	}

	/**
	 * Update a setup intent object on stripe, using parameters passed as argument
	 *
	 * @param $params array Array of parameters used to update Payment intent
	 *
	 * @return \Stripe\StripeObject|bool Updated payment intent or false on failure
	 */
	public function update_setup_intent( $setup_intent_id, $params ) {
		try {
			return SetupIntent::update( $setup_intent_id, $params );
		} catch ( \Stripe\Exception\ApiErrorException $e ) {
			return false;
		}
	}

	/**
	 * Retrieve a PaymentIntent or a SetupIntent, depending on the id that it receives
	 *
	 * @param $id string Id of the intent that method should retrieve
	 *
	 * @return \Stripe\StripeObject|bool Intent or false on failure
	 */
	public function get_correct_intent( $id ) {
		try {
			if ( strpos( $id, 'seti' ) !== false ) {
				return $this->get_setup_intent( $id );
			} else {
				return $this->get_intent( $id );
			}
		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Update a PaymentIntent or a SetupIntent, depending on the id that it receives
	 *
	 * @param $id     string Id of the intent that method should retrieve
	 * @param $params array Array of parameters that should be used to update intent
	 *
	 * @return \Stripe\StripeObject|bool Intent or false on failure
	 */
	public function update_correct_intent( $id, $params ) {
		try {
			if ( strpos( $id, 'seti' ) !== false ) {
				return $this->update_setup_intent( $id, $params );
			} else {
				return $this->update_intent( $id, $params );
			}
		} catch ( Exception $e ) {
			return false;
		}
	}

	/* === SESSION METHODS === */

	/**
	 * Retrieves a payment session by session id
	 *
	 * @param $session_id string Session id
	 *
	 * @return \Stripe\StripeObject|bool Session object, or false on failure
	 */
	public function get_session( $session_id ) {
		try {
			return Session::retrieve( $session_id );
		} catch ( \Stripe\Exception\ApiErrorException $e ) {
			return false;
		}
	}

	/**
	 * Create checkout session, used by Stripe Checkout to process payment
	 *
	 * @param $params array Array of parameters used to create session
	 *
	 * @return \Stripe\StripeObject|bool Session created, or false on failure
	 */
	public function create_session( $params ) {
		try {
			return Session::create( $params );
		} catch ( \Stripe\Exception\ApiErrorException $e ) {
			return false;
		}
	}

	/* === MISC METHODS === */

	/**
	 * Retrieve an event from event ID
	 *
	 * @param $event_id string
	 *
	 * @return \Stripe\Event
	 * @throws \Stripe\Exception\ApiErrorException
	 */
	public function get_event( $event_id ) {
		return Event::retrieve( $event_id );
	}

	/**
	 * Create webhook on Stripe
	 *
	 * @param $params array Parameters for webhook creations
	 *
	 * @return \Stripe\WebhookEndpoint
	 * @throws \Stripe\Exception\ApiErrorException
	 */
	public function create_webhook( $params ) {
		return WebhookEndpoint::create( $params );
	}

	/**
	 * Genereate a semi-random string
	 *
	 * @since 1.0.0
	 */
	protected static function generateRandomString( $length = 24 ) {
		$characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTU';
		$charactersLength = strlen( $characters );
		$randomString     = '';
		for ( $i = 0; $i < $length; $i ++ ) {
			$randomString .= $characters[ rand( 0, $charactersLength - 1 ) ];
		}

		return $randomString;
	}

}