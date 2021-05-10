<?php
/**
 * The template for the waitlist left notification sent to a customer when removed from a waitlist (Plain Text)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/plain/waitlist-left.php.
 *
 * HOWEVER, on occasion WooCommerce Waitlist will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @version 3.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo _x( "Hi There,", 'Email salutation', 'woocommerce-waitlist' );
echo "\n\n";
printf( __( 'You have been sent this email because your email address was removed from a waitlist for %1$s at %2$s. ', 'woocommerce-waitlist' ), esc_html( $product_title ), esc_html( get_bloginfo( 'name' ) ) );
echo "\n\n";
printf( __( 'If this is an error you can add yourself back to the waitlist here: %s.', 'woocommerce-waitlist' ), esc_attr( $product_link ) );
echo "\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
