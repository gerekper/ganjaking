<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Implements YWSBS_Subscription_Cron Class
 *
 * @class   YWSBS_Subscription_Cron
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable  WordPress.DB.DirectDatabaseQuery.NoCaching
// phpcs:disable  WordPress.DateTime.CurrentTimeTimestamp.Requested
// phpcs:disable  WordPress.DB.PreparedSQL.InterpolatedNotPrepared
// phpcs:disable  WordPress.DB.DirectDatabaseQuery.DirectQuery
// phpcs:disable  WordPress.DB.PreparedSQL.NotPrepared
if ( ! class_exists( 'YWSBS_Subscription_Cron' ) ) {
	/**
	 * Class YWSBS_Subscription_Cron
	 */
	class YWSBS_Subscription_Cron {
		/**
		 * Single instance of the class
		 *
		 * @var YWSBS_Subscription_Cron
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YWSBS_Subscription_Cron
		 * @since  1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * Initialize class and registers actions and filters to be used
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'wp_loaded', array( $this, 'set_cron' ), 30 );

			add_action( 'ywsbs_renew_orders', array( $this, 'renew_orders' ) );
			add_action( 'ywsbs_pay_renew_subscription_orders', array( $this, 'ywsbs_pay_renew_subscription_orders' ) );

			add_action( 'ywsbs_check_subscription_payment', array( $this, 'ywsbs_check_subscription_payment' ) );
			add_action( 'ywsbs_cancel_subscription_expired', array( $this, 'ywsbs_cancel_subscription_expired' ) );

			add_action( 'ywsbs_resume_orders', array( $this, 'resume_orders' ) );

			add_action( 'ywsbs_check_overdue_subscriptions', array( $this, 'ywsbs_check_overdue_subscriptions' ) );
			add_action( 'ywsbs_check_suspended_subscriptions', array( $this, 'ywsbs_check_suspended_subscriptions' ) );

			// Trigger emails to send.
			add_action( 'ywsbs_trigger_email_renew_reminder', array( $this, 'ywsbs_trigger_email_renew_reminder' ) );
			add_action( 'ywsbs_trigger_email_before_subscription_expired', array( $this, 'ywsbs_trigger_email_before_subscription_expired' ) );

			if ( ywsbs_delete_cancelled_pending_enabled( true ) ) {
				add_action( 'ywsbs_trash_pending_subscriptions', array( $this, 'ywsbs_trash_pending_subscriptions' ) );
				add_action( 'ywsbs_trash_cancelled_subscriptions', array( $this, 'ywsbs_trash_cancelled_subscriptions' ) );
			}
		}

		/**
		 * Set cron.
		 */
		public function set_cron() {

			$ve         = get_option( 'gmt_offset' ) > 0 ? '+' : '-';
			$time_start = strtotime( '00:00 ' . $ve . get_option( 'gmt_offset' ) . ' HOURS' );

			if ( ! wp_next_scheduled( 'ywsbs_renew_orders' ) ) {
				wp_schedule_event( $time_start, 'hourly', 'ywsbs_renew_orders' );
			}

			if ( ! wp_next_scheduled( 'ywsbs_pay_renew_subscription_orders' ) ) {
				wp_schedule_event( $time_start, 'hourly', 'ywsbs_pay_renew_subscription_orders' );
			}

			if ( ! wp_next_scheduled( 'ywsbs_check_subscription_payment' ) ) {
				wp_schedule_event( $time_start, 'daily', 'ywsbs_check_subscription_payment' );
			}

			if ( ! wp_next_scheduled( 'ywsbs_cancel_subscription_expired' ) ) {
				wp_schedule_event( $time_start, 'daily', 'ywsbs_cancel_subscription_expired' );
			}

			// Reactivate the subscription when the period of pause is expired.
			if ( ! wp_next_scheduled( 'ywsbs_resume_orders' ) ) {
				wp_schedule_event( $time_start, 'daily', 'ywsbs_resume_orders' );
			}

			// Check subscriptions that are in overdue status.
			if ( ywsbs_get_overdue_time() && ! wp_next_scheduled( 'ywsbs_check_overdue_subscriptions' ) ) {
				wp_schedule_event( $time_start, 'daily', 'ywsbs_check_overdue_subscriptions' );
			}

			// Check subscriptions that are in suspended status.
			if ( ywsbs_get_suspension_time() && ! wp_next_scheduled( 'ywsbs_check_suspended_subscriptions' ) ) {
				wp_schedule_event( $time_start, 'daily', 'ywsbs_check_suspended_subscriptions' );
			}

			if ( ! wp_next_scheduled( 'ywsbs_trigger_email_renew_reminder' ) ) {
				wp_schedule_event( $time_start, 'daily', 'ywsbs_trigger_email_renew_reminder' );
			}

			if ( ! wp_next_scheduled( 'ywsbs_trigger_email_before_subscription_expired' ) ) {
				wp_schedule_event( $time_start, 'twicedaily', 'ywsbs_trigger_email_before_subscription_expired' );
			}

			// Privacy.
			if ( ywsbs_delete_cancelled_pending_enabled() ) {
				$trash_pending = get_option( 'ywsbs_trash_pending_subscriptions' );
				if ( isset( $trash_pending['number'] ) && ! empty( $trash_pending['number'] ) && ! wp_next_scheduled( 'ywsbs_trash_pending_subscriptions' ) ) {
					wp_schedule_event( $time_start, 'daily', 'ywsbs_trash_pending_subscriptions' );
				}

				$trash_cancelled = get_option( 'ywsbs_trash_cancelled_subscriptions' );
				if ( isset( $trash_cancelled['number'] ) && ! empty( $trash_cancelled['number'] ) && ! wp_next_scheduled( 'ywsbs_trash_cancelled_subscriptions' ) ) {
					wp_schedule_event( $time_start, 'daily', 'ywsbs_trash_cancelled_subscriptions' );
				}
			} else {
				wp_clear_scheduled_hook( 'ywsbs_trash_pending_subscriptions' );
				wp_clear_scheduled_hook( 'ywsbs_trash_cancelled_subscriptions' );
			}

		}


		/**
		 * Renew Order
		 *
		 * Create new order for active or in trial period subscription.
		 *
		 * @since  1.0.0
		 */
		public function renew_orders() {

			global $wpdb;

			$lock = get_option( 'yith_lock_renew_orders_cron', false );

			// If scheduled actions are enabled check only the subscription that are not converted to the scheduling.
			// Otherwise manage the subscriptions with CRON.
			if ( ! $lock ) {
				$to = current_time( 'timestamp' ) + 86400;
				yith_subscription_log( '======================', 'subscription_payment' );
				yith_subscription_log( 'Start renew order creation cron at ' . current_time( 'mysql' ), 'subscription_payment' );
				yith_subscription_log( 'Search subscriptions with Payment due date < ' . gmdate( 'Y-m-d H:i:s', $to ), 'subscription_payment' );

				if ( ywsbs_scheduled_actions_enabled() ) {
					$query = $wpdb->prepare(
						"SELECT ywsbs_p.ID FROM {$wpdb->posts} as ywsbs_p
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 LEFT JOIN  {$wpdb->postmeta} as ywsbs_pm4 ON ( ywsbs_p.ID = ywsbs_pm4.post_id AND ywsbs_pm4.meta_key='ywsbs_version' )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ( ywsbs_pm.meta_value = 'active' OR  ywsbs_pm.meta_value = 'trial') )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='payment_due_date' AND  ( ywsbs_pm2.meta_value  < $to ) )
                 AND ywsbs_pm4.post_id IS NULL 
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC",
						YITH_YWSBS_POST_TYPE
					);
				} else {
					$query = $wpdb->prepare(
						"SELECT ywsbs_p.ID FROM {$wpdb->posts} as ywsbs_p
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ( ywsbs_pm.meta_value = 'active' OR  ywsbs_pm.meta_value = 'trial') )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='payment_due_date' AND  ( ywsbs_pm2.meta_value  < $to ) )
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC",
						YITH_YWSBS_POST_TYPE
					);
				}

				$subscriptions = $wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

				yith_subscription_log( 'Found ' . count( $subscriptions ) . ' subscriptions', 'subscription_payment' );

				if ( ! empty( $subscriptions ) ) {
					foreach ( $subscriptions as $subscription_post ) {
						$subscription = ywsbs_get_subscription( $subscription_post->ID );

						if ( ! $subscription ) {
							yith_subscription_log( 'The subscription ' . $subscription_post->ID . ' does not exists', 'subscription_payment' );
							continue;
						}

						yith_subscription_log( 'Subscription #' . $subscription->get_id() . ' found', 'subscription_payment' );

						if ( ywsbs_scheduled_actions_enabled() && $subscription->get_payment_due_date() > time() ) {
							yith_subscription_log( 'The subscription ' . $subscription_post->ID . ' will be scheduled', 'subscription_payment' );
							$subscription->schedule_actions();
						} else {
							$renew_order_id = $subscription->get_renew_order_id();
							if ( 0 === $renew_order_id ) {
								if ( apply_filters( 'ywsbs_renew_order_by_cron', true, $subscription ) ) {
									$order_id = YWSBS_Subscription_Order()->renew_order( $subscription->get_id() );
									yith_subscription_log( 'New order created with id ' . $order_id . ' for the subscription', 'subscription_payment' );
								}
							} else {
								yith_subscription_log( 'a renew order exist with id ' . $renew_order_id, 'subscription_payment' );
							}
						}
					}
				}

				yith_subscription_log( '======================', 'subscription_payment' );
				delete_option( 'yith_lock_renew_orders_cron' );
			}

		}

		/**
		 * Check if there are subscription with payment in pending and change the status to overdue or suspended or remove the subscription
		 *
		 * @since  1.0.0
		 */
		public function ywsbs_check_subscription_payment() {

			global $wpdb;

			$after_renew_order_creation = get_option( 'ywsbs_change_status_after_renew_order_creation' );
			$offset                     = empty( $after_renew_ordercreation['wait_for'] ) ? 48 : (int) $after_renew_order_creation['wait_for'] * HOUR_IN_SECONDS;
			$new_status                 = $after_renew_order_creation['status'];
			$timestamp                  = current_time( 'timestamp' ) - intval( $offset );

			yith_subscription_log( '======================' );
			yith_subscription_log( 'Start check subscriptions that should change status after the payment due date at ' . current_time( 'mysql' ) );
			yith_subscription_log( 'Search subscriptions with Payment due date < ' . gmdate( 'Y-m-d H:i:s', $timestamp ) );

			if ( ywsbs_scheduled_actions_enabled() ) {
				$query = $wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->posts} as ywsbs_p
                 INNER JOIN {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN {$wpdb->postmeta} as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 LEFT JOIN {$wpdb->postmeta} as ywsbs_pm4 ON ( ywsbs_p.ID = ywsbs_pm4.post_id AND ywsbs_pm4.meta_key='ywsbs_version' )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'active' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='payment_due_date' AND  ywsbs_pm2.meta_value  < %d )
                 AND ywsbs_pm4.post_id IS NULL 
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ",
					'ywsbs_subscription',
					$timestamp
				);
			} else {
				$query = $wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->posts} as ywsbs_p
                 INNER JOIN {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN {$wpdb->postmeta} as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'active' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='payment_due_date' AND  ywsbs_pm2.meta_value  < %d )
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ",
					'ywsbs_subscription',
					$timestamp
				);
			}

			$subscriptions = $wpdb->get_results( $query );

			yith_subscription_log( 'Found ' . count( $subscriptions ) . ' subscriptions' );

			if ( ! empty( $subscriptions ) ) {
				foreach ( $subscriptions as $subscription_post ) {
					$subscription = ywsbs_get_subscription( $subscription_post->ID );

					yith_subscription_log( 'Process subscription with id #' . $subscription->get_id() );

					if ( ywsbs_scheduled_actions_enabled() ) {
						yith_subscription_log( 'Scheduling the status changes' );
						$subscription->set_status_during_the_renew();
					} else {
						if ( 'overdue' === $new_status ) {
							$subscription->update_status( 'overdue' );
							yith_subscription_log( 'Subscription ' . $subscription->get_id() . ' overdue' );
							YITH_WC_Activity()->add_activity( $subscription->get_id(), 'overdue', 'success', $subscription->get_order_id(), __( 'The subscription is overdue because the payment has not been received', 'yith-woocommerce-subscription' ) );
							continue;
						}

						if ( 'suspended' === $new_status ) {
							$subscription->update_status( 'suspended' );
							yith_subscription_log( 'Subscription ' . $subscription->get_id() . ' suspended' );
							YITH_WC_Activity()->add_activity( $subscription->get_id(), 'suspended', 'success', $subscription->get_order_id(), __( 'The subscription is suspended because the payment has not been received', 'yith-woocommerce-subscription' ) );
							continue;
						}

						yith_subscription_log( 'Subscription ' . $subscription->get_id() . ' cancelled' );
						YITH_WC_Activity()->add_activity( $subscription->get_id(), 'auto-cancelled', 'success', $subscription->get_order_id(), __( 'The subscription is cancelled because the payment has not been received', 'yith-woocommerce-subscription' ) );
						$subscription->cancel();
					}
				}
			}

			yith_subscription_log( '===============' );
		}

		/**
		 * Check if there are subscription expired and change the status to expired
		 *
		 * @since  1.0.0
		 */
		public function ywsbs_cancel_subscription_expired() {

			global $wpdb;

			// get all subscriptions that have status active and _expired_data < NOW to cancel the subscription.
			$timestamp = current_time( 'timestamp' );

			yith_subscription_log( '======================' );
			yith_subscription_log( 'Start expired cron at ' . gmdate( 'Y-m-d H:i:s', $timestamp ) );
			yith_subscription_log( 'Search subscription with expired date < ' . gmdate( 'Y-m-d H:i:s', $timestamp ) );

			if ( ywsbs_scheduled_actions_enabled() ) {
				$query = $wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->posts} as ywsbs_p
                 INNER JOIN {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN {$wpdb->postmeta} as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                LEFT JOIN {$wpdb->postmeta} as ywsbs_pm4 ON ( ywsbs_p.ID = ywsbs_pm4.post_id AND ywsbs_pm4.meta_key='ywsbs_version' )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'active' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='expired_date' AND  ( ywsbs_pm2.meta_value <> '' AND ywsbs_pm2.meta_value  < %d ) )
                 AND ywsbs_pm4.post_id IS NULL 
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ",
					'ywsbs_subscription',
					$timestamp
				);
			} else {
				$query = $wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->posts} as ywsbs_p
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'active' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='expired_date' AND  ( ywsbs_pm2.meta_value <> '' AND ywsbs_pm2.meta_value  < %d ) )
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ",
					'ywsbs_subscription',
					$timestamp
				);
			}
			$subscriptions = $wpdb->get_results( $query );

			yith_subscription_log( 'Found ' . count( $subscriptions ) . ' subscriptions' );

			if ( ! empty( $subscriptions ) ) {
				foreach ( $subscriptions as $subscription_post ) {
					$subscription = ywsbs_get_subscription( $subscription_post->ID );
					$subscription->update_status( 'expired' );
					yith_subscription_log( 'Subscription ' . $subscription->get_id() . ' expired' );
					YITH_WC_Activity()->add_activity( $subscription->get_id(), 'expired', 'success', $subscription->get_order_id(), __( 'The subscription has been cancelled because it has expired', 'yith-woocommerce-subscription' ) );
				}
			}

			yith_subscription_log( '======================' );
		}

		/**
		 * Resume Order
		 *
		 * Resume the subscription if the pause period is expired.
		 *
		 * @since  1.0.0
		 */
		public function resume_orders() {

			global $wpdb;

			$from = current_time( 'timestamp' );

			yith_subscription_log( '======================' );
			yith_subscription_log( 'Start resume orders cron at ' . current_time( 'mysql' ) );
			yith_subscription_log( 'Search subscriptions to resume with expired pause date <  ' . gmdate( 'Y-m-d H:i:s' ) );

			if ( ywsbs_scheduled_actions_enabled() ) {
				$query = $wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->posts} as ywsbs_p
                 INNER JOIN {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN {$wpdb->postmeta} as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 LEFT JOIN {$wpdb->postmeta} as ywsbs_pm4 ON ( ywsbs_p.ID = ywsbs_pm4.post_id AND ywsbs_pm4.meta_key='ywsbs_version' )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'paused' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='expired_pause_date' AND  ywsbs_pm2.meta_value  < $from )
                 AND ywsbs_pm4.post_id IS NULL 
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ",
					'ywsbs_subscription'
				);

			} else {
				$query = $wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->posts} as ywsbs_p
                 INNER JOIN {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN {$wpdb->postmeta} as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'paused' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='expired_pause_date' AND  ywsbs_pm2.meta_value  < $from )
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ",
					'ywsbs_subscription'
				);

			}

			$subscriptions = $wpdb->get_results( $query );

			yith_subscription_log( 'Found ' . count( $subscriptions ) . ' subscriptions' );
			if ( ! empty( $subscriptions ) ) {
				foreach ( $subscriptions as $subscription_post ) {
					$subscription = ywsbs_get_subscription( $subscription_post->ID );
					$result       = $subscription->update_status( 'resume' );
					$resumed      = $result ? 'resumed' : 'no resumed';
					yith_subscription_log( 'Subscription ' . $subscription->id . ' ' . $resumed . ' properly' );
				}
			}

			yith_subscription_log( '======================' );

		}

		/**
		 * Pay the renew orders
		 */
		public function ywsbs_pay_renew_subscription_orders() {
			global $wpdb;

			$lock = get_option( 'yith_lock_pay_renew_orders', false );
			if ( ! $lock ) {

				$status          = 'wc-' . YWSBS_Subscription_Order()->get_renew_order_status();
				$current_time    = current_time( 'timestamp' );
				$from            = $current_time - DAY_IN_SECONDS;
				$is_manual_renew = false;
				$messages        = array();

				yith_subscription_log( '===============', 'subscription_payment' );
				yith_subscription_log( 'Start Payment Renews cron at ' . current_time( 'mysql' ), 'subscription_payment' );
				yith_subscription_log( 'Search renew order with date < ' . gmdate( 'Y-m-d H:i:s', $from ), 'subscription_payment' );

				$query = "SELECT ywsbs_p.ID FROM {$wpdb->posts} as ywsbs_p
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 AND ( ywsbs_pm.meta_key='is_a_renew' AND  ywsbs_pm.meta_value = 'yes' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = %s";

				if ( ! apply_filters( 'ywsbs_use_date_format', false ) ) {
					$query .= " AND ywsbs_p.post_date_gmt < FROM_UNIXTIME($from)";
				} else {
					$date_from = gmdate( 'Y-m-d H:i:s', $from );
					$query    .= " AND ywsbs_p.post_date_gmt < '$date_from'";
				}

				$query .= " AND ( ywsbs_pm2.meta_key='failed_attemps' AND ywsbs_pm2.meta_value = 0 ) 
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC";

				$query = $wpdb->prepare( $query, 'shop_order', $status );

				$renew_orders_for_first_time = $wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

				yith_subscription_log( 'Found ' . count( $renew_orders_for_first_time ) . ' new renew orders', 'subscription_payment' );
				$messages [] = 'Search orders with failed payment';

				$query = $wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->posts} as ywsbs_p
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm3 ON ( ywsbs_p.ID = ywsbs_pm3.post_id )
                 AND ( ywsbs_pm2.meta_key='is_a_renew' AND  ywsbs_pm2.meta_value = 'yes' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = %s
                 AND ( ywsbs_pm3.meta_key='failed_attemps' AND ywsbs_pm3.meta_value > 0 )
                 AND ( ywsbs_pm.meta_key='next_payment_attempt' AND ywsbs_pm.meta_value <= %d )
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC",
					'shop_order',
					$status,
					$current_time
				);

				$renew_failed_orders = $wpdb->get_results( $query );   // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				yith_subscription_log( 'Found ' . count( $renew_failed_orders ) . ' failed renew orders', 'subscription_payment' );
				$renew_orders = array_merge( $renew_orders_for_first_time, $renew_failed_orders );

				if ( $renew_orders ) {
					foreach ( $renew_orders as $renew_order ) {
						if ( ywsbs_scheduled_actions_enabled() ) {
							$renew_order = wc_get_order( $renew_order->ID );
							if ( $renew_order ) {
								$subscriptions = $renew_order->get_meta( 'subscriptions' );
								if ( ! empty( $subscriptions ) ) {
									$subscription = ywsbs_get_subscription( $subscriptions[0] );
									// Pay only if the subscription is not scheduled.
									if ( $subscription && empty( $subscription->get( 'ywsbs_version' ) ) ) {
										YWSBS_Subscription_Order()->pay_renew_order( $renew_order->get_id(), $is_manual_renew );
									}
								}
							}
						} else {
							YWSBS_Subscription_Order()->pay_renew_order( $renew_order->ID, $is_manual_renew );
						}
					}
				}

				yith_subscription_log( '=======================', 'subscription_payment' );
				delete_option( 'yith_lock_pay_renew_orders' );
			}
		}


		/**
		 * Check if there are subscription with payment in pending and change the status to overdue or suspended or remove the subscription
		 *
		 * @since  1.0.0
		 */
		public function ywsbs_check_overdue_subscriptions() {

			global $wpdb;

			// get all subscriptions that have status overdue and _payment_due_date < NOW+ overdue period.
			$overdue_period = ywsbs_get_overdue_time();
			$timestamp      = current_time( 'timestamp' ) - $overdue_period;
			yith_subscription_log( '===============' );
			yith_subscription_log( 'Start check overdue subscription cron at ' . current_time( 'mysql' ) );
			yith_subscription_log( 'Search subscriptions with Payment due date < ' . gmdate( 'Y-m-d H:i:s', $timestamp ) );

			if ( ywsbs_scheduled_actions_enabled() ) {
				$query = $wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->posts} as ywsbs_p
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 LEFT JOIN {$wpdb->postmeta} as ywsbs_pm4 ON ( ywsbs_p.ID = ywsbs_pm4.post_id AND ywsbs_pm4.meta_key='ywsbs_version' )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'overdue' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='payment_due_date' AND  ywsbs_pm2.meta_value  < $timestamp )
                 AND ywsbs_pm4.post_id IS NULL 
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ",
					'ywsbs_subscription'
				);
			} else {
				$query = $wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->posts} as ywsbs_p
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'overdue' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='payment_due_date' AND  ywsbs_pm2.meta_value  < $timestamp )
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ",
					'ywsbs_subscription'
				);
			}

			$subscriptions = $wpdb->get_results( $query );

			yith_subscription_log( 'Found ' . count( $subscriptions ) . ' subscriptions' );
			if ( ! empty( $subscriptions ) ) {
				foreach ( $subscriptions as $subscription_post ) {
					$subscription = ywsbs_get_subscription( $subscription_post->ID );

					yith_subscription_log( 'Process subscription #' . $subscription->id );
					if ( ywsbs_get_suspension_time() ) {
						$subscription->update_status( 'suspended' );
						yith_subscription_log( 'Subscription change status to suspended' );
						YITH_WC_Activity()->add_activity( $subscription->id, 'suspended', 'success', $subscription->order_id, __( 'The subscription is suspended because the overdue period has finished.', 'yith-woocommerce-subscription' ) );
						continue;
					}

					YITH_WC_Activity()->add_activity( $subscription->id, 'auto-cancelled', 'success', $subscription->order_id, __( 'The subscription has been cancelled because the overdue period has finished.', 'yith-woocommerce-subscription' ) );
					$subscription->cancel();
					yith_subscription_log( 'Subscription change status to cancelled' );
				}
			}

			yith_subscription_log( '===============' );
		}

		/**
		 * Check if there are subscription with status suspended and if the period of suspension is expired
		 *
		 * @since  1.0.0
		 */
		public function ywsbs_check_suspended_subscriptions() {

			global $wpdb;

			// Before cancel the suspended subscription, try to pay the renews.
			$this->ywsbs_pay_renew_subscription_orders();

			$suspension_time = ywsbs_get_suspension_time();
			$timestamp       = current_time( 'timestamp' ) - $suspension_time;

			yith_subscription_log( '===============' );
			yith_subscription_log( 'Start check suspended subscription at ' . current_time( 'mysql' ) );
			yith_subscription_log( 'Search suspended subscriptions with Payment due date < ' . gmdate( 'Y-m-d H:i:s', $timestamp ) );
			if ( ywsbs_scheduled_actions_enabled() ) {
				$query = $wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->posts} as ywsbs_p
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
				 LEFT JOIN {$wpdb->postmeta} as ywsbs_pm4 ON ( ywsbs_p.ID = ywsbs_pm4.post_id AND ywsbs_pm4.meta_key='ywsbs_version' )                 
				 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'suspended' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='payment_due_date' AND  ywsbs_pm2.meta_value  < $timestamp )
                 AND ywsbs_pm4.post_id IS NULL 
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC",
					'ywsbs_subscription'
				);
			} else {
				$query = $wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->posts} as ywsbs_p
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'suspended' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='payment_due_date' AND  ywsbs_pm2.meta_value  < $timestamp )
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC",
					'ywsbs_subscription'
				);

			}
			$subscriptions = $wpdb->get_results( $query );

			yith_subscription_log( 'Found ' . count( $subscriptions ) . ' subscriptions' );
			if ( ! empty( $subscriptions ) ) {
				foreach ( $subscriptions as $subscription_post ) {
					$subscription = ywsbs_get_subscription( $subscription_post->ID );
					$subscription->cancel();
					yith_subscription_log( 'Subscription  ' . $subscription->get_id() . ' cancelled' );
					YITH_WC_Activity()->add_activity( $subscription->get_id(), 'cancelled', 'success', $subscription->get_order_id(), __( 'The subscription has been cancelled because the suspension period has finished.', 'yith-woocommerce-subscription' ) );
				}
			}

			yith_subscription_log( '================' );

		}

		/**
		 * Check if there are subscription expired and change the status to expired
		 *
		 * @since  1.0.0
		 */
		public function ywsbs_trigger_email_before_subscription_expired() {

			global $wpdb;

			// get all subscriptions that have status active and _expired_data < NOW to cancel the subscription.
			$timestamp     = current_time( 'timestamp' ) + apply_filters( 'ywsbs_trigger_email_before', 86400 );
			$subscriptions = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->posts} as ywsbs_p
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'active' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='expired_date' AND  ( ywsbs_pm2.meta_value <> '' AND ywsbs_pm2.meta_value  < %d ) )
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ",
					'ywsbs_subscription',
					$timestamp
				)
			);

			if ( ! empty( $subscriptions ) ) {
				foreach ( $subscriptions as $subscription_post ) {
					$subscription = ywsbs_get_subscription( $subscription_post->ID );
					do_action( 'ywsbs_customer_subscription_before_expired_mail', $subscription );
				}
			}
		}

		/**
		 * Check if there are email to send as renew reminder
		 *
		 * @since  1.0.0
		 */
		public function ywsbs_trigger_email_renew_reminder() {

			global $wpdb;

			$emails = WC()->mailer()->get_emails();
			if ( isset( $emails['YITH_WC_Customer_Subscription_Renew_Reminder'] ) ) {
				$email   = $emails['YITH_WC_Customer_Subscription_Renew_Reminder'];
				$enabled = $email->is_enabled();
			} else {
				return false;
			}

			$enabled = apply_filters( 'ywsbs_enable_email_renew_reminder', $enabled );

			if ( ! yith_plugin_fw_is_true( $enabled ) ) {
				return false;
			}

			$delay = $email ? $email->get_option( 'delay' ) : 15;
			$delay = $delay <= 0 ? 1 : $delay;

			// get all subscriptions that have status active and _expired_data < NOW to cancel the subscription.
			$gap  = apply_filters( 'ywsbs_enable_email_renew_reminder_time', $delay * DAY_IN_SECONDS );
			$to   = current_time( 'timestamp' ) + $gap;
			$from = absint( current_time( 'timestamp' ) + $gap - DAY_IN_SECONDS );

			$product_ids = implode( ',', apply_filters( 'ywsbs_enable_email_renew_reminder_products', array() ) );

			if ( ! empty( $product_ids ) ) {
				$query = $wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->posts} as ywsbs_p
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm3 ON ( ywsbs_p.ID = ywsbs_pm3.post_id )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'active' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='payment_due_date' AND  ( ywsbs_pm2.meta_value  < $to AND  ywsbs_pm2.meta_value  >= $from ) )
                 AND ( ywsbs_pm3.meta_key='product_id' AND  ywsbs_pm3.meta_value IN ( {$product_ids}) )
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ",
					'ywsbs_subscription'
				);

			} else {
				$query = $wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->posts} as ywsbs_p
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'active' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='payment_due_date' AND  ( ywsbs_pm2.meta_value  < $to AND  ywsbs_pm2.meta_value  >= $from ) )
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ",
					'ywsbs_subscription'
				);

			}

			$subscriptions = $wpdb->get_results( $query );
			if ( ! empty( $subscriptions ) ) {
				foreach ( $subscriptions as $subscription_post ) {

					$subscription = ywsbs_get_subscription( $subscription_post->ID );
					if ( empty( $subscription->get( 'num_of_rates' ) ) || ( $subscription->get_paid_rates() < $subscription->get( 'num_of_rates' ) ) ) {
						do_action( 'ywsbs_customer_subscription_renew_reminder_mail', $subscription );
					}
				}
			}
		}

		/**
		 * Trash pending subscriptions after a specific time.
		 *
		 * @since 1.4.0
		 */
		public function ywsbs_trash_pending_subscriptions() {

			global $wpdb;

			$trash_pending = get_option( 'ywsbs_trash_pending_subscriptions' );
			if ( ! ywsbs_delete_cancelled_pending_enabled() || ! isset( $trash_pending['number'] ) || empty( $trash_pending['number'] ) ) {
				return;
			}

			$time = strtotime( '-' . $trash_pending['number'] . ' ' . $trash_pending['unit'] );

			$subscriptions = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->posts} as ywsbs_p
                 INNER JOIN {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'pending' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ywsbs_p.post_date < %s 
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ",
					YITH_YWSBS_POST_TYPE,
					gmdate( 'Y-m-d H:i:s', $time )
				)
			);

			if ( ! empty( $subscriptions ) ) {
				foreach ( $subscriptions as $subscription ) {
					$subscription_id = $subscription->ID;
					$subscription    = ywsbs_get_subscription( $subscription->ID );
					$order           = $subscription->get( 'order_id' );
					$subscription->trash();
					YITH_WC_Activity()->add_activity( $subscription_id, 'trashed', 'success', $order, __( 'The subscription was been trashed after the specific duration because was in pending status.', 'yith-woocommerce-subscription' ) );
				}
			}
		}

		/**
		 * Trash cancelled subscriptions after a specific time.
		 *
		 * @since 1.4.0
		 */
		public function ywsbs_trash_cancelled_subscriptions() {
			global $wpdb;
			$trash_cancelled = get_option( 'ywsbs_trash_cancelled_subscriptions' );

			if ( ! ywsbs_delete_cancelled_pending_enabled() || ! isset( $trash_cancelled['number'] ) || empty( $trash_cancelled['number'] ) ) {
				return;
			}

			$time = strtotime( '-' . $trash_cancelled['number'] . ' ' . $trash_cancelled['unit'] );

			$subscriptions = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->posts} as ywsbs_p
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->postmeta} as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'cancelled' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='end_date' AND  ywsbs_pm2.meta_value  < %d )
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ",
					YITH_YWSBS_POST_TYPE,
					$time
				)
			);

			if ( ! empty( $subscriptions ) ) {
				foreach ( $subscriptions as $subscription ) {
					$subscription_id = $subscription->ID;
					$subscription    = ywsbs_get_subscription( $subscription->ID );
					$order           = $subscription->get( 'order_id' );
					$subscription->trash();
					YITH_WC_Activity()->add_activity( $subscription_id, 'trashed', 'success', $order, __( 'The subscription was been trashed after the specific duration because was in cancelled status.', 'yith-woocommerce-subscription' ) );
				}
			}

		}

	}
}


/**
 * Unique access to instance of YWSBS_Subscription_Cron class
 *
 * @return YWSBS_Subscription_Cron
 */
function YWSBS_Subscription_Cron() { // phpcs:ignore
	return YWSBS_Subscription_Cron::get_instance();
}
