<?php
/**
 * class-woocommerce-product-search-product.php
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
 * @since 1.2.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product search enhancements.
 */
class WooCommerce_Product_Search_Product {

	/**
	 * WordPress database version from which the termmeta table is available.
	 *
	 * @var int
	 */
	const WP_TERMMETA_DB_VERSION = 34370;

	/**
	 * Registers the request filter.
	 */
	public static function init() {
		$options = get_option( 'woocommerce-product-search', array() );
		$use_weights = isset( $options[WooCommerce_Product_Search::USE_WEIGHTS] ) ? $options[WooCommerce_Product_Search::USE_WEIGHTS] : WooCommerce_Product_Search::USE_WEIGHTS_DEFAULT;
		if ( $use_weights ) {
			add_filter( 'posts_fields', array( __CLASS__, 'posts_fields' ), 10, 2 );
			add_filter( 'posts_join', array( __CLASS__, 'posts_join' ), 10, 2 );
			add_filter( 'posts_orderby', array( __CLASS__, 'posts_orderby' ), 10, 2 );
		}
	}

	/**
	 * Implements our posts_fields filter.
	 *
	 * @param string $fields fields query part
	 * @param WP_Query $query query object
	 *
	 * @return string
	 */
	public static function posts_fields( $fields, $query ) {
		global $wpdb;
		if ( self::use_weights( $query ) ) {

			$addends = array();

			$addends[] = 'COALESCE(search_weight.meta_value, 0)';

			$addends[] = 'COALESCE(cat_max_weight.weight,0)';

			if ( isset( $_REQUEST[WooCommerce_Product_Search_Service::SEARCH_QUERY] ) ) {

				$options = get_option( 'woocommerce-product-search', array() );

				$use_weights       = isset( $options[WooCommerce_Product_Search::USE_WEIGHTS] ) ? $options[WooCommerce_Product_Search::USE_WEIGHTS] : WooCommerce_Product_Search::USE_WEIGHTS_DEFAULT;
				$weight_title      = intval( isset( $options[WooCommerce_Product_Search::WEIGHT_TITLE] ) ? $options[WooCommerce_Product_Search::WEIGHT_TITLE] : WooCommerce_Product_Search::WEIGHT_TITLE_DEFAULT );
				$weight_excerpt    = intval( isset( $options[WooCommerce_Product_Search::WEIGHT_EXCERPT] ) ? $options[WooCommerce_Product_Search::WEIGHT_EXCERPT] : WooCommerce_Product_Search::WEIGHT_EXCERPT_DEFAULT );
				$weight_content    = intval( isset( $options[WooCommerce_Product_Search::WEIGHT_CONTENT] ) ? $options[WooCommerce_Product_Search::WEIGHT_CONTENT] : WooCommerce_Product_Search::WEIGHT_CONTENT_DEFAULT );
				$weight_tags       = intval( isset( $options[WooCommerce_Product_Search::WEIGHT_TAGS] ) ? $options[WooCommerce_Product_Search::WEIGHT_TAGS] : WooCommerce_Product_Search::WEIGHT_TAGS_DEFAULT );
				$weight_categories = intval( isset( $options[WooCommerce_Product_Search::WEIGHT_CATEGORIES] ) ? $options[WooCommerce_Product_Search::WEIGHT_CATEGORIES] : WooCommerce_Product_Search::WEIGHT_CATEGORIES_DEFAULT );
				$weight_attributes = intval( isset( $options[WooCommerce_Product_Search::WEIGHT_ATTRIBUTES] ) ? $options[WooCommerce_Product_Search::WEIGHT_ATTRIBUTES] : WooCommerce_Product_Search::WEIGHT_ATTRIBUTES_DEFAULT );
				$weight_sku        = intval( isset( $options[WooCommerce_Product_Search::WEIGHT_SKU] ) ? $options[WooCommerce_Product_Search::WEIGHT_SKU] : WooCommerce_Product_Search::WEIGHT_SKU_DEFAULT );

				$title       = ( $weight_title != 0 ) && ( isset( $_REQUEST[WooCommerce_Product_Search_Service::TITLE] ) ? intval( $_REQUEST[WooCommerce_Product_Search_Service::TITLE] ) > 0 : WooCommerce_Product_Search_Service::DEFAULT_TITLE );
				$excerpt     = ( $weight_excerpt != 0 ) && ( isset( $_REQUEST[WooCommerce_Product_Search_Service::EXCERPT] ) ? intval( $_REQUEST[WooCommerce_Product_Search_Service::EXCERPT] ) > 0 : WooCommerce_Product_Search_Service::DEFAULT_EXCERPT );
				$content     = ( $weight_content != 0 ) && ( isset( $_REQUEST[WooCommerce_Product_Search_Service::CONTENT] ) ? intval( $_REQUEST[WooCommerce_Product_Search_Service::CONTENT] ) > 0 : WooCommerce_Product_Search_Service::DEFAULT_CONTENT );
				$tags        = ( $weight_tags != 0 ) && ( isset( $_REQUEST[WooCommerce_Product_Search_Service::TAGS] ) ? intval( $_REQUEST[WooCommerce_Product_Search_Service::TAGS] ) > 0 : WooCommerce_Product_Search_Service::DEFAULT_TAGS );
				$categories  = ( $weight_categories != 0 ) && ( isset( $_REQUEST[WooCommerce_Product_Search_Service::CATEGORIES] ) ? intval( $_REQUEST[WooCommerce_Product_Search_Service::CATEGORIES] ) > 0 : WooCommerce_Product_Search_Service::DEFAULT_CATEGORIES );
				$attributes  = ( $weight_attributes != 0 ) && ( isset( $_REQUEST[WooCommerce_Product_Search_Service::ATTRIBUTES] ) ? intval( $_REQUEST[WooCommerce_Product_Search_Service::ATTRIBUTES] ) > 0 : WooCommerce_Product_Search_Service::DEFAULT_ATTRIBUTES );
				$sku         = ( $weight_sku != 0 ) && ( isset( $_REQUEST[WooCommerce_Product_Search_Service::SKU] ) ? intval( $_REQUEST[WooCommerce_Product_Search_Service::SKU] ) > 0 : WooCommerce_Product_Search_Service::DEFAULT_SKU );


				if (
					!$title && !$excerpt && !$content && !$tags && !$sku && !$categories && !$attributes
				) {
					$title = true;
				}

				$search_query = isset( $_REQUEST[WooCommerce_Product_Search_Service::SEARCH_QUERY] ) ? $_REQUEST[WooCommerce_Product_Search_Service::SEARCH_QUERY] : '';
				$search_query = apply_filters( 'woocommerce_product_search_request_search_query', $search_query );
				$search_query = preg_replace( '/[^\p{L}\p{N}]++/u', ' ', $search_query );
				$search_query = trim( preg_replace( '/\s+/', ' ', $search_query ) );
				$search_terms = explode( ' ', $search_query );
				$search_terms = array_unique( $search_terms );

				if ( $title || $excerpt || $content || $tags || $categories || $attributes || $sku ) {

					$options = get_option( 'woocommerce-product-search', null );
					$match_split = isset( $options[WooCommerce_Product_Search_Service::MATCH_SPLIT] ) ? intval( $options[WooCommerce_Product_Search_Service::MATCH_SPLIT] ) : WooCommerce_Product_Search_Service::MATCH_SPLIT_DEFAULT;

					$indexer = new WooCommerce_Product_Search_Indexer();
					$object_type_ids = array(
						'post_title' => $indexer->get_object_type_id( 'product', 'product', 'posts', 'post_title' ),
						'post_excerpt' => $indexer->get_object_type_id( 'product', 'product', 'posts', 'post_excerpt' ),
						'post_content' => $indexer->get_object_type_id( 'product', 'product', 'posts', 'post_content' ),
						'sku' => $indexer->get_object_type_id( 'product', 'sku', 'postmeta', 'meta_key', '_sku' ),
						'tag' => $indexer->get_object_type_id( 'product', 'tag', 'term_taxonomy', 'taxonomy', 'product_tag' ),
						'category' => $indexer->get_object_type_id( 'product', 'category', 'term_taxonomy', 'taxonomy', 'product_cat' )
					);
					if ( $attributes ) {
						$attribute_object_type_ids = array();
						$attribute_taxonomies = wc_get_attribute_taxonomies();
						if ( !empty( $attribute_taxonomies ) ) {
							foreach ( $attribute_taxonomies as $attribute ) {
								$attribute_object_type_id = $indexer->get_object_type_id( 'product', $attribute->attribute_name, 'term_taxonomy', 'taxonomy', 'pa_' . $attribute->attribute_name );
								$object_type_ids[$attribute->attribute_name] = $attribute_object_type_id;
								$attribute_object_type_ids[] = $attribute_object_type_id;
							}
						}
					}
					unset( $indexer );

					$key_table   = WooCommerce_Product_Search_Controller::get_tablename( 'key' );
					$index_table = WooCommerce_Product_Search_Controller::get_tablename( 'index' );
					foreach ( $search_terms as $search_term ) {

						$length = function_exists( 'mb_strlen' ) ? mb_strlen( $search_term ) : strlen( $search_term );

						if ( $length === 0 ) {
							continue;
						}

						$equals = sprintf( " `key` = '%s' ", esc_sql( $search_term ) );
						$like   = sprintf( " `key` LIKE '%s' ", esc_sql( $wpdb->esc_like( $search_term ) ) . '%' );
						$where  = $length < $match_split ? $equals : $like;

						if ( $title ) {
							$addends[] = sprintf(
								" IF ( ID IN ( SELECT object_id FROM $index_table WHERE object_type_id = %d AND key_id IN ( SELECT key_id FROM $key_table WHERE %s ) ), %d, 0 ) ",
								intval( $object_type_ids['post_title'] ),
								$where,
								intval( $weight_title )
							);
						}
						if ( $excerpt ) {
							$addends[] = sprintf(
								" IF ( ID IN ( SELECT object_id FROM $index_table WHERE object_type_id = %d AND key_id IN ( SELECT key_id FROM $key_table WHERE %s ) ), %d, 0 ) ",
								intval( $object_type_ids['post_excerpt'] ),
								$where,
								intval( $weight_excerpt )
							);
						}
						if ( $content ) {
							$addends[] = sprintf(
								" IF ( ID IN ( SELECT object_id FROM $index_table WHERE object_type_id = %d AND key_id IN ( SELECT key_id FROM $key_table WHERE %s ) ), %d, 0 ) ",
								intval( $object_type_ids['post_content'] ),
								$where,
								intval( $weight_content )
							);
						}
						if ( $tags ) {
							$addends[] = sprintf(
								" IF ( ID IN ( SELECT object_id FROM $index_table WHERE object_type_id = %d AND key_id IN ( SELECT key_id FROM $key_table WHERE %s ) ), %d, 0 ) ",
								intval( $object_type_ids['tag'] ),
								$where,
								intval( $weight_tags )
							);
						}
						if ( $categories ) {
							$addends[] = sprintf(
								" IF ( ID IN ( SELECT object_id FROM $index_table WHERE object_type_id = %d AND key_id IN ( SELECT key_id FROM $key_table WHERE %s ) ), %d, 0 ) ",
								intval( $object_type_ids['category'] ),
								$where,
								intval( $weight_categories )
							);
						}
						if ( $attributes ) {
							if ( count( $attribute_object_type_ids ) > 0 ) {
								$addends[] = sprintf(
									" IF ( ID IN ( SELECT object_id FROM $index_table WHERE object_type_id IN (" . ( implode( ',', array_map( 'intval', $attribute_object_type_ids ) ) ) . ") AND key_id IN ( SELECT key_id FROM $key_table WHERE %s ) ), %d, 0 ) ",
									$where,
									intval( $weight_attributes )
								);
							}
						}
						if ( $sku ) {
							$addends[] = sprintf(
								" IF ( ID IN ( SELECT object_id FROM $index_table WHERE object_type_id = %d AND key_id IN ( SELECT key_id FROM $key_table WHERE %s ) ), %d, 0 ) ",
								intval( $object_type_ids['sku'] ),
								$where,
								intval( $weight_sku )
							);
						}
					}
				}
			}

			$sum = implode( ' + ', $addends );

			if ( strlen( $fields ) > 0 ) {
				$fields .= ' , ';
			}
			$fields .= $sum . ' as search_weight ';

		}
		return $fields;
	}

