<?php
/**
 * Customer booking pending confirmation email, plain text.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-bookings/emails/plain/customer-booking-pending-confirmation.php
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/bookings-templates/
 * @author  Automattic
 * @version 1.14.6
 * @since   1.14.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo '= ' . esc_html( $email_heading ) . " =\n\n";

if ( $booking->get_order() ) {
	/* translators: 1: billing first name */
	echo esc_html( sprintf( __( 'Hello %s', 'woocommerce-bookings' ), ( is_callable( array( $booking->get_order(), 'get_billing_first_name' ) ) ? $booking->get_order()->get_billing_first_name() : $booking->get_order()->billing_first_name ) ) ) . "\n\n";
}

echo esc_html( __( 'Your booking has been received and it\'s pending confirmation. The details of your booking are shown below.', 'woocommerce-bookings' ) ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/* translators: 1: booking product title */
echo esc_html( sprintf( __( 'Booked: %s', 'woocommerce-bookings' ), $booking->get_product()->get_title() ) ) . "\n";
/* translators: 1: booking id */
echo esc_html( sprintf( __( 'Booking ID: %s', 'woocommerce-bookings' ), $booking->get_id() ) ) . "\n";

$resource = $booking->get_resource();

if ( $booking->has_resources() && $resource ) {
	/* translators: 1: booking title */
	echo esc_html( sprintf( __( 'Booking Type: %s', 'woocommerce-bookings' ), $resource->post_title ) ) . "\n";
}

/* translators: 1: booking start date */
echo esc_html( sprintf( __( 'Booking Start Date: %s', 'woocommerce-bookings' ), $booking->get_start_date( null, null, wc_should_convert_timezone( $booking ) ) ) ) . "\n";
/* translators: 1: booking end date */
echo esc_html( sprintf( __( 'Booking End Date: %s', 'woocommerce-bookings' ), $booking->get_end_date( null, null, wc_should_convert_timezone( $booking ) ) ) ) . "\n";

if ( wc_should_convert_timezone( $booking ) ) {
	/* translators: 1: time zone */
	echo esc_html( sprintf( __( 'Time Zone: %s', 'woocommerce-bookings' ), str_replace( '_', ' ', $booking->get_local_timezone() ) ) );
}

if ( $booking->has_persons() ) {
	foreach ( $booking->get_persons() as $id => $qty ) {
		if ( 0 === $qty ) {
			continue;
		}

		$person_type = ( 0 < $id ) ? get_the_title( $id ) : __( 'Person(s)', 'woocommerce-bookings' );
		/* translators: 1: person type 2: quantity */
		echo esc_html( sprintf( __( '%1$s: %2$d', 'woocommerce-bookings' ), $person_type, $qty ) ) . "\n";
	}
}

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
