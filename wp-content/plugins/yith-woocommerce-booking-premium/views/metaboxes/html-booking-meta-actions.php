<?php
/**
 * Booking Actions Metabox
 *
 * @var YITH_WCBK_Booking $booking The booking.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

?>
<div class="yith-wcbk-booking-actions-metabox-content">
	<p style="text-align: center">
		<a href="<?php echo esc_url( $booking->get_pdf_url( 'customer' ) ); ?>" class="yith-wcbk-admin-button yith-wcbk-admin-button--small yith-wcbk-admin-button--outline" target="_blank"><?php esc_html_e( 'Customer PDF', 'yith-booking-for-woocommerce' ); ?></a>
		<a href="<?php echo esc_url( $booking->get_pdf_url( 'admin' ) ); ?>" class="yith-wcbk-admin-button yith-wcbk-admin-button--small yith-wcbk-admin-button--outline" target="_blank"><?php esc_html_e( 'Admin PDF', 'yith-booking-for-woocommerce' ); ?></a>
	</p>

	<?php
	/**
	 * DO_ACTION: yith_wcbk_booking_actions_meta_box_after
	 * Hook to output something at the end of the "Actions" meta-box in booking details (admin side).
	 *
	 * @param YITH_WCBK_Booking $booking The booking.
	 */
	do_action( 'yith_wcbk_booking_actions_meta_box_after', $booking );
	?>
</div>
