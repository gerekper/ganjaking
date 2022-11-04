<?php
/**
 * The template for displaying a booking summary to customers.
 * It will display when the booking is pending for confirmation.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-bookings/emails/plain/customer-booking-display.php
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/bookings-templates/
 * @author  Automattic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( $booking_ids ) {
	foreach ( $booking_ids as $booking_id ) {
		$booking    = new WC_Booking( $booking_id );
		$order      = $booking->get_order();
		$plain_text = false;

		if ( ! $order ) {
			continue;
		}

		foreach ( $order->get_items() as $item_id => $item ) {
			// Product name.
			echo esc_html( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) ) . "\n\n";

			// allow other plugins to add additional product information here.
			do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, $plain_text );

			wc_display_item_meta(
				$item,
				array(
					'before'       => '',
					'after'        => "\n",
					'separator'    => "\n",
					'echo'         => true,
					'autop'        => false,
					'label_before' => '',
					'label_after'  => ': ',
				)
			);

			$product  = $booking->get_product();
			$resource = $booking->get_resource();
			$label    = $product && is_callable( array( $product, 'get_resource_label' ) ) && $product->get_resource_label() ? $product->get_resource_label() : __( 'Type', 'woocommerce-bookings' );

			$template_args = array(
				'booking'          => $booking,
				'product'          => $product,
				'resource'         => $resource,
				'label'            => $label,
				'booking_date'     => '',
				'booking_timezone' => '',
				'is_admin'         => false,
			);

			wc_get_template( 'emails/plain/booking-summary-list.php', $template_args, 'woocommerce-bookings', WC_BOOKINGS_TEMPLATE_PATH );
		}
		
		if ( $booking_id && function_exists( 'wc_get_endpoint_url' ) && wc_get_page_id( 'myaccount' ) && 0 !== $booking->get_customer_id() ) {
			echo 'View my bookings: ' . esc_url( wc_get_endpoint_url( $endpoint, '', wc_get_page_permalink( 'myaccount' ) ) ) . "\n";
		}
	}
}
