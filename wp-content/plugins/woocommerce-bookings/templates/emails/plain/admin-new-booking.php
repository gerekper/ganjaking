<?php
/**
 * Admin new booking email, plain text.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-bookings/emails/plain/admin-new-booking.php
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/bookings-templates/
 * @author  Automattic
 * @version 1.10.0
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo '= ' . esc_html( $email_heading ) . " =\n\n";

if ( wc_booking_order_requires_confirmation( $booking->get_order() ) && 'pending-confirmation' === $booking->get_status() ) {
	/* translators: 1: billing first and last name */
	$opening_paragraph = __( 'A booking has been made by %s and is awaiting your approval. The details of this booking are as follows:', 'woocommerce-bookings' );
} else {
	/* translators: 1: billing first and last name */
	$opening_paragraph = __( 'A new booking has been made by %s. The details of this booking are as follows:', 'woocommerce-bookings' );
}


$order = $booking->get_order();

if ( $order ) {
	if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
		$first_name = $order->billing_first_name;
		$last_name  = $order->billing_last_name;
	} else {
		$first_name = $order->get_billing_first_name();
		$last_name  = $order->get_billing_last_name();
	}
}

if ( ! empty( $first_name ) && ! empty( $last_name ) ) {
	echo esc_html( sprintf( $opening_paragraph, $first_name . ' ' . $last_name ) ) . "\n\n";
}

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

if ( wc_booking_order_requires_confirmation( $booking->get_order() ) && $booking->get_status() === 'pending-confirmation' ) {
	echo esc_html( __( 'This booking is awaiting your approval. Please check it and inform the customer if the date is available or not.', 'woocommerce-bookings' ) ) . "\n\n";
}

echo esc_html( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
