<?php
/**
 * Admin new order email
 *
 * @author WooThemes
 * @package WooCommerce/Templates/Emails/HTML
 * @version 2.0.0
 *
 * @var string $email_heading
 * @var YITH_Commission $commission
 * @var WC_Order $order
 * @var WC_Product $product
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( is_null( $vendor ) || is_null( $commission ) || is_null( $order ) ){
	return false;
}

echo __( 'Status', 'yith-woocommerce-product-vendors' ) . ':';
echo $commission->get_status( 'display' ) . " \n\n";

echo __( 'Date', 'yith-woocommerce-product-vendors' ) . ':';
echo $commission->get_date( 'display' ) . " \n\n";

echo __( 'Amount', 'yith-woocommerce-product-vendors' ) . ':';
echo $commission->get_amount( 'display', array( 'currency' => $order->get_currency() ) ) . " \n\n";

echo __( 'PayPal email', 'yith-woocommerce-product-vendors' ) . ':';
echo $vendor->paypal_email . " \n\n";

echo __( 'Vendor', 'yith-woocommerce-product-vendors' ) . ':';
echo $vendor->name . " \n\n";

echo __( 'Order number', 'yith-woocommerce-product-vendors' ) . ':';
echo $order->get_order_number() . " \n\n";

echo __( 'Product', 'yith-woocommerce-product-vendors' ) . ':';
echo $item['name'] . " \n\n";