<?php
/**
 * class-woocommerce-product-search-service.php
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
 * @since 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product search service.
 */
class WooCommerce_Product_Search_Service {

	const SEARCH_TOKEN  = 'product-search';
	const SEARCH_QUERY  = 'product-query';

	const LIMIT         = 'limit';
	const DEFAULT_LIMIT = 10;

	const TITLE         = 'title';
	const EXCERPT       = 'excerpt';
	const CONTENT       = 'content';
	const TAGS          = 'tags';
	const SKU           = 'sku';

	const DEFAULT_TITLE   = true;
	const DEFAULT_EXCERPT = true;
	const DEFAULT_CONTENT = true;
	const DEFAULT_TAGS    = true;
	const DEFAULT_SKU     = false;

	const ORDER            = 'order';
	const DEFAULT_ORDER    = 'DESC';
	const ORDER_BY         = 'order_by';
	const DEFAULT_ORDER_BY = 'date';

	const PRODUCT_THUMBNAILS          = 'product_thumbnails';
	const DEFAULT_PRODUCT_THUMBNAILS  = true;

	const CATEGORY_RESULTS         = 'category_results';
	const DEFAULT_CATEGORY_RESULTS = true;
	const CATEGORY_LIMIT           = 'category_limit';
	const DEFAULT_CATEGORY_LIMIT   = 5;

