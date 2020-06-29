<?php
/**
 * YITH WooCommerce Recently Viewed Products
 */

if (!defined('YITH_WRVP')) {
    exit; // Exit if accessed directly
}

$loop = 0;
?>

<table id="ywrvp-products-list">
    <tbody>

    <?php while ( $products->have_posts() ) : $products->the_post();

        $product_id = get_the_ID();
        $product    = wc_get_product( $product_id );
        if( ! $product ) {
            continue;
        }
        $product_link = ywrvp_campaign_build_link( $product->get_permalink() );
        if( defined('YITH_WCWL') && YITH_WCWL ) {
            $wishlist_link = ywrvp_campaign_build_link( YITH_WCWL()->get_wishlist_url() );
        }
        
        $loop++;        
        $class = ( $loop == $products->post_count ) ? 'last' : '';
        ?>

        <tr>
            <td class="ywrvp-product <?php echo esc_html( $class ); ?>">
                <table id="ywrvp-product-info">
                    <tbody>
                    <tr>
                        <td class="product-image">
                            <?php echo wp_kses_post( yith_wrvp_get_mail_product_image( $product, $product_link ) ); ?>
                        </td>
                        <td class="product-info">
                            <h3>
                                <a href="<?php echo esc_url( $product_link );?>">
                                    <?php the_title() ?>
                                </a>
                            </h3>
                            <div>
                                <?php wc_get_template('loop/price.php'); ?>
                            </div>
                        </td>

                        <td class="product-action">
                            <div>
                                <a href="<?php echo esc_url( $product_link ) ?>" class="mail-button"><?php esc_html_e('View Details', 'yith-woocommerce-recently-viewed-products' ) ?></a>
                            </div>
                            <?php if( defined('YITH_WCWL') && YITH_WCWL ) : ?>
                                <div>
                                    <a href="<?php echo esc_url( add_query_arg( 'add_to_wishlist', $product_id, $wishlist_link ) )?>" class="mail-button"><?php esc_html_e( 'Add to wishlist', 'yith-woocommerce-recently-viewed-products' ) ?></a>
                                </div>
                            <?php endif; ?>
                        </td>

                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>

    <?php endwhile; // end of the loop. ?>

    </tbody>
</table>