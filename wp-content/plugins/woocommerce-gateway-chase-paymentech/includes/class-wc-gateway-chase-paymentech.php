<?php
/**
 * WooCommerce Chase Paymentech
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Chase Paymentech to newer
 * versions in the future. If you wish to customize WooCommerce Chase Paymentech for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-chase-paymentech/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_3 as Framework;

/**
 * Chase Paymentech Payment Gateway
 *
 * @since 1.0.0
 *
 * @method \WC_Chase_Paymentech get_plugin()
 */
class WC_Gateway_Chase_Paymentech extends Framework\SV_WC_Payment_Gateway_Direct {


	/** The production hosted pay form URL endpoint */
	const PRODUCTION_HOSTED_PAY_FORM_ENDPOINT = 'https://www.chasepaymentechhostedpay.com/hpf/1_1';

	/** The test hosted pay form URL endpoint */
	const TEST_HOSTED_PAY_FORM_ENDPOINT = 'https://www.chasepaymentechhostedpay-var.com/hpf/1_1';

	/** the production hosted pay form UID URL endpoint */
	const PRODUCTION_HOSTED_PAY_FORM_UID_URL = 'https://www.chasepaymentechhostedpay.com/direct/services/request/init';

	/** the test hosted pay form UID URL endpoint */
	const TEST_HOSTED_PAY_FORM_UID_URL = 'https://www.chasepaymentechhostedpay-var.com/direct/services/request/init';

	/** The production Orbital Connection API primary endpoint */
	const PRODUCTION_ORBITAL_GATEWAY_PRIMARY_ENDPOINT = 'https://orbital1.chasepaymentech.com/authorize';

	/** The production Orbital Connection API secondary endpoint */
	const PRODUCTION_ORBITAL_GATEWAY_SECONDARY_ENDPOINT = 'https://orbital2.chasepaymentech.com/authorize';

	/** The test Orbital Connection API primary endpoint */
	const TEST_ORBITAL_GATEWAY_PRIMARY_ENDPOINT = 'https://orbitalvar1.chasepaymentech.com/authorize';

	/** The test Orbital Connection API secondary endpoint */
	const TEST_ORBITAL_GATEWAY_SECONDARY_ENDPOINT = 'https://orbitalvar2.chasepaymentech.com/authorize';


	/** @var string production secure account id */
	protected $secure_account_id;

	/** @var string test secure account id */
	protected $test_secure_account_id;

	/** @var string production secure API token */
	protected $secure_api_token;

	/** @var string test secure API token */
	protected $test_secure_api_token;

	/** @var string Production Connection Username set up on Orbital Gateway */
	protected $username;

	/** @var string Test Connection Username set up on Orbital Gateway */
	protected $test_username;

	/** @var string Production Connection Password used in conjunction with Orbital Username */
	protected $password;

	/** @var string Test Connection Password used in conjunction with Orbital Username */
	protected $test_password;

	/** @var string Production 12-digit gateway merchant account number assigned by Chase Paymentech */
	protected $merchant_id;

	/** @var string Test 12-digit gateway merchant account number assigned by Chase Paymentech */
	protected $test_merchant_id;

	/** @var string Production 3-digit merchant terminal ID assigned by Chase Paymentech */
	protected $terminal_id;

	/** @var string Test 3-digit merchant terminal ID assigned by Chase Paymentech */
	protected $test_terminal_id;

	/** @var string whether CSC (and customer name) is required */
	protected $require_csc;

	/** @var string optional pay form css URL */
	protected $pay_form_css_url;

	/** @var string Whether test certification mode is currently enabled, one of 'yes' or 'no' */
	protected $test_certification_mode;


	/**
	 * Initialize the gateway
	 *
	 * @since 1.0
	 */
	public function __construct() {

		parent::__construct(
			WC_Chase_Paymentech::CREDIT_CARD_GATEWAY_ID,
			wc_chase_paymentech(),
			array(
				'method_title'       => __( 'Chase Paymentech', 'woocommerce-gateway-chase-paymentech' ),
				'method_description' => __( 'Allow customers to securely pay using their credit cards with Chase Paymentech.', 'woocommerce-gateway-chase-paymentech' ),
				'supports'           => array(
					self::FEATURE_PRODUCTS,
					self::FEATURE_CARD_TYPES,
					self::FEATURE_TOKENIZATION,
					self::FEATURE_ADD_PAYMENT_METHOD,
					self::FEATURE_TOKEN_EDITOR,
					self::FEATURE_CREDIT_CARD_CHARGE,
					self::FEATURE_CREDIT_CARD_CHARGE_VIRTUAL,
					self::FEATURE_CREDIT_CARD_AUTHORIZATION,
					self::FEATURE_CREDIT_CARD_CAPTURE,
					self::FEATURE_DETAILED_CUSTOMER_DECLINE_MESSAGES,
					self::FEATURE_REFUNDS,
					self::FEATURE_VOIDS,
				 ),
				'payment_type'       => 'credit-card',
				'environments'       => array( 'production' => __( 'Production', 'woocommerce-gateway-chase-paymentech' ), 'test' => __( 'Test', 'woocommerce-gateway-chase-paymentech' ) ),
			)
		);

		// IPN listener hook
		add_action( 'woocommerce_api_' . strtolower( get_class( $this ) ), array( $this, 'process_ipn' ) );

		if ( ! has_action( 'wc_' . $this->get_id() . '_api_request_performed' ) ) {
			add_action( 'wc_' . $this->get_id() . '_api_request_performed', array( $this, 'log_api_communication' ), 10, 2 );
		}

		// remove support for customer payment method changes
		if ( $this->supports_subscriptions() ) {
			$this->remove_support( array(
				'subscription_payment_method_change_customer', // 2.0.x
				'subscription_payment_method_change' // 1.5.x
			) );
		}
	}


	/**
	 * Determines if the gateway is properly configured to perform transactions.
	 *
	 * Chase Paymentech Hosted Pay Form requires:
	 *
	 * + secure account id
	 *
	 * Orbital Connection (XML API) requires:
	 *
	 * + `Orbital Connection Username`
	 * + `Orbital Connection Password`
	 * + `Merchant Account Number (id)`
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function is_configured() {

		$is_configured = parent::is_configured();

		// missing configuration
		if ( ! $this->get_secure_account_id() || 0 === count( $this->get_card_types() ) ) {
			$is_configured = false;
		}

		return $is_configured;
	}


	/**
	 * Returns true if all the direct API settings are configured for the
	 * optional direct API used for capturing authorizations and performing
	 * tokenized transactions.
	 *
	 * @since 1.2.0
	 * @return boolean true if the direct API settings are configured
	 */
	public function is_direct_api_configured() {

		if ( $this->get_username() && $this->get_password() && $this->get_merchant_id() && $this->get_terminal_id() ) {
			return true;
		}

		return false;
	}


	/** Admin methods ******************************************************/


	/**
	 * Returns an array of form fields specific for this method
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway::get_method_form_fields()
	 * @return array of form fields
	 */
	protected function get_method_form_fields() {

		return array(

			'secure_account_id' => array(
				'title'    => __( 'Secure Account ID', 'woocommerce-gateway-chase-paymentech' ),
				'type'     => 'text',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'Your Orbital Gateway production Hosted Payment account ID, provided by Chase', 'woocommerce-gateway-chase-paymentech' ),
			),

			'test_secure_account_id' => array(
				'title'    => __( 'Secure Account ID', 'woocommerce-gateway-chase-paymentech' ),
				'type'     => 'text',
				'class'    => 'environment-field test-field',
				'desc_tip' => __( 'Your Orbital Gateway test Hosted Payment account ID, provided by Chase', 'woocommerce-gateway-chase-paymentech' ),
			),

			'secure_api_token' => array(
				'title'    => __( 'Secure API Token', 'woocommerce-gateway-chase-paymentech' ),
				'type'     => 'text',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'Your Hosted Secure API Token, provided by Chase. Set this only if your merchant account is configured to use Order Abstraction.', 'woocommerce-gateway-chase-paymentech' ),
			),

