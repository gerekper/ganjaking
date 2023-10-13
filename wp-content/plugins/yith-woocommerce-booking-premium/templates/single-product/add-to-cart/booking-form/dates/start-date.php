<?php
/**
 * Start date field in booking form
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/booking-form/dates/start-date.php
 *
 * @var WC_Product_Booking $product The booking product.
 * @var array              $date_info
 * @var array              $not_available_dates
 * @var string             $calendar_day_range_picker_class
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

?>
<div class="yith-wcbk-form-section yith-wcbk-form-section-dates <?php echo esc_attr( $calendar_day_range_picker_class ); ?>">

	<label for="yith-wcbk-booking-start-date-<?php echo esc_attr( $product->get_id() ); ?>" class='yith-wcbk-form-section__label yith-wcbk-booking-form__label'><?php echo esc_html( yith_wcbk_get_label( 'start-date' ) ); ?></label>
	<div class="yith-wcbk-form-section__content">
		<?php
		yith_wcbk_print_field(
			array(
				'type'  => yith_wcbk()->settings->display_date_picker_inline() ? 'datepicker-inline' : 'datepicker',
				'id'    => 'yith-wcbk-booking-start-date-' . $product->get_id(),
				'name'  => 'start-date',
				'class' => 'yith-wcbk-booking-date yith-wcbk-booking-start-date',
				'data'  => array(
					'type'                => 'from',
					'all-day'             => ! ! $all_day_booking ? 'yes' : 'no',
					'ajax-load-months'    => ! ! $ajax_load_months ? 'yes' : 'no',
					'min-duration'        => $product->get_minimum_duration(),
					'month-to-load'       => $next_month,
					'year-to-load'        => $next_year,
					'min-date'            => $min_date,
					'max-date'            => $max_date,
					'not-available-dates' => $not_available_dates ? wp_json_encode( $not_available_dates ) : '',
					'product-id'          => $product->get_id(),
					'related-to'          => '#yith-wcbk-booking-end-date-' . $product->get_id(),
					'allow-same-date'     => ! ! $all_day_booking ? 'yes' : 'no',
					'allowed-days'        => wp_json_encode( $product->get_allowed_start_days() ),
					'on-select-open'      => '#yith-wcbk-booking-end-date-' . $product->get_id(),
					'static'              => 'yes',
					'loaded-months'       => wp_json_encode( $loaded_months ),
					'months-to-load'      => $months_to_load,
				),
				'value' => $default_start_date,
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
	</div>
</div>
