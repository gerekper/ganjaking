<?php
/**
 * Anti Fraud Paypal email
 *
 * @author        WooThemes
 * @version       1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

	<p><?php printf( __(get_option('wc_settings_anti_fraud_email_body') ) ); ?></p>
	<p><?php printf( __( '%sClick here to verify the order.%s.', 'woocommerce-anti-fraud' ), '<a href="' . $url . '" style="cursor:pointer">', '</a>' ); ?></p>

<?php do_action( 'woocommerce_email_footer' ); ?>