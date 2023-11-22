<?php
/**
 * class-woocommerce-product-search.php
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

use com\itthinx\woocommerce\search\engine\Settings;

/**
 * Boots; activation; deactivation; update; setup.
 */
class WooCommerce_Product_Search {

	const ENABLE_CSS                = 'enable-css';
	const ENABLE_CSS_DEFAULT        = true;
	const ENABLE_INLINE_CSS         = 'enable-inline-css';
	const ENABLE_INLINE_CSS_DEFAULT = false;
	const INLINE_CSS                = 'inline-css';
	const INLINE_CSS_DEFAULT        = '';
	const DEFAULT_DELAY             = 500;
	const MIN_DELAY                 = 250;
	const DEFAULT_CHARACTERS        = 1;
	const MIN_CHARACTERS            = 1;
	const USE_ADMIN_AJAX            = 'admin-ajax';
	const USE_ADMIN_AJAX_DEFAULT    = true;

	const RECORD_HITS               = 'record-hits';
	const RECORD_HITS_DEFAULT       = true;

	const LOG_QUERY_TIMES           = 'log-query-times';
	const LOG_QUERY_TIMES_DEFAULT   = false;

	const USE_WEIGHTS               = 'use-weights';
	const USE_WEIGHTS_DEFAULT       = false;
	const WEIGHT_TITLE              = 'weight-title';
	const WEIGHT_EXCERPT            = 'weight-excerpt';
	const WEIGHT_CONTENT            = 'weight-content';
	const WEIGHT_TAGS               = 'weight-tags';
	const WEIGHT_CATEGORIES         = 'weight-categories';
	const WEIGHT_ATTRIBUTES         = 'weight-attributes';
	const WEIGHT_SKU                = 'weight-sku';
	const WEIGHT_TITLE_DEFAULT      = 50;
	const WEIGHT_EXCERPT_DEFAULT    = 20;
	const WEIGHT_CONTENT_DEFAULT    = 0;
	const WEIGHT_TAGS_DEFAULT       = 10;
	const WEIGHT_CATEGORIES_DEFAULT = 0;
	const WEIGHT_ATTRIBUTES_DEFAULT = 0;
	const WEIGHT_SKU_DEFAULT        = 25;

	const DELETE_DATA               = 'delete-data';
	const NETWORK_DELETE_DATA       = 'network-delete-data';

	const AUTO_REPLACE               = 'auto-replace';
	const AUTO_REPLACE_DEFAULT       = true;
	const AUTO_REPLACE_ADMIN         = 'auto-replace-admin';
	const AUTO_REPLACE_ADMIN_DEFAULT = true;
	const AUTO_REPLACE_JSON          = 'auto-replace-json';
	const AUTO_REPLACE_JSON_DEFAULT  = true;
	const JSON_LIMIT                 = 'json-limit';
	const JSON_LIMIT_DEFAULT         = 250;
	const AUTO_REPLACE_FORM          = 'auto-replace-form';
	const AUTO_REPLACE_FORM_DEFAULT  = true;
	const AUTO_INSTANCE              = 'auto-instance';
	const AUTO_REPLACE_REST          = 'auto-replace-rest';
	const AUTO_REPLACE_REST_DEFAULT  = true;

	const MAX_TITLE_WORDS              = 'max-title-words';
	const MAX_TITLE_WORDS_DEFAULT      = 0;
	const MAX_TITLE_CHARACTERS         = 'max-title-characters';
	const MAX_TITLE_CHARACTERS_DEFAULT = 0;

	const USE_SHORT_DESCRIPTION         = 'use-short-description';
	const USE_SHORT_DESCRIPTION_DEFAULT = true;

	const MAX_EXCERPT_WORDS              = 'max-excerpt-words';
	const MAX_EXCERPT_WORDS_DEFAULT      = 10;
	const MAX_EXCERPT_CHARACTERS         = 'max-excerpt-characters';
	const MAX_EXCERPT_CHARACTERS_DEFAULT = 50;

	const FILTER_PROCESS_DOM         = 'filter-process-dom';
	const FILTER_PROCESS_DOM_DEFAULT = true;
	const FILTER_PARSE_DOM           = 'filter-parse-dom';
	const FILTER_PARSE_DOM_DEFAULT   = false;

	const SERVICE_GET_TERMS_ARGS_APPLY         = 'service-get-terms-args-apply';
	const SERVICE_GET_TERMS_ARGS_APPLY_DEFAULT = false;

