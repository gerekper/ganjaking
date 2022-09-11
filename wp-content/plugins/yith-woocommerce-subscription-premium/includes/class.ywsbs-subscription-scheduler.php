<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YWSBS_Subscription_Scheduler Object.
 *
 * @class   YITH_WC_Subscription
 * @since   2.0.0
 * @author  YITH
 * @package YITH WooCommerce Subscription
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'YWSBS_Subscription_Scheduler' ) ) {

	/**
	 * Class YWSBS_Subscription_Scheduler
	 */
	class YWSBS_Subscription_Scheduler {


		/**
		 * Single instance of the class
		 *
		 * @var YWSBS_Subscription_Scheduler
		 */
		protected static $instance;


		/**
		 * Returns single instance of the class
		 *
		 * @return YWSBS_Subscription_Scheduler
		 * @since  2.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * Initialize the YWSBS_Subscription_Scheduler Object
		 *
		 * @since 2.0.0
		 */
		public function __construct() {
			add_action( 'ywsbs_updated_subscription_date', array( $this, 'update_scheduled_action_by_date_change' ), 10, 3 );
			add_action( 'ywsbs_subscription_status_changed', array( $this, 'update_scheduled_action_by_status_change' ), 10, 3 );
		}


		/**
		 * Update scheduled action
		 *
		 * @param YWSBS_Subscription $subscription Subscription Object.
		 * @param string             $date_key     Type of date changed.
		 * @param string             $new_date     Timestamp of new value.
		 *
		 * @return void;
		 */
		public function update_scheduled_action_by_date_change( $subscription, $date_key, $new_date ) {

			/**
			 * The action scheduler uses UTC+0 time so we need to adjust the date according to this
			 */
			$gmt_offset = absint( get_option( 'gmt_offset' ) ) * 3600;
			$new_date   = $gmt_offset > 0 ? ( absint( $new_date ) + $gmt_offset ) : ( absint( $new_date ) - $gmt_offset );

			if ( 'payment_due_date' === $date_key && 'paypal' === $subscription->get_payment_method() ) {
				$new_date = ( $new_date - 12 * HOUR_IN_SECONDS );
			}

			$schedule_info = $this->get_schedule_info( $subscription, $date_key );

			if ( ! $schedule_info['hook'] ) {
				return;
			}

			$has_hook_scheduled = as_next_scheduled_action( $schedule_info['hook'], $schedule_info['args'] );

			if ( $has_hook_scheduled !== $new_date ) {
				as_unschedule_all_actions( $schedule_info['hook'], $schedule_info['args'] );

				if ( $new_date > time() && ! $subscription->has_status( array( 'cancelled', 'expired' ) ) ) {
					as_schedule_single_action( $new_date, $schedule_info['hook'], $schedule_info['args'] );
				}
			}
		}

		/**
		 * Update scheduled action when the status changes
		 *
		 * @param int    $subscription_id Subscription ID.
		 * @param string $old_status      Type of date changed.
		 * @param string $new_status      Timestamp of new value.
		 *
		 * @return void;
		 */
		public function update_scheduled_action_by_status_change( $subscription_id, $old_status, $new_status ) {

			$subscription = ywsbs_get_subscription( $subscription_id );

			if ( ! $subscription ) {
				return;
			}

			switch ( $new_status ) {
				case 'active':
					if ( 'cancelled' === $old_status && 0 !== $subscription->get_expired_date() ) {
						$this->update_scheduled_action_by_date_change( $subscription, 'expired_date', $subscription->get_expired_date() );
					}
					break;
				case 'trial':
					break;
				case 'paused':
					$this->update_scheduled_action_by_date_change( $subscription, 'payment_due_date', 0 );
					$this->update_scheduled_action_by_date_change( $subscription, 'expired_date', 0 );
					break;
				case 'cancelled':
				case 'expired':
					// delete all scheduled actions.
					$date_list = $this->get_date_changes_to_schedule();
					foreach ( $date_list as $date_key ) {
						$this->update_scheduled_action_by_date_change( $subscription, $date_key, 0 );
					}
					break;

			}
		}

		/**
		 * Get schedule hook
		 *
		 * @param YWSBS_Subscription $subscription Subscription.
		 * @param string             $key          Key.
		 *
		 * @return string
		 */
		public function get_schedule_info( $subscription, $key ) {
			$subscription_info = array(
				'hook' => false,
				'args' => array( 'subscription_id' => $subscription->get_id() ),
			);

			switch ( $key ) {
				case 'payment_due_date':
					$subscription_info['hook'] = 'yswbw_schedule_subscription_payment';
					break;
				case 'expired_date':
					$subscription_info['hook']               = 'yswbw_schedule_status_change';
					$subscription_info['args']['new_status'] = 'expired';
					break;
				case 'expired_pause_date':
					$subscription_info['hook']               = 'yswbw_schedule_status_change';
					$subscription_info['args']['new_status'] = 'resume';
					break;
				case 'next_attempt_date':
					$subscription_info['hook']                = 'yswbw_schedule_subscription_new_attempt';
					$subscription_info['args']['renew_order'] = $subscription->get_renew_order_id();
					break;
				case 'check_the_renew_order':
					$subscription_info['hook']                = 'yswbw_schedule_subscription_check';
					$subscription_info['args']['renew_order'] = $subscription->get_renew_order_id();
					break;
				case 'next_failed_status_change_date':
					$subscription_info['hook'] = 'yswbw_schedule_next_failed_status_change';
					break;
			}

			return $subscription_info;
		}

		/**
		 * Return the list of date that should be used to set, update or delete schedule action.
		 *
		 * @return array
		 */
		public static function get_date_changes_to_schedule() {
			$date_changes = array(
				'payment_due_date',
				'expired_date',
				'expired_pause_date',
				'next_attempt_date',
				'next_failed_status_change_date',
				'check_the_renew_order',
			);

			return apply_filters( 'ywsbs_data_changes_to_schedule', $date_changes );
		}

	}
}
