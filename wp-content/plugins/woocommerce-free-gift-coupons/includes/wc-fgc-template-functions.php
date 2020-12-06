<?php
/**
 * Template Functions
 *
 * Functions for the WooCommerce Free Gift Coupons templating system.
 *
 * @package  WooCommerce Free Gift Coupons/Functions
 * @since    3.1.1
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Display Product Image template in Variation edit in cart.
 *
 * @since 3.1.0
 */
function wc_fgc_template_display_product_image() {
	wc_get_template(
		'cart/product-image.php',
		array(
		),
		'',
		WC_Free_Gift_Coupons::plugin_path() . '/templates/'
	);
}


/**
 * Customize product display of ajax-loaded template.
 *
 * @since 3.1.0
 */
function wc_fgc_customize_product_template_in_cart() {
	add_filter( 'woocommerce_product_single_add_to_cart_text', 'wc_fgc_add_to_cart_text', 999 );
}

/**
 * Change add to cart text
 *
 * @since 3.1.0
 * @param  string $text the Add to cart button text.
 * @return  string
 */
function wc_fgc_add_to_cart_text( $text ) {
	return esc_html_x( 'Update gift', 'Replacement text for add to cart button in cart updating context.', 'wc_free_gift_coupons' );
}
