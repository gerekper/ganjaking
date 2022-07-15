<?php
/**
 * WooCommerce Core Compatibilty
 *
 * @package  WooCommerce Mix and Match Products/Compatibility
 * @since    1.2.0
 * @version  2.0.0
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
	 * Current REST request.
	 *
	 * @var WP_REST_Request
	 */
	private static $request;

	/**
	 * Attach any hooks/filters for core WooCommerce
	 *
	 * @since 2.0.0
	 */
	public static function attach_hooks_and_filters() {
		// Save current rest request. Is there a better way to get it?
		add_filter( 'rest_pre_dispatch', array( __CLASS__, 'save_rest_request' ), 10, 3 );
		add_filter( 'woocommerce_product_data_store_cpt_get_products_query', array( __CLASS__, 'query_products_by_category_ids' ), 10, 2 );
		add_filter( 'posts_orderby', array( __CLASS__, 'posts_orderby_category' ), 10, 2 );
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

	/**
	 * Filter wc_get_products() to support querying by category IDs.
	 *
	 * @since  2.0.0
	 *
	 * @param array $wp_query_args Query vars sent to WP_Query.
	 * @param array $query_vars Query vars from a WC_Product_Query.
	 * @return array
	 */
	public static function query_products_by_category_ids( $wp_query_args, $query_vars ) {

		// Handle product categories by ID.
		if ( ! empty( $query_vars[ 'category_id' ] ) ) {
			unset( $wp_query_args[ 'category_id' ] );
			$wp_query_args[ 'tax_query' ][] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => $query_vars[ 'category_id' ],
			);
		}

		return $wp_query_args;
	}

	/**
	 * Order/Group products by category
	 *
	 * @since 2.0.0
	 *
	 * @param string   $orderby The GROUP BY clause of the query.
	 * @param WP_Query $query   The WP_Query instance (passed by reference).
	 * @return string
	 */
	public static function posts_orderby_category( $orderby, $query ) {
		global $wpdb;

		if ( ! empty( $query->query_vars[ 'order_by_category_id' ] ) && count( $query->query_vars[ 'order_by_category_id' ] ) > 1 && count( $query->query_vars[ 'tax_query' ] ) > 1 ) {
			$orderby = "FIELD(tt1.term_taxonomy_id," . implode( ',', array_map( 'absint', (array) $query->query_vars[ 'order_by_category_id' ] ) ) . '), ' . $orderby;
		}
		return $orderby;
	}

	/*
	|--------------------------------------------------------------------------
	| Utilities.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Helper method to get the version of the currently installed WooCommerce.
	 *
	 * @since  1.2.0
	 *
	 * @return string
	 */
	public static function get_wc_version() {
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
		$result = WC_MNM_Helpers::cache_get( $version, 'wc_version_gte' );
		if ( null === $result ) {
			$result = self::get_wc_version() && version_compare( self::get_wc_version(), $version, '>=' );
			WC_MNM_Helpers::cache_set( $version, 'wc_version_gte' );
		}
		return $result;
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
		$result = WC_MNM_Helpers::cache_get( $version, 'wc_version_gt' );
		if ( null === $result ) {
			$result = self::get_wc_version() && version_compare( self::get_wc_version(), $version, '>' );
			WC_MNM_Helpers::cache_set( $version, 'wc_version_gt' );
		}
		return $result;
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
	 * Whether this is a Store/REST API request.
	 *
	 * @since  2.0.0
	 *
	 * @return boolean
	 */
	public static function is_api_request() {
		return self::is_store_api_request() || self::is_rest_api_request();
	}

	/**
	 * Returns the current Store/REST API request or false.
	 *
	 * @since  2.0.0
	 *
	 * @return WP_REST_Request|false
	 */
	public static function get_api_request() {
		return self::$request instanceof WP_REST_Request ? self::$request : false;
	}

	/**
	 * Whether this is a Store API request.
	 *
	 * @since  2.0.0
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

	/**
	 * Back-compat wrapper for 'is_rest_api_request'.
	 *
	 * @since  2.0.0
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
	| Deprecated Functions.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Back-compat wrapper for wc_set_loop_prop
	 *
	 * @since 1.3.0
	 * @deprecated 2.0.0
	 * @param string $prop Prop to set.
	 * @param string $value Value to set.
	 */
	public static function set_loop_prop( $prop, $value = '' ) {
		wc_deprecated_function( 'WC_MNM_Core_Compatibility::set_loop_prop()', '2.0', 'wc_set_loop_prop()' );
		if ( self::is_wc_version_gte( '3.3.0' ) ) {
			wc_set_loop_prop( $prop, $value );
		} else {
			$GLOBALS['woocommerce_loop'][ $prop ] = $value;
		}
	}

	/**
	 * Back-compat wrapper for 'get_parent_id'.
	 *
	 * @since  1.2.0
	 * @deprecated 2.0.0
	 *
	 * @param  WC_Product  $product
	 * @return mixed
	 */
	public static function get_parent_id( $product ) {
		wc_deprecated_function( 'WC_MNM_Core_Compatibility::get_parent_id()', '2.0', 'WC_Product::get_parent_id()' );
		return $product->get_parent_id();
	}

	/**
	 * Back-compat wrapper for 'get_id'.
	 *
	 * @since  1.2.0
	 * @deprecated 2.0.0
	 *
	 * @param  WC_Product  $product
	 * @return mixed
	 */
	public static function get_id( $product ) {
		wc_deprecated_function( 'WC_MNM_Core_Compatibility::get_id', '2.0', 'WC_Product::get_id' );
		return $product->get_id();
	}

	/**
	 * Back-compat wrapper for getting CRUD object props directly.
	 * Falls back to meta under WC 2.7+.
	 *
	 * @since  1.2.0
	 * @deprecated 2.0.0
	 *
	 * @param  WC_Data  $obj
	 * @param  string   $name
	 * @param  string   $context
	 * @return mixed
	 */
	public static function get_prop( $obj, $name, $context = 'view' ) {
		wc_deprecated_function( 'WC_MNM_Core_Compatibility::get_prop', '2.0', 'WC_Product::get_prop()' );
		$get_fn = 'get_' . $name;
		return is_callable( array( $obj, $get_fn ) ) ? $obj->$get_fn( $context ) : $obj->get_meta( '_' . $name, true );
	}

	/**
	 * Back-compat wrapper for setting CRUD object props directly.
	 * Falls back to meta under WC 2.7+.
	 *
	 * @since  1.2.0
	 * @deprecated 2.0.0
	 *
	 * @param  WC_Data  $product
	 * @param  string   $name
	 * @param  mixed    $value
	 * @return void
	 */
	public static function set_prop( $obj, $name, $value ) {
		wc_deprecated_function( 'WC_MNM_Core_Compatibility::set_prop', '2.0', 'WC_Product::set_prop()' );
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
	 * @deprecated 2.0.0
	 *
	 * @param  WC_Data  $obj
	 * @param  string   $key
	 * @return mixed
	 */
	public static function get_meta( $obj, $key ) {
		wc_deprecated_function( 'WC_MNM_Core_Compatibility::get_meta', '2.0', 'WC_Product::get_meta()' );
		return $obj->get_meta( $key, true );
	}

	/**
	 * Back-compat wrapper for 'wc_get_formatted_variation'.
	 *
	 * @since  1.2.0
	 * @deprecated 2.0.0
	 *
	 * @param  WC_Product_Variation  $variation
	 * @param  bool               $flat
	 * @return string
	 */
	public static function wc_get_formatted_variation( $variation, $flat = false ) {
		wc_deprecated_function( 'WC_MNM_Core_Compatibility::wc_get_formatted_variation', '2.0', 'wc_get_formatted_variation()' );
		wc_get_formatted_variation( $variation, $flat );
	}

	/**
	 * Get prefix for use with wp_cache_set. Allows all cache in a group to be invalidated at once..
	 *
	 * @since  1.2.0
	 * @deprecated 2.0.0
	 *
	 * @param  string  $group
	 * @return string
	 */
	public static function wc_cache_helper_get_cache_prefix( $group ) {
		wc_deprecated_function( 'WC_MNM_Core_Compatibility::wc_cache_helper_get_cache_prefix', '2.0', 'WC_Cache_Helper::get_cache_prefix()' );
		return WC_Cache_Helper::get_cache_prefix( $group );
	}

	/**
	 * Increment group cache prefix (invalidates cache).
	 *
	 * @since  1.2.0
	 * @deprecated 2.0.0
	 *
	 * @param  string  $group
	 */
	public static function wc_cache_helper_incr_cache_prefix( $group ) {
		wc_deprecated_function( 'WC_MNM_Core_Compatibility::wc_cache_helper_incr_cache_prefix', '2.0', 'WC_Cache_Helper::invalidate_cache_group()' );
		self::is_wc_version_gte( '3.9' ) ? WC_Cache_Helper::invalidate_cache_group( $group ) : WC_Cache_Helper::incr_cache_prefix( $group );
	}

	/**
	 * Returns the price including or excluding tax, based on the 'woocommerce_tax_display_shop' setting.
	 *
	 * @since  1.2.0
	 * @deprecated 2.0.0
	 *
	 * @param  WC_Product $product
	 * @param  array $args
	 */
	public static function wc_get_price_to_display( $product, $args = array() ) {
		wc_deprecated_function( 'WC_MNM_Core_Compatibility::wc_get_price_to_display', '2.0', 'wc_get_price_to_display()' );
		return wc_get_price_to_display( $product, $args );
	}


	/**
	 * Get price including tax.
	 *
	 * @since  1.2.0
	 * @deprecated 2.0.0
	 *
	 * @param  WC_Product $product
	 * @param  array $args
	 */
	public static function wc_get_price_including_tax( $product, $args = array() ) {
		wc_deprecated_function( 'WC_MNM_Core_Compatibility::wc_get_price_including_tax', '2.0', 'wc_get_price_including_tax()' );
		return wc_get_price_including_tax( $product, $args );
	}

	/**
	 * Get price excluding tax.
	 *
	 * @since  1.2.0
	 * @deprecated 2.0.0
	 *
	 * @param  WC_Product $product
	 * @param  array $args
	 */
	public static function wc_get_price_excluding_tax( $product, $args = array() ) {
		wc_deprecated_function( 'WC_MNM_Core_Compatibility::wc_get_price_excluding_tax', '2.0', 'wc_get_price_excluding_tax()' );
		return wc_get_price_excluding_tax( $product, $args );
	}

	/**
	 * Backwards compatible logging using 'WC_Logger' class.
	 *
	 * @since  1.2.0
	 * @deprecated 2.0.0
	 *
	 * @param  string  $message
	 * @param  string  $level
	 * @param  string  $context
	 */
	public static function log( $message, $level, $context ) {
		wc_deprecated_function( 'WC_MNM_Core_Compatibility::log', '2.0', 'wc_get_logger()' );
		$logger = wc_get_logger();
		$logger->log( $level, $message, array( 'source' => $context ) );
	}

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

	/**
	 * Get rounding precision.
	 * Needed to avoid an infinite loop when filtering.
	 *
	 * @since  1.4.0
	 * @deprecated 2.0.0
	 *
	 * @return int
	 */
	public static function wc_get_rounding_precision( $price_decimals = false ) {
		wc_deprecated_function( 'WC_MNM_Core_Compatibility::wc_get_rounding_precision()', '2.0', 'WC_MNM_Product_Prices::extend_rounding_precision()' );
		return WC_MNM_Product_Prices::extend_rounding_precision( $price_decimals );
	}
}
WC_MNM_Core_Compatibility::attach_hooks_and_filters();
