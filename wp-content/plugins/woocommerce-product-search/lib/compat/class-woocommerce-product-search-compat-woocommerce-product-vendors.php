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

use com\itthinx\woocommerce\search\engine\Tools;

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

			add_action( 'edited_term', array( __CLASS__, 'edited_term' ), 10, 3 );

			add_action( 'deleted_term_relationships', array( __CLASS__, 'deleted_term_relationships' ), 10, 3 );
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
	 * Add the vendor to content indexing so product searches for the vendor include related products.
	 *
	 * @param string $content
	 * @param string $context
	 * @param int $post_id
	 *
	 * @return string
	 */
	public static function woocommerce_product_search_indexer_filter_content( $content, $context, $post_id ) {
		if ( $context === 'post_content' ) {

			$post_type = get_post_type( $post_id );
			if ( $post_type === 'product_variation' ) {
				$product = wc_get_product( $post_id );
				if ( $product ) {
					$parent_id = $product->get_parent_id();
					if ( $parent_id ) {
						$post_id = $parent_id;
					}
				}
			}

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

	/**
	 * A term has been updated.
	 *
	 * @param int $term_id term ID
	 * @param int $tt_id term taxonomy ID
	 * @param string $taxonomy taxonomy slug
	 * @param array $args arguments passed to wp_update_term() which triggers this action (added in WP 6.1.0, we don't use it)
	 */
	public static function edited_term( $term_id, $tt_id, $taxonomy, $args = null ) {
		if ( $taxonomy === self::$taxonomy ) {
			$post_ids = get_objects_in_term( $term_id, self::$taxonomy );
			if ( is_array( $post_ids ) && count( $post_ids ) > 0 ) {
				Tools::unique_int( $post_ids );

				$indexer = new WooCommerce_Product_Search_Indexer();
				foreach ( $post_ids as $post_id ) {
					if ( $post_id ) {
						$post_type = get_post_type( $post_id );
						if ( $post_type === 'product' ) {
							$indexer->purge( $post_id );
						}
					}
				}
			}
		}
	}

	/**
	 * An object-term relationship has been deleted.
	 *
	 * @param int $object_id
	 * @param array $tt_ids
	 * @param string $taxonomy
	 */
	public static function deleted_term_relationships( $object_id, $tt_ids, $taxonomy ) {
		if ( $taxonomy === self::$taxonomy ) {
			if ( $object_id ) {
				$post_type = get_post_type( $object_id );
				if ( $post_type === 'product' ) {
					$indexer = new WooCommerce_Product_Search_Indexer();
					$indexer->purge( (int) $object_id );
				}
			}
		}
	}
}
WooCommerce_Product_Search_Compat_WooCommerce_Product_Vendors::init();
