<?php

/**
 * Class that parses and returns rules for bookable products.
 */
class WC_Product_Booking_Rule_Manager {

	/**
	 * Get a range and put value inside each day
	 *
	 * @param  string $from
	 * @param  string $to
	 * @param  mixed  $value
	 * @return array
	 */
	private static function get_custom_range( $from, $to, $value ) {
		$availability = array();
		$from_date    = strtotime( $from );
		$to_date      = strtotime( $to );

		if ( empty( $to ) || empty( $from ) || $to_date < $from_date ) {
			return;
		}
		// We have at least 1 day, even if from_date == to_date
		$number_of_days = 1 + ( $to_date - $from_date ) / 60 / 60 / 24;

		for ( $i = 0; $i < $number_of_days; $i ++ ) {
			$year  = date( 'Y', strtotime( "+{$i} days", $from_date ) );
			$month = date( 'n', strtotime( "+{$i} days", $from_date ) );
			$day   = date( 'j', strtotime( "+{$i} days", $from_date ) );

			$availability[ $year ][ $month ][ $day ] = $value;
		}

		return $availability;
	}

	/**
	 * Get a range and put value inside each day
	 *
	 * Generates availability data where time range starts on first date on the beginning
	 * time and ends on the last date at the end time.
	 *
	 * @since 1.13.0
	 *
	 * @param  string $from
	 * @param  string $to
	 * @param  mixed  $value
	 * @return array
	 */
	private static function get_custom_datetime_range( $from_day, $to_day, $time_range ) {
		$availability = array();
		$from_date    = strtotime( $from_day );
		$to_date      = strtotime( $to_day );

		if ( empty( $to_day ) || empty( $from_day ) || $to_date < $from_date ) {
			return;
		}
		// We have at least 1 day, even if from_date == to_date
		$number_of_days = 1 + ( $to_date - $from_date ) / 60 / 60 / 24;

		for ( $i = 0; $i < $number_of_days; $i ++ ) {
			$year  = date( 'Y', strtotime( "+{$i} days", $from_date ) );
			$month = date( 'n', strtotime( "+{$i} days", $from_date ) );
			$day   = date( 'j', strtotime( "+{$i} days", $from_date ) );

			// First day starts at start time, other days start at midnight.
			if ( 0 === $i ) {
				$start = $time_range['from'];
			} else {
				$start = '00:00';
			}

			// Last day ends at end time, other days end at midnight.
			if ( $number_of_days - 1 === $i ) {
				$end = $time_range['to'];
			} else {
				$end = '24:00';
			}

			$time_range_for_day = array(
				'from' => $start,
				'to'   => $end,
				'rule' => $time_range['rule'],
			);

			$availability[ $year ][ $month ][ $day ] = $time_range_for_day;
		}

		return $availability;
	}

	/**
	 * Get a range and put value inside each day
	 *
	 * @param  string $from
	 * @param  string $to
	 * @param  mixed  $value
	 * @return array
	 */
	private static function get_months_range( $from, $to, $value ) {
		$months = array();
		$diff   = $to - $from;
		$diff   = ( $diff < 0 ) ? 12 + $diff : $diff;
		$month  = $from;

		for ( $i = 0; $i <= $diff; $i ++ ) {
			$months[ $month ] = $value;

			$month ++;

			if ( $month > 52 ) {
				$month = 1;
			}
		}

		return $months;
	}

	/**
	 * Get a range and put value inside each day
	 *
	 * @param  string $from
	 * @param  string $to
	 * @param  mixed  $value
	 * @return array
	 */
	private static function get_weeks_range( $from, $to, $value ) {
		$weeks = array();
		$diff  = $to - $from;
		$diff  = ( $diff < 0 ) ? 52 + $diff : $diff;
		$week  = $from;

		for ( $i = 0; $i <= $diff; $i ++ ) {
			$weeks[ $week ] = $value;

			$week ++;

			if ( $week > 52 ) {
				$week = 1;
			}
		}

		return $weeks;
	}

	/**
	 * Get a range and put value inside each day
	 *
	 * @param  string $from
	 * @param  string $to
	 * @param  mixed  $value
	 * @return array
	 */
	private static function get_days_range( $from, $to, $value ) {
		$day_of_week = $from;
		$diff        = $to - $from;
		$diff        = ( $diff < 0 ) ? 7 + $diff : $diff;
		$days        = array();

		for ( $i = 0; $i <= $diff; $i ++ ) {
			$days[ $day_of_week ] = $value;

			$day_of_week ++;

			if ( $day_of_week > 7 ) {
				$day_of_week = 1;
			}
		}

		return $days;
	}

	/**
	 * Get a range and put value inside each day
	 *
	 * @param  string $from
	 * @param  string $to
	 * @param  mixed  $value
	 * @return array
	 */
	private static function get_time_range( $from, $to, $value, $day = 0 ) {
		return array(
			'from' => $from,
			'to'   => $to,
			'rule' => $value,
			'day'  => $day,
		);
	}

	/**
	 * Get a time range for a set of custom dates.
	 *
	 * Generates availability data where time range is repeated for each day in range.
	 *
	 * @param  string $from_date
	 * @param  string $to_date
	 * @param  string $from_time
	 * @param  string $to_time
	 * @param  mixed  $value
	 * @return array
	 */
	private static function get_time_range_for_custom_date( $from_date, $to_date, $from_time, $to_time, $value ) {
		$time_range = array(
			'from' => $from_time,
			'to'   => $to_time,
			'rule' => $value,
		);
		return self::get_custom_range( $from_date, $to_date, $time_range );
	}

