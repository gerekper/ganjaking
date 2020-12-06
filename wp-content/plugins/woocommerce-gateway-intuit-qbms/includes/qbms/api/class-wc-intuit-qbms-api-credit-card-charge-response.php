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
 * Intuit QBMS Credit Card Charge Response
 *
 * Represents a credit card charge response
 *
 * @since 1.0
 */
class WC_Intuit_QBMS_API_Credit_Card_Charge_Response extends WC_Intuit_QBMS_API_Credit_Card_Authorization_Response {


	/**
	 * Returns the QBMS account number of the merchant who is running the
	 * transaction using the customer's credit card. It is returned from the
	 * transaction request and should be stored for subsequent transactions,
	 * such as using it in the QB SDK SalesReceiptAdd, ReceivePaymentAdd,
	 * ARRefundCreditCard requests to save the transaction data in QuickBooks.
	 *
	 * @since 1.0
	 * @return string merchant account number
	 */
	public function get_merchant_account_number() {

		$element = $this->get_action_response_element();

		return isset( $element->MerchantAccountNumber ) ? (string) $element->MerchantAccountNumber : null;
	}


	/**
	 * This value is returned by QBMS from the transaction request, and is used
	 * internally by the QuickBooks/QBMS Recon (reconcile) feature. You should
	 * save this value without modification and include it in the QB SDK
	 * SalesReceiptAdd or ReceivePaymentAdd requests if you want to save the
	 * transaction data in QuickBooks.
	 *
	 * @since 1.0
	 * @return string recon batch id
	 */
	public function get_recon_batch_id() {

		$element = $this->get_action_response_element();

		return isset( $element->ReconBatchID ) ? (string) $element->ReconBatchID : null;
	}


	/**
	 * Internal code needed for the QuickBooks reconciliation feature, if
	 * integrating with QuickBooks. This value is returned in the QBMS response
	 * to a QBMS transaction request, and must be subsequently supplied by your
	 * application in the QB SDK ReceivePaymentAdd or SalesReceiptAdd request.
	 *
	 * @since 1.0
	 * @return string payment grouping code
	 */
	public function get_payment_grouping_code() {

		$element = $this->get_action_response_element();

		return isset( $element->PaymentGroupingCode ) ? (string) $element->PaymentGroupingCode : null;
	}


	/**
	 * This value is used to support the credit card transaction Reconcile
	 * feature within QuickBooks.
	 *
	 * @since 1.0
	 * @return string txn authorization stamp
	 */
	public function get_txn_authorization_stamp() {

		$element = $this->get_action_response_element();

		return isset( $element->TxnAuthorizationStamp ) ? (string) $element->TxnAuthorizationStamp : null;
	}


}
