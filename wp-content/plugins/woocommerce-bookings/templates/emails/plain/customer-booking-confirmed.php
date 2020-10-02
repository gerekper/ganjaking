<?php
/**
 * Customer booking confirmed email, plain text.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-bookings/emails/plain/customer-booking-confirmed.php
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

if ( $booking->get_order() ) {
	/* translators: 1: billing first name */
	echo esc_html( sprintf( __( 'Hello %s', 'woocommerce-bookings' ), ( is_callable( array( $booking->get_order(), 'get_billing_first_name' ) ) ? $booking->get_order()->get_billing_first_name() : $booking->get_order()->billing_first_name ) ) ) . "\n\n";
}

echo esc_html( __( 'Your booking has been confirmed. The details of your booking are shown below.', 'woocommerce-bookings' ) ) . "\n\n";

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

$order = $booking->get_order();
if ( $order ) {
	if ( 'pending' === $order->get_status() ) {
		/* translators: 1: checkout payment url */
		echo esc_html( sprintf( __( 'To pay for this booking please use the following link: %s', 'woocommerce-bookings' ), $order->get_checkout_payment_url() ) ) . "\n\n";
	}

	do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email );

	$pre_wc_30 = version_compare( WC_VERSION, '3.0', '<' );

	if ( $pre_wc_30 ) {
		$order_date = $order->order_date;
	} else {
		$order_date = $order->get_date_created() ? $order->get_date_created()->date( 'Y-m-d H:i:s' ) : '';
	}

	/* translators: 1: order number */
	echo esc_html( sprintf( __( 'Order number: %s', 'woocommerce-bookings' ), $order->get_order_number() ) ) . "\n";
	/* translators: 1: order date */
	echo esc_html( sprintf( __( 'Order date: %s', 'woocommerce-bookings' ), date_i18n( wc_bookings_date_format(), strtotime( $order_date ) ) ) ) . "\n";

	do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

	echo "\n";

	switch ( $order->get_status() ) {
		case 'completed':
			echo wp_kses_post( $pre_wc_30 ? $order->email_order_items_table( array(
				'show_sku'   => false,
				'plain_text' => true,
			) ) : wc_get_email_order_items( $order, array(
				'show_sku'   => false,
				'plain_text' => true,
			) ) );
			break;
		case 'processing':
		default:
			echo wp_kses_post( $pre_wc_30 ? $order->email_order_items_table( array(
				'show_sku'   => true,
				'plain_text' => true,
			) ) : wc_get_email_order_items( $order, array(
				'show_sku'   => true,
				'plain_text' => true,
			) ) );
			break;
	}

	echo "==========\n\n";

	$totals = $order->get_order_item_totals();
	if ( $totals ) {
		foreach ( $totals as $total ) {
			echo esc_html( $total['label'] ) . "\t " . esc_html( $total['value'] ) . "\n";
		}
	}

	echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

	do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email );
}

echo esc_html( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
