<?php
/**
 * Vendor registration email to admin (plain text).
 *
 * @version 2.0.0
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo "= " . esc_html( wp_strip_all_tags( $email_heading ) ) . " =\n\n";

echo esc_html__( 'Hello! A vendor has requested to be registered.', 'woocommerce-product-vendors' ) . "\n\n";

echo esc_html__( 'Vendor Information', 'woocommerce-product-vendors' ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html( sprintf( __( 'Email: %s', 'woocommerce-product-vendors' ), $user_email ) ) . "\n\n";
echo esc_html( sprintf( __( 'First Name: %s', 'woocommerce-product-vendors' ), $first_name ) ) . "\n\n";
echo esc_html( sprintf( __( 'Last Name: %s', 'woocommerce-product-vendors' ), $last_name ) ) . "\n\n";
echo esc_html( sprintf( __( 'Vendor Name: %s', 'woocommerce-product-vendors' ), $vendor_name ) ) . "\n\n";
echo esc_html__( 'Vendor Description:', 'woocommerce-product-vendors' ) . "\n\n";
echo esc_html( wp_strip_all_tags( $vendor_desc ) ) . "\n\n";
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/* translators: %s is the pending vendors list url. */
printf( esc_html__( 'You can approve this vendor at %s.', 'woocommerce-product-vendors' ), esc_url( admin_url( 'users.php?role=wc_product_vendors_pending_vendor' ) ) );

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
