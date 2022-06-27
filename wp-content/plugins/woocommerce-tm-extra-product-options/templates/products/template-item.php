<?php
/**
 * The template for displaying the product element item alt
 *
 * @author  ThemeComplete
 * @package WooCommerce Extra Product Options/Templates/Products
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'wc_epo_before_product_item_container', $current_product );

?>
<div class="tc-epo-element-product-container tm-hidden" data-product_variations="<?php echo esc_attr( $product_list_available_variations[ $product_id ] ); ?>" data-product_id="<?php echo esc_attr( $product_id ); ?>">
	<?php
	if ( $show_image ) {
		echo '<div class="tc-epo-element-product-container-left">';
		require THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-image.php';
		echo '</div>';
	}
	?>
	<div class="<?php echo esc_attr( ( $show_image ) ? 'tc-epo-element-product-container-right' : 'tc-epo-element-product-container-full' ); ?>">
		<div class="tc-epo-element-product-container-cart" data-per_product_pricing="<?php echo esc_attr( $priced_individually ); ?>">
			<?php
			if ( $show_title ) {
				echo '<h4 class="product-title">';
				echo esc_html( $current_product->get_name() );
				echo '</h4>';
			}
			if ( $show_price && $priced_individually ) {
				echo '<div class="product-price">';
				echo '<span class="price">' . apply_filters( 'wc_epo_kses', wp_kses_post( $current_product->get_price_html() ), $current_product->get_price_html(), false ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
				echo '</div>';
			}
			if ( $show_description ) {
				if ( $current_product->is_type( 'variation' ) ) {
					$description = $current_product->get_description();
				} else {
					$description = $current_product->get_short_description();
				}
				echo '<div class="product-description">' . wpautop( do_shortcode( apply_filters( 'wc_epo_kses', wp_kses_post( $description ), $description, false ) ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
			}
			require THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-variable.php';
			do_action( 'wc_epo_associated_product_display', $current_product, $tm_element_settings['uniqid'] . '.' . $option['counter'], $priced_individually, $args['discount'], $args['discount_type'], $option['counter'], $tm_element_settings['uniqid'] );
			require THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-availability.php';
			require THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-quantity.php';

			if ( $show_meta ) {
				echo '<div class="product-meta">';
				if ( $current_product->get_sku() || $current_product->is_type( 'variable' ) ) {
					$sku = $current_product->get_sku();
					if ( ! $sku ) {
						$sku = esc_html__( 'N/A', 'woocommerce-tm-extra-product-options' );
					}
					echo '<span class="tc-product-sku-wrapper">' . esc_html__( 'SKU:', 'woocommerce-tm-extra-product-options' ) . ' <span class="tc-product-sku">' . esc_html( $sku ) . '</span></span>';
				}
				echo '</div>';
			}
			?>
		</div>
	</div>
</div>
<?php

do_action( 'wc_epo_after_product_item_container', $current_product );
