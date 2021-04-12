<?php
/**
 * WC_CP_Helpers class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper functions.
 *
 * @class    WC_CP_Helpers
 * @version  7.0.7
 */
class WC_CP_Helpers {

	/**
	 * General-purpose runtime key/value cache.
	 *
	 * @var array
	 */
	private static $cache = array();

	/**
	 * Flag indicating whether 'get_extended_price_precision' is being used to filter WC decimals.
	 *
	 * @var boolean
	 */
	private static $filtering_price_decimals = false;

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
	 * @return void
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
	 * @return void
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
	 * Simple runtime group cache invalidator.
	 *
	 * @since  7.1.4
	 *
	 * @param  string  $key
	 * @param  string  $group_key
	 * @param  mixed   $value
	 * @return void
	 */
	public static function cache_invalidate( $group_key = '' ) {

		if ( '' === $group_key ) {
			self::$cache = array();
		} elseif ( $group_id = self::cache_get( $group_key . '_id' ) ) {
			$group_id = md5( $group_key . '_' . $group_id );
			self::cache_set( $group_key . '_id', $group_id );
		}
	}

	/**
	 * True when processing a FE request.
	 *
	 * @return boolean
	 */
	public static function is_front_end() {
		$is_fe = ( ! is_admin() ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX );
		return $is_fe;
	}

	/**
	 * Filters the 'woocommerce_price_num_decimals' option to use the internal WC rounding precision.
	 */
	public static function extend_price_display_precision() {
		add_filter( 'wc_get_price_decimals', array( __CLASS__, 'get_extended_price_precision' ) );
	}

	/**
	 * Reset applied filters to the 'woocommerce_price_num_decimals' option.
	 */
	public static function reset_price_display_precision() {
		remove_filter( 'wc_get_price_decimals', array( __CLASS__, 'get_extended_price_precision' ) );
	}

	/**
	 * Get extended rounding precision.
	 *
	 * @since  8.0.2
	 *
	 * @param  int  $decimals
	 * @return int
	 */
	public static function get_extended_price_precision( $decimals = null ) {

		// Prevent infinite loops through 'wc_cp_price_num_decimals'.
		if ( ! is_null( $decimals ) && self::$filtering_price_decimals ) {
			return $decimals;
		}

		self::$filtering_price_decimals = true;
		$decimals = wc_cp_price_num_decimals( 'extended' );
		self::$filtering_price_decimals = false;

		return $decimals;
	}

	/**
	 * Loads variation IDs for a given variable product.
	 *
	 * @param  WC_Product_Variable|int  $product
	 * @return array
	 */
	public static function get_product_variations( $product ) {

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( ! $product || ! $product->is_type( 'variable' ) ) {
			return false;
		}

		return $product->get_children();
	}

	/**
	 * Loads variation descriptions and ids for a given variable product.
	 *
	 * @param  WC_Product_Variable|int  $product
	 * @param  string                   $format
	 * @return array
	 */
	public static function get_product_variation_descriptions( $product, $format = 'flat' ) {

		$variation_descriptions = array();
		$variations             = self::get_product_variations( $product );

		if ( empty( $variations ) ) {
			return $variation_descriptions;
		}

		foreach ( $variations as $variation_id ) {

			$variation_description = self::get_product_variation_title( $variation_id, $format );

			if ( ! $variation_description ) {
				continue;
			}

			$variation_descriptions[ $variation_id ] = $variation_description;
		}

		return $variation_descriptions;
	}

	/**
	 * Return a formatted variation title.
	 *
	 * @param  WC_Product_Variation|int  $variation
	 * @param  string                    $format
	 *
	 * @return string
	 */
	public static function get_product_variation_title( $variation, $format = 'flat' ) {

		if ( ! is_object( $variation ) ) {
			$variation = wc_get_product( $variation );
		}

		if ( ! $variation ) {
			return false;
		}

		if ( 'core' === $format || true === $format ) {

			$title = $variation->get_formatted_name();

		} else {

			$description = wc_get_formatted_variation( $variation, true );

			$title = $variation->get_title();
			$sku   = $variation->get_sku();
			$id    = $variation->get_id();

			if ( $sku ) {
				$identifier = $sku;
			} else {
				$identifier = '#' . $id;
			}

			$title = self::format_product_title( $title, $identifier, $description );
		}

		return $title;
	}

