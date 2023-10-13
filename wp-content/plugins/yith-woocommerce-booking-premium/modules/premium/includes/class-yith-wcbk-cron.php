<?php
/**
 * Cron class.
 * handle Cron processes.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Premium
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Cron' ) ) {
	/**
	 * Class YITH_WCBK_Cron
	 *
	 * @since  2.0.0
	 */
	class YITH_WCBK_Cron {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * The constructor.
		 */
		protected function __construct() {
			add_action( 'yith_wcbk_check_reject_pending_confirmation_bookings', array( $this, 'check_reject_pending_confirmation_bookings' ) );
			add_action( 'yith_wcbk_check_complete_paid_bookings', array( $this, 'check_complete_paid_bookings' ) );
			add_action( 'yith_wcbk_schedule_booking_notifications', array( $this, 'schedule_booking_notifications' ) );

			add_action( 'wp_loaded', array( $this, 'schedule_actions' ), 30 );
		}

		/**
		 * Schedule actions through the WooCommerce Action Scheduler.
		 */
		public function schedule_actions() {
			$gmt_hours_offset  = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
			$site_tomorrow_utc = strtotime( 'midnight', strtotime( 'tomorrow' ) + $gmt_hours_offset ) - $gmt_hours_offset;

			if ( ! WC()->queue()->get_next( 'yith_wcbk_check_reject_pending_confirmation_bookings' ) ) {
				WC()->queue()->schedule_single( strtotime( 'tomorrow midnight' ), 'yith_wcbk_check_reject_pending_confirmation_bookings', array(), 'yith-booking' );
			}

			if ( ! WC()->queue()->get_next( 'yith_wcbk_check_complete_paid_bookings' ) ) {
				WC()->queue()->schedule_single( strtotime( 'tomorrow midnight' ), 'yith_wcbk_check_complete_paid_bookings', array(), 'yith-booking' );
			}

			if ( ! WC()->queue()->get_next( 'yith_wcbk_schedule_booking_notifications' ) ) {
				WC()->queue()->schedule_single( $site_tomorrow_utc, 'yith_wcbk_schedule_booking_notifications', array(), 'yith-booking' );
			}
		}

		/**
		 * Check if reject pending confirmation bookings
		 */
		public function check_reject_pending_confirmation_bookings() {
			// TODO: the check should be made in batches of XX bookings (for example, by updating 20 bookings at time).
			$enabled = yith_wcbk()->settings->get_reject_pending_confirmation_bookings_enabled();
			$after   = yith_wcbk()->settings->get_reject_pending_confirmation_bookings_after();
			if ( $enabled && $after ) {
				$after_day = $after - 1;

				$args = array(
					'post_status' => array( 'bk-pending-confirm' ),
					'date_query'  => array(
						array(
							'before' => gmdate( 'Y-m-d H:i:s', strtotime( "now -$after_day day midnight" ) ),
						),
					),
				);

				$booking_ids = yith_wcbk_get_booking_post_ids( $args );
				$bookings    = array_filter( array_map( 'yith_get_booking', $booking_ids ) );

				if ( ! ! $bookings ) {
					foreach ( $bookings as $booking ) {
						$booking->update_status(
							'unconfirmed',
							sprintf(
							// translators: %s is the number of days.
								__( 'Automatically reject booking after %d day(s) from creating', 'yith-booking-for-woocommerce' ),
								$after
							)
						);
					}
				}
			}
		}

		/**
		 * Check if reject pending confirmation bookings
		 */
		public function check_complete_paid_bookings() {
			// TODO: the check should be made in batches of XX bookings (for example, by updating 20 bookings at time).
			if ( yith_wcbk()->settings->get_complete_paid_bookings_enabled() ) {
				$after     = yith_wcbk()->settings->get_complete_paid_bookings_after();
				$after_day = $after - 1;
				$sign      = $after_day < 0 ? '+' : '-';

				$bookings = yith_wcbk_get_bookings(
					array(
						'status'     => 'paid',
						'return'     => 'bookings',
						'data_query' => array(
							array(
								'key'      => 'to',
								'value'    => strtotime( "now {$sign}{$after_day} day midnight" ),
								'operator' => '<',
							),
						),
					)
				);

				if ( ! ! $bookings ) {
					foreach ( $bookings as $booking ) {
						if ( $booking instanceof YITH_WCBK_Booking ) {
							$booking->update_status(
								'completed',
								sprintf(
								// translators: %s is the number of days.
									__( 'Automatically complete booking after %d day(s) from End Date', 'yith-booking-for-woocommerce' ),
									$after
								)
							);
						}
					}
				}
			}
		}

		/**
		 * Schedule booking notifications.
		 */
		public function schedule_booking_notifications() {
			$email_actions = array(
				'YITH_WCBK_Email_Customer_Booking_Notification_Before_Start' => 'yith_wcbk_email_customer_notification_before_start_batch',
				'YITH_WCBK_Email_Customer_Booking_Notification_After_Start'  => 'yith_wcbk_email_customer_notification_after_start_batch',
				'YITH_WCBK_Email_Customer_Booking_Notification_Before_End'   => 'yith_wcbk_email_customer_notification_before_end_batch',
				'YITH_WCBK_Email_Customer_Booking_Notification_After_End'    => 'yith_wcbk_email_customer_notification_after_end_batch',
			);

			$mailer = WC()->mailer();
			$emails = $mailer->get_emails();
			$index  = 1;

			foreach ( $email_actions as $email_class => $action ) {
				$email = $emails[ $email_class ] ?? false;
				if ( $email && $email->is_enabled() ) {
					WC()->queue()->schedule_single( time() + ( $index * MINUTE_IN_SECONDS ), $action, array(), 'yith_wcbk_booking_notifications' );
				}
				$index ++;
			}
		}
	}
}
