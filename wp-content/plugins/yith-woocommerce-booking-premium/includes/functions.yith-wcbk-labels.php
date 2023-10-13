<?php
/**
 * Labels Functions
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Functions
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! function_exists( 'yith_wcbk_get_duration_unit_label' ) ) {
	/**
	 * Get duration unit label.
	 *
	 * @param string $duration_unit  Duration unit.
	 * @param int    $plural_control Plural control.
	 *
	 * @return string
	 */
	function yith_wcbk_get_duration_unit_label( $duration_unit, $plural_control = 1 ) {
		$units         = yith_wcbk_get_duration_units( $plural_control );
		$duration_unit = $units[ $duration_unit ] ?? '';

		return $duration_unit;
	}
}

if ( ! function_exists( 'yith_wcbk_format_duration' ) ) {
	/**
	 * Format the duration; example: "XX days"
	 *
	 * @param int    $duration Duration.
	 * @param string $unit     Duration unit.
	 * @param string $mode     The mode (allowed values: duration | unit | period).
	 *
	 * @return string
	 * @since 3.0.0 added $mode param.
	 */
	function yith_wcbk_format_duration( $duration, $unit, $mode = 'duration' ) {
		$plural       = $duration > 1;
		$label_string = yith_wcbk_get_duration_label_string( $unit, $plural, $mode );
		$label        = ! ! $label_string ? sprintf( $label_string, $duration ) : '';

		return apply_filters( 'yith_wcbk_format_duration', $label, $duration, $unit );
	}
}

if ( ! function_exists( 'yith_wcbk_get_booking_meta_label' ) ) {
	/**
	 * Get booking meta label.
	 *
	 * @param string $key The key.
	 *
	 * @return string
	 */
	function yith_wcbk_get_booking_meta_label( $key ) {
		$booking_meta_labels = apply_filters(
			'yith_wcbk_booking_meta_labels',
			array(
				'from'     => yith_wcbk_get_label( 'from' ),
				'to'       => yith_wcbk_get_label( 'to' ),
				'duration' => yith_wcbk_get_label( 'duration' ),
				'persons'  => yith_wcbk_get_label( 'people' ),
			)
		);
		$label               = array_key_exists( $key, $booking_meta_labels ) ? $booking_meta_labels[ $key ] : $key;

		return apply_filters( 'yith_wcbk_get_booking_meta_label', $label, $key, $booking_meta_labels );
	}
}

if ( ! function_exists( 'yith_wcbk_get_label' ) ) {
	/**
	 * Get a label
	 *
	 * @param string $key The label key.
	 *
	 * @return string
	 */
	function yith_wcbk_get_label( $key ) {
		return yith_wcbk()->language->get_label( $key );
	}
}

if ( ! function_exists( 'yith_wcbk_get_default_label' ) ) {
	/**
	 * Get the default label
	 *
	 * @param string $key The label key.
	 *
	 * @return string
	 */
	function yith_wcbk_get_default_label( $key ) {
		return yith_wcbk()->language->get_default_label( $key );
	}
}

if ( ! function_exists( 'yith_wcbk_get_duration_label' ) ) {
	/**
	 * Get duration label.
	 *
	 * @param int    $duration      Duration.
	 * @param string $duration_unit Duration unit.
	 * @param string $mode          The mode (allowed values: duration | unit | period).
	 *
	 * @return string
	 * @since 3.0.0
	 */
	function yith_wcbk_get_duration_label( $duration, $duration_unit, $mode ) {
		$label_string = yith_wcbk_get_duration_label_string( $duration_unit, $duration > 1, $mode );
		$label        = sprintf( $label_string, $duration );

		return apply_filters( 'yith_wcbk_get_duration_label', $label, $duration, $duration_unit, $mode );
	}
}

