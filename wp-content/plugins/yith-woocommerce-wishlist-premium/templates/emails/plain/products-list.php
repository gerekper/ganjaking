<?php
/**
 * Product table template (part of various emails)
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Templates\Emails
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $email_heading string Email heading string
 * @var $email \WC_Email Email object
 * @var $email_content string Email content (HTML)
 * @var $items YITH_WCWL_Wishlist_Item[] Array of wishlist items
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

echo "======\n\n";

foreach ( $items as $item ) {
	$product = $item->get_product();

	if ( ! $product ) {
		continue;
	}

	echo esc_html( wp_strip_all_tags( sprintf( '%1$s (%2$s) [%3$s]', $product->get_name(), wc_price( $item->get_product_price() ), $product->get_permalink() ) ) ) . "\n";
}

echo "\n======\n\n";
