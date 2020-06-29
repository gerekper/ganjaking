<?php
/**
 * YITH Account Funds Gateway class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAF_YITH_YWF' ) ) {
	/**
	 * WooCommerce Paypal Gateway
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_YITH_YWF {

		/**
		 * Single instance of the class for each token
		 *
		 * @var \YITH_WCAF_Paypal_Gateway
		 * @since 1.0.0
		 */
		protected static $instance = null;

		/* === PAYMENT METHODS === */

		/**
		 * Execute a mass payment
		 *
		 * @param $payments_id array Array of registered payments to credit to funds
		 *
		 * @return mixed Array with operation status and messages
		 * @since 1.0.0
		 */
		public function pay( $payments_id ) {

			if ( ! class_exists( 'YITH_YWF_Customer' ) ) {
				return array(
					'status'   => false,
					'messages' => __( 'There was an issue with gateway installation; please, refer to technical support', 'yith-woocommerce-affiliates' )
				);
			}

			$mass_pay_payments = array();

			// if single payment id, convert it to array
			if ( ! is_array( $payments_id ) ) {
				$payments_id = (array) $payments_id;
			}

			foreach ( $payments_id as $payment_id ) {
				$payment         = YITH_WCAF_Payment_Handler()->get_payment( $payment_id );
				$commissions     = YITH_WCAF_Payment_Handler()->get_payment_commissions( $payment_id );
				$commissions_ids = wp_list_pluck( $commissions, 'ID' );

				if ( ! $payment ) {
					continue;
				}

				$mass_pay_payments[] = $payment;

				$affiliate              = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $payment['affiliate_id'] );
				$customer_funds_handler = new YITH_YWF_Customer( $affiliate['user_id'] );

				// credit funds to user account
				$credit = round( $payment['amount'], wc_get_price_decimals() );
				$customer_funds_handler->add_funds( $credit );

				YITH_WCAF_Payment_Handler()->change_payment_status( $payment_id, 'pending' );

				YWF_Log()->add_log( array(
					'user_id'        => $affiliate['user_id'],
					'order_id'       => '',
					'fund_user'      => $credit,
					'type_operation' => 'admin_op',
					'description'    => sprintf( _n( 'Funds credited to user as payment for commission %s (Payment #%d)', 'Funds credited to user as payment for commissions %s (Payment #%d)', count( $commissions ), 'yith-woocommerce-affiliates' ), implode( ', ', $commissions_ids ), $payment_id )
				) );

				do_action( 'yith_wcaf_ipn_received', array(
					'status'    => 'Completed',
					'unique_id' => $payment_id,
					'txn_id'    => ''
				) );

			}

			if ( ! empty( $mass_pay_payments ) ) {
				foreach ( $mass_pay_payments as $sent_payment ) {
					YITH_WCAF_Payment_Handler()->add_note( array(
						'payment_id'   => $sent_payment['ID'],
						'note_content' => __( 'Payment correctly credited to user funds', 'yith-woocommerce-affiliates' )
					) );
				}
			}

			return array(
				'status'   => true,
				'messages' => __( 'Payment sent', 'yith-woocommerce-affiliates' )
			);
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAF_Paypal_Gateway
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}
	}
}

/**
 * Unique access to instance of YITH_WCAF_Paypal_Gateway class
 *
 * @return \YITH_WCAF_Paypal_Gateway
 * @since 1.0.0
 */
function YITH_WCAF_YITH_YWF() {
	return YITH_WCAF_YITH_YWF::get_instance();
}