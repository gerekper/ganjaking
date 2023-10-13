<?php
/**
 * Class YITH_WCBK_Emails_Premium
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Premium
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Emails_Premium' ) ) {
	/**
	 * YITH_WCBK_Emails_Premium class.
	 */
	class YITH_WCBK_Emails_Premium extends YITH_WCBK_Emails {

		/**
		 * The constructor.
		 */
		protected function __construct() {
			parent::__construct();

			add_action( 'yith_wcbk_email_customer_notification_before_start_batch', array( $this, 'email_customer_notification_before_start_batch' ) );
			add_action( 'yith_wcbk_email_customer_notification_after_start_batch', array( $this, 'email_customer_notification_after_start_batch' ) );
			add_action( 'yith_wcbk_email_customer_notification_before_end_batch', array( $this, 'email_customer_notification_before_end_batch' ) );
			add_action( 'yith_wcbk_email_customer_notification_after_end_batch', array( $this, 'email_customer_notification_after_end_batch' ) );

			add_action( 'yith_wcbk_booking_created', array( $this, 'maybe_send_customer_email_notifications_for_booking' ) );
			add_action( 'yith_wcbk_booking_status_changed', array( $this, 'maybe_send_customer_email_notifications_on_status_change' ), 10, 4 );
		}

		/**
		 * Add email classes to WooCommerce
		 *
		 * @param array $emails Emails.
		 *
		 * @return array
		 */
		public function add_email_classes( $emails ) {
			$emails = parent::add_email_classes( $emails );

			$emails['YITH_WCBK_Email_Customer_Booking_Notification_Before_Start'] = include __DIR__ . '/emails/class-yith-wcbk-email-customer-booking-notification-before-start.php';
			$emails['YITH_WCBK_Email_Customer_Booking_Notification_After_Start']  = include __DIR__ . '/emails/class-yith-wcbk-email-customer-booking-notification-after-start.php';
			$emails['YITH_WCBK_Email_Customer_Booking_Notification_Before_End']   = include __DIR__ . '/emails/class-yith-wcbk-email-customer-booking-notification-before-end.php';
			$emails['YITH_WCBK_Email_Customer_Booking_Notification_After_End']    = include __DIR__ . '/emails/class-yith-wcbk-email-customer-booking-notification-after-end.php';

			return $emails;
		}

		/**
		 * Send email notifications to customers before start in batch.
		 */
		public function email_customer_notification_before_start_batch() {
			$next = $this->maybe_send_customer_email_notification( 'YITH_WCBK_Email_Customer_Booking_Notification_Before_Start', 'before', 'from' );
			if ( $next ) {
				WC()->queue()->schedule_single( time() + 1, 'yith_wcbk_email_customer_notification_before_start_batch', array(), 'yith_wcbk_booking_notifications' );
			}
		}

		/**
		 * Send email notifications to customers after start in batch.
		 */
		public function email_customer_notification_after_start_batch() {
			$next = $this->maybe_send_customer_email_notification( 'YITH_WCBK_Email_Customer_Booking_Notification_After_Start', 'after', 'from' );
			if ( $next ) {
				WC()->queue()->schedule_single( time() + 1, 'yith_wcbk_email_customer_notification_after_start_batch', array(), 'yith_wcbk_booking_notifications' );
			}
		}

		/**
		 * Send email notifications to customers before end in batch.
		 */
		public function email_customer_notification_before_end_batch() {
			$next = $this->maybe_send_customer_email_notification( 'YITH_WCBK_Email_Customer_Booking_Notification_Before_End', 'before', 'to' );
			if ( $next ) {
				WC()->queue()->schedule_single( time() + 1, 'yith_wcbk_email_customer_notification_before_end_batch', array(), 'yith_wcbk_booking_notifications' );
			}
		}

		/**
		 * Send email notifications to customers after end in batch.
		 */
		public function email_customer_notification_after_end_batch() {
			$next = $this->maybe_send_customer_email_notification( 'YITH_WCBK_Email_Customer_Booking_Notification_After_End', 'after', 'to' );
			if ( $next ) {
				WC()->queue()->schedule_single( time() + 1, 'yith_wcbk_email_customer_notification_after_end_batch', array(), 'yith_wcbk_booking_notifications' );
			}
		}

		/**
		 * Get the params related to a customer email notification.
		 *
		 * @param string $email_class Email class.
		 * @param string $operator    The operator (values: before, after).
		 *
		 * @return array{'status': array, 'from': int, 'to': int, 'sent_meta_key': string, 'email': YITH_WCBK_Email}|false
		 */
		protected function get_customer_email_notification_params( string $email_class, string $operator ) {
			$site_now      = yith_wcbk_get_local_timezone_timestamp();
			$sent_meta_key = strtolower( $email_class ) . '_sent';
			$mailer        = WC()->mailer();
			$emails        = $mailer->get_emails();
			$email         = $emails[ $email_class ] ?? false;

			if ( $email instanceof YITH_WCBK_Email && $email->is_enabled() ) {
				$sign           = 'before' === $operator ? '+' : '-';
				$days           = $email->get_option( 'days' );
				$booking_status = $email->get_option( 'booking_status' );

				if ( $booking_status ) {
					return array(
						'status'        => $booking_status,
						'from'          => strtotime( "{$sign}{$days} day midnight", $site_now ),
						'to'            => strtotime( "tomorrow {$sign}{$days} day midnight", $site_now ),
						'sent_meta_key' => $sent_meta_key,
						'email'         => $email,
					);
				}
			}

			return false;
		}

		/**
		 * Maybe send customer email notification.
		 *
		 * @param string $email_class Email class name.
		 * @param string $operator    Operator (values: before, after).
		 * @param string $date_key    The date key (values: from, to).
		 *
		 * @return bool True if it needs another batch operation.
		 */
		protected function maybe_send_customer_email_notification( string $email_class, string $operator, string $date_key ): bool {
			$params = $this->get_customer_email_notification_params( $email_class, $operator );
			$limit  = 20;

			if ( $params ) {
				$email    = $params['email'];
				$bookings = yith_wcbk_get_bookings(
					array(
						'status'         => $params['status'],
						'items_per_page' => $limit,
						'return'         => 'bookings',
						'data_query'     => array(
							array(
								'key'      => $date_key,
								'value'    => $params['from'],
								'operator' => '>=',
							),
							array(
								'key'      => $date_key,
								'value'    => $params['to'],
								'operator' => '<',
							),
							array(
								'key'      => $params['sent_meta_key'],
								'operator' => 'NOT EXISTS',
							),
						),
					)
				);

				if ( $bookings ) {
					foreach ( $bookings as $booking ) {
						$email->trigger( $booking->get_id() );
						$booking->update_meta_data( $params['sent_meta_key'], time() );
						$booking->save();
					}

					// Schedule next batch.
					return true;
				}
			}

			return false;
		}

		/**
		 * Maybe send emails on booking creation if timed out to send it.
		 * Example:
		 *    Today is 15th Feb; there is a notification set to be sent 2 days before the booking start date.
		 *    Someone books a product for tomorrow 16th Feb: if we'd wait for the cron, the email will never be sent,
		 *    since it should be sent on 14th Feb. On the contrary, we sent the email soon.
		 *
		 * @param YITH_WCBK_Booking $booking The booking.
		 *
		 * @return void
		 */
		public function maybe_send_customer_email_notifications_for_booking( $booking ) {
			$notifications = array(
				array(
					'email_class' => 'YITH_WCBK_Email_Customer_Booking_Notification_Before_Start',
					'operator'    => 'before',
					'date_key'    => 'from',
				),
				array(
					'email_class' => 'YITH_WCBK_Email_Customer_Booking_Notification_After_Start',
					'operator'    => 'after',
					'date_key'    => 'from',
				),
				array(
					'email_class' => 'YITH_WCBK_Email_Customer_Booking_Notification_Before_End',
					'operator'    => 'before',
					'date_key'    => 'to',
				),
				array(
					'email_class' => 'YITH_WCBK_Email_Customer_Booking_Notification_After_End',
					'operator'    => 'after',
					'date_key'    => 'to',
				),
			);

			foreach ( $notifications as $notification ) {
				$params = $this->get_customer_email_notification_params( $notification['email_class'], $notification['operator'] );
				$getter = 'get_' . $notification['date_key'];

				if (
					! ! $params &&
					! $booking->get_meta( $params['sent_meta_key'] ) &&
					is_callable( array( $booking, $getter ) ) &&
					$booking->$getter() < $params['from'] &&
					$booking->has_status( $params['status'] )
				) {
					$params['email']->trigger( $booking->get_id() );
					$booking->update_meta_data( $params['sent_meta_key'], time() );
					$booking->save();
				}
			}
		}

		/**
		 * Maybe send emails on booking status change.
		 *
		 * @param int               $booking_id  The booking ID.
		 * @param string            $status_from The old status.
		 * @param string            $status_to   The new status.
		 * @param YITH_WCBK_Booking $booking     The booking.
		 *
		 * @return void
		 */
		public function maybe_send_customer_email_notifications_on_status_change( $booking_id, $status_from, $status_to, $booking ) {
			$this->maybe_send_customer_email_notifications_for_booking( $booking );
		}
	}
}
