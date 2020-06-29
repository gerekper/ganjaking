<?php
/**
 * Product table template (part of various emails)
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $email_heading string Email heading string
 * @var $email \WC_Email Email object
 * @var $email_content string Email content (HTML)
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo "======\n\n";

foreach( $items as $item ){
	$product = $item->get_product();

	if( ! $product ){
		continue;
	}

	echo $product->get_name() . " (" . $item->get_formatted_product_price() . ") [" . $product->get_permalink() . "]\n";
}

echo "\n======\n\n";