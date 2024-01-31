<?php
/**
 * WC_Shipment_Tracking class file.
 *
 * @package WC_Shipment_Tracking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin's main class.
 */
class WC_Shipment_Tracking {

	/**
	 * Instance of WC_Shipment_Tracking_Actions.
	 *
	 * @var WC_Shipment_Tracking_Actions
	 */
	public $actions;

	/**
	 * Instance of WC_Shipment_Tracking_Compat.
	 *
	 * @var WC_Shipment_Tracking_Compat
	 */
	public $compat;

	/**
	 * Plugin file.
	 *
	 * @since 1.6.2
	 * @var string
	 */
	public $plugin_file;

	/**
	 * Plugin dir.
	 *
	 * @since 1.6.2
	 * @var string
	 */
	public $plugin_dir;

	/**
	 * Plugin URL.
	 *
	 * @since 1.6.2
	 * @var string
	 */
	public $plugin_url;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_file = WC_SHIPMENT_TRACKING_FILE;
		$this->plugin_dir  = WC_SHIPMENT_TRACKING_DIR;
		$this->plugin_url  = untrailingslashit( plugin_dir_url( WC_SHIPMENT_TRACKING_FILE ) );

		// Include required files.
		$this->includes();

		// Init REST API.
		$this->init_rest_api();

		add_action( 'admin_print_styles', array( $this->actions, 'admin_styles' ) );
		add_action( 'add_meta_boxes', array( $this->actions, 'add_meta_box' ) );
		add_action( 'woocommerce_process_shop_order_meta', array( $this->actions, 'save_meta_box' ), 0, 2 );

		// View Order Page.
		add_action( 'woocommerce_view_order', array( $this->actions, 'display_tracking_info' ) );
		add_action( 'woocommerce_email_before_order_table', array( $this->actions, 'email_display' ), 0, 4 );

		// Custom tracking column in admin orders list.
		add_filter( 'manage_shop_order_posts_columns', array( $this->actions, 'add_wc_orders_list_columns' ), 99 );
		add_action( 'manage_shop_order_posts_custom_column', array( $this->actions, 'render_shop_order_columns' ), 10, 2 );
		add_filter( 'manage_woocommerce_page_wc-orders_columns', array( $this->actions, 'add_wc_orders_list_columns' ), 99 );
		add_action( 'manage_woocommerce_page_wc-orders_custom_column', array( $this->actions, 'render_wc_orders_list_columns' ), 10, 2 );

		// Order page metabox actions.
		add_action( 'wp_ajax_wc_shipment_tracking_delete_item', array( $this->actions, 'meta_box_delete_tracking' ) );
		add_action( 'wp_ajax_wc_shipment_tracking_save_form', array( $this->actions, 'save_meta_box_ajax' ) );
		add_action( 'wp_ajax_wc_shipment_tracking_get_items', array( $this->actions, 'get_meta_box_items_ajax' ) );

		$subs_version = $this->get_subscriptions_version();

		// Prevent data being copied to subscriptions.
		if ( null !== $subs_version && version_compare( $subs_version, '2.5.0', '>=' ) ) {
			add_filter( 'wc_subscriptions_renewal_order_data', array( $this->actions, 'prevent_copying_shipment_tracking_data' ), 10 );
		} elseif ( null !== $subs_version && version_compare( $subs_version, '2.0.0', '>=' ) ) {
			add_filter( 'wcs_renewal_order_meta_query', array( $this->actions, 'woocommerce_subscriptions_renewal_order_meta_query' ), 10, 3 );
		} else {
			add_filter( 'woocommerce_subscriptions_renewal_order_meta_query', array( $this->actions, 'woocommerce_subscriptions_renewal_order_meta_query' ), 10, 3 );
		}

		// Subscribe to automated translations.
		add_filter( 'woocommerce_translations_updates_for_woocommerce-shipment-tracking', '__return_true' );

