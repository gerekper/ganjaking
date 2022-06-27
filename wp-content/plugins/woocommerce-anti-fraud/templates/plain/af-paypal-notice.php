<?php
/**
 * Admin notice Email Plan
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

echo esc_html__($email_heading) . "\n\n";

printf( esc_html__(get_option('wc_settings_anti_fraud_email_body') ) ) . "\r\n\r\n";

/* translators: 1. start of link, 2. end of link. */
printf( esc_html__( '%1$sClick here to view the order.%2$s.', 'woocommerce-anti-fraud' ), '<a href="' . esc_url($url) . '">', '</a>' ) . "\r\n\r\n";

echo "\n****************************************************\n\n";

echo esc_html__( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ));
