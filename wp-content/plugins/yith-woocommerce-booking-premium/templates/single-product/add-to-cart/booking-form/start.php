<?php
/**
 * Booking form start template.
 *
 * @var WC_Product_Booking $product
 *
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;
?>
<div
		class="yith-wcbk-booking-form"
		data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
		data-booking_data="<?php echo esc_attr( wp_json_encode( $product->get_booking_data() ) ); ?>"
>
