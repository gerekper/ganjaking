<?php
/**
 * Product Addons Compatibility
 *
 * @package  WooCommerce Free Gift Coupons/Compatibility
 * @since    3.1.0
 * @version  3.1.0
 */

// Exit if accessed directly.
 defined( 'ABSPATH' ) || exit;

/**
 * WC_FGC_Product_Addons_Compatibility Class.
 *
 * Adds compatibility with WooCommerce Product Addons.
 */
class WC_FGC_Product_Addons_Compatibility {

	public static function init() {
		// Cart variation edit and Validation.
		add_action( 'wc_fgc_before_single_product_cart_template', array( __CLASS__, 'maybe_disable_addons_fields_display' ) );
		add_action( 'wc_fgc_before_updating_product_in_cart', array( __CLASS__, 'disable_addons_fields_validation' ) );

	}

	/**
	 * Get coupon meta.
	 * 
	 * @param string $coupon_code Coupon code.
	 * @param string $coupon_meta The meta key.
	 * @return string
	 * @since 3.1.0
	 */
	public static function get_coupon_meta( $coupon_code, $coupon_meta ) {
		$value  = '';
		$coupon = new WC_Coupon( $coupon_code );

		if ( $coupon instanceof WC_Coupon && $coupon->get_object_read() ) {
			$value = $coupon->get_meta( $coupon_meta, true, 'edit' );
		}
		return $value;
	}

	/**
	 * Maybe Disable Product Addons fields.
	 * 
	 * This disables addons fields only in the cart context.
	 *
	 * @param $cart_item
	 * @since 3.1.0
	 */
	public static function maybe_disable_addons_fields_display( $cart_item ) {
		add_action( 'woocommerce_before_variations_form', array( __CLASS__, 'disable_addons_fields_display' ), 0 );
	}

	/**
	 * Disable Product Addons fields.
	 * 
	 * This finally removes the Add-ons.
	 * Must be done after PAO adds the display to the woocommerce_before_variations_form hook.
	 *
	 * @since 3.1.0
	 */
	public static function disable_addons_fields_display( $cart_item ) {

		$display_class = $GLOBALS['Product_Addon_Display'];

		// Generate priority to always remove the actions properly, incase priorities change.
		$hook_display_priority            = has_action( 'woocommerce_before_add_to_cart_button', array( $display_class, 'display' ) );
		$hook_reposition_display_priority = has_action( 'woocommerce_before_variations_form', array( $display_class, 'reposition_display_for_variable_product' ) );

		remove_action( 'woocommerce_before_add_to_cart_button', array( $display_class, 'display' ), $hook_display_priority );
		remove_action( 'woocommerce_before_variations_form', array( $display_class, 'reposition_display_for_variable_product' ), $hook_reposition_display_priority );

	}
	/**
	 * Disable Product Addons field validation.
	 * 
	 * This helps in disabling addons fields
	 * depending on the coupon setting.
	 *
	 * @param $cart_item
	 * @since 3.1.0
	 */
	public static function disable_addons_fields_validation( $cart_item ) {

		// Check if the coupon allows product addons or not.
		$addon_disabled = 'yes';

		// Hope it's addons disabled?
		if ( 'yes' === $addon_disabled ) {
			$cart_validation_class = $GLOBALS['Product_Addon_Cart'];

			// Disable Validation section.
			$hook_validate_cart_priority = has_filter( 'woocommerce_add_to_cart_validation', array( $cart_validation_class, 'validate_add_cart_item' ) );
			remove_filter( 'woocommerce_add_to_cart_validation', array( $cart_validation_class, 'validate_add_cart_item' ), $hook_validate_cart_priority );
		}

	}

}

WC_FGC_Product_Addons_Compatibility::init();
