<?php
/**
 * Template Hooks
 *
 * Action/filter hooks used for WooCommerce Mix and Match Products functions/templates.
 *
 * @package  WooCommerce Mix and Match Products/Templates
 * @since    1.0.0
 * @version  2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Single product template for Mix and Match products. Form location: Default.
add_action( 'woocommerce_mix-and-match_add_to_cart', 'wc_mnm_template_add_to_cart' );

// Single product template for Mix and Match. Form location: After summary.
add_action( 'woocommerce_after_single_product_summary', 'wc_mnm_template_add_to_cart_after_summary', -1000 );

// The contents loop.
add_action( 'wc_mnm_content_loop', 'wc_mnm_content_loop' );

// First category caption.
add_action( 'wc_mnm_before_child_items', 'wc_mnm_first_category_caption', -10 );

// Open and close table.
add_action( 'wc_mnm_before_child_items', 'wc_mnm_template_child_items_wrapper_open', 0 );
add_action( 'wc_mnm_after_child_items', 'wc_mnm_template_child_items_wrapper_close', 100 );

// MNM single child item.
add_action( 'wc_mnm_child_item_details', 'wc_mnm_category_caption', -10, 2 );
add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_details_wrapper_open', 0, 2 );

// Check whether or not to display thumbnails.
if ( 'yes' === get_option( 'wc_mnm_display_thumbnail', 'yes' ) ) {

	add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_thumbnail_open', 10, 2 );
	add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_thumbnail', 20, 2 );
	add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_section_close', 30, 2 );
} else {
	add_filter(
        'wc_mnm_tabular_column_headers',
        function( $headers ) {
		unset( $headers[ 'thumbnail' ] );
		return $headers;
        } 
    );
}

add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_details_open', 40, 2 );
add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_title', 50, 2 );
add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_data_details', 55, 2 );
add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_attributes', 60, 2 );

// Check whether or not to display short description.
if ( 'yes' === get_option( 'wc_mnm_display_short_description', 'no' ) ) {
	add_action( 'wc_mnm_child_item_details', 'wc_mnm_child_item_short_description', 63, 2 );
}

add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_price', 65, 2 );

add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_section_close', 70, 2 );
add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_quantity_open', 80, 2 );
add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_quantity', 90, 2 );
add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_section_close', 100, 2 );
add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_details_wrapper_close', 110, 2 );

// Reset Link.
add_action( 'wc_mnm_add_to_cart_wrap', 'wc_mnm_template_reset_link', 10 );

// Single product add-to-cart buttons area template for Mix and Match.
add_action( 'wc_mnm_add_to_cart_wrap', 'wc_mnm_template_add_to_cart_wrap', 20 );
add_action( 'wc_mnm_add_to_cart_button', 'wc_mnm_template_add_to_cart_button' );

// Backcompatibility Functions.
add_action( 'woocommerce_mix-and-match_add_to_cart', '_wc_mnm_add_template_backcompatibility', -10 );
