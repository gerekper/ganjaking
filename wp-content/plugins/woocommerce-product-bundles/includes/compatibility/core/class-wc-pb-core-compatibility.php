<?php
/**
 * WC_PB_Core_Compatibility class
 *
 * @package  WooCommerce Product Bundles
 * @since    4.7.6
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Functions for WC core back-compatibility.
 *
 * @class    WC_PB_Core_Compatibility
 * @version  6.15.3
 */
class WC_PB_Core_Compatibility {

	/**
	 * Cache 'gte' comparison results.
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
	 * Cache 'gt' comparison results for WP version.
	 *
	 * @since  5.13.3
	 * @var    array
	 */
	private static $is_wp_version_gt = array();

	/**
	 * Cache 'gte' comparison results for WP version.
	 *
	 * @since  5.13.3
	 * @var    array
	 */
	private static $is_wp_version_gte = array();

	/**
	 * Cache 'is_wc_admin_active' result.
	 *
	 * @since 6.3.1
	 * @var   bool
	 */
	private static $is_wc_admin_active;

	/**
	 * Current REST request.
	 *
	 * @var WP_REST_Request
	 */
	private static $request;

	/**
	 * Constructor.
	 */
	public static function init() {
		// Save current rest request. Is there a better way to get it?
		add_filter( 'rest_pre_dispatch', array( __CLASS__, 'save_rest_request' ), 10, 3 );
	}
	/*
	|--------------------------------------------------------------------------
	| Callbacks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Saves the current rest request.
	 *
	 * @since  6.15.0
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
	| Utilities.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Helper method to get the version of the currently installed WooCommerce.
	 *
	 * @since  4.7.6
	 *
	 * @return string
	 */
	private static function get_wc_version() {
		return defined( 'WC_VERSION' ) && WC_VERSION ? WC_VERSION : null;
	}

