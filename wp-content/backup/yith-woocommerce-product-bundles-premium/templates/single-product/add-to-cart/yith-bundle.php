<?php
/**
 * Template for bundles
 *
 * @version 4.8.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/** @var WC_Product_Yith_Bundle $product */
global $product;

if ( !$product->is_purchasable() ) {
    return;
}

?>

<?php echo wc_get_stock_html( $product ); ?>

<?php if ( $product->is_in_stock() ) : ?>

    <?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

    <form class="cart" method="post" enctype='multipart/form-data'>


        <?php
        $bundled_items = $product->get_bundled_items();
        if ( $bundled_items ) {
            echo '<table class="yith-wcpb-product-bundled-items">';
            foreach ( $bundled_items as $bundled_item ) {
                $bundled_product = $bundled_item->get_product();
                $bundled_post    = get_post( yit_get_base_product_id( $bundled_product ) );
                $quantity        = $bundled_item->get_quantity();
                ?>
                <tr>
                    <td class="yith-wcpb-product-bundled-item-image"> <?php echo $bundled_product->get_image() ?></td>
                    <td class="yith-wcpb-product-bundled-item-data">
                        <h3><a href="<?php echo $bundled_product->get_permalink() ?>">
                                <?php echo $quantity . ' x ' . $bundled_product->get_title() ?>
                            </a>
                        </h3>
                        <p><?php echo $bundled_post->post_excerpt; ?></p>

                        <?php
                        if ( $bundled_product->has_enough_stock( $quantity ) && $bundled_product->is_in_stock() ) {
                            echo '<div class="yith-wcpb-product-bundled-item-instock">' . __( 'In stock', 'woocommerce' ) . '</div>';
                        } else {
                            echo '<div class="yith-wcpb-product-bundled-item-outofstock">' . __( 'Out of stock', 'woocommerce' ) . '</div>';
                        }
                        ?>

                    </td>
                </tr>
                <?php
            }
            echo '</table>';
        }
        ?>

        <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
        <?php
        if ( !$product->is_sold_individually() )
            woocommerce_quantity_input( array(
                                            'min_value' => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
                                            'max_value' => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product )
                                        ) );
        ?>

        <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>"/>

        <button type="submit" class="single_add_to_cart_button button alt"><?php echo $product->single_add_to_cart_text(); ?></button>

        <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
    </form>

    <?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>