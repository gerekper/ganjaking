<?php
/**
 * Astra Theme
 *
 * @package  WooCommerce Mix and Match Products/Theme Compatibility
 * @since    2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_Astra_Compatibility Class.
 *
 * @version  2.0.6
 */
class WC_MNM_Astra_Compatibility {

	/**
	 * Attach hooks and filters.
	 */
	public static function init() {

		// Filters the body classes.
		add_filter( 'body_class', array( __CLASS__, 'body_classes' ) );

		// Disabled MNM display: flex, Astra uses display: grid.
		add_filter( 'wc_mnm_grid_has_flex_layout', '__return_false' );

	}


	/**
	 * Add theme-specific classes to body.
	 *
	 * @param  array     $classes - All classes on the body.
	 * @return array
	 */
	public static function body_classes( $classes ) {

		if ( is_product() ) {
			global $post;

			if ( has_term( 'mix-and-match', 'product_type', $post ) ) {
				$shop_grid = astra_get_option( 'shop-grids' );
				$classes[] = 'tablet-columns-' . $shop_grid['tablet'];
				$classes[] = 'mobile-columns-' . $shop_grid['mobile'];
			}

		}

		return $classes;
	}


	/**
	 * Add theme-specific style rules to header.
	 *
	 * @deprecated 2.0.6
	 */
	public static function inline_styles() {

		wc_deprecated_function( 'WC_MNM_Astra_Compatibility::inline_styles()', '1.6.0', 'Function is no longer used.' );

		$custom_css = "
			.theme-astra .mnm_form.layout_grid ul.products {
				display: grid;
			}
		";

		wp_add_inline_style( 'wc-mnm-frontend', $custom_css );

	}


} // End class.
WC_MNM_Astra_Compatibility::init();
