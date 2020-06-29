<?php
/**
 * Plugin Name: WooCommerce Google Product Feed
 * Plugin URI: https://woocommerce.com/products/google-product-feed/
 * Description: WooCommerce extension that allows you to more easily populate advanced attributes into the Google Merchant Centre feed
 * Author: Ademti Software Ltd.
 * Version: 8.1.3
 * Woo: 18619:d55b4f852872025741312839f142447e
 * WC requires at least: 3.6
 * WC tested up to: 4.3
 * Author URI: https://www.ademti-software.co.uk/
 * License: GPLv3
 *
 * @package woocommerce-gpf
 */

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once 'woo-includes/woo-functions.php';
}

// The current DB schema version.
define( 'WOOCOMMERCE_GPF_DB_VERSION', 7 );

// The current version.
define( 'WOOCOMMERCE_GPF_VERSION', '8.1.3' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'd55b4f852872025741312839f142447e', '18619' );

require_once dirname( __FILE__ ) . '/vendor/autoload_52.php';

global $woocommerce_gpf_common;
$woocommerce_gpf_common = new WoocommerceGpfCommon();
$woocommerce_gpf_common->initialise();

global $woocommerce_gpf_rest_api;
$woocommerce_gpf_rest_api = new WoocommerceGpfRestApi( $woocommerce_gpf_common );
$woocommerce_gpf_rest_api->initialise();

if ( is_admin() ) {
	global $woocommerce_gpf_admin;
	$woocommerce_gpf_admin = new WoocommerceGpfAdmin();
	global $woocommerce_prf_admin;
	$woocommerce_prf_admin         = new WoocommercePrfAdmin();
	$woocommerce_gpf_status_report = new WoocommerceGpfStatusReport( new WoocommerceGpfTemplateLoader(), $woocommerce_gpf_common );
	$woocommerce_gpf_status_report->initialise();
}


/**
 * Import / export support for WooCommerce 3.1+
 */
global $woocommerce_gpf_import_export;
$woocommerce_gpf_import_export = new WoocommerceGpfImportExportIntegration();

/**
 * Bodge for WPEngine.com users - provide the feed at a URL that doesn't
 * rely on query arguments as WPEngine don't support URLs with query args
 * if the requestor is a googlebot. #broken
 */
function woocommerce_gpf_endpoints() {
	add_rewrite_tag( '%woocommerce_gpf%', '([^/]+)' );
	add_rewrite_tag( '%gpf_start%', '([0-9]{1,})' );
	add_rewrite_tag( '%gpf_limit%', '([0-9]{1,})' );
	add_rewrite_tag( '%gpf_categories%', '^(\d+(,\d+)*)?$' );
	add_rewrite_rule( 'woocommerce_gpf/([^/]+)/gpf_start/([0-9]{1,})/gpf_limit/([0-9]{1,})/gpf_categories/(\d+(,\d+)*)', 'index.php?woocommerce_gpf=$matches[1]&gpf_start=$matches[2]&gpf_limit=$matches[3]&gpf_categories=$matches[4]', 'top' );
	add_rewrite_rule( 'woocommerce_gpf/([^/]+)/gpf_start/([0-9]{1,})/gpf_limit/([0-9]{1,})', 'index.php?woocommerce_gpf=$matches[1]&gpf_start=$matches[2]&gpf_limit=$matches[3]', 'top' );
	add_rewrite_rule( 'woocommerce_gpf/([^/]+)/gpf_start/([0-9]{1,})', 'index.php?woocommerce_gpf=$matches[1]&gpf_start=$matches[2]', 'top' );
	add_rewrite_rule( 'woocommerce_gpf/([^/]+)/gpf_categories/(\d+(,\d+)*)', 'index.php?woocommerce_gpf=$matches[1]&gpf_categories=$matches[2]', 'top' );
	add_rewrite_rule( 'woocommerce_gpf/([^/]+)', 'index.php?woocommerce_gpf=$matches[1]', 'top' );
}

add_action( 'init', 'woocommerce_gpf_endpoints' );

/**
 * Include the relevant files dependant on the page request type
 */