	/**
	 * Get a time range for a set of custom dates.
	 *
	 * Generates availability data where time range starts on first date on the beginning
	 * time and ends on the last date at the end time.
	 *
	 * @since 1.13.0
	 *
	 * @param  string $from_date
	 * @param  string $to_date
	 * @param  string $from_time
	 * @param  string $to_time
	 * @param  mixed  $value
	 * @return array
	 */
	private static function get_time_range_for_custom_datetime( $from_date, $to_date, $from_time, $to_time, $value ) {
		$time_range = array(
			'from' => $from_time,
			'to'   => $to_time,
			'rule' => $value,
		);
		return self::get_custom_datetime_range( $from_date, $to_date, $time_range );
	}

	/**
	 * Get duration range
	 *
	 * @param  [type] $from
	 * @param  [type] $to
	 * @param  [type] $value
	 * @return [type]
	 */
	private static function get_duration_range( $from, $to, $value ) {
		return array(
			'from' => $from,
			'to'   => $to,
			'rule' => $value,
		);
	}

	/**
	 * Get Persons range
	 *
	 * @param  [type] $from
	 * @param  [type] $to
	 * @param  [type] $value
	 * @return [type]
	 */
	private static function get_persons_range( $from, $to, $value ) {
		return array(
			'from' => $from,
			'to'   => $to,
			'rule' => $value,
		);
	}

	/**
	 * Get blocks range
	 *
	 * @param  [type] $from
	 * @param  [type] $to
	 * @param  [type] $value
	 * @return [type]
	 */
	private static function get_blocks_range( $from, $to, $value ) {
		return array(
			'from' => $from,
			'to'   => $to,
			'rule' => $value,
		);
	}

	private static function get_rrule_range( $from, $to, $rrule, $value ) {
		return array(
			'from'  => $from,
			'to'    => $to,
			'rule'  => $value,
			'rrule' => $rrule,
		);
	}

	/**
	 * Process and return formatted cost rules.
	 *
	 * @param  $rules array
	 * @return array
	 */
	public static function process_cost_rules( $rules ) {
		$costs = array();
		$index = 1;
		// Go through rules
		foreach ( $rules as $key => $fields ) {
			if ( empty( $fields['cost'] ) && empty( $fields['base_cost'] ) && empty( $fields['override_block'] ) ) {
				continue;
			}

			$cost           = apply_filters( 'woocommerce_bookings_process_cost_rules_cost', $fields['cost'], $fields, $key );
			$modifier       = $fields['modifier'];
			$base_cost      = apply_filters( 'woocommerce_bookings_process_cost_rules_base_cost', $fields['base_cost'], $fields, $key );
			$base_modifier  = $fields['base_modifier'];
			$override_block = apply_filters( 'woocommerce_bookings_process_cost_rules_override_block', ( isset( $fields['override_block'] ) ? $fields['override_block'] : '' ), $fields, $key );

			$cost_array = array(
				'base'     => array( $base_modifier, $base_cost ),
				'block'    => array( $modifier, $cost ),
				'override' => $override_block,
			);

			$type_function = self::get_type_function( $fields['type'] );
			if ( 'get_time_range_for_custom_date' === $type_function ) {
				$type_costs = self::$type_function( $fields['from_date'], $fields['to_date'], $fields['from'], $fields['to'], $cost_array );
			} else {
				$type_costs = self::$type_function( $fields['from'], $fields['to'], $cost_array );
			}

			// Ensure day gets specified for time: rules
			if ( strrpos( $fields['type'], 'time:' ) === 0 && 'time:range' !== $fields['type'] ) {
				list( , $day )     = explode( ':', $fields['type'] );
				$type_costs['day'] = absint( $day );
			}

			if ( $type_costs ) {
				$costs[ $index ] = array( $fields['type'], $type_costs );
				$index ++;
			}
		}

		return $costs;
	}

	/**
	 * Returns a function name (for this class) that returns our time or date range
	 *
	 * @param  string $type rule type
	 * @return string       function name
	 */
	public static function get_type_function( $type ) {
		if ( 'time:range' === $type ) {
			return 'get_time_range_for_custom_date';
		}
		if ( 'custom:daterange' === $type ) {
			return 'get_time_range_for_custom_datetime';
		}
		return strrpos( $type, 'time:' ) === 0 ? 'get_time_range' : 'get_' . $type . '_range';
	}

	/**
	 * Split the store availability rule into one or more classic availability rules.
	 *
	 * @param WC_Global_Availability $rule Current Store availability rule.
	 * @param string                 $level Rule level, 'global', 'product', or 'resource.
	 * @param bool                   $hide_past Hide Past rules.
	 *
	 * @return array
	 */
	private static function process_store_availability_rule( WC_Global_Availability $rule, $level, $hide_past ) {

		$rules       = array();
		$start_times = array_filter( explode( ',', $rule->get_from_range() ) );
		$end_times   = array_filter( explode( ',', $rule->get_to_range() ) );

		$unbookable_start_times = array( '00:00' );
		$unbookable_end_times   = array();

		$rrule    = $rule->get_rrule();
		$bookable = $rule->get_bookable();
		if ( $start_times ) {
			foreach ( $start_times as $key => $start_time ) {
				$end_time = $end_times[ $key ];

				$unbookable_end_times[]   = $start_time;
				$unbookable_start_times[] = $end_time;
				if ( $rrule ) {
					$rules[] = array(
						'from'     => $rule->get_from_date() . ' ' . $start_time,
						'to'       => $rule->get_to_date() . ' ' . $end_time,
						'type'     => 'rrule',
						'rrule'    => $rrule,
						'bookable' => $bookable,
					);
				} else {
					$rules[] = array(
						'from_date' => $rule->get_from_date(),
						'to_date'   => $rule->get_to_date(),
						'from'      => $start_time,
						'to'        => $end_time,
						'type'      => 'time:range',
						'bookable'  => $bookable,
					);
				}
			}
			$unbookable_end_times[] = '23:59';

			foreach ( $unbookable_start_times as $key => $start_time ) {
				$end_time = $unbookable_end_times[ $key ];
				if ( $rrule ) {
					$rules[] = array(
						'from'     => $rule->get_from_date() . ' ' . $start_time,
						'to'       => $rule->get_to_date() . ' ' . $end_time,
						'type'     => 'rrule',
						'rrule'    => $rrule,
						'bookable' => 'yes' === $bookable ? 'no' : 'yes',
					);
				} else {
					$rules[] = array(
						'from_date' => $rule->get_from_date(),
						'to_date'   => $rule->get_to_date(),
						'from'      => $start_time,
						'to'        => $end_time,
						'type'      => 'time:range',
						'bookable'  => 'yes' === $bookable ? 'no' : 'yes',
					);
				}
			}
		} else {
			if ( $rrule ) {
				$rules[] = array(
					'from'     => $rule->get_from_date(),
					'to'       => $rule->get_to_date(),
					'type'     => 'rrule',
					'rrule'    => $rrule,
					'bookable' => $bookable,
				);
			} else {
				$rules[] = array(
					'from'     => $rule->get_from_date(),
					'to'       => $rule->get_to_date(),
					'type'     => 'custom',
					'bookable' => $bookable,
				);
			}
		}

		return static::process_availability_rules( $rules, $level, $hide_past );
	}

