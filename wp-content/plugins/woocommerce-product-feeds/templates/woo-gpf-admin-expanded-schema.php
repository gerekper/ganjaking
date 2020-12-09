<?php
/**
 * Template for variation support opt-in.
 *
 * @package  woocommerce-gpf
 */

?>
<p>
	<input type="checkbox" class="woocommerce_gpf_field_selector" name="woocommerce_gpf_config[expanded_schema]" id="woocommerce_gpf_config[expanded_schema]" {expanded_schema_selected}>
	<label for="woocommerce_gpf_config[expanded_schema]"><?php
	echo __( '<strong>[BETA]</strong> Include expanded schema markup on product pages</strong>.', 'woocommerce_gpf' ); // WPCS: XSS OK.
	?></label>
    <?php echo __( '<br><strong>Note:</strong> Before enabling this option, please see <a href="https://docs.woocommerce.com/document/google-product-feed-expanded-structured-data/" target="_blank" rel="nofollow noopener">this article</a> about the status of the feature, and the potential consequences of using this pre-release feature.', 'woocommere_gpf' ); // WPCS: XSS OK ?>
</p>
