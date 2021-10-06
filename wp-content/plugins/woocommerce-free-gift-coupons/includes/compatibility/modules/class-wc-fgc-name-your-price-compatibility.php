<?php
/**
 * Name Your Price Compatibility
 *
 * @package  WooCommerce Free Gift Coupons/Compatibility
 * @since    3.1.0
 * @version  3.1.0
 */

// Exit if accessed directly.
 defined( 'ABSPATH' ) || exit;

/**
 * WC_FGC_Name_Your_Price_Compatibility Class.
 *
 * Adds compatibility with WooCommerce Name Your Price.
 */
class WC_FGC_Name_Your_Price_Compatibility {

	public static function init() {

		// Single Product page.
		add_action( 'woocommerce_before_single_product', array( __CLASS__, 'maybe_disable_nyp_inputs' ), 10 );

		// Cart Validation.

		// On ajax product update.
		add_action( 'wc_fgc_before_updating_product_in_cart', array( __CLASS__, 'disable_nyp_fields_validation' ), 10 );

		// On cart update/adding.
		add_filter( 'woocommerce_cart_updated', array( __CLASS__, 'maybe_disable_nyp_cart_validation' ), 10 );

		// Cart display.
		add_filter( 'wc_nyp_isset_disable_edit_it_cart', array( __CLASS__, 'maybe_disable_edit_link_in_cart' ), 10, 3 );

	}

	/**
	 * Disable NYP field validation.
	 *
	 * @param array $cart_item
	 * @since 3.1.0
	 */
	public static function disable_nyp_fields_validation( $cart_item ) {

		// Disable Validation section.
		$hook_validate_cart_priority = has_filter( 'woocommerce_add_to_cart_validation', array( WC_Name_Your_Price()->cart, 'validate_add_cart_item' ) );
		remove_filter( 'woocommerce_add_to_cart_validation', array( WC_Name_Your_Price()->cart, 'validate_add_cart_item' ), $hook_validate_cart_priority );

	}

	/**
	 * Maybe disable NYP validation on adding to cart.
	 *
	 * @since 3.1.0
	 */
	public static function maybe_disable_nyp_cart_validation() {

		// phpcs:disable WordPress.Security.NonceVerification
		if ( isset( $_REQUEST['update-gift'] ) ) {
			self::disable_nyp_fields_validation( array() );
		}

	}

	/**
	 * Maybe disable NYP inputs on single product page.
	 *
	 * @return bool
	 */
	public static function maybe_disable_nyp_inputs() {

		// phpcs:disable WordPress.Security.NonceVerification
		if ( isset( $_GET['update-gift'] ) ) {
			// It's a gift update, disable inputs.
			$hook_priority = has_action( 'woocommerce_single_variation', array( WC_Name_Your_Price()->display, 'display_variable_price_input' ) );
			remove_action( 'woocommerce_single_variation', array( WC_Name_Your_Price()->display, 'display_variable_price_input' ), $hook_priority );

			// Remove all suggested, minimum,etc.
			add_filter( 'wc_nyp_suggested_price_html', '__return_null' );
			add_filter( 'wc_nyp_minimum_price_html', '__return_null' );

		}

	}

	/**
	 * Maybe remove edit link to cart items.
	 *
	 * @param  bool   $answer
	 * @param  array  $cart_item
	 * @param  string $cart_item_key
	 * @return bool
	 */
	public static function maybe_disable_edit_link_in_cart( $answer, $cart_item, $cart_item_key ) {

		if ( isset( $cart_item['free_gift'] ) && ! empty( $cart_item['free_gift'] ) ) {
			$answer = true;
		}

		return $answer;

	}

}

WC_FGC_Name_Your_Price_Compatibility::init();
