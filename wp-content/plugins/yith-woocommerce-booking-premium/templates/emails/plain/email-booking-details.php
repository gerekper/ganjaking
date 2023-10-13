<?php
/**
 * Booking details - plain.
 *
 * @var YITH_WCBK_Booking $booking        The booking.
 * @var string            $email_heading  The heading.
 * @var WC_Email          $email          The email.
 * @var bool              $sent_to_admin  Is this sent to admin?
 * @var bool              $plain_text     Is this plain?
 * @var string            $custom_message The email message including booking details through {booking_details} placeholder.
 *
 * @package YITH\Booking\Templates\Emails
 */

defined( 'YITH_WCBK' ) || exit;

$order_id        = apply_filters( 'yith_wcbk_email_booking_details_order_id', $booking->get_order_id(), $booking, $sent_to_admin, $plain_text, $email );
$the_order       = ! ! $order_id ? wc_get_order( $order_id ) : false;
$args            = array(
	'order_id'       => $order_id,
	'order'          => $the_order,
	'split_services' => true,
);
$data_to_display = $booking->get_booking_data_to_display( $sent_to_admin ? 'admin' : 'frontend', $args );
unset( $data_to_display['status'] );

/**
 * DO_ACTION: yith_wcbk_email_before_booking_table
 * Hook to output something before the booking details table in emails.
 *
 * @param YITH_WCBK_Booking $booking       The booking.
 * @param bool              $sent_to_admin True if the email is sent to admin, false otherwise.
 * @param bool              $plain_text    True if the email type is text/plain.
 * @param WC_Email          $email         The email object
 */
do_action( 'yith_wcbk_email_before_booking_table', $booking, $sent_to_admin, $plain_text, $email );

echo "-----------------------------------------\n";

echo esc_html( wp_strip_all_tags( __( 'Booking ID', 'yith-booking-for-woocommerce' ) . ': #' . $booking->get_id() ) ) . "\n";

foreach ( $data_to_display as $data_key => $data ) {
	$data_label = $data['label'] ?? '';
	$data_value = $data['display'] ?? '';
	if ( $data_value ) {
		echo esc_html( wp_strip_all_tags( $data_label . ': ' . $data_value ) ) . "\n";
	}
}

echo esc_html( wp_strip_all_tags( __( 'Status', 'yith-booking-for-woocommerce' ) . ': ' . $booking->get_status_text() ) ) . "\n";

echo "-----------------------------------------\n\n";

/**
 * DO_ACTION: yith_wcbk_email_after_booking_table
 * Hook to output something after the booking details table in emails.
 *
 * @param YITH_WCBK_Booking $booking       The booking.
 * @param bool              $sent_to_admin True if the email is sent to admin, false otherwise.
 * @param bool              $plain_text    True if the email type is text/plain.
 * @param WC_Email          $email         The email object
 */
do_action( 'yith_wcbk_email_after_booking_table', $booking, $sent_to_admin, $plain_text, $email );
