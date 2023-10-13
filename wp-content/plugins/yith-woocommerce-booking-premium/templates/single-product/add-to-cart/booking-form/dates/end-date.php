<?php
/**
 * End Date Field in booking form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/booking-form/dates/end-date.php
 *
 * @var WC_Product_Booking $product The booking product.
 * @var array              $date_info
 * @var string             $calendar_day_range_picker_class
 *
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
	) = yith_plugin_fw_extract( $date_info, 'current_year', 'current_month', 'next_year', 'next_month', 'min_date', 'min_date_timestamp', 'max_date', 'max_date_timestamp', 'default_start_date', 'default_end_date', 'months_to_load', 'ajax_load_months' );

?>
<div class="yith-wcbk-form-section yith-wcbk-form-section-dates <?php echo esc_attr( $calendar_day_range_picker_class ); ?>">

	<label for="yith-wcbk-booking-end-date-<?php echo esc_attr( $product->get_id() ); ?>" class='yith-wcbk-form-section__label yith-wcbk-booking-form__label'><?php echo esc_html( yith_wcbk_get_label( 'end-date' ) ); ?></label>

	<div class="yith-wcbk-form-section__content">
		<?php
		yith_wcbk_print_field(
			array(
				'type'  => yith_wcbk()->settings->display_date_picker_inline() ? 'datepicker-inline' : 'datepicker',
				'id'    => 'yith-wcbk-booking-end-date-' . $product->get_id(),
				'name'  => 'to',
				'class' => 'yith-wcbk-booking-date yith-wcbk-booking-end-date',
				'data'  => array(
					'type'            => 'to',
					'min-date'        => $min_date,
					'max-date'        => $max_date,
					'related-from'    => '#yith-wcbk-booking-start-date-' . $product->get_id(),
					'allow-same-date' => ! ! $all_day_booking ? 'yes' : 'no',
					'static'          => 'yes',
				),
				'value' => $default_end_date,
			)
		);
		?>
	</div>
</div>
