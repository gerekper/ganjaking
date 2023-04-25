<?php
/**
 * Woodmart Theme
 *
 * @package  WooCommerce Mix and Match Products/Theme Compatibility
 * @since    2.0.3
 * @version  2.4.3
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_Woodmart_Compatibility Class.
 */
class WC_MNM_Woodmart_Compatibility {

	/**
	 * Attach hooks and filters.
	 */
	public static function init() {

		// Filters the loop classes.
		add_filter( 'wc_mnm_loop_classes', array( __CLASS__, 'loop_classes' ), 10, 2 );

		// Filters the child items classes.
		add_filter( 'wc_mnm_child_item_classes', array( __CLASS__, 'child_item_classes' ), 10, 2 );

	}


	/**
	 * Add theme-specific wrapper classes to Mix and Match grid wrapper.
	 *
	 * @param  array     $classes - All classes on the wrapper container.
	 * @param obj $product WC_Mix_And_Match of parent product
	 * @return array
	 */
	public static function loop_classes( $classes, $product ) {

		$columns = wc_get_loop_prop( 'columns' );

		$new_classes = array( 'elements-grid', 'wd-products-holder', 'wd-spacing-20', 'wd-quantity-enabled', 'title-line-two', 'align-items-start', 'row' );
		$new_classes[] = 'grid-columns-' . $columns;

		return array_merge( $classes, $new_classes );

	}

	/**
	 * Add theme-specific wrapper classes to child items.
	 *
	 * @param  array     $classes - All classes on the wrapper container.
	 * @param obj $product WC_Mix_And_Match of parent product
	 * @return array
	 */
	public static function child_item_classes( $classes, $product ) {
		$new_classes = array( 'product-grid-item', 'wd-with-labels', 'col-6' );

		$columns = (int) apply_filters( 'wc_mnm_grid_layout_columns', get_option( 'wc_mnm_number_columns', 3 ), $product );

		if ( $columns ) {
			$bootstrap_columns = floor ( 12 / $columns );
			$new_classes[] = 'col-lg-' . $bootstrap_columns;
		}

		return array_merge( $classes, $new_classes );
		
	}

} // End class.
WC_MNM_Woodmart_Compatibility::init();
