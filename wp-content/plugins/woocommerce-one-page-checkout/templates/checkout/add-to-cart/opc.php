<?php
/**
 * Add to Cart Input Template - Displays the appropriate cart input or stock/availability notice for the OPC templates
 *
 * @package WooCommerce-One-Page-Checkout/Templates
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( $product->is_in_stock() ) {

	do_action( 'wcopc_before_add_to_cart_button', $product );

	if ( $product->is_sold_individually() ) {
		wc_get_template( 'checkout/add-to-cart/button.php', array( 'product' => $product ), '', PP_One_Page_Checkout::$template_path );
	} else {
		wc_get_template( 'checkout/add-to-cart/quantity-input.php', array( 'product' => $product ), '', PP_One_Page_Checkout::$template_path );
	}

	do_action( 'wcopc_after_add_to_cart_button', $product );

} else {
	
	wc_get_template( 'checkout/add-to-cart/availability.php', array( 'product' => $product ), '', PP_One_Page_Checkout::$template_path );

} ?>

