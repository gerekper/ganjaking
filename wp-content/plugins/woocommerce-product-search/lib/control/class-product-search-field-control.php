<?php
/**
 * class-product-search-field-control.php
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
 * @since 5.0.0
 */

namespace com\itthinx\woocommerce\search\engine;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Search Field Control.
 */
class Product_Search_Field_Control {

	const LIMIT         = 'limit';
	const DEFAULT_LIMIT = 10;
	const MAX_LIMIT     = 100;

	const TITLE         = 'title';
	const EXCERPT       = 'excerpt';
	const CONTENT       = 'content';
	const CATEGORIES    = 'categories';
	const TAGS          = 'tags';
	const SKU           = 'sku';
	const ATTRIBUTES    = 'attributes';
	const VARIATIONS    = 'variations';

	const MIN_PRICE     = 'min_price';
	const MAX_PRICE     = 'max_price';

	const ON_SALE       = 'on_sale';
	const RATING        = 'rating';
	const IN_STOCK      = 'in_stock';

	const DEFAULT_TITLE      = true;
	const DEFAULT_EXCERPT    = true;
	const DEFAULT_CONTENT    = true;
	const DEFAULT_TAGS       = true;
	const DEFAULT_CATEGORIES = true;
	const DEFAULT_SKU        = true;
	const DEFAULT_ATTRIBUTES = true;
	const DEFAULT_VARIATIONS = false;

	const DEFAULT_ON_SALE  = false;
	const DEFAULT_RATING   = null;
	const DEFAULT_IN_STOCK = false;

	const ORDER            = 'order';
	const DEFAULT_ORDER    = 'DESC';
	const ORDER_BY         = 'order_by';
	const DEFAULT_ORDER_BY = 'date';

	const PRODUCT_THUMBNAILS         = 'product_thumbnails';
	const DEFAULT_PRODUCT_THUMBNAILS = true;

	const CATEGORY_RESULTS         = 'category_results';
	const DEFAULT_CATEGORY_RESULTS = true;
	const CATEGORY_LIMIT           = 'category_limit';
	const DEFAULT_CATEGORY_LIMIT   = 5;

	const CACHE_LIFETIME   = Cache::HOUR;
	const TERM_CACHE_GROUP = 'ixwpst';

	private static $posts_stage_count = null;

	/**
	 * Register action and filter hooks.
	 */
	public static function init() {
		add_action( 'wp_ajax_product_search', array( __CLASS__, 'wp_ajax_product_search' ) );
		add_action( 'wp_ajax_nopriv_product_search', array( __CLASS__, 'wp_ajax_product_search' ) );
	}

