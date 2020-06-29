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

use SkyVerge\WooCommerce\PluginFramework\v5_5_1 as Framework;

/**
 * Chase Paymentech Orbital Gateway Hosted Pay Form Response Class
 *
 * Note: the (string) casts here are critical, without these you'll tend to get untraceable
 * errors like "Serialization of 'SimpleXMLElement' is not allowed"
 *
 * @since 1.0
 */
class WC_Orbital_Gateway_IPN_Response {


	/** @var string string representation of this response */
	private $raw_response_xml;

	/** @var SimpleXMLElement response XML object */
	private $response_xml;


	/**
	 * Build a response object from the raw response xml
	 *
	 * @since 1.0
	 * @param string $raw_response_xml the raw response XML
	 */
	public function __construct( $raw_response_xml ) {

		$this->raw_response_xml = $raw_response_xml;

		// LIBXML_NOCDATA ensures that any XML fields wrapped in [CDATA] will be included as text nodes
		$this->response_xml     = new SimpleXMLElement( $raw_response_xml, LIBXML_NOCDATA );

	}


	/**
	 * Status code of the transaction.  000 for success
	 *
	 * @since 1.0
	 * @return string status code
	 */
	public function get_status() {

		return (string) $this->response_xml->status;
	}


	/**
	 * The approval code from the financial institution
	 *
	 * @since 1.0
	 * @return string approval code
	 */
	public function get_approval_code() {

		return (string) $this->response_xml->approvalCode;
	}


	/**
	 * The Orbital generated transaction ID. (TxRefNum)
	 *
	 * @since 1.0
	 * @return string transaction id
	 */
	public function get_transaction_id() {

		return (string) $this->response_xml->transId;
	}


	/**
	 * Session ID passed to the transaction processor
	 *
	 * @since 1.0
	 * @return string session id
	 */
	public function get_session_id() {

		return (string) $this->response_xml->sessionId;
	}


	/**
	 * Order ID portion of the session ID
	 *
	 * @since 1.0
	 * @return string order id
	 */
	public function get_order_id() {

		$session_id = $this->get_session_id();

		return substr( $session_id, 0, strpos( $session_id, '-' ) );
	}


	/**
	 * Gets the transaction amount.
	 *
	 * @since 1.9.0
	 * @return float the transaction amount
	 */
	public function get_transaction_amount() {

		return (float) $this->response_xml->amount;
	}


	/**
	 * Return the card type (one of VISA, MC, etc)
	 *
	 * @since 1.0
	 * @return string card type
	 */
	public function get_card_type() {

		$card_type_name = (string) $this->response_xml->cardType;

		$name_to_type = array(
			'Visa'             => Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_VISA,
			'Mastercard'       => Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_MASTERCARD,
			'American Express' => Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_AMEX,
			'Discover'         => Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_DISCOVER,
			'Diners Club'      => Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_DINERSCLUB,
			'JCB'              => Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_JCB,
		);

		return $name_to_type[ $card_type_name ];
	}


	/**
	 * Returns the card type name (one of Visa, MasterCard, etc)
	 *
	 * @since 1.0
	 * @return string card type name
	 */
	public function get_card_type_name() {

		return Framework\SV_WC_Payment_Gateway_Helper::payment_type_to_name( $this->get_card_type() );
	}


	/**
	 * Returns the last four digits of the account number
	 *
	 * @since 1.0
	 * @return string last four digits of the account number
	 */
	public function get_account_last_four() {

		return substr( (string) $this->response_xml->cardNumber, -4 );
	}


	/**
	 * Returns the card expiration month
	 *
	 * @since 1.0
	 * @return string card expiration month
	 */
	public function get_exp_month() {

		return (string) $this->response_xml->expMonth;
	}


	/**
	 * Returns the card expiration year YYYY
	 *
	 * @since 1.0
	 * @return string card expiration year YYYY
	 */
	public function get_exp_year() {

		return (string) $this->response_xml->expYear;
	}