	/**
	 * Process and return formatted availability rules
	 *
	 * @version 1.10.7
	 * @param   array  $rules Rules to process.
	 * @param   string $level Resource, Product or Globally.
	 * @return  array
	 */
	public static function process_availability_rules( $rules, $level, $hide_past = true ) {
		$processed_rules = array();

		if ( empty( $rules ) ) {
			return $processed_rules;
		}

		// Go through rules.
		foreach ( $rules as $order_on_product => $fields ) {
			if ( empty( $fields['bookable'] ) ) {
				continue;
			}

			// Do not include dates that are in the past.

			if ( $hide_past && ( in_array( $fields['type'], array( 'custom', 'time:range' ), true ) ) ) {
				$to_date = ! empty( $fields['to_date'] ) ? $fields['to_date'] : $fields['to'];
				if ( strtotime( $to_date ) < strtotime( 'midnight -1 day' ) ) {
					continue;
				}
			}

			$type_function = self::get_type_function( $fields['type'] );
			$bookable      = 'yes' === $fields['bookable'] ? true : false;
			if ( defined( 'WC_BOOKINGS_ENABLE_STORE_AVAILABILITY_CALENDAR' ) && WC_BOOKINGS_ENABLE_STORE_AVAILABILITY_CALENDAR && ( 'store_availability' === $fields['type'] ) ) {
				$type_availability = false;
			} elseif ( 'get_rrule_range' === $type_function ) {
				$type_availability = self::$type_function( $fields['from'], $fields['to'], $fields['rrule'], $bookable );
			} elseif ( in_array( $type_function, array( 'get_time_range_for_custom_date', 'get_time_range_for_custom_datetime' ) ) ) {
				$type_availability = self::$type_function( $fields['from_date'], $fields['to_date'], $fields['from'], $fields['to'], $bookable );
			} else {
				$type_availability = self::$type_function( $fields['from'], $fields['to'], $bookable );
			}

			$priority = intval( ( isset( $fields['priority'] ) ? $fields['priority'] : 10 ) );

			// Ensure day gets specified for time: rules.
			if ( strrpos( $fields['type'], 'time:' ) === 0 && 'time:range' !== $fields['type'] ) {
				list( , $day )            = explode( ':', $fields['type'] );
				$type_availability['day'] = absint( $day );
			}

			if ( ! empty( $type_availability ) ) {
				$processed_rule = array(
					'type'     => $fields['type'],
					'range'    => $type_availability,
					'priority' => $priority,
					'level'    => $level,
					'order'    => $order_on_product,
				);

				if ( 'resource' === $level && ! empty( $fields['resource_id'] ) ) {
					$processed_rule['resource_id'] = $fields['resource_id'];
				}
				$processed_rules[] = $processed_rule;
			} elseif ( defined( 'WC_BOOKINGS_ENABLE_STORE_AVAILABILITY_CALENDAR' ) && WC_BOOKINGS_ENABLE_STORE_AVAILABILITY_CALENDAR && ( 'store_availability' === $fields['type'] ) ) {
				$rule            = new WC_Global_Availability( $fields['ID'] );
				$processed_rules = array_merge( $processed_rules, static::process_store_availability_rule( $rule, $level, $hide_past ) );
			}
		}

		return $processed_rules;
	}

