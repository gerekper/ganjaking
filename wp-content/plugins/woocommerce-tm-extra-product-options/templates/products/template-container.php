<?php
/**
 * The template for displaying the product element container
 *
 * Used by the Thumbnail, Radio and Dropdown layout mode
 *
 * @author  themeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 5.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="tc-epo-element-product-container-wrap"><?php

	foreach ( $product_list as $product_id => $attributes ) {
		$current_product = wc_get_product( $product_id );
		if ($args['discount']){
			$current_price = THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS()->get_discounted_price( $current_product->get_price(), $args['discount'], $args['discount_type']);
			$current_product->set_sale_price($current_price); 
			$current_product->set_price($current_price);  
		}

		include( THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-item.php' );
	}

	?></div>