<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

do_action( 'woocommerce_email_header', $email_heading, $email );

$body     = ! empty( $email->email_body ) ? $email->email_body : '';
$product  = wc_get_product( $email->object['product_id'] );
$order_id = $email->object['customer_order_id'];
$order    = wc_get_order( $order_id );
$customer = get_user_by( 'id', $order->get_user_id() );

$product_link = '<a href="' . $product->get_permalink() . '">' . $product->get_title() . '</a>';
$order_link   = '<a href="' . $order->get_view_order_url() . '">' . $order_id . '</a>';

echo '<p>';
$body = str_replace(
	array(
		'{customer_name}',
		'{product_name}',
		'{order_number}'
	),
	array(
		ucwords( $customer->display_name ),
		$product_link,
		$order_link
	),
	$body
);
echo $body;
echo '</p>';


?>

<?php
if ( $product->is_downloadable() && $order->is_download_permitted() ) {
	?>
    <p><?php esc_html_e( 'Download link:', 'yith-pre-order-for-woocommerce' ); ?></p><?php
	foreach ( $order->get_items() as $item ) {
		$item_product_id = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
		if ( $email->object['product_id'] == $item_product_id ) {
			if ( $order instanceof WC_Data ) {
				wc_display_item_meta( $item );
				wc_display_item_downloads( $item );
			} else {
				$order->display_item_meta( $item );
				$order->display_item_downloads( $item );
			}
		}
	}
}

do_action( 'woocommerce_email_footer' );