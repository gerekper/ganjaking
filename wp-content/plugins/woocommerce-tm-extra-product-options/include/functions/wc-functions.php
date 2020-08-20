<?php
/**
 * Required WooCommerce functions
 *
 * @package Extra Product Options/Functions
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_get_price_decimal_separator' ) ) {
	/**
	 * Return the decimal separator for prices
	 *
	 * @since  2.3
	 * @return string
	 */
	function wc_get_price_decimal_separator() {
		$separator = apply_filters( 'wc_get_price_decimal_separator', get_option( 'woocommerce_price_decimal_sep' ) );

		return $separator ? stripslashes( $separator ) : '.';
	}
}

if ( ! function_exists( 'wc_get_price_thousand_separator' ) ) {
	/**
	 * Return the thousand separator for prices
	 *
	 * @since  2.3
	 * @return string
	 */
	function wc_get_price_thousand_separator() {
		return stripslashes( apply_filters( 'wc_get_price_thousand_separator', get_option( 'woocommerce_price_thousand_sep' ) ) );
	}
}

if ( ! function_exists( 'wc_get_price_decimals' ) ) {
	/**
	 * Return the number of decimals after the decimal point
	 *
	 * @since  2.3
	 * @return int
	 */
	function wc_get_price_decimals() {
		$decimals = apply_filters( 'wc_get_price_decimals', get_option( 'woocommerce_price_num_decimals', 2 ) );

		return absint( $decimals );
	}
}
if ( ! function_exists( 'wc_get_rounding_precision' ) ) {
	/**
	 * Get rounding precision for internal WC calculations
	 * Will increase the precision of wc_get_price_decimals by 2 decimals, 
	 * unless WC_ROUNDING_PRECISION is set to a higher number.
	 *
	 * @since 2.6.3
	 * @return int
	 */
	function wc_get_rounding_precision() {
		$precision = wc_get_price_decimals() + 2;
		if ( defined( 'WC_ROUNDING_PRECISION' ) && absint( WC_ROUNDING_PRECISION ) > $precision ) {
			$precision = absint( WC_ROUNDING_PRECISION );
		}

		return $precision;
	}
}
if ( ! function_exists( 'wc_tax_enabled' ) ) {

	/**
	 * Are store-wide taxes enabled?
	 *
	 * @return bool
	 */
	function wc_tax_enabled() {
		return apply_filters( 'wc_tax_enabled', get_option( 'woocommerce_calc_taxes' ) === 'yes' );
	}
}

if ( ! function_exists( 'wc_prices_include_tax' ) ) {

	/**
	 * Are prices inclusive of tax?
	 *
	 * @return bool
	 */
	function wc_prices_include_tax() {
		return wc_tax_enabled() && 'yes' === get_option( 'woocommerce_prices_include_tax' );
	}
}
