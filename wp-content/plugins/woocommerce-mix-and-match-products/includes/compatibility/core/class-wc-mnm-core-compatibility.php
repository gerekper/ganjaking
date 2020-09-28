<?php
/**
 * WooCommerce Core Compatibilty
 *
 * @author   SomewhereWarm
 * @category Classes
 * @package  WooCommerce Mix and Match Products/Compatibility
 * @since    1.2.0
 * @version  1.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_Core_Compatibility Class.
 *
 * Wrapper functions for WC core back-compatibility.
 */
class WC_MNM_Core_Compatibility {

	/**
	 * Cache 'gte' >= comparison results.
	 * 
	 * @var array
	 */
	private static $is_wc_version_gte = array();

	/**
	 * Cache 'gt' comparison results.
	 * 
	 * @var array
	 */
	private static $is_wc_version_gt = array();

	/**
	 * Helper method to get the version of the currently installed WooCommerce.
	 *
	 * @since  1.2.0
	 *
	 * @return string
	 */
	private static function get_wc_version() {
		return defined( 'WC_VERSION' ) && WC_VERSION ? WC_VERSION : null;
	}

	/**
	 * Returns true if the installed version of WooCommerce is greater than or equal to $version.
	 *
	 * @since  1.2.0
	 *
	 * @param  string  $version the version to compare
	 * @return bool true if the installed version of WooCommerce is > $version
	 */
	public static function is_wc_version_gte( $version ) {
		if ( ! isset( self::$is_wc_version_gte[ $version ] ) ) {
			self::$is_wc_version_gte[ $version ] = self::get_wc_version() && version_compare( self::get_wc_version(), $version, '>=' );
		}
		return self::$is_wc_version_gte[ $version ];
	}

	/**
	 * Returns true if the installed version of WooCommerce is greater than $version.
	 *
	 * @since  1.2.0
	 *
	 * @param  string  $version the version to compare
	 * @return bool true if the installed version of WooCommerce is > $version
	 */
	public static function is_wc_version_gt( $version ) {
		if ( ! isset( self::$is_wc_version_gt[ $version ] ) ) {
			self::$is_wc_version_gt[ $version ] = self::get_wc_version() && version_compare( self::get_wc_version(), $version, '>' );
		}
		return self::$is_wc_version_gt[ $version ];
	}


	/**
	 * Back-compat wrapper for wc_set_loop_prop
	 *
	 * @since 1.3.0
	 * @param string $prop Prop to set.
	 * @param string $value Value to set.
	 */
	public static function set_loop_prop( $prop, $value = '' ) {
		if( self::is_wc_version_gte( '3.3.0' ) ) {
			wc_set_loop_prop( $prop, $value );
		} else {
			$GLOBALS['woocommerce_loop'][ $prop ] = $value;
		}
	}

	/**
	 * Back-compat wrapper for 'get_parent_id'.
	 *
	 * @since  1.2.0
	 *
	 * @param  WC_Product  $product
	 * @return mixed
	 */
	public static function get_parent_id( $product ) {
		return $product->get_parent_id();
	}

	/**
	 * Back-compat wrapper for 'get_id'.
	 *
	 * @since  1.2.0
	 *
	 * @param  WC_Product  $product
	 * @return mixed
	 */
	public static function get_id( $product ) {
		return $product->get_id();
	}

	/**
	 * Back-compat wrapper for getting CRUD object props directly.
	 * Falls back to meta under WC 2.7+.
	 *
	 * @since  1.2.0
	 *
	 * @param  WC_Data  $obj
	 * @param  string   $name
	 * @param  string   $context
	 * @return mixed
	 */
	public static function get_prop( $obj, $name, $context = 'view' ) {
		$get_fn = 'get_' . $name;
		return is_callable( array( $obj, $get_fn ) ) ? $obj->$get_fn( $context ) : $obj->get_meta( '_' . $name, true );
	}

	/**
	 * Back-compat wrapper for setting CRUD object props directly.
	 * Falls back to meta under WC 2.7+.
	 *
	 * @since  1.2.0
	 *
	 * @param  WC_Data  $product
	 * @param  string   $name
	 * @param  mixed    $value
	 * @return void
	 */
	public static function set_prop( $obj, $name, $value ) {
		$set_fn = 'set_' . $name;
		if ( is_callable( array( $obj, $set_fn ) ) ) {
			$obj->$set_fn( $value );
		} else {
			$obj->add_meta_data( '_' . $name, $value, true );
		}
	}

	/**
	 * Back-compat wrapper for getting CRUD object meta.
	 *
	 * @since  1.2.0
	 *
	 * @param  WC_Data  $obj
	 * @param  string   $key
	 * @return mixed
	 */
	public static function get_meta( $obj, $key ) {
		return $obj->get_meta( $key, true );
	}

	/**
	 * Back-compat wrapper for 'wc_get_formatted_variation'.
	 *
	 * @since  1.2.0
	 *
	 * @param  WC_Product_Variation  $variation
	 * @param  bool               $flat
	 * @return string
	 */
	public static function wc_get_formatted_variation( $variation, $flat = false ) {
		wc_get_formatted_variation( $variation, $flat );
	}

