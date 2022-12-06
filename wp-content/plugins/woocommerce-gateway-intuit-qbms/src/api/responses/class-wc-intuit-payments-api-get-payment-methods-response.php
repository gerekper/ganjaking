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
 * The get payment methods response class.
 *
 * @since 2.0.0
 */
class WC_Intuit_Payments_API_Get_Payment_Methods_Response extends WC_Intuit_Payments_API_Response {


	/**
	 * Gets the store payment token objects.
	 *
	 * @since 2.0.0
	 * @return array of Framework\SV_WC_Payment_Gateway_Payment_Token objects
	 */
	public function get_payment_tokens() {

		$methods = ! empty( $this->response_data ) ? $this->response_data : array();
		$tokens  = array();

		foreach ( $methods as $method ) {

			if ( ! empty( $method->expMonth ) ) {

				$data = array(
					'type'      => 'credit_card',
					'card_type' => $method->cardType,
					'last_four' => ltrim( $method->number, 'x' ),
					'exp_month' => $method->expMonth,
					'exp_year'  => $method->expYear,
				);

			} else {

				$data = array(
					'type'         => 'echeck',
					'last_four'    => ltrim( $method->accountNumber, 'x' ),
					'account_type' => 'PERSONAL_SAVINGS' === $method->accountType ? 'savings' : 'checking',
				);
			}

			$tokens[ $method->id ] = new Framework\SV_WC_Payment_Gateway_Payment_Token( $method->id, $data );
		}

		return $tokens;
	}


}
