<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( class_exists( 'WC_Free_Gift_Coupons_Legacy' ) ) {
	return; // Exit if class exists.
}

/**
 * Holds legacy references to methods that have been moved or deprecated.
 * NOT FOR USE.
 *
 * @class WC_Free_Gift_Coupons_Legacy
 * @version	2.0.0
 */
class WC_Free_Gift_Coupons_Legacy {

	/*
	|--------------------------------------------------------------------------
	| Deprecated methods.
	|--------------------------------------------------------------------------
	*/


	/**
	 * Register admin script
	 *
	 * @return void
	 * @since 1.0
	 * @deprecated 2.0.0
	 */
	public static function admin_scripts() {
		wc_deprecated_function( 'WC_Free_Gift_Coupons::admin_scripts', '2.0.0', 'WC_Free_Gift_Coupons_Admin::admin_scripts' );
		return WC_Free_Gift_Coupons_Admin::discount_types( $types );
	}


	/**
	 * Output the new Coupon metabox fields
	 *
	 * @return HTML
	 * @since 1.0
	 */
	public static function coupon_options() {
		wc_deprecated_function( 'WC_Free_Gift_Coupons::coupon_options', '2.0.0', 'WC_Free_Gift_Coupons_Admin::coupon_options' );
		return WC_Free_Gift_Coupons_Admin::coupon_options();
	}

	/**
	 * Save the new coupon metabox field data
	 *
	 * @param integer $post_id
	 * @param obect $post
	 * @return void
	 * @since 1.0
	 */
	public static function process_shop_coupon_meta( $post_id, $post ) {
		wc_deprecated_function( 'WC_Free_Gift_Coupons::process_shop_coupon_meta', '2.0.0', 'WC_Free_Gift_Coupons_Admin::process_shop_coupon_meta()' );
		return WC_Free_Gift_Coupons_Admin::process_shop_coupon_meta( $post_id, $post );
	}

	/**
	 * Get Free Gift IDs from a coupon's ID.
	 *
	 * @param   mixed $code int coupon ID  | str coupon code
	 * @return	array
	 * @since   1.1.1
	 * @deprecated 2.0.0
	 */
	public static function get_gift_ids( $code ) {
		wc_deprecated_function( 'WC_Free_Gift_Coupons::get_gift_ids', '2.0', 'WC_Free_Gift_Coupons_Legacy:get_gift_data' );
		return array_keys( self::get_gift_data( $code ) );
	}

	/**
	 * Check is the installed version of WooCommerce is 2.3 or newer.
	 * props to Brent Shepard
	 *
	 * @return	boolean
	 * @since 1.0.4
	 * @deprecated 1.1.0
	 */
	public static function is_woocommerce_2_3() {
		wc_deprecated_function( 'WC_Free_Gift_Coupons::is_woocommerce_2_3', '1.1.0', 'WC_Free_Gift_Coupons::wc_is_version()' );
		return self::wc_is_version( '2.3' );
	}

	/**
	 * Refresh the cart page when coupon is added.
	 *
	 * @return void
	 * @since 1.1.0
	 * @deprecated 1.2.0
	 */
	public static function frontend_scripts() {
		wc_deprecated_function( 'WC_Free_Gift_Coupons::frontend_scripts', '1.2.0', 'WooCommerce 3.0 no longer requires this script.' );
		return false;
	}

} // End class.

