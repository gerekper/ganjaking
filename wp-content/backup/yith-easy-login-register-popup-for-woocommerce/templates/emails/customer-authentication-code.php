<?php
/**
 * Customer Authentication Code email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/yith-welrp/emails/customer-authentication-code.php.
 */

defined( 'YITH_WELRP' ) || exit; // Exit if accessed directly

?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer first name */ ?>
	<p><?php printf( esc_html__( 'Hi %s,', 'yith-easy-login-register-popup-for-woocommerce' ), esc_html( $user_login ) ); ?>
		<?php /* translators: %s: Store name */ ?>
	<p><?php printf( esc_html__( 'Someone has requested a new password for the following account on %s:', 'yith-easy-login-register-popup-for-woocommerce' ), esc_html( wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ) ); ?></p>
<?php /* translators: %s Customer username */ ?>
	<p><?php printf( esc_html__( 'Username: %s', 'yith-easy-login-register-popup-for-woocommerce' ), esc_html( $user_login ) ); ?></p>
	<p><?php esc_html_e( 'If it was not you, just ignore this email. If you\'d like to proceed, copy and paste the following code into the authentication popup to validate the request and set a new password', 'yith-easy-login-register-popup-for-woocommerce' ); ?></p>
	<p><strong><?php echo esc_html( $authentication_code ); ?></strong>

<?php
/**
 * Show user-defined additonal content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

do_action( 'woocommerce_email_footer', $email );