	const SHOW_IN_ADMIN_BAR         = 'show-in-admin-bar';
	const SHOW_IN_ADMIN_BAR_DEFAULT = true;

	/**
	 * Collects messages to notify in admin.
	 *
	 * @var array
	 */
	private static $admin_messages = array();

	/**
	 * Put hooks in place and activate.
	 */
	public static function init() {

		if ( !self::check_dependencies() ) {
			return;
		}

		add_action( 'before_woocommerce_init', function() {
			if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WOO_PS_FILE, true );
			}
		} );

		require_once WOO_PS_CORE_LIB . '/class-settings.php';

		$settings = Settings::get_instance();

		register_activation_hook( WOO_PS_FILE, array( __CLASS__, 'activate' ) );
		register_deactivation_hook( WOO_PS_FILE, array( __CLASS__, 'deactivate' ) );
		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
		add_action( 'init', array( __CLASS__, 'wp_init' ) );

		add_action( 'woocommerce_product_search_update_db', array( __CLASS__, 'update_db' ) );
		add_action( 'plugins_loaded', array( __CLASS__, 'plugins_loaded' ) );

		add_action( 'wpmu_new_blog', array( __CLASS__, 'wpmu_new_blog' ), 9, 2 );
		add_action( 'delete_blog', array( __CLASS__, 'delete_blog' ), 10, 2 );

		require_once WOO_PS_CORE_LIB . '/interface-base.php';

		require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-log.php';

		require_once WOO_PS_CORE_LIB . '/class-woocommerce-product-search-system.php';

		require_once WOO_PS_CORE_LIB . '/class-lock.php';

		require_once WOO_PS_CACHE_LIB . '/class-cache-boot.php';

		require_once WOO_PS_ENGINE_LIB . '/class-engine-boot.php';

		require_once WOO_PS_CORE_LIB . '/class-woocommerce-product-search-controller.php';
		require_once WOO_PS_CORE_LIB . '/class-woocommerce-product-search-guardian.php';
		require_once WOO_PS_CORE_LIB . '/class-woocommerce-product-search-indexer.php';
		require_once WOO_PS_CORE_LIB . '/class-woocommerce-product-search-product-processor.php';
		require_once WOO_PS_CORE_LIB . '/class-woocommerce-product-search-term-processor.php';
		require_once WOO_PS_CORE_LIB . '/class-woocommerce-product-search-worker.php';
		require_once WOO_PS_CORE_LIB . '/class-woocommerce-product-search-service.php';
		require_once WOO_PS_CONTROL_LIB . '/class-product-search-field-control.php';
		require_once WOO_PS_CONTROL_LIB . '/class-query-control.php';
		require_once WOO_PS_CONTROL_LIB . '/class-term-control.php';
		require_once WOO_PS_CORE_LIB . '/class-woocommerce-product-search-hit.php';
		require_once WOO_PS_CORE_LIB . '/class-woocommerce-product-search-utility.php';
		require_once WOO_PS_BLOCKS_LIB . '/class-blocks.php';

		if ( !empty( $_REQUEST['ixmbd'] ) ) {
			add_action( 'plugins_loaded', array( __CLASS__, 'signal_filter_response' ), PHP_INT_MAX );
			$filter_process_dom = $settings->get( self::FILTER_PROCESS_DOM, self::FILTER_PROCESS_DOM_DEFAULT );
			if ( $filter_process_dom ) {
				add_filter( 'wp_print_scripts', array( __CLASS__, 'wp_print_scripts' ), PHP_INT_MAX );
				add_filter( 'wp_print_styles', array( __CLASS__, 'wp_print_styles' ), PHP_INT_MAX );

			}

			add_action( 'wp_loaded', array( __CLASS__, 'wp_loaded' ), 0 );
			self::cleanup_shutdown();

			add_action( 'shutdown', array( __CLASS__, 'shutdown' ), PHP_INT_MIN );

			$_SERVER['REQUEST_URI'] = preg_replace( '/(\?|&)(ixmbd(=[^&\?])|ixmbd=|ixmbd)/i', '', $_SERVER['REQUEST_URI'] );

			unset( $_REQUEST['ixmbd'] );
			unset( $_GET['ixmbd'] );
			unset( $_POST['ixmbd'] );

			add_action( 'template_redirect', array( __CLASS__, 'template_redirect' ) );

			add_filter( 'redirect_canonical', '__return_false' );
		}

		if (
			empty( $_REQUEST['ixmbd'] ) &&
			(
				!empty( $_REQUEST['ixwpss'] ) ||
				!empty( $_REQUEST['ixwpst'] ) ||
				!empty( $_REQUEST['ixwpsf'] ) ||
				!empty( $_REQUEST['ixwpsp'] ) ||
				isset( $_REQUEST['ixwpse'] )
			)
		) {
			add_action( 'template_redirect', array( __CLASS__, 'template_redirect' ) );

			add_filter( 'query_vars', array( __CLASS__, 'query_vars' ), PHP_INT_MAX );

		}

		if ( apply_filters( 'woocommerce_product_search_avoid_redirect_canonical_loops', true ) ) {
			add_filter( 'redirect_canonical', array( __CLASS__, 'redirect_canonical' ), PHP_INT_MAX, 2 );
		}
	}

	/**
	 * Fires the woocommerce_product_search_signal_filter_response action.
	 * Signals that a product filter response is handled.
	 */
	public static function signal_filter_response() {
		do_action( 'woocommerce_product_search_signal_filter_response' );
	}

	/**
	 * Act on template_redirect.
	 */
	public static function template_redirect() {

		if ( is_404() ) {
			$url = $_SERVER['REQUEST_URI'];

			$redirect_url = preg_replace( '~/' . self::get_pagination_base() . '/[0-9]+~i', '', $url );
			$redirect_url = remove_query_arg( 'paged', $redirect_url );
			if ( $url !== $redirect_url ) {
				wp_redirect( $redirect_url, 301 );
				exit;
			}
		}
	}

	/**
	 * Returns the $wp_rewrite->pagination base which defaults to 'page'.
	 *
	 * @since 4.10.0
	 *
	 * @return string pagination base
	 */
	public static function get_pagination_base() {
		global $wp_rewrite;
		$pagination_base = $wp_rewrite->pagination_base;
		$pagination_base = apply_filters( 'woocommerce_product_search_pagination_base', $pagination_base );

		if ( !is_string( $pagination_base ) || empty( $pagination_base ) ) {
			$pagination_base = 'page';
		}
		return $pagination_base;
	}

	/**
	 * Determine whether a redirect should proceed and avoid redirect-loops caused by WordPress' redirect_canonical().
	 *
	 * @since 3.8.0
	 *
	 * @param string $redirect_url
	 * @param string $requested_url
	 *
	 * @return string|boolean
	 */
	public static function redirect_canonical( $redirect_url, $requested_url ) {

		if ( is_string( $redirect_url ) && is_string( $requested_url ) ) {
			if ( strcmp( urldecode( $redirect_url ), urldecode( $requested_url ) ) === 0 ) {
				$redirect_url = false;
			}
		}
		return $redirect_url;
	}

	public static function wp_print_scripts() {
		global $wp_scripts;
		$wp_scripts->queue = array();
	}

	public static function wp_print_styles() {
		global $wp_styles;
		$wp_styles->queue = array();
	}

	public static function wp_loaded() {
		ob_start( array( __CLASS__, 'ob_start' ) );
		global $wps_wp_loaded;
		$wps_wp_loaded = function_exists( 'microtime' ) ? microtime( true ) : time();
	}

	/**
	 * Stabilize query_vars during filter requests to avoid conflicts and interference.
	 *
	 * @since 3.0.0
	 *
	 * @param array $query_vars
	 *
	 * @return array
	 */
	public static function query_vars( $query_vars ) {

		return array_diff(
			$query_vars,
			array(
				WooCommerce_Product_Search_Service::TITLE,
				WooCommerce_Product_Search_Service::EXCERPT,
				WooCommerce_Product_Search_Service::CONTENT,
				WooCommerce_Product_Search_Service::CATEGORIES,
				WooCommerce_Product_Search_Service::TAGS,
				WooCommerce_Product_Search_Service::SKU,
				WooCommerce_Product_Search_Service::ATTRIBUTES,
				WooCommerce_Product_Search_Service::VARIATIONS,
				WooCommerce_Product_Search_Service::MIN_PRICE,
				WooCommerce_Product_Search_Service::MAX_PRICE,
				WooCommerce_Product_Search_Service::ON_SALE,
				WooCommerce_Product_Search_Service::RATING,
				WooCommerce_Product_Search_Service::IN_STOCK,

				'ixwpss',
				'ixwpst',
				'ixwpsf',
				'ixwpsp',
				'ixwpse',
				'ixmbd'
			)
		);

	}

	public static function shutdown() {

		global $wps_wp_loaded, $wps_dom_processing;

		$n = ob_get_level();
		for ( $i = 0; $i < $n ; $i++ ) {
			ob_end_flush();
		}

		if ( WPS_DEBUG && isset( $wps_dom_processing ) ) {
			$wps_shutdown = function_exists( 'microtime' ) ? microtime( true ) : time();
			$wp_loaded_to_shutdown = $wps_shutdown - $wps_wp_loaded;
			$r = $wps_dom_processing['r'];
			$l = $wps_dom_processing['l'];
			$t = $wps_dom_processing['t'];
			wps_log_info( sprintf(
				__( 'WooCommerce Product Search - Buffer %sK / %sK %s%% - Processing %ss / %ss %s%%', 'woocommerce-product-search' ),
				round( $r / 1024, 2 ),
				round( $l / 1024, 2 ),
				( $l > 0 ? round( 100 * $r / $l, 2 ) : '~' ),
				$t,
				$wp_loaded_to_shutdown,
				( $wp_loaded_to_shutdown > 0 ? round( 100 * $t / $wp_loaded_to_shutdown, 2 ) : '~' )
			) );
		}

		self::cleanup_shutdown();
	}

	/**
	 * @since 3.0.0
	 */
	private static function cleanup_shutdown() {

		if ( apply_filters( 'woocommerce_product_search_shutdown_remove_all_actions', true ) ) {
			$add_filter_shutdown = false;
			if ( has_action( 'shutdown', array( 'WooCommerce_Product_Search_Filter', 'shutdown' ) ) ) {
				$add_filter_shutdown = true;
			}
			remove_all_actions( 'shutdown' );
			if ( $add_filter_shutdown ) {
				add_action( 'shutdown', array( 'WooCommerce_Product_Search_Filter', 'shutdown' ) );
			}
		}
	}

	public static function ob_start( $buffer ) {

		global $wps_dom_processing;

		$buffer_length = strlen( $buffer );

		if ( $buffer_length === 0 ) {
			return $buffer;
		}

		$settings = Settings::get_instance();
		$filter_parse_dom = $settings->get( self::FILTER_PARSE_DOM, self::FILTER_PARSE_DOM_DEFAULT );

		$start = function_exists( 'microtime' ) ? microtime( true ) : time();

		if ( $filter_parse_dom ) {
			$libxml_use_internal_errors = libxml_use_internal_errors( true );
			$document = new DOMDocument();
			$document->preserveWhiteSpace = false;
			$document->formatOutput = false;
			$document->loadHTML( $buffer );
			foreach ( array( 'script', 'style', 'link', 'head' ) as $tag ) {
				$list = $document->getElementsByTagName( $tag );
				for ( $i = $list->length; --$i >= 0; ) {
					$node = $list->item( $i );
					$node->parentNode->removeChild( $node );
				}
			}

			$x_path = new DOMXPath( $document );
			foreach ( $x_path->query( '//comment()' ) as $comment ) {
				$comment->parentNode->removeChild( $comment );
			}

			foreach ( $x_path->query( '//*[*]/text()' ) as $text ) {

				$text_content_compact = trim( preg_replace( '/\s+/mu', ' ', $text->textContent ) );
				if ( strlen( $text_content_compact ) === 0 ) {
					$text->parentNode->removeChild( $text );
				}
			}
			$buffer = $document->saveHTML() . '<!-- ixwps -->';

			$buffer = str_replace( array( "\t", "\r", "\n" ), '', $buffer );
			if ( WPS_DEBUG_DOM ) {
				$errors = libxml_get_errors();
				foreach ( $errors as $error ) {
					$type = null;
					switch( $error->level ) {
						case LIBXML_ERR_WARNING :
							$type = 'Warning';
							break;
						case LIBXML_ERR_ERROR :
							$type = 'Error';
							break;
						case LIBXML_ERR_FATAL :
							$type = 'Fatal Error';
							break;
					}
					if ( $type !== null ) {
						wps_log_warning( sprintf( 'DOMDocument %s (%s) [%d:%d] %s', $type, $error->code, $error->line, $error->column, rtrim( $error->message ) ) );
					}
				}
			}
			libxml_clear_errors();
			libxml_use_internal_errors( $libxml_use_internal_errors );
		} else {
			$_buffer = preg_replace( '@<(script|style|head)[^>]*?>.*?</\\1>@si', '', $buffer );
			if ( $_buffer !== null ) {
				$buffer = $_buffer;
			}
			$_buffer = preg_replace( '/<link([^>]+)>/si', '', $buffer );
			if ( $_buffer !== null ) {
				$buffer = $_buffer;
			}
			$_buffer = preg_replace( '/<!--.+?-->/sm', '', $buffer );
			if ( $_buffer !== null ) {
				$buffer = $_buffer;
			}
			$buffer .= '<!-- ixwps/ -->';
		}

		$wps_dom_processing = array(
			't' => ( function_exists( 'microtime' ) ? microtime( true ) : time() ) - $start,
			'l' => $buffer_length,
			'r' => strlen( $buffer )
		);

		return $buffer;
	}

	/**
	 * Pull in our resources, hooked on plugins_loaded.
	 */
	public static function plugins_loaded() {

		load_plugin_textdomain( 'woocommerce-product-search', false, 'woocommerce-product-search/languages' );

		if ( class_exists( 'WooCommerce' ) ) {
			require_once WOO_PS_CORE_LIB . '/class-woocommerce-product-search-product.php';
			require_once WOO_PS_VIEWS_LIB . '/class-filter-renderer.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-field.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-filter.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-filter-context.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-filter-category.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-filter-tag.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-filter-attribute.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-filter-price.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-filter-sale.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-filter-stock.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-filter-rating.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-filter-reset.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-widget.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-filter-widget.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-filter-category-widget.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-filter-tag-widget.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-filter-attribute-widget.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-filter-price-widget.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-filter-sale-widget.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-filter-stock-widget.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-filter-rating-widget.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-filter-reset-widget.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-term-node.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-term-node-renderer.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-term-node-tree-renderer.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-term-node-select-renderer.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-thumbnail.php';
			if ( is_admin() ) {
				require_once WOO_PS_ADMIN_LIB . '/class-woocommerce-product-search-admin-base.php';
				require_once WOO_PS_ADMIN_LIB . '/class-woocommerce-product-search-admin.php';
				require_once WOO_PS_ADMIN_LIB . '/class-woocommerce-product-search-admin-bar.php';
				require_once WOO_PS_ADMIN_LIB . '/class-woocommerce-product-search-admin-navigation.php';
				require_once WOO_PS_ADMIN_LIB . '/class-woocommerce-product-search-admin-product.php';
				require_once WOO_PS_ADMIN_LIB . '/class-woocommerce-product-search-admin-taxonomy.php';
				require_once WOO_PS_ADMIN_LIB . '/class-woocommerce-product-search-admin-notice.php';
				require_once WOO_PS_ADMIN_LIB . '/class-woocommerce-product-search-admin-reports.php';
			}
			require_once WOO_PS_ADMIN_LIB . '/reports/class-woocommerce-product-search-queries-export.php';
			require_once WOO_PS_EXT_LIB . '/class-wps-wc-product-data-store-cpt.php';
			require_once WOO_PS_COMPAT_LIB . '/class-woocommerce-product-search-compat.php';
		}
	}

	/**
	 * Returns an array of blog_ids for current blogs.
	 *
	 * @return array of int with blog ids
	 */
	public static function get_blogs() {
		global $wpdb;
		$result = array();
		if ( is_multisite() ) {
			$blogs = $wpdb->get_results( $wpdb->prepare(
				"SELECT blog_id FROM $wpdb->blogs WHERE site_id = %d AND archived = '0' AND spam = '0' AND deleted = '0' ORDER BY registered DESC",
				$wpdb->siteid
			) );
			if ( is_array( $blogs ) ) {
				foreach( $blogs as $blog ) {
					$result[] = $blog->blog_id;
				}
			}
		} else {
			$result[] = get_current_blog_id();
		}
		return $result;
	}

	/**
	 * Activation of a new blog (multisite).
	 *
	 * @param int $blog_id
	 */
	public static function wpmu_new_blog( $blog_id, $user_id ) {
		if ( is_multisite() ) {
			$active_sitewide_plugins = get_site_option( 'active_sitewide_plugins', array() );
			if ( key_exists( 'woocommerce-product-search/woocommerce-product-search.php', $active_sitewide_plugins ) ) {
				self::switch_to_blog( $blog_id );
				self::setup();
				self::restore_current_blog();
			}
		}
	}

	/**
	 * Deactivation for a blog to be deleted (multisite).
	 *
	 * @param int $blog_id
	 */
	public static function delete_blog( $blog_id, $drop = false ) {
		if ( is_multisite() ) {
			$active_sitewide_plugins = get_site_option( 'active_sitewide_plugins', array() );
			if ( key_exists( 'woocommerce-product-search/woocommerce-product-search.php', $active_sitewide_plugins ) ) {
				self::switch_to_blog( $blog_id );
				self::cleanup( $drop );
				self::restore_current_blog();
			}
		}
	}

	/**
	 * Clear the cache after switching to the blog to avoid using
	 * another blog's cached values.
	 *
	 * @link http://core.trac.wordpress.org/ticket/14941
	 *
	 * @param int $blog_id
	 */
	public static function switch_to_blog( $blog_id ) {

		switch_to_blog( $blog_id );
		if ( function_exists( 'wp_cache_switch_to_blog' ) ) {
			wp_cache_switch_to_blog( $blog_id );
		} else if ( function_exists( 'wp_cache_init' ) ) {
			wp_cache_init();

		} else if ( function_exists( 'wp_cache_flush' ) ) {

			wp_cache_flush();
		}
	}

	/**
	 * Switch back to previous blog.
	 */
	public static function restore_current_blog() {
		restore_current_blog();
	}

	/**
	 * Checks the current version and triggers update if needed.
	 * This will also run the update when switching from WC 2.x to WC 3.x. as we only define our version as of WPS 2.0.0.
	 * Loads translations.
	 */
	public static function wp_init() {

		global $woocommerce_product_search_version;

		$previous_version = get_option( 'woocommerce_product_search_plugin_version', '' );
		$woocommerce_product_search_version = WOO_PS_PLUGIN_VERSION;

		if ( version_compare( $previous_version, $woocommerce_product_search_version ) < 0 ) {
			update_option( 'woocommerce_product_search_plugin_version', $woocommerce_product_search_version );
			self::update( $previous_version );
		}

		$db_version = get_option( 'woocommerce_product_search_db_version', '0' );
		if ( version_compare( $db_version, WOO_PS_PLUGIN_VERSION ) < 0 ) {
			if ( !self::needs_db_update() ) {

				self::update_db();
			}
		}
	}

	/**
	 * Activated plugin, handle setup.
	 *
	 * @param boolean $network_wide if activated network-wide
	 */
	public static function activate( $network_wide = false ) {
		if ( is_multisite() && $network_wide ) {
			$blog_ids = self::get_blogs();
			foreach ( $blog_ids as $blog_id ) {
				self::switch_to_blog( $blog_id );
				self::setup();
				self::restore_current_blog();
			}
		} else {
			self::setup();
		}
	}

	/**
	 * Runs setup based on version update.
	 */
	public static function update( $previous_version ) {
		if ( empty( $previous_version ) ) {
			$previous_version = '0';
		}
		if ( version_compare( $previous_version, '2.0.0' ) < 0 ) {
			self::setup();
			self::cleanup_v1_indexes();
		}
		WooCommerce_Product_Search_Controller::update( $previous_version );
	}

	/**
	 * Whether the database needs to be updated.
	 *
	 * @since 3.0.0
	 *
	 * @return boolean
	 */
	public static function needs_db_update() {

		if ( did_action( 'init' ) < 1 ) {
			return false;
		}
		$db_version = get_option( 'woocommerce_product_search_db_version', '0' );
		return
			( version_compare( $db_version, WOO_PS_PLUGIN_VERSION ) < 0 )
			&&
			( version_compare( $db_version, '3.1.0' ) < 0 );
	}

	/**
	 * Schedule a database update.
	 *
	 * @since 3.0.0
	 */
	public static function schedule_db_update() {
		if ( !self::is_db_update_scheduled() ) {
			$scheduled = wp_schedule_single_event( time() + 20, 'woocommerce_product_search_update_db' );
			if ( $scheduled ) {
				wps_log_info( 'A database update has been scheduled.' );
			} else {
				wps_log_error( 'Failed to schedule a database update.' );
			}
		}
	}

	/**
	 * Whether a database update is scheduled.
	 *
	 * @since 3.0.0
	 *
	 * @return boolean
	 */
	public static function is_db_update_scheduled() {
		return wp_next_scheduled( 'woocommerce_product_search_update_db' ) !== false;
	}

	/**
	 * Update the database.
	 *
	 * @since 3.0.0
	 */
	public static function update_db() {
		WooCommerce_Product_Search_Controller::update_db();
	}

	/**
	 * Runs the setup procedures.
	 */
	private static function setup() {
		WooCommerce_Product_Search_Controller::setup();
	}

	/**
	 * Deactivated plugin, handle data cleanup.
	 *
	 * @param boolean $network_wide if deactivated network-wide
	 */
	public static function deactivate( $network_wide = false ) {
		if ( is_multisite() && $network_wide ) {
			$settings = Settings::get_instance();
			$network_delete_data = $settings->get( WooCommerce_Product_Search::NETWORK_DELETE_DATA, false );
			if ( $network_delete_data ) {
				$blog_ids = self::get_blogs();
				foreach ( $blog_ids as $blog_id ) {
					self::switch_to_blog( $blog_id );
					do_action( 'woocommerce_product_search_deactivate' );
					self::cleanup( true );
					self::restore_current_blog();
				}
			}
		} else {
			do_action( 'woocommerce_product_search_deactivate' );
			self::cleanup();
		}
	}

	/**
	 * Plugin deactivation cleanup - deletes its tables and options.
	 *
	 * @param boolean $drop overrides the plugin's delete-data option, default is false
	 */
	private static function cleanup( $drop = false ) {
		$settings = Settings::get_instance();
		$delete_data = $settings->get( WooCommerce_Product_Search::DELETE_DATA, false );
		if ( $delete_data || $drop ) {
			self::cleanup_metas();
			WooCommerce_Product_Search_Controller::cleanup( true );
			self::cleanup_v1_indexes();
			delete_option( 'woocommerce_product_search_engine_cache_marker' );
			delete_option( 'woocommerce_product_search_file_cache_gc_scheduled' );
			delete_option( 'woocommerce_product_search_worker_init_scheduled' );
			delete_option( 'woocommerce_product_search_plugin_tables' );
			delete_option( 'woocommerce_product_search_plugin_version' );
			delete_option( 'woocommerce_product_search_db_version' );
			$settings->flush();
		}
	}

	/**
	 * Removes site option and all user meta for notices.
	 * Removes all weight meta for products and product categories.
	 * Removes all image meta for terms.
	 */
	private static function cleanup_metas() {
		global $wpdb;

		delete_site_option( 'woocommerce-product-search-init-time' );
		delete_metadata( 'user', null, 'woocommerce-product-search-hide-welcome-notice', null, true );
		delete_metadata( 'user', null, 'woocommerce-product-search-hide-review-notice', null, true );
		delete_metadata( 'user', null, 'woocommerce-product-search-remind-welcome-notice', null, true );
		delete_metadata( 'user', null, 'woocommerce-product-search-remind-later-notice', null, true );
		delete_metadata( 'user', null, 'woocommerce-product-search-report-queries-per-page', null, true );

		$wpdb->query(
			"DELETE FROM $wpdb->postmeta WHERE meta_key = '_search_weight' AND post_id IN ( SELECT ID FROM $wpdb->posts WHERE post_type = 'product' )"
		);
		if ( function_exists( 'delete_term_meta' ) ) {

			$wpdb->query(
				"DELETE FROM $wpdb->termmeta WHERE meta_key IN ( '_search_weight', '_search_weight_sum' ) AND term_id IN ( SELECT term_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'product_cat' )"
			);
		} else {
			delete_metadata( 'woocommerce_term', null, '_search_weight', '', true );
		}
		if ( function_exists( 'delete_term_meta' ) ) {
			$product_taxonomies = array(
				'product_cat',
				'product_tag'
			);
			$attribute_taxonomies = wc_get_attribute_taxonomies();
			if ( !empty( $attribute_taxonomies ) ) {
				foreach ( $attribute_taxonomies as $attribute ) {
					$product_taxonomies[] = 'pa_' . $attribute->attribute_name;
				}
			}
			$product_taxonomies = '\'' . implode( '\',\'', array_map( 'esc_sql', $product_taxonomies ) ) . '\'';
			$wpdb->query(
				"DELETE FROM $wpdb->termmeta WHERE meta_key = 'product_search_image_id' AND term_id IN ( SELECT term_id FROM $wpdb->term_taxonomy WHERE taxonomy IN ($product_taxonomies) )"
			);
		} else {
			delete_metadata( 'woocommerce_term', null, 'product_search_image_id', '', true );
		}
	}

	/**
	 * Removes the fulltext indexes.
	 */
	private static function cleanup_v1_indexes() {

		wps_log_info( 'Cleaning up old FT indexes.' );

		global $wpdb;

		@set_time_limit( 0 );
		@ignore_user_abort( true );

		$settings = Settings::get_instance();
		$settings->delete( 'use-fulltext' );
		$settings->delete( 'fulltext-boolean' );
		$settings->delete( 'fulltext-wildcards' );
		$settings->delete( 'ft_min_word_len' );
		$settings->save();

		$indexes = array();
		$results = $wpdb->get_results( "SHOW INDEX FROM $wpdb->posts WHERE Key_name = 'wps_ft_title'" );
		if ( !empty( $results ) ) {
			$indexes[] = 'DROP INDEX wps_ft_title';
		}
		$results = $wpdb->get_results( "SHOW INDEX FROM $wpdb->posts WHERE Key_name = 'wps_ft_excerpt'" );
		if ( !empty( $results ) ) {
			$indexes[] = 'DROP INDEX wps_ft_excerpt';
		}
		$results = $wpdb->get_results( "SHOW INDEX FROM $wpdb->posts WHERE Key_name = 'wps_ft_content'" );
		if ( !empty( $results ) ) {
			$indexes[] = 'DROP INDEX wps_ft_content';
		}

		foreach ( $indexes as $index ) {
			$query = "ALTER TABLE $wpdb->posts $index";
			if ( $wpdb->query( $query ) === false ) {
				wps_log_error( $wpdb->last_error );
			}
		}

		$results = $wpdb->get_results( "SHOW INDEX FROM $wpdb->terms WHERE Key_name = 'wps_ft_name'" );
		if ( !empty( $results ) ) {
			$query = "ALTER TABLE $wpdb->terms DROP INDEX wps_ft_name";
			if ( $wpdb->query( $query ) === false ) {
				wps_log_error( $wpdb->last_error );
			}
		}
	}

	/**
	 * Prints admin notices.
	 */
	public static function admin_notices() {
		if ( !empty( self::$admin_messages ) ) {
			foreach ( self::$admin_messages as $msg ) {
				echo wp_kses(
					$msg,
					array(
						'a'      => array( 'href' => array(), 'target' => array(), 'title' => array() ),
						'br'     => array(),
						'div'    => array( 'class' => array() ),
						'em'     => array(),
						'id'     => array(),
						'p'      => array( 'class' => array() ),
						'strong' => array()
					)
				);
			}
		}
	}

	/**
	 * Check plugin dependencies and nag if they are not met.
	 *
	 * @param boolean $disable @deprecated parameter since 4.13.0 [was: disable the plugin if true, defaults to false]
	 */
	public static function check_dependencies( $disable = false ) {
		$woocommerce_is_active = false;

		if ( function_exists( 'wp_get_active_and_valid_plugins' ) ) {
			$woocommerce_plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';
			$active_plugin_paths = wp_get_active_and_valid_plugins();
			$woocommerce_is_active = in_array( $woocommerce_plugin_path, $active_plugin_paths );
			if ( !$woocommerce_is_active && is_multisite() && function_exists( 'wp_get_active_network_plugins' ) ) {
				$active_network_plugin_paths = wp_get_active_network_plugins();
				$woocommerce_is_active = in_array( $woocommerce_plugin_path, $active_network_plugin_paths );
			}
		} else {
			$active_plugins = get_option( 'active_plugins', array() );
			if ( is_multisite() ) {
				$active_sitewide_plugins = get_site_option( 'active_sitewide_plugins', array() );
				$active_sitewide_plugins = array_keys( $active_sitewide_plugins );
				$active_plugins = array_merge( $active_plugins, $active_sitewide_plugins );
			}
			$woocommerce_is_active = in_array( 'woocommerce/woocommerce.php', $active_plugins );
		}

		if ( !$woocommerce_is_active ) {
			add_action( 'admin_notices', array( __CLASS__, 'woocommerce_is_missing' ) );

		}

		return $woocommerce_is_active;
	}

	/**
	 * Admin notice about WooCommerce missing.
	 *
	 * @since 4.13.0
	 */
	public static function woocommerce_is_missing() {
		echo '<div class="error">';
		echo '<p>';
		echo '<strong>';
		/* translators: Link */
		printf( esc_html__( 'WooCommerce Product Search requires %s to be installed and active.', 'woocommerce-product-search' ), '<a href="https://woo.com/" target="_blank">WooCommerce</a>' ) ;
		echo '</strong>';
		echo '</p>';
		echo '</div>';
	}
}
WooCommerce_Product_Search::init();
