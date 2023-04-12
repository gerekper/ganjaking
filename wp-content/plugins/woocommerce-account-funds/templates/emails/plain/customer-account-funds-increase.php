<?php
/**
 * Customer increase account funds email (plain text).
 *
 * @package WC_Account_Funds/Templates/Emails
 * @version 2.8.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var WC_Email $email              Email object.
 * @var string   $email_heading      Email heading.
 * @var string   $message            Email message.
 * @var float    $funds_amount       Funds amount.
 * @var string   $additional_content Additional content.
 */

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/**
 * Show email message - this is set in the email's settings.
 */
echo esc_html( wp_strip_all_tags( wptexturize( $message ) ) );

echo "\n\n----------------------------------------\n\n";

echo esc_html( wp_strip_all_tags( wc_price( $funds_amount ) ) );

echo "\n\n----------------------------------------\n\n";

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) :
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );
	echo "\n\n----------------------------------------\n\n";
endif;

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
