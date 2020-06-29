<?php
/**
 * Dialog Variation Form template
 *
 * @author  YITH
 * @package YITH WooCommerce Frequently Bought Together Premium
 * @version 1.5.0
 */

if ( ! defined( 'YITH_WFBT' ) ) {
	exit;
} // Exit if accessed directly

$title 			= get_option( 'yith-wfbt-popup-title', __( 'Choose the product', 'yith-woocommerce-frequently-bought-together' ) );
$button_label 	= get_option( 'yith-wfbt-popup-submit-button-label', __( 'Add product', 'yith-woocommerce-frequently-bought-together' ) );

?>
<h2 class="popup-title"><?php echo esc_html( $title ); ?> </h2>
<div class="yith-wfbt-wrap-single-product product">
	<div class="yith-wfbt-single-product-left">
		<figure class="product-image">
			<?php echo $single_product->get_image( 'shop_single' ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
		</figure>
		<p class="product-name">
			<?php echo $single_product->get_formatted_name(); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
		</p>
	</div>
	<div class="yith-wfbt-single-product-right">
		<?php
		if ( 'variable' == $single_product->get_type() ) {

			global $product;

			$product = $single_product;

			wc_get_template( 'single-product/add-to-cart/variation.php' );

			// Get Available variations?
			$get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );

			// Load the template.
			wc_get_template(
				'single-product/add-to-cart/variable.php',
				array(
					'available_variations' => $get_variations ? $product->get_available_variations() : false,
					'attributes'           => $product->get_variation_attributes(),
					'selected_attributes'  => $product->get_default_attributes(),
				)
			);
			?>
			<div class="yith-wfbt-stock-status"></div>
			<input type="hidden" name="yith-wfbt-variation-id" value="">
			<input type="hidden" name="yith-wfbt-main-product-id" value="<?php echo esc_attr( $product_id ); ?>">
			<button id="yith-wfbt-submit-variation" disabled="disabled"><?php echo esc_attr( $button_label ); ?></button>
			<?php

		}
		?>
	</div>
</div>
