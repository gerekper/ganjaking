<?php
/**
 * Astra Theme
 *
 * @package  WooCommerce Mix and Match Products/Theme Compatibility
 * @since    2.0.0
 * @version  2.4.5
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

		// Quickview support.
		add_action( 'woocommerce_shop_loop', array( __CLASS__, 'init_quick_view' ) );
		add_action( 'astra_woo_quick_view_product_summary', array( __CLASS__, 'attach_hooks' ), 0 );

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

	/**
	 * Add theme-specific style rules to header.
	 * 
	 * @since 2.4.5
	 */
	public static function init_quick_view() {

		$qv_enable = astra_get_option( 'shop-quick-view-enable' );

		if ( 'disabled' !== $qv_enable ) {
			wp_enqueue_script( 'wc-add-to-cart-mnm' );
			wp_enqueue_style( 'wc-mnm-frontend' );

			// Initialize MNM script when modal is loaded.
			wp_add_inline_script(
				'wc-add-to-cart-mnm',
				"jQuery( document ).on(
						'ast_quick_view_loader_stop',
						function () {
							jQuery('.mnm_form').each(
								function () {
									jQuery(this).wc_mnm_form();
								}
							);
				});"
			);
		}
	}

	/**
	 * Add filter on the form location prop
	 * 
	 * @since 2.4.5
	 */
	public static function attach_hooks() {
		add_filter( 'woocommerce_product_get_add_to_cart_form_location', array( __CLASS__, 'filter_form_location' ) );
	}

	/**
	 * Set form location prop to default in QV
	 * 
	 * @since 2.4.5
	 *
	 * @param string $location
	 * @return string
	 */
	public static function filter_form_location( $location ) {
		return 'default';
	}


} // End class.
WC_MNM_Astra_Compatibility::init();
