<?php
/**
 * class-woocommerce-product-search-compat-woocommerce-brands.php
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
 * @since 2.20.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Brands compatibility.
 */
class WooCommerce_Product_Search_Compat_WooCommerce_Brands {

	/**
	 * Filter to add the product_brand taxonomy.
	 */
	public static function init() {
		add_filter( 'woocommerce_product_search_process_query_product_taxonomies', array( __CLASS__, 'woocommerce_product_search_process_query_product_taxonomies' ), 10, 2 );
		if ( apply_filters( 'woocommerce_product_search_compat_woocommerce_brands_index', true ) ) {
			add_filter( 'woocommerce_product_search_indexer_filter_content', array( __CLASS__, 'woocommerce_product_search_indexer_filter_content' ), 10, 3 );
		}
	}

	/**
	 * Add the product_brand taxonomy to handled taxonomies.
	 *
	 * @param string[] $product_taxonomies
	 * @param WP_Query $wp_query
	 *
	 * return string[]
	 */
	public static function woocommerce_product_search_process_query_product_taxonomies( $product_taxonomies, $wp_query ) {
		if ( is_array( $product_taxonomies ) && !in_array( 'product_brand', $product_taxonomies ) ) {
			$product_taxonomies[] = 'product_brand';
		}
		return $product_taxonomies;
	}

	/**
	 * Add the brand to content indexing so product searches for the brand include related products.
	 *
	 * @param string $content
	 * @param string $context
	 * @param int $post_id
	 *
	 * @return string
	 */
	public static function woocommerce_product_search_indexer_filter_content( $content, $context, $post_id ) {
		if ( $context === 'post_content' ) {
			$brands = null;

			$terms = get_the_terms( $post_id, 'product_brand' );
			if ( !is_wp_error( $terms ) && !empty( $terms ) && is_array( $terms ) ) {
				$brands = array();
				foreach ( $terms as $term ) {
					$brands[] = $term->name;
				}
				$brands = implode( ' ', $brands );
			}
			if ( $brands !== null && is_string( $brands ) ) {
				$content .= ' ' . $brands;
			}
		}
		return $content;
	}
}
WooCommerce_Product_Search_Compat_WooCommerce_Brands::init();
