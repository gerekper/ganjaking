<?php
/**
 * Customer verify account email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-verify-account.php.
 *
 * @author  YITH
 * @package YITH WooCommerce Customize My Account Page
 * @version 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s Customer first name */ ?>
	<p><?php printf( esc_html__( 'Hi %s,', 'yith-woocommerce-customize-myaccount-page' ), esc_html( stripslashes( $customer->user_login ) ) ); ?></p>
<?php /* translators: %1$s: Site title, %2$s: Username, %3$s: My account link */ ?>
	<p><?php printf( __( 'Thanks for creating an account on %1$s. To complete registration process you need to verify your account email by clicking this link: %2$s', 'yith-woocommerce-customize-myaccount-page' ), esc_html( $blogname ), make_clickable( esc_url( $verify_url ) ) ); ?></p><?php // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>

	<p><?php esc_html_e( 'We look forward to seeing you soon.', 'yith-woocommerce-customize-myaccount-page' ); ?></p>
<?php
do_action( 'woocommerce_email_footer', $email );
