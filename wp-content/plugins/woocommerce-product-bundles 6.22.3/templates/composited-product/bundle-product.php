<?php
/**
 * Composited Product Bundle template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/composited-product/bundle-product.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version  6.21.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><div class="details component_data <?php echo esc_attr( $classes ); ?>"><?php

	/**
	 * 'woocommerce_composited_product_details' hook.
	 *
	 * Composited product details template.
	 *
	 * @param WC_Product_Bundle    $product
	 * @param mixed                $component_id
	 * @param WC_Product_Composite $composite_product
	 *
	 * @hooked wc_cp_composited_product_excerpt - 10
	 */
	do_action( 'woocommerce_composited_product_details', $product, $component_id, $composite_product );

	/**
	 * 'woocommerce_before_composited_bundled_items' hook.
	 *
	 * @param WC_Product_Bundle    $product
	 * @param mixed                $component_id
	 * @param WC_Product_Composite $composite_product
	 */
	do_action( 'woocommerce_before_composited_bundled_items', $product, $component_id, $composite_product );

	/**
	 * 'woocommerce_before_bundled_items' action.
	 *
	 * @param WC_Product_Bundle $product
	 */
	do_action( 'woocommerce_before_bundled_items', $product );

	foreach ( $bundled_items as $bundled_item ) {

		/**
		 * 'woocommerce_bundled_item_details' hook.
		 *
		 * @param WC_Bundled_Item   $bundled_item
		 * @param WC_Product_Bundle $bundle
		 *
		 * @hooked wc_pb_template_bundled_item_thumbnail       -   5
		 * @hooked wc_pb_template_bundled_item_details_open    -  10
		 * @hooked wc_pb_template_bundled_item_title           -  15
		 * @hooked wc_pb_template_bundled_item_description     -  20
		 * @hooked wc_pb_template_bundled_item_product_details -  25
		 * @hooked wc_pb_template_bundled_item_details_close   - 100
		 */
		do_action( 'woocommerce_bundled_item_details', $bundled_item, $product );
	}

	/**
	 * 'woocommerce_before_bundled_items' action.
	 *
	 * @param WC_Product_Bundle $product
	 */
	do_action( 'woocommerce_after_bundled_items', $product );

	/**
	 * 'woocommerce_after_composited_bundled_items' hook.
	 *
	 * @param WC_Product_Bundle    $product
	 * @param mixed                $component_id
	 * @param WC_Product_Composite $composite_product
	 */
	do_action( 'woocommerce_after_composited_bundled_items', $product, $component_id, $composite_product );

	?><div class="cart bundle_data bundle_data_<?php echo esc_attr( $product->get_id() ); ?>" data-bundle_form_data="<?php echo wc_esc_json( json_encode( $bundle_form_data ) ); ?>" data-bundle_id="<?php echo esc_attr( $product->get_id() ); ?>"><?php

		do_action( 'woocommerce_composited_product_add_to_cart', $product, $component_id, $composite_product );

		?><div class="bundle_wrap component_wrap">
			<div class="bundle_price"></div>
			<div class="bundle_availability"><?php

				// Availability html.
				echo $composited_product->get_availability_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			?></div>
			<div class="bundle_button"><?php

				wc_get_template( 'composited-product/quantity.php', array(
					'quantity_min'      => $quantity_min,
					'quantity_max'      => $quantity_max,
					'component_id'      => $component_id,
					'product'           => $product,
					'composite_product' => $composite_product
				), '', WC_CP()->plugin_path() . '/templates/' );

			?></div>
		</div>
	</div>
</div>