	/**
	 * Get the minutes that should be available based on the rules and the date to check.
	 *
	 * The minutes are returned in a range from the start to increment minutes right up to the last available minute.
	 *
	 * This function expects the rules to be ordered in the sequence that is should be processed. Later rule minutes
	 * will override prior rule minutes in the order given.
	 *
	 * @since 1.9.14 moved from WC_Product_Booking.
	 *
	 * @param array $rules
	 * @param int $check_date
	 * @param array $bookable_minutes
	 *
	 * @return array $bookable_minutes
	 */
	public static function get_minutes_from_rules( $rules, $check_date, $bookable_minutes = array() ) {
		$resource_minutes = array();

		foreach ( $rules as $rule ) {
			// Something terribly wrong if a rule has no level.
			if ( ! isset( $rule['level'] ) ) {
				continue;
			}

			$data_for_rule = self::get_rule_minute_range( $rule, $check_date );

			// split up the rules on a resource level to be dealt with independently
			// after the rules loop. This ensure resource do not affect one another.
			if ( 'resource' === $rule['level'] ) {
				$resource_id      = $rule['resource_id'];
				$availability_key = $data_for_rule['is_bookable'] ? 'bookable' : 'not_bookable';
				// adding minutes in the order of the rules received, higher index higher override power.
				$resource_minutes[ $resource_id ][] = array( $availability_key => $data_for_rule['minutes'] );
				continue;
			}

			// At this point we assume all resource rules have been processed as they have a lower
			// override order in the $rules given.
			// Remove available resource minutes if being overridden at the product or global level.
			if ( ! self::check_timestamp_against_rule( $check_date, $rule, true ) ) {
				$resource_minutes = array();
			}

			if ( $data_for_rule['is_bookable'] ) {
				// If this time range is bookable, add to bookable minutes.
				$bookable_minutes = array_merge( $bookable_minutes, $data_for_rule['minutes'] );
				continue;
			}

			// Handle NON-resource removal of unavailable minutes.
			$bookable_minutes = array_diff( $bookable_minutes, $data_for_rule['minutes'] );

			// Handle resource specific removal of unavailable minutes.
			foreach ( $resource_minutes as $id => $minute_ranges ) {
				foreach ( $minute_ranges as $index => $minute_range ) {
					if ( ! isset( $minute_range['bookable'] ) || empty( $data_for_rule['minutes'] ) ) {
						continue;
					}
					// remove the last minute from the array for hours not to be thrown off
					// what happens is that this last minute could fall right at the beginning of the
					// next slot like 7:00 to 8:00 range the last minute will be on 8:00 which means
					// 8:00 will be removed, leaving the resulting range to start at 8:01.
					array_pop( $data_for_rule['minutes'] );
					$resource_minutes[ $id ][ $index ]['bookable'] = array_diff( $minute_range['bookable'], $data_for_rule['minutes'] );
				}
			}
		}

		// One resource should not override the other, when automatically assigned: as long as one is available.
		foreach ( $resource_minutes as $resource_id => $minutes_for_rule_order ) {
			$resource_minutes = array();

			foreach ( $minutes_for_rule_order as $rule_minutes_with_availability ) {
				$is_bookable = isset( $rule_minutes_with_availability['bookable'] );
				if ( $is_bookable ) {
					$resource_minutes = array_merge( $resource_minutes, $rule_minutes_with_availability['bookable'] );
				} else {
					$resource_minutes = array_diff( $resource_minutes, $rule_minutes_with_availability['not_bookable'] );
				}
			}

			$bookable_minutes = array_merge( $resource_minutes, $bookable_minutes );
		}

		$bookable_minutes = array_unique( array_values( $bookable_minutes ) );

		sort( $bookable_minutes );
		return $bookable_minutes;
	}

	/**
	 * This function is a mediator that simplifies the creation of
	 * a data object representing the range of rules minutes and the property of bookable or not.
	 *
	 * @since 1.10.10
	 *
	 * @param array $rule
	 * @param int   $check_date
	 *
	 * @return array $minute_range
	 */
	public static function get_rule_minute_range( $rule, $check_date ) {
		$minute_range = array(
			'is_bookable' => false,
			'minutes'     => array(),
		);

		if ( ( strpos( $rule['type'], 'time' ) > -1 ) || ( 'custom:daterange' === $rule['type'] ) ) {
			$minute_range = self::get_rule_minutes_for_time( $rule, $check_date );
		} elseif ( 'days' === $rule['type'] ) {
			$minute_range = self::get_rule_minutes_for_days( $rule, $check_date );
		} elseif ( 'weeks' === $rule['type'] ) {
			$minute_range = self::get_rule_minutes_for_weeks( $rule, $check_date );
		} elseif ( 'months' === $rule['type'] ) {
			$minute_range = self::get_rule_minutes_for_months( $rule, $check_date );
		} elseif ( 'custom' === $rule['type'] ) {
			$minute_range = self::get_rule_minutes_for_custom( $rule, $check_date );
		} elseif ( 'rrule' === $rule['type'] ) {
			$minute_range = self::get_rule_minutes_for_rrule( $rule, $check_date );
		}

		return $minute_range;
	}

	/**
	 * Calculate minutes range.
	 *
	 * @since 1.13.0
	 * @param $from
	 * @param $to
	 *
	 * @return array
	 */
	protected static function calculate_minute_range( $from, $to ) {
		$from_hour = absint( date( 'H', strtotime( $from ) ) );
		$from_min  = absint( date( 'i', strtotime( $from ) ) );
		$to_hour   = absint( date( 'H', strtotime( $to ) ) );
		$to_min    = absint( date( 'i', strtotime( $to ) ) );

		// If "to" is set to midnight, it is safe to assume they mean the end of the day
		// php wraps 24 hours to "12AM the next day"
		if ( 0 === $to_hour && 0 === $to_min ) {
			$to_hour = 24;
		}

		$minute_range = array( ( $from_hour * 60 ) + $from_min, ( $to_hour * 60 ) + $to_min );
		$merge_ranges = array();
		$minutes      = array();

		// if first time in range is larger than second, we
		// assume they want to go over midnight
		if ( $minute_range[0] > $minute_range[1] ) {
			$merge_ranges[] = array( $minute_range[0], 1440 );
			// fix for https://github.com/woothemes/woocommerce-bookings/issues/710
			$merge_ranges[] = array( $minute_range[0], ( 1440 + $minute_range[1] ) );
		} else {
			$merge_ranges[] = array( $minute_range[0], $minute_range[1] );
		}

		foreach ( $merge_ranges as $range ) {
			// Add ranges to minutes this rule affects.
			$minutes = array_merge( $minutes, range( $range[0], $range[1] ) );
		}

		return $minutes;
	}

