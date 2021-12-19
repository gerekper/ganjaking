<?php
/**
 * WC_CP_Admin_Analytics class
 *
 * @package  WooCommerce Composite Products
 * @since    8.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Composite Products WooCommerce Analytics.
 *
 * @version  8.3.0
 */
class WC_CP_Admin_Analytics {

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

			// TODO: REST API Controllers.
			add_filter( 'woocommerce_admin_rest_controllers', array( __CLASS__, 'add_rest_api_controllers' ) );

			// Register data stores.
			add_filter( 'woocommerce_data_stores', array( __CLASS__, 'register_data_stores' ) );

			// Sync data.
			WC_CP_Admin_Analytics_Sync::init();
		}
	}

	/**
	 * Includes.
	 * @TODO
	 *
	 * @return void
	 */
	protected static function includes() {

		// Global.
		require_once WC_CP_ABSPATH . '/includes/admin/analytics/reports/class-wc-cp-analytics-data-store.php';

		// Revenue.
		require_once WC_CP_ABSPATH . '/includes/admin/analytics/reports/revenue/class-wc-cp-analytics-revenue-rest-controller.php';
		require_once WC_CP_ABSPATH . '/includes/admin/analytics/reports/revenue/class-wc-cp-analytics-revenue-data-store.php';
		require_once WC_CP_ABSPATH . '/includes/admin/analytics/reports/revenue/class-wc-cp-analytics-revenue-query.php';
		// Revenue Stats.
		require_once WC_CP_ABSPATH . '/includes/admin/analytics/reports/revenue/stats/class-wc-cp-analytics-revenue-stats-rest-controller.php';
		require_once WC_CP_ABSPATH . '/includes/admin/analytics/reports/revenue/stats/class-wc-cp-analytics-revenue-stats-data-store.php';
		require_once WC_CP_ABSPATH . '/includes/admin/analytics/reports/revenue/stats/class-wc-cp-analytics-revenue-stats-query.php';

		// Sync.
		require_once( WC_CP_ABSPATH . 'includes/admin/analytics/class-wc-cp-admin-analytics-sync.php' );
	}


	/**
	 * Add "Composite Products" as a Analytics submenu item.
	 *
	 * @param  array  $report_pages  Report page menu items.
	 * @return array
	 */
	public static function add_report_menu_item( $report_pages ) {

		$composite_products_report = array( array(
			'id'     => 'wc-cp-composites-analytics-report',
			'title'  => __( 'Composites', 'woocommerce-composite-products' ),
			'parent' => 'woocommerce-analytics',
			'path'   => '/analytics/composites',
			'nav_args' => array(
				'order'  => 110,
				'parent' => 'woocommerce-analytics',
			),
		) );

		// Make sure that we are at least above the "Setting" menu item.
		array_splice( $report_pages, count( $report_pages ) - 1, 0, $composite_products_report );

		return $report_pages;
	}

	/**
	 * Register analytics JS.
	 */
	public static function register_script() {
		if ( ! class_exists( 'Automattic\WooCommerce\Admin\Loader' ) || ! Automattic\WooCommerce\Admin\Loader::is_admin_or_embed_page() ) {
			return;
		}

		$suffix            = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$script_path       = '/assets/js/admin/analytics' . $suffix . '.js';
		$script_asset_path = WC_CP_ABSPATH . 'assets/js/admin/analytics.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require( $script_asset_path )
			: array(
				'dependencies' => array(),
				'version'      => WC_CP()->version
			);
		$script_url        = WC_CP()->plugin_url() . $script_path;

		wp_register_script(
			'wc-cp-composites-analytics-report',
			$script_url,
			$script_asset[ 'dependencies' ],
			$script_asset[ 'version' ],
			true
		);

		// Load JS translations.
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'wc-cp-composites-analytics-report', 'woocommerce-composite-products', WC_CP_ABSPATH . 'languages/' );
		}

		// Enqueue script.
		wp_enqueue_script( 'wc-cp-composites-analytics-report' );
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
				'composites_report_columns',
			)
		);
	}

	/**
	 * Analytics includes and register REST controllers.
	 *
	 * @param  array  $controllers
	 * @return array
	 */
	public static function add_rest_api_controllers( $controllers ) {
		$controllers[] = 'WC_CP_Analytics_Revenue_REST_Controller';
		$controllers[] = 'WC_CP_Analytics_Revenue_Stats_REST_Controller';

		return $controllers;
	}

	/**
	 * Register Analytics data stores.
	 *
	 * @param  array  $stores
	 * @return array
	 */
	public static function register_data_stores( $stores ) {
		$stores[ 'report-composites-revenue' ]       = 'WC_CP_Analytics_Revenue_Data_Store';
		$stores[ 'report-composites-revenue-stats' ] = 'WC_CP_Analytics_Revenue_Stats_Data_Store';

		return $stores;
	}

	/**
	 * Whether or not the new Analytics reports are enabled.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		$enabled                   = WC_CP_Core_Compatibility::is_wc_admin_active();
		$minimum_wc_admin_required = $enabled && defined( 'WC_ADMIN_VERSION_NUMBER' ) && version_compare( WC_ADMIN_VERSION_NUMBER, '1.6.1', '>=' );
		$minimum_wc_required       = WC_CP_Core_Compatibility::is_wc_version_gte( '4.8' );

		$is_enabled = $enabled && $minimum_wc_required && $minimum_wc_admin_required;
		return (bool) apply_filters( 'woocommerce_cp_analytics_enabled', $is_enabled );
	}
}

WC_CP_Admin_Analytics::init();
