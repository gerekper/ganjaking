<?php
/**
 * Add to Cart Input Template - Variable Product
 *
 * @package WooCommerce-One-Page-Checkout/Templates
 * @version 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

ob_start();

woocommerce_variable_add_to_cart();

echo str_replace( array( '<form','</form>', 'method="post"', "enctype='multipart/form-data'" ), array( '<div', '</div>', '', '' ), ob_get_clean() );
