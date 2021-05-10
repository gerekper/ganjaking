<?php
/**
 * The template for the waitlist in stock notification email (Plain Text)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/plain/waitlist-mailout.php.
 *
 * HOWEVER, on occasion WooCommerce Waitlist will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @version 2.1.9
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo _x( "Hi There,", 'Email salutation', 'woocommerce-waitlist' ) . "\n\n";

printf( __( '%s is now back in stock at %s. ', 'woocommerce-waitlist' ), $product_title, get_bloginfo( 'title' ) );
_e( 'You have been sent this email because your email address was registered on a waitlist for this product.', 'woocommerce-waitlist' );
echo "\n\n";
printf( __( 'If you would like to purchase %1$s please visit the following link: %2$s.', 'woocommerce-waitlist' ), $product_title, $product_link  );
echo "\n\n";
if ( WooCommerce_Waitlist_Plugin::persistent_waitlists_are_disabled( $product_id ) && ! $triggered_manually ) {
	_e( 'You have been removed from the waitlist for this product', 'woocommerce-waitlist' );
	echo "\n\n";
}
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
