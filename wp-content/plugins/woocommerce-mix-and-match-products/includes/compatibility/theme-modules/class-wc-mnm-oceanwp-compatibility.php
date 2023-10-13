<?php
/**
 * OceanWP Theme
 *
 * @package  WooCommerce Mix and Match Products/Theme Compatibility
 * @since    2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_OceanWP_Compatibility Class.
 *
 * @version  2.0.8
 */
class WC_MNM_OceanWP_Compatibility {

	/**
	 * Attach hooks and filters.
	 */
	public static function init() {
		// Filters the loop classes.
		add_filter( 'wc_mnm_loop_classes', array( __CLASS__, 'loop_classes' ), 10, 2 );

		// Add inline style.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'inline_style' ), 20 );
	}

	/**
	 * Add theme-specific wrapper classes to loop.
	 *
	 * @param  array     $classes - All classes on the wrapper container.
	 * @param obj $product WC_Mix_And_Match of parent product
	 * @return array
	 */
	public static function loop_classes( $classes, $product ) {

		if ( 'grid' === $product->get_layout() ) {

			// Classes.
			$classes = array_merge( $classes, array( 'oceanwp-row', 'clr', 'grid' ) );

			// List/grid style.
			if ( ( oceanwp_is_woo_shop() || oceanwp_is_woo_tax() )
				&& get_theme_mod( 'ocean_woo_grid_list', true )
				&& 'list' === get_theme_mod( 'ocean_woo_catalog_view', 'grid' ) ) {
				$classes[] = 'list';
			} else {
				$classes[] = 'grid';
			}

			// Responsive columns.
			$tablet_columns = get_theme_mod( 'ocean_woocommerce_tablet_shop_columns' );
			$mobile_columns = get_theme_mod( 'ocean_woocommerce_mobile_shop_columns' );

			if ( ! empty( $tablet_columns ) ) {
				$classes[] = 'tablet-col';
				$classes[] = 'tablet-' . $tablet_columns . '-col';
			}
			if ( ! empty( $mobile_columns ) ) {
				$classes[] = 'mobile-col';
				$classes[] = 'mobile-' . $mobile_columns . '-col';
			}
		}

		return $classes;
	}


	/**
	 * Add theme-specific styles.
	 */
	public static function inline_style() {

		$custom_css = '
			.mnm_form.layout_grid ul.products li.product { text-align: center; }
			.mnm_form.layout_grid ul.products li.product .product-quantity { margin-left: auto; margin-right: auto; }
		';

		wp_add_inline_style( 'wc-mnm-frontend', $custom_css );
	}
} // End class.
WC_MNM_OceanWP_Compatibility::init();
