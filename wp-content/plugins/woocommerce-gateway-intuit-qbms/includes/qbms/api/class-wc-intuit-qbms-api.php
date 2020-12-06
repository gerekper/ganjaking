<?php
/**
 * WooCommerce Intuit QBMS
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
 * @package   WC-Intuit-QBMS/API
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_1 as Framework;

/**
 * Intuit QBMS API Class
 *
 * Handles sending/receiving/parsing of Intuit QBMS XML, this is the main API
 * class responsible for communication with the Intuit QBMS API
 *
 * @since 1.0
 */
class WC_Intuit_QBMS_API extends Framework\SV_WC_API_Base implements Framework\SV_WC_Payment_Gateway_API {

	/** @var string API id */
	private $id;

	/** @var string API URL endpoint */
	private $endpoint;

	/** @var string the application login value */
	private $application_login;

	/** @var string the application id value */
	private $application_id;

	/** @var string the connection ticket value */
	public $connection_ticket;

	/** @var \WC_Order|null order associated with the request, if any */
	protected $order;


	/**
	 * Constructor - setup request object and set endpoint
	 *
	 * @since 1.0
	 * @param string $id API id
	 * @param string $api_endpoint API URL endpoint
	 * @param string $application_login application login value
	 * @param string $application_id application id value
	 * @param string $connection_ticket connection ticket value
	 */
	public function __construct( $id, $api_endpoint, $application_login, $application_id, $connection_ticket ) {

		$this->id                = $id;
		$this->endpoint          = $api_endpoint;
		$this->application_login = $application_login;
		$this->application_id    = $application_id;
		$this->connection_ticket = $connection_ticket;

		$this->request_uri = $api_endpoint;

		$this->set_request_content_type_header( 'application/x-qbmsxml' );
		$this->set_request_accept_header( 'application/x-qbmsxml' );
	}


