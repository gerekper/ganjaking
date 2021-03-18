<?php
/**
 * class-woocommerce-product-search-compat-wpml.php
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
 * @since 3.6.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WPML compatibility.
 */
class WooCommerce_Product_Search_Compat_WPML {

	/**
	 * Filter priorities.
	 *
	 * @var array
	 */
	private static $priority = null;

	/**
	 * Class action hooks.
	 */
	public static function init() {
		add_action( 'woocommerce_product_search_indexer_index_start', array( __CLASS__, 'woocommerce_product_search_indexer_index_start' ) );
		add_action( 'woocommerce_product_search_indexer_index_end', array( __CLASS__, 'woocommerce_product_search_indexer_index_end' ) );

		add_action( 'wcml_after_sync_product_data', array( __CLASS__, 'wcml_after_sync_product_data' ), 10, 3 );
	}

	/**
	 * Hooked on index start.
	 *
	 * @param int $post_id
	 */
	public static function woocommerce_product_search_indexer_index_start( $post_id ) {

		global $sitepress;

		if ( !empty( $sitepress ) && is_object( $sitepress ) ) {
			self::$priority = array();

			$priority = has_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter' ) );
			self::$priority['get_terms_args'] = $priority;
			if ( $priority !== false ) {
				remove_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter' ), $priority );
			}

			$priority = has_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ) );
			self::$priority['get_term'] = $priority;
			if ( $priority !== false ) {
				remove_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), $priority );
			}

			$priority = has_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ) );
			self::$priority['terms_clauses'] = $priority;
			if ( $priority !== false ) {
				remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ), $priority );
			}
		}
	}

	/**
	 * Hooked on index end.
	 *
	 * @param int $post_id
	 */
	public static function woocommerce_product_search_indexer_index_end( $post_id ) {

		global $sitepress;

		if ( !empty( $sitepress ) && is_object( $sitepress ) ) {

			$priority = isset( self::$priority['get_terms_args'] ) ? self::$priority['get_terms_args'] : false;
			if ( $priority !== false ) {
				if ( method_exists( $sitepress, 'get_terms_args_filter' ) ) {
					add_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter' ), $priority, 2 );
				}
			}

			$priority = isset( self::$priority['get_term'] ) ? self::$priority['get_term'] : false;
			if ( $priority !== false ) {
				if ( method_exists( $sitepress, 'get_term_adjust_id' ) ) {
					add_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), $priority );
				}
			}

			$priority = isset( self::$priority['terms_clauses'] ) ? self::$priority['terms_clauses'] : false;
			if ( $priority !== false ) {
				if ( method_exists( $sitepress, 'terms_clauses' ) ) {
					add_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ), $priority, 3 );
				}
			}
		}
	}

	/**
	 * Hooked on wcml_after_sync_product_data.
	 *
	 * @param int $original_product_id
	 * @param int $tr_product_id
	 * @param string $lang
	 */
	public static function wcml_after_sync_product_data( $original_product_id, $tr_product_id, $lang ) {

		$product = wc_get_product( $tr_product_id );
		if ( $product instanceof WC_Product && $product->exists() ) {

			$product_taxonomies = WooCommerce_Product_Search_Indexer::get_applicable_product_taxonomies();
			foreach ( $product_taxonomies as $taxonomy ) {
				clean_object_term_cache( $tr_product_id, $taxonomy );
			}
			WooCommerce_Product_Search_Product_Processor::woocommerce_update_product( $tr_product_id, $product );
		}
	}
}

WooCommerce_Product_Search_Compat_WPML::init();
