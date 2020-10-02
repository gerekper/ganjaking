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

use SkyVerge\WooCommerce\PluginFramework\v5_8_1 as Framework;


/**
 * Intuit QBMS API Base Response Class
 *
 * Parses XML received from Intuit QBMS API, the general response body looks like:
 *
 * <?xml version="1.0" encoding="ISO-8859-1" standalone="no"?>
 * <!DOCTYPE QBMSXML PUBLIC "-//INTUIT//DTD QBMSXML QBMS 4.5//EN" "http://merchantaccount.ptc.quickbooks.com/dtds/qbmsxml45.dtd">
 * <QBMSXML>
 *  <SignonMsgsRs>
 *   <SignonDesktopRs statusCode="0" statusSeverity="INFO">
 *    <ServerDateTime>2013-09-11T21:33:16</ServerDateTime>
 *    <SessionTicket>V1-67-Q3xnxgt19saf0k5n5tceab:1004728840</SessionTicket>
 *   </SignonDesktopRs>
 *  </SignonMsgsRs>
 *  <QBMSXMLMsgsRs>
 *   <ActionResponseElementName statusCode="0" statusMessage="Status OK" statusSeverity="INFO">
 *    <StatusDetail>...</StatusDetail>
 *   </ActionResponseElementName>
 *  </QBMSXMLMsgsRs>
 * </QBMSXML>
 *
 * Where ActionResponseElementName is the response element name for the given
 * request action.  ActionResponseElementName generally includes some response-
 * specific fields (see child classes) along with the optional StatusDetail
 * element
 *
 * Note: the (string) casts here are critical, without these you'll tend to get untraceable
 * errors like "Serialization of 'SimpleXMLElement' is not allowed"
 *
 * @link https://member.developer.intuit.com/qbSDK-current/Common/newOSR/index.html
 *
 * @since 1.0
 * @see Framework\SV_WC_Payment_Gateway_API_Response
 */
class WC_Intuit_QBMS_API_Response implements Framework\SV_WC_Payment_Gateway_API_Response {


	/** @var WC_Intuit_QBMS_API_Request the request that resulted in this response */
	protected $request;

	/** @var string string representation of this response */
	private $raw_response_xml;

	/** @var SimpleXMLElement response XML object */
	private $response_xml;


	/**
	 * Build a response object from the raw response xml
	 *
	 * @since 1.0
	 * @param WC_Intuit_QBMS_API_Request $request the request that resulted in this response
	 * @param string $raw_response_xml the raw response XML
	 */
	public function __construct( $request, $raw_response_xml ) {

		$this->request = $request;

		$this->raw_response_xml = $raw_response_xml;

		try {

			// LIBXML_NOCDATA ensures that any XML fields wrapped in [CDATA] will be included as text nodes
			$this->response_xml     = new SimpleXMLElement( $raw_response_xml, LIBXML_NOCDATA );

		} catch ( Exception $e ) {

			throw new Framework\SV_WC_Payment_Gateway_Exception( $e->getMessage() );
		}
	}


	/**
	 * Checks if the transaction failed due to a QBMS Connection-Related Error.
	 * QBMS connection-related errors resulting from your application's attempt
	 * to communicate with it
	 *
	 * @link https://developer.intuit.com/docs/030_qbms/0060_documentation/error_handling#QBMS_Connection-Related_Errors
	 *
	 * @since 1.0
	 * @return bool true if there was an error with the communication
	 */
	public function has_connection_error() {

		// true if there's an error
		return '0' !== $this->get_connection_status_code();

	}


	/**
	 * Gets the connection status code
	 *
	 * @link https://developer.intuit.com/docs/030_qbms/0060_documentation/error_handling#QBMS_Connection-Related_Errors
	 *
	 * @since 1.0
	 * @return string connection status code or null if none was found
	 */
	public function get_connection_status_code() {

		return (string) $this->response_xml->SignonMsgsRs->SignonDesktopRs['statusCode'];
	}


