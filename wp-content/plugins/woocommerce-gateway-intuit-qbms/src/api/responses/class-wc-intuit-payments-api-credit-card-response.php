<?php
/**
 * WooCommerce Intuit Payments
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
 * @package   WC-Intuit-Payments/API
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2023, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_4 as Framework;

/**
 * The Payments API credit card response class.
 *
 * @since 2.0.0
 */
class WC_Intuit_Payments_API_Credit_Card_Response extends WC_Intuit_Payments_API_Payment_Response implements Framework\SV_WC_Payment_Gateway_API_Authorization_Response {


	/**
	 * Gets a list of the transaction statuses considered "approved".
	 *
	 * @since 2.0.0
	 * @return array
	 */
	protected function get_approved_statuses() {

		return array( 'AUTHORIZED', 'CAPTURED' );
	}


	/**
	 * Determines if the transaction was held.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public function transaction_held() {

		$error_types = (array) $this->get_errors()->get_error_data();

		return in_array( 'fraud_warning', $error_types, true );
	}


	/**
	 * Gets the authorization code.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_authorization_code() {

		return $this->authCode;
	}


	/**
	 * Gets the transaction type.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_transaction_type() {

		return $this->type;
	}


	/**
	 * Returns the result of the AVS check.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_avs_result() {

		return $this->avsStreet;
	}


	/**
	 * Gets the result of the CSC check.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_csc_result() {

		return $this->cardSecurityCodeMatch;
	}


	/**
	 * Determines if the CSC check was successful.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public function csc_match() {

		return true;
	}


	/**
	 * Gets the customer-friendly message.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_user_message() {

		if ( $this->transaction_held() ) {
			return '';
		}

		$message_ids = array();

		$helper = new Framework\SV_WC_Payment_Gateway_API_Response_Message_Helper();

		foreach ( $this->get_errors()->get_error_codes() as $code ) {

			switch ( $code ) {

				// Invalid CSC
				case 'PMT-2000':
				case 'PMT-2001':
					$message_ids[] = 'csc_mismatch';
				break;

				// Invalid address
				case 'PMT-2002':
				case 'PMT-2003':
					$message_ids[] = 'avs_mismatch';
				break;

				// Plain old decline
				case 'PMT-5000':
					$message_ids[] = 'decline';
				break;

				// Invalid card type
				case 'PMT-5001':
					$message_ids[] = 'card_type_not_accepted';
				break;

				// Generic error
				default:
					$message_ids[] = 'error';
			}
		}

		if ( 'DECLINED' === $this->get_status_code() && ! in_array( 'decline', $message_ids, true ) ) {
			$message_ids[] = 'card_declined';
		}

		return $helper->get_user_messages( $message_ids );
	}


	/**
	 * Gets the payment type.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_payment_type() {

		return Framework\SV_WC_Payment_Gateway::PAYMENT_TYPE_CREDIT_CARD;
	}


}
