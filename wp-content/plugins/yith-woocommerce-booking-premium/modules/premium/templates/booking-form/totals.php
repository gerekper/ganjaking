<?php
/**
 * Booking Totals template.
 *
 * @var WC_Product_Booking $product the booking product
 *
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;
?>
<div class="yith-wcbk-form-section yith-wcbk-form-section-totals">
	<label class='yith-wcbk-form-section__label yith-wcbk-booking-form__label'><?php esc_html_e( 'Totals', 'yith-booking-for-woocommerce' ); ?></label>
	<div class="yith-wcbk-form-section__content">
		<div class="yith-wcbk-booking-form-totals"></div>
	</div>
</div>
