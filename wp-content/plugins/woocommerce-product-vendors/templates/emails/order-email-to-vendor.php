<?php
/**
 * Order email to vendor.
 *
 * @version 2.1.0
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$order_date         = $order->get_date_created();
$billing_first_name = $order->get_billing_first_name();
$billing_last_name  = $order->get_billing_last_name();
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( esc_html__( 'You have received an order from %s. The order is as follows:', 'woocommerce-product-vendors' ), esc_html( $billing_first_name ) . ' ' . esc_html( $billing_last_name ) ); ?></p>

<h2><?php printf( esc_html__( 'Order #%s', 'woocommerce-product-vendors' ), esc_html( $order->get_order_number() ) ); ?> (<?php printf( '<time datetime="%s">%s</time>', esc_html( date_i18n( 'c', strtotime( $order_date ) ) ), esc_html( date_i18n( wc_date_format(), strtotime( $order_date ) ) ) ); ?>)</h2>

<?php $email->render_order_details_table( $order, $sent_to_admin, $plain_text, $email, $this_vendor ); ?>

<?php do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email ); ?>

<?php do_action( 'wc_product_vendors_email_order_meta', $order, $sent_to_admin, $plain_text, $email ); ?>

<?php do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email ); ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
