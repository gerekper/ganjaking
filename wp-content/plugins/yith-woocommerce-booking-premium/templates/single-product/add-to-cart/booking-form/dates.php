<?php
/**
 * Booking form dates
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/booking-form/dates.php.
 *
 * @var WC_Product_Booking $product The booking product.
 *
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.
?>
<div class="yith-wcbk-form-section-dates-wrapper yith-wcbk-form-section-wrapper">
	<?php

	/**
	 * DO_ACTION: yith_wcbk_booking_form_dates_date_fields
	 * Hook to output the booking form date fields.
	 *
	 * @hooked yith_wcbk_booking_form_dates_date_fields - 10
	 *
	 * @param WC_Product_Booking $product The bookable product.
	 */
	do_action( 'yith_wcbk_booking_form_dates_date_fields', $product );

	/**
	 * DO_ACTION: yith_wcbk_booking_form_dates_duration
	 * Hook to output the booking form duration field.
	 *
	 * @hooked yith_wcbk_booking_form_dates_duration - 10
	 *
	 * @param WC_Product_Booking $product The bookable product.
	 */
	do_action( 'yith_wcbk_booking_form_dates_duration', $product );

	?>
</div>
