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
 * @copyright   Copyright (c) 2012-2022, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Local_Pickup_Plus\Pickup_Locations\Pickup_Location;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\Local_Pickup_Plus\Appointments\Appointment;
use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;
use SkyVerge\WooCommerce\Local_Pickup_Plus\Appointments\Timezones;

/**
 * Handles appointments data for a specific pickup location it is attached to.
 *
 * @since 2.7.0
 */
class Appointments {


	/** @var \WC_Local_Pickup_Plus_Pickup_Location|null */
	private $pickup_location;


	/**
	 * Pickup location appointments handler.
	 *
	 * @since 2.7.0
	 *
	 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location pickup location object appointments should relate to
	 */
	public function __construct( \WC_Local_Pickup_Plus_Pickup_Location $pickup_location ) {

		$this->pickup_location = $pickup_location;
	}


	/**
	 * Gets the pickup location.
	 *
	 * @since 2.7.0
	 *
	 * @return \WC_Local_Pickup_Plus_Pickup_Location|null
	 */
	private function get_pickup_location() {

		return $this->pickup_location;
	}


	/**
	 * Determines whether appointments do not have a specified duration.
	 *
	 * If "Anytime during open hours" is enabled, appointments can occur any time following the location's business hours and lead time settings.
	 *
	 * TODO in the future, if pickup locations become able to override the global setting, this method should retrieve this flag from a pickup location's meta, and use the global setting for fallback {FN 2019-11-21}
	 *
	 * @since 2.7.0
	 *
	 * @return bool
	 */
	public function is_anytime_appointments_enabled() {

		return wc_local_pickup_plus_shipping_method()->is_anytime_appointments_enabled();
	}


	/**
	 * Gets the duration of an appointment given the
	 *
	 * @since 2.7.0
	 *
	 * TODO in the future, if pickup locations become able to override the global setting, this method should retrieve the duration from a pickup location's meta and use the global setting as default fallback {FN 2019-11-21}
	 *
	 * @param \DateTime $date datetime object to get duration for that day
	 * @return int duration as a partial timestamp
	 */
	public function get_appointment_duration( \DateTime $date ) {

		if ( $this->is_anytime_appointments_enabled() ) {

			$start_date = $this->get_first_available_pickup_time( $date );
			$start_time = $end_time = $start_date->getTimestamp();

			if ( $pickup_location = $this->get_pickup_location() ) {

				$raw_schedule = $pickup_location->get_business_hours()->get_schedule();

				if ( ! empty( $raw_schedule[ $start_date->format( 'w' ) ] ) ) {

					$end_hours = array_pop( $raw_schedule[ $start_date->format( 'w' ) ] );

					if ( ! empty( $end_hours ) && is_numeric( $end_hours ) ) {
						$end_time += $end_hours;
					}
				}
			}

			$duration = max( 0, $end_time - $start_time );

		} else {

			$duration = wc_local_pickup_plus_shipping_method()->get_default_appointment_duration();
		}

		return $duration;
	}


