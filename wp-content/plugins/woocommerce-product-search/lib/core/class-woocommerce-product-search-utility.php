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
	 * Whether we are on a page that is considered part of the shop.
	 *
	 * @since 4.0.0
	 *
	 * @return boolean
	 */
	public static function is_shop() {

		global $current_screen, $wp_customize;

		$is_widgets_block_editor = false;
		$is_widgets_admin = false;
		$is_customizer = false;
		if ( function_exists( 'wp_use_widgets_block_editor' ) ) {
			$is_widgets_block_editor = wp_use_widgets_block_editor();
		}
		if ( $is_widgets_block_editor ) {
			if ( is_admin() && !empty( $current_screen ) ) {
				if ( isset( $current_screen->id ) && $current_screen->id === 'widgets' ) {
					$is_widgets_admin = true;
				}
			} else if ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) {

				$is_widgets_admin = strpos( wp_get_referer(), admin_url( 'widgets.php' ) ) !== false;
			}
		}

		if ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) {
			$is_customizer = strpos( wp_get_referer(), admin_url( 'customize.php' ) ) !== false;
		}

		$result = apply_filters(
			'woocommerce_product_search_is_shop',
			is_shop() ||
			is_product_taxonomy() ||
			( $is_widgets_block_editor && $is_widgets_admin ) ||
			$is_customizer
		);
		$result = boolval( $result );
		return $result;
	}

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

	/**
	 * Return the boolean value corresponding to the input value.
	 *
	 * @param string $value
	 *
	 * @since 4.0.0
	 *
	 * @return boolean
	 */
	public static function get_input_yn( &$value ) {
		$result = false;
		if ( !empty( $value ) ) {
			$test = $value;
			if ( is_string( $test ) ) {
				$test = strtolower( $test );
				$test = trim( $test );
			}
			switch ( $test ) {
				case true:
				case 'true':
				case 'yes':
					$result = true;
					break;
				case false:
				case 'false':
				case 'no':
				case '':
					$result = 'yes';
					break;
				default:
					$result = 'no';
			}
		}
		return $result;
	}

	/**
	 * Apply safex to script.
	 *
	 * @param string $script
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public static function safex( $script ) {

		$safex = '';
		if ( is_string( $script ) && strlen( $script ) > 0 ) {
			$nl = '';
			if ( WPS_DEBUG_SCRIPTS ) {
				$nl = "\n";
				$safex .= "\n";
				$safex .= '<!-- SAFEX START -->';
				$safex .= "\n";
				$script =
					"\n" .
					'<!-- SAFEX SCRIPT START -->' .
					"\n" .
					$script .
					"\n" .
					'<!-- SAFEX SCRIPT END -->' .
					"\n";
			}
			$safex .= '( function() {' . $nl;
			$safex .= 'const f = function() {' . $nl;
			$safex .= $script;
			$safex .= '};' . $nl;
			$safex .= 'if ( document.readyState === "complete" ) {' . $nl;
			$safex .= 'f();' . $nl;
			$safex .= '} else {' . $nl;
			$safex .= 'document.addEventListener(' . $nl;
			$safex .= '"readystatechange",' . $nl;
			$safex .= 'function( event ) {' . $nl;
			$safex .= 'if ( event.target.readyState === "complete" ) {' . $nl;
			$safex .= 'f();' . $nl;
			$safex .= '}' . $nl;
			$safex .= '}' . $nl;
			$safex .= ');' . $nl;
			$safex .= '}' . $nl;
			$safex .= '} )();' . $nl;
			if ( WPS_DEBUG_SCRIPTS ) {
				$safex .= "\n";
				$safex .= '<!-- SAFEX END -->';
				$safex .= "\n";
			}
		}
		return $safex;
	}
}

/**
 * Whether we are on a page that is considered part of the shop.
 *
 * @since 4.0.0
 *
 * @return boolean
 */
function woocommerce_product_search_is_shop() {
	return WooCommerce_Product_Search_Utility::is_shop();
}

/**
 * Return the boolean value corresponding to the input value.
 *
 * @param string $value
 *
 * @since 4.0.0
 *
 * @return boolean
 */
function woocommerce_product_search_input_yn( &$value ) {
	return WooCommerce_Product_Search_Utility::get_input_yn( $value );
}

/**
 * Apply safex to script.
 *
 * @param string $script
 *
 * @since 4.0.0
 *
 * @return string
 */
function woocommerce_product_search_safex( $script ) {
	return WooCommerce_Product_Search_Utility::safex( $script );
}
