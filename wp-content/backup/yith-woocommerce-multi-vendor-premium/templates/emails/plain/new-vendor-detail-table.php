<?php
/**
 * Admin new order email
 *
 * @author WooThemes
 * @package WooCommerce/Templates/Emails/HTML
 * @version 2.0.0
 *
 * @var string $email_heading
 * @var YITH_Vendor $vendor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$store_name_label = apply_filters( 'yith_wcmv_vendor_admin_settings_store_name_label', __( 'Store name', 'yith-woocommerce-product-vendors' ) );
$store_email_label = apply_filters( 'yith_wcmv_vendor_admin_settings_store_email_label', __( 'Store email', 'yith-woocommerce-product-vendors' ) );

echo __( 'Owner', 'yith-woocommerce-product-vendors' ) . ':';
echo $owner->user_firstname . ' ' . $owner->user_lastname . " \n\n";

echo $store_name_label . ':';
echo $vendor->name . " \n\n";

echo __( 'Location', 'yith-woocommerce-product-vendors' ) . ':';
echo $vendor->location . " \n\n";

echo $store_email_label . ':';
echo $vendor->store_email . " \n\n";

echo __( 'Telephone', 'yith-woocommerce-product-vendors' ) . ':';
echo $vendor->telephone . " \n\n";