<?php
/**
 * Date Fields in booking form
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/booking-form/dates/dates.php
 *
 * @var WC_Product_Booking $product the booking product
 *
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$unit            = $product->get_duration_unit();
$all_day_booking = $product->is_full_day();
$date_info       = yith_wcbk_get_booking_form_date_info( $product );

list(
	$current_year,
	$current_month,
	$next_year,
	$next_month,
	$min_date,
	$min_date_timestamp,
	$max_date,
	$max_date_timestamp,
	$default_start_date,
	$default_end_date,
	$months_to_load,
	$ajax_load_months,
	) = yith_plugin_fw_extract( $date_info, 'current_year', 'current_month', 'next_year', 'next_month', 'min_date', 'min_date_timestamp', 'max_date', 'max_date_timestamp', 'default_start_date', 'default_end_date', 'months_to_load', 'ajax_load_months' );

if ( 'month' !== $unit ) {
	if ( yith_wcbk()->settings->is_unique_calendar_range_picker_enabled() && $product->has_calendar_picker_enabled() ) {
		wc_get_template( '/single-product/add-to-cart/booking-form/dates/range-picker.php', compact( 'product', 'date_info' ), '', YITH_WCBK_TEMPLATE_PATH );
	} else {
		$not_available_dates             = $product->get_non_available_dates( $current_year, $current_month, $next_year, $next_month, array( 'check_min_max_duration' => yith_wcbk()->settings->check_min_max_duration_in_calendar() ) );
		$calendar_day_range_picker_class = $product->has_calendar_picker_enabled() ? ' calendar-day-range-picker' : '';

		wc_get_template( '/single-product/add-to-cart/booking-form/dates/start-date.php', compact( 'product', 'date_info', 'not_available_dates', 'calendar_day_range_picker_class' ), '', YITH_WCBK_TEMPLATE_PATH );

		if ( $product->has_calendar_picker_enabled() ) {
			wc_get_template( '/single-product/add-to-cart/booking-form/dates/end-date.php', compact( 'product', 'date_info', 'calendar_day_range_picker_class' ), '', YITH_WCBK_TEMPLATE_PATH );
		}

		if ( in_array( $unit, array( 'hour', 'minute' ), true ) ) {
			wc_get_template( '/single-product/add-to-cart/booking-form/dates/time.php', compact( 'product' ), '', YITH_WCBK_TEMPLATE_PATH );
		}
	}
} else {
	$not_available_months = $product->get_not_available_months( $current_year, $current_month, $next_year, $next_month );
	wc_get_template( '/single-product/add-to-cart/booking-form/dates/month.php', compact( 'product', 'date_info', 'not_available_months' ), '', YITH_WCBK_TEMPLATE_PATH );
}
