<?php
/**
 * Helper Functions
 *
 * @package  WooCommerce Mix and Match Products/Helpers
 * @since    1.0.0
 * @version  2.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Formerly the WC_Mix_and_Match_Helpers class.
 * Renamed in 2.0.0
 */
class_alias( 'WC_MNM_Helpers', 'WC_Mix_and_Match_Helpers' );

/**
 * WC_MNM_Helpers Class.
 *
 * Mix and Match order caching helper functions.
 */
class WC_MNM_Helpers {

	/**
	 * Runtime cache for simple storage.
	 *
	 * @var array $cache
	 */
	public static $cache = array();

	/**
	 * Simple runtime cache getter.
	 *
	 * @param  string  $key
	 * @param  string  $group_key
	 * @return mixed
	 */
	public static function cache_get( $key, $group_key = '' ) {

		$value = null;

		if ( $group_key ) {

			if ( $group_id = self::cache_get( $group_key . '_id' ) ) {
				$value = self::cache_get( $group_key . '_' . $group_id . '_' . $key );
			}

		} elseif ( isset( self::$cache[ $key ] ) ) {
			$value = self::$cache[ $key ];
		}

		return $value;
	}

	/**
	 * Simple runtime cache setter.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  string  $group_key
	 */
	public static function cache_set( $key, $value, $group_key = '' ) {

		if ( $group_key ) {

			if ( null === ( $group_id = self::cache_get( $group_key . '_id' ) ) ) {
				$group_id = md5( $group_key );
				self::cache_set( $group_key . '_id', $group_id );
			}

			self::$cache[ $group_key . '_' . $group_id . '_' . $key ] = $value;

		} else {
			self::$cache[ $key ] = $value;
		}
	}

	/**
	 * Simple runtime cache unsetter.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  string  $group_key
	 */
	public static function cache_delete( $key, $group_key = '' ) {

		if ( $group_key ) {

			if ( $group_id = self::cache_get( $group_key . '_id' ) ) {
				self::cache_delete( $group_key . '_' . $group_id . '_' . $key );
			}

		} elseif ( isset( self::$cache[ $key ] ) ) {
			unset( self::$cache[ $key ] );
		}
	}

	/**
	 * Product types supported by the plugin.
	 * You can dynamically attach these product types to Mix and Match Product.
	 *
	 * @static
	 * @since  1.1.6
	 * @return array
	 */
	public static function get_supported_product_types() {
		/**
		 * Supported types.
		 *
		 * @param  array
		 */
		return apply_filters( 'wc_mnm_supported_products', array( 'simple', 'variation' ) );
	}

	/**
	 * Check if child is supported type.
	 *
	 * @since  1.10.0
	 *
	 * @param  WC_Product $product
	 * @return  boolean
	 */
	public static function is_child_supported_product_type( $product ) {
		return $product instanceOf WC_Product && ( in_array( $product->get_type(), self::get_supported_product_types() ) || ( $product->is_type( 'variation' ) && WC_MNM_Core_Compatibility::has_all_attributes_set( $product ) ) );
	}

	/**
	 * Format a product title incl qty.
	 *
	 * @since 1.11.4
	 *
	 * @param  string  $title
	 * @param  string  $qty
	 * @return string
	 */
	public static function format_product_title( $title, $qty = '', $args = array() ) {

		$args = wp_parse_args(
            $args,
            array(
			'title_first' => true,
            ) 
        );

		$title_string = $title;

		if ( $qty ) {
			if ( $args[ 'title_first' ] ) {
				$title_string = sprintf( esc_html_x( '%1$s &times; %2$d', '[Frontend] product title x quantity', 'woocommerce-mix-and-match-products' ), $title_string, $qty );
			} else {
				$title_string = sprintf( esc_html_x( '%1$d &times; %2$s', '[Frontend] quantity x product title', 'woocommerce-mix-and-match-products' ), $qty, $title_string );
			}
		}

		return $title_string;
	}

	
	/**
	 * Recursive version of 'urlencode' for multidimensional assosciative arrays.
	 * 
	 * Hat tip to Composite Products.
	 *
	 * @since  2.2.0
	 *
	 * @param  function  $array
	 * @param  array     $escaped_array
	 * @return array
	 */
	public static function urlencode_recursive( $array ) {

		$escaped_array = array();

		foreach ( $array as $key => $value ) {

			if ( is_array( $value ) ) {
				$data = self::urlencode_recursive( $value );
			} else {
				$data = urlencode( $value );
			}

			$escaped_array[ urlencode( $key ) ] = $data;
		}

		return $escaped_array;
	}


	/*-----------------------------------------------------------------------------------*/
	/* Deprecated Functions */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Helper method to get the version of the currently installed WooCommerce
	 *
	 * @since 1.0.5
	 * @deprecated 1.2.0
	 * @see WC_MNM_Core_Compatibility::get_wc_version()
	 *
	 * @return string woocommerce version number or null
	 */
	public static function get_wc_version() {
		wc_deprecated_function( 'WC_MNM_Helpers::get_wc_version()', '1.2.0', 'WC_MNM_Core_Compatibility::get_wc_version()' );
		return WC_MNM_Core_Compatibility::get_wc_version();
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.4 or greater
	 *
	 * @since 1.0.5
	 * @deprecated 1.2.0
	 * @see WC_MNM_Core_Compatibility::get_wc_version()
	 *
	 * @return bool true if the installed version of WooCommerce is 2.2 or greater
	 */
	public static function is_wc_version_gte_2_4() {
		wc_deprecated_function( 'WC_MNM_Helpers::is_wc_version_gte_2_4()', '1.2.0', 'WC_MNM_Core_Compatibility::is_wc_version_gte( "2.4.0" )' );
		return WC_MNM_Core_Compatibility::is_wc_version_gte( '2.4.0' );
	}

	/**
	 * Calculates child product prices incl. or excl. tax depending on the 'woocommerce_tax_display_shop' setting.
	 * For WC < 3.0.
	 *
	 * @since 1.0.5
	 * @deprecated 1.3.0
	 * @see wc_get_price_to_display()
	 *
	 * @param  WC_Product   $product    the product
	 * @param  double       $price      the product price
	 * @return double                   modified product price incl. or excl. tax
	 */
	public static function get_product_display_price( $product, $price ) {
		wc_deprecated_function( 'WC_MNM_Helpers::get_product_display_price()', '1.3.0', 'wc_get_price_to_display()' );
		return wc_get_price_to_display( $product, array( 'price' => $price ) );
	}

	/**
	 * Get formatted variation data with WC < 2.4 back compat and proper formatting of text-based attribute names.
	 *
	 * @since 1.0.4
	 * @deprecated 1.2.0
	 * @see wc_get_formatted_variation()
	 *
	 * @param  WC_Product_Variation  $variation   the variation
	 * @return string                             formatted attributes
	 */
	public static function get_formatted_variation_attributes( $variation, $flat = false ) {
		wc_deprecated_function( 'WC_MNM_Helpers::get_formatted_variation_attributes()', '1.2.0', 'wc_get_formatted_variation()' );
		return wc_get_formatted_variation( $variation, $flat );
	}

} //end class
