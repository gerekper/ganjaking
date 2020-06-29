<?php
/**
 * Component Options template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/component-options.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @since    1.0.0
 * @version  3.14.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><div id="component_options_<?php echo $component_id; ?>" class="component_options" data-options_data="<?php echo htmlspecialchars( json_encode( $component_options_data ) ); ?>">
	<div class="component_options_inner cp_clearfix"><?php

		/**
		 * Action 'woocommerce_composite_component_options_{$options_style}'.
		 *
		 * @since  3.6.0
		 *
		 * @param  string                $component_id
		 * @param  WC_Product_Composite  $product
		 *
		 * @hooked wc_cp_component_options_dropdowns  - 10
		 * @hooked wc_cp_component_options_thumbnails - 10
		 * @hooked wc_cp_component_options_radios     - 10
		 */
		do_action( 'woocommerce_composite_component_options_' . $options_style, $component_id, $product );

	?></div>
</div>
