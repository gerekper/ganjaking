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
$product_permalink = $current_product->is_visible() ? $current_product->get_permalink() : '';

?>
<div class="tc-epo-element-product-container tm-hidden" data-product_variations="<?php echo esc_attr( $product_list_available_variations[ $product_id ] ); ?>" data-product_id="<?php echo esc_attr( $product_id ); ?>">
	<?php
	if ( $show_image ) {
		echo '<div class="tc-epo-element-product-container-left">';
		wc_get_template(
			'products/template-image.php',
			[
				'product_id' => $product_id,
			],
			THEMECOMPLETE_EPO_DISPLAY()->get_template_path(),
			THEMECOMPLETE_EPO_DISPLAY()->get_default_path()
		);
		echo '</div>';
	}
	?>
	<div class="<?php echo esc_attr( ( $show_image ) ? 'tc-epo-element-product-container-right' : 'tc-epo-element-product-container-full' ); ?>">
		<div class="tc-epo-element-product-container-cart" data-per_product_pricing="<?php echo esc_attr( $priced_individually ); ?>">
			<?php
			if ( $show_title ) {
				echo '<h4 class="product-title">';
				$product_name = apply_filters( 'wc_epo_associated_product_name', $current_product->get_name(), $current_product, $product_id );
				if ( ! $product_permalink ) {
					echo wp_kses_post( $product_name );
				} else {
					echo wp_kses_post( sprintf( '<a target="_blank" href="%s">%s</a>', esc_url( $product_permalink ), $product_name ) );
				}
				echo '</h4>';
			}
			if ( $show_price && $priced_individually ) {
				echo '<div class="product-price">';
				$discount_applied = isset( $discount_applied ) ? $discount_applied : false;
				$discounted_price = THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS()->get_associated_price_html( $current_product, $args['discount'], $args['discount_type'], $discount_applied );
				echo '<span class="associated-price">' . apply_filters( 'wc_epo_kses', wp_kses_post( $discounted_price ), $discounted_price, false ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
				echo '</div>';
			}
			if ( $show_description ) {
				if ( $current_product->is_type( 'variation' ) ) {
					$description = $current_product->get_description();
				} else {
					$description = $current_product->get_short_description();
				}
				echo '<div class="product-description">' . wpautop( themecomplete_do_shortcode( apply_filters( 'wc_epo_kses', wp_kses_post( $description ), $description, false ) ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
			}
			require THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-variable.php';
			do_action( 'wc_epo_associated_product_display', $current_product, $tm_element_settings['uniqid'], isset( $tm_element_settings['disable_epo'] ) ? $tm_element_settings['disable_epo'] : '', $priced_individually, $args['discount'], $args['discount_type'], $args['discount_exclude_addons'], $option['counter'], $tm_element_settings['uniqid'] );
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
