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
 * Intuit QBMS Credit Card Authorization Refund Response
 *
 * Represents a credit card refund response
 *
 * @since 2.0.0
 */
class WC_Intuit_QBMS_API_Credit_Card_Refund_Response extends WC_Intuit_QBMS_API_Credit_Card_Charge_Response {


	/**
	 * Determines if this ended up as a void.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public function is_void() {

		$element = $this->get_action_response_element();

		return isset( $element->VoidOrRefundTxnType ) && 'Void' === $element->VoidOrRefundTxnType;
	}


	/**
	 * Not available for refund response
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Authorization_Response::get_avs_result()
	 * @return null
	 */
	public function get_avs_result() {
		return null;
	}


	/**
	 * Not available for refund response
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Authorization_Response::get_csc_result()
	 * @return null
	 */
	public function get_csc_result() {
		return null;
	}


	/**
	 * Not available for refund response
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Authorization_Response::csc_match()
	 * @return null
	 */
	public function csc_match() {
		return null;
	}


}
