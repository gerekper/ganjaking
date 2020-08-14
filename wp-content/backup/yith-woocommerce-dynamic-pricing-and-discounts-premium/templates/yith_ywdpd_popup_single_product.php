<?php

/**
 * Popup gift single product.
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.0.0
 * @version 1.6.0
 * @author  YITH
 *
 * @var integer $rule_id
 * @var integer $product_id
 */

if ( ! defined( 'ABSPATH' ) && ! isset( $product_id ) ) {
	exit;
}

$single_product = wc_get_product( $product_id );

?>
<div class="single-product">
	<span class="ywdpd_back"></span>
	<h3><?php esc_html_e( 'Select a variation', 'ywdpd' ); ?></h3>
	<div id="product-<?php echo esc_attr( $product_id ); ?>" class="ywdpdp_single_product product">
		<div class="ywdpd_single_product_left">
			<?php
			echo $single_product->get_image( 'shop_single' ); //phpcs:ignore
			echo $single_product->get_formatted_name(); //phpcs:ignore
			?>
			<span class="price">
			   <del><?php echo wp_kses_post( wc_price( $single_product->get_price() ) ); ?></del><ins><?php esc_html_e( 'Free!', 'ywdpd' ); ?></ins>
			</span>
		</div>
		<div class="ywdpd_single_product_right">
			<?php
			if ( 'variable' === $single_product->get_type() ) {

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
				<div class="ywdpd_button_add_to_gift">
					<button
						class="ywdpd_add_to_gift button single_add_to_cart_button"><?php esc_html_e( 'Add Gift', 'ywdpd' ); ?></button>
					<input type="hidden" class="ywdpd_rule_id" value="<?php echo esc_attr( $rule_id ); ?>">
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>
