<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package     WC_Pre_Orders/Templates/Email
 */

/**
 * Customer pre-order cancelled notification email
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

esc_html_e( 'Your pre-order has been cancelled. Your order details are shown below for your reference.', 'woocommerce-pre-orders' );
?>
</p>

<?php if ( $message ) : ?>
<blockquote><?php echo wpautop( wptexturize( $message ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></blockquote>
<?php endif; ?>

<?php do_action( 'woocommerce_email_order_details', $order, false, $plain_text, $email ); ?>

<?php do_action( 'woocommerce_email_order_meta', $order, false, $plain_text, $email ); ?>

<?php do_action( 'woocommerce_email_customer_details', $order, false, $plain_text, $email ); ?>

<?php
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}
?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
