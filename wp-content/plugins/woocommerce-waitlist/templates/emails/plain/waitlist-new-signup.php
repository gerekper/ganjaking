<?php
/**
 * Admin new waitlist sign-up email (plain)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/plain/waitlist-new-signup.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @version 2.1.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo '= ' . esc_html( $email_heading ) . " =\n\n";

echo sprintf( __( '%s has just signed up to the waitlist for %s', 'woocommerce-waitlist' ), $user_email, $product_title );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo sprintf( __( 'There are now %d customers on this waitlist.  ', 'woocommerce-waitlist' ), $count );
_e( 'To review the waitlist for this product visit the edit product screen and click on the waitlist tab', 'woocommerce-waitlist' );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
