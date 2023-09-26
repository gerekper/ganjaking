<?php
/**
 * WC_CSP_Core_Compatibility class
 *
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
 * @version  1.13.0
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
	 * Cache block based checkout detection result.
	 *
	 * @since  1.13.0
	 * @var    array
	 */
	private static $is_block_based_checkout = null;

	/**
	 * Current REST request.
	 *
	 * @since  1.13.0
	 *
	 * @var WP_REST_Request
	 */
	private static $request;

	/**
	 * Initialization and hooks.
	 */
	public static function init() {

		// Save current rest request. Is there a better way to get it?
		add_filter( 'rest_pre_dispatch', array( __CLASS__, 'save_rest_request' ), 10, 3 );

		self::$updated_shipping_method_instance_ids = get_option( 'woocommerce_updated_instance_ids', array() );

		if ( is_admin() ) {
			add_filter( 'woocommerce_enable_deprecated_additional_flat_rates', array( __CLASS__, 'enable_deprecated_addon_flat_rates' ) );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Callbacks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Saves the current rest request.
	 *
	 * @since  1.13.0
	 *
	 * @param  mixed            $result
	 * @param  WP_REST_Server   $server
	 * @param  WP_REST_Request  $request
	 * @return mixed
	 */
	public static function save_rest_request( $result, $server, $request ) {
		self::$request = $request;
		return $result;
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
			return '<img class="help_tip woocommerce-help-tip" data-tip="' . esc_attr( $tip ) . '" src="' . WC()->plugin_url() . '/assets/images/help.png" />';
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

	/**
	 * Back-compat wrapper for 'wp_timezone'.
	 *
	 * @since  1.9.0
	 *
	 * @return DateTimeZone
	 */
	public static function wp_timezone( ) {
		if ( self::is_wp_version_gte( '5.3' ) ) {
			return wp_timezone();
		}

		// Fallback follows the same code as in wp_timezone_string
		$timezone_string = get_option( 'timezone_string' );

		if ( $timezone_string ) {
			return new DateTimeZone($timezone_string);
		}

		$offset  = (float) get_option( 'gmt_offset' );
		$hours   = (int) $offset;
		$minutes = ( $offset - $hours );

		$sign      = ( $offset < 0 ) ? '-' : '+';
		$abs_hour  = abs( $hours );
		$abs_mins  = abs( $minutes * 60 );
		$tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );

		return new DateTimeZone($tz_offset);
	}

	/**
	 * Back-compat wrapper for 'is_rest_api_request'.
	 *
	 * @since  1.13.0
	 *
	 * @return boolean
	 */
	public static function is_rest_api_request() {

		if ( false !== self::get_api_request() ) {
			return true;
		}

		return method_exists( WC(), 'is_rest_api_request' ) ? WC()->is_rest_api_request() : defined( 'REST_REQUEST' );
	}

	/*
	|--------------------------------------------------------------------------
	| Utilities.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Whether this is a Store/REST API request.
	 *
	 * @since  1.13.0
	 *
	 * @return boolean
	 */
	public static function is_api_request() {
		return self::is_store_api_request() || self::is_rest_api_request();
	}

	/**
	 * Returns the current Store/REST API request or false.
	 *
	 * @since  1.13.0
	 *
	 * @return WP_REST_Request|false
	 */
	public static function get_api_request() {
		return self::$request instanceof WP_REST_Request ? self::$request : false;
	}

	/**
	 * Whether this is a Store API request.
	 *
	 * @since  1.13.0
	 *
	 * @param  string  $route
	 * @return boolean
	 */
	public static function is_store_api_request( $route = '', $method = '' ) {

		$request = self::get_api_request();

		if ( false !== $request && strpos( $request->get_route(), 'wc/store' ) !== false ) {

			$check_route  = ! empty( $route );
			$check_method = ! empty( $method );

			if ( ! $check_route && ! $check_method ) {
				// Generic store api question.
				return true;
			}

			$route_result  = ! $check_route || strpos( $request->get_route(), $route ) !== false;
			$method_result = ! $check_method || strtolower( $request->get_method() ) === strtolower( $method );

			return $route_result && $method_result;
		}

		return false;
	}

	/**
	 * Whether the checkout page contains the checkout block.
	 *
	 * @since  1.13.0
	 *
	 * @param  string  $route
	 * @return boolean
	 */
	public static function is_block_based_checkout() {

		if ( ! WC_CSP_Compatibility::is_module_loaded( 'blocks' ) ) {
			return false;
		}

		if ( is_null( self::$is_block_based_checkout ) ) {

			self::$is_block_based_checkout = false;

			$checkout_block_data = class_exists( 'WC_Blocks_Utils' ) ? WC_Blocks_Utils::get_blocks_from_page( 'woocommerce/checkout', 'checkout' ) : false;

			if ( ! empty( $checkout_block_data ) ) {
				self::$is_block_based_checkout = true;
			}
		}

		return self::$is_block_based_checkout;
	}
}

WC_CSP_Core_Compatibility::init();
