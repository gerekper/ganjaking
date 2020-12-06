<?php

/**
 * Get a booking object
 * @param  int $id
 * @return WC_Booking|false
 */
function get_wc_booking( $id ) {
	try {
		return new WC_Booking( $id );
	} catch ( Exception $e ) {
		return false;
	}
}

/**
 * Get a bookable product object
 * @param  int $id
 * @return WC_Product_Booking|false
 */
function get_wc_product_booking( $id ) {
	try {
		return new WC_Product_Booking( $id );
	} catch ( Exception $e ) {
		return false;
	}
}

/**
 * Gets a cost based on the base cost and default resource.
 *
 * @param  WC_Product_Booking $product
 * @return string
 */
function wc_booking_calculated_base_cost( $product ) {
	wc_deprecated_function( __FUNCTION__, '1.15.0', 'WC_Bookings_Cost_Calculation::calculated_base_cost()' );

	return WC_Bookings_Cost_Calculation::calculated_base_cost( $product );
}

/**
 * Santiize and format a string into a valid 24 hour time
 * @return string
 */
function wc_booking_sanitize_time( $raw_time ) {
	$time = wc_clean( $raw_time );
	$time = date( 'H:i', strtotime( $time ) );
	return $time;
}

/**
 * Returns true if the product is a booking product, false if not
 * @return bool
 */
function is_wc_booking_product( $product ) {
	$booking_product_types = apply_filters( 'woocommerce_bookings_product_types', array( 'booking' ) );
	return isset( $product ) && $product->is_type( $booking_product_types );
}

/**
 * Convert key to a nice readable label
 * @param  string $key
 * @return string
 */
function get_wc_booking_data_label( $key, $product ) {
	$labels = apply_filters( 'woocommerce_bookings_data_labels', array(
		'type'     => ( $product->get_resource_label() ? $product->get_resource_label() : __( 'Booking Type', 'woocommerce-bookings' ) ),
		'date'     => __( 'Booking Date', 'woocommerce-bookings' ),
		'time'     => __( 'Booking Time', 'woocommerce-bookings' ),
		'timezone' => __( 'Time Zone', 'woocommerce-bookings' ),
		'duration' => __( 'Duration', 'woocommerce-bookings' ),
		'persons'  => __( 'Person(s)', 'woocommerce-bookings' ),
	) );

	if ( ! array_key_exists( $key, $labels ) ) {
		return $key;
	}

	return $labels[ $key ];
}

/**
 * Convert status to human readable label.
 *
 * @since  1.10.0
 * @param  string $status
 * @return string
 */
function wc_bookings_get_status_label( $status ) {
	$statuses = array(
		'unpaid'               => __( 'Unpaid', 'woocommerce-bookings' ),
		'pending-confirmation' => __( 'Pending Confirmation', 'woocommerce-bookings' ),
		'confirmed'            => __( 'Confirmed', 'woocommerce-bookings' ),
		'paid'                 => __( 'Paid', 'woocommerce-bookings' ),
		'cancelled'            => __( 'Cancelled', 'woocommerce-bookings' ),
		'complete'             => __( 'Complete', 'woocommerce-bookings' ),
	);

	/**
	 * Filter the return value of wc_bookings_get_status_label.
	 *
	 * @since 1.11.0
	 */
	$statuses = apply_filters( 'woocommerce_bookings_get_status_label', $statuses );

	return array_key_exists( $status, $statuses ) ? $statuses[ $status ] : $status;
}

/**
 * Returns a list of booking statuses.
 *
 * @since 1.9.13 Add new parameter that allows globalised status strings as part of the array.
 * @param  string $context An optional context (filters) for user or cancel statuses
 * @param boolean $include_translation_strings. Defaults to false. This introduces status translations text string. In future (2.0) should default to true.
 * @return array $statuses
 */
function get_wc_booking_statuses( $context = 'fully_booked', $include_translation_strings = false ) {
	if ( 'user' === $context ) {
		$statuses = apply_filters( 'woocommerce_bookings_for_user_statuses', array(
			'unpaid'               => __( 'Unpaid', 'woocommerce-bookings' ),
			'pending-confirmation' => __( 'Pending Confirmation', 'woocommerce-bookings' ),
			'confirmed'            => __( 'Confirmed', 'woocommerce-bookings' ),
			'paid'                 => __( 'Paid', 'woocommerce-bookings' ),
			'cancelled'            => __( 'Cancelled', 'woocommerce-bookings' ),
			'complete'             => __( 'Complete', 'woocommerce-bookings' ),
		) );
	} elseif ( 'cancel' === $context ) {
		$statuses = apply_filters( 'woocommerce_valid_booking_statuses_for_cancel', array(
			'unpaid'               => __( 'Unpaid', 'woocommerce-bookings' ),
			'pending-confirmation' => __( 'Pending Confirmation', 'woocommerce-bookings' ),
			'confirmed'            => __( 'Confirmed', 'woocommerce-bookings' ),
			'paid'                 => __( 'Paid', 'woocommerce-bookings' ),
		) );
	} elseif ( 'scheduled' === $context ) {
		$statuses = apply_filters( 'woocommerce_bookings_scheduled_statuses', array(
			'confirmed'            => __( 'Confirmed', 'woocommerce-bookings' ),
			'paid'                 => __( 'Paid', 'woocommerce-bookings' ),
		) );
	} else {
		$statuses = apply_filters( 'woocommerce_bookings_fully_booked_statuses', array(
			'unpaid'               => __( 'Unpaid', 'woocommerce-bookings' ),
			'pending-confirmation' => __( 'Pending Confirmation', 'woocommerce-bookings' ),
			'confirmed'            => __( 'Confirmed', 'woocommerce-bookings' ),
			'paid'                 => __( 'Paid', 'woocommerce-bookings' ),
			'complete'             => __( 'Complete', 'woocommerce-bookings' ),
			'in-cart'              => __( 'In Cart', 'woocommerce-bookings' ),
		) );
	}

	/**
	 * Filter the return value of get_wc_booking_statuses.
	 *
	 * @since 1.11.0
	 */
	$statuses = apply_filters( 'woocommerce_bookings_get_wc_booking_statuses', $statuses );

	// backwards compatibility
	return $include_translation_strings ? $statuses : array_keys( $statuses );
}

/**
 * Validate and create a new booking manually.
 *
 * @version  1.10.7
 * @see      WC_Booking::new_booking() for available $new_booking_data args
 * @param    int    $product_id you are booking
 * @param    array  $new_booking_data
 * @param    string $status
 * @param    bool   $exact If false, the function will look for the next available block after your start date if the date is unavailable.
 * @return   mixed  WC_Booking object on success or false on fail
 */
function create_wc_booking( $product_id, $new_booking_data = array(), $status = 'confirmed', $exact = false ) {
	// Merge booking data
	$defaults = array(
		'product_id'  => $product_id, // Booking ID
		'start_date'  => '',
		'end_date'    => '',
		'resource_id' => '',
	);

	$new_booking_data = wp_parse_args( $new_booking_data, $defaults );
	$product          = wc_get_product( $product_id );
	$start_date       = $new_booking_data['start_date'];
	$end_date         = $new_booking_data['end_date'];
	$max_date         = $product->get_max_date();
	$all_day          = isset( $new_booking_data['all_day'] ) && $new_booking_data['all_day'] ? true : false;
	$qty = 1;

	if ( $product->has_person_qty_multiplier() && ! empty( $new_booking_data['persons'] ) ) {
		if ( is_array( $new_booking_data['persons'] ) ) {
			$qty = array_sum( $new_booking_data['persons'] );
		} else {
			$qty = $new_booking_data['persons'];
			$new_booking_data['persons'] = array( $qty );
		}
	}

	// If not set, use next available
	if ( ! $start_date ) {
		$min_date   = $product->get_min_date();
		$start_date = strtotime( "+{$min_date['value']} {$min_date['unit']}", current_time( 'timestamp' ) );
	}

	// If not set, use next available + block duration
	if ( ! $end_date ) {
		$end_date = strtotime( '+' . $product->get_duration() . ' ' . $product->get_duration_unit(), $start_date );
	}

	$searching = true;
	$date_diff = $all_day ? DAY_IN_SECONDS : $end_date - $start_date;

	while ( $searching ) {

		$available_bookings = $product->get_available_bookings( $start_date, $end_date, $new_booking_data['resource_id'], $qty );

		if ( $available_bookings && ! is_wp_error( $available_bookings ) ) {

			if ( ! $new_booking_data['resource_id'] && is_array( $available_bookings ) ) {
				$new_booking_data['resource_id'] = current( array_keys( $available_bookings ) );
			}

			$searching = false;

		} else {
			if ( $exact ) {
				return false;
			}

			$start_date += $date_diff;
			$end_date   += $date_diff;

			if ( $end_date > strtotime( "+{$max_date['value']} {$max_date['unit']}" ) ) {
				return false;
			}
		}
	}

	// Set dates
	$new_booking_data['start_date'] = $start_date;
	$new_booking_data['end_date']   = $end_date;

	// Create it
	$new_booking = get_wc_booking( $new_booking_data );
	$new_booking->create( $status );

	return $new_booking;
}

/**
 * Check if product/booking requires confirmation.
 *
 * @param  int $id Product ID.
 *
 * @return bool
 */
function wc_booking_requires_confirmation( $id ) {
	$product = wc_get_product( $id );

	if (
		is_object( $product )
		&& is_wc_booking_product( $product )
		&& $product->requires_confirmation()
	) {
		return true;
	}

	return false;
}

/**
 * Check if the cart has booking that requires confirmation.
 *
 * @return bool
 */
function wc_booking_cart_requires_confirmation() {
	$requires = false;

	if ( ! empty( WC()->cart->cart_contents ) ) {
		foreach ( WC()->cart->cart_contents as $item ) {
			if ( wc_booking_requires_confirmation( $item['product_id'] ) ) {
				$requires = true;
				break;
			}
		}
	}

	return $requires;
}

/**
 * Check if the order has booking that requires confirmation.
 *
 * @param  WC_Order $order
 *
 * @return bool
 */
function wc_booking_order_requires_confirmation( $order ) {
	$requires = false;

	if ( $order ) {
		foreach ( $order->get_items() as $item ) {
			if ( wc_booking_requires_confirmation( $item['product_id'] ) ) {
				$requires = true;
				break;
			}
		}
	}

	return $requires;
}

/**
 * Check if user has location based timezone selected in settings.
 *
 * @since 1.13.0
 *
 * @return bool
 */
function wc_booking_has_location_timezone_set() {

	$timezone = get_option( 'timezone_string' );

	if ( ! empty( $timezone ) && false !== strpos( $timezone, 'Etc/GMT' ) ) {
		$timezone = '';
	}

	if ( empty( $timezone ) ) {
		return false;
	}

	return true;
}

/**
 * Get timezone string.
 *
 * inspired by https://wordpress.org/plugins/event-organiser/
 *
 * @return string
 */
function wc_booking_get_timezone_string() {
	$timezone = wp_cache_get( 'wc_bookings_timezone_string' );

	if ( false === $timezone ) {
		$timezone   = get_option( 'timezone_string' );
		$gmt_offset = get_option( 'gmt_offset' );

		// Remove old Etc mappings. Fallback to gmt_offset.
		if ( ! empty( $timezone ) && false !== strpos( $timezone, 'Etc/GMT' ) ) {
			$timezone = '';
		}

		if ( empty( $timezone ) && 0 != $gmt_offset ) {
			// Use gmt_offset
			$gmt_offset   *= 3600; // convert hour offset to seconds
			$allowed_zones = timezone_abbreviations_list();

			foreach ( $allowed_zones as $abbr ) {
				foreach ( $abbr as $city ) {
					if ( $city['offset'] == $gmt_offset ) {
						$timezone = $city['timezone_id'];
						break 2;
					}
				}
			}
		}

		// Issue with the timezone selected, set to 'UTC'
		if ( empty( $timezone ) ) {
			$timezone = 'UTC';
		}

		// Cache the timezone string.
		wp_cache_set( 'wc_bookings_timezone_string', $timezone );
	}

	return $timezone;
}