	/**
	 * Returns the mysterious token id
	 *
	 * @since 1.0
	 * @return string token id
	 */
	public function get_token_id() {

		return (string) $this->response_xml->tokenId;
	}


	/**
	 * Returns the customer reference number, available if tokenization is
	 * enabled.  This represents one payment method for a customer, so a single
	 * customer with multiple tokenized payment methods will have multiple
	 * customer ref nums.
	 *
	 * @since 1.0
	 * @return string customer reference number
	 */
	public function get_customer_ref_num() {

		if ( isset( $this->response_xml->extToken->customerRefNum ) ) {
			return (string) $this->response_xml->extToken->customerRefNum;
		}

		return '';
	}


	/**
	 * Returns the result status of profile management, where '0' indicates
	 * success
	 *
	 * @since 1.0
	 * @return string result status of profile management, '0' for success
	 */
	public function get_profile_proc_status() {

		if ( isset( $this->response_xml->profileProcStatus ) ) {
			return (string) $this->response_xml->profileProcStatus;
		}

		return '';
	}


	/**
	 * Returns true if there is a profileProcStatus value
	 *
	 * @since 1.0
	 * @return boolean true if there is a profileProcStatus value
	 */
	public function has_profile_proc_status() {
		return isset( $this->response_xml->profileProcStatus ) && '' !== (string) $this->response_xml->profileProcStatus;
	}


	/**
	 * Returns true if the profile management operation was successful
	 *
	 * @since 1.0
	 * @return boolean true if the profile management operation was successful, false otherwise
	 */
	public function profile_proc_approved() {

		return '0' === $this->get_profile_proc_status();
	}


	/**
	 * Text Message Associated with ProfileProcStatus Value
	 *
	 * @since 1.0
	 * @return string Text Message Associated with ProfileProcStatus Value
	 */
	public function get_profile_proc_status_message() {

		if ( isset( $this->response_xml->profileProcStatusMsg ) ) {
			return (string) $this->response_xml->profileProcStatusMsg;
		}

		return '';
	}


	/**
	 * Returns the CVV response
	 *
	 * @since 1.0
	 * @return string CVV response
	 */
	public function get_cvv_match() {

		if ( isset( $this->response_xml->CVVMatch ) ) {
			return trim( (string) $this->response_xml->CVVMatch );
		}

		return '';
	}


	/**
	 * Returns the AVS response
	 *
	 * @since 1.0
	 * @return string AVS response
	 */
	public function get_avs_match() {

		if ( isset( $this->response_xml->AVSMatch ) ) {
			return trim( (string) $this->response_xml->AVSMatch );
		}

		return '';
	}


	/**
	 * Returns the payment token
	 *
	 * @since 1.0
	 * @return Framework\SV_WC_Payment_Gateway_Payment_Token payment token
	 */
	public function get_payment_token() {

		$data = array(
			'default'   => true,
			'type'      => 'credit_card',
			'last_four' => $this->get_account_last_four(),
			'card_type' => $this->get_card_type(),
			'exp_month' => $this->get_exp_month(),
			'exp_year'  => $this->get_exp_year(),
		);

		return new Framework\SV_WC_Payment_Gateway_Payment_Token( $this->get_customer_ref_num(), $data );

	}


	/**
	 * Checks if the transaction was successful
	 *
	 * @since 1.0
	 * @return bool true if approved, false otherwise
	 */
	public function transaction_approved() {

		return '000' === $this->get_status();
	}


	/**
	 * Returns the string representation of this response, nicely formatted
	 *
	 * @since 1.0
	 * @return string formatted response
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
	 * @return string response safe for logging/displaying
	 */
	public function to_string_safe() {

		// no sensitive data to mask
		return $this->to_string();

	}


	/**
	 * Gets the customer-friendly response message.
	 *
	 * No-op for IPN responses.
	 *
	 * @since 1.10.0
	 * @return string
	 */
	public function get_user_message() {

		return '';
	}


}
