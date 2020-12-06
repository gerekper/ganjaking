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
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-intuit-qbms/
 *
 * @package   WC-Intuit-QBMS/Gateway
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The transaction capture handler.
 *
 * @since 2.7.3
 */
class WC_Gateway_Intuit_QBMS_Capture_Handler extends Framework\Payment_Gateway\Handlers\Capture {


	/**
	 * Adds the standard capture data to an order.
	 *
	 * Moved from deprecated method WC_Gateway_Intuit_QBMS_Credit_Card::add_payment_gateway_capture_data()
	 *
	 * @since 2.7.3
	 *
	 * @param \WC_Order $order the order object
	 * @param Framework\SV_WC_Payment_Gateway_API_Response $response transaction response
	 */
	public function do_capture_success( \WC_Order $order, Framework\SV_WC_Payment_Gateway_API_Response $response ) {

		parent::do_capture_success( $order, $response );

		// capture fields
		if ( $response instanceof \WC_Intuit_QBMS_API_Credit_Card_Charge_Response ) {

			$this->get_gateway()->update_order_meta( $order, 'merchant_account_number', $response->get_merchant_account_number() );
			$this->get_gateway()->update_order_meta( $order, 'recon_batch_id',          $response->get_recon_batch_id() );
			$this->get_gateway()->update_order_meta( $order, 'payment_grouping_code',   $response->get_payment_grouping_code() );
			$this->get_gateway()->update_order_meta( $order, 'txn_authorization_stamp', $response->get_txn_authorization_stamp() );

			$this->get_gateway()->update_order_meta( $order, 'capture_trans_id',           $response->get_transaction_id() );
			$this->get_gateway()->update_order_meta( $order, 'capture_authorization_code', $response->get_authorization_code() );
			$this->get_gateway()->update_order_meta( $order, 'capture_client_trans_id',    $response->get_client_trans_id() );
		}
	}


	/**
	 * Handles capture authorization errors.
	 *
	 * Moved from deprecated method WC_Gateway_Intuit_QBMS_Credit_Card::do_credit_card_capture_failed()
	 *
	 * @since 2.7.3
	 *
	 * @param \WC_Order $order WooCommerce order object
	 * @param Framework\SV_WC_Payment_Gateway_API_Response $response API response object
	 */
	public function do_capture_failed( \WC_Order $order, Framework\SV_WC_Payment_Gateway_API_Response $response ) {

		// check for the 10406 error code, which indicates the transaction is invalid for capture
		// if we get this back, mark it as invalid for capture so it isn't tried again
		if ( ! $response->transaction_approved() && '10406' == $response->get_status_code() ) {

			// mark the capture as invalid
			$this->get_gateway()->update_order_meta( $order, 'auth_can_be_captured', 'no' );
		}
	}


}
