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

echo '= ' . esc_html( $email_heading ) . " =\n\n";

/* translators: %s Customer first name */
echo sprintf( esc_html__( 'Hi %s,', 'yith-woocommerce-customize-myaccount-page' ), esc_html( $user_login ) ) . "\n\n";
/* translators: %1$s: Site title, %2$s: Username, %3$s: My account link */
echo sprintf( esc_html__( 'Thanks for creating an account on %1$s. To complete registration process you need to verify your account email by clicking this link: %2$s', 'yith-woocommerce-customize-myaccount-page' ), esc_html( $blogname ), esc_url( $verify_url ) ) . "\n\n";

echo esc_html__( 'We look forward to seeing you soon.', 'yith-woocommerce-customize-myaccount-page' ) . "\n\n";

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