/**
 * Get bookable product resources.
 *
 * @param int $product_id product ID.
 *
 * @return array Resources objects list.
 */
function wc_booking_get_product_resources( $product_id ) {
	wc_deprecated_function( __FUNCTION__, '1.15.0', 'WC_Product_Booking->get_resources()' );

	$booking_product = get_wc_product_booking( $product_id );
	return $booking_product->get_resources();
}

/**
 * Get bookable product resource by ID.
 *
 * @param int $product_id product ID.
 * @param int $resource_id resource ID
 *
 * @return array Resources object.
 */
function wc_booking_get_product_resource( $product_id, $resource_id ) {
	wc_deprecated_function( __FUNCTION__, '1.15.0', 'WC_Product_Booking->get_resource()' );

	$booking_product = get_wc_product_booking( $product_id );
	return $booking_product->get_resource( $resource_id );
}

/**
 * get_wc_booking_priority_explanation.
 *
 * @since 1.9.10
 * @return string
 */
function get_wc_booking_rules_explanation() {
	return __( 'Rules with lower priority numbers will override rules with a higher priority (e.g. 9 overrides 10 ). Ordering is only applied within the same priority and higher order overrides lower order.', 'woocommerce-bookings' );
}

/**
 * get_wc_booking_priority_explanation.
 *
 * @return string
 */
function get_wc_booking_priority_explanation() {
	return __( 'Rules with lower priority numbers will override rules with a higher priority (e.g. 9 overrides 10 ). Global rules take priority over product rules which take priority over resource rules. By using priority numbers you can execute rules in different orders.', 'woocommerce-bookings' );
}

/**
 * Get the min timestamp that is bookable based on settings.
 *
 * If $today is the current day, offset starts from NOW rather than midnight.
 *
 * @param int $today Current timestamp, defaults to now.
 * @param int $offset
 * @param string $unit
 * @return int
 */
function wc_bookings_get_min_timestamp_for_day( $date, $offset, $unit ) {
	$timestamp = $date;

	$now = current_time( 'timestamp' );
	$is_today     = date( 'y-m-d', $date ) === date( 'y-m-d', $now );

	if ( $is_today || empty( $date ) ) {
		$timestamp = strtotime( "midnight +{$offset} {$unit}", $now );
	}
	return $timestamp;
}

/**
 * Give this function a booking or resource ID, and a range of dates and get back
 * how many places are available for the requested quantity of bookings for all blocks within those dates.
 *
 * Replaces the WC_Product_Booking::get_available_bookings method.
 *
 * @param  WC_Product_Booking | integer $bookable_product Can be a product object or a booking prouct ID.
 * @param  integer $start_date
 * @param  integer $end_date
 * @param  integer|null optional $resource_id
 * @param  integer $qty
 * @param  array   $intervals
 * @return array|int|boolean|WP_Error False if no places/blocks are available or the dates are invalid.
 */
function wc_bookings_get_total_available_bookings_for_range( $bookable_product, $start_date, $end_date, $resource_id = null, $qty = 1, $intervals = array() ) {
	// alter the end date to limit it to go up to one slot if the setting is enabled
	if ( $bookable_product->get_check_start_block_only() ) {
		$end_date = strtotime( '+ ' . $bookable_product->get_duration() . ' ' . $bookable_product->get_duration_unit(), $start_date );
	}
	// Check the date is not in the past
	if ( date( 'Ymd', $start_date ) < date( 'Ymd', current_time( 'timestamp' ) ) ) {
		return false;
	}
	// Check we have a resource if needed
	$booking_resource = $resource_id ? $bookable_product->get_resource( $resource_id ) : null;
	if ( $bookable_product->has_resources() && ! is_numeric( $resource_id ) ) {
		return false;
	}
	$min_date   = $bookable_product->get_min_date();
	$max_date   = $bookable_product->get_max_date();
	$check_from = strtotime( "midnight +{$min_date['value']} {$min_date['unit']}", current_time( 'timestamp' ) );
	$check_to   = strtotime( "+{$max_date['value']} {$max_date['unit']}", current_time( 'timestamp' ) );
	// Min max checks
	if ( 'month' === $bookable_product->get_duration_unit() ) {
		$check_to = strtotime( 'midnight', strtotime( date( 'Y-m-t', $check_to ) ) );
	}
	if ( $end_date < $check_from || $start_date > $check_to ) {
		return false;
	}
	// Get availability of each resource - no resource has been chosen yet
	if ( $bookable_product->has_resources() && ! $resource_id ) {
		return $bookable_product->get_all_resources_availability( $start_date, $end_date, $qty );
	} else {
		// If we are checking for bookings for a specific resource, or have none.
		$check_date     = $start_date;
		if ( in_array( $bookable_product->get_duration_unit(), array( 'minute', 'hour' ) ) ) {
			if ( ! $bookable_product->check_availability_rules_against_time( $start_date, $end_date, $resource_id ) ) {
				return false;
			}
		} else {
			while ( $check_date < $end_date ) {
				if ( ! $bookable_product->check_availability_rules_against_date( $check_date, $resource_id ) ) {
					return false;
				}
				if ( $bookable_product->get_check_start_block_only() ) {
					break; // Only need to check first day
				}
				$check_date = strtotime( '+1 day', $check_date );
			}
		}
		// Get blocks availability
		return $bookable_product->get_blocks_availability( $start_date, $end_date, $qty, $booking_resource, $intervals );
	}
}

/**
 * Find available and booked blocks for specific resources (if any) and return them as array.
 *
 * @param \WC_Product_Booking $bookable_product
 * @param  array  $blocks
 * @param  array  $intervals
 * @param  integer $resource_id
 * @param  integer $from The starting date for the set of blocks
 * @param  integer $to
 * @return array
 *
 * @version  1.10.5
 */
function wc_bookings_get_time_slots( $bookable_product, $blocks, $intervals = array(), $resource_id = 0, $from = 0, $to = 0, $include_sold_out = false ) {
	$transient_name               = 'book_ts_' . md5( http_build_query( array( $bookable_product->get_id(), $resource_id, $from, $to, $include_sold_out ) ) );
	$available_slots              = WC_Bookings_Cache::get( $transient_name );
	$booking_slots_transient_keys = array_filter( (array) WC_Bookings_Cache::get( 'booking_slots_transient_keys' ) );

	if ( ! isset( $booking_slots_transient_keys[ $bookable_product->get_id() ] ) ) {
		$booking_slots_transient_keys[ $bookable_product->get_id() ] = array();
	}

	if ( ! in_array( $transient_name, $booking_slots_transient_keys[ $bookable_product->get_id() ] ) ) {
		$booking_slots_transient_keys[ $bookable_product->get_id() ][] = $transient_name;
		// Give array of keys a long ttl because if it expires we won't be able to flush the keys when needed.
		// We can't use 0 to never expire because then WordPress will autoload the option on every page.
		WC_Bookings_Cache::set( 'booking_slots_transient_keys', $booking_slots_transient_keys, YEAR_IN_SECONDS );
	}

	if ( false === $available_slots ) {
		if ( empty( $intervals ) ) {
			$default_interval = 'hour' === $bookable_product->get_duration_unit() ? $bookable_product->get_duration() * 60 : $bookable_product->get_duration();
			$interval         = $bookable_product->get_min_duration() * $default_interval;
			$intervals        = array( $interval, $default_interval );
		}

		list( $interval, $base_interval ) = $intervals;
		$interval = $bookable_product->get_check_start_block_only() ? $base_interval : $interval;

		if ( ! $include_sold_out ) {
			$blocks   = $bookable_product->get_available_blocks( array(
				'blocks'      => $blocks,
				'intervals'   => $intervals,
				'resource_id' => $resource_id,
				'from'        => $from,
				'to'          => $to,
			) );
		}

		$booking_resource = $resource_id ? $bookable_product->get_resource( $resource_id ) : null;
		$available_slots  = array();
		$has_qty          = ! is_null( $booking_resource ) ? $booking_resource->has_qty() : false;
		$has_resources    = $bookable_product->has_resources();

		/*
		 * The calculations below are performed using array of minutes for the following reason:
		 *
		 * We are trying to figure-out resource and availability quote for the booking $block.
		 * In order to do that we have to check all other bookings that are already booked and
		 * take time overlapping the $block. Inconveniently bookings in $existing_bookings may come
		 * from products that are different than our $bookable_product. This means that their start
		 * and end times may not be aligned with our product $block and $interval. We can't because of that
		 * check quote and resources 1 to 1 but we need to look at individual minutes.
		 */
		$existing_bookings = WC_Booking_Data_Store::get_all_existing_bookings( $bookable_product, $from, $to );

		foreach ( $blocks as $block ) {
			$resources = array();

			// Figure out how much qty have, either based on combined resource quantity,
			// single resource, or just product.
			if ( $has_resources && ( ! is_a( $booking_resource, 'WC_Product_Booking_Resource' ) || ! $has_qty ) ) {
				$available_qty = 0;

				foreach ( $bookable_product->get_resources() as $resource ) {

					// Only include if it is available for this selection.
					if ( ! WC_Product_Booking_Rule_Manager::check_availability_rules_against_date( $bookable_product, $resource->get_id(), $block ) ) {
						continue;
					}

					if ( in_array( $bookable_product->get_duration_unit(), array( 'minute', 'hour' ) )
						&& ! $bookable_product->check_availability_rules_against_time( $block, strtotime( "+{$interval} minutes", $block ), $resource->get_id() ) ) {
						continue;
					}

					$qty = $resource->get_qty();
					$available_qty += $qty;
					$resources[ $resource->get_id() ] = $qty;
				}
			} elseif ( $has_resources && $has_qty ) {
				// Only include if it is available for this selection. We set this block to be bookable by default, unless some of the rules apply.
				if ( ! $bookable_product->check_availability_rules_against_time( $block, strtotime( "+{$interval} minutes", $block ), $booking_resource->get_id() ) ) {
					continue;
				}

				$qty = $booking_resource->get_qty();
				$available_qty = $qty;
				$resources[ $booking_resource->get_id() ] = $qty;
			} else {
				$available_qty = $bookable_product->get_qty();
				$resources[0] = $bookable_product->get_qty();
			}

			$qty_booked_in_block = 0;

			$inteval_in_minutes = in_array( $bookable_product->get_duration_unit(), array( 'minute', 'hour' ) ) ? $interval : $interval * ( DAY_IN_SECONDS / MINUTE_IN_SECONDS );

			// Prepare ( array ) of minutes we are looking at.
			$block_minutes_array = wc_bookings_get_block_minutes_array( $block, $inteval_in_minutes );

			// Spread resources and booked quote using minutes array.
			$resources = array_map(
				function( $r ) use ( $block_minutes_array ) {
					return array_fill_keys( array_keys( $block_minutes_array ), $r );
				},
				$resources
			);
			$qty_booked_in_block = $block_minutes_array;

			if ( ! empty( $existing_bookings ) ) {
				foreach ( $existing_bookings as $existing_booking ) {
					if ( $existing_booking->is_within_block( $block, strtotime( "+{$inteval_in_minutes} minutes", $block ) ) ) {
						$existing_booking_product    = $existing_booking->get_product();
						$qty_to_add                  = $existing_booking_product->has_person_qty_multiplier() ? max( 1, array_sum( $existing_booking->get_persons() ) ) : 1;
						// Default resource in case we don't have anything in product or existing bookings.
						$res                         = 0;
						if ( $has_resources ) {
							if ( $existing_booking->get_resource_id() === absint( $resource_id ) ) {
								// Include the quantity to subtract if an existing booking matches the selected resource id
								$res = $resource_id;
							} elseif ( ( is_null( $booking_resource ) || ! $has_qty ) && $existing_booking->get_resource() ) {
								// Include the quantity to subtract if the resource is auto selected (null/resource id empty)
								// but the existing booking includes a resource
								$res = $existing_booking->get_resource_id();
							} else {
								// We have resource but the existing booking resource does not overlap so we don't need to take it into consideration.
								continue;
							}
						}

						$existing_booking_block      = $existing_booking->get_start_cached();
						$existing_booking_interval   = ceil( ( $existing_booking->get_end_cached() - $existing_booking_block ) / MINUTE_IN_SECONDS );
						$booking_block_minutes_array = wc_bookings_get_block_minutes_array( $existing_booking_block, $existing_booking_interval );
						$qty_booked_in_block         = wc_bookings_add_at_intersection( $qty_booked_in_block, $booking_block_minutes_array, $qty_to_add );
						$res                         = $has_resources ? $existing_booking->get_resource_id() : 0;
						$resources[ $res ]           = ( isset( $resources[ $res ] ) ? $resources[ $res ] : $block_minutes_array );
						$resources[ $res ]           = wc_bookings_add_at_intersection( $resources[ $res ], $booking_block_minutes_array, - $qty_to_add );
					}
				}
			}

			// The minute with minimal resource value is the actual resource available from the perspective of the whole $block.
			$resources = array_map(
				function( $r ) {
					return min( $r );
				},
				$resources
			);
			// The minute with maximal booked quota value is the actual booked quota from the perspective of the whole $block.
			$qty_booked_in_block = max( $qty_booked_in_block );

			$available_slots[ $block ] = array(
				'booked'    => $qty_booked_in_block,
				'available' => $available_qty - $qty_booked_in_block,
				'resources' => $resources,
			);
		}

		WC_Bookings_Cache::set( $transient_name, $available_slots );
	}

	return $available_slots;
}

