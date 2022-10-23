<?php
/**
 * Template Hooks
 *
 * Action/filter hooks used for WooCommerce Free Gift Coupons functions/templates.
 *
 * @package  WooCommerce Free Gift Coupons/Templates
 * @since    3.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Product image in variation edit cart.
add_action( 'wc_fgc_before_single_product_summary', 'wc_fgc_template_display_product_image' );

// Single add to cart.
add_action( 'wc_fgc_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

// Remove link from product image thumbnail.
add_filter( 'wc_fgc_single_product_image_thumbnail_html', 'wc_fgc_remove_product_image_link', 10, 2 );

// Add filters only to the ajax loaded template.
add_action( 'wc_fgc_before_single_cart_product', 'wc_fgc_customize_product_template_in_cart' );
remove_action( 'wc_fgc_after_single_cart_product', 'wc_fgc_customize_product_template_in_cart' );
