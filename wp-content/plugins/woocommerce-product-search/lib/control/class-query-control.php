<?php
/**
 * class-query-control.php
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
 * Query Control.
 */
class Query_Control {

	const LIMIT         = 'limit';
	const DEFAULT_LIMIT = 0;

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

	const DEFAULT_ON_SALE    = false;
	const DEFAULT_RATING     = null;
	const DEFAULT_IN_STOCK   = false;

	const ORDER    = 'order';
	const ORDER_BY = 'order_by';

	const OBJECT_TERM_LIMIT = 100;

	const PRE_GET_POSTS_ACTION_PRIORITY = 10000;

	/**
	 * @var boolean
	 */
	private static $do_pre_get_posts = true;

	/**
	 * @var Query_Control
	 */
	private static $instance = null;

	/**
	 * @var \WP_Query
	 */
	private $query = null;

	/**
	 * @var bool|int|null
	 */
	private $pre_get_posts = null;

	/**
	 * @var boolean
	 */
	private $handle_query = false;

	/**
	 * @var boolean
	 */
	private $doing_pre_get_posts = false;

	/**
	 * @var array
	 */
	private static $parameters = array();

	/**
	 * Initialize class and pre_get_posts handler instance.
	 */
	public static function init() {
		if ( self::$do_pre_get_posts ) {
			if ( self::$instance === null ) {
				self::$instance = new Query_Control();
				add_action( 'pre_get_posts', array( self::$instance, 'pre_get_posts' ), self::PRE_GET_POSTS_ACTION_PRIORITY );
			}
		}
	}

	/**
	 * Enable or disable pre_get_posts processing.
	 *
	 * @param boolean $do
	 */
	public static function do_pre_get_posts( $do ) {
		self::$do_pre_get_posts = boolval( $do );
	}

	/**
	 * Provide the main instance.
	 *
	 * @return \com\itthinx\woocommerce\search\engine\Query_Control
	 */
	public static function get_instance() {
		return self::$instance;
	}

	/**
	 * Determine whether the query should be handled.
	 *
	 * @param boolean $handle
	 */
	public function set_handle_query( $handle ) {

		if ( is_bool( $handle ) ) {
			$this->handle_query = boolval( $handle );
		}
	}

	/**
	 * Whether the query should be handled.
	 *
	 * @return boolean
	 */
	public function get_handle_query() {
		return $this->handle_query;
	}

	/**
	 * New instance initialization.
	 */
	public function __construct() {

		add_action( 'woocommerce_product_search_engine_process_start', array( $this, 'woocommerce_product_search_engine_process_start' ) );
		add_action( 'woocommerce_product_search_engine_process_end', array( $this, 'woocommerce_product_search_engine_process_end' ) );
	}

	/**
	 * Instance destruction.
	 */
	public function __destruct() {
		remove_action( 'woocommerce_product_search_engine_process_start', array( $this, 'woocommerce_product_search_engine_process_start' ) );
		remove_action( 'woocommerce_product_search_engine_process_end', array( $this, 'woocommerce_product_search_engine_process_end' ) );
	}

	/**
	 * Engine processing start action handler.
	 *
	 * @param Engine $engine
	 */
	public function woocommerce_product_search_engine_process_start( $engine ) {
		$this->pre_get_posts = has_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
		if ( $this->pre_get_posts !== false ) {
			remove_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
		}
	}