/**
 * Take two arrays, check where they intersect,
 * add value to all elements in array1 from the intersection.
 *
 * @since 1.15.13
 *
 * @param array   $array1 First array.
 * @param array   $array1 Second array.
 * @param integer $value  Value to add to the intersection.
 * @return array          First array updated with $value at intersection.
 */
function wc_bookings_add_at_intersection( $array1, $array2, $value ) {
	$timestamps = array_keys( array_intersect_key( $array1, $array2 ) );
	foreach ( $timestamps as $timestamp ) {
		$array1[ $timestamp ] += $value;
	}
	return $array1;
}

/**
 * Generate an array with keys representing minutes of a block of time.
 *
 * @since 1.15.13
 *
 * @param array   $block    Block timestamp.
 * @param integer $interval How long is the block in minutes.
 * @return array            Array with keys representing block minutes.
 */
function wc_bookings_get_block_minutes_array( $block, $interval ) {
	$block_array = [];
	for( $i = 0; $i < $interval; $i++ ) {
		$block_array[ $block + ( $i * MINUTE_IN_SECONDS ) ] = 0;
	}
	return $block_array;
}

/**
 * Builds the HTML to display the start time for hours/minutes.
 *
 * @since 1.13.0
 * @since 1.15.0 Deprecated
 * @param \WC_Product_Booking $bookable_product
 * @param  array  $blocks
 * @param  array  $intervals
 * @param  integer $resource_id
 * @param  integer $from The starting date for the set of blocks
 * @param  integer $to
 * @param  array $available_blocks
 * @return string
 *
 */
function wc_bookings_get_start_time_html( $bookable_product, $blocks, $intervals = array(), $resource_id = 0, $from = 0, $to = 0 ) {
	wc_deprecated_function( __FUNCTION__, '1.15.0', 'WC_Booking_Form::get_start_time_html()' );

	$booking_form = new WC_Booking_form( $bookable_product );

	return $booking_form->get_start_time_html( $blocks, $intervals, $resource_id, $from, $to );
}

/**
 * Builds the data to display the end time for hours/minutes.
 *
 * @since 1.13.0
 * @param \WC_Product_Booking $bookable_product
 * @param  array  $blocks
 * @param  string $start_date_time Date of the start time.
 * @param  array  $intervals
 * @param  integer $resource_id
 * @param  integer $from The starting date for the set of blocks
 * @param  integer $to
 * @param  bool    $check Whether to just check if there's any data at all.
 * @return array
 *
 */
function wc_bookings_get_end_times( $bookable_product, $blocks, $start_date_time = '', $intervals = array(), $resource_id = 0, $from = 0, $to = 0, $check = false ) {
	wc_deprecated_function( __FUNCTION__, '1.15.0', 'WC_Booking_Form::get_end_times()' );

	$booking_form = new WC_Booking_Form( $bookable_product );

	return $booking_form->get_end_times( $blocks, $start_date_time, $intervals, $resource_id, $from, $to, $check );
}

/**
 * Renders the HTML to display the end time for hours/minutes.
 *
 * @since 1.13.0
 * @since 1.15.0 Deprecated
 * @param \WC_Product_Booking $bookable_product
 * @param  array  $blocks
 * @param  string $start_date_time Date of the start time.
 * @param  array  $intervals
 * @param  integer $resource_id
 * @param  integer $from The starting date for the set of blocks
 * @param  integer $to
 * @return string
 *
 */
function wc_bookings_get_end_time_html( $bookable_product, $blocks, $start_date_time = '', $intervals = array(), $resource_id = 0, $from = 0, $to = 0 ) {
	wc_deprecated_function( __FUNCTION__, '1.15.0', 'WC_Booking_Form::get_end_time_html()' );

	$booking_form = new WC_Booking_Form( $bookable_product );

	return $booking_form->get_end_time_html( $blocks, $start_date_time, $intervals, $resource_id, $from, $to );
}

/**
 * Find available blocks and return HTML for the user to choose a block. Used in class-wc-bookings-ajax.php.
 *
 * @param \WC_Product_Booking $bookable_product
 * @param  array  $blocks
 * @param  array  $intervals
 * @param  integer $resource_id
 * @param  integer $from The starting date for the set of blocks
 * @param  integer $to
 * @return string
 *
 * @version  1.10.7
 */
