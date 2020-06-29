<?php
/**
 * Admin new order email
 *
 * @author WooThemes
 * @package WooCommerce/Templates/Emails/HTML
 * @version 2.0.0
 *
 * @var YITH_Commission $commission
 * @var YITH_Vendor $vendor
 * @var WC_Product $product
 * @var WC_Order $order
 * @var array $item
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( is_null( $vendor ) || is_null( $commission ) || is_null( $order ) ){
    return false;
}

?>

		<tr>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php _e( 'Status', 'yith-woocommerce-product-vendors' ) ?></td>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php echo $commission->get_status( 'display' ) ?></td>
		</tr>

		<tr>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php _e( 'Date', 'yith-woocommerce-product-vendors' ) ?></td>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php echo $commission->get_date( 'display' ) ?></td>
		</tr>

		<tr>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php _e( 'Amount', 'yith-woocommerce-product-vendors' ) ?></td>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php echo $commission->get_amount( 'display', array( 'currency' => $order->get_currency() ) ) ?></td>
		</tr>

		<tr>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php _e( 'Paypal email', 'yith-woocommerce-product-vendors' ) ?></td>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php echo $vendor->paypal_email ?></td>
		</tr>

		<tr>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php _e( 'Vendor', 'yith-woocommerce-product-vendors' ) ?></td>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php echo $vendor->name ?></td>
		</tr>

		<tr>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php _e( 'Order number', 'yith-woocommerce-product-vendors' ) ?></td>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php echo $order->get_order_number() ?></td>
		</tr>

		<tr>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php _e( 'Product', 'yith-woocommerce-product-vendors' ) ?></td>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php echo $item['name'] ?></td>
		</tr>
