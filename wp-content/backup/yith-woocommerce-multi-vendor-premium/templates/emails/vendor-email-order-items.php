<?php
/**
 * Email Order Items
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     2.1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$vendor_products = $vendor->get_products( array( 'fields' => 'ids', 'post_status' => 'any' ) );
$commission_ids = YITH_Commissions()->get_commissions( array( 'order_id' => $order->get_id(), 'status' => 'all' )  ) ?>
<tbody>
<?php foreach ( $items as $item_id => $item ) :
    $products_from_item = null;

    if( YITH_Vendors()->is_wc_2_7_or_greather && is_callable( array( $item, 'get_product' ) ) ){
        /**
         * @var $item WC_Order_Item_Product
         */
        $products_from_item = $item->get_product();
    }

    else {
        $products_from_item = $order->get_product_from_item( $item );
    }

	$_product = apply_filters( 'woocommerce_order_item_product', $products_from_item, $item );

    if( ! empty( $_product ) && ! in_array( yit_get_base_product_id( $_product ), $vendor_products ) ) {
        continue;
    }

	$item_meta = YITH_Vendors()->is_wc_2_7_or_greather ? new WC_Order_Item_Product( $item ) : new WC_Order_Item_Meta( $items['item_meta'], $_product );

    /** @var $commission YITH_Commission */
    $commission = false;

    if( ! empty( $item['commission_id'] ) ){
        $commission = YITH_Commission( $item['commission_id'] );
        $commission_ids[] = $commission->id;
    }

	if ( apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
		?>
		<tr class="<?php echo esc_attr( apply_filters( 'woocoomerce_order_item_class', 'order_item', $item, $order ) ); ?>">
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php

				// Show title/image etc
				if ( $show_image ) {
					echo apply_filters( 'woocommerce_order_item_thumbnail', '<img src="' . ( $_product->get_image_id() ? current( wp_get_attachment_image_src( $_product->get_image_id(), 'thumbnail') ) : wc_placeholder_img_src() ) .'" alt="' . __( 'Product Image', 'yith-woocommerce-product-vendors' ) . '" height="' . esc_attr( $image_size[1] ) . '" width="' . esc_attr( $image_size[0] ) . '" style="vertical-align:middle; margin-right: 10px;" />', $item );
				}

				// Product name
				echo apply_filters( 'woocommerce_order_item_name', $item['name'], $item, $_product->is_visible() );

				// SKU
				if ( $show_sku && $_product instanceof WC_Product && $_product->get_sku() ) {
					echo ' (#' . $_product->get_sku() . ')';
				}

				// allow other plugins to add additional product information here
				do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order );

				if( function_exists( 'wc_display_item_meta' ) ) {
				    wc_display_item_meta( $item );
				}

				// Variation
				if ( ! empty( $item_meta ) && ! empty( $item_meta->meta ) ) {
					echo '<br/><small>' . nl2br( $item_meta->display( true, true, '_', "\n" ) ) . '</small>';
				}

                // Commission id
				if ( ! empty( $item['commission_id'] ) ) {
                    $link = '<a href="' . $commission->get_view_url( 'admin' ) . '">' . $commission->id . '</a>';
					echo '<br/><small>' . _x( 'Commission id:', 'New Order Email', 'yith-woocommerce-product-vendors' ) . ' ' . $link . '</small>';
				}

				// File URLs
				if ( $show_download_links && $_product instanceof WC_Product && $_product->exists() && $_product->is_downloadable() ) {

					$download_files = $order->get_item_downloads( $item );
					$i              = 0;

					foreach ( $download_files as $download_id => $file ) {
						$i++;

						if ( count( $download_files ) > 1 ) {
							$prefix = sprintf( __( 'Download %d', 'yith-woocommerce-product-vendors' ), $i );
						} elseif ( $i == 1 ) {
							$prefix = __( 'Download', 'yith-woocommerce-product-vendors' );
						}

						echo '<br/><small>' . $prefix . ': <a href="' . esc_url( $file['download_url'] ) . '" target="_blank">' . esc_html( $file['name'] ) . '</a></small>';
					}
				}

				// allow other plugins to add additional product information here
				do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order );

			?></td>
			<td style="text-align:center; vertical-align:middle; border: 1px solid #eee;"><?php echo $item['qty'] ;?></td>
            <?php $total = 'split' == get_option( 'yith_wpv_commissions_tax_management', 'website' ) ?  ($item->get_total() + $item->get_total_tax() ) : $item->get_total(); ?>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee;"><?php echo wc_price( $total ); ?></td>
			<td style="text-align:center; vertical-align:middle; border: 1px solid #eee;"><?php echo $commission ? $commission->rate * 100 . '%' : '-'; ?></td>
			<?php if( $tax_credited_to_vendor ) : ?>
                <td style="text-align:left; vertical-align:middle; border: 1px solid #eee;"><?php echo $order->get_item_tax( $item ); ?></td>
			<?php endif; ?>
            <td style="text-align:left; vertical-align:middle; border: 1px solid #eee;"><?php echo $commission ? wc_price( $commission->amount ) : '-'; ?></td>
		</tr>
		<?php
	}

	if ( $show_purchase_note && $_product instanceof WC_Product && ( $purchase_note = $_product->get_purchase_note() ) ) : ?>
		<tr>
			<td colspan="3" style="text-align:left; vertical-align:middle; border: 1px solid #eee;"><?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); ?></td>
		</tr>
	<?php endif; ?>

<?php endforeach; ?>