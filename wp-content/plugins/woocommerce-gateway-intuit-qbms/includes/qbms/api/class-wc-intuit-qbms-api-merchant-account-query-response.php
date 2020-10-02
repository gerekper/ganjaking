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
 * Intuit QBMS Merchant Account Query Response
 *
 * Represents a merchant account query response
 *
 * @since 1.0
 */
class WC_Intuit_QBMS_API_Merchant_Account_Query_Response extends WC_Intuit_QBMS_API_Response {


	/**
	 * Some merchants charge a fixed fee, regardless of transaction amount, for
	 * credit card transactions. This field value indicates the amount of the
	 * fee charged by the current merchant.
	 *
	 * @since 1.0
	 * @return string merchant account fees
	 */
	public function get_convenience_fees() {

		$element = $this->get_action_response_element();

		return isset( $element->ConvenienceFees ) ? (string) $element->ConvenienceFees : null;
	}


	/**
	 * The type of credit card accepted by the merchant, for example, Visa or
	 * MasterCard. One CreditCardType element is returned for each supported
	 * card type in the response.
	 *
	 * @since 1.0
	 * @return array of accepted credit card type names, ie 'JCB', 'DinersClub', 'Visa', 'MasterCard', 'Discover', 'AmericanExpress'
	 */
	public function get_credit_card_types() {

		$element = $this->get_action_response_element();

		$credit_card_types = array();

		if ( $element ) {
			foreach ( $element->children() as $child ) {
				if ( 'CreditCardType' == $child->getName() ) {
					$credit_card_types[] = (string) $child;
				}
			}
		}

		return $credit_card_types;
	}


	/**
	 * Returns true, if merchant account is set to process check transactions.
	 *
	 * @since 1.0
	 * @return boolean true if check transactions are accepted
	 */
	public function is_check_accepted() {

		$element = $this->get_action_response_element();

		return isset( $element->IsCheckAccepted ) ? ( 'true' == (string) $element->IsCheckAccepted || '1' == (string) $element->IsCheckAccepted ) : null;
	}


	/**
	 * An integer (0-23) representing the hour to close batches in Greenwich
	 * Mean Time (GMT). All transactions with a defined BatchID will
	 * automatically close at this time.
	 *
	 * Note: Merchant controlled batches are either manually closed using the
	 * MerchantBatchClose request or configured to automatically close using
	 * the MerchantAccountMod request. The two methods of closing merchant
	 * controlled batches cannot be mixed.
	 *
	 * @since 1.0
	 * @return int the hour to close batches in GMT
	 */
	public function get_batch_hour_close() {

		$element = $this->get_action_response_element();

		return isset( $element->BatchCloseHour ) ? (int) $element->BatchCloseHour : null;
	}


}
