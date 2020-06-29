<?php
/**
 * WC_CSP_Core_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Functions related to core back-compatibility.
 *
 * @class    WC_CSP_Core_Compatibility
 * @version  1.5.9
 */
class WC_CSP_Core_Compatibility {

	/**
	 * Modified shipping method instance IDs during the WC 2.6 upgrade.
	 * @var array
	 */
	public static $updated_shipping_method_instance_ids;
	/**
	 * Shipping methods that got the 'lagacy' treatment in WC 2.6.
	 * @var array
	 */
	public static $legacy_methods = array( 'flat_rate', 'free_shipping', 'international_delivery', 'local_delivery', 'local_pickup' );

	/**
	 * Shipping methods IDs whose rate IDs changed after the WC 2.6 upgrade, for which CSP is providing back-compat.
	 * @var array
	 */
	public static $upgraded_methods = array( 'table_rate', 'flat_rate_boxes' );

	/**
	 * Cache 'gte' comparison results.
	 * @var array
	 */
	private static $is_wc_version_gte = array();

	/**
	 * Cache 'gt' comparison results.
	 * @var array
	 */
	private static $is_wc_version_gt = array();

	/**
	 * Cache 'gt' comparison results for WP version.
	 *
	 * @since  1.5.9
	 * @var    array
	 */
	private static $is_wp_version_gt = array();

	/**
	 * Cache 'gte' comparison results for WP version.
	 *
	 * @since  1.5.9
	 * @var    array
	 */
	private static $is_wp_version_gte = array();

