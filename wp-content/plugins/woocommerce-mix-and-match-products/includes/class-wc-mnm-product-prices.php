<?php
/**
 * WC_MNM_Product_Prices class
 *
 * @package  WooCommerce Mix and Match Products/Prices
 * @since    2.0.0
 * @version  2.3.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Price functions and hooks.
 *
 * @class    WC_MNM_Product_Prices
 */
class WC_MNM_Product_Prices {

	/**
	 * Initialize.
	 */
	public static function init() {

		// Add price filters. Needs to be filtered from a plugin, this fires on plugins_loaded, so theme's functions.php will be too late.
		if ( 'filters' === self::get_discount_method() ) {

			add_filter( 'woocommerce_product_get_price', array( __CLASS__, 'filter_get_price' ), 99, 2 );
			add_filter( 'woocommerce_product_get_sale_price', array( __CLASS__, 'filter_get_sale_price' ), 99, 2 );
			add_filter( 'woocommerce_product_get_regular_price', array( __CLASS__, 'filter_get_regular_price' ), 99, 2 );

			add_filter( 'woocommerce_product_variation_get_price', array( __CLASS__, 'filter_get_price' ), 99, 2 );
			add_filter( 'woocommerce_product_variation_get_sale_price', array( __CLASS__, 'filter_get_sale_price' ), 99, 2 );
			add_filter( 'woocommerce_product_variation_get_regular_price', array( __CLASS__, 'filter_get_regular_price' ), 99, 2 );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Class methods.
	|--------------------------------------------------------------------------
	*/

	/**
	 * A non-strict way to tell if a product's prices are being altered due to the presence of a parent "container".
	 *
	 * @param  WC_Product  $product
	 * @param  string      $context
	 * @return boolean
	 */
	public static function is_child_pricing_context( $product, $context = 'any' ) {
		return property_exists( $product, 'mnm_child_item' ) && $product->mnm_child_item instanceof WC_MNM_Child_Item;
	}

	/**
	 * Method to use for calculating cart item discounts. Values: 'filters' | 'props'
	 *
	 * @return string  $method
	 */
	public static function get_discount_method() {
		/**
		 * 'wc_mnm_price_discount_method' filter.
		 *
		 * @param  string  $method  Method to use for calculating item discounts. Values: 'filters' | 'props'.
		 */
		$discount_method = apply_filters( 'wc_mnm_price_discount_method', 'filters' );

		return in_array( $discount_method, array( 'filters', 'props' ) ) ? $discount_method : 'filters';
	}

	/**
	 * Returns the incl/excl tax coefficients for calculating prices incl/excl tax on the client side.
	 *
	 * @param  WC_Product  $product
	 * @return array
	 */
	public static function get_tax_ratios( $product ) {

		// Filters the 'woocommerce_price_num_decimals' option to use the internal WC rounding precision.
		add_filter( 'option_woocommerce_price_num_decimals', array( __CLASS__, 'extend_rounding_precision' ) );

		$ref_price      = 1000.0;
		$ref_price_incl = wc_get_price_including_tax( $product, array( 'qty' => 1, 'price' => $ref_price ) );
		$ref_price_excl = wc_get_price_excluding_tax( $product, array( 'qty' => 1, 'price' => $ref_price ) );

		// Reset applied filters to the 'woocommerce_price_num_decimals' option.
		remove_filter( 'option_woocommerce_price_num_decimals', array( __CLASS__, 'extend_rounding_precision' ) );

		return array(
			'incl' => $ref_price_incl / $ref_price,
			'excl' => $ref_price_excl / $ref_price
		);
	}

	/**
	 * Discounted price getter.
	 *
	 * @param  mixed  $price
	 * @param  mixed  $discount
	 * @return mixed
	 */
	public static function get_discounted_price( $price, $discount ) {

		$discounted_price = $price;

		if ( ! empty( $price ) && ! empty( $discount ) ) {
			$discounted_price = round( ( double ) $price * ( 100 - $discount ) / 100, wc_get_rounding_precision() );
		}

		return $discounted_price;
	}

	/**
	 * Calculates product prices.
	 *
	 * @param  WC_Product  $product
	 * @param  array       $args
	 * @return mixed
	 */
	public static function get_product_price( $product, $args ) {
		$defaults = array(
			'price' => '',
			'qty'   => 1,
			'calc'  => ''
		);

		$args  = wp_parse_args( $args, $defaults );
		$price = $args[ 'price' ];
		$qty   = $args[ 'qty' ];
		$calc  = $args[ 'calc' ];

		if ( $price ) {

			if ( 'display' === $calc ) {
				$calc = 'excl' === get_option( 'woocommerce_tax_display_shop' ) ? 'excl_tax' : 'incl_tax';
			}

			if ( 'incl_tax' === $calc ) {
				$price = wc_get_price_including_tax( $product, array( 'qty' => $qty, 'price' => $price ) );
			} elseif ( 'excl_tax' === $calc ) {
				$price = wc_get_price_excluding_tax( $product, array( 'qty' => $qty, 'price' => $price ) );
			} else {
				$price = $price * $qty;
			}
		}

		return $price;

	}

	/**
	 * Get rounding precision.
	 * Needed to avoid an infinite loop when filtering.
	 *
	 * @since  1.4.0
	 *
	 * @return int
	 */
	public static function extend_rounding_precision( $price_decimals = false ) {
		if ( false === $price_decimals ) {
			$price_decimals = wc_get_price_decimals();
		}
		return absint( $price_decimals ) + 2;
	}

	/*
	|--------------------------------------------------------------------------
	| Callbacks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Filter get_price() calls for child products to include discounts.
	 *
	 * @param  double      $price
	 * @param  WC_Product  $product
	 * @return double
	 */
	public static function filter_get_price( $price, $product ) {

		if ( self::is_child_pricing_context( $product ) ) {

			if ( '' === $price ) {
				return $price;
			}

			if ( ! $product->mnm_child_item->is_priced_individually() ) {
				return 0;
			}

			$discount = $product->mnm_child_item->get_discount();

			if ( $discount ) {
				if ( $product->mnm_child_item->is_discounted_from_regular_price() ) {
					do_action( 'wc_mnm_child_item_get_unfiltered_regular_price_start' );
					$price = $product->get_regular_price();
					do_action( 'wc_mnm_child_item_get_unfiltered_regular_price_end' );
				}
				$price = self::get_discounted_price( $price, $discount );
			}

		}
		return $price;
	}

	/**
	 * Filter get_regular_price() calls for child products to include discounts.
	 *
	 * @param  double      $price
	 * @param  WC_Product  $product
	 * @return double
	 */
	public static function filter_get_regular_price( $regular_price, $product ) {

		if ( self::is_child_pricing_context( $product ) ) {

			if ( ! $product->mnm_child_item->is_priced_individually() ) {
				return 0;
			}
		}

		return $regular_price;
	}

	/**
	 * Filter get_sale_price() calls for child products to include discounts.
	 *
	 * @param  double      $price
	 * @param  WC_Product  $product
	 * @param  string      $context
	 * @return double
	 */
	public static function filter_get_sale_price( $sale_price, $product, $context = '' ) {

		if ( self::is_child_pricing_context( $product ) ) {

			if ( ! $product->mnm_child_item->is_priced_individually() ) {
				return 0;
			}

			$discount = $product->mnm_child_item->get_discount();

			if ( $discount ) {
				$sale_price = self::get_discounted_price( $product->mnm_child_item->is_discounted_from_regular_price() || '' === $sale_price ? $product->get_regular_price() : $sale_price, $discount );
			}

		}
		return $sale_price;
	}

}
WC_MNM_Product_Prices::init();