	/**
	 * Calculates the first available pickup time for this location.
	 *
	 * Takes into consideration the pickup location's:
	 * - @see \WC_Local_Pickup_Plus_Schedule_Adjustment lead time
	 * - @see \WC_Local_Pickup_Plus_Business_Hours business hours
	 * - @see \WC_Local_Pickup_Plus_Public_Holidays holidays calendar
	 *
	 * @since 2.7.0
	 *
	 * @param \DateTime $start_date the requested date for which day we should display the first available appointment slot
	 * @return \DateTime first available pickup time in the timezone of the supplied start date
	 */
	public function get_first_available_pickup_time( \DateTime $start_date ) {

		$pickup_location = $this->get_pickup_location();

		if ( empty( $pickup_location ) ) {
			return $start_date;
		}

		// maybe convert start date to the location timezone because business hours are in the location timezone
		if ( ! Timezones::is_same_timezone( $pickup_location->get_address()->get_timezone(), $start_date->getTimezone() ) ) {
			$first_pickup_time   = ( clone $start_date )->setTimezone( $pickup_location->get_address()->get_timezone() );
			$convert_to_timezone = $start_date->getTimezone();
		} else {
			$first_pickup_time   = clone $start_date;
			$convert_to_timezone = false;
		}

		if ( $pickup_location->has_pickup_lead_time() ) {

			$lead_time             = $pickup_location->get_pickup_lead_time()->in_seconds();
			$lead_time_calculation = wc_local_pickup_plus_shipping_method()->get_lead_time_calculation();

			if ( 'calendar_days' === $lead_time_calculation ) {

				// count calendar days, regardless of business hours and holidays
				try {

					$lead_time_interval = new \DateInterval( "PT${lead_time}S" );

					$first_pickup_time->add( $lead_time_interval );

				} catch ( \Exception $e ) {

					wc_local_pickup_plus()->log( sprintf( 'Could not calculate first available pickup time with lead time %1$: %2$s', $pickup_location->get_pickup_lead_time()->in_seconds(), $e->getMessage() ) );
				}

			} else {

				// count only open hours, pausing outside business hours and on holidays
				$remaining_lead_time = $lead_time;
				$schedules           = $pickup_location->get_business_hours()->get_schedule();

				while ( $remaining_lead_time > 0 ) {

					$week_day         = $first_pickup_time->format( 'w' );
					$day_start        = ( clone $first_pickup_time )->setTime( 0, 0, 0 );
					$seconds_since_00 = $first_pickup_time->getTimestamp() - $day_start->getTimestamp();

					// not a holiday and has business hours set
					if ( ! empty( $schedules[ $week_day ] ) && ! $pickup_location->get_public_holidays()->is_public_holiday( $first_pickup_time ) ) {

						// get schedules for the day of the week
						$day_schedules = $schedules[ $week_day ];

						foreach ( $day_schedules as $opening_time => $closing_time ) {

							// calculate remaining business hours in seconds
							$remaining_business_seconds = $closing_time - max( $opening_time, $seconds_since_00 );

							if ( $remaining_business_seconds > 0 ) {

								if ( $remaining_business_seconds < $remaining_lead_time ) {

									// subtract remaining business hours from the remaining lead time
									$remaining_lead_time -= $remaining_business_seconds;

								} else {

									try {

										// add opening time if brand new day
										if ( $seconds_since_00 === 0 ) {
											$first_pickup_time->add( new \DateInterval( "PT${opening_time}S" ) );
										}

										// when a customer places an order prior to the opening of the shop on the same day, the lead time of open hours should be respected
										$is_start_date_today   = $first_pickup_time->format( 'mdY' ) === ( new \DateTime( 'now', $first_pickup_time->getTimezone() ) )->format( 'mdY' );
										$is_store_not_open_yet = $seconds_since_00 < $opening_time;

										if ( $is_start_date_today && $is_store_not_open_yet ) {
											$first_pickup_time->setTime( 0 ,0, 0 );
											$first_pickup_time->add( new \DateInterval( "PT${opening_time}S" ) );
										}

										// add the remaining lead time
										$first_pickup_time->add( new \DateInterval( "PT${remaining_lead_time}S" ) );

									} catch ( \Exception $e ) {

										continue;
									}

									break 2;
								}
							}
						}
					}

					try {

						// jump to next day at 0:00
						$first_pickup_time = $day_start->add( new \DateInterval( 'P1D' ) );

					} catch ( \Exception $e ) {

						break;
					}
				}
			}
		}

		// convert first pickup date back to the original timezone
		if ( $convert_to_timezone ) {
			$first_pickup_time->setTimezone( $convert_to_timezone );
		}

		return $first_pickup_time;
	}


