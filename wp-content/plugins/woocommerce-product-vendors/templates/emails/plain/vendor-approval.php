<?php
/**
 * Vendor approval (plain text).
 *
 * @version 2.0.0
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( 'wc_product_vendors_admin_vendor' === $role ) {
	$message = __( 'You have full administration access.', 'woocommerce-product-vendors' );

} else {
	$message = __( 'You have limited management access.', 'woocommerce-product-vendors' );
}

echo "= " . esc_html( wp_strip_all_tags( $email_heading ) ) . " =\n\n";

echo esc_html__( 'Hello! You have been approved to be a vendor on this store.', 'woocommerce-product-vendors' ) . "\n\n";

echo esc_html( wp_strip_all_tags( $message ) ) . "\n\n";

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html__( 'Please login to the site and visit your vendor dashboard to start managing your products.', 'woocommerce-product-vendors' ) . "\n\n";

echo sprintf( esc_html__( 'Login Address: %s', 'woocommerce-product-vendors' ), esc_url( wp_login_url() ) ) . "\n\n";

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
