<?php
/**
 * YITH WooCommerce Recently Viewed Products
 *
 * @version 1.0.1
 */

if ( ! defined( 'YITH_WRVP' ) ) {
    exit; // Exit if accessed directly
}

global $woocommerce_loop;

if( $columns )
    $woocommerce_loop['columns'] = $columns;
else
    $columns = 4;

// set slider
$slider = apply_filters( 'yith_wrvp_force_slider_view',  $slider == 'yes' && $products->post_count > $columns );
?>

<div class="woocommerce yith-similar-products cols-<?php echo esc_attr( $columns ) ?> <?php echo esc_attr( $class ) ?>" data-slider="<?php echo esc_attr( $slider ) ? '1' : '0' ?>"
     data-autoplay="<?php echo $autoplay == 'yes' ? '1' : '0' ?>" data-numcolumns="<?php echo esc_attr( $columns ); ?>" data-autoplayspeed="<?php echo esc_attr( $autoplay_speed ) ?>">

      <h2><?php echo esc_html( $title ) ?></h2>

    <?php woocommerce_product_loop_start(); ?>

    <?php while ( $products->have_posts() ) : $products->the_post(); ?>

        <?php wc_get_template_part('content', 'product'); ?>

    <?php endwhile; // end of the loop. ?>

    <?php woocommerce_product_loop_end(); ?>

</div>