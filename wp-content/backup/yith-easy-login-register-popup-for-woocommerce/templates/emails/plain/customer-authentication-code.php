<?php
/**
 * Customer Authentication Code email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/yith-welrp/emails/plain/customer-authentication-code.php.
 */

defined( 'YITH_WELRP' ) || exit; // Exit if accessed directly

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/* translators: %s: Customer first name */
echo sprintf( esc_html__( 'Hi %s,', 'yith-easy-login-register-popup-for-woocommerce' ), esc_html( $user_login ) ) . "\n\n";
/* translators: %s: Store name */
echo sprintf( esc_html__( 'Someone has requested a new password for the following account on %s:', 'yith-easy-login-register-popup-for-woocommerce' ), esc_html( wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ) ) . "\n\n";
/* translators: %s: Customer username */
echo sprintf( esc_html__( 'Username: %s', 'yith-easy-login-register-popup-for-woocommerce' ), esc_html( $user_login ) ) . "\n\n";
echo esc_html__( 'If it was not you, just ignore this email. If you\'d like to proceed, copy and paste the following code into the authentication popup to validate the request and set a new password', 'yith-easy-login-register-popup-for-woocommerce' ) . "\n\n";
echo esc_html( $authentication_code ) . "\n\n";

echo "\n\n----------------------------------------\n\n";

/**
 * Show user-defined additonal content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );
	echo "\n\n----------------------------------------\n\n";
}

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
