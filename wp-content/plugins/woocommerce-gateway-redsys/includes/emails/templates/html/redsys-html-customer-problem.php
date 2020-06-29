<?php
/**
 * Cancelled Order sent to Customer.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * @hooked WC_Emails::email_header() Output the email header
 */

do_action( 'woocommerce_email_header', $email_heading, $email );
$order       = new wc_order( $order_id );
$ds_error    = get_post_meta( $order->get_id(), '_redsys_error_payment_ds_error_value' );
$ds_response = get_post_meta( $order->get_id(), '_redsys_error_payment_ds_response_value' );
?>

	<p><?php printf( __( 'The order #%d has been cancelled. Order Details:', 'woocommerce-redsys' ), esc_html__( $order->get_order_number() ) ); ?></p>

	<?php if ( $ds_error ) { ?>
		<p><?php printf( __( 'The error was: #%d, Order Details: ', 'woocommerce-redsys' ), esc_html__( $ds_error ) ); ?></p>

	<?php }
	if ( $ds_response ) { ?>
		<p><?php printf( __( 'The error was: #%d, Order Details: ', 'woocommerce-redsys' ), esc_html__( $ds_response ) ); ?></p>
	<?php }

/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Emails::order_schema_markup() Adds Schema.org markup.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );
/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );
/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