if ( ! function_exists( 'yith_wcbk_get_duration_label_string' ) ) {
	/**
	 * Get duration label string.
	 *
	 * @param string      $duration_unit Duration unit.
	 * @param bool        $plural        Plural flag.
	 * @param bool|string $mode          The mode (allowed values: duration | unit | period).
	 *
	 * @return string
	 * @since 2.1.0
	 * @since 3.0.0 $mode params is a string with 3 different values: duration | unit | period.
	 */
	function yith_wcbk_get_duration_label_string( $duration_unit, $plural = false, $mode = 'duration' ) {
		$duration = ! $plural ? 1 : 2;
		if ( is_bool( $mode ) ) {
			// backward-compatibility.
			$mode = ! ! $mode ? 'unit' : 'duration';
		}

		// phpcs:disable WordPress.WP.I18n.MismatchedPlaceholders, WordPress.WP.I18n.MissingSingularPlaceholder
		$labels = array(
			'duration' => array(
				// translators: %s is the number of months.
				'month'  => _n( '%s month', '%s months', $duration, 'yith-booking-for-woocommerce' ),
				// translators: %s is the number of weeks.
				'week'   => _n( '%s week', '%s weeks', $duration, 'yith-booking-for-woocommerce' ),
				// translators: %s is the number of days.
				'day'    => _n( '%s day', '%s days', $duration, 'yith-booking-for-woocommerce' ),
				// translators: %s is the number of hours.
				'hour'   => _n( '%s hour', '%s hours', $duration, 'yith-booking-for-woocommerce' ),
				// translators: %s is the number of minutes.
				'minute' => _n( '%s minute', '%s minutes', $duration, 'yith-booking-for-woocommerce' ),
				// translators: %s is the number of seconds.
				'second' => _n( '%s second', '%s seconds', $duration, 'yith-booking-for-woocommerce' ),
			),
			'unit'     => array(
				// translators: %s is the number of months.
				'month'  => _n( 'month(s)', '&times; %s months', $duration, 'yith-booking-for-woocommerce' ),
				// translators: %s is the number of weeks.
				'week'   => _n( 'week(s)', '&times; %s weeks', $duration, 'yith-booking-for-woocommerce' ),
				// translators: %s is the number of days.
				'day'    => _n( 'day(s)', '&times; %s days', $duration, 'yith-booking-for-woocommerce' ),
				// translators: %s is the number of hours.
				'hour'   => _n( 'hour(s)', '&times; %s hours', $duration, 'yith-booking-for-woocommerce' ),
				// translators: %s is the number of minutes.
				'minute' => _n( 'minute(s)', '&times; %s minutes', $duration, 'yith-booking-for-woocommerce' ),
				// translators: %s is the number of seconds.
				'second' => _n( 'second(s)', '&times; %s seconds', $duration, 'yith-booking-for-woocommerce' ),
			),
			'period'   => array(
				// translators: %s is the number of months.
				'month'  => _n( 'month', '%s months', $duration, 'yith-booking-for-woocommerce' ),
				// translators: %s is the number of weeks.
				'week'   => _n( 'week', '%s weeks', $duration, 'yith-booking-for-woocommerce' ),
				// translators: %s is the number of days.
				'day'    => _n( 'day', '%s days', $duration, 'yith-booking-for-woocommerce' ),
				// translators: %s is the number of hours.
				'hour'   => _n( 'hour', '%s hours', $duration, 'yith-booking-for-woocommerce' ),
				// translators: %s is the number of minutes.
				'minute' => _n( 'minute', '%s minutes', $duration, 'yith-booking-for-woocommerce' ),
				// translators: %s is the number of seconds.
				'second' => _n( 'second', '%s seconds', $duration, 'yith-booking-for-woocommerce' ),
			),
		);

		// phpcs:enable

		$duration_labels = array_key_exists( $mode, $labels ) ? $labels[ $mode ] : $labels['duration'];
		$label           = array_key_exists( $duration_unit, $duration_labels ) ? $duration_labels[ $duration_unit ] : '';

		return apply_filters( 'yith_wcbk_get_duration_label_string', $label, $duration_unit, $plural, $mode );
	}
}
