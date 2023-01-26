<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package     WC_Pre_Orders/Templates/Email
 */

/**
 * Admin new order email
 *
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p>
<?php
$full_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
/* translators: %s: billing first name and last name */
printf( esc_html__( 'You have received a pre-order from %s. Their pre-order is as follows:', 'wc-pre-orders' ), $full_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
?>
</p>

<?php do_action( 'woocommerce_email_order_details', $order, true, $plain_text, $email ); ?>

<?php do_action( 'woocommerce_email_order_meta', $order, true, $plain_text ); ?>

<?php do_action( 'woocommerce_email_customer_details', $order, true, $plain_text, $email ); ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
