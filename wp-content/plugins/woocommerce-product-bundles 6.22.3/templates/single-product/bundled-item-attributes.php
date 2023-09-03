<?php
/**
 * Bundled Product Attributes template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/bundled-item-attributes.php'.
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
<h4 class="bundled_product_attributes_title"><?php echo wp_kses_post( $title ); ?></h4>
<?php

wc_get_template( 'single-product/product-attributes.php', array(
	'product'            => $product,
	'attributes'         => $attributes,
	'display_dimensions' => $display_dimensions,
	'product_attributes' => $product_attributes
) );
