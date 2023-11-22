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

use com\itthinx\woocommerce\search\engine\Base;
use com\itthinx\woocommerce\search\engine\Cache;
use com\itthinx\woocommerce\search\engine\Engine;
use com\itthinx\woocommerce\search\engine\Engine_Stage_Price;
use com\itthinx\woocommerce\search\engine\Settings;
use com\itthinx\woocommerce\search\engine\Query_Control;
use com\itthinx\woocommerce\search\engine\Term_Control;
use com\itthinx\woocommerce\search\engine\Tools;

/**
 * Product search service.
 */
class WooCommerce_Product_Search_Service {

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

	const MATCH_SPLIT         = 'match-split';
	const MATCH_SPLIT_DEFAULT = 3;
	const MATCH_SPLIT_MIN     = 0;
	const MATCH_SPLIT_MAX     = 10;

	const DEFAULT_CATEGORY_LIMIT = 5;

	const CACHE_LIFETIME = 900;

	const MIN_MAX_PRICE_CACHE_GROUP = 'ixwpsmmp';

	const NAUGHT = -1;
	const NONE = array( self::NAUGHT );

	private static $maybe_record_hit = true;

	private static $scripts_registered = false;

	private static $styles_registered = false;

	private static $get_terms_priority = null;

	/**
	 * Adds several filters and actions.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'wp_init' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'wp_enqueue_scripts' ) );
		add_action( 'woocommerce_product_search_engine_process_start', array( __CLASS__, 'woocommerce_product_search_engine_process_start' ) );
		add_action( 'woocommerce_product_search_engine_process_end', array( __CLASS__, 'woocommerce_product_search_engine_process_end' ) );
	}

	/**
	 * Adds actions and filters conditionally.
	 */
	public static function wp_init() {
		if (
			self::use_engine() ||
			isset( $_REQUEST['ixwpss'] ) ||
			isset( $_REQUEST['ixwpst'] ) ||
			isset( $_REQUEST['ixwpsp'] ) ||
			isset( $_REQUEST['ixwpse'] )
		) {
			add_filter( 'request', array( __CLASS__, 'request' ), 0 );
			add_action( 'posts_selection', array( __CLASS__, 'posts_selection' ) );
			add_action( 'posts_search', array( __CLASS__, 'posts_search' ), 10, 2 );
		}
		if (
			isset( $_REQUEST['ixwpss'] ) ||
			isset( $_REQUEST['ixwpst'] ) ||
			isset( $_REQUEST['ixwpsp'] ) ||
			isset( $_REQUEST['ixwpse'] )
		) {

			add_filter( 'woocommerce_redirect_single_search_result', '__return_false' );
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
	 * Engine processing start action handler.
	 *
	 * @param Engine $engine
	 *
	 * @since 5.0.0
	 */
	public static function woocommerce_product_search_engine_process_start( $engine ) {
		self::$get_terms_priority = has_filter( 'get_terms', array( __CLASS__, 'get_terms' ) );
		if ( self::$get_terms_priority !== false ) {
			remove_filter( 'get_terms', array( __CLASS__, 'get_terms' ), self::$get_terms_priority );
		}
	}

	/**
	 * Engine processing end action handler.
	 *
	 * @param Engine $engine
	 *
	 * @since 5.0.0
	 */
	public static function woocommerce_product_search_engine_process_end( $engine ) {
		if ( self::$get_terms_priority !== null && self::$get_terms_priority !== false ) {
			add_filter( 'get_terms', array( __CLASS__, 'get_terms' ), self::$get_terms_priority, 4 );
		}
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
			if ( isset( $_REQUEST['search'] ) ) {
				$settings = Settings::get_instance();
				$auto_replace_rest = $settings->get( WooCommerce_Product_Search::AUTO_REPLACE_REST, WooCommerce_Product_Search::AUTO_REPLACE_REST_DEFAULT );
				if ( $auto_replace_rest ) {
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

		if ( !self::$scripts_registered ) {
			$scripts = array(
				'typewatch' => array(
					'src' => WOO_PS_PLUGIN_URL . ( WPS_DEBUG_SCRIPTS ? '/js/jquery.ix.typewatch.js' : '/js/jquery.ix.typewatch.min.js' ),
					'deps' => array( 'jquery' )
				),
				'product-search' => array(
					'src' => WOO_PS_PLUGIN_URL . ( WPS_DEBUG_SCRIPTS ? '/js/product-search.js' : '/js/product-search.min.js' ),
					'deps' => array( 'jquery', 'typewatch' )
				),
				'wps-price-slider' => array(
					'src' => WOO_PS_PLUGIN_URL . ( WPS_DEBUG_SCRIPTS ? '/js/price-slider.js' : '/js/price-slider.min.js' ),
					'deps' => array( 'jquery', 'jquery-ui-slider' )
				),
				'selectize' => array(
					'src' => WOO_PS_PLUGIN_URL . ( WPS_DEBUG_SCRIPTS ? '/js/selectize/selectize.js' : '/js/selectize/selectize.min.js' ),
					'deps' =>  array( 'jquery' )
				),
				'selectize-ix' => array(
					'src' => WOO_PS_PLUGIN_URL . ( WPS_DEBUG_SCRIPTS ? '/js/selectize.ix.js' : '/js/selectize.ix.min.js' ),
					'deps' => array( 'jquery', 'selectize' )
				),
				'product-filter' => array(
					'src' => WOO_PS_PLUGIN_URL . ( WPS_DEBUG_SCRIPTS ? '/js/product-filter.js' : '/js/product-filter.min.js' ),
					'deps' => array( 'jquery', 'typewatch', 'selectize', 'selectize-ix' )
				)
			);
			$scripts_registered = 0;
			foreach ( $scripts as $handle => $script ) {
				if ( wp_script_is( $handle, 'registered' ) ) {
					$wp_scripts = wp_scripts();
					if (
						is_object( $wp_scripts ) &&
						$wp_scripts instanceof WP_Scripts &&
						property_exists( $wp_scripts, 'registered' ) &&
						isset( $wp_scripts->registered[$handle] ) &&
						is_object( $wp_scripts->registered[$handle] ) &&
						property_exists( $wp_scripts->registered[$handle], 'src' ) &&
						in_array( 'src', get_class_vars( get_class( $wp_scripts->registered[$handle] ) ) ) &&
						$wp_scripts->registered[$handle]->src !== $script['src']
					) {
						wps_log_warning( sprintf( 'Conflicting script %s will be replaced.', $handle ) );
						wp_deregister_script( $handle );
					}
				}

				if ( !wp_script_is( $handle, 'registered' ) ) {
					$script_registered = wp_register_script( $handle, $script['src'], $script['deps'], WOO_PS_PLUGIN_VERSION, true );
					if ( !$script_registered ) {
						wps_log_error( sprintf( 'Script %s could not be registered.', $handle ) );
					} else {
						$scripts_registered++;
					}
				}
			}
			if ( $scripts_registered === count( $scripts ) ) {
				self::$scripts_registered = true;
			}

			wp_localize_script(
				'selectize-ix',
				'selectize_ix',
				array(
					'clear' => __( 'Clear', 'woocommerce-product-search' )
				)
			);

			wp_localize_script(
				'product-filter',
				'woocommerce_product_search_context',
				array(
					'pagination_base' => WooCommerce_Product_Search::get_pagination_base()
				)
			);
		}

		if ( !self::$styles_registered ) {
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
			$styles = array(
				'wps-price-slider' => array(
					'src' => WOO_PS_PLUGIN_URL . ( WPS_DEBUG_STYLES ? '/css/price-slider.css' : '/css/price-slider.min.css' ),
					'deps' => array()
				),
				'selectize' => array(
					'src' => WOO_PS_PLUGIN_URL . ( WPS_DEBUG_STYLES ? '/css/selectize/' . $selectize_css . '.css' : '/css/selectize/' . $selectize_css . '.min.css' ),
					'deps' => array()
				),
				'product-search' => array(
					'src' => WOO_PS_PLUGIN_URL . ( WPS_DEBUG_STYLES ? '/css/product-search.css' : '/css/product-search.min.css' ),
					'deps' => array( 'selectize', 'wps-price-slider' )
				)
			);
			$styles_registered = 0;
			foreach ( $styles as $handle => $style ) {
				if ( wp_style_is( $handle, 'registered' ) ) {
					$wp_styles = wp_styles();
					if (
						is_object( $wp_styles ) &&
						$wp_styles instanceof WP_Styles &&
						property_exists( $wp_styles, 'registered' ) &&
						isset( $wp_styles->registered[$handle] ) &&
						is_object( $wp_styles->registered[$handle] ) &&
						property_exists( $wp_styles->registered[$handle], 'src' ) &&
						in_array( 'src', get_class_vars( get_class( $wp_styles->registered[$handle] ) ) ) &&
						$wp_styles->registered[$handle]->src !== $style['src']
					) {
						wps_log_warning( sprintf( 'Conflicting style %s will be replaced.', $handle ) );
						wp_deregister_style( $handle );
					}
				}
				if ( !wp_style_is( $handle, 'registered' ) ) {
					$style_registered = wp_register_style( $handle, $style['src'], $style['deps'], WOO_PS_PLUGIN_VERSION );
					if ( !$style_registered ) {
						wps_log_error( sprintf( 'Style %s could not be registered.', $handle ) );
					} else {
						$styles_registered++;
					}
				}
			}
			if ( $styles_registered === count( $styles ) ) {
				self::$styles_registered = true;
			}
		}
	}

	/**
	 * Wether to use the engine.
	 *
	 * @return boolean whether to use the search engine
	 */
	public static function use_engine() {

		$settings = Settings::get_instance();
		$auto_replace = $settings->get( WooCommerce_Product_Search::AUTO_REPLACE, WooCommerce_Product_Search::AUTO_REPLACE_DEFAULT );
		$auto_replace_admin = $settings->get( WooCommerce_Product_Search::AUTO_REPLACE_ADMIN, WooCommerce_Product_Search::AUTO_REPLACE_ADMIN_DEFAULT );
		$is_admin = is_admin();
		$use_engine = $auto_replace && !$is_admin || $auto_replace_admin && $is_admin || isset( $_REQUEST['ixwps'] );

		if ( !$use_engine && defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			$auto_replace_rest = $settings->get( WooCommerce_Product_Search::AUTO_REPLACE_REST, WooCommerce_Product_Search::AUTO_REPLACE_REST_DEFAULT );
			if ( $auto_replace_rest ) {
				$use_engine = true;
			}
		}

		$use_engine = apply_filters( 'woocommerce_product_search_use_engine', $use_engine );

		return $use_engine;
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
						$counts[$taxonomy] = Term_Control::get_term_counts( $taxonomy );
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
	 * Handler for term_link (while in the shop loop).
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
				if ( !isset( $_REQUEST[Base::SEARCH_QUERY] ) ) {
					$_REQUEST[Base::SEARCH_QUERY] = $_REQUEST['ixwpss'];
				}
				$search_query = preg_replace( '/[^\p{L}\p{N}]++/u', ' ', $_REQUEST[Base::SEARCH_QUERY] );
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
	 * @param $count int number of results found for the query string
	 *
	 * @return int hit_id if it was recorded (null otherwise, also when it was previously recorded)
	 */
	public static function maybe_record_hit( $search_query, $count ) {

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
	 * @param $min_price float
	 * @param $max_price float
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
	 * @param $min_price float
	 * @param $max_price float
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
					$min_price = isset( $_REQUEST[self::MIN_PRICE] ) ? WooCommerce_Product_Search_Utility::to_float( $_REQUEST[self::MIN_PRICE] ) : null;
					$max_price = isset( $_REQUEST[self::MAX_PRICE] ) ? WooCommerce_Product_Search_Utility::to_float( $_REQUEST[self::MAX_PRICE] ) : null;
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

		global $wpdb, $wp_query;

		$min_max = array(
			'min_price' => 0,
			'max_price' => ''
		);

		$query_control = new Query_Control();
		if ( isset( $wp_query ) && $wp_query->is_main_query() ) {
			$query_control->set_query( $wp_query );
		}
		$parameters = $query_control->get_request_parameters();
		$parameters['min_price'] = null;
		$parameters['max_price'] = null;

		$cache_key = self::get_cache_key( $parameters );
		$cache = Cache::get_instance();
		$cached_min_max = $cache->get( $cache_key, self::MIN_MAX_PRICE_CACHE_GROUP );

		if ( $cached_min_max !== null ) {
			$min_max = $cached_min_max;
		} else {
			$ids = $query_control->get_ids( $parameters );
			if ( count( $ids ) > 0 ) {
				Tools::unique_int( $ids );
				$query =
					"SELECT MIN( 0 + wc_product_meta_lookup.min_price ) AS min_price, MAX( 0 + wc_product_meta_lookup.max_price ) AS max_price " .
					"FROM {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup " .
					"WHERE wc_product_meta_lookup.product_id IN (" . implode( ',', $ids ) . ") ";

				$min_max_row_cache_key = 'min_max_row_' . md5( $query );
				$min_max_row = $cache->get( $min_max_row_cache_key, self::MIN_MAX_PRICE_CACHE_GROUP );
				if ( $min_max_row === null ) {
					$min_max_row = $wpdb->get_row( $query, ARRAY_A );
					$cache->set( $min_max_row_cache_key, $min_max_row, self::MIN_MAX_PRICE_CACHE_GROUP, self::get_cache_lifetime( Engine_Stage_Price::CACHE_LIFETIME, self::MIN_MAX_PRICE_CACHE_GROUP ) );
				}
				if ( $min_max_row ) {
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

			$cache->set( $cache_key, $min_max, self::MIN_MAX_PRICE_CACHE_GROUP, self::get_cache_lifetime( Engine_Stage_Price::CACHE_LIFETIME, self::MIN_MAX_PRICE_CACHE_GROUP ) );
		}

		$_min_max = apply_filters( 'woocommerce_product_search_get_min_max_price', $min_max );
		if ( is_array( $_min_max ) ) {
			foreach ( $_min_max as $key => $value ) {
				switch ( $key ) {
					case 'min_price' :
					case 'max_price' :
						if ( is_numeric( $value ) ) {
							if ( floatval( $value ) === floatval( intval( $value ) ) ) {
								$value = intval( $value );
							} else {
								$value = floatval( $value );
							}
							$min_max[$key] = $value;
						}
					break;
				}
			}
		}

		return $min_max;
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
	 * Computes a cache key based on the parameters provided.
	 *
	 * @param array $parameters set of parameters for which to compute the key
	 *
	 * @return string
	 */
	private static function get_cache_key( $parameters ) {

		return md5( json_encode( $parameters ) );
	}

	/**
	 * Returns the cache lifetime for stored results in seconds.
	 *
	 * @param int $lifetime specific lifetime in seconds @since 5.0.0
	 * @param string $group specific cache group @since 5.0.0
	 *
	 * @return int
	 */
	public static function get_cache_lifetime( $lifetime = null, $group = null ) {
		if ( $lifetime === null ) {
			$lifetime = self::CACHE_LIFETIME;
		}

		$l = intval( apply_filters( 'woocommerce_product_search_cache_lifetime', $lifetime, $group ) );
		return $l;
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
				$max_words = $settings->get( WooCommerce_Product_Search::MAX_TITLE_WORDS, WooCommerce_Product_Search::MAX_TITLE_WORDS_DEFAULT );
				$max_characters = $settings->get( WooCommerce_Product_Search::MAX_TITLE_CHARACTERS, WooCommerce_Product_Search::MAX_TITLE_CHARACTERS_DEFAULT );
				break;
			default :
				$max_words = $settings->get( WooCommerce_Product_Search::MAX_EXCERPT_WORDS, WooCommerce_Product_Search::MAX_EXCERPT_WORDS_DEFAULT );
				$max_characters = $settings->get( WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS, WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS_DEFAULT );
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
