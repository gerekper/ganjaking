<?php
/**
 * Anti Fraud admin email
 *
 * @author        WooThemes
 * @version       1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

	<p><?php printf( __( 'An order with ID #%s scored a Risk Score of %s.', 'woocommerce-anti-fraud' ), $order_id, $score ); ?></p>
	<p><?php printf( __( '%sClick here to view the order.%s.', 'woocommerce-anti-fraud' ), '<a href="' . $order_url . '">', '</a>' ); ?></p>

<?php do_action( 'woocommerce_email_footer' ); ?>