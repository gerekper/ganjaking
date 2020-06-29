<?php
/**
 * Admin new order email (plain text)
 *
 * @author		WooThemes
 * @package 	WooCommerce/Templates/Emails/Plain
 * @version 	2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$billing_first_name = yit_get_prop( $order, 'billing_first_name' );
$billing_last_name = yit_get_prop( $order, 'billing_last_name' );
$order_date = yit_get_prop( $order, 'date_created' );

echo "= " . $email_heading . " =\n\n";

echo sprintf( __( 'You have received an order from %s.', 'yith-woocommerce-product-vendors' ), $billing_first_name . ' ' . $billing_last_name ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $yith_wc_email );

echo strtoupper( sprintf( __( 'Order number: %s', 'yith-woocommerce-product-vendors' ), $order_number ) ) . "\n";
echo date_i18n( __( 'jS F Y', 'yith-woocommerce-product-vendors' ), strtotime( $order_date ) ) . "\n";

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text );

echo "\n" . $vendor->email_order_items_table( $order, false, true, '', '', '', true );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $yith_wc_email );

do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );