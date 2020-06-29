<?php
/**
 * Admin new order email (plain text)
 *
 * @author		WooThemes
 * @package 	WooCommerce/Templates/Emails/Plain
 * @version 	2.0.0
 *
 * @var string $email_heading
 * @var YITH_Commission $commission
 * @var bool $sent_to_admin
 * @var bool $plain_text
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo "= " . $email_heading . " =\n\n";

echo __( 'The commission has been credited successfully.', 'yith-woocommerce-product-vendors' ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

do_action( 'woocommerce_email_before_commission_table', $commission, $sent_to_admin, $plain_text );

echo strtoupper( sprintf( __( 'Commission number: %s', 'yith-woocommerce-product-vendors' ), $commission->id ) ) . "\n";
echo date_i18n( __( 'jS F Y', 'yith-woocommerce-product-vendors' ), strtotime( $commission->get_date() ) ) . "\n";

echo "\n" . $commission->email_commission_details_table( true );

do_action( 'woocommerce_email_after_commission_table', $commission, $sent_to_admin, $plain_text );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );