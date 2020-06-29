<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package     WC_Pre_Orders/Templates/Email
 * @author      WooThemes
 * @copyright   Copyright (c) 2013, WooThemes
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * Admin new order email (plain text)
 *
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$pre_wc_30 = version_compare( WC_VERSION, '3.0', '<' );
$billing_email = $pre_wc_30 ? $order->billing_email : $order->get_billing_email();
$billing_phone = $pre_wc_30 ? $order->billing_phone : $order->get_billing_phone();

echo $email_heading . "\n\n";

$full_name = version_compare( WC_VERSION, '3.0', '<' ) ? $order->billing_first_name . ' ' . $order->billing_last_name : $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
/* translators: 1: first name and last name */
echo sprintf( __( 'You have received a pre-order from %s. Their pre-order is as follows:', 'wc-pre-orders' ), $full_name ) . "\n\n";

echo "****************************************************\n\n";

do_action( 'woocommerce_email_before_order_table', $order, true, $plain_text, $email );

/* translators: 1: order number */
echo sprintf( __( 'Order number: %s', 'wc-pre-orders'), $order->get_order_number() ) . "\n";
/* translators: 1: order date */
echo sprintf( __( 'Order date: %s', 'wc-pre-orders'), date_i18n( __( 'jS F Y', 'wc-pre-orders' ), strtotime( $pre_wc_30 ? $order->order_date : ( $order->get_date_created() ? gmdate( 'Y-m-d H:i:s', $order->get_date_created()->getOffsetTimestamp() ) : '' ) ) ) ) . "\n";

do_action( 'woocommerce_email_order_meta', $order, true, $plain_text, $email );

echo "\n" . ( $pre_wc_30 ? $order->email_order_items_table( array( 'plain_text' => true, 'sent_to_admin' => true ) ) : wc_get_email_order_items( $order, array( 'plain_text' => true, 'sent_to_admin' => true ) ) );

echo "----------\n\n";

if ( $totals = $order->get_order_item_totals() ) {
	foreach ( $totals as $total ) {
		echo $total['label'] . "\t " . $total['value'] . "\n";
	}
}

echo "\n****************************************************\n\n";

do_action( 'woocommerce_email_after_order_table', $order, true, $plain_text, $email );

_e( 'Customer details', 'wc-pre-orders' );

if ( $billing_email ) {
	echo __( 'Email:', 'wc-pre-orders' );
	echo $billing_email . "\n";
}
if ( $billing_phone ) {
	echo __( 'Tel:', 'wc-pre-orders' );
	echo $billing_phone . "\n";
}
wc_get_template( 'emails/plain/email-addresses.php', array( 'order' => $order ) );

echo "\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
