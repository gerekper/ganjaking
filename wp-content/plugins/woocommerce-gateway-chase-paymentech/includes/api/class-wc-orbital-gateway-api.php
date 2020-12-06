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

use SkyVerge\WooCommerce\PluginFramework\v5_10_1 as Framework;

/**
 * Chase Paymentech Orbital Gateway API Class
 *
 * Handles sending/receiving/parsing of Orbital Gateway XML, this is the main API
 * class responsible for communication with the Orbital Gateway API
 *
 * @since 1.0
 */
class WC_Orbital_Gateway_API implements Framework\SV_WC_Payment_Gateway_API {

	/** @var string API URL endpoint */
	private $endpoint;

	/** @var string secondary API URL endpoint for failover requests */
	private $secondary_endpoint;

	/** @var string Connection Username set up on Orbital Gateway */
	private $username;

	/** @var string Connection Password used in conjunction with Orbital Username */
	private $password;

	/** @var string 12-digit gateway merchant account number assigned by Chase Paymentech */
	public $merchant_id;

	/** @var string 3-digit merchant terminal ID assigned by Chase Paymentech */
	private $terminal_id;

	/** @var Framework\SV_WC_Payment_Gateway_API_Request most recent request */
	private $request;

	/** @var Framework\SV_WC_Payment_Gateway_API_Response most recent response */
	private $response;

	/** @var \WC_Order|null order associated with the request, if any */
	protected $order;


	/**
	 * Constructor - setup request object and set endpoint
	 *
	 * @since 1.0.0
	 *
	 * @param string $api_endpoint API URL endpoint
	 * @param string $secondary_endpoint secondary API URL endpoint for failover requests
	 * @param string $username Orbital Connection Username set up on Orbital Gateway
	 * @param string $password Orbital Connection Password used in conjunction with Orbital Username
	 * @param string $merchant_id 12-digit gateway merchant account number assigned by Chase Paymentech
	 * @param string 3-digit merchant terminal ID assigned by Chase Paymentech
	 */
	public function __construct( $api_endpoint, $secondary_endpoint, $username, $password, $merchant_id, $terminal_id ) {

		$this->endpoint           = $api_endpoint;
		$this->secondary_endpoint = $secondary_endpoint;
		$this->username           = $username;
		$this->password           = $password;
		$this->merchant_id        = $merchant_id;
		$this->terminal_id        = $terminal_id;
	}


	/**
	 * Create a new cc charge transaction using the Orbital Gateway XML API
	 *
	 * This request, if successful, causes a charge to be incurred by the
	 * specified credit card. Notice that the authorization for the charge is
	 * obtained when the card issuer receives this request. The resulting
	 * authorization code is returned in the response to this request.
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API::credit_card_charge()
	 * @param WC_Order $order the order
	 * @return WC_Orbital_Gateway_API_Credit_Card_Charge_Response Orbital Gateway API credit card charge response object
	 * @throws Exception network timeouts, etc
	 */
	public function credit_card_charge( WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request();
		$request->credit_card_charge( $order );

		return $this->perform_request( $request, 'WC_Orbital_Gateway_API_New_Order_Response' );
	}


	/**
	 * Create a new cc auth transaction using the Orbital Gateway XML API
	 *
	 * This request is used for a transaction in which the merchant needs
	 * authorization of a charge, but does not wish to actually make the charge
	 * at this point in time. For example, if a customer orders merchandise to
	 * be shipped, you could issue this request at the time of the order to
	 * make sure the merchandise will be paid for by the card issuer. Then at
	 * the time of actual merchandise shipment, you perform the actual charge
	 * using the transaction ref.
	 *
	 * Note: The authorization is valid only for a fixed amount of time, which
	 * may vary by card issuer, but which is usually several days.
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API::credit_card_authorization()
	 * @param WC_Order $order the order
	 * @return WC_Orbital_Gateway_API_Credit_Card_Authorization_Response Orbital Gateway API credit card auth response object
	 * @throws Exception network timeouts, etc
	 */
	public function credit_card_authorization( WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request();
		$request->credit_card_auth( $order );

		return $this->perform_request( $request, 'WC_Orbital_Gateway_API_New_Order_Response' );
	}


	/**
	 * Perform a credit card capture for a given authorized order
	 *
	 * If the gateway does not support credit card capture, this method can be a no-op.
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API::credit_card_capture()
	 * @param WC_Order $order the order
	 * @return Framework\SV_WC_Payment_Gateway_API_Response credit card capture response
	 * @throws Exception network timeouts, etc
	 */
	public function credit_card_capture( WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request();
		$request->credit_card_capture( $order );

		return $this->perform_request( $request, 'WC_Orbital_Gateway_API_Capture_Response' );
	}


