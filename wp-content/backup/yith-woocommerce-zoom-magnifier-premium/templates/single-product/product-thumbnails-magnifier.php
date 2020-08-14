<?php
/**
 * Single Product Image
 *
 * @author        YIThemes
 * @package       YITH_Magnifier/Templates
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

global $post, $product, $woocommerce;

$enable_slider = get_option('yith_wcmg_enableslider') == 'yes' ? true : false;
$post_thumbnail_id = apply_filters( 'yith_wcmg_get_post_thumbnail_id', get_post_thumbnail_id(), $post->ID );

$attachment_ids = version_compare(WC()->version, '2.7.0', '<') ? $product->get_gallery_attachment_ids() : $product->get_gallery_image_ids();
if (!empty($attachment_ids)) array_unshift($attachment_ids, $post_thumbnail_id );

//  make sure attachments ids are unique
$attachment_ids = array_unique($attachment_ids);
if ($attachment_ids) {

    $columns = apply_filters('woocommerce_product_thumbnails_columns', get_option('yith_wcmg_slider_items', 3));
    if ( ! isset( $columns ) || $columns == null ) $columns = 3;

    $li_width = floor( 90 / $columns );
    $li_width = 90 / $columns;

    $li_margins_l_r = 10 / ( $columns * 2 );

    $li_style = apply_filters( 'woocommerce_single_product_image_thumbnail_html_cloumns_style', 'width: ' . $li_width . '%; margin-left: ' . $li_margins_l_r . '%; margin-right: ' . $li_margins_l_r . '%;' , $columns );

    ?>

    <div class="thumbnails <?php echo $enable_slider ? 'slider' : 'noslider' ?>">
        <ul class="yith_magnifier_gallery" data-columns="<?php echo $columns; ?>" data-circular="<?php echo get_option ( 'yith_wcmg_slider_circular' ); ?>" data-slider_infinite="<?php echo get_option ( 'yith_wcmg_slider_infinite' ); ?>" data-auto_carousel="<?php echo get_option ( 'ywzm_auto_carousel' ); ?>">
            <?php
            $loop = 1;

            foreach ( $attachment_ids as $attachment_id ) {

                $classes = array( 'yith_magnifier_thumbnail' );

                if ( $loop == 1 ) {
                    $classes[] = 'first';

                }

                if ( $loop == $columns ) {
                    $classes[] = 'last';
                }

                if ( $loop > $columns )
                    $li_style = apply_filters( 'woocommerce_single_product_image_thumbnail_html_cloumns_style', 'display: none; width: ' . $li_width . '%; margin-left:' . $li_margins_l_r . '%; margin-right: ' . $li_margins_l_r . '%;' , $columns );

                $image = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
                $image_class = esc_attr(implode(' ', $classes ) );
                $image_title = apply_filters( 'ywcmg_get_image_title', esc_attr( get_the_title( $attachment_id ) ), $attachment_id );

                list( $thumbnail_url, $thumbnail_width, $thumbnail_height ) = wp_get_attachment_image_src( $attachment_id, apply_filters( 'yith_zoom_magnifier_thumbnail_size', "shop_single" ) );
                list( $magnifier_url, $magnifier_width, $magnifier_height ) = wp_get_attachment_image_src( $attachment_id, "full" );

                echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf('<li class="%s" style="%s"><a href="%s" class="%s" title="%s" data-small="%s">%s</a></li>', $image_class, $li_style, $magnifier_url, $image_class, $image_title, $thumbnail_url, $image), $attachment_id, $post->ID, $image_class, $columns );

                $loop++;
            }
            ?>
        </ul>

        <?php if ( $enable_slider && ( count( $attachment_ids ) > $columns ) ) : ?>
            <div id="slider-prev"></div>
            <div id="slider-next"></div>
        <?php endif; ?>
        <input id="yith_wc_zm_carousel_controler" type="hidden" value="1">
    </div>

    <?php
}
?>