	/**
	 * Engine processing end action handler.
	 *
	 * @param Engine $engine
	 */
	public function woocommerce_product_search_engine_process_end( $engine ) {
		if ( $this->pre_get_posts !== null && $this->pre_get_posts !== false ) {
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), self::PRE_GET_POSTS_ACTION_PRIORITY );
		}
	}

	/**
	 * Set the WP_Query for this instance, or remove it by supplying null.
	 *
	 * @param \WP_Query $query
	 */
	public function set_query( $query ) {
		if ( $query instanceof \WP_Query || $query === null ) {
			$this->query = $query;
		}
	}

	/**
	 * The stored query for this instance.
	 *
	 * @return \WP_Query|null
	 */
	public function get_query() {
		return $this->query;
	}

	/**
	 * Handler for pre_get_posts.
	 *
	 * @param \WP_Query $wp_query query object
	 */
	public function pre_get_posts( $wp_query ) {

		if ( self::$do_pre_get_posts ) {
			$this->doing_pre_get_posts = true;
			$this->process_query( $wp_query );
			$this->doing_pre_get_posts = false;
		}
	}

	/**
	 * Request parameters.
	 *
	 * @return array
	 */
	public function get_request_parameters() {

		$key = $this->query !== null ? md5( serialize( $this->query ) ) : '';
		if ( isset( self::$parameters[$key] ) ) {
			return self::$parameters[$key];
		}

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

		$search_query = isset( $_REQUEST[Base::SEARCH_QUERY] ) && is_string( $_REQUEST[Base::SEARCH_QUERY] ) ? sanitize_text_field( $_REQUEST[Base::SEARCH_QUERY] ) : null;
		if ( $search_query !== null ) {
			$search_query = trim( preg_replace( '/\s+/', ' ', $search_query ) );
			if ( strlen( $search_query ) === 0 ) {
				$search_query = null;
			}
		}
		$ixwpss = isset( $_REQUEST['ixwpss'] ) && is_string( $_REQUEST['ixwpss'] ) ? sanitize_text_field( $_REQUEST['ixwpss'] ) : null;
		if ( $ixwpss !== null ) {
			$ixwpss = trim( preg_replace( '/\s+/', ' ', $ixwpss ) );
			if ( strlen( $ixwpss ) === 0 ) {
				$ixwpss = null;
			}
		}
		if ( $search_query === null ) {
			$s = \WooCommerce_Product_Search_Service::get_s();
			if ( is_string( $s ) ) {
				$s = trim( sanitize_text_field( $s ) );
				if ( strlen( $s ) === 0 ) {
					$s = null;
				}
			} else {
				$s = null;
			}
			if ( $ixwpss !== null ) {
				$search_query = $ixwpss;
				if ( $s !== null && $s !== $ixwpss ) {
					$search_query .= ' ' . $s;
				}
			} else if ( $s !== null ) {
				$search_query = $s;
			}
		}

		$limit = isset( $_REQUEST[self::LIMIT] ) ? intval( $_REQUEST[self::LIMIT] ) : self::DEFAULT_LIMIT;
		$limit = max( 0, intval( apply_filters( 'product_search_limit', $limit ) ) );

		$offset = isset( $_REQUEST['offset'] ) && is_numeric( $_REQUEST['offset'] ) ? max( 0, intval( $_REQUEST['offset'] ) ) : null;
		$page = isset( $_REQUEST['page'] ) && is_numeric( $_REQUEST['page'] ) ? max( 1, intval( $_REQUEST['page'] ) ) : null;
		$per_page = isset( $_REQUEST['per_page'] ) && is_numeric( $_REQUEST['per_page'] ) ? max( 1, intval( $_REQUEST['per_page'] ) ) : null;

		$order = isset( $_REQUEST[self::ORDER] ) ? strtoupper( sanitize_text_field( trim( $_REQUEST[self::ORDER] ) ) ) : null;
		switch ( $order ) {
			case 'DESC' :
			case 'ASC' :
				break;
			default :
				$order = null;
		}
		$order_by = isset( $_REQUEST[self::ORDER_BY] ) ? sanitize_text_field( trim( $_REQUEST[self::ORDER_BY] ) ) : null;

		$ixwpse = isset( $_REQUEST['ixwpse'] ) ? boolval( $_REQUEST['ixwpse'] ) : false;
		$ixwpsp = isset( $_REQUEST['ixwpsp'] ) ? boolval( $_REQUEST['ixwpsp'] ) : false;

		$ixwpst = Term_Control::get_ixwpst( $this->query );

		$term_limit = apply_filters( 'woocommerce_product_search_process_query_object_term_limit', self::OBJECT_TERM_LIMIT );
		if ( is_numeric( $term_limit ) ) {
			$term_limit = intval( $term_limit );
		} else {
			$term_limit = self::OBJECT_TERM_LIMIT;
		}
		$term_limit = max( 1, $term_limit );
		foreach ( $ixwpst as $taxonomy => $term_ids ) {
			$term_count = count( $term_ids );
			if ( $term_count > $term_limit ) {
				$term_ids = array_slice( $term_ids, 0, $term_limit );
				$ixwpst[$taxonomy] = $term_ids;
				if ( WPS_DEBUG_VERBOSE ) {
					wps_log_warning(
						sprintf(
							'The number of processed terms [%s] has been limited to %d, the number of requested terms was %d.',
							esc_html( $taxonomy ),
							$term_limit,
							$term_count
						)
					);
				}
			}
		}

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
			'offset' => $offset,
			'page' => $page,
			'per_page' => $per_page,
			'order' => $order,
			'orderby' => $order_by,
			'ixwpse' => $ixwpse,
			'ixwpsp' => $ixwpsp,
			'ixwpss' => $ixwpss,
			'ixwpst' => $ixwpst
		);

		self::$parameters[$key] = $parameters;

		return $parameters;
	}

	/**
	 * Handle the query?
	 *
	 * @param \WP_Query $wp_query
	 *
	 * @return boolean
	 */
	private function handle( $wp_query ) {

		$handle = false;

		$s = \WooCommerce_Product_Search_Service::get_s();

		if ( $s === null ) {
			if ( defined( 'WP_CLI' ) && WP_CLI && $wp_query->get( 'post_type' ) === 'product' ) {
				$settings = Settings::get_instance();
				$auto_replace_rest = $settings->get( \WooCommerce_Product_Search::AUTO_REPLACE_REST, \WooCommerce_Product_Search::AUTO_REPLACE_REST_DEFAULT );
				if ( $auto_replace_rest ) {
					$s = $wp_query->get( 's', null );
				}
			}
		}

		$is_search =
			$s !== null &&
			(
				$wp_query->is_search()
				||
				$wp_query->get( 'product_search', false )
				||
				defined( 'REST_REQUEST' ) && REST_REQUEST && $wp_query->get( 'post_type' ) === 'product' && $wp_query->get( 'search', false )
				||
				defined( 'WP_CLI' ) && WP_CLI && $wp_query->get( 'post_type' ) === 'product' && $wp_query->get( 's', false )
			);

		$use_engine = \WooCommerce_Product_Search_Service::use_engine();

		$params = $this->get_request_parameters();
		$is_filtering =
			$params['ixwpss'] !== null ||
			!empty( $params['ixwpst'] ) ||
			$params['ixwpsp'] ||
			$params['ixwpse'];

		$process_query = false;
		$post_type     = $wp_query->get( 'post_type' );
		if ( $post_type === 'product' ) {
			$process_query = true;
		} else if ( empty( $post_type ) ) {
			if ( $wp_query->is_tax ) {
				$product_taxonomies = array( 'product_cat', 'product_tag' );
				$product_taxonomies = array_merge( $product_taxonomies, wc_get_attribute_taxonomy_names() );
				$product_taxonomies = apply_filters( 'woocommerce_product_search_process_query_product_taxonomies', $product_taxonomies, $wp_query );
				$product_taxonomies = array_unique( $product_taxonomies );
				$queried_object     = $wp_query->get_queried_object();
				if ( is_object( $queried_object ) ) {
					if ( in_array( $queried_object->taxonomy, $product_taxonomies ) ) {
						$process_query = true;
					}
				}
			}
		}

		$is_main_query = $wp_query->is_main_query();

		$handle = $process_query && (
			$is_search && $use_engine ||
			$is_filtering && ( $is_main_query || $this->handle_query )
		);

		return $handle;
	}

	/**
	 * Process the query.
	 *
	 * @param \WP_Query $wp_query
	 */
	private function process_query( $wp_query ) {

		remove_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), self::PRE_GET_POSTS_ACTION_PRIORITY );

		global $wps_process_query;

		if ( isset( $wps_process_query ) && !$wps_process_query ) {
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), self::PRE_GET_POSTS_ACTION_PRIORITY );
			return;
		}

		$this->set_query( $wp_query );

		if ( !$this->handle( $wp_query ) ) {

			$this->set_query( null );
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), self::PRE_GET_POSTS_ACTION_PRIORITY );
			return;
		}

		$params = $this->get_request_parameters();
		$params['offset'] = 0;
		$params['page'] = null;
		$params['per_page'] = null;

		$post_ids = null;

		$s = \WooCommerce_Product_Search_Service::get_s();

		if ( $s === null ) {
			if ( defined( 'WP_CLI' ) && WP_CLI && $wp_query->get( 'post_type' ) === 'product' ) {
				$settings = Settings::get_instance();
				$auto_replace_rest = $settings->get( \WooCommerce_Product_Search::AUTO_REPLACE_REST, \WooCommerce_Product_Search::AUTO_REPLACE_REST_DEFAULT );
				if ( $auto_replace_rest ) {
					$s = $wp_query->get( 's', null );
				}
			}
		}

		if ( $params['search_query'] !== null ) {
			if ( defined( 'REST_REQUEST' ) && REST_REQUEST && $wp_query->get( 'post_type' ) === 'product' && $wp_query->get( 'search', false ) ) {
				$wp_query->set( 'search', null );
				$params['variations'] = true;

			}
		}

		$post_ids = $this->get_ids( $params );

		if ( $post_ids !== null ) {
			if ( count( $post_ids ) > 0 ) {
				$wp_query->set( 'post__in', $post_ids );
			} else {
				$wp_query->set( 'post__in', \WooCommerce_Product_Search_Service::NONE );
			}
		}

		if ( $params['ixwpsp'] ) {
			$meta_query = $wp_query->get( 'meta_query' );
			if ( isset( $meta_query['price_filter'] ) ) {
				unset( $meta_query['price_filter'] );
				$wp_query->set( 'meta_query', $meta_query );
			}
		}

		add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), self::PRE_GET_POSTS_ACTION_PRIORITY );
	}

	/**
	 * Provide results
	 *
	 * @param $params array
	 *
	 * @return array|null
	 */
	public function get_ids( $params = null ) {

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
		$offset     = $params['offset'];
		$page       = $params['page'];
		$per_page   = $params['per_page'];
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

		if ( is_array( $params['ixwpst'] ) && count( $params['ixwpst'] ) > 0 ) {
			foreach ( $params['ixwpst'] as $taxonomy => $term_ids ) {
				if ( count( $term_ids ) > 0 ) {
					$args = array(
						'taxonomy' => $taxonomy,
						'terms'    => $term_ids,
						'id_by'    => 'id',
						'op'       => 'or',
						'variations' => $stage_variations
					);
					$stage = new \com\itthinx\woocommerce\search\engine\Engine_Stage_Terms( $args );
					$engine->attach_stage( $stage );
				}
			}
		}

		if ( $engine->get_stage_count() > 0 ) {
			$args = array( 'variations' => $stage_variations );
			$stage = new \com\itthinx\woocommerce\search\engine\Engine_Stage_Synchrotron( $args );
			$engine->attach_stage( $stage );
		}

		if ( !$this->doing_pre_get_posts ) {
			$post_status = \WooCommerce_Product_Search_Service::get_post_status();
			$args = array(
				'order'      => $order,
				'orderby'    => $orderby,
				'status'     => $post_status,
				'variations' => $variations
			);
			$stage = new \com\itthinx\woocommerce\search\engine\Engine_Stage_Posts( $args );
			$engine->attach_stage( $stage );
		}

		if ( $limit !== null && $limit > 0 || $offset !== null ) {

			$args = array(
				'limit' => $limit,
				'offset' => $offset !== null ? $offset : 0,
				'page' => null,
				'per_page' => null
			);
			$stage = new \com\itthinx\woocommerce\search\engine\Engine_Stage_Pagination( $args );
			$engine->attach_stage( $stage );
		} else if ( $per_page !== null && $page !== null ) {
			$args = array(
				'limit' => null,
				'offset' => null,
				'page' => $page,
				'per_page' => $per_page
			);
			$stage = new \com\itthinx\woocommerce\search\engine\Engine_Stage_Pagination( $args );
			$engine->attach_stage( $stage );
		}

		if ( $this->doing_pre_get_posts && $engine->get_stage_count() === 0 ) {
			return null;
		}

		$ids = $engine->get_ids();

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

}

Query_Control::init();
