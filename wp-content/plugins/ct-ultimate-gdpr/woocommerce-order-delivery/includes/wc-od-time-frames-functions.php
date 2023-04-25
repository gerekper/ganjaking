<?php
/**
 * Time frames functions
 *
 * @package WC_OD/Functions
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the time frame object.
 *
 * @since 1.5.0
 * @since 1.6.0 Returns a WC_OD_Time_Frame object.
 * @since 2.0.0 Accepts a time frame object or ID as parameter. Return false if the time frame is not found.
 *
 * @param mixed $time_frame Time frame object, ID, or an array with data.
 * @return WC_OD_Time_Frame|false
 */
function wc_od_get_time_frame( $time_frame ) {
	if ( $time_frame instanceof WC_OD_Time_Frame ) {
		return $time_frame;
	}

	try {
		return new WC_OD_Time_Frame( $time_frame );
	} catch ( Exception $e ) {
		return false;
	}
}

/**
 * Gets the string representation of the time frame.
 *
 * @since 1.5.0
 * @since 1.6.0 Accepts a WC_OD_Time_Frame object as the parameter.
 *
 * @param mixed  $time_frame Time frame object, ID, or an array with data.
 * @param string $context    Optional. The context.
 * @return string
 */
function wc_od_time_frame_to_string( $time_frame, $context = '' ) {
	$time_frame = wc_od_get_time_frame( $time_frame );

	if ( ! $time_frame ) {
		return '';
	}

	$fee_amount = $time_frame->get_fee_amount();

	// Display fee amount including tax.
	if (
		'checkout' === $context && 0 < $fee_amount && 'taxable' === $time_frame->get_fee_tax_status() &&
		WC()->cart && WC()->cart->display_prices_including_tax()
	) {
		$taxes = WC_Tax::calc_tax( $fee_amount, WC_Tax::get_rates( $time_frame->get_fee_tax_class() ) );

		if ( 'yes' !== get_option( 'woocommerce_tax_round_at_subtotal' ) ) {
			$taxes = array_map( 'wc_round_tax_total', $taxes );
		}

		$fee_amount += array_sum( $taxes );
	}

	if ( 'checkout' === $context && 0 < $fee_amount ) {
		$label = _x( '[time_from] &ndash; [time_to] (+ [fee_amount])', 'Time Frame option label. Allowed tags: [title], [time_from], [time_to], [fee_amount]', 'woocommerce-order-delivery' );
	} else {
		$label = _x( '[time_from] &ndash; [time_to]', 'Time Frame. Allowed tags: [time_from], [time_to], [title]', 'woocommerce-order-delivery' );
	}

	$string = str_replace(
		array(
			'[title]',
			'[time_from]',
			'[time_to]',
			'[fee_amount]',
		),
		array(
			$time_frame->get_title(),
			wc_od_localize_time( $time_frame->get_time_from() ),
			wc_od_localize_time( $time_frame->get_time_to() ),
			wp_strip_all_tags( wc_price( $fee_amount ) ),
		),
		$label
	);

	/**
	 * Filters the string representation of the time frame.
	 *
	 * @since 1.5.0
	 * @since 1.6.0 The parameter `$time_frame` is a WC_OD_Time_Frame object.
	 *
	 * @param string           $string     The time frame string.
	 * @param WC_OD_Time_Frame $time_frame Time frame object.
	 * @param string           $context    The context.
	 */
	return apply_filters( 'wc_od_time_frame_to_string', $string, $time_frame, $context );
}

/**
 * Gets the time frame value to store with the order metadata.
 *
 * @since 1.5.0
 * @since 1.6.0 Accepts a WC_OD_Time_Frame object as the parameter.
 *
 * @param mixed $time_frame Time frame object, ID, or an array with data.
 * @return array An array with the time frame data.
 */
function wc_od_time_frame_to_order( $time_frame ) {
	$time_frame = wc_od_get_time_frame( $time_frame );

	$order_time_frame = $time_frame->get_props( array( 'time_from', 'time_to' ) );

	/**
	 * Filters the time frame value to store with the order metadata.
	 *
	 * @since 1.5.0
	 * @since 1.6.0 The parameter `$time_frame` is a WC_OD_Time_Frame object.
	 *
	 * @param array            $order_time_frame The order time frame data.
	 * @param WC_OD_Time_Frame $time_frame       Time frame object.
	 */
	return apply_filters( 'wc_od_time_frame_to_order', $order_time_frame, $time_frame );
}

/**
 * Parses the time frame ID.
 *
 * @since 1.5.0
 * @since 1.6.0 Accepts a WC_OD_Time_Frame object as the parameter.
 *
 * @param mixed $time_frame The time frame data or just its ID.
 * @return int|false The time frame ID. False otherwise.
 */
