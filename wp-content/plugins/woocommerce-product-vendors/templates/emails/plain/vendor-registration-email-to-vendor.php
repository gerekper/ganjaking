<?php
/**
 * Vendor registration email to vendor (plain text).
 *
 * @version 2.0.0
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo "= " . esc_html( wp_strip_all_tags( $email_heading ) ) . " =\n\n";

echo esc_html__( 'Hello! Thank you for registering to become a vendor.', 'woocommerce-product-vendors' ) . "\n\n";

echo esc_html__( 'Once your application has been approved, you will be able to login.', 'woocommerce-product-vendors' ) . "\n\n";

echo esc_html__( 'Here is your login account information:', 'woocommerce-product-vendors' ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo sprintf( esc_html__( 'Login Address: %s', 'woocommerce-product-vendors' ), esc_url( wp_login_url() ) ) . "\n\n";
echo sprintf( esc_html__( 'Login Name: %s', 'woocommerce-product-vendors' ), esc_html( $user_login ) ) . "\n\n";

echo esc_html__( 'Click the link below to set your password and gain access to your account.', 'woocommerce-product-vendors' ) . "\n";

echo esc_url( add_query_arg( array( 'action' => 'rp', 'key' => $password_reset_key, 'login' => rawurlencode( $user_login ) ), wp_login_url() ) ) . "\n\n";

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
