<?php
/**
 * Anti Fraud admin email
 *
 * @version       1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php 
	/**
	 * Email header
	 *
	 * @since  1.0.0
	 */
	do_action( 'woocommerce_email_header', $email_heading ); 
?>

	<p>
		<?php 
		/* translators: 1. order ID, 2. resik score. */
		printf( esc_html__( 'An order with ID #%1$s scored a Risk Score of %2$s.', 'woocommerce-anti-fraud' ), esc_html__($order_id), esc_html__($score) ); 
		?>
	</p>
	<p>
		<?php 
		/* translators: 1. start of link, 2. end of link. */
			printf( esc_html__( '%1$sClick here to view the order.%2$s.', 'woocommerce-anti-fraud' ), '<a href="' . esc_url($order_url) . '">', '</a>' ); 
		?>
	</p>

<?php 
	/**
	 * Email footer
	 *
	 * @since  1.0.0
	 */
	do_action( 'woocommerce_email_footer' ); 
?>
