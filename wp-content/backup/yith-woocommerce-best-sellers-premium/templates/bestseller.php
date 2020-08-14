<?php
/**
 * Template of Best Seller
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers
 * @version 1.0.0
 */


$bestseller_product = wc_get_product( $id );

if ( $bestseller_product ) : ?>
    <div class="yith-wcbsl-bestseller-wrapper">
        <a href="<?php echo get_permalink( $id ); ?>">
            <div class="yith-wcbsl-bestseller-container">
                <span class="yith-wcbsl-bestseller-position"><?php echo $loop ?></span>
                <?php
                $thumb_id = get_post_thumbnail_id( $id );
                if ( $thumb_id ) :
                    ?>

                    <div class="yith-wcbsl-bestseller-thumb-wrapper">
                        <?php $image_title = esc_attr( get_the_title( $thumb_id ) );
                        $image_caption     = get_post( $thumb_id )->post_excerpt;
                        $image_link        = wp_get_attachment_url( $thumb_id );
                        $image             = get_the_post_thumbnail( $id, 'shop_catalog' );
                        $resized_link      = wp_get_attachment_image_src( $thumb_id, 'shop_catalog' );

                        $image_link = !empty( $resized_link ) ? $resized_link[ 0 ] : $image_link;

                        echo "<img src='{$image_link}' title='{$image_title}' alt='{$image_title}' />";
                        ?>
                    </div>
                <?php endif; ?>
                <div class="yith-wcbsl-bestseller-content-wrapper">
                    <h3><?php echo $bestseller_product->get_title(); ?></h3>

                    <span class="price"> <?php echo $bestseller_product->get_price_html(); ?></span>

                </div>
            </div>
        </a>
    </div>
<?php endif; ?>

