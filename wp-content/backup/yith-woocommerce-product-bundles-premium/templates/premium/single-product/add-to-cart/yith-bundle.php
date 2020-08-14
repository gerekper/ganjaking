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

echo wc_get_stock_html( $product );
?>

<?php if ( $product->is_in_stock() ) : ?>

    <?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

    <form class="cart yith-wcpb-bundle-form" method="post" enctype='multipart/form-data'
          data-product-id="<?php echo $product->get_id(); ?>"
          data-per-item-pricing="<?php echo $product->per_items_pricing ?>"
          data-ajax-update-price="<?php echo apply_filters( 'yith_wcpb_ajax_update_price_enabled', $product->per_items_pricing, $product ); ?>">

        <?php
        $bi_args = array(
            'available_variations' => $available_variations,
            'attributes'           => $attributes,
            'selected_attributes'  => $selected_attributes,
            'bundled_items'        => $bundled_items,
        );
        wc_get_template( '/single-product/add-to-cart/yith-bundle-items-list.php', $bi_args, '', YITH_WCPB_TEMPLATE_PATH . '/premium' );

        if ( !$product->is_purchasable() ) {
            echo '</form>';

            return;
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

        <?php
        /**
         * @since 1.2.8
         */
        do_action( 'woocommerce_after_add_to_cart_quantity' ); ?>

        <?php
        $add_to_cart_button = '<button type="submit" class="single_add_to_cart_button button alt">' . $product->single_add_to_cart_text() . '</button>';
        echo apply_filters( 'yith_wcpb_single_product_add_to_cart_button', $add_to_cart_button );
        ?>

        <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
    </form>

    <?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>