	/**
	 * Get minutes from rules for a time rule type.
	 *
	 * @since 1.9.14
	 * @param $rule
	 * @param integer $check_date
	 *
	 * @return array
	 */
	public static function get_rule_minutes_for_time( $rule, $check_date ) {
		$minutes = array(
			'is_bookable' => false,
			'minutes'     => array(),
		);

		$type    = $rule['type'];
		$range   = $rule['range'];

		$year        = date( 'Y', $check_date );
		$month       = date( 'n', $check_date );
		$day         = date( 'j', $check_date );
		$day_of_week = date( 'N', $check_date );

		if ( in_array( $type, array( 'time:range', 'custom:daterange' ) ) ) { // type: date range with time
			if ( ! isset( $range[ $year ][ $month ][ $day ] ) ) {
				return $minutes;
			} else {
				$range = $range[ $year ][ $month ][ $day ];
			}

			$from                   = $range['from'];
			$to                     = $range['to'];
			$minutes['is_bookable'] = $range['rule'];
		} elseif ( strpos( $rule['type'], 'time:' ) > -1 ) { // type: single week day with time
			if ( $day_of_week != $range['day'] ) {
				return $minutes;
			}

			$from                   = $range['from'];
			$to                     = $range['to'];
			$minutes['is_bookable'] = $range['rule'];
		} else {  // type: time all week per day
			$from                   = $range['from'];
			$to                     = $range['to'];
			$minutes['is_bookable'] = $range['rule'];
		}

		$minutes['minutes'] = self::calculate_minute_range( $from, $to );

		return $minutes;
	}

	/**
	 * Get minutes from rules for a 'rrule' rule type.
	 *
	 * @since 1.13.0
	 * @param $rule
	 * @param integer $check_date
	 *
	 * @return array
	 */
	public static function get_rule_minutes_for_rrule( $rule, $check_date ) {
		$start       = new WC_DateTime( $rule['range']['from'] );
		$end         = new WC_DateTime( $rule['range']['to'] );
		$is_all_day  = false === strpos( $rule['range']['from'], ':' );
		$date_format = $is_all_day ? 'Y-m-d' : 'Y-m-d g:i A';

		$minutes = array(
			'is_bookable' => false,
			'minutes'     => array(),
		);

		try {
			$rset = new \RRule\RSet( $rule['range']['rrule'], $is_all_day ? $start->format( $date_format ) : $start );
		} catch ( Exception $e ) {
			return $minutes;
		}

		$duration = $start->diff( $end, true );

		$current_date  = ( new DateTime( '@' . $check_date ) )->modify( 'midnight' );
		$tomorrow_date = ( new DateTime( '@' . $check_date ) )->modify( 'tomorrow' );
		// Offset from the server's timezone, back to UTC.
		$current_date->modify( '-' . get_option( 'gmt_offset' ) . ' hours' );
		$tomorrow_date->modify( '-' . get_option( 'gmt_offset' ) . ' hours' );

		// Use a limit since this reccurence can potentially be infinite.
		$occurrences = $rset->getOccurrencesBetween( $current_date, $tomorrow_date );

		foreach ( $occurrences as $occurrence ) {
			if ( date( 'Y-m-d', $check_date ) !== $occurrence->format( 'Y-m-d' ) ) {
				continue;
			}

			// Format to remove timezone since it's already been offseted to the server's timezone.
			$from = $occurrence->format( 'H:i' );
			$to   = $occurrence->add( $duration )->format( 'H:i' );

			$minute_range = self::calculate_minute_range( $from, $to );

			$minutes['minutes'] = array_merge( $minutes['minutes'], $minute_range );

		}

		return $minutes;
	}

	/**
	 * Get minutes from rules for days rule type.
	 *
	 * @since 1.9.14
	 * @param $rule
	 * @param integer $check_date
	 *
	 * @return array
	 */
	public static function get_rule_minutes_for_days( $rule, $check_date ) {
		$_rules      = $rule['range'];
		$minutes     = array();
		$is_bookable = false;
		$day_of_week = intval( date( 'N', $check_date ) );

		if ( isset( $_rules[ $day_of_week ] ) ) {
			$minutes     = range( 0, 1440 );
			$is_bookable = $_rules[ $day_of_week ];
		}

		return array(
			'is_bookable' => $is_bookable,
			'minutes'     => $minutes,
		);
	}

	/**
	 * Get minutes from rules for a weeks rule type.
	 *
	 * @since 1.9.14
	 * @param $rule
	 * @param integer $check_date
	 *
	 * @return array
	 */
	public static function get_rule_minutes_for_weeks( $rule, $check_date ) {

		$range       = $rule['range'];
		$week_number = intval( date( 'W', $check_date ) );
		$minutes     = array();
		$is_bookable = false;

		if ( isset( $range[ $week_number ] ) ) {
			$minutes     = range( 0, 1440 );
			$is_bookable = $range[ $week_number ];
		}

		return array(
			'is_bookable' => $is_bookable,
			'minutes'     => $minutes,
		);
	}

	/**
	 * Get minutes from rules for a months rule type.
	 *
	 * @since 1.9.14
	 * @param $rule
	 * @param integer $check_date
	 *
	 * @return array
	 */
	public static function get_rule_minutes_for_months( $rule, $check_date ) {

		$range       = $rule['range'];
		$month       = date( 'n', $check_date );
		$minutes     = array();
		$is_bookable = false;
		if ( isset( $range[ $month ] ) ) {
			$minutes     = range( 0, 1440 );
			$is_bookable = $range[ $month ];
		}

		return array(
			'is_bookable' => $is_bookable,
			'minutes'     => $minutes,
		);
	}

