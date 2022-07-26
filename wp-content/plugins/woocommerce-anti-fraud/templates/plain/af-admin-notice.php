<?php
/**
 * Admin notice Email Plan
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

echo esc_html__($email_heading) . "\n\n";
/* translators: 1. order ID, 2. resik score. */
printf( esc_html__( 'Hi there. Order with ID #%1$s scored a Risk Score of %2$s.', 'woocommerce-anti-fraud' ), esc_html__($order_id), esc_html__($score) ) . "\r\n\r\n";
/* translators: 1. start of link, 2. end of link. */
printf( esc_html__( '%1$sClick here to view the order.%2$s.', 'woocommerce-anti-fraud' ), '<a href="' . esc_url($order_url) . '">', '</a>' ) . "\r\n\r\n";

echo "\n****************************************************\n\n";

/**
 * Get email footer text
 *
 * @since  1.0.0
 */
echo esc_html__(apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ));
