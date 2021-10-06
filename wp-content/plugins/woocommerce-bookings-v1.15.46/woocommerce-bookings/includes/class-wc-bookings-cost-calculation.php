<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that handles all cost calculations.
 *
 * @since 1.15.0
 */
class WC_Bookings_Cost_Calculation {
	public static $applied_cost_rules;

	/**
	 * Calculate costs from posted values
	 * @param  array $data
	 * @return string cost
	 */
	public static function calculate_booking_cost( $data, $product ) {
		// Get costs
		$costs    = $product->get_costs();
		$validate = $product->is_bookable( $data );

		if ( is_wp_error( $validate ) ) {
			return $validate;
		}

		$base_cost        = max( 0, $product->get_cost() );
		$base_block_cost  = max( 0, $product->get_block_cost() );
		$total_block_cost = 0;

		/* Person costs. */
		$person_base_costs        = 0;
		$person_block_costs       = 0;
		$total_person_block_costs = 0;

		// Get resource cost.
		if ( isset( $data['_resource_id'] ) ) {
			$resource         = $product->get_resource( $data['_resource_id'] );
			$base_block_cost += $resource->get_block_cost();
			$base_cost       += $resource->get_base_cost();
		}

		// Potentially increase costs if dealing with persons.
		if ( ! empty( $data['_persons'] ) && $product->has_person_types() ) {
			foreach ( $data['_persons'] as $person_id => $person_count ) {
				$person_type       = new WC_Product_Booking_Person_Type( $person_id );
				$person_cost       = $person_type->get_cost();
				$person_block_cost = $person_type->get_block_cost();

				// Only a single cost - multiplication comes later if wc_booking_person_cost_multiplier is enabled.
				if ( $person_count > 0 && $person_cost > 0 ) {
					if ( $product->get_has_person_cost_multiplier() ) {
						// If there are person types with costs and person multiplier, separate person costs for calculations.
						$person_base_costs += ( $person_cost * $person_count );
					} else {
						$base_cost += ( $person_cost * $person_count );
					}
				}
				if ( $person_count > 0 && $person_block_cost > 0 ) {
					$person_block_costs += ( $person_block_cost * $person_count );
				}
			}
		}

		self::$applied_cost_rules = array();
		$block_duration           = $product->get_duration();
		$block_unit               = $product->get_duration_unit();
		$blocks_booked            = isset( $data['_duration'] ) ? absint( $data['_duration'] ) : $block_duration;
		$block_timestamp          = $data['_start_date'];

		if ( $product->is_duration_type( 'fixed' ) ) {
			$blocks_booked = ceil( $blocks_booked / $block_duration );
		}

		$buffer_period = $product->get_buffer_period();
		if ( ! empty( $buffer_period ) ) {
			// handle day buffers
			if ( ! in_array( $product->get_duration_unit(), array( 'minute', 'hour' ) ) ) {
				$buffer_days = WC_Bookings_Controller::find_buffer_day_blocks( $product );
				$contains_buffer_days = false;
				// Evaluate costs for each booked block
				for ( $block = 0; $block < $blocks_booked; $block ++ ) {
					$block_start_time_offset = $block * $block_duration;
					$block_end_time_offset   = ( ( $block + 1 ) * $block_duration ) - 1;
					$block_start_time        = date( 'Y-n-j', strtotime( "+{$block_start_time_offset} {$block_unit}", $block_timestamp ) );
					$block_end_time          = date( 'Y-n-j', strtotime( "+{$block_end_time_offset} {$block_unit}", $block_timestamp ) );

					if ( in_array( $block_end_time, $buffer_days ) ) {
						$contains_buffer_days = true;
					}

					if ( in_array( $block_start_time, $buffer_days ) ) {
						$contains_buffer_days = true;
					}
				}

				if ( $contains_buffer_days ) {
					$block_duration_string = $block_duration;
					if ( 'week' === $block_unit ) {
						$block_duration_string = $block_duration * 7;
					}

					/* translators: 1: block duration days */
					return new WP_Error( 'Error', sprintf( __( 'The duration of this booking must be at least %s days.', 'woocommerce-bookings' ), $block_duration_string ) );
				}
			}
		}

		$override_blocks = array();
		// Evaluate costs for each booked block
		for ( $block = 0; $block < $blocks_booked; $block ++ ) {
			// If there are person types with costs and person multiplier, separate person costs.
			if ( ( $person_block_costs > 0 ) && $product->get_has_person_cost_multiplier() ) {
				$block_cost = $base_block_cost;
			} else {
				$block_cost = $base_block_cost + $person_block_costs;
			}

			$block_start_time_offset = $block * $block_duration;
			$block_end_time_offset   = ( $block + 1 ) * $block_duration;
			$block_start_time        = wc_bookings_get_formatted_times( strtotime( "+{$block_start_time_offset} {$block_unit}", $block_timestamp ) );
			$block_end_time          = wc_bookings_get_formatted_times( strtotime( "+{$block_end_time_offset} {$block_unit}", $block_timestamp ) );

			if ( in_array( $product->get_duration_unit(), array( 'night' ) ) ) {
				$block_start_time        = wc_bookings_get_formatted_times( strtotime( "+{$block_start_time_offset} day", $block_timestamp ) );
				$block_end_time = wc_bookings_get_formatted_times( strtotime( "+{$block_end_time_offset} day", $block_timestamp ) );
			}

			foreach ( $costs as $rule_key => $rule ) {
				$type         = $rule[0];
				$rules        = $rule[1];
				$rule_applied = false;

				if ( strrpos( $type, 'time' ) === 0 ) {
					if ( ! in_array( $product->get_duration_unit(), array( 'minute', 'hour' ) ) ) {
						continue;
					}

					if ( 'time:range' === $type ) {
						$year = date( 'Y', $block_start_time['timestamp'] );
						$month = date( 'n', $block_start_time['timestamp'] );
						$day = date( 'j', $block_start_time['timestamp'] );

						if ( ! isset( $rules[ $year ][ $month ][ $day ] ) ) {
							continue;
						}

						$rule_val = $rules[ $year ][ $month ][ $day ]['rule'];
						$from     = $rules[ $year ][ $month ][ $day ]['from'];
						$to       = $rules[ $year ][ $month ][ $day ]['to'];
					} else {
						if ( ! empty( $rules['day'] ) ) {
							if ( $rules['day'] != $block_start_time['day_of_week'] ) {
								continue;
							}
						}

						$rule_val = $rules['rule'];
						$from     = $rules['from'];
						$to       = $rules['to'];
					}

					$rule_start_time_hi = date( 'YmdHi', strtotime( str_replace( ':', '', $from ), $block_start_time['timestamp'] ) );
					$rule_end_time_hi   = date( 'YmdHi', strtotime( str_replace( ':', '', $to ), $block_start_time['timestamp'] ) );
					$matched            = false;

					// Reverse time rule - The end time is tomorrow e.g. 16:00 today - 12:00 tomorrow
					if ( $rule_end_time_hi <= $rule_start_time_hi ) {

						if ( $block_end_time['time'] > $rule_start_time_hi ) {
							$matched = true;
						}
						if ( $block_start_time['time'] >= $rule_start_time_hi && $block_end_time['time'] >= $rule_end_time_hi ) {
							$matched = true;
						}
						if ( $block_start_time['time'] <= $rule_start_time_hi && $block_end_time['time'] <= $rule_end_time_hi ) {
							$matched = true;
						}
					} else {
						// Else Normal rule.
						if ( $block_start_time['time'] >= $rule_start_time_hi && $block_end_time['time'] <= $rule_end_time_hi ) {
							$matched = true;
						}
					}

					if ( $matched ) {
						$block_cost   = self::apply_cost( $block_cost, $rule_val['block'][0], $rule_val['block'][1] );
						$base_cost    = self::apply_base_cost( $base_cost, $rule_val['base'][0], $rule_val['base'][1], $rule_key );
						$rule_applied = true;
					}
				} else {
					switch ( $type ) {
						case 'months':
						case 'weeks':
						case 'days':
							$check_date = $block_start_time['timestamp'];

							while ( $check_date < $block_end_time['timestamp'] ) {
								$checking_date = wc_bookings_get_formatted_times( $check_date );
								$date_key      = 'days' == $type ? 'day_of_week' : substr( $type, 0, -1 );

								// cater to months beyond this year
								if ( 'month' === $date_key && intval( $checking_date['year'] ) > intval( date( 'Y' ) ) ) {

									$month_beyond_this_year = intval( $checking_date['month'] ) + 12;
									$checking_date['month'] = (string) ( $month_beyond_this_year % 12 );
									if ( '0' === $checking_date['month'] ) {
										$checking_date['month'] = '12';
									}
								}

								if ( isset( $rules[ $checking_date[ $date_key ] ] ) ) {
									$rule       = $rules[ $checking_date[ $date_key ] ];
									$block_cost   = self::apply_cost( $block_cost, $rule['block'][0], $rule['block'][1] );
									$base_cost    = self::apply_base_cost( $base_cost, $rule['base'][0], $rule['base'][1], $rule_key );
									$rule_applied = true;
									if ( $rule['override'] && empty( $override_blocks[ $check_date ] ) ) {
										$override_blocks[ $check_date ] = $rule['override'];
									}
								}
								$check_date = strtotime( "+1 {$type}", $check_date );
							}
							break;
						case 'custom':
							$check_date = $block_start_time['timestamp'];

							while ( $check_date < $block_end_time['timestamp'] ) {
								$checking_date = wc_bookings_get_formatted_times( $check_date );
								if ( isset( $rules[ $checking_date['year'] ][ $checking_date['month'] ][ $checking_date['day'] ] ) ) {
									$rule         = $rules[ $checking_date['year'] ][ $checking_date['month'] ][ $checking_date['day'] ];
									$block_cost   = self::apply_cost( $block_cost, $rule['block'][0], $rule['block'][1] );
									$base_cost    = self::apply_base_cost( $base_cost, $rule['base'][0], $rule['base'][1], $rule_key );
									$rule_applied = true;

									if ( $rule['override'] && empty( $override_blocks[ $check_date ] ) ) {
										$override_blocks[ $check_date ] = $rule['override'];
									}
									/*
									 * Why do we break?
									 * See: Applying a cost rule to a booking block
									 * from the DEVELOPER.md
									 */
									break;
								}
								$check_date = strtotime( '+1 day', $check_date );
							}
							break;
						case 'persons':
							if ( ! empty( $data['_persons'] ) ) {
								if ( $rules['from'] <= array_sum( $data['_persons'] ) && $rules['to'] >= array_sum( $data['_persons'] ) ) {
									$block_cost   = self::apply_cost( $block_cost, $rules['rule']['block'][0], $rules['rule']['block'][1] );
									$base_cost    = self::apply_base_cost( $base_cost, $rules['rule']['base'][0], $rules['rule']['base'][1], $rule_key );
									$rule_applied = true;
								}
							}
							break;
						case 'blocks':
							if ( ! empty( $data['_duration'] ) ) {
								if ( $rules['from'] <= $data['_duration'] && $rules['to'] >= $data['_duration'] ) {
									$block_cost   = self::apply_cost( $block_cost, $rules['rule']['block'][0], $rules['rule']['block'][1] );
									$base_cost    = self::apply_base_cost( $base_cost, $rules['rule']['base'][0], $rules['rule']['base'][1], $rule_key );
									$rule_applied = true;
								}
							}
							break;
					}
				}
				/**
				 * Filter to modify rule cost logic. By default, all relevant cost rules will be
				 * applied to a block. Hooks returning false can modify this so only the first
				 * applicable rule will modify the block cost.
				 *
				 * @since 1.16.0
				 * @param bool
				 * @param WC_Product_Booking Current bookable product.
				 */
				if ( $rule_applied && ( ! apply_filters( 'woocommerce_bookings_apply_multiple_rules_per_block', true, $product ) ) ) {
					break;
				}
			}
			$total_block_cost         += $block_cost;
			$total_person_block_costs += $person_block_costs;
		}

		foreach ( $override_blocks as $over_cost ) {
			$total_block_cost = $total_block_cost - $base_block_cost;
			$total_block_cost += $over_cost;
		}

		$booking_cost = max( 0, $total_block_cost + $base_cost );

		if ( ! empty( $data['_persons'] ) ) {
			if ( $product->get_has_person_cost_multiplier() ) {
				// Person multiplier multiplies booking costs, not person costs.
				$booking_cost = $booking_cost * array_sum( $data['_persons'] ) + max( 0, $total_person_block_costs + $person_base_costs );
			}
		}

		return apply_filters( 'woocommerce_bookings_calculated_booking_cost', $booking_cost, $product, $data );
	}

