<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWAF_Ajax' ) ) {

	/**
	 * Implements AJAX for YWAF plugin
	 *
	 * @class   YWAF_Ajax
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWAF_Ajax {

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			add_action( 'wp_ajax_ywaf_fraud_risk_check', array( $this, 'fraud_risk_check' ) );
			add_action( 'wp_ajax_ywaf_resend_email', array( $this, 'resend_paypal_email' ) );
			add_action( 'wp_ajax_nopriv_ywaf_resend_email', array( $this, 'resend_paypal_email' ) );

		}

		/**
		 * Start Fraud risk check from admin pages
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function fraud_risk_check() {

			if ( check_admin_referer( 'ywaf-check-fraud-risk' ) ) {

				try {

					$repeat   = false;
					$response = array();

					if ( ( isset( $_POST['repeat'] ) && $_POST['repeat'] == 'true' ) || ( isset( $_GET['repeat'] ) && $_GET['repeat'] == 'true' ) ) {
						$repeat = true;
					}

					$order_id = absint( $_GET['order_id'] );
					YITH_WAF()->set_fraud_check( $order_id, $repeat );

					$redirect = wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=shop_order' );

					if ( isset( $_GET['single'] ) ) {

						$response['status']   = 'success';
						$response['redirect'] = $redirect;

					} else {
						wp_safe_redirect( $redirect );
					}


				} catch ( Exception $e ) {

					$response['status'] = 'fail';
					$response['error']  = $e->getMessage();

				}

				wp_send_json( $response );

			}

			exit;

		}

		/**
		 * Re-send verify email to customer
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function resend_paypal_email() {

			if ( check_admin_referer( 'ywaf-resend-email' ) ) {

				$response = array();

				try {

					$order = wc_get_order( $_GET['order_id'] );

					YITH_WAF()->paypal_mail_send( $order );

					if ( is_ajax() ) {

						$response['status'] = 'success';
						wc_add_notice( __( 'Email was sent successfully! Please check your mailbox', 'yith-woocommerce-anti-fraud' ), 'success' );

					} else {
						$redirect = wp_get_referer() ? wp_get_referer() : $order->get_view_order_url();
						wp_safe_redirect( $redirect );
						exit;
					}


				} catch ( Exception $e ) {

					if ( ! empty( $e ) ) {
						$response['status'] = 'failure';
						wc_add_notice( $e->getMessage(), 'error' );
					}

				}

				if ( is_ajax() ) {

					ob_start();
					wc_print_notices();
					$messages = ob_get_clean();

					$response['messages'] = isset( $messages ) ? $messages : '';

				}

				wp_send_json( $response );

			}

			exit;

		}

	}

	new YWAF_Ajax();

}