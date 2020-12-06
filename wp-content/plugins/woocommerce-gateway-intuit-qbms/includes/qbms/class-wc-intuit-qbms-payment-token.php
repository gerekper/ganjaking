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
 * @package   WC-Intuit-QBMS/Classes
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_1 as Framework;

/**
 * WooCommerce Intuit QBMS Payment Token
 *
 * Represents a credit card or check payment token
 *
 * @since 1.0
 */
class WC_Intuit_QBMS_Payment_Token extends Framework\SV_WC_Payment_Gateway_Payment_Token {


	/**
	 * Initializes a payment token where $token is a 24 digit globally unique id
	 * provided by QBMS with a predetermined structure used to infer
	 * certain information about the payment method. The WalletEntryID is
	 * structured as follows:
	 *
	 * + 1 digit wallet entry type (1 - Credit Card, 2 - Check)
	 * + 2 digit brand type (01 - Visa, 02 - MasterCard, 03 - Amex, 04 - Discover, 05 - DinersClub, 06 - JCB, 00 - Check)
	 * + 17 digit random number
	 * + Last 4 digits of the credit card or check account number.
	 *
	 * @since 1.0.0
	 *
	 * @param string $token the QBMS Wallet entry ID value
	 * @param array|\WC_Payment_Token $data associated data or the core token object
	 */
	public function __construct( $token, $data ) {

		if ( $data instanceof \WC_Payment_Token ) {
			$data = $data->get_data();
		}

		$data['type']      = $this->get_type_from_token( $token );
		$data['last_four'] = $this->get_last_four_from_token( $token );

		if ( 'credit_card' === $data['type'] ) {
			$data['card_type'] = $this->get_card_type_from_token( $token );
		}

		parent::__construct( $token, $data );
	}


	/**
	 * Returns 'credit_card' or 'check' depending on the wallet type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $token payment token as a string
	 * @return string one of 'credit_card' or 'check' depending on the payment type
	 */
	private function get_type_from_token( $token ) {

		return strpos( $token, '1' ) === 0 ? 'credit_card' : 'check';
	}


	/**
	 * Returns the payment type (visa, mc, amex, disc, diners, jcb, echeck, etc).
	 *
	 * @since 1.0.0
	 *
	 * @param string $token payment token as a string
	 * @return string the payment type
	 */
	private function get_card_type_from_token( $token ) {

		$type_id = substr( $token, 1, 2 );

		switch ( $type_id ) {

			case '01': return Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_VISA;
			case '02': return Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_MASTERCARD;
			case '03': return Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_AMEX;
			case '04': return Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_DISCOVER;
			case '05': return Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_DINERSCLUB;
			case '06': return Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_JCB;
			case '00': return 'echeck';
			default:   return 'unknown';
		}
	}


	/**
	 * Returns the last four digits of the credit card or check account number.
	 *
	 * @since 1.0.0
	 *
	 * @param string $token payment token as a string
	 * @return string last four of account
	 */
	private function get_last_four_from_token( $token ) {

		return substr( $token, -4 );
	}


}