function woocommerce_gpf_includes() {

	global $wp_query;

	// Parsing for legacy URLs.
	if ( isset( $_REQUEST['action'] ) && 'woocommerce_gpf' === $_REQUEST['action'] ) {
		if ( isset( $_REQUEST['feed_format'] ) ) {
			$wp_query->query_vars['woocommerce_gpf'] = $_REQUEST['feed_format'];
		} else {
			$wp_query->query_vars['woocommerce_gpf'] = 'google';
		}
	}

	if ( isset( $wp_query->query_vars['woocommerce_gpf'] ) ) {
		if ( $wp_query->query_vars['woocommerce_gpf'] === 'googlereview' ) {
			global $woocommerce_prf_feed;
			$woocommerce_prf_feed = new WoocommercePrfGoogleReviewFeed();
		} else {
			global $woocommerce_gpf_frontend;
			$woocommerce_gpf_frontend = new WoocommerceGpfFrontend();
		}
	}
}

add_action( 'template_redirect', 'woocommerce_gpf_includes' );

/**
 * Include/invoke relevant classes if we're doing product structured data.
 */
function woocommerce_gpf_structured_data() {
	global $woocommerce_gpf_structured_data;
	$woocommerce_gpf_structured_data = new WoocommerceGpfStructuredData();
}

// Loads at priority 5 to ensure it runs before WooCommerce's hook.
add_action( 'woocommerce_single_product_summary', 'woocommerce_gpf_structured_data', 5 );


function woocommerce_gpf_load_integrations() {
	if ( class_exists( 'WC_COG_Loader' ) ) {
		global $woocommerce_gpf_cost_of_goods;
		$woocommerce_gpf_cost_of_goods = new WoocommerceCostOfGoods();
		$woocommerce_gpf_cost_of_goods->run();
	}
	if ( defined( 'WOOCOMMERCE_MULTICURRENCY_VERSION' ) &&
	     version_compare( WOOCOMMERCE_MULTICURRENCY_VERSION, '1.9.0', 'ge' ) ) {
		global $woocommerce_gpf_multicurrency;
		$woocommerce_gpf_multicurrency = new WoocommerceGpfMulticurrency();
		$woocommerce_gpf_multicurrency->run();
	}
	if ( defined( 'WC_MIN_MAX_QUANTITIES' ) &&
	     version_compare( WC_MIN_MAX_QUANTITIES, '2.4.5', 'ge' ) ) {
		global $woocommerce_gpf_min_max_quantities;
		$woocommerce_gpf_min_max_quantities = new WoocommerceMinMaxQuantities();
		$woocommerce_gpf_min_max_quantities->run();
	}
	if (defined('WC_PRODUCT_VENDORS_VERSION') &&
	    version_compare( WC_PRODUCT_VENDORS_VERSION, '2.1.16', 'ge' ) ) {
		global $woocommerce_gpf_product_vendors;
		$woocommerce_gpf_product_vendors = new WoocommerceProductVendors();
		$woocommerce_gpf_product_vendors->run();
	}
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		global $woocommerce_gpf_the_content_protection;
		$woocommerce_gpf_the_content_protection = new WoocommerceGpfTheContentProtection();
		$woocommerce_gpf_the_content_protection->run();
	}
	if ( defined( 'WPSEO_WOO_VERSION' ) ) {
		global $woocommerce_gpf_yoast_woocommerce_seo;
		$woocommerce_gpf_yoast_woocommerce_seo = new WoocommerceGpfYoastWoocommerceSeo();
		$woocommerce_gpf_yoast_woocommerce_seo->run();
	}
}

add_action( 'plugins_loaded', 'woocommerce_gpf_load_integrations' );

/**
 * Determine if this is a feed URL.
 *
 * May need to be used before parse_query, so we have to manually check all
 * sorts of combinations.
 *
 * @return boolean  True if a feed is being generated.
 */
function woocommerce_gpf_is_generating_feed() {
	return ( isset( $_REQUEST['action'] ) && 'woocommerce_gpf' === $_REQUEST['action'] ) ||
	       ( isset( $_SERVER['REQUEST_URI'] ) && stripos( $_SERVER['REQUEST_URI'], '/woocommerce_gpf' ) === 0 ) ||
	       isset( $_REQUEST['woocommerce_gpf'] );
}

/**
 * Override the default customer address.
 */
function woocommerce_gpf_set_customer_default_location( $location ) {
	if ( woocommerce_gpf_is_generating_feed() ) {
		return wc_format_country_state_string( get_option( 'woocommerce_default_country' ) );
	} else {
		return $location;
	}
}

add_filter( 'woocommerce_customer_default_location_array', 'woocommerce_gpf_set_customer_default_location' );

/**
 * Create database table to cache the Google product taxonomy.
 */
