<?php
/**
 * Deprecated functions
 * Where functions come to die.
 *
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! function_exists( 'yith_wcbk_show_deprecated_notices' ) ) {
	/**
	 * Should I show noticed for deprecated functions/hooks?
	 *
	 * @return bool
	 */
	function yith_wcbk_show_deprecated_notices() {
		return ! ! apply_filters( 'yith_wcbk_show_deprecated_notices', true );
	}
}

if ( ! function_exists( 'yith_wcbk_debug_errors_mode' ) ) {
	/**
	 * Should I show noticed for deprecated functions/hooks?
	 *
	 * @return string
	 * @since 3.0.0
	 */
	function yith_wcbk_debug_errors_mode() {
		$available_modes = array( 'trigger_error', 'wc_logger', 'booking_logger', 'error_log' );
		$mode            = 'trigger_error';
		$conditions      = array(
			'is_ajax'             => is_ajax(),
			'is_rest_api_request' => ( is_callable( array( WC(), 'is_rest_api_request' ) ) && WC()->is_rest_api_request() ),
		);

		if ( $conditions['is_ajax'] || $conditions['is_rest_api_request'] ) {
			$mode = 'error_log';
		}

		$filtered_mode = apply_filters( 'yith_wcbk_debug_errors_mode', $mode, $conditions );
		if ( in_array( $filtered_mode, $available_modes, true ) ) {
			$mode = $filtered_mode;
		}

		return $mode;
	}
}

