<?php
/**
 * Product Bundles template hooks
 *
 * @package  WooCommerce Product Bundles
 * @since    4.11.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Single product template for Product Bundles. Form location: Default.
add_action( 'woocommerce_bundle_add_to_cart', 'wc_pb_template_add_to_cart' );

// Single product template for Product Bundles. Form location: After summary.
add_action( 'woocommerce_after_single_product_summary', 'wc_pb_template_add_to_cart_after_summary', -1000 );

// Single product add-to-cart buttons area template for Product Bundles.
add_action( 'woocommerce_bundles_add_to_cart_wrap', 'wc_pb_template_add_to_cart_wrap' );

// Single product add-to-cart button template for Product Bundles.
add_action( 'woocommerce_bundles_add_to_cart_button', 'wc_pb_template_add_to_cart_button' );

// Bundled item wrapper open.
add_action( 'woocommerce_bundled_item_details', 'wc_pb_template_bundled_item_details_wrapper_open', 0, 2 );

// Bundled item image.
add_action( 'woocommerce_bundled_item_details', 'wc_pb_template_bundled_item_thumbnail', 5, 2 );

// Bundled item details container open.
add_action( 'woocommerce_bundled_item_details', 'wc_pb_template_bundled_item_details_open', 10, 2 );

// Bundled item title.
add_action( 'woocommerce_bundled_item_details', 'wc_pb_template_bundled_item_title', 15, 2 );

// Bundled item description.
add_action( 'woocommerce_bundled_item_details', 'wc_pb_template_bundled_item_description', 20, 2 );

// Bundled product details template.
add_action( 'woocommerce_bundled_item_details', 'wc_pb_template_bundled_item_product_details', 25, 2 );

// Bundled item details container close.
add_action( 'woocommerce_bundled_item_details', 'wc_pb_template_bundled_item_details_close', 30, 2 );

// Bundled item qty template in tabular layout.
add_action( 'woocommerce_bundled_item_details', 'wc_pb_template_tabular_bundled_item_qty', 35, 2 );

// Bundled item wrapper close.
add_action( 'woocommerce_bundled_item_details', 'wc_pb_template_bundled_item_details_wrapper_close', 100, 2 );

// Bundled item qty.
add_action( 'woocommerce_after_bundled_item_cart_details', 'wc_pb_template_default_bundled_item_qty' );

// Bundled variation template.
add_action( 'woocommerce_bundled_single_variation', 'wc_pb_template_single_variation', 10, 2 );
add_action( 'woocommerce_bundled_single_variation', 'wc_pb_template_single_variation_template', 20, 2 );

// Open and close table.
add_action( 'woocommerce_before_bundled_items', 'wc_pb_template_before_bundled_items', 100 );
add_action( 'woocommerce_after_bundled_items', 'wc_pb_template_after_bundled_items', 0 );

// Bundled item attributes.
add_action( 'woocommerce_product_additional_information', 'wc_pb_template_bundled_item_attributes', 11 );
