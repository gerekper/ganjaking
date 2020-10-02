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
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_8_1 as Framework;

/**
 * The payment method response class.
 *
 * @since 2.0.0
 */
class WC_Intuit_Payments_API_Payment_Method_Response extends WC_Intuit_Payments_API_Payment_Response implements Framework\SV_WC_Payment_Gateway_API_Create_Payment_Token_Response {


	/**
	 * Determines if the transaction was approved.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public function transaction_approved() {

		$errors = $this->get_errors()->get_error_codes();

		return empty( $errors );
	}


	/**
	 * Gets the saved payment method ID.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	protected function get_method_id() {

		return $this->id;
	}


	/**
	 * Gets the saved payment method type.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	protected function get_method_type() {

		return isset( $this->response_data->expMonth ) ? 'credit_card' : 'echeck';
	}


	/**
	 * Gets the payment token object.
	 *
	 * This is generated from the saved payment method data.
	 *
	 * @since 2.0.0
	 * @return Framework\SV_WC_Payment_Gateway_Payment_Token
	 */
	public function get_payment_token() {

		if ( 'credit_card' === $this->get_method_type() ) {

			$data = array(
				'type'      => $this->get_method_type(),
				'card_type' => $this->cardType,
				'last_four' => ltrim( $this->number, 'x' ),
				'exp_month' => $this->expMonth,
				'exp_year'  => $this->expYear,
			);

		} else {

			$data = array(
				'type'         => $this->get_method_type(),
				'last_four'    => ltrim( $this->accountNumber, 'X' ),
				'account_type' => 'PERSONAL_SAVINGS' === $this->accountType ? 'savings' : 'checking',
			);
		}

		return new Framework\SV_WC_Payment_Gateway_Payment_Token( $this->get_method_id(), $data );
	}


}
