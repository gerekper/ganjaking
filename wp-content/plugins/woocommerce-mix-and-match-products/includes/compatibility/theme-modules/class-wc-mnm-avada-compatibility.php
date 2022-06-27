<?php
/**
 * Avada Theme
 *
 * @package  WooCommerce Mix and Match Products/Theme Compatibility
 * @since    2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_Avada_Compatibility Class.
 *
 * @version  2.0.0
 */
class WC_MNM_Avada_Compatibility {

	/**
	 * Attach hooks and filters.
	 */
	public static function init() {
		// Filters the loop classes.
		add_filter( 'wc_mnm_loop_classes', array( __CLASS__, 'loop_classes' ), 10, 2 );

		// Wrapping div.
		add_action( 'wc_mnm_child_item_details', array( __CLASS__, 'entry_wrap_open' ), 1, 2 );
		add_action( 'wc_mnm_child_item_details', array( __CLASS__, 'entry_wrap_close' ), 109, 2 );

		add_action( 'wc_mnm_child_item_details', array( __CLASS__, 'entry_details_open' ), 39, 2 );
		add_action( 'wc_mnm_child_item_details', array( __CLASS__, 'entry_details_close' ), 101, 2 );

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
			echo '<div class="fusion-product-wrapper">';
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
			echo '</div><!-- .fusion-product-wrapper-->';
		}
	}


	/**
	 * Add theme-specific wrapper.
	 *
	 * @param obj WC_Product $child_product the child product
	 * @param obj WC_Mix_and_Match $container_product the parent container
	 */
	public static function entry_details_open( $child_product, $container_product ) {
		if ( 'grid' === $container_product->get_layout() ) {
			echo '<div class="fusion-product-content">';
		}
	}


	/**
	 * Add theme-specific wrapper.
	 *
	 * @param obj WC_Product $child_product the child product
	 * @param obj WC_Mix_and_Match $container_product the parent container
	 */
	public static function entry_details_close( $child_product, $container_product ) {
		if ( 'grid' === $container_product->get_layout() ) {
			echo '</div><!-- .fusion-product-content-->';
		}
	}


	/**
	 * Add theme-specific wrapper classes to loop.
	 *
	 * @param  array     $classes - All classes on the wrapper container.
	 * @param obj $product WC_Mix_And_Match of parent product
	 * @return array
	 */
	public static function loop_classes( $classes, $product ) {
		$columns = wc_get_loop_prop( 'columns' );
		return array_merge(
            $classes,
            array( 'products-'.$columns )
		);
	}


	/**
	 * Add theme-specific styles.
	 */
	public static function inline_style() {
		$dir = is_rtl() ? 'right' : 'left';
		$custom_css = "
			.mnm_form.layout_grid ul.products li.product .product-quantity { overflow: hidden; }
			.mnm_form.layout_grid ul.products li.product .product-quantity .quantity { float: " . $dir . "; }
		";

		wp_add_inline_style( 'wc-mnm-frontend', $custom_css );
	}

} // End class.
WC_MNM_Avada_Compatibility::init();
