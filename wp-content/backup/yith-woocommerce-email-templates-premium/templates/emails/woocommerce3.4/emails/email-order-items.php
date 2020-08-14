<?php
/**
 * Email Order Items
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-items.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $current_email;
$template   = yith_wcet_get_email_template( $current_email );
$meta       = yith_wcet_get_template_meta( $template );
$show_image = ( isset( $meta[ 'show_prod_thumb' ] ) ) ? $meta[ 'show_prod_thumb' ] : false;

$count_items = count( $items );
$i           = 0;

$text_align        = is_rtl() ? 'right' : 'left';

foreach ( $items as $item_id => $item ) :
    $product = $item->get_product();
    $sku           = '';
    $purchase_note = '';
    $image         = '';
    $i++;
    $last_class = ( $i == $count_items && !$purchase_note ) ? 'last' : 'not_last';

    if ( !apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
        continue;
    }

    if ( is_object( $product ) ) {
        $sku           = $product->get_sku();
        $purchase_note = $product->get_purchase_note();
        $image         = $product->get_image( $image_size );
    }

    ?>
    <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
        <td class="yith-wcet-order-items-table-element <?php echo $last_class; ?>" style="text-align:left; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
            <table class="yith-wcet-order-items-table-product-title yith-wcet-no-border">
                <tr>
                    <?php

                    // Show title/image etc
                    if ( $show_image ) {
                        echo '<td class="yith-wcet-no-border" width="' . esc_attr( $image_size[ 0 ] ) . 'px" style="vertical-align:middle; padding: 0 10px 0 0 !important;">';
                        echo wp_kses_post( apply_filters( 'woocommerce_order_item_thumbnail', '<img src="' . ( $product->get_image_id() ? current( wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' ) ) : wc_placeholder_img_src() ) . '" alt="' . esc_attr__( 'Product image', 'woocommerce' ) . '" height="' . esc_attr( $image_size[ 1 ] ) . '" width="' . esc_attr( $image_size[ 0 ] ) . '" style="vertical-align:middle;" /> ', $item ) );
                        echo '</td>';
                    }

                    echo '<td class="yith-wcet-no-border" style="vertical-align:middle; padding: 0 !important;">';

                    // Product name
                    echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );

                    // SKU
                    if ( $show_sku && $sku ) {
                        echo wp_kses_post( ' (#' . $sku . ')' );
                    }

                    // allow other plugins to add additional product information here
                    do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, $plain_text );

                    wc_display_item_meta( $item );

                    // allow other plugins to add additional product information here
                    do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, $plain_text );

                    echo '</td>';
                    ?>
                </tr>
            </table>
        </td>
        <td class="yith-wcet-order-items-table-el-quantity-<?php echo $last_class; ?>"
            style="vertical-align:middle;"><?php echo wp_kses_post( apply_filters( 'woocommerce_email_order_item_quantity', $item->get_quantity(), $item ) ); ?></td>
        <td class="yith-wcet-order-items-table-el-price-<?php echo $last_class; ?>"
            style="vertical-align:middle;"><?php echo wp_kses_post( apply_filters( 'yith_wcet_email_order_item_price', $order->get_formatted_line_subtotal( $item ), $item, $order ) ); ?></td>
    </tr>
    <?php

    if ( $show_purchase_note && $purchase_note ) {
        ?>
        <tr>
            <td colspan="3" style="text-align:left; vertical-align:middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
                <?php
                echo wp_kses_post( wpautop( do_shortcode( $purchase_note ) ) );
                ?>
            </td>
        </tr>
        <?php
    }
    ?>

<?php endforeach; ?>