	/**
	 * Gets the connection status message
	 *
	 * @link https://developer.intuit.com/docs/030_qbms/0060_documentation/error_handling#QBMS_Connection-Related_Errors
	 *
	 * @since 1.0
	 * @return string response message
	 */
	public function get_connection_status_message() {

		switch ( $this->get_connection_status_code() ) {
			case 0:    return __( 'Authentication successful', 'woocommerce-gateway-intuit-payments' );
			case 2000: return __( 'Application agent not found - the ticket provided is invalid or cancelled', 'woocommerce-gateway-intuit-payments' );
			case 2010: return __( 'Unauthorized', 'woocommerce-gateway-intuit-payments' );
			case 2020: return __( 'Session Authentication required', 'woocommerce-gateway-intuit-payments' );
			case 2040: return __( 'Internal error', 'woocommerce-gateway-intuit-payments' );

			default: return __( 'Unknown error', 'woocommerce-gateway-intuit-payments' );
		}
	}


	/**
	 * Checks if the transaction was successful
	 *
	 * @link https://developer.intuit.com/docs/030_qbms/0060_documentation/error_handling#QBMS_and_Processing_Errors
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::transaction_approved()
	 * @return bool true if approved, false otherwise
	 */
	public function transaction_approved() {

		return ! $this->has_connection_error() && '0' === $this->get_transaction_status_code();

	}


	/**
	 * Returns true if the transaction was held, for instance due to AVS/CSC
	 * Fraud Settings.  This indicates that the transaction was successful, but
	 * did not pass a fraud check and should be reviewed.
	 *
	 * @link https://developer.intuit.com/docs/030_qbms/0060_documentation/error_handling#QBMS_and_Processing_Errors
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::transaction_held()
	 * @return bool true if approved, false otherwise
	 */
	public function transaction_held() {

		return ! $this->has_connection_error() && ( '10100' === $this->get_transaction_status_code() ||  '10101' === $this->get_transaction_status_code() );

	}


	/**
	 * Gets the response transaction id, or null if there is no transaction id
	 * associated with this transaction.
	 *
	 * Defaults to returning null since not all transaction responses include a
	 * transaction id, so this should be overridden accordingly for those that do.
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::get_transaction_id()
	 * @return string transaction id
	 */
	public function get_transaction_id() {

		return null;

	}


	/**
	 * Gets the transaction status message:  connection status if there was a
	 * connection error, otherwise the transaction status message
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::get_status_message()
	 * @return string status message
	 */
	public function get_status_message() {

		if ( $this->has_connection_error() ) return $this->get_connection_status_message();

		return $this->get_transaction_status_message();
	}


	/**
	 * Gets the transaction status code:  connection status if there was a
	 * connection error, otherwise the transaction status code
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::get_status_code()
	 * @return string status code
	 */
	public function get_status_code() {

		if ( $this->has_connection_error() ) return $this->get_connection_status_code();

		return $this->get_transaction_status_code();
	}


	/**
	 * Gets the transaction status code
	 *
	 * @link https://developer.intuit.com/docs/030_qbms/0060_documentation/error_handling#QBMS_and_Processing_Errors
	 *
	 * @since 1.0
	 * @return string transaction status code or null if none was found
	 */
	public function get_transaction_status_code() {

		$element = $this->get_action_response_element();

		return isset( $element['statusCode'] ) ? (string) $element['statusCode'] : null;
	}


	/**
	 * Gets the transaction status message
	 *
	 * @since 1.0
	 * @return string transaction status message
	 */
	public function get_transaction_status_message() {

		$element = $this->get_action_response_element();

		return isset( $element['statusMessage'] ) ? (string) $element['statusMessage'] : null;
	}


	/**
	 * If a response contains a statusCode that represents an error case, the
	 * status detail may be available to provide more information about the
	 * error. For instance, a 10312 status code represents an invalid field.
	 * If this code is returned, the invalid field name will be present in the
	 * status detail.
	 *
	 * @since 1.0
	 * @return string status detail or null
	 */
	public function get_status_detail() {

		$element = $this->get_action_response_element();

		return isset( $element->StatusDetail ) ? (string) $element->StatusDetail : null;
	}


	/**
	 * Gets the main response element.
	 *
	 * QBMS XML API responses consist of a Signon response portion, as well as an action response portion.
	 * Since the element name of the action response portion varies depending upon the particular response, yet they share a lot of commonalities,
	 * we take a generic approach to retrieving that element.
	 *
	 * @since 1.0
	 *
	 * @return null|SimpleXMLElement the add response element, or null if there is none
	 */
	protected function get_action_response_element() {

		// get the first child (the action response)
		if ( $this->response_xml->QBMSXMLMsgsRs ) {
			return current( $this->response_xml->QBMSXMLMsgsRs->children() );
		}

		return null;
	}


