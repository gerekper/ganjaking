<?php
/**
 * Storefront Theme
 *
 * @package  WooCommerce Mix and Match Products/Theme Compatibility
 * @since    2.0.6
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_Storefront_Compatibility Class.
 *
 * @version  2.0.6
 */
class WC_MNM_Storefront_Compatibility {

	/**
	 * Attach hooks and filters.
	 */
	public static function init() {

		// Add filters
		add_action( 'wc_quick_view_before_single_product', array( __CLASS__, 'add_filters' ) );

		// Remove filters.
		add_action( 'wc_quick_view_after_single_product', array( __CLASS__, 'remove_filters' ) );

	}

	/**
	 * Add theme-specific style rules to product post class.
	 */
	public static function add_filters() {
		add_filter( 'woocommerce_post_class', array( __CLASS__, 'post_class' ) );
	}

	/**
	 * Remove theme-specific style rules.
	 */
	public static function remove_filters() {
		remove_filter( 'woocommerce_post_class', array( __CLASS__, 'post_class' ) );
	}

	/**
	 * Add theme-specific classes to body.
	 *
	 * @param array      $classes Array of CSS classes.
	 * @return array
	 */
	public static function body_classes( $classes ) {
		$classes[] = 'site-main';
		return $classes;
	}


} // End class.
WC_MNM_Storefront_Compatibility::init();
