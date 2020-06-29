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

echo __( 'New vendor registered', 'yith-woocommerce-product-vendors' ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

do_action( 'woocommerce_email_before_new_vendor_table', $vendor, $sent_to_admin, $plain_text );

echo strtoupper( __( 'A new user has made a request to become a vendor in your store.', 'yith-woocommerce-product-vendors' ) ) . "\n";

echo "\n";

yith_wcpv_get_template( 'new-vendor-detail-table', array( 'vendor' => $vendor, 'owner' => $vendor->get_owner() ), 'emails/plain' );

do_action( 'woocommerce_email_after_new_vendor_table', $commission, $sent_to_admin, $plain_text );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );