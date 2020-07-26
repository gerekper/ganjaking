<?php
/**
 * Shipping methods functions
 *
 * @package WC_OD/Functions
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the shipping method object.
 *
 * @since 1.6.0
 *
 * @param mixed $the_method Shipping method object or instance ID.
 * @return bool|WC_Shipping_Method The shipping method object. False on failure.
 */
function wc_od_get_shipping_method( $the_method ) {
	return ( $the_method instanceof WC_Shipping_Method ? $the_method : WC_Shipping_Zones::get_shipping_method( intval( $the_method ) ) );
}

/**
 * Gets the rates of the specified `Table Rate Shipping` method.
 *
 * @since 1.6.0
 *
 * @param mixed $the_method Shipping method object or instance ID.
 * @return array|bool An array with the rates. False on failure.
 */
function wc_od_get_shipping_table_rates( $the_method ) {
	$shipping_method = wc_od_get_shipping_method( $the_method );

	return ( $shipping_method instanceof WC_Shipping_Table_Rate ? $shipping_method->get_normalized_shipping_rates() : false );
}

/**
 * Gets the shipping table rate by field.
 *
 * @since 1.6.0
 *
 * @param mixed  $the_method Shipping method object or instance ID.
 * @param string $field      The field key.
 * @param mixed  $value      The field value.
 * @return array|bool An array with the rate data. False on failure.
 */
function wc_od_get_shipping_table_rate_by_field( $the_method, $field, $value ) {
	$rates = wc_od_get_shipping_table_rates( $the_method );

	$rate = false;

	if ( ! empty( $rates ) ) {
		$values = wp_list_pluck( $rates, $field );
		$index  = array_search( $value, $values ); // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict

		if ( false !== $index ) {
			$rate = $rates[ $index ];
		}
	}

	return $rate;
}

/**
 * Gets the shipping table rate by ID.
 *
 * @since 1.6.0
 *
 * @param mixed $the_method Shipping method object or instance ID.
 * @param int   $rate_id    The rate ID.
 * @return array|bool An array with the rate data. False on failure.
 */
function wc_od_get_shipping_table_rate_by_id( $the_method, $rate_id ) {
	return wc_od_get_shipping_table_rate_by_field( $the_method, 'rate_id', $rate_id );
}

/**
 * Gets the shipping methods choices to use them in a select field.
 *
 * @since 1.5.0
 *
 * @return array An array with the choices.
 */
function wc_od_get_shipping_methods_choices() {
	$choices = array();

	$zones        = WC_Shipping_Zones::get_zones();
	$default_zone = WC_Shipping_Zones::get_zone( 0 );

	// Add the default shipping zone.
	if ( $default_zone ) {
		$zones[0] = array(
			'zone_id'          => 0,
			'shipping_methods' => $default_zone->get_shipping_methods(),
		);
	}

	foreach ( $zones as $zone ) {
		// Skip empty zones.
		if ( empty( $zone['shipping_methods'] ) ) {
			continue;
		}

		$zone_id = "zone:{$zone['zone_id']}";

		// Add the shipping zone.
		$choices[ $zone_id ] = wc_od_shipping_method_choice_label( $zone_id );

		// Add the shipping methods of the current zone.
		foreach ( $zone['shipping_methods'] as $method_id => $method ) {
			if ( ! wc_string_to_bool( $method->enabled ) ) {
				continue;
			}

			$value = wc_od_shipping_method_choice_value( $method );
			$label = '&nbsp;&nbsp; ' . wc_od_shipping_method_choice_label( $value );

			$choices[ $value ] = $label;

			// Also add the table rates as options.
			if ( 'table_rate' === $method->id ) {
				$rates = wc_od_get_shipping_table_rates( $method );

				if ( ! empty( $rates ) ) {
					foreach ( $rates as $rate ) {
						$rate_value = $value . ':' . $rate['rate_id'];
						$label      = '&nbsp;&nbsp;&nbsp;&nbsp; ' . wc_od_shipping_method_choice_label( $rate_value );

						$choices[ $rate_value ] = $label;
					}
				}
			}
		}
	}

	/**
	 * Filters the shipping methods choices to use them in a select field.
	 *
	 * @since 1.6.0
	 *
	 * @param array $choices The choices.
	 */
	return apply_filters( 'wc_od_get_shipping_methods_choices', $choices );
}

/**
 * Gets the choice value for the specified shipping method.
 *
 * @since 1.7.0
 *
 * @param WC_Shipping_Method $method The shipping method instance.
 * @return string
 */
function wc_od_shipping_method_choice_value( $method ) {
	if ( ! $method instanceof WC_Shipping_Method ) {
		return '';
	}

	return ( $method->id . ':' . $method->instance_id );
}

/**
 * Gets the label for shipping method choice.
 *
 * @since 1.5.0
 *
 * @param string $choice_id The choice ID.
 * @return string
 */
