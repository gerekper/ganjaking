<?php
/**
 * Template of Best Seller in widget
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers
 * @version 1.0.0
 */


$bestseller_product = wc_get_product( $id );
$thumb_id           = get_post_thumbnail_id( $id );
$image_link         = '';
if ( $thumb_id ) {
    $image_title   = esc_attr( get_the_title( $thumb_id ) );
    $image_caption = get_post( $thumb_id )->post_excerpt;
    $image_link    = wp_get_attachment_url( $thumb_id );
    $image         = get_the_post_thumbnail( $id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
        'title' => $image_title,
        'alt'   => $image_title
    ) );
}
$width_title_css = '';
if ( empty( $image_link ) || !$show_thumb ) {
    $width_title_css = 'width:calc(100% - 40px)';
}

if ( $bestseller_product ) : ?>
    <li>
        <div class="yith-wcbsl-widget-position"><?php echo $loop ?></div>

        <?php if ( !empty( $image_link ) && $show_thumb ) : ?>
            <div class="yith-wcbsl-widget-image" style="background-image: url(<?php echo $image_link ?>)"></div>
        <?php endif; ?>

        <div class="yith-wcbsl-widget-title" style="<?php echo $width_title_css; ?>">
            <a href="<?php echo get_permalink( $id ); ?>">
                <?php echo $bestseller_product->get_title(); ?>
            </a>
        </div>

    </li>
<?php endif; ?>

