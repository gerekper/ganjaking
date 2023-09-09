<?php
/**
 * WooCommerce Chase Paymentech
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Chase Paymentech to newer
 * versions in the future. If you wish to customize WooCommerce Chase Paymentech for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-chase-paymentech/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Chase_Paymentech;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_7 as Framework;

/**
 * The capture handler.
 *
 * @since 1.12.0
 *
 * @method  \WC_Gateway_Chase_Paymentech get_gateway()
 */
class Capture extends Framework\Payment_Gateway\Handlers\Capture {


	/**
	 * Handles successful capture.
	 *
	 * @since 1.12.0
	 *
	 * @param \WC_Order $order order object
	 * @param Framework\SV_WC_Payment_Gateway_API_Response $response API response object
	 */
	public function do_capture_success( \WC_Order $order, Framework\SV_WC_Payment_Gateway_API_Response $response ) {

		parent::do_capture_success( $order, $response );

		if ( $this->get_gateway()->is_certification_mode() ) {

			$message = __( 'Orbital Certification Capture Charge Test Results:', 'woocommerce-gateway-chase-paymentech' );

			$note_data = array();

			// if a the transaction was tokenized
			if ( $token = $this->get_gateway()->get_order_meta( $order, 'payment_token' ) ) {
				$note_data[ __( 'Customer Profile ID', 'woocommerce-gateway-chase-paymentech' ) ] = $token;
			}

			// if the test generated an authorization code
			if ( $auth_code = $this->get_gateway()->get_order_meta( $order, 'authorization_code' ) ) {
				$note_data[ __( 'Auth Code', 'woocommerce-gateway-chase-paymentech' ) ] = $auth_code;
			}

			// if the test generated a TxRefNum
			if ( $transaction_id = $response->get_transaction_id() ) {
				$note_data[ __( 'TxRefNum', 'woocommerce-gateway-chase-paymentech' ) ] = $transaction_id;
			}

			foreach ( $note_data as $label => $value ) {
				$message .= '<br /><strong>' . esc_html( $label ) . '</strong>: ' . esc_html( $value );
			}

			$order->add_order_note( $message );
		}
	}


	/**
	 * Determines if an order is ready for capture.
	 *
	 * This adds an additional check for API configuration.
	 *
	 * @since 1.12.0
	 *
	 * @param \WC_Order $order order object
	 * @return bool
	 */
	public function is_order_ready_for_capture( \WC_Order $order ) {

		return $this->get_gateway()->is_direct_api_configured() && parent::is_order_ready_for_capture( $order );
	}


}
