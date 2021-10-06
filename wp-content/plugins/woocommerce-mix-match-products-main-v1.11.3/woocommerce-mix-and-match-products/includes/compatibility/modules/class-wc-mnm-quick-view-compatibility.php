<?php
/**
 * Quick View Compatibility
 *
 * Adds support for "After summary" add to cart form location.
 *
 * @package  WooCommerce Mix and Match Products/Compatibility
 * @since    2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_Quick_View_Compatibility Class.
 *
 * Adds support for "After summary" add to cart form location.
 */
class WC_MNM_Quick_View_Compatibility {

	public static function init() {
		add_action( 'wc_quick_view_after_single_product', 'wc_mnm_template_add_to_cart_after_summary' );

	} // END __construct().

} // END class.

WC_MNM_Quick_View_Compatibility::init();