	/**
	 * Returns a message appropriate for a frontend user.  This should be used
	 * to provide enough information to a user to allow them to resolve an
	 * issue on their own, but not enough to help nefarious folks fishing for
	 * info.
	 *
	 * @since 1.3.1
	 * @see Framework\SV_WC_Payment_Gateway_API_Response_Message_Helper
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::get_user_message()
	 * @return string user message, if there is one
	 */
	public function get_user_message() {

		$message_helper = new Framework\SV_WC_Payment_Gateway_API_Response_Message_Helper( 'woocommerce-gateway-intuit-payments' );
		$user_message   = null;

		switch ( $this->get_status_code() ) {

			case 10101:
				// just get the validation error
				$user_message = preg_replace('/.*error:/', '', $this->get_transaction_status_message() );

				if ( 'Card Verification Code not available.' === $user_message ) {
					$user_message = $message_helper->get_user_message( 'csc_missing' );
				}
				// otherwise just use the string from Intuit (ie "Incorrect Street Address and Zip Code", and who knows what else)
			break;

			case 10301:
				$user_message = $message_helper->get_user_message( 'card_number_invalid' );
			break;

			case 10303:
				if ( strpos( $this->get_status_detail(), 'CreditCardNumber' ) !== false ) {
					$user_message = $message_helper->get_user_message( 'card_number_missing' );
				}
			break;

			case 10302:
				if ( 'ExpirationMonth/ExpirationYear' == $this->get_status_detail() ) {
					$user_message = $message_helper->get_user_message( 'card_expired' );
				} elseif ( ( 'ExpirationMonth' == $this->get_status_detail() || 'ExpirationYear' == $this->get_status_detail() ) && strpos( $this->get_transaction_status_message(), 'date value null' ) !== false ) {
					$user_message = $message_helper->get_user_message( 'card_expiry_missing' );
					break;
				}
				// the remaining status details are processed the same as 10309 below

			case 10309:
				if ( 'CardSecurityCode' == $this->get_status_detail() ) {
					$user_message = $message_helper->get_user_message( 'csc_invalid' );
				} elseif ( 'CreditCardNumber' == $this->get_status_detail() ) {
					$user_message = $message_helper->get_user_message( 'card_number_invalid' );
				} elseif ( 'ExpirationMonth' == $this->get_status_detail() ) {
					$user_message = $message_helper->get_user_message( 'card_expiry_month_invalid' );
				} elseif ( 'ExpirationYear' == $this->get_status_detail() ) {
					$user_message = $message_helper->get_user_message( 'card_expiry_year_invalid' );
				}
			break;

			case 10400:
				$user_message = $message_helper->get_user_message( 'insufficient_funds' );
			break;

			case 10401:
				$user_message = $message_helper->get_user_message( 'card_declined' );
			break;

			case 10409:
				$user_message = $this->get_transaction_status_message();
			break;

			case 10415:
				$user_message = $message_helper->get_user_message( 'card_expired' );
			break;

			case 10420:
				$user_message = $message_helper->get_user_message( 'card_declined' );
			break;

		}

		return apply_filters( 'wc_payment_gateway_intuit_qbms_customer_checkout_message', $user_message, $this );
	}


	/**
	 * Returns the string representation of this response
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::to_string()
	 * @return string response
	 */
	public function to_string() {

		$string = $this->raw_response_xml;

		$dom = new DOMDocument();

		// suppress errors for invalid XML syntax issues
		if ( @$dom->loadXML( $string ) ) {
			$dom->formatOutput = true;
			$string = $dom->saveXML();
		}

		return $string;

	}


	/**
	 * Returns the string representation of this response with any and all
	 * sensitive elements masked or removed
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::to_string_safe()
	 * @return string response safe for logging/displaying
	 */
	public function to_string_safe() {

		// no sensitive data to mask
		return $this->to_string();

	}


	/**
	 * Determine the payment type for this response.
	 *
	 * @since 1.9.0
	 * @return null
	 */
	public function get_payment_type() {
		return null;
	}


}
