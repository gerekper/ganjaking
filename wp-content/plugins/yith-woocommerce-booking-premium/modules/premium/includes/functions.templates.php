<?php
/**
 * Functions
 *
 * @package YITH\Booking\Modules\Premium
 */

defined( 'YITH_WCBK' ) || exit;

add_action( 'yith_wcbk_booking_form_content', 'yith_wcbk_booking_form_totals', 40, 1 );

if ( ! function_exists( 'yith_wcbk_booking_form_totals' ) ) {
	/**
	 * Booking form totals.
	 *
	 * @param WC_Product $product The product.
	 */
	function yith_wcbk_booking_form_totals( $product ) {
		if ( ! $product || ! $product instanceof WC_Product_Booking ) {
			return;
		}

		yith_wcbk_get_module_template( 'premium', 'booking-form/totals.php', array( 'product' => $product ), 'single-product/add-to-cart/' );
	}
}
