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

/**
 * Boots.
 * Essentials.
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

	const LOG_QUERY_TIMES           = 'log-query-times';
	const LOG_QUERY_TIMES_DEFAULT   = false;
	const USE_FULLTEXT              = 'use-fulltext';
	const USE_FULLTEXT_DEFAULT      = false;
	const FULLTEXT_BOOLEAN          = 'fulltext-boolean';
	const FULLTEXT_BOOLEAN_DEFAULT  = true;
	const FULLTEXT_WILDCARDS        = 'fulltext-wildcards';
	const FULLTEXT_WILDCARDS_DEFAULT = true;
	const FT_MIN_WORD_LEN            = 'ft_min_word_len';

	const USE_WEIGHTS               = 'use-weights';
	const USE_WEIGHTS_DEFAULT       = false;
	const WEIGHT_TITLE              = 'weight-title';
	const WEIGHT_EXCERPT            = 'weight-excerpt';
	const WEIGHT_CONTENT            = 'weight-content';
	const WEIGHT_TAGS               = 'weight-tags';
	const WEIGHT_TITLE_DEFAULT      = 50;
	const WEIGHT_EXCERPT_DEFAULT    = 20;
	const WEIGHT_CONTENT_DEFAULT    = 0;
	const WEIGHT_TAGS_DEFAULT       = 10;

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
		register_activation_hook( WOO_PS_FILE, array( __CLASS__, 'activate' ) );
		register_deactivation_hook( WOO_PS_FILE, array( __CLASS__, 'deactivate' ) );
		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
		add_action( 'init', array( __CLASS__, 'wp_init' ) );
		add_action( 'plugins_loaded', array( __CLASS__, 'plugins_loaded' ) );
	}

	/**
	 * Pull in our resources, hooked on plugins_loaded.
	 */
	public static function plugins_loaded() {
		if ( self::check_dependencies() ) {
			require_once WOO_PS_CORE_LIB . '/class-woocommerce-product-search-product.php';
			require_once WOO_PS_CORE_LIB . '/class-woocommerce-product-search-service.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-shortcodes.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-widget.php';
			require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-thumbnail.php';
			if ( is_admin() ) {
				require_once WOO_PS_ADMIN_LIB . '/class-woocommerce-product-search-admin.php';
				require_once WOO_PS_ADMIN_LIB . '/class-woocommerce-product-search-admin-product.php';
			}
		}
	}

	/**
	 * Loads translations.
	 */
	public static function wp_init() {

		load_plugin_textdomain( 'woocommerce-product-search', false, 'woocommerce-product-search/languages' );
	}

	/**
	 * Activate plugin.
	 * Reschedules pending tasks.
	 *
	 * @param boolean $network_wide
	 */
	public static function activate( $network_wide = false ) {
	}

	/**
	 * Deactivate plugin.
	 *
	 * @param boolean $network_wide
	 */
	public static function deactivate( $network_wide = false ) {
	}

	/**
	 * Uninstall plugin.
	 */
	public static function uninstall() {
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
	 * @param boolean $disable disable the plugin if true, defaults to false
	 */
	public static function check_dependencies( $disable = false ) {
		$result = true;
		$active_plugins = get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$active_sitewide_plugins = get_site_option( 'active_sitewide_plugins', array() );
			$active_sitewide_plugins = array_keys( $active_sitewide_plugins );
			$active_plugins = array_merge( $active_plugins, $active_sitewide_plugins );
		}
		$woocommerce_is_active = in_array( 'woocommerce/woocommerce.php', $active_plugins );
		if ( !$woocommerce_is_active ) {
			self::$admin_messages[] = '<div class="error">' . __( '<em>WooCommerce Product Search</em> is an extension for the <a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a> plugin. Please install and activate it.', 'woocommerce-product-search' ) . '</div>';
		}

		if ( !$woocommerce_is_active ) {
			if ( $disable ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				deactivate_plugins( array( __FILE__ ) );
			}
			$result = false;
		}
		return $result;
	}
}
WooCommerce_Product_Search::init();