function wc_od_parse_time_frame_id( $time_frame ) {
	$time_frame_id = false;

	// Remove the prefix from the string.
	if ( is_string( $time_frame ) && 0 === strpos( $time_frame, 'time_frame:' ) ) {
		$id = str_replace( 'time_frame:', '', $time_frame );

		if ( is_numeric( $id ) ) {
			$time_frame_id = intval( $id );
		}
	} else {
		$time_frame = wc_od_get_time_frame( $time_frame );

		if ( $time_frame && $time_frame->get_id() ) {
			$time_frame_id = $time_frame->get_id();
		}
	}

	return $time_frame_id;
}

/**
 * Gets a time frame by ID for the specified date.
 *
 * @since 1.6.0
 *
 * @param string|int $date The date or timestamp.
 * @param mixed      $id   The time frame ID.
 * @return WC_OD_Time_Frame|null The time frame object. Null on failure.
 */
function wc_od_get_time_frame_for_date( $date, $id ) {
	if ( is_numeric( $id ) ) {
		$time_frame = wc_od_get_time_frame( $id );

		return ( $time_frame ? $time_frame : null );
	}

	// Fetch the time frame by position (Backward compatibility).
	$time_frames = wc_od_get_time_frames_for_date( $date );
	$position    = wc_od_parse_time_frame_id( $id );

	return wc_od_get_time_frame_at_position( $time_frames, $position );
}

/**
 * Gets the time frame at the specified position.
 *
 * @since 2.0.0
 *
 * @param mixed $time_frames A collection or an array of time frames.
 * @param int   $position    The position of the time frame to fetch.
 * @return WC_OD_Time_Frame|null.
 */
function wc_od_get_time_frame_at_position( $time_frames, $position ) {
	if ( ! $time_frames instanceof WC_OD_Collection_Time_Frames ) {
		$time_frames = new WC_OD_Collection_Time_Frames( $time_frames );
	}

	if ( $time_frames->is_empty() ) {
		return null;
	}

	$keys = $time_frames->keys();
	$key  = ( isset( $keys[ $position ] ) ? $keys[ $position ] : - 1 );

	return $time_frames->get( $key );
}

/**
 * Gets the key of the first time frame that matches with the specified parameters.
 *
 * @since 1.5.0
 * @since 1.6.0 The parameter `$time_frames` also accepts a WC_OD_Collection_Time_Frames object.
 *
 * @param mixed $time_frames WC_OD_Collection_Time_Frames object or an array with the time frames data.
 * @param array $params      The parameters to look for.
 * @return mixed The time frame key. False otherwise.
 */
function wc_od_search_time_frame( $time_frames, $params = array() ) {
	if ( is_array( $time_frames ) ) {
		$time_frames = new WC_OD_Collection_Time_Frames( $time_frames );
	}

	$key = $time_frames->search( $params );

	return ( ! is_null( $key ) ? $key : false );
}

/**
 * Gets the time frames for the specified delivery day.
 *
 * @since 1.5.0
 * @since 1.6.0 The `$delivery_day` parameter also accepts a WC_OD_Delivery_Day object. Returns a WC_OD_Collection_Time_Frames object.
 *
 * @param mixed  $delivery_day Delivery day object, ID, or an array with data.
 * @param array  $args         Optional. The additional arguments.
 * @param string $context      Optional. The context.
 * @return WC_OD_Collection_Time_Frames.
 */
function wc_od_get_time_frames_for_delivery_day( $delivery_day, $args = array(), $context = '' ) {
	$defaults = array(
		'shipping_method' => '',
	);

	$delivery_day = wc_od_get_delivery_day( $delivery_day );

	/**
	 * Filters the arguments used to calculate the time frames of the delivery day.
	 *
	 * @since 1.5.0
	 * @since 1.6.0 The `$delivery_day` parameter is a WC_OD_Delivery_Day object.
	 *
	 * @param array              $args         The arguments.
	 * @param WC_OD_Delivery_Day $delivery_day The delivery day.
	 * @param string             $context      The context.
	 */
	$args = apply_filters( 'wc_od_get_time_frames_for_delivery_day_args', wp_parse_args( $args, $defaults ), $delivery_day, $context );

	$time_frames = $delivery_day->get_time_frames();

	// Filter by shipping method.
	if ( $args['shipping_method'] ) {
		$filtered_time_frames = array();

		foreach ( $time_frames as $index => $time_frame ) {
			if ( $time_frame->validate_shipping_method( $args['shipping_method'] ) ) {
				$filtered_time_frames[ $index ] = $time_frame;
			}
		}

		$time_frames = new WC_OD_Collection_Time_Frames( $filtered_time_frames );
	}

	/**
	 * Filters the time frames for the specified delivery day.
	 *
	 * @since 1.5.0
	 * @since 1.6.0 The `$time_frames` parameter is a WC_OD_Collection_Time_Frames object.
	 *              The `$delivery_day` parameter is a WC_OD_Delivery_Day object.
	 *
	 * @param WC_OD_Collection_Time_Frames $time_frames  The time frames collection.
	 * @param array                        $args         The arguments.
	 * @param WC_OD_Delivery_Day           $delivery_day The delivery day.
	 * @param string                       $context      The context.
	 */
	return apply_filters( 'wc_od_get_time_frames_for_delivery_day', $time_frames, $delivery_day, $args, $context );
}

