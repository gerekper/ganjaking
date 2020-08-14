<?php
/**
 * Template of Best Sellers SLIDER
 *
 * @var array  $bestsellers
 * @var string $animate 'yes' or 'no', default 'yes'
 * @var int    $delay     number of milliseconds before starting animation
 * @var int    $category
 * @var string $range
 * @var string $title
 */
?>

<?php if ( !empty( $best_sellers ) ) : ?>

    <div class="yith-wcbsl-bestsellers-slider" data-animate="<?php echo $animate ?>" data-delay="<?php echo $delay ?>">
        <div class="yith-wcbsl-bestsellers-slider-title">
            <div class="yith-wcbsl-bestsellers-slider-title-centered"><?php echo $title ?></div>
        </div>

        <div class="yith-wcbsl-bestsellers-slider-wrapper">
            <div class="yith-wcbsl-bestsellers-slider-container">
                <?php
                $loop = 1;
                foreach ( $best_sellers as $best_seller ) {
                    $args = array( 'id' => absint( $best_seller->product_id ), 'loop' => $loop );
                    wc_get_template( 'slider/bestseller.php', $args, '', YITH_WCBSL_TEMPLATE_PATH . '/' );
                    $loop++;
                }
                ?>
            </div>
        </div>

        <span class="yith-wcbsl-bestseller-slider-left dashicons dashicons-arrow-left-alt2"></span>
        <span class="yith-wcbsl-bestseller-slider-right dashicons dashicons-arrow-right-alt2"></span>
    </div>

<?php endif; ?>