	/**
	 * Return a formatted product title.
	 *
	 * @param  WC_Product|int  $product
	 * @param  string          $title
	 * @param  string          $meta
	 * @return string
	 */
	public static function get_product_title( $product, $title = '', $meta = '' ) {

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( ! $product ) {
			return false;
		}

		$title = $title ? $title : $product->get_title();
		$sku   = $product->get_sku();
		$id    = $product->get_id();

		if ( $sku ) {
			$identifier = $sku;
		} else {
			$identifier = '#' . $id;
		}

		return self::format_product_title( $title, $identifier, $meta );
	}

	/**
	 * Format a product title.
	 *
	 * @param  string  $title
	 * @param  string  $identifier
	 * @param  string  $meta
	 * @param  string  $paren
	 * @return string
	 */
	public static function format_product_title( $title, $identifier = '', $meta = '', $paren = true ) {

		if ( $identifier && $meta ) {
			if ( $paren ) {
				$title = sprintf( _x( '%1$s (%2$s) &ndash; %3$s', 'product title followed by sku in parenthesis and meta', 'woocommerce-composite-products' ), $title, $identifier, $meta );
			} else {
				$title = sprintf( _x( '%1$s &ndash; %2$s &ndash; %3$s', 'product title followed by sku and meta', 'woocommerce-composite-products' ), $title, $identifier, $meta );
			}
		} elseif ( $identifier ) {
			if ( $paren ) {
				$title = sprintf( _x( '%1$s (%2$s)', 'product title followed by sku in parenthesis', 'woocommerce-composite-products' ), $title, $identifier );
			} else {
				$title = sprintf( _x( '%1$s &ndash; %2$s', 'product title followed by sku', 'woocommerce-composite-products' ), $title, $identifier );
			}
		} elseif ( $meta ) {
			if ( $paren ) {
				$title = sprintf( _x( '%1$s (%2$s)', 'product title followed by meta in parenthesis', 'woocommerce-composite-products' ), $title, $meta );
			} else {
				$title = sprintf( _x( '%1$s &ndash; %2$s', 'product title followed by meta', 'woocommerce-composite-products' ), $title, $meta );
			}
		}

		return $title;
	}

	/**
	 * Format prices without html content.
	 *
	 * @param  mixed  $price
	 * @param  array  $args
	 * @return string
	 */
	public static function format_raw_price( $price, $args = array() ) {

		$return          = '';
		$num_decimals    = wc_cp_price_num_decimals();
		$currency        = isset( $args['currency'] ) ? $args['currency'] : '';
		$currency_symbol = get_woocommerce_currency_symbol( $currency );
		$decimal_sep     = wc_cp_price_decimal_sep();
		$thousands_sep   = wc_cp_price_thousand_sep();

		$price = apply_filters( 'raw_woocommerce_price', floatval( $price ) );
		$price = apply_filters( 'formatted_woocommerce_price', number_format( $price, $num_decimals, $decimal_sep, $thousands_sep ), $price, $num_decimals, $decimal_sep, $thousands_sep );

		if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $num_decimals > 0 ) {
			$price = wc_trim_zeros( $price );
		}

		$return = sprintf( get_woocommerce_price_format(), $currency_symbol, $price );

		return $return;
	}

	/**
	 * Version of 'in_array' operating on the values of an input array.
	 *
	 * @since  3.9.0
	 *
	 * @param  array   $array
	 * @param  mixed   $key
	 * @param  mixed   $value
	 * @return boolean
	 */
	public static function in_array_key( $array, $key, $value ) {
		if ( ! empty( $array ) && is_array( $array ) && ! empty( $array[ $key ] ) && is_array( $array[ $key ] ) && in_array( $value, $array[ $key ] ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Recursive version of 'urlencode' for multidimensional assosciative arrays.
	 *
	 * @since  3.14.0
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

	/**
	 * Get a new product instance, preserving runtime meta from another one.
	 *
	 * @since  7.0.7
	 *
	 * @param  WC_Product  $product
	 * @return WC_Product
	 */
	public static function get_product_preserving_meta( $product ) {

		$clone = wc_get_product( $product->get_id() );

		$meta_data_to_set = array();

		foreach ( $product->get_meta_data() as $meta ) {
			if ( ! isset( $meta->id ) ) {
				$meta_data_to_set[] = array(
					'key'   => $meta->key,
					'value' => $meta->value
				);
			}
		}

		foreach ( $meta_data_to_set as $meta ) {
			$clone->add_meta_data( $meta[ 'key' ], $meta[ 'value' ], true );
		}

		return $clone;
	}
}
