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
 * Intuit QBMS Credit Card Authorization Response
 *
 * Represents a credit card auth response
 *
 * @since 1.0
 */
class WC_Intuit_QBMS_API_Credit_Card_Authorization_Response extends WC_Intuit_QBMS_API_Response implements Framework\SV_WC_Payment_Gateway_API_Authorization_Response {


	/**
	 * This transaction ID is returned from the credit card processor. You
	 * should save this value for any possible QBMS SDK transaction requests
	 * that need it, for example, CustomerCreditCardTxnVoidRq, or
	 * CustomerCreditCardCaptureRq.
	 *
	 * You should also save this transaction ID and include it in the QB SDK
	 * SalesReceiptAdd or ReceivePaymentAdd requests if you are intending to
	 * save the transaction data in QuickBooks.
	 *
	 * @since 1.0
	 * @return string credit card transaction id or null if there is no id
	 */
	public function get_credit_card_transaction_id() {

		$element = $this->get_action_response_element();

		return isset( $element->CreditCardTransID ) ? (string) $element->CreditCardTransID : null;
	}


	/**
	 * Gets the response transaction id
	 *
	 * @since 1.0
	 * @see WC_Intuit_QBMS_API_Response::get_transaction_id()
	 * @return string transaction id
	 */
	public function get_transaction_id() {

		return $this->get_credit_card_transaction_id();

	}


	/**
	 * The authorization code is returned from the credit card processor to
	 * indicate that the charge will be paid by the card issuer.
	 *
	 * In a voice authorization request (CustomerCreditCardVoiceAuthRq), this
	 * value must be supplied with the value obtained over the telephone from
	 * the card issuer.
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Authorization_Response::get_authorization_code()
	 * @return string credit card authorization code
	 */
	public function get_authorization_code() {

		$element = $this->get_action_response_element();

		return isset( $element->AuthorizationCode ) ? (string) $element->AuthorizationCode : null;
	}


	/**
	 * Returns the result of the AVS check:
	 *
	 * + `Z` - zip match, locale no match
	 * + `A` - zip no match, locale match
	 * + `N` - zip no match, locale no match
	 * + `Y` - zip match, locale match
	 * + `U` - zip and locale could not be verified
	 * + nil - unsupported card, or unknown response
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Authorization_Response::get_avs_result()
	 * @return string result of the AVS check, if any
	 */
	public function get_avs_result() {
		$element = $this->get_action_response_element();

		if ( ! $element ) {
			return null;
		}

		$locale_fail = 'Fail' == (string) $element->AVSStreet;
		$zip_fail    = 'Fail' == (string) $element->AVSZip;

		if ( ! $zip_fail && $locale_fail ) return 'Z';
		if ( $zip_fail && ! $locale_fail ) return 'A';
		if ( $zip_fail && $locale_fail )   return 'G';
		return 'Y';
	}


	/**
	 * Returns the result of the CSC check
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Authorization_Response::get_csc_result()
	 * @return string result of CSC check
	 */
	public function get_csc_result() {

		$element = $this->get_action_response_element();

		return isset( $element->CardSecurityCodeMatch ) ? (string) $element->CardSecurityCodeMatch : null;
	}


	/**
	 * Returns true if the CSC check was successful
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Authorization_Response::csc_match()
	 * @return boolean true if the CSC check was successful
	 */
	public function csc_match() {
		return 'Y' == $this->get_csc_result();
	}


	/**
	 * This value is returned from QBMS transactions for future use by the
	 * QuickBooks Reconciliation feature
	 *
	 * @since 1.0
	 * @return string client transaction id
	 */
	public function get_client_trans_id() {

		$element = $this->get_action_response_element();

		return isset( $element->ClientTransID ) ? (string) $element->ClientTransID : null;
	}


	/**
	 * Determine the payment type for this response.
	 *
	 * @since 1.9.0
	 * @return string
	 */
	public function get_payment_type() {

		return Framework\SV_WC_Payment_Gateway::PAYMENT_TYPE_CREDIT_CARD;
	}


}
