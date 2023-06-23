<?php
/**
 * Bundled Variation Product template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/bundled-variation.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 6.21.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="woocommerce-variation-add-to-cart variations_button bundled_item_after_cart_details bundled_item_button">
	<input type="hidden" class="variation_id" name="<?php echo esc_attr( $bundle_fields_prefix . 'bundle_variation_id_' . $bundled_item->get_id() ); ?>" value=""/><?php

	/**
	 * 'woocommerce_after_bundled_item_cart_details' hook.
	 *
	 * @since 5.0.0
	 *
	 * @param WC_Bundled_Item $bundled_item
	 *
	 * @hooked wc_pb_template_default_bundled_item_qty - 10
	 */
	do_action( 'woocommerce_after_bundled_item_cart_details', $bundled_item );

?></div><?php
