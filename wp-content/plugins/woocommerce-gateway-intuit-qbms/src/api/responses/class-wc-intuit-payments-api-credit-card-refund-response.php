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
 * @copyright Copyright (c) 2013-2022, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * The Payments API credit card refund response class.
 *
 * @since 2.0.0
 */
class WC_Intuit_Payments_API_Credit_Card_Refund_Response extends WC_Intuit_Payments_API_Payment_Refund_Response {


	/**
	 * Gets a list of the transaction statuses considered "approved".
	 *
	 * @since 2.0.0
	 * @return array
	 */
	protected function get_approved_statuses() {

		return array( 'ISSUED', 'SETTLED' );
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
