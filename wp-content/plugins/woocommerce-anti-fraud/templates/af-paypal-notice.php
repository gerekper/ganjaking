<?php
/**
 * Anti Fraud Paypal email
 *
 * @version       1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

	<p><?php printf( esc_html__(get_option('wc_settings_anti_fraud_email_body') ) ); ?></p>
	
	<p>
		<?php /* translators: 1. start of link, 2. end of link. */ ?>
		<?php printf( esc_html__( '%1$sClick here to verify the order.%2$s.', 'woocommerce-anti-fraud' ), '<a href="' . esc_url($url) . '" style="cursor:pointer">', '</a>' ); ?>
	</p>

<?php do_action( 'woocommerce_email_footer' ); ?>