	/**
	 * Handles wp_ajax_product_search and wp_ajax_nopriv_product_search actions.
	 */
	public static function wp_ajax_product_search() {

		global $wps_doing_ajax;
		$wps_doing_ajax = true;

		ob_start();
		$results = self::request_results();
		$ob = ob_get_clean();
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG && $ob ) {
			wps_log_error( $ob );
		}
		echo json_encode( $results );
		exit;
	}

	/**
	 * Returns eligible post status or post statuses.
	 *
	 * @return string|array post status or statuses
	 */
	public static function get_post_status() {

		global $wps_doing_ajax;

		if ( is_admin() && !isset( $wps_doing_ajax ) ) {
			$status = array( 'publish', 'pending', 'draft' );
			if ( current_user_can( 'edit_private_products' ) ) {
				$status[] = 'private';
			}
		} else {
			$status = 'publish';
			if ( current_user_can( 'edit_private_products' ) ) {
				$status = array( 'publish', 'private' );
			}
		}
		return $status;
	}

	/**
	 * Request parameters.
	 *
	 * @return array
	 */
	private static function get_request_parameters() {
		$title      = isset( $_REQUEST[self::TITLE] ) ? intval( $_REQUEST[self::TITLE] ) > 0 : self::DEFAULT_TITLE;
		$excerpt    = isset( $_REQUEST[self::EXCERPT] ) ? intval( $_REQUEST[self::EXCERPT] ) > 0 : self::DEFAULT_EXCERPT;
		$content    = isset( $_REQUEST[self::CONTENT] ) ? intval( $_REQUEST[self::CONTENT] ) > 0 : self::DEFAULT_CONTENT;
		$tags       = isset( $_REQUEST[self::TAGS] ) ? intval( $_REQUEST[self::TAGS] ) > 0 : self::DEFAULT_TAGS;
		$sku        = isset( $_REQUEST[self::SKU] ) ? intval( $_REQUEST[self::SKU] ) > 0 : self::DEFAULT_SKU;
		$categories = isset( $_REQUEST[self::CATEGORIES] ) ? intval( $_REQUEST[self::CATEGORIES] ) > 0 : self::DEFAULT_CATEGORIES;
		$attributes = isset( $_REQUEST[self::ATTRIBUTES] ) ? intval( $_REQUEST[self::ATTRIBUTES] ) > 0 : self::DEFAULT_ATTRIBUTES;
		$variations = isset( $_REQUEST[self::VARIATIONS] ) ? intval( $_REQUEST[self::VARIATIONS] ) > 0 : self::DEFAULT_VARIATIONS;
		$min_price  = isset( $_REQUEST[self::MIN_PRICE] ) ? \WooCommerce_Product_Search_Utility::to_float( $_REQUEST[self::MIN_PRICE] ) : null;
		$max_price  = isset( $_REQUEST[self::MAX_PRICE] ) ? \WooCommerce_Product_Search_Utility::to_float( $_REQUEST[self::MAX_PRICE] ) : null;
		$on_sale    = isset( $_REQUEST[self::ON_SALE] ) ? intval( $_REQUEST[self::ON_SALE] ) > 0 : self::DEFAULT_ON_SALE;
		$rating     = isset( $_REQUEST[self::RATING] ) ? intval( $_REQUEST[self::RATING] ) : self::DEFAULT_RATING;
		$in_stock   = isset( $_REQUEST[self::IN_STOCK] ) ? intval( $_REQUEST[self::IN_STOCK] ) > 0 : self::DEFAULT_IN_STOCK;

		$search_query = isset( $_REQUEST[Base::SEARCH_QUERY] ) && is_string( $_REQUEST[Base::SEARCH_QUERY] ) ? sanitize_text_field( $_REQUEST[Base::SEARCH_QUERY] ) : '';
		$search_query = trim( preg_replace( '/\s+/', ' ', $search_query ) );

		$limit = isset( $_REQUEST[self::LIMIT] ) && is_numeric( $_REQUEST[self::LIMIT] ) ? intval( $_REQUEST[self::LIMIT] ) : self::DEFAULT_LIMIT;
		$limit = max( 0, intval( apply_filters( 'product_search_limit', min( $limit, self::MAX_LIMIT ) ) ) );

		$order = isset( $_REQUEST[self::ORDER] ) ? strtoupper( trim( $_REQUEST[self::ORDER] ) ) : self::DEFAULT_ORDER;
		switch ( $order ) {
			case 'DESC' :
			case 'ASC' :
				break;
			default :
				$order = 'DESC';
		}

		$order_by = isset( $_REQUEST[self::ORDER_BY] ) ? strtolower( trim( $_REQUEST[self::ORDER_BY] ) ) : self::DEFAULT_ORDER_BY;
		switch ( $order_by ) {
			case 'date' :
			case 'title' :
			case 'ID' :
			case 'rand' :
			case 'sku' :
			case '' :
			case 'popularity' :
			case 'rating' :
				break;
			default :
				$order_by = 'date';
		}

		$product_thumbnails = isset( $_REQUEST[self::PRODUCT_THUMBNAILS] ) ? intval( $_REQUEST[self::PRODUCT_THUMBNAILS] ) > 0 : self::DEFAULT_PRODUCT_THUMBNAILS;
		$category_results   = isset( $_REQUEST[self::CATEGORY_RESULTS] ) ? intval( $_REQUEST[self::CATEGORY_RESULTS] ) > 0 : self::DEFAULT_CATEGORY_RESULTS;
		$category_limit     = isset( $_REQUEST[self::CATEGORY_LIMIT] ) ? intval( $_REQUEST[self::CATEGORY_LIMIT] ) : self::DEFAULT_CATEGORY_LIMIT;

		$parameters = array(
			'title' => $title,
			'excerpt' => $excerpt,
			'content' => $content,
			'tags' => $tags,
			'sku' => $sku,
			'categories' => $categories,
			'attributes' => $attributes,
			'variations' => $variations,
			'min_price' => $min_price,
			'max_price' => $max_price,
			'on_sale' => $on_sale,
			'rating' => $rating,
			'in_stock' => $in_stock,
			'search_query' => $search_query,
			'limit' => $limit,
			'order' => $order,
			'orderby' => $order_by,
			'product_thumbnails' => $product_thumbnails,
			'category_results' => $category_results,
			'category_limit' => $category_limit
		);
		return $parameters;
	}

	/**
	 * Computes a cache key based on the parameters provided.
	 *
	 * @param array $parameters set of parameters for which to compute the key
	 *
	 * @return string
	 */
	protected static function get_cache_key( $parameters ) {

		return md5( json_encode( $parameters ) );
	}

	/**
	 * Provide results
	 *
	 * @param $params array
	 *
	 * @return array
	 */
	public static function get_post_ids_for_request( $params = null ) {

		global $wps_doing_ajax;

		$title      = $params['title'];
		$excerpt    = $params['excerpt'];
		$content    = $params['content'];
		$tags       = $params['tags'];
		$sku        = $params['sku'];
		$categories = $params['categories'];
		$attributes = $params['attributes'];
		$variations = $params['variations'];
		$limit      = $params['limit'];
		$min_price  = $params['min_price'];
		$max_price  = $params['max_price'];
		if ( $min_price !== null && $min_price <= 0 ) {
			$min_price = null;
		}
		if ( $max_price !== null && $max_price <= 0 ) {
			$max_price = null;
		}
		if ( $min_price !== null && $max_price !== null && $max_price < $min_price ) {
			$max_price = null;
		}
		\WooCommerce_Product_Search_Service::min_max_price_adjust( $min_price, $max_price );
		$on_sale = $params['on_sale'];
		$rating = $params['rating'];
		if ( $rating !== self::DEFAULT_RATING ) {
			if ( $rating < \WooCommerce_Product_Search_Filter_Rating::MIN_RATING ) {
				$rating = \WooCommerce_Product_Search_Filter_Rating::MIN_RATING;
			}
			if ( $rating > \WooCommerce_Product_Search_Filter_Rating::MAX_RATING ) {
				$rating = \WooCommerce_Product_Search_Filter_Rating::MAX_RATING;
			}
		}
		$in_stock = $params['in_stock'];
		$search_query = $params['search_query'];
		$order = $params['order'];
		$orderby = $params['orderby'];

		if (
			!$title && !$excerpt && !$content && !$tags && !$sku && !$categories && !$attributes &&
			$min_price === null && $max_price === null &&
			!$on_sale &&
			$rating === null &&
			!$in_stock
		) {
			$title = true;
		}

		$stage_variations = true;

		$engine = new \com\itthinx\woocommerce\search\engine\Engine();

		if ( !empty( $search_query ) ) {
			$args = array(
				'q' => $search_query,
				'title' => $title,
				'excerpt' => $excerpt,
				'content' => $content,
				'tags' => $tags,
				'sku' => $sku,
				'categories' => $categories,
				'attributes' => $attributes,
				'variations' => $stage_variations
			);
			$stage = new \com\itthinx\woocommerce\search\engine\Engine_Stage_Words( $args );
			$engine->attach_stage( $stage );
		}

		if ( $min_price !== null || $max_price !== null ) {
			$args = array( 'variations' => $stage_variations );
			if ( $min_price !== null ) {
				$args['min_price'] = trim( sanitize_text_field( $min_price ) );
			}
			if ( $max_price !== null ) {
				$args['max_price'] = trim( sanitize_text_field( $max_price ) );
			}
			$stage = new \com\itthinx\woocommerce\search\engine\Engine_Stage_Price( $args );
			$engine->attach_stage( $stage );
		}

		if ( $on_sale ) {
			$args = array( 'sale' => 'onsale', 'variations' => $stage_variations );
			$stage = new \com\itthinx\woocommerce\search\engine\Engine_Stage_Sale( $args );
			$engine->attach_stage( $stage );
		}

		if ( $rating ) {
			$args = array( 'rating' => $rating, 'variations' => $stage_variations );
			$stage = new \com\itthinx\woocommerce\search\engine\Engine_Stage_Rating( $args );
			$engine->attach_stage( $stage );
		}

		if ( get_option( 'woocommerce_hide_out_of_stock_items' ) === 'yes' ) {
			$in_stock = true;
		}
		if ( $in_stock ) {
			$args = array( 'stock' => 'instock', 'variations' => $stage_variations );
			$stage = new \com\itthinx\woocommerce\search\engine\Engine_Stage_Stock( $args );
			$engine->attach_stage( $stage );
		}

		if ( is_admin() && !isset( $wps_doing_ajax ) ) {
		} else {

			global $wp_query;
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
							$wp_query->get( 'product_search', false );;
					}
				}
			}
			$visibility = null;
			if ( $is_product_search ) {
				$visibility = 'search';
			} else if ( \WooCommerce_Product_Search_Utility::is_shop() ) {
				$visibility = 'catalog';
			}
			if ( $visibility !== null ) {
				$args = array( 'visibility' => $visibility, 'variations' => $stage_variations );
				$stage = new \com\itthinx\woocommerce\search\engine\Engine_Stage_Visibility( $args );
				$engine->attach_stage( $stage );
			}
		}

		if ( $engine->get_stage_count() > 0 ) {
			$args = array( 'variations' => $stage_variations );
			$stage = new \com\itthinx\woocommerce\search\engine\Engine_Stage_Synchrotron( $args );
			$engine->attach_stage( $stage );
		}

		$post_status = self::get_post_status();
		$args = array(
			'order'      => $order,
			'orderby'    => $orderby,
			'status'     => $post_status,
			'variations' => $variations
		);
		$stage = new \com\itthinx\woocommerce\search\engine\Engine_Stage_Posts( $args );
		$engine->attach_stage( $stage );
		$posts_stage = $stage;

		if ( $limit !== null && $limit > 0 ) {
			$args = array(
				'limit' => $limit,
				'offset' => 0,
				'per_page' => null
			);
			$stage = new \com\itthinx\woocommerce\search\engine\Engine_Stage_Pagination( $args );
			$engine->attach_stage( $stage );
		}

		$ids = $engine->get_ids();

		self::$posts_stage_count = $posts_stage->get_count();

		if ( has_action( 'woocommerce_product_search_service_post_ids_for_request' ) ) {
			$context = array(
				'title'        => $title,
				'excerpt'      => $excerpt,
				'content'      => $content,
				'tags'         => $tags,
				'sku'          => $sku,
				'categories'   => $categories,
				'attributes'   => $attributes,
				'variations'   => $variations,
				'search_query' => $search_query,
				'min_price'    => $min_price,
				'max_price'    => $max_price,
				'on_sale'      => $on_sale,
				'rating'       => $rating,
				'in_stock'     => $in_stock
			);
			do_action_ref_array(
				'woocommerce_product_search_service_post_ids_for_request',
				array( &$ids, $context )
			);
			foreach ( $ids as $key => $value ) {
				$ids[$key] = intval( $value );
			}
		}

		$count = count( $ids );

		if ( !empty( $search_query ) ) {
			$record_search_query = \WooCommerce_Product_Search_Indexer::equalize( $search_query );
			\WooCommerce_Product_Search_Service::maybe_record_hit( $record_search_query, $count );
		}

		return $ids;
	}

	/**
	 * Retrieve terms.
	 *
	 * @param array $post_ids set of post IDs
	 *
	 * @return array[] array of arrays, mapping property keys to property values for each term
	 */
	public static function get_product_categories_for_request( $params ) {

		global $wpdb;

		$cache_key = self::get_cache_key( $params );
		$cache = Cache::get_instance();
		$terms = $cache->get( $cache_key, self::TERM_CACHE_GROUP );
		if ( is_array( $terms ) ) {
			return $terms;
		}

		$terms = array();

		if ( !\WooCommerce_Product_Search_Controller::table_exists( 'object_term' ) ) {
			return $terms;
		}

		$params['limit'] = null;
		$params['order'] = null;
		$params['orderby'] = null;
		self::maybe_remove_wpml_query_filter();
		$ids = self::get_post_ids_for_request( $params );
		self::maybe_add_wpml_query_filter();

		if ( count( $ids ) > 0 ) {

			Tools::unique_int( $ids );
			$object_term_table = \WooCommerce_Product_Search_Controller::get_tablename( 'object_term' );
			$cat_query =
				"SELECT t.* FROM $wpdb->terms t WHERE t.term_id IN ( " .
				"SELECT DISTINCT term_id FROM $object_term_table WHERE " .
				"taxonomy = 'product_cat' " .
				'AND object_id IN ( ' . implode( ',', $ids ) . ' ) ' .
				')';
			$categories = $wpdb->get_results( $cat_query );
			if ( is_array( $categories ) ) {

				$term_ids = array();
				foreach ( $categories as $category ) {
					$term_ids[] = (int) $category->term_id;
				}
				if ( count( $term_ids ) > 0 ) {
					$term_objects = get_terms( array( 'taxonomy' => 'product_cat', 'include' => $term_ids ) );
					if ( is_array( $term_objects ) ) {
						foreach ( $term_objects as $term_object ) {
							$terms[] = array(
								'term_id' => $term_object->term_id,
								'count' => $term_object->count,
								'description' => $term_object->description,
								'filter' => $term_object->filter,
								'name' => $term_object->name,
								'parent' => $term_object->parent,
								'slug' => $term_object->slug,
								'taxonomy' => $term_object->taxonomy,
								'term_group' => $term_object->term_group,
								'term_taxonomy_id' => $term_object->term_taxonomy_id
							);
						}
					}
				}
			}
		}

		$cache->set( $cache_key, $terms, self::TERM_CACHE_GROUP, self::CACHE_LIFETIME );

		return $terms;
	}

	/**
	 * Obtain results
	 *
	 * @return array
	 */
	public static function request_results() {

		$switch_lang = self::wpml_maybe_switch_lang();

		$settings = Settings::get_instance();
		$use_short_description = $settings->get( \WooCommerce_Product_Search::USE_SHORT_DESCRIPTION, \WooCommerce_Product_Search::USE_SHORT_DESCRIPTION_DEFAULT );

		$params = self::get_request_parameters();
		$tags               = $params['tags'];
		$limit              = $params['limit'];
		$order              = $params['order'];
		$order_by           = $params['orderby'];
		$product_thumbnails = $params['product_thumbnails'];
		$category_results   = $params['category_results'];
		$category_limit     = $params['category_limit'];
		$search_query       = $params['search_query'];

		self::maybe_remove_wpml_query_filter();
		$ids = self::get_post_ids_for_request( $params );
		self::maybe_add_wpml_query_filter();

		$results = array();
		$i = 0;
		foreach ( $ids as $id ) {

			$product = wc_get_product( $id );

			$thumbnail_url = null;
			$thumbnail_alt = null;
			if ( $thumbnail_id = get_post_thumbnail_id( $id ) ) {
				if ( $image = wp_get_attachment_image_src( $thumbnail_id, \WooCommerce_Product_Search_Thumbnail::thumbnail_size_name(), false ) ) {
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
				$placeholder = \WooCommerce_Product_Search_Thumbnail::get_placeholder_thumbnail();
				if ( $placeholder !== null ) {
					list( $thumbnail_url, $thumbnail_width, $thumbnail_height ) = $placeholder;
					$thumbnail_alt = __( 'Placeholder Image', 'woocommerce-product-search' );
				}
			}
			$title = self::shorten( wp_strip_all_tags( $product->get_title() ), 'title' );
			$_description = '';
			if ( $use_short_description ) {
				$_description = wc_format_content( $product->get_short_description() );
			}
			if ( empty( $_description ) ) {
				$_description = wc_format_content( $product->get_description() );
			}
			$description = self::shorten( wp_strip_all_tags( $_description ), 'description' );
			$results[$id] = array(
				'id'    => $id,
				'type'  => 'product',
				'url'   => get_permalink( $id ),
				'title' => apply_filters( 'woocommerce_product_search_field_product_title', $title, $id ),
				'description' => apply_filters( 'woocommerce_product_search_field_product_description', $description, $id ),
				'i'     => $i
			);
			if ( $product_thumbnails ) {
				if ( $thumbnail_url !== null ) {
					$results[$id]['thumbnail']        = $thumbnail_url;
					$results[$id]['thumbnail_width']  = $thumbnail_width;
					$results[$id]['thumbnail_height'] = $thumbnail_height;
					if ( !empty( $thumbnail_alt ) ) {
						$results[$id]['thumbnail_alt'] = $thumbnail_alt;
					}
				}
			}
			$price_html = $product->get_price_html();
			$results[$id]['price'] = apply_filters( 'woocommerce_product_search_field_product_price_html', $price_html, $id );
			$add_to_cart_html = self::get_add_to_cart( $id );
			$results[$id]['add_to_cart'] = apply_filters( 'woocommerce_product_search_field_product_add_to_cart_html', $add_to_cart_html, $id );
			$i++;

			if ( $limit > 0 && $i >= $limit ) {
				break;
			}
		}

		usort( $results, array( __CLASS__, 'usort' ) );

		if ( self::$posts_stage_count > $limit ) {
			$url_query_args = array(
				's'          => urlencode( $search_query ),
				'post_type'  => 'product',
				'ixwps'      => 1,
				'title'      => $params['title'],
				'excerpt'    => $params['excerpt'],
				'content'    => $params['content'],
				'categories' => $params['categories'],
				'attributes' => $params['attributes'],
				'tags'       => $params['tags'],
				'sku'        => $params['sku']
			);

			$orderby_value = '';
			if ( !empty( $order_by ) ) {
				$orderby_value = $order_by;
				if ( !empty( $order ) ) {
					$orderby_value .= '-' . $order;
				}
			}

			if ( $orderby_value !== '' ) {
				$url_query_args['orderby'] = urlencode( $orderby_value );
			}
			$results[PHP_INT_MAX] = array(
				'id'      => PHP_INT_MAX,
				'type'    => 's_more',
				'url'     => add_query_arg( $url_query_args, home_url( '/' ) ),
				'title'   => esc_html( apply_filters( 'woocommerce_product_search_field_more_title', __( 'more &hellip;', 'woocommerce-product-search' ) ) ),
				'a_title' => esc_html( apply_filters( 'woocommerce_product_search_field_more_anchor_title', __( 'Search for more &hellip;', 'woocommerce-product-search' ) ) ),
				'i'       => $i
			);
			$i++;

			usort( $results, array( __CLASS__, 'usort' ) );
		}

		$c_results = array();
		if ( $category_results ) {
			$c_i = 0;
			if ( !empty( $ids ) ) {
				$categories = self::get_product_categories_for_request( $params );
				foreach ( $categories as $category ) {
					$variables = array(
						'post_type'   => 'product',
						'product_cat' => $category['slug'],
						'ixwps'       => 1,
						self::TAGS    => $tags ? '1' : '0'
					);
					if ( !isset( $_REQUEST['ixwpss'] ) ) {
						$variables['s'] = $search_query;
					} else {
						$variables['ixwpss'] = $search_query;
					}
					$c_url = add_query_arg(
						$variables,
						home_url( '/' )
					);
					if ( !is_wp_error( $c_url ) ) {
						$c_results[$category['term_id']] = array(
							'id'    => $category['term_id'],
							'type'  => 's_product_cat',
							'url'   => $c_url,
							'title' => sprintf(

								esc_html( __( 'Search in %s', 'woocommerce-product-search' ) ),
								esc_html( self::single_term_title( apply_filters( 'single_term_title', $category['name'] ) ) )
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

		self::wpml_maybe_restore_lang( $switch_lang );

		return $results;
	}

	/**
	 * Filter out the WPML language suffix from term titles.
	 *
	 * @param string $title term title
	 */
	public static function single_term_title( $title ) {
		$language = isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : null;
		if ( $language !== null ) {
			$title = str_replace( '@' . $language, '', $title );
		}
		return $title;
	}

	/**
	 * Index sort.
	 *
	 * @param array $e1 first element
	 * @param array $e2 second element
	 *
	 * @return int
	 */
	public static function usort( $e1, $e2 ) {
		return $e1['i'] - $e2['i'];
	}

	/**
	 * Switch language for request.
	 *
	 * @return boolean language switched
	 */
	private static function wpml_maybe_switch_lang() {
		global $sitepress;
		$switch_lang = false;

		if ( isset( $sitepress ) && is_object( $sitepress ) && method_exists( $sitepress, 'get_current_language' ) && method_exists( $sitepress, 'switch_lang' ) ) {
			if ( !empty( $_REQUEST['lang'] ) ) {
				if ( $sitepress->get_current_language() !== $_REQUEST['lang'] ) {
					$sitepress->switch_lang( $_REQUEST['lang'] );
					$switch_lang = true;
				}
			} else {

				if ( $sitepress->get_current_language() !== 'all' ) {
					$sitepress->switch_lang( 'all' );
					$switch_lang = true;
				}
			}
		}
		return $switch_lang;
	}

	/**
	 * Restore language.
	 *
	 * @param boolean $switch_lang restore language
	 */
	private static function wpml_maybe_restore_lang( $switch_lang ) {
		global $sitepress;

		if ( $switch_lang ) {
			$sitepress->switch_lang();
		}
	}

	/**
	 * Used to temporarily remove the WPML query filter on posts_where.
	 */
	private static function maybe_remove_wpml_query_filter() {
		global $wpml_query_filter, $wps_removed_wpml_query_filter;
		if ( isset( $wpml_query_filter ) ) {
			$language = !empty( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : null;
			if ( $language === null ) {
				$wps_removed_wpml_query_filter = remove_filter( 'posts_where', array( $wpml_query_filter, 'posts_where_filter' ), 10, 2 );
			}
		}
	}

	/**
	 * Reinstates the WPML query filter on posts_where.
	 */
	private static function maybe_add_wpml_query_filter() {
		global $wpml_query_filter, $wps_removed_wpml_query_filter;
		if ( isset( $wpml_query_filter ) ) {
			if ( $wps_removed_wpml_query_filter ) {
				if ( has_filter( 'posts_where', array( $wpml_query_filter, 'posts_where_filter' ) ) === false ) {
					add_filter( 'posts_where', array( $wpml_query_filter, 'posts_where_filter' ), 10, 2 );
				}
			}
		}
	}

	/**
	 * Returns the shortened content.
	 *
	 * @param string $content description to shorten
	 * @param string $what what's to be shortened, 'description' by default or 'title'
	 *
	 * @return string shortened description
	 */
	private static function shorten( $content, $what = 'description' ) {

		$settings = Settings::get_instance();

		switch ( $what ) {
			case 'description' :
			case 'title' :
				break;
			default :
				$what = 'description';
		}

		switch( $what ) {
			case 'title' :
				$max_words = $settings->get( \WooCommerce_Product_Search::MAX_TITLE_WORDS, \WooCommerce_Product_Search::MAX_TITLE_WORDS_DEFAULT );
				$max_characters = $settings->get( \WooCommerce_Product_Search::MAX_TITLE_CHARACTERS, \WooCommerce_Product_Search::MAX_TITLE_CHARACTERS_DEFAULT );
				break;
			default :
				$max_words = $settings->get( \WooCommerce_Product_Search::MAX_EXCERPT_WORDS, \WooCommerce_Product_Search::MAX_EXCERPT_WORDS_DEFAULT );
				$max_characters = $settings->get( \WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS, \WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS_DEFAULT );
		}

		$ellipsis = esc_html( apply_filters( 'woocommerce_product_search_shorten_ellipsis', '&hellip;', $content, $what ) );

		$output = '';

		if ( $max_words > 0 ) {
			$content = preg_replace( '/\s+/', ' ', $content );
			$words = explode( ' ', $content );
			$nwords = count( $words );
			for ( $i = 0; ( $i < $max_words ) && ( $i < $nwords ); $i++ ) {
				$output .= $words[$i];
				if ( $i < $max_words - 1) {
					$output .= ' ';
				} else {
					$output .= $ellipsis;
				}
			}
		} else {
			$output = $content;
		}

		if ( $max_characters > 0 ) {
			if ( function_exists( 'mb_substr' ) ) {
				$charset = get_bloginfo( 'charset' );
				$output = html_entity_decode( $output );
				$length = mb_strlen( $output );
				$output = mb_substr( $output, 0, $max_characters );
				if ( mb_strlen( $output ) < $length ) {
					$output .= $ellipsis;
				}
				$output = htmlentities( $output, ENT_COMPAT | ENT_HTML401, $charset, false );
			} else {
				$length = strlen( $output );
				$output = substr( $output, 0, $max_characters );
				if ( strlen( $output ) < $length ) {
					$output .= $ellipsis;
				}
			}
		}
		return $output;
	}

	/**
	 * Returns the HTML for the add to cart button of the product.
	 *
	 * @param int $product_id ID of the product
	 *
	 * @return string add to cart HTML
	 */
	private static function get_add_to_cart( $product_id ) {

		global $post;

		$ajax_add_to_cart_enabled = 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' );
		if ( !$ajax_add_to_cart_enabled ) {
			add_filter( 'woocommerce_product_add_to_cart_url', array( __CLASS__, 'woocommerce_product_add_to_cart_url' ), 10, 2 );
		}

		$output = '';
		if ( function_exists( 'woocommerce_template_loop_add_to_cart' ) ) {
			if ( $product = wc_setup_product_data( $product_id ) ) {
				ob_start();
				woocommerce_template_loop_add_to_cart( array( 'quantity' => 1 ) );

				wc_setup_product_data( $post );
				$output = ob_get_clean();
			}
		}

		if ( !$ajax_add_to_cart_enabled ) {
			remove_filter( 'woocommerce_product_add_to_cart_url', array( __CLASS__, 'woocommerce_product_add_to_cart_url' ), 10 );
		}

		return $output;
	}

	/**
	 * Adjust URL to exclude admin-ajax from request.
	 *
	 * @param string $url
	 * @param \WC_Product $product
	 *
	 * @return string
	 */
	public static function woocommerce_product_add_to_cart_url( $url, $product ) {
		if ( $product->is_purchasable() && $product->is_in_stock() && !$product->is_type( 'variable' ) ) {
			$url = remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $product->get_id(), wc_get_page_permalink( 'shop' ) ) );
		} else {
			$url = get_permalink( $product->get_id() );
		}
		return $url;
	}

}

Product_Search_Field_Control::init();
