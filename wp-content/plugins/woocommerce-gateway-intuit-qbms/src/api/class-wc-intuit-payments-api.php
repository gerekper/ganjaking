<?php
/**
 * WooCommerce Intuit Payments
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Intuit QBMS to newer
 * versions in the future. If you wish to customize WooCommerce Intuit QBMS for your
 * needs please refer to https://docs.woocommerce.com/document/intuit-qbms/
 *
 * @package   WC-Intuit-Payments/API
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2023, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_4 as Framework;

/**
 * The base Payments API class.
 *
 * API reference: https://developer.intuit.com/docs/api/payments
 *
 * @since 2.0.0
 *
 * @method \WC_Intuit_Payments_API_Response get_response()
 */
class WC_Intuit_Payments_API extends Framework\SV_WC_API_Base implements Framework\SV_WC_Payment_Gateway_API {


	/** @var string the Payments API version */
	const VERSION = '4';


	/** @var \WC_Order order object associated with this request */
	private $order;

	/** @var \WC_Gateway_Inuit_Payments the gateway instance */
	protected $gateway;


	/**
	 * Constructs the class.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Gateway_Inuit_Payments $gateway gateway object
	 */
	public function __construct( \WC_Gateway_Inuit_Payments $gateway ) {

		$this->gateway = $gateway;

		$this->request_uri = $this->get_gateway()->get_api_endpoint() . '/quickbooks/v' . self::VERSION;

		$this->set_request_content_type_header( 'application/json' );
		$this->set_request_accept_header( 'application/json' );
	}


	/**
	 * Gets the request URL query.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	protected function get_request_query() {

		return rawurldecode( parent::get_request_query() );
	}


	/**
	 * Perform the request and return a parsed response.
	 *
	 * Overridden to set the oAuth header after the request object is available
	 * (so that the method & URL are accurate) but before the request is
	 * performed.
	 *
	 * @since 2.0.0
	 * @param \WC_Intuit_Payments_API_Request $request the request object
	 * @return \WC_Intuit_Payments_API_Credit_Card_Response|\WC_Intuit_Payments_API_Payment_Refund_Response|\WC_Intuit_Payments_API_Get_Payment_Methods_Response|Framework\SV_WC_Payment_Gateway_API_Create_Payment_Token_Response|\WC_Intuit_Payments_API_oAuth_Response|\WC_Intuit_Payments_API_OAuth2_Response response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	protected function perform_request( $request ) {

		$this->request = $request;

		$this->set_auth_header();

		return parent::perform_request( $request );
	}


	/**
	 * Performs a credit card authorization for the given order.
	 *
	 * @since 2.0.0
	 * @param \WC_Order $order the order object
	 * @return \WC_Intuit_Payments_API_Credit_Card_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function credit_card_authorization( \WC_Order $order ) {

		$request = $this->get_new_credit_card_request( $order );

		$request->set_authorization_data();

		return $this->perform_request( $request );
	}


	/**
	 * Performs a credit card charge for the given order.
	 *
	 * @since 2.0.0
	 * @param \WC_Order $order the order object
	 * @return \WC_Intuit_Payments_API_Credit_Card_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function credit_card_charge( \WC_Order $order ) {

		$request = $this->get_new_credit_card_request( $order );

		$request->set_charge_data();

		return $this->perform_request( $request );
	}


	/**
	 * Performs a credit card capture for a given authorized order.
	 *
	 * @since 2.0.0
	 * @param \WC_Order $order the order object
	 * @return \WC_Intuit_Payments_API_Credit_Card_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function credit_card_capture( \WC_Order $order ) {

		$request = $this->get_new_credit_card_request( $order );

		$request->set_capture_data();

		return $this->perform_request( $request );
	}


	/**
	 * Performs an eCheck debit (ACH transaction) for the given order.
	 *
	 * @since 2.0.0
	 * @param \WC_Order $order the order object
	 * @return \WC_Intuit_Payments_API_Credit_Card_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function check_debit( \WC_Order $order ) {

		$request = $this->get_new_echeck_request( $order );

		$request->set_echeck_data();

		return $this->perform_request( $request );
	}


	/**
	 * Performs a refund for the given order.
	 *
	 * @since 2.0.0
	 * @param \WC_Order $order the order object
	 * @return \WC_Intuit_Payments_API_Payment_Refund_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function refund( \WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request( array(
			'type'         => 'refund',
			'payment_type' => $this->get_gateway()->get_payment_type(),
		) );

		$request->set_refund_data();

		return $this->perform_request( $request );
	}


	/**
	 * The Intuit Payments API does no support voids.
	 *
	 * @since 2.0.0
	 * @param \WC_Order $order the order object
	 * @return \WC_Intuit_Payments_API_Payment_Refund_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function void( \WC_Order $order ) {

		// if there was already a refund request that returned as a void, use it
		if ( $this->get_response() instanceof WC_Intuit_Payments_API_Payment_Refund_Response ) {

			return $this->get_response();

		// otherwise, process a new refund
		} else {

			return $this->refund( $order );
		}
	}


	/**
	 * Creates a payment token for the given order.
	 *
	 * @since 2.0.0
	 * @param \WC_Order $order the order object
	 * @return Framework\SV_WC_Payment_Gateway_API_Create_Payment_Token_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function tokenize_payment_method( \WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request( array(
			'type'         => 'payment-method',
			'payment_type' => $this->get_order()->payment->type,
			'customer_id'  => $this->get_order()->customer_id,
		) );

		$request->set_create_method_data( $this->get_order()->payment->js_token );

		return $this->perform_request( $request );
	}


	/**
	 * Removes a tokenized payment methods for the customer.
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_Payment_Gateway_API::supports_remove_tokenized_payment_method()
	 * @param string $token_id token ID
	 * @param string $customer_id the customer ID
	 * @return \WC_Intuit_Payments_API_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function remove_tokenized_payment_method( $token_id, $customer_id ) {

		$request = $this->get_new_request( array(
			'type'         => 'payment-method',
			'payment_type' => $this->get_gateway()->get_payment_type(),
			'customer_id'  => $customer_id,
		) );

		$request->set_delete_method_data( $token_id );

		return $this->perform_request( $request );
	}


	/**
	 * Determines if this API supports a "remove tokenized payment method" request.
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_Payment_Gateway_API::remove_tokenized_payment_method()
	 * @return bool
	 */
	public function supports_remove_tokenized_payment_method() {

		return true;
	}


	/**
	 * Determines if the API supports updating payment methods.
	 *
	 * Intuit does not support updating payment methods via API.
	 *
	 * @since 2.3.3
	 *
	 * @return false
	 */
	public function supports_update_tokenized_payment_method() {

		return false;
	}


	/**
	 * Gets all tokenized payment methods for the customer.
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_Payment_Gateway_API::supports_get_tokenized_payment_methods()
	 * @param string $customer_id the customer ID
	 * @return \WC_Intuit_Payments_API_Get_Payment_Methods_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_tokenized_payment_methods( $customer_id ) {

		$request = $this->get_new_request( array(
			'type'         => 'payment-method',
			'payment_type' => $this->get_gateway()->get_payment_type(),
			'customer_id'  => $customer_id,
		) );

		$this->set_response_handler( 'WC_Intuit_Payments_API_Get_Payment_Methods_Response' );

		return $this->perform_request( $request );
	}


	/**
	 * Determines if this API supports a "get tokenized payment methods" request.
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_Payment_Gateway_API::get_tokenized_payment_methods()
	 * @return bool
	 */
	public function supports_get_tokenized_payment_methods() {

		return true;
	}


	/**
	 * Gets a new credit card payment request.
	 *
	 * @since 2.0.0
	 * @param \WC_Order $order the order object
	 * @return \WC_Intuit_Payments_API_Credit_Card_Request
	 * @throws Framework\SV_WC_API_Exception
	 */
	protected function get_new_credit_card_request( \WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request( array(
			'type'         => 'payment',
			'payment_type' => 'credit_card',
		) );

		return $request;
	}


	/**
	 * Gets a new echeck payment request.
	 *
	 * @since 2.0.0
	 * @param \WC_Order $order the order object
	 * @return \WC_Intuit_Payments_API_eCheck_Request
	 * @throws Framework\SV_WC_API_Exception
	 */
	protected function get_new_echeck_request( \WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request( array(
			'type'         => 'payment',
			'payment_type' => 'echeck',
		) );

		return $request;
	}


