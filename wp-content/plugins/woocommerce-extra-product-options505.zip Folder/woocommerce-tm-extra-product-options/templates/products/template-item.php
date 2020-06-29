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
    <div class="tc-epo-element-product-container-left">
    <?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-image.php'); ?>        
    </div>
    <div class="tc-epo-element-product-container-right">
        <h4 class="product_title"><?php echo esc_html( $current_product->get_title() ); ?></h4>
        <span class="price"><?php echo apply_filters( 'wc_epo_kses', wp_kses_post( $current_product->get_price_html() ), $current_product->get_price_html(), FALSE ); ?></span>
        <div class="product_description"><?php echo wpautop( do_shortcode( apply_filters( 'wc_epo_kses', wp_kses_post( $current_product->get_short_description() ), $current_product->get_short_description(), FALSE ) ) ); ?></div>
		<?php
		include( THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-variable.php' );
		do_action( 'wc_epo_associated_product_display', $product_id, $tm_element_settings['uniqid'] );
		include( THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-quantity.php' );
		?>
        <div class="product-meta"><?php
        if ( $current_product->get_sku() || $current_product->is_type( 'variable' ) ) : ?>
            <span class="tc-product-sku-wrapper"><?php esc_html_e( 'SKU:', 'woocommerce-tm-extra-product-options' ); ?> <span class="tc-product-sku"><?php echo ( $sku = $current_product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce-tm-extra-product-options' ); ?></span></span>
        <?php endif; ?>
        </div>
    </div>
</div>