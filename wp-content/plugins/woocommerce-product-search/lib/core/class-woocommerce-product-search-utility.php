<?php
/**
 * class-woocommerce-product-search-utility.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 2.9.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Utility methods.
 */
class WooCommerce_Product_Search_Utility {

	/**
	 * Checks the $value and returns a valid dimension string or '' if $value is not recognized as valid.
	 *
	 * @access private
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public static function get_css_unit( $value ) {
		if ( ( $matched = preg_match( '/(\d*\.?\d+)(\s)*(px|mm|cm|in|pt|pc|em|ex|ch|rem|vw|vh)?/i', $value, $matches ) ) === 1 ) {
			$number = '';
			$units = '';
			if ( isset( $matches[1] ) ) {
				$number = floatval( $matches[1] );
			}
			if ( isset( $matches[3] ) ) {
				$units = $matches[3];
			}
			$value = $number . $units;
		} else {
			$value = '';
		}
		return $value;
	}
}
