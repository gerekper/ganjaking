<?php global $product; ?>
<li>
    <div class="yith-wcbm-widget-bundle-container">

        <?php do_action('yith_wcpb_widget_before_product_title',$product) ?>

        <a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" title="<?php echo esc_attr( $product->get_title() ); ?>">
            <span class="product-title"><?php echo $product->get_title(); ?></span>
        </a>
        <?php if ( ! empty( $show_rating ) ) echo $product->get_rating_html(); ?>
        <div class="yith-wcbm-widget-bundle-price"><?php echo $product->get_price_html(); ?></div>
    </div>

    <?php if ( $show_bundled ){
        $bundled_items = $product->get_bundled_items();
        if ($bundled_items){
            ?>
            <ul class="yith-wcbm-widget-bundled-items">
                <?php
                foreach ($bundled_items as $item) {
                    if ($item->is_hidden())
                        continue;
                    $item_prod = $item->product;
                    $product_link = $item_prod->is_visible() ? get_permalink( $item_prod->get_id() ) : '#';
                    ?>
                    <li>
                        <a href="<?php echo $product_link ?>" title="<?php echo esc_attr( $item_prod->get_title() ); ?>">

                            <?php
                            if ( !$item->hide_thumbnail && $show_bundled_thumb ){
                                echo $item_prod->get_image();
                            }
                            ?>
                            <span class="product-title"><?php echo $item_prod->get_title(); ?></span>
                    </li>
                    <?php
                }
                ?>
            </ul>
            <?php
        }
    } ?>
</li>