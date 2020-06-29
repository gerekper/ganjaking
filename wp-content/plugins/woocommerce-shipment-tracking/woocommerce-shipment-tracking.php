<?php
/**
 * Plugin Name: WooCommerce Shipment Tracking
 * Plugin URI: https://woocommerce.com/products/shipment-tracking/
 * Description: Add tracking numbers to orders allowing customers to track their orders via a link. Supports many shipping providers, as well as custom ones if neccessary via a regular link.
 * Version: 1.6.23
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Text Domain: woocommerce-shipment-tracking
 * Domain Path: /languages
 * WC requires at least: 2.6
 * WC tested up to: 4.2
 * Tested up to: 5.3
 *
 * Copyright: © 2020 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WC_Shipment_Tracking
 *
 * Woo: 18693:1968e199038a8a001c9f9966fd06bf88
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce fallback notice.
 *
 * @since 1.6.20
 * @return void
 */
function woocommerce_shipment_tracking_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Shipment Tracking requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-shipment-tracking' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

/**
 * WC_Shipment_Tracking class
 */
if ( ! class_exists( 'WC_Shipment_Tracking' ) ) :
	define( 'WC_SHIPMENT_TRACKING_VERSION', '1.6.23' ); // WRCS: DEFINED_VERSION.

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
			$this->plugin_file = __FILE__;
			$this->plugin_dir  = untrailingslashit( plugin_dir_path( __FILE__ ) );
			$this->plugin_url  = untrailingslashit( plugin_dir_url( __FILE__ ) );

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
			add_filter( 'manage_shop_order_posts_columns', array( $this->actions, 'shop_order_columns' ), 99 );
			add_action( 'manage_shop_order_posts_custom_column', array( $this->actions, 'render_shop_order_columns' ) );

			// Order page metabox actions.
			add_action( 'wp_ajax_wc_shipment_tracking_delete_item', array( $this->actions, 'meta_box_delete_tracking' ) );
			add_action( 'wp_ajax_wc_shipment_tracking_save_form', array( $this->actions, 'save_meta_box_ajax' ) );
			add_action( 'wp_ajax_wc_shipment_tracking_get_items', array( $this->actions, 'get_meta_box_items_ajax' ) );

			$subs_version = class_exists( 'WC_Subscriptions' ) && ! empty( WC_Subscriptions::$version ) ? WC_Subscriptions::$version : null;

			// Prevent data being copied to subscriptions.
			if ( null !== $subs_version && version_compare( $subs_version, '2.0.0', '>=' ) ) {
				add_filter( 'wcs_renewal_order_meta_query', array( $this->actions, 'woocommerce_subscriptions_renewal_order_meta_query' ), 10, 3 );
			} else {
				add_filter( 'woocommerce_subscriptions_renewal_order_meta_query', array( $this->actions, 'woocommerce_subscriptions_renewal_order_meta_query' ), 10, 3 );
			}

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
			require_once dirname( __FILE__ ) . '/includes/class-wc-shipment-tracking-privacy.php';
		}

		/**
		 * Include required files.
		 *
		 * @since 1.4.0
		 */
		private function includes() {
			require $this->plugin_dir . '/includes/class-wc-shipment-tracking.php';
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
			if ( isset( $this->plugin_path ) ) {
				return $this->plugin_path;
			}

			$this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );

			return $this->plugin_path;
		}
	}
endif;

add_action( 'plugins_loaded', 'woocommerce_shipment_tracking_init' );

/**
 * Initializes the extension.
 *
 * @since 1.6.20
 * @return void
 */
function woocommerce_shipment_tracking_init() {
	load_plugin_textdomain( 'woocommerce-shipment-tracking', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_shipment_tracking_missing_wc_notice' );
		return;
	}

	$GLOBALS['WC_Shipment_Tracking'] = wc_shipment_tracking();
}

/**
 * Returns an instance of WC_Shipment_Tracking.
 *
 * @since 1.6.5
 * @version 1.6.5
 *
 * @return WC_Shipment_Tracking
 */
function wc_shipment_tracking() {
	static $instance;

	if ( ! isset( $instance ) ) {
		$instance = new WC_Shipment_Tracking();
	}

	return $instance;
}

/**
 * Adds a tracking number to an order.
 *
 * @param int         $order_id        The order id of the order you want to
 *                                     attach this tracking number to.
 * @param string      $tracking_number The tracking number.
 * @param string      $provider        The tracking provider. If you use one
 *                                     from `WC_Shipment_Tracking_Actions::get_providers()`,
 *                                     the tracking url will be taken case of.
 * @param int         $date_shipped    The timestamp of the shipped date.
 *                                     This is optional, if not set it will
 *                                     use current time.
 * @param bool|string $custom_url      If you are not using a provder from
 *                                     `WC_Shipment_Tracking_Actions::get_providers()`,
 *                                     you can add a url for tracking here.
 *                                     This is optional.
 */
function wc_st_add_tracking_number( $order_id, $tracking_number, $provider, $date_shipped = null, $custom_url = false ) {
	if ( ! $date_shipped ) {
		$date_shipped = current_time( 'timestamp' );
	}

	$st            = WC_Shipment_Tracking_Actions::get_instance();
	$provider_list = $st->get_providers();
	$custom        = true;

	// Check if a given `$provider` is predefined or custom.
	foreach ( $provider_list as $country ) {
		foreach ( $country as $provider_code => $url ) {
			if ( sanitize_title( $provider ) === sanitize_title( $provider_code ) ) {
				$provider = sanitize_title( $provider_code );
				$custom   = false;
				break;
			}
		}

		if ( ! $custom ) {
			break;
		}
	}

	if ( $custom ) {
		$args = array(
			'tracking_provider'        => '',
			'custom_tracking_provider' => $provider,
			'custom_tracking_link'     => $custom_url,
			'tracking_number'          => $tracking_number,
			'date_shipped'             => date( 'Y-m-d', $date_shipped ),
		);
	} else {
		$args = array(
			'tracking_provider'        => $provider,
			'custom_tracking_provider' => '',
			'custom_tracking_link'     => '',
			'tracking_number'          => $tracking_number,
			'date_shipped'             => date( 'Y-m-d', $date_shipped ),
		);
	}

	$st->add_tracking_item( $order_id, $args );
}

/**
 * Deletes tracking information based on tracking_number relating to an order.
 *
 * @param int    $order_id        Order ID.
 * @param string $tracking_number The tracking number to be deleted.
 * @param string $provider        You can filter the delete by specifying a
 *                                tracking provider. This is optional.
 */
function wc_st_delete_tracking_number( $order_id, $tracking_number, $provider = false ) {
	$st = WC_Shipment_Tracking_Actions::get_instance();

	$tracking_items = $st->get_tracking_items( $order_id );

	if ( count( $tracking_items ) > 0 ) {
		foreach ( $tracking_items as $item ) {
			if ( ! $provider ) {
				if ( $item['tracking_number'] == $tracking_number ) {
					$st->delete_tracking_item( $order_id, $item['tracking_id'] );
					return true;
				}
			} else {
				if ( $item['tracking_number'] === $tracking_number && ( sanitize_title( $provider ) === $item['tracking_provider'] || sanitize_title( $provider ) === $item['custom_tracking_provider'] ) ) {
					$st->delete_tracking_item( $order_id, $item['tracking_id'] );
					return true;
				}
			}
		}
	}
	return false;
}
