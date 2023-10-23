<?php
/**
 * AJAX class
 *
 * @package YITH\ReviewReminder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YWRR_Ajax' ) ) {

	/**
	 * Implements AJAX for YWRR plugin
	 *
	 * @class   YWRR_Ajax
	 * @since   1.1.5
	 * @author  YITH <plugins@yithemes.com>
	 *
	 * @package YITH
	 */
	class YWRR_Ajax {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.1.5
		 */
		public function __construct() {

			add_action( 'wp_ajax_ywrr_send_test_mail', array( $this, 'send_test_mail' ) );
			add_action( 'wp_ajax_ywrr_add_to_blocklist', array( $this, 'add_to_blocklist_admin' ) );
			add_action( 'wp_ajax_ywrr_unsubscribe', array( $this, 'add_to_blocklist' ) );
			add_action( 'wp_ajax_nopriv_ywrr_unsubscribe', array( $this, 'add_to_blocklist' ) );
			add_action( 'wp_ajax_ywrr_send_request_mail', array( $this, 'send_request_mail' ) );
			add_action( 'wp_ajax_ywrr_reschedule_mail', array( $this, 'reschedule_mail' ) );
			add_action( 'wp_ajax_ywrr_cancel_mail', array( $this, 'cancel_mail' ) );
			add_action( 'wp_ajax_ywrr_mass_schedule', array( $this, 'mass_schedule' ) );
			add_action( 'wp_ajax_ywrr_mass_unschedule', array( $this, 'mass_unschedule' ) );
			add_action( 'wp_ajax_ywrr_clear_sent', array( $this, 'clear_sent' ) );
			add_action( 'wp_ajax_ywrr_clear_cancelled', array( $this, 'clear_cancelled' ) );

		}

		/**
		 * Send a test mail from option panel
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function send_test_mail() {

			$total_products = wp_count_posts( 'product' );

			if ( ! $total_products->publish ) {

				wp_send_json( array( 'error' => esc_html__( 'In order to send the test email, at least one product has to be published', 'yith-woocommerce-review-reminder' ) ) );

			} else {

				$args            = array(
					'posts_per_page' => 2,
					'orderby'        => 'rand',
					'post_type'      => 'product',
				);
				$random_products = get_posts( $args );
				$test_items      = array();

				foreach ( $random_products as $item ) {
					$test_items[ $item->ID ]['id']   = $item->ID;
					$test_items[ $item->ID ]['name'] = $item->post_title;
				}

				$days       = get_option( 'ywrr_mail_schedule_day' );
				$posted     = $_POST; //phpcs:ignore WordPress.Security.NonceVerification.Missing
				$test_email = $posted['email'];
				$template   = $posted['template'];

				try {
					$mail_args   = array(
						'order_id'   => 0,
						'item_list'  => $test_items,
						'days_ago'   => $days,
						'test_email' => $test_email,
						'template'   => $template,
						'type'       => 'order',
					);
					$mail_result = apply_filters( 'send_ywrr_mail', $mail_args );

					if ( ! $mail_result ) {
						wp_send_json( array( 'error' => esc_html__( 'There was an error while sending the email', 'yith-woocommerce-review-reminder' ) ) );
					} else {
						wp_send_json(
							array(
								'success' => true,
								'message' => esc_html__( 'Test email has been sent successfully!', 'yith-woocommerce-review-reminder' ),
							)
						);
					}
				} catch ( Exception $e ) {
					wp_send_json( array( 'error' => $e->getMessage() ) );
				}
			}

		}

		/**
		 * Handles the unsubscribe form on frontend
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function add_to_blocklist() {

			$response       = array(
				'status' => 'failure',
			);
			$posted         = $_POST; //phpcs:ignore WordPress.Security.NonceVerification.Missing
			$customer_id    = ! empty( $posted['user_id'] ) ? $posted['user_id'] : 0;
			$customer_email = ! empty( $posted['email'] ) ? sanitize_email( $posted['email'] ) : '';
			$decoded_email  = urldecode( base64_decode( $posted['email_hash'] ) ); ///phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode

			if ( empty( $customer_email ) || ! is_email( $customer_email ) ) {
				wc_add_notice( esc_html__( 'Please provide a valid email address.', 'yith-woocommerce-review-reminder' ), 'error' );
			} elseif ( $decoded_email !== $customer_email ) {
				wc_add_notice( esc_html__( 'Please retype the email address as provided.', 'yith-woocommerce-review-reminder' ), 'error' );
			} else {
				if ( true === ywrr_check_blocklist( $customer_id, $customer_email ) ) {
					try {
						ywrr_add_to_blocklist( $customer_id, $customer_email );
						wc_add_notice( esc_html__( 'Unsubscribe was successful.', 'yith-woocommerce-review-reminder' ) );
						$response['status'] = 'success';
					} catch ( Exception $e ) {
						wc_add_notice( esc_html__( 'An error has occurred', 'yith-woocommerce-review-reminder' ), 'error' );
					}
				} else {
					wc_add_notice( esc_html__( 'You have already unsubscribed', 'yith-woocommerce-review-reminder' ), 'error' );
				}
			}

			ob_start();
			wc_print_notices();
			$response['messages'] = ob_get_clean();

			echo '<!--WC_START-->' . wp_json_encode( $response ) . '<!--WC_END-->';

			exit;

		}

		/**
		 * Handles the blocklist on backend
		 *
		 * @return  void
		 * @since   1.6.0
		 */
		public function add_to_blocklist_admin() {
			$posted         = $_POST; //phpcs:ignore WordPress.Security.NonceVerification.Missing
			$user           = get_user_by( 'email', $posted['email'] );
			$customer_id    = ( ! $user ? 0 : $user->ID );
			$customer_email = $posted['email'];

			if ( true === ywrr_check_blocklist( $customer_id, $customer_email ) ) {

				try {
					ywrr_add_to_blocklist( $customer_id, $customer_email );
					wp_send_json(
						array(
							'success' => true,
							/* translators: %s user email */
							'message' => sprintf( esc_html__( 'User %s added successfully', 'yith-woocommerce-review-reminder' ), '<b>' . $customer_email . '</b>' ),
						)
					);
				} catch ( Exception $e ) {
					wp_send_json( array( 'error' => $e->getMessage() ) );
				}
			} else {
				wp_send_json(
					array(
						'error'   => true,
						/* translators: %s user email */
						'message' => sprintf( esc_html__( 'User %s already unsubscribed', 'yith-woocommerce-review-reminder' ), '<b>' . $customer_email . '</b>' ),
					)
				);
			}

		}

		/**
		 * Send a request mail from order details page
		 *
		 * @return  void
		 * @throws  Exception An exception.
		 * @since   1.0.0
		 */
		public function send_request_mail() {

			$posted          = $_POST; //phpcs:ignore WordPress.Security.NonceVerification.Missing
			$order           = wc_get_order( $posted['order_id'] );
			$booking_id      = $posted['booking_id'];
			$items_to_review = json_decode( sanitize_text_field( stripslashes( $posted['items_to_review'] ) ), true );
			$today           = new DateTime( current_time( 'mysql' ) );
			$pay_date        = new DateTime( gmdate( 'Y-m-d H:i:s', $posted['order_date'] ) );
			$days            = $pay_date->diff( $today );

			try {
				$type         = ( '' === (string) $booking_id || 0 === (int) $booking_id ) ? 'order' : 'booking';
				$email_result = ywrr_send_email( $order->get_id(), $days->days, $items_to_review, array(), $type );

				if ( true !== $email_result ) {
					wp_send_json( array( 'error' => esc_html__( 'There was an error while sending the email', 'yith-woocommerce-review-reminder' ) ) );
				} else {
					if ( (int) ywrr_check_exists_schedule( $order->get_id(), $booking_id ) !== 0 ) {
						ywrr_change_schedule_status( $order->get_id(), 'sent', $booking_id );
					} else {
						ywrr_log_unscheduled_email( $order, $booking_id, ywrr_get_review_list_forced( $items_to_review, $order->get_id() ) );
					}

					wp_send_json( array( 'success' => true ) );
				}
			} catch ( Exception $e ) {
				wp_send_json( array( 'error' => $e->getMessage() ) );
			}

		}

		/**
		 * Reschedule mail from order details page
		 *
		 * @return  void
		 * @throws  Exception An exception.
		 * @since   1.0.0
		 */
		public function reschedule_mail() {

			$posted          = $_POST; //phpcs:ignore WordPress.Security.NonceVerification.Missing
			$order_id        = $posted['order_id'];
			$booking_id      = $posted['booking_id'];
			$scheduled_date  = $posted['schedule_date'];
			$items_to_review = json_decode( sanitize_text_field( stripslashes( $posted['items_to_review'] ) ), true );
			$list            = '';

			try {

				if ( ! empty( $items_to_review ) ) {
					$list        = array();
					$order       = wc_get_order( $order_id );
					$is_funds    = $order->get_meta( '_order_has_deposit' ) === 'yes';
					$is_deposits = $order->get_created_via() === 'yith_wcdp_balance_order';
					/**
					 * APPLY_FILTERS: ywrr_skip_renewal_orders
					 *
					 * Check if plugin should skip subscription renewal orders.
					 *
					 * @param boolean $value Value to check if renewals should be skipped.
					 *
					 * @return boolean
					 */
					$is_renew = $order->get_meta( 'is_a_renew' ) === 'yes' && apply_filters( 'ywrr_skip_renewal_orders', true );
					if ( ! $is_funds && ! $is_deposits && ! $is_renew ) {
						$list = ywrr_get_review_list_forced( $items_to_review, $order_id );
					}
				}

				if ( (int) ywrr_check_exists_schedule( $order_id, $booking_id ) !== 0 ) {
					$message = ywrr_reschedule( $order_id, $scheduled_date, $list );
				} else {
					if ( '' !== (string) $booking_id && 0 !== (int) $booking_id ) {
						$message = ywrr_schedule_booking_mail( $booking_id, $scheduled_date );
					} else {
						$message = ywrr_schedule_mail( $order_id, $list );
					}
				}

				if ( '' !== $message ) {
					throw new Exception( $message );
				}

				global $wpdb;
				//phpcs:ignore
				$schedule = $wpdb->get_var(
					$wpdb->prepare(
						"
								SELECT  scheduled_date
								FROM    {$wpdb->prefix}ywrr_email_schedule
								WHERE	order_id = %d AND mail_status = 'pending'
								",
						$order_id
					)
				);

				wp_send_json(
					array(
						'success'  => true,
						/* translators: %s send date */
						'schedule' => sprintf( esc_html__( 'The request will be sent on %s', 'yith-woocommerce-review-reminder' ), date_i18n( get_option( 'date_format' ), strtotime( $schedule ) ) ),
					)
				);

			} catch ( Exception $e ) {
				wp_send_json( array( 'error' => $e->getMessage() ) );
			}

		}

		/**
		 * Cancel schedule mail from order details page
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function cancel_mail() {

			$posted     = $_POST; //phpcs:ignore WordPress.Security.NonceVerification.Missing
			$order_id   = $posted['order_id'];
			$booking_id = $posted['booking_id'];

			try {
				if ( (int) ywrr_check_exists_schedule( $order_id, $booking_id ) !== 0 ) {
					ywrr_change_schedule_status( $order_id, 'cancelled', $booking_id );
					wp_send_json( true );
				} else {
					wp_send_json( 'notfound' );
				}
			} catch ( Exception $e ) {
				wp_send_json( array( 'error' => $e->getMessage() ) );
			}

		}

		/**
		 * Mass schedule mail from options panel
		 *
		 * @return  void
		 * @since   1.2.3
		 */
		public function mass_schedule() {

			try {

				global $wpdb;

				// Get the list of already scheduled orders.
				$scheduled_list = $wpdb->get_col( //phpcs:ignore
					"
                    SELECT    order_id
                    FROM      {$wpdb->prefix}ywrr_email_schedule
                    "
				);

				// Get never scheduled orders.
				$orders = wc_get_orders(
					array(
						'status'  => array( 'wc-completed' ),
						'type'    => 'shop_order',
						'exclude' => $scheduled_list,
						'parent'  => 0,
						'limit'   => -1,
					)
				);

				$count = 0;

				if ( count( $orders ) > 0 ) {

					foreach ( $orders as $order ) {

						$customer_id    = $order->get_user_id();
						$customer_email = $order->get_billing_email();

						if ( ywrr_check_blocklist( $customer_id, $customer_email ) === true ) {
							if ( '' === ywrr_schedule_mail( $order->get_id() ) ) {
								$count++;
							};
						}
					}

					wp_send_json(
						array(
							'success' => true,
							/* translators: %s number of scheduled orders */
							'message' => sprintf( _n( '%s scheduled order', '%s scheduled orders', $count, 'yith-woocommerce-review-reminder' ), $count ),
						)
					);

				} else {
					wp_send_json(
						array(
							'success' => true,
							'message' => esc_html__( 'No scheduled order', 'yith-woocommerce-review-reminder' ),
						)
					);
				}
			} catch ( Exception $e ) {
				wp_send_json( array( 'message' => $e->getMessage() ) );
			}

		}

		/**
		 * Mass unschedule mail from options panel
		 *
		 * @return  void
		 * @since   1.3.5
		 */
		public function mass_unschedule() {

			try {

				global $wpdb;

				$scheduled_list = $wpdb->get_results( //phpcs:ignore
					"
							SELECT  order_id,
							 		mail_type
							FROM    {$wpdb->prefix}ywrr_email_schedule
							WHERE	mail_status = 'pending'
							"
				);

				if ( $scheduled_list ) {

					foreach ( $scheduled_list as $schedule ) {
						$booking_id = 'order' === $schedule->mail_type ? '' : str_replace( 'booking-', '', $schedule->mail_type );
						ywrr_change_schedule_status( $schedule->order_id, 'cancelled', $booking_id );
					}

					wp_send_json(
						array(
							'success' => true,
							/* translators: %s number of orders */
							'message' => sprintf( _n( '%s unscheduled order', '%s unscheduled orders', count( $scheduled_list ), 'yith-woocommerce-review-reminder' ), count( $scheduled_list ) ),
						)
					);

				} else {
					wp_send_json(
						array(
							'success' => true,
							'message' => esc_html__( 'No scheduled order', 'yith-woocommerce-review-reminder' ),
						)
					);
				}
			} catch ( Exception $e ) {
				wp_send_json( array( 'message' => $e->getMessage() ) );
			}

		}

		/**
		 * Mass clear sent mail from options panel
		 *
		 * @return  void
		 * @since   1.3.2
		 */
		public function clear_sent() {

			try {
				global $wpdb;

				$deleted = $wpdb->delete( //phpcs:ignore
					$wpdb->prefix . 'ywrr_email_schedule',
					array( 'mail_status' => 'sent' ),
					array( '%s' )
				);

				if ( $deleted > 0 ) {

					wp_send_json(
						array(
							'success' => true,
							/* translators: %s number of items */
							'message' => sprintf( _n( '%s deleted item', '%s deleted items', $deleted, 'yith-woocommerce-review-reminder' ), $deleted ),
						)
					);

				} else {
					wp_send_json(
						array(
							'success' => true,
							'message' => esc_html__( 'No items deleted', 'yith-woocommerce-review-reminder' ),
						)
					);
				}
			} catch ( Exception $e ) {
				wp_send_json( array( 'message' => $e->getMessage() ) );
			}

		}

		/**
		 * Mass clear cancelled mail from options panel
		 *
		 * @return  void
		 * @since   1.3.5
		 */
		public function clear_cancelled() {

			try {

				global $wpdb;

				$deleted = $wpdb->delete( //phpcs:ignore
					$wpdb->prefix . 'ywrr_email_schedule',
					array( 'mail_status' => 'cancelled' ),
					array( '%s' )
				);

				if ( $deleted > 0 ) {

					wp_send_json(
						array(
							'success' => true,
							/* translators: %s number of items */
							'message' => sprintf( _n( '%s deleted item', '%s deleted items', $deleted, 'yith-woocommerce-review-reminder' ), $deleted ),
						)
					);

				} else {
					wp_send_json(
						array(
							'success' => true,
							'message' => esc_html__( 'No items deleted', 'yith-woocommerce-review-reminder' ),
						)
					);
				}
			} catch ( Exception $e ) {
				wp_send_json( array( 'message' => $e->getMessage() ) );
			}

		}

	}

	new YWRR_Ajax();

}
