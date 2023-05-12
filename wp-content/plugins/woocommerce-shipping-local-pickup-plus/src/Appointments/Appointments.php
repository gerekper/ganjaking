<?php
/**
 * WooCommerce Local Pickup Plus
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2023, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Local_Pickup_Plus\Appointments;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\Local_Pickup_Plus\Appointments\Appointment;
use SkyVerge\WooCommerce\PluginFramework\v5_11_0 as Framework;

/**
 * Handler for appointment objects associated with orders and shipping items.
 *
 * @since 2.7.0
 */
class Appointments {


	/**
	 * Gets an array of appointments for the given order.
	 *
	 * @since 2.7.0
	 *
	 * @param int|\WC_Order $order_id the ID of an order ar an order object
	 * @return Appointment[]
	 */
	public function get_order_appointments( $order_id ) {

		$appointments = [];

		if ( $order = $order_id instanceof \WC_Order ? $order_id : wc_get_order( $order_id ) ) {

			foreach ( $order->get_shipping_methods() as $shipping_item_id => $shipping_item ) {

				if ( $appointment = $this->get_shipping_item_appointment( $shipping_item ) ) {

					$appointments[ $shipping_item_id ] = $appointment;
				}
			}
		}

		return $appointments;
	}


	/**
	 * Gets the appointment object associated with the given shipping item.
	 *
	 * @since 2.7.0
	 *
	 * @param int|\WC_Order_Item_Shipping $shipping_item_id the ID of a shipping item or a shipping item object
	 * @return null|Appointment the appointment object or null if the shipping item is invalid or doesn't have an appointment date
	 */
	public function get_shipping_item_appointment( $shipping_item_id ) {

		try {
			// an invalid $shipping_item_id would create an empty appointment object
			$appointment = $shipping_item_id ? new Appointment( $shipping_item_id ) : null;
		} catch ( Framework\SV_WC_Plugin_Exception $e ) {
			$appointment = null;
		}

		return $appointment;
	}


