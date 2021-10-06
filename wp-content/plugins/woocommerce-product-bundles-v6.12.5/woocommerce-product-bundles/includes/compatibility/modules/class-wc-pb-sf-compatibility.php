<?php
/**
 * WC_PB_SF_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
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
}

WC_PB_SF_Compatibility::init();
