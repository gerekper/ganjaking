<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YWSBS_Subscription_Scheduler_Actions Object.
 *
 * @class   YITH_WC_Subscription
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'YWSBS_Subscription_Scheduler_Actions' ) ) {

	/**
	 * Class YWSBS_Subscription_Scheduler_Actions
	 */
	class YWSBS_Subscription_Scheduler_Actions {


		/**
		 * Single instance of the class
		 *
		 * @var YWSBS_Subscription_Scheduler_Actions
		 */
		protected static $instance;


		/**
		 * Returns single instance of the class
		 *
		 * @return YWSBS_Subscription_Scheduler_Actions
		 * @since  2.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * Initialize the YWSBS_Subscription_Scheduler_Actions Object
		 *
		 * @since 2.0.0
		 */
		public function __construct() {
			add_action( 'yswbw_schedule_subscription_payment', array( $this, 'subscription_payment' ), 10, 1 );

			add_action( 'yswbw_schedule_status_change', array( $this, 'change_status' ), 10, 2 );

			add_action( 'yswbw_schedule_subscription_check', array( $this, 'check_the_renew_order' ), 10, 2 );

			add_action( 'yswbw_schedule_subscription_new_attempt', array( $this, 'subscription_payment_retry' ), 10, 2 );
			add_action( 'yswbw_schedule_next_failed_status_change', array( $this, 'failed_status_change' ), 10, 1 );
		}

		/**
		 * Try to pay the subscription.
		 *
		 * @param int $subscription_id Subscription to pay.
		 */
		public function subscription_payment( $subscription_id ) {

			yith_subscription_log( '___ Scheduled Action - Request Payment for the subscription #' . $subscription_id . ' _____', 'subscription_payment' );

			$subscription = ywsbs_get_subscription( $subscription_id );

			if ( ! $subscription ) {
				yith_subscription_log( 'Payment failed. The subscription #' . $subscription_id . ' does not exists', 'subscription_payment' );
				return;
			}

			$renew_order = $subscription->get_renew_order();

			if ( ! $renew_order ) {
				$renew_order_id = YWSBS_Subscription_Order()->renew_order( $subscription_id );
			} else {
				$renew_order_id = $renew_order->get_id();
			}

			// change the subscription status or scheduling this change.

			$subscription->set_status_during_the_renew();

			YWSBS_Subscription_Order()->pay_renew_order( $renew_order_id );

		}


		/**
		 * Try to pay the renew order.
		 *
		 * @param int $subscription_id Subscription to pay.
		 * @param int $renew_order Renew order.
		 */
		public function subscription_payment_retry( $subscription_id, $renew_order ) {

			yith_subscription_log( '___ Scheduled Action - Request New Attempt Payment for the subscription #' . $subscription_id . ' _____', 'subscription_payment' );

			$subscription = ywsbs_get_subscription( $subscription_id );

			if ( ! $subscription ) {
				yith_subscription_log( 'Payment failed. The subscription #' . $subscription_id . ' does not exists', 'subscription_payment' );
				return;
			}
			if ( empty( $subscription->get_renew_order_id() ) ) {
				yith_subscription_log( 'Payment failed. The subscription does not have renew orders to pay', 'subscription_payment' );
			} elseif ( $subscription->get_renew_order_id() === $renew_order ) {

				YWSBS_Subscription_Order()->pay_renew_order( $renew_order );
			} else {
				yith_subscription_log( 'Payment failed. The renew order is not the current renew order of subscription the renew order to pay is: ' . $subscription->get_renew_order_id(), 'subscription_payment' );
			}

		}

		/**
		 * Try to pay the renew order.
		 *
		 * @param int $subscription_id Subscription to pay.
		 * @param int $renew_order Renew order to pay.
		 */
		public function check_the_renew_order( $subscription_id, $renew_order ) {

			yith_subscription_log( '___ Scheduled Action - Request to check the Payment for the subscription #' . $subscription_id . ' _____', 'subscription_payment' );

			$subscription = ywsbs_get_subscription( $subscription_id );

			if ( ! $subscription ) {
				yith_subscription_log( 'Payment failed. The subscription #' . $subscription_id . ' does not exists', 'subscription_payment' );
				return;
			}

			if ( empty( $subscription->get_renew_order_id() ) ) {
				yith_subscription_log( 'Payment failed. The subscription doesn not have renew orders to pay', 'subscription_payment' );
			} elseif ( $subscription->get_renew_order_id() === (int) $renew_order ) {
				// check the subscription payment after 10 minutes.
				YWSBS_Subscription_Order()->pay_renew_order( $renew_order );
			} else {
				yith_subscription_log( 'Payment failed. The renew order is not the current renew order of subscription the renew order to pay is: ' . $subscription->get_renew_order_id(), 'subscription_payment' );
			}

		}

		/**
		 * Change the status to the subscription.
		 *
		 * @param int    $subscription_id Subscription to pay.
		 * @param string $new_status New status.
		 */
		public function change_status( $subscription_id, $new_status ) {

			yith_subscription_log( '___ Scheduled Action - Triggered change failed status for the subscription #' . $subscription_id . ' _____', 'subscription_payment' );

			$subscription = ywsbs_get_subscription( $subscription_id );

			if ( ! $subscription ) {
				return;
			}

			$update = false;
			switch ( $new_status ) {
				case 'resume':
					$update = $subscription->can_be_resumed();
					break;
				case 'expired':
					$update = $subscription->can_be_expired();
					break;
			}

			! $update && yith_subscription_log( 'It is not possible set the new status ' . $new_status, 'subscription_payment' );

			$update && $subscription->update_status( $new_status );
		}

		/**
		 * Change the status to the subscription.
		 *
		 * @param int $subscription_id Subscription to pay.
		 */
		public function failed_status_change( $subscription_id ) {

			yith_subscription_log( '___ Scheduled Action - Triggered change status for the subscription #' . $subscription_id . ' _____', 'subscription_payment' );

			$subscription = ywsbs_get_subscription( $subscription_id );

			if ( ! $subscription ) {
				return;
			}

			$subscription->update_failed_status();

		}


	}
}
