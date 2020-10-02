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
 * The Payments API eCheck response class.
 *
 * @since 2.0.0
 */
class WC_Intuit_Payments_API_eCheck_Response extends WC_Intuit_Payments_API_Payment_Response {


	/**
	 * Determines if the transaction was held.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public function transaction_held() {

		return 'PENDING' === $this->get_status_code();
	}


	/**
	 * Gets a list of the transaction statuses considered "approved".
	 *
	 * @since 2.0.0
	 * @return array
	 */
	protected function get_approved_statuses() {

		return array( 'SUCCEEDED' );
	}


	/**
	 * Gets the transaction status message.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_status_message() {

		if ( $this->transaction_approved() ) {
			$message = 'The transaction has been accepted by the ACH network and the money has been moved';
		} elseif ( $this->transaction_held() ) {
			$message = 'The transaction has been submitted to the ACH network and is waiting for approval';
		} else {
			$message = 'The transaction has been rejected by the ACH network and no money moved';
		}

		return $message;
	}


	/**
	 * Gets the payment type.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_payment_type() {

		return Framework\SV_WC_Payment_Gateway::PAYMENT_TYPE_ECHECK;
	}


}
