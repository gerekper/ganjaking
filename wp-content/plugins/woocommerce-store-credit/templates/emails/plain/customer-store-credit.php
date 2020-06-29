<?php
/**
 * Customer send store credit email (plain text).
 *
 * @package WC_Store_Credit/Templates/Emails/Plain
 * @version 3.1.0
 */

defined( 'ABSPATH' ) || exit;

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

if ( $coupon->get_description() ) :
	echo esc_html( wp_strip_all_tags( wptexturize( $coupon->get_description() ) ) ) . "\n\n";
endif;

echo esc_html_x( 'To redeem your store credit use the following code during checkout:', 'email text', 'woocommerce-store-credit' ) . "\n";

echo "\n----------------------------------------\n\n";

echo esc_html( $coupon->get_code() ) . "\n";

echo "\n----------------------------------------\n\n";

if ( $additional_content ) :
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );
	echo "\n\n----------------------------------------\n\n";
endif;

echo esc_html( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
