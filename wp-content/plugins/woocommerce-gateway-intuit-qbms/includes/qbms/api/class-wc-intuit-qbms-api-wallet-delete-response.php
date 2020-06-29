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
 * Intuit QBMS API Wallet Delete Response
 *
 * Represents the reponse from a Delete Wallet request for removing a tokenized
 * payment method
 *
 * @since 1.0
 */
class WC_Intuit_QBMS_API_Wallet_Delete_Response extends WC_Intuit_QBMS_API_Response {


	/**
	 * Checks if the transaction was successful
	 *
	 * @link https://developer.intuit.com/docs/030_qbms/0060_documentation/error_handling#QBMS_and_Processing_Errors
	 *
	 * @since 1.0
	 * @see WC_Intuit_QBMS_API_Response::transaction_approved()
	 * @return bool true if approved, false otherwise
	 */
	public function transaction_approved() {

		$approved = parent::transaction_approved();

		// "No wallet-related records found in database." but we don't care, we just want the token to be gone
		if ( 10315 == $this->get_status_code() ) $approved = true;

		return $approved;

	}

}
