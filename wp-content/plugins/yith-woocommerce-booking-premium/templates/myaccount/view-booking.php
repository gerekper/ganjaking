<?php
/**
 * View booking Template
 * Shows booking on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/view-booking.php.
 *
 * @var YITH_WCBK_Booking $booking    The booking.
 * @var int               $booking_id The booking ID.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.
?>
<p>
	<?php
	echo wp_kses_post(
		sprintf(
		// translators: 1. The booking name with ID; 2. the date; 3. the status.
			__( '%1$s was placed on %2$s and is currently %3$s.', 'yith-booking-for-woocommerce' ),
			'<mark class="booking-id">' . $booking->get_name() . '</mark>',
			'<mark class="booking-date">' . yith_wcbk_date( $booking->get_date_created()->getTimestamp() ) . '</mark>',
			'<mark class="booking-status">' . $booking->get_status_text() . '</mark>'
		)
	);
	?>
</p>

<?php
/**
 * DO_ACTION: yith_wcbk_view_booking
 *
 * Allows to render some content or fire some action in My Account > Bookings > View booking page.
 *
 * @param int $booking_id The booking ID.
 */
do_action( 'yith_wcbk_view_booking', $booking_id );
?>