	/**
	 * Create a new cc charge transaction using Intuit QBMS XML API
	 *
	 * This request, if successful, causes a charge to be incurred by the
	 * specified credit card. Notice that the authorization for the charge is
	 * obtained when the card issuer receives this request. The resulting
	 * authorization code is returned in the response to this request.
	 *
	 * Notice that voice authorizations cannot be handled by this request. For
	 * voice authorizations, use the CustomerCreditCardVoiceAuth request
	 * followed by a CustomerCreditCardCapture request.
	 *
	 * Note: It's important that these elements appear in the expected order,
	 * otherwise there will be parsing errors returned from the QBMS API
	 *
	 * @since 1.0.0
	 * @see Framework\SV_WC_Payment_Gateway_API::credit_card_charge()
	 * @param \WC_Order $order the order object
	 * @return \WC_Intuit_QBMS_API_Credit_Card_Charge_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function credit_card_charge( \WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request();

		$request->credit_card_charge( $order );

		$this->set_response_handler( 'WC_Intuit_QBMS_API_Credit_Card_Charge_Response' );

		return $this->perform_request( $request );

	}


	/**
	 * Creates a new credit card authorization transaction.
	 *
	 * This request is used for a transaction in which the merchant needs
	 * authorization of a charge, but does not wish to actually make the charge
	 * at this point in time. For example, if a customer orders merchandise to
	 * be shipped, you could issue this request at the time of the order to
	 * make sure the merchandise will be paid for by the card issuer. Then at
	 * the time of actual merchandise shipment, you perform the actual charge
	 * using the request CustomerCreditCardCaptureRq.
	 *
	 * It is very important to save the CreditCardTransID from the response to
	 * this request, because this is required for the subsequent
	 * CustomerCreditCardCapture request.
	 *
	 * Note: The authorization is valid only for a fixed amount of time, which
	 * may vary by card issuer, but which is usually several days. QBMS imposes
	 * its own maximum of 30 days after the date of the original authorization,
	 * but most issuers are expected to have a validity period significantly
	 * less than this.
	 *
	 * @since 1.0.0
	 * @see Framework\SV_WC_Payment_Gateway_API::credit_card_authorization()
	 * @param \WC_Order $order the order object
	 * @return \WC_Intuit_QBMS_API_Credit_Card_Authorization_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function credit_card_authorization( \WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request();

		$request->credit_card_auth( $order );

		$this->set_response_handler( 'WC_Intuit_QBMS_API_Credit_Card_Authorization_Response' );

		return $this->perform_request( $request );
	}


	/**
	 * Captures a credit card authorization.
	 *
	 * This request can be made only after a previous and successful
	 * CustomerCreditCardAuth request, where the card issuer has authorized a
	 * charge to be made against the specified credit card in the future. The
	 * CreditCardTransID from that prior transaction must be used in this
	 * subsequent and related transaction. This request actually causes that
	 * authorized charge to be incurred against the customer's credit card.
	 *
	 * Notice that you cannot have multiple capture requests against a single
	 * CustomerCreditCardAuth request. Each CustomerCreditCardAuth request must
	 * have one and only one capture request.
	 *
	 * Note: The authorization to be captured is valid only for a fixed amount
	 * of time, which may vary by card issuer, but which is usually several
	 * days. QBMS imposes its own maximum of 30 days after the date of the
	 * original authorization, but most issuers are expected to have a validity
	 * period significantly less than this.
	 *
	 * @since 1.0.0
	 * @see Framework\SV_WC_Payment_Gateway_API::credit_card_capture()
	 * @param \WC_Order $order the order object
	 * @return \WC_Intuit_QBMS_API_Credit_Card_Capture_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function credit_card_capture( \WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request();

		$request->credit_card_capture( $order );

		$this->set_response_handler( 'WC_Intuit_QBMS_API_Credit_Card_Capture_Response' );

		return $this->perform_request( $request );
	}


	/**
	 * Performs a customer check debit transaction.
	 *
	 * @since 1.0.0
	 * @param \WC_Order $order the order object
	 * @return \WC_Intuit_QBMS_API_Customer_Check_Debit_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function check_debit( \WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request();

		$request->customer_check_debit( $order );

		$this->set_response_handler( 'WC_Intuit_QBMS_API_Customer_Check_Debit_Response' );

		return $this->perform_request( $request );

	}


	/**
	 * Performs a refund for the given order.
	 *
	 * @since 1.6.0
	 * @see Framework\SV_WC_Payment_Gateway_API::refund()
	 * @param \WC_Order $order the order object
	 * @return Framework\SV_WC_Payment_Gateway_API_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function refund( \WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request();

		$request->credit_card_refund( $order );

		$this->set_response_handler( 'WC_Intuit_QBMS_API_Credit_Card_Refund_Response' );

		return $this->perform_request( $request );
	}


	/**
	 * Performs a void for the given order.
	 *
	 * Note that this relies on \WC_Intuit_QBMS_API::refund() because the QBMS
	 * API has only one 'CustomerCreditCardTxnVoidOrRefund' transaction type for
	 * both voids & refunds.
	 *
	 * Here we check for a refund response object and pass it through for cases
	 * where a refund was already performed via `process_refund()`,
	 * \WC_Gateway_Intuit_QBMS_Credit_Card::maybe_void_instead_of_refund() was
	 * called, and the response indicated it as "Void".
	 *
	 * @see \WC_Intuit_QBMS_API_Credit_Card_Refund_Response::is_void()
	 *
	 * There is a `CustomerCreditCardTxnVoid` transaction type documented, but
	 * it only returns a parse error from their API.
	 *
	 * @since 1.6.0
	 * @see Framework\SV_WC_Payment_Gateway_API::void()
	 * @param \WC_Order $order the order object
	 * @return Framework\SV_WC_Payment_Gateway_API_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function void( \WC_Order $order ) {

		// if there was already a refund request that returned as a void, use it
		if ( $this->get_response() instanceof WC_Intuit_QBMS_API_Credit_Card_Refund_Response ) {

			return $this->get_response();

		// otherwise, process a new refund
		} else {

			return $this->refund( $order );
		}
	}


	/**
	 * Tokenizes a payment method for the given order and customer.
	 *
	 * @since 1.0.0
	 * @see Framework\SV_WC_Payment_Gateway_API::tokenize_payment_method()
	 * @param \WC_Order $order the order object
	 * @return \WC_Intuit_QBMS_API_Wallet_Add_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function tokenize_payment_method( \WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request();

		$request->wallet_add( $order );

		$this->set_response_handler( 'WC_Intuit_QBMS_API_Wallet_Add_Response' );

		return $this->perform_request( $request );

	}


	/**
	 * Removes the tokenized payment method.
	 *
	 * @since 1.0.0
	 * @see Framework\SV_WC_Payment_Gateway_API::remove_tokenized_payment_method()
	 * @param string $token the payment method token
	 * @param string $customer_id the Inuit QBMS customer ID
	 * @return \WC_Intuit_QBMS_API_Wallet_Delete_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function remove_tokenized_payment_method( $token, $customer_id ) {

		$request = $this->get_new_request();

		$request->wallet_del( $token, $customer_id );

		$this->set_response_handler( 'WC_Intuit_QBMS_API_Wallet_Delete_Response' );

		return $this->perform_request( $request );

	}


	/**
	 * Gets all tokenized payment methods for the user.
	 *
	 * @since 1.0.0
	 * @see Framework\SV_WC_Payment_Gateway_API::get_tokenized_payment_methods()
	 * @param string $customer_id the Inuit QBMS customer ID
	 * @return \WC_Intuit_QBMS_API_Wallet_Query_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_tokenized_payment_methods( $customer_id ) {

		$request = $this->get_new_request();

		$request->wallet_query( $customer_id );

		$this->set_response_handler( 'WC_Intuit_QBMS_API_Wallet_Query_Response' );

		return $this->perform_request( $request );

	}


	/**
	 * Updates a tokenized payment method.
	 *
	 * @since 2.3.3
	 *
	 * @param \WC_Order $order order object
	 * @return \WC_Intuit_QBMS_API_Wallet_Update_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function update_tokenized_payment_method( \WC_Order $order ) {

		$request = $this->get_new_request();

		$request->wallet_update( $order );

		$this->set_response_handler( 'WC_Intuit_QBMS_API_Wallet_Update_Response' );

		return $this->perform_request( $request );
	}


	/**
	 * Retrieves merchant account info including:
	 *
	 * * merchant convenience fee amount
	 * * credit card types accepted ie array( 'JCB', 'DinersClub', 'Visa', 'MasterCard', 'Discover', 'AmericanExpress' )
	 * * is check accepted (according to Intuit this will always be false)
	 * * batch hour close
	 *
	 * @since 1.0.0
	 * @return \WC_Intuit_QBMS_API_Merchant_Account_Query_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function merchant_account_query() {

		$request = $this->get_new_request();

		$request->merchant_account_query();

		$this->set_response_handler( 'WC_Intuit_QBMS_API_Merchant_Account_Query_Response' );

		return $this->perform_request( $request );

	}


	/**
	 * Enables retrieving payment methods via the API.
	 *
	 * @since 1.0.0
	 * @see Framework\SV_WC_Payment_Gateway_API::supports_get_tokenized_payment_methods()
	 * @return bool
	 */
	public function supports_get_tokenized_payment_methods() {

		return true;
	}


