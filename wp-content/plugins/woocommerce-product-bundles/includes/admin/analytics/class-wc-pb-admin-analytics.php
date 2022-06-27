<?php
/**
 * WC_PB_Admin_Analytics class
 *
 * @package  WooCommerce Product Bundles
 * @since    6.9.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Bundles WooCommerce Analytics.
 *
 * @version  6.15.3
 */
class WC_PB_Admin_Analytics {

	/*
	 * Init.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'setup' ) );
	}

	/*
	 * Setup Analytics.
	 */
	public static function setup() {

		if ( self::is_enabled() ) {

			self::includes();

			// Analytics init.
			add_filter( 'woocommerce_analytics_report_menu_items', array( __CLASS__, 'add_report_menu_item' ) );
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'register_script' ) );

			// Define custom woocommerce_meta keys.
			add_filter( 'woocommerce_admin_get_user_data_fields', array( __CLASS__, 'add_user_data_fields' ) );

			// REST API Controllers.
			add_filter( 'woocommerce_admin_rest_controllers', array( __CLASS__, 'add_rest_api_controllers' ) );

			// Register data stores.
			add_filter( 'woocommerce_data_stores', array( __CLASS__, 'register_data_stores' ) );

			// Sync data.
			WC_PB_Admin_Analytics_Sync::init();
		}
	}

	/**
	 * Includes.
	 *
	 * @return void
	 */
	protected static function includes() {

		// Global.
		require_once WC_PB_ABSPATH . '/includes/admin/analytics/reports/class-wc-pb-analytics-data-store.php';

		// Stock.
		require_once WC_PB_ABSPATH . '/includes/admin/analytics/reports/stock/class-wc-pb-analytics-stock-rest-controller.php';
		require_once WC_PB_ABSPATH . '/includes/admin/analytics/reports/stock/class-wc-pb-analytics-stock-data-store.php';
		require_once WC_PB_ABSPATH . '/includes/admin/analytics/reports/stock/class-wc-pb-analytics-stock-query.php';

		// Revenue.
		require_once WC_PB_ABSPATH . '/includes/admin/analytics/reports/revenue/class-wc-pb-analytics-revenue-rest-controller.php';
		require_once WC_PB_ABSPATH . '/includes/admin/analytics/reports/revenue/class-wc-pb-analytics-revenue-data-store.php';
		require_once WC_PB_ABSPATH . '/includes/admin/analytics/reports/revenue/class-wc-pb-analytics-revenue-query.php';
		// Revenue Stats.
		require_once WC_PB_ABSPATH . '/includes/admin/analytics/reports/revenue/stats/class-wc-pb-analytics-revenue-stats-controller.php';
		require_once WC_PB_ABSPATH . '/includes/admin/analytics/reports/revenue/stats/class-wc-pb-analytics-revenue-stats-data-store.php';
		require_once WC_PB_ABSPATH . '/includes/admin/analytics/reports/revenue/stats/class-wc-pb-analytics-revenue-stats-query.php';

		// Sync.
		require_once( WC_PB_ABSPATH . 'includes/admin/analytics/class-wc-pb-admin-analytics-sync.php' );
	}


	/**
	 * Add "Bundles" as a Analytics submenu item.
	 *
	 * @param  array  $report_pages  Report page menu items.
	 * @return array
	 */
	public static function add_report_menu_item( $report_pages ) {

		$bundles_report = array( array(
			'id'     => 'wc-pb-bundles-analytics-report',
			'title'  => __( 'Bundles', 'woocommerce-product-bundles' ),
			'parent' => 'woocommerce-analytics',
			'path'   => '/analytics/bundles',
			'nav_args' => array(
				'order'  => 110,
				'parent' => 'woocommerce-analytics',
			),
		) );

		// Make sure that we are at least above the "Setting" menu item.
		array_splice( $report_pages, count( $report_pages ) - 1, 0, $bundles_report );

		return $report_pages;
	}

	/**
	 * Register analytics JS.
	 */
	public static function register_script() {

		if ( ! WC_PB_Core_Compatibility::is_admin_or_embed_page() ) {
			return;
		}

		$suffix            = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$script_path       = '/assets/dist/admin/analytics' . $suffix . '.js';
		$script_asset_path = WC_PB_ABSPATH . 'assets/dist/admin/analytics.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require( $script_asset_path )
			: array(
				'dependencies' => array(),
				'version'      => WC_PB()->version
			);
		$script_url        = WC_PB()->plugin_url() . $script_path;

		wp_register_script(
			'wc-pb-bundles-analytics-report',
			$script_url,
			$script_asset[ 'dependencies' ],
			$script_asset[ 'version' ],
			true
		);

		// Load JS translations.
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'wc-pb-bundles-analytics-report', 'woocommerce-product-bundles', WC_PB_ABSPATH . 'languages/' );
		}

		// Enqueue script.
		wp_enqueue_script( 'wc-pb-bundles-analytics-report' );
	}

	/**
	 * Adds fields so that we can store user preferences for the columns to display on a report.
	 *
	 * @param array $user_data_fields User data fields.
	 * @return array
	 */
	public static function add_user_data_fields( $user_data_fields ) {
		return array_merge(
			$user_data_fields,
			array(
				'bundles_report_columns',
				'bundles_stock_report_columns'
			)
		);
	}

	/**
	 * Analytics includes and register REST contollers.
	 *
	 * @param  array  $controllers
	 * @return array
	 */
	public static function add_rest_api_controllers( $controllers ) {
		$controllers[] = 'WC_PB_Analytics_Stock_REST_Controller';
		$controllers[] = 'WC_PB_Analytics_Revenue_REST_Controller';
		$controllers[] = 'WC_PB_Analytics_Revenue_Stats_REST_Controller';

		return $controllers;
	}

	/**
	 * Register Analytics data stores.
	 *
	 * @param  array  $stores
	 * @return array
	 */
	public static function register_data_stores( $stores ) {
		$stores[ 'report-bundles-stock' ]         = 'WC_PB_Analytics_Stock_Data_Store';
		$stores[ 'report-bundles-revenue' ]       = 'WC_PB_Analytics_Revenue_Data_Store';
		$stores[ 'report-bundles-revenue-stats' ] = 'WC_PB_Analytics_Revenue_Stats_Data_Store';

		return $stores;
	}

	/**
	 * Whether or not the new Analytics reports are enabled.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		$enabled                   = WC_PB_Core_Compatibility::is_wc_admin_active();
		$minimum_wc_admin_required = $enabled && defined( 'WC_ADMIN_VERSION_NUMBER' ) && version_compare( WC_ADMIN_VERSION_NUMBER, '1.6.1', '>=' );
		$minimum_wc_required       = WC_PB_Core_Compatibility::is_wc_version_gte( '4.8' );

		$is_enabled = $enabled && $minimum_wc_required && $minimum_wc_admin_required;
		return (bool) apply_filters( 'woocommerce_pb_analytics_enabled', $is_enabled );
	}
}

WC_PB_Admin_Analytics::init();