function woocommerce_gpf_install() {

	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();

	$table_name = $wpdb->prefix . 'woocommerce_gpf_google_taxonomy';

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$sql = "CREATE TABLE $table_name (
	            taxonomy_term text,
	            search_term text
			) $charset_collate";
	dbDelta( $sql );

	$sql = 'CREATE TABLE `' . $wpdb->prefix . "wc_gpf_render_cache` (
	  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	  `post_id` bigint(20) unsigned NOT NULL,
	  `name` varchar(32) NOT NULL,
	  `value` LONGTEXT NOT NULL,
	  UNIQUE KEY composite_cache_idx (`post_id`, `name`)
	) $charset_collate";
	dbDelta( $sql );

	flush_rewrite_rules();

	// Upgrade old tables on plugin deactivation / activation.
	$wpdb->query( "ALTER TABLE $table_name CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci" );

	update_option( 'woocommerce_gpf_db_version', WOOCOMMERCE_GPF_DB_VERSION );

	// Set default settings if there are none.
	$settings = get_option( 'woocommerce_gpf_config' );
	if ( false === $settings ) {
		$settings = array(
			'product_fields'      => array(
				'availability'            => 'on',
				'brand'                   => 'on',
				'mpn'                     => 'on',
				'product_type'            => 'on',
				'google_product_category' => 'on',
				'size_system'             => 'on',
			),
			'product_defaults'    => array(
				'availability' => 'in stock',
			),
			'product_prepopulate' => array(
				'description' => 'description:fullvar',
			),
			'gpf_enabled_feeds'   => array( 'google' => 'on' ),
		);
		$settings['include_variations'] = 'on';
		$settings['send_item_group_id'] = '';
		add_option( 'woocommerce_gpf_config', $settings, '', 'yes' );
	}
	update_option( 'woocommerce_gpf_debug_key', wp_generate_uuid4() );
}
register_activation_hook( __FILE__, 'woocommerce_gpf_install' );

/**
 * Disable attempts to GZIP the feed output to avoid memory issues.
 */
function woocommerce_gpf_block_wordpress_gzip_compression() {
	if ( isset( $_GET['woocommerce_gpf'] ) ) {
		remove_action( 'init', 'ezgz_buffer' );
	}
}
add_action( 'plugins_loaded', 'woocommerce_gpf_block_wordpress_gzip_compression' );

function woocommerce_gpf_prevent_wporg_update_check( $r, $url ) {
	if ( 0 === strpos( $url, 'https://api.wordpress.org/plugins/update-check/' ) ) {
		$my_plugin = plugin_basename( __FILE__ );
		$plugins   = @json_decode( $r['body']['plugins'], true );
		if ( null === $plugins ) {
			return $r;
		}
		unset( $plugins['active'][ array_search( $my_plugin, $plugins['active'], true ) ] );
		unset( $plugins['plugins'][ $my_plugin ] );
		$r['body']['plugins'] = json_encode( $plugins );
	}

	return $r;
}
add_filter( 'http_request_args', 'woocommerce_gpf_prevent_wporg_update_check', 10, 2 );

// Make sure the job handlers are available when running cron.
function woocommerce_gpf_ensure_cron_handlers_available() {
	new WoocommerceGpfCache();
}
add_action( 'plugins_loaded', 'woocommerce_gpf_ensure_cron_handlers_available' );

// Make sure the cron schedules are available when running cron.
function woocommerce_gpf_cron_schedules( $schedules ) {
	$schedules['wp_woocommerce_gpf_rebuild_all_cron_interval']     = array(
		'interval' => MINUTE_IN_SECONDS * 5,
		'display'  => sprintf( __( 'Every %d Minutes' ), 5 ),
	);
	$schedules['wp_woocommerce_gpf_rebuild_product_cron_interval'] = array(
		'interval' => MINUTE_IN_SECONDS * 5,
		'display'  => sprintf( __( 'Every %d Minutes' ), 5 ),
	);
	$schedules['wp_woocommerce_gpf_rebuild_term_cron_interval']    = array(
		'interval' => MINUTE_IN_SECONDS * 5,
		'display'  => sprintf( __( 'Every %d Minutes' ), 5 ),
	);

	return $schedules;
}
add_filter( 'cron_schedules', 'woocommerce_gpf_cron_schedules' );

require_once dirname( __FILE__ ) . '/src/gpf/woocommerce-gpf-template-functions.php';