	/**
	 * Apply a cost.
	 *
	 * @since 1.15.0
	 * @param  float $base
	 * @param  string $multiplier
	 * @param  float $cost
	 * @return float
	 */
	public static function apply_cost( $base, $multiplier, $cost ) {
		$base = floatval( $base );
		$cost = floatval( $cost );

		switch ( $multiplier ) {
			case 'times':
				$new_cost = $base * $cost;
				break;
			case 'divide':
				$new_cost = $base / $cost;
				break;
			case 'minus':
				$new_cost = $base - $cost;
				break;
			case 'equals':
				$new_cost = $cost;
				break;
			default:
				$new_cost = $base + $cost;
				break;
		}
		return $new_cost;
	}

	/**
	 * Apply base cost.
	 *
	 * @since 1.15.0
	 * @param  float $base
	 * @param  string $multiplier
	 * @param  float $cost
	 * @param  string $rule_key Cost to apply the rule to - used for * and /
	 * @return float
	 */
	public static function apply_base_cost( $base, $multiplier, $cost, $rule_key = '' ) {
		if ( in_array( $rule_key, self::$applied_cost_rules, true ) ) {
			return $base;
		}
		self::$applied_cost_rules[] = $rule_key;

		return self::apply_cost( $base, $multiplier, $cost );
	}

