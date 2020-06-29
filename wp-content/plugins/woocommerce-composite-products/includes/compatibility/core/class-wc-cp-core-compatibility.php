<?php
/**
 * WC_CP_Core_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.5.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Functions related to WC core backwards compatibility.
 *
 * @class    WC_CP_Core_Compatibility
 * @version  5.0.5
 */
class WC_CP_Core_Compatibility {

	/*
	|--------------------------------------------------------------------------
	| Version check methods.
	|--------------------------------------------------------------------------
	*/

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
	 * @since  5.0.5
	 * @var    array
	 */
	private static $is_wp_version_gt = array();

	/**
	 * Cache 'gte' comparison results for WP version.
	 *
	 * @since  5.0.5
	 * @var    array
	 */
	private static $is_wp_version_gte = array();

	/**
	 * Helper method to get the version of the currently installed WooCommerce.
	 *
	 * @since  3.2.0
	 *
	 * @return string
	 */
	public static function get_wc_version() {
		return defined( 'WC_VERSION' ) && WC_VERSION ? WC_VERSION : null;
	}

	/**
	 * Returns true if the installed version of WooCommerce is 3.1 or greater.
	 *
	 * @since  3.11.0
	 *
	 * @return boolean
	 */
	public static function is_wc_version_gte_3_1() {
		return self::is_wc_version_gte( '3.1' );
	}


	/**
	 * Returns true if the installed version of WooCommerce is 2.7 or greater.
	 *
	 * @since  3.7.0
	 *
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_7() {
		return self::is_wc_version_gte( '2.7' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.6 or greater.
	 *
	 * @since  3.6.5
	 *
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_6() {
		return self::is_wc_version_gte( '2.6' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.5 or greater.
	 *
	 * @since  3.5.0
	 *
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_5() {
		return self::is_wc_version_gte( '2.5' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.4 or greater.
	 *
	 * @since  3.2.0
	 *
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_4() {
		return self::is_wc_version_gte( '2.4' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.3 or greater.
	 *
	 * @since  3.0.0
	 *
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_3() {
		return self::is_wc_version_gte( '2.3' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.2 or greater.
	 *
	 * @since  3.0.0
	 *
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_2() {
		return self::is_wc_version_gte( '2.2' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is greater than or equal to $version.
	 *
	 * @since  3.9.0
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
	 * @since  3.0.0
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
	 * Returns true if the installed version of WooCommerce is greater than or equal to $version.
	 *
	 * @since  5.0.5
	 *
	 * @param  string  $version
	 * @return boolean
	 */
	public static function is_wp_version_gt( $version ) {
		if ( ! isset( self::$is_wp_version_gt[ $version ] ) ) {
			global $wp_version;
			self::$is_wp_version_gt[ $version ] = $wp_version && version_compare( WC_CP()->plugin_version( true, $wp_version ), $version, '>' );
		}
		return self::$is_wp_version_gt[ $version ];
	}

	/**
	 * Returns true if the installed version of WooCommerce is greater than or equal to $version.
	 *
	 * @since  5.0.5
	 *
	 * @param  string  $version
	 * @return boolean
	 */
	public static function is_wp_version_gte( $version ) {
		if ( ! isset( self::$is_wp_version_gte[ $version ] ) ) {
			global $wp_version;
			self::$is_wp_version_gte[ $version ] = $wp_version && version_compare( WC_CP()->plugin_version( true, $wp_version ), $version, '>=' );
		}
		return self::$is_wp_version_gte[ $version ];
	}

