<?php
/**
 * Range picker field in booking form
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/booking-form/dates/range-picker.php
 *
 * @var WC_Product_Booking $product The booking product.
 * @var array              $date_info
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$all_day_booking = $product->is_full_day();

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
	$loaded_months
	) = yith_plugin_fw_extract( $date_info, 'current_year', 'current_month', 'next_year', 'next_month', 'min_date', 'min_date_timestamp', 'max_date', 'max_date_timestamp', 'default_start_date', 'default_end_date', 'months_to_load', 'ajax_load_months', 'loaded_months' );

$non_available_dates = $product->get_non_available_dates( $current_year, $current_month, $next_year, $next_month, array( 'check_min_max_duration' => yith_wcbk()->settings->check_min_max_duration_in_calendar() ) );
?>
<div class="yith-wcbk-form-section yith-wcbk-form-section-dates yith-wcbk-form-section-dates-range-picker">
	<label class='yith-wcbk-form-section__label yith-wcbk-booking-form__label'><?php echo esc_html( yith_wcbk_get_label( 'dates' ) ); ?></label>
	<div class="yith-wcbk-form-section__content">
		<div class="yith-wcbk-date-range-picker yith-wcbk-clearfix">
			<?php
			yith_wcbk_print_field(
				array(
					'type'              => 'text',
					'id'                => 'yith-wcbk-booking-start-date-' . $product->get_id(),
					'name'              => 'start-date',
					'class'             => 'yith-wcbk-date-picker yith-wcbk-booking-date yith-wcbk-booking-start-date',
					'data'              => array(
						'type'                => 'from',
						'all-day'             => ! ! $all_day_booking ? 'yes' : 'no',
						'ajax-load-months'    => ! ! $ajax_load_months ? 'yes' : 'no',
						'min-duration'        => $product->get_minimum_duration(),
						'month-to-load'       => $next_month,
						'year-to-load'        => $next_year,
						'min-date'            => $min_date,
						'max-date'            => $max_date,
						'not-available-dates' => $non_available_dates ? wp_json_encode( $non_available_dates ) : '',
						'product-id'          => $product->get_id(),
						'related-to'          => '#yith-wcbk-booking-end-date-' . $product->get_id(),
						'allow-same-date'     => ! ! $all_day_booking ? 'yes' : 'no',
						'allowed-days'        => wp_json_encode( $product->get_allowed_start_days() ),
						'on-select-open'      => '#yith-wcbk-booking-end-date-' . $product->get_id(),
						'static'              => 'yes',
						'loaded-months'       => wp_json_encode( $loaded_months ),
						'months-to-load'      => $months_to_load,
					),
					'custom_attributes' => 'placeholder="' . yith_wcbk_get_label( 'start-date' ) . '" readonly',
					'value'             => $default_start_date,
				)
			);


			yith_wcbk_print_field(
				array(
					'type'              => 'text',
					'id'                => 'yith-wcbk-booking-start-date-' . $product->get_id() . '--formatted',
					'name'              => '',
					'class'             => 'yith-wcbk-date-picker--formatted yith-wcbk-booking-date yith-wcbk-booking-start-date',
					'custom_attributes' => 'placeholder="' . yith_wcbk_get_label( 'start-date' ) . '" readonly',
				)
			);

			yith_wcbk_print_field(
				array(
					'id'    => 'yith-wcbk-booking-hidden-from' . $product->get_id(),
					'type'  => 'hidden',
					'name'  => 'from',
					'class' => 'yith-wcbk-booking-date yith-wcbk-booking-hidden-from',
					'value' => $default_start_date,
				)
			);

			?>
			<span class="yith-wcbk-date-range-picker__arrow yith-icon yith-icon-arrow-right"></span>
			<?php

			yith_wcbk_print_field(
				array(
					'type'              => 'text',
					'id'                => 'yith-wcbk-booking-end-date-' . $product->get_id(),
					'name'              => 'to',
					'class'             => 'yith-wcbk-date-picker yith-wcbk-booking-date yith-wcbk-booking-end-date',
					'data'              => array(
						'type'                             => 'to',
						'min-date'                         => $min_date,
						'max-date'                         => $max_date,
						'related-from'                     => '#yith-wcbk-booking-start-date-' . $product->get_id(),
						'allow-same-date'                  => ! ! $all_day_booking ? 'yes' : 'no',
						'disable-after-non-available-date' => 'yes',
					),
					'custom_attributes' => 'placeholder="' . yith_wcbk_get_label( 'end-date' ) . '" readonly',
					'value'             => $default_end_date,
				)
			);

			yith_wcbk_print_field(
				array(
					'type'              => 'text',
					'id'                => 'yith-wcbk-booking-end-date-' . $product->get_id() . '--formatted',
					'name'              => '',
					'class'             => 'yith-wcbk-date-picker--formatted yith-wcbk-booking-date yith-wcbk-booking-end-date',
					'custom_attributes' => 'placeholder="' . yith_wcbk_get_label( 'end-date' ) . '" readonly',
				)
			);
			?>
		</div>
	</div>
</div>
