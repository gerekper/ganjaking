<?php
/**
 * Single Product Image
 *
 * @author        YIThemes
 * @package       YITH_Magnifier/Templates
 */

if ( ! defined ( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $woocommerce, $product, $is_IE;

$enable_slider = get_option ( 'yith_wcmg_enableslider' ) == 'yes' ? true : false;

$placeholder = function_exists ( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src () : woocommerce_placeholder_img_src ();

$slider_items = get_option ( 'yith_wcmg_slider_items', 3 );
if ( ! isset( $slider_items ) || ( $slider_items == null ) ) $slider_items = 3;
$extra_classes = apply_filters( 'yith_wcmg_single_product_image_extra_classes', array() );
if( is_array( $extra_classes ) ){
	$extra_classes = implode( " ", $extra_classes );
}
?>
<input type="hidden" id="yith_wczm_traffic_light" value="free">

<div class="images<?php if ( $is_IE ): ?> ie<?php endif ?>">

    <?php
    if ( has_post_thumbnail () ) {

        $image       = get_the_post_thumbnail ( $post->ID, apply_filters ( 'single_product_large_thumbnail_size', 'shop_single' ) );
        $image_title = esc_attr ( get_the_title ( get_post_thumbnail_id () ) );
        $image_link  = wp_get_attachment_url ( get_post_thumbnail_id () );
        list( $magnifier_url, $magnifier_width, $magnifier_height ) = wp_get_attachment_image_src ( get_post_thumbnail_id (), "full" );

        echo apply_filters ( 'woocommerce_single_product_image_html', sprintf ( '<div class="woocommerce-product-gallery__image %s"><a href="%s" itemprop="image" class="yith_magnifier_zoom woocommerce-main-image" title="%s">%s</a></div>', $extra_classes, $magnifier_url, $image_title, $image ), $post->ID );

    } else {

        echo apply_filters ( 'woocommerce_single_product_image_html', sprintf ( '<a href="%s" itemprop="image" class="yith_magnifier_zoom woocommerce-main-image %s"><img src="%s" alt="Placeholder" /></a>', $placeholder, $extra_classes,$placeholder ), $post->ID );
    }
    ?>

    <?php do_action ( 'woocommerce_product_thumbnails' ); ?>

</div>


<script type="text/javascript" charset="utf-8">

    var yith_magnifier_options = {
        enableSlider: <?php echo $enable_slider ? 'true' : 'false' ?>,

        <?php if ( $enable_slider ): ?>
        sliderOptions: {
            responsive: <?php echo get_option ( 'yith_wcmg_slider_responsive' ) == 'yes' ? 'true' : 'false' ?>,
            circular: <?php echo get_option ( 'yith_wcmg_slider_circular' ) == 'yes' ? 'true' : 'false' ?>,
            infinite: <?php echo get_option ( 'yith_wcmg_slider_infinite' ) == 'yes' ? 'true' : 'false' ?>,
            direction: 'left',
            debug: false,
            auto: <?php echo ('yes' == get_option('ywzm_auto_carousel', 'no')) ? 'true' : 'false' ?>,
            align: 'left',
            prev: {
                button: "#slider-prev",
                key: "left"
            },
            next: {
                button: "#slider-next",
                key: "right"
            },
            //width   : <?php echo yit_shop_single_w () + 18 ?>,
            scroll: {
                items: 1,
                pauseOnHover: true
            },
            items: {
                //width: <?php echo yit_shop_thumbnail_w () + 4 ?>,
                visible: <?php echo apply_filters ( 'woocommerce_product_thumbnails_columns', $slider_items ) ?>
            }
        },

        <?php endif ?>

        showTitle: false,
        zoomWidth: '<?php echo get_option ( 'yith_wcmg_zoom_width' ) ?>',
        zoomHeight: '<?php echo get_option ( 'yith_wcmg_zoom_height' ) ?>',
        position: '<?php echo get_option ( 'yith_wcmg_zoom_position' ) ?>',
        //tint: <?php //echo get_option('yith_wcmg_tint') == '' ? 'false' : "'".get_option('yith_wcmg_tint')."'" ?>,
        //tintOpacity: <?php //echo get_option('yith_wcmg_tint_opacity') ?>,
        lensOpacity: <?php echo get_option ( 'yith_wcmg_lens_opacity' ) ?>,
        softFocus: <?php echo get_option ( 'yith_wcmg_softfocus' ) == 'yes' ? 'true' : 'false' ?>,
        //smoothMove: <?php //echo get_option('yith_wcmg_smooth') ?>,
        adjustY: 0,
        disableRightClick: false,
        phoneBehavior: '<?php echo get_option ( 'yith_wcmg_zoom_mobile_position' ) ?>',
        loadingLabel: '<?php echo stripslashes ( get_option ( 'yith_wcmg_loading_label' ) ) ?>',
        zoom_wrap_additional_css: '<?php echo apply_filters ( 'yith_ywzm_zoom_wrap_additional_css', '', $post->ID ); ?>',
    };

</script>