	/**
	 * Gets an array of available pickup times for this location on a given date.
	 *
	 * Takes into consideration the appointment duration and the pickup location's:
	 * - @see \WC_Local_Pickup_Plus_Schedule_Adjustment lead time
	 * - @see \WC_Local_Pickup_Plus_Schedule_Adjustment deadline
	 * - @see \WC_Local_Pickup_Plus_Business_Hours business hours
	 * - @see \WC_Local_Pickup_Plus_Public_Holidays holidays calendar
	 *
	 * @since 2.7.0
	 *
	 * @param \DateTime $which_date start of the day for which we want to get available times
	 * @return \DateTime[] in the timezone of the supplied start date
	 */
	public function get_available_times( \DateTime $which_date ) {

		$available_times = [];

		if ( $pickup_location = $this->get_pickup_location() ) {

			// clone the given date to avoid changing the original object and set date to start of day
			$date = ( clone $which_date )->setTime( 0, 0, 0 );

			$shipping_method   = wc_local_pickup_plus_shipping_method();
			$location_timezone = $pickup_location->get_address()->get_timezone();
			$duration          = $shipping_method->get_default_appointment_duration();

			try {

				// get the first possible pickup time from now (now is the start date for the lead time calculation)
				$possible_first_pickup_time = $this->get_first_available_pickup_time( new \DateTime( 'now', $location_timezone ) );
				$first_pickup_date          = ( clone $possible_first_pickup_time )->setTime( 0, 0, 0 );

				$duration_interval = new \DateInterval( "PT${duration}S" );

				$deadline_last_pickup_time = false;

				if ( $pickup_location->has_pickup_deadline() ) {
					$deadline                  = $pickup_location->get_pickup_deadline()->in_seconds();
					$deadline_last_pickup_time = ( new \DateTime( 'now', $location_timezone ) )->add( new \DateInterval( "PT${deadline}S" ) );
					$deadline_last_pickup_time = $deadline_last_pickup_time->sub( $duration_interval );
				}

			} catch ( \Exception $e ) {

				return [];
			}

			if ( $date->format( 'Y-m-d' ) === $first_pickup_date->format( 'Y-m-d' ) ) {
				// date is the first available date, first time has to consider lead time
				$first_pickup_time = $possible_first_pickup_time;
			} elseif ( $date > $first_pickup_date ) {
				// date is after the first available date, first time will be the opening time
				$first_pickup_time = $date;
			} else {
				// date is before the first available date, no pickup times are available
				$first_pickup_time = false;
			}

			// given date is not before lead time or after the deadline
			if ( $first_pickup_time && ( ! $deadline_last_pickup_time || $date < $deadline_last_pickup_time ) ) {

				$schedules = $pickup_location->get_business_hours()->get_schedule();
				$week_day  = $date->format( 'w' );

				// not a holiday and has business hours set
				if ( ! empty( $schedules[ $week_day ] ) && ! $pickup_location->get_public_holidays()->is_public_holiday( $date ) ) {

					// get schedules for the day of the week
					$day_schedules = $schedules[ $week_day ];

					foreach ( $day_schedules as $opening_time_offset => $closing_time_offset ) {

						try {
							$opening_time = ( clone $date )->add( new \DateInterval( "PT${opening_time_offset}S" ) );
							$closing_time = ( clone $date )->add( new \DateInterval( "PT${closing_time_offset}S" ) );
						} catch ( \Exception $e ) {
							continue;
						}

						$next_pickup_time = $opening_time;
						$last_pickup_time = ( clone $closing_time )->sub( $duration_interval );

						// check whether next pickup time is at most the last possible pickup time for this range and occurs before the deadline, if a deadline is set
						while ( $next_pickup_time <= $last_pickup_time && ( ! $deadline_last_pickup_time || $next_pickup_time <= $deadline_last_pickup_time ) ) {

							if ( $next_pickup_time >= $first_pickup_time ) {
								$available_times[] = $next_pickup_time;
							}

							// add duration in seconds
							$next_pickup_time = ( clone $next_pickup_time )->add( $duration_interval );
						}
					}
				}
			}

			if ( ! empty( $available_times ) && 'required' === $shipping_method->pickup_appointments_mode() && $shipping_method->is_default_appointment_limits( 'limited' ) ) {

				// search range is the period of time between first available pickup time and last available pickup time adjusted by appointment duration
				$end_date   = end( $available_times );
				$start_date = reset( $available_times );

				$start_date = ( clone $start_date );
				$end_date   = ( clone $end_date )->modify( "+ {$duration} seconds" );

				// get number of scheduled appointments organized by their start time
				$scheduled_appointments = wc_local_pickup_plus()->get_appointments_instance()->get_appointments_count_by_time_slot( $pickup_location->get_id(), $start_date->getTimestamp(), $end_date->getTimestamp() );

				// get the max number of appointments that can be scheduled for a given pickup time
				$max_appointments = $shipping_method->get_default_appointment_limits_max_customers();

				$available_times = $this->filter_available_times( $available_times, $scheduled_appointments, $max_appointments, $duration );
			}
		}

		return $available_times;
	}


