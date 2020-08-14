<?php
/**
 * Template of Best Seller
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers
 * @version 1.0.0
 */

$id                 = YITH_WCBSL_WPML_Integration()->get_current_language_id( $id );
$bestseller_product = wc_get_product( $id );

$last_qty = 0;
if ( isset( $last_best_sellers[ $id ] ) ) {
    $last_qty = absint( $last_best_sellers[ $id ] );
}

$show_indicator = get_option( 'yith-wcbsl-show-bestseller-indicator', 'yes' ) == 'yes';

if ( $bestseller_product ) : ?>
    <div class="yith-wcbsl-bestseller-wrapper">
        <a href="<?php echo get_permalink( $id ); ?>">
            <div class="yith-wcbsl-bestseller-container">
                <span class="yith-wcbsl-bestseller-position"><?php echo $loop ?></span>

                <?php
                if ( $show_indicator ) {
                    $indicator_class   = 'equal';
                    $indicator_content = '=';
                    if ( $qty > $last_qty ) {
                        $indicator_class   = 'up dashicons dashicons-arrow-up-alt2';
                        $indicator_content = '';
                    } elseif ( $qty < $last_qty ) {
                        $indicator_class   = 'down dashicons dashicons-arrow-down-alt2';
                        $indicator_content = '';
                    }
                    echo "<span class='yith-wcbsl-bestseller-indicator yith-wcbsl-bestseller-indicator-{$indicator_class}'>{$indicator_content}</span>";
                }
                ?>


                <?php if ( current_user_can( 'manage_options' ) ): ?>
                    <span class="yith-wcbsl-bestseller-quantity">
                        <strong><?php echo number_format_i18n( $qty ) ?></strong><br/>
                        <?php echo _n( 'sale', 'sales', $qty, 'yith-woocommerce-best-sellers' ) ?>
                    </span>
                <?php endif; ?>

                <div class="yith-wcbsl-bestseller-thumb-wrapper">
                    <?php
                    $thumb_id   = get_post_thumbnail_id( $id );
                    $image_html = '';
                    if ( $thumb_id ) {
                        $image_title  = esc_attr( get_the_title( $thumb_id ) );
                        $image        = wp_get_attachment_url( $thumb_id );
                        $resized_link = wp_get_attachment_image_src( $thumb_id, 'shop_catalog' );
                        $image        = !empty( $resized_link ) ? $resized_link[ 0 ] : $image;

                        $image_html = "<img src='{$image}' title='{$image_title}' alt='{$image_title}' />";

                    }

                    echo apply_filters( 'yith_wcbsl_bestseller_thumbnail_html', $image_html, $thumb_id );
                    ?>
                </div>
                <div class="yith-wcbsl-bestseller-content-wrapper">
                    <h3><?php echo $bestseller_product->get_title(); ?></h3>

                    <span class="price"> <?php echo $bestseller_product->get_price_html(); ?></span>
                </div>
            </div>
        </a>
    </div>
<?php endif; ?>

