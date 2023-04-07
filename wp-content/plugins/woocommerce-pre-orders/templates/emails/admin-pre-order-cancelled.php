<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package WC_Pre_Orders/Templates/Email
 */

/**
 * Admin pre-order cancelled notification email
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php
/**
 * Adds the email header content
 *
 * @since 1.7.3
 */
do_action( 'woocommerce_email_header', $email_heading, $email );
?>

<p>
<?php
$pre_wc_30 = version_compare( WC_VERSION, '3.0', '<' );
$full_name = $pre_wc_30 ? $order->billing_first_name . ' ' . $order->billing_last_name : $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();

/* Translators: %s Full name of the customer */
printf( esc_html__( 'A pre-order from %s has been cancelled. The order details are shown below for your reference.', 'woocommerce-pre-orders' ), esc_html( $full_name ) );
?>
</p>

<?php if ( $message ) : ?>
	<blockquote>
		<?php echo wpautop( wptexturize( $message ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</blockquote>
<?php endif; ?>

<?php
/**
 * Injects the order details of the email
 *
 * @since 1.7.3
 */
do_action( 'woocommerce_email_order_details', $order, false, $plain_text, $email );

/**
 * Adds the order meta details
 *
 * @since 1.7.3
 */
do_action( 'woocommerce_email_order_meta', $order, false, $plain_text, $email );

/**
 * Adds customer details
 *
 * @since 1.7.3
 */
do_action( 'woocommerce_email_customer_details', $order, false, $plain_text, $email );

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/**
 * Adds the email footer
 *
 * @since 1.7.3
 */
do_action( 'woocommerce_email_footer', $email );
?>
