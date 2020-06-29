<?php
/**
 * Given order email
 *
 * @author        WooCommerce
 * @version       1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo $email_heading . "\n\n";

echo __( "You've been gifted this order:", 'woocommerce-give-products' ) . "\n\n";

echo "****************************************************\n\n";

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text );

/* translators: 1: order number */
echo sprintf( __( 'Order number: %s', 'woocommerce-give-products' ), $order->get_order_number() ) . "\n";

$order_date = version_compare( WC_VERSION, '3.0', '<' ) ? $order->order_date : ( $order->get_date_created() ? $order->get_date_created()->date( 'Y-m-d H:i:s' ) : '' );

/* translators: 1: order date */
echo sprintf( __( 'Order date: %s', 'woocommerce-give-products' ), date_i18n( wc_date_format(), strtotime( $order_date ) ) ) . "\n";

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text );

if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
	$billing_email = $order->billing_email;
	$billing_phone = $order->billing_phone;
	echo "\n" . $order->email_order_items_table( $order->is_download_permitted(), true, $order->has_status( 'processing' ), '', '', true );
} else {
	$billing_email = $order->get_billing_email();
	$billing_phone = $order->get_billing_phone();
	echo "\n" . wc_get_email_order_items( $order );
}

echo "----------\n\n";

$totals = $order->get_order_item_totals();

if ( $totals ) {
	foreach ( $totals as $total ) {
		echo $total['label'] . "\t " . $total['value'] . "\n";
	}
}

echo "\n****************************************************\n\n";

do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text );

echo __( 'Your details', 'woocommerce-give-products' ) . "\n\n";

if ( $billing_email ) {
	echo __( 'Email:', 'woocommerce-give-products' );
	echo $billing_email . "\n";
}

if ( $billing_phone ) {
	echo __( 'Tel:', 'woocommerce-give-products' ); ?> <?php echo $billing_phone . "\n";
}

wc_get_template( 'emails/plain/email-addresses.php', array( 'order' => $order ) );

echo "\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
