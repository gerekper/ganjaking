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
 * Intuit QBMS API Wallet Add Response
 *
 * Represents the reponse from an Add Wallet request for tokenizing a payment
 * method
 *
 * @since 1.0
 */
class WC_Intuit_QBMS_API_Wallet_Add_Response extends WC_Intuit_QBMS_API_Response implements Framework\SV_WC_Payment_Gateway_API_Create_Payment_Token_Response {


	/**
	 * Returns the payment token
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Create_Payment_Token_Response::get_payment_token()
	 * @return WC_Intuit_QBMS_Payment_Token payment token
	 */
	public function get_payment_token() {

		$data = array( 'default' => true, 'exp_month' => $this->request->get_order()->payment->exp_month, 'exp_year' => $this->request->get_order()->payment->exp_year );

		return new WC_Intuit_QBMS_Payment_Token( $this->get_wallet_entry_id(), $data );

	}


	/**
	 * Returns a 24 digit globally unique id provided by QBMS that you store
	 * securely in conjunction with a CustomerID to represent a form of payment
	 * for a particular customer. You use this, rather than an actual credit
	 * card number or check account information, to process transactions.
	 *
	 * You can use the predetermined structure of the WalletEntryID to infer
	 * certain information about the payment method. The WalletEntryID is
	 * structured as follows:
	 *
	 * + 1 digit wallet entry type (1 - Credit Card, 2 - Check)
	 * + 2 digit brand type (01 - Visa, 02 - MasterCard, 03 - Amex, 04 - Discover, 05 - DinersClub, 06 - JCB, 00 - Check)
	 * + 17 digit random number
	 * + Last 4 digits of the credit card or check account number.
	 *
	 * @since 1.0
	 * @return string wallet entry id
	 */
	public function get_wallet_entry_id() {

		$element = $this->get_action_response_element();

		return isset( $element->WalletEntryID ) ? (string) $element->WalletEntryID : null;
	}


	/**
	 * Indicates whether the payment information is already associated with a
	 * particular customer. To modify certain information, use the
	 * corresponding wallet modify request.
	 *
	 * @since 1.0
	 * @return boolean true if the wallet entry is a duplicate
	 */
	public function is_duplicate() {

		$element = $this->get_action_response_element();

		return isset( $element->IsDuplicate ) ? ( 'true' == (string) $element->IsDuplicate || '1' == (string) $element->IsDuplicate ) : null;
	}


}
