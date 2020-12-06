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
 * Intuit QBMS API Wallet Query Response.
 *
 * Represents the response from a Wallet Query request, which is used to retrieve any and all of a customers tokenized credit cards.
 * This response looks like:
 *
 * <CustomerCreditCardWalletQuery>
 *   <CreditCardWalletEntry>
 *     <WalletEntryID />
 *     <MaskedCreditCardNumber />
 *     <ExpirationMonth />
 *     <ExpirationYear />
 *     <NameOnCard />
 *     <CreditCardAddress />
 *     <CreditCardCity />
 *     <CreditCardState />
 *     <CreditCardPostalCode />
 *   </CreditCardWalletEntry>
 * </CustomerCreditCardWalletQuery>
 *
 * Note: No version of this exists for the QBMS eCheck payment type as of API version 4.5
 *
 * @since 1.0
 */
class WC_Intuit_QBMS_API_Wallet_Query_Response extends WC_Intuit_QBMS_API_Response implements Framework\SV_WC_Payment_Gateway_API_Get_Tokenized_Payment_Methods_Response {


	/**
	 * Gets any payment tokens.
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_API_Get_Tokenized_Payment_Methods_Response::get_payment_tokens()
	 *
	 * @return \WC_Intuit_QBMS_Payment_Token[] payment tokens
	 */
	public function get_payment_tokens() {

		$element = $this->get_action_response_element();
		$tokens  = [];

		if ( $element ) {

			foreach ( $element->children() as $wallet_entry ) {

				/**
				 * Note: 'cart_type' and 'last four' are automatically determined in {@see \WC_Intuit_QBMS_Payment_Token::__construct()}:
				 * @see \WC_Intuit_QBMS_Payment_Token::get_type_from_token()
				 * @see \WC_Intuit_QBMS_Payment_Token::get_last_four_from_token()
				 */
				$token = new \WC_Intuit_QBMS_Payment_Token( (string) $wallet_entry->WalletEntryID, [
					'exp_month' => (string) $wallet_entry->ExpirationMonth,
					'exp_year'  => (string) $wallet_entry->ExpirationYear
				] );

				$tokens[ (string) $wallet_entry->WalletEntryID ] = $token;
			}
		}

		return $tokens;
	}


}
