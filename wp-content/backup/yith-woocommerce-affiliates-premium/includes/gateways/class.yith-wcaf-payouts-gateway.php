<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_WCAF_PayOuts_Gateway' ) ) {

	class YITH_WCAF_PayOuts_Gateway {

		protected static $instance = null;

		public function __construct() {

			add_action( 'yith_paypal_payout_item_change_status', array(
				$this,
				'change_affiliate_payment_status'
			), 10, 3 );
			add_filter( 'yith_payouts_receivers', array( $this, 'yith_remove_affiliate_from_receiver_list' ), 10, 1 );
			add_filter( 'yith_payout_receiver_email', array( $this, 'yith_return_affiliate_paypal_email' ), 10, 2 );
		}

		/**
		 * @return YITH_WCAF_PayOuts_Gateway
		 * @author Salvatore Strano
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}


		/* === PAYMENT METHODS === */

		/**
		 * Execute a mass payment
		 *
		 * @param $payments_id array Array of registered payments to issue to paypal servers
		 *
		 * @return mixed Array with operation status and messages
		 * @since 1.0.0
		 */
		public function pay( $payments_id ) {
			$payout_items      = array();
			$mass_pay_payments = array();
			$status            = true;
			$errors            = array();

			if ( ! function_exists( 'YITH_PayOuts_Service' ) ) {

				YITH_PayPal_Payouts()->load_payouts_classes();
			}

			if ( ! YITH_PayOuts_Service()->check_service_configuration() ) {

				$payouts_query_args = array(
					'page' => 'yith_wc_paypal_payouts_panel',
					'tab'  => 'general-settings'
				);

				$payouts_url = esc_url( add_query_arg( $payouts_query_args, admin_url( 'admin.php' ) ) );
				$message     = sprintf( '%s <a href="%s">%s</a>',
					_x( 'Cannot use PayOuts service, please check the Payout configuration', 'Cannot use PayOuts service, please check the Payout configuration here', 'yith-woocommerce-affiliates' ),
					$payouts_url,
					_x( 'here', 'par of :  Cannot use PayOuts service, please check the Payout configuration here', 'yith-woocommerce-affiliates' )
				);

				return array(
					'status'   => false,
					'messages' => esc_html( $message )
				);

			}

			// if single payment id, convert it to array
			if ( ! is_array( $payments_id ) ) {
				$payments_id = (array) $payments_id;
			}

			$currency = get_woocommerce_currency();

			foreach ( $payments_id as $payment_id ) {

				$single_payment = YITH_WCAF_Payment_Handler()->get_payment( $payment_id );
				if ( ! $single_payment || empty( $single_payment['payment_email'] ) ) {
					continue;
				}

				$single_payout = array(
					'recipient_type' => 'EMAIL',
					"receiver"       => $single_payment['payment_email'],
					"note"           => "Thank you.",
					"sender_item_id" => 'affiliate_payment_' . $payment_id,
					"amount"         => array(
						"value"    => $single_payment['amount'],
						"currency" => $currency
					)
				);

				$payout_items[] = $single_payout;


				$mass_pay_payments[] = $single_payment;
			}

			if ( count( $payout_items ) == 0 ) {

				$status    = false;
				$errors [] = __( 'No record could be processed for PayPal payment; payment email field is mandatory', 'yith-woocommerce-affiliates' );

			} else {

				$register_args = array(
					'sender_batch_id' => 'affiliate_' . uniqid(),
					'payout_mode'     => 'affiliate',
					'order_id'        => '',
					'items'           => $payout_items
				);
				YITH_PayOuts_Service()->register_payouts( $register_args );

				unset( $register_args['order_id'] );
				unset( $register_args['items'] );
				unset( $register_args['payout_mode'] );
				unset( $register_args['items'] );

				$register_args['sender_items'] = $payout_items;
				$payout                        = YITH_PayOuts_Service()->PayOuts( $register_args );

				if ( $payout instanceof \PayPal\Api\PayoutBatch && $payout->getBatchHeader()->getErrors() ) {

					$status   = false;
					$errors[] = $payout->getBatchHeader()->getErrors()->getMessage();
				}

				if ( ! empty( $mass_pay_payments ) ) {
					foreach ( $mass_pay_payments as $payment ) {
						YITH_WCAF_Payment_Handler_Premium()->change_payment_status( $payment['ID'], 'pending' );
						do_action( 'yith_wcaf_payment_sent', $payment );
					}
				}
			}

			return array(
				'status'   => $status,
				'messages' => count( $errors ) > 0 ? $errors : __( 'Payment sent', 'yith-woocommerce-affiliates' )
			);
		}

		/**
		 * @param string $payout_item_id
		 * @param string $transaction_status
		 * @param array  $resource
		 */
		public function change_affiliate_payment_status( $payout_item_id, $transaction_status, $resource ) {

			$sender_item_id = isset( $resource['payout_item']['sender_item_id'] ) ? $resource['payout_item']['sender_item_id'] : '';
			$sender_item_id = str_replace( 'affiliate_payment_', '', $sender_item_id );
			$transaction_id = isset( $resource['transaction_id'] ) ? $resource['transaction_id'] : '';

			if ( $sender_item_id > 0 ) {

				if ( 'success' == $transaction_status ) {

					$args = array(
						'unique_id' => $sender_item_id,
						'status'    => 'Completed',
						'txn_id'    => $transaction_id
					);

					YITH_WCAF_Payment_Handler_Premium()->handle_notification( $args );
				}
			}
		}

		/**
		 * remove affiliate user
		 *
		 * @param array $receivers
		 *
		 * @return array
		 * @author Salvatore Strano
		 * @since  1.0.0
		 *
		 */
		public function yith_remove_affiliate_from_receiver_list( $receivers ) {

			$new_receivers = array();
			foreach ( $receivers as $key => $receiver ) {

				$user_id = $receiver['user_id'];

				if ( ! YITH_WCAF_Affiliate_Handler()->is_user_valid_affiliate( $user_id ) ) {
					$new_receivers[] = $receiver;
				}

			}

			return $new_receivers;
		}

		/**
		 * get the affiliate paypal email for show payouts details on myaccount
		 *
		 * @param string $paypal_email
		 * @param int    $user_id
		 *
		 * @return string
		 * @since  1.0.0
		 *
		 * @author Salvatore Strano
		 */
		public function yith_return_affiliate_paypal_email( $paypal_email, $user_id ) {

			if ( empty( $paypal_email ) && YITH_WCAF_Affiliate_Handler()->is_user_valid_affiliate( $user_id ) ) {

				$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_user_id( $user_id );

				$paypal_email = $affiliate['payment_email'];

			}

			return $paypal_email;
		}
	}
}

/**
 * Unique access to instance of YITH_WCAF_Paypal_Gateway class
 *
 * @return \YITH_WCAF_PayOuts_Gateway
 * @since 1.0.0
 */
function YITH_WCAF_PayOuts_Gateway() {
	return YITH_WCAF_PayOuts_Gateway::get_instance();
}