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
 * Gets whether the shipping method has rates.
 *
 * @since 2.2.0
 *
 * @param mixed $the_method Shipping method object or instance ID.
 * @return bool
 */
function wc_od_shipping_method_has_rates( $the_method ) {
	$shipping_method = wc_od_get_shipping_method( $the_method );

	if ( ! $shipping_method ) {
		return false;
	}

	/**
	 * Filters whether the shipping method has rates.
	 *
	 * The dynamic portion of the hook name refers to the shipping method ID.
	 *
	 * @since 2.2.0
	 *
	 * @param bool               $has_rates       True if the shipping method has rates. False otherwise.
	 * @param WC_Shipping_Method $shipping_method Shipping method object.
	 */
	return apply_filters( "wc_od_{$shipping_method->id}_shipping_method_has_rates", false, $shipping_method );
}

/**
 * Gets the rate IDs for the specified shipping method.
 *
 * @since 2.2.0
 *
 * @param mixed $the_method Shipping method object or instance ID.
 * @return array
 */
function wc_od_get_shipping_method_rate_ids( $the_method ) {
	$shipping_method = wc_od_get_shipping_method( $the_method );

	if ( ! $shipping_method || ! wc_od_shipping_method_has_rates( $shipping_method ) ) {
		return array();
	}

	/**
	 * Filters the rate IDs of a shipping method.
	 *
	 * The dynamic portion of the hook name refers to the shipping method ID.
	 *
	 * @since 2.2.0
	 *
	 * @param array              $rate_ids        An array with the rate IDs.
	 * @param WC_Shipping_Method $shipping_method Shipping method object.
	 */
	return apply_filters( "wc_od_{$shipping_method->id}_shipping_method_rate_ids", array(), $shipping_method );
}

/**
 * Gets the shipping methods choices to use them in a select field.
 *
 * @since 1.5.0
 *
 * @return array An array with the choices.
 */
function wc_od_get_shipping_methods_choices() {
	/**
	 * Filters the shipping methods choices for a select field.
	 *
	 * @since 1.6.0
	 *
	 * @param array $choices The shipping methods choices.
	 */
	return apply_filters( 'wc_od_get_shipping_methods_choices', WC_OD_Shipping_Methods_Selector::get_options() );
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
	return WC_OD_Shipping_Methods_Selector::get_option_value_for_method( $method );
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
	$label = WC_OD_Shipping_Methods_Selector::get_option_label( $choice_id );

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

	foreach ( $shipping_methods as $shipping_method ) {
		$parts = explode( ':', $shipping_method );

		if ( 2 > count( $parts ) ) {
			continue;
		}

		// Replace the zone by all its shipping methods.
		if ( 'zone' === $parts[0] ) {
			$zone = WC_Shipping_Zones::get_zone( $parts[1] );

			if ( $zone ) {
				$methods = $zone->get_shipping_methods( true );

				foreach ( $methods as $method ) {
					$expanded_methods[] = wc_od_shipping_method_choice_value( $method );
				}
			}
		} elseif ( 2 === count( $parts ) && wc_od_shipping_method_has_rates( $parts[1] ) ) {
			// Replace the shipping method by all its rates.
			$rate_ids = wc_od_get_shipping_method_rate_ids( $parts[1] );

			foreach ( $rate_ids as $rate_id ) {
				$expanded_methods[] = "{$shipping_method}:{$rate_id}";
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
			$time_frame = $delivery_day->get_time_frame( $time_frame_id );

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

	$value = $shipping_item->get_method_id() . ':' . $shipping_item->get_instance_id();

	/**
	 * Filters the value of the order shipping method.
	 *
	 * @since 2.2.0
	 *
	 * @param string                 $value         The shipping method value.
	 * @param WC_Order_Item_Shipping $shipping_item Order item shipping object.
	 * @param WC_Order               $order         Order object.
	 */
	$shipping_method = apply_filters( 'wc_od_order_shipping_method_value', $value, $shipping_item, $order );

	if ( has_filter( 'wc_od_get_order_shipping_method' ) ) {
		wc_deprecated_hook( 'wc_od_get_order_shipping_method', '2.2.0', 'wc_od_order_shipping_method_value' );

		/**
		 * Filters the first shipping method used in the specified order.
		 *
		 * @since 1.6.0
		 * @deprecated 2.2.0
		 *
		 * @param string   $shipping_method The shipping method.
		 * @param WC_Order $order           The order object.
		 */
		$shipping_method = apply_filters( 'wc_od_get_order_shipping_method', $shipping_method, $order );
	}

	// Cache the result.
	wp_cache_set( $cache_key, $shipping_method, 'shipping_methods' );

	return $shipping_method;
}
