<?php
/**
 * Email Order Items
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-items.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see           http://docs.woothemes.com/document/template-structure/
 * @author        WooThemes
 * @package       WooCommerce/Templates/Emails
 * @version       2.1.2
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $current_email;
$template   = yith_wcet_get_email_template( $current_email );
$meta       = yith_wcet_get_template_meta( $template );
$show_image = ( isset( $meta[ 'show_prod_thumb' ] ) ) ? $meta[ 'show_prod_thumb' ] : false;

$count_items   = count( $items );
$i             = 0;
foreach ( $items as $item_id => $item ) :
    $product = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
    $item_meta = new WC_Order_Item_Meta( $item, $product );

    $i++;
    $purchase_note = $show_purchase_note && is_object( $product ) && ( $purchase_note = get_post_meta( $product->id, '_purchase_note', true ) );
    $last_class    = ( $i == $count_items && !$purchase_note ) ? 'last' : 'not_last';

    if ( apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
        ?>
        <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
            <td class="yith-wcet-order-items-table-element <?php echo $last_class; ?>" style="text-align:left; vertical-align:middle; word-wrap:break-word;">
                <table class="yith-wcet-order-items-table-product-title yith-wcet-no-border">
                    <tr>
                        <?php

                        // Show title/image etc
                        if ( $show_image && is_object( $product ) ) {
                            echo '<td class="yith-wcet-no-border" width="' . esc_attr( $image_size[ 0 ] ) . 'px" style="vertical-align:middle; padding: 0 10px 0 0 !important;">';
                            echo apply_filters( 'woocommerce_order_item_thumbnail', '<img src="' . ( $product->get_image_id() ? current( wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' ) ) : wc_placeholder_img_src() ) . '" alt="' . esc_attr__( 'Product Image', 'woocommerce' ) . '" height="' . esc_attr( $image_size[ 1 ] ) . '" width="' . esc_attr( $image_size[ 0 ] ) . '" style="vertical-align:middle;" /> ', $item );
                            echo '</td>';
                        }

                        echo '<td class="yith-wcet-no-border" style="vertical-align:middle; padding: 0 !important;">';

                        // Product name
                        echo apply_filters( 'woocommerce_order_item_name', $item[ 'name' ], $item, false );

                        // SKU
                        if ( $show_sku && is_object( $product ) && $product->get_sku() ) {
                            echo ' (#' . $product->get_sku() . ')';
                        }

                        // allow other plugins to add additional product information here
                        do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order );

                        // Variation
                        if ( !empty( $item_meta->meta ) ) {
                            echo '<br/><small>' . nl2br( $item_meta->display( true, true, '_', "\n" ) ) . '</small>';
                        }

                        // File URLs
                        if ( $show_download_links ) {
                            $order->display_item_downloads( $item );
                        }

                        // allow other plugins to add additional product information here
                        do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order );

                        echo '</td>';
                        ?>
                    </tr>
                </table>
            </td>
            <td class="yith-wcet-order-items-table-el-quantity-<?php echo $last_class; ?>"
                style="vertical-align:middle;"><?php echo apply_filters( 'woocommerce_email_order_item_quantity', $item[ 'qty' ], $item ); ?></td>
            <td class="yith-wcet-order-items-table-el-price-<?php echo $last_class; ?>"
                style="vertical-align:middle;"><?php echo apply_filters( 'yith_wcet_email_order_item_price', $order->get_formatted_line_subtotal( $item ), $item, $order ); ?></td>
        </tr>
        <?php
    }

    if ( $show_purchase_note && is_object( $product ) && ( $purchase_note = get_post_meta( $product->id, '_purchase_note', true ) ) ) :
        $last_class = $i == $count_items ? 'last' : 'not_last';
        ?>
        <tr>
            <td class="yith-wcet-order-items-table-element <?php echo $last_class; ?>" colspan="3"
                style="vertical-align:middle;"><?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); ?></td>
        </tr>
    <?php endif; ?>

<?php endforeach; ?>
