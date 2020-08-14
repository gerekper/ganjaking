<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Delivery_Date_Manager' ) ) {
	class YITH_Delivery_Date_Manager {

		protected static $_instance;


		public function __construct() {
			add_action( 'woocommerce_checkout_update_order_review', array( $this, 'set_timeslot_session' ) );
			add_action( 'woocommerce_cart_calculate_fees', array( $this, 'add_timeslot_fee' ), 10 );
		}

		public static function get_instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}


		/**
		 * return the first shipping date for a processing method
		 *
		 * @param $id
		 * @param array $args
		 *
		 * @return int
		 * @since 2.0
		 *
		 * @author Salvatore Strano
		 */
		public function get_first_shipping_date( $id, $args = array() ) {

			$default_args = array(
				'start_date'      => current_time( 'Y-m-d H:i:s' ),
				'min_working_day' => YITH_Delivery_Date_Processing_Method()->get_min_working_day( $id ),
				'work_days'       => YITH_Delivery_Date_Processing_Method()->get_work_days( $id ),
				'today'           => current_time( 'Y-m-d H:i:s' )
			);

			$args                = wp_parse_args( $args, $default_args );
			$first_shipping_date = false;

			$start_timestamp   = strtotime( $args['start_date'] );
			$today             = strtotime( $args['today'] );
			$all_days          = array_keys( yith_get_worksday( false ) );
			$wday              = strtolower( date( 'D', $start_timestamp ) );
			$i                 = array_search( $wday, $all_days );
			$days_for_shipping = 0;
			$min_workdays      = $args['min_working_day'];

			$max_execution = 1000;
			if ( count( $args['work_days'] ) > 0 ) {
				do {

					$current_work_day = isset( $args['work_days'][ $wday ] ) ? $args['work_days'][ $wday ] : false;
					if ( $current_work_day && 'yes' === $current_work_day['enabled'] ) {
						$timestamp = strtotime( "{$days_for_shipping} days", $start_timestamp );

						$is_holiday = YITH_Delivery_Date_Calendar()->is_holiday( $id, $timestamp );

						if ( ! $is_holiday ) {

							if ( 0 == $days_for_shipping && $today === $timestamp ) {

								if ( ! empty( $current_work_day['timelimit'] ) ) {
									$timestamp_limit = strtotime( current_time( 'Y-m-d' ) . " {$current_work_day['timelimit']}" );
									if ( current_time( 'timestamp' ) < $timestamp_limit ) {
										$first_shipping_date = $today;
										$min_workdays --;
									}
								} else {
									$first_shipping_date = $today;
									$min_workdays --;
								}
							} else {
								$first_shipping_date = $timestamp;
								$min_workdays --;
							}
						}

					}
					$days_for_shipping ++;
					$i    = ( $i + 1 ) % 7;
					$wday = $all_days[ $i ];

				} while ( $min_workdays >= 0 && $max_execution > 0 );
			}

			return apply_filters( 'ywcdd_first_shipping_date', $first_shipping_date );
		}

		/**
		 * return the first delivery date for a carrier
		 *
		 * @param $id
		 * @param array $args
		 *
		 * @return int
		 * @since 2.0
		 *
		 * @author Salvatore Strano
		 */
		public function get_first_delivery_date( $id, $args = array() ) {
			$default_args = array(
				'shipping_date'   => current_time( 'Y-m-d H:i:s' ),
				'min_working_day' => YITH_Delivery_Date_Carrier()->get_min_working_day( $id ),
				'work_days'       => YITH_Delivery_Date_Carrier()->get_work_days( $id ),
				'max_range'       => YITH_Delivery_Date_Carrier()->get_max_range( $id ),
				'today'           => current_time( 'Y-m-d H:i:s' )
			);


			$args                = wp_parse_args( $args, $default_args );
			$first_delivery_date = false;
			$start_timestamp     = is_string( $args['shipping_date'] ) ? strtotime( $args['shipping_date'] ) : $args['shipping_date'];
			$today               = strtotime( $args['today'] );
			$wday                = strtolower( date( 'D', $start_timestamp ) );
			$all_day             = array_keys( yith_get_worksday( false ) );
			$i                   = array_search( $wday, $all_day );
			$min_workdays        = $args['min_working_day'];
			$end_by              = strtotime( ( $args['max_range'] + 1 ) . ' days', $start_timestamp );
			$days_for_delivery   = ceil( ( $start_timestamp - $today ) / DAY_IN_SECONDS );

			$has_time_slot = YITH_Delivery_Date_Carrier()->get_enabled_time_slots( $id );
			$has_time_slot = ! ( empty( $has_time_slot ) );
			do {
				$timestamp = strtotime( "{$days_for_delivery} days", $today );


				if ( isset( $args['work_days'][ $wday ] ) || ( $start_timestamp === $timestamp && 0 < $min_workdays ) ) {

					$is_holiday = YITH_Delivery_Date_Calendar()->is_holiday( $id, $timestamp );

					$day_have_slots = $this->get_available_time_slots( $id, $timestamp );

					if ( ! $is_holiday && ( ! $has_time_slot || count( $day_have_slots ) > 0 ) ) {


						if ( $min_workdays <= 0 ) {

							$first_delivery_date = $timestamp;
						}
						$min_workdays --;
					}
				}

				$days_for_delivery ++;
				$i = ( $i + 1 ) % 7;;
				$wday = $all_day[ $i ];

			} while ( ! $first_delivery_date && ( $min_workdays >= 0 || $timestamp < $end_by ) );


			$first_delivery_date = $first_delivery_date ? $first_delivery_date : $end_by;

			return apply_filters( 'ywcdd_first_delivery_date', $first_delivery_date );
		}

		/**
		 * return all delivery date ( array of timestamp )
		 *
		 * @param $id
		 * @param array $args
		 *
		 * @return array
		 * @since 2.0.0
		 *
		 * @author YITH
		 */
		public function get_all_delivery_dates( $id, $args = array() ) {
			$default_args = array(
				'from_date' => current_time( 'Y-m-d H:i:s' ),
				'work_days' => YITH_Delivery_Date_Carrier()->get_work_days( $id ),
				'max_range' => YITH_Delivery_Date_Carrier()->get_max_range( $id )
			);

			$args = wp_parse_args( $args, $default_args );


			$from_date = is_string( $args['from_date'] ) ? strtotime( $args['from_date'] ) : $args['from_date'];

			$start_time_stamp = $from_date + DAY_IN_SECONDS;
			$delivery_dates   = array( $from_date );

			$has_time_slot = YITH_Delivery_Date_Carrier()->get_enabled_time_slots( $id );
			$has_time_slot = ! ( empty( $has_time_slot ) );
			$count_days    = 1;
			while ( $count_days < $args['max_range'] ) {

				$day1           = strtolower( date( 'D', $start_time_stamp ) );
				$is_work_day    = in_array( $day1, $args['work_days'] );
				$is_holiday     = YITH_Delivery_Date_Calendar()->is_holiday( $id, $start_time_stamp );
				$day_have_slots = $has_time_slot ? $this->get_available_time_slots( $id, $start_time_stamp ) : array();

				if ( $is_work_day && ! $is_holiday && ( ! $has_time_slot || count( $day_have_slots ) > 0 ) ) {

					$delivery_dates[] = $start_time_stamp;
					$count_days ++;
				}
				$start_time_stamp += DAY_IN_SECONDS;

			}
			$delivery_dates = apply_filters( 'ywcdd_add_custom_delivery_dates', array_unique( $delivery_dates ), $id );
			asort( $delivery_dates );

			return $delivery_dates;

		}

		/**
		 * get the last useful shipping date
		 *
		 * @param $date
		 * @param $processing_id
		 * @param $carrier_id
		 *
		 * @return int
		 * @author YITH
		 *
		 */
		public function get_last_shipping_date( $date, $processing_id, $carrier_id, $args = array() ) {


			if ( ! is_numeric( $date ) ) {
				$date = strtotime( $date );

			}
			$default_args = array(
				'processing_min_working_day' => YITH_Delivery_Date_Processing_Method()->get_min_working_day( $processing_id ),
				'carrier_min_working_day'    => YITH_Delivery_Date_Carrier()->get_min_working_day( $carrier_id ),
			);

			$args = wp_parse_args( $args, $default_args );


			extract( $args );

			$processing_work_days = YITH_Delivery_Date_Processing_Method()->get_work_days( $processing_id );
			$carrier_work_days    = YITH_Delivery_Date_Carrier()->get_work_days( $carrier_id );
			$has_time_slot        = YITH_Delivery_Date_Carrier()->get_enabled_time_slots( $carrier_id );
			$has_time_slot        = ! ( empty( $has_time_slot ) );
			$day_for_delivery     = 0;
			$end_by               = strtotime( current_time( 'Y-m-d H:i:s' ) );
			$last_shipping_date   = false;
			$wday                 = strtolower( date( 'D', $date ) );
			$all_days             = array_keys( yith_get_worksday( false ) );
			$i                    = array_search( $wday, $all_days );


			do {
				$timestamp = strtotime( "{$day_for_delivery} days", $date );

				if ( $carrier_min_working_day > 0 ) {

					$is_working_day = isset( $carrier_work_days[ $wday ] );
					$is_holiday     = YITH_Delivery_Date_Calendar()->is_holiday( $carrier_id, $timestamp );
					$day_have_slots = $this->get_available_time_slots( $carrier_id, $timestamp );

					if ( $is_working_day && ! $is_holiday && ( ! $has_time_slot || count( $day_have_slots ) > 0 ) ) {
						$carrier_min_working_day --;
					}
				} elseif ( isset( $processing_work_days[ $wday ] ) && ! YITH_Delivery_Date_Calendar()->is_holiday( $processing_id, $timestamp ) ) {
					$last_shipping_date = $timestamp;


				}
				$i = ( 0 == $i ) ? 6 : $i - 1;
				$day_for_delivery --;
				$wday = $all_days[ $i ];


			} while ( ! $last_shipping_date && $timestamp > $end_by );


			return $last_shipping_date ? $last_shipping_date : strtotime( current_time( 'Y-m-d' ) );
		}

		/**
		 * @param int $from
		 * @param int $to
		 *
		 * @return array
		 */
		public function get_date_range( $from, $to ) {

			$range = array();
			while ( $from <= $to ) {
				$range[] = $from;
				$from    += DAY_IN_SECONDS;
			}

			return $range;
		}

		/**
		 * get available slots for a date
		 *
		 * @param $carrier_id
		 * @param $date
		 *
		 * @return array
		 */
		public function get_available_time_slots( $carrier_id, $date, $check = false ) {

			$all_slots       = YITH_Delivery_Date_Carrier()->get_enabled_time_slots( $carrier_id );
			$available_slots = array();

			if ( count( $all_slots ) > 0 ) {

				$available_slots = $all_slots;

				if ( ! is_numeric( $date ) ) {
					$date = strtotime( $date );
				}

				$a            = HOUR_IN_SECONDS;
				$now          = current_time( 'Y-m-d H:i:s' );
				$cut_off_time = apply_filters( 'ywcdd_cut_off_time', 0, $carrier_id );
				$now_time     = strtotime( $now ) + $cut_off_time;
				$wday         = strtolower( date( "D", $date ) );

				foreach ( $all_slots as $slot_id => $slot ) {

					$time_from = strtotime( $slot['timefrom'], $date );
					$time_to   = strtotime( $slot['timeto'], $date );

					$check_time = $time_to < $now_time;

					$check_time = $check_time || apply_filters( 'ywcdd_is_invalid_time_slot', false, $available_slots, $slot_id, $time_to, $time_from, $now_time, $date, $carrier_id );

					$day_selected = ! empty( $slot['day_selected'] ) ? $slot['day_selected'] : array();

					$check_override_day = ( isset( $slot['override_days'] ) && yith_plugin_fw_is_true( $slot['override_days'] ) && count( $day_selected ) > 0 && ! in_array( $wday, $slot['day_selected'] ) );

					$check_lockout_order = $this->check_if_time_slot_is_lockout( $slot, $carrier_id, $date );

					if ( $check_time || $check_override_day || $check_lockout_order ) {

						unset( $available_slots[ $slot_id ] );
					}

				}

			}

			return $available_slots;
		}


		/**
		 * check if a time slot is lockout
		 *
		 * @param array $slot
		 * @param int $carrier_id
		 * @param $date_selected
		 *
		 * @return bool
		 */
		public function check_if_time_slot_is_lockout( $slot, $carrier_id, $date_selected ) {

			$is_lockout = false;

			if ( ( $slot['max_order'] != '' && $slot['max_order'] > 0 ) ) {
				global $wpdb;

				/*
				 * SELECT COUNT(p.ID) AS total_order FROM wp_posts as p INNER JOIN wp_postmeta as m1 ON p.ID=m1.post_id INNER JOIN wp_postmeta as m2 ON p.ID = m2.post_id  INNER JOIN wp_postmeta as m3 ON p.ID = m3.post_id INNER JOIN wp_postmeta AS m4 WHERE(
    p.post_type = 'shop_order' AND p.post_status IN ('wc-pending','wc-completed','wc-on-hold' ) AND
    (m1.meta_key = 'ywcdd_order_delivery_date' AND m1.meta_value = '2020-05-17' ) AND
    (m2.meta_key = 'ywcdd_order_slot_from' AND m2.meta_value = '11:00' ) AND
    (m3.meta_key = 'ywcdd_order_slot_to' AND m3.meta_value = '12:00' ) AND
	(m4.meta_key = 'ywcdd_order_carrier_id' AND m4.meta_value = '57937' )
)
				 */

				$order_status  = apply_filters( 'ywcdd_order_status', array(
					'wc-pending',
					'wc-processing',
					'wc-on-hold',
					'wc-completed'
				) );

				$query   = "SELECT COUNT( DISTINCT  p.ID) AS total_order FROM {$wpdb->posts} as p INNER JOIN {$wpdb->postmeta} as m1 ON p.ID=m1.post_id INNER JOIN {$wpdb->postmeta} as m2 ON p.ID = m2.post_id  INNER JOIN {$wpdb->postmeta} as m3 ON p.ID = m3.post_id INNER JOIN {$wpdb->postmeta} AS m4 WHERE(
						    p.post_type = 'shop_order' AND p.post_status IN ('" . implode( "','", $order_status ) . "') AND
						    (m1.meta_key = 'ywcdd_order_delivery_date' AND m1.meta_value = %s ) AND
						    (m2.meta_key = 'ywcdd_order_slot_from' AND m2.meta_value = %s ) AND
						    (m3.meta_key = 'ywcdd_order_slot_to' AND m3.meta_value = %s ) AND
							(m4.meta_key = 'ywcdd_order_carrier_id' AND m4.meta_value = %s )
						)";

				$query = $wpdb->prepare( $query, date('Y-m-d', $date_selected), $slot['timefrom'], $slot['timeto'], $carrier_id );

				$result = $wpdb->get_var($query);

				if( ($slot['max_order']-$result )<=0 ){

					$is_lockout = true;
				}

			}

			return $is_lockout;
		}


		/* count time slot used by carrier,delivery date
		*
		* @author YITHEMES
		* @since 1.0.0
		*
		* @param        $date_selected
		* @param string $carrier_name
		*
		* @return array
		*/
		public function count_timeslot_order_used( $date_selected, $carrier_id ) {

			global $wpdb;

			$order_status  = apply_filters( 'ywcdd_order_status', array(
				'wc-pending',
				'wc-processing',
				'wc-on-hold',
				'wc-completed'
			) );
			$date_selected = date( 'Y-m-d', $date_selected );
			$query         = $wpdb->prepare( "SELECT ord.ID FROM {$wpdb->posts} ord INNER JOIN {$wpdb->postmeta}  pm ON ord.ID = pm.post_id
                                      WHERE ord.post_type='%s' AND ord.post_status IN ('" . implode( "','", $order_status ) . "') AND pm.meta_key='%s' AND pm.meta_value='%s'", 'shop_order', 'ywcdd_order_delivery_date', $date_selected );
			$order_ids     = $wpdb->get_col( $query );

			$results = array();
			foreach ( $order_ids as $order_id ) {

				$order    = wc_get_order( $order_id );
				$timefrom = strtolower( $order->get_meta( 'ywcdd_order_slot_from' ) );
				$timeto   = strtolower( $order->get_meta( 'ywcdd_order_slot_to' ) );
				$carrier  = $order->get_meta( 'ywcdd_order_carrier_id' );

				if ( - 1 == $carrier ) {
					$carrier = get_option( 'ywcdd_default_carrier_id', - 1 );
					$order->update_meta_data( 'ywcdd_order_carrier_id', $carrier );
					$order->save();
				}
				$skip = ( $carrier_id != $carrier ) || $carrier_id == $carrier && ( $timefrom === '' || $timeto === '' );

				if ( ! $skip ) {

					if ( ! is_numeric( $timefrom ) && ! is_numeric( $timeto ) ) {
						$key = $timefrom . '-' . $timeto;
					} else {
						$key = date( 'H:i', $timefrom ) . '-' . date( 'H:i', $timeto );
					}
					if ( ! isset( $results[ $carrier_id ][ $key ] ) ) {
						$results[ $carrier_id ][ $key ] = 1;
					} else {
						$results[ $carrier_id ][ $key ] = $results[ $carrier_id ][ $key ] + 1;
					}
				}
			}

			return $results;
		}

		/**
		 * @param $post_data
		 */
		public function set_timeslot_session( $post_data ) {

			$args = wp_parse_args( $post_data );

			WC()->session->__unset( 'ywcdd_fee' );
			WC()->session->__unset( 'ywcdd_fee_name' );
			if ( isset( $args['ywcdd_timeslot_av'] ) && 'yes' === $args['ywcdd_timeslot_av'] ) {

				$timeslot_id   = isset( $args['ywcdd_timeslot'] ) ? $args['ywcdd_timeslot'] : '';
				$carrier_id    = isset( $args['ywcdd_carrier'] ) ? $args['ywcdd_carrier'] : - 1;
				$date_selected = isset( $args['ywcdd_delivery_date'] ) ? $args['ywcdd_delivery_date'] : current_time( 'Y-m-d' );

				if ( ! empty( $timeslot_id ) ) {
					$all_slots     = $this->get_available_time_slots( $carrier_id, $date_selected );
					$selected_slot = ( isset( $all_slots[ $timeslot_id ] ) ) ? $all_slots[ $timeslot_id ] : false;

					if ( $selected_slot ) {

						$fee      = wc_format_decimal( $selected_slot['fee'] );
						$fee_name = ! empty( $selected_slot['fee_name'] ) ? $selected_slot['fee_name'] : get_option( 'ywcdd_fee_label', 'Time Slot Fee' );
					} else {
						$fee      = 0;
						$fee_name = '';
					}

					if ( $fee > 0 ) {
						WC()->session->set( 'ywcdd_fee', $fee );
						WC()->session->set( 'ywcdd_fee_name', $fee_name );
					}
				}
			}
		}

		/**
		 * add timeslot fee
		 *
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_timeslot_fee() {

			if ( WC()->session->get( 'ywcdd_fee' ) ) {

				$time_slot_fee = WC()->session->get( 'ywcdd_fee_name' );
				$time_slot_fee = apply_filters( 'ywcdd_time_slot_fee_text', $time_slot_fee );
				$is_taxable    = 'yes' == get_option( 'ywcdd_fee_is_taxable', 'no' );
				$is_taxable    = apply_filters( 'ywcdd_time_slot_fee_taxable', $is_taxable );
				$tax_class     = '';

				if ( $is_taxable ) {

					$tax_class = get_option( 'ywcdd_fee_tax_class', '' );
					$tax_class = apply_filters( 'ywcdd_fee_tax_class', $tax_class );
				}

				WC()->cart->add_fee( $time_slot_fee, WC()->session->get( 'ywcdd_fee' ), $is_taxable, $tax_class );
			}
		}
	}

}

/**
 * @return YITH_Delivery_Date_Manager
 */
function YITH_Delivery_Date_Manager() {
	return YITH_Delivery_Date_Manager::get_instance();
}

YITH_Delivery_Date_Manager();
