<?php
/**
 * class-woocommerce-product-search-compat-woocommerce-product-vendors.php
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
 * WooCommerce Product Vendors compatibility.
 */
class WooCommerce_Product_Search_Compat_WooCommerce_Product_Vendors {

	/**
	 * The vendors taxonomy.
	 *
	 * @var string
	 */
	private static $taxonomy = 'wcpv_product_vendors';

	/**
	 * Filter to add the vendor taxonomy.
	 */
	public static function init() {
		if ( defined( 'WC_PRODUCT_VENDORS_TAXONOMY' ) ) {
			self::$taxonomy = WC_PRODUCT_VENDORS_TAXONOMY;
		}
		add_filter( 'woocommerce_product_search_process_query_product_taxonomies', array( __CLASS__, 'woocommerce_product_search_process_query_product_taxonomies' ), 10, 2 );
		if ( apply_filters( 'woocommerce_product_search_compat_woocommerce_product_vendors_index', true ) ) {
			add_filter( 'woocommerce_product_search_indexer_filter_content', array( __CLASS__, 'woocommerce_product_search_indexer_filter_content' ), 10, 3 );
		}
	}

	/**
	 * Add the vendor taxonomy to handled taxonomies.
	 *
	 * @param string[] $product_taxonomies
	 * @param WP_Query $wp_query
	 *
	 * return string[]
	 */
	public static function woocommerce_product_search_process_query_product_taxonomies( $product_taxonomies, $wp_query ) {

		if ( is_array( $product_taxonomies ) && !in_array( self::$taxonomy, $product_taxonomies ) ) {
			$product_taxonomies[] = self::$taxonomy;
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
			$vendors = null;

			$terms = get_the_terms( $post_id, self::$taxonomy );
			if ( !is_wp_error( $terms ) && !empty( $terms ) && is_array( $terms ) ) {
				$vendors = array();
				foreach ( $terms as $term ) {
					$vendors[] = $term->name;
				}
				$vendors = implode( ' ', $vendors );
			}
			if ( $vendors !== null && is_string( $vendors ) ) {
				$content .= ' ' . $vendors;
			}
		}
		return $content;
	}
}
WooCommerce_Product_Search_Compat_WooCommerce_Product_Vendors::init();