	const CACHE_LIFETIME   = 300; 
	const POST_CACHE_GROUP = 'ixwpsp';
	const TERM_CACHE_GROUP = 'ixwpst';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'wp_init' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'wp_enqueue_scripts' ) );

		add_action( 'wp_ajax_product_search', array( __CLASS__, 'wp_ajax_product_search' ) );
		add_action( 'wp_ajax_nopriv_product_search', array( __CLASS__, 'wp_ajax_product_search' ) );

		add_filter( 'icl_set_current_language', array( __CLASS__, 'icl_set_current_language' ) );
	}

	public static function wp_ajax_product_search() {
		ob_start();
		$results = self::request_results();
		$ob = ob_get_clean();
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG && $ob ) {
			error_log( $ob );
		}
		echo json_encode( $results );
		exit;
	}

	public static function wp_init() {
		if ( isset( $_REQUEST['ixwps'] ) ) {
			add_action( 'pre_get_posts', array( __CLASS__, 'wps_pre_get_posts' ) );
			add_action( 'posts_search', array( __CLASS__, 'posts_search' ), 10, 2 );

		}
	}

	public static function wp_enqueue_scripts() {
		wp_register_script( 'typewatch', WOO_PS_PLUGIN_URL . '/js/jquery.typewatch.js', array( 'jquery' ), WOO_PS_PLUGIN_VERSION, true );
		wp_register_script( 'product-search', WOO_PS_PLUGIN_URL . '/js/product-search.js', array( 'jquery', 'typewatch' ), WOO_PS_PLUGIN_VERSION, true );
		wp_register_style( 'product-search', WOO_PS_PLUGIN_URL . '/css/product-search.css', array(), WOO_PS_PLUGIN_VERSION );
	}

	public static function wps_pre_get_posts( $wp_query ) {
		if ( $wp_query->is_search() ) {

			if ( isset( $_REQUEST['s'] ) && isset( $_REQUEST['ixwps'] ) ) {

				if ( !isset( $_REQUEST[self::SEARCH_QUERY] ) ) {
					$_REQUEST[self::SEARCH_QUERY] = $_REQUEST['s'];
				}
				$post_ids = self::get_post_ids_for_request();
				if ( !empty( $post_ids ) ) {
					$wp_query->set( 'post__in', $post_ids );
				}
			}
		}
	}

	public static function posts_search( $search, $wp_query ) {
		if ( isset( $_REQUEST['s'] ) && isset( $_REQUEST['ixwps'] ) ) {

			$post__in = $wp_query->get( 'post__in' );
			if ( !empty( $post__in ) ) {
				$search = '';
			}
		}
		return $search;
	}

	/**
	 * @deprecated
	 */
	public static function posts_join( $join, $wp_query ) {

		global $wpml_query_filter;

		if ( isset( $wpml_query_filter ) ) {
			if ( strpos( $join, 'icl_translations' ) === false ) {
				$join = $wpml_query_filter->posts_where_filter( $join, $wp_query );
			}
		}
		return $join;
	}

	/**
	 * @deprecated
	 */
	public static function posts_where( $where, $wp_query ) {

		global $wpdb, $wpml_query_filter;

		if ( $wp_query->is_search() ) {

			if ( isset( $_REQUEST['s'] ) && isset( $_REQUEST['ixwps'] ) ) {

				if ( !isset( $_REQUEST[self::SEARCH_QUERY] ) ) {
					$_REQUEST[self::SEARCH_QUERY] = $_REQUEST['s'];
				}

				$post_ids = self::get_post_ids_for_request();
				if ( !empty( $post_ids ) ) {
					$posts_id_in = implode( ',', $post_ids );
					if ( strlen( $posts_id_in ) > 0 ) {
						$where .= sprintf( " OR ( $wpdb->posts.ID IN (%s) ) ", $posts_id_in );


						if ( isset( $wpml_query_filter ) ) {
							$where = $wpml_query_filter->posts_where_filter( $where, $wp_query );
						}
					}
				}
			}
		}
		return $where;
	}

	public static function get_post_ids_for_request() {

		global $wpdb;

		$title       = isset( $_REQUEST[self::TITLE] ) ? intval( $_REQUEST[self::TITLE] ) > 0 : self::DEFAULT_TITLE;
		$excerpt     = isset( $_REQUEST[self::EXCERPT] ) ? intval( $_REQUEST[self::EXCERPT] ) > 0 : self::DEFAULT_EXCERPT;
		$content     = isset( $_REQUEST[self::CONTENT] ) ? intval( $_REQUEST[self::CONTENT] ) > 0 : self::DEFAULT_CONTENT;
		$tags        = isset( $_REQUEST[self::TAGS] ) ? intval( $_REQUEST[self::TAGS] ) > 0 : self::DEFAULT_TAGS;
		$sku         = isset( $_REQUEST[self::SKU] ) ? intval( $_REQUEST[self::SKU] ) > 0 : self::DEFAULT_SKU;

		$limit       = isset( $_REQUEST[self::LIMIT] ) ? intval( $_REQUEST[self::LIMIT] ) : self::DEFAULT_LIMIT;
		$numberposts = intval( apply_filters( 'product_search_limit', $limit ) );

		$order       = isset( $_REQUEST[self::ORDER] ) ? strtoupper( trim( $_REQUEST[self::ORDER] ) ) : self::DEFAULT_ORDER;
		switch ( $order ) {
			case 'DESC' :
			case 'ASC' :
				break;
			default :
				$order = 'DESC';
		}
		$order_by    = isset( $_REQUEST[self::ORDER_BY] ) ? strtolower( trim( $_REQUEST[self::ORDER_BY] ) ) : self::DEFAULT_ORDER_BY;
		switch ( $order_by ) {
			case 'date' :
			case 'title' :
			case 'ID' :
			case 'rand' :
				break;
			default :
				$order_by = 'date';
		}

		$product_thumbnails = isset( $_REQUEST[self::PRODUCT_THUMBNAILS] ) ? intval( $_REQUEST[self::PRODUCT_THUMBNAILS] ) > 0 : self::DEFAULT_PRODUCT_THUMBNAILS;

		$category_results   = isset( $_REQUEST[self::CATEGORY_RESULTS] ) ? intval( $_REQUEST[self::CATEGORY_RESULTS] ) > 0 : self::DEFAULT_CATEGORY_RESULTS;
		$category_limit     = isset( $_REQUEST[self::CATEGORY_LIMIT] ) ? intval( $_REQUEST[self::CATEGORY_LIMIT] ) : self::DEFAULT_CATEGORY_LIMIT;

		if ( !$title && !$excerpt && !$content && !$tags ) {
			$title = true;
		}

		$search_query = preg_replace( '/[^\p{L}\p{N}]++/u', ' ', $_REQUEST[self::SEARCH_QUERY] );
		$search_query = trim( preg_replace( '/\s+/', ' ', $search_query ) );
		$search_terms = explode( ' ', $search_query );

		$cache_key = self::get_cache_key( array(
			'title'        => $title,
			'excerpt'      => $excerpt,
			'content'      => $content,
			'tags'         => $tags,
			'limit'        => $numberposts,
			'order'        => $order,
			'order_by'     => $order_by,
			'search_query' => $search_query
		) );

		$post_ids = wp_cache_get( $cache_key, self::POST_CACHE_GROUP, true );
		if ( $post_ids !== false ) {
			return $post_ids;
		}

		$options = get_option( 'woocommerce-product-search', null );

		$log_query_times    = isset( $options[WooCommerce_Product_Search::LOG_QUERY_TIMES] ) ? $options[WooCommerce_Product_Search::LOG_QUERY_TIMES] : WooCommerce_Product_Search::LOG_QUERY_TIMES_DEFAULT;
		$use_fulltext       = isset( $options[WooCommerce_Product_Search::USE_FULLTEXT] ) ? $options[WooCommerce_Product_Search::USE_FULLTEXT] : WooCommerce_Product_Search::USE_FULLTEXT_DEFAULT;
		$ft_boolean         = isset( $options[WooCommerce_Product_Search::FULLTEXT_BOOLEAN] ) ? $options[WooCommerce_Product_Search::FULLTEXT_BOOLEAN] : WooCommerce_Product_Search::FULLTEXT_BOOLEAN_DEFAULT;
		$prefix             = $ft_boolean ? '+' : '';
		$fulltext_wildcards = $ft_boolean && isset( $options[WooCommerce_Product_Search::FULLTEXT_WILDCARDS] ) ? $options[WooCommerce_Product_Search::FULLTEXT_WILDCARDS] : WooCommerce_Product_Search::FULLTEXT_WILDCARDS_DEFAULT;
		$wildcard           = $fulltext_wildcards ? '*' : '';
		$ft_min_word_len    = isset( $options[WooCommerce_Product_Search::FT_MIN_WORD_LEN] ) ? $options[WooCommerce_Product_Search::FT_MIN_WORD_LEN] : self::get_ft_min_word_len();

		$conj = array();

		$relevance = array();

		foreach ( $search_terms as $search_term ) {

			if ( strlen( $search_term ) === 0 ) {
				continue;
			}

			if ( $use_fulltext && !$fulltext_wildcards && ( strlen( $search_term ) < $ft_min_word_len ) ) {
				continue;
			}

			$args   = array();
			$params = array();

			$rel_args = array();
			$rel_params = array();

			if ( $use_fulltext ) {
				$like = $wpdb->esc_like( $prefix . $search_term . $wildcard );
				$like_ = '%' . $wpdb->esc_like( $search_term ) . '%';
			} else {
				$like = '%' . $wpdb->esc_like( $search_term ) . '%';
			}

			if ( $title ) {
				if ( $use_fulltext ) {
					$args[]       = ' MATCH (post_title) AGAINST (%s' . ( $ft_boolean ? ' IN BOOLEAN MODE' : '' ) . ') ';
					$rel_args[]   = ' MATCH (post_title) AGAINST (%s' . ( $ft_boolean ? ' IN BOOLEAN MODE' : '' ) . ') ';
					$rel_params[] = $like;
				} else {
					$args[] = ' post_title LIKE %s ';
				}
				$params[] = $like;
			}
			if ( $excerpt ) {
				if ( $use_fulltext ) {
					$args[]       = ' MATCH (post_excerpt) AGAINST (%s' . ( $ft_boolean ? ' IN BOOLEAN MODE' : '' ) . ') ';
					$rel_args[]   = ' MATCH (post_excerpt) AGAINST (%s' . ( $ft_boolean ? ' IN BOOLEAN MODE' : '' ) . ') ';
					$rel_params[] = $like;
				} else {
					$args[] = ' post_excerpt LIKE %s ';
				}
				$params[] = $like;
			}
			if ( $content ) {
				if ( $use_fulltext ) {
					$args[]       = ' MATCH (post_content) AGAINST (%s' . ( $ft_boolean ? ' IN BOOLEAN MODE' : '' ) . ') ';
					$rel_args[]   = ' MATCH (post_content) AGAINST (%s' . ( $ft_boolean ? ' IN BOOLEAN MODE' : '' ) . ') ';
					$rel_params[] = $like;
				} else {
					$args[] = ' post_content LIKE %s ';
				}
				$params[] = $like;
			}
			if ( $sku ) {
				if ( false && $use_fulltext ) { 
					$args[] =
						' ID IN ' .
						' ( ' .
						"SELECT DISTINCT post_id FROM $wpdb->postmeta " .
						'WHERE ' .
						"meta_key = '_sku' " .
						'AND MATCH (meta_value) AGAINST (%s' . ( $ft_boolean ? ' IN BOOLEAN MODE' : '' ) . ') ' .
						'UNION ALL ' .
						'SELECT DISTINCT p.post_parent post_id ' .
						"FROM $wpdb->posts as p " .
						"JOIN $wpdb->postmeta pm ON p.ID = pm.post_id AND " .
						"pm.meta_key='_sku' AND " .
						'MATCH (pm.meta_value) AGAINST (%s' . ( $ft_boolean ? ' IN BOOLEAN MODE' : '' ) . ') ' .
						'WHERE p.post_parent <> 0 ' .
						' ) ';
					$params[] = $like;
					$params[] = $like;
				} else {
					$args[] =
						' ID IN ' .
						' ( ' .
						"SELECT DISTINCT post_id FROM $wpdb->postmeta " .
						'WHERE ' .
						"meta_key = '_sku' " .
						'AND meta_value LIKE %s' .
						'UNION ALL ' .
						'SELECT DISTINCT p.post_parent post_id ' .
						"FROM $wpdb->posts as p " .
						"JOIN $wpdb->postmeta pm ON p.ID = pm.post_id AND " .
						"pm.meta_key='_sku' AND " .
						'pm.meta_value LIKE %s ' .
						'WHERE p.post_parent <> 0 ' .
						' ) ';
					$params[] = $use_fulltext ? $like_ : $like;
					$params[] = $use_fulltext ? $like_ : $like;
				}
			}

			if ( $tags ) {
				$args[] =
					' ID IN ' .
					'( ' .
					"SELECT p.ID FROM $wpdb->terms t " .
					"LEFT JOIN $wpdb->term_taxonomy tt ON t.term_id = tt.term_id " .
					"LEFT JOIN $wpdb->term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id " .
					"LEFT JOIN $wpdb->posts p ON p.ID = tr.object_id " .
					'WHERE ' .
					( $use_fulltext ? ' MATCH (t.name) AGAINST (%s' . ( $ft_boolean ? ' IN BOOLEAN MODE' : '' ) . ') ' : 't.name like %s ' ) .
					"AND tt.taxonomy = 'product_tag' " .
					"AND p.post_type = 'product' " .
					"AND p.post_status = 'publish' " .
					') ';
				$params[] = $like;
			}

			if ( !empty( $args ) ) {

				$conj[] = $wpdb->prepare( sprintf( ' ( %s ) ', implode( ' OR ', $args ) ), $params );
			}

			if ( !empty( $rel_args ) ) {
				$relevance[] = $wpdb->prepare( sprintf( ' %s ', implode( ' + ', $rel_args ) ), $rel_params );
			}

		}

		$include = array();

		if ( !empty( $conj ) ) {
			$conditions = implode( ' AND ', $conj );

			$order_by = '';
			if ( $ft_boolean && !empty( $relevance ) ) {
				$relevance = implode( ' + ', $relevance );
				$relevance = ', ' . $relevance . ' relevance ';
				$order_by = ' ORDER BY relevance DESC ';
			}

			if ( $title || $excerpt || $content || $tags ) {
				$join   = " LEFT JOIN $wpdb->postmeta AS meta_visibility ON posts.ID = meta_visibility.post_id ";
				$where  = " post_type = 'product' ";
				$where .= " AND post_status = 'publish' ";
				$where .= " AND meta_visibility.meta_key = '_visibility' ";
				$where .= " AND meta_visibility.meta_value IN ('visible', 'search') ";
				if ( get_option( 'woocommerce_hide_out_of_stock_items' ) == 'yes' ) {
					$join  .= " LEFT JOIN $wpdb->postmeta AS meta_stock ON posts.ID = meta_stock.post_id ";
					$where .= " AND meta_stock.meta_key = '_stock_status' ";
					$where .= " AND meta_stock.meta_value = 'instock' ";
				}
				if ( $ft_boolean && !empty( $relevance ) ) {

					$query = sprintf( "SELECT ID %s FROM $wpdb->posts posts %s WHERE %s AND %s %s", $relevance, $join, $where, $conditions, $order_by );
				} else {
					$query = sprintf( "SELECT ID FROM $wpdb->posts posts %s WHERE %s AND %s", $join, $where, $conditions );
				}

				if ( $log_query_times ) {
					$start = function_exists( 'microtime' ) ? microtime( true ) : time();
				}
				$results = $wpdb->get_results( $query );
				if ( $log_query_times ) {
					$time = ( function_exists( 'microtime' ) ? microtime( true ) : time() ) - $start;
					error_log(
						sprintf(
							__( 'WooCommerce Product Search - %1$s - Main Query Time: %2$fs - Search Terms: %3$s', 'woocommerce-product-search' ),
							( $use_fulltext ?
								( $wildcard ?
									__( 'Full-Text Search & Wildcard Mode', 'woocommerce-product-search' )
									:
									__( 'Full-Text Search', 'woocommerce-product-search' )
								)
								:
								__( 'Standard Search', 'woocommerce-product-search' )
							),
							$time,
							implode( ' ', $search_terms )
						)
					);
				}
				if ( !empty( $results ) && is_array( $results ) ) {
					foreach ( $results as $result ) {
						$include[] = intval( $result->ID );
					}
				}
				unset( $results );
			}
		}

		$cached = wp_cache_set( $cache_key, $include, self::POST_CACHE_GROUP, self::get_cache_lifetime() );

		return $include;
	}

	public static function mand( $a, $b ) {
		return $a && $b;
	}

	public static function get_terms_for_request( &$post_ids ) {

		global $wpdb;

		$cache_key = self::get_cache_key( $post_ids );

		$terms = wp_cache_get( $cache_key, self::TERM_CACHE_GROUP, true );
		if ( $terms !== false ) {
			return $terms;
		}

		$terms = array();
		$cat_query =
			"SELECT t.* ".
			"FROM $wpdb->terms t " .
			"LEFT JOIN $wpdb->term_taxonomy tt ON t.term_id = tt.term_id " .
			"LEFT JOIN $wpdb->term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id " .
			"LEFT JOIN $wpdb->posts p ON p.ID = tr.object_id " .
			"WHERE  tt.taxonomy = 'product_cat' AND " .
			"tr.object_id IN (" . implode( ',', array_map( 'intval', $post_ids ) ) . ")";
		if ( $categories = $wpdb->get_results( $cat_query ) ) {
			if ( is_array( $categories ) ) {
				$terms = $categories;
			}
		}

		$cached = wp_cache_set( $cache_key, $terms, self::TERM_CACHE_GROUP, self::get_cache_lifetime() );

		return $terms;
	}

	public static function request_results() {

		global $wpdb, $sitepress;

		$switch_lang = false;

		if ( isset( $sitepress ) ) {
			if ( !empty( $_REQUEST['lang'] ) ) {
				if ( $sitepress->get_current_language() != $_REQUEST['lang'] ) {
					$sitepress->switch_lang( $_REQUEST['lang'] );
					$switch_lang = true;
				}
			} else {

				if ( $sitepress->get_current_language() != 'all' ) {
					$sitepress->switch_lang( 'all' );
					$switch_lang = true;
				}
			}
		}

		$tags        = isset( $_REQUEST[self::TAGS] ) ? intval( $_REQUEST[self::TAGS] ) > 0 : self::DEFAULT_TAGS;
		$limit       = isset( $_REQUEST[self::LIMIT] ) ? intval( $_REQUEST[self::LIMIT] ) : self::DEFAULT_LIMIT;
		$numberposts = intval( apply_filters( 'product_search_limit', $limit ) ); 

		$order       = isset( $_REQUEST[self::ORDER] ) ? strtoupper( trim( $_REQUEST[self::ORDER] ) ) : self::DEFAULT_ORDER;
		switch ( $order ) {
			case 'DESC' :
			case 'ASC' :
				break;
			default :
				$order = 'DESC';
		}
		$order_by    = isset( $_REQUEST[self::ORDER_BY] ) ? strtolower( trim( $_REQUEST[self::ORDER_BY] ) ) : self::DEFAULT_ORDER_BY;
		switch ( $order_by ) {
			case 'date' :
			case 'title' :
			case 'ID' :
			case 'rand' :
				break;
			default :
				$order_by = 'date';
		}

		$product_thumbnails  = isset( $_REQUEST[self::PRODUCT_THUMBNAILS] ) ? intval( $_REQUEST[self::PRODUCT_THUMBNAILS] ) > 0 : self::DEFAULT_PRODUCT_THUMBNAILS;

		$category_results    = isset( $_REQUEST[self::CATEGORY_RESULTS] ) ? intval( $_REQUEST[self::CATEGORY_RESULTS] ) > 0 : self::DEFAULT_CATEGORY_RESULTS;
		$category_limit      = isset( $_REQUEST[self::CATEGORY_LIMIT] ) ? intval( $_REQUEST[self::CATEGORY_LIMIT] ) : self::DEFAULT_CATEGORY_LIMIT;

		$search_query = trim( preg_replace( '/\s+/', ' ', $_REQUEST[self::SEARCH_QUERY] ) );
		$search_terms = explode( ' ', $search_query );

		$include = self::get_post_ids_for_request();

		$results = array();
		$post_ids = array();
		if ( count( $include ) > 0 ) {

			$query_args = array(
				'fields'      => 'ids',
				'post_type'   => 'product',
				'post_status' => 'publish',
				'numberposts' => $numberposts, 
				'include'     => $include,
				'order'       => $order,
				'orderby'     => $order_by,
				'suppress_filters' => 0 
			);
			self::pre_get_posts();
			$posts = get_posts( $query_args );
			self::post_get_posts();
			$i = 0; 
			foreach ( $posts as $post ) {

				if ( $post = get_post( $post ) ) {

					$post_ids[] = $post->ID;

					$thumbnail_url = null;
					$thumbnail_alt = null;
					if ( $thumbnail_id = get_post_thumbnail_id( $post ) ) {
						if ( $image = wp_get_attachment_image_src( $thumbnail_id, WooCommerce_Product_Search_Thumbnail::thumbnail_size_name(), false ) ) {
							$thumbnail_url    = $image[0];
							$thumbnail_width  = $image[1];
							$thumbnail_height = $image[2];

							$thumbnail_alt = trim( strip_tags( get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ) );
							if ( empty( $thumbnail_alt ) ) {
								if ( $attachment = get_post( $thumbnail_id ) ) {
									$thumbnail_alt = trim( strip_tags( $attachment->post_excerpt ) );
									if ( empty( $thumbnail_alt ) ) {
										$thumbnail_alt = trim( strip_tags( $attachment->post_title ) );
									}
								}
							}
						}
					}

					if ( $thumbnail_url === null ) {
						$placeholder = WooCommerce_Product_Search_Thumbnail::get_placeholder_thumbnail();
						if ( $placeholder !== null ) {
							list( $thumbnail_url, $thumbnail_width, $thumbnail_height ) = $placeholder;
							$thumbnail_alt = __( 'Placeholder Image', 'woocommerce-product-search' );
						}
					}
					$results[$post->ID] = array(
						'id'    => $post->ID,
						'type'  => 'product',
						'url'   => get_permalink( $post ),
						'title' => get_the_title( $post ),
						'description' => wp_strip_all_tags( apply_filters( 'woocommerce_short_description', $post->post_excerpt ) ),
						'i'     => $i
					);
					if ( $product_thumbnails ) {
						if ( $thumbnail_url !== null ) {
							$results[$post->ID]['thumbnail']        = $thumbnail_url;
							$results[$post->ID]['thumbnail_width']  = $thumbnail_width;
							$results[$post->ID]['thumbnail_height'] = $thumbnail_height;
							if ( !empty( $thumbnail_alt ) ) {
								$results[$post->ID]['thumbnail_alt'] = $thumbnail_alt;
							}
						}
					}
					if ( function_exists( 'wc_get_product' ) ) {
						if ( $product = wc_get_product( $post ) ) {
							$price_html = $product->get_price_html();
							$results[$post->ID]['price'] = $price_html;
							unset( $product );
						}
					}
					$i++;

					if ( $i >= $numberposts ) {
						break;
					}

					unset( $post );
				}
			}
			unset( $posts );

			usort( $results, array( __CLASS__, 'usort' ) );
		}

		$c_results = array();
		if ( $category_results ) {
			$c_i = 0;
			if ( !empty( $post_ids ) ) {
				$categories = self::get_terms_for_request( $post_ids );
				foreach ( $categories as $category ) {
					$c_url = add_query_arg(
						array(
							's'           => $search_query,
							'post_type'   => 'product',
							'product_cat' => $category->slug,
							'ixwps'       => 1,
							self::TAGS    => $tags ? '1' : '0'
						),
						home_url( '/' )
					);
					if ( !is_wp_error( $c_url ) ) {
						$c_results[$category->term_id] = array(
							'id'    => $category->term_id,
							'type'  => 's_product_cat',
							'url'   => $c_url,
							'title' => sprintf(
								__( 'Search in %s', 'woocommerce-product-search' ),
								esc_html( self::single_term_title( apply_filters( 'single_term_title', $category->name ) ) )
							),
							'i'     => $i
						);
					}
					$i++;
					$c_i++;
					if ( $c_i >= $category_limit ) {
						break;
					}
				}
			}
			usort( $c_results, array( __CLASS__, 'usort' ) );
			$results = array_merge( $results, $c_results );
		}

		if ( $switch_lang ) {
			$sitepress->switch_lang();
		}

		return $results;
	}

	private static function get_cache_key( $parameters ) {
		return md5( implode( '-', $parameters ) );
	}

	private static function get_cache_lifetime() {
		$l = intval( apply_filters( 'woocommerce_product_search_cache_lifetime', self::CACHE_LIFETIME ) );
		return $l;
	}

	public static function single_term_title( $title ) {
		$language = isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : null;
		if ( $language !== null ) {
			$title = str_replace( '@' . $language, '', $title );
		}
		return $title;
	}

	public static function icl_set_current_language( $lang ) {
		$language = isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : null;
		if ( $language !== null ) {
			$lang = $language;
		}
		return $lang;
	}

	public static function usort( $e1, $e2 ) {
		return $e1['i'] - $e2['i'];
	}

	private static function pre_get_posts() {
		global $wpml_query_filter, $wps_removed_wpml_query_filter;
		if ( isset( $wpml_query_filter ) ) {
			$language = !empty( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : null;
			if ( $language === null ) {
				$wps_removed_wpml_query_filter = remove_filter( 'posts_where', array( $wpml_query_filter, 'posts_where_filter' ), 10, 2 );
			}
		}
	}

	private static function post_get_posts() {
		global $wpml_query_filter, $wps_removed_wpml_query_filter;
		if ( isset( $wpml_query_filter ) ) {
			if ( $wps_removed_wpml_query_filter ) {
				if ( has_filter( 'posts_where', array( $wpml_query_filter, 'posts_where_filter' ) ) === false ) {
					add_filter( 'posts_where', array( $wpml_query_filter, 'posts_where_filter' ), 10, 2 );
				}
			}
		}
	}

	public static function get_ft_min_word_len() {

		global $wpdb;

		$ft_min_word_len = null;

		if ( $row = $wpdb->get_row( "show global variables like 'ft_min_word_len'" ) ) {
			if ( $row->Variable_name == 'ft_min_word_len' ) {
				$ft_min_word_len = $row->Value;
			}
		}
		if ( $ft_min_word_len === null ) {
			$performance_schema = $wpdb->get_row( "show variables like 'performance_schema'" );
			if (
				$performance_schema &&
				isset( $performance_schema->Variable_name ) &&
				( $performance_schema->Variable_name == 'performance_schema' ) &&
				isset( $performance_schema->Value ) &&
				( strcasecmp( 'ON', $performance_schema->Value ) === 0 )
			) {
				$ft_min_word_len = $wpdb->get_var( "SELECT variable_value FROM performance_schema.global_variables WHERE variable_name = 'ft_min_word_len'" );
			}
			if ( $ft_min_word_len === null ) {
				$ft_min_word_len = $wpdb->get_var( "SELECT variable_value FROM information_schema.global_variables WHERE variable_name = 'ft_min_word_len'" );
			}
		}

		return $ft_min_word_len;
	}
}
WooCommerce_Product_Search_Service::init();