/**
 * Gets the time frames for the specified date.
 *
 * @since 1.5.0
 * @since 1.6.0 Returns a WC_OD_Collection_Time_Frames object.
 *
 * @param string|int $date    The date or timestamp.
 * @param array      $args    Optional. The additional arguments.
 * @param string     $context Optional. The context.
 * @return WC_OD_Collection_Time_Frames.
 */
function wc_od_get_time_frames_for_date( $date, $args = array(), $context = '' ) {
	$timestamp = wc_od_get_timestamp( $date );

	if ( ! $timestamp ) {
		$time_frames = new WC_OD_Collection_Time_Frames();
	} else {
		$time_frames = wc_od_get_time_frames_for_delivery_day( date( 'w', $timestamp ), $args, $context );

		// Remove expired time frames for the current date.
		if ( date( 'Y-m-d', $timestamp ) === wc_od_get_local_date( false ) ) {
			$time_to     = wc_od_get_local_date( false, 'H:i' );
			$time_frames = $time_frames->where( 'time_to', $time_to, '>' );
		}
	}

	/**
	 * Filters the time frames for the specified date.
	 *
	 * @since 1.5.0
	 * @since 1.6.0 The `$time_frames` parameter is a WC_OD_Collection_Time_Frames object.
	 *
	 * @param WC_OD_Collection_Time_Frames $time_frames The time frames collection.
	 * @param int                          $timestamp   The timestamp representing the date.
	 * @param array                        $args        The additional arguments.
	 * @param string                       $context     The context.
	 */
	return apply_filters( 'wc_od_get_time_frames_for_date', $time_frames, $timestamp, $args, $context );
}

/**
 * Gets the time frames choices to use them in a select field.
 *
 * @since 1.5.0
 * @since 1.6.0 The parameter `$time_frames` also accepts a WC_OD_Collection_Time_Frames object.
 *
 * @param mixed  $time_frames WC_OD_Collection_Time_Frames object or an array with the time frames' data.
 * @param string $context     Optional. The context.
 * @return array An array with the choices.
 */
function wc_od_get_time_frames_choices( $time_frames, $context = '' ) {
	$choices = array();

	foreach ( $time_frames as $key => $time_frame ) {
		$value = str_replace( 'new:', 'time_frame:', $key ); // Backward compatibility.

		$choices[ $value ] = esc_html( wc_od_time_frame_to_string( $time_frame, $context ) );
	}

	if ( 1 < count( $choices ) ) {
		// Don't use array_merge to avoid reindexing.
		$choices = array(
			'' => __( 'Choose a time frame', 'woocommerce-order-delivery' ),
		) + $choices;
	}

	/**
	 * Filters the time frames choices to use in a select field.
	 *
	 * @since 1.5.0
	 *
	 * @param array  $choices     The choices.
	 * @param array  $time_frames The time frames.
	 * @param string $context     The context.
	 */
	return apply_filters( 'wc_od_get_time_frames_choices', $choices, $time_frames, $context );
}

/**
 * Gets the time frames choices to use in a select field for the specified date.
 *
 * @since 1.5.0
 *
 * @param string|int $date    The date or timestamp.
 * @param array      $args    Optional. The additional arguments.
 * @param string     $context Optional. The context.
 * @return array An array with the choices.
 */
function wc_od_get_time_frames_choices_for_date( $date, $args = array(), $context = '' ) {
	$time_frames = wc_od_get_time_frames_for_date( $date, $args, $context );

	return wc_od_get_time_frames_choices( $time_frames, $context );
}

/**
 * Checks if a given time frame in a certain date has room for more orders.
 *
 * @param int|string       $timestamp The date timestamp.
 * @param WC_OD_Time_Frame $time_frame A time frame object.
 *
 * @return bool
 */
function wc_od_time_frame_is_full( $timestamp, $time_frame ) {
	$orders           = wc_od_get_orders_to_deliver_in_time_frame( $timestamp, $time_frame->get_time_from(), $time_frame->get_time_to() );
	$number_of_orders = $time_frame->get_number_of_orders();

	// 0 means no limit
	if ( $number_of_orders < 1 ) {
		return false;
	}

	return $orders >= $number_of_orders;
}
