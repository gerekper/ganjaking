<?php
/*
*
* Every snippet can be used by placing it in your theme's functions.php file
*
*/


// Change "add to cart" to "select options" on archive pages; eg categories, shop..etc
function wwob_replace_woocommerce_loop_add_to_cart_link($button, $product, $args)
{

	$id = $product->get_id();
	$wwob_status = get_post_meta($id, 'wwob_enable_disable', 1);
	if ($wwob_status == 'enabled' && $product->is_type('simple')) {
		return sprintf(
			'<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
			esc_url($product->get_permalink()),
			esc_attr(isset($args['quantity']) ? $args['quantity'] : 1),
			esc_attr(isset($args['class']) ? $args['class'] : 'button'),
			isset($args['attributes']) ? wc_implode_html_attributes($args['attributes']) : '',
			esc_html(__('Select options', 'woocommerce'))
		);
	}
	return $button;
}
add_filter('woocommerce_loop_add_to_cart_link', 'wwob_replace_woocommerce_loop_add_to_cart_link', 10, 3);
