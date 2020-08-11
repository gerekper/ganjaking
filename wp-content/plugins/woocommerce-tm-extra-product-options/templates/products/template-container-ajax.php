<?php
/**
 * The template for displaying the product html via ajax
 *
 * @author  themeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 5.0
 */

defined( 'ABSPATH' ) || exit;

$attributes      = $product_list[ $product_id ];
$current_product = wc_get_product( $product_id );
if ($_POST['discount']){
	$current_price = THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS()->get_discounted_price( $current_product->get_price(), $_POST['discount'], $_POST['discount_type']);
	$current_product->set_sale_price($current_price); 
	$current_product->set_price($current_price);  
}

include( apply_filters( 'wc_epo_template_path_product_element', THEMECOMPLETE_EPO_TEMPLATE_PATH ) . apply_filters( 'wc_epo_template_element', 'products/' . $template . '.php', 'product', array() ) );
