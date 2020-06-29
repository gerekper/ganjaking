<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Stripe
 * @version 1.0.0
 */

use \Stripe\PaymentIntent;
use \Stripe\SetupIntent;

if ( ! defined( 'YITH_WCSTRIPE' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCStripe_Gateway_Advanced' ) ) {
	/**
	 * WooCommerce Stripe gateway class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCStripe_Gateway_Advanced extends YITH_WCStripe_Gateway {

		/**
		 * @var string $save_cards (yes|no)
		 */
		public $save_cards;

		/**
		 * @var string $capture (yes|no)
		 */
		public $capture;

		/**
		 * @var string $debug (yes|no)
		 */
		public $debug;

		/**
		 * @var string $bitcoin (yes|no)
		 */
		public $bitcoin;

		/**
		 * @var bool $add_billing_fields
		 */
		public $add_billing_fields;

		/**
		 * @var bool $hosted_billing
		 */
		public $hosted_billing;

		/**
		 * @var bool $elements_show_zip
		 */
		public $elements_show_zip;

		/**
		 * @var bool $show_name_on_card
		 */
		public $show_name_on_card;

		/**
		 * @var string $save_cards_mode (prompt|register)
		 */
		public $save_cards_mode;

		/**
		 * @var $_current_customer \Stripe\Customer
		 */
		protected $_current_customer = null;

		/**
		 * @var $_current_intent_secret string
		 */
		protected $_current_intent_secret = null;

		/**
		 * Constructor.
		 *
		 * @return \YITH_WCStripe_Gateway_Advanced
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct();

			// gateway properties
			$this->order_button_text = $this->get_option( 'button_label', __( 'Place order', 'yith-woocommerce-stripe' ) );
			$this->new_method_label  = __( 'Use a new card', 'yith-woocommerce-stripe' );
			$this->supports          = array(
				'products',
				'default_credit_card_form',
				'refunds'
			);

			// Add premium options
			$this->init_premium_fields();

			// Define user set variables
			$this->mode               = $this->get_option( 'mode', 'standard' );
			$this->debug              = strcmp( $this->get_option( 'debug' ), 'yes' ) == 0;
			$this->save_cards         = strcmp( $this->get_option( 'save_cards', 'yes' ), 'yes' ) == 0;
			$this->save_cards_mode    = $this->get_option( 'save_cards_mode', 'register' );
			$this->capture            = strcmp( $this->get_option( 'capture', 'no' ), 'yes' ) == 0;
			$this->add_billing_fields = strcmp( $this->get_option( 'add_billing_fields', 'no' ), 'yes' ) == 0;
			$this->hosted_billing     = strcmp( $this->get_option( 'add_billing_hosted_fields', 'no' ), 'yes' ) == 0;
			$this->show_name_on_card  = strcmp( $this->get_option( 'show_name_on_card', 'yes' ), 'yes' ) == 0;
			$this->elements_show_zip  = strcmp( $this->get_option( 'elements_show_zip', 'yes' ), 'yes' ) == 0;
			$this->renew_mode         = $this->get_option( 'renew_mode', 'stripe' );

			if ( $this->mode == 'hosted_std' ) {
				$this->update_option( 'mode', 'hosted' );
				$this->mode = 'hosted';
			}

			// enable tokenization support if the option is enabled
			if ( in_array( $this->mode, array( 'standard', 'elements' ) ) && $this->save_cards ) {
				$this->supports[] = 'tokenization';
			}

			// Logs
			if ( $this->debug ) {
				$this->log = new WC_Logger();
			}

			// hooks
			add_filter( 'woocommerce_credit_card_form_fields', array( $this, 'credit_form_add_fields' ), 10, 2 );
			add_filter( 'wc_payment_token_display_name', array( $this, 'token_display_name' ), 10, 2 );
		}

		/* === GATEWAY METHODS === */

		/**
		 * Initialize form fields for the admin
		 *
		 * @since 1.0.0
		 */
		public function init_premium_fields() {

			$this->add_form_field( array(
				'capture' => array(
					'title'       => __( 'Capture', 'yith-woocommerce-stripe' ),
					'type'        => 'select',
					'description' => sprintf( __( 'Decide whether to immediately capture the charge or not. When "Authorize only & Capture later" is selected, the charge issues an authorization (or pre-authorization), and will be captured later. Uncaptured charges expire in %2$s7 days%3$s. %1$sFor further information, see %4$sauthorizing charges and settling later%5$s.', 'yith-woocommerce-stripe' ),
						'<br />',
						'<b>',
						'</b>',
						'<a href="https://support.stripe.com/questions/can-i-authorize-a-charge-and-then-wait-to-settle-it-later" target="_blank">',
						'</a>'
					),
					'default'     => 'no',
					'options'     => array(
						'no'  => __( 'Authorize only & Capture later', 'yith-woocommerce-stripe' ),
						'yes' => __( 'Authorize & Capture immediately', 'yith-woocommerce-stripe' )
					)
				),

				'mode' => array(
					'title'       => __( 'Payment Mode', 'yith-woocommerce-stripe' ),
					'type'        => 'select',
					'description' => sprintf( __( 'Standard will display credit card fields on your store (SSL required). %1$s Stripe checkout will redirect the user to the checkout page hosted in Stripe. %1$s Elements will show an embedded form handled by Stripe', 'yith-woocommerce-stripe' ), '<br />' ),
					'default'     => 'standard',
					'options'     => array(
						'standard' => __( 'Standard', 'yith-woocommerce-stripe' ),
						'hosted'   => __( 'Stripe Checkout', 'yith-woocommerce-stripe' ),
						'elements' => __( 'Stripe Elements', 'yith-woocommerce-stripe' )
					)
				),

				'save_cards' => array(
					'title'       => __( 'Save cards', 'yith-woocommerce-stripe' ),
					'type'        => 'checkbox',
					'label'       => __( 'Enable "Remember cards"', 'yith-woocommerce-stripe' ),
					'description' => __( "Save users' credit cards to let them use them for future payments.", 'yith-woocommerce-stripe' ),
					'default'     => 'yes'
				),

				'save_cards_mode' => array(
					'title'       => __( 'Card registration mode', 'yith-woocommerce-stripe' ),
					'type'        => 'select',
					'description' => sprintf( __( 'If you choose to automatically register cards, every card used by the customer will be registered automatically %1$s Otherwise, system will register cards only when customer mark "Save card" checkbox %1$s Please note that this option does not affect Stripe, that register cards for internal processing anyway', 'yith-woocommerce-stripe' ), '<br />' ),
					'default'     => 'standard',
					'options'     => array(
						'register' => __( 'Register automatically', 'yith-woocommerce-stripe' ),
						'prompt'   => __( 'Let user choose', 'yith-woocommerce-stripe' )
					)
				),

				'add_billing_hosted_fields' => array(
					'title'       => __( 'Add billing fields for Stripe Checkout', 'yith-woocommerce-stripe' ),
					'type'        => 'checkbox',
					'description' => __( "Option available only for \"Stripe Checkout\" payment mode.", 'yith-woocommerce-stripe' ),
					'default'     => 'no',
					'class'       => 'yith-billing'
				),

				'add_billing_fields' => array(
					'title'       => __( 'Add billing fields', 'yith-woocommerce-stripe' ),
					'type'        => 'checkbox',
					'description' => __( 'If you have installed any WooCommerce extension to edit checkout fields, this option allows you require some necessary information associated to the credit card, in order to further reduce the risk of fraudulent transactions.', 'yith-woocommerce-stripe' ),
					'default'     => 'no'
				),

				'show_name_on_card' => array(
					'title'       => __( 'Show Name on Card', 'yith-woocommerce-stripe' ),
					'type'        => 'checkbox',
					'description' => __( 'Show Name on Card field in Elements and Standard form; Name will be sent within card data, to let Stripe perform additional check over user and better evaluate risk', 'yith-woocommerce-stripe' ),
					'default'     => 'yes'
				),

				'elements_show_zip' => array(
					'title'       => __( 'Show Zip Field', 'yith-woocommerce-stripe' ),
					'type'        => 'checkbox',
					'description' => __( 'Show Zip field in Elements form; ZIP code will be sent within card data, to let Stripe perform additional check over user and better evaluate risk', 'yith-woocommerce-stripe' ),
					'default'     => 'yes'
				)
			), 'after', 'description' );

			if ( defined( 'YITH_YWSBS_PREMIUM' ) && defined( 'YITH_YWSBS_VERSION' ) && version_compare( YITH_YWSBS_VERSION, '1.4.6', '>=' ) ) {
				$this->add_form_field( array(
					'subscription' => array(
						'title'       => __( 'Subscriptions', 'yith-woocommerce-stripe' ),
						'type'        => 'title',
						'description' => __( 'Choose option to integrate Stripe gateway with YITH WooCommerce Subscription Premium', 'yith-woocommerce-stripe' ),
					),

					'renew_mode' => array(
						'title'       => __( 'Subscriptions\' renew mode', 'yith-woocommerce-stripe' ),
						'type'        => 'select',
						'description' => sprintf( __( 'Select how you want to process Subscriptions\' renews. %1$s Stripe Classic will create subscriptions on Stripe side, and let Stripe manage renews automatically. %1$s YWSBS Renews will pay renews when YITH WooCommerce Subscription triggers them; this grants more flexibility.', 'yith-woocommerce-stripe' ), '<br/>' ),
						'default'     => 'stripe',
						'options'     => array(
							'stripe' => __( 'Stripe Classic', 'yith-woocommerce-stripe' ),
							'ywsbs'  => __( 'YWSBS Renews', 'yith-woocommerce-stripe' )
						)
					),

					'retry_with_other_cards' => array(
						'title'       => __( 'When renew fails, try again with other cards', 'yith-woocommerce-stripe' ),
						'type'        => 'checkbox',
						'description' => __( 'When a renew fails, and customer have additional cards registered, try to process payment with other cards, before giving up', 'yith-woocommerce-stripe' ),
						'default'     => 'no'
					)

				), 'after', 'elements_show_zip' );
			}

			$this->add_form_field( array(
				'button_label' => array(
					'title'       => __( 'Button label', 'yith-woocommerce-stripe' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => __( 'Define the label for the button on checkout.', 'yith-woocommerce-stripe' ),
					'default'     => __( 'Placeholder.', 'yith-woocommerce-stripe' )
				),
			), 'after', 'customization' );

			$this->add_form_field( array(
				'debug' => array(
					'title'       => __( 'Debug Log', 'yith-woocommerce-stripe' ),
					'type'        => 'checkbox',
					'label'       => __( 'Enable logging', 'yith-woocommerce-stripe' ),
					'default'     => 'no',
					'description' => sprintf( __( 'Log Stripe events inside <code>%s</code>', 'yith-woocommerce-stripe' ), wc_get_log_file_path( 'stripe' ) ) . '<br />' . sprintf( __( 'You can also consult the logs in your <a href="%s">Logs Dashboard</a>, without checking this option.', 'yith-woocommerce-stripe' ), 'https://dashboard.stripe.com/logs' )
				),
			), 'after', 'enabled_test_mode' );

			$webhook_already_processed = get_option( "yith_wcstripe_{$this->env}_webhook_processed", false );
			$generate_webhook_text     = $webhook_already_processed ?
				sprintf( __( 'You already configured your webhook for %s environment. If you want to process them again use the following shortcut: ', 'yith-woocommerce-stripe' ), $this->env ) . '<button id="config_webhook" class="button-secondary" style="vertical-align: middle; margin-left: 15px;">' . __( 'Configure Webhooks', 'yith-woocommerce-stripe' ) . '</button>' :
				sprintf( __( 'You can automatically configure your webhooks for %s environment by using the following shortcut: ', 'yith-woocommerce-stripe' ), $this->env ) . '<button id="config_webhook" class="button-secondary" style="vertical-align: middle; margin-left: 15px;">' . __( 'Configure Webhooks', 'yith-woocommerce-stripe' ) . '</button>';

			$this->add_form_field( array(
				'webhooks' => array(
					'title'       => __( 'Config Webhooks', 'yith-woocommerce-stripe' ),
					'type'        => 'title',
					'description' => sprintf( __( 'You can configure the webhook url %s in your <a href="%s">developers settings</a>. All the webhooks for your account will be sent to this endpoint.', 'yith-woocommerce-stripe' ), '<code>' . esc_url( add_query_arg( 'wc-api', 'stripe_webhook', site_url( '/' ) ) ) . '</code>', 'https://dashboard.stripe.com/account/webhooks' ) . '<br /><br />'
									 . __( "It's important to note that only test webhooks will be sent to your development webhook url. Yet, if you are working on a live website, <b>both live and test</b> webhooks will be sent to your production webhook URL. This is due to the fact that you can create both live and test objects under a production application.", 'yith-woocommerce-stripe' ) . '<br /><br />'
									 . sprintf( __( 'For more information about webhooks, see the <a href="%s">webhook documentation</a>', 'yith-woocommerce-stripe' ), 'https://stripe.com/docs/webhooks' ) . '<br /><br />'
									 . $generate_webhook_text
				),
			), 'after', 'live_publishable_key' );

			$this->add_form_field( array(
				'security'         => array(
					'title'       => __( 'Security', 'yith-woocommerce-stripe' ),
					'type'        => 'title',
					'description' => __( 'Enable here the testing mode, to debug the payment system before going into production', 'yith-woocommerce-stripe' ),
				),
				'enable_blacklist' => array(
					'title'   => __( 'Enable Blacklist', 'yith-woocommerce-stripe' ),
					'type'    => 'checkbox',
					'label'   => __( 'Hide gateway payment on frontend if the same user or the same IP address have already failed a payment. The blacklist table is available on WooCommerce -> Stripe Blacklist', 'yith-woocommerce-stripe' ),
					'default' => 'no'
				),
			), 'after', 'modal_image' );

		}

		/**
		 * Get return url for payment intent
		 *
		 * @param $order \WC_Order Order
		 *
		 * @return string Return url
		 */
		public function get_return_url( $order = null ) {
			$redirect = parent::get_return_url( $order );

			if ( ! $order || empty( $this->_current_intent_secret ) ) {
				return $redirect;
			}

			// Put the final thank you page redirect into the verification URL.
			$verification_url = add_query_arg(
				array(
					'order'       => $order->get_id(),
					'redirect_to' => rawurlencode( $redirect ),
				),
				WC_AJAX::get_endpoint( 'yith_wcstripe_verify_intent' )
			);

			// Combine into a hash.
			$redirect = sprintf( '#yith-confirm-pi-%s:%s', $this->_current_intent_secret, $verification_url );

			return $redirect;
		}

		/* === PAYMENT METHODS === */

		/**
		 * Handling payment and processing the order.
		 *
		 * @param int $order_id
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function process_payment( $order_id ) {
			$order                = wc_get_order( $order_id );
			$this->_current_order = $order;
			$this->log( 'Generating payment form for order ' . $order->get_order_number() . '.' );

			if ( 'hosted_std' == $this->mode || 'hosted' == $this->mode ) {
				return $this->process_hosted_payment();
			} else {
				return $this->process_standard_payment();
			}
		}

		/**
		 * Process refund
		 *
		 * Overriding refund method
		 *
		 * @access      public
		 *
		 * @param int    $order_id
		 * @param float  $amount
		 * @param string $reason
		 *
		 * @return      mixed True or False based on success, or WP_Error
		 */
		public function process_refund( $order_id, $amount = null, $reason = '' ) {
			$order          = wc_get_order( $order_id );
			$transaction_id = $order->get_transaction_id();
			$order_currency = $this->get_currency( $order );

			if ( ! $transaction_id ) {
				return new WP_Error( 'yith_stripe_no_transaction_id',
					sprintf(
						__( "There isn't any charge linked to this order", 'yith-woocommerce-stripe' )
					)
				);
			}

			if ( yit_get_prop( $order, 'bitcoin_inbound_address' ) || yit_get_prop( $order, 'bitcoin_uri' ) ) {
				return new WP_Error( 'yith_stripe_no_bitcoin',
					sprintf(
						__( "Refund not supported for bitcoin", 'yith-woocommerce-stripe' )
					)
				);
			}

			try {

				// Initializate SDK and set private key
				$this->init_stripe_sdk();

				$params = array();

				// get the last refund object created before to process this method, to get own object
				$refunds = $order->get_refunds();
				$refund  = array_shift( $refunds );

				// If the amount is set, refund that amount, otherwise the entire amount is refunded
				if ( $amount ) {
					$params['amount'] = YITH_WCStripe::get_amount( $amount, $order_currency, $order );
				}

				// If a reason is provided, add it to the Stripe metadata for the refund
				if ( $reason && in_array( $reason, array( 'duplicate', 'fraudulent', 'requested_by_customer' ) ) ) {
					$params['reason'] = $reason;
				}

				$this->log( 'Stripe Refund Request: ' . print_r( $params, true ) );

				// Send the refund to the Stripe API
				$stripe_refund = $this->api->refund( $transaction_id, $params );
				yit_save_prop( $refund, '_refund_stripe_id', $stripe_refund->id );

				$this->log( 'Stripe Refund Response: ' . print_r( $stripe_refund, true ) );

				$order->add_order_note( sprintf( __( 'Refunded %1$s - Refund ID: %2$s', 'woocommerce' ), $amount, $stripe_refund['id'] ) );

				return true;

			} catch ( Stripe\Exception\ApiErrorException $e ) {
				$message = $this->error_handling( $e, array(
					'mode'   => 'note',
					'order'  => $order,
					'format' => __( 'Stripe Credit Card Refund Failed with message: "%s"', 'yith-woocommerce-stripe' )
				) );

				// Something failed somewhere, send a message.
				return new WP_Error( 'yith_stripe_refund_error', $message );
			}
		}

		/**
		 * Handling payment and processing the order.
		 *
		 * @param WC_Order $order
		 *
		 * @return array
		 * @since 1.0.0
		 */
		protected function process_standard_payment( $order = null ) {
			if ( empty( $order ) ) {
				$order = $this->_current_order;
			}

			try {

				// Initialize SDK and set private key
				$this->init_stripe_sdk();

				// retrieve payment intent
				$intent = $this->get_intent( $order );

				// no intent yet; return error
				if ( ! $intent ) {
					throw new Exception( __( 'Sorry, There was an error while processing payment; please, try again', 'yith-woocommerce-stripe' ), null );
				}

				$payment_method = isset( $_POST['stripe_payment_method'] ) ? sanitize_text_field( $_POST['stripe_payment_method'] ) : false;

				if ( ! $payment_method && isset( $_POST['wc-yith-stripe-payment-token'] ) && 'new' !== $_POST['wc-yith-stripe-payment-token'] ) {
					$token_id = intval( $_POST['wc-yith-stripe-payment-token'] );
					$token    = WC_Payment_Tokens::get( $token_id );

					if ( $token && $token->get_user_id() == get_current_user_id() && $token->get_gateway_id() == $this->id ) {
						$payment_method = $token->get_token();
					}
				}

				// it intent is missing payment method, or requires update, proceed with update.
				if (
					'requires_payment_method' == $intent->status && $payment_method ||
					(
						(
							YITH_WCStripe::get_amount( $order->get_total(), $order->get_currency(), $order ) != $intent->amount ||
							strtolower( $order->get_currency() ) != $intent->currency ||
							$order->get_id() != $intent->metadata->order_id
						) &&
						! in_array( $intent->status, array( 'requires_action', 'requires_capture', 'succeeded', 'canceled' ) )
					)
				) {
					// updates session intent.
					$intent = $this->update_session_intent( $payment_method, $order->get_id() );
				}

				// if intent is still missing payment method, return an error.
				if ( $intent->status == 'requires_payment_method' ) {
					throw new Exception( __( 'No payment method could be applied to this payment; please try again selecting another payment method', 'yith-woocommerce-stripe' ) );
				}

				// intent requires confirmation; try to confirm it
				if ( $intent->status == 'requires_confirmation' ) {
					$intent->confirm();
				}

				// register intent for the order
				$order->update_meta_data( 'intent_id', $intent->id );

				// confirmation requires additional action; return to customer
				if ( $intent->status == 'requires_action' ) {
					$order->save();

					// manual confirm after checkout
					$this->_current_intent_secret = $intent->client_secret;

					return array(
						'result'   => 'success',
						'redirect' => $this->get_return_url( $order )
					);
				}

				// everything done with the intent (payment has been approved); try to pay
				$response = $this->pay( $order );

				if ( $response === true ) {
					$response = array(
						'result'   => 'success',
						'redirect' => $this->get_return_url( $order )
					);

				} elseif ( is_a( $response, 'WP_Error' ) ) {
					throw new Exception( $response->get_error_message( 'stripe_error' ) );
				}

				return $response;

			} catch ( Stripe\Exception\ApiErrorException $e ) {
				$this->error_handling( $e, array(
					'mode'  => 'both',
					'order' => $order
				) );

				return array(
					'result'   => 'fail',
					'redirect' => ''
				);
			} catch ( Exception $e ) {
				wc_add_notice( $e->getMessage(), 'error' );

				return array(
					'result'   => 'fail',
					'redirect' => ''
				);
			}
		}

		/**
		 * Performs the payment on Stripe
		 *
		 * @param $order  WC_Order
		 * @param $amount float Amount to pay; if null, order total will be used instead
		 *
		 * @return bool|WP_Error
		 * @throws Stripe\Exception\ApiErrorException|Exception
		 * @since 1.0.0
		 */
		public function pay( $order = null, $amount = null ) {
			// Initialize SDK and set private key
			$this->init_stripe_sdk();

			$order_id = yit_get_order_id( $order );

			// get amount
			$amount = ! is_null( $amount ) ? $amount : $order->get_total();

			if ( 0 == $amount ) {
				// Payment complete
				$order->payment_complete();

				return true;
			}

			// retrieve payment intent
			$intent = $this->get_intent( $order );

			if ( ! $intent ) {
				return new WP_Error( 'stripe_error', __( 'Sorry, There was an error while processing payment; please, try again', 'yith-woocommerce-stripe' ) );
			}

			if ( $intent->status == 'requires_confirmation' ) {
				$intent->confirm();
			}

			if ( $intent->status == 'requires_action' ) {
				do_action( 'yith_wcstripe_intent_requires_action', $intent, $order );

				return new WP_Error( 'stripe_error', __( 'Please, validate your payment method before proceeding further; in order to do this, refresh the page and proceed at checkout as usual', 'yith-woocommerce-stripe' ) );
			} elseif ( ! in_array( $intent->status, array( 'succeeded', 'requires_capture' ) ) ) {
				return new WP_Error( 'stripe_error', __( 'Sorry, There was an error while processing payment; please, try again', 'yith-woocommerce-stripe' ) );
			}

			// register intent for the order
			$order->update_meta_data( 'intent_id', $intent->id );

			// update intent data
			if ( ! isset( $intent->metadata ) || empty( $intent->metadata->order_id ) ) {
				$this->api->update_intent( $intent->id, array(
					'description' => apply_filters( 'yith_wcstripe_charge_description', sprintf( __( '%s - Order %s', 'yith-woocommerce-stripe' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ),
					'metadata'    => apply_filters( 'yith_wcstripe_metadata', array(
						'order_id'    => $order_id,
						'order_email' => yit_get_prop( $order, 'billing_email' ),
						'instance'    => $this->instance,
					), 'charge' )
				) );
			}

			// retrieve charge to use for next steps
			$charge = end( $intent->charges->data );

			// attach payment method to customer
			$customer = $this->get_customer( $order );

			// save card token
			$token = $this->save_token( $intent->payment_method );

			if ( $token ) {
				$order->add_payment_token( $token );
			}

			// Payment complete
			$order->payment_complete( $charge->id );

			// Add order note
			$order->add_order_note( sprintf( __( 'Stripe payment approved (ID: %s)', 'yith-woocommerce-stripe' ), $charge->id ) );

			// Remove cart
			WC()->cart->empty_cart();

			// delete session
			$this->delete_session_intent();

			// update post meta
			yit_save_prop( $order, '_captured', ( $charge->captured ? 'yes' : 'no' ) );
			yit_save_prop( $order, '_stripe_customer_id', $customer ? $customer->id : false );

			// Return thank you page redirect
			return true;
		}

		/**
		 * Performs the payment on ajax call
		 *
		 * @param $order \WC_Order
		 *
		 * @return bool|WP_Error True or WP_Error with details about the error
		 */
		public function pay_ajax( $order ) {
			try {
				return self::pay( $order );
			} catch ( Exception $e ) {
				return new WP_Error( 'stripe_error', $e->getMessage() );
			}
		}

		/* === FRONTEND METHODS === */

		/**
		 * Javascript library
		 *
		 * @since 1.0.0
		 */
		public function payment_scripts() {
			$load_scripts = false;

			if ( $this->is_available() && ( is_checkout() || is_wc_endpoint_url( 'payment-methods' ) || is_wc_endpoint_url( 'add-payment-method' ) || apply_filters( 'yith_wcstripe_load_assets', false ) ) ) {
				$load_scripts = true;
			}

			if ( false === $load_scripts ) {
				return;
			}

			$suffix         = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$wc_assets_path = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';

			// style
			if ( in_array( $this->mode, array( 'standard', 'elements' ) ) ) {
				wp_register_style( 'stripe-css', YITH_WCSTRIPE_URL . 'assets/css/stripe.css', array(), YITH_WCSTRIPE_VERSION );
				wp_enqueue_style( 'stripe-css' );
			}

			if ( 'standard' == $this->mode ) {
				wp_enqueue_style( 'woocommerce_prettyPhoto_css', $wc_assets_path . 'css/prettyPhoto.css' );
				wp_enqueue_script( 'prettyPhoto', $wc_assets_path . 'js/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ), '3.1.5', true );
			}

			// scripts
			if ( 'hosted' == $this->mode || 'hosted_std' == $this->mode ) {
				wp_register_script( 'stripe-js', 'https://js.stripe.com/v3/', array( 'jquery' ), YITH_WCSTRIPE_VERSION, true );
				wp_register_script( 'yith-stripe-js', YITH_WCSTRIPE_URL . 'assets/js/stripe-checkout.js', array(
					'jquery',
					'jquery-blockui',
					'stripe-js'
				), YITH_WCSTRIPE_VERSION, true );
				wp_enqueue_script( 'yith-stripe-js' );

				wp_localize_script( 'yith-stripe-js', 'yith_stripe_info', array(
					'public_key' => $this->public_key,
					'mode'       => $this->mode,
					'ajaxurl'    => admin_url( 'admin-ajax.php' )
				) );
			} elseif ( 'elements' == $this->mode || 'standard' == $this->mode ) {
				global $wp;

				wp_register_script( 'stripe-js', 'https://js.stripe.com/v3/', array( 'jquery' ), YITH_WCSTRIPE_VERSION, true );
				wp_register_script( 'yith-stripe-js', YITH_WCSTRIPE_URL . 'assets/js/stripe-elements.js', array(
					'jquery',
					'stripe-js'
				), YITH_WCSTRIPE_VERSION, true );
				wp_enqueue_script( 'yith-stripe-js' );

				wp_localize_script( 'yith-stripe-js', 'yith_stripe_info', array(
					'public_key'            => $this->public_key,
					'mode'                  => $this->mode,
					'elements_container_id' => '#' . esc_attr( $this->id ) . '-card-elements',
					'currency'              => strtolower( $this->get_currency() ),
					'show_zip'              => $this->elements_show_zip,
					'ajaxurl'               => admin_url( 'admin-ajax.php' ),
					'is_checkout'           => is_checkout(),
					'refresh_intent'        => wp_create_nonce( 'refresh-intent' ),
					'order'                 => isset( $wp->query_vars['order-pay'] ) ? $wp->query_vars['order-pay'] : false
				) );
			}
		}

		/**
		 * Payment form on checkout page
		 *
		 * @since 1.0.0
		 */
		public function payment_fields() {
			$description = $this->get_description();

			if ( 'test' == $this->env ) {
				$description .= ' ' . sprintf( __( 'TEST MODE ENABLED. Use a test card: %s', 'yith-woocommerce-stripe' ), '<a href="https://stripe.com/docs/testing">https://stripe.com/docs/testing</a>' );
			}

			if ( $description ) {
				echo wpautop( wptexturize( trim( $description ) ) );
			}

			if ( in_array( $this->mode, array( 'standard', 'elements' ) ) ) {
				WC_Payment_Gateway_CC::payment_fields();
			}
		}

		/**
		 * Add checkbox to choose if save credit card or not
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function credit_form_add_fields( $fields, $id ) {
			if ( $id != $this->id ) {
				return $fields;
			}

			$fields = array( 'fields-container' => '<div class="' . esc_attr( $this->id ) . '-form-container ' . $this->mode . '">' );

			$form_row_first = ! wp_is_mobile() ? 'form-row-first' : '';
			$form_row_last  = ! wp_is_mobile() ? 'form-row-last' : '';

			if ( 'standard' == $this->mode ) {
				$fields = array_merge( $fields,
					$this->show_name_on_card ? array(
						'card-name-field' => '<p class="form-row ' . $form_row_first . ' ">
                            <label for="' . esc_attr( $this->id ) . '-card-name">' . apply_filters( 'yith_wcstripe_name_on_card_label', __( 'Name on Card', 'yith-woocommerce-stripe' ) ) . ' <span class="required">*</span></label>
                            <input id="' . esc_attr( $this->id ) . '-card-name" class="input-text wc-credit-card-form-card-name" type="text" autocomplete="off" placeholder="' . __( 'Name on Card', 'yith-woocommerce-stripe' ) . '" ' . $this->field_name( 'card-name' ) . ' />
                        </p>',
					) : array(),
					array(
						'card-number-field' => '<p class="form-row ' . ( $this->show_name_on_card ? $form_row_last : $form_row_first ) . '">
                            <label for="' . esc_attr( $this->id ) . '-card-number">' . apply_filters( 'yith_wcstripe_card_number_label', __( 'Card Number', 'yith-woocommerce-stripe' ) ) . ' <span class="required">*</span></label>
                            <input id="' . esc_attr( $this->id ) . '-card-number" class="input-text wc-credit-card-form-card-number" type="text" maxlength="20" autocomplete="off" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" ' . $this->field_name( 'card-number' ) . ' />
                        </p>',

						'card-expiry-field' => '<p class="form-row ' . ( $this->show_name_on_card ? $form_row_first : $form_row_last ) . '">
                            <label for="' . esc_attr( $this->id ) . '-card-expiry">' . apply_filters( 'yith_wcstripe_card_expiry_label', __( 'Expiration Date (MM/YY)', 'yith-woocommerce-stripe' ) ) . ' <span class="required">*</span></label>
                            <input id="' . esc_attr( $this->id ) . '-card-expiry" class="input-text wc-credit-card-form-card-expiry" type="text" maxlength="7" autocomplete="off" placeholder="' . esc_attr__( 'MM / YY', 'yith-woocommerce-stripe' ) . '" ' . $this->field_name( 'card-expiry' ) . ' />
                        </p>',
					)
				);

			} elseif ( 'elements' == $this->mode ) {
				$fields = array_merge( $fields,
					$this->show_name_on_card ? array(
						'card-name-field' => '<p class="form-row form-row-full">
                            <label for="' . esc_attr( $this->id ) . '-card-name">' . apply_filters( 'yith_wcstripe_name_on_card_label', __( 'Name on Card', 'yith-woocommerce-stripe' ) ) . ' <span class="required">*</span></label>
                            <input id="' . esc_attr( $this->id ) . '-card-name" class="input-text wc-credit-card-form-card-name" type="text" autocomplete="off" placeholder="' . __( 'Name on Card', 'yith-woocommerce-stripe' ) . '" ' . $this->field_name( 'card-name' ) . ' />
                        </p>',
					) : array(),
					array(
						'card-elements' => '<div class="form-row form-row-full">
                            <label for="' . esc_attr( $this->id ) . '-card-elements">' . apply_filters( 'yith_wcstripe_name_on_card_label', __( 'Card Details', 'yith-woocommerce-stripe' ) ) . ' <span class="required">*</span></label>
                            <div id="' . esc_attr( $this->id ) . '-card-elements"></div>
                        </div>',
					)
				);
			}

			// add cvc popup suggestion
			if ( 'standard' == $this->mode && ! $this->supports( 'credit_card_form_cvc_on_saved_method' ) ) {
				$fields['card-cvc-field'] = '<p class="form-row ' . ( $this->show_name_on_card ? $form_row_last : $form_row_first ) . '">
					<label for="' . esc_attr( $this->id ) . '-card-cvc">' . apply_filters( 'yith_wcstripe_card_cvc_label', __( 'Security Code', 'yith-woocommerce-stripe' ) ) . ' <span class="required">*</span> <a href="#cvv-suggestion" class="cvv2-help" rel="prettyPhoto">' . apply_filters( 'yith_wcstripe_what_is_my_cvv_label', __( 'What is my CVV code?', 'yith-woocommerce-stripe' ) ) . '</a></label>
					<input id="' . esc_attr( $this->id ) . '-card-cvc" class="input-text wc-credit-card-form-card-cvc" type="text" autocomplete="off" placeholder="' . esc_attr__( 'CVC', 'woocommerce' ) . '" ' . $this->field_name( 'card-cvc' ) . ' />
				</p>
				<div id="cvv-suggestion">
					<p style="font-size: 13px;">
						<strong>' . __( 'Visa&reg;, Mastercard&reg;, and Discover&reg; cardholders:', 'yith-woocommerce-stripe' ) . '</strong><br>
						<a href="//www.cvvnumber.com/" target="_blank"><img height="192" src="//www.cvvnumber.com/csc_1.gif" width="351" align="left" border="0" alt="cvv" style="width: 220px; height:auto;"></a>
						' . __( 'Turn your card over and look at the signature box. You should see either the entire 16-digit credit card number or just the last four digits followed by a special 3-digit code. This 3-digit code is your CVV number / Card Security Code.', 'yith-woocommerce-stripe' ) . '
					</p>
					<p>&nbsp;</p>
					<p style="font-size: 13px;">
						<strong>' . __( 'American Express&reg; cardholders:', 'yith-woocommerce-stripe' ) . '</strong><br>
						<a href="//www.cvvnumber.com/" target="_blank"><img height="140" src="//www.cvvnumber.com/csc_2.gif" width="200" align="left" border="0" alt="cid" style="width: 220px; height:auto;"></a>
						' . __( 'Look for the 4-digit code printed on the front of your card just above and to the right of your main credit card number. This 4-digit code is your Card Identification Number (CID). The CID is the four-digit code printed just above the Account Number.', 'yith-woocommerce-stripe' ) . '
					</p>
				</div>';
			}

			// add checkout fields for credit cart
			if ( in_array( $this->mode, array( 'standard', 'elements' ) ) && $this->add_billing_fields ) {
				$fields_to_check = array(
					'billing_country',
					'billing_city',
					'billing_address_1',
					'billing_address_2',
					'billing_state',
					'billing_postcode'
				);
				$original_fields = WC()->countries->get_default_address_fields();

				$shown_fields = is_checkout() ? WC()->checkout()->checkout_fields['billing'] : array();

				$fields['separator'] = '<hr style="clear: both;" />';

				foreach ( $fields_to_check as $i => $field_name ) {
					if ( isset( $shown_fields[ $field_name ] ) ) {
						unset( $fields_to_check[ $i ] );
						continue;
					}

					$field_index = str_replace( array( 'billing_' ), array( '' ), $field_name );
					$customer    = is_user_logged_in() ? new WC_Customer( get_current_user_id() ) : false;

					if ( is_checkout() ) {
						$value = WC()->checkout()->get_value( $field_name );
					} elseif ( $customer ) {
						$method_name = 'get_' . $field_name;
						$value       = method_exists( $customer, $method_name ) ? $customer->{$method_name}() : '';
					} else {
						$value = '';
					}

					if ( isset( $original_fields[ $field_index ] ) ) {
						$fields[ $field_name ] = woocommerce_form_field( $field_name, array_merge( array( 'return' => true ), $original_fields[ $field_index ] ), $value );
					}

				}

				if ( empty( $fields_to_check ) ) {
					unset( $fields['separator'] );
				}

			}

			$fields = array_merge(
				$fields,
				array(
					'fields-container-end' => '</div>'
				)
			);

			return $fields;
		}

		/**
		 * Outputs a checkbox for saving a new payment method to the database.
		 */
		public function save_payment_method_checkbox() {
			if ( $this->save_cards_mode == 'prompt' ) {
				parent::save_payment_method_checkbox();
			} else {
				return;
			}
		}

		/* === BLACKLIST METHODS === */

		/**
		 * Method to check blacklist (only for premium)
		 *
		 * @param bool $user_id
		 * @param bool $ip
		 *
		 * @return bool
		 * @since 1.1.3
		 *
		 */
		public function is_blocked( $user_id = false, $ip = false ) {
			if ( $this->get_option( 'enable_blacklist', 'no' ) == 'no' ) {
				return false;
			}

			global $wpdb;

			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			if ( ! $ip ) {
				$ip = $_SERVER['REMOTE_ADDR'];
			}

			$res = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM {$wpdb->yith_wc_stripe_blacklist} WHERE ( user_id = %d OR ip = %s ) AND unbanned = 0", $user_id, $ip ) );

			return $res > 0 ? true : false;
		}

		/**
		 * Check if the user is unbanned by admin
		 *
		 * @param bool $user_id
		 * @param bool $ip
		 *
		 * @return bool
		 */
		public function is_unbanned( $user_id = false, $ip = false ) {
			if ( $this->get_option( 'enable_blacklist', 'no' ) == 'no' ) {
				return false;
			}

			global $wpdb;

			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			if ( ! $ip ) {
				$ip = $_SERVER['REMOTE_ADDR'];
			}

			$res = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->yith_wc_stripe_blacklist} WHERE ( user_id = %d OR ip = %s ) AND unbanned = %d", $user_id, $ip, 1 ) );

			return $res > 0 ? true : false;
		}

		/**
		 * Register the block on blacklist
		 *
		 * @param array $args
		 *
		 * @return bool
		 *
		 * @since 1.1.3
		 *
		 */
		public function add_block( $args = array() ) {
			extract( wp_parse_args( $args,
				array(
					'user_id'  => get_current_user_id(),
					'ip'       => $_SERVER['REMOTE_ADDR'],
					'order_id' => 0,
					'ua'       => ! empty( $_SERVER['HTTP_USER_AGENT'] ) ? wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) : ''
				)
			) );

			/**
			 * @var $user_id  int
			 * @var $ip       string
			 * @var $order_id int
			 * @var $ua       string
			 */

			if ( $this->get_option( 'enable_blacklist', 'no' ) == 'no' || $this->have_purchased( $user_id ) || $this->is_blocked( $user_id, $ip ) || $this->is_unbanned( $user_id, $ip ) ) {
				return false;
			}

			global $wpdb;

			// add the user and the ip
			$wpdb->insert( $wpdb->yith_wc_stripe_blacklist, array(
				'user_id'      => $user_id,
				'ip'           => $ip,
				'order_id'     => $order_id,
				'ua'           => $ua,
				'ban_date'     => current_time( 'mysql' ),
				'ban_date_gmt' => current_time( 'mysql', 1 )
			) );

			return true;
		}

		/* === PAYMENT INTENT MANAGEMENT === */

		/**
		 * Retrieve intent for current operation; if none, creates one
		 *
		 * @param $order \WC_Order|bool Current order
		 *
		 * @return \Stripe\PaymentIntent|bool Payment intent or false on failure
		 */
		public function get_intent( $order = false ) {
			$intent_id = false;

			// check order first
			if ( $order ) {
				$intent_id = $order->get_meta( 'intent_id', true );
			}

			// then $_POST
			if ( ! $intent_id && isset( $_POST['stripe_intent'] ) ) {
				$intent_id = sanitize_text_field( $_POST['stripe_intent'] );
			}

			// and finally session
			if ( ! $intent_id ) {
				$intent    = $this->get_session_intent( $order ? $order->get_id() : false );
				$intent_id = $intent ? $intent->id : false;
			}

			if ( ! $intent_id ) {
				return false;
			}

			// retrieve intent from id
			if ( ! isset( $intent ) ) {
				$intent = $this->api->get_correct_intent( $intent_id );
			}

			if ( ! $intent ) {
				return false;
			}

			return $intent;
		}

		/**
		 * Get intent for current session
		 *
		 * @param $order_id int Order id, if any specified
		 *
		 * @return \Stripe\PaymentIntent|bool Session payment intent or false on failure
		 */
		public function get_session_intent( $order_id = false ) {
			global $wp;

			// Initialize SDK and set private key.
			$this->init_stripe_sdk();

			$session = WC()->session;

			if ( ! $session ) {
				return false;
			}

			$intent_id       = $session->get( 'yith_wcstripe_intent' );
			$locked_statuses = array( 'requires_payment_method', 'requires_confirmation', 'requires_action' );

			if ( ! $order_id && is_checkout_pay_page() ) {
				$order_id = isset( $wp->query_vars['order-pay'] ) ? $wp->query_vars['order-pay'] : false;
			}

			if ( $order_id ) {
				$order       = wc_get_order( $order_id );
				$currency    = strtolower( $order->get_currency() );
				$total       = YITH_WCStripe::get_amount( $order->get_total(), $currency, $order );

				// translators: 1. Blog name. 2. Order number.
				$description = apply_filters( 'yith_wcstripe_charge_description', sprintf( __( '%1$s - Order %2$s', 'yith-woocommerce-stripe' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() );

				$metadata    = array(
					'cart_hash'   => '',
					'order_id'    => $order_id,
					'order_email' => yit_get_prop( $order, 'billing_email' ),
				);
			} else {
				$cart = WC()->cart;
				$cart && $cart->calculate_totals();
				$total       = $cart ? YITH_WCStripe::get_amount( $cart->total ) : 0;
				$currency    = strtolower( get_woocommerce_currency() );

				// translators: 1. Cart hash ( unique identifier of current cart ).
				$description = $cart ? sprintf( __( 'Payment intent for cart %s', 'yith-woocommerce-stripe' ), yith_wcstripe_get_cart_hash() ) : '';

				$metadata    = array(
					'cart_hash'   => $cart ? yith_wcstripe_get_cart_hash() : '',
					'order_id'    => '',
					'order_email' => '',
				);
			}

			$is_checkout = is_checkout() || ( defined( 'WOOCOMMERCE_CHECKOUT' ) && WOOCOMMERCE_CHECKOUT ) || ( defined( 'YITH_WCSTRIPE_DOING_CHECKOUT' ) && YITH_WCSTRIPE_DOING_CHECKOUT );

			if ( ! $total || ! $is_checkout ) {
				return $this->get_session_setup_intent();
			}

			// if total don't match requirements, skip intent creation.
			if ( ! $total || $total > 99999999 ) {
				$this->delete_session_intent();

				return false;
			}

			if ( $intent_id ) {
				$intent = $this->api->get_intent( $intent_id );

				if ( $intent ) {

					// if intent isn't longer available, generate a new one.
					if ( ! in_array( $intent->status, $locked_statuses ) && ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
						$this->delete_session_intent( $intent );

						return $this->create_session_intent( array( 'order_id' => $order_id ) );
					}

					if ( $intent->amount != $total || $intent->currency != $currency || $intent->metadata->order_id != $order_id ) {
						if ( ! in_array( $intent->status, $locked_statuses ) ) {
							$intent = $this->api->update_intent(
								$intent->id,
								array(
									'amount'      => $total,
									'currency'    => $currency,
									'description' => $description,
									'metadata'    => apply_filters(
										'yith_wcstripe_metadata',
										array_merge(
											array( 'instance' => $this->instance ),
											$metadata
										),
										'create_payment_intent'
									),
								)
							);
						} else {
							$this->delete_session_intent( $intent );

							return $this->create_session_intent( array( 'order_id' => $order_id ) );
						}
					}

					return $intent;
				}
			}

			return $this->create_session_intent( array( 'order_id' => $order_id ) );
		}

		/**
		 * Get setup intent for current session
		 *
		 * @return \Stripe\SetupIntent|bool Session setup intent or false on failure
		 */
		public function get_session_setup_intent() {
			$session   = WC()->session;
			$intent_id = $session->get( 'yith_wcstripe_setup_intent' );

			if ( $intent_id ) {
				$intent = $this->api->get_setup_intent( $intent_id );

				if ( $intent ) {
					// if intent isn't longer available, generate a new one
					if ( ! in_array( $intent->status, array(
						'requires_payment_method',
						'requires_confirmation',
						'requires_action'
					) ) ) {
						$this->delete_session_setup_intent( $intent );

						return $this->create_session_setup_intent();
					}

					return $intent;
				}
			}

			return $this->create_session_setup_intent();
		}

		/**
		 * Create a new intent for current session
		 *
		 * @param $args array array of argument to use for intent creation. Following a list of accepted params<br/>
		 *              [
		 *              'amount' // total to pay
		 *              'currency' // order currency
		 *              'description' // transaction description; will be modified after confirm
		 *              'metadata' // metadata for the transaction; will be modified after confirm
		 *              'setup_future_usage' // default to 'off_session', to reuse in renews when needed
		 *              'customer' // stripe customer id for current user, if any
		 *              ]
		 *
		 * @return \Stripe\PaymentIntent|bool Generate payment intent, or false on failure
		 */
		public function create_session_intent( $args = array() ) {
			global $wp;

			$customer_id = false;
			$order_id    = false;

			if ( is_user_logged_in() ) {
				$customer_id = $this->get_customer_id( get_current_user_id() );
			}

			if ( isset( $args['order_id'] ) ) {
				$order_id = $args['order_id'];
				unset( $args['order_id'] );
			} elseif ( is_checkout_pay_page() ) {
				$order_id = isset( $wp->query_vars['order-pay'] ) ? $wp->query_vars['order-pay'] : false;
			}

			if ( $order_id ) {
				$order       = wc_get_order( $order_id );
				$currency    = $order->get_currency();
				$total       = YITH_WCStripe::get_amount( $order->get_total(), $currency, $order );
				$description = apply_filters( 'yith_wcstripe_charge_description', sprintf( __( '%s - Order %s', 'yith-woocommerce-stripe' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() );
				$metadata    = array(
					'order_id'    => $order_id,
					'order_email' => yit_get_prop( $order, 'billing_email' ),
					'cart_hash'   => ''
				);
			} else {
				$cart        = WC()->cart;
				$total       = $cart ? YITH_WCStripe::get_amount( $cart->total ) : 0;
				$currency    = strtolower( get_woocommerce_currency() );
				$description = $cart ? sprintf( __( 'Payment intent for cart %s', 'yith-woocommerce-stripe' ), yith_wcstripe_get_cart_hash() ) : '';
				$metadata    = array(
					'cart_hash'   => $cart ? yith_wcstripe_get_cart_hash() : '',
					'order_id'    => '',
					'order_email' => ''
				);
			}

			// Guest user
			if ( ! $customer_id && $order_id ) {
				$order    = wc_get_order( $order_id );
				$customer = $this->get_customer( $order );
				if ( $customer ) {
					$customer_id = $customer->id;
				}
			}

			$defaults = apply_filters( 'yith_wcstripe_create_payment_intent', array_merge(
				array(
					'amount'              => $total,
					'currency'            => $currency,
					'description'         => $description,
					'metadata'            => apply_filters( 'yith_wcstripe_metadata', array_merge(
						array(
							'instance' => $this->instance
						),
						$metadata
					), 'create_payment_intent' ),
					'setup_future_usage'  => 'off_session',
					'capture_method'      => $this->capture ? 'automatic' : 'manual',
					'confirmation_method' => 'manual'
				),
				$customer_id ? array(
					'customer' => $customer_id
				) : array()
			) );

			$args = wp_parse_args( $args, $defaults );

			// Initialize SDK and set private key
			$this->init_stripe_sdk();

			$session = WC()->session;

			try {
				$intent = $this->api->create_intent( $args );
			} catch ( Exception $e ) {
				return false;
			}

			if ( ! $intent ) {
				return false;
			}

			if ( $session ) {
				$session->set( 'yith_wcstripe_intent', $intent->id );
			}

			return $intent;
		}

		/**
		 * Create a new setup intent for current session
		 *
		 * @param $args array array of argument to use for intent creation. Following a list of accepted params<br/>
		 *              [
		 *              'metadata' // metadata for the transaction; will be modified after confirm
		 *              'usage' // default to 'off_session', to reuse in renews when needed
		 *              'customer' // stripe customer id for current user, if any
		 *              ]
		 *
		 * @return \Stripe\PaymentIntent|bool Generate payment intent, or false on failure
		 */
		public function create_session_setup_intent( $args = array() ) {
			$customer_id = false;

			if ( is_user_logged_in() ) {
				$customer_id = $this->get_customer_id( get_current_user_id() );
			}

			$defaults = apply_filters( 'yith_wcstripe_create_payment_intent', array_merge(
				array(
					'metadata' => apply_filters( 'yith_wcstripe_metadata', array(
						'instance' => $this->instance
					), 'create_setup_intent' ),
					'usage'    => 'off_session',
				),
				$customer_id ? array(
					'customer' => $customer_id
				) : array()
			) );

			$args = wp_parse_args( $args, $defaults );

			// Initialize SDK and set private key
			$this->init_stripe_sdk();

			$session = WC()->session;

			$intent = $this->api->create_setup_intent( $args );

			if ( ! $intent ) {
				return false;
			}

			$session->set( 'yith_wcstripe_setup_intent', $intent->id );

			return $intent;
		}

		/**
		 * Update session intent, registering new cart total and currency, and configuring a payment method if needed
		 *
		 * @param int|bool $token Selected token id, or null if new payment method is used.
		 * @param int|bool $order Current order id, or null if cart should be used.
		 *
		 * @return PaymentIntent|SetupIntent|bool Updated intent, or false on failure
		 * @throws Exception When API request fails.
		 */
		public function update_session_intent( $token = false, $order = false ) {
			// retrieve intent; this will automatically update total and currency.
			$intent = $this->get_session_intent( $order );

			if ( ! $intent ) {
				throw new Exception( __( 'There was an error with payment process; please try again later', 'yith-woocommerce-stripe' ) );
			}

			if ( ! $token ) {
				return $intent;
			}

			// prepare payment method to use for update.
			if ( is_int( $token ) ) {
				if ( ! is_user_logged_in() ) {
					throw new Exception( __( 'You must login before using a registered card', 'yith-woocommerce-stripe' ) );
				}

				$token = WC_Payment_Tokens::get( $token );

				if ( ! $token || $token->get_user_id() != get_current_user_id() ) {
					throw new Exception( __( 'The card you\'re trying to use isn\'t valid; please, try again with another payment method', 'yith-woocommerce-stripe' ) );
				}

				$payment_method = $token->get_token();
			} elseif ( is_string( $token ) ) {
				$payment_method = $token;
			}

			// if a payment method was provided, try to bind it to payment intent.
			if ( $payment_method ) {
				$result = $this->api->update_correct_intent(
					$intent->id,
					array( 'payment_method' => $payment_method )
				);

				// check if update was successful.
				if ( ! $result ) {
					throw new Exception( __( 'The card you\'re trying to use isn\'t valid; please, try again with another payment method', 'yith-woocommerce-stripe' ) );
				}

				// update intent object that will be returned.
				$intent = $result;
			}

			return $intent;
		}

		/**
		 * Removes intent from current session
		 * Method is intended to cancel session, but will also cancel PaymentIntent on Stripe, if object is passed as param
		 *
		 * @param $intent \Stripe\PaymentIntent|bool Payment intent to cancel, or false if it is not required
		 *
		 * @return void
		 */
		public function delete_session_intent( $intent = false ) {
			// Initialize SDK and set private key
			$this->init_stripe_sdk();

			$session = WC()->session;
			$session->set( 'yith_wcstripe_intent', '' );

			if ( $intent && isset( $intent->status ) && ! in_array( $intent->status, array(
					'succeeded',
					'cancelled'
				) ) ) {
				$intent->cancel();
			}
		}

		/**
		 * Removes intent from current session
		 * Method is intended to cancel session, but will also cancel SetupIntent on Stripe, if object is passed as param
		 *
		 * @param $intent \Stripe\setupIntent|bool Setup intent to cancel, or false if it is not required
		 *
		 * @return void
		 */
		public function delete_session_setup_intent( $intent = false ) {
			// Initialize SDK and set private key
			$this->init_stripe_sdk();

			$session = WC()->session;
			$session->set( 'yith_wcstripe_setup_intent', '' );

			if ( $intent && isset( $intent->status ) && ! in_array( $intent->status, array(
					'succeeded',
					'cancelled'
				) ) ) {
				$intent->cancel();
			}
		}

		/* === CHECKOUT SESSION METHODS */

		/**
		 * Create checkout session
		 *
		 * @param $args array Params used to create CheckoutSession object
		 *
		 * @return \Stripe\StripeObject|bool Checkout session or false on failure
		 */
		public function create_checkout_session( $args = array() ) {
			$args = array_merge( $args, array(
				'billing_address_collection' => $this->hosted_billing ? 'required' : 'auto',
				'payment_intent_data'        => array(
					'capture_method' => $this->capture ? 'automatic' : 'manual',
				),
			) );

			return parent::create_checkout_session( $args );
		}

		/* === TOKENS MANAGEMENT === */

		/**
		 * Add payment method
		 *
		 * @return array|bool
		 */
		public function add_payment_method() {
			try {
				// Initializate SDK and set private key
				$this->init_stripe_sdk();

				$intent = $this->get_intent();

				// if no intent was found, crate one on the fly
				if ( ! $intent ) {
					$intent = $this->create_session_setup_intent();
				}

				if ( ! $intent ) {
					throw new Exception( __( 'Sorry, There was an error while registering payment method; please, try again', 'yith-woocommerce-stripe' ) );
				} elseif ( $intent->status == 'requires_action' ) {
					do_action( 'yith_wcstripe_setup_intent_requires_action', $intent, get_current_user_id() );

					throw new Exception( __( 'Please, validate your payment method before proceeding further; in order to do this, refresh the page and proceed at checkout as usual', 'yith-woocommerce-stripe' ) );
				} elseif ( ! in_array( $intent->status, array( 'succeeded', 'requires_capture' ) ) ) {
					throw new Exception( __( 'Sorry, There was an error while registering payment method; please, try again', 'yith-woocommerce-stripe' ) );
				}

				$token = $this->save_token( $intent->payment_method );

				return apply_filters( 'yith_wcstripe_add_payment_method_result', array(
					'result'   => 'success',
					'redirect' => wc_get_endpoint_url( 'payment-methods' ),
				), $token );

			} catch ( Stripe\Exception\ApiErrorException $e ) {
				$this->error_handling( $e );

				return false;
			} catch ( Exception $e ) {
				wc_add_notice( $e->getMessage(), 'error' );

				return false;
			}
		}

		/**
		 * Save the token on db
		 *
		 * @param $payment_method_id string Payment method to save
		 *
		 * @return WC_Payment_Token|bool Registered token or false on failure
		 *
		 * @throws Exception
		 */
		public function save_token( $payment_method_id ) {

			if ( ! is_user_logged_in() || ! $this->save_cards || ( is_checkout() && $this->save_cards_mode == 'prompt' && ! isset( $_POST['wc-yith-stripe-new-payment-method'] ) ) ) {
				return false;
			}

			// Initializate SDK and set private key
			$this->init_stripe_sdk();

			$user           = wp_get_current_user();
			$local_customer = YITH_WCStripe()->get_customer()->get_usermeta_info( $user->ID );
			$customer       = ! empty( $local_customer['id'] ) ? $this->api->get_customer( $local_customer['id'] ) : false;
			$payment_method = $this->api->get_payment_method( $payment_method_id );

			if ( $customer && $payment_method->customer != $customer->id ) {
				$this->attach_payment_method( $customer, $payment_method_id );
				$customer->sources->data[] = $payment_method->card;
			} elseif ( ! $customer ) {
				$params = array(
					'payment_method' => $payment_method_id,
					'email'          => $user->billing_email,
					'description'    => substr( $user->user_login . ' (#' . $user->ID . ' - ' . $user->user_email . ') ' . $user->billing_first_name . ' ' . $user->billing_last_name, 0, 350 ),
					'metadata'       => apply_filters( 'yith_wcstripe_metadata', array(
						'user_id'  => $user->ID,
						'instance' => $this->instance
					), 'create_customer' )
				);

				$customer = $this->api->create_customer( $params );
			}

			$already_registered        = false;
			$registered_token          = false;
			$already_registered_tokens = WC_Payment_Tokens::get_customer_tokens( $user->ID, $this->id );

			if ( ! empty( $already_registered_tokens ) ) {
				foreach ( $already_registered_tokens as $registered_token ) {
					/**
					 * @var $registered_token \WC_Payment_Token
					 */
					$registered_fingerprint = $registered_token->get_meta( 'fingerprint', true );

					if ( $registered_fingerprint == $payment_method->card->fingerprint || $registered_token->get_token() == $payment_method_id ) {
						$already_registered = true;
						break;
					}
				}
			}

			if ( ! $already_registered ) {
				// save card
				$token = new WC_Payment_Token_CC();
				$token->set_token( $payment_method_id );
				$token->set_gateway_id( $this->id );
				$token->set_user_id( $user->ID );

				$token->set_card_type( strtolower( $payment_method->card->brand ) );
				$token->set_last4( $payment_method->card->last4 );
				$token->set_expiry_month( ( 1 === strlen( $payment_method->card->exp_month ) ? '0' . $payment_method->card->exp_month : $payment_method->card->exp_month ) );
				$token->set_expiry_year( $payment_method->card->exp_year );
				$token->set_default( true );
				$token->add_meta_data( 'fingerprint', $payment_method->card->fingerprint );
				$token->add_meta_data( 'confirmed', true );

				if ( ! $token->save() ) {
					throw new Exception( __( 'Credit card info not valid', 'yith-woocommerce-stripe' ) );
				}

				// backward compatibility
				if ( $customer ) {
					YITH_WCStripe()->get_customer()->update_usermeta_info( $customer->metadata->user_id, array(
						'id'             => $customer->id,
						'cards'          => $customer->sources->data,
						'default_source' => $customer->invoice_settings->default_payment_method
					) );
				}

				do_action( 'yith_wcstripe_created_card', $payment_method_id, $customer );

				return $token;
			} else {
				$registered_token->set_default( true );
				$registered_token->save();

				return $registered_token;
			}
		}

		/**
		 * Attach payment method to customer
		 *
		 * @param $customer          string|Stripe\Customer Customer to update
		 * @param $payment_method_id string Payment method to save
		 *
		 * @return bool Status of the operation
		 *
		 * @throws Exception
		 */
		public function attach_payment_method( $customer, $payment_method_id ) {

			try {
				$customer       = $this->api->get_customer( $customer );
				$payment_method = $this->api->get_payment_method( $payment_method_id );

				$payment_method->attach( array(
					'customer' => $customer->id
				) );
			} catch ( Exception $e ) {
				return false;
			}

			$this->api->update_customer( $customer, array(
				'invoice_settings' => array(
					'default_payment_method' => $payment_method_id
				)
			) );

			return true;
		}

		/**
		 * Set one of the currently registered tokens as default
		 *
		 * @param $payment_method_id string Payment Method id
		 *
		 * @return bool Operation status
		 */
		public function set_default_token( $payment_method_id ) {
			if ( ! is_user_logged_in() ) {
				return false;
			}

			$user                      = wp_get_current_user();
			$already_registered_tokens = WC_Payment_Tokens::get_customer_tokens( $user->ID, $this->id );

			if ( ! empty( $already_registered_tokens ) ) {
				foreach ( $already_registered_tokens as $registered_token ) {
					/**
					 * @var $registered_token \WC_Payment_Token
					 */
					if ( $registered_token->get_token() == $payment_method_id ) {
						$registered_token->set_default( true );
						$registered_token->save();

						return true;
					}
				}
			}

			return false;
		}

		/**
		 * Sync tokens on website from stripe $customer object
		 *
		 * @param int|WP_User      $user
		 * @param \Stripe\Customer $customer
		 */
		public function sync_tokens( $user, $customer ) {
			if ( ! is_a( $user, 'WP_User' ) ) {
				$user = get_user_by( 'id', $user );
			}

			if ( ! $this->save_cards || ( $this->save_cards_mode == 'prompt' ) ) {
				return;
			}

			$this->init_stripe_sdk();

			$sources = $this->api->get_payment_methods( $customer->id );
			$tokens  = WC_Payment_Tokens::get_customer_tokens( $user->ID, $this->id );
			$to_add  = $sources;

			/** @var WC_Payment_Token_CC $token */
			foreach ( $tokens as $token_id => $token ) {
				$found = false;

				foreach ( $sources as $k => $source ) {
					if ( $token->get_token() === $source->id ) {
						$found = true;
						break;
					}
				}

				// edit token if found if between stripe ones and if something is changed
				if ( $found ) {
					// remove the source from global array, to add the remaining on website
					unset( $to_add[ $k ] );

					$source = $source->card;

					$changed = false;

					if ( $token->get_last4() != $source->last4 ) {
						$token->set_last4( $source->last4 );
						$changed = true;
					}

					if ( $token->get_expiry_month() != ( 1 === strlen( $source->exp_month ) ? '0' . $source->exp_month : $source->exp_month ) ) {
						$token->set_expiry_month( ( 1 === strlen( $source->exp_month ) ? '0' . $source->exp_month : $source->exp_month ) );
						$changed = true;
					}

					if ( $token->get_expiry_year() != $source->exp_year ) {
						$token->set_expiry_year( $source->exp_year );
						$changed = true;
					}

					if ( $token->get_meta( 'fingerprint' ) != $source->fingerprint ) {
						$token->update_meta_data( 'fingerprint', $source->fingerprint );
						$changed = true;
					}

					if ( $token->get_token() === $customer->default_source && ! $token->is_default() ) {
						$token->set_default( true );
						$changed = true;
					}

					if ( $token->get_token() !== $customer->default_source && $token->is_default() ) {
						$token->set_default( false );
						$changed = true;
					}

					// save it if changed
					if ( $changed ) {
						$token->save();
					}
				} // if not found any token between stripe, remove token
				else {
					$token->delete();
				}
			}

			// add remaining sources not added as token on website yet
			foreach ( $to_add as $source ) {
				$method_id = $source->id;
				$source    = $source->card;

				$token = new WC_Payment_Token_CC();
				$token->set_token( $method_id );
				$token->set_gateway_id( $this->id );
				$token->set_user_id( $user->ID );

				$token->set_card_type( strtolower( $source->brand ) );
				$token->set_last4( $source->last4 );
				$token->set_expiry_month( ( 1 === strlen( $source->exp_month ) ? '0' . $source->exp_month : $source->exp_month ) );
				$token->set_expiry_year( $source->exp_year );
				$token->add_meta_data( 'fingerprint', $source->fingerprint );

				$token->save();
			}

			// back-compatibility
			YITH_WCStripe()->get_customer()->update_usermeta_info( $customer->metadata->user_id, array(
				'id'             => $customer->id,
				'cards'          => $customer->sources->data,
				'default_source' => $customer->invoice_settings->default_payment_method
			) );
		}

		/**
		 * Change display name on checkout page for token
		 *
		 * @param $display
		 * @param $token WC_Payment_Token_CC
		 *
		 * @return string
		 */
		public function token_display_name( $display, $token ) {
			$icon    = WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/' . $token->get_card_type() . '.png' );
			$display = '<img src="' . $icon . '" alt="' . $token->get_card_type() . '" style="width:40px;"/>';
			$display .= sprintf(
				'<span class="card-type">%s</span> <span class="card-number"><em>&bull;&bull;&bull;&bull;</em>%s</span> <span class="card-expire">(%s/%s)</span>',
				$token->get_card_type(),
				$token->get_last4(),
				$token->get_expiry_month(),
				$token->get_expiry_year()
			);

			return $display;
		}

		/* === HELPER METHODS === */

		/**
		 * Get customer ID of Stripe account from user ID
		 *
		 * @param $user_id
		 *
		 * @return integer
		 * @since 1.0.0
		 */
		public function get_customer_id( $user_id ) {
			$customer = YITH_WCStripe()->get_customer()->get_usermeta_info( $user_id );

			if ( ! isset( $customer['id'] ) ) {
				return 0;
			}

			return $customer['id'];
		}

		/**
		 * Get customer of Stripe account or create a new one if not exists
		 *
		 * @param $order WC_Order
		 *
		 * @return \Stripe\Customer|bool
		 * @since 1.0.0
		 */
		public function get_customer( $order ) {
			if ( is_int( $order ) ) {
				$order = wc_get_order( $order );
			}

			$current_order_id = ! empty ( $this->_current_order ) ? yit_get_order_id( $this->_current_order ) : false;
			$order_id         = yit_get_order_id( $order );

			if ( $current_order_id == $order_id && ! empty( $this->_current_customer ) ) {
				return $this->_current_customer;
			}

			$user_id         = is_user_logged_in() ? $order->get_user_id() : false;
			$local_customer  = is_user_logged_in() ? YITH_WCStripe()->get_customer()->get_usermeta_info( $user_id ) : false;
			$stripe_customer = false;

			// get existing
			if ( $local_customer && ! empty( $local_customer['id'] ) ) {
				try {
					$stripe_customer = $this->api->get_customer( $local_customer['id'] );

					if ( $current_order_id == $order_id ) {
						$this->_current_customer = $stripe_customer;
					}
				} catch ( Exception $e ) {
					// do nothing, and try to create a new customer
				}
			}

			// create new one
			if ( ! $stripe_customer ) {
				$user = is_user_logged_in() ? $order->get_user() : false;

				if ( is_user_logged_in() ) {
					$description = $user->user_login . ' (#' . $order->get_user_id() . ' - ' . $user->user_email . ') ' . yit_get_prop( $order, 'billing_first_name' ) . ' ' . yit_get_prop( $order, 'billing_last_name' );
				} else {
					$description = yit_get_prop( $order, 'billing_email' ) . ' (' . __( 'Guest', 'yith-woocommerce-stripe' ) . ' - ' . yit_get_prop( $order, 'billing_email' ) . ') ' . yit_get_prop( $order, 'billing_first_name' ) . ' ' . yit_get_prop( $order, 'billing_last_name' );
				}

				$params = array(
					'email'       => yit_get_prop( $order, 'billing_email' ),
					'description' => substr( $description, 0, 350 ),
					'metadata'    => apply_filters( 'yith_wcstripe_metadata', array(
						'user_id'  => is_user_logged_in() ? $order->get_user_id() : false,
						'instance' => $this->instance
					), 'create_customer' )
				);

				try {
					$stripe_customer = $this->api->create_customer( $params );

					// update user meta
					if ( is_user_logged_in() ) {
						YITH_WCStripe()->get_customer()->update_usermeta_info( $user_id, array(
							'id'             => $stripe_customer->id,
							'cards'          => $stripe_customer->sources->data,
							'default_source' => $stripe_customer->invoice_settings->default_payment_method
						) );
					}

					if ( $current_order_id == $order_id ) {
						$this->_current_customer = $stripe_customer;
					}
				} catch ( Exception $e ) {
					return false;
				}

			}

			return $stripe_customer;
		}

		/**
		 * Say if the user in parameter have already purchased properly previously
		 *
		 * @param bool $user_id
		 *
		 * @return bool
		 * @since 1.1.3
		 *
		 */
		public function have_purchased( $user_id = false ) {
			global $wpdb;

			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_status IN ( %s, %s ) AND post_author = %d", 'wc-completed', 'wc-processing', $user_id ) );

			return $count > 0 ? true : false;
		}

		/**
		 * Log to txt file
		 *
		 * @param $message
		 *
		 * @since 1.0.0
		 */
		public function log( $message ) {
			if ( isset( $this->log, $this->debug ) && $this->debug ) {
				$this->log->add( 'stripe', $message );
			}
		}

		/**
		 * Standard error handling for exceptions thrown by API class
		 *
		 * @param $e Stripe\Exception\ApiErrorException
		 *
		 * @return string Final error message
		 */
		public function error_handling( $e, $args = array() ) {
			$message = parent::error_handling( $e, $args );
			$body    = $e->getJsonBody();

			// register error within log file
			$this->log( 'Stripe Error: ' . $e->getHttpStatus() . ' - ' . print_r( $e->getJsonBody(), true ) );

			// add block if there is an error on card
			if ( $body && isset( $args['order_id'] ) ) {
				$err = $body['error'];

				if ( isset( $err['type'] ) && $err['type'] == 'card_error' ) {
					$this->add_block( "order_id={$args['order_id']}" );
					WC()->session->refresh_totals = true;
				}
			}

			return $message;
		}

		/**
		 * Give ability to add options to $this->form_fields
		 *
		 * @param        $field
		 * @param string $where (first, last, after, before) (optional, default: last)
		 * @param string $who   (optional, default: empty string)
		 *
		 * @since  2.0.0
		 */
		private function add_form_field( $field, $where = 'last', $who = '' ) {
			switch ( $where ) {

				case 'first':
					$this->form_fields = array_merge( $field, $this->form_fields );
					break;

				case 'last':
					$this->form_fields = array_merge( $this->form_fields, $field );
					break;

				case 'before':
				case 'after' :
					if ( array_key_exists( $who, $this->form_fields ) ) {

						$who_position = array_search( $who, array_keys( $this->form_fields ) );

						if ( $where == 'after' ) {
							$who_position = ( $who_position + 1 );
						}

						$before = array_slice( $this->form_fields, 0, $who_position );
						$after  = array_slice( $this->form_fields, $who_position );

						$this->form_fields = array_merge( $before, $field, $after );
					}
					break;
			}
		}
	}
}
