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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php
$pre_wc_30 = version_compare( WC_VERSION, '3.0', '<' );
$billing_email = $pre_wc_30 ? $order->billing_email : $order->get_billing_email();
$billing_phone = $pre_wc_30 ? $order->billing_phone : $order->get_billing_phone();

if ( 'pending' === $order->get_status() && ! WC_Pre_Orders_Manager::is_zero_cost_order( $order ) ) : ?>

	<p><?php
/* translators: 1: href link for checkout payment url 2: closing href link */
printf( __( "Your pre-order is now available, but requires payment. %sPlease pay for your pre-order now.%s", 'wc-pre-orders' ), '<a href="' . $order->get_checkout_payment_url() . '">', '</a>' ); ?></p>

<?php elseif ( 'failed' === $order->get_status() || 'on-hold' === $order->get_status() ) : ?>

	<p><?php
/* translators: 1: href link for checkout payment url 2: closing href link */
printf( __( "Your pre-order is now available, but automatic payment failed. %sPlease update your payment information now.%s", 'wc-pre-orders' ), '<a href="' . $order->get_checkout_payment_url() . '">', '</a>' ); ?></p>

<?php else : ?>

<p><?php _e( "Your pre-order is now available. Your order details are shown below for your reference.", 'wc-pre-orders' ); ?></p>

<?php endif; ?>

<?php if ( $message ) : ?>
	<blockquote><?php echo wpautop( wptexturize( $message ) ); ?></blockquote>
<?php endif; ?>

<?php
do_action( 'woocommerce_email_before_order_table', $order, false, $plain_text, $email );

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

do_action( 'woocommerce_email_after_order_table', $order, false, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

?>
<p>
<?php esc_html_e( 'Thanks for shopping with us.', 'wc-pre-orders' ); ?>
</p>
<?php

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
