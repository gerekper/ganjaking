<?php
/**
 * Add to Cart Input Template - Grouped Products
 *
 * @package WooCommerce-One-Page-Checkout/Templates
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

ob_start();

woocommerce_grouped_add_to_cart();

echo str_replace( array( '<form','</form>', 'method="post"', "enctype='multipart/form-data'" ), array( '<div', '</div>', '', '' ), ob_get_clean() );