if ( ! function_exists( 'yith_wcbk_debug_errors_trigger' ) ) {
	/**
	 * Should I show noticed for deprecated functions/hooks?
	 *
	 * @param string $message The message to be shown.
	 * @param string $mode    The debug errors mode.
	 *
	 * @since 3.0.0
	 */
	function yith_wcbk_debug_errors_trigger( $message, $mode = false ) {
		$mode = ! ! $mode ? $mode : yith_wcbk_debug_errors_mode();

		switch ( $mode ) {
			case 'wc_logger':
				wc_get_logger()->error( $message, array( 'source' => 'yith-wcbk-debug-errors' ) );
				break;
			case 'booking_logger':
				yith_wcbk_logger()->add( $message, YITH_WCBK_Logger_Types::ERROR, YITH_WCBK_Logger_Groups::DEBUG );
				break;
			case 'error_log':
			default:
				error_log( $message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				break;
		}
	}
}

if ( ! function_exists( 'yith_wcbk_deprecated_function' ) ) {
	/**
	 * Wrapper for deprecated functions, so we can apply some extra logic.
	 *
	 * @param string $function    Function used.
	 * @param string $version     Version the message was added in.
	 * @param string $replacement Replacement for the called function.
	 *
	 * @since 2.1
	 */
	function yith_wcbk_deprecated_function( $function, $version, $replacement = null ) {
		// phpcs:disable
		if ( yith_wcbk_show_deprecated_notices() ) {
			$backtrace   = ' Backtrace: ' . wp_debug_backtrace_summary();
			$errors_mode = yith_wcbk_debug_errors_mode();

			if ( 'trigger_error' === $errors_mode ) {
				_deprecated_function( $function, $version, $replacement );
			} else {
				do_action( 'deprecated_function_run', $function, $replacement, $version );
				$log_string = "The {$function} function is deprecated since version {$version}.";

				$log_string .= $replacement ? " Replace with {$replacement}." : '';
				$log_string .= $backtrace;

				yith_wcbk_debug_errors_trigger( $log_string, $errors_mode );
			}
		}
		// phpcs:enable
	}
}

if ( ! function_exists( 'yith_wcbk_deprecated_hook' ) ) {
	/**
	 * Wrapper for deprecated hook so we can apply some extra logic.
	 *
	 * @param string $hook        The hook that was used.
	 * @param string $version     The Booking plugin version that deprecated the hook.
	 * @param string $replacement The hook that should have been used.
	 * @param string $message     A message regarding the change.
	 *
	 * @since 2.1
	 */
	function yith_wcbk_deprecated_hook( $hook, $version, $replacement = null, $message = null ) {
		// phpcs:disable
		if ( yith_wcbk_show_deprecated_notices() ) {
			$backtrace   = ' Backtrace: ' . wp_debug_backtrace_summary();
			$errors_mode = yith_wcbk_debug_errors_mode();

			if ( 'trigger_error' === $errors_mode ) {
				_deprecated_hook( $hook, $version, $replacement, $message );
			} else {
				do_action( 'deprecated_hook_run', $hook, $replacement, $version, $message );

				$message    = empty( $message ) ? '' : ' ' . $message;
				$log_string = "{$hook} is deprecated since version {$version}";

				$log_string .= $replacement ? "! Use {$replacement} instead." : ' with no alternative available.';
				$log_string .= $message;
				$log_string .= $backtrace;

				yith_wcbk_debug_errors_trigger( $log_string, $errors_mode );
			}
		}
		// phpcs:enable
	}
}

if ( ! function_exists( 'yith_wcbk_deprecated_filter' ) ) {
	/**
	 * Wrapper for deprecated filter hook so we can apply some extra logic.
	 *
	 * @param string $hook        The hook that was used.
	 * @param string $version     The Booking plugin version that deprecated the hook.
	 * @param string $replacement The hook that should have been used.
	 * @param string $message     A message regarding the change.
	 *
	 * @since 2.1
	 */
	function yith_wcbk_deprecated_filter( $hook, $version, $replacement = null, $message = null ) {
		if ( has_filter( $hook ) ) {
			yith_wcbk_deprecated_hook( $hook . ' filter', $version, $replacement, $message );
		}
	}
}

if ( ! function_exists( 'yith_wcbk_deprecated_action' ) ) {
	/**
	 * Wrapper for deprecated action hook so we can apply some extra logic.
	 *
	 * @param string $hook        The hook that was used.
	 * @param string $version     The Booking plugin version that deprecated the hook.
	 * @param string $replacement The hook that should have been used.
	 * @param string $message     A message regarding the change.
	 *
	 * @since 2.1
	 */
	function yith_wcbk_deprecated_action( $hook, $version, $replacement = null, $message = null ) {
		if ( has_action( $hook ) ) {
			yith_wcbk_deprecated_hook( $hook . ' action', $version, $replacement, $message );
		}
	}
}

if ( ! function_exists( 'yith_wcbk_do_deprecated_action' ) ) {
	/**
	 * Fires a deprecated action, printing a notice, only if used.
	 *
	 * @param string $hook        The name of the action hook.
	 * @param array  $args        Function arguments to be passed to do_action().
	 * @param string $version     The Booking plugin version that deprecated the hook.
	 * @param string $replacement The hook that should have been used.
	 * @param string $message     A message regarding the change.
	 *
	 * @since 3.0
	 */
	function yith_wcbk_do_deprecated_action( $hook, $args, $version, $replacement = null, $message = null ) {
		if ( ! has_action( $hook ) ) {
			return;
		}

		yith_wcbk_deprecated_hook( $hook . ' action', $version, $replacement, $message );
		do_action_ref_array( $hook, $args );
	}
}

/**
 * Wrapper for _doing_it_wrong().
 *
 * @param string $function Function used.
 * @param string $message  Message to log.
 * @param string $version  Version the message was added in.
 *
 * @since 3.0.0
 */
function yith_wcbk_doing_it_wrong( $function, $message, $version ) {
	// phpcs:disable
	$message .= ' Backtrace: ' . wp_debug_backtrace_summary();

	$errors_mode = yith_wcbk_debug_errors_mode();

	if ( 'trigger_error' === $errors_mode ) {
		_doing_it_wrong( $function, $message, $version );
	} else {
		do_action( 'doing_it_wrong_run', $function, $message, $version );

		$log_string = "{$function} was called incorrectly. {$message}. This message was added in version {$version}.";

		yith_wcbk_debug_errors_trigger( $log_string, $errors_mode );
	}
	// phpcs:enable
}

/**
 * Trigger an error.
 *
 * @param string $message Message to log.
 *
 * @since 4.0.0
 */
function yith_wcbk_error( $message ) {
	yith_wcbk_debug_errors_trigger( $message );
}

/** ------------------------------------------------------------------------------
 * Deprecated Filters
 */
$deprecated_filters_map = array(
	array(
		'deprecated' => 'yith_wcbk_booking_loaded',
		'since'      => '3.0.0',
		'use'        => 'yith_wcbk_booking_read',
		'params'     => 1,
	),
	array(
		'deprecated' => 'yith_wck_booking_helper_count_booked_bookings_in_period_query_args',
		'since'      => '3.0.0',
		'use'        => 'yith_wcbk_booking_helper_count_booked_bookings_in_period_query_args',
		'params'     => 2,
	),
	array(
		'deprecated' => 'yith_wck_booking_helper_count_booked_bookings_in_period',
		'since'      => '3.0.0',
		'use'        => 'yith_wcbk_booking_helper_count_booked_bookings_in_period',
		'params'     => 2,
	),
	array(
		'deprecated' => 'yith_wcbk_args_for_get_bookings_in_time_range',
		'since'      => '4.0.0',
		'use'        => 'yith_wcbk_booking_helper_get_bookings_in_time_range_args',
		'params'     => 1,
	),
	array(
		'deprecated' => 'yith_booking_cart_item_data',
		'since'      => '4.0.0',
		'use'        => 'yith_wcbk_cart_get_booking_data_from_booking',
		'params'     => 2,
	),
	array(
		'deprecated' => 'yith_wcbk_searched_value_for_field',
		'since'      => '4.0.0',
		'use'        => 'yith_wcbk_get_query_string_param',
		'params'     => 2,
	),
	array(
		'deprecated' => 'yith_wcbk_get_service_name',
		'since'      => '4.0.0',
		'use'        => 'yith_wcbk_service_get_name',
		'params'     => 2,
	),
	array(
		'deprecated' => 'yith_wcbk_booking_service_get_description',
		'since'      => '4.0.0',
		'use'        => 'yith_wcbk_service_get_description',
		'params'     => 2,
	),
	array(
		'deprecated' => 'yith_wcbk_get_name_with_quantity',
		'since'      => '4.0.0',
		'use'        => 'yith_wcbk_service_get_name_with_quantity',
		'params'     => 2,
	),
	array(
		'deprecated' => 'yith_wcbk_json_search_order_number',
		'since'      => '4.4.0',
		'use'        => 'yith_wcbk_json_search_order_term',
		'params'     => 1,
	),
	array(
		'deprecated' => 'yith_wcbkk_create_booking_options',
		'since'      => '4.4.0',
		'use'        => 'yith_wcbk_create_booking_assign_order_options',
		'params'     => 1,
	),
	array(
		'deprecated' => 'yith_wcbk_booking_has_status_set_paid',
		'since'      => '5.1.0',
		'use'        => 'yith_wcbk_orders_should_set_booking_as_paid',
		'params'     => 3,
	),
);

foreach ( $deprecated_filters_map as $deprecated_filter => $options ) {
	$deprecated_filter = $options['deprecated'];
	$new_filter        = $options['use'];
	$params            = $options['params'];
	$since             = $options['since'];
	add_filter(
		$new_filter,
		function () use ( $deprecated_filter, $since, $new_filter ) {
			$args = func_get_args();
			$r    = $args[0];

			if ( has_filter( $deprecated_filter ) ) {
				yith_wcbk_deprecated_hook( $deprecated_filter, $since, $new_filter );

				$r = call_user_func_array( 'apply_filters', array_merge( array( $deprecated_filter ), $args ) );
			}

			return $r;
		},
		10,
		$params
	);
}

/** ------------------------------------------------------------------------------
 * Deprecated functions
 */

if ( ! function_exists( ' yith_wcbk_array_to_external_booking' ) ) {
	/**
	 * Convert array to external bookings.
	 *
	 * @param array $args Arguments.
	 *
	 * @return YITH_WCBK_Booking_External|false
	 * @deprecated since 2.1 | use yith_wcbk_booking_external instead
	 */
	function yith_wcbk_array_to_external_booking( $args ) {
		yith_wcbk_deprecated_function( 'yith_wcbk_array_to_external_booking', '2.1', 'yith_wcbk_booking_external' );

		return function_exists( 'yith_wcbk_booking_external' ) ? yith_wcbk_booking_external( $args ) : false;
	}
}

if ( ! function_exists( 'yith_wcbk_parse_booking_person_types_array' ) ) {
	/**
	 * Parse booking person types
	 *
	 * @param array $person_types Person Types.
	 * @param bool  $reverse      Reverse option.
	 *
	 * @return array
	 * @deprecated since 2.1 | use yith_wcbk_booking_person_types_to_list and yith_wcbk_booking_person_types_to_id_number_array instead
	 */
	function yith_wcbk_parse_booking_person_types_array( $person_types, $reverse = false ) {
		yith_wcbk_deprecated_function( 'yith_wcbk_parse_booking_person_types_array', '2.1', 'yith_wcbk_booking_person_types_to_list and yith_wcbk_booking_person_types_to_id_number_array' );

		return ! $reverse ? yith_wcbk_booking_person_types_to_list( $person_types ) : yith_wcbk_booking_person_types_to_id_number_array( $person_types );
	}
}

if ( ! function_exists( 'yith_wcbk_add_one_day' ) ) {
	/**
	 * Add one day to a date.
	 *
	 * @param string $date The date string.
	 *
	 * @return string
	 * @deprecated 3.0.0
	 */
	function yith_wcbk_add_one_day( $date ) {
		yith_wcbk_deprecated_function( 'yith_wcbk_add_one_day', '3.0.0' );

		return gmdate( 'Y-m-d', strtotime( $date . ' +1 day' ) );
	}
}

if ( ! function_exists( 'yith_wcbk_add_some_day' ) ) {
	/**
	 * Add some days to a date.
	 *
	 * @param string $date        The date string.
	 * @param int    $days_to_add The number of days to add.
	 *
	 * @return string
	 * @deprecated 3.0.0
	 */
	function yith_wcbk_add_some_day( $date, $days_to_add = 1 ) {
		yith_wcbk_deprecated_function( 'yith_wcbk_add_some_day', '3.0.0' );

		return gmdate( 'Y-m-d', strtotime( $date . ' +' . $days_to_add . ' day' ) );
	}
}

if ( ! function_exists( 'yith_wcbk_create_complete_time_array' ) ) {
	/**
	 * Create an array of daily times
	 *
	 * @param string $unit     The unit.
	 * @param int    $duration The duration.
	 *
	 * @return array
	 * @since      2.0.0
	 * @deprecated 3.0.0
	 */
	function yith_wcbk_create_complete_time_array( $unit, $duration = 1 ) {
		yith_wcbk_deprecated_function( 'yith_wcbk_create_complete_time_array', '3.0.0' );
		$times = array();
		if ( in_array( $unit, array( 'hour', 'minute' ), true ) ) {
			$date_helper  = yith_wcbk_date_helper();
			$from         = strtotime( 'now midnight' );
			$tomorrow     = $date_helper->get_time_sum( $from, 1, 'day', true );
			$current_time = $from;

			while ( $current_time < $tomorrow ) {
				$times[]      = gmdate( 'H:i', $current_time );
				$current_time = $date_helper->get_time_sum( $current_time, $duration, $unit );
			}
		}

		return $times;
	}
}

if ( ! function_exists( 'yith_wcbk_get_product_duration_label' ) ) {
	/**
	 * Get product duration label.
	 *
	 * @param string $duration        Duration.
	 * @param string $duration_unit   Duration unit.
	 * @param bool   $is_fixed_blocks Is fixed block flag.
	 *
	 * @return string
	 * @deprecated 3.0.0 | use yith_wcbk_get_duration_label instead.
	 */
	function yith_wcbk_get_product_duration_label( $duration, $duration_unit, $is_fixed_blocks ) {
		yith_wcbk_deprecated_function( 'yith_wcbk_get_product_duration_label', '3.0.0', 'yith_wcbk_get_duration_label' );

		$mode  = $is_fixed_blocks ? 'duration' : 'unit';
		$label = yith_wcbk_get_duration_label( $duration, $duration_unit, $mode );

		if ( has_filter( 'yith_wcbk_get_product_duration_label' ) ) {
			yith_wcbk_deprecated_hook( 'yith_wcbk_get_product_duration_label', '3.0.0' );
			$label = apply_filters( 'yith_wcbk_get_product_duration_label', $label, $duration, $duration_unit, $is_fixed_blocks );
		}

		return $label;
	}
}


if ( ! function_exists( 'yith_wcbk_email_booking_actions' ) ) {
	/**
	 * Print booking actions.
	 *
	 * @param YITH_WCBK_Booking $deprecated_1 Deprecated argument.
	 * @param false             $deprecated_2 Deprecated argument.
	 * @param false             $deprecated_3 Deprecated argument.
	 * @param null              $deprecated_4 Deprecated argument.
	 * @param array             $deprecated_5 Deprecated argument.
	 *
	 * @deprecated 3.0.0 | email actions are printed in booking-details template.
	 */
	function yith_wcbk_email_booking_actions( $deprecated_1, $deprecated_2 = false, $deprecated_3 = false, $deprecated_4 = null, $deprecated_5 = array() ) {
		yith_wcbk_deprecated_function( 'yith_wcbk_email_booking_actions', '3.0.0' );
		// Do nothing.
	}
}

if ( ! function_exists( 'yith_wcbk_get_time_sum' ) ) {
	/**
	 * Retrieve the time sum
	 *
	 * @param int        $time     The timestamp.
	 * @param int        $number   The number to be summed.
	 * @param string     $unit     The unit of the number.
	 * @param bool|false $midnight Set to true to return a midnight timestamp.
	 *
	 * @return int
	 * @deprecated 3.0.0 | use YITH_WCBK_Date_Helper::get_time_sum instead
	 */
	function yith_wcbk_get_time_sum( $time, $number = 0, $unit = 'day', $midnight = false ) {
		yith_wcbk_deprecated_function( 'yith_wcbk_get_time_sum', '3.0.0', 'YITH_WCBK_Date_Helper::get_time_sum' );

		return yith_wcbk_date_helper()->get_time_sum( $time, $number, $unit, $midnight );
	}
}

if ( ! function_exists( 'yith_wcbk_get_time_diff' ) ) {
	/**
	 * Retrieve the time difference
	 *
	 * @param int    $timestamp1 The first timestamp.
	 * @param int    $timestamp2 The second timestamp.
	 * @param string $return     The return type.
	 *
	 * @return bool|DateInterval|int
	 * @deprecated 3.0.0 | use YITH_WCBK_Date_Helper::get_time_diff instead
	 */
	function yith_wcbk_get_time_diff( $timestamp1, $timestamp2, $return = '' ) {
		yith_wcbk_deprecated_function( 'yith_wcbk_get_time_diff', '3.0.0', 'YITH_WCBK_Date_Helper::get_time_diff' );

		return yith_wcbk_date_helper()->get_time_diff( $timestamp1, $timestamp2, $return );
	}
}

if ( ! function_exists( 'yith_wcbk_add_product_class' ) ) {
	/**
	 * Add product class.
	 *
	 * @param array $classes Classes.
	 *
	 * @return array
	 * @deprecated 3.0.0
	 */
	function yith_wcbk_add_product_class( $classes ) {
		yith_wcbk_deprecated_function( 'yith_wcbk_add_product_class', '3.0.0' );
		$classes[] = 'product';

		return $classes;
	}
}

if ( ! function_exists( 'yith_wcbk_format_decimals_with_variables' ) ) {
	/**
	 * Format decimals with variables.
	 *
	 * @param string $price The price.
	 *
	 * @return float|string
	 * @deprecated 3.0.0
	 */
	function yith_wcbk_format_decimals_with_variables( $price ) {
		yith_wcbk_deprecated_function( 'yith_wcbk_format_decimals_with_variables', '3.0.0' );
		if ( strpos( $price, '*' ) ) {
			list( $_price, $variable ) = explode( '*', $price, 2 );

			$price = wc_format_decimal( $_price ) . '*' . $variable;
		} elseif ( strpos( $price, '/' ) ) {
			list( $_price, $variable ) = explode( '/', $price, 2 );

			$price = wc_format_decimal( $_price ) . '/' . $variable;
		} else {
			$price = wc_format_decimal( $price );
		}

		return $price;
	}
}

if ( ! function_exists( 'yith_wcbk_product_metabox_form_field' ) ) {
	/**
	 * Print a form field for product meta-box
	 *
	 * @param array $field The field.
	 *
	 * @since      2.1.0
	 * @deprecated 4.0.0 | Use yith_wcbk_form_field instead.
	 */
	function yith_wcbk_product_metabox_form_field( $field ) {
		yith_wcbk_deprecated_function( 'yith_wcbk_product_metabox_form_field', '4.0.0', 'yith_wcbk_form_field' );
		yith_wcbk_form_field( $field );
	}
}

if ( ! function_exists( 'yith_wcbk_delete_data_for_booking_products' ) ) {
	/**
	 * Delete data for all booking products
	 *
	 * @since      2.0.8
	 * @deprecated 5.0.0 | use yith_wcbk_invalidate_product_cache instead
	 */
	function yith_wcbk_delete_data_for_booking_products() {
		yith_wcbk_deprecated_function( 'yith_wcbk_delete_data_for_booking_products', '5.0.0', 'yith_wcbk_invalidate_product_cache' );
		yith_wcbk_invalidate_product_cache();
	}
}
