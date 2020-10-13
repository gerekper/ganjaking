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
 * @since 1.6.0 Returns a WC_OD_Time_Frame object from a time frame array data.
 *
 * @param mixed $time_frame WC_OD_Time_Frame object or an array with the time frame data.
 * @param null  $deprecated Deprecated since 1.6.0.
 * @return mixed The time frame object. Null on failure.
 */
function wc_od_get_time_frame( $time_frame, $deprecated = null ) {
	// Backward compatibility.
	if ( ! is_null( $deprecated ) ) {
		wc_deprecated_argument( 'id', '1.6.0', 'Use the function wc_od_get_time_frame_for_date().' );

		$time_frame = wc_od_get_time_frame_for_date( $time_frame, $deprecated );

		return ( ! is_null( $time_frame ) ? $time_frame->to_array() : false );
	}

	return ( is_array( $time_frame ) ? new WC_OD_Time_Frame( $time_frame ) : $time_frame );
}

/**
 * Gets the string representation of the time frame.
 *
 * @since 1.5.0
 * @since 1.6.0 Accepts a WC_OD_Time_Frame object as the parameter.
 *
 * @param mixed  $time_frame WC_OD_Time_Frame object or an array with the time frame data.
 * @param string $context    Optional. The context.
 * @return string
 */
function wc_od_time_frame_to_string( $time_frame, $context = '' ) {
	$string     = '';
	$time_frame = wc_od_get_time_frame( $time_frame );

	if ( $time_frame instanceof WC_OD_Time_Frame ) {
		$string = str_replace(
			array(
				'[title]',
				'[time_from]',
				'[time_to]',
			),
			array(
				$time_frame->get_title(),
				wc_od_localize_time( $time_frame->get_time_from() ),
				wc_od_localize_time( $time_frame->get_time_to() ),
			),
			_x( '[time_from] &ndash; [time_to]', 'Time Frame. Allowed tags: [time_from], [time_to], [title]', 'woocommerce-order-delivery' )
		);
	}

	/**
	 * Filters the string representation of the time frame.
	 *
	 * @since 1.5.0
	 * @since 1.6.0 The parameter `$time_frame` is a WC_OD_Time_Frame object.
	 *
	 * @param string           $string     The time frame string.
	 * @param WC_OD_Time_Frame $time_frame The time frame object.
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
 * @param mixed $time_frame WC_OD_Time_Frame object or an array with the time frame data.
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
	 * @param WC_OD_Time_Frame $time_frame       The time frame object.
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
	$id = ( ( is_array( $time_frame ) || $time_frame instanceof WC_OD_Time_Frame ) ? $time_frame['id'] : $time_frame );

	// Remove the prefix if it exists.
	$id = str_replace( 'time_frame:', '', $id );

	if ( ! is_numeric( $id ) ) {
		return false;
	}

	return intval( $id );
}

/**
 * Gets a time frame by ID for the specified date.
 *
 * @since 1.6.0
 *
 * @param string|int $date The date or timestamp.
 * @param mixed      $id   The time frame Id.
 * @return WC_OD_Time_Frame|null The time frame object. Null on failure.
 */
function wc_od_get_time_frame_for_date( $date, $id ) {
	$id          = wc_od_parse_time_frame_id( $id );
	$time_frames = wc_od_get_time_frames_for_date( $date );

	return $time_frames->get( $id );
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
 * @param mixed  $delivery_day WC_OD_Delivery_Day object, an array with the delivery day data or the weekday number.
 * @param array  $args         Optional. The additional arguments.
 * @param string $context      Optional. The context.
 * @return WC_OD_Collection_Time_Frames.
 */
function wc_od_get_time_frames_for_delivery_day( $delivery_day, $args = array(), $context = '' ) {
	$defaults = array(
		'shipping_method' => false,
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
 * @param mixed  $time_frames WC_OD_Collection_Time_Frames object or an array with the time frames data.
 * @param string $context     Optional. The context.
 * @return array An array with the choices.
 */
function wc_od_get_time_frames_choices( $time_frames, $context = '' ) {
	$choices = array();

	foreach ( $time_frames as $key => $time_frame ) {
		$choices[ 'time_frame:' . $key ] = esc_html( wc_od_time_frame_to_string( $time_frame, $context ) );
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
