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
 * Customer pre-order available notification email
 *
 * @since 1.0.0
 * @version 1.5.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$pre_wc_30 = version_compare( WC_VERSION, '3.0', '<' );
$billing_email = $pre_wc_30 ? $order->billing_email : $order->get_billing_email();
$billing_phone = $pre_wc_30 ? $order->billing_phone : $order->get_billing_phone();

echo $email_heading . "\n\n";

if ( 'pending' === $order->get_status() && ! WC_Pre_Orders_Manager::is_zero_cost_order( $order ) ) :

	echo __( 'Your pre-order is now available, but requires payment. Please pay for your pre-order now: ', 'wc-pre-orders' ) . esc_url( $order->get_checkout_payment_url() ) . "\n\n";

elseif ( 'failed' === $order->get_status() || 'on-hold' === $order->get_status() ) :

	echo __( "Your pre-order is now available, but automatic payment failed. Please update your payment information now : ", 'wc-pre-orders' ) . esc_url( $order->get_checkout_payment_url() ) . "\n\n";

else :

	echo __( "Your pre-order is now available. Your order details are shown below for your reference.", 'wc-pre-orders' ) . "\n\n";

endif;

if ( $message ) :

echo "----------\n\n";
echo wptexturize( $message ) . "\n\n";
echo "----------\n\n";

endif;

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

echo esc_html__( 'Thanks for shopping with us.', 'wc-pre-orders' ) . "\n\n";

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