			'test_secure_api_token' => array(
				'title'    => __( 'Secure API Token', 'woocommerce-gateway-chase-paymentech' ),
				'type'     => 'text',
				'class'    => 'environment-field test-field',
				'desc_tip' => __( 'Your optional Hosted Secure API Token, provided by Chase. Set this only if Chase requires you to use Order Abstraction.', 'woocommerce-gateway-chase-paymentech' ),
			),

			'username' => array(
				'title'    => __( 'User Name', 'woocommerce-gateway-chase-paymentech' ),
				'type'     => 'text',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'Optional Orbital Connection production username set up on Orbital Gateway.  Required only for admin capturing authorized charges, or tokenization transactions', 'woocommerce-gateway-chase-paymentech' ),
			),

			'test_username' => array(
				'title'    => __( 'User Name', 'woocommerce-gateway-chase-paymentech' ),
				'type'     => 'text',
				'class'    => 'environment-field test-field',
				'desc_tip' => __( 'Optional Orbital Connection test username set up on Orbital Gateway.  Required only for admin capturing authorized charges, or tokenization transactions', 'woocommerce-gateway-chase-paymentech' ),
			),

			'password' => array(
				'title'    => __( 'Password', 'woocommerce-gateway-chase-paymentech' ),
				'type'     => 'password',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'Optional Orbital Connection production password used in conjunction with Orbital Username.  Required only for admin capturing authorized charges, or tokenization transactions', 'woocommerce-gateway-chase-paymentech' ),
			),

			'test_password' => array(
				'title'    => __( 'Password', 'woocommerce-gateway-chase-paymentech' ),
				'type'     => 'password',
				'class'    => 'environment-field test-field',
				'desc_tip' => __( 'Optional Orbital Connection test password used in conjunction with Orbital Username.  Required only for admin capturing authorized charges, or tokenization transactions', 'woocommerce-gateway-chase-paymentech' ),
			),

			'merchant_id' => array(
				'title'    => __( 'Merchant ID', 'woocommerce-gateway-chase-paymentech' ),
				'type'     => 'text',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'Optional 12-digit gateway merchant production account number assigned by Chase Paymentech.  Required only for admin capturing authorized charges, or tokenization transactions', 'woocommerce-gateway-chase-paymentech' ),
			),

			'test_merchant_id' => array(
				'title'    => __( 'Merchant ID', 'woocommerce-gateway-chase-paymentech' ),
				'type'     => 'text',
				'class'    => 'environment-field test-field',
				'desc_tip' => __( 'Optional 12-digit gateway merchant test account number assigned by Chase Paymentech.  Required only for admin capturing authorized charges, or tokenization transactions', 'woocommerce-gateway-chase-paymentech' ),
			),

			'terminal_id' => array(
				'title'    => __( 'Terminal ID', 'woocommerce-gateway-chase-paymentech' ),
				'type'     => 'text',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'Optional 3-digit merchant terminal ID assigned by Chase Paymentech: between 001 and 999; typically 001.  Required only for admin capturing authorized charges, or tokenization transactions', 'woocommerce-gateway-chase-paymentech' ),
			),

			'test_terminal_id' => array(
				'title'    => __( 'Terminal ID', 'woocommerce-gateway-chase-paymentech' ),
				'type'     => 'text',
				'class'    => 'environment-field test-field',
				'desc_tip' => __( 'Optional 3-digit merchant terminal ID assigned by Chase Paymentech: between 001 and 999; typically 001.  Required only for admin capturing authorized charges, or tokenization transactions', 'woocommerce-gateway-chase-paymentech' ),
			),

			// before I settled on this I first tried to serve the styles from a PHP script so that additional
			//  styles could be configured in the gateway settings and included.  But of course there's no way
			//  to load WordPress from a random script.  Next I tried to server via the wc-api, which worked,
			//  except that Chase removes the query string.
			'pay_form_css_url' => array (
				'title'    => __( 'Pay Form Style URL', 'woocommerce-gateway-chase-paymentech' ),
				'type'     => 'text',
				'desc_tip' => __( 'Optional URL to a CSS stylesheet to be used to style the pay form.  URL must be on this server, over https.', 'woocommerce-gateway-chase-paymentech' ),
			),

			'test_certification_mode' => array(
				'title'       => __( 'Certification Mode', 'woocommerce-gateway-chase-paymentech' ),
				'label'       => __( 'Enable built-in tools to assist with Chase\'s Orbital Certification process', 'woocommerce-gateway-chase-paymentech' ),
				'description' => sprintf( __( 'Only enable while working through the Orbital Certification Test Cases. %1$sLearn more &raquo;%2$s', 'woocommerce-gateway-chase-paymentech' ), '<a href="https://docs.woocommerce.com/document/woocommerce-chase-paymentech-certification-mode/" target="_blank">', '</a>' ),
				'type'        => 'checkbox',
				'class'       => 'environment-field test-field',
				'default'     => 'no',
			),

		);
	}


	/**
	 * Adds any tokenization form fields for the settings page
	 *
	 * @since 1.2.0
	 * @see Framework\SV_WC_Payment_Gateway::add_tokenization_form_fields()
	 * @param array $form_fields gateway form fields
	 * @return array $form_fields gateway form fields
	 */
	protected function add_tokenization_form_fields( $form_fields ) {

		$form_fields = parent::add_tokenization_form_fields( $form_fields );

		$form_fields['tokenization']['label'] = _x( 'Allow customers to securely save their payment details for future checkout.  This requires the User Name, Password, Merchant ID, and Terminal ID settings below to be properly configured.', 'Supports tokenization', 'woocommerce-gateway-chase-paymentech' );

		return $form_fields;
	}


	/**
	 * Adds the require Card Security Code form fields.
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway::add_csc_form_fields()
	 * @param array $form_fields gateway form fields
	 * @return array $form_fields gateway form fields
	 */
	protected function add_csc_form_fields( $form_fields ) {

		// note that we are *not* calling super, because the CSC field itself
		//  is not optional with Chase Paymentech, only whether it's required is
		//  optional

		$form_fields['require_csc'] = array(
			'title'    => __( 'Require Card Verification', 'woocommerce-gateway-chase-paymentech' ),
			'label'    => __( 'Require the Card Security Code (CV2)', 'woocommerce-gateway-chase-paymentech' ),
			'type'     => 'checkbox',
			'default'  => 'no',
		);

		return $form_fields;
	}


	/**
	 * Returns true if the Card Security Code (CVV) field should be used on checkout
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway::csc_enabled()
	 * @return boolean true if the Card Security Code field should be used on checkout
	 */
	public function csc_enabled() {
		// CSC always enabled for Chase Paymentech
		return true;
	}


	/** Frontend methods ******************************************************/


	/**
	 * Enqueues the required gateway.js library and custom checkout javascript.
	 * Also localizes payment method validation errors
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway::enqueue_scripts()
	 * @return boolean true if the scripts were enqueued, false otherwise
	 */
	public function enqueue_scripts() {

		// if we're not on the pay page for this gateway, bail.  otherwise call to parent and determine whether we need to load
		if ( false === $this->is_pay_page_gateway() || ! parent::enqueue_scripts() ) {
			return false;
		}

		// enqueue the frontend styles
		wp_enqueue_style( 'wc-chase-paymentech', $this->get_plugin()->get_plugin_url() . '/assets/css/frontend/wc-chase-paymentech.min.css', false, WC_Chase_Paymentech::VERSION );

		return true;
	}


	/**
	 * Returns an array of javascript script params to localize for the
	 * checkout/pay page javascript.
	 *
	 * @since 1.6.0
	 * @return array associative array of param name to value
	 */
	protected function get_gateway_js_localized_script_params() {

		// get the general error messages
		$params = parent::get_payment_form_js_localized_script_params();

		// add a couple of chase-specific error messages
		$params = array_merge( $params, array(
			'card_number_missing_or_invalid' => __( 'Card number is missing or invalid', 'woocommerce-gateway-chase-paymentech' ),
			'cvv_incorrect'                  => __( 'Card security code does not match card', 'woocommerce-gateway-chase-paymentech' ),
			'general_error'                  => __( 'An error occurred with your payment, please try again or try another payment method', 'woocommerce-gateway-chase-paymentech' ),
			/* translators: Placeholders: %1$s - <strong>, %2$s - </strong> */
			'what_is_csc'                    => sprintf( __( 'The %1$sCard Security Code%2$s (also known as "CVC" or "CVV") is the last 3 digits on the back of your credit card.  (Amex: 4 digits on front)', 'woocommerce-gateway-chase-paymentech' ), '<strong>', '</strong>' ),
			'what_is_csc_image_url'          => $this->get_plugin()->get_plugin_url() . '/assets/images/what-is-csc.png',
		) );

		// chase error code mapping to messages
		$params['error_codes'] = array(
			'310' => 'card_number_missing_or_invalid',
			'315' => 'card_number_invalid',
			'350' => 'cvv_incorrect',
			'355' => 'cvv_missing',
			'370' => 'card_exp_date_invalid',
		);

		// ajaxurl isn't available on the pay page for whatever reason
		$params['ajaxurl']               = admin_url( 'admin-ajax.php', 'relative' );
		$params['ajax_loader_url']       = WC_HTTPS::force_https_url( $this->get_plugin()->get_framework_assets_url() . '/images/ajax-loader.gif' );
		$params['checkout_url']          = add_query_arg( 'action', 'wc-chase-paymentech-checkout', WC()->ajax_url() );
		$params['update_checkout_nonce'] = wp_create_nonce( 'update-checkout' );

		// get the current order and add the cancel/return URLs
		$order_id = isset( $GLOBALS['wp']->query_vars['order-pay'] ) ? absint( $GLOBALS['wp']->query_vars['order-pay'] ) : 0;

		if ( $order_id ) {

			$order = wc_get_order( $order_id );

			$params['order_id']   = $order_id;
			$params['cancel_url'] = $order->get_checkout_payment_url();
			$params['return_url'] = $this->get_return_url( $order );

		} elseif ( is_add_payment_method_page() ) {

			$params['return_url'] = $params['cancel_url'] = wc_get_account_endpoint_url( 'payment-methods' );
		}

		$params['is_certification_mode'] = $this->is_certification_mode();

		return $params;
	}


	/**
	 * Process the payment by redirecting customer to the pay page from the
	 * Checkout page, or performing a direct tokenized transaction from the
	 * Pay page
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_Direct::process_payment()
	 * @param int $order_id the order to process
	 * @return array with keys 'result' and 'redirect'
	 */
	public function process_payment( $order_id ) {

		$order = wc_get_order( $order_id );

		if ( Framework\SV_WC_Helper::get_posted_value( 'woocommerce_pay_page' ) || ( $this->supports_subscriptions() && wcs_order_contains_renewal( $order_id ) && $this->get_order_meta( $order, 'payment_token' ) ) ) {
			// direct (tokenized) checkout from Pay page or processing subscription renewal (2.0.x only)
			return parent::process_payment( $order_id );
		}

		// set the certification test values
		if ( $this->is_certification_mode() ) {

			$input_id = 'wc-' . $this->get_id_dasherized() . '-test';

			$test_details = array(
				'label'            => Framework\SV_WC_Helper::get_posted_value( $input_id . '-label' ),
				'transaction_type' => Framework\SV_WC_Helper::get_posted_value( $input_id . '-transaction-type' ),
				'amount'           => Framework\SV_WC_Helper::get_posted_value( $input_id . '-amount' ),
			);

			WC()->session->set( 'wc_chase_paymentech_certification_test_details', $test_details );

			// save the test case label for admin display
			$this->update_order_meta( $order, 'certification_test', $test_details['label'] );

			update_post_meta( $order_id, '_order_total', Framework\SV_WC_Helper::number_format( $test_details['amount'] ) );
		}

		WC()->cart->empty_cart();

		return array(
			'result'   => 'success',
			'redirect' => $order->get_checkout_payment_url( true ),
		);
	}


	/** Direct API methods ******************************************************/


	/**
	 * Performs a credit card transaction for the given order and returns the
	 * result.
	 *
	 * Overridden so merchants can specify the secondary failover URL while going
	 * through certification.
	 *
	 * @since 1.11.1
	 *
	 * @param \WC_Order $order the order object
	 * @param Framework\SV_WC_Payment_Gateway_API_Response $response optional credit card transaction response
	 * @return Framework\SV_WC_Payment_Gateway_API_Response response object
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	protected function do_credit_card_transaction( $order, $response = null ) {

		if ( $this->is_certification_mode() && 'yes' === Framework\SV_WC_Helper::get_posted_value( 'wc-chase-paymentech-test-failover' ) ) {
			add_filter( 'wc_chase_paymentech_api_request_url', array( $this, 'get_secondary_api_endpoint' ) );
		}

		return parent::do_credit_card_transaction( $order, $response );
	}


	/**
	 * Add any Chase Paymentech specific payment and transaction information to an order object.
	 *
	 * @see Framework\SV_WC_Payment_Gateway::get_order()
	 *
	 * @since 1.0.0
	 *
	 * @param int $order_id order ID being processed
	 * @return \WC_Order object with payment and transaction information attached
	 */
	public function get_order( $order_id ) {

		// add common order members
		$order = parent::get_order( $order_id );

		// add Chase-specific order properties

		// if ( isset( $order->payment->token ) ) {
			// keep track of the retry trace number
			// commented out for now until we have a clarification on how this retry trace system is supposed to work
			//$this->update_order_meta( $order->get_id(), 'retry_trace_number', $order->get_id() );
		// }

		// when generating the Hosted Pay Form we need to perform a zero-dollar profile creation request for pay upon release pre-orders
		if (
			$this->supports_pre_orders()
			&& \WC_Pre_Orders_Order::order_contains_pre_order( $order_id )
			&& \WC_Pre_Orders_Order::order_requires_payment_tokenization( $order_id )
		) {

			$order->payment_total = 0;
		}

		// handle Subscriptions additional data
		if ( ! empty( $order->payment->subscriptions ) ) {

			foreach ( $order->payment->subscriptions as $i => $subscription_data ) {

				if ( is_object( $subscription_data ) && ! empty( $subscription_data->id ) ) {

					$order->payment->subscriptions[ $i ]->series_id = $this->get_order_meta( $subscription_data->id, 'series_id' );
				}
			}
		}

		return $order;
	}


	/**
	 * Called after an unsuccessful transaction attempt
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_Direct::do_transaction_failed_result()
	 * @param WC_Order $order the order
	 * @param Framework\SV_WC_Payment_Gateway_API_Response $response the transaction response
	 * @return boolean false
	 */
	protected function do_transaction_failed_result( WC_Order $order, Framework\SV_WC_Payment_Gateway_API_Response $response ) {

		// profile not found for customer ref number/merchant id combo so remove the token
		if ( 9581 == $response->get_profile_proc_status() ) {
			$this->get_payment_tokens_handler()->remove_token( $order->get_user_id(), $order->payment->token );
		}

		// pass control back up to parent
		return parent::do_transaction_failed_result( $order, $response );
	}


	/** Redirect API methods ******************************************************/


	/**
	 * No payment fields to display on the checkout page since this is a Pay
	 * Page gateway.
	 *
	 * @since 1.0
	 * @see WC_Payment_Gateway::payment_fields()
	 */
	public function payment_fields() {

		if ( is_add_payment_method_page() ) {

			$this->render_add_payment_method_form();

		} elseif ( $this->is_certification_mode() ) {

			$this->get_plugin()->get_certification_handler()->display_payment_fields();

		} else {

			parent::payment_fields();
		}

		echo '<style type="text/css">#payment ul.payment_methods li label[for="payment_method_' . esc_attr( $this->get_id() ) . '"] img:nth-child(n+2) { margin-left:1px; }</style>';
	}


	/**
	 * No validation as this is a tokenized-only direct gateway
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_Direct::validate_credit_card_fields()
	 * @param boolean $is_valid true if the fields are valid, false otherwise
	 * @return boolean true if the fields are valid, false otherwise
	 */
	protected function validate_credit_card_fields( $is_valid ) {

		if ( ! $this->is_certification_mode() ) {
			return $is_valid;
		}

		if ( '' === Framework\SV_WC_Helper::get_posted_value( 'wc-' . $this->get_id_dasherized() . '-test-amount' ) ) {
			/** translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> **/
			Framework\SV_WC_Helper::wc_add_notice( sprintf( __( '%1$sTest Amount%2$s is a required field.', 'woocommerce-gateway-chase-paymentech' ), '<strong>', '</strong>' ), 'error' );
			$is_valid = false;
		}

		return $is_valid;
	}


	/**
	 * Validates the provided Card Security Code, adding user error messages as
	 * needed
	 *
	 * @since 1.6.1
	 * @param string $csc the customer-provided card security code
	 * @return boolean true if the card security code is valid, false otherwise
	 */
	protected function validate_csc( $csc ) {

		// If this is a tokenized transaction, and the CSC is empty, then there is no CSC field
		if ( $this->supports_tokenization() && Framework\SV_WC_Helper::get_posted_value( 'wc-' . $this->get_id_dasherized() . '-payment-token' ) && empty( $csc ) ) {
			return true;
		}

		return parent::validate_csc( $csc );
	}


	/**
	 * Renders the full payment form on the WooCommerce pay page.
	 *
	 * @since 1.0.0
	 * @deprecated 1.11.1
	 *
	 * @param \WC_Order|int $order the order ID or object
	 */
	public function render_pay_page_pay_form( $order ) {

		$this->payment_page( $order );
	}


	/**
	 * Renders the full payment form on the WooCommerce pay page.
	 *
	 * @since 1.11.1
	 *
	 * @param \WC_Order|int $order_id order object or ID
	 */
	public function payment_page( $order_id ) {

		$order = $this->get_order( $order_id );

		// if Order Abstraction is enabled, get a form uID
		if ( $this->order_abstraction_enabled() ) {

			try {

				$params = array(
					'uID' => $this->get_hosted_payment_form_uid( $order ),
				);

			} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

				$this->mark_order_as_failed( $order, $exception->getMessage() );

				wp_safe_redirect( $order->get_checkout_payment_url() );
				exit;
			}

		} else {

			$params = $this->get_hosted_payment_form_params( $order );

			// log the request
			$this->log_transaction_request( array(
				'uri'    => $this->get_hosted_pay_form_endpoint(),
				'params' => $params,
			) );
		}

		woocommerce_chase_paymentech_payment_fields( $this, $this->get_hosted_pay_form_endpoint(), $params );
	}


	/**
	 * Renders the hosted payment form for the Add Payment Method screen.
	 *
	 * @since 1.9.0
	 */
	private function render_add_payment_method_form() {

		$order  = $this->get_order_for_add_payment_method();
		$params = $this->get_hosted_payment_form_params( $order );

		// store the session ID so the IPN can be validated later.
		// while \WC_Gateway_Chase_Paymentech::get_hosted_payment_form_params()
		// stores this in order meta, the add payment method order doesn't
		// actually exist so we need to save as user meta.
		update_user_meta( get_current_user_id(), '_wc_' . $this->get_id() . '_add_payment_method_session', $params['sessionId'] );

		// if Order Abstraction is enabled, get a form uID
		if ( $this->order_abstraction_enabled() ) {

			try {

				$params = array(
					'uID' => $this->get_hosted_payment_form_uid( $order, $params ),
				);

			} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

				echo '<p class="woocommerce-error">' . esc_html__( 'Oops, something went wrong. Please use a different payment method.', 'woocommerce-gateway-chase-paymentech' ) . '</p>';

				return;
			}

		} else {

			// log the request
			$this->log_transaction_request( array(
				'uri'    => $this->get_hosted_pay_form_endpoint(),
				'params' => $params,
			) );
		}

		$src = add_query_arg( urlencode_deep( $params ), $this->get_hosted_pay_form_endpoint() );

		echo '<iframe id="wc-chase-paymentech-pay-form" name="wc_chase_paymentech_pay_form" height="475" style="width:100%;margin-bottom:0;border:0;" src="' . esc_url( $src ) . '"></iframe>';
	}


	/**
	 * Gets a hosted payment form uID from an order.
	 *
	 * This method doesn't use the standard API classes, as it's a different
	 * API entirely from the Orbital Gateway and no special request params are
	 * required.
	 *
	 * @since 1.11.1
	 *
	 * @param \WC_Order $order order object
	 * @param array $params form params. If not passed, new params will be generated from the order
	 * @return string
	 * @throws Framework\SV_WC_API_Exception
	 */
	private function get_hosted_payment_form_uid( WC_Order $order, array $params = array() ) {

		if ( empty( $params ) ) {
			$params = $this->get_hosted_payment_form_params( $order );
		}

		$params['hostedSecureAPIToken'] = $this->get_secure_api_token();

		// log the request
		$this->log_transaction_request( array(
			'uri'    => $this->get_hosted_pay_form_uid_url(),
			'params' => $params,
		) );

		$response = wp_remote_get( add_query_arg( urlencode_deep( $params ), $this->get_hosted_pay_form_uid_url() ) );

		// catch HTTP errors
		if ( is_wp_error( $response ) ) {
			throw new Framework\SV_WC_API_Exception( $response->get_error_message(), (int) $response->get_error_code() );
		}

		parse_str( $response['body'], $data );

		// catch any errors while creating the uID
		if ( ! empty( $data['error'] ) ) {
			throw new Framework\SV_WC_API_Exception( $data['message'], (int) $data['error'] );
		}

		// this shouldn't happen, but ensure the uID is available
		if ( empty( $data['uID'] ) ) {
			throw new Framework\SV_WC_API_Exception( 'Order Abstraction uID is missing' );
		}

		return $data['uID'];
	}


	/**
	 * Gets the hosted payment form parameters for the given order.
	 *
	 * @since 1.9.0
	 *
	 * @param \WC_Order $order the order object
	 * @return array the hosted payment form parameters
	 */
	private function get_hosted_payment_form_params( WC_Order $order ) {

		$session_id = $this->get_order_meta( $order, 'session_id' );

		if ( ! $session_id ) {

			$session_id = uniqid( $order->get_id() . '-' );

			// record the unique session id so we can validate the IPN response
			$this->update_order_meta( $order, 'session_id', $session_id );
		}

		$params = array(
			'hostedSecureID'       => $this->get_secure_account_id(),
			'action'               => 'buildForm',
			'sessionId'            => $session_id,
			'callback_url'         => $this->get_plugin()->get_plugin_url() . '/assets/js/frontend/Callback.html',
			'css_url'              => $this->get_pay_form_css_url(),
			'payment_type'         => 'Credit_Card',
			'formType'             => '1',
			'allowed_types'        => $this->get_allowed_types(),
			'trans_type'           => $this->perform_credit_card_charge( $order ) ? 'auth_capture' : 'auth_only',
			'required'             => $this->csc_required() ? 'all' : 'minimum',
			'collectAddress'       => '0',
			'amount'               => $order->payment_total,
			'orderId'              => substr( ltrim( $order->get_order_number(), _x( '#', 'hash before order number', 'woocommerce-gateway-chase-paymentech' ) ), 0, 22 ),
			'currency_code'        => substr( $order->get_currency(), 0, 3 ),
			'customer_email'       => substr( $order->get_billing_email( 'edit' ), 0, 50 ),
			'address'              => substr( $order->get_billing_address_1( 'edit' ), 0, 28 ), // integration doc claims 30 but 28 seems to be correct for address lines
			'address2'             => substr( $order->get_billing_address_2( 'edit' ), 0, 28 ),
			'city'                 => substr( $order->get_billing_city( 'edit' ), 0, 20 ),
			'state'                => substr( $order->get_billing_state( 'edit' ), 0, 2 ),
			'zip'                  => substr( self::format_postcode( $order->get_billing_postcode( 'edit' ), $order->get_billing_country( 'edit' ) ), 0, 10 ),
			'delivery_firstname'   => substr( $order->get_shipping_first_name( 'edit' ), 0, 30 ),
			'delivery_lastname'    => substr( $order->get_shipping_last_name( 'edit' ), 0, 30 ),
			'delivery_address'     => substr( $order->get_shipping_address_1( 'edit' ), 0, 28 ),
			'delivery_address2'    => substr( $order->get_shipping_address_2( 'edit' ), 0, 28 ),
			'delivery_email'       => substr( $order->get_billing_email( 'edit' ), 0, 50 ),
			'delivery_phone'       => substr( $order->get_billing_phone( 'edit' ), 0, 14 ),
			'delivery_city'        => substr( $order->get_shipping_city( 'edit' ), 0, 20 ),
			'delivery_state'       => substr( $order->get_shipping_state( 'edit' ), 0, 2 ),
			'delivery_postal_code' => substr( self::format_postcode( $order->get_shipping_postcode( 'edit' ), $order->get_shipping_country( 'edit' ) ), 0, 10 ),
			'delivery_country'     => substr( $order->get_shipping_country( 'edit' ), 0, 2 ),
			'name'                 => substr( $order->get_formatted_billing_full_name(), 0, 30 ),
		);

		// tokenization enabled
		if ( $this->tokenization_enabled() && ( is_add_payment_method_page() || $this->get_payment_tokens_handler()->tokenization_forced() || $this->get_payment_tokens_handler()->should_tokenize() ) ) {

			if ( 0 == $order->payment_total ) {
				$params['hosted_tokenize'] = 'store_only';
			} else {
				$params['hosted_tokenize'] = 'store_authorize';
			}

			$params['mitMsgType']             = 'CSTO';
			$params['mitStoredCredentialInd'] = 'N';
		}

		// CIT param for initial recurring payment transaction
		if ( ! empty ( $order->payment->recurring ) && ! empty( $order->payment->subscriptions ) ) {

			/** @see SV_WC_Payment_Gateway_Integration_Subscriptions::add_subscriptions_details_to_order() */
			foreach ( $order->payment->subscriptions as $subscription_data ) {

				if ( ! $subscription_data->is_renewal ) {

					$params['mitMsgType'] = 'CREC';
					break;
				}
			}
		}

		if ( $this->get_payment_tokens_handler()->tokenized_payment_method_selected() ) {
			$params['mitStoredCredentialInd'] = 'Y';
		}

		/**
		 * Filters the hosted pay form request parameters.
		 *
		 * @since 1.9.0
		 * @param array $params
		 * @param \WC_Order $order the order object. Note that this may be an "add payment method" placeholder order.
		 * @param \WC_Gateway_Chase_Paymentech $gateway the gateway instance
		 */
		return apply_filters( 'wc_payment_gateway_chase_paymentech_pay_form_params', $params, $order, $this );
	}


	/**
	 * Returns true if tokenization is enabled and properly configured
	 *
	 * @since 1.2.0
	 * @see Framework\SV_WC_Payment_Gateway::tokenization_enabled()
	 * @return boolean true if tokenization is enabled
	 */
	public function tokenization_enabled() {

		return parent::tokenization_enabled() && $this->is_direct_api_configured();
	}


	/**
	 * Instantiate the payment tokens handler.
	 *
	 * @since 1.6.0
	 * @return \WC_Chase_Paymentech_Payment_Tokens_Handler
	 */
	protected function build_payment_tokens_handler() {

		return new WC_Chase_Paymentech_Payment_Tokens_Handler( $this );
	}


	/**
	 * Process Chase Paymentech IPN
	 *
	 * @since 1.0
	 */
	public function process_ipn() {

		// load the IPN response class
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/api/class-wc-orbital-gateway-ipn-response.php' );

		// XML request from Chase
		$response_xml = file_get_contents( 'php://input' );

		if ( ! $response_xml ) {
			$this->add_debug_message( __( 'IPN: Response missing or empty!', 'woocommerce-gateway-chase-paymentech' ) );
			die;
		}

		$response = new WC_Orbital_Gateway_IPN_Response( $response_xml );

		// log the response
		$this->log_transaction_response_request( $response->to_string_safe() );

		// order identifier
		$order_id = (int) $response->get_order_id();

		if ( $this->tokenization_enabled() && 0 === $order_id && 0.00 === $response->get_transaction_amount() && $response->get_customer_ref_num() ) {

			$this->process_add_payment_method_ipn( $response );

			// after the payment method is added, we're done
			status_header( 200 );
			die;

		} elseif ( ! $order_id ) {

			$this->add_debug_message( __( 'IPN: Order id missing or empty!', 'woocommerce-gateway-chase-paymentech' ) );
			die;
		}

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			$this->add_debug_message( sprintf( __( 'IPN: Order %s not found!', 'woocommerce-gateway-chase-paymentech' ), $order_id ) );
			die;
		}

		// validate that the session id's match
		if ( $response->get_session_id() !== $this->get_order_meta( $order, 'session_id' ) ) {
			$this->mark_order_as_failed( $order, sprintf( __( 'IPN: Returned session ID does not match session ID saved to Order', 'woocommerce-gateway-chase-paymentech' ), $order_id ), $response );
			die;
		}

		// verify order has not already been completed (skip this check for $0 orders, which probably indicate store-only tokenization request)
		if ( ! $order->needs_payment() && $order->get_total() > 0 ) {
			$this->add_debug_message( sprintf( __( "Order %s is already paid for.", 'woocommerce-gateway-chase-paymentech' ), $order->get_order_number() ) );
			die;
		}

		$order->payment_total = Framework\SV_WC_Helper::number_format( $order->get_total() );

		// if a pay upon release pre-order
		if ( $this->supports_pre_orders() &&
			WC_Pre_Orders_Order::order_contains_pre_order( $order_id ) &&
			WC_Pre_Orders_Order::order_requires_payment_tokenization( $order_id ) ) {

			$order->payment_total = 0;
		}

		if ( $response->transaction_approved() ) {

			// add the standard transaction data
			$this->add_transaction_data_ipn( $order, $response );

			if ( $order->payment_total > 0 ) {

				// approval note
				$message = sprintf(
					/* translators: Placeholders: %1$s - payment method title, %2$s - type of transaction, either an authorization or charge, %3$s - card type name, %4$s - last 4 digits of card, %5$s - card expiration date MM/YY, %6$s - transaction ID */
					__( '%1$s %2$s Approved: %3$s ending in %4$s (expires %5$s) (Transaction ID %6$s)', 'woocommerce-gateway-chase-paymentech' ),
					$this->get_method_title(),
					$this->perform_credit_card_authorization( $order ) ? 'Authorization' : 'Charge',
					$response->get_card_type_name(),
					$response->get_account_last_four(),
					$response->get_exp_month() . '/' . substr( $response->get_exp_year(), -2 ),
					$response->get_transaction_id()
				);

			} else {

				// approval note
				$message = sprintf(
					/* translators: Placeholders: %1$s - payment method title, %2$s - card type name, %3$s - last 4 digits of card, %4$s - card expiration date MM/YY */
					__( '%1$s Payment Method Tokenized: %2$s ending in %3$s (expires %4$s)', 'woocommerce-gateway-chase-paymentech' ),
					$this->get_method_title(),
					$response->get_card_type_name(),
					$response->get_account_last_four(),
					$response->get_exp_month() . '/' . substr( $response->get_exp_year(), -2 )
				);

			}

			// transaction may have succeeded, but we got a profile management error
			if ( $response->has_profile_proc_status() && ! $response->profile_proc_approved() ) {
				/* translators: Placeholders: %1$s - error message, %2$s - status code */
				$message .= ' ' . sprintf( __( 'Profile management error: %1$s (%2$s)', 'woocommerce-gateway-chase-paymentech' ), $response->get_profile_proc_status_message(), $response->get_profile_proc_status() );
			}

			if ( $this->perform_credit_card_charge( $order ) || 0 == $order->payment_total ) {

				$order->add_order_note( $message );

				if ( $this->get_plugin()->is_pre_orders_active() &&
					$this->supports_pre_orders() &&
					WC_Pre_Orders_Order::order_contains_pre_order( $order_id ) &&
					WC_Pre_Orders_Order::order_requires_payment_tokenization( $order_id ) ) {

					// mark order as pre-ordered / reduce order stock
					WC_Pre_Orders_Order::mark_order_as_pre_ordered( $order );

				} else {
					// standard order processing
					$order->payment_complete();
				}

			} else {

				$this->mark_order_as_held( $order, $message, $response );

				// reduce stock for held orders, but don't complete payment yet:
				// we pass an order ID rather than the order object so WooCommerce core will fetch the updated stock to avoid reducing it further
				wc_reduce_stock_levels( $order->get_id() );

			}

		} else {

			// In theory this path should never execute, but just for completeness
			$this->mark_order_as_failed( $order, sprintf( __( 'Status Code: %s', 'woocommerce-gateway-chase-paymentech' ), $response->get_status() ), $response );

		}

		// reply success
		status_header( 200 );
	}


	/**
	 * Adds a new payment method from an IPN response.
	 *
	 * @since 1.9.0
	 * @param \WC_Orbital_Gateway_IPN_Response $response the IPN response object
	 */
	private function process_add_payment_method_ipn( WC_Orbital_Gateway_IPN_Response $response ) {
		global $wpdb;

		$session_key   = '_wc_' . $this->get_id() . '_add_payment_method_session';
		$session_value = $response->get_session_id();

		try {

			// find the user ID that started this session
			$user_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %s", $session_key, $session_value ) );

			if ( ! $user_id ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'IPN user session not found', 'woocommerce-gateway-chase-paymentech' ) );
			}

			// remove the user session meta
			delete_user_meta( $user_id, $session_key );

			if ( $response->transaction_approved() ) {

				$token = $response->get_payment_token();

				$this->get_payment_tokens_handler()->add_token( $user_id, $token );

				$message = sprintf( esc_html__( 'Nice! New payment method added: %1$s ending in %2$s (expires %3$s)', 'woocommerce-plugin-framework' ),
					$token->get_type_full(),
					$token->get_last_four(),
					$token->get_exp_date()
				);

				$result = 'success';

				$this->add_add_payment_method_transaction_data( $response );

			// it probably won't ever come to this, since errors are handled by
			// `wc-chase-paymentech.js`, but just in case
			} else {

				throw new Framework\SV_WC_Plugin_Exception( sprintf(
					/** translators: Placeholders: %s - a failure status code */
					__( 'Status Code: %s', 'woocommerce-gateway-chase-paymentech' ),
					$response->get_status()
				) );
			}

		} catch ( Framework\SV_WC_Plugin_Exception $e ) {

			$result  = 'error';

			/** translators: Placeholders: %s - the reason for failure */
			$this->add_debug_message( sprintf( __( 'Add Payment Method failure. %s', 'woocommerce-gateway-chase-paymentech' ), $e->getMessage() ), $result );

			$message = __( 'Payment method could not be added.', 'woocommerce-gateway-chase-paymentech' );
		}

		Framework\SV_WC_Helper::wc_add_notice( $message, $result );
	}


	/**
	 * Adds the standard transaction data to the order from the IPN response
	 *
	 * @since 1.0
	 * @param WC_Order $order the order object
	 * @param WC_Orbital_Gateway_IPN_Response $response IPN response
	 */
	private function add_transaction_data_ipn( $order, $response ) {

		// transaction date
		$this->update_order_meta( $order, 'trans_date', current_time( 'mysql' ) );

		if ( $order->payment_total > 0 ) {
			$this->update_order_meta( $order, 'charge_captured', $this->perform_credit_card_charge( $order ) ? 'yes' : 'no' );
		}

		if ( $response->get_transaction_id() ) {
			$this->update_order_meta( $order, 'trans_id', $response->get_transaction_id() );
		}

		if ( $response->get_approval_code() ) {
			$this->update_order_meta( $order, 'authorization_code', $response->get_approval_code() );
		}

		$this->update_order_meta( $order, 'account_four',     $response->get_account_last_four() );
		$this->update_order_meta( $order, 'card_expiry_date', $response->get_exp_year() . '-' . $response->get_exp_month() );
		$this->update_order_meta( $order, 'card_type',        $response->get_card_type() );
		$this->update_order_meta( $order, 'environment',      $this->get_environment() );

		// mysterious token id returned?
		if ( $response->get_token_id() ) {
			$this->update_order_meta( $order, 'token_id',  $response->get_token_id() );
		}

		// tokenization customer ref num returned? (normal transaction with tokenization - store_authorize, or simply customer profile creation - store_only)
		if ( ( $this->tokenization_enabled() && $response->profile_proc_approved() ) || ( 0 == $order->payment_total && $response->get_customer_ref_num() ) ) {

			// store the returned customer ref num with the order meta
			$this->update_order_meta( $order, 'payment_token', $response->get_customer_ref_num() );

			// store the token if the user is logged in and this is not certification mode
			if ( $order->get_user_id() ) {

				$this->get_payment_tokens_handler()->add_token( $order->get_user_id(), $response->get_payment_token() );

				// save token for Subscriptions 2.0+
				if ( $this->supports_subscriptions() ) {

					// a single order can contain multiple subscriptions
					foreach ( wcs_get_subscriptions_for_order( $order->get_id() ) as $subscription ) {

						// payment token
						$this->update_order_meta( $subscription, 'payment_token', $response->get_customer_ref_num() );
					}
				}
			}
		}
	}


	/** AJAX methods ******************************************************/


	/**
	 * Handle transaction error
	 *
	 * This is called from an AJAX context because silly Paymentech doesn't
	 * send a transaction error alert
	 *
	 * @since 1.0
	 */
	public function handle_transaction_error() {

		$order_id        = isset( $_GET['orderId'] )        ? $_GET['orderId']        : 0;
		$error_code      = isset( $_GET['errorCode'] )      ? $_GET['errorCode']      : '';
		$gateway_code    = isset( $_GET['gatewayCode'] )    ? $_GET['gatewayCode']    : '';
		$gateway_message = isset( $_GET['gatewayMessage'] ) ? $_GET['gatewayMessage'] : '';

		$order = wc_get_order( $order_id );

		// add error data to certification test details.
		if ( $this->is_certification_mode() ) {

			$test_details = WC()->session->get( 'wc_chase_paymentech_certification_test_details' );

			$test_details['error'] = array(
				'code'    => $gateway_code,
				'message' => $gateway_message,
			);

			WC()->session->set( 'wc_chase_paymentech_certification_test_details', $test_details );
		}

		$error_codes = explode( '|', trim( $error_code, '|' ) );

		$code_to_message = $this->get_gateway_js_localized_script_params();

		$error_messages = array();

		// translate the error codes to messages when possible
		foreach ( $error_codes as $code ) {

			if ( isset( $code_to_message['error_codes'][ $code ] ) ) {
				$index = $code_to_message['error_codes'][ $code ];
				$error_messages[] = $code_to_message[ $index ];
			}
		}

		// compose the order note: include any available error messages
		$order_note = implode( '.  ', $error_messages );
		if ( $order_note )
			$order_note .= '.  ';

		// next: error codes
		$order_note .= sprintf( __( 'Error Codes: %s', 'woocommerce-gateway-chase-paymentech' ), implode( ', ', $error_codes ) );

		// optional gateway code
		if ( $gateway_code ) {
			$order_note .= ' ' . sprintf( __( 'Gateway Code: %s', 'woocommerce-gateway-chase-paymentech' ), $gateway_code );
		}

		// optional gateway message
		if ( $gateway_message ) {
			$order_note .= ' ' . sprintf( __( 'Gateway Message: %s', 'woocommerce-gateway-chase-paymentech' ), $gateway_message );
		}

		if ( $order ) {
			$this->mark_order_as_failed( $order, $order_note );
		} else {
			$this->add_debug_message( $order_note );
		}

		if ( $this->is_detailed_customer_decline_messages_enabled() ) {

			$message_helper = new WC_Chase_Paymentech_Response_Message_Helper;

			// get the code from the gateway message
			preg_match( '#\((.*?)\)#', $gateway_message, $gateway_codes );

			$message_ids = $message_helper->get_message_ids( $gateway_codes );

			$error_messages = array_merge( $error_messages, array_map( array( $message_helper, 'get_user_message' ), $message_ids ) );
		}

		wp_send_json_success( $error_messages );
	}


	/** Helper methods ******************************************************/


	/**
	 * Gets the accepted card types.
	 *
	 * @since 1.0.0
	 * @return string the accepted card types, pipe delimited
	 */
	private function get_allowed_types() {

		$allowed_types = array();

		foreach ( $this->get_card_types() as $type ) {
			$allowed_types[] = Framework\SV_WC_Payment_Gateway_Helper::payment_type_to_name( $type );
		}

		return implode( '|', $allowed_types );
	}


	/**
	 * Log API requests/responses to the checkout page and/or log file
	 *
	 * @since 1.0
	 * @param array $request the request data with members 'method', 'uri' and 'body'
	 * @param array|null $response the response data with members 'code' and 'body', or null
	 */
	public function log_api_communication( $request, $response ) {

		// format the request headers
		$max_header_name = 0;
		foreach ( array_keys( $request['headers'] ) as $name ) {
			$max_header_name = max( $max_header_name, strlen( $name ) );
		}

		$headers = '';
		foreach ( $request['headers'] as $name => $value ) {
			$padding = $max_header_name - strlen( $name );
			$headers .= sprintf( "\t%s: %s%s\n", $name, str_repeat( ' ', $padding ), $value );
		}
		$headers = rtrim( $headers );

		$this->add_debug_message(
			sprintf( __( "Request Time (s): %s\nRequest Method: %s\nRequest URI: %s\nRequest Headers:\n%s\nRequest Body: %s", 'woocommerce-gateway-chase-paymentech' ),
				$request['time'],
				$request['method'],
				$request['uri'],
				$headers,
				$request['body']
			),
			'message',
			true
		);

		if ( $response ) {
			$this->add_debug_message( sprintf( __( "Response Code: %s\nResponse Body: %s", 'woocommerce-gateway-chase-paymentech' ), $response['code'], $response['body'] ), 'message', true );
		}

	}


	/**
	 * Log redirect API requests to the checkout page and/or log file
	 *
	 * @since 1.0
	 * @param array $request the request data with members 'method', 'uri' and 'body'
	 */
	public function log_transaction_request( $request ) {

		$this->add_debug_message( sprintf( __( "Request URI: %s\nRequest Params: %s", 'woocommerce-gateway-chase-paymentech' ), $request['uri'], print_r( $request['params'], true ) ), 'message', true );
	}


	/**
	 * Log redirect API responses to the checkout page and/or log file
	 *
	 * @since 1.0
	 * @param string $response the response data
	 */
	public function log_transaction_response_request( $response ) {

		$this->add_debug_message( sprintf( __( "Response Body: %s", 'woocommerce-gateway-chase-paymentech' ), $response ), 'message' );
	}


	/** Subscriptions/Pre-orders ******************************************************/


	/**
	 * Tweak the labels shown when editing the payment method for a Subscription
	 *
	 * @hooked from Framework\SV_WC_Payment_Gateway_Integration_Subscriptions
	 *
	 * @since 1.4.2
	 * @see Framework\SV_WC_Payment_Gateway_Integration_Subscriptions::admin_add_payment_meta()
	 * @param array $meta payment meta
	 * @param \WC_Subscription $subscription subscription being edited, unused
	 * @return array
	 */
	public function subscriptions_admin_add_payment_meta( $meta, $subscription ) {

		if ( isset( $meta[ $this->get_id() ] ) ) {

			$meta[ $this->get_id() ]['post_meta'][ $this->get_order_meta_prefix() . 'payment_token' ]['label'] = __( 'Customer Profile ID', 'woocommerce-gateway-chase-paymentech' );

			// customer ID is not used
			unset( $meta[ $this->get_id() ]['post_meta'][ $this->get_order_meta_prefix() . 'customer_id' ] );
		}

		return $meta;
	}


	/**
	 * Validate the payment meta for a Subscription by ensuring the customer
	 * profile ID is numeric
	 *
	 *
	 * @since 1.4.2
	 * @see Framework\SV_WC_Payment_Gateway_Integration_Subscriptions::admin_validate_payment_meta()
	 * @param array $meta payment meta
	 * @throws \Exception if payment profile/customer profile IDs are not numeric
	 */
	public function subscriptions_admin_validate_payment_meta( $meta ) {

		// customer profile ID (payment_token) must be numeric
		if ( ! ctype_digit( (string) $meta['post_meta'][ $this->get_order_meta_prefix() . 'payment_token' ]['value'] ) ) {
			throw new \Exception( __( 'Customer Profile ID must be numeric.', 'woocommerce-gateway-chase-paymentech' ) );
		}
	}


	/**
	 * Returns meta keys to be excluded when copying over meta data when:
	 *
	 * + a renewal order is created from a subscription
	 * + the user changes their payment method for a subscription
	 * + processing the upgrade from Subscriptions 1.5.x to 2.0.x
	 *
	 * @since 1.4.2
	 * @param array $meta_keys
	 * @return array
	 */
	public function subscriptions_get_excluded_order_meta_keys( $meta_keys ) {

		$meta_keys[] = $this->get_order_meta_prefix() . 'retry_trace_number';
		$meta_keys[] = $this->get_order_meta_prefix() . 'session_id';
		$meta_keys[] = $this->get_order_meta_prefix() . 'token_id';

		return $meta_keys;
	}


	/**
	 * Marks the given order as failed and set the order note.
	 *
	 * Overridden to avoid incorrect error reporting after a transaction is successfully
	 * completed.
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Order $order order object
	 * @param string $error_message a message to display inside the "Payment Failed" order note
	 * @param Framework\SV_WC_Payment_Gateway_API_Response $response API response object
	 */
	public function mark_order_as_failed( $order, $error_message, $response = null ) {

		/* translators: Placeholders: %1$s - payment method title, %2$s - error message */
		$order_note = sprintf( __( '%1$s Payment Failed (%2$s)', 'woocommerce-gateway-chase-paymentech' ), $this->get_method_title(), $error_message );

		// Mark order as failed if not already set, otherwise, make sure we add the order note so we can detect when someone fails to check out multiple times
		if ( ! $order->has_status( 'failed' ) ) {
			$order->update_status( 'failed', $order_note );
		} else {
			$order->add_order_note( $order_note );
		}

		$this->add_debug_message( $error_message, 'error' );

		// user message
		$user_message = '';

		if ( $response && $this->is_detailed_customer_decline_messages_enabled() ) {
			$user_message = $response->get_user_message();
		}

		if ( ! $user_message ) {
			$user_message = esc_html__( 'An error occurred, please try again or try an alternate form of payment.', 'woocommerce-gateway-chase-paymentech' );
		}

		// modification is to not add the "an error occurred" notice during an ajax request, otherwise it will be displayed on the "thank you" page after there's a failure during checkout in the iframe
		if ( ! is_ajax() || $response ) {
			Framework\SV_WC_Helper::wc_add_notice( $user_message, 'error' );
		}
	}


	/** Certification Mode ******************************************************/


	/**
	 * Returns true if the gateway is currently in certification mode
	 *
	 * @since 1.1.0
	 * @return boolean true if orbital certification mode is enabled, false otherwise
	 */
	public function is_certification_mode() {
		return $this->is_test_environment() && 'yes' === $this->test_certification_mode;
	}


	/**
	 * Determines if a credit card charge should be performed.
	 *
	 * @since 1.1.0
	 *
	 * @param \WC_Order $order order object
	 * @return bool
	 */
	public function perform_credit_card_charge( WC_Order $order = null ) {

		if ( $this->is_certification_mode() ) {

			// get the current test's data
			$test_details = WC()->session->get( 'wc_chase_paymentech_certification_test_details' );

			return 'auth_capture' === $test_details['transaction_type'];
		}

		return parent::perform_credit_card_charge( $order );
	}

	/**
	 * Determines if a credit card authorization should be performed.
	 *
	 * @since 1.1.0
	 *
	 * @param \WC_Order $order order object
	 * @return bool
	 */
	public function perform_credit_card_authorization( WC_Order $order = null ) {

		if ( $this->is_certification_mode() ) {

			// get the current test's data
			$test_details = WC()->session->get( 'wc_chase_paymentech_certification_test_details' );

			return 'auth_only' === $test_details['transaction_type'];
		}

		return parent::perform_credit_card_authorization( $order );
	}


	/**
	 * Returns true if a transaction should be forced (meaning payment
	 * processed even if the order amount is 0).  This is useful mostly for
	 * testing situations
	 *
	 * @since 1.1.0
	 * @see Framework\SV_WC_Payment_Gateway_Direct::transaction_forced()
	 * @return boolean true if the transaction request should be forced
	 */
	public function transaction_forced() {

		if ( $this->is_certification_mode() ) {
			return true;
		}

		return parent::transaction_forced();
	}


	/** Getter methods ******************************************************/


	/**
	 * Gets the API object, loading any required files
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway::get_api()
	 * @return WC_Orbital_Gateway_API API instance
	 */
	public function get_api() {

		if ( isset( $this->api ) ) {
			return $this->api;
		}

		require_once( $this->get_plugin()->get_plugin_path() . '/includes/api/class-wc-orbital-gateway-api.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/api/class-wc-orbital-gateway-api-request.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/api/class-wc-orbital-gateway-api-response.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/api/class-wc-orbital-gateway-api-capture-response.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/api/class-wc-orbital-gateway-api-refund-response.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/api/class-wc-orbital-gateway-api-void-response.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/api/class-wc-orbital-gateway-api-new-order-response.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/api/class-wc-orbital-gateway-api-profile-delete-response.php' );

		return $this->api = new WC_Orbital_Gateway_API( $this->get_api_endpoint(), $this->get_secondary_api_endpoint(), $this->get_username(), $this->get_password(), $this->get_merchant_id(), $this->get_terminal_id() );
	}


	/**
	 * Returns the Orbital Connection API endpoint for the current environment
	 *
	 * @since 1.0
	 * @return string Orbital Connection API endpoint URL for the current environment
	 */
	private function get_api_endpoint() {
		return $this->is_production_environment() ? self::PRODUCTION_ORBITAL_GATEWAY_PRIMARY_ENDPOINT : self::TEST_ORBITAL_GATEWAY_PRIMARY_ENDPOINT;
	}


	/**
	 * Returns the Orbital Connection Secondary API endpoint for the current environment.
	 *
	 * This is used as a retry backup in case of connection failure, as recommended by Paymentech.
	 *
	 * @since 1.11.1
	 *
	 * @return string
	 */
	public function get_secondary_api_endpoint() {
		return $this->is_production_environment() ? self::PRODUCTION_ORBITAL_GATEWAY_SECONDARY_ENDPOINT : self::TEST_ORBITAL_GATEWAY_SECONDARY_ENDPOINT;
	}


	/**
	 * Chase Paymentech is a hybrid hosted/direct gateway
	 *
	 * @see Framework\SV_WC_Payment_Gateway::is_hosted_gateway()
	 * @since 1.0
	 * @return boolean if this is a hosted payment gateway
	 */
	public function is_hosted_gateway() {
		return true;
	}


	/**
	 * Returns true if the CSC is required
	 *
	 * @since 1.0
	 * @return boolean true if the CSC is required
	 */
	public function csc_required() {

		return 'yes' === $this->require_csc;
	}


	/**
	 * Determines if order abstraction is enabled.
	 *
	 * @since 1.11.1
	 *
	 * @return bool
	 */
	public function order_abstraction_enabled() {

		/**
		 * Filters whether Order Abstraction is enabled.
		 *
		 * Currently this is enabled by filling in the Secure API Token setting.
		 *
		 * @since 1.11.1
		 *
		 * @param bool $is_enabled whether order abstraction is enabled
		 * @param \WC_Gateway_Chase_Paymentech $gateway gateway instance
		 */
		return (bool) apply_filters( 'wc_chase_paymentech_order_abstraction_enabled', ( $this->get_secure_api_token() ), $this );
	}


	/**
	 * Returns the hosted pay form endpoint URL for the configured environment
	 *
	 * @since 1.0
	 * @return string the hosted pay form endpoint URL
	 */
	public function get_hosted_pay_form_endpoint() {
		return $this->is_production_environment() ? self::PRODUCTION_HOSTED_PAY_FORM_ENDPOINT : self::TEST_HOSTED_PAY_FORM_ENDPOINT;
	}


	/**
	 * Returns the hosted pay form UID URL for the configured environment.
	 *
	 * @since 1.11.1
	 *
	 * @return string
	 */
	public function get_hosted_pay_form_uid_url() {
		return $this->is_production_environment() ? self::PRODUCTION_HOSTED_PAY_FORM_UID_URL : self::TEST_HOSTED_PAY_FORM_UID_URL;
	}


	/**
	 * Returns the secure account id for the current environment
	 *
	 * @since 1.0
	 * @return string the secure account id to use
	 */
	public function get_secure_account_id() {
		return $this->is_production_environment() ? $this->secure_account_id : $this->test_secure_account_id;
	}


	/**
	 * Returns the secure API token for the current environment.
	 *
	 * This is used for Order Abstraction. If it is not set, the payment form
	 * will be generated using traditional means.
	 *
	 * @since 1.11.1
	 *
	 * @return string
	 */
	public function get_secure_api_token() {
		return $this->is_production_environment() ? $this->secure_api_token : $this->test_secure_api_token;
	}


	/**
	 * Returns the Connection Username set up on Orbital Gateway, for the current environment
	 *
	 * @since 1.0
	 * @return string the connection username to use
	 */
	public function get_username() {
		return $this->is_production_environment() ? $this->username : $this->test_username;
	}


	/**
	 * Returns the Connection Password used in conjunction with Orbital Username, for the current environment
	 *
	 * @since 1.0
	 * @return string the connection password to use
	 */
	public function get_password() {
		return $this->is_production_environment() ? $this->password : $this->test_password;
	}


	/**
	 * Returns the 12-digit gateway merchant account number assigned by Chase Paymentech, for the current environment
	 *
	 * @since 1.0
	 * @return string the merchant account number to use
	 */
	public function get_merchant_id() {
		return $this->is_production_environment() ? $this->merchant_id : $this->test_merchant_id;
	}


	/**
	 * Returns the 3-digit merchant terminal ID assigned by Chase Paymentech
	 *
	 * @since 1.0
	 * @return string the merchant terminal ID to use
	 */
	public function get_terminal_id() {
		return $this->is_production_environment() ? $this->terminal_id : $this->test_terminal_id;
	}


	/**
	 * Return the URL to the CSS stylesheet used to style the Pay Form,
	 * defaulting to assets/css/frontend/pay-form.css
	 *
	 * @since 1.0
	 * @return string CSS stylesheet URL
	 */
	private function get_pay_form_css_url() {
		return $this->pay_form_css_url ? $this->pay_form_css_url : $this->get_plugin()->get_plugin_url() . '/assets/css/frontend/pay-form.css';
	}


	/**
	 * Overridden because Chase Paymentech doesn't use a customer id
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway::get_customer_id_user_meta_name()
	 * @param $environment_id
	 * @return bool false
	 */
	public function get_customer_id_user_meta_name( $environment_id = null ) {
		return false;
	}


	/**
	 * Overridden because Chase Paymentech doesn't use a customer id
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway::get_guest_customer_id()
	 * @param WC_Order $order
	 * @return bool false
	 */
	public function get_guest_customer_id( WC_Order $order ) {
		return false;
	}


	/**
	 * Overridden because Chase Paymentech doesn't use a customer id
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway::get_customer_id()
	 * @param int $user_id
	 * @param array $args
	 * @return bool false
	 */
	public function get_customer_id( $user_id, $args = array() ) {
		return false;
	}


	/**
	 * Formats a postcode based on country.
	 *
	 * Right now this just properly formats US +4 postcodes, as WooCommerce itself will not.
	 *
	 * @since 1.8.1
	 * @param string $postcode the postcode to format
	 * @param string $country_code the country code
	 * @return string
	 */
	public static function format_postcode( $postcode, $country_code ) {

		switch ( $country_code ) {

			case 'US' :

				if ( strlen( $postcode ) > 5 && false === strpos( $postcode, '-' ) ) {
					$postcode = trim( substr_replace( $postcode, '-', 5, 0 ) );
				}

			break;
		}

		return $postcode;
	}


}
