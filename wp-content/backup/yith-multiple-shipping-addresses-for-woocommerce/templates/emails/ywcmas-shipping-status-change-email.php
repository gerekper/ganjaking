<?php
/**
 * Email for user notification
 *
 * @author  Yithemes
 * @package yith-advanced-refund-system-for-woocommerce.premium\templates\emails
 */

if ( ! defined( 'YITH_WCMAS_VERSION' ) ) {
    exit; // Exit if accessed directly
}
$body         = ! empty( $email->email_body ) ? $email->email_body : '';
$order_id   = $email->order_id;
$order        = wc_get_order( $order_id );
$order_url    = $order->get_view_order_url();
$order_link   = '<a href="' . $order_url . '">#' . $order_id . '</a>';
$shipping_item = $order->get_item( $email->shipping_item_id );
$contents = $email->contents;
$statuses = yith_wcmas_shipping_item_statuses();

do_action( 'woocommerce_email_header', $email_heading, $email );

echo '<p>';
$body = str_replace(
    array(
	    '{order_number}',
	    '{new_status}',
	    '{old_status}'
    ),
    array(
        $order_link,
	    ! empty( $email->new_status ) ? $statuses[$email->new_status] : '',
	    ! empty( $email->old_status ) ? $statuses[$email->old_status] : ''
    ),
    $body
);
echo $body;
echo '</p>';

?>
<table>
	<thead><th colspan="3"><?php esc_html_e( 'Items', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></th></thead>
	<tbody>
	<?php
	foreach ( $contents as $item ) : ?>
		<?php
        $product = wc_get_product( ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'] );

		$dimensions = wc_get_image_size( array( 60, 60, 1 ) );
		$height     = esc_attr( $dimensions['height'] );
		$width      = esc_attr( $dimensions['width'] );
		$src        = ( $product->get_image_id() ) ? current( wp_get_attachment_image_src( $product->get_image_id(), 'shop_catalog' ) ) : wc_placeholder_img_src();

		$image = '<a href="' . $product->get_permalink() . '"><img src="'. $src . '" height="' . $height . '" width="' . $width . '" /></a>';
		?>
        <tr>
            <td><?php echo $image; ?></td>
            <td>
                <span><a href="<?php echo $product->get_permalink(); ?>"><?php echo $product->get_name(); ?></a></span>
            </td>
            <td>
                <strong>&times;</strong><span><?php echo $item['quantity']; ?></span>
            </td>
        </tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php

do_action( 'woocommerce_email_footer' );