<?php
/**
 * Customer verify account email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-verify-account.php.
 *
 * @author  YITH <plugins@yithemes.com>
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
<?php /* translators: %1$s: Site title */ ?>
	<p><?php printf( __( 'Thanks for creating an account on %1$s.', 'yith-woocommerce-customize-myaccount-page' ), esc_html( $blogname ) ); ?></p>
<?php /* translators: %1$s: My Account link */ ?>
	<p><?php echo esc_html__( 'To complete registration process you need to verify your account email by clicking this link:', 'yith-woocommerce-customize-myaccount-page' ); ?></p>
	<p><a href="<?php echo esc_attr( $verify_url ); ?>"><?php printf( esc_html__( 'Verify your account >', 'yith-woocommerce-customize-myaccount-page' ) ); ?></a></p>
	<p><?php esc_html_e( 'We look forward to seeing you soon.', 'yith-woocommerce-customize-myaccount-page' ); ?></p>
<?php /* translators: %1$s: Site title */ ?>
	<p><?php printf( __( 'Regards, <br>%1$s staff', 'yith-woocommerce-customize-myaccount-page' ), esc_html( $blogname ) ); ?></p>
<?php
do_action( 'woocommerce_email_footer', $email );