	/**
	 * Gets the number of appointments for paid or pending orders at a given location and time slot.
	 *
	 * @since 2.8.0
	 *
	 * @param int $pickup_location_id pickup location ID: return appointments at this location
	 * @param int $start_time start time to return appointments from a time slot
	 * @param int $end_time end time to return appointments from a time slot
	 *
	 * @return array of integers, indexed by the time slot
	 */
	public function get_appointments_count_by_time_slot( $pickup_location_id, $start_time, $end_time ) {
		global $wpdb;

		$appointments_count_per_start_time = [];

		// query by time slot
		$start_time_results = $wpdb->get_results( $wpdb->prepare( "
			SELECT order_item_id, meta_value
			FROM {$wpdb->prefix}woocommerce_order_itemmeta
			WHERE meta_key = '_pickup_appointment_start'
			AND CAST(meta_value AS UNSIGNED) >= %d
			AND CAST(meta_value AS UNSIGNED) < %d
		", (int) $start_time, (int) $end_time ), ARRAY_A );

		if ( ! empty( $start_time_results ) ) {

			$start_time_results_ids = Framework\SV_WC_Helper::get_escaped_id_list( array_column( $start_time_results, 'order_item_id' ) );

			// query by location
			$location_results = $wpdb->get_col( $wpdb->prepare( "
				SELECT order_item_id
				FROM {$wpdb->prefix}woocommerce_order_itemmeta
				WHERE meta_key = '_pickup_location_id'
				AND meta_value = %d
				AND order_item_id IN ($start_time_results_ids)
			", $pickup_location_id ) );

			if ( ! empty( $location_results ) ) {

				// TODO: remove if statement when WC 3.6 is the minimum version supported {WV 2020-04-22}
				if ( function_exists( 'wc_get_is_pending_statuses' ) ) {
					$order_statuses = array_merge( wc_get_is_paid_statuses(), wc_get_is_pending_statuses(), [ 'on-hold' ] );
				} else {
					$order_statuses = array_merge( wc_get_is_paid_statuses(), [ 'pending', 'on-hold' ] );
				}

				$order_statuses = array_map( static function ( $status ) {
					return "wc-$status";
				}, $order_statuses );

				$order_types = Framework\SV_WC_Helper::get_escaped_string_list( wc_get_order_types() );
				$order_statuses = Framework\SV_WC_Helper::get_escaped_string_list( $order_statuses );
				$location_results = Framework\SV_WC_Helper::get_escaped_id_list( $location_results );

				// query by order status
				$order_status_results = $wpdb->get_col( "
					SELECT order_item_id
					FROM {$wpdb->prefix}posts AS posts
					INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON ( posts.ID = order_items.order_id AND order_items.order_item_type = 'shipping' )
					WHERE posts.post_type IN ($order_types)
					AND posts.post_status IN ($order_statuses)
					AND order_items.order_item_id IN ($location_results)
				" );

				/* @var array start times, indexed by order item ID */
				$appointment_start_times = [];

				foreach ( $start_time_results as $result ) {
					$appointment_start_times[ $result['order_item_id'] ] = $result['meta_value'];
				}

				foreach ( $order_status_results as $result ) {

					if ( ! empty ( $start_time = $appointment_start_times[ $result ] ) ) {

						if ( ! isset( $appointments_count_per_start_time[ $start_time ] ) ) {
							$appointments_count_per_start_time[ $start_time ] = 0;
						}

						$appointments_count_per_start_time[ $start_time ] ++;
					}
				}
			}
		}

		ksort( $appointments_count_per_start_time );

		return $appointments_count_per_start_time;
	}


	/**
	 * Determines whether the appointment defined by the given start date and end date
	 * is available considering the pickup location's settings.
	 *
	 * TODO: consider splitting this method in two {WV 2020-04-23}
	 *  - one that accepts $pickup_location, $appointment_duration, $start_date, $end_date for fixed (not anytime) appointment times
	 *  - one that accepts $pickup_location, $order_created_date, $appointment_date for anytime appointment times
	 *
	 * @since 2.8.0
	 *
	 * @param \DateTime $calendar_day used to calculate the first available pickup time for anytime appointments
	 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location the pickup location object
	 * @param int|null $appointment_duration the duration of an appointment in this pickup location (unless "anytime" is used)
	 * @param \DateTime $start_date the selected appointment start time
	 * @param \DateTime $end_date the selected appointment end time
	 * @return bool
	 */
	public function is_appointment_time_available( $calendar_day, $pickup_location, $appointment_duration, $start_date, $end_date ) {

		$is_available_appointment_time = false;

		if ( $pickup_location->get_appointments()->is_anytime_appointments_enabled() ) {

			$shipping_method = wc_local_pickup_plus_shipping_method();

			if ( $start_date >= $pickup_location->get_appointments()->get_first_available_pickup_time( $calendar_day ) ) {

				if ( 'required' === $shipping_method->pickup_appointments_mode() && $shipping_method->is_default_appointment_limits( 'limited' ) ) {

					$appointments_count = $this->get_appointments_count_by_time_slot(
						$pickup_location->get_id(),
						( clone $start_date )->setTime( 0, 0, 0 )->getTimestamp(),
						$end_date->getTimestamp()
					);

					$is_available_appointment_time = array_sum( $appointments_count ) < $shipping_method->get_default_appointment_limits_max_customers();

				} else {

					$is_available_appointment_time = true;
				}
			}

		} else {

			$appointment_ranges = [];
			$current_start_date = null;
			$current_end_date   = null;

			// group available appointment times into range of times where an appointment can be defined
			foreach ( $pickup_location->get_appointments()->get_available_times( $start_date ) as $appointment_start ) {

				try {
					$appointment_end = ( clone $appointment_start )->add( new \DateInterval( sprintf( 'PT%dS', $appointment_duration ) ) );
				} catch ( \Exception $e ) {
					continue;
				}

				// start a new range if one is not defined or the current appointment start time is greater than the current range's end time
				if ( null === $current_start_date || null === $current_end_date || $appointment_start > $current_end_date ) {

					$appointment_ranges[ $appointment_start->getTimestamp() ] = $appointment_end->getTimestamp();
					$current_start_date = $appointment_start;
					$current_end_date   = $appointment_end;

				// expand the current range to include the current appointment end time
				} else {

					$appointment_ranges[ $current_start_date->getTimestamp() ] = $appointment_end->getTimestamp();
					$current_end_date = $appointment_end;
				}
			}

			// the appointment time is available if the start and end times are contained in one of the appointment ranges
			foreach ( $appointment_ranges as $range_start => $range_end ) {
				if ( $start_date->getTimestamp() >= $range_start && $end_date->getTimestamp() <= $range_end ) {
					$is_available_appointment_time = true;
					break;
				}
			}
		}

		return $is_available_appointment_time;
	}


}
