<?php
/**
 * The template for displaying the product element item alt
 *
 * @author  themeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 5.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="tc-epo-element-product-container tm-hidden" data-product_variations="<?php echo esc_attr( $product_list_available_variations[ $product_id ] ); ?>" data-product_id="<?php echo esc_attr( $product_id ); ?>">
    <?php if ($show_image) : ?>
    <div class="tc-epo-element-product-container-left">
    <?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-image.php'); ?>        
    </div>
    <?php endif; ?>
    <div class="<?php if ($show_image) {echo "tc-epo-element-product-container-right";} else {echo "tc-epo-element-product-container-full";} ?>">
        <div class="tc-epo-element-product-container-cart" data-per_product_pricing="<?php echo $priced_individually; ?>">
            <?php if ($show_title) : ?>
            <h4 class="product-title"><?php echo esc_html( $current_product->get_title() ); ?></h4>
            <?php endif; ?>
            <?php if ($show_price && $priced_individually) : ?>
            <div class="product-price">
                <span class="price"><?php echo apply_filters( 'wc_epo_kses', wp_kses_post( $current_product->get_price_html() ), $current_product->get_price_html(), FALSE ); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($show_description) : ?>
            <div class="product-description"><?php echo wpautop( do_shortcode( apply_filters( 'wc_epo_kses', wp_kses_post( $current_product->get_short_description() ), $current_product->get_short_description(), FALSE ) ) ); ?></div>
            <?php endif; ?>
    		<?php 
    		include( THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-variable.php' );
    		do_action( 'wc_epo_associated_product_display', $current_product, $tm_element_settings['uniqid'], $priced_individually, $args['discount'], $args['discount_type'] );
    		include( THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-quantity.php' );
    		?>
            <?php if ($show_meta) : ?>
            <div class="product-meta"><?php
            if ( $current_product->get_sku() || $current_product->is_type( 'variable' ) ) : ?>
                <span class="tc-product-sku-wrapper"><?php esc_html_e( 'SKU:', 'woocommerce-tm-extra-product-options' ); ?> <span class="tc-product-sku"><?php echo ( $sku = $current_product->get_sku() ) ? esc_html( $sku ) : esc_html__( 'N/A', 'woocommerce-tm-extra-product-options' ); ?></span></span>
            <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>