	/**
	 * Gets a new request object.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args request args
	 * @return \WC_Intuit_Payments_API_Credit_Card_Request|\WC_Intuit_Payments_API_eCheck_Request|\WC_Intuit_Payments_API_Payment_Method_Request|\WC_Intuit_Payments_API_OAuth2_Request|\WC_Intuit_Payments_API_oAuth_Request
	 * @throws Framework\SV_WC_API_Exception
	 */
	protected function get_new_request( $args = array() ) {

		$args = wp_parse_args( $args, array(
			'type'         => '',
			'payment_type' => '',
			'customer_id'  => 0,
		) );

		if ( 'oauth' !== $args['type'] && 'oauth2' !== $args['type'] ) {

			$this->maybe_refresh_access_tokens();

			// set the header back to JSON
			$this->set_request_content_type_header( 'application/json' );
		}

		$args['payment_type'] = str_replace( '-', '_', $args['payment_type'] );

		switch ( $args['type'] ) {

			// OAuth 2 requests
			case 'oauth2':

				$this->set_request_content_type_header( 'application/x-www-form-urlencoded' );

				$request = new WC_Intuit_Payments_API_OAuth2_Request();

				$this->set_response_handler( 'WC_Intuit_Payments_API_OAuth2_Response' );

			break;

			// OAuth 1 requests
			case 'oauth':

				$request = new WC_Intuit_Payments_API_oAuth_Request( $this->get_request_uri(), $this->get_gateway() );

				$this->set_response_handler( 'WC_Intuit_Payments_API_oAuth_Response' );

			break;

			// payment requests
			case 'payment':
				$request = $this->get_new_payment_request( $args['payment_type'] );
			break;

			case 'refund':

				$request = $this->get_new_payment_request( $args['payment_type'] );

				if ( 'credit_card' === $args['payment_type'] ) {
					$this->set_response_handler( 'WC_Intuit_Payments_API_Credit_Card_Refund_Response' );
				} elseif ( 'echeck' === $args['payment_type'] ) {
					$this->set_response_handler( 'WC_Intuit_Payments_API_eCheck_Refund_Response' );
				}

			break;

			// payment method request
			case 'payment-method':

				if ( ! $args['customer_id'] ) {
					throw new Framework\SV_WC_API_Exception( 'Customer ID is missing or invalid' );
				}

				$request = new WC_Intuit_Payments_API_Payment_Method_Request( $args['customer_id'], $args['payment_type'] );

				$this->set_response_handler( 'WC_Intuit_Payments_API_Payment_Method_Response' );

			break;

			// no matching request type, bail
			default:
				throw new Framework\SV_WC_API_Exception( 'Invalid request type' );
		}

		return $request;
	}


	/**
	 * Gets a new payment request.
	 *
	 * @since 2.0.0
	 * @param string $type the payment type.
	 * @return \WC_Intuit_Payments_API_Payment_Request
	 * @throws Framework\SV_WC_API_Exception
	 */
	protected function get_new_payment_request( $type = '' ) {

		// an order is required for payment requests
		if ( ! $this->get_order() ) {
			throw new Framework\SV_WC_API_Exception( 'Order is missing or invalid' );
		}

		switch ( $type ) {

			case 'credit_card':

				$request_class  = 'WC_Intuit_Payments_API_Credit_Card_Request';
				$response_class = 'WC_Intuit_Payments_API_Credit_Card_Response';

			break;

			case 'echeck':

				$request_class  = 'WC_Intuit_Payments_API_eCheck_Request';
				$response_class = 'WC_Intuit_Payments_API_eCheck_Response';

			break;

			// no matching payment type, bail
			default:
				throw new Framework\SV_WC_API_Exception( 'Invalid payment type' );
		}

		$this->set_response_handler( $response_class );

		return new $request_class( $this->get_order() );
	}


	/**
	 * Validates the response data before it has been parsed.
	 *
	 * @since 2.0.0
	 *
	 * @throws Framework\SV_WC_API_Exception
	 */
	protected function do_pre_parse_response_validation() {

		if ( 401 === $this->get_response_code() ) {

			// try and refresh access
			// if that fails, set an option so the admin knows
			if ( ! $this->maybe_refresh_access_tokens() ) {
				update_option( 'wc_intuit_payments_connected', 'no' );
			}

			throw new Framework\SV_WC_API_Exception( 'Invalid access token.' );
		}

		// if we made it this far, it means we're okay
		update_option( 'wc_intuit_payments_connected', 'yes' );
	}


