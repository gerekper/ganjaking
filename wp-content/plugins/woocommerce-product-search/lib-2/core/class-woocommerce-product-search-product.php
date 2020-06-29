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

				$use_fulltext       = isset( $options[WooCommerce_Product_Search::USE_FULLTEXT] ) ? $options[WooCommerce_Product_Search::USE_FULLTEXT] : WooCommerce_Product_Search::USE_FULLTEXT_DEFAULT;
				$ft_boolean         = isset( $options[WooCommerce_Product_Search::FULLTEXT_BOOLEAN] ) ? $options[WooCommerce_Product_Search::FULLTEXT_BOOLEAN] : WooCommerce_Product_Search::FULLTEXT_BOOLEAN_DEFAULT;
				$prefix             = $ft_boolean ? '+' : '';
				$fulltext_wildcards = $ft_boolean && isset( $options[WooCommerce_Product_Search::FULLTEXT_WILDCARDS] ) ? $options[WooCommerce_Product_Search::FULLTEXT_WILDCARDS] : WooCommerce_Product_Search::FULLTEXT_WILDCARDS_DEFAULT;
				$wildcard           = $fulltext_wildcards ? '*' : '';

				$title       = ( $weight_title != 0 ) && ( isset( $_REQUEST[WooCommerce_Product_Search_Service::TITLE] ) ? intval( $_REQUEST[WooCommerce_Product_Search_Service::TITLE] ) > 0 : WooCommerce_Product_Search_Service::DEFAULT_TITLE );
				$excerpt     = ( $weight_excerpt != 0 ) && ( isset( $_REQUEST[WooCommerce_Product_Search_Service::EXCERPT] ) ? intval( $_REQUEST[WooCommerce_Product_Search_Service::EXCERPT] ) > 0 : WooCommerce_Product_Search_Service::DEFAULT_EXCERPT );
				$content     = ( $weight_content != 0 ) && ( isset( $_REQUEST[WooCommerce_Product_Search_Service::CONTENT] ) ? intval( $_REQUEST[WooCommerce_Product_Search_Service::CONTENT] ) > 0 : WooCommerce_Product_Search_Service::DEFAULT_CONTENT );
				$tags        = ( $weight_tags != 0 ) && ( isset( $_REQUEST[WooCommerce_Product_Search_Service::TAGS] ) ? intval( $_REQUEST[WooCommerce_Product_Search_Service::TAGS] ) > 0 : WooCommerce_Product_Search_Service::DEFAULT_TAGS );

				$search_query = preg_replace( '/[^\p{L}\p{N}]++/u', ' ', $_REQUEST[WooCommerce_Product_Search_Service::SEARCH_QUERY] );
				$search_query = trim( preg_replace( '/\s+/', ' ', $search_query ) );
				$search_terms = explode( ' ', $search_query );

				if ( $title || $excerpt || $content || $tags ) {

					foreach ( $search_terms as $search_term ) {

						if ( strlen( $search_term ) === 0 ) {
							continue;
						}

						if ( $use_fulltext ) {
							$like = $wpdb->esc_like( $prefix . $search_term . $wildcard );
						} else {
							$like = '%' . esc_sql( $wpdb->esc_like( $search_term ) ) . '%';
						}

						if ( $title ) {
							if ( $use_fulltext ) {
								$addends[] = sprintf( " IF (MATCH (post_title) AGAINST ('%s'" . ( $ft_boolean ? ' IN BOOLEAN MODE' : '' ) . '), %d, 0 ) ', $like, $weight_title );
							} else {
								$addends[] = sprintf( "IF (post_title LIKE '%s', %d, 0)", $like, $weight_title );
							}
						}
						if ( $excerpt ) {
							if ( $use_fulltext ) {
								$addends[] = sprintf( " IF (MATCH (post_excerpt) AGAINST ('%s'" . ( $ft_boolean ? ' IN BOOLEAN MODE' : '' ) . '), %d, 0 ) ', $like, $weight_excerpt );
							} else {
								$addends[] = sprintf( "IF (post_excerpt LIKE '%s', %d, 0)", $like, $weight_excerpt );
							}
						}
						if ( $content ) {
							if ( $use_fulltext ) {
								$addends[] = sprintf( " IF (MATCH (post_content) AGAINST ('%s'" . ( $ft_boolean ? ' IN BOOLEAN MODE' : '' ) . '), %d, 0 ) ', $like, $weight_content );
							} else {
								$addends[] = sprintf( "IF (post_content LIKE '%s', %d, 0)", $like, $weight_content );
							}
						}
						if ( $tags ) {
							$addends[] = sprintf(
								'IF (ID IN ' .
								'( ' .
								"SELECT p.ID FROM $wpdb->terms t " .
								"LEFT JOIN $wpdb->term_taxonomy tt ON t.term_id = tt.term_id " .
								"LEFT JOIN $wpdb->term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id " .
								"LEFT JOIN $wpdb->posts p ON p.ID = tr.object_id " .
								'WHERE ' .
								( $use_fulltext ? " MATCH (t.name) AGAINST ('%s'" . ( $ft_boolean ? ' IN BOOLEAN MODE' : '' ) . ') ' : "t.name like '%s' " ) .
								"AND tt.taxonomy = 'product_tag' " .
								"AND p.post_type = 'product' " .
								"AND p.post_status = 'publish' " .
								') ' .
								', %d, 0)',
								$like,
								$weight_tags
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
	 * Handler for posts_join
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
	 * Handler for posts_orderby
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
			if ( $query->is_search() || isset( $_REQUEST[WooCommerce_Product_Search_Service::SEARCH_TOKEN] ) ) {
				$result = true;
			}
		}
		return $result;
	}
}
WooCommerce_Product_Search_Product::init();
