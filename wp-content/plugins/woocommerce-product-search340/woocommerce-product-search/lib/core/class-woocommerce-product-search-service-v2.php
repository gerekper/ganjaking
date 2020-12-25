<?php
/**
 * class-woocommerce-product-search-service-v2.php
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
	const TERM_IDS      = 'term-ids';

	const LIMIT         = 'limit';
	const DEFAULT_LIMIT = 10;

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

	const MATCH_SPLIT         = 'match-split';
	const MATCH_SPLIT_DEFAULT = 3;
	const MATCH_SPLIT_MIN     = 0;
	const MATCH_SPLIT_MAX     = 10;

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

	const CACHE_LIFETIME              = 300;
	const POST_CACHE_GROUP            = 'ixwpsp';
	const POST_FILTERED_CACHE_GROUP   = 'ixwpspf';
	const TERM_CACHE_GROUP            = 'ixwpst';
	const TERM_COUNT_CACHE_GROUP      = 'ixwpstc';
	const TERM_COUNTS_CACHE_GROUP     = 'ixwpstcs';
	const GET_TERMS_WHERE_CACHE_GROUP = 'ixwpsgtw';
	const GET_TERMS_POSTS_CACHE_GROUP = 'ixwpsgtp';

	const TERMS_CLAUSES_PRIORITY = 99999;
	const PARSE_REQUEST_PRIORITY = 99999;

	const IXWPST_CACHE_GROUP = 'ixwps_service_ixwpst';

	private static $maybe_record_hit = true;

	/**
	 * Adds several filters and actions.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'wp_init' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'wp_enqueue_scripts' ) );

		add_action( 'wp_ajax_product_search', array( __CLASS__, 'wp_ajax_product_search' ) );
		add_action( 'wp_ajax_nopriv_product_search', array( __CLASS__, 'wp_ajax_product_search' ) );

		add_filter( 'icl_set_current_language', array( __CLASS__, 'icl_set_current_language' ) );

		add_action( 'parse_request', array( __CLASS__, 'parse_request' ), self::PARSE_REQUEST_PRIORITY );
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
	 * Adds actions on pre_get_posts and posts_search.
	 * Inhibits single product view for product filter results.
	 */
	public static function wp_init() {

		global $wp_query;

		if (
			self::use_engine() ||
			isset( $_REQUEST['ixwpss'] ) ||
			isset( $_REQUEST['ixwpst'] ) ||
			isset( $_REQUEST['ixwpsp'] ) ||
			isset( $_REQUEST['ixwpse'] )
		) {
			add_filter( 'request', array( __CLASS__, 'request' ), 0 );
			add_action( 'pre_get_posts', array( __CLASS__, 'wps_pre_get_posts' ) );
			add_action( 'posts_selection', array( __CLASS__, 'posts_selection' ) );
			add_action( 'posts_search', array( __CLASS__, 'posts_search' ), 10, 2 );
		}
		if (
			isset( $_REQUEST['ixwpss'] ) ||
			isset( $_REQUEST['ixwpst'] ) ||
			isset( $_REQUEST['ixwpsp'] ) ||
			isset( $_REQUEST['ixwpse'] ) ||
			self::get_s() !== null
		) {

			if ( isset( $_REQUEST['ixwpss'] ) || isset( $_REQUEST['ixwpst'] ) || isset( $_REQUEST['ixwpsp'] )
				|| isset( $_REQUEST['ixwpse'] )
			) {
				add_filter( 'woocommerce_redirect_single_search_result', '__return_false' );
			}
			add_filter( 'get_terms_args', array( __CLASS__, 'get_terms_args' ), 10, 2 );
		}
		if ( apply_filters( 'woocommerce_product_search_filter_terms', true ) ) {
			if ( apply_filters( 'woocommerce_product_search_filter_terms_always', false ) ) {
				self::woocommerce_before_shop_loop();
			} else {
				add_action( 'woocommerce_before_shop_loop', array( __CLASS__, 'woocommerce_before_shop_loop' ) );
				add_action( 'woocommerce_after_shop_loop', array( __CLASS__, 'woocommerce_after_shop_loop' ) );
			}
		}
		if ( isset( $_REQUEST['ixwpsp'] ) ) {
			add_filter( 'woocommerce_product_query_meta_query', array( __CLASS__, 'woocommerce_product_query_meta_query' ), 10, 2 );
		}

		if (
			self::use_engine()
			&&
			(
				isset( $_REQUEST['ixmbd'] ) ||
				self::get_s() !== null ||
				isset( $_REQUEST['ixwpss'] ) ||
				isset( $_REQUEST['ixwpst'] ) ||
				isset( $_REQUEST['ixwpsp'] ) ||
				isset( $_REQUEST['ixwpse'] )
			)
		) {
			add_filter( 'get_the_generator_html', array( __CLASS__, 'get_the_generator_type' ), 10, 2 );
			add_filter( 'get_the_generator_xhtml', array( __CLASS__, 'get_the_generator_type' ), 10, 2 );
		}
	}

	/**
	 * Handle the parse_request action.
	 *
	 * @param WP $wp
	 */
	public static function parse_request( $wp ) {

		if ( !has_action( 'get_terms_args', array( __CLASS__, 'get_terms_args' ) ) ) {
			if ( self::is_product_taxonomy_request( $wp->query_vars ) ) {
				add_filter( 'get_terms_args', array( __CLASS__, 'get_terms_args' ), 10, 2 );
			}
		}
	}

	/**
	 * Check for product taxonomy request.
	 *
	 * @param array $query_vars
	 *
	 * @return boolean
	 */
	private static function is_product_taxonomy_request( $query_vars ) {
		$result = false;
		$product_taxonomies = array( 'product_cat', 'product_tag' );
		$product_taxonomies = array_merge( $product_taxonomies, wc_get_attribute_taxonomy_names() );
		$product_taxonomies = array_unique( $product_taxonomies );
		foreach ( $product_taxonomies as $taxonomy ) {
			if ( key_exists( $taxonomy, $query_vars ) ) {

				$result = true;
				break;
			}
		}
		return $result;
	}

	/**
	 * Handler for the request filter.
	 *
	 * @param array $query_vars
	 *
	 * @return array
	 */
	public static function request( $query_vars ) {

		global $woocommerce_product_search_s;
		if ( isset( $_REQUEST['s'] ) ) {
			$woocommerce_product_search_s = $_REQUEST['s'];
		}

		global $wps_process_query;
		if ( is_admin() ) {
			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
				if ( isset( $screen->id ) && $screen->id === 'edit-product' ) {
					global $typenow;
					if ( isset( $typenow ) && $typenow === 'product' ) {
						if ( isset( $query_vars['s'] ) ) {
							if ( apply_filters( 'woocommerce_product_search_handle_admin_product_search', true ) ) {

								$wps_process_query = false;
							}
						}
					}
				}
			}
		}
		return $query_vars;
	}

	/**
	 * 's' handler
	 *
	 * @return string
	 */
	public static function get_s() {

		global $woocommerce_product_search_s;
		$s = null;
		if ( isset( $_REQUEST['s'] ) ) {
			$s = $_REQUEST['s'];
		} else if ( isset( $woocommerce_product_search_s ) ) {
			$s = $woocommerce_product_search_s;
		} else if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			$options = get_option( 'woocommerce-product-search', array() );
			$auto_replace_rest = isset( $options[WooCommerce_Product_Search::AUTO_REPLACE_REST] ) ? $options[WooCommerce_Product_Search::AUTO_REPLACE_REST] : WooCommerce_Product_Search::AUTO_REPLACE_REST_DEFAULT;
			if ( $auto_replace_rest ) {
				if ( isset( $_REQUEST['search'] ) ) {
					$s = $_REQUEST['search'];
				}
			}
		}
		return $s;
	}

	/**
	 * Adds the get_terms and term_link filters to apply filters on categories/tags.
	 */
	public static function woocommerce_before_shop_loop() {
		if (
			isset( $_REQUEST['ixwpss'] )

		) {
			add_filter( 'get_terms', array( __CLASS__, 'get_terms' ), 10, 4 );
			add_filter( 'term_link', array( __CLASS__, 'term_link' ), 10, 3 );
		}
	}

	/**
	 * Removes the get_terms and term_link filters.
	 */
	public static function woocommerce_after_shop_loop() {
		remove_filter( 'get_terms', array( __CLASS__, 'get_terms' ), 10 );
		remove_filter( 'term_link', array( __CLASS__, 'term_link' ), 10 );
	}

	/**
	 * Registers our scripts and styles.
	 */
	public static function wp_enqueue_scripts() {

		foreach ( array(
			'typewatch',
			'product-search',
			'product-filter',
			'wps-price-slider',
			'selectize',
			'selectize-ix'
		) as $script ) {
			if ( wp_script_is( $script, 'registered' ) ) {
				wp_deregister_script( $script );
			}
		}
		wp_register_script( 'typewatch', WOO_PS_PLUGIN_URL . ( WPS_DEBUG_SCRIPTS ? '/js/jquery.ix.typewatch.js' : '/js/jquery.ix.typewatch.min.js' ), array( 'jquery' ), WOO_PS_PLUGIN_VERSION, true );
		wp_register_script( 'product-search', WOO_PS_PLUGIN_URL . ( WPS_DEBUG_SCRIPTS ? '/js/product-search.js' : '/js/product-search.min.js' ), array( 'jquery', 'typewatch' ), WOO_PS_PLUGIN_VERSION, true );
		wp_register_script( 'product-filter', WOO_PS_PLUGIN_URL . ( WPS_DEBUG_SCRIPTS ? '/js/product-filter.js' : '/js/product-filter.min.js' ), array( 'jquery', 'typewatch' ), WOO_PS_PLUGIN_VERSION, true );
		wp_register_script( 'wps-price-slider', WOO_PS_PLUGIN_URL . ( WPS_DEBUG_SCRIPTS ? '/js/price-slider.js' : '/js/price-slider.min.js' ), array( 'jquery', 'jquery-ui-slider' ), WOO_PS_PLUGIN_VERSION, true );
		wp_register_script( 'selectize', WOO_PS_PLUGIN_URL . ( WPS_DEBUG_SCRIPTS ? '/js/selectize/selectize.js' : '/js/selectize/selectize.min.js' ), array( 'jquery' ), WOO_PS_PLUGIN_VERSION, true );
		wp_register_script( 'selectize-ix', WOO_PS_PLUGIN_URL . ( WPS_DEBUG_SCRIPTS ? '/js/selectize.ix.js' : '/js/selectize.ix.min.js' ), array( 'jquery', 'selectize' ), WOO_PS_PLUGIN_VERSION, true );

		foreach ( array(
			'product-search',
			'wps-price-slider',
			'selectize'
		) as $style ) {
			if ( wp_style_is( $style, 'registered' ) ) {
				wp_deregister_style( $style );
			}
		}
		wp_register_style( 'product-search', WOO_PS_PLUGIN_URL . ( WPS_DEBUG_STYLES ? '/css/product-search.css' : '/css/product-search.min.css' ), array(), WOO_PS_PLUGIN_VERSION );
		wp_register_style( 'wps-price-slider', WOO_PS_PLUGIN_URL . ( WPS_DEBUG_STYLES ? '/css/price-slider.css' : '/css/price-slider.min.css' ), array(), WOO_PS_PLUGIN_VERSION );
		$selectize_css = apply_filters( 'woocommerce_product_search_selectize_css', 'selectize' );
		switch( $selectize_css ) {
			case 'selectize' :
			case 'selectize.default' :
			case 'selectize.bootstrap2' :
			case 'selectize.bootstrap3' :
			case 'selectize.legacy' :
				break;
			default :
				$selectize_css = 'selectize';
		}
		wp_register_style( 'selectize', WOO_PS_PLUGIN_URL . ( WPS_DEBUG_STYLES ? '/css/selectize/' . $selectize_css . '.css' : '/css/selectize/' . $selectize_css . '.min.css' ), array( 'product-search' ), WOO_PS_PLUGIN_VERSION );

		wp_localize_script(
			'selectize-ix',
			'selectize_ix',
			array(
				'clear' => __( 'Clear', 'woocommerce-product-search' )
			)
		);
	}

	/**
	 * Wether to use the engine.
	 *
	 * @return boolean whether to use the search engine
	 */
	public static function use_engine() {

		$options = get_option( 'woocommerce-product-search', array() );
		$auto_replace = isset( $options[WooCommerce_Product_Search::AUTO_REPLACE] ) ? $options[WooCommerce_Product_Search::AUTO_REPLACE] : WooCommerce_Product_Search::AUTO_REPLACE_DEFAULT;
		$auto_replace_admin = isset( $options[WooCommerce_Product_Search::AUTO_REPLACE_ADMIN] ) ? $options[WooCommerce_Product_Search::AUTO_REPLACE_ADMIN] : WooCommerce_Product_Search::AUTO_REPLACE_ADMIN_DEFAULT;
		$is_admin = is_admin();
		$use_engine = $auto_replace && !$is_admin || $auto_replace_admin && $is_admin || isset( $_REQUEST['ixwps'] );

		if ( !$use_engine && defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			$auto_replace_rest = isset( $options[WooCommerce_Product_Search::AUTO_REPLACE_REST] ) ? $options[WooCommerce_Product_Search::AUTO_REPLACE_REST] : WooCommerce_Product_Search::AUTO_REPLACE_REST_DEFAULT;
			if ( $auto_replace_rest ) {
				$use_engine = true;
			}
		}

		return $use_engine;
	}

	/**
	 * Handler for pre_get_posts.
	 *
	 * @since 1.7.0
	 *
	 * @param WP_Query $wp_query query object
	 */
	public static function wps_pre_get_posts( $wp_query ) {

		self::process_query( $wp_query );

	}

	/**
	 * Handler for posts_selection.
	 *
	 * @since 2.20.0
	 *
	 * @param string $selection
	 */
	public static function posts_selection( $selection ) {

		global $wps_wc_query_price_filter_post_clauses;
		if ( isset( $wps_wc_query_price_filter_post_clauses ) ) {
			if ( $wps_wc_query_price_filter_post_clauses !== false ) {
				add_filter( 'posts_clauses', array( WC()->query, 'price_filter_post_clauses' ), $wps_wc_query_price_filter_post_clauses, 2 );
			}
		}
		unset( $wps_wc_query_price_filter_post_clauses );
	}

	/**
	 * Process the query.
	 *
	 * @since 2.1.2
	 *
	 * @param WP_Query $wp_query
	 */
	private static function process_query( $wp_query ) {

		global $wps_process_query_vars, $wps_process_query;

		if ( isset( $wps_process_query ) && !$wps_process_query ) {
			return;
		}

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
		if ( !$process_query ) {
			return;
		}

		if (
			$wp_query->is_search() ||
			$wp_query->get( 'product_search', false ) ||
			isset( $_REQUEST['ixwpss'] ) ||
			isset( $_REQUEST['ixwpsp'] ) ||
			isset( $_REQUEST['ixwpse'] )
		) {

			$s = self::get_s();
			$use_engine = self::use_engine();
			if (
				$s !== null && $use_engine ||
				isset( $_REQUEST['ixwpss'] ) ||
				isset( $_REQUEST['ixwpsp'] ) ||
				isset( $_REQUEST['ixwpse'] )
			) {

				if ( !isset( $_REQUEST[self::SEARCH_QUERY] ) ) {
					if (
						isset( $_REQUEST['ixwpss'] ) ||
						isset( $_REQUEST['ixwpsp'] ) ||
						isset( $_REQUEST['ixwpse'] )
					) {

						if ( isset( $_REQUEST['ixwpss'] ) ) {
							$_REQUEST[self::SEARCH_QUERY] = $_REQUEST['ixwpss'];

							if ( $s !== null && $s !== $_REQUEST['ixwpss'] ) {
								$_REQUEST[self::SEARCH_QUERY] .= ' ' . $s;
							}
						} else {
							if ( $s !== null ) {
								$_REQUEST[self::SEARCH_QUERY] = $s;
							} else {
								$_REQUEST[self::SEARCH_QUERY] = '';
							}
						}
					} else {
						$_REQUEST[self::SEARCH_QUERY] = $s;
					}
				}

				$post_ids = self::get_post_ids_for_request();

				if ( !empty( $post_ids ) ) {
					$wp_query->set( 'post__in', $post_ids );
				} else {

					if (
						$s !== null && $use_engine ||
						isset( $_REQUEST['ixwpss'] ) ||
						isset( $_REQUEST['ixwpsp'] ) ||
						isset( $_REQUEST['ixwpse'] )
					) {

						if (
							( $s !== null && strlen( trim( $s ) ) > 0 ) ||
							( isset( $_REQUEST['ixwpss'] ) && strlen( trim( $_REQUEST['ixwpss'] ) ) > 0 ) ||
							( isset( $_REQUEST['ixwpsp'] ) && ( !empty( $_REQUEST['min_price'] ) || !empty( $_REQUEST['max_price'] ) ) ) ||
							( isset( $_REQUEST['ixwpse'] ) && !empty( $_REQUEST['on_sale'] ) ) ||
							( isset( $_REQUEST['ixwpse'] ) && !empty( $_REQUEST['rating'] ) )
						) {

							$wp_query->set( 'post__in', array( 0 ) );
						}
					}
				}

				if ( isset( $_REQUEST['ixwpsp'] ) ) {

					$meta_query = $wp_query->get( 'meta_query' );
					if ( isset( $meta_query['price_filter'] ) ) {
						unset( $meta_query['price_filter'] );
						$wp_query->set( 'meta_query', $meta_query );
					}
				}
			}

		}

		$ixwpst = self::get_ixwpst( $wp_query );
		if ( !empty( $ixwpst ) ) {
			$tax_query = $wp_query->get( 'tax_query' );
			if ( empty( $tax_query ) ) {
				$tax_query = array();
			}

			$terms = array();
			foreach ( $ixwpst as $index => $term_ids ) {

				if ( !is_array( $term_ids ) ) {
					$term_ids = array( $term_ids );
				}
				foreach ( $term_ids as $term_id ) {
					$term_id = intval( $term_id );
					$term = get_term( $term_id );
					if ( ( $term !== null ) && !( $term instanceof WP_Error) ) {
						$terms[$term->taxonomy][] = $term->term_id;

					}
				}
			}

			foreach ( $terms as $taxonomy => $term_ids ) {
				$tax_query[] = array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $term_ids,
					'operator' => 'IN'
				);
			}
			if ( count( $tax_query ) > 0 ) {

				if ( count( $tax_query ) > 1 ) {
					$tax_query['relation'] = 'AND';
				}
				$wp_query->set( 'tax_query', $tax_query );
			}
		}

		$wps_process_query_vars = $wp_query->query_vars;

	}

	/**
	 * Resolves the ixwpst for the current context.
	 *
	 * @param WP_Query $wp_query
	 *
	 * @return array ixwpst
	 */
	private static function get_ixwpst( $wp_query ) {

		$ixwpst = isset( $_REQUEST['ixwpst'] ) && is_array( $_REQUEST['ixwpst'] ) ? $_REQUEST['ixwpst'] : array();

		$cache_key = md5( json_encode( $wp_query ) . json_encode( $ixwpst ) );
		$cached = wp_cache_get( $cache_key, self::IXWPST_CACHE_GROUP );
		if ( $cached !== false ) {
			return $cached;
		}

		if ( is_single() || is_page() ) {

			$context = WooCommerce_Product_Search_Filter_Context::get_context();
			if ( $context !== null ) {

				if ( !empty( $context['taxonomy_terms'] ) && is_array( $context['taxonomy_terms'] ) ) {
					$taxonomy_terms = $context['taxonomy_terms'];
					foreach ( $taxonomy_terms as $taxonomy => $term_ids ) {
						if ( count( $term_ids ) > 0 ) {

							if (
								!isset( $ixwpst[$taxonomy] ) ||
								is_array( $ixwpst[$taxonomy] ) && count( $ixwpst[$taxonomy] ) === 0
							) {
								$ixwpst[$taxonomy] = $term_ids;
							} else {

								$context_children = array();
								foreach ( $term_ids as $term_id ) {
									$term_children = get_term_children( $term_id, $taxonomy );
									if (
										!empty( $term_children ) &&
										!( $term_children instanceof WP_Error ) &&
										( count( $term_children ) > 0 )
									) {
										$context_children = array_merge( $context_children, $term_children );
									}
								}
								$context_term_ids = array_merge( $term_ids, $context_children );

								$ixwpst[$taxonomy] = array_intersect( $ixwpst[$taxonomy], $context_term_ids );

								if ( count( $ixwpst[$taxonomy] ) === 0 ) {
									$ixwpst[$taxonomy] = $term_ids;
								}

							}
						}
					}
				}
			}

		}

		if ( !empty( $wp_query ) && $wp_query->is_tax ) {

			$process_query      = false;
			$product_taxonomies = array( 'product_cat', 'product_tag' );
			$product_taxonomies = array_merge( $product_taxonomies, wc_get_attribute_taxonomy_names() );
			$product_taxonomies = array_unique( $product_taxonomies );
			$queried_object     = $wp_query->get_queried_object();
			if ( is_object( $queried_object ) ) {
				if ( in_array( $queried_object->taxonomy, $product_taxonomies ) ) {
					$process_query = true;
				}
			}
			if ( !$process_query ) {
				$cached = wp_cache_set( $cache_key, $ixwpst, self::IXWPST_CACHE_GROUP );
				return $ixwpst;
			}

			$had_get_terms_args = remove_filter( 'get_terms_args', array( __CLASS__, 'get_terms_args' ), 10 );
			$had_get_terms = remove_filter( 'get_terms', array( __CLASS__, 'get_terms' ), 10 );
			$queried_object = $wp_query->get_queried_object();
			if ( $had_get_terms_args ) { add_filter( 'get_terms_args', array( __CLASS__, 'get_terms_args' ), 10, 2 ); }
			if ( $had_get_terms ) { add_filter( 'get_terms', array( __CLASS__, 'get_terms' ), 10, 4 ); }
			if ( is_object( $queried_object ) ) {
				if ( isset( $queried_object->taxonomy ) && isset( $queried_object->term_id ) ) {
					$skip = false;

					if (
						isset( $_REQUEST['ixwpst'] ) &&
						is_array( $_REQUEST['ixwpst'] ) &&
						key_exists( $queried_object->taxonomy, $_REQUEST['ixwpst'] ) &&
						is_array( $_REQUEST['ixwpst'][$queried_object->taxonomy] )
					) {
						$term_children = get_term_children( $queried_object->term_id, $queried_object->taxonomy );
						if (
							!empty( $term_children ) &&
							!( $term_children instanceof WP_Error ) &&
							( count( $term_children ) > 0 )
						) {
							$requested_children = array_intersect( $term_children, $_REQUEST['ixwpst'][$queried_object->taxonomy] );
							if ( count( $requested_children ) > 0 ) {
								$skip = true;
							}
						}
					}
					if ( !$skip ) {
						$ixwpst[$queried_object->taxonomy][] = $queried_object->term_id;
					}
				}
			}
		}
		$cached = wp_cache_set( $cache_key, $ixwpst, self::IXWPST_CACHE_GROUP );
		return $ixwpst;
	}

	/**
	 * Handler for posts_search
	 *
	 * @param string $search search string
	 * @param WP_Query $wp_query query
	 *
	 * @return string
	 */
	public static function posts_search( $search, $wp_query ) {

		if ( ( self::get_s() !== null ) && self::use_engine() ) {

			$post__in = $wp_query->get( 'post__in' );
			if ( !empty( $post__in ) ) {
				$search = '';
			}
		}
		return $search;
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
	 * Returns term IDs corresponding to current context.
	 *
	 * @since 2.1.2
	 *
	 * @param array $args options
	 * @param array $taxonomies
	 *
	 * @return array term IDs
	 */
	public static function get_term_ids_for_request( $args, $taxonomies ) {

		global $wpdb, $wp_query, $wps_doing_ajax;

		$result = array();

		if ( is_string( $taxonomies ) ) {
			$taxonomies = array( $taxonomies );
		}
		if ( is_array( $taxonomies ) ) {
			$taxonomies = array_unique( $taxonomies );
		} else {
			return $result;
		}

		$product_taxonomies = array( 'product_cat', 'product_tag' );
		$product_taxonomies = array_merge( $product_taxonomies, wc_get_attribute_taxonomy_names() );
		$product_taxonomies = array_unique( $product_taxonomies );
		$target_taxonomies  = $product_taxonomies;
		$product_taxonomies = array_intersect( $taxonomies, $product_taxonomies );
		$process_terms      = count( $product_taxonomies ) !== 0 && count( $product_taxonomies ) === count( $taxonomies );

		if ( $process_terms ) {
			foreach ( $taxonomies as $taxonomy ) {
				if (
					isset( $_REQUEST['ixwpsf'] ) &&
					isset( $_REQUEST['ixwpsf']['taxonomy'] ) &&
					isset( $_REQUEST['ixwpsf']['taxonomy'][$taxonomy] ) &&
					isset( $_REQUEST['ixwpsf']['taxonomy'][$taxonomy]['filter'] )
				) {
					if ( strval( $_REQUEST['ixwpsf']['taxonomy'][$taxonomy]['filter'] ) === '0' ) {

						$process_terms = false;
						break;
					}
				}
			}
		}

		$multiple_taxonomies = array();
		if ( isset( $_REQUEST['ixwpsf'] ) && isset( $_REQUEST['ixwpsf']['taxonomy'] ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				if ( isset( $_REQUEST['ixwpsf']['taxonomy'][$taxonomy] ) ) {
					if (
						isset( $_REQUEST['ixwpsf']['taxonomy'][$taxonomy]['multiple'] ) &&
						intval( $_REQUEST['ixwpsf']['taxonomy'][$taxonomy]['multiple'] ) === 1
					) {
						$multiple_taxonomies[] = $taxonomy;
					}
				}
			}
		}

		$post_ids = null;
		if (
			isset( $_REQUEST['ixwpss'] ) ||
			isset( $_REQUEST['ixwpsp'] ) ||
			isset( $_REQUEST['ixwpse'] ) ||
			self::get_s() !== null
		) {

			if ( !isset( $_REQUEST[self::SEARCH_QUERY] ) ) {
				$_REQUEST[self::SEARCH_QUERY] = isset( $_REQUEST['ixwpss'] ) ? $_REQUEST['ixwpss'] : '';
			}

			$post_ids = self::get_post_ids_for_request();
			if ( count( $post_ids ) > 0 ) {

				$cache_key = self::get_cache_key( $post_ids );
				$posts = wp_cache_get( $cache_key, self::GET_TERMS_POSTS_CACHE_GROUP );
				if ( $posts === false ) {

					$query_args = array(
						'fields'           => 'ids',
						'post_type'        => 'product',
						'post_status'      => self::get_post_status(),
						'post__in'         => $post_ids,
						'include'          => $post_ids,
						'posts_per_page'   => -1,
						'numberposts'      => -1,
						'orderby'          => 'none',
						'suppress_filters' => 0
					);
					$had_pre_get_posts = remove_action( 'pre_get_posts', array( __CLASS__, 'wps_pre_get_posts' ) );
					$had_get_terms_args = remove_filter( 'get_terms_args', array( __CLASS__, 'get_terms_args' ), 10 );
					$had_get_terms = remove_filter( 'get_terms', array( __CLASS__, 'get_terms' ), 10 );
					self::pre_get_posts();
					$posts = get_posts( $query_args );
					self::post_get_posts();
					if ( $had_pre_get_posts ) { add_action( 'pre_get_posts', array( __CLASS__, 'wps_pre_get_posts' ) ); }
					if ( $had_get_terms_args ) { add_filter( 'get_terms_args', array( __CLASS__, 'get_terms_args' ), 10, 2 ); }
					if ( $had_get_terms ) { add_filter( 'get_terms', array( __CLASS__, 'get_terms' ), 10, 4 ); }
					$cached = wp_cache_set( $cache_key, $posts, self::GET_TERMS_POSTS_CACHE_GROUP, self::get_cache_lifetime() );
				}
				if ( is_array( $posts ) && count( $posts ) > 0 ) {
					$post_ids = $posts;
				} else {
					$post_ids = array( -1 );
				}
			}
		}

		$ixwpst = self::get_ixwpst( $wp_query );

		$taxonomy_term_ids = null;
		if ( !empty( $ixwpst ) ) {
			$taxonomy_term_ids = array();
			foreach ( $ixwpst as $index => $term_ids ) {

				if ( !is_array( $term_ids ) ) {
					$term_ids = array( $term_ids );
				}
				foreach ( $term_ids as $term_id ) {
					$term_id = intval( $term_id );
					$term = get_term( $term_id );
					if ( ( $term !== null ) && !( $term instanceof WP_Error) ) {
						if ( in_array( $term->taxonomy, $target_taxonomies ) ) {
							$taxonomy_term_ids[$term->taxonomy][] = $term->term_id;

							$term_children = get_term_children( $term->term_id, $term->taxonomy );
							if ( !empty( $term_children ) && !( $term_children instanceof WP_Error ) ) {
								foreach ( $term_children as $child_term_id ) {
									$taxonomy_term_ids[$term->taxonomy][] = $child_term_id;
									$taxonomy_term_ids[$term->taxonomy] = array_unique( $taxonomy_term_ids[$term->taxonomy] );
								}
							}
						}
					}
				}
			}
		}

		$where = array(
			"tt.taxonomy IN ('" . implode( "','", esc_sql( $product_taxonomies ) ) . "') "
		);
		if ( isset( $args['include'] ) ) {
			if ( is_array( $args['include'] ) && count( $args['include'] ) > 0 ) {
				$where[] = 'tt.term_id IN (' . implode( ',', array_map( 'intval', $args['include'] ) ) . ') ';
			}
		}
		if ( isset( $args['exclude'] ) ) {
			if ( is_array( $args['exclude'] ) && count( $args['exclude'] ) > 0 ) {
				$where[] = 'tt.term_id NOT IN (' . implode( ',', array_map( 'intval', $args['exclude'] ) ) . ') ';
			}
		}

		if ( $post_ids === null ) {

			$cache_key = self::get_cache_key( array( '*' ) );
			$posts = wp_cache_get( $cache_key, self::GET_TERMS_POSTS_CACHE_GROUP );
			if ( $posts === false ) {

				$query_args = array(
					'fields'           => 'ids',
					'post_type'        => 'product',
					'post_status'      => self::get_post_status(),
					'posts_per_page'   => -1,
					'numberposts'      => -1,
					'orderby'          => 'none',
					'suppress_filters' => 0
				);
				$had_pre_get_posts = remove_action( 'pre_get_posts', array( __CLASS__, 'wps_pre_get_posts' ) );
				$had_get_terms_args = remove_filter( 'get_terms_args', array( __CLASS__, 'get_terms_args' ), 10 );
				$had_get_terms = remove_filter( 'get_terms', array( __CLASS__, 'get_terms' ), 10 );
				self::pre_get_posts();
				$posts = get_posts( $query_args );
				self::post_get_posts();
				if ( $had_pre_get_posts ) { add_action( 'pre_get_posts', array( __CLASS__, 'wps_pre_get_posts' ) ); }
				if ( $had_get_terms_args ) { add_filter( 'get_terms_args', array( __CLASS__, 'get_terms_args' ), 10, 2 ); }
				if ( $had_get_terms ) { add_filter( 'get_terms', array( __CLASS__, 'get_terms' ), 10, 4 ); }
				$cached = wp_cache_set( $cache_key, $posts, self::GET_TERMS_POSTS_CACHE_GROUP, self::get_cache_lifetime() );
			}
			if ( is_array( $posts ) && count( $posts ) > 0 ) {
				$post_ids = $posts;
			} else {
				$post_ids = array( -1 );
			}
		}

		if ( $post_ids !== null && is_array( $post_ids ) && count( $post_ids ) > 0 ) {

			$hide_empty = !isset( $args['hide_empty'] ) || $args['hide_empty'];
			if ( $hide_empty ) {
				$where[] = "tr.object_id IN (" . implode( ',', array_map( 'intval', $post_ids ) ) . ") ";
			}
		}

		if ( $taxonomy_term_ids !== null && is_array( $taxonomy_term_ids ) && count( $taxonomy_term_ids ) > 0 ) {
			foreach ( $taxonomy_term_ids as $taxonomy => $term_ids ) {
				if ( count( $term_ids ) > 0 ) {
					if ( in_array( $taxonomy, $multiple_taxonomies ) ) {

						$where[] =
							" ( " .
							"tt.taxonomy = '" . esc_sql( $taxonomy ) . "' OR " .
							"tr.object_id IN (" .
							"SELECT tr2.object_id FROM $wpdb->term_relationships tr2 " .
							"LEFT JOIN $wpdb->term_taxonomy tt2 ON tr2.term_taxonomy_id = tt2.term_taxonomy_id " .
							"WHERE tt2.term_id IN (" . implode( ',', array_map( 'intval', $term_ids ) ) . ") " .
							") " .
							" ) ";
					} else {

						$where[] =
							"tr.object_id IN (" .
							"SELECT tr2.object_id FROM $wpdb->term_relationships tr2 " .
							"LEFT JOIN $wpdb->term_taxonomy tt2 ON tr2.term_taxonomy_id = tt2.term_taxonomy_id " .
							"WHERE tt2.term_id IN (" . implode( ',', array_map( 'intval', $term_ids ) ) . ") " .
							") ";
					}
				}
			}

			foreach ( $product_taxonomies as $taxonomy ) {

				if ( in_array( $taxonomy, $multiple_taxonomies ) ) {
					continue;
				}
				if ( key_exists( $taxonomy, $taxonomy_term_ids ) ) {
					if ( count( $taxonomy_term_ids[$taxonomy] ) > 0 ) {
						$where[] = " tt.term_id IN (" . implode( ',', array_map( 'intval', $taxonomy_term_ids[$taxonomy] ) ) . ") ";
					}
				}
			}
		}

		$cache_key = self::get_cache_key( $where );
		$allowed_term_ids = wp_cache_get( $cache_key, self::GET_TERMS_WHERE_CACHE_GROUP );
		if ( $allowed_term_ids === false ) {

			$query =
				"SELECT /*! STRAIGHT_JOIN */ DISTINCT tt.term_id FROM $wpdb->term_taxonomy tt " .
				"LEFT JOIN $wpdb->term_relationships tr ON tr.term_taxonomy_id = tt.term_taxonomy_id " .
				"WHERE " . implode( ' AND ', $where );
			$allowed_term_ids = $wpdb->get_col( $query );
			$cached = wp_cache_set( $cache_key, $allowed_term_ids, self::GET_TERMS_WHERE_CACHE_GROUP, self::get_cache_lifetime() );
		}
		if ( is_array( $allowed_term_ids ) && count( $allowed_term_ids ) > 0 ) {

			$result = array_map( 'intval', $allowed_term_ids );
		}

		return $result;
	}

	/**
	 * Handler for the get_terms_args filter.
	 *
	 * @param array $args
	 * @param array $taxonomies
	 *
	 * @return array
	 */
	public static function get_terms_args( $args, $taxonomies ) {

		global $wpdb, $wp_query;

		$options = get_option( 'woocommerce-product-search', array() );
		$apply = isset( $options[WooCommerce_Product_Search::SERVICE_GET_TERMS_ARGS_APPLY] ) ? $options[WooCommerce_Product_Search::SERVICE_GET_TERMS_ARGS_APPLY] : WooCommerce_Product_Search::SERVICE_GET_TERMS_ARGS_APPLY_DEFAULT;
		if ( !apply_filters( 'woocommerce_product_search_get_terms_args_apply', $apply, $args, $taxonomies ) ) {
			return $args;
		}

		$stop_args = apply_filters(
			'woocommerce_product_search_get_terms_args_stop_args',
			array(
				'child_of',
				'description__like',
				'name',
				'name__like',
				'object_ids',
				'parent',
				'search',
				'slug',
				'term_taxonomy_id'
			)
		);
		foreach ( $stop_args as $stop_arg) {
			if ( !empty( $args[$stop_arg] ) ) {
				return $args;
			}
		}

		if ( is_string( $taxonomies ) ) {
			$taxonomies = array( $taxonomies );
		}
		if ( is_array( $taxonomies ) ) {
			$taxonomies = array_unique( $taxonomies );
		} else {
			return $args;
		}

		$product_taxonomies = array( 'product_cat', 'product_tag' );
		$product_taxonomies = array_merge( $product_taxonomies, wc_get_attribute_taxonomy_names() );
		$product_taxonomies = array_unique( $product_taxonomies );
		$target_taxonomies  = $product_taxonomies;
		$product_taxonomies = array_intersect( $taxonomies, $product_taxonomies );
		$process_terms      = count( $product_taxonomies ) !== 0 && count( $product_taxonomies ) === count( $taxonomies );

		if ( $process_terms ) {
			foreach ( $taxonomies as $taxonomy ) {
				if (
					isset( $_REQUEST['ixwpsf'] ) &&
					isset( $_REQUEST['ixwpsf']['taxonomy'] ) &&
					isset( $_REQUEST['ixwpsf']['taxonomy'][$taxonomy] ) &&
					isset( $_REQUEST['ixwpsf']['taxonomy'][$taxonomy]['filter'] )
				) {
					if ( strval( $_REQUEST['ixwpsf']['taxonomy'][$taxonomy]['filter'] ) === '0' ) {

						$process_terms = false;
						break;
					}
				}
			}
		}

		$process_terms = apply_filters(
			'woocommerce_product_search_get_terms_args_process_terms',
			$process_terms,
			$args,
			$taxonomies
		);

		if ( !$process_terms ) {
			return $args;
		}

		$allowed_term_ids = self::get_term_ids_for_request( $args, $taxonomies );

		if ( is_array( $allowed_term_ids ) && count( $allowed_term_ids ) > 0 ) {

			$args['include'] = array_map( 'intval', $allowed_term_ids );
		} else {
			$args['include'] = array( -1 );
		}

		return $args;

	}

	/**
	 * Handler for terms_clauses
	 *
	 * @param array $pieces query pieces
	 * @param array $taxonomies involved taxonomies
	 * @param array $args further parameters
	 *
	 * @return array
	 */
	public static function terms_clauses( $pieces, $taxonomies, $args ) {

		global $woocommerce_product_search_get_terms_args_object_ids_hash;
		if (
			isset( $woocommerce_product_search_get_terms_args_object_ids_hash ) &&
			isset( $args['object_ids'] ) &&
			is_array( $args['object_ids'] )
		) {
			$hash = md5( implode( ',', $taxonomies ) . implode( ',', $args['object_ids'] ) );
			if ( $woocommerce_product_search_get_terms_args_object_ids_hash === $hash ) {
				if ( stripos( $pieces['orderby'], 'GROUP BY' ) === false ) {
					$pieces['orderby'] = ' GROUP BY t.term_id ' . $pieces['orderby'];
				}
			}
		}
		remove_filter( 'terms_clauses', array( __CLASS__, 'terms_clauses' ), self::TERMS_CLAUSES_PRIORITY );
		return $pieces;
	}

	/**
	 * Handler for get_terms
	 *
	 * @param array $terms      Array of found terms.
	 * @param array $taxonomies An array of taxonomies.
	 * @param array $args       An array of get_terms() arguments.
	 * @param WP_Term_Query $term_query The WP_Term_Query object. (since WP 4.6.0)
	 *
	 * @return array
	 */
	public static function get_terms( $terms, $taxonomies, $args, $term_query = null ) {

		if ( is_string( $taxonomies ) ) {
			$taxonomies = array( $taxonomies );
		}
		if ( is_array( $taxonomies ) ) {
			$product_taxonomies = array( 'product_cat', 'product_tag' );
			$product_taxonomies = array_merge( $product_taxonomies, wc_get_attribute_taxonomy_names() );
			$product_taxonomies = array_unique( $product_taxonomies );
			$check_taxonomies   = array_intersect( $taxonomies, $product_taxonomies );
			if ( count( $check_taxonomies ) > 0 ) {
				if ( apply_filters( 'woocommerce_product_search_get_terms_filter_counts', true, $terms, $taxonomies, $args, $term_query ) ) {
					$counts = array();
					foreach ( $check_taxonomies as $taxonomy ) {
						$counts[$taxonomy] = self::get_term_counts( $taxonomy );
					}
					foreach ( $terms as $term ) {
						if ( is_object( $term ) ) {
							if ( isset( $counts[$term->taxonomy] ) && key_exists( $term->term_id, $counts[$term->taxonomy] ) ) {
								$term->count = $counts[$term->taxonomy][$term->term_id];
							} else {
								$term->count = 0;
							}
						}
					}
				}
			}
		}
		return $terms;
	}

	/**
	 * Handler for term_link
	 *
	 * @param string $termlink term link URL
	 * @param object $term term object
	 * @param string $taxonomy taxonomy slug
	 *
	 * @return string
	 */
	public static function term_link( $termlink, $term, $taxonomy ) {

		if ( 'product_cat' == $taxonomy || 'product_tag' == $taxonomy ) {
			if ( !empty( $_REQUEST['ixwpss'] ) ) {
				if ( !isset( $_REQUEST[self::SEARCH_QUERY] ) ) {
					$_REQUEST[self::SEARCH_QUERY] = $_REQUEST['ixwpss'];
				}
				$search_query = preg_replace( '/[^\p{L}\p{N}]++/u', ' ', $_REQUEST[self::SEARCH_QUERY] );
				$search_query = trim( preg_replace( '/\s+/', ' ', $search_query ) );
				$title       = isset( $_REQUEST[self::TITLE] ) ? intval( $_REQUEST[self::TITLE] ) > 0 : self::DEFAULT_TITLE;
				$excerpt     = isset( $_REQUEST[self::EXCERPT] ) ? intval( $_REQUEST[self::EXCERPT] ) > 0 : self::DEFAULT_EXCERPT;
				$content     = isset( $_REQUEST[self::CONTENT] ) ? intval( $_REQUEST[self::CONTENT] ) > 0 : self::DEFAULT_CONTENT;
				$tags        = isset( $_REQUEST[self::TAGS] ) ? intval( $_REQUEST[self::TAGS] ) > 0 : self::DEFAULT_TAGS;
				$sku         = isset( $_REQUEST[self::SKU] ) ? intval( $_REQUEST[self::SKU] ) > 0 : self::DEFAULT_SKU;
				$params = array();
				$params['ixwpss'] = $search_query;
				if ( $title !== self::DEFAULT_TITLE ) {
					$params[self::TITLE] = $title;
				}
				if ( $excerpt !== self::DEFAULT_EXCERPT ) {
					$params[self::EXCERPT] = $excerpt;
				}
				if ( $content !== self::DEFAULT_CONTENT ) {
					$params[self::CONTENT] = $content;
				}
				if ( $tags !== self::DEFAULT_TAGS ) {
					$params[self::TAGS] = $tags;
				}
				if ( $sku !== self::DEFAULT_SKU ) {
					$params[self::SKU] = $sku;
				}

				$termlink = remove_query_arg( array( 'ixwpss',self::TITLE,self::EXCERPT, self::CONTENT, self::TAGS, self::SKU ), $termlink );
				$termlink = add_query_arg( $params, $termlink );
			}
		}
		return $termlink;
	}

	/**
	 * Provide results
	 *
	 * @return array
	 */
	public static function get_post_ids_for_request() {

		global $wpdb, $wps_doing_ajax, $wps_wc_query_price_filter_post_clauses;

		$title       = isset( $_REQUEST[self::TITLE] ) ? intval( $_REQUEST[self::TITLE] ) > 0 : self::DEFAULT_TITLE;
		$excerpt     = isset( $_REQUEST[self::EXCERPT] ) ? intval( $_REQUEST[self::EXCERPT] ) > 0 : self::DEFAULT_EXCERPT;
		$content     = isset( $_REQUEST[self::CONTENT] ) ? intval( $_REQUEST[self::CONTENT] ) > 0 : self::DEFAULT_CONTENT;
		$tags        = isset( $_REQUEST[self::TAGS] ) ? intval( $_REQUEST[self::TAGS] ) > 0 : self::DEFAULT_TAGS;
		$sku         = isset( $_REQUEST[self::SKU] ) ? intval( $_REQUEST[self::SKU] ) > 0 : self::DEFAULT_SKU;
		$categories  = isset( $_REQUEST[self::CATEGORIES] ) ? intval( $_REQUEST[self::CATEGORIES] ) > 0 : self::DEFAULT_CATEGORIES;
		$attributes  = isset( $_REQUEST[self::ATTRIBUTES] ) ? intval( $_REQUEST[self::ATTRIBUTES] ) > 0 : self::DEFAULT_ATTRIBUTES;
		$variations  = isset( $_REQUEST[self::VARIATIONS] ) ? intval( $_REQUEST[self::VARIATIONS] ) > 0 : self::DEFAULT_VARIATIONS;

		$min_price   = isset( $_REQUEST[self::MIN_PRICE] ) ? self::to_float( $_REQUEST[self::MIN_PRICE] ) : null;
		$max_price   = isset( $_REQUEST[self::MAX_PRICE] ) ? self::to_float( $_REQUEST[self::MAX_PRICE] ) : null;

		if ( $min_price !== null && $min_price <= 0 ) {
			$min_price = null;
		}
		if ( $max_price !== null && $max_price <= 0 ) {
			$max_price = null;
		}
		if ( $min_price !== null && $max_price !== null && $max_price < $min_price ) {
			$max_price = null;
		}
		self::min_max_price_adjust( $min_price, $max_price );

		$on_sale = isset( $_REQUEST[self::ON_SALE] ) ? intval( $_REQUEST[self::ON_SALE] ) > 0 : self::DEFAULT_ON_SALE;
		$rating  = isset( $_REQUEST[self::RATING] ) ? intval( $_REQUEST[self::RATING] ) : self::DEFAULT_RATING;
		if ( $rating !== self::DEFAULT_RATING ) {
			if ( $rating < WooCommerce_Product_Search_Filter_Rating::MIN_RATING ) {
				$rating = WooCommerce_Product_Search_Filter_Rating::MIN_RATING;
			}
			if ( $rating > WooCommerce_Product_Search_Filter_Rating::MAX_RATING ) {
				$rating = WooCommerce_Product_Search_Filter_Rating::MAX_RATING;
			}
		}

		$product_thumbnails = isset( $_REQUEST[self::PRODUCT_THUMBNAILS] ) ? intval( $_REQUEST[self::PRODUCT_THUMBNAILS] ) > 0 : self::DEFAULT_PRODUCT_THUMBNAILS;

		$category_results   = isset( $_REQUEST[self::CATEGORY_RESULTS] ) ? intval( $_REQUEST[self::CATEGORY_RESULTS] ) > 0 : self::DEFAULT_CATEGORY_RESULTS;
		$category_limit     = isset( $_REQUEST[self::CATEGORY_LIMIT] ) ? intval( $_REQUEST[self::CATEGORY_LIMIT] ) : self::DEFAULT_CATEGORY_LIMIT;

		if (
			!$title && !$excerpt && !$content && !$tags && !$sku && !$categories && !$attributes &&
			$min_price === null && $max_price === null &&
			!$on_sale &&
			$rating === null
		) {
			$title = true;
		}

		$search_query = isset( $_REQUEST[self::SEARCH_QUERY] ) ? $_REQUEST[self::SEARCH_QUERY] : '';
		$search_query = apply_filters( 'woocommerce_product_search_request_search_query', $search_query );

		$record_search_query = WooCommerce_Product_Search_Indexer::equalize( $search_query );

		$search_query = WooCommerce_Product_Search_Indexer::normalize( $search_query );

		$search_query = trim( remove_accents( $search_query ) );
		$search_terms = explode( ' ', $search_query );
		$search_terms = array_unique( $search_terms );

		$cache_key = self::get_cache_key( array(
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
			'rating'       => $rating
		) );

		$post_ids = wp_cache_get( $cache_key, self::POST_CACHE_GROUP );
		if ( $post_ids !== false ) {
			self::maybe_record_hit( $record_search_query, count( $post_ids ) );
			return $post_ids;
		}

		$options = get_option( 'woocommerce-product-search', null );

		$log_query_times    = isset( $options[WooCommerce_Product_Search::LOG_QUERY_TIMES] ) ? $options[WooCommerce_Product_Search::LOG_QUERY_TIMES] : WooCommerce_Product_Search::LOG_QUERY_TIMES_DEFAULT;
		$match_split        = isset( $options[self::MATCH_SPLIT] ) ? intval( $options[self::MATCH_SPLIT] ) : self::MATCH_SPLIT_DEFAULT;

		$indexer = new WooCommerce_Product_Search_Indexer();
		$object_type_ids = array();
		$object_types = array( 'product' );
		if ( $variations ) {
			$object_types[] = 'product_variation';
		}
		foreach ( $object_types as $object_type ) {
			if ( $title ) {
				$object_type_ids[] = $indexer->get_object_type_id( $object_type, 'product', 'posts', 'post_title' );
			}
			if ( $excerpt ) {
				$object_type_ids[] = $indexer->get_object_type_id( $object_type, 'product', 'posts', 'post_excerpt' );
			}
			if ( $content ) {
				$object_type_ids[] = $indexer->get_object_type_id( $object_type, 'product', 'posts', 'post_content' );
			}
			if ( $sku ) {
				$object_type_ids[] = $indexer->get_object_type_id( $object_type, 'sku', 'postmeta', 'meta_key', '_sku' );
				$object_type_ids[] = $indexer->get_object_type_id( $object_type, 'product', 'posts', 'post_id' );
			}
			if ( $tags ) {
				$object_type_ids[] = $indexer->get_object_type_id( $object_type, 'tag', 'term_taxonomy', 'taxonomy', 'product_tag' );
			}
			if ( $categories ) {
				$object_type_ids[] = $indexer->get_object_type_id( $object_type, 'category', 'term_taxonomy', 'taxonomy', 'product_cat' );
			}
			if ( $attributes ) {
				$attribute_taxonomies = wc_get_attribute_taxonomies();
				if ( !empty( $attribute_taxonomies ) ) {
					foreach ( $attribute_taxonomies as $attribute ) {
						$object_type_ids[] = $indexer->get_object_type_id( $object_type, $attribute->attribute_name, 'term_taxonomy', 'taxonomy', 'pa_' . $attribute->attribute_name );
					}
				}
			}
		}
		unset( $indexer );

		$conj = array();

		if ( count( $object_type_ids ) > 0 ) {
			$like_prefix = apply_filters( 'woocommerce_product_search_like_within', false, $object_type_ids, $search_terms ) ? '%' : '';
			$key_table   = WooCommerce_Product_Search_Controller::get_tablename( 'key' );
			$index_table = WooCommerce_Product_Search_Controller::get_tablename( 'index' );
			foreach ( $search_terms as $search_term ) {

				$length = function_exists( 'mb_strlen' ) ? mb_strlen( $search_term ) : strlen( $search_term );

				if ( $length === 0 ) {
					continue;
				}

				if ( $length < $match_split ) {
					$conj[] = $wpdb->prepare(
						" ID IN ( SELECT object_id FROM $index_table WHERE key_id IN ( SELECT key_id FROM $key_table WHERE `key` = %s ) AND object_type_id IN ( " . implode( ',', array_map( 'intval', $object_type_ids ) ) . " ) ) ",
						$search_term
					);
				} else {
					$like = $like_prefix . $wpdb->esc_like( $search_term ) . '%';

					$conj[] = $wpdb->prepare(
					" ID IN ( SELECT object_id FROM $index_table WHERE key_id IN ( SELECT key_id FROM $key_table WHERE `key` LIKE %s ) AND object_type_id IN ( " . implode( ',', array_map( 'intval', $object_type_ids ) ) . " ) ) ",
						$like
					);
				}

			}
		}

		if ( $min_price !== null || $max_price !== null ) {

			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.6.0' ) >= 0 ) {

				$price_union = '';
				global $woocommerce_wpml;
				if (
					isset( $woocommerce_wpml ) &&
					class_exists( 'woocommerce_wpml' ) &&
					( $woocommerce_wpml instanceof woocommerce_wpml )
				) {
					$multi_currency = $woocommerce_wpml->get_multi_currency();
					if (
						!empty( $multi_currency->prices ) &&
						class_exists( 'WCML_Multi_Currency_Prices' ) &&
						( $multi_currency->prices instanceof WCML_Multi_Currency_Prices )
					) {
						if ( method_exists( $multi_currency, 'get_client_currency' ) ) {
						$currency = $multi_currency->get_client_currency();
						if ( function_exists( 'wcml_get_woocommerce_currency_option' ) ) {
								if ( $currency !== wcml_get_woocommerce_currency_option() ) {
									if ( $min_price !== null && $max_price === null ) {
										$price_union = sprintf( " UNION SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_price_%s' AND meta_value >= %s ", esc_sql( $currency ), floatval( $min_price ) );
									} else if ( $min_price === null && $max_price !== null ) {
										$price_union = sprintf( " UNION SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_price_%s' AND meta_value <= %s ", esc_sql( $currency ), floatval( $max_price ) );
									} else {
										$price_union = sprintf( " UNION SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_price_%s' AND meta_value BETWEEN %s AND %s ", esc_sql( $currency ), floatval( $min_price ), floatval( $max_price ) );
									}
								}
							}
						}
					}
				}

				if ( $min_price !== null && $max_price === null ) {
					$conj[] = sprintf( " ID IN ( SELECT product_id FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup WHERE min_price >= %s %s ) ", floatval( $min_price ), $price_union );
				} else if ( $min_price === null && $max_price !== null ) {
					$conj[] = sprintf( " ID IN ( SELECT product_id FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup WHERE max_price <= %s %s ) ", floatval( $max_price ), $price_union );
				} else {
					$conj[] = sprintf( " ID IN ( SELECT product_id FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup WHERE min_price >= %s AND max_price <= %s %s ) ", floatval( $min_price ), floatval( $max_price ), $price_union );
				}

				$wps_wc_query_price_filter_post_clauses = has_filter( 'posts_clauses', array( WC()->query, 'price_filter_post_clauses' ) );
				if ( $wps_wc_query_price_filter_post_clauses !== false ) {
					remove_filter( 'posts_clauses', array( WC()->query, 'price_filter_post_clauses' ), 10, 2 );
				}

			} else {

				if ( $min_price !== null && $max_price === null ) {
					$conj[] = sprintf( " ID IN ( SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_price' AND meta_value >= %s ) ", floatval( $min_price ) );
				} else if ( $min_price === null && $max_price !== null ) {
					$conj[] = sprintf( " ID IN ( SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_price' AND meta_value <= %s ) ", floatval( $max_price ) );
				} else {
					$conj[] = sprintf( " ID IN ( SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_price' AND meta_value BETWEEN %s AND %s ) ", floatval( $min_price ), floatval( $max_price ) );
				}
			}
		}

		if ( $on_sale ) {
			$on_sale_ids = wc_get_product_ids_on_sale();
			if ( is_array( $on_sale_ids ) && count( $on_sale_ids ) > 0 ) {
				$conj[] = sprintf( " ID IN ( " . implode( ',', array_map( 'intval', $on_sale_ids ) ) . " ) " );
			}
		}

		if ( $rating !== null ) {
			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.6.0' ) >= 0 ) {
				$conj[] = sprintf(
					" ID IN ( SELECT product_id FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup WHERE wc_product_meta_lookup.average_rating >= %s ) ",
					esc_sql( floor( $rating ) - 0.5 )
				);
			} else {
				$conj[] = sprintf(
					" ID IN ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wc_average_rating' AND meta_value >= %s ) ",
					esc_sql( floor( $rating ) - 0.5 )
				);
			}
		}

		$include = array();

		if ( !empty( $conj ) ) {
			$conditions = implode( ' AND ', $conj );

			if (
				$title || $excerpt || $content || $tags || $sku || $categories ||
				$min_price !== null || $max_price !== null ||
				$on_sale ||
				$rating !== null
			) {
				$join = '';

				if ( $variations ) {
					$where  = " post_type IN ( 'product', 'product_variation' ) ";
				} else {
					$where  = " post_type = 'product' ";
				}
				$where_post_status = self::get_post_status();
				if ( is_array( $where_post_status ) && count( $where_post_status ) > 1 ) {
					$where .= " AND post_status IN ( '" . implode( "', '", array_map( 'esc_sql', $where_post_status ) ) . "' ) ";
				} else {
					if ( is_array( $where_post_status ) ) {
						$where .= " AND post_status = '" . esc_sql( array_shift( $where_post_status ) ) . "' ";
					} else {
						$where .= " AND post_status = '" . esc_sql( $where_post_status ) . "' ";
					}
				}
				if ( is_admin() && !isset( $wps_doing_ajax ) ) {
				} else {
					$tname = " AND t.name = 'exclude-from-search' ";
					if ( get_option( 'woocommerce_hide_out_of_stock_items' ) == 'yes' ) {
						$tname = " AND t.name IN ('exclude-from-search','outofstock') ";
					}
					$where .= " AND ID NOT IN ( SELECT object_id FROM $wpdb->term_relationships tr LEFT JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id LEFT JOIN $wpdb->terms t ON tt.term_id = t.term_id WHERE tt.taxonomy = 'product_visibility' $tname ) ";
				}
				$query = sprintf( "SELECT /*! STRAIGHT_JOIN */ ID FROM $wpdb->posts posts %s WHERE %s AND %s", $join, $where, $conditions );

				if ( $log_query_times ) {
					$start = function_exists( 'microtime' ) ? microtime( true ) : time();
				}

				$results = $wpdb->get_results( $query );

				if ( $log_query_times ) {
					$time = ( function_exists( 'microtime' ) ? microtime( true ) : time() ) - $start;
					wps_log_info(
						sprintf(
							__( 'WooCommerce Product Search - %1$s - Main Query Time: %2$fs - Search Terms: %3$s', 'woocommerce-product-search' ),
							__( 'Standard Search', 'woocommerce-product-search' ),
							$time,
							implode( ' ', $search_terms )
						) .
						( $min_price !== null || $max_price !== null ?
							sprintf(
								__( ' | Min Price: %1$s - Max Price: %2$s', 'woocommerce-product-search' ),
								( $min_price !== null ? esc_html( $min_price ) : '' ),
								( $max_price !== null ? esc_html( $max_price ) : '' )
							)
							:
							''
						),
						true
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

		self::maybe_record_hit( $record_search_query, count( $include ) );

		return $include;
	}

	/**
	 * Provide eligible post IDs after filtering.
	 *
	 * @return null|array of post IDs
	 */
	public static function get_post_ids_for_request_filtered() {

		global $wps_doing_ajax;

		$post_ids = null;
		if (
			isset( $_REQUEST['ixwpss'] ) ||
			isset( $_REQUEST['ixwpsp'] ) ||
			isset( $_REQUEST['ixwpse'] ) ||
			self::get_s() !== null
		) {
			if ( !isset( $_REQUEST[self::SEARCH_QUERY] ) ) {
				$_REQUEST[self::SEARCH_QUERY] = isset( $_REQUEST['ixwpss'] ) ? $_REQUEST['ixwpss'] : '';
			}

			$post_ids = self::get_post_ids_for_request();
			if ( count( $post_ids ) > 0 ) {

				$cache_key = self::get_cache_key( $post_ids );
				$posts = wp_cache_get( $cache_key, self::POST_FILTERED_CACHE_GROUP );
				if ( $posts === false ) {

					$query_args = array(
						'fields'           => 'ids',
						'post_type'        => 'product',
						'post_status'      => self::get_post_status(),
						'post__in'         => $post_ids,
						'include'          => $post_ids,
						'posts_per_page'   => -1,
						'numberposts'      => -1,
						'orderby'          => 'none',
						'suppress_filters' => 0
					);
					$had_pre_get_posts = remove_action( 'pre_get_posts', array( __CLASS__, 'wps_pre_get_posts' ) );
					$had_get_terms_args = remove_filter( 'get_terms_args', array( __CLASS__, 'get_terms_args' ), 10 );
					$had_get_terms = remove_filter( 'get_terms', array( __CLASS__, 'get_terms' ), 10 );
					self::pre_get_posts();
					$posts = get_posts( $query_args );
					self::post_get_posts();
					if ( $had_pre_get_posts ) { add_action( 'pre_get_posts', array( __CLASS__, 'wps_pre_get_posts' ) ); }
					if ( $had_get_terms_args ) { add_filter( 'get_terms_args', array( __CLASS__, 'get_terms_args' ), 10, 2 ); }
					if ( $had_get_terms ) { add_filter( 'get_terms', array( __CLASS__, 'get_terms' ), 10, 4 ); }
					$cached = wp_cache_set( $cache_key, $posts, self::POST_FILTERED_CACHE_GROUP, self::get_cache_lifetime() );
				}
				if ( is_array( $posts ) && count( $posts ) > 0 ) {
					$post_ids = array_map( 'intval', $posts );
				} else {
					$post_ids = array( -1 );
				}
			} else {

				$post_ids = array( -1 );
			}
		} else {

			$cache_key = self::get_cache_key( array( '*' ) );
			$posts = wp_cache_get( $cache_key, self::POST_FILTERED_CACHE_GROUP );
			if ( $posts === false ) {
				$query_args = array(
					'fields'           => 'ids',
					'post_type'        => 'product',
					'post_status'      => self::get_post_status(),
					'posts_per_page'   => -1,
					'numberposts'      => -1,
					'orderby'          => 'none',
					'suppress_filters' => 0
				);

				$product_visibility_terms  = wc_get_product_visibility_term_ids();
				$product_visibility_not_in = array( $product_visibility_terms['exclude-from-catalog'] );
				if ( get_option( 'woocommerce_hide_out_of_stock_items' ) === 'yes' ) {
					$product_visibility_not_in[] = $product_visibility_terms['outofstock'];
				}
				$query_args['tax_query'] = array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_not_in,
						'operator' => 'NOT IN',
					)
				);

				$had_pre_get_posts = remove_action( 'pre_get_posts', array( __CLASS__, 'wps_pre_get_posts' ) );
				$had_get_terms_args = remove_filter( 'get_terms_args', array( __CLASS__, 'get_terms_args' ), 10 );
				$had_get_terms = remove_filter( 'get_terms', array( __CLASS__, 'get_terms' ), 10 );
				self::pre_get_posts();
				$posts = get_posts( $query_args );
				self::post_get_posts();
				if ( $had_pre_get_posts ) { add_action( 'pre_get_posts', array( __CLASS__, 'wps_pre_get_posts' ) ); }
				if ( $had_get_terms_args ) { add_filter( 'get_terms_args', array( __CLASS__, 'get_terms_args' ), 10, 2 ); }
				if ( $had_get_terms ) { add_filter( 'get_terms', array( __CLASS__, 'get_terms' ), 10, 4 ); }
				$cached = wp_cache_set( $cache_key, $posts, self::POST_FILTERED_CACHE_GROUP, self::get_cache_lifetime() );
			}
			if ( is_array( $posts ) && count( $posts ) > 0 ) {
				$post_ids = array_map( 'intval', $posts );
			} else {
				$post_ids = array( -1 );
			}
		}
		return $post_ids;
	}

	/**
	 * Whether to record a hit.
	 *
	 * @return boolean
	 */
	private static function record_hit() {

		return ( !is_admin() || wp_doing_ajax() ) && !( class_exists( 'WPS_WC_Product_Data_Store_CPT' ) && WPS_WC_Product_Data_Store_CPT::is_json_product_search() );
	}

	/**
	 * Record a first hit during the request.
	 *
	 * @param $search_query string the query string
	 * @param $count int numner of results found for the query string
	 *
	 * @return int hit_id if it was recorded (null otherwise, also when it was previously recorded)
	 */
	private static function maybe_record_hit( $search_query, $count ) {
		$hit_id = null;
		if ( self::$maybe_record_hit ) {
			if ( self::record_hit() ) {
				$hit_id = WooCommerce_Product_Search_Hit::record( $search_query, $count );

				self::$maybe_record_hit = false;
			}
		}
		return $hit_id;
	}

	/**
	 * Min-max adjustment
	 *
	 * @param float $min_price
	 * @param float $max_price
	 */
	public static function min_max_price_adjust( &$min_price, &$max_price ) {

		if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
			$tax_classes = array_merge( array( '' ), WC_Tax::get_tax_classes() );
			$min = $min_price;
			$max = $max_price;
			foreach ( $tax_classes as $tax_class ) {
				if ( $tax_rates = WC_Tax::get_rates( $tax_class ) ) {
					if ( $min !== null ) {
						$min = $min_price - WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $min_price, $tax_rates ) );
						$min = round( $min, wc_get_price_decimals(), PHP_ROUND_HALF_DOWN );
					}
					if ( $max !== null ) {
						$max = $max_price - WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $max_price, $tax_rates ) );
						$max = round( $max, wc_get_price_decimals(), PHP_ROUND_HALF_UP );
					}
				}
			}
			$decimals = apply_filters( 'woocommerce_product_search_service_min_max_price_adjust_decimals', WooCommerce_Product_Search_Filter_Price::DECIMALS );
			if ( !is_numeric( $decimals ) ) {
				$decimals = WooCommerce_Product_Search_Filter_Price::DECIMALS;
			}
			$decimals = max( 0, intval( $decimals ) );
			$factor = pow( 10, $decimals );
			if ( $min !== null && $min !== '' ) {
				$min = floor( $min * $factor ) / $factor;
			}
			if ( $max !== null && $max !== '' ) {
				$max = ceil( $max * $factor ) / $factor;
			}
			$min_price = $min;
			$max_price = $max;
		}
	}

	/**
	 * Min-max adjustment for display.
	 *
	 * @param float $min_price
	 * @param float $max_price
	 */
	public static function min_max_price_adjust_for_display( &$min_price, &$max_price ) {

		if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
			$tax_classes = array_merge( array( '' ), WC_Tax::get_tax_classes() );
			$min = $min_price;
			$max = $max_price;
			foreach ( $tax_classes as $tax_class ) {
				if ( $tax_rates = WC_Tax::get_rates( $tax_class ) ) {
					if ( $min !== null && !empty( $min ) ) {
						$min = $min_price + WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $min_price, $tax_rates ) );
						$min = round( $min, wc_get_price_decimals(), PHP_ROUND_HALF_DOWN );
					}
					if ( $max !== null && !empty( $max ) ) {
						$max = $max_price + WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $max_price, $tax_rates ) );
						$max = round( $max, wc_get_price_decimals(), PHP_ROUND_HALF_UP );
					}
				}
			}
			$decimals = apply_filters( 'woocommerce_product_search_service_min_max_price_adjust_for_display_decimals', WooCommerce_Product_Search_Filter_Price::DECIMALS );
			if ( !is_numeric( $decimals ) ) {
				$decimals = WooCommerce_Product_Search_Filter_Price::DECIMALS;
			}
			$decimals = max( 0, intval( $decimals ) );
			$factor = pow( 10, $decimals );
			if ( $min !== null && $min !== '' ) {
				$min = floor( $min * $factor ) / $factor;
			}
			if ( $max !== null && $max !== '' ) {
				$max = ceil( $max * $factor ) / $factor;
			}
			$min_price = $min;
			$max_price = $max;
		}
	}

	/**
	 * Filter hook to reset incorrectly adjusted prices.
	 *
	 * @param $meta_query array
	 * @param $wc_query WC_Query
	 *
	 * @return array
	 */
	public static function woocommerce_product_query_meta_query( $meta_query, $wc_query ) {

		if ( isset( $_REQUEST['ixwpsp'] ) ) {
			if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
				if (
					isset( $meta_query['price_filter'] ) &&
					isset( $meta_query['price_filter']['value'] )
				) {
					$min_price   = isset( $_REQUEST[self::MIN_PRICE] ) ? self::to_float( $_REQUEST[self::MIN_PRICE] ) : null;
					$max_price   = isset( $_REQUEST[self::MAX_PRICE] ) ? self::to_float( $_REQUEST[self::MAX_PRICE] ) : null;
					if ( $min_price !== null && $min_price <= 0 ) {
						$min_price = null;
					}
					if ( $max_price !== null && $max_price <= 0 ) {
						$max_price = null;
					}
					if ( $min_price !== null && $max_price !== null && $max_price < $min_price ) {
						$max_price = null;
					}
					self::min_max_price_adjust( $min_price, $max_price );
					$meta_query['price_filter']['value'] = array( $min_price, $max_price );
				}
			}
		}
		return $meta_query;
	}

	/**
	 * Returns the limits.
	 *
	 * @return array
	 */
	public static function get_min_max_price() {

		global $wpdb, $wps_process_query_vars;

		$min_max = array(
			'min_price' => 0,
			'max_price' => ''
		);

		$unset_wps_process_query_vars = false;
		$basic_query_vars = null;
		if ( !isset( $wps_process_query_vars ) ) {
			$wps_process_query_vars = array(
				'post_type'           => 'product',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => 1,
				'orderby'             => 'none',
				'posts_per_page'      => -1,
				'fields'              => 'ids'
			);
			$basic_query_vars = $wps_process_query_vars;
			$unset_wps_process_query_vars = true;
		}

		if ( isset( $wps_process_query_vars ) ) {

			$meta_query_price_filter = null;
			if ( isset( $wps_process_query_vars['meta_query'] ) && isset( $wps_process_query_vars['meta_query']['price_filter'] ) ) {
				$meta_query_price_filter = $wps_process_query_vars['meta_query']['price_filter'];
				unset( $wps_process_query_vars['meta_query']['price_filter'] );
			}
			$request_min_price = null;
			if ( isset( $_REQUEST[self::MIN_PRICE] ) ) {
				$request_min_price = $_REQUEST[self::MIN_PRICE];
				unset( $_REQUEST[self::MIN_PRICE] );
			}
			$request_max_price = null;
			if ( isset( $_REQUEST[self::MAX_PRICE] ) ) {
				$request_max_price = $_REQUEST[self::MAX_PRICE];
				unset( $_REQUEST[self::MAX_PRICE] );
			}
			$post__in = null;
			if ( isset( $wps_process_query_vars['post__in'] ) ) {
				$post__in = $wps_process_query_vars['post__in'];
				unset( $wps_process_query_vars['post__in'] );
			}

			$query_vars = $wps_process_query_vars;
			$query_vars['posts_per_page'] = -1;
			$query_vars['fields'] = 'ids';
			$query_vars['orderby'] = 'none';
			$q = new WP_Query();
			$products = $q->query( $query_vars );
			if ( count( $products ) > 0 ) {

				if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.6.0' ) >= 0 ) {
					$query =
						"SELECT MIN( 0 + wc_product_meta_lookup.min_price ) AS min_price, MAX( 0 + wc_product_meta_lookup.max_price ) AS max_price " .
						"FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup " .
						"WHERE wc_product_meta_lookup.product_id IN (" . implode( ',', array_map( 'intval', $products ) ) . ") ";
				} else {

					$query =
						"SELECT MIN( 0 + price_meta.meta_value ) AS min_price, MAX( 0 + price_meta.meta_value ) AS max_price " .
						"FROM $wpdb->postmeta AS price_meta " .
						"WHERE price_meta.post_id IN (" . implode( ',', array_map( 'intval', $products ) ) . ") " .
						"AND price_meta.meta_key IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price' ) ) ) ) . "') " .
						"AND price_meta.meta_value > ''";
				}
				if ( $min_max_row = $wpdb->get_row( $query, ARRAY_A ) ) {

					$min_max['min_price'] = isset( $min_max_row['min_price'] ) ? intval( floor( $min_max_row['min_price'] ) ) : 0;
					$min_max['max_price'] = isset( $min_max_row['max_price'] ) ? intval( ceil( $min_max_row['max_price'] ) ) : '';

					global $woocommerce_wpml;
					if (
						isset( $woocommerce_wpml ) &&
						class_exists( 'woocommerce_wpml' ) &&
						( $woocommerce_wpml instanceof woocommerce_wpml )
					) {
						$multi_currency = $woocommerce_wpml->get_multi_currency();
						if (
							!empty( $multi_currency->prices ) &&
							class_exists( 'WCML_Multi_Currency_Prices' ) &&
							( $multi_currency->prices instanceof WCML_Multi_Currency_Prices )
						) {
							if ( method_exists( $multi_currency, 'get_client_currency' ) ) {
							$currency = $multi_currency->get_client_currency();
							if ( function_exists( 'wcml_get_woocommerce_currency_option' ) ) {
									if ( $currency !== wcml_get_woocommerce_currency_option() ) {
										if ( method_exists( $multi_currency->prices, 'convert_price_amount' ) ) {
											$min_max['min_price'] = isset( $min_max_row['min_price'] ) ? intval( floor( $multi_currency->prices->convert_price_amount( $min_max_row['min_price'], $currency ) ) ) : 0;
											$min_max['max_price'] = isset( $min_max_row['max_price'] ) ? intval( ceil( $multi_currency->prices->convert_price_amount( $min_max_row['max_price'], $currency ) ) ) : '';
										}
									}
								}
							}
						}
					}
				}
			}

			if ( $meta_query_price_filter !== null ) {
				$wps_process_query_vars['meta_query']['price_filter'] = $meta_query_price_filter;
			}
			if ( $request_min_price !== null ) {
				$_REQUEST[self::MIN_PRICE] = $request_min_price;
			}
			if ( $request_max_price !== null ) {
				$_REQUEST[self::MAX_PRICE] = $request_max_price;
			}
			if ( $post__in !== null ) {
				$wps_process_query_vars['post__in'] = $post__in;
			}

			if ( $unset_wps_process_query_vars ) {

				$diff = strcmp( json_encode( $wps_process_query_vars ), json_encode( $basic_query_vars ) );
				if ( $diff === 0 ) {
					unset( $wps_process_query_vars );
				}
			}
		}

		return $min_max;
	}

	/**
	 * Float conversion.
	 *
	 * @param string|float|null $x to convert
	 *
	 * @return float|null converted or null
	 */
	public static function to_float( $x ) {

		if ( $x !== null && !is_float( $x ) && is_string( $x ) ) {
			$locale = localeconv();
			$decimal_characters = array_unique( array( wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'], '.', ',' ) );
			$x = str_replace( $decimal_characters, '.', trim( $x ) );
			$x = preg_replace( '/[^0-9\.,-]/', '', $x );
			$i = strrpos( $x, '.' );
			if ( $i !== false ) {
				$x = ( $i > 0 ? str_replace( '.', '', substr( $x, 0, $i ) ) : '' ) . '.' . ( $i < strlen( $x ) ? str_replace( '.', '', substr( $x, $i + 1 ) ) : '' );
			}
			if ( strlen( $x ) > 0 ) {
				$x = floatval( $x );
			} else {
				$x = null;
			}
		}
		return $x;
	}

	/**
	 * Helper to array_map boolean and.
	 *
	 * @param boolean $a first element
	 * @param boolean $b second element
	 *
	 * @return boolean
	 */
	public static function mand( $a, $b ) {
		return $a && $b;
	}

	/**
	 * Retrieve terms.
	 *
	 * @param array $post_ids set of post IDs
	 *
	 * @return array of objects (rows from $wpdb->terms)
	 */
	public static function get_product_categories_for_request( &$post_ids ) {

		global $wpdb;

		$cache_key = self::get_cache_key( $post_ids );

		$terms = wp_cache_get( $cache_key, self::TERM_CACHE_GROUP );
		if ( $terms !== false ) {
			return $terms;
		}

		$terms = array();

		if ( count( $post_ids ) > 0 ) {
			$cat_query =
				'SELECT t.* ' .
				"FROM $wpdb->terms t " .
				"LEFT JOIN $wpdb->term_taxonomy tt ON t.term_id = tt.term_id " .
				"LEFT JOIN $wpdb->term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id " .
				"LEFT JOIN $wpdb->posts p ON p.ID = tr.object_id " .
				"WHERE  tt.taxonomy = 'product_cat' AND " .
				'tr.object_id IN (' . implode( ',', array_map( 'intval', $post_ids ) ) . ') ' .
				'GROUP BY t.term_id';

			if ( $categories = $wpdb->get_results( $cat_query ) ) {
				if ( is_array( $categories ) ) {
					$terms = $categories;
				}
			}
		}

		$cached = wp_cache_set( $cache_key, $terms, self::TERM_CACHE_GROUP, self::get_cache_lifetime() );

		return $terms;
	}

	/**
	 * Retrieve term counts
	 *
	 * @deprecated
	 *
	 * @param array of int $post_ids set of post IDs
	 *
	 * @return array of objects
	 */
	public static function get_term_counts_for_request( &$post_ids ) {

		global $wpdb;

		$cache_key = self::get_cache_key( $post_ids );

		$terms = wp_cache_get( $cache_key, self::TERM_COUNT_CACHE_GROUP );
		if ( $terms !== false ) {
			return $terms;
		}

		$terms = array();
		if ( count( $post_ids ) > 0 ) {
			$product_taxonomies = array( 'product_cat', 'product_tag' );
			$product_taxonomies = array_merge( $product_taxonomies, wc_get_attribute_taxonomy_names() );
			$product_taxonomies = array_unique( $product_taxonomies );
			$t_query =
				'SELECT t.*, COUNT(DISTINCT p.ID) AS count ' .
				"FROM $wpdb->terms t " .
				"LEFT JOIN $wpdb->term_taxonomy tt ON t.term_id = tt.term_id " .
				"LEFT JOIN $wpdb->term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id " .
				"LEFT JOIN $wpdb->posts p ON p.ID = tr.object_id " .
				"WHERE  tt.taxonomy IN ('" . implode( "','", array_map( 'esc_sql', $product_taxonomies ) ) . "') AND " .
				'tr.object_id IN (' . implode( ',', array_map( 'intval', $post_ids ) ) . ') ' .
				'GROUP BY t.term_id';
			if ( $_terms = $wpdb->get_results( $t_query ) ) {
				if ( is_array( $_terms ) ) {
					foreach ( $_terms as $term ) {
						$terms[$term->term_id] = $term;
					}
				}
			}
		}

		$cached = wp_cache_set( $cache_key, $terms, self::TERM_COUNT_CACHE_GROUP, self::get_cache_lifetime() );

		return $terms;
	}

	/**
	 * Provide the number of related products for the term.
	 *
	 * @param int $term_id
	 *
	 * @return int
	 */
	public static function get_term_count( $term_id ) {

		$count = 0;
		$term_id = intval( $term_id );
		$term = get_term( $term_id );
		if ( ( $term !== null ) && !( $term instanceof WP_Error) ) {
			$count = $term->count;
			$term_counts = self::get_term_counts( $term->taxonomy );
			if ( isset( $term_counts[$term_id] ) ) {
				$count = $term_counts[$term_id];
			} else {
				$count = 0;
			}
		}
		return $count;
	}

	/**
	 * Get all product counts for the terms in the given taxonomy.
	 *
	 * @param string $taxonomy
	 * @return array
	 */
	public static function get_term_counts( $taxonomy ) {

		global $wpdb, $wp_query;

		$ixwpst = self::get_ixwpst( $wp_query );

		$cache_key = self::get_cache_key(
			array(
				$taxonomy,
				json_encode( $ixwpst ),
				isset( $_REQUEST['ixwpss'] ) ? json_encode( $_REQUEST['ixwpss'] ) : '',
				isset( $_REQUEST['ixwpsp'] ) ? json_encode( $_REQUEST['ixwpsp'] ) : '',
				isset( $_REQUEST['ixwpsf'] ) ? json_encode( $_REQUEST['ixwpsf'] ) : '',
				isset( $_REQUEST['ixwpse'] ) ? json_encode( $_REQUEST['ixwpse'] ) : '',
				json_encode( self::get_base_request_cache_key_parameters() )
			)
		);

		$counts = wp_cache_get( $cache_key, self::TERM_COUNTS_CACHE_GROUP );
		if ( $counts !== false ) {
			return $counts;
		}
		$counts = array();

		$count_where = array( "tt.taxonomy = '" . esc_sql( $taxonomy ) . "'" );
		$post_ids = self::get_post_ids_for_request_filtered();
		if ( $post_ids !== null && is_array( $post_ids ) && count( $post_ids ) > 0 ) {
			$count_where[] = ' tr.object_id IN (' . implode( ',', array_map( 'intval', $post_ids ) ) . ') ';
		}

		if ( !empty( $ixwpst ) ) {

			$terms = array();
			foreach ( $ixwpst as $index => $term_ids ) {
				if ( !is_array( $term_ids ) ) {
					$term_ids = array( $term_ids );
				}
				foreach ( $term_ids as $term_id ) {
					$term_id = intval( $term_id );
					$term = get_term( $term_id );
					if ( ( $term !== null ) && !( $term instanceof WP_Error) ) {
						$terms[$term->taxonomy][] = $term->term_id;
					}
				}
			}

			foreach ( $terms as $term_taxonomy => $term_ids ) {
				foreach ( $term_ids as $term_id ) {
					$child_term_ids = get_term_children( $term_id, $term_taxonomy );
					if ( $child_term_ids ) {
						foreach ( $child_term_ids as $child_term_id ) {
							$terms[$term_taxonomy][] = $child_term_id;
						}
					}
				}
				if ( count( $terms[$term_taxonomy] ) > 0 ) {
					$terms[$term_taxonomy] = array_unique( $terms[$term_taxonomy] );
				}
			}

			unset( $terms[$taxonomy] );

			foreach ( $terms as $term_taxonomy => $term_ids ) {
				if ( count( $term_ids ) > 0 ) {

					$count_where[] =
						'tr.object_id IN (' .
						"SELECT tr.object_id FROM $wpdb->term_relationships tr " .
						"LEFT JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id " .
						'WHERE tt.term_id IN (' .implode( ',', array_map( 'intval', $term_ids ) ) . ')' .
						')';
				}
			}
		}

		$count_query =
			'SELECT /*! STRAIGHT_JOIN */ tt.term_id, tt.parent, COUNT(DISTINCT tr.object_id) AS count ' .
			"FROM $wpdb->term_taxonomy tt " .
			"LEFT JOIN $wpdb->term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id " .
			'WHERE ' .
			implode( ' AND ', $count_where ) . ' ' .
			'GROUP BY tt.term_id';
		if ( $results = $wpdb->get_results( $count_query ) ) {
			if ( is_array( $results ) ) {

				$children = array();
				if ( is_taxonomy_hierarchical( $taxonomy ) ) {
					if ( $all_terms = $wpdb->get_results( $wpdb->prepare(
						"SELECT term_id, parent FROM $wpdb->term_taxonomy WHERE taxonomy = %s",
						$taxonomy
					) ) ) {
						foreach ( $all_terms as $entry ) {
							if ( !empty( $entry->parent ) ) {
								$parent = intval( $entry->parent );
								if ( $parent > 0 ) {
									$children[$entry->parent][] = intval( $entry->term_id );
								}
							}
						}
						unset( $all_terms );
					}
				}

				$nodes = array();
				foreach ( $results as $entry ) {
					$term_id = intval( $entry->term_id );
					$parent = !empty( $entry->parent ) ? intval( $entry->parent ) : null;
					$nodes[intval( $entry->term_id )] = array(
						'count' => intval( $entry->count ),
						'parent' => $parent,
						'children' => isset( $children[$term_id] ) ? $children[$term_id] : null
					);
				}

				foreach ( $children as $parent => $children ) {
					if ( !isset( $nodes[$parent] ) ) {
						$nodes[$parent] = array(
							'count' => 0,
							'parent' => null,
							'children' => $children
						);
					}
				}

				foreach ( $nodes as $term_id => $node ) {
					$counts[$term_id] = self::sum_tree( $nodes, $term_id );
				}

			}
		}
		$cached = wp_cache_set( $cache_key, $counts, self::TERM_COUNTS_CACHE_GROUP, self::get_cache_lifetime() );
		return $counts;
	}

	/**
	 * Calculate the sum of counts for the given $term_id.
	 *
	 * @param array $nodes
	 * @param int $term_id
	 *
	 * @return int
	 */
	private static function sum_tree( &$nodes, $term_id = null ) {

		$sum = 0;
		if ( $term_id !== null ) {
			if ( isset( $nodes[$term_id] ) ) {
				if ( !isset( $nodes[$term_id]['sum'] ) ) {
					if ( !empty( $nodes[$term_id]['children'] ) ) {
						foreach ( $nodes[$term_id]['children'] as $child_id ) {
							$sum += self::sum_tree( $nodes, $child_id );
						}
					}
					$sum += $nodes[$term_id]['count'];
					$nodes[$term_id]['sum'] = $sum;
				} else {
					$sum = $nodes[$term_id]['sum'];
				}
			}
		}
		return $sum;
	}

	/**
	 * Obtain results
	 *
	 * @return array
	 */
	public static function request_results() {

		global $wpdb, $sitepress, $wps_doing_ajax;

		$switch_lang = false;

		if ( isset( $sitepress ) && is_object( $sitepress ) && method_exists( $sitepress, 'get_current_language' ) && method_exists( $sitepress, 'switch_lang' ) ) {
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

		$options = get_option( 'woocommerce-product-search', array() );
		$use_short_description = isset( $options[WooCommerce_Product_Search::USE_SHORT_DESCRIPTION] ) ? $options[WooCommerce_Product_Search::USE_SHORT_DESCRIPTION] : WooCommerce_Product_Search::USE_SHORT_DESCRIPTION_DEFAULT;

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
			case 'sku' :
			case '' :
			case 'popularity' :
			case 'rating' :
				break;
			default :
				$order_by = 'date';
		}

		$orderby_value = '';
		if ( !empty( $order_by ) ) {
			$orderby_value = $order_by;
			if ( !empty( $order ) ) {
				$orderby_value .= '-' . $order;
			}
		}

		$product_thumbnails  = isset( $_REQUEST[self::PRODUCT_THUMBNAILS] ) ? intval( $_REQUEST[self::PRODUCT_THUMBNAILS] ) > 0 : self::DEFAULT_PRODUCT_THUMBNAILS;

		$category_results    = isset( $_REQUEST[self::CATEGORY_RESULTS] ) ? intval( $_REQUEST[self::CATEGORY_RESULTS] ) > 0 : self::DEFAULT_CATEGORY_RESULTS;
		$category_limit      = isset( $_REQUEST[self::CATEGORY_LIMIT] ) ? intval( $_REQUEST[self::CATEGORY_LIMIT] ) : self::DEFAULT_CATEGORY_LIMIT;

		$search_query = trim( preg_replace( '/\s+/', ' ', $_REQUEST[self::SEARCH_QUERY] ) );
		$search_terms = explode( ' ', $search_query );

		$include = self::get_post_ids_for_request();
		$n       = count( $include );

		$results = array();
		$post_ids = array();
		if ( count( $include ) > 0 ) {

			$query_args = array(
				'fields'      => 'ids',
				'post_type'   => 'product',
				'post_status' => self::get_post_status(),
				'numberposts' => $numberposts,
				'include'     => $include,
				'suppress_filters' => 0
			);

			if ( $order_by !== '' ) {
				switch ( $order_by ) {

					case 'sku' :
						$query_args['orderby']  = 'meta_value';
						$query_args['order']    = $order;
						$query_args['meta_key'] = '_sku';
						break;
					case 'popularity' :

						if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.6.0' ) >= 0 ) {
							$query_args['join']    = " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
							$query_args['orderby'] = 'wc_product_meta_lookup.total_sales';
							$query_args['order']   = $order;
						}
						break;
					case 'rating' :
						if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.6.0' ) >= 0 ) {

							$query_args['join']    = " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
							$query_args['orderby'] = 'wc_product_meta_lookup.average_rating';
							$query_args['order']   = $order;
						}
						break;
					default :
						$query_args['orderby'] = $order_by;
						$query_args['order']   = $order;
				}
			}

			if ( $order_by === 'popularity' ) {
				add_filter( 'posts_orderby', array( __CLASS__, 'posts_orderby_popularity' ), 10, 2 );
				add_filter( 'posts_join', array( __CLASS__, 'posts_join_popularity' ), 10, 2 );
			} else if ( $order_by === 'rating' ) {
				add_filter( 'posts_orderby', array( __CLASS__, 'posts_orderby_rating' ), 10, 2 );
				add_filter( 'posts_join', array( __CLASS__, 'posts_join_rating' ), 10, 2 );
			}

			self::pre_get_posts();
			$posts = get_posts( $query_args );
			self::post_get_posts();

			if ( $order_by === 'popularity' ) {
				remove_filter( 'posts_orderby', array( __CLASS__, 'posts_orderby_popularity' ) );
				remove_filter( 'posts_join', array( __CLASS__, 'posts_join_popularity' ) );
			} else if ( $order_by === 'rating' ) {
				remove_filter( 'posts_orderby', array( __CLASS__, 'posts_orderby_rating' ) );
				remove_filter( 'posts_join', array( __CLASS__, 'posts_join_rating' ) );
			}

			$i = 0;
			foreach ( $posts as $post ) {

				if ( $post = get_post( $post ) ) {

					$product = wc_get_product( $post );

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
					$title = self::shorten( wp_strip_all_tags( $product->get_title() ), 'title' );
					$_description = '';
					if ( $use_short_description ) {
						$_description = wc_format_content( $product->get_short_description() );
					}
					if ( empty( $_description ) ) {
						$_description = wc_format_content( $product->get_description() );
					}
					$description = self::shorten( wp_strip_all_tags( $_description ), 'description' );
					$results[$post->ID] = array(
						'id'    => $post->ID,
						'type'  => 'product',
						'url'   => get_permalink( $post ),
						'title' => apply_filters( 'woocommerce_product_search_field_product_title', $title, $post->ID ),
						'description' => apply_filters( 'woocommerce_product_search_field_product_description', $description, $post->ID ),
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
					$price_html = $product->get_price_html();
					$results[$post->ID]['price'] = apply_filters( 'woocommerce_product_search_field_product_price_html', $price_html, $post->ID );
					$add_to_cart_html = self::get_add_to_cart( $post->ID );
					$results[$post->ID]['add_to_cart'] = apply_filters( 'woocommerce_product_search_field_product_add_to_cart_html', $add_to_cart_html, $post->ID );
					$i++;

					if ( $i >= $numberposts ) {
						break;
					}

					unset( $post );
					unset( $product );
				}
			}
			unset( $posts );

			usort( $results, array( __CLASS__, 'usort' ) );
		}

		if ( $n > $limit ) {
			$url_query_args = array(
				's'          => urlencode( $search_query ),
				'post_type'  => 'product',
				'ixwps'      => 1,
				'title'      => intval( isset( $_REQUEST[self::TITLE] ) ? intval( $_REQUEST[self::TITLE] ) > 0 : self::DEFAULT_TITLE ),
				'excerpt'    => intval( isset( $_REQUEST[self::EXCERPT] ) ? intval( $_REQUEST[self::EXCERPT] ) > 0 : self::DEFAULT_EXCERPT ),
				'content'    => intval( isset( $_REQUEST[self::CONTENT] ) ? intval( $_REQUEST[self::CONTENT] ) > 0 : self::DEFAULT_CONTENT ),
				'categories' => intval( isset( $_REQUEST[self::CATEGORIES] ) ? intval( $_REQUEST[self::CATEGORIES] ) > 0 : self::DEFAULT_CATEGORIES ),
				'attributes' => intval( isset( $_REQUEST[self::ATTRIBUTES] ) ? intval( $_REQUEST[self::ATTRIBUTES] ) > 0 : self::DEFAULT_ATTRIBUTES ),
				'tags'       => intval( isset( $_REQUEST[self::TAGS] ) ? intval( $_REQUEST[self::TAGS] ) > 0 : self::DEFAULT_TAGS ),
				'sku'        => intval( isset( $_REQUEST[self::SKU] ) ? intval( $_REQUEST[self::SKU] ) > 0 : self::DEFAULT_SKU )
			);

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
			if ( !empty( $post_ids ) ) {
				$categories = self::get_product_categories_for_request( $post_ids );
				foreach ( $categories as $category ) {
					$variables = array(
						'post_type'   => 'product',
						'product_cat' => $category->slug,
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
						$c_results[$category->term_id] = array(
							'id'    => $category->term_id,
							'type'  => 's_product_cat',
							'url'   => $c_url,
							'title' => sprintf(

								esc_html( __( 'Search in %s', 'woocommerce-product-search' ) ),
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

	/**
	 * Computes a cache key based on the parameters provided.
	 *
	 * @param array $parameters set of parameters for which to compute the key
	 *
	 * @return string
	 */
	private static function get_cache_key( $parameters ) {
		return md5( implode( '-', $parameters ) );
	}

	/**
	 * Returns the cache lifetime for stored results in seconds.
	 *
	 * @return int
	 */
	public static function get_cache_lifetime() {
		$l = intval( apply_filters( 'woocommerce_product_search_cache_lifetime', self::CACHE_LIFETIME ) );
		return $l;
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
	 * Set the language if specified in the request.
	 *
	 * @param string $lang language code
	 *
	 * @return string
	 */
	public static function icl_set_current_language( $lang ) {
		$language = isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : null;
		if ( $language !== null ) {
			$lang = $language;
		}
		return $lang;
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
	 * Used to temporarily remove the WPML query filter on posts_where.
	 */
	private static function pre_get_posts() {
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

	/**
	 * Modify the orderby to be able to filter by popularity.
	 *
	 * @since 2.20.0
	 *
	 * @param string $orderby
	 * @param WP_Query $query
	 *
	 * @return string
	 */
	public static function posts_orderby_popularity( $orderby, $query ) {
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.6.0' ) >= 0 ) {
			if ( !empty( $query->query ) && isset( $query->query['orderby'] ) && isset( $query->query['order'] ) ) {
				$orderby = esc_sql( $query->query['orderby'] ) . ' ' . esc_sql( $query->query['order'] );
				if ( strpos( $orderby, 'wc_product_meta_lookup' ) !== false ) {
					$orderby .= ', wc_product_meta_lookup.product_id DESC';
				}
			}
		}
		return $orderby;
	}

	/**
	 * Modify the join to be able to filter by popularity.
	 *
	 * @since 2.20.0
	 *
	 * @param string $join
	 * @param WP_Query $query
	 *
	 * @return string
	 */
	public static function posts_join_popularity( $join, $query ) {
		global $wpdb;
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.6.0' ) >= 0 ) {
			if ( strpos( $join, 'wc_product_meta_lookup' ) === false ) {
				$join .= " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
			}
		}
		return $join;
	}

	/**
	 * Modify the orderby to be able to filter by rating.
	 *
	 * @since 2.20.0
	 *
	 * @param string $orderby
	 * @param WP_Query $query
	 *
	 * @return string
	 */
	public static function posts_orderby_rating( $orderby, $query ) {
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.6.0' ) >= 0 ) {
			if ( !empty( $query->query ) && isset( $query->query['orderby'] ) && isset( $query->query['order'] ) ) {
				$orderby = esc_sql( $query->query['orderby'] ) . ' ' . esc_sql( $query->query['order'] );
				if ( strpos( $orderby, 'wc_product_meta_lookup' ) !== false ) {
					$orderby .= ', wc_product_meta_lookup.product_id DESC';
				}
			}
		}
		return $orderby;
	}

	/**
	 * Modify the join to be able to filter by rating.
	 *
	 * @since 2.20.0
	 *
	 * @param string $join
	 * @param WP_Query $query
	 *
	 * @return string
	 */
	public static function posts_join_rating( $join, $query ) {
		global $wpdb;
		if ( strpos( $join, 'wc_product_meta_lookup' ) === false ) {
			$join .= " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
		}
		return $join;
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

		$options = get_option( 'woocommerce-product-search', array() );

		switch ( $what ) {
			case 'description' :
			case 'title' :
				break;
			default :
				$what = 'description';
		}

		switch( $what ) {
			case 'title' :
				$max_words = isset( $options[WooCommerce_Product_Search::MAX_TITLE_WORDS] ) ? $options[WooCommerce_Product_Search::MAX_TITLE_WORDS] : WooCommerce_Product_Search::MAX_TITLE_WORDS_DEFAULT;
				$max_characters = isset( $options[WooCommerce_Product_Search::MAX_TITLE_CHARACTERS] ) ? $options[WooCommerce_Product_Search::MAX_TITLE_CHARACTERS] : WooCommerce_Product_Search::MAX_TITLE_CHARACTERS_DEFAULT;
				break;
			default :
				$max_words = isset( $options[WooCommerce_Product_Search::MAX_EXCERPT_WORDS] ) ? $options[WooCommerce_Product_Search::MAX_EXCERPT_WORDS] : WooCommerce_Product_Search::MAX_EXCERPT_WORDS_DEFAULT;
				$max_characters = isset( $options[WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS] ) ? $options[WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS] : WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS_DEFAULT;
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
	 * @param WC_Product $product
	 *
	 * @return string
	 */
	public static function woocommerce_product_add_to_cart_url( $url, $product ) {
		if ( $product->is_purchasable() && $product->is_in_stock() ) {
			$url = remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $product->get_id(), wc_get_page_permalink( 'shop' ) ) );
		} else {
			$url = get_permalink( $product->get_id() );
		}
		return $url;
	}

	/**
	 * Get a basic array of cache key parameters for the current request.
	 *
	 * @return array cache key parameters
	 */
	private static function get_base_request_cache_key_parameters() {

		$title       = isset( $_REQUEST[self::TITLE] ) ? intval( $_REQUEST[self::TITLE] ) > 0 : self::DEFAULT_TITLE;
		$excerpt     = isset( $_REQUEST[self::EXCERPT] ) ? intval( $_REQUEST[self::EXCERPT] ) > 0 : self::DEFAULT_EXCERPT;
		$content     = isset( $_REQUEST[self::CONTENT] ) ? intval( $_REQUEST[self::CONTENT] ) > 0 : self::DEFAULT_CONTENT;
		$tags        = isset( $_REQUEST[self::TAGS] ) ? intval( $_REQUEST[self::TAGS] ) > 0 : self::DEFAULT_TAGS;
		$sku         = isset( $_REQUEST[self::SKU] ) ? intval( $_REQUEST[self::SKU] ) > 0 : self::DEFAULT_SKU;
		$categories  = isset( $_REQUEST[self::CATEGORIES] ) ? intval( $_REQUEST[self::CATEGORIES] ) > 0 : self::DEFAULT_CATEGORIES;
		$attributes  = isset( $_REQUEST[self::ATTRIBUTES] ) ? intval( $_REQUEST[self::ATTRIBUTES] ) > 0 : self::DEFAULT_ATTRIBUTES;
		$variations  = isset( $_REQUEST[self::VARIATIONS] ) ? intval( $_REQUEST[self::VARIATIONS] ) > 0 : self::DEFAULT_VARIATIONS;
		$min_price   = isset( $_REQUEST[self::MIN_PRICE] ) ? self::to_float( $_REQUEST[self::MIN_PRICE] ) : null;
		$max_price   = isset( $_REQUEST[self::MAX_PRICE] ) ? self::to_float( $_REQUEST[self::MAX_PRICE] ) : null;
		if ( $min_price !== null && $min_price <= 0 ) {
			$min_price = null;
		}
		if ( $max_price !== null && $max_price <= 0 ) {
			$max_price = null;
		}
		if ( $min_price !== null && $max_price !== null && $max_price < $min_price ) {
			$max_price = null;
		}
		self::min_max_price_adjust( $min_price, $max_price );
		$on_sale = isset( $_REQUEST[self::ON_SALE] ) ? intval( $_REQUEST[self::ON_SALE] ) > 0 : self::DEFAULT_ON_SALE;
		$rating  = isset( $_REQUEST[self::RATING] ) ? intval( $_REQUEST[self::RATING] ) : self::DEFAULT_RATING;
		if ( $rating !== self::DEFAULT_RATING ) {
			if ( $rating < WooCommerce_Product_Search_Filter_Rating::MIN_RATING ) {
				$rating = WooCommerce_Product_Search_Filter_Rating::MIN_RATING;
			}
			if ( $rating > WooCommerce_Product_Search_Filter_Rating::MAX_RATING ) {
				$rating = WooCommerce_Product_Search_Filter_Rating::MAX_RATING;
			}
		}

		if (
			!$title && !$excerpt && !$content && !$tags && !$sku && !$categories && !$attributes &&
			$min_price === null && $max_price === null && !$on_sale && $rating === null
		) {
			$title = true;
		}
		$search_query = isset( $_REQUEST[self::SEARCH_QUERY] ) ? $_REQUEST[self::SEARCH_QUERY] : '';
		$search_query = apply_filters( 'woocommerce_product_search_request_search_query', $search_query );
		$search_query = WooCommerce_Product_Search_Indexer::normalize( $search_query );
		$search_query = trim( remove_accents( $search_query ) );

		$parameters = array(
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
			'rating'       => $rating
		);

		return $parameters;
	}

	/**
	 * Produce the additional generator markup for appropriate generator context.
	 *
	 * @since 2.20.0
	 *
	 * @param string $gen HTML markup
	 * @param string $type type of generator
	 *
	 * @return string
	 */
	public static function get_the_generator_type( $gen, $type ) {
		switch ( $type ) {
			case 'html' :
			case 'xhtml' :
				$gen .= "\n";
				$gen .= sprintf(
					'<meta name="generator" content="WooCommerce Product Search %s" />',
					esc_attr( WOO_PS_PLUGIN_VERSION )
				);
				break;
		}
		return $gen;
	}
}
WooCommerce_Product_Search_Service::init();
