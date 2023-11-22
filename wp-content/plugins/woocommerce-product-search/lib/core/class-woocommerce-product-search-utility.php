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

use com\itthinx\woocommerce\search\engine\Base;
use com\itthinx\woocommerce\search\engine\Cache;

/**
 * Utility methods.
 */
class WooCommerce_Product_Search_Utility {

	const CACHE_GROUP = 'ixwps_uty';

	/**
	 * Float conversion.
	 *
	 * @since 5.0.0 moved here
	 *
	 * @param string|float|null $x to convert
	 *
	 * @return float|null converted or null
	 */
	public static function to_float( $x ) {

		if ( $x !== null && !is_float( $x ) && is_string( $x ) ) {
			$locale = localeconv();
			$decimal_characters = array_unique( array( wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'], '.', ',' ) );
			$x = str_replace( $decimal_characters, '.', trim( $x ) );
			$x = preg_replace( '/[^0-9\.,-]/', '', $x );
			$i = strrpos( $x, '.' );
			if ( $i !== false ) {
				$x = ( $i > 0 ? str_replace( '.', '', substr( $x, 0, $i ) ) : '' ) . '.' . ( $i < strlen( $x ) ? str_replace( '.', '', substr( $x, $i + 1 ) ) : '' );
			}
			if ( strlen( $x ) > 0 ) {
				$x = floatval( $x );
			} else {
				$x = null;
			}
		}
		return $x;
	}

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

		$is_shop =
			is_shop() ||
			is_product_taxonomy() ||
			( $is_widgets_block_editor && $is_widgets_admin ) ||
			$is_customizer;

		if ( !$is_shop ) {
			global $post;
			if ( !empty( $post ) && isset( $post->post_content ) ) {
				if (
					has_shortcode( $post->post_content, 'woocommerce_product_filter_products' ) ||
					has_shortcode( $post->post_content, 'products' ) ||
					has_block( 'woocommerce-product-search/woocommerce-product-filter-products' )

				) {
					$is_shop = true;
				}
			}
		}

		$result = apply_filters(
			'woocommerce_product_search_is_shop',
			$is_shop
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

	public static function has_on_sale() {
		$has_on_sale = false;
		$on_sale_ids = self::get_product_ids_on_sale( array( 'limit' => 1 ) );
		if ( count( $on_sale_ids ) > 0 ) {
			$has_on_sale = true;
		}
		return $has_on_sale;
	}

	/**
	 * Return product IDs for products on sale, published and not hidden from the catalog.
	 *
	 * @since 4.1.0
	 *
	 * @return int[]
	 */
	public static function get_product_ids_on_sale( $args = array() ) {

		$cache_key = 'GPIDSOS_' . md5( json_encode( $args ) );
		$cache = Cache::get_instance();
		$product_ids = $cache->get( $cache_key, self::CACHE_GROUP );
		if ( $product_ids === null ) {
			$on_sale_products = self::get_on_sale_products( $args );

			$product_ids = array();
			$ids = array_column( $on_sale_products, 'id' );
			foreach ( $ids as $id ) {
				$product_ids[] = intval( $id );
			}
			$parent_ids = array_column( $on_sale_products, 'parent_id' );
			foreach ( $parent_ids as $id ) {
				$id = intval( $id );
				if ( $id > 0 ) {
					$product_ids[] = $id;
				}
			}

			$product_ids = array_keys( array_flip( $product_ids ) );

			$cache->set( $cache_key, $product_ids, self::CACHE_GROUP, WooCommerce_Product_Search_Service::get_cache_lifetime() );
		}
		return $product_ids;
	}

	public static function get_on_sale_products( $args = array() ) {

		global $wpdb, $wp_query;

		$limit = null;
		if ( isset( $args['limit'] ) ) {
			$limit = max( 0, intval( $args['limit'] ) );
		}
		if ( $limit === 0 ) {
			$limit = null;
		}

		$is_product_search = false;
		if ( $wp_query->is_main_query() ) {

			$is_product_search = isset( $_REQUEST[Base::SEARCH_TOKEN] );

			if ( !$is_product_search ) {
				$post_type = $wp_query->get( 'post_type', false );
				if (
					is_string( $post_type ) && $post_type === 'product' ||
					is_array( $post_type ) && in_array( 'product', $post_type )
				) {
					$is_product_search =
						$wp_query->is_search() ||
						$wp_query->get( 'product_search', false );
				}
			}
		}

		$exclude_term_ids            = array();
		$outofstock_join             = '';
		$outofstock_where            = '';
		$product_visibility_term_ids = wc_get_product_visibility_term_ids();

		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && $product_visibility_term_ids['outofstock'] ) {
			$exclude_term_ids[] = $product_visibility_term_ids['outofstock'];
			$outofstock_join  = " LEFT JOIN ( SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN ( " . implode( ',', array_map( 'absint', $exclude_term_ids ) ) . ' ) ) AS exclude_join ON exclude_join.object_id = id';
			$outofstock_where = ' AND exclude_join.object_id IS NULL';
		}

		$visibility_join = '';
		$visibility_where = '';
		$parent_visibility_join = '';
		$parent_visibility_where = '';

		$visibility_term_ids = array();
		if ( $is_product_search ) {
			$visibility_term_ids[] = $product_visibility_term_ids['exclude-from-search'];
		} else if ( self::is_shop() ) {
			$visibility_term_ids[] = $product_visibility_term_ids['exclude-from-catalog'];
		}
		if ( count( $visibility_term_ids ) > 0 ) {
			$visibility_join  = " LEFT JOIN ( SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN ( " . implode( ',', array_map( 'absint', $visibility_term_ids ) ) . ' ) ) AS visibility_join ON visibility_join.object_id = posts.ID';
			$visibility_where = ' AND visibility_join.object_id IS NULL';
			$parent_visibility_join  = " LEFT JOIN ( SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN ( " . implode( ',', array_map( 'absint', $visibility_term_ids ) ) . ' ) ) AS parent_visibility_join ON parent_visibility_join.object_id = posts.post_parent';
			$parent_visibility_where = ' AND parent_visibility_join.object_id IS NULL';
		}

		$query =
			"SELECT posts.ID as id, posts.post_parent as parent_id " .
			"FROM {$wpdb->posts} AS posts " .
			"INNER JOIN {$wpdb->wc_product_meta_lookup} AS lookup ON posts.ID = lookup.product_id " .
			"$outofstock_join " .
			"$visibility_join " .
			"$parent_visibility_join " .
			"WHERE posts.post_type IN ( 'product', 'product_variation' ) " .
			"AND posts.post_status = 'publish' " .
			"AND lookup.onsale = 1 " .
			"$outofstock_where " .
			"$visibility_where " .
			"$parent_visibility_where " .
			"AND posts.post_parent NOT IN ( " .
				"SELECT ID FROM `$wpdb->posts` as posts " .
				"WHERE posts.post_type = 'product' " .
				"AND posts.post_parent = 0 " .
				"AND posts.post_status != 'publish' " .
			") " .
			"GROUP BY posts.ID";
			if ( $limit !== null ) {
				$query .= ' LIMIT ' . intval( $limit );
			}

		$cache_key = 'PIDSOS_' . md5( $query );
		$cache = Cache::get_instance();
		$results = $cache->get( $cache_key, self::CACHE_GROUP );
		if ( $results === null ) {
			$results = $wpdb->get_results( $query );

			$n = count( $results );
			for ( $i = 0; $i < $n; $i++ ) {
				$results[$i]->id = intval( $results[$i]->id );
				$results[$i]->parent_id = intval( $results[$i]->parent_id );
			}
			$cache->set( $cache_key, $results, self::CACHE_GROUP, WooCommerce_Product_Search_Service::get_cache_lifetime() );

		}

		return $results;

	}

	/**
	 * Apply intval to all values in the array.
	 *
	 * @since 4.9.0
	 *
	 * @param array $values
	 */
	public static function intval_map( &$values ) {

		foreach ( $values as $key => $value ) {
			$values[$key] = intval( $value );
		}
	}

	/**
	 * Reduce to unique values in the array.
	 *
	 * @since 4.9.0
	 *
	 * @param array $values
	 */
	public static function unique_map( &$values ) {

		$values = array_keys( array_flip( $values ) );
	}

	/**
	 * Apply intval and reduce to unique values in the array.
	 *
	 * @since 4.9.0
	 *
	 * @param array $values
	 */
	public static function unique_intval_map( &$values ) {

		foreach ( $values as $key => $value ) {
			$values[$key] = intval( $value );
		}
		$values = array_keys( array_flip( $values ) );
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