	/**
	 * Modify the join clase to take weights into account.
	 *
	 * @param string $join
	 * @param WP_Query $query
	 * @return string
	 */
	public static function posts_join( $join, $query ) {
		global $wpdb;
		if ( self::use_weights( $query ) ) {

			$join .= " LEFT JOIN $wpdb->postmeta search_weight ON ($wpdb->posts.ID = search_weight.post_id AND search_weight.meta_key = '_search_weight') ";

			if ( ( get_option( 'db_version' ) >= self::WP_TERMMETA_DB_VERSION ) && defined( 'WC_VERSION' ) && ( version_compare( WC_VERSION, '2.6.0' ) >= 0 ) ) {
				$join .= " LEFT JOIN (SELECT max(wt.meta_value) weight, tr.object_id object_id FROM {$wpdb->prefix}termmeta wt LEFT JOIN $wpdb->term_taxonomy tt ON wt.term_id = tt.term_id LEFT JOIN $wpdb->term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE wt.meta_key = '_search_weight' AND tt.taxonomy = 'product_cat' GROUP BY tr.object_id) cat_max_weight ON $wpdb->posts.ID = cat_max_weight.object_id ";
			} else {
				$join .= " LEFT JOIN (SELECT max(wt.meta_value) weight, tr.object_id object_id FROM {$wpdb->prefix}woocommerce_termmeta wt LEFT JOIN $wpdb->term_taxonomy tt ON wt.woocommerce_term_id = tt.term_id LEFT JOIN $wpdb->term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE wt.meta_key = '_search_weight' AND tt.taxonomy = 'product_cat' GROUP BY tr.object_id) cat_max_weight ON $wpdb->posts.ID = cat_max_weight.object_id ";
			}

		}
		return $join;
	}

	/**
	 * Modify the orderby clause to take weights into account.
	 *
	 * @param string $orderby
	 * @param WP_Query $query
	 * @return string
	 */
	public static function posts_orderby( $orderby, $query ) {
		if ( self::use_weights( $query ) ) {
			$search = 'search_weight DESC';
			if ( strlen( $orderby ) > 0 ) {
				$orderby = $search . ' , ' . $orderby;
			} else {
				$orderby = $search;
			}
		}
		return $orderby;
	}

	/**
	 * Whether to take weights into account.
	 *
	 * @param WP_Query $query
	 * @return boolean
	 */
	private static function use_weights( &$query ) {
		$result = false;
		if ( $query->query_vars['post_type'] == 'product' ) {
			if (
				$query->is_search() ||
				$query->get( 'product_search', false ) ||
				isset( $_REQUEST[WooCommerce_Product_Search_Service::SEARCH_TOKEN] )
			) {
				$result = true;
			}
		}
		return $result;
	}
}
WooCommerce_Product_Search_Product::init();
