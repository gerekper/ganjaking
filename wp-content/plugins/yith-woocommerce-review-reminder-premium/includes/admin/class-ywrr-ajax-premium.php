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

if ( ! class_exists( 'YWRR_Ajax_Premium' ) ) {

	/**
	 * Implements AJAX for YWRR plugin
	 *
	 * @class   YWRR_Ajax_Premium
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWRR_Ajax_Premium {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			add_action( 'wp_ajax_ywrr_send_request_mail', array( $this, 'send_request_mail' ) );
			add_action( 'wp_ajax_ywrr_reschedule_mail', array( $this, 'reschedule_mail' ) );
			add_action( 'wp_ajax_ywrr_cancel_mail', array( $this, 'cancel_mail' ) );
			add_action( 'wp_ajax_ywrr_mass_schedule', array( $this, 'mass_schedule' ) );
			add_action( 'wp_ajax_ywrr_mass_unschedule', array( $this, 'mass_unschedule' ) );
			add_action( 'wp_ajax_ywrr_clear_sent', array( $this, 'clear_sent' ) );
			add_action( 'wp_ajax_ywrr_clear_cancelled', array( $this, 'clear_cancelled' ) );

		}

		/**
		 * Send a request mail from order details page
		 *
		 * @return  void
		 * @throws  Exception
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function send_request_mail() {

			$order           = wc_get_order( $_POST['order_id'] );
			$booking_id      = $_POST['booking_id'];
			$items_to_review = json_decode( sanitize_text_field( stripslashes( $_POST['items_to_review'] ) ), true );
			$today           = new DateTime( current_time( 'mysql' ) );
			$pay_date        = new DateTime( date( 'Y-m-d H:i:s', $_POST['order_date'] ) );
			$days            = $pay_date->diff( $today );

			try {
				$type         = ( $booking_id == '' || $booking_id == 0 ) ? 'order' : 'booking';
				$email_result = ywrr_send_email( $order->get_id(), $days->days, $items_to_review, array(), $type );

				if ( $email_result !== true ) {
					wp_send_json( array( 'error' => esc_html__( 'There was an error while sending the email', 'yith-woocommerce-review-reminder' ) ) );
				} else {

					if ( ywrr_check_exists_schedule( $order->get_id(), $booking_id ) != 0 ) {
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
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function reschedule_mail() {

			$order_id        = $_POST['order_id'];
			$booking_id      = $_POST['booking_id'];
			$scheduled_date  = $_POST['schedule_date'];
			$items_to_review = json_decode( sanitize_text_field( stripslashes( $_POST['items_to_review'] ) ), true );
			$list            = '';

			try {

				if ( ! empty( $items_to_review ) ) {

					$list        = array();
					$order       = wc_get_order( $order_id );
					$is_funds    = $order->get_meta( '_order_has_deposit' ) == 'yes';
					$is_deposits = $order->get_created_via() == 'yith_wcdp_balance_order';
					//APPLY_FILTER: ywrr_skip_renewal_orders: check if plugin should skip subscription renewal orders
					$is_renew = $order->get_meta( 'is_a_renew' ) == 'yes' && apply_filters( 'ywrr_skip_renewal_orders', true );

					if ( ! $is_funds && ! $is_deposits && ! $is_renew ) {
						$list = ywrr_get_review_list_forced( $items_to_review, $order_id );
					}

				}

				if ( ywrr_check_exists_schedule( $order_id, $booking_id ) != 0 ) {
					$message = ywrr_reschedule( $order_id, $scheduled_date, $list );
				} else {
					if ( $booking_id != '' && $booking_id != 0 ) {
						$message = ywrr_schedule_booking_mail( $booking_id, $scheduled_date );
					} else {
						$message = ywrr_schedule_mail( $order_id, $list );
					}
				}

				if ( $message != '' ) {
					throw new Exception( $message );
				}

				global $wpdb;

				$schedule = $wpdb->get_var( $wpdb->prepare( "
								SELECT  scheduled_date
								FROM    {$wpdb->prefix}ywrr_email_schedule
								WHERE	order_id = %d
								AND		mail_status = 'pending'
								", $order_id ) );

				wp_send_json( array( 'success' => true, 'schedule' => sprintf( esc_html__( 'The request will be sent on %s', 'yith-woocommerce-review-reminder' ), date_i18n( get_option( 'date_format' ), strtotime( $schedule ) ) ) ) );

			} catch ( Exception $e ) {
				wp_send_json( array( 'error' => $e->getMessage() ) );
			}

		}

		/**
		 * Cancel schedule mail from order details page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function cancel_mail() {

			$order_id   = $_POST['order_id'];
			$booking_id = $_POST['booking_id'];

			try {

				if ( ywrr_check_exists_schedule( $order_id, $booking_id ) != 0 ) {
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
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function mass_schedule() {

			try {

				global $wpdb;

				//Get the list of already scheduled orders
				$scheduled_list = $wpdb->get_col( "
                    SELECT    order_id
                    FROM      {$wpdb->prefix}ywrr_email_schedule
                    " );

				//Get never scheduled orders
				$args = array(
					'post_type'      => 'shop_order',
					'post__not_in'   => $scheduled_list,
					'post_parent'    => 0,
					'post_status'    => array( 'wc-completed' ),
					'posts_per_page' => - 1,

				);

				$query = new WP_Query( $args );
				$count = 0;

				if ( $query->have_posts() ) {

					while ( $query->have_posts() ) {

						$query->the_post();

						$order          = wc_get_order( $query->post->ID );
						$customer_id    = $order->get_user_id();
						$customer_email = $order->get_billing_email();

						if ( ywrr_check_blocklist( $customer_id, $customer_email ) == true ) {

							if ( '' == ywrr_schedule_mail( $query->post->ID ) ) {
								$count ++;
							};

						}

					}

					wp_send_json( array( 'success' => true, 'message' => sprintf( _n( '1 scheduled order', '%s scheduled orders', $count, 'yith-woocommerce-review-reminder' ), $count ) ) );

				} else {

					wp_send_json( array( 'success' => true, 'message' => esc_html__( 'No scheduled order', 'yith-woocommerce-review-reminder' ) ) );

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
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function mass_unschedule() {

			try {

				global $wpdb;

				$scheduled_list = $wpdb->get_results( "
									SELECT  order_id,
									 		mail_type
									FROM    {$wpdb->prefix}ywrr_email_schedule
									WHERE	mail_status = 'pending'
									" );

				if ( $scheduled_list ) {

					foreach ( $scheduled_list as $schedule ) {
						$booking_id = $schedule->mail_type == 'order' ? '' : str_replace( 'booking-', '', $schedule->mail_type );
						ywrr_change_schedule_status( $schedule->order_id, 'cancelled', $booking_id );
					}

					wp_send_json( array( 'success' => true, 'message' => sprintf( _n( '1 unscheduled order', '%s unscheduled orders', count( $scheduled_list ), 'yith-woocommerce-review-reminder' ), count( $scheduled_list ) ) ) );

				} else {

					wp_send_json( array( 'success' => true, 'message' => esc_html__( 'No scheduled order', 'yith-woocommerce-review-reminder' ) ) );

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
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function clear_sent() {

			try {

				global $wpdb;


				$deleted = $wpdb->delete(
					$wpdb->prefix . 'ywrr_email_schedule',
					array( 'mail_status' => 'sent' ),
					array( '%s' )
				);

				if ( $deleted > 0 ) {

					wp_send_json( array( 'success' => true, 'message' => sprintf( _n( '1 deleted item', '%s deleted items', $deleted, 'yith-woocommerce-review-reminder' ), $deleted ) ) );

				} else {

					wp_send_json( array( 'success' => true, 'message' => esc_html__( 'No items deleted', 'yith-woocommerce-review-reminder' ) ) );

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
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function clear_cancelled() {

			try {

				global $wpdb;

				$deleted = $wpdb->delete(
					$wpdb->prefix . 'ywrr_email_schedule',
					array( 'mail_status' => 'cancelled' ),
					array( '%s' )
				);

				if ( $deleted > 0 ) {

					wp_send_json( array( 'success' => true, 'message' => sprintf( _n( '1  deleted item', '%s  deleted items', $deleted, 'yith-woocommerce-review-reminder' ), $deleted ) ) );

				} else {

					wp_send_json( array( 'success' => true, 'message' => esc_html__( 'No items deleted', 'yith-woocommerce-review-reminder' ) ) );

				}


			} catch ( Exception $e ) {

				wp_send_json( array( 'message' => $e->getMessage() ) );

			}

		}

	}

	new YWRR_Ajax_Premium();

}