	/**
	 * Removes pickup times that already have the allowed number of appointments scheduled.
	 *
	 * @since 2.8.0
	 *
	 * @param \DateTime[] $available_times available pickup times for this pickup location
	 * @param array $scheduled_appointments number of scheduled appointments organized by their start time
	 * @param int $max_appointments max number of appointments that can be scheduled for a given pickup time
	 * @param int $appointment_duration appointment duration in seconds
	 * @return \DateTime[]
	 */
	private function filter_available_times( $available_times, $scheduled_appointments, $max_appointments, $appointment_duration ) {

		// use an iterator to move through the array of scheduled appointments as we loop over the available pickup times
		$appointments_iterator = ( new \ArrayObject( $scheduled_appointments ) )->getIterator();
		$filtered_times        = [];

		foreach ( $available_times as $pickup_time ) {

			$number_of_appointments = 0;

			// continue checking scheduled appointments from the previous position in the array
			// check scheduled appointments that occur before or during the current pickup time
			while ( $appointments_iterator->key() && ( $appointments_iterator->key() - $pickup_time->getTimestamp() < $appointment_duration ) ) {

				// only count scheduled appointments that occur during the current pickup time
				if (  $appointments_iterator->key() >= $pickup_time->getTimestamp() ) {
					$number_of_appointments += $appointments_iterator->current();
				}

				$appointments_iterator->next();
			}

			// keep the pickup time if the current number of scheduled appointments is less than the max allowed
			if ( $number_of_appointments < $max_appointments ) {
				$filtered_times[] = $pickup_time;
			}
		}

		return $filtered_times;
	}


	/**
	 * Returns the schedule minimum available time based on lead time.
	 *
	 * In 2.7.0, extracted from {@see \WC_Local_Pickup_Plus_Pickup_Location_Package_Field}
	 *
	 * @since 2.3.5
	 *
	 * @param \DateTime $chosen_time the chosen pickup date
	 * @return null|int the minimum time (as in hours in seconds) or null if no minimum
	 */
	public function get_schedule_minimum_hours( $chosen_time ) {

		$minimum_hours = null;

		if ( $pickup_location = $this->get_pickup_location() ) {

			try {

				$first_pickup_time = $pickup_location->get_appointments()->get_first_available_pickup_time( new \DateTime( 'now', $pickup_location->get_address()->get_timezone() ) );

			} catch ( \Exception $e ) {

				wc_local_pickup_plus()->log( sprintf( 'Could not calculate first available pickup time with lead time %1$: %2$s', $pickup_location->get_pickup_lead_time()->in_seconds(), $e->getMessage() ) );
				return $minimum_hours;
			}

			// is it the same day?
			if ( $first_pickup_time && $first_pickup_time->format( 'Y-m-d' ) === $chosen_time->format( 'Y-m-d' ) ) {

				$minimum_hours = $first_pickup_time->getTimestamp() - $chosen_time->getTimestamp();

				// LPP schedule system advances hours by quarters, so we round the minutes to the nearest quarter
				$quarter       = 15 * MINUTE_IN_SECONDS; // 15 minutes in seconds
				$minimum_hours = ( ceil( $minimum_hours / $quarter ) * $quarter );
			}
		}

		return $minimum_hours;
	}


}