	/**
	 * Get minutes from rules for custom rule type.
	 *
	 * @since 1.9.14
	 * @param $rule
	 * @param integer $check_date
	 *
	 * @return array
	 */
	public static function get_rule_minutes_for_custom( $rule, $check_date ) {

		$range = $rule['range'];
		$year  = date( 'Y', $check_date );
		$month = date( 'n', $check_date );
		$day   = date( 'j', $check_date );

		$minutes     = array();
		$is_bookable = false;

		if ( isset( $range[ $year ][ $month ][ $day ] ) ) {
			$minutes     = range( 0, 1440 );
			$is_bookable = $range[ $year ][ $month ][ $day ];
		}

		return array(
			'is_bookable' => $is_bookable,
			'minutes'     => $minutes,
		);
	}

	/**
	 * Sort rules in order of precedence.
	 *
	 * @version 1.9.14 sort order reversed
	 * The order produced will be from the lowest to the highest.
	 * The elements with higher indexes overrides those with lower indexes e.g. `4` overrides `3`
	 * Index corresponds to override power. The higher the element index the higher the override power
	 *
	 * Level    : `global` > `product` > `product` (greater in terms off override power)
	 * Priority : within a level
	 * Order    : Within a priority The lower the order index higher the override power.
	 *
	 * @param array $rule1
	 * @param array $rule2
	 *
	 * @return integer
	 */
	public static function sort_rules_callback( $rule1, $rule2 ) {
		$level_weight = array(
			'resource' => 1,
			'product'  => 3,
			'global'   => 5,
		);

		// The override power goes from the outside inward.
		// Priority is outside which means it has the most weight when sorting.
		// Then level(global, product, resource)
		// Lastly order is applied within the level.
		if ( $rule1['priority'] === $rule2['priority'] ) {
			if ( $level_weight[ $rule1['level'] ] === $level_weight[ $rule2['level'] ] ) {
				// if `order index of 1` < `order index of 2` $rule1 one has a higher override power. So we
				// increase the index for $rule1 which corresponds to override power.
				return ( $rule1['order'] < $rule2['order'] ) ? 1 : -1;
			}

			// if `level of 1` < `level of 2` $rule1 must have lower override power. So we
			// decrease the index for 1 which corresponds to override power.
			return $level_weight[ $rule1['level'] ] < $level_weight[ $rule2['level'] ] ? -1 : 1;
		}

		// if `priority of 1` < `priority of 2` $rule1 must have lower override power. So we
		// decrease the index for 1 which corresponds to override power.
		return $rule1['priority'] < $rule2['priority'] ? 1 : -1;
	}

	/**
	 * Filter out all but time rules.
	 *
	 * @param  array $rule
	 * @return boolean
	 */
	private static function filter_time_rules( $rule ) {
		return ! empty( $rule['type'] ) && ! in_array( $rule['type'], array( 'days', 'custom', 'months', 'weeks' ) );
	}

	/**
	 * Check a bookable product's availability rules against a time range and return if bookable or not.
	 *
	 * @param  WC_Product_Booking $bookable_product
	 * @param  int                $resource_id
	 * @param  int                $start timestamp
	 * @param  int                $end timestamp
	 * @return boolean
	 */
	public static function check_range_availability_rules( $bookable_product, $resource_id, $start, $end ) {
		// This is a time range.
		if ( in_array( $bookable_product->get_duration_unit(), array( 'minute', 'hour' ) ) ) {
			return self::check_availability_rules_against_time( $start, $end, $resource_id, $bookable_product );
		} else {  // Else this is a date range (days).
			$timestamp = $start;

			while ( $timestamp < $end ) {
				if ( ! self::check_availability_rules_against_date( $bookable_product, $resource_id, $timestamp ) ) {
					return false;
				}
				if ( $bookable_product->get_check_start_block_only() ) {
					break; // Only need to check first day.
				}
				$timestamp = strtotime( '+1 day', $timestamp );
			}
		}

		return true;
	}