function wc_od_shipping_method_choice_label( $choice_id ) {
	$parts = preg_split( '/:/', $choice_id );

	if ( 2 > count( $parts ) ) {
		return '';
	}

	$title = '';

	// Sanitize zone_id/instance_id.
	$parts[1] = (int) $parts[1];

	if ( 'zone' === $parts[0] ) {
		$zone  = WC_Shipping_Zones::get_zone( $parts[1] );
		$title = ': ' . __( 'All shipping methods', 'woocommerce-order-delivery' );
	} else {
		$zone   = WC_Shipping_Zones::get_zone_by( 'instance_id', $parts[1] );
		$method = WC_Shipping_Zones::get_shipping_method( $parts[1] );

		if ( $method ) {
			$title = ' — ' . ( $method->title ? $method->title : $method->method_title );
		}

		// Table rate shipping.
		if ( 'table_rate' === $parts[0] ) {
			$rate_id = ( isset( $parts[2] ) ? intval( $parts[2] ) : 0 );

			if ( $rate_id ) {
				$rate = wc_od_get_shipping_table_rate_by_id( $method, $rate_id );

				if ( $rate ) {
					$title .= " - {$rate['rate_label']}";
				}
			} else {
				$title .= ': ' . __( 'All rates', 'woocommerce-order-delivery' );
			}
		}
	}

	$label = '';

	if ( $zone && $title ) {
		// Name for the default zone.
		if ( 0 === $zone->get_id() ) {
			$zone->set_zone_name( _x( 'Other locations', 'label for the default shipping zone', 'woocommerce-order-delivery' ) );
		}

		$label = $zone->get_zone_name() . $title;
	}

	/**
	 * Filters the label used to display the shipping method choice.
	 *
	 * @since 1.5.0
	 *
	 * @param string $label     The choice label.
	 * @param string $choice_id The choice ID.
	 */
	return apply_filters( 'wc_od_shipping_method_choice_label', $label, $choice_id );
}

/**
 * Gets all the shipping methods replacing the shipping zones.
 *
 * @since 1.5.0
 * @since 1.6.0 Also replaces the table rate shipping methods by their rates.
 *
 * @param array $shipping_methods The shipping methods to process.
 * @return mixed
 */
function wc_od_expand_shipping_methods( $shipping_methods ) {
	$expanded_methods = array();

	foreach ( $shipping_methods as $index => $shipping_method ) {
		// Replace the zone by all its shipping methods.
		if ( 0 === strpos( $shipping_method, 'zone' ) ) {
			$zone_id = (int) str_replace( 'zone:', '', $shipping_method );
			$zone    = WC_Shipping_Zones::get_zone( $zone_id );

			if ( $zone ) {
				$zone_methods = $zone->get_shipping_methods( true );

				foreach ( $zone_methods as $method ) {
					$expanded_methods[] = wc_od_shipping_method_choice_value( $method );
				}
			}
		} elseif ( 0 === strpos( $shipping_method, 'table_rate' ) ) {
			$parts = preg_split( '/:/', $shipping_method );

			// Replace the table rate shipping method by all its rates.
			if ( 2 === count( $parts ) ) {
				$rates = wc_od_get_shipping_table_rates( $parts[1] );

				if ( ! empty( $rates ) ) {
					foreach ( $rates as $rate ) {
						$expanded_methods[] = "{$shipping_method}:{$rate['rate_id']}";
					}
				}
			} else {
				$expanded_methods[] = $shipping_method;
			}
		} else {
			$expanded_methods[] = $shipping_method;
		}
	}

	/**
	 * Filters the expanded shipping methods.
	 *
	 * @since 1.6.0
	 *
	 * @param array $expanded_methods An array with the expanded shipping methods.
	 * @param array $shipping_methods An array with the shipping methods to parse.
	 */
	return apply_filters( 'wc_od_expand_shipping_methods', array_unique( $expanded_methods ), $shipping_methods );
}

/**
 * Gets the available shipping methods for the specified delivery day.
 *
 * An empty array means that all the shipping methods are available.
 *
 * @since 1.5.0
 * @since 1.6.0 Also accepts a WC_OD_Delivery_Day object as the parameter.
 *              Return only the delivery day shipping methods instead of merging all the shipping methods from its time frames when the parameter `time_frame` is empty.
 *
 * @param mixed  $delivery_day WC_OD_Delivery_Day object, an array with the delivery day data or the weekday number.
 * @param array  $args         Optional. The additional arguments.
 * @param string $context      Optional. The context.
 * @return array
 */
