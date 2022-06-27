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
 * @version  2.0.0
 */
class WC_MNM_OceanWP_Compatibility {

	/**
	 * Attach hooks and filters.
	 */
	public static function init() {
		// Filters the loop classes.
		add_filter( 'wc_mnm_loop_classes', array( __CLASS__, 'loop_classes' ) );

		// Add inline style.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'inline_style' ), 20 );
	}

	/**
	 * Add theme-specific wrapper classes to loop.
	 *
	 * @param  array     $classes - All classes on the wrapper container.
	 * @return array
	 */
	public static function loop_classes( $classes ) {

		// Classes.
		$wrap_classes = array( 'products', 'oceanwp-row', 'clr' );

		// List/grid style.
		if ( ( oceanwp_is_woo_shop() || oceanwp_is_woo_tax() )
			&& get_theme_mod( 'ocean_woo_grid_list', true )
			&& 'list' === get_theme_mod( 'ocean_woo_catalog_view', 'grid' ) ) {
			$wrap_classes[] = 'list';
		} else {
			$wrap_classes[] = 'grid';
		}

		// Responsive columns.
		$tablet_columns = get_theme_mod( 'ocean_woocommerce_tablet_shop_columns' );
		$mobile_columns = get_theme_mod( 'ocean_woocommerce_mobile_shop_columns' );

		if ( ! empty( $tablet_columns ) ) {
			$wrap_classes[] = 'tablet-col';
			$wrap_classes[] = 'tablet-' . $tablet_columns . '-col';
		}
		if ( ! empty( $mobile_columns ) ) {
			$wrap_classes[] = 'mobile-col';
			$wrap_classes[] = 'mobile-' . $mobile_columns . '-col';
		}

		return array_merge(
            $classes,
            array( 
			'oceanwp-row',
			'clr',
			'grid',
			)
		);
	}


	/**
	 * Add theme-specific styles.
	 */
	public static function inline_style() {

		$custom_css = "
			.mnm_form.layout_grid ul.products li.product { text-align: center; }
			.mnm_form.layout_grid ul.products li.product .product-quantity { margin-left: auto; margin-right: auto; }
		";

		wp_add_inline_style( 'wc-mnm-frontend', $custom_css );
	}

} // End class.
WC_MNM_OceanWP_Compatibility::init();
