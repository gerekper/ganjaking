<?php
/**
 * Class YITH_WCBK_Date_Helper
 * do you need help with dates? Use me!
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Date_Helper' ) ) {
	/**
	 * Class YITH_WCBK_Date_Helper
	 * do you need help with dates? Use me!
	 */
	class YITH_WCBK_Date_Helper {

		use YITH_WCBK_Singleton_Trait;

		/**
		 * Non-localized list of days
		 *
		 * @var array Day names
		 */
		private $days = array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );

		/**
		 * Non-localized list of months
		 *
		 * @var array Month names
		 */
		private $months = array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );

		/**
		 * Check if one date range intersects other date range
		 *
		 * @param int $start1 The start timestamp of the first range.
		 * @param int $end1   The end timestamp of the first range.
		 * @param int $start2 The start timestamp of the second range.
		 * @param int $end2   The end timestamp of the second range.
		 *
		 * @return bool
		 */
		public function check_intersect_dates( $start1, $end1, $start2, $end2 ) {
			return $start1 <= $end2 && $end1 >= $start2;
		}


		/**
		 * Get array of days or months
		 *
		 * @param string $type  The type ('days' or 'months').
		 * @param bool   $short Is this short? Set true to crop the name to 3 letters (Jan, Feb, Mar, ...); set false to not crop.
		 *
		 * @return array
		 */
		public function get_names_array( $type, $short = false ) {
			$allowed = array( 'days', 'months' );
			if ( ! in_array( $type, $allowed, true ) ) {
				return array();
			}

			$ret = 'days' === $type ? $this->days : $this->months;

			if ( $short ) {
				$length       = 3;
				$array_count  = count( $ret );
				$zero_array   = array_fill( 0, $array_count, 0 );
				$lenght_array = array_fill( 0, $array_count, $length );
				$ret          = array_map( 'substr', $ret, $zero_array, $lenght_array );
			}

			return $ret;
		}

		/**
		 * Get day name.
		 *
		 * @param int  $value The number of the day 1: Monday | 2:Tuesday.
		 * @param bool $short Set true to crop the name to 3 letters (Mon, Tue, Wed, ...); set to false to not crop.
		 *
		 * @return string
		 */
		public function get_day_name( $value, $short = false ) {
			$value = absint( $value - 1 ) % 7;
			$days  = $this->get_names_array( 'days', $short );

			return $days[ $value ] ?? '';
		}

		/** Get month name.
		 *
		 * @param int  $value the number of the day 1: January | 2:February.
		 * @param bool $short set true to crop the name to 3 letters (Jan, Feb, Mar, ...); set to false to not crop.
		 *
		 * @return string
		 */
		public function get_month_name( $value, $short = false ) {
			$value  = absint( $value - 1 ) % 12;
			$months = $this->get_names_array( 'months', $short );

			return $months[ $value ] ?? '';
		}

		/**
		 * Get timestamp of first day of month searched
		 *
		 * @param int        $timestamp             The timestamp.
		 * @param int|string $month                 The month number.
		 * @param bool       $include_current_month Set tru to include the current month.
		 *
		 * @return int
		 */
		public function get_first_month_from_date( $timestamp, $month, $include_current_month = false ) {
			if ( is_numeric( $month ) ) {
				$month = $this->get_month_name( $month );
			}

			if ( ! $include_current_month ) {
				$timestamp = strtotime( '+1 month', $timestamp );
			}
			$first_of_searched_month = strtotime( 'first day of ' . $month, $timestamp );
			$first_day_of_timestamp  = strtotime( 'first day', $timestamp );

			if ( $first_day_of_timestamp > $first_of_searched_month ) {
				$first_of_searched_month = strtotime( '+ 1 year', $first_of_searched_month );
			}

			return $first_of_searched_month;
		}

		/**
		 * Get timestamp of first day searched
		 * for example can be used to search the next monday since a date
		 *
		 * @param int        $timestamp           The timestamp.
		 * @param int|string $day                 The day number.
		 * @param bool       $include_current_day Set tru to include the current day.
		 *
		 * @return int
		 */
		public function get_first_day_from_date( $timestamp, $day, $include_current_day = false ) {
			if ( is_numeric( $day ) ) {
				$day = $this->get_day_name( $day );
			}
			if ( $include_current_day ) {
				$timestamp -= DAY_IN_SECONDS;
			}

			$first_day = strtotime( 'next ' . $day, $timestamp );

			return $first_day;
		}


		/**
		 * Get timestamp for searched day in this week
		 *
		 * @param int $timestamp The timestamp.
		 * @param int $day       The day number.
		 *
		 * @return int
		 */
		public function get_day_on_this_week( $timestamp, $day ) {
			$day_number     = gmdate( 'N', $timestamp );
			$day_difference = $day - $day_number;

			return strtotime( $day_difference . ' days midnight', $timestamp );
		}

		/**
		 * Get timestamp of the next number week of the year
		 *
		 * @param int  $timestamp            The timestamp.
		 * @param int  $week_number          The week number.
		 * @param bool $include_current_week Set tru to include the current week.
		 *
		 * @return int
		 */
		public function get_first_week_from_date( $timestamp, $week_number, $include_current_week = false ) {
			$current_week_number         = absint( gmdate( 'W', $timestamp ) );
			$year_of_current_week_number = absint( gmdate( 'o', $timestamp ) );
			$week_number                 = absint( $week_number );

			if ( $current_week_number > $week_number || ( $current_week_number === $week_number && ! $include_current_week ) ) {
				$year_of_current_week_number ++;
			}

			if ( $week_number < 10 ) {
				$week_number = '0' . $week_number;
			}

			$operator = $year_of_current_week_number . 'W' . $week_number;
			$first    = strtotime( $operator );

			return $first;
		}

		/**
		 * Retrieve the time sum
		 *
		 * @param int        $time     The timestamp.
		 * @param int        $number   The number to be summed.
		 * @param string     $unit     The unit of the number.
		 * @param bool|false $midnight Set to true to return a midnight timestamp.
		 *
		 * @return int
		 */
		public function get_time_sum( $time, $number = 0, $unit = 'day', $midnight = false ) {
			$sum = $time;

			$params = $midnight ? 'midnight' : '';

			$operator   = $number >= 0 ? '+' : '-';
			$abs_number = abs( $number );

			switch ( $unit ) {
				case 'month':
					$sum = strtotime( $operator . $abs_number . ' months ' . $params, $time );
					break;
				case 'day':
					$sum = strtotime( $operator . $abs_number . ' days ' . $params, $time );
					break;
				case 'hour':
					$sum = $time + ( $number * 60 * 60 );
					break;
				case 'minute':
					$sum = $time + ( $number * 60 );
					break;
				case 'seconds':
					$sum = $time + $number;
					break;
			}

			return $sum;
		}

		/**
		 * Retrieve the time difference
		 *
		 * @param int    $timestamp1 The first timestamp.
		 * @param int    $timestamp2 The second timestamp.
		 * @param string $return     The return type.
		 *
		 * @return bool|DateInterval|int
		 */
		public function get_time_diff( $timestamp1, $timestamp2, $return = 'interval' ) {
			$date1 = new DateTime();
			$date2 = new DateTime();
			$date1->setTimestamp( $timestamp1 );
			$date2->setTimestamp( $timestamp2 );

			$interval = date_diff( $date1, $date2 );

			switch ( $return ) {
				case 'year':
				case 'y':
					$value = $interval->y;
					break;
				case 'month':
				case 'm':
					$value = $interval->y * 12 + $interval->m;
					break;
				case 'day':
				case 'd':
					$value = $interval->days;
					break;
				case 'hour':
				case 'h':
					$value = $interval->days * 24 + $interval->h;
					break;
				case 'minute':
				case 'i':
					$value = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;
					break;
				case 'seconds':
				case 's':
					$value = $interval->days * DAY_IN_SECONDS + $interval->h * HOUR_IN_SECONDS + $interval->i * MINUTE_IN_SECONDS + $interval->s;
					break;
				case 'interval':
				default:
					$value = $interval;
					break;
			}

			return apply_filters( 'yith_wcbk_date_helper_time_diff', $value, $timestamp1, $timestamp2, $return );
		}

		/**
		 * Create a numeric range array
		 *
		 * @param int $from The start number.
		 * @param int $to   The end number.
		 * @param int $max  The maximum value.
		 * @param int $min  The minimum value.
		 *
		 * @return array
		 */
		public function create_numeric_range( $from, $to, $max = 0, $min = 0 ) {
			if ( ! $max ) {
				$from = min( $from, $to );
				$to   = max( $from, $to );
				$from = max( $from, $min );

				return range( $from, $to );
			} else {
				if ( $from <= $to ) {
					$from = max( $from, $min );
					$to   = min( $to, $max );

					return range( $from, $to );
				} else {
					$range1 = range( $from, $max );
					$range2 = range( $min, $to );

					return array_unique( array_merge( $range1, $range2 ) );
				}
			}
		}

		/**
		 * Check a date inclusion in a time range
		 *
		 * @param string    $range_type The range type.
		 * @param string    $range_from The range from.
		 * @param string    $range_to   The range to.
		 * @param int       $date_from  The date from.
		 * @param int       $date_to    The date to.
		 * @param bool|true $intersect  Set true to intersect dates.
		 *
		 * @return bool
		 */
		public function check_date_inclusion_in_range( $range_type, $range_from, $range_to, $date_from, $date_to, $intersect = true ) {
			switch ( $range_type ) {
				case 'custom':
				case 'specific':
					$range_from = strtotime( $range_from );
					$range_to   = strtotime( $range_to . ' + 1 day' ) - 1;

					if ( $intersect ) {
						if ( $this->check_intersect_dates( $range_from, $range_to, $date_from, $date_to ) ) {
							return true;
						}
					} else {
						if ( $range_from <= $date_from && $range_to >= $date_to ) {
							return true;
						}
					}
					break;
				case 'generic':
					if ( '01-01' === $range_from && '12-31' === $range_to ) {
						return true;
					}

					if ( $this->get_time_diff( $date_from, $date_to, 'y' ) > 0 ) {
						return false;
					}

					$date_from_string = gmdate( 'm-d', $date_from );
					$date_to_string   = gmdate( 'm-d', $date_to );

					if ( $range_from <= $range_to ) {
						if ( $intersect ) {
							if ( $date_from_string <= $date_to_string ) {
								if ( $range_from <= $date_to_string && $range_to >= $date_from_string ) {
									return true;
								}
							} else {
								if ( $range_from <= $date_to_string ) {
									return true;
								}
							}
						} else {
							if ( $date_from_string <= $date_to_string ) {
								if ( $range_from <= $date_from_string && $range_to >= $date_to_string ) {
									return true;
								}
							}
						}
					} else {
						if ( $intersect ) {
							if ( $date_from_string <= $date_to_string ) {
								if ( $range_from <= $date_to_string || $range_to >= $date_from_string ) {
									return true;
								}
							} else {
								return true;
							}
						} else {
							if ( $date_from_string <= $date_to_string ) {
								if ( $range_from <= $date_from_string || $range_to >= $date_to_string ) {
									return true;
								}
							} else {
								if ( $range_from <= $date_from_string && $range_to >= $date_to_string ) {
									return true;
								}
							}
						}
					}
					break;
				case 'month':
					$range_from = absint( $range_from );
					$range_to   = absint( $range_to );

					$date_from = strtotime( gmdate( 'Y-m-01', $date_from ) );
					$date_to   = strtotime( gmdate( 'Y-m-01', $date_to ) );

					if ( $this->get_time_diff( $date_from, $date_to, 'y' ) > 0 ) {
						$months_request_range = range( 1, 12 );
					} else {
						$request_month_from   = gmdate( 'm', $date_from );
						$request_month_to     = gmdate( 'm', $date_to );
						$months_request_range = $this->create_numeric_range( $request_month_from, $request_month_to, 12, 1 );
					}

					$months_bookable_range = $this->create_numeric_range( $range_from, $range_to, 12, 1 );
					$months_intersect      = array_intersect( $months_request_range, $months_bookable_range );

					$is_included  = count( $months_intersect ) === count( $months_request_range );
					$is_intersect = count( $months_intersect ) > 0;

					return $intersect ? $is_intersect : $is_included;
				case 'week':
					// There are 53 weeks in one year.
					$range_from = absint( $range_from );
					$range_to   = absint( $range_to );

					$date_from = $this->get_day_on_this_week( $date_from, 1 );
					$date_to   = $this->get_day_on_this_week( $date_to, 1 );

					if ( $this->get_time_diff( $date_from, $date_to, 'y' ) > 0 ) {
						$weeks_request_range = range( 1, 53 );
					} else {
						$request_week_from   = gmdate( 'W', $date_from );
						$request_week_to     = gmdate( 'W', $date_to );
						$weeks_request_range = $this->create_numeric_range( $request_week_from, $request_week_to, 53, 1 );
					}

					$weeks_bookable_range = $this->create_numeric_range( $range_from, $range_to, 53, 1 );
					$weeks_intersect      = array_intersect( $weeks_request_range, $weeks_bookable_range );

					$is_included  = count( $weeks_intersect ) === count( $weeks_request_range );
					$is_intersect = count( $weeks_intersect ) > 0;

					return $intersect ? $is_intersect : $is_included;
				case 'day':
					$range_from = absint( $range_from );
					$range_to   = absint( $range_to );

					$date_from = strtotime( 'midnight', $date_from );
					$date_to   = strtotime( 'midnight', $date_to );

					if ( $this->get_time_diff( $date_from, $date_to, 'day' ) > 6 ) {
						$days_request_range = range( 1, 7 );
					} else {
						$request_day_from   = gmdate( 'N', $date_from );
						$request_day_to     = gmdate( 'N', $date_to );
						$days_request_range = $this->create_numeric_range( $request_day_from, $request_day_to, 7, 1 );
					}

					$days_bookable_range = $this->create_numeric_range( $range_from, $range_to, 7, 1 );
					$days_intersect      = array_intersect( $days_request_range, $days_bookable_range );

					$is_included  = count( $days_intersect ) === count( $days_request_range );
					$is_intersect = count( $days_intersect ) > 0;

					return $intersect ? $is_intersect : $is_included;
				case 'time':
					if ( '00:00' === $range_to ) {
						$range_to = '24:00';
					}
					$range_from = strtotime( $range_from, $date_from );
					$range_to   = strtotime( $range_to, $date_to ) - 1;

					if ( $range_to < $range_from && ( $range_to + DAY_IN_SECONDS ) > $range_from ) {
						// Example from 17:00 to 08:00.
						$range_to_tomorrow    = $range_to + DAY_IN_SECONDS;
						$range_from_yesterday = $range_from - DAY_IN_SECONDS;

						if ( $intersect ) {
							return $this->check_intersect_dates( $range_from, $range_to_tomorrow, $date_from, $date_to ) || $this->check_intersect_dates( $range_from_yesterday, $range_to, $date_from, $date_to );
						} else {
							return $range_from <= $date_from && $range_to_tomorrow >= $date_to || $range_from_yesterday <= $date_from && $range_to >= $date_to;
						}
					} else {
						if ( $intersect ) {
							return $this->check_intersect_dates( $range_from, $range_to, $date_from, $date_to );
						} else {
							return $range_from <= $date_from && $range_to >= $date_to;
						}
					}
					break;
			}

			return false;
		}


		/**
		 * Return a formatted interval.
		 *
		 * @param DateInterval $interval The interval.
		 * @param array        $args     Arguments.
		 *
		 * @since 3.0.0
		 */
		public function format_interval( $interval, $args = array() ) {
			$default_args = array(
				'minimum_unit'  => '',
				'at_least_deep' => 1,
				'max_deep'      => false,
				'separator'     => ', ',
			);
			$args         = wp_parse_args( $args, $default_args );
			$units        = array(
				'y' => 'year',
				'm' => 'month',
				'd' => 'day',
				'h' => 'hour',
				'i' => 'minute',
				's' => 'second',
			);

			list( $minimum_unit, $at_least_deep, $max_deep, $separator ) = yith_plugin_fw_extract( $args, 'minimum_unit', 'at_least_deep', 'max_deep', 'separator' );

			$formatted       = array();
			$printed         = 0;
			$minimum_reached = false;

			foreach ( $units as $unit_char => $unit ) {
				if ( ! empty( $interval->$unit_char ) ) {
					$formatted[] = yith_wcbk_format_duration( $interval->$unit_char, $unit );
					$printed ++;
				}

				if ( $minimum_unit === $unit ) {
					$minimum_reached = true;
				}

				if ( $minimum_reached && $at_least_deep <= $printed ) {
					break;
				}

				if ( $max_deep && $max_deep <= $printed ) {
					break;
				}
			}

			return implode( $separator, $formatted );
		}

	}
}

/**
 * Unique access to instance of YITH_WCBK_Date_Helper class
 *
 * @return YITH_WCBK_Date_Helper
 */
function yith_wcbk_date_helper() {
	return YITH_WCBK_Date_Helper::get_instance();
}
