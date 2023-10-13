<?php
/**
 * Booking Search Form Field Date daily
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/fields/date.php.
 *
 * @var YITH_WCBK_Search_Form $search_form
 * @package YITH\Booking\Modules\SearchForms\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$current_id = $search_form->get_unique_id();
?>
<div class="yith-wcbk-booking-search-form__row yith-wcbk-booking-search-form__row--start-date">
	<label class="yith-wcbk-booking-search-form__row__label" for="yith-wcbk-booking-search-form-date-day-start-date-<?php echo esc_attr( $current_id ); ?>">
		<?php echo esc_html( yith_wcbk_get_label( 'start-date' ) ); ?>
	</label>
	<div class="yith-wcbk-booking-search-form__row__content">
		<?php
		yith_wcbk_print_field(
			array(
				'type'  => 'datepicker',
				'id'    => 'yith-wcbk-booking-search-form-date-day-start-date-' . $current_id,
				'name'  => 'from',
				'class' => 'yith-wcbk-booking-field yith-wcbk-booking-date yith-wcbk-booking-start-date',
				'data'  => apply_filters(
					'yith_wcbk_search_form_start_date_input_data',
					array(
						'min-date'       => '+0D',
						'related-to'     => '#yith-wcbk-booking-search-form-date-day-end-date-' . $current_id,
						'on-select-open' => '#yith-wcbk-booking-search-form-date-day-end-date-' . $current_id,
					),
					$search_form
				),
				'value' => yith_wcbk_get_query_string_param( 'from' ),
			)
		);
		?>
	</div>
</div>

<div class="yith-wcbk-booking-search-form__row yith-wcbk-booking-search-form__row--end-date">
	<label class="yith-wcbk-booking-search-form__row__label" for="yith-wcbk-booking-search-form-date-day-end-date-<?php echo esc_attr( $current_id ); ?>">
		<?php echo esc_html( yith_wcbk_get_label( 'end-date' ) ); ?>
	</label>
	<div class="yith-wcbk-booking-search-form__row__content">
		<?php
		yith_wcbk_print_field(
			array(
				'type'  => 'datepicker',
				'id'    => 'yith-wcbk-booking-search-form-date-day-end-date-' . $current_id,
				'name'  => 'to',
				'class' => 'yith-wcbk-booking-field yith-wcbk-booking-date yith-wcbk-booking-end-date',
				'data'  => array(
					'min-date'     => '+0D',
					'related-from' => '#yith-wcbk-booking-search-form-date-day-start-date-' . $current_id,
				),
				'value' => yith_wcbk_get_query_string_param( 'to' ),
			)
		);
		?>
	</div>
</div>
