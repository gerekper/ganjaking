<?php
/**
 * Background Process Functions
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Functions
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! function_exists( 'yith_wcbk_bg_process_booking_product_regenerate_product_data' ) ) {
	/**
	 * Regenerate product data
	 *
	 * @param int   $product_id Product ID.
	 * @param array $data       Data.
	 */
	function yith_wcbk_bg_process_booking_product_regenerate_product_data( $product_id, $data = array() ) {
		yith_wcbk_maybe_debug( sprintf( 'Regenerate product data for product #%s', $product_id ), YITH_WCBK_Logger_Groups::BACKGROUND_PROCESS );
		$product = wc_get_product( $product_id );
		if ( $product && yith_wcbk_is_booking_product( $product ) ) {
			/**
			 * Booking product
			 *
			 * @var WC_Product_Booking $product
			 */
			$product->regenerate_data( $data );
		}
	}
}