	/**
	 * Returns true if the installed version of WooCommerce is 3.1 or greater.
	 *
	 * @since  5.4.0
	 *
	 * @return boolean
	 */
	public static function is_wc_version_gte_3_1() {
		return self::is_wc_version_gte( '3.1' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.7 or greater.
	 *
	 * @since  5.0.0
	 *
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_7() {
		return self::is_wc_version_gte( '2.7' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.6 or greater.
	 *
	 * @since  5.0.0
	 *
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_6() {
		return self::is_wc_version_gte( '2.6' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.5 or greater.
	 *
	 * @since  4.10.2
	 *
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_5() {
		return self::is_wc_version_gte( '2.5' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.4 or greater.
	 *
	 * @since  4.10.2
	 *
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_4() {
		return self::is_wc_version_gte( '2.4' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.3 or greater.
	 *
	 * @since  4.7.6
	 *
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_3() {
		return self::is_wc_version_gte( '2.3' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.2 or greater.
	 *
	 * @since  4.7.6
	 *
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_2() {
		return self::is_wc_version_gte( '2.2' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is greater than or equal to $version.
	 *
	 * @since  5.2.0
	 *
	 * @param  string  $version the version to compare
	 * @return boolean true if the installed version of WooCommerce is > $version
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
	 * @since  4.7.6
	 *
	 * @param  string  $version the version to compare
	 * @return boolean true if the installed version of WooCommerce is > $version
	 */
	public static function is_wc_version_gt( $version ) {
		if ( ! isset( self::$is_wc_version_gt[ $version ] ) ) {
			self::$is_wc_version_gt[ $version ] = self::get_wc_version() && version_compare( self::get_wc_version(), $version, '>' );
		}
		return self::$is_wc_version_gt[ $version ];
	}

	/**
	 * Returns true if the installed version of WooCommerce is greater than or equal to $version.
	 *
	 * @since  5.13.3
	 *
	 * @param  string  $version
	 * @return boolean
	 */
	public static function is_wp_version_gt( $version ) {
		if ( ! isset( self::$is_wp_version_gt[ $version ] ) ) {
			global $wp_version;
			self::$is_wp_version_gt[ $version ] = $wp_version && version_compare( WC_PB()->plugin_version( true, $wp_version ), $version, '>' );
		}
		return self::$is_wp_version_gt[ $version ];
	}

	/**
	 * Returns true if the installed version of WooCommerce is greater than or equal to $version.
	 *
	 * @since  5.13.3
	 *
	 * @param  string  $version
	 * @return boolean
	 */
	public static function is_wp_version_gte( $version ) {
		if ( ! isset( self::$is_wp_version_gte[ $version ] ) ) {
			global $wp_version;
			self::$is_wp_version_gte[ $version ] = $wp_version && version_compare( WC_PB()->plugin_version( true, $wp_version ), $version, '>=' );
		}
		return self::$is_wp_version_gte[ $version ];
	}

	/**
	 * Whether this is a Store/REST API request.
	 *
	 * @since  6.15.0
	 *
	 * @return boolean
	 */
	public static function is_api_request() {
		return self::is_store_api_request() || self::is_rest_api_request();
	}

	/**
	 * Returns the current Store/REST API request or false.
	 *
	 * @since  6.15.0
	 *
	 * @return WP_REST_Request|false
	 */
	public static function get_api_request() {
		return self::$request instanceof WP_REST_Request ? self::$request : false;
	}

	/**
	 * Whether this is a Store API request.
	 *
	 * @since  6.15.0
	 *
	 * @param  string  $route
	 * @return boolean
	 */
	public static function is_store_api_request( $route = '' ) {

		$request = self::get_api_request();

		if ( false !== $request && strpos( $request->get_route(), 'wc/store' ) !== false ) {
			if ( '' === $route || strpos( $request->get_route(), $route ) !== false ) {
				return true;
			}
		}

		return false;
	}

	/*
	|--------------------------------------------------------------------------
	| Compatibility wrappers.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get the WC Product instance for a given product ID or post.
	 *
	 * get_product() is soft-deprecated in WC 2.2
	 *
	 * @since 4.7.6
	 *
	 * @param  bool|int|string|WP_Post  $the_product
	 * @param  array                    $args
	 * @return WC_Product
	 */
	public static function wc_get_product( $the_product = false, $args = array() ) {
		return wc_get_product( $the_product, $args );
	}

	/**
	 * Get all product cats for a product by ID, including hierarchy.
	 *
	 * @since  4.13.1
	 *
	 * @param  int  $product_id
	 * @return array
	 */
	public static function wc_get_product_cat_ids( $product_id ) {
		return wc_get_product_cat_ids( $product_id );
	}

	/**
	 * Wrapper for wp_get_post_terms which supports ordering by parent.
	 *
	 * @since  4.13.1
	 *
	 * @param  int     $product_id
	 * @param  string  $taxonomy
	 * @param  array   $args
	 * @return array
	 */
	public static function wc_get_product_terms( $product_id, $attribute_name, $args ) {
		return wc_get_product_terms( $product_id, $attribute_name, $args );
	}

	/**
	 * Get rounding precision.
	 *
	 * @since  4.14.6
	 *
	 * @return int
	 */
	public static function wc_get_rounding_precision( $price_decimals = false ) {
		if ( false === $price_decimals ) {
			$price_decimals = wc_get_price_decimals();
		}
		return absint( $price_decimals ) + 2;
	}

	/**
	 * Return the number of decimals after the decimal point.
	 *
	 * @since  4.13.1
	 *
	 * @return int
	 */
	public static function wc_get_price_decimals() {
		return wc_get_price_decimals();
	}

	/**
	 * Output a list of variation attributes for use in the cart forms.
	 *
	 * @since 4.13.1
	 *
	 * @param array  $args
	 */
	public static function wc_dropdown_variation_attribute_options( $args = array() ) {
		return wc_dropdown_variation_attribute_options( $args );
	}

	/**
	 * Display a WooCommerce help tip.
	 *
	 * @since  4.14.0
	 *
	 * @param  string  $tip
	 * @return string
	 */
	public static function wc_help_tip( $tip ) {
		return wc_help_tip( $tip );
	}

	/**
	 * Back-compat wrapper for 'wc_variation_attribute_name'.
	 *
	 * @since  5.0.2
	 *
	 * @param  string  $attribute_name
	 * @return string
	 */
	public static function wc_variation_attribute_name( $attribute_name ) {
		return wc_variation_attribute_name( $attribute_name );
	}

	/**
	 * Back-compat wrapper for 'WC_Product_Factory::get_product_type'.
	 *
	 * @since  5.2.0
	 *
	 * @param  mixed  $product_id
	 * @return mixed
	 */
	public static function get_product_type( $product_id ) {
		$product_type = false;
		if ( $product_id ) {
			$product_type = WC_Product_Factory::get_product_type( $product_id );
		}
		return $product_type;
	}

	/**
	 * Back-compat wrapper for 'get_parent_id'.
	 *
	 * @since  5.1.0
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
	 * @since  5.1.0
	 *
	 * @param  WC_Product  $product
	 * @return mixed
	 */
	public static function get_id( $product ) {
		return $product->get_id();
	}

	/**
	 * Back-compat wrapper for getting CRUD object props directly.
	 *
	 * @since  5.1.0
	 *
	 * @param  WC_Data  $obj
	 * @param  string   $name
	 * @param  string   $context
	 * @return mixed
	 */
	public static function get_prop( $obj, $name, $context = 'edit' ) {
		$get_fn = 'get_' . $name;
		return is_callable( array( $obj, $get_fn ) ) ? $obj->$get_fn( $context ) : $obj->get_meta( '_wc_pb_' . $name, true );
	}

	/**
	 * Back-compat wrapper for setting CRUD object props directly.
	 *
	 * @since  5.1.0
	 *
	 * @param  WC_Data  $obj
	 * @param  string   $name
	 * @param  mixed    $value
	 * @return void
	 */
	public static function set_prop( $obj, $name, $value ) {
		$set_fn = 'set_' . $name;
		if ( is_callable( array( $obj, $set_fn ) ) ) {
			$obj->$set_fn( $value );
		} else {
			$obj->add_meta_data( '_wc_pb_' . $name, $value, true );
		}
	}

	/**
	 * Back-compat wrapper for checking if a CRUD object props exists.
	 *
	 * @since  5.3.0
	 *
	 * @param  object  $obj
	 * @param  string  $name
	 * @return mixed
	 */
	public static function prop_exists( $obj, $name ) {
		$get_fn = 'get_' . $name;
		return is_callable( array( $obj, $get_fn ) ) ? true : $obj->meta_exists( '_wc_pb_' . $name );
	}

	/**
	 * Back-compat wrapper for getting CRUD object meta.
	 *
	 * @since  5.2.0
	 *
	 * @param  WC_Data  $obj
	 * @param  string   $key
	 * @return mixed
	 */
	public static function get_meta( $obj, $key ) {
		return $obj->get_meta( $key, true );
	}

	/**
	 * Back-compat wrapper for 'wc_get_price_including_tax'.
	 *
	 * @since  5.2.0
	 *
	 * @param  WC_Product  $product
	 * @param  array       $args
	 * @return mixed
	 */
	public static function wc_get_price_including_tax( $product, $args ) {
		return wc_get_price_including_tax( $product, $args );
	}

	/**
	 * Back-compat wrapper for 'wc_get_price_excluding_tax'.
	 *
	 * @since  5.2.0
	 *
	 * @param  WC_Product  $product
	 * @param  array       $args
	 * @return mixed
	 */
	public static function wc_get_price_excluding_tax( $product, $args ) {
		return wc_get_price_excluding_tax( $product, $args );
	}

	/**
	 * Back-compat wrapper for 'get_default_attributes'.
	 *
	 * @since  5.2.0
	 *
	 * @param  WC_Product  $product
	 * @return mixed
	 */
	public static function get_default_attributes( $product, $context = 'view' ) {
		return $product->get_default_attributes( $context );
	}

	/**
	 * Back-compat wrapper for 'wc_get_stock_html'.
	 *
	 * @since  5.2.0
	 *
	 * @param  WC_Product  $product
	 * @return mixed
	 */
	public static function wc_get_stock_html( $product ) {
		return wc_get_stock_html( $product );
	}

	/**
	 * Back-compat wrapper for 'wc_get_formatted_variation'.
	 *
	 * @since  5.1.0
	 *
	 * @param  WC_Product_Variation  $variation
	 * @param  boolean               $flat
	 * @return string
	 */
	public static function wc_get_formatted_variation( $variation, $flat ) {
		return wc_get_formatted_variation( $variation, $flat );
	}

	/**
	 * Back-compat wrapper for 'wc_get_cart_remove_url'.
	 *
	 * @since  5.7.7
	 *
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public static function wc_get_cart_remove_url( $cart_item_key ) {
		return wc_get_cart_remove_url( $cart_item_key );
	}

	/**
	 * Get prefix for use with wp_cache_set. Allows all cache in a group to be invalidated at once..
	 *
	 * @since  5.0.0
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
	 * @since  5.0.0
	 *
	 * @param  string  $group
	 */
	public static function wc_cache_helper_incr_cache_prefix( $group ) {
		WC_Cache_Helper::incr_cache_prefix( $group );
	}

	/**
	 * Backwards compatible logging using 'WC_Logger' class.
	 *
	 * @since  5.2.0
	 *
	 * @param  string  $message
	 * @param  string  $level
	 * @param  string  $context
	 */
	public static function log( $message, $level, $context ) {

		// Limit some types of logging to staging/dev environments only.
		if ( in_array( $context, apply_filters( 'woocommerce_bundles_debug_log_contexts', array( 'wc_pb_db_sync_tasks' ) ) ) ) {
			if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
				return;
			}
		}

		$logger = wc_get_logger();
		$logger->log( $level, $message, array( 'source' => $context ) );
	}

	/**
	 * Back-compat wrapper for 'is_rest_api_request'.
	 *
	 * @since  5.11.1
	 *
	 * @return boolean
	 */
	public static function is_rest_api_request() {

		if ( false !== self::get_api_request() ) {
			return true;
		}

		return method_exists( WC(), 'is_rest_api_request' ) ? WC()->is_rest_api_request() : defined( 'REST_REQUEST' );
	}

	/**
	 * Compatibility wrapper for invalidating cache groups.
	 *
	 * @param  string  $group
	 * @return void
	 */
	public static function invalidate_cache_group( $group ) {
		if ( self::is_wc_version_gte( '3.9' ) ) {
			WC_Cache_Helper::invalidate_cache_group( $group );
		} else {
			WC_Cache_Helper::incr_cache_prefix( $group );
		}
	}

	/**
	 * True if 'wc-admin' is active.
	 *
	 * @since  6.3.1
	 *
	 * @return boolean
	 */
	public static function is_wc_admin_active() {

		if ( ! isset( self::$is_wc_admin_active ) ) {

			$enabled = self::is_wc_version_gte( '4.0' ) && defined( 'WC_ADMIN_VERSION_NUMBER' ) && version_compare( WC_ADMIN_VERSION_NUMBER, '1.0.0', '>=' );
			if ( $enabled && version_compare( WC_ADMIN_VERSION_NUMBER, '2.3.0', '>=' ) && true === apply_filters( 'woocommerce_admin_disabled', false ) ) {
				$enabled = false;
			}

			self::$is_wc_admin_active = $enabled;
		}

		return self::$is_wc_admin_active;
	}

	/**
	 * Returns true if is a react based admin page.
	 *
	 * @since  6.15.3
	 *
	 * @return boolean
	 */
	public static function is_admin_or_embed_page() {

		if ( class_exists( '\Automattic\WooCommerce\Admin\PageController' ) && method_exists( '\Automattic\WooCommerce\Admin\PageController', 'is_admin_or_embed_page' ) ) {

			return \Automattic\WooCommerce\Admin\PageController::is_admin_or_embed_page();

		} elseif ( class_exists( '\Automattic\WooCommerce\Admin\Loader' ) && method_exists( '\Automattic\WooCommerce\Admin\Loader', 'is_admin_or_embed_page' ) ) {

			return \Automattic\WooCommerce\Admin\Loader::is_admin_or_embed_page();
		}

		return false;
	}
}

WC_PB_Core_Compatibility::init();
