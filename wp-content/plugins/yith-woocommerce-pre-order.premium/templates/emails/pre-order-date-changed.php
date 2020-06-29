<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

do_action( 'woocommerce_email_header', $email_heading, $email );

$order        = wc_get_order( $email->object['customer_order_id'] );
$product      = wc_get_product( $email->object['product_id'] );
$customer     = get_user_by( 'id', $order->get_user_id() );
$product_link = '<a href="' . $product->get_permalink() . '">' . $product->get_title() . '</a>';
$body         = ! empty( $email->email_body ) ? $email->email_body : '';
$name         = '';
if ( $customer ) {
	$name = $customer->display_name ? ucwords( $customer->display_name ) : ucwords( $customer->nickname );
}

$gmt_offset = get_option( 'gmt_offset' );
if ( 0 <= $gmt_offset )
	$offset_name = '+' . $gmt_offset;
else
	$offset_name = (string)$gmt_offset;

$offset_name = str_replace( array( '.25', '.5', '.75' ), array( ':15', ':30', ':45' ), $offset_name );
$offset_name = 'UTC' . $offset_name;

echo '<p>';
$body = str_replace(
	array(
		'{customer_name}',
		'{product_name}',
		'{previous_sale_date}',
		'{new_sale_date}',
		'{offset_name}'
	),
	array(
		$name,
		$product_link,
		$email->object['previous_sale_date'],
		$email->object['new_sale_date'],
		$offset_name
	),
	$body
);
echo $body;
echo '</p>';

do_action( 'woocommerce_email_footer' );