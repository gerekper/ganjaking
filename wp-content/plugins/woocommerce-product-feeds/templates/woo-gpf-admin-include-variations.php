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
        ?></label><br>
	<?php echo __( '<strong>Note:</strong> We strongly recommend leaving this enabled. If you feel you may need to disable this, please reach out to our <a href="https://woo.com/my-account/contact-support/" rel="noopener noreferrer">support team</a> to discuss your options first.', 'woocommere_gpf' ); // WPCS: XSS OK ?>
</p>
