<?php
/**
 * Template for variation support opt-in.
 *
 * @package  woocommerce-gpf
 */

?>
<h3><?php esc_html_e( 'Other settings', 'woocommerce_gpf' ); ?></h3>
<p>
	<input type="checkbox" class="woocommerce_gpf_field_selector" name="woocommerce_gpf_config[include_variations]" id="woocommerce_gpf_config[include_variations]" {include_variations_selected}>
	<label for="woocommerce_gpf_config[include_variations]"><?php
	echo __( 'Include variations in your feed.', 'woocommerce_gpf' ); // WPCS: XSS OK.
	?></label>
</p>