	/**
	 * Check a time against the time specific availability rules
	 *
	 * @param integer                                                     $slot_start_time
	 * @param integer                                                     $slot_end_time
	 * @param integer                                                     $resource_id
	 * @param WC_Product_Booking                                          $bookable_product
	 * @param bool|null If not null, it will default to the boolean value. If null, it will use product default availability.
	 *
	 * @return bool available or not
	 */
	public static function check_availability_rules_against_time( $slot_start_time, $slot_end_time, $resource_id, $bookable_product, $bookable = null ) {
		$rules = $bookable_product->get_availability_rules( $resource_id );
		if ( is_null( $bookable ) ) {
			$bookable = $bookable_product->get_default_availability();
		}

		if ( empty( $rules ) ) {
			return $bookable;
		}

		$slot_start_time = is_numeric( $slot_start_time ) ? $slot_start_time : strtotime( $slot_start_time );
		$slot_end_time   = is_numeric( $slot_end_time ) ? $slot_end_time : strtotime( $slot_end_time );

		// Get the date values for the slots being checked.
		$slot_year   = intval( date( 'Y', $slot_start_time ) );
		$slot_month  = intval( date( 'n', $slot_start_time ) );
		$slot_date   = intval( date( 'j', $slot_start_time ) );
		$slot_day_no = intval( date( 'N', $slot_start_time ) );
		$slot_week   = intval( date( 'W', $slot_start_time ) );

		// default from and to for the whole day.
		$from = strtotime( 'midnight', $slot_start_time );
		$to   = strtotime( 'midnight + 1 day', $slot_start_time );

		foreach ( $rules as $rule ) {
			$type  = $rule['type'];
			$range = $rule['range'];

			if ( 'rrule' === $type ) {
				if ( self::rrule_matches_timestamp( $range, $slot_start_time + 1 ) || self::rrule_matches_timestamp( $range, $slot_end_time - 1 ) ) {
					return $range['rule'];
				} else {
					continue;
				}
			}

			// handling none time specific rules first.
			if ( in_array( $type, array( 'days', 'custom', 'months', 'weeks' ), true ) ) {
				if ( 'days' === $type ) {
					if ( ! isset( $range[ $slot_day_no ] ) ) {
						continue;
					}
				} elseif ( 'custom' === $type ) {
					if ( ! isset( $range[ $slot_year ][ $slot_month ][ $slot_date ] ) ) {
						continue;
					}
				} elseif ( 'months' === $type ) {
					if ( ! isset( $range[ $slot_month ] ) ) {
						continue;
					}
				} elseif ( 'weeks' === $type ) {
					if ( ! isset( $range[ $slot_week ] ) ) {
						continue;
					}
				}

				$rule_val = self::check_timestamp_against_rule( $slot_start_time, $rule, $bookable_product->get_default_availability() );
			}

			// Handling all time specific rules.
			$apply_rule_times = false;
			if ( in_array( $type, array( 'time:range', 'custom:daterange' ) ) ) {
				if ( ! isset( $range[ $slot_year ][ $slot_month ][ $slot_date ] ) ) {
					continue;
				}
				$time_range_rule  = $range[ $slot_year ][ $slot_month ][ $slot_date ];
				$rule_val         = $time_range_rule['rule'];
				$from             = $time_range_rule['from'];
				$to               = $time_range_rule['to'];
				$apply_rule_times = true;
			} elseif ( false !== strpos( $type, 'time' ) ) {
				// if the day doesn't match and the day is not zero skip the rule
				// zero means all days. SO rule only apply for zero or a matching day.
				if ( ! empty( $range['day'] ) && $slot_day_no != $range['day'] ) {
					continue;
				}

				// check that the rule should be applied to the current slot
				// if not time it must be time:day_number
				if ( 'time' !== $type ) {
					if ( ! strpos( $type, (string) $slot_day_no ) ) {
						continue;
					}
				}

				$rule_val         = $range['rule'];
				$from             = $range['from'];
				$to               = $range['to'];
				$apply_rule_times = true;
			}

			$rule_start_time = $apply_rule_times ? strtotime( $from, $slot_start_time ) : $slot_start_time;
			$rule_end_time   = $apply_rule_times ? strtotime( $to, $slot_start_time ) : $slot_start_time;

			// Reverse time rule - The end time is tomorrow e.g. 16:00 today - 12:00 tomorrow
			if ( $rule_end_time <= $rule_start_time ) {
				if ( $slot_end_time > $rule_start_time ) {
					$bookable = $rule_val;
					continue;
				}
				if ( $slot_start_time >= $rule_start_time && $slot_end_time >= $rule_end_time ) {
					$bookable = $rule_val;
					continue;
				}
				// does this rule apply?
				// does slot start before rule start and end after rules start time {goes over start time}
				if ( $slot_start_time < $rule_start_time && $slot_end_time > $rule_start_time ) {
					$bookable = $rule_val;
					continue;
				}
			} else {
				// Normal rule.
				if ( $slot_start_time < $rule_end_time && $slot_end_time > $rule_start_time ) {
					if ( 'hour' === $bookable_product->get_duration_unit() || 'minute' === $bookable_product->get_duration_unit() ) {
						// If the product is not available by default and the rule makes the product available,
						// slot_end_time has to be also inside of the rule_end_time for products with duration of hours or minutes.
						$check_in_range = $rule_val && ! $bookable_product->get_default_availability() && ! $bookable_product->get_check_start_block_only();

						if ( $apply_rule_times && $check_in_range && ( $slot_end_time > $rule_end_time ) ) {
							continue;
						}
					}
					$bookable = $rule_val;
					continue;
				}

				// specific to hour duration types. If start time is in between
				// rule start and end times the rule should be applied.
				if ( 'hour' === $bookable_product->get_duration_unit()
					&& $slot_start_time > $rule_start_time
					&& $slot_start_time < $rule_end_time ) {

					$bookable = $rule_val;
					continue;

				}
			}
		}

		return $bookable;
	}

	/**
	 * Check a date against the availability rules
	 *
	 * @version 1.11.3 Added woocommerce_bookings_is_date_bookable filter hook
	 * @version 1.10.0  Moved to this class from WC_Product_Booking
	 *                  only apply rules if within their scope
	 *                  keep booking value alive within the loop to ensure the next rule with higher power can override
	 * @version 1.9.14  removed all calls to break 2 to ensure we get to the highest
	 *                  priority rules, otherwise higher order/priority rules will not
	 *                  override lower ones and the function exit with the wrong value.
	 *
	 * @param  WC_Product_Booking $bookable_product
	 * @param  int                $resource_id
	 * @param  int                $check_date timestamp
	 * @return bool available or not
	 */
	public static function check_availability_rules_against_date( $bookable_product, $resource_id, $check_date ) {
		$bookable = $bookable_product->get_default_availability();
		foreach ( $bookable_product->get_availability_rules( $resource_id ) as $rule ) {
			if ( self::does_rule_apply( $rule, $check_date ) ) {
				// passing $bookable into the next check as it overrides the previous value
				$bookable = self::check_timestamp_against_rule( $check_date, $rule, $bookable );
			}
		}

		/**
		 * Is date bookable hook.
		 *
		 * Filter allows for overriding whether or not date is bookable. Filters should return true
		 * if bookable or false if not.
		 *
		 * @since 1.11.3
		 *
		 * @param bool $bookable available or not
		 * @param WC_Product_Booking $bookable_product
		 * @param int $resource_id
		 * @param int $check_date timestamp
		 */
		return apply_filters( 'woocommerce_bookings_is_date_bookable', $bookable, $bookable_product, $resource_id, $check_date );
	}

