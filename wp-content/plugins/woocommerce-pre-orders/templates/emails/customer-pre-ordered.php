<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package     WC_Pre_Orders/Templates/Email
 */

/**
 * Customer pre-ordered order email
 *
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php
do_action( 'woocommerce_email_header', $email_heading, $email );

/* translators: %s: availability date */
$availability_date_text = ( ! empty( $availability_date ) ) ? sprintf( __( ' on %s.', 'wc-pre-orders' ), $availability_date ) : '.';
?>

<?php if ( WC_Pre_Orders_Order::order_will_be_charged_upon_release( $order ) ) : ?>

	<?php
	if ( WC_Pre_Orders_Order::order_has_payment_token( $order ) ) {
		/* translators: %s: availability date */
		echo '<p>' . sprintf( esc_html__( 'Your pre-order has been received. You will be automatically charged for your order via your selected payment method when your pre-order is released%s Your order details are shown below for your reference.', 'wc-pre-orders' ), $availability_date_text ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} else {
		/* translators: %s: availability date */
		echo '<p>' . sprintf( esc_html__( 'Your pre-order has been received. You will be prompted for payment for your order when your pre-order is released%s Your order details are shown below for your reference.', 'wc-pre-orders' ), $availability_date_text ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	?>
<?php else : ?>

<p>
	<?php
	/* translators: %s: availability date */
	printf( esc_html__( 'Your pre-order has been received. You will be notified when your pre-order is released%s Your order details are shown below for your reference.', 'wc-pre-orders' ), $availability_date_text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
</p>

<?php endif; ?>

<?php do_action( 'woocommerce_email_order_details', $order, false, $plain_text, $email ); ?>

<?php do_action( 'woocommerce_email_order_meta', $order, false, $plain_text, $email ); ?>

<?php do_action( 'woocommerce_email_customer_details', $order, true, $plain_text, $email ); ?>

<?php
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}
?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