	/**
	 * Get prefix for use with wp_cache_set. Allows all cache in a group to be invalidated at once..
	 *
	 * @since  1.2.0
	 *
	 * @param  string  $group
	 * @return string
	 */
	public static function wc_cache_helper_get_cache_prefix( $group ) {
		return WC_Cache_Helper::get_cache_prefix( $group );
	}

	/**
	 * Increment group cache prefix (invalidates cache).
	 *
	 * @since  1.2.0
	 *
	 * @param  string  $group
	 */
	public static function wc_cache_helper_incr_cache_prefix( $group ) {
		self::is_wc_version_gte( '3.9' ) ? WC_Cache_Helper::invalidate_cache_group( $group ) : WC_Cache_Helper::incr_cache_prefix( $group );
	}

	/**
	 * Returns the price including or excluding tax, based on the 'woocommerce_tax_display_shop' setting.
	 *
	 * @since  1.2.0
	 *
	 * @param  WC_Product $product
	 * @param  array $args
	 */
	public static function wc_get_price_to_display( $product, $args = array() ) {
		return wc_get_price_to_display( $product, $args );
	}


	/**
	 * Get price including tax.
	 *
	 * @since  1.2.0
	 *
	 * @param  WC_Product $product
	 * @param  array $args
	 */
	public static function wc_get_price_including_tax( $product, $args = array() ) {
		return wc_get_price_including_tax( $product, $args );
	}

	/**
	 * Get price excluding tax.
	 *
	 * @since  1.2.0
	 *
	 * @param  WC_Product $product
	 * @param  array $args
	 */
	public static function wc_get_price_excluding_tax( $product, $args = array() ) {
		return wc_get_price_excluding_tax( $product, $args );
	}

	/**
	 * Check if all variation's attributes are set.
	 *
	 * @since  1.2.0
	 *
	 * @param  WC_Product_Variation $variation
	 */
	public static function has_all_attributes_set( $variation ) {
		$set = true;
		foreach ( $variation->get_variation_attributes() as $att ) {
			if ( ! $att ) {
				$set = false;
				break;
			}
		}
		return $set;
	}

	/**
	 * Backwards compatible logging using 'WC_Logger' class.
	 *
	 * @since  1.2.0
	 *
	 * @param  string  $message
	 * @param  string  $level
	 * @param  string  $context
	 */
	public static function log( $message, $level, $context ) {
		$logger = wc_get_logger();
		$logger->log( $level, $message, array( 'source' => $context ) );
	}

	/**
	 * Get rounding precision.
	 * Needed to avoid an infinite loop when filtering. 
	 *
	 * @since  1.4.0
	 *
	 * @return int
	 */
	public static function wc_get_rounding_precision( $price_decimals = false ) {
		if ( false === $price_decimals ) {
			$price_decimals = wc_get_price_decimals();
		}
		return absint( $price_decimals ) + 2;
	}

	/*-----------------------------------------------------------------------------------*/
	/* Deprecated Functions */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Returns true if the installed version of WooCommerce is 2.7 or greater.
	 *
	 * @since  1.2.0
	 * @deprecated 1.2.5
	 * @see WC_MNM_Core_Compatibility::is_wc_version_gte()
	 *
	 * @return bool
	 */
	public static function is_wc_version_gte_2_7() {
		wc_deprecated_function( 'WC_MNM_Core_Compatibility::is_wc_version_gte_2_7', '1.2.5', 'WC_MNM_Core_Compatibility::is_wc_version_gte("2.7.0")' );
		return self::is_wc_version_gte( '2.7' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.6 or greater.
	 *
	 * @since  1.2.0
	 * @deprecated 1.2.5
	 * @see WC_MNM_Core_Compatibility::is_wc_version_gte()
	 *
	 * @return bool
	 */
	public static function is_wc_version_gte_2_6() {
		wc_deprecated_function( 'WC_MNM_Core_Compatibility::is_wc_version_gte_2_6', '1.2.5', 'WC_MNM_Core_Compatibility::is_wc_version_gte("2.6.0")' );
		return self::is_wc_version_gte( '2.6' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.5 or greater.
	 *
	 * @since  1.2.0
	 * @deprecated 1.2.5
	 * @see WC_MNM_Core_Compatibility::is_wc_version_gte()
	 *
	 * @return bool
	 */
	public static function is_wc_version_gte_2_5() {
		wc_deprecated_function( 'WC_MNM_Core_Compatibility::is_wc_version_gte_2_5', '1.2.5', 'WC_MNM_Core_Compatibility::is_wc_version_gte("2.5.0")' );
		return self::is_wc_version_gte( '2.5' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.4 or greater.
	 *
	 * @since  1.2.0
	 * @deprecated 1.2.5
	 * @see WC_MNM_Core_Compatibility::is_wc_version_gte()
	 *
	 * @return bool
	 */
	public static function is_wc_version_gte_2_4() {
		wc_deprecated_function( 'WC_MNM_Core_Compatibility::is_wc_version_gte_2_4', '1.2.5', 'WC_MNM_Core_Compatibility::is_wc_version_gte("2.4.0")' );
		return self::is_wc_version_gte( '2.4' );
	}

}