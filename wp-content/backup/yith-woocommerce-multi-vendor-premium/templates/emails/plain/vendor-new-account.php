<?php
/**
 * Customer new account email
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails/Plain
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo "= " . $email_heading . " =\n\n";

echo sprintf( __( "Your vendor account has been approved on %s.", 'yith-woocommerce-product-vendors' ), $blogname ) . "\n\n";

echo sprintf(  __( 'From your vendor dashboard you can view your recent commissions, view the sales report and manage your store and payment settings. Click <a href="%s">here</a> to access <strong>store dashboard</strong>.', 'yith-woocommerce-product-vendors' ), $admin_url ) . "\n\n";

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