	/**
	 * Initialization and hooks.
	 */
	public static function init() {

		self::$updated_shipping_method_instance_ids = get_option( 'woocommerce_updated_instance_ids', array() );

		if ( is_admin() ) {
			add_filter( 'woocommerce_enable_deprecated_additional_flat_rates', array( __CLASS__, 'enable_deprecated_addon_flat_rates' ) );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| WC version handling.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Helper method to get the version of the currently installed WooCommerce.
	 *
	 * @since  1.0.4
	 *
	 * @return string
	 */
	public static function get_wc_version() {
		return defined( 'WC_VERSION' ) && WC_VERSION ? WC_VERSION : null;
	}

	/**
	 * Helper method to get the class string for all versions.
	 *
	 * @since  1.4.0
	 *
	 * @return string
	 */
	public static function get_versions_class() {

		$classes = array();

		if ( self::is_wc_version_gte( '2.4' ) ) {
			$classes[] = 'wc_gte_24';
		}
		if ( self::is_wc_version_gte( '2.7' ) ) {
			$classes[] = 'wc_gte_27';
		}
		if ( self::is_wc_version_gte( '2.6' ) ) {
			$classes[] = 'wc_gte_26';
		}
		if ( self::is_wc_version_gte( '3.0' ) ) {
			$classes[] = 'wc_gte_30';
		}
		if ( self::is_wc_version_gte( '3.3' ) ) {
			$classes[] = 'wc_gte_33';
		}
		if ( self::is_wc_version_gte( '3.4' ) ) {
			$classes[] = 'wc_gte_34';
		}

		return implode( ' ', $classes );
	}

	/**
	 * Returns true if the installed cersion of WooCommerce is 3.0 or greater.
	 *
	 * @since 1.4.0
	 *
	 * @return boolean
	 */
	public static function is_wc_version_gte_3_0() {
		_deprecated_function( __METHOD__ . '()', '1.4.0', __CLASS__ . '::is_wc_version_gte( "3.0" )' );
		return self::is_wc_version_gte( '3.0' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.7 or greater.
	 *
	 * @since  1.2.4
	 *
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_7() {
		_deprecated_function( __METHOD__ . '()', '1.4.0', __CLASS__ . '::is_wc_version_gte( "2.7" )' );
		return self::is_wc_version_gte( '2.7' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.6 or greater.
	 *
	 * @since  1.1.12
	 *
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_6() {
		_deprecated_function( __METHOD__ . '()', '1.4.0', __CLASS__ . '::is_wc_version_gte( "2.6" )' );
		return self::is_wc_version_gte( '2.6' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.5 or greater.
	 *
	 * @since  1.1.11
	 *
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_5() {
		_deprecated_function( __METHOD__ . '()', '1.4.0', __CLASS__ . '::is_wc_version_gte( "2.5" )' );
		return self::is_wc_version_gte( '2.5' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.4 or greater.
	 *
	 * @since  1.2.5
	 *
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_4() {
		_deprecated_function( __METHOD__ . '()', '1.4.0', __CLASS__ . '::is_wc_version_gte( "2.4" )' );
		return self::is_wc_version_gte( '2.4' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.2 or greater.
	 *
	 * @since  1.0.4
	 *
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_3() {
		_deprecated_function( __METHOD__ . '()', '1.4.0', __CLASS__ . '::is_wc_version_gte( "2.3" )' );
		return self::is_wc_version_gte( '2.3' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.2 or greater.
	 *
	 * @since  1.0.4
	 *
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_2() {
		_deprecated_function( __METHOD__ . '()', '1.4.0', __CLASS__ . '::is_wc_version_gte( "2.2" )' );
		return self::is_wc_version_gte( '2.2' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is greater than or equal to $version.
	 *
	 * @since  1.2.5
	 *
	 * @param  string  $version
	 * @return boolean
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
	 * @since  1.0.4
	 *
	 * @param  string  $version
	 * @return boolean
	 */
	public static function is_wc_version_gt( $version ) {
		if ( ! isset( self::$is_wc_version_gt[ $version ] ) ) {
			self::$is_wc_version_gt[ $version ] = self::get_wc_version() && version_compare( self::get_wc_version(), $version, '>' );
		}
		return self::$is_wc_version_gt[ $version ];
	}

	/**
	 * Returns true if the installed version of WooCommerce is lower than or equal $version.
	 *
	 * @since  1.4.0
	 *
	 * @param  string  $version
	 * @return boolean
	 */
	public static function is_wc_version_lte( $version ) {
		if ( ! isset( self::$is_wc_version_gt[ $version ] ) ) {
			self::$is_wc_version_gt[ $version ] = self::get_wc_version() && version_compare( self::get_wc_version(), $version, '<=' );
		}
		return self::$is_wc_version_gt[ $version ];
	}

	/**
	 * Returns true if the installed version of WooCommerce is lower than $version.
	 *
	 * @since  1.4.0
	 *
	 * @param  string  $version
	 * @return boolean
	 */
	public static function is_wc_version_lt( $version ) {
		if ( ! isset( self::$is_wc_version_gt[ $version ] ) ) {
			self::$is_wc_version_gt[ $version ] = self::get_wc_version() && version_compare( self::get_wc_version(), $version, '<' );
		}
		return self::$is_wc_version_gt[ $version ];
	}

	/**
	 * Returns true if the installed version of WooCommerce is greater than or equal to $version.
	 *
	 * @since  1.5.9
	 *
	 * @param  string  $version
	 * @return boolean
	 */
	public static function is_wp_version_gt( $version ) {
		if ( ! isset( self::$is_wp_version_gt[ $version ] ) ) {
			global $wp_version;
			self::$is_wp_version_gt[ $version ] = $wp_version && version_compare( WC_CSP()->plugin_version( true, $wp_version ), $version, '>' );
		}
		return self::$is_wp_version_gt[ $version ];
	}

	/**
	 * Returns true if the installed version of WooCommerce is greater than or equal to $version.
	 *
	 * @since  1.5.9
	 *
	 * @param  string  $version
	 * @return boolean
	 */
	public static function is_wp_version_gte( $version ) {
		if ( ! isset( self::$is_wp_version_gte[ $version ] ) ) {
			global $wp_version;
			self::$is_wp_version_gte[ $version ] = $wp_version && version_compare( WC_CSP()->plugin_version( true, $wp_version ), $version, '>=' );
		}
		return self::$is_wp_version_gte[ $version ];
	}

	/*
	|--------------------------------------------------------------------------
	| Hooks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Enable deprecated Add-on flat rate options panel.
	 *
	 * @param  boolean $enable
	 * @return boolean
	 */
	public static function enable_deprecated_addon_flat_rates( $enable ) {
		return true;
	}

	/*
	|--------------------------------------------------------------------------
	| Back compat.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Display a WooCommerce help tip.
	 *
	 * @since  1.2.0
	 *
	 * @param  string $tip        Help tip text
	 * @return string
	 */
	public static function wc_help_tip( $tip ) {

		if ( self::is_wc_version_gte( '2.5' ) ) {
			return wc_help_tip( $tip );
		} else {
			return '<img class="help_tip woocommerce-help-tip" data-tip="' . $tip . '" src="' . WC()->plugin_url() . '/assets/images/help.png" />';
		}
	}

	/**
	 * Get the WC Product instance for a given product ID or post.
	 *
	 * get_product() is soft-deprecated in WC 2.2
	 *
	 * @since  1.0.4
	 * @param  bool|int|string|\WP_Post $the_product
	 * @param  array $args
	 * @return WC_Product
	 */
	public static function wc_get_product( $the_product = false, $args = array() ) {
		return get_product( $the_product, $args );
	}

	/**
	 * Back-compat wrapper for 'get_id'.
	 *
	 * @since  1.2.4
	 *
	 * @param  WC_Product  $product
	 * @return mixed
	 */
	public static function get_id( $product ) {
		if ( self::is_wc_version_gte( '2.7' ) ) {
			return $product->get_id();
		} else {
			return $product->is_type( 'variation' ) ? absint( $product->variation_id ) : absint( $product->id );
		}
	}

	/**
	 * Back-compat wrapper for 'get_parent_id'.
	 *
	 * @since  1.2.5
	 *
	 * @param  WC_Product  $product
	 * @return mixed
	 */
	public static function get_parent_id( $product ) {
		if ( self::is_wc_version_gte( '2.7' ) ) {
			return $product->get_parent_id();
		} else {
			return $product->is_type( 'variation' ) ? absint( $product->id ) : 0;
		}
	}

	/**
	 * Back-compat wrapper for 'get_parent_id' with fallback to 'get_id'.
	 *
	 * @since  1.2.5
	 *
	 * @param  WC_Product  $product
	 * @return mixed
	 */
	public static function get_product_id( $product ) {
		if ( self::is_wc_version_gte( '2.7' ) ) {
			$parent_id = $product->get_parent_id();
			return $parent_id ? $parent_id : $product->get_id();
		} else {
			return absint( $product->id );
		}
	}

	/**
	 * Back-compat wrapper for 'wc_normalize_postcode' with fallback to 'preg_replace'.
	 *
	 * @since  1.4.0
	 *
	 * @param  string  $postcode
	 * @return mixed
	 */
	public static function wc_normalize_postcode( $postcode ) {
		if ( self::is_wc_version_gte( '2.6' ) ) {
			return wc_normalize_postcode( $postcode );
		} else {
			return preg_replace( '/[\s\-]/', '', trim( strtoupper( $postcode ) ) );
		}
	}


	/**
	 * Return product title with attributes -- if variation.
	 *
	 * @since  1.5.8
	 *
	 * @param  WC_Product_Variation|int  $product
	 *
	 * @return string
	 */
	public static function get_name( $product ) {

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( ! $product ) {
			return false;
		}

		$title = $product->get_title();
		$name  = $title;

		if ( is_a( $product, 'WC_Product_Variation' ) ) {
			$description = wc_get_formatted_variation( $product, true );
			$name        = sprintf( _x( '%1$s &ndash; %2$s', 'variation title followed by attributes', 'woocommerce-conditional-shipping-and-payments' ), $title, $description );
		}

		return $name;
	}

	/*
	|--------------------------------------------------------------------------
	| Helpers.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Clears cached shipping rates.
	 *
	 * @return void
	 */
	public static function clear_cached_shipping_rates() {
		global $wpdb;

		// WC 2.2 - WC 2.4: Rates cached as transients.
		$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('\_transient\_wc\_ship\_%') OR `option_name` LIKE ('\_transient\_timeout\_wc\_ship\_%')" );

		// WC 2.5: Rates cached in session.
		if ( self::is_wc_version_gte( '2.5' ) ) {
			// Increments the shipping transient version to invalidate session entries.
			WC_Cache_Helper::get_transient_version( 'shipping', true );
		}
	}
}

WC_CSP_Core_Compatibility::init();
