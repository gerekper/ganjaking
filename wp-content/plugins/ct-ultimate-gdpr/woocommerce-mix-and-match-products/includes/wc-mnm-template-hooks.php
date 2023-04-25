<?php
/**
 * Template Hooks
 *
 * Action/filter hooks used for WooCommerce Mix and Match Products functions/templates.
 *
 * @package  WooCommerce Mix and Match Products/Templates
 * @since    1.0.0
 * @version  2.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Single product template for Mix and Match products. Form location: Default.
add_action( 'woocommerce_mix-and-match_add_to_cart', 'wc_mnm_template_add_to_cart' );

// Single product template for Mix and Match. Form location: After summary.
add_action( 'woocommerce_after_single_product_summary', 'wc_mnm_template_add_to_cart_after_summary', -1000 );

// Single product add-to-cart buttons area template for Mix and Match.
add_action( 'wc_mnm_content_loop', 'wc_mnm_content_loop' );
add_action( 'wc_mnm_content_loop', 'wc_mnm_template_reset_link', 20 );
add_action( 'wc_mnm_content_loop', 'wc_mnm_template_container_status', 30 );
add_action( 'wc_mnm_content_loop', 'wc_mnm_template_add_to_cart_button', 40 );

// First category caption.
add_action( 'wc_mnm_before_child_items', 'wc_mnm_first_category_caption', -10 );

// Open and close table.
add_action( 'wc_mnm_before_child_items', 'wc_mnm_template_child_items_wrapper_open', 0 );
add_action( 'wc_mnm_after_child_items', 'wc_mnm_template_child_items_wrapper_close', 100 );

// MNM single child item.
add_action( 'wc_mnm_child_item_details', 'wc_mnm_category_caption', -10, 2 );
add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_details_wrapper_open', 0, 2 );

// Display category title.
add_action( 'wc_mnm_category_caption', 'wc_mnm_category_title', 10, 2 );
add_action( 'wc_mnm_category_caption', 'wc_mnm_category_description', 20, 2 );

// Display thumbnails.
if ( wc_string_to_bool( get_option( 'wc_mnm_display_thumbnail', 'yes' ) ) ) {
	add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_thumbnail_open', 10, 2 );
	add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_thumbnail', 20, 2 );
	add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_thumbnail_close', 30, 2 );
}

add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_details_open', 40, 2 );
add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_title', 50, 2 );
add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_data_details', 55, 2 );
add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_attributes', 60, 2 );

// Maybe display short description.
if ( wc_string_to_bool( get_option( 'wc_mnm_display_short_description', 'no' ) ) ) {
	add_action( 'wc_mnm_child_item_details', 'wc_mnm_child_item_short_description', 63, 2 );
}

add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_price', 65, 2 );
add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_stock_remaining', 67, 2 );

add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_section_close', 70, 2 );
add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_quantity_open', 80, 2 );
add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_quantity', 90, 2 );
add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_section_close', 100, 2 );
add_action( 'wc_mnm_child_item_details', 'wc_mnm_template_child_item_details_wrapper_close', 110, 2 );

// Plus/minus buttons.
if ( wc_string_to_bool( get_option( 'wc_mnm_display_plus_minus_buttons', 'no' ) ) ) {
	add_action( 'wc_mnm_before_child_items', 'wc_mnm_add_plus_minus_buttons' );
	add_action( 'wc_mnm_after_child_items', 'wc_mnm_remove_plus_minus_buttons' );
}

// Backcompatibility Functions.
add_action( 'woocommerce_mix-and-match_add_to_cart', '_wc_mnm_add_template_backcompatibility', -10 );

/*-----------------------------------------------------------------------------------*/
/*  Edit template hooks.                                                                  */
/*-----------------------------------------------------------------------------------*/

// Edit container form - stripped down add to cart form.
add_action( 'wc_mnm_edit_container_order_item', 'wc_mnm_template_edit_container_order_item', 10, 4 );

// Port add to cart elements.
add_action( 'wc_mnm_edit_container_order_item_content', 'wc_mnm_content_loop', 10 );
add_action( 'wc_mnm_edit_container_order_item_content', 'wc_mnm_template_reset_link', 20 );
add_action( 'wc_mnm_edit_container_order_item_content', 'wc_mnm_template_container_status', 30 );
