<?php
/**
 * Customer send store credit email (plain text).
 *
 * @package WC_Store_Credit/Templates/Emails/Plain
 * @version 3.7.0
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

echo esc_html( $email->get_button_text() ) . "\n";

echo esc_url( wc_store_credit_get_redeem_url( $coupon ) ) . "\n";

if ( $coupon->get_date_expires() ) :
	echo "\n----------------------------------------\n\n";

	printf(
		/* translators: %s expiration date */
		esc_html_x( 'This credit can be redeemed until %s.', 'email text', 'woocommerce-store-credit' ) . "\n",
		esc_html( wc_format_datetime( $coupon->get_date_expires() ) )
	);
endif;

echo "\n----------------------------------------\n\n";

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) :
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );
	echo "\n\n----------------------------------------\n\n";
endif;

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
