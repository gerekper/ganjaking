<?php
/**
 * Template of Best Seller for SLDIER
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers
 * @version 1.0.0
 */


$bestseller_product = wc_get_product( $id );

if ( $bestseller_product ) : ?>
    <div class="yith-wcbsl-bestseller-in-slider-wrapper">
        <a href="<?php echo get_permalink( $id ); ?>">
            <div class="yith-wcbsl-bestseller-in-slider-container">
                <span class="yith-wcbsl-bestseller-in-slider-position"><?php echo $loop ?></span>

                <div class="yith-wcbsl-bestseller-in-slider-thumb-wrapper">
                    <?php
                    $thumb_id = get_post_thumbnail_id( $id );
                    if ( $thumb_id ) {
                        $image_title   = esc_attr( get_the_title( $thumb_id ) );
                        $image_caption = get_post( $thumb_id )->post_excerpt;
                        $image_link    = wp_get_attachment_url( $thumb_id );
                        $image         = get_the_post_thumbnail( $id, 'shop_catalog' );
                        $resized_link  = wp_get_attachment_image_src( $thumb_id, 'shop_catalog' );

                        $image_link = !empty( $resized_link ) ? $resized_link[ 0 ] : $image_link;

                        echo "<img src='{$image_link}' title='{$image_title}' alt='{$image_title}' />";
                    }
                    ?>
                </div>
                <div class="yith-wcbsl-bestseller-in-slider-content-wrapper">
                    <table class="yith-wcbsl-bestseller-in-slider-content-wrapper-table"><tr><td valign="center"><h3><?php echo $bestseller_product->get_title(); ?></h3></td></tr></table>
                </div>
            </div>
        </a>
    </div>
<?php endif; ?>