	/**
	 * Gets a cost based on the base cost and default resource.
	 *
	 * @param  WC_Product_Booking $product
	 * @return string
	 */
	public static function calculated_base_cost( $product ) {
		// If display cost is set, use that always.
		if ( $product->get_display_cost() ) {
			return $product->get_display_cost();
		}

		// Otherwise calculate it.
		$min_duration  = $product->get_min_duration();
		$display_cost  = ( $product->get_block_cost() * $min_duration ) + $product->get_cost();
		$resource_cost = 0;

		if ( $product->has_resources() ) {
			$resources = $product->get_resources();
			$cheapest  = null;

			foreach ( $resources as $resource ) {
				$maybe_cheapest = ( $resource->get_block_cost() * $min_duration ) + $resource->get_base_cost();
				if ( is_null( $cheapest ) || ( $maybe_cheapest < $cheapest ) ) {
					$cheapest = $maybe_cheapest;
				}
			}

			$resource_cost = $cheapest;
		}

		if ( $product->has_persons() && $product->has_person_types() ) {
			$persons       = $product->get_person_types();
			$cheapest      = null;
			$persons_costs = array();

			foreach ( $persons as $person ) {
				$min = $person->get_min();

				if ( empty( $min ) && ! is_numeric( $min ) ) {
					$min = $product->get_min_persons();
				} else {
					$persons_costs[ $person->get_id() ]['min'] = $min;
				}

				$cost = ( ( $person->get_block_cost() * $min_duration ) + $person->get_cost() ) * (float) $min;
				$persons_costs[ $person->get_id() ]['cost'] = $cost;

				if ( ! is_null( $cost ) && ( is_null( $cheapest ) || $cost < $cheapest ) ) {
					$cheapest = $cost;
				}
			}

			if ( ! $product->get_has_person_cost_multiplier() ) {
				$display_cost += $cheapest ? $cheapest : 0;
			}
		}

		if ( $product->has_persons() && $product->has_person_types() && $product->get_has_person_cost_multiplier() ) {
			$persons_total = 0;
			$persons_count = 0;

			foreach ( $persons_costs as $person ) {
				if ( isset( $person['min'] ) ) {
					$persons_total += $person['cost'];
					$persons_count += $person['min'];
				}
			}

			// If count is 0, we use the product setting.
			$persons_count = ( 0 !== $persons_count ) ? $persons_count : $product->get_min_persons();
			// If total is 0, we use the cheapest from previous loop.
			$persons_total = ( 0 !== $persons_total ) ? $persons_total : $cheapest;

			// Don't think about this too hard, your brain will cease to function.
			$display_cost = ( ( $display_cost + $persons_total ) * $persons_count ) + ( $resource_cost * $persons_count );
		} elseif ( $product->has_persons() && $product->get_min_persons() > 1 && $product->get_has_person_cost_multiplier() ) {
			$display_cost = ( $display_cost + $resource_cost ) * $product->get_min_persons();
		} else {
			$display_cost = $display_cost + $resource_cost;
		}

		return $display_cost;
	}
}
