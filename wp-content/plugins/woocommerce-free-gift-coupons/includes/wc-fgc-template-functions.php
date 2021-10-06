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
		array(),
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
	add_action( 'woocommerce_after_add_to_cart_button', 'wc_fgc_cancel_edit_link', 0 );
	add_filter( 'woocommerce_product_single_add_to_cart_text', 'wc_fgc_add_to_cart_text', 999 );
}

/**
 * Display "Cancel edit" link.
 *
 * @since 3.1.0
 */
function wc_fgc_cancel_edit_link() {
	global $product;
	// translators: %1$s Screen reader text opening <span> %2$s Product title %3$s Closing </span>
	$cancel_text = sprintf( esc_html_x( 'Cancel edit %1$soptions for %2$s%3$s', 'edit in cart cancel link text', 'wc_free_gift_coupons' ),
		'<span class="screen-reader-text">',
		$product->get_title(),
		'</span>'
	);
	echo '<a href="" class="wc-fgc-close-link">' . $cancel_text . '</a>';
}

/**
 * Change add to cart text.
 *
 * @param  string $text the Add to cart button text.
 * @return  string
 * @since 3.1.0
 */
function wc_fgc_add_to_cart_text( $text ) {
	return _x( 'Update gift', 'Replacement text for add to cart button in cart updating context.', 'wc_free_gift_coupons' );
}

/**
 * Remove Link from Image thumbnail.
 *
 * @param string $html The image html data
 * @param int $post_thumbnail_id The thumbnail id.
 * @return string
 * @since 3.1.0
 */
function wc_fgc_remove_product_image_link( $html, $post_thumbnail_id ) {
	return preg_replace( '!<(a|/a).*?>!', '', $html );
}