	/**
	 * Does the time stamp fall within the scope of the rule?
	 *
	 * @param $rule
	 * @param $timestamp
	 * @return bool
	 */
	public static function does_rule_apply( $rule, $timestamp ) {
		$year        = intval( date( 'Y', $timestamp ) );
		$month       = intval( date( 'n', $timestamp ) );
		$day         = intval( date( 'j', $timestamp ) );
		$day_of_week = intval( date( 'N', $timestamp ) );
		$week        = intval( date( 'W', $timestamp ) );

		$range = $rule['range'];

		switch ( $rule['type'] ) {
			case 'months':
				if ( isset( $range[ $month ] ) ) {
					return true;
				}
				break;
			case 'weeks':
				if ( isset( $range[ $week ] ) ) {
					return true;
				}
				break;
			case 'days':
				if ( isset( $range[ $day_of_week ] ) ) {
					return true;
				}
				break;
			case 'custom':
				if ( isset( $range[ $year ][ $month ][ $day ] ) ) {
					return true;
				}
				break;
			case 'rrule':
				if ( self::rrule_matches_timestamp( $range, $timestamp ) ) {
					return true;
				}
				break;
			case 'time':
			case 'time:1':
			case 'time:2':
			case 'time:3':
			case 'time:4':
			case 'time:5':
			case 'time:6':
			case 'time:7':
				if ( $day_of_week === $range['day'] || 0 === $range['day'] ) {
					return true;
				}
				break;
			case 'custom:daterange':
			case 'time:range':
				if ( isset( $range[ $year ][ $month ][ $day ] ) ) {
					return true;
				}
				break;
		}

		return false;
	}

	/**
	 * Given a timestamp and rule check to see if the time stamp is bookable based on the rule.
	 *
	 * @since 1.10.0
	 *
	 * @param integer $timestamp
	 * @param array   $rule
	 * @param boolean $default
	 * @return boolean
	 */
	public static function check_timestamp_against_rule( $timestamp, $rule, $default ) {
		$year        = intval( date( 'Y', $timestamp ) );
		$month       = intval( date( 'n', $timestamp ) );
		$day         = intval( date( 'j', $timestamp ) );
		$day_of_week = intval( date( 'N', $timestamp ) );
		$week        = intval( date( 'W', $timestamp ) );

		$type     = $rule['type'];
		$range    = $rule['range'];
		$bookable = $default;

		switch ( $type ) {
			case 'months':
				if ( isset( $range[ $month ] ) ) {
					$bookable = $range[ $month ];
				}
				break;
			case 'weeks':
				if ( isset( $range[ $week ] ) ) {
					$bookable = $range[ $week ];
				}
				break;
			case 'days':
				if ( isset( $range[ $day_of_week ] ) ) {
					$bookable = $range[ $day_of_week ];
				}
				break;
			case 'custom':
				if ( isset( $range[ $year ][ $month ][ $day ] ) ) {
					$bookable = $range[ $year ][ $month ][ $day ];
				}
				break;
			case 'rrule':
				if ( self::rrule_matches_timestamp( $range, $timestamp ) ) {
					return $range['rule'];
				}
				break;
			case 'time':
			case 'time:1':
			case 'time:2':
			case 'time:3':
			case 'time:4':
			case 'time:5':
			case 'time:6':
			case 'time:7':
				if ( false === $default && ( $day_of_week === $range['day'] || 0 === $range['day'] ) ) {
					$bookable = $range['rule'];
				}
				break;
			case 'custom:daterange':
			case 'time:range':
				if ( false === $default && ( isset( $range[ $year ][ $month ][ $day ] ) ) ) {
					$bookable = $range[ $year ][ $month ][ $day ]['rule'];
				}
				break;
		}

		return $bookable;
	}

	/**
	 * Checks if the given rrule and event happens at the given timestamp.
	 *
	 * @param array $range Range and rrule to check.
	 * @param int   $timestamp Timestamp to check against.
	 *
	 * @return bool
	 */
	private static function rrule_matches_timestamp( $range, $timestamp ) {

		// This function is normally called twice with the same parameters so let's cache the result.
		static $cache = array();

		// This function will be called with the same rrules but different timestamps, so cache the rrule object and duration here.
		static $rrule_cache = array();

		$rrule_cache_key    = $range['from'] . ':' . $range['to'] . ':' . $range['rrule'];
		$cache_key    = $rrule_cache_key . ':' . $timestamp;

		if ( isset( $cache[ $cache_key ] ) ) {
			return $cache[ $cache_key ];
		}

		try {

			$datetime   = new DateTime( '@' . $timestamp );

			if ( ! isset( $rrule_cache[ $rrule_cache_key ] ) ) {
				$is_all_day = false === strpos( $range['from'], ':' );
				$start      = new DateTime( $range['from'] );
				$end        = new DateTime( $range['to'] );
				$start->setTimezone( new DateTimeZone( 'GMT' ) );
				$end->setTimezone( new DateTimeZone( 'GMT' ) );
				$end->modify( get_option( 'gmt_offset' ) . ' hours' );
				$start->modify( get_option( 'gmt_offset' ) . ' hours' );

				$duration = $start->diff( $end, true );

				$rrule  = new \RRule\RSet( $range['rrule'], $is_all_day ? $start->format( 'Y-m-d' ) : $start );
				$rrule_cache[ $rrule_cache_key ] = array(
					'rrule_object' => $rrule,
					'duration' => $duration
				);
			} else {

				$rrule = $rrule_cache[ $rrule_cache_key ]['rrule_object'];
				$duration = $rrule_cache[ $rrule_cache_key]['duration'];
			}

			foreach ( $rrule as $occurrence ) {
				if ( $occurrence <= $datetime && $datetime <= $occurrence->add( $duration ) ) {
					$cache[ $cache_key ] = true;
					return true;
				}
				if ( $occurrence > $datetime ) {
					break;
				}
			}
		} catch ( Exception $e ) {
			wc_get_logger()->error( $e->getMessage() );
		}
		$cache[ $cache_key ] = false;
		return false;
	}
}
