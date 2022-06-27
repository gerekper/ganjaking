<?php
/**
 * WC_PB_SF_Compatibility class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.7.9
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Storefront 2.3+ integration.
 *
 * @version  5.7.9
 */
class WC_PB_SF_Compatibility {

	public static function init() {
		// Add hooks if the active parent theme is Storefront.
		add_action( 'after_setup_theme', array( __CLASS__, 'maybe_add_hooks' ) );
	}

	/**
	 * Add hooks if the active parent theme is Storefront.
	 */
	public static function maybe_add_hooks() {
		if ( class_exists( 'Storefront_WooCommerce' ) ) {
			// Fix sticky add to cart button behavior when "Form Location" is "After Summary".
			add_filter( 'storefront_sticky_add_to_cart_params', array( __CLASS__, 'sticky_add_to_cart_params' ) );

			// Prevent use of 'parent_cart_item_meta' flag when using the new block-based cart.
			if ( WC_PB()->compatibility->is_module_loaded( 'blocks' ) ) {
				add_filter( 'woocommerce_bundles_group_mode_options_data', array( __CLASS__, 'bundles_group_mode_options_data' ), 11 );
			}
		}
	}

	/**
	 * Set corrent sticky add to cart button trigger element when "Form Location" is "After Summary".
	 *
	 * @param  array  $params
	 * @return array
	 */
	public static function sticky_add_to_cart_params( $params ) {

		global $product;

		if ( wc_pb_is_product_bundle() && 'after_summary' === $product->get_add_to_cart_form_location() ) {
			$params[ 'trigger_class' ] = 'summary-add-to-cart-form-bundle';
		}

		return $params;
	}

		/**
		 * Prevent use of 'parent_cart_item_meta' flag when using the new block-based cart.
		 *
		 * @since  6.15.0
		 *
		 * @param  array  $group_mode_data
		 * @return array
		 */
		public static function bundles_group_mode_options_data( $group_mode_data ) {
			$group_mode_data[ 'parent' ][ 'features' ] = array_diff( $group_mode_data[ 'parent' ][ 'features' ], array( 'parent_cart_item_meta' ) );
			return $group_mode_data;
		}
}

WC_PB_SF_Compatibility::init();
