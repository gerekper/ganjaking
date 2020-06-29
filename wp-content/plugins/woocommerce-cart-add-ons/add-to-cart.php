<?php
/**
 * Loop Add to Cart
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     1.6.4
 */

global $product, $woocommerce;

if ( ! $product->is_purchasable() && ! $product->is_type( array( 'external', 'grouped' ) ) ) return;
?>

<?php if ( ! $product->is_in_stock() ) : ?>

    <a href="<?php echo apply_filters( 'out_of_stock_add_to_cart_url', get_permalink( $product->get_id() ) ); ?>" class="button"><?php echo apply_filters( 'out_of_stock_add_to_cart_text', __( 'Read More', 'sfn_cart_addons' ) ); ?></a>

<?php else : ?>

    <?php

        switch ( $product->get_type() ) {
            case "variation" :
                $url    = add_query_arg( array('add-to-cart' => $product->get_id(), 'variation_id' => $product->get_id()), get_permalink( $product->get_id() ) );
                $variation_data = $product->get_variation_attributes();

                // load attributes
                foreach ( $variation_data as $key => $value ) {
                    $url = add_query_arg( array($key => $value), $url );
                }

                $link   = apply_filters( 'variation_add_to_cart_url', $url );
                $label  = apply_filters( 'variation_add_to_cart_text', __('Add to Cart', 'sfn_cart_addons') );
            break;
        }

        printf('<a href="%s" rel="nofollow" data-product_id="%s" class="add_to_cart_button button product_type_%s">%s</a>', $link, $product->get_id(), $product->get_type(), $label);

    ?>

<?php endif; ?>
