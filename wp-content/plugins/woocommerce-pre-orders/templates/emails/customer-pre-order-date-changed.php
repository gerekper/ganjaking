<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package     WC_Pre_Orders/Templates/Email
 */

/**
 * Customer pre-order date changed notification email
 *
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php
do_action( 'woocommerce_email_header', $email_heading, $email );
?>

<?php if ( $availability_date ) : ?>
<p>
	<?php
	/* translators: %s: availability date */
	printf( esc_html__( 'Your pre-order release date has been changed. The new release date is %s. Your order details are shown below for your reference.', 'wc-pre-orders' ), esc_html( $availability_date ) );
	?>
</p>
<?php else : ?>
	<p>
	<?php esc_html_e( 'Your pre-order release date has been changed. Your order details are shown below for your reference.', 'wc-pre-orders' ); ?>
	</p>
<?php endif; ?>

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