	/**
	 * Determines if the API supports updating payment methods.
	 *
	 * @since 2.3.3
	 *
	 * @return bool
	 */
	public function supports_update_tokenized_payment_method() {

		return true;
	}


	/**
	 * Enables removing payment methods via the API.
	 *
	 * @since 1.0.0
	 * @see Framework\SV_WC_Payment_Gateway_API::supports_remove_tokenized_payment_method()
	 * @return bool
	 */
	public function supports_remove_tokenized_payment_method() {

		return true;
	}


	/**
	 * Checks for communication errors before attempting to parse the response.
	 *
	 * @since 2.0.0
	 */
	protected function do_pre_parse_response_validation() {

		if ( 200 !== $this->get_response_code() ) {
			throw new Framework\SV_WC_API_Exception( $this->get_response_message() );
		}

		return true;
	}


	/**
	 * Gets the parsed response object for the request.
	 *
	 * @since 2.0.0
	 * @param string $raw_response_body the raw response body
	 * @return \WC_Intuit_QBMS_API_Response
	 */
	protected function get_parsed_response( $raw_response_body ) {

		$handler_class = $this->get_response_handler();

		return new $handler_class( $this->get_request(), $raw_response_body );
	}


	/**
	 * Builds and returns a new API request object.
	 *
	 * @since 1.0.0
	 * @return \WC_Intuit_QBMS_API_Request
	 */
	protected function get_new_request( $type = array() ) {

		return new WC_Intuit_QBMS_API_Request( $this->application_login, $this->application_id, $this->connection_ticket );
	}


	/**
	 * Gets the order associated with the request, if any.
	 *
	 * @since 1.7.1
	 * @return \WC_Order|null
	 */
	public function get_order() {

		return $this->order;
	}


	/**
	 * Gets the ID for the API, used primarily to namespace the action name for
	 * broadcasting requests.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	protected function get_api_id() {

		return $this->id;
	}


	/**
	 * Gets the plugin class instance associated with this API.
	 *
	 * @since 2.0.0
	 * @return Framework\SV_WC_Plugin
	 */
	protected function get_plugin() {

		return wc_intuit_payments();
	}


}