function wc_od_get_shipping_methods_for_delivery_day( $delivery_day, $args = array(), $context = '' ) {
	$defaults = array(
		'time_frame' => '',
	);

	$args = wp_parse_args( $args, $defaults );

	$shipping_methods = array();
	$delivery_day     = wc_od_get_delivery_day( $delivery_day );

	// Use the shipping methods of the time frames if exists.
	if ( $args['time_frame'] && $delivery_day->has_time_frames() ) {
		$time_frame_id = wc_od_parse_time_frame_id( $args['time_frame'] );

		if ( false !== $time_frame_id ) {
			$time_frame = $delivery_day->get_time_frames()->get( $time_frame_id );

			if ( $time_frame ) {
				$shipping_methods = $time_frame->get_shipping_methods();
			}
		}
	} else {
		$shipping_methods = $delivery_day->get_shipping_methods();
	}

	/**
	 * Filters the shipping methods for the specified delivery day.
	 *
	 * @since 1.5.0
	 * @since 1.6.0 The `$delivery_day` parameter is a WC_OD_Delivery_Day object.
	 *
	 * @param array  $time_frames  The time frames.
	 * @param array              $args         The arguments.
	 * @param WC_OD_Delivery_Day $delivery_day The delivery day.
	 * @param string             $context      The context.
	 */
	return apply_filters( 'wc_od_get_shipping_methods_for_delivery_day', wc_od_expand_shipping_methods( $shipping_methods ), $delivery_day, $args, $context );
}

/**
 * Gets the shipping methods for the specified date.
 *
 * @since 1.5.0
 *
 * @param string|int $date    The date or timestamp.
 * @param array      $args    Optional. The additional arguments.
 * @param string     $context Optional. The context.
 * @return array
 */
function wc_od_get_shipping_methods_for_date( $date, $args = array(), $context = '' ) {
	$shipping_methods = array();
	$timestamp        = wc_od_get_timestamp( $date );

	if ( $timestamp ) {
		$shipping_methods = wc_od_get_shipping_methods_for_delivery_day( date( 'w', $timestamp ), $args, $context );
	}

	/**
	 * Filters the shipping methods for the specified date.
	 *
	 * @since 1.5.0
	 *
	 * @param array  $shipping_methods The shipping methods.
	 * @param int    $timestamp        The timestamp representing the date.
	 * @param array  $args             The additional arguments.
	 * @param string $context          The context.
	 */
	return apply_filters( 'wc_od_get_shipping_methods_for_date', $shipping_methods, $timestamp, $args, $context );
}

/**
 * Gets the first shipping method used in the specified order.
 *
 * Returns the string 'method_id:instance_id[:rate_id]'.
 *
 * @since 1.5.0
 * @since 1.6.0 The returned string also includes the rate ID for table rate shipping methods.
 *
 * @param mixed $the_order Post object or post ID of the order.
 * @return string|false The shipping method. False on failure.
 */
function wc_od_get_order_shipping_method( $the_order ) {
	$order = wc_od_get_order( $the_order );

	if ( ! $order ) {
		return false;
	}

	$cache_key       = 'wc_od_order_shipping_method_' . $order->get_id();
	$shipping_method = wp_cache_get( $cache_key, 'shipping_methods' );

	if ( false !== $shipping_method ) {
		return $shipping_method;
	}

	$shipping_items = $order->get_shipping_methods();
	$shipping_item  = reset( $shipping_items );

	if ( ! $shipping_item ) {
		return false;
	}

	/*
	 * Since WC 3.4, the method `WC_Order_Item_Shipping_Data_Store->read` parses the 'method_id' meta and it removes
	 * the 'rate_id' part from the 'table_rate' shipping methods (table_rate:1:2 => table_rate:1).
	 * So, we have to fetch the original value directly from the db.
	 */
	$shipping_item_id = key( $shipping_items );
	$method_id        = get_metadata( 'order_item', $shipping_item_id, 'method_id', true );

	if ( ! $method_id ) {
		return false;
	}

	$shipping_method = $method_id;

	// Maybe add the 'instance_id' to the shipping method.
	if ( ! strstr( $method_id, ':' ) ) {
		$instance_id = ( isset( $shipping_item['instance_id'] ) ? $shipping_item['instance_id'] : null );

		if ( $instance_id || 0 === $instance_id ) {
			$shipping_method .= ":{$instance_id}";
		}
	}

	/*
	 * Look for the 'rate_id' of the 'table_rate' shipping method.
	 * This info is not stored in the 'WC_Order_Item_Shipping' object meta data.
	 */
	if ( false !== strpos( $shipping_method, 'table_rate' ) ) {
		$parts = preg_split( '/:/', $shipping_method );

		if ( 2 === count( $parts ) ) {
			$rate = wc_od_get_shipping_table_rate_by_field( $parts[1], 'rate_label', $shipping_item['method_title'] );

			if ( ! empty( $rate ) && ! empty( $rate['rate_id'] ) ) {
				$shipping_method .= ":{$rate['rate_id']}";
			}
		}
	}

	/**
	 * Filters the first shipping method used in the specified order.
	 *
	 * @since 1.6.0
	 *
	 * @param string   $shipping_method The shipping method.
	 * @param WC_Order $order           The order object.
	 */
	$shipping_method = apply_filters( 'wc_od_get_order_shipping_method', $shipping_method, $order );

	// Cache the result.
	wp_cache_set( $cache_key, $shipping_method, 'shipping_methods' );

	return $shipping_method;
}