		// Check for updates.
		add_action( 'init', array( 'WC_Shipment_Tracking_Updates', 'check_updates' ) );
		add_action( 'woocommerce_init', array( $this, 'load_post_wc_class' ) );
	}

	/**
	 * Loads any class that needs to check for WC loaded.
	 *
	 * @since 1.6.11
	 */
	public function load_post_wc_class() {
		require_once $this->plugin_dir . '/includes/class-wc-shipment-tracking-privacy.php';
	}

	/**
	 * Include required files.
	 *
	 * @since 1.4.0
	 */
	private function includes() {
		require $this->plugin_dir . '/includes/class-wc-shipment-tracking-actions.php';
		$this->actions = WC_Shipment_Tracking_Actions::get_instance();

		require_once $this->plugin_dir . '/includes/class-wc-shipment-tracking-compat.php';
		$this->compat = new WC_Shipment_Tracking_Compat();
		$this->compat->load_compats();

		require_once $this->plugin_dir . '/includes/class-wc-shipment-tracking-updates.php';
	}

	/**
	 * Init shipment tracking REST API.
	 *
	 * @since 1.5.0
	 */
	private function init_rest_api() {
		add_action( 'rest_api_init', array( $this, 'rest_api_register_routes' ) );
	}

	/**
	 * Register shipment tracking routes.
	 *
	 * @since 1.5.0
	 */
	public function rest_api_register_routes() {
		if ( ! is_a( WC()->api, 'WC_API' ) ) {
			return;
		}

		require_once $this->plugin_dir . '/includes/api/class-wc-shipment-tracking-rest-api-controller.php';
		require_once $this->plugin_dir . '/includes/api/class-wc-shipment-tracking-order-rest-api-controller.php';

		// Register route with default namespace wc-shipment-tracking/v3.
		$api_controller = new WC_Shipment_Tracking_REST_API_Controller();
		$api_controller->register_routes();

		// These are all the same code but with different namespaces for compatibility reasons.
		$api_controller_v1 = new WC_Shipment_Tracking_REST_API_Controller();
		$api_controller_v1->set_namespace( 'wc/v1' );
		$api_controller_v1->register_routes();

		$api_controller_v2 = new WC_Shipment_Tracking_REST_API_Controller();
		$api_controller_v2->set_namespace( 'wc/v2' );
		$api_controller_v2->register_routes();

		// Register route with default namespace wc-shipment-tracking/v3.
		$order_api_controller = new WC_Shipment_Tracking_Order_REST_API_Controller();
		$order_api_controller->register_routes();

		// These are all the same code but with different namespaces for compatibility reasons.
		$order_api_controller_v1 = new WC_Shipment_Tracking_Order_REST_API_Controller();
		$order_api_controller_v1->set_namespace( 'wc/v1' );
		$order_api_controller_v1->register_routes();

		$order_api_controller_v2 = new WC_Shipment_Tracking_Order_REST_API_Controller();
		$order_api_controller_v2->set_namespace( 'wc/v2' );
		$order_api_controller_v2->register_routes();
	}

	/**
	 * Gets the absolute plugin path without a trailing slash, e.g.
	 * /path/to/wp-content/plugins/plugin-directory.
	 *
	 * @return string plugin path
	 */
	public function get_plugin_path() {
		return $this->plugin_dir;
	}

	/**
	 * Gets the version of Subscriptions active on the store.
	 *
	 * This works for stores that have the WC Subscriptions extension activated or
	 * the subscriptions-core version that is bundled in WooCommerce Payments.
	 *
	 * @return null|string
	 */
	public function get_subscriptions_version() {
		$version = null;

		if ( class_exists( 'WC_Subscriptions_Core_Plugin' ) ) {
			$version = WC_Subscriptions_Core_Plugin::instance()->get_plugin_version();
		} elseif ( class_exists( 'WC_Subscriptions' ) ) {
			$version = WC_Subscriptions::$version;
		}

		return $version;
	}
}
