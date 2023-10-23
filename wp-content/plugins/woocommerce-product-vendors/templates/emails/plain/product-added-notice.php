<?php
/**
 * Product added notice (plain text).
 *
 * @version 2.0.0
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo "= " . esc_html( wp_strip_all_tags( $email_heading ) ) . " =\n\n";

printf( esc_html__( 'Hello! A vendor ( %s ) has added a new product awaiting review.', 'woocommerce-product-vendors' ), esc_html( $vendor_name ) );

echo "\n\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html( wp_strip_all_tags( $product_name ) ) . ': ' . esc_html( esc_url( $product_link ) ) . "\n\n";

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