function wc_bookings_get_time_slots_html( $bookable_product, $blocks, $intervals = array(), $resource_id = 0, $from = 0, $to = 0 ) {
	wc_deprecated_function( __FUNCTION__, '1.15.0', 'WC_Booking_Form::get_time_slots_html()' );

	$booking_form = new WC_Booking_Form( $bookable_product );

	return $booking_form->get_time_slots_html( $blocks, $intervals, $resource_id, $from, $to );
}

function get_time_as_iso8601( $timestamp ) {
	$timezone = wc_booking_get_timezone_string();
	$server_time = new DateTime( date( 'Y-m-d\TH:i:s', $timestamp ), new DateTimeZone( $timezone ) );

	return $server_time->format( DateTime::ISO8601 );
}

/**
 * Find available blocks and return HTML for the user to choose a block. Used in class-wc-bookings-ajax.php.
 *
 * @deprecated since 1.10.0
 * @param \WC_Product_Booking $bookable_product
 * @param  array  $blocks
 * @param  array  $intervals
 * @param  integer $resource_id
 * @param  string  $from The starting date for the set of blocks
 * @return string
 */
function wc_bookings_available_blocks_html( $bookable_product, $blocks, $intervals = array(), $resource_id = 0, $from = '' ) {
	_deprecated_function( 'Please use wc_bookings_get_time_slots_html', 'Bookings: 1.10.0' );
	return wc_bookings_get_time_slots_html( $bookable_product, $blocks, $intervals, $resource_id, $from );
}

/**
 * Summary of booking data for admin and checkout.
 *
 * @version 1.10.7
 *
 * @param  WC_Booking $booking
 * @param  bool       $is_admin To determine if this is being called in admin or not.
 */
function wc_bookings_get_summary_list( $booking, $is_admin = false ) {
	$product  = $booking->get_product();
	$resource = $booking->get_resource();
	$label    = $product && is_callable( array( $product, 'get_resource_label' ) ) && $product->get_resource_label() ? $product->get_resource_label() : __( 'Type', 'woocommerce-bookings' );

	$get_local_time = wc_should_convert_timezone( $booking );
	if ( strtotime( 'midnight', $booking->get_start() ) === strtotime( 'midnight', $booking->get_end() ) ) {
		$booking_date = sprintf( '%1$s', $booking->get_start_date( null, null, $get_local_time ) );
	} else {
		$booking_date = sprintf( '%1$s - %2$s', $booking->get_start_date( null, null, $get_local_time ), $booking->get_end_date( null, null, $get_local_time ) );
	}

	$template_args = array(
		'booking'          => $booking,
		'product'          => $product,
		'resource'         => $resource,
		'label'            => $label,
		'booking_date'     => $booking_date,
		'booking_timezone' => str_replace( '_', ' ', $booking->get_local_timezone() ),
		'is_admin'         => $is_admin,
	);

	wc_get_template( 'order/booking-summary-list.php', $template_args, 'woocommerce-bookings', WC_BOOKINGS_TEMPLATE_PATH );
}

/**
 * Converts a string (e.g. yes or no) to a bool.
 * @param  string $string
 * @return boolean
 */
function wc_bookings_string_to_bool( $string ) {
	if ( function_exists( 'wc_string_to_bool' ) ) {
		return wc_string_to_bool( $string );
	}
	return is_bool( $string ) ? $string : ( 'yes' === $string || 1 === $string || 'true' === $string || '1' === $string );
}

/**
 * @since 1.10.0
 * @param $minute
 * @param $check_date
 *
 * @return int
 */
function wc_booking_minute_to_time_stamp( $minute, $check_date ) {
	return strtotime( "+ $minute minutes", $check_date );
}

/**
 * Convert a timestamp into the minutes after 0:00
 *
 * @since 1.10.0
 * @param integer $timestamp
 * @return integer $minutes_after_midnight
 */
function wc_booking_time_stamp_to_minutes_after_midnight( $timestamp ) {
	$hour = absint( date( 'H', $timestamp ) );
	$min  = absint( date( 'i', $timestamp ) );
	return  $min + ( $hour * 60 );
}

/**
 * Get timezone offset in seconds.
 *
 * @since  1.10.3
 * @return float
 */
function wc_booking_timezone_offset() {
	$timezone = get_option( 'timezone_string' );
	if ( $timezone ) {
		$timezone_object = new DateTimeZone( $timezone );
		return $timezone_object->getOffset( new DateTime( 'now' ) );
	} else {
		return floatval( get_option( 'gmt_offset', 0 ) ) * HOUR_IN_SECONDS;
	}
}

/**
 * Determine whether Booking time should be converted to local time.
 *
 * @since  1.11.4
 * @return bool
 */
function wc_should_convert_timezone( $booking = null ) {
	if ( 'no' === WC_Bookings_Timezone_Settings::get( 'use_client_timezone' ) ) {
		return false;
	}

	// If we don't have a booking, just use the setting and return true
	if ( is_null( $booking ) ) {
		return true;
	}

	// If a Booking exists, make sure the local timezone is populated (does not happen for day duration e.g.)
	return ! empty( $booking->get_local_timezone() );
}

if ( ! function_exists( 'wc_string_to_timestamp' ) ) {
	/**
	 * Convert mysql datetime to PHP timestamp, forcing UTC. Wrapper for strtotime.
	 *
	 * Based on wcs_strtotime_dark_knight() from WC Subscriptions by Prospress.
	 *
	 * @since  3.0.0
	 *
	 * @param  string $time_string Time string.
	 * @param  int|null $from_timestamp Timestamp to convert from.
	 *
	 * @return int
	 */
	function wc_string_to_timestamp( $time_string, $from_timestamp = null ) {
		$original_timezone = date_default_timezone_get();
		// @codingStandardsIgnoreStart
		date_default_timezone_set( 'UTC' );
		if ( null === $from_timestamp ) {
			$next_timestamp = strtotime( $time_string );
		} else {
			$next_timestamp = strtotime( $time_string, $from_timestamp );
		}
		date_default_timezone_set( $original_timezone );

		// @codingStandardsIgnoreEnd
		return $next_timestamp;
	}
}

