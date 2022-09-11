<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YWSBS_Subscription_Delivery_Schedules Object.
 *
 * @class   YWSBS_Subscription_Delivery_Schedules
 * @package YITH WooCommerce Subscription
 * @since   2.2.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'YWSBS_Subscription_Delivery_Schedules' ) ) {

	/**
	 * Class YWSBS_Subscription_Delivery_Schedules
	 */
	class YWSBS_Subscription_Delivery_Schedules {


		/**
		 * Delivery schedules table name.
		 *
		 * @var string
		 */
		public $table_name = 'yith_ywsbs_delivery_schedules';

		/**
		 * Single instance of the class
		 *
		 * @var YWSBS_Subscription_Delivery_Schedules
		 */
		protected static $instance;

		/**
		 * Time of the day when the synchronization should be scheduled.
		 * Usually when the site has lower traffic.
		 *
		 * @var int
		 */
		protected $time_of_day = 0;


		/**
		 * Returns single instance of the class
		 *
		 * @return YWSBS_Subscription_Delivery_Schedules
		 * @since  2.2.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * Initialize the YWSBS_Subscription_Delivery_Schedules Object
		 *
		 * @since 2.2.0
		 */
		public function __construct() {

			global $wpdb;
			$this->table_name = $wpdb->prefix . $this->table_name;

			$this->time_of_day = apply_filters( 'ywsbs_delivery_schedules_time_of_day', 4 );

			add_action( 'ywsbs_subscription_started', array( $this, 'set_delivery_schedules' ), 10, 1 );
			add_action( 'ywsbs_subscription_updated', array( $this, 'set_delivery_schedules' ), 10, 1 );
			add_action( 'ywsbs_subscription_status_cancelled', array( $this, 'update_delivery_for_cancelled_subscription' ), 10, 1 );
			add_action( 'ywsbs_subscription_status_resume', array( $this, 'update_delivery_for_resumed_subscription' ), 10, 1 );

			add_action( 'ywsbs_scheduled_data_updated', array( $this, 'update_delivery_for_change_payment_due_date' ), 10, 4 );
			add_action( 'ywsbs_delivery_schedules_status_change', array( $this, 'set_status_to_delivery_schedules' ) );

			add_action( 'deleted_post', array( $this, 'maybe_delete_delivery_status' ) );

		}


		/**
		 * Return the delivery settings to store inside the subscription meta.
		 *
		 * @param WC_Product $product Subscription product.
		 *
		 * @return array
		 */
		public function get_delivery_settings( $product ) {
			$override = $product->get_meta( '_ywsbs_override_delivery_schedule' );

			return ( 'yes' === $override ) ? $product->get_meta( '_ywsbs_delivery_synch' ) : $this->get_general_delivery_options();
		}

		/**
		 * Check if the product has a delivery scheduled.
		 *
		 * @param WC_Product $product Subscription product.
		 * @return bool
		 */
		public function has_delivery_scheduled( $product ) {
			$enabled_delivery = get_option( 'ywsbs_enable_delivery', 'no' );
			$result           = false;
			switch ( $enabled_delivery ) {
				case 'no':
					$result = false;
					break;
				case 'all_products':
					$exclude_products_category = get_option( 'ywsbs_delivery_exclude_category_and_product', 'no' );
					if ( 'yes' === $exclude_products_category ) {
						$excluded_products = (array) get_option( 'ywsbs_delivery_exclude_products_all_products', array() );
						$result            = ! in_array( $product->get_id(), $excluded_products ); //phpcs:ignore

						if ( $result ) {
							$excluded_categories = (array) get_option( 'ywsbs_delivery_exclude_categories_all_products', array() );
							$result              = ! ywsbs_check_categories( $product, $excluded_categories );
						}
					} else {
						$result = true;
					}
					break;
				case 'physical':
					$result = ! $product->is_virtual();

					if ( $result && 'yes' === get_option( 'ywsbs_delivery_exclude_category_and_product_non_virtual', 'yes' ) ) {
						$excluded_products = (array) get_option( 'ywsbs_delivery_exclude_products_physical', array() );
						$result            = ! in_array( $product->get_id(), $excluded_products ); //phpcs:ignore

						if ( $result ) {
							$excluded_categories = (array) get_option( 'ywsbs_delivery_exclude_categories_physical', array() );
							$result              = ! ywsbs_check_categories( $product, $excluded_categories );
						}
					}

					break;
				case 'products':
					$included = (array) get_option( 'ywsbs_delivery_include_product', array() );
					$result   = in_array( $product->get_id(), $included ); //phpcs:ignore
					break;
				case 'categories':
					$categories       = (array) get_option( 'ywsbs_delivery_include_categories', array() );
					$result           = ywsbs_check_categories( $product, $categories );
					$exclude_products = get_option( 'ywsbs_delivery_include_categories_enable_exclude_products', 'no' );
					if ( $result && 'yes' === $exclude_products ) {
						$excluded_products = (array) get_option( 'ywsbs_delivery_exclude_products_from_categories', array() );
						$result            = ! in_array( $product->get_id(), $excluded_products ); //phpcs:ignore
					}
			}

			return apply_filters( 'ywsbs_has_delivery_scheduled', $result, $product );
		}


		/**
		 * Return general delivery options.
		 *
		 * @return array
		 */
		public function get_general_delivery_options() {
			$delivery_default_schedule  = get_option(
				'ywsbs_delivery_default_schedule',
				array(
					'delivery_gap'    => 1,
					'delivery_period' => 'months',
				)
			);
			$delivery_default_schedule2 = get_option(
				'ywsbs_delivery_default_schedule2',
				array(
					'sych_weeks' => 1,
					'months'     => 'months',
				)
			);
			$general_delivery_option    = array(
				'delivery_gap'    => $delivery_default_schedule['delivery_gap'],
				'delivery_period' => $delivery_default_schedule['delivery_period'],
				'on'              => get_option( 'ywsbs_delivery_sync_delivery_schedules', 'no' ),
				'sych_weeks'      => $delivery_default_schedule2['sych_weeks'],
				'months'          => $delivery_default_schedule2['months'],
				'years_month'     => isset( $delivery_default_schedule2['years_month'] ) ? $delivery_default_schedule2['years_month'] : '',
				'years_day'       => isset( $delivery_default_schedule2['years_day'] ) ? $delivery_default_schedule2['years_day'] : '',
			);

			return $general_delivery_option;
		}

		/**
		 * Set the schedule inside the table.
		 *
		 * @param int $subscription_id Subscription id.
		 */
		public function set_delivery_schedules( $subscription_id ) {
			$subscription      = ywsbs_get_subscription( $subscription_id );
			$delivery_settings = $subscription->get( 'delivery_schedules' );

			if ( empty( $delivery_settings ) ) {
				return;
			}

			$previous_payment_date = $subscription->get( 'previous_payment_due_date' );
			$start_date            = new DateTime();
			if ( ! empty( $previous_payment_date ) && $previous_payment_date > time() ) {
				$start_date->setTimestamp( $previous_payment_date );
			}

			$schedules = $this->get_delivery_schedules_by_subscription( $subscription->get_id() );
			if ( $schedules ) {
				$last_date  = end( $schedules );
				$start_date = new DateTime( $last_date->scheduled_date );
				if ( 'yes' === $delivery_settings['on'] ) {
					$start_date->modify( '+1 day' );
				} else {
					$caller     = 'get_delivery_date_for_' . $delivery_settings['delivery_period'];
					$start_date = $this->$caller( $delivery_settings, $start_date, 'date' );
				}
			}

			$first_delivery_date = ( 'yes' === $delivery_settings['on'] ) ? $this->calculate_first_delivery_date( $delivery_settings, $start_date ) : $start_date->getTimestamp();
			$last_delivery_date  = ywsbs_get_timestamp_from_option( $first_delivery_date, $subscription->get( 'price_is_per' ), $subscription->get( 'price_time_option' ) );

			$this->add_multiple_delivery_schedules( $delivery_settings, $first_delivery_date, $last_delivery_date, $subscription_id );

		}

		/**
		 * Calculate the first delivery date.
		 *
		 * @param array    $delivery_settings Delivery settings.
		 * @param DateTime $start_date Start date.
		 * @return int|bool
		 */
		public function calculate_first_delivery_date( $delivery_settings, $start_date ) {

			if ( ! in_array( $delivery_settings['delivery_period'], array( 'weeks', 'months', 'years' ), true ) ) {
				$start_date = new DateTime();
				return $start_date->getTimestamp();
			}

			$caller = 'get_delivery_date_for_' . $delivery_settings['delivery_period'];

			$first_delivery_date = $this->$caller( $delivery_settings, $start_date );

			return $first_delivery_date;

		}

		/**
		 * Return the delivery date calculated for weekly periods.
		 *
		 * @param array    $delivery_settings Delivery settings.
		 * @param DateTime $start_date Start date.
		 * @param string   $type Format of date.
		 *
		 * @return int
		 */
		public function get_delivery_date_for_weeks( $delivery_settings, $start_date, $type = 'timestamp' ) {

			$new_date = $start_date;
			$new_date->modify( 'next ' . ywsbs_get_week_day_string( $delivery_settings['sych_weeks'] ) );
			$new_date->setTime( $this->time_of_day, 0, 0 );

			return 'timestamp' === $type ? $new_date->getTimestamp() : $new_date;
		}

		/**
		 * Return the delivery date calculated for monthly periods.
		 *
		 * @param array    $delivery_settings Delivery settings.
		 * @param DateTime $start_date Start date.
		 * @param string   $type Format of date.
		 *
		 * @return int
		 */
		public function get_delivery_date_for_months( $delivery_settings, $start_date, $type = 'timestamp' ) {

			$new_date = $start_date;

			if ( 'end' === $delivery_settings['months'] ) {
				$new_date->modify( 'last day of this month' );
			} else {
				$month = (int) $delivery_settings['months'];
				if ( $new_date->format( 'd' ) <= (int) $month ) {
					$diff = $month - $new_date->format( 'd' );
					$new_date->modify( '+ ' . $diff . ' days' );
				} else {
					$new_date->modify( 'first day of next month' );
					$new_date->add( new DateInterval( 'P' . ( $month - 1 ) . 'D' ) );
				}
			}

			$new_date->setTime( $this->time_of_day, 0, 0 );
			return 'timestamp' === $type ? $new_date->getTimestamp() : $new_date;
		}

		/**
		 * Return the delivery date calculated for yearly periods.
		 *
		 * @param array    $delivery_settings Delivery settings.
		 * @param DateTime $start_date Start date.
		 * @param string   $type Format of date.
		 *
		 * @return int
		 */
		public function get_delivery_date_for_years( $delivery_settings, $start_date, $type = 'timestamp' ) {

			$new_date = $start_date;

			$day   = ( 'end' === $delivery_settings['years_day'] ) ? 1 : $delivery_settings['years_day'];
			$month = $delivery_settings['years_month'];

			if ( $new_date->format( 'm' ) < $month || ( $new_date->format( 'm' ) == $month ) && ( $new_date->format( 'd' ) < $month ) ) { //phpcs:ignore
				$new_date = $new_date->modify( $new_date->format( 'y' ) . '-' . $month . '-' . $day );
			} else {
				$new_date = $new_date->modify( ( (int) $new_date->format( 'y' ) + 1 ) . '-' . $month . '-' . $day );
			}

			// Move the date at the end of the month.
			if ( 'end' === $delivery_settings['years_day'] ) {
				$new_date->modify( 'last day of this month' );
			}

			$new_date->setTime( $this->time_of_day, 0, 0 );

			return 'timestamp' === $type ? $new_date->getTimestamp() : $new_date;
		}

		/**
		 * Add new delivery schedule inside the table
		 *
		 * @param int    $subscription_id Subscription id.
		 * @param string $schedule_date Schedule date.
		 * @param string $status Status.
		 *
		 * @since 2.2.0
		 */
		public function add_delivery_schedules( $subscription_id, $schedule_date, $status = 'waiting' ) {
			global $wpdb;

			$data = array(
				'subscription_id' => $subscription_id,
				'status'          => $status,
				'entry_date'      => current_time( 'mysql' ),
				'scheduled_date'  => wp_date( 'Y-m-d H:i:s', $schedule_date ),
			);

			$wpdb->insert( $this->table_name, $data ); // phpcs:ignore
		}

		/**
		 * Return the delivery schedules of a subscription.
		 *
		 * @param int $delivery_id Delivery id.
		 */
		public function get_delivery_schedules_by_id( $delivery_id ) {
			global $wpdb;
			$result = $wpdb->get_row( $wpdb->prepare( "Select * from {$this->table_name} WHERE id = %d", $delivery_id ) );  // phpcs:ignore

			return $result;
		}

		/**
		 * Return the delivery schedules of a subscription.
		 *
		 * @param int      $subscription_id Subscription id.
		 * @param string   $status Status.
		 * @param int|bool $limit Limit quantity.
		 */
		public function get_delivery_schedules_by_subscription( $subscription_id, $status = '', $limit = false ) {
			global $wpdb;

			$status_filter = empty( $status ) ? '%' : $status;
			if ( $limit ) {
				$result = $wpdb->get_results( $wpdb->prepare( "Select * from {$this->table_name} WHERE subscription_id = %d AND status like %s ORDER by scheduled_date ASC LIMIT %d", $subscription_id, $status_filter, $limit ) );  // phpcs:ignore
			} else {
				$result = $wpdb->get_results( $wpdb->prepare( "Select * from {$this->table_name} WHERE subscription_id = %d AND status like %s ORDER by scheduled_date ASC ", $subscription_id, $status_filter ) );  // phpcs:ignore
			}

			return $result;
		}

		/**
		 * Return the delivery schedules of a subscription.
		 *
		 * @param int $subscription_id Subscription id.
		 */
		public function get_delivery_schedules_ordered( $subscription_id ) {
			global $wpdb;

			$result = $wpdb->get_results( $wpdb->prepare( "Select * from {$this->table_name} WHERE subscription_id = %d ORDER BY FIELD( status, 'processing', 'waiting','shipped', 'cancelled'), scheduled_date ASC", $subscription_id ) );  // phpcs:ignore
			return $result;
		}

		/**
		 * Get status.
		 *
		 * @return mixed|void
		 */
		public function get_status() {
			$status = array(
				'processing' => esc_html_x( 'In process', 'Delivery schedules status', 'yith-woocommerce-subscription' ),
				'waiting'    => esc_html_x( 'Waiting', 'Delivery schedules status', 'yith-woocommerce-subscription' ),
				'shipped'    => esc_html_x( 'Shipped', 'Delivery schedules status', 'yith-woocommerce-subscription' ),
				'cancelled'  => esc_html_x( 'Cancelled', 'Delivery schedules status', 'yith-woocommerce-subscription' ),
			);

			return apply_filters( 'ywsbs_delivery_schedules_status', $status );
		}

		/**
		 * Get status label
		 *
		 * @param string $status_index Status.
		 * @return array|mixed
		 */
		public function get_status_label( $status_index ) {
			$status = self::get_status();
			return isset( $status[ $status_index ] ) ? $status[ $status_index ] : $status_index;
		}

		/**
		 * Update the status of of the delivery schedules.
		 */
		public function set_status_to_delivery_schedules() {
			global $wpdb;

			$timestamp = time() - DAY_IN_SECONDS;
			$data      = gmdate( 'Y-m-d H:i:s', $timestamp );
			$now       = gmdate( 'Y-m-d H:i:s', time() );

			$q = $wpdb->prepare(
				"Update {$this->table_name} as ds
			LEFT JOIN {$wpdb->postmeta} as pm on ds.subscription_id = pm.post_id SET  status = 'processing' WHERE (pm.meta_key = 'status' and pm.meta_value NOT IN ('cancelled', 'paused' ) ) AND scheduled_date <= %s AND scheduled_date >= %s AND status NOT LIKE %s",
				$now,
				$data,
				'shipped'
			);

			$wpdb->get_results( $q );  // phpcs:ignore
		}

		/**
		 * Update the status of a delivery schedules
		 *
		 * @param int    $delivery_id Delivery id.
		 * @param string $new_status New status.
		 * @return array
		 */
		public function update_status( $delivery_id, $new_status ) {
			global $wpdb;

			$update_result = array(
				'updated' => 0,
				'sent_on' => '',
			);

			$delivery_info = self::get_delivery_schedules_by_id( $delivery_id );

			if ( $delivery_info ) {
				$now                      = 'shipped' === $new_status ? gmdate( 'Y-m-d H:i:s', time() ) : '';
				$update_result['sent_on'] = $now;
				$update_result['updated'] = $wpdb->query( $wpdb->prepare( "Update {$this->table_name} SET  status = %s, sent_on = %s  WHERE id = %d", $new_status, $now, $delivery_id ) );  // phpcs:ignore

				if ( $update_result['updated'] ) {
					do_action( 'ywsbs_delivery_status_change', $new_status, $delivery_id );
					if ( 'shipped' === $new_status ) {
						WC()->mailer();
						do_action( 'ywsbs_customer_subscription_delivery_schedules_mail_notification', $this->get_delivery_schedules_by_id( $delivery_id ) );
					}
				}
			}

			return $update_result;
		}

		/**
		 * Check if there are delivery schedules on table
		 *
		 * @return bool
		 * @since 2.2.0
		 */
		public function is_delivery_schedules_table_empty() {
			global $wpdb;
			$count = $wpdb->get_var( "Select count(0) as c from {$this->table_name}" );  // phpcs:ignore

			return 0 === $count;
		}

		/**
		 * Update delivery schedules status when the subscription is cancelled.
		 *
		 * @param int $subscription_id Subscription cancelled.
		 */
		public function update_delivery_for_cancelled_subscription( $subscription_id ) {
			$subscription      = ywsbs_get_subscription( $subscription_id );
			$delivery_settings = $subscription->get( 'delivery_schedules' );

			if ( empty( $delivery_settings ) ) {
				return;
			}

			$end_date = $subscription->get_end_date();
			// set to cancelled the status of delivery schedules.
			if ( $end_date <= current_time( 'timestamp' ) ) {  // phpcs:ignore
				global $wpdb;
				$wpdb->get_results( $wpdb->prepare( "Update {$this->table_name} SET status = 'cancelled' WHERE subscription_id = %d AND status NOT LIKE %s and scheduled_date >= CURRENT_DATE()", $subscription_id, 'shipped' ) );  // phpcs:ignore
			}
		}

		/**
		 * Update delivery schedules status when the subscription is resumed from a pause.
		 *
		 * @param int $subscription_id Subscription cancelled.
		 */
		public function update_delivery_for_resumed_subscription( $subscription_id ) {
			$subscription      = ywsbs_get_subscription( $subscription_id );
			$delivery_settings = $subscription->get( 'delivery_schedules' );

			if ( empty( $delivery_settings ) ) {
				return;
			}

			$start_date = new DateTime();
			$date_pause = $subscription->get( 'date_of_pauses' );
			$last       = ( $date_pause[ count( $date_pause ) - 1 ] );

			global $wpdb;
			$ds_to_update          = $wpdb->get_results( $wpdb->prepare( "Select * from {$this->table_name} WHERE subscription_id = %d and scheduled_date >= FROM_UNIXTIME(%s) and status NOT LIKE %s ", $subscription_id, $last, 'shipped' ) );
			$processing_date       = ( time() + DAY_IN_SECONDS );
			$current_delivery_date = ( 'yes' === $delivery_settings['on'] ) ? $this->calculate_first_delivery_date( $delivery_settings, $start_date ) : $start_date->getTimestamp();
			if ( $ds_to_update ) {
				foreach ( $ds_to_update as $current_ds ) {
					$status = ( $processing_date > $current_delivery_date ) ? 'processing' : 'waiting';
					$wpdb->query(
						$wpdb->prepare( "Update {$this->table_name} SET scheduled_date = FROM_UNIXTIME(%s), status = %s WHERE id = %d", $current_delivery_date, $status, $current_ds->id )
					);
					$current_delivery_date = ywsbs_get_timestamp_from_option( $current_delivery_date, $delivery_settings['delivery_gap'], $delivery_settings['delivery_period'] );
				}
			}
		}

		/**
		 * Update delivery scheduled
		 *
		 * @param string             $key Meta data changed.
		 * @param mixed              $new_value New date changed.
		 * @param mixed              $old_value Old date.
		 * @param YWSBS_Subscription $subscription Subscription.
		 */
		public function update_delivery_for_change_payment_due_date( $key, $new_value, $old_value, $subscription ) {
			// check if the date changed is the payment due date or if it changed.
			if ( 'payment_due_date' !== $key || $old_value > $new_value ) {
				return;
			}

			$delivery_settings = $subscription->get( 'delivery_schedules' );
			// check if delivery setting is set.
			if ( empty( $delivery_settings ) ) {
				return;
			}

			$ds                    = $this->get_delivery_schedules_by_subscription( $subscription->get_id() );
			$last                  = end( $ds );
			$current_delivery_date = strtotime( $last->scheduled_date );

			$this->add_multiple_delivery_schedules( $delivery_settings, $current_delivery_date, $new_value, $subscription->get_id() );

		}

		/**
		 * Add delivery schedule from a start date to a last date.
		 *
		 * @param array $delivery_settings Delivery settings.
		 * @param int   $current_delivery_date Start date timestamp.
		 * @param int   $last_delivery_date Last date timestamp.
		 * @param int   $subscription_id Subscription id.
		 */
		public function add_multiple_delivery_schedules( $delivery_settings, $current_delivery_date, $last_delivery_date, $subscription_id ) {
			$processing_date = ( time() + DAY_IN_SECONDS );

			while ( $current_delivery_date < $last_delivery_date ) {
				$status = ( $processing_date > $current_delivery_date ) ? 'processing' : 'waiting';
				$this->add_delivery_schedules( $subscription_id, $current_delivery_date, $status );
				$current_delivery_date = ywsbs_get_timestamp_from_option( $current_delivery_date, $delivery_settings['delivery_gap'], $delivery_settings['delivery_period'] );
			}
		}

		/**
		 * Check if the post deleted is a subscription and in that case remove the delivery schedules.
		 *
		 * @param int $post_id Post deleted.
		 */
		public function maybe_delete_delivery_status( $post_id ) {
			$post = get_post( $post_id );
			if ( $post && YITH_YWSBS_POST_TYPE === $post->post_type ) {
				$this->delete_delivery_status_of_a_subscription( $post_id );
			}
		}

		/**
		 * Delete the delivery schedules from the table when a subscription is deleted.
		 *
		 * @param int $subscription_id Subscription id.
		 */
		public function delete_delivery_status_of_a_subscription( $subscription_id ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$this->table_name} WHERE subscription_id = %d", $subscription_id ) );
		}

		/**
		 * Return a message for a product that has a delivery schedule.
		 *
		 * @param WC_Product $product Product.
		 * @return string
		 */
		public function get_product_delivery_message( $product ) {

			if ( 'yes' !== get_option( 'ywsbs_delivery_show_product_info', 'yes' ) || ! $this->has_delivery_scheduled( $product ) ) {
				return '';
			}

			$message           = sprintf( '%s %s %s', '<strong>', esc_html_x( 'Delivery schedules:', 'delivery info in single product page', 'yith-woocommerce-subscription' ), '</strong>' ) . ' ';
			$delivery_settings = $this->get_delivery_settings( $product );

			if ( $delivery_settings['delivery_gap'] ) {
				$gap      = ( 1 == $delivery_settings['delivery_gap'] ) ? '' : $delivery_settings['delivery_gap']; //phpcs:ignore
				// translators: placeholder i.e. Every 5 days.
				$message .= sprintf( __( 'Every %1$s %2$s', 'yith-woocommerce-subscription' ), $gap, ywsbs_get_time_options_sing_plur( $delivery_settings['delivery_period'], (int) $delivery_settings['delivery_gap'] ) );
				if ( 'days' !== $delivery_settings['delivery_period'] && 'yes' === $delivery_settings['on'] ) {
					$years_day = $delivery_settings['years_day'];
					$months    = $delivery_settings['months'];

					if ( class_exists( 'NumberFormatter' ) ) {
						$nf        = new NumberFormatter( get_locale(), NumberFormatter::ORDINAL );
						$months    = $nf->format( $delivery_settings['months'] );
						$years_day = $nf->format( $delivery_settings['years_day'] );
					}

					switch ( $delivery_settings['delivery_period'] ) {
						case 'weeks':
							$day_weeks = ywsbs_get_period_options( 'day_weeks' );
							// translators: placeholder day of week i.e. on Friday.
							$message .= ' ' . sprintf( __( 'on %s', 'yith-woocommerce-subscription' ), $day_weeks[ $delivery_settings['sych_weeks'] ] );
							break;
						case 'months':
							$day = 'end' !== $delivery_settings['months'] ? $months : __( 'at the end of month', 'yith-woocommerce-subscription' );
							// translators: placeholder day of month i.e. on day 15.
							$message .= ' ' . sprintf( __( 'on day %s ', 'yith-woocommerce-subscription' ), $day );
							break;
						case 'years':
							$day_months = ywsbs_get_period_options( 'months' );
							$day        = 'end' !== $delivery_settings['years_day'] ? $years_day : __( 'at the end of ', 'yith-woocommerce-subscription' );
							// translators: placeholder day of year i.e. on 15 August.
							$message .= ' ' . sprintf( __( 'on %1$s %2$s', 'yith-woocommerce-subscription' ), $day, $day_months[ $delivery_settings['years_month'] ] );
							break;
					}

					if ( apply_filters( 'ywsbs_delivery_schedules_next_delivery_date', true, $product, $delivery_settings ) ) {
						$start_date          = new DateTime();
						$first_delivery_date = $this->calculate_first_delivery_date( $delivery_settings, $start_date );
						$message            .= sprintf( ' - %s %s %s ', '<strong>', esc_html_x( 'Next delivery:', 'delivery info in single product page', 'yith-woocommerce-subscription' ), '</strong>' );
						$message            .= date_i18n( wc_date_format(), $first_delivery_date );
					}
				}
			}

			return apply_filters( 'ywsbs_delivery_schedules_next_delivery_date', $message, $product, $delivery_settings );

		}

		/**
		 * Return all the schedule
		 *
		 * @return array|object|null
		 */
		public function get_processing_delivery_schedules() {
			global $wpdb;
			$q = $wpdb->prepare( "Select * from {$this->table_name} as ds where status = %s order by id DESC ", 'processing' );
			return $wpdb->get_results( $q );
		}
	}
}


/**
 * Unique access to instance of YWSBS_Delivery_Schedules_List_Table class
 *
 * @return YWSBS_Subscription_Delivery_Schedules
 */
function YWSBS_Subscription_Delivery_Schedules() { //phpcs:ignore
	return YWSBS_Subscription_Delivery_Schedules::get_instance();
}
