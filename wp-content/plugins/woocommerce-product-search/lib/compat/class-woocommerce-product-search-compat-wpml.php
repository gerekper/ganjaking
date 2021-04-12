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

			add_filter( 'woocommerce_product_search_indexer_object_term_term_ids', array( __CLASS__, 'woocommerce_product_search_indexer_object_term_term_ids' ), 10, 2 );

			add_filter( 'woocommerce_product_search_indexer_filter_content', array( __CLASS__, 'woocommerce_product_search_indexer_filter_content' ), 10, 3 );
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

	/**
	 * Relate terms for untranslated products.
	 *
	 * @param int[] $term_ids
	 * @param WC_Product $product
	 *
	 * @return int[]
	 */
	public static function woocommerce_product_search_indexer_object_term_term_ids( $term_ids, $product ) {
		global $sitepress, $woocommerce_wpml;

		$product_id = $product->get_id();
		if (
			!empty( $product_id ) &&
			!empty( $sitepress ) &&
			is_object( $sitepress ) &&
			method_exists( $sitepress, 'is_display_as_translated_post_type' ) &&
			method_exists( $sitepress, 'post_translations' ) &&
			!empty( $woocommerce_wpml ) &&
			is_object( $woocommerce_wpml ) &&
			!empty( $woocommerce_wpml->products ) &&
			is_object( $woocommerce_wpml->products ) &&
			method_exists( $woocommerce_wpml->products, 'is_original_product' ) &&
			!empty( $woocommerce_wpml->terms ) &&
			is_object( $woocommerce_wpml->terms ) &&
			method_exists( $woocommerce_wpml->terms, 'wcml_get_translated_term' )
		) {

			$post_type = get_post_type( $product_id );
			if (
				$sitepress->is_translated_post_type( $post_type ) &&
				$sitepress->is_display_as_translated_post_type( $post_type )
			) {

				if ( $woocommerce_wpml->products->is_original_product( $product_id ) ) {

					$active_languages = $sitepress->get_active_languages();
					foreach ( $active_languages as $language ) {

						if ( $sitepress->post_translations()->get_element_lang_code( $product_id ) !== $language['code'] ) {

							$translated_product_ids = $sitepress->post_translations()->get_element_translations( $product_id );
							if ( is_array( $translated_product_ids ) ) {
								$translated_language_codes = array();
								foreach ( $translated_product_ids as $translated_product_id ) {
									$translated_language_codes[] = $sitepress->post_translations()->get_element_lang_code( $translated_product_id );
								}
								if ( !in_array( $language['code'], $translated_language_codes ) ) {

									$translated_term_ids = array();
									foreach ( $term_ids as $term_id ) {

										$term = get_term( $term_id );
										$translated_term = $woocommerce_wpml->terms->wcml_get_translated_term( $term_id, $term->taxonomy, $language['code'] );
										if (
											!empty( $translated_term ) &&
											!empty( $translated_term->term_id )
										) {
											$translated_term_ids[] = intval( $translated_term->term_id );
										}
									}
									$term_ids = array_unique( array_merge( $term_ids, $translated_term_ids ) );
								}
							}
						}
					}
				}
			}
		}
		return $term_ids;
	}

	/**
	 * Add translated terms for untranslated products.
	 *
	 * @param string $content
	 * @param string $context
	 * @param int $product_id
	 *
	 * @return string
	 */
	public static function woocommerce_product_search_indexer_filter_content( $content, $context, $product_id ) {

		global $sitepress, $woocommerce_wpml;

		$language_codes = array();
		if (
			!empty( $product_id ) &&
			!empty( $sitepress ) &&
			is_object( $sitepress ) &&
			method_exists( $sitepress, 'is_display_as_translated_post_type' ) &&
			method_exists( $sitepress, 'post_translations' ) &&
			!empty( $woocommerce_wpml ) &&
			is_object( $woocommerce_wpml ) &&
			!empty( $woocommerce_wpml->products ) &&
			is_object( $woocommerce_wpml->products ) &&
			method_exists( $woocommerce_wpml->products, 'is_original_product' ) &&
			!empty( $woocommerce_wpml->terms ) &&
			is_object( $woocommerce_wpml->terms ) &&
			method_exists( $woocommerce_wpml->terms, 'wcml_get_translated_term' )
		) {

			$post_type = get_post_type( $product_id );
			if (
				$sitepress->is_translated_post_type( $post_type ) &&
				$sitepress->is_display_as_translated_post_type( $post_type )
			) {

				if ( $woocommerce_wpml->products->is_original_product( $product_id ) ) {

					$active_languages = $sitepress->get_active_languages();
					foreach ( $active_languages as $language ) {

						if ( $sitepress->post_translations()->get_element_lang_code( $product_id ) !== $language['code'] ) {

							$translated_product_ids = $sitepress->post_translations()->get_element_translations( $product_id );
							if ( is_array( $translated_product_ids ) ) {
								$translated_language_codes = array();
								foreach ( $translated_product_ids as $translated_product_id ) {
									$translated_language_codes[] = $sitepress->post_translations()->get_element_lang_code( $translated_product_id );
								}
								if ( !in_array( $language['code'], $translated_language_codes ) ) {
									$language_codes[] = $language['code'];
								}
							}
						}
					}
				}
			}
		}

		if ( count( $language_codes ) > 0 ) {
			$taxonomy = null;
			switch ( $context ) {
				case 'tag':

					$taxonomy = 'product_tag';
					break;
				case 'category':

					$taxonomy = 'product_cat';
					break;
				default:

					if ( taxonomy_exists( 'pa_' . $context ) ) {
						$taxonomy = 'pa_' . $context;
					}
			}
			if ( $taxonomy !== null ) {
				$terms = get_the_terms( $product_id, $taxonomy );
				if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						foreach ( $language_codes as $language_code ) {
							$translated_term = $woocommerce_wpml->terms->wcml_get_translated_term( $term->term_id, $term->taxonomy, $language_code );
							if ( !empty( $translated_term ) ) {
								$content .= ' ' . $translated_term->name;
							}
						}
					}
				}
			}
		}
		return $content;
	}
}

WooCommerce_Product_Search_Compat_WPML::init();