	/**
	 * Validates the response data after it has been parsed.
	 *
	 * @since 2.0.0
	 *
	 * @throws Framework\SV_WC_API_Exception
	 */
	protected function do_post_parse_response_validation() {

		$response = $this->get_response();

		if ( $response->has_api_errors() ) {

			$errors = $response->get_api_errors();

			$messages = array();

			foreach ( $errors->get_error_codes() as $code ) {
				$messages[] = '[' . $code . '] ' . $errors->get_error_message( $code );
			}

			$message = implode( '. ', $messages );

			throw new Framework\SV_WC_API_Exception( $message );
		}
	}


	/** oAuth 2.0 Methods *****************************************************/


	/**
	 * Gets oAuth tokens from an authorization code.
	 *
	 * @since 2.1.0
	 *
	 * @param string $code authorization code, returned after the user authorizes their app
	 * @param string $redirect_uri redirect URI
	 * @return \WC_Intuit_Payments_API_OAuth2_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_oauth_tokens( $code, $redirect_uri ) {

		$this->request_uri = 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer';

		$request = $this->get_new_request( array(
			'type' => 'oauth2',
		) );

		$request->set_authorization_data( $code, $redirect_uri );

		return $this->perform_request( $request );
	}


	/**
	 * Refreshes the oAuth tokens.
	 *
	 * @since 2.1.0
	 *
	 * @return \WC_Intuit_Payments_API_OAuth2_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function refresh_oauth_tokens() {

		$this->request_uri = 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer';

		$request = $this->get_new_request( array(
			'type' => 'oauth2',
		) );

		$request->set_refresh_data( $this->get_gateway()->get_connection_handler()->get_refresh_token() );

		return $this->perform_request( $request );
	}


	/**
	 * Refreshes the OAuth 2 access token if it's expired.
	 *
	 * @since 2.1.0
	 *
	 * @return bool
	 */
	protected function maybe_refresh_access_tokens() {

		$return = false;

		$request_uri = $this->request_uri;

		if ( time() >= $this->get_gateway()->get_connection_handler()->get_access_token_expiry() ) {

			try {

				$response = $this->refresh_oauth_tokens();

				$this->get_gateway()->get_connection_handler()->set_access_token( $response->get_access_token() );
				$this->get_gateway()->get_connection_handler()->set_refresh_token( $response->get_refresh_token() );
				$this->get_gateway()->get_connection_handler()->set_access_token_expiry( $response->get_access_token_expiry() );

				$return = true;

			} catch ( Framework\SV_WC_API_Exception $e ) {

				$this->get_plugin()->log( 'Could not refresh access tokens. ' . $e->getMessage(), $this->get_gateway()->get_id() );

				$return = false;
			}
		}

		$this->request_uri = $request_uri;

		return $return;
	}


	/**
	 * Sets the oAuth header.
	 *
	 * @since 2.0.0
	 */
	protected function set_auth_header() {

		// if this is specifically an OAuth 2 request, like when connecting
		if ( $this->get_request() instanceof WC_Intuit_Payments_API_OAuth2_Request ) {

			$this->set_http_basic_auth( $this->get_gateway()->get_client_id(), $this->get_gateway()->get_client_secret() );

		} else {

			$this->request_headers['Authorization'] = 'Bearer ' . $this->get_gateway()->get_connection_handler()->get_access_token();
		}

		$this->request_headers['Request-ID'] = uniqid();
	}


	/**
	 * Determines if this API requires TLS v1.2
	 *
	 * @since 2.1.1
	 *
	 * @return bool
	 */
	public function require_tls_1_2() {

		return true;
	}


	/**
	 * Gets the order object associated with the request, if any.
	 *
	 * @since 2.0.0
	 * @return \WC_Order|null
	 */
	public function get_order() {

		return $this->order;
	}


	/**
	 * Gets the ID for the API.
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_API_Base::get_api_id()
	 * @return string
	 */
	protected function get_api_id() {

		return $this->get_gateway()->get_id();
	}


	/**
	 * Gets the gateway instance.
	 *
	 * @since 2.0.0
	 * @return \WC_Gateway_Inuit_Payments
	 */
	protected function get_gateway() {

		return $this->gateway;
	}


	/**
	 * Gets the plugin instance.
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_API_Base::get_plugin()
	 * @return \WC_Intuit_Payments
	 */
	protected function get_plugin() {

		return wc_intuit_payments();
	}


	/** No-Op Methods *********************************************************/


	/**
	 * No-Op - Intuit does not support updating payment methods via API.
	 *
	 * @since 2.3.3
	 *
	 * @param \WC_Order $order order object
	 */
	public function update_tokenized_payment_method( \WC_Order $order ) { }


}
