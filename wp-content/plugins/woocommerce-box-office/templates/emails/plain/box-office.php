<?php
/**
 * WC Box Office transactional email template (plain text)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/admin-new-order.php.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package woocommerce-box-office
 */

defined( 'ABSPATH' ) || exit;

if ( ! empty( $email_heading ) ) {
	echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
	echo esc_html( wp_strip_all_tags( $email_heading ) );
	echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
}

echo esc_html( wp_strip_all_tags( $email_message ) );
echo "\n\n----------------------------------------\n\n";

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
