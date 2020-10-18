<?php
/**
 * Waitlist new sign-up email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/waitlist-new-signup.php.
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

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $admin_email ); ?>

<p><?php printf( __( '%1$s has just signed up to the waitlist for %2$s', 'woocommerce-waitlist' ), $user_email, $product_title ); ?></p>
<p><?php printf( __( 'There are now %d customers on this waitlist.', 'woocommerce-waitlist' ), $count ); ?></p>
<p><?php printf( __( 'To review the waitlist for this product visit the %1$sedit product screen%2$s and click on the waitlist tab', 'woocommerce-waitlist' ), '<a href="' . $product_link . '">', '</a>' ); ?></p>

<?php

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $user_email );