if ( ! function_exists( 'wc_timezone_offset' ) ) {
	/**
	 * Get timezone offset in seconds.
	 *
	 * @since  3.0.0
	 * @return float
	 */
	function wc_timezone_offset() {
		$timezone = get_option( 'timezone_string' );
		if ( $timezone ) {
			$timezone_object = new DateTimeZone( $timezone );

			return $timezone_object->getOffset( new DateTime( 'now' ) );
		} else {
			return floatval( get_option( 'gmt_offset', 0 ) ) * HOUR_IN_SECONDS;
		}
	}
}

/**
 * Clear booking slots transient.
 *
 * In contexts where we have a product id, it will only delete the specific ones.
 * However, not all contexts will have a product id, e.g. Global Availability.
 *
 * @param  int|null $bookable_product_id
 * @since  1.13.12
 */
function delete_booking_slots_transient( $bookable_product_id = null ) {
	wc_deprecated_function( __FUNCTION__, '1.15.0', 'WC_Bookings_Cache::delete_booking_slots_transient()' );

	return WC_Bookings_Cache::delete_booking_slots_transient( $bookable_product_id );
}

/**
 * Renders a json object with a paginated availability set.
 *
 * @since 1.14.0
 */
function wc_bookings_paginated_availability( $availability, $page, $records_per_page ) {
	$records = array();

	if ( false === $page ) {
		$records = $availability;
	} else {
		$records = array_slice( $availability, ( $page - 1 ) * $records_per_page, $records_per_page );
	}
	$paginated_booking_slots = array(
		'records' => $records,
		'count' => count( $availability ),
	);

	return $paginated_booking_slots;
}

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\DeviceParserAbstract;

/**
 * Get posted form data into a neat array.
 *
 * @since 1.15.0
 * @param  array $posted
 * @param  object $product
 * @return array
 */
function wc_bookings_get_posted_data( $posted, $product ) {
	if ( empty( $posted ) ) {
		$posted = $_POST;
	}

	$data = array(
		'_year'    => '',
		'_month'   => '',
		'_day'     => '',
		'_persons' => array(),
	);

	// Get date fields (y, m, d)
	if ( ! empty( $posted['wc_bookings_field_start_date_year'] ) && ! empty( $posted['wc_bookings_field_start_date_month'] ) && ! empty( $posted['wc_bookings_field_start_date_day'] ) ) {
		$data['_year']  = absint( $posted['wc_bookings_field_start_date_year'] );
		$data['_year']  = $data['_year'] ? $data['_year'] : date( 'Y' );
		$data['_month'] = absint( $posted['wc_bookings_field_start_date_month'] );
		$data['_day']   = absint( $posted['wc_bookings_field_start_date_day'] );
		$data['_date']  = $data['_year'] . '-' . $data['_month'] . '-' . $data['_day'];
		$data['date']   = date_i18n( wc_bookings_date_format(), strtotime( $data['_date'] ) );
	}

	// Get year month field
	if ( ! empty( $posted['wc_bookings_field_start_date_yearmonth'] ) ) {
		$yearmonth      = strtotime( $posted['wc_bookings_field_start_date_yearmonth'] . '-01' );
		$data['_year']  = absint( date( 'Y', $yearmonth ) );
		$data['_month'] = absint( date( 'm', $yearmonth ) );
		$data['_day']   = 1;
		$data['_date']  = $data['_year'] . '-' . $data['_month'] . '-' . $data['_day'];
		$data['date']   = date_i18n( 'F Y', $yearmonth );
	}

	// Get time field
	if ( ! empty( $posted['wc_bookings_field_start_date_time'] ) ) {
		$date_time      = new DateTime( $posted['wc_bookings_field_start_date_time'] ); // Contains ISO 8061 formatted datetime
		$data['_year']  = $date_time->format( 'Y' );
		$data['_month'] = $date_time->format( 'm' );
		$data['_day']   = $date_time->format( 'd' );
		$data['_date']  = $data['_year'] . '-' . $data['_month'] . '-' . $data['_day'];
		$data['date']   = date_i18n( wc_bookings_date_format(), strtotime( $data['_date'] ) );
		$data['_time']  = $date_time->format( 'G:i' );
		$data['time']   = date_i18n( wc_bookings_time_format(), strtotime( "{$data['_year']}-{$data['_month']}-{$data['_day']} {$data['_time']}" ) );
	} else {
		$data['_time']  = '';
	}

	// Quantity being booked
	$data['_qty'] = 1;

	// Work out persons
	if ( $product->has_persons() ) {
		if ( $product->has_person_types() ) {
			$person_types = $product->get_person_types();

			foreach ( $person_types as $person_type ) {
				if ( isset( $posted[ 'wc_bookings_field_persons_' . $person_type->ID ] )
					&& absint( $posted[ 'wc_bookings_field_persons_' . $person_type->ID ] ) > 0 ) {
					$data[ $person_type->post_title ]     = absint( $posted[ 'wc_bookings_field_persons_' . $person_type->ID ] );
					$data['_persons'][ $person_type->ID ] = $data[ $person_type->post_title ];
				}
			}
		} elseif ( isset( $posted['wc_bookings_field_persons'] ) ) {
			$data[ __( 'Persons', 'woocommerce-bookings' ) ] = absint( $posted['wc_bookings_field_persons'] );
			$data['_persons'][0]                             = absint( $posted['wc_bookings_field_persons'] );
		}

		if ( $product->get_has_person_qty_multiplier() ) {
			$data['_qty'] = array_sum( $data['_persons'] );
		}
	}

	// Duration
	if ( 'customer' == $product->get_duration_type() ) {
		$booking_duration       = isset( $posted['wc_bookings_field_duration'] ) ? max( 0, absint( $posted['wc_bookings_field_duration'] ) ) : 0;
		$booking_duration_unit  = $product->get_duration_unit();

		$data['_duration_unit'] = $booking_duration_unit;
		$data['_duration']      = $booking_duration;

		// Get the duration * block duration
		$total_duration = $booking_duration * $product->get_duration();

		// Nice formatted version
		switch ( $booking_duration_unit ) {
			case 'month':
				$data['duration'] = $total_duration . ' ' . _n( 'month', 'months', $total_duration, 'woocommerce-bookings' );
				break;
			case 'day':
				if ( $total_duration % 7 ) {
					$data['duration']  = $total_duration . ' ' . _n( 'day', 'days', $total_duration, 'woocommerce-bookings' );
				} else {
					$duration_in_weeks = ( $total_duration / 7 );
					$data['duration']  = $duration_in_weeks . ' ' . _n( 'week', 'weeks', $duration_in_weeks, 'woocommerce-bookings' );
				}
				break;
			case 'hour':
				$data['duration'] = $total_duration . ' ' . _n( 'hour', 'hours', $total_duration, 'woocommerce-bookings' );
				break;
			case 'minute':
				$data['duration'] = $total_duration . ' ' . _n( 'minute', 'minutes', $total_duration, 'woocommerce-bookings' );
				break;
			case 'night':
				$data['duration'] = $total_duration . ' ' . _n( 'night', 'nights', $total_duration, 'woocommerce-bookings' );
				break;
			default:
				$data['duration'] = $total_duration;
				break;
		}
	} else {
		// Fixed duration
		$booking_duration      = $product->get_duration();
		$booking_duration_unit = $product->get_duration_unit();
		$total_duration        = $booking_duration;
	}

	// Work out start and end dates/times
	if ( ! empty( $data['_time'] ) ) {
		$data['_start_date'] = strtotime( "{$data['_year']}-{$data['_month']}-{$data['_day']} {$data['_time']}" );
		$data['_end_date']   = strtotime( "+{$total_duration} {$booking_duration_unit}", $data['_start_date'] );
		$data['_all_day']    = 0;
	} else {
		$data['_start_date'] = strtotime( "{$data['_year']}-{$data['_month']}-{$data['_day']}" );
		// We need the following calculation to not add extra days (see #2147)
		$data['_end_date']   = strtotime( "+{$total_duration} {$booking_duration_unit} - 1 second", $data['_start_date'] );
		$data['_all_day']    = 1;
	}

	// Get posted resource or assign one for the date range
	if ( $product->has_resources() ) {
		if ( $product->is_resource_assignment_type( 'customer' ) ) {
			$resource = $product->get_resource( isset( $posted['wc_bookings_field_resource'] ) ? absint( $posted['wc_bookings_field_resource'] ) : 0 );

			if ( $resource ) {
				$data['_resource_id'] = $resource->ID;
				$data['type']         = $resource->post_title;
			} else {
				$data['_resource_id'] = 0;
			}
		} else {
			// Assign an available resource automatically
			$available_bookings = wc_bookings_get_total_available_bookings_for_range( $product, $data['_start_date'], $data['_end_date'], 0, $data['_qty'] );

			if ( is_array( $available_bookings ) ) {
				$data['_resource_id'] = current( array_keys( $available_bookings ) );
				$data['type']         = get_the_title( current( array_keys( $available_bookings ) ) );
			}
		}
	}

	$data['_local_timezone'] = '';
	if ( ! empty( $posted['wc_bookings_field_start_date_local_timezone'] ) ) {
		$data['_local_timezone'] = $posted['wc_bookings_field_start_date_local_timezone'];
	}

	return apply_filters( 'woocommerce_booking_form_get_posted_data', $data, $product, $total_duration );
}

