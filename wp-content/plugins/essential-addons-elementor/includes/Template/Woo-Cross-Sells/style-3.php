<?php
/**
 * Template Name: Style 3
 *
 * @var $cs_product
 * @var $image_size
 * @var $is_purchasable
 * @var $parent_id
 * @var $settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="eael-cs-single-product">
    <div class="eael-cs-product-image">
		<?php echo $cs_product->get_image( $image_size ); ?>
    </div>
    <div class="eael-cs-product-info">
		<?php if ( $settings['eael_cross_sales_visibility_title'] === 'yes' ) { ?>
            <div class="eael-cs-product-title">
                <a href="<?php echo esc_url( $cs_product->get_permalink() ); ?>"><?php echo esc_html( $cs_product->get_title() ); ?></a>
            </div>
		<?php }
		if ( $settings['eael_cross_sales_visibility_price'] === 'yes' ) { ?>
            <div class="eael-cs-product-price">
				<?php echo $cs_product->get_price_html(); ?>
            </div>
		<?php }
		if ( $settings['eael_cross_sales_visibility_description'] === 'yes' ) { ?>
            <div class="eael-cs-product-excerpt">
				<?php
				if ( $parent_id > 0 ) {
					$product_instance          = wc_get_product( $parent_id );
					$product_short_description = $product_instance->get_short_description();
				} else {
					$product_short_description = $cs_product->get_short_description();
				}

				echo wp_trim_words( strip_shortcodes( $product_short_description ), $settings['eael_cross_sales_excerpt_length'],
					$settings['eael_cross_sales_excerpt_expanison_indicator'] );
				?>
            </div>
		<?php }
		if ( $settings['eael_cross_sales_visibility_buttons'] === 'yes' ) { ?>
            <div class="eael-cs-product-buttons <?php echo $is_purchasable ? 'eael-cs-purchasable' : ''; ?>">
				<?php if ( $is_purchasable ) { ?>
                    <a href="<?php echo esc_url( $cs_product->add_to_cart_url() ); ?>" class="add_to_cart_button ajax_add_to_cart"
                       data-product_id="<?php echo esc_html( $cs_product->get_ID() ); ?>" data-quantity="1"><i
                                class="fas fa-shopping-cart"></i><?php echo esc_html( $settings['eael_woo_cross_sales_add_to_cart_label'] ); ?></a>
				<?php } else { ?>
                    <a href="<?php echo esc_url( $cs_product->get_permalink() ); ?>"><i
                                class="fas fa-eye"></i><?php echo esc_html( $settings['eael_woo_cross_sales_view_product_label'] ); ?></a>
				<?php } ?>
            </div>
		<?php } ?>
    </div>
</div>