	/**
	 * Removes the tokenized payment method
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API::remove_tokenized_payment_method()
	 * @param string $token the payment method token
	 * @param string $customer_id optional unique customer id for gateways that support it
	 * @return Framework\SV_WC_Payment_Gateway_API_Response remove tokenized payment method response
	 * @throws Exception network timeouts, etc
	 */
	public function remove_tokenized_payment_method( $token, $customer_id ) {

		$request = $this->get_new_request();
		$request->profile_delete( $token );

		return $this->perform_request( $request, 'WC_Orbital_Gateway_API_Profile_Delete_Response' );
	}


	/**
	 * Returns true
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API::supports_remove_tokenized_payment_method()
	 * @return boolean true
	 */
	public function supports_remove_tokenized_payment_method() {
		return true;
	}


	/**
	 * Performs a refund for the given order.
	 *
	 * @since 1.3.0
	 * @see \Framework\SV_WC_Payment_Gateway_API::refund()
	 * @param \WC_Order $order the order object
	 * @return \Framework\SV_WC_Payment_Gateway_API_Response
	 * @throws \Framework\SV_WC_Payment_Gateway_Exception
	 */
	public function refund( WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request();

		$request->refund( $order );

		return $this->perform_request( $request, 'WC_Orbital_Gateway_API_Refund_Response' );
	}


	/**
	 * Performs a void for the given order.
	 *
	 * @since 1.3.0
	 * @see \Framework\SV_WC_Payment_Gateway_API::void()
	 * @param \WC_Order $order the order object
	 * @return \Framework\SV_WC_Payment_Gateway_API_Response
	 * @throws \Framework\SV_WC_Payment_Gateway_Exception
	 */
	public function void( WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request();

		$request->void( $order );

		return $this->perform_request( $request, 'WC_Orbital_Gateway_API_Void_Response' );
	}


	/** Helper methods ******************************************************/


	/**
	 * Performs the request post to the active endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Orbital_Gateway_API_Request $request the request object
	 * @param string $response_class_name the class name of the response
	 * @param bool $is_failover whether the request is a secondary failover attempt
	 * @return WC_Orbital_Gateway_API_Response response object
	 * @throws Framework\SV_WC_Payment_Gateway_Exception network timeouts
	 */
	private function perform_request( $request, $response_class_name, $is_failover = false ) {

		// save the request object
		$this->request = $request;

		$method = 'POST';

		// perform the request
		$wp_http_args = [
			'method'      => $method,
			'timeout'     => 90, // seconds
			'redirection' => 0,
			'httpversion' => '1.1',
			'sslverify'   => true,
			'blocking'    => true,
			'user-agent'  => "WooCommerce/" . WC_VERSION,
			'headers'     => [
				'mime-version'              => '1.1',
				'content-type'              => 'application/PTI79',
				'content-transfer-encoding' => 'text',
				'request-number'            => '1',
				'document-type'             => 'Request',
				'interface-version'         => 'WooCommerce Chase Paymentech v' . WC_Chase_Paymentech::VERSION,
			],
			'body'        => trim( $request->to_xml() ),
			'cookies'     => [],
		];

		if ( $request->get_retry_trace_number() ) {
			$wp_http_args['headers']['trace-number'] = $request->get_retry_trace_number();
		}

		/**
		 * Filters the Orbital API request URL.
		 *
		 * @since 1.11.1
		 *
		 * @param string $request_url request URL
		 */
		$request_url = apply_filters( 'wc_chase_paymentech_api_request_url', $is_failover ? $this->secondary_endpoint : $this->endpoint );

		$start_time = microtime( true );
		$response = wp_safe_remote_post( $request_url, $wp_http_args );
		$time = round( microtime( true ) - $start_time, 5 );

		// prepare the request/response data for the request performed action
		$request_data  = array( 'method' => $method, 'headers' => $wp_http_args['headers'], 'uri' => $request_url, 'body' => $request->to_string_safe(), 'time' => $time );
		$response_data = null;

		// Check for Network timeout, etc.
		if ( is_wp_error( $response ) ) {

			do_action( 'wc_chase_paymentech_api_request_performed', $request_data, $response_data );

			// if this was a first try that failed, try again with the failover endpoint
			// otherwise, just pass the exception along
			if ( ! $is_failover ) {
				return $this->perform_request( $this->request, $response_class_name, true );
			} else {
				throw new Framework\SV_WC_Payment_Gateway_Exception( $response->get_error_message() );
			}
		}

		// now we know the response isn't an error
		$response_data = array( 'code' => ( isset( $response['response']['code'] ) ) ? $response['response']['code'] : '', 'body' => ( isset( $response['body'] ) ) ? $response['body'] : '' );

		// Status Codes:
		// 200 - approved
		// 400 - invalid request XML
		// 403 - forbidden: SSL connection required
		// 408 - request timed out
		// 412 - IP security failure (A non-registered IP Address attempted to connect to the Orbital Gateway. The HTTP connection was refused as a result.)
		// 500 - internal server error
		// 502 - connection error (The server, while acting as a gateway or proxy, received an invalid response from the upstream server it accessed in attempting to fulfill the request.)
		if ( 200 != $response['response']['code'] ) {

			// response will include the http status code/message
			/* translators: Placeholders: %1$s - response code, %2$s - response message */
			$message = sprintf( 'HTTP %1$s: %2$s', $response['response']['code'], $response['response']['message'] );

			// the body (if any)
			if ( trim( $response['body'] ) )
				$message .= ' - ' . $response['body'];

			do_action( 'wc_chase_paymentech_api_request_performed', $request_data, $response_data );

			throw new Framework\SV_WC_Payment_Gateway_Exception( $message );
		}

		// return blank XML document if response body doesn't exist
		$response = ( isset( $response[ 'body' ] ) ) ? $response[ 'body' ] : '<?xml version="1.0" encoding="utf-8"?>';

		// create the response and tie it to the request
		$response = $this->parse_response( $response_class_name, $request, $response );

		// full response object
		$response_data['body'] = $response->to_string_safe();

		do_action( 'wc_chase_paymentech_api_request_performed', $request_data, $response_data );

		return $response;
	}


