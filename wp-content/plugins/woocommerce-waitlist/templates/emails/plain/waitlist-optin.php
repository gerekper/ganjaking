<?php
/**
 * The template for the waitlist double optin email (Plain Text)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/plain/waitlist-optin.php.
 *
 * HOWEVER, on occasion WooCommerce Waitlist will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @version 2.4.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo esc_html_x( "Hi There,", 'Email salutation', 'woocommerce-waitlist' ) . "\n\n";

printf( __( 'Please click the link below to confirm your email address and be added to the waitlist for %1$s at %2$s.', 'woocommerce-waitlist' ), esc_html( $product_title ), esc_html( get_bloginfo( 'title' ) ) );
echo "\n\n";
$link = add_query_arg( array(
	'wcwl_user_optin' => esc_attr( sanitize_email( $email ) ),
	'product_id'      => absint( $product_id ),
	'products'        => $products,
	'key'             => $key,
	'lang'            => $lang,
), $product_link );
echo esc_url( $link );
echo "\n\n";
_e( 'If you did not make this request please ignore this email.', 'woocommerce-waitlist' );
echo "\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
