<?php
/**
 * Composited Simple Product template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/composited-product/simple-product.php'.
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

	?><div class="component_wrap"><?php

		/**
		 * 'woocommerce_composited_product_add_to_cart' hook.
		 *
		 * @hooked wc_cp_composited_product_price - 8
		 */
		do_action( 'woocommerce_composited_product_add_to_cart', $product, $component_id, $composite_product );

		// Availability html.
		echo $component_option->get_availability_html();

		?><div class="quantity_button"><?php

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