/**
 * Get an array of formatted time values.
 *
 * @since 1.15.0
 * @param  string $timestamp
 * @return array
 */
function wc_bookings_get_formatted_times( $timestamp ) {
	return array(
		'timestamp'   => $timestamp,
		'year'        => intval( date( 'Y', $timestamp ) ),
		'month'       => intval( date( 'n', $timestamp ) ),
		'day'         => intval( date( 'j', $timestamp ) ),
		'week'        => intval( date( 'W', $timestamp ) ),
		'day_of_week' => intval( date( 'N', $timestamp ) ),
		'time'        => date( 'YmdHi', $timestamp ),
	);
}

/**
 * Attempt to convert a date formatting string from PHP to Moment.
 *
 * @param string $format
 * @return string
 */
function wc_bookings_convert_to_moment_format( $format ) {
	$replacements = array(
		'd' => 'DD',
		'D' => 'ddd',
		'j' => 'D',
		'l' => 'dddd',
		'N' => 'E',
		'S' => 'o',
		'w' => 'e',
		'z' => 'DDD',
		'W' => 'W',
		'F' => 'MMMM',
		'm' => 'MM',
		'M' => 'MMM',
		'n' => 'M',
		't' => '', // no equivalent
		'L' => '', // no equivalent
		'o' => 'YYYY',
		'Y' => 'YYYY',
		'y' => 'YY',
		'a' => 'a',
		'A' => 'A',
		'B' => '', // no equivalent
		'g' => 'h',
		'G' => 'H',
		'h' => 'hh',
		'H' => 'HH',
		'i' => 'mm',
		's' => 'ss',
		'u' => 'SSS',
		'e' => 'zz', // deprecated since version 1.6.0 of moment.js
		'I' => '', // no equivalent
		'O' => '', // no equivalent
		'P' => '', // no equivalent
		'T' => '', // no equivalent
		'Z' => '', // no equivalent
		'c' => '', // no equivalent
		'r' => '', // no equivalent
		'U' => 'X',
	);

	return strtr( $format, $replacements );
}

/**
 * Return WP's time format, defaulting to a non-empty one if it is unset.
 *
 * @return string
 */
function wc_bookings_time_format() {
	return wc_time_format() ?: 'g:i a';
}

/**
 * Return WP's date format, defaulting to a non-empty one if it is unset.
 *
 * @return string
 */
function wc_bookings_date_format() {
	return wc_date_format() ?: 'F j, Y';
}

/**
 * Search bookings.
 *
 * @param  string $term Term to search.
 * @return array List of bookings ID.
 */
function wc_booking_search( $term ) {
	$data_store = WC_Data_Store::load( 'booking' );
	return $data_store->search_bookings( str_replace( 'Booking #', '', wc_clean( $term ) ) );
}
