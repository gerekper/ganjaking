<?php
/**
 * Composited Variation Product template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/composited-product/variation.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 3.14.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="woocommerce-variation-add-to-cart variations_button quantity_button">
	<input type="hidden" class="variation_id" name="wccp_variation_id[<?php echo $component_id; ?>]" value=""/><?php

	wc_get_template( 'composited-product/quantity.php', array(
		'quantity_min'      => $quantity_min,
		'quantity_max'      => $quantity_max,
		'component_id'      => $component_id,
		'product'           => $product,
		'composite_product' => $composite_product
	), '', WC_CP()->plugin_path() . '/templates/' );

?></div>
