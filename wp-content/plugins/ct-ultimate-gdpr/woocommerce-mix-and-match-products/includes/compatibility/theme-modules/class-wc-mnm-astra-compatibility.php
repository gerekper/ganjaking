<?php
/**
 * Astra Theme
 *
 * @package  WooCommerce Mix and Match Products/Theme Compatibility
 * @since    2.0.0
 * @version  2.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_Astra_Compatibility Class.
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

		// Left align the quantity inputs.
		add_filter( 'wc_mnm_center_align_quantity', '__return_false' );

		// Inline styles.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'inline_styles' ), 20 );

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
	 */
	public static function inline_styles() {

		$custom_css = "
			.theme-astra .mnm_form .child-item .ast-stock-avail {
				display: none;
			}
			.theme-astra .mnm_form .mnm-checkbox-qty.buttons_added .minus,
			.theme-astra .mnm_form .mnm-checkbox-qty.buttons_added .plus {
				display: none;
			}
		";

		wp_add_inline_style( 'wc-mnm-frontend', $custom_css );

	}


} // End class.
WC_MNM_Astra_Compatibility::init();
