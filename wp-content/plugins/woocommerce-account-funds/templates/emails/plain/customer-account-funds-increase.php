<?php
/**
 * Customer Account Funds Increase Email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/plain/customer-account-funds-increase.php.
 *
 * @version 2.1.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo "= " . $email_heading . " =\n\n";

echo sprintf( __( 'Your account funds on %1$s have increased from %2$s to %3$s.', 'woocommerce-account-funds' ), $home_url, $current_funds, $new_funds ) . "\n\n";

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
