<?php
/**
 * HTML Template Email Recover Abandoned Cart
 *
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author YITH
 */

extract( $args );

do_action( 'woocommerce_email_header', $email_heading, $email );
?>

<?php echo wpautop( $email_content ); //phpcs:ignore ?>

<?php
	do_action( 'woocommerce_email_footer', $email );

