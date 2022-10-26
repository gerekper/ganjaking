<?php
/**
 * Flatsome Theme
 *
 * @package  WooCommerce Mix and Match Products/Theme Compatibility
 * @since    2.0.7
 * @version  2.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_Flatsome_Compatibility Class.
 */
class WC_MNM_Flatsome_Compatibility {

	/**
	 * Attach hooks and filters.
	 */
	public static function init() {

		// Filters the loop classes.
		add_filter( 'wc_mnm_loop_classes', array( __CLASS__, 'loop_classes' ), 10, 2 );

		// Filters the child items classes.
		add_filter( 'wc_mnm_child_item_classes', array( __CLASS__, 'child_item_classes' ), 10, 3 );

		// Flatsome has it's own flex layout.
		add_filter( 'wc_mnm_grid_has_flex_layout', '__return_false' );

		// Wrapping divs.
		add_action( 'wc_mnm_child_item_details', array( __CLASS__, 'entry_wrap_open' ), 1, 2 );
		add_action( 'wc_mnm_child_item_details', array( __CLASS__, 'image_wrap_close' ), 35, 2 );
		add_action( 'wc_mnm_child_item_details', array( __CLASS__, 'entry_wrap_close' ), 109, 2 );

		// Add inline style.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'inline_style' ), 20 );
	}


	/**
	 * Add theme-specific wrapper classes to Mix and Match grid wrapper.
	 *
	 * @param  array     $classes - All classes on the wrapper container.
	 * @param obj $product WC_Mix_And_Match of parent product
	 * @return array
	 */
	public static function loop_classes( $classes, $product ) {
		if ( 'grid' === $product->get_layout() ) {
			$columns = wc_get_loop_prop( 'columns' );
			$new_classes = explode( ' ', flatsome_product_row_classes( $columns ) );
			$classes = array_merge( $classes, $new_classes );
		}
		return $classes;
	}

	/**
	 * Add theme-specific wrapper classes to child items.
	 *
	 * @param  array     $classes - All classes on the wrapper container.
	 * @param  obj $child_item WC_MNM_Child_Item
	 * @param obj $product WC_Mix_And_Match of parent product
	 * @return array
	 */
	public static function child_item_classes( $classes, $child_item, $product ) {
		if ( 'grid' === $product->get_layout() ) {
			$new_classes = array( 'product-small', 'col' );
			$classes = array_merge( $classes, $new_classes );
		}
		return $classes;
	}

	/**
	 * Add theme-specific wrapper.
	 *
	 * @param obj WC_Product $child_product the child product
	 * @param obj WC_Mix_and_Match $container_product the parent container
	 */
	public static function entry_wrap_open( $child_product, $container_product ) {
		if ( 'grid' === $container_product->get_layout() ) {
			echo '<div class="col-inner"><div class="product-small box"><div class="box-image">';
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
			echo '</div></div></div><!-- .col-inner-->';
		}
	}

	/**
	 * Add theme-specific wrapper.
	 *
	 * @param obj WC_Product $child_product the child product
	 * @param obj WC_Mix_and_Match $container_product the parent container
	 */
	public static function image_wrap_close( $child_product, $container_product ) {
		if ( 'grid' === $container_product->get_layout() ) {
			echo '</div><!-- .col-inner--><div class="box-text box-text-products">';
		}
	}

	/**
	 * Add theme-specific styles.
	 */
	public static function inline_style() {
		$custom_css = "
		.mnm_form.layout_grid .mnm_item { margin-left: 0; margin-right: 0; }
		.mnm_form .mnm_item .box-text .quantity .button { margin-top: 0; }
		";

		wp_add_inline_style( 'wc-mnm-frontend', $custom_css );
	}

} // End class.
WC_MNM_Flatsome_Compatibility::init();
