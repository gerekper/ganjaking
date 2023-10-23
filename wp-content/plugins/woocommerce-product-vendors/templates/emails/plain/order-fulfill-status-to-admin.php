<?php
/**
 * Order fulfillment status to admin (plain text).
 *
 * @version 2.0.16
 * @since 2.0.16
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$order_id = $order->get_id();

echo "= " . esc_html( wp_strip_all_tags( $email_heading ) ) . " =\n\n";

echo esc_html__( 'Hello! A vendor has updated an order item fulfillment status.', 'woocommerce-product-vendors' ) . "\n\n";

echo esc_html__( 'Order Information', 'woocommerce-product-vendors' ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html( sprintf( __( 'Vendor: %s', 'woocommerce-product-vendors' ), $vendor_name ) ) . "\n\n";
echo esc_html( sprintf( __( 'Order Number: %s', 'woocommerce-product-vendors' ), $order->get_order_number() ) ) . "\n\n";
echo esc_html( sprintf( __( 'Order Item: %s', 'woocommerce-product-vendors' ), $order_item_name ) ) . "\n\n";
echo esc_html( sprintf( __( 'Fulfillment Status: %s', 'woocommerce-product-vendors' ), ucfirst( $fulfillment_status ) ) ) . "\n\n";
echo esc_url( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) ) . "\n\n";
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
