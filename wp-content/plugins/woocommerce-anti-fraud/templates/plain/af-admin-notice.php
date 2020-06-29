<?php
/**
 * Admin notice Email Plan
 *
 * @author        WooThemes
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

echo $email_heading . "\n\n";

printf( __( 'Hi there. Order with ID #%s scored a Risk Score of %s.', 'woocommerce-anti-fraud' ), $order_id, $score ) . "\r\n\r\n";
printf( __( '%sClick here to view the order.%s.', 'woocommerce-anti-fraud' ), '<a href="' . $order_url . '">', '</a>' ) . "\r\n\r\n";

echo "\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );