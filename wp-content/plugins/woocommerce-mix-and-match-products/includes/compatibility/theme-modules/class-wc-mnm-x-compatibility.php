<?php
/**
 * X Theme
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
 * WC_MNM_X_Compatibility Class.
 */
class WC_MNM_X_Compatibility {

	/**
	 * Attach hooks and filters.
	 */
	public static function init() {
		// Wrapping div.
		add_action( 'wc_mnm_child_item_details', array( __CLASS__, 'entry_wrap_open' ), 39, 2 );
		add_action( 'wc_mnm_child_item_details', array( __CLASS__, 'entry_wrap_close' ), 101, 2 );

		// Left align the quantity inputs.
		add_filter( 'wc_mnm_center_align_quantity', '__return_false' );

		// Add inline style.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'inline_style' ), 20 );
	}

	/**
	 * Add theme-specific wrapper.
	 *
	 * @param obj WC_Product $child_product the child product
	 * @param obj WC_Mix_and_Match $container_product the parent container
	 */
	public static function entry_wrap_open( $child_product, $container_product ) {
		if ( 'grid' === $container_product->get_layout() ) {
			echo '<div class="entry-wrap">';
		}
	}


	/**
	 * Add theme-specific wrapper.
	 *
	 * @param obj WC_Product $child_product the child product
	 * @param obj WC_Mix_and_Match $container_product the parent container
	 */
	public static function entry_wrap_close( $child_product, $container_product ) {
		if ( 'grid' === $container_product->get_layout() ) {
			echo '</div><!-- .entry-wrap-->';
		}
	}


	/**
	 * Add theme-specific styles.
	 */
	public static function inline_style() {

		$custom_css = "
			.mnm_form.layout_grid ul.products li.product .entry-wrap { box-shadow: none; }
		";

		wp_add_inline_style( 'wc-mnm-frontend', $custom_css );
	}

} // End class.
WC_MNM_X_Compatibility::init();
