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

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;


/**
 * Intuit QBMS Customer Check Debit Response
 *
 * Represents an echeck debit response
 *
 * @since 1.0
 */
class WC_Intuit_QBMS_API_Customer_Check_Debit_Response extends WC_Intuit_QBMS_API_Response {


	/**
	 * The transaction ID returned when processing a check payment. You should
	 * save this value for any possible QBMS SDK transaction request that needs
	 * it. For example, CustomerCheckTxnVoidRq
	 *
	 * @since 1.0
	 * @return string check transaction id
	 */
	public function get_check_transaction_id() {

		$element = $this->get_action_response_element();

		return isset( $element->CheckTransID ) ? (string) $element->CheckTransID : null;
	}


	/**
	 * Gets the response transaction id
	 *
	 * @since 1.0
	 * @see WC_Intuit_QBMS_API_Response::get_transaction_id()
	 * @return string transaction id
	 */
	public function get_transaction_id() {

		return $this->get_check_transaction_id();

	}


	/**
	 * Authorization code for the check transaction.
	 *
	 * @since 1.0
	 * @return string credit card authorization code
	 */
	public function get_check_authorization_code() {

		$element = $this->get_action_response_element();

		return isset( $element->CheckAuthorizationCode ) ? (string) $element->CheckAuthorizationCode : null;
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

		return Framework\SV_WC_Payment_Gateway::PAYMENT_TYPE_ECHECK;
	}


}
