<?php
/**
 * class-woocommerce-product-search-compat-shortcodes.php
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
 * @since 3.2.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !defined( 'WPS_SHORTCODES_PRODUCTS_FILTER' ) ) {
	define( 'WPS_SHORTCODES_PRODUCTS_FILTER', false );
}

if ( !function_exists( 'woocommerce_product_search_filter_shortcode_products' ) ) {
	/**
	 * Allows to switch on or off our handling of the [products] shortcode so that our
	 * filters will affect the results it presents.
	 *
	 * @param array $atts desired filter options
	 *
	 * @return string form HTML
	 */
	function woocommerce_product_search_filter_shortcode_products( $atts = array() ) {
		return WooCommerce_Product_Search_Compat_Shortcodes::filter_shortcode_products( $atts );
	}
}
if ( !function_exists( 'woocommerce_product_search_filter_shortcode_products_enable' ) ) {
	function woocommerce_product_search_filter_shortcode_products_enable() {
		WooCommerce_Product_Search_Compat_Shortcodes::enable_filter_shortcode_products();
	}
}
if ( !function_exists( 'woocommerce_product_search_filter_shortcode_products_disable' ) ) {
	function woocommerce_product_search_filter_shortcode_products_disable() {
		WooCommerce_Product_Search_Compat_Shortcodes::disable_filter_shortcode_products();
	}
}

/**
 * Shortcodes handling ... to integrate with our filters.
 */
class WooCommerce_Product_Search_Compat_Shortcodes {

	/**
	 * Register actions, filters and shortcodes.
	 */
	public static function init() {
		add_shortcode( 'woocommerce_product_search_filter_shortcode_products', array( __CLASS__, 'filter_shortcode_products' ) );
		if ( apply_filters( 'woocommerce_product_search_filter_shortcode_products', WPS_SHORTCODES_PRODUCTS_FILTER ) ) {
			self::enable_filter_shortcode_products();
		}
	}

	/**
	 * [woocommerce_product_search_filter_shortcode_products] shortcode renderer which allows to switch
	 * on or off our handling of the [products] shortcode so that our filters will affect the results
	 * it presents.
	 *
	 * @param array $atts shortcode parameters
	 * @param string $content not used
	 *
	 * @return string|mixed
	 */
	public static function filter_shortcode_products( $atts = array(), $content = '' ) {
		$atts = shortcode_atts(
			array(
				'enable' => 'yes'
			),
			$atts
		);
		$enable = $atts['enable'];
		if ( is_string( $enable ) ) {
			$enable = strtolower( $enable );
		} else if ( is_bool( $enable ) ) {
			$enable = $enable ? 'yes' : 'no';
		}
		switch ( $enable ) {
			case 'true':
			case 'yes':
			case '1':
				$enable = true;
				break;
			default:
				$enable = false;
		}
		if ( $enable ) {
			self::enable_filter_shortcode_products();
		} else {
			self::disable_filter_shortcode_products();
		}
	}

	/**
	 * Enable filtering.
	 */
	public static function enable_filter_shortcode_products() {
		if ( !has_filter( 'woocommerce_shortcode_products_query', array( __CLASS__, 'woocommerce_shortcode_products_query' ) ) ) {
			add_filter( 'woocommerce_shortcode_products_query', array( __CLASS__, 'woocommerce_shortcode_products_query' ), 10, 3 );
		}
	}

	/**
	 * Disable filtering.
	 */
	public static function disable_filter_shortcode_products() {
		if ( has_filter( 'woocommerce_shortcode_products_query', array( __CLASS__, 'woocommerce_shortcode_products_query' ) ) ) {
			remove_filter( 'woocommerce_shortcode_products_query', array( __CLASS__, 'woocommerce_shortcode_products_query' ), 10 );
		}
	}

	/**
	 * Filter the products query.
	 *
	 * @param array $query_args
	 * @param array $attributes
	 * @param string $type
	 *
	 * @return array
	 */
	public static function woocommerce_shortcode_products_query( $query_args, $attributes, $type ) {

		$post_type = isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : null;
		$_REQUEST['post_type'] = 'product';

		$post_ids = WooCommerce_Product_Search_Service::get_post_ids_for_request_filtered( array( 'variations' => true ) );

		if ( $post_type !== null ) {
			$_REQUEST['post_type'] = $post_type;
		} else {
			unset( $_REQUEST['post_type'] );
		}

		if ( is_array( $post_ids ) && count( $post_ids ) > 0 ) {
			$post_in = isset( $query_args['post__in'] ) && is_array( $query_args['post__in'] ) ? $query_args['post__in'] : array();
			if ( count( $post_in ) === 0 && isset( $query_args['p'] ) ) {
				$post_in[] = $query_args['p'];
			}
			if ( count( $post_in ) > 0 ) {
				$post_in = array_unique( array_map( 'intval', $post_in ) );
			} else {
				$post_in = null;
			}
			if ( $post_in !== null ) {
				$post_ids = array_intersect( $post_ids, array_map( 'intval', $query_args['post__in'] ) );
				if ( count( $post_ids ) === 0 ) {
					$post_ids = WooCommerce_Product_Search_Service::NONE;
				}
				$query_args['post__in'] = $post_ids;
			} else {
				$query_args['post__in'] = $post_ids;
			}

			$query_args['nocache'] = microtime( true );
		}
		return $query_args;
	}

}
WooCommerce_Product_Search_Compat_Shortcodes::init();