	/*
	|--------------------------------------------------------------------------
	| Compatibility wrappers.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get the WC Product instance for a given product ID or post.
	 *
	 * get_product() is soft-deprecated in WC 2.2.
	 *
	 * @since  3.0.0
	 *
	 * @param  bool|int|string|WP_Post $the_product
	 * @param  array                   $args
	 * @return WC_Product
	 */
	public static function wc_get_product( $the_product = false, $args = array() ) {
		return wc_get_product( $the_product, $args );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.5 or greater.
	 *
	 * @since  3.2.0
	 *
	 * @return boolean
	 */
	public static function use_wc_ajax() {
		return apply_filters( 'woocommerce_composite_use_wc_ajax', self::is_wc_version_gte_2_4() );
	}

	/**
	 * Wrapper for wp_get_post_terms which supports ordering by parent.
	 *
	 * @since  3.5.2
	 * @param  int $product_id
	 * @param  string $taxonomy
	 * @param  array  $args
	 * @return array
	 */
	public static function wc_get_product_terms( $product_id, $attribute_name, $args ) {
		return wc_get_product_terms( $product_id, $attribute_name, $args );
	}

	/**
	 * WC_Product_Variable::get_variation_default_attribute() back-compat wrapper.
	 *
	 * @since  3.5.2
	 * @return string
	 */
	public static function wc_get_variation_default_attribute( $product, $attribute_name ) {
		return $product->get_variation_default_attribute( $attribute_name );
	}

	/**
	 * Output a list of variation attributes for use in the cart forms.
	 *
	 * @since 3.5.2
	 * @param array $args
	 */
	public static function wc_dropdown_variation_attribute_options( $args = array() ) {
		return wc_dropdown_variation_attribute_options( $args );
	}

	/**
	 * Get all product cats for a product by ID, including hierarchy.
	 *
	 * @since  3.5.2
	 * @param  int $product_id
	 * @return array
	 */
	public static function wc_get_product_cat_ids( $product_id ) {
		return wc_get_product_cat_ids( $product_id );
	}

	/**
	 * Display a WooCommerce help tip.
	 *
	 * @since  3.6.0
	 *
	 * @param  string $tip        Help tip text
	 * @return string
	 */
	public static function wc_help_tip( $tip ) {
		return wc_help_tip( $tip );
	}

	/**
	 * Get rounding precision.
	 *
	 * @since  3.6.9
	 *
	 * @return int
	 */
	public static function wc_get_rounding_precision( $price_decimals = false ) {
		if ( false === $price_decimals ) {
			$price_decimals = wc_cp_price_num_decimals();
		}
		return absint( $price_decimals ) + 2;
	}

	/**
	 * Back-compat wrapper for 'get_parent_id'.
	 *
	 * @since  3.8.0
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
	 * @since  3.8.0
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
	 * @since  3.8.0
	 *
	 * @param  object  $obj
	 * @param  string  $name
	 * @param  string  $context
	 * @return mixed
	 */
	public static function get_prop( $obj, $name, $context = 'edit' ) {
		$get_fn = 'get_' . $name;
		return is_callable( array( $obj, $get_fn ) ) ? $obj->$get_fn( $context ) : $obj->get_meta( '_wc_cp_' . $name, true );
	}

	/**
	 * Back-compat wrapper for setting CRUD object props directly.
	 *
	 * @since  3.8.0
	 *
	 * @param  WC_Product  $product
	 * @param  string      $name
	 * @param  mixed       $value
	 * @return void
	 */
	public static function set_prop( $obj, $name, $value ) {
		$set_fn = 'set_' . $name;
		if ( is_callable( array( $obj, $set_fn ) ) ) {
			$obj->$set_fn( $value );
		} else {
			$obj->add_meta_data( '_wc_cp_' . $name, $value, true );
		}
	}

	/**
	 * Back-compat wrapper for 'wc_variation_attribute_name'.
	 *
	 * @since  3.8.0
	 *
	 * @param  string  $attribute_name
	 * @return string
	 */
	public static function wc_variation_attribute_name( $attribute_name ) {
		return wc_variation_attribute_name( $attribute_name );
	}

	/**
	 * Back-compat wrapper for 'wc_get_formatted_variation'.
	 *
	 * @since  3.8.0
	 *
	 * @param  WC_Product_Variation  $variation
	 * @param  boolean               $flat
	 * @return string
	 */
	public static function wc_get_formatted_variation( $variation, $flat ) {
		return wc_get_formatted_variation( $variation, $flat );
	}

	/**
	 * Back-compat wrapper for 'WC_Product_Factory::get_product_type'.
	 *
	 * @since  3.9.0
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
	 * Back-compat wrapper for 'wc_get_price_including_tax'.
	 *
	 * @since  3.9.0
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
	 * @since  3.9.0
	 *
	 * @param  WC_Product  $product
	 * @param  array       $args
	 * @return mixed
	 */
	public static function wc_get_price_excluding_tax( $product, $args ) {
		return wc_get_price_excluding_tax( $product, $args );
	}

	/**
	 * Back-compat wrapper for 'wc_get_price_to_display'.
	 *
	 * @since  3.9.0
	 *
	 * @param  WC_Product  $product
	 * @param  array       $args
	 * @return mixed
	 */
	public static function wc_get_price_to_display( $product, $args = array() ) {
		return wc_get_price_to_display( $product, $args );
	}

	/**
	 * Back-compat wrapper for 'get_default_attributes'.
	 *
	 * @since  3.9.0
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
	 * @since  3.9.0
	 *
	 * @param  WC_Product  $product
	 * @return mixed
	 */
	public static function wc_get_stock_html( $product ) {
		return wc_get_stock_html( $product );
	}

	/**
	 * Backwards compatible logging using 'WC_Logger' class.
	 *
	 * @since  3.9.0
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
	 * Back-compat wrapper for 'get_parent_id' with fallback to 'get_id'.
	 *
	 * @since  3.9.3
	 *
	 * @param  WC_Product  $product
	 * @return mixed
	 */
	public static function get_product_id( $product ) {
		$parent_id = $product->get_parent_id();
		return $parent_id ? $parent_id : $product->get_id();
	}

	/**
	 * Back-compat wrapper for checking if a CRUD object props exists.
	 *
	 * @since  3.10.0
	 *
	 * @param  object  $obj
	 * @param  string  $name
	 * @return mixed
	 */
	public static function prop_exists( $obj, $name ) {
		$get_fn = 'get_' . $name;
		return is_callable( array( $obj, $get_fn ) ) ? true : $obj->meta_exists( '_wc_cp_' . $name );
	}

	/**
	 * Back-compat wrapper for 'is_rest_api_request'.
	 *
	 * @since  4.1.1
	 *
	 * @return boolean
	 */
	public static function is_rest_api_request() {
		return method_exists( WC(), 'is_rest_api_request' ) ? WC()->is_rest_api_request() : defined( 'REST_REQUEST' );
	}
}
