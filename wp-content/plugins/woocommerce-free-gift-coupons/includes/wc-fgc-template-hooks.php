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

// Product title.
add_action( 'wc_fgc_single_product_summary', 'woocommerce_template_single_title', 5 );

// Single add to cart.
add_action( 'wc_fgc_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

// Add filters only to the ajax loaded template.
add_action( 'wc_fgc_before_single_cart_product', 'wc_fgc_customize_product_template_in_cart' );
remove_action( 'wc_fgc_after_single_cart_product', 'wc_fgc_customize_product_template_in_cart' );