	/**
	 * Return a new WC_Orbital_Gateway_API_Response object from the response XML
	 *
	 * @since 1.0
	 * @param string $response_class_name the class name of the response
	 * @param WC_Orbital_Gateway_API_Request $request the request
	 * @param string $response xml response
	 * @return WC_Orbital_Gateway_API_Response API response object
	 */
	private function parse_response( $response_class_name, $request, $response ) {

		// save the most recent response object
		return $this->response = new $response_class_name( $request, $response );
	}


	/**
	 * Builds and returns a new API request object
	 *
	 * @since 1.0
	 * @return WC_Orbital_Gateway_API_Request API request object
	 */
	private function get_new_request() {
		return new WC_Orbital_Gateway_API_Request( $this->username, $this->password, $this->merchant_id, $this->terminal_id );
	}


	/** Getter methods ******************************************************/


	/**
	 * Returns the most recent request object
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API::get_request()
	 * @return Framework\SV_WC_Payment_Gateway_API_Request the most recent request object
	 */
	public function get_request() {
		return $this->request;
	}


	/**
	 * Returns the most recent response object
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API::get_response()
	 * @return Framework\SV_WC_Payment_Gateway_API_Response the most recent response object
	 */
	public function get_response() {
		return $this->response;
	}


	/**
	 * Return the order associated with the request, if any
	 *
	 * @since 1.4.2
	 * @return \WC_Order|null
	 */
	public function get_order() {

		return $this->order;
	}


	/** No-op methods ******************************************************/


	/**
	 * Returns false as there doesn't seem to be an Orbital Gateway token retrieval request
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API::supports_get_tokenized_payment_methods()
	 * @return boolean false
	 */
	public function supports_get_tokenized_payment_methods() {
		// no-op
		return false;
	}


	/**
	 * Perform a customer check debit transaction using the Orbital Gateway XML API
	 *
	 * An amount will be debited from the customer's account to the merchant's account.
	 *
	 * @since 1.0
	 * @param WC_Order $order the order
	 * @return Framework\SV_WC_Payment_Gateway_API_Response check debit response
	 * @throws Exception network timeouts, etc
	 */
	public function check_debit( WC_Order $order ) {
		// no-op
	}


	/**
	 * This is a no-op method as payment methods are tokenized via the redirect
	 * API
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API::tokenize_payment_method()
	 * @param WC_Order $order the order with associated payment and customer info
	 * @return Framework\SV_WC_Payment_Gateway_API_Create_Payment_Token_Response wallet add response
	 * @throws Exception network timeouts, etc
	 */
	public function tokenize_payment_method( WC_Order $order ) {
		// no-op, this is handled by the Redirect API
	}


	/**
	 * No-op method as there doesn't seem to be an Orbital Gateway token retrieval request
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API::get_tokenized_payment_methods()
	 * @param string $customer_id unique customer id
	 * @return Framework\SV_WC_API_Get_Tokenized_Payment_Methods_Response null
	 * @throws Exception network timeouts, etc
	 */
	public function get_tokenized_payment_methods( $customer_id ) {
		// no-op
	}


	/**
	 * Updates a tokenized payment method.
	 *
	 * @since 1.12.0
	 *
	 * @param \WC_Order $order order object
	 */
	public function update_tokenized_payment_method( \WC_Order $order ) {}


	/**
	 * Determines if this API supports updating tokenized payment methods.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 */
	public function supports_update_tokenized_payment_method() {

		return false;
	}


}
