<?php
/**
 * Order email addresses to vendor (plain text).
 *
 * @version 2.1.52
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$billing = $order->get_formatted_billing_address();

/**
 * Determine if we should show billing information.
 *
 * @since 2.1.52
 * @param boolean  $show_billing Whether to show billing information. Default true.
 * @param WC_Order $order        Order object.
 */
if ( $billing && apply_filters( 'wcpv_email_to_vendor_show_billing', true, $order ) ) :
	echo esc_html__( 'Billing Address', 'woocommerce-product-vendors' ) . "\n\n";

	echo preg_replace( '#<br\s*/?>#i', "\n", wp_kses_post( $billing ) ) . "\n\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
endif;

if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && ( $shipping = $order->get_formatted_shipping_address() ) ) :
	echo esc_html__( 'Shipping Address', 'woocommerce-product-vendors' ) . "\n\n";

	echo preg_replace( '#<br\s*/?>#i', "\n", wp_kses_post( $shipping ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
endif;
