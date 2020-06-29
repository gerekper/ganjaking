<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements YWSBS_Subscription_Cron Class
 *
 * @class   YWSBS_Subscription_Cron
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YWSBS_Subscription_Cron' ) ) {

	class YWSBS_Subscription_Cron {

		/**
		 * Single instance of the class
		 *
		 * @var \YWSBS_Subscription_Cron
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YWSBS_Subscription_Cron
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			add_action( 'wp_loaded', array( $this, 'set_cron' ), 30 );

			add_action( 'ywsbs_renew_orders', array( $this, 'renew_orders' ) );
			add_action( 'ywsbs_check_subscription_payment', array( $this, 'ywsbs_check_subscription_payment' ) );
			add_action( 'ywsbs_cancel_subscription_expired', array( $this, 'ywsbs_cancel_subscription_expired' ) );
			add_action( 'ywsbs_trigger_email_renew_reminder', array( $this, 'ywsbs_trigger_email_renew_reminder' ) );
			add_action(
				'ywsbs_trigger_email_before_subscription_expired',
				array(
					$this,
					'ywsbs_trigger_email_before_subscription_expired',
				)
			);
			add_action( 'ywsbs_resume_orders', array( $this, 'resume_orders' ) );
			add_action( 'ywsbs_check_overdue_subscriptions', array( $this, 'ywsbs_check_overdue_subscriptions' ) );
			add_action( 'ywsbs_check_suspended_subscriptions', array( $this, 'ywsbs_check_suspended_subscriptions' ) );

			if ( yith_check_privacy_enabled( true ) ) {
				add_action( 'ywsbs_trash_pending_subscriptions', array( $this, 'ywsbs_trash_pending_subscriptions' ) );
				add_action(
					'ywsbs_trash_cancelled_subscriptions',
					array(
						$this,
						'ywsbs_trash_cancelled_subscriptions',
					)
				);
			}

			add_action( 'ywsbs_pay_renew_subscription_orders', array( $this, 'ywsbs_pay_renew_subscription_orders' ) );

		}


		/**
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function set_cron() {

			$ve         = get_option( 'gmt_offset' ) > 0 ? '+' : '-';
			$time_start = strtotime( '00:00 ' . $ve . get_option( 'gmt_offset' ) . ' HOURS' );

			if ( ! wp_next_scheduled( 'ywsbs_renew_orders' ) ) {
				wp_schedule_event( $time_start, 'hourly', 'ywsbs_renew_orders' );
			}

			if ( ! wp_next_scheduled( 'ywsbs_check_subscription_payment' ) ) {
				wp_schedule_event( $time_start, 'daily', 'ywsbs_check_subscription_payment' );
			}

			if ( ! wp_next_scheduled( 'ywsbs_cancel_subscription_expired' ) ) {
				wp_schedule_event( $time_start, 'daily', 'ywsbs_cancel_subscription_expired' );
			}

			if ( ! wp_next_scheduled( 'ywsbs_trigger_email_renew_reminder' ) ) {
				wp_schedule_event( $time_start, 'daily', 'ywsbs_trigger_email_renew_reminder' );
			}

			if ( ! wp_next_scheduled( 'ywsbs_trigger_email_before_subscription_expired' ) ) {
				wp_schedule_event( $time_start, 'twicedaily', 'ywsbs_trigger_email_before_subscription_expired' );
			}

			// Reactive the subscription when the period of pause is expired
			if ( ! wp_next_scheduled( 'ywsbs_resume_orders' ) ) {
				wp_schedule_event( $time_start, 'daily', 'ywsbs_resume_orders' );
			}

			// Check subscriptions that are in overdue status
			if ( YITH_WC_Subscription()->overdue_time() && ! wp_next_scheduled( 'ywsbs_check_overdue_subscriptions' ) ) {
				wp_schedule_event( $time_start, 'daily', 'ywsbs_check_overdue_subscriptions' );
			}

			// Check subscriptions that are in suspended status
			if ( YITH_WC_Subscription()->suspension_time() && ! wp_next_scheduled( 'ywsbs_check_suspended_subscriptions' ) ) {
				wp_schedule_event( $time_start, 'daily', 'ywsbs_check_suspended_subscriptions' );
			}

			if ( yith_check_privacy_enabled( true ) ) {
				$trash_pending = get_option( 'ywsbs_trash_pending_subscriptions' );
				if ( isset( $trash_pending['number'] ) && ! empty( $trash_pending['number'] ) && ! wp_next_scheduled( 'ywsbs_trash_pending_subscriptions' ) ) {
					wp_schedule_event( $time_start, 'daily', 'ywsbs_trash_pending_subscriptions' );
				}

				$trash_cancelled = get_option( 'ywsbs_trash_cancelled_subscriptions' );
				if ( isset( $trash_cancelled['number'] ) && ! empty( $trash_cancelled['number'] ) && ! wp_next_scheduled( 'ywsbs_trash_cancelled_subscriptions' ) ) {
					wp_schedule_event( $time_start, 'daily', 'ywsbs_trash_cancelled_subscriptions' );
				}
			}

			if ( ! wp_next_scheduled( 'ywsbs_pay_renew_subscription_orders' ) ) {
				wp_schedule_event( $time_start, 'hourly', 'ywsbs_pay_renew_subscription_orders' );
			}

		}


		/**
		 * Renew Order
		 *
		 * Create new order for active or in trial period subscription
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function renew_orders() {

			global $wpdb;

			$to = current_time( 'timestamp' ) + 86400;
			yith_subscription_log( '======================', 'subscription_payment' );
			yith_subscription_log( 'Start renew order cron at ' . current_time( 'mysql' ), 'subscription_payment' );
			yith_subscription_log( 'Search subscriptions with Payment due date < ' . date( 'Y-m-d H:i:s', $to ), 'subscription_payment' );

			$query = $wpdb->prepare(
				"SELECT ywsbs_p.ID FROM {$wpdb->prefix}posts as ywsbs_p
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm3 ON ( ywsbs_p.ID = ywsbs_pm3.post_id )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ( ywsbs_pm.meta_value = 'active' OR  ywsbs_pm.meta_value = 'trial') )
                 AND ( ywsbs_pm3.meta_key='renew_order' AND  ywsbs_pm3.meta_value = 0 )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='payment_due_date' AND  ( ywsbs_pm2.meta_value  < $to ) )
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ",
				'ywsbs_subscription'
			);

			$subscriptions = $wpdb->get_results( $query );
			yith_subscription_log( 'Found ' . count( $subscriptions ) . ' subscriptions', 'subscription_payment' );
			if ( ! empty( $subscriptions ) ) {
				foreach ( $subscriptions as $subscription_post ) {
					$subscription  = ywsbs_get_subscription( $subscription_post->ID );
					$order_pending = $subscription->renew_order;

					yith_subscription_log( 'Subscription #' . $subscription->id . ' found', 'subscription_payment' );

					if ( $order_pending == 0 ) {
						if ( $subscription->get( 'end_date' ) != '' ) {
							$paid_orders = $subscription->get( 'payed_order_list' );
							$num_rates   = $subscription->get( 'max_length' );
							if ( ! empty( $paid_orders ) && ! empty( $num_rates ) && $paid_orders >= $num_rates ) {
								continue;
							}
						}
						if ( ! apply_filters( 'ywsbs_renew_order_by_cron', true, $subscription ) ) {
							continue;
						}

						$order_id = YWSBS_Subscription_Order()->renew_order( $subscription->id );

						yith_subscription_log( 'New order created with id ' . $order_id . ' for the subscription', 'subscription_payment' );
					} else {
						yith_subscription_log( 'a renew order exist with id ' . $order_pending, 'subscription_payment' );
					}
				}
			}

			yith_subscription_log( '======================', 'subscription_payment' );

		}

		/**
		 * Resume Order
		 *
		 * Resume the subscription if the pause period is expired
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function resume_orders() {

			global $wpdb;

			$from = current_time( 'timestamp' );

			yith_subscription_log( '======================' );

			yith_subscription_log( 'Start resume orders cron at ' . current_time( 'mysql' ) );
			yith_subscription_log( 'Search subscriptions to resume with expired pause date <  ' . date( 'Y-m-d H:i:s' ) );

			$query = $wpdb->prepare(
				"SELECT ywsbs_p.ID FROM {$wpdb->prefix}posts as ywsbs_p
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'paused' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='expired_pause_date' AND  ywsbs_pm2.meta_value  < $from )
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ",
				'ywsbs_subscription'
			);

			$subscriptions = $wpdb->get_results( $query );
			yith_subscription_log( 'Found ' . count( $subscriptions ) . ' subscriptions' );
			if ( ! empty( $subscriptions ) ) {
				foreach ( $subscriptions as $subscription_post ) {
					$subscription = ywsbs_get_subscription( $subscription_post->ID );

					$result = $subscription->update_status( 'resume' );

					if ( $result ) {
						$resumed = 'resumed';
					} else {
						$resumed = 'no resumed';
					}
					yith_subscription_log( 'Subscription ' . $subscription->id . ' ' . $resumed . ' properly' );
				}
			}

			yith_subscription_log( '======================' );

		}

		/**
		 * Check if there are subscription expired and change the status to expired
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function ywsbs_cancel_subscription_expired() {

			global $wpdb;

			// get all subscriptions that have status active and _expired_data < NOW to cancel the subscription
			$timestamp = current_time( 'timestamp' );

			yith_subscription_log( '======================' );
			yith_subscription_log( 'Start expired cron at ' . date( 'Y-m-d H:i:s', $timestamp ) );
			yith_subscription_log( 'Search subscription with expired date < ' . date( 'Y-m-d H:i:s', $timestamp ) );

			$subscriptions = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->prefix}posts as ywsbs_p
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'active' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='expired_date' AND  ( ywsbs_pm2.meta_value <> '' AND ywsbs_pm2.meta_value  < $timestamp ) )
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ",
					'ywsbs_subscription'
				)
			);

			yith_subscription_log( 'Found ' . count( $subscriptions ) . ' subscriptions' );
			if ( ! empty( $subscriptions ) ) {
				foreach ( $subscriptions as $subscription_post ) {
					$subscription = new YWSBS_Subscription( $subscription_post->ID );
					$subscription->update_status( 'expired' );
					yith_subscription_log( 'Subscription ' . $subscription->id . ' expired' );
					YITH_WC_Activity()->add_activity( $subscription->id, 'expired', 'success', $subscription->order_id, __( 'The subscription has been cancelled because it has expired', 'yith-woocommerce-subscription' ) );
				}
			}

			yith_subscription_log( '======================' );
		}

		/**
		 * Check if there are email to send as renew reminder
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
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

			// get all subscriptions that have status active and _expired_data < NOW to cancel the subscription
			$gap  = apply_filters( 'ywsbs_enable_email_renew_reminder_time', $delay * DAY_IN_SECONDS );
			$to   = current_time( 'timestamp' ) + $gap;
			$from = absint( current_time( 'timestamp' ) + $gap - DAY_IN_SECONDS );

			$product_ids = implode( ',', apply_filters( 'ywsbs_enable_email_renew_reminder_products', array() ) );

			if ( ! empty( $product_ids ) ) {
				$query = $wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->prefix}posts as ywsbs_p
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm3 ON ( ywsbs_p.ID = ywsbs_pm3.post_id )
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
					"SELECT ywsbs_p.ID FROM {$wpdb->prefix}posts as ywsbs_p
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
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
					$subscription = new YWSBS_Subscription( $subscription_post->ID );
					do_action( 'ywsbs_customer_subscription_renew_reminder_mail', $subscription );
				}
			}
		}

		/**
		 * Check if there are subscription expired and change the status to expired
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function ywsbs_trigger_email_before_subscription_expired() {

			global $wpdb;

			// get all subscriptions that have status active and _expired_data < NOW to cancel the subscription
			$timestamp     = current_time( 'timestamp' ) + apply_filters( 'ywsbs_trigger_email_before', 86400 );
			$subscriptions = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->prefix}posts as ywsbs_p
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'active' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='expired_date' AND  ( ywsbs_pm2.meta_value <> '' AND ywsbs_pm2.meta_value  < $timestamp ) )
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ",
					'ywsbs_subscription'
				)
			);

			if ( ! empty( $subscriptions ) ) {
				foreach ( $subscriptions as $subscription_post ) {
					$subscription = new YWSBS_Subscription( $subscription_post->ID );
					do_action( 'ywsbs_customer_subscription_before_expired_mail', $subscription );
				}
			}
		}

		/**
		 * Check if there are subscription with payment in pending and change the status to overdue or suspended or remove the subscription
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function ywsbs_check_subscription_payment() {

			global $wpdb;

			// get all subscriptions that have status active and _payment_due_date < NOW to cancel the subscription
			$start_period = get_option( 'ywsbs_cancel_start_period', 24 );

			$offset = ! empty( $start_period ) ? $start_period : 24;

			if ( YITH_WC_Subscription()->overdue_time() ) {
				$offset = get_option( 'ywsbs_overdue_start_period' );
			}

			if ( YITH_WC_Subscription()->suspension_time() ) {
				$offset = get_option( 'ywsbs_enable_suspension_period' );
			}

			$timestamp = current_time( 'timestamp' ) - intval( $offset ) * 3600;

			yith_subscription_log( '======================' );
			yith_subscription_log( 'Start check subscription payment cron at ' . current_time( 'mysql' ) );
			yith_subscription_log( 'Search subscriptions with Payment due date < ' . date( 'Y-m-d H:i:s', $timestamp ) );

			$subscriptions = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->prefix}posts as ywsbs_p
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'active' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='payment_due_date' AND  ywsbs_pm2.meta_value  < $timestamp )
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ",
					'ywsbs_subscription'
				)
			);

			yith_subscription_log( 'Found ' . count( $subscriptions ) . ' subscriptions' );
			if ( ! empty( $subscriptions ) ) {
				foreach ( $subscriptions as $subscription_post ) {
					$subscription = new YWSBS_Subscription( $subscription_post->ID );

					yith_subscription_log( 'Process subscription with id #' . $subscription->id );
					// if the subscription have a payment with a gateway and the option suspend for failed recurring payment is checked
					// the plugin doesn't change the status of the subscription. If the gateway will send the failed payment the subscription will
					// change the status to suspended
					if ( get_option( 'ywsbs_suspend_for_failed_recurring_payment' ) == 'yes' && in_array( $subscription->payment_method, ywsbs_get_gateways_list() ) ) {
						yith_subscription_log( 'No action for this subscription because has paid with ' . $subscription->payment_method );
						continue;
					}

					if ( YITH_WC_Subscription()->overdue_time() ) {
						$subscription->update_status( 'overdue' );
						yith_subscription_log( 'Subscription ' . $subscription->id . ' overdue' );
						YITH_WC_Activity()->add_activity( $subscription->id, 'overdue', 'success', $subscription->order_id, __( 'The subscription is overdue because the payment has not been received', 'yith-woocommerce-subscription' ) );
						continue;
					}

					if ( YITH_WC_Subscription()->suspension_time() ) {
						$subscription->update_status( 'suspended' );
						yith_subscription_log( 'Subscription ' . $subscription->id . ' suspended' );
						YITH_WC_Activity()->add_activity( $subscription->id, 'suspended', 'success', $subscription->order_id, __( 'The subscription is suspended because the payment has not been received', 'yith-woocommerce-subscription' ) );
						continue;
					}
					yith_subscription_log( 'Subscription ' . $subscription->id . ' cancelled' );
					YITH_WC_Activity()->add_activity( $subscription->id, 'auto-cancelled', 'success', $subscription->order_id, __( 'The subscription is cancelled because the payment has not been received', 'yith-woocommerce-subscription' ) );
					$subscription->cancel();

				}
			}

			yith_subscription_log( '===============' );
		}

		/**
		 * Check if there are subscription with payment in pending and change the status to overdue or suspended or remove the subscription
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function ywsbs_check_overdue_subscriptions() {

			global $wpdb;

			// get all subscriptions that have status overdue and _payment_due_date < NOW+ overdue period
			$overdue_period = YITH_WC_Subscription()->overdue_time();
			$timestamp      = current_time( 'timestamp' ) - $overdue_period;
			yith_subscription_log( '===============' );
			yith_subscription_log( 'Start check overdue subscription cron at ' . current_time( 'mysql' ) );
			yith_subscription_log( 'Search subscriptions with Payment due date < ' . date( 'Y-m-d H:i:s', $timestamp ) );
			$subscriptions = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->prefix}posts as ywsbs_p
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'overdue' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='payment_due_date' AND  ywsbs_pm2.meta_value  < $timestamp )
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ",
					'ywsbs_subscription'
				)
			);

			yith_subscription_log( 'Found ' . count( $subscriptions ) . ' subscriptions' );
			if ( ! empty( $subscriptions ) ) {
				foreach ( $subscriptions as $subscription_post ) {
					$subscription = new YWSBS_Subscription( $subscription_post->ID );

					yith_subscription_log( 'Process subscription #' . $subscription->id );
					if ( YITH_WC_Subscription()->suspension_time() ) {
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
		 * @author Emanuela Castorina
		 */
		public function ywsbs_check_suspended_subscriptions() {

			global $wpdb;

			// get all subscriptions that have status active and _expired_data < NOW to cancel the subscription

			// Before cancel the suspended subscription, try to pay the renews
			$this->ywsbs_pay_renew_subscription_orders();

			$suspension_time = YITH_WC_Subscription()->suspension_time();
			$timestamp       = current_time( 'timestamp' ) - $suspension_time;

			yith_subscription_log( '===============' );
			yith_subscription_log( 'Start check suspended subscription at ' . current_time( 'mysql' ) );
			yith_subscription_log( 'Search suspended subscriptions with Payment due date < ' . date( 'Y-m-d H:i:s', $timestamp ) );

			$subscriptions = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->prefix}posts as ywsbs_p
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'suspended' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='payment_due_date' AND  ywsbs_pm2.meta_value  < $timestamp )
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC",
					'ywsbs_subscription'
				)
			);

			yith_subscription_log( 'Found ' . count( $subscriptions ) . ' subscriptions' );
			if ( ! empty( $subscriptions ) ) {
				foreach ( $subscriptions as $subscription_post ) {
					$subscription = new YWSBS_Subscription( $subscription_post->ID );
					$subscription->cancel();
					yith_subscription_log( 'Subscription  ' . $subscription->id . ' cancelled' );
					YITH_WC_Activity()->add_activity( $subscription->id, 'cancelled', 'success', $subscription->order_id, __( 'The subscription has been cancelled because the suspension period has finished.', 'yith-woocommerce-subscription' ) );
				}
			}

			yith_subscription_log( '================' );

		}

		/**
		 * Trash pending subscriptions after a specific time.
		 *
		 * @since 1.4.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function ywsbs_trash_pending_subscriptions() {
			global $wpdb;
			$trash_pending = get_option( 'ywsbs_trash_pending_subscriptions' );
			if ( ! isset( $trash_pending['number'] ) || empty( $trash_pending['number'] ) ) {
				return;
			}

			$time = strtotime( '-' . $trash_pending['number'] . ' ' . $trash_pending['unit'] );

			$subscriptions = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->prefix}posts as ywsbs_p
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'pending' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ywsbs_p.post_date < %s 
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ",
					'ywsbs_subscription',
					date( 'Y-m-d H:i:s', $time )
				)
			);

			if ( ! empty( $subscriptions ) ) {
				foreach ( $subscriptions as $subscription ) {
					$subscription_id = $subscription->ID;
					wp_trash_post( $subscription_id );
					do_action( 'ywsbs_subscription_trashed', $subscription_id );
					YITH_WC_Activity()->add_activity( $subscription_id, 'trashed', 'success', 0, __( 'The subscription was been trashed after the specific duration because was in pending status.', 'yith-woocommerce-subscription' ) );
				}
			}

		}

		/**
		 * Trash cancelled subscriptions after a specific time.
		 *
		 * @since 1.4.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function ywsbs_trash_cancelled_subscriptions() {
			global $wpdb;
			$trash_cancelled = get_option( 'ywsbs_trash_cancelled_subscriptions' );
			if ( ! isset( $trash_cancelled['number'] ) || empty( $trash_cancelled['number'] ) ) {
				return;
			}

			$time = strtotime( '-' . $trash_cancelled['number'] . ' ' . $trash_cancelled['unit'] );

			$subscriptions = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT ywsbs_p.ID FROM {$wpdb->prefix}posts as ywsbs_p
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 WHERE ( ywsbs_pm.meta_key='status' AND  ywsbs_pm.meta_value = 'cancelled' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = 'publish'
                 AND ( ywsbs_pm2.meta_key='end_date' AND  ywsbs_pm2.meta_value  < %d )
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC
                ",
					'ywsbs_subscription',
					$time
				)
			);

			if ( ! empty( $subscriptions ) ) {
				foreach ( $subscriptions as $subscription ) {
					$subscription_id = $subscription->ID;
					wp_trash_post( $subscription_id );
					do_action( 'ywsbs_subscription_trashed', $subscription_id );
					YITH_WC_Activity()->add_activity( $subscription_id, 'trashed', 'success', 0, __( 'The subscription was been trashed after the specific duration because was in cancelled status.', 'yith-woocommerce-subscription' ) );
				}
			}

		}

		public function ywsbs_pay_renew_subscription_orders() {
			global $wpdb;

			$status          = 'wc-' . YWSBS_Subscription_Order()->get_renew_order_status();
			$current_time    = current_time( 'timestamp' );
			$from            = $current_time - DAY_IN_SECONDS;
			$is_manual_renew = false;
			$messages        = array();
			yith_subscription_log( '===============', 'subscription_payment' );
			yith_subscription_log( 'Start Payment Renews cron at ' . current_time( 'mysql' ), 'subscription_payment' );
			yith_subscription_log( 'Search renew order with date < ' . date( 'Y-m-d H:i:s', $from ), 'subscription_payment' );
			$query = "SELECT ywsbs_p.ID FROM {$wpdb->prefix}posts as ywsbs_p
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 AND ( ywsbs_pm.meta_key='is_a_renew' AND  ywsbs_pm.meta_value = 'yes' )
                 AND ywsbs_p.post_type = %s
                 AND ywsbs_p.post_status = %s";

			if ( ! apply_filters( 'ywsbs_use_date_format', false ) ) {

				$query .= " AND ywsbs_p.post_date_gmt < FROM_UNIXTIME($from)";
			} else {
				$date_from = date( 'Y-m-d H:i:s', $from );
				$query    .= " AND ywsbs_p.post_date_gmt < '$date_from'";
			}

			$query .= " AND ( ywsbs_pm2.meta_key='failed_attemps' AND ywsbs_pm2.meta_value = 0 ) 
                 GROUP BY ywsbs_p.ID ORDER BY ywsbs_p.ID DESC";

			$query = $wpdb->prepare( $query, 'shop_order', $status );

			$renew_orders_for_first_time = $wpdb->get_results( $query );

			yith_subscription_log( 'Found ' . count( $renew_orders_for_first_time ) . ' new renew orders', 'subscription_payment' );
			$messages [] = 'Search orders with failed payment';

			$query = $wpdb->prepare(
				"SELECT ywsbs_p.ID FROM {$wpdb->prefix}posts as ywsbs_p
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id )
                 INNER JOIN  {$wpdb->prefix}postmeta as ywsbs_pm3 ON ( ywsbs_p.ID = ywsbs_pm3.post_id )
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

			$renew_failed_orders = $wpdb->get_results( $query );
			yith_subscription_log( 'Found ' . count( $renew_failed_orders ) . ' failed renew orders', 'subscription_payment' );
			$renew_orders = array_merge( $renew_orders_for_first_time, $renew_failed_orders );

			if ( $renew_orders ) {
				foreach ( $renew_orders as $renew_order ) {
					/**
					 * @var WC_Order $current_order
					 */
					$current_order = wc_get_order( $renew_order->ID );
					$current_order = apply_filters( 'ywsbs_check_order_before_pay_renew_order', $current_order );
					yith_subscription_log( 'Pay order #' . $current_order->get_id(), 'subscription_payment' );
					if ( ywsbs_check_renew_order_before_pay( $current_order ) && WC()->payment_gateways() ) {
						$gateway_id = $current_order->get_payment_method();
						yith_subscription_log( 'The order ' . $current_order->get_id() . ' should be pay with ' . $current_order->get_payment_method_title() . '( ' . $gateway_id . ' )', 'subscription_payment' );
						do_action( 'ywsbs_pay_renew_order_with_' . $gateway_id, $current_order, $is_manual_renew );
					}
				}
			}

			yith_subscription_log( '=======================', 'subscription_payment' );
		}
	}
}

/**
 * Unique access to instance of YWSBS_Subscription_Cron class
 *
 * @return \YWSBS_Subscription_Cron
 */
function YWSBS_Subscription_Cron() {
	return YWSBS_Subscription_Cron::get_instance();
}

