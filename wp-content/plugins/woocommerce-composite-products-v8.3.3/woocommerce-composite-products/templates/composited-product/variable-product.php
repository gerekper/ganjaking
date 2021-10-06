<?php
/**
 * Composited Variable Product template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/composited-product/variable-product.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><div class="details component_data"><?php

	/**
	 * 'woocommerce_composited_product_details' hook.
	 *
	 * @since 3.2.0
	 *
	 * @hooked wc_cp_composited_product_excerpt - 10
	 */
	do_action( 'woocommerce_composited_product_details', $product, $component_id, $composite_product );

	?><table class="variations" cellspacing="0">
		<tbody><?php

			foreach ( $attributes as $attribute_name => $options ) {

				?><tr class="attribute_options" data-attribute_label="<?php echo esc_attr( wc_attribute_label( $attribute_name ) ); ?>">
					<td class="label">
						<label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo wc_attribute_label( $attribute_name ); ?> <abbr class="required" title="<?php _e( 'Required option', 'woocommerce-composite-products' ); ?>">*</abbr></label>
					</td>
					<td class="value"><?php

						echo wc_cp_composited_single_variation_attribute_options( array(
							'options'    => $options,
							'attributes' => $attributes,
							'attribute'  => $attribute_name,
							'product'    => $product,
							'component'  => $composite_product->get_component( $component_id )
						) );

					?></td>
				</tr><?php
			}

		?></tbody>
	</table><?php

	/**
	 * 'woocommerce_composited_product_add_to_cart' hook.
	 *
	 * Useful for outputting content normally hooked to 'woocommerce_before_add_to_cart_button'.
	 */
	do_action( 'woocommerce_composited_product_add_to_cart', $product, $component_id, $composite_product );

	?><div class="single_variation_wrap component_wrap"><?php

		/**
		 * 'woocommerce_composited_single_variation' hook.
		 *
		 * Used to output the cart button and placeholder for variation data.
		 *
		 * @since 3.4.0
		 *
		 * @hooked wc_cp_composited_single_variation          - 10
		 * @hooked wc_cp_composited_single_variation_template - 20
		 */
		do_action( 'woocommerce_composited_single_variation', $product, $component_id, $composite_product );

	?></div>
</div>
