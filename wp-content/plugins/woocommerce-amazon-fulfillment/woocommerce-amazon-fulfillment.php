<?php
/**
 * Plugin Name: WooCommerce Amazon Fulfillment
 * Plugin URI: https://neversettle.it
 * Description: Integrates Amazon MCF (Multi-channel Fulfillment) and FBA with WooCommerce.
 * Version: 4.1.7
 * Author: Never Settle
 * Author URI: https://neversettle.it
 * Requires at least: 5.0
 * Tested up to: 6.2
 * WC requires at least: 5.0.0
 * WC tested up to: 7.5.0
 * Woo: 669839:b73d2c19a6ff0f06485e0f11eb4bf922
 *
 * Text Domain: ns-fba-for-woocommerce
 * Domain Path: /lang/
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 * @author Never Settle
 * @since 1.0.0
 * @copyright Copyright (c) 2012-2022, Never Settle (dev@neversettle.it)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Plugin updates.
if ( function_exists( 'woothemes_queue_update' ) ) {
	// IDs provided by Woo.
	woothemes_queue_update( plugin_basename( __FILE__ ), 'b73d2c19a6ff0f06485e0f11eb4bf922', '669839' );
}

// adds the Kint library for pretty debug logging.
// see: http://raveren.github.io/kint/ for more details.
if ( ! class_exists( 'Kint', false ) ) {
	require_once 'vendor/autoload.php';
}

// phpmailer for mail handling - primarily for notifications if shipment fails.
// see: https://github.com/PHPMailer/PHPMailer
// use WP built in version to prevent issues with WP include not checking if it already exists.
if ( file_exists( ABSPATH . 'wp-includes/PHPMailer/PHPMailer.php' ) ) {
	require_once ABSPATH . 'wp-includes/PHPMailer/PHPMailer.php';
	require_once ABSPATH . 'wp-includes/PHPMailer/Exception.php';
} else {
	require_once ABSPATH . 'wp-includes/class-phpmailer.php';
}

// Register our deactivation handler so that we can clear any scheduled syncs.
register_deactivation_hook( __FILE__, array( 'NS_FBA', 'on_deactivation' ) );

/**
 * Check if WooCommerce is active
 */
$wc_active_for_blog    = in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true );
$wc_active_for_network = is_multisite() && in_array( 'woocommerce/woocommerce.php', array_keys( get_site_option( 'active_sitewide_plugins', array() ) ), true );
if ( $wc_active_for_blog || $wc_active_for_network ) {

	if ( ! class_exists( 'NS_FBA' ) ) {

		/**
		 * Main WooCommerce Amazon Fulfillment class.
		 *
		 * @since 1.0.0
		 */
		class NS_FBA {

			/**
			 * Plugin version for use in logging and updates and other such wondrous things.
			 *
			 * @var string $version
			 */
			public $version = '4.1.7';

			/**
			 * The App name, primarily used for Amazon's record keeping as passed in the user_agent for example.
			 *
			 * @var string $app_name
			 */
			public $app_name = 'WooCommerceMCF';

			/**
			 * The options string name for this plugin.
			 *
			 * @var string $options_name
			 */
			private $options_name = 'woocommerce_fba_settings';

			/**
			 * Text domain used for localization.
			 *
			 * @var string $text_domain
			 */
			public $text_domain = 'ns-fba-for-woocommerce';

			/**
			 * WooCommerce Helper Class Instance.
			 *
			 * @var object $wc
			 */
			public $wc;

			/**
			 * Utils Helper Class Instance.
			 *
			 * @var object $utils
			 */
			public $utils;


			/**
			 * File utils Helper Class Instance.
			 *
			 * @var object $file_utils
			 */
			public $file_utils;

			/**
			 * Maintenance Helper Class Instance.
			 *
			 * @var object $maint
			 */
			public $maint;

			/**
			 * Inventory Helper Class Instance.
			 *
			 * @var object $inventory
			 */
			public $inventory;

			/**
			 * WooCommerce Integration Helper Class Instance.
			 *
			 * @var NS_MCF_Fulfillment $fulfill
			 */
			public $fulfill;

			/**
			 * Will store an SP_API class instance.
			 *
			 * @var sp_api $sp_api
			 */
			public $sp_api;

			/**
			 * Our main generic logging object which can handle a variety of logging operations.
			 *
			 * @var NS_MCF_Logs $logger
			 */
			public $logger;

			/**
			 * WooCommerce Integration Helper Class Instance.
			 *
			 * @var WC_Integration_FBA $wc_integration
			 */
			public $wc_integration;

			/**
			 * Stores the options for this plugin.
			 *
			 * @var array $options
			 */
			public $options = array();

			/**
			 * The path to this plugin.
			 *
			 * @var string $plugin_url
			 */
			public $plugin_url = '';

			/**
			 * The path to this plugin.
			 *
			 * @var string $plugin_path
			 */
			public $plugin_path = '';

			/**
			 * The url to logs for this session.
			 *
			 * @var string $log_url
			 */
			public $log_url = '';

			/**
			 * The path to logs for this session.
			 *
			 * @var string $log_path
			 */
			public $log_path = '';

			/**
			 * The url to error logs for this session.
			 *
			 * @var string $err_log_url
			 */
			public $err_log_url = '';

			/**
			 * The path to error logs for this session.
			 *
			 * @var string $err_log_path
			 */
			public $err_log_path = '';

			/**
			 * The URL to debug logs for this session.
			 *
			 * @var string $debug_log_url
			 */
			public $debug_log_url = '';

			/**
			 * The path to debug logs for this session.
			 *
			 * @var string $debug_log_path
			 */
			public $debug_log_path = '';

			/**
			 * The URL to inventory sync logs.
			 *
			 * @var string $inv_log_url
			 */
			public $inv_log_url = '';

			/**
			 * The path to inventory sync logs.
			 *
			 * @var string $inv_log_path
			 */
			public $inv_log_path = '';

			/**
			 * The URL to transaction logs.
			 *
			 * @var string $trans_log_url
			 */
			public $trans_log_url = '';

			/**
			 * The path to transaction logs.
			 *
			 * @var string $trans_log_path
			 */
			public $trans_log_path = '';

			/**
			 * Debug mode status.
			 *
			 * @var bool $is_debug
			 */
			public $is_debug = false;

			/**
			 * Configuration mode status.
			 *
			 * @var bool $is_configured
			 */
			public $is_configured = false;

			/**
			 * The single instance of the class.
			 *
			 * @var object $ns_fba
			 */
			private static $ns_fba = null;

			/**
			 * SINGLETON INSTANCE
			 */
			public static function get_instance() {
				if ( is_null( self::$ns_fba ) ) {
					self::$ns_fba = new self();
				}
				return self::$ns_fba;
			}

			/**
			 * PRIVATE CONSTRUCTOR FOR SINGLETON
			 */
			private function __construct() {
				self::$ns_fba = $this;

				// Language Setup.
				$locale = get_locale();
				$mo     = dirname( __FILE__ ) . '/lang/' . $this->text_domain . '-' . $locale . '.mo';
				load_textdomain( $this->text_domain, $mo );

				// Define plugin constants.
				$this->define_constants();

				// Define paths used.
				$this->init_paths();

				// Load our helper class includes and the Amazon PHP libraries.
				$this->require_all( $this->plugin_path . 'lib', 3 );

				add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
				add_action( 'init', array( $this, 'init_test_upgrade' ) );
			}

			/**
			 * Initiate the classes once the plugins have loaded.
			 */
			public function plugins_loaded() {

				// Instantiate and assign our helper class objects, passing $this in so they have access to properties.
				$this->file_utils = new NS_MCF_File();
				$this->wc         = new NS_MCF_WooCommerce( $this );
				$this->utils      = new NS_MCF_Utils( $this );
				$this->maint      = new NS_MCF_Maintenance( $this );
				$this->inventory  = new NS_MCF_Inventory( $this );
				$this->fulfill    = new NS_MCF_Fulfillment( $this );
				$this->sp_api     = new SP_API( $this );
				$this->logger     = new NS_MCF_Logs( $this );

				// Initialize the options.
				$this->get_options();

				// Set is_debug based on retrieved value from DB.
				// Clean install does not have this set, we need to check.
				$this->is_debug = isset( $this->options['ns_fba_debug_mode'] ) ? $this->utils->isset_on( $this->options['ns_fba_debug_mode'] ) : false;

				// This is an important test to prevent potential error conditions where
				// values aren't available that hooks depend on and therefore get wired prematurely.
				$this->is_configured = $this->utils->is_configured();

				// Plugin init for wiring the WC Integration.
				$this->setup_integration();

				// Add custom amazon fulfillment settings tab under WooCommerce Product Data Tabs.
				add_filter( 'woocommerce_product_data_tabs', array( $this->wc, 'woo_fba_product_tab' ) );
				add_action( 'woocommerce_product_data_panels', array( $this->wc, 'custom_product_fba_panel' ) );

				// Register order status.
				$this->wc->add_custom_order_status();

				// Add custom save handler to our custom settings.
				add_action( 'woocommerce_process_product_meta', array( $this->wc, 'save_custom_settings' ) );
				// Add custom order statuses to normal reporting for woocommerce.
				add_filter( 'woocommerce_reports_order_statuses', array( $this->wc, 'add_custom_status_reporting' ) );

				// Add bulk send functionality.
				add_filter( 'bulk_actions-edit-shop_order', array( $this, 'register_bulk_actions' ) );
				add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'handle_bulk_actions' ), 10, 3 );
				add_action( 'admin_notices', array( $this, 'display_bulk_action_message' ) );
				add_action( 'rest_api_init', array( $this, 'register_sp_api_check_state' ) );

				// Add admin notice for configured status and manual order submission to FBA status.
				add_action( 'admin_notices', array( $this, 'not_configured_notice' ) );
				add_action( 'admin_notices', array( $this->wc, 'order_edit_notice' ) );
				add_action( 'admin_enqueue_scripts', array( $this, 'add_nsfba_scripts_and_styles' ) );

				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( &$this, 'filter_plugin_actions' ) );

				// PLUGIN AUTOMATION AND EVENT HANDLING ACTIONS - ONLY RUN IF PLUGIN IS CONFIGURED.
				if ( $this->is_configured ) {

					// Add scheduled event for inventory sync and fulfillment status sync if either option is selected.
					if ( $this->utils->isset_on( $this->options['ns_fba_clean_logs'] ) ) {
						if ( ! wp_next_scheduled( 'ns_fba_clean_logs_daily' ) ) {
							wp_schedule_event( time(), 'daily', 'ns_fba_clean_logs_daily' );
						}
						// Make sure we only wire the actions to the active syncs.
						if ( $this->utils->isset_on( $this->options['ns_fba_clean_logs'] ) ) {
							add_action( 'ns_fba_clean_logs_daily', array( $this->utils, 'delete_older_logs' ) );
						}
					} else {
						// Clear the event once the option is no longer checked.
						wp_clear_scheduled_hook( 'ns_fba_clean_logs_daily' );
					}

					// Process 'Send to Amazon FBA' order meta box order action.
					add_action( 'woocommerce_order_action_ns_fba_send_to_fulfillment', array( $this->wc, 'process_order_meta_box_actions' ) );

					// Add custom action on payment complete to send order data to fulfillment.
					add_action( 'woocommerce_payment_complete', array( $this, 'create_fulfillment_order' ) );
					add_action( 'woocommerce_payment_complete_order_status_processing', array( $this, 'check_create_fulfillment_order' ) );

					// Process 'Check Amazon Tracking Info' order action.
					// This fragment of code could be encapsulated together with legacy counterpart.
					// To avoid errors we go to keep it separated.
					add_action(
						'woocommerce_order_action_ns_fba_check_tracking',
						function( $order ) {
							if ( empty( get_post_meta( $order->get_id(), '_sent_to_fba', true ) ) ) {
								wp_die( esc_html__( 'This order has not been sent to Amazon for fulfillment.', 'ns-fba-for-woocommerce' ) );
							}
							ob_start();
							$this->fulfill->get_fulfillment_order_shipping_info( $order->get_id() );
							ob_end_clean();
						}
					);

					// Add 'Send to Amazon FBA' order meta box order action.
					add_action( 'woocommerce_order_actions', array( $this->wc, 'add_order_meta_box_actions' ) );
				}

				add_filter( 'woocommerce_shipping_methods', array( $this, 'ns_fba_shipping_methods' ) );
			}

			/**
			 * Initiate the conditions for manually running the plugin upgrade test.
			 */
			public function init_test_upgrade() {
				// Trigger after_upgrade_plugin() to run through testing that if we have the query param test_upgrade.
				// Upgrade sequence can be tested by accessing URL.
				// /wp-admin/admin.php?page=wc-settings&tab=integration&section=fba&test-upgrade=1 .

				// Bail out if the LWA token refresh process has already happened.
				if ( $this->is_configured ) {
					return;
				}

				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( ! empty( $_GET['test-upgrade'] ) ) {
					// We have to add these requires, or it will kick a fatal error.
					require_once ABSPATH . 'wp-admin/includes/file.php';
					require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
					require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
					$temp_upgrader = new WP_Upgrader();
					$temp_options  = array();
					$this->wc_integration->after_upgrade_plugin( $temp_upgrader, $temp_options );
				}
			}

			/**
			 * Set up the paths used in the plugin.
			 *
			 * @return void
			 */
			private function init_paths() {
				// Constants and globals setup.
				$this->plugin_url     = trailingslashit( plugin_dir_url( __FILE__ ) );
				$this->plugin_path    = trailingslashit( plugin_dir_path( __FILE__ ) );
				$date_name            = uniqid();
				$this->log_url        = $this->plugin_url . 'logs/ns-fba-' . $date_name . '-success.html';
				$this->log_path       = $this->plugin_path . 'logs/ns-fba-' . $date_name . '-success.html';
				$this->err_log_url    = $this->plugin_url . 'logs/ns-fba-' . $date_name . '-ERROR.html';
				$this->err_log_path   = $this->plugin_path . 'logs/ns-fba-' . $date_name . '-ERROR.html';
				$this->debug_log_url  = $this->plugin_url . 'logs/ns-fba-' . $date_name . '-DEBUG.html';
				$this->debug_log_path = $this->plugin_path . 'logs/ns-fba-' . $date_name . '-DEBUG.html';
				$this->inv_log_url    = $this->plugin_url . 'logs/ns-fba-inventory-log.html';
				$this->inv_log_path   = $this->plugin_path . 'logs/ns-fba-inventory-log.html';
				$this->trans_log_url  = $this->plugin_url . 'logs/ns-fba-translation-log.csv';
				$this->trans_log_path = $this->plugin_path . 'logs/ns-fba-translation-log.csv';
			}

			/**
			 * Define plugin constants
			 * Global constants used within the plugin if they are not already defined
			 */
			protected function define_constants() {
				$this->define( 'SP_API_DEBUG_MODE', true );
				$this->define( 'SP_API_ID', 'amzn1.sellerapps.app.37bb1030-6d6b-43dd-85e0-4408bf9660f5' );
				$this->define( 'SP_API_CONSENT_URL', 'https://sellercentral.amazon.com/apps/authorize/consent' );
				$this->define( 'SP_API_REDIRECT_URI', 'https://mcf.atouchpoint.com/api/sp-api-oauth' );
				$this->define( 'SP_API_MWS_MIGRATE_URI', 'https://mcf.atouchpoint.com/api/sp-api-oauth-migrate' );
				$this->define( 'LAST_INVENTORY_SYNC_OPT_NAME', 'ns_fba_last_inventory_sync_date' );
				$this->define( 'SP_API_STATE_TRANSIENT', 'ns_fba_sp_api_state' );
				$this->define( 'SP_API_STATE_MIGRATE_TRANSIENT', 'ns_fba_sp_api_state_migrate' );
			}

			/**
			 * Define constant helper if not already set
			 *
			 * @param string      $name The name.
			 * @param string|bool $value The value.
			 */
			private function define( $name, $value ) {
				if ( ! defined( $name ) ) {
					define( $name, $value );
				}
			}

			/**
			 * ************ PLUGIN SETUP AND ADMIN FUNCTIONS **********************************************************************************
			 */

			/**
			 * Scan the path, recursively including all PHP files. This is primarily to include all Amazon lib files
			 *
			 * @param string $dir The directory to scan.
			 * @param int    $depth Optional. Depth to scan. Defaults to 0.
			 */
			protected function require_all( $dir, $depth = 0 ) {
				// Require all php files.
				$scan = glob( "$dir/*" );
				foreach ( $scan as $path ) {
					if ( preg_match( '/\.php$/', $path ) ) {
						require_once $path;
					} elseif ( is_dir( $path ) ) {
						$this->require_all( $path, $depth + 1 );
					}
				}
			}

			/**
			 * Set up the integration.
			 * Register the WooCommerce integration that is used in the settings.
			 */
			public function setup_integration() {
				// Checks if WooCommerce is installed.
				if ( class_exists( 'WC_Integration' ) ) {
					// Include our integration class.
					require_once 'woocommerce-settings-integration.php';

					$this->wc_integration = new WC_Integration_FBA( $this );
					$this->sp_api->init_api( $this->wc_integration->get_SP_API_options() );

					// Register the integration.
					add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );

					// View order page hook to get and display order shipping and tracking data from Amazon.
					// Only activate the hook if the option is turned ON.
					if ( $this->utils->isset_on( $this->options['ns_fba_display_order_tracking'] ) ) {

						add_action( 'woocommerce_view_order', array( $this->fulfill, 'get_fulfillment_order_shipping_info' ) );
					}

					if ( $this->is_configured ) {
						// There is already a check for inventory in the method `sync_inventory`.
						add_action( 'sp_api_sync_inventory', array( $this->fulfill, 'sync_inventory' ) );
						// Add scheduled event for inventory sync and fulfillment status sync if either option is selected.
						if ( $this->utils->isset_on( $this->options['ns_fba_sync_ship_status'] ) ) {
							add_action( 'sp_api_sync_inventory', array( $this->fulfill, 'sync_fulfillment_order_status' ) );
						}
					}
				}
			}

			/**
			 * Not Configured Notice.
			 */
			public function not_configured_notice() {
				$this->is_configured = $this->utils->is_configured();
				if ( ! $this->is_configured ) {
					$url = admin_url( 'admin.php?page=wc-settings&tab=integration&section=fba' );
					?>
					<div class="notice notice-error is-dismissible">
						<p>
							Amazon Fulfillment (MCF) for WooCommerce is not configured properly to communicate with Amazon.
							Please use the new "Login with Amazon" button before July 31, 2022 on the settings screen (link
							below) to connect to and authorize Amazon to integrate with WooCommerce.
							<a href="<?php printf( esc_html( $url ) ); ?>">Authorize and Configure Amazon Fulfillment</a>
						</p>
					</div>
					<?php
				}
			}

			/**
			 * Setup plugin scripts and styles.
			 */
			public function add_nsfba_scripts_and_styles() {
				$screen         = get_current_screen();
				$screen_id      = $screen ? $screen->id : '';
				// Only load on required pages to avoid script conflicts.
				$plugin_screens = array( 'woocommerce_page_wc-settings', 'edit-product', 'edit-shop_order', 'shop_order' );
				if ( ! in_array( $screen_id, $plugin_screens, true ) ) {
					return;
				}
				wp_enqueue_style( 'ns-fba-style', plugins_url( 'css/ns-fba-style.css', __FILE__ ), array(), $this->version );
				wp_enqueue_script( 'ns-fba-script', plugins_url( 'js/ns-fba-script.js', __FILE__ ), array( 'jquery-ui-dialog' ), $this->version, false );
				wp_localize_script(
					'ns-fba-script',
					'ns_fba',
					array(
						'nonce' => wp_create_nonce( 'ns-fba-ajax' ),
					)
				);
			}

			/**
			 * Add a new FBA integration to WooCommerce.
			 *
			 * @param array $integrations  The current WooCommerce integrations.
			 *
			 * @return array
			 */
			public function add_integration( array $integrations ): array {
				$integrations[] = 'WC_Integration_FBA';
				return $integrations;
			}

			/**
			 * Adds the Settings link to the plugin activate/deactivate page.
			 *
			 * @param mixed $links  Plugin Action links.
			 *
			 * @return array
			 */
			public function filter_plugin_actions( $links ): array {
				$settings_link = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=integration&section=fba' ) . '">' . __( 'Settings', 'ns-fba-for-woocommerce' ) . '</a>';
				array_unshift( $links, $settings_link ); // before other links.
				return $links;
			}

			/**
			 * Add new bulk order actions.
			 *
			 * @param array $actions  Existing bulk actions.
			 *
			 * @return array
			 */
			public function register_bulk_actions( array $actions ): array {
				$actions['ns_fba_send'] = __( 'Fulfill with Amazon', 'ns-fba-for-woocommerce' );

				return $actions;
			}

			/**
			 * Receive an order created in WC and creates also the order in Seller account.
			 *
			 * @param   int $order_id  The current order id.
			 */
			public function create_fulfillment_order( int $order_id ) {

				$order = wc_get_order( $order_id );

				/**
				 * `ns_fba_skip_post_fulfillment_order` filter
				 * Allow overriding sending to FBA from an external plugin or script.
				 *
				 * @param bool     $skip Defaults to false to not skip. Set to true to skip.
				 * @param WC_Order $order The order object to be fullfiled.
				 *
				 * @return void
				 */
				if ( apply_filters( 'ns_fba_skip_post_fulfillment_order', false, $order ) ) {
					$order->add_order_note( __( 'Order was not sent to Amazon because it has been overriden by another plugin.', $this->text_domain ) );
					return;
				}

				$response = $this->fulfill->post_fulfillment_order( $order );

				if ( 'yes' !== $this->wc_integration->get_option( 'ns_fba_email_on_error' ) ) {
					return;
				}

				if ( is_wp_error( $response ) ) {
					$mail_body = "An error has occurred creating this order in your seller account in Amazon. \n";
					$mail_body = $mail_body . "The error is: \n" . $response->get_error_message();
					$this->utils->mail_message( $mail_body, 'Error creating Fulfillment order in Amazon' );
				}
			}

			/**
			 * Check for sent order.
			 *
			 * @param int $order_id The current order id.
			 */
			public function check_create_fulfillment_order( $order_id ) {
				$this->fulfill->check_post_fulfillment_order( $order_id );
			}

			/**
			 * Adds new API endpoint to check if the SPA API state is valid
			 */
			public function register_sp_api_check_state() {
				if ( ! class_exists( 'WC_Integration' ) ) {
					return;
				}
				register_rest_route(
					$this->text_domain . '/v1',
					'/is_valid_state',
					array(
						'methods'             => 'GET',
						'callback'            => function ( WP_REST_Request $request ) {
							$state     = $request->get_param( 'state' );
							$transient = get_transient( SP_API_STATE_TRANSIENT );

							return ( ! empty( $transient ) && ! empty( $state ) && $state === $transient );
						},
						'permission_callback' => function () {
							return true;
						},
					)
				);
				register_rest_route(
					$this->text_domain . '/v1',
					'/is_valid_migration_state',
					array(
						'methods'             => 'GET',
						'callback'            => function ( WP_REST_Request $request ) {
							$state     = $request->get_param( 'state' );
							$transient = get_transient( SP_API_STATE_MIGRATE_TRANSIENT );

							return ( ! empty( $transient ) && ! empty( $state ) && $state === $transient );
						},
						'permission_callback' => function () {
							return true;
						},
					)
				);
			}

			/**
			 * Process/handle new bulk order actions
			 *
			 * @param string $redirect The redirect URL.
			 * @param string $action   The action being taken.
			 * @param array  $post_ids The post ids.
			 *
			 * @return string
			 */
			public function handle_bulk_actions( $redirect, $action, $post_ids ) {

				if ( 'ns_fba_send' === $action ) {
					foreach ( $post_ids as $id ) {

						$order = wc_get_order( $id );

						if ( false !== $order ) {
							$this->fulfill->post_fulfillment_order( $order, true );
						}
					}

					return $redirect . '&ns_fba_sent_bulk=' . count( $post_ids );
				}

				return $redirect;
			}

			/**
			 * Display admin notice after using bulk order action for fulfillment submission
			 */
			public function display_bulk_action_message() {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( isset( $_GET['ns_fba_sent_bulk'] ) ) {
					?>
					<div class="updated">
						<p>
							<?php
							// phpcs:ignore WordPress.Security.NonceVerification.Recommended
							$total_bulk_orders = intval( $_GET['ns_fba_sent_bulk'] );
							// translators: The total orders sent.
							printf( esc_html__( '%d order(s) sent to Amazon. Review for confirmation.', 'ns-fba-for-woocommerce' ), esc_attr( $total_bulk_orders ) );
							?>
						</p>
					</div>
					<?php
				}
			}

			/**
			 * Run Deactivation Actions
			 */
			public static function on_deactivation() {
				wp_clear_scheduled_hook( 'ns_fba_inventory_sync' );
				wp_clear_scheduled_hook( 'ns_fba_clean_logs_daily' );
			}

			/**
			 * Retrieves the plugin options from the database.
			 */
			public function get_options() {

				// check if we are upgrading from NS FBA to WC Amazon Fulfillment
				// if so, copy the settings to the new key, make a backup and delete
				// the original so we know it's been updated.
				$old_options = get_option( 'ns_fba_options' );
				if ( $old_options ) {
					update_option( $this->options_name, $old_options );
					update_option( 'ns_fba_options_backup', $old_options );
					delete_option( 'ns_fba_options' );
				}

				// if the options don't already exist.
				$the_options = get_option( $this->options_name );
				if ( ! $the_options ) {
					// set up the default options.
					$the_options = array(
						'ns_fba_version'                => $this->version,
						'ns_fba_service_url'            => 'https://sellingpartnerapi-na.amazon.com',
						'ns_fba_shipping_speed'         => 'Standard',
						'ns_fba_fulfillment_policy'     => 'FillOrKill',
						'ns_fba_order_prefix'           => 'WC-',
						'ns_fba_order_comment'          => 'Thank you for your order!',
						'ns_fba_email_on_error'         => 'yes',
						'ns_fba_manual_item_override'   => 'yes',
						'ns_fba_display_order_tracking' => 'yes',
						'ns_fba_clean_logs'             => 'no',
						'ns_fba_aws_access_key_id_na'   => 'AKIAJWS2EIS3BUSQ2S4A',
						'ns_fba_aws_access_key_id_eu'   => 'AKIAJWTDDIP74M6U6MCQ',
						'ns_fba_aws_access_key_id_fe'   => 'AKIAIMXMPRR23GW2TVJA',
						'ns_fba_aws_access_key_id_cn'   => '',
					);
					update_option( $this->options_name, $the_options );
				}
				// updates legacy checkbox setting format in case this installation is updated from an older version.
				if ( empty( $the_options['ns_fba_version'] ) ) {

					$the_options['ns_fba_update_inventory']               = $this->utils->isset_how( $the_options['ns_fba_update_inventory'] );
					$the_options['ns_fba_update_inventory_selected_only'] = $this->utils->isset_how( $the_options['ns_fba_update_inventory_selected_only'] );
					$the_options['ns_fba_email_on_error']                 = $this->utils->isset_how( $the_options['ns_fba_email_on_error'] );
					$the_options['ns_fba_exclude_phone']                  = $this->utils->isset_how( $the_options['ns_fba_exclude_phone'] );
					$the_options['ns_fba_encode_convert_bypass']          = $this->utils->isset_how( $the_options['ns_fba_encode_convert_bypass'] );
					$the_options['ns_fba_encode_check_override']          = $this->utils->isset_how( $the_options['ns_fba_encode_check_override'] );
					$the_options['ns_fba_automatic_completion']           = $this->utils->isset_how( $the_options['ns_fba_automatic_completion'] );
					$the_options['ns_fba_sync_ship_status']               = $this->utils->isset_how( $the_options['ns_fba_sync_ship_status'] );
					$the_options['ns_fba_disable_shipping_email']         = $this->utils->isset_how( $the_options['ns_fba_disable_shipping_email'] );
					$the_options['ns_fba_display_order_tracking']         = $this->utils->isset_how( $the_options['ns_fba_display_order_tracking'] );
					$the_options['ns_fba_debug_mode']                     = $this->utils->isset_how( $the_options['ns_fba_debug_mode'] );
					$the_options['ns_fba_manual_order_override']          = $this->utils->isset_how( $the_options['ns_fba_manual_order_override'] );
					$the_options['ns_fba_disable_international']          = $this->utils->isset_how( $the_options['ns_fba_disable_international'] );
					$the_options['ns_fba_manual_item_override']           = $this->utils->isset_how( $the_options['ns_fba_manual_item_override'] );
					$the_options['ns_fba_manual_only_mode']               = $this->utils->isset_how( $the_options['ns_fba_manual_only_mode'] );
					$the_options['ns_fba_vacation_mode']                  = $this->utils->isset_how( $the_options['ns_fba_vacation_mode'] );
					$the_options['ns_fba_perfection_mode']                = $this->utils->isset_how( $the_options['ns_fba_perfection_mode'] );
					$the_options['ns_fba_version']                        = $this->version;

					update_option( $this->options_name, $the_options );
				}

				// to test if the legacy update is working you can use this
				// $the_options['ns_fba_version']                    = '';
				// update_option( $this->options_name, $the_options );
				// ----------------------------------------------------------.

				// handle timing issues if this is a postback from the WC Integration settings that won't at this
				// point be reflected in the DB... we mainly need this for the not configured warning and hiding
				// or showing the rest of the options on the settings page right after a save operation.
				// phpcs:disable WordPress.Security.NonceVerification
				if ( isset( $_GET['tab'] ) && 'integration' === $_GET['tab'] && isset( $_POST['save'] ) ) {
					// if this is a save postback then have to assume it's not configured and properly check
					// (override the values in the DB with the values in POST).

					if ( isset( $_POST['woocommerce_fba_ns_fba_aws_access_key_id'] ) ) {
						$the_options['ns_fba_aws_access_key_id'] = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_aws_access_key_id'] ) );
					}

					if ( isset( $_POST['woocommerce_fba_ns_fba_aws_secret_access_key'] ) ) {
						$the_options['ns_fba_aws_secret_access_key'] = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_aws_secret_access_key'] ) );
					}

					if ( isset( $_POST['woocommerce_fba_ns_fba_merchant_id'] ) ) {
						$the_options['ns_fba_merchant_id'] = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_merchant_id'] ) );
					}

					if ( isset( $_POST['woocommerce_fba_ns_fba_marketplace_id'] ) ) {
						$the_options['ns_fba_marketplace_id'] = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_marketplace_id'] ) );
					}
					// removed because as of 3.2.0 we set this automatically
					// $the_options['ns_fba_app_name']	       			= $_POST['woocommerce_fba_ns_fba_app_name'];
					// $the_options['ns_fba_app_name']	       			= 'estore';
					// removed because as of 3.1.4 we now set this automatically based on plugin version
					// $the_options['ns_fba_app_version']              = $_POST['woocommerce_fba_ns_fba_app_version'];
					// $the_options['ns_fba_app_version']              = $this->version;.
				}

				// handle inventory SKU testing in case this is the first time the SKU has been provided it did not save first.
				if ( isset( $_POST['woocommerce_fba_ns_fba_test_inventory_sku'] ) ) {
					$the_options['ns_fba_test_inventory_sku'] = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_test_inventory_sku'] ) );
				}
				// phpcs:enable WordPress.Security.NonceVerification.Missing

				$the_options = get_option( $this->options_name );

				// handle existing installs adding the ns_fba_aws_access_key_id_na option for the first time.
				if ( empty( $the_options['ns_fba_aws_access_key_id_na'] ) ) {
					$the_options ['ns_fba_aws_access_key_id_na'] = 'AKIAJWS2EIS3BUSQ2S4A';
					update_option( $this->options_name, $the_options );
				}

				// handle existing installs adding the ns_fba_aws_access_key_id_eu option for the first time.
				if ( empty( $the_options['ns_fba_aws_access_key_id_eu'] ) ) {
					$the_options ['ns_fba_aws_access_key_id_eu'] = 'AKIAJWTDDIP74M6U6MCQ';
					update_option( $this->options_name, $the_options );
				}

				// handle existing installs adding the ns_fba_aws_access_key_id_fe option for the first time.
				if ( empty( $the_options['ns_fba_aws_access_key_id_fe'] ) ) {
					$the_options ['ns_fba_aws_access_key_id_fe'] = 'AKIAIMXMPRR23GW2TVJA';
					update_option( $this->options_name, $the_options );
				}

				// handle existing installs adding the ns_fba_aws_access_key_id_cn option for the first time.
				if ( empty( $the_options['ns_fba_aws_access_key_id_cn'] ) ) {
					$the_options ['ns_fba_aws_access_key_id_cn'] = '';
					update_option( $this->options_name, $the_options );
				}

				// handle existing installs adding the ns_fba_clean_logs option for the first time.
				if ( empty( $the_options['ns_fba_clean_logs'] ) ) {
					$the_options ['ns_fba_clean_logs'] = 'no';
					update_option( $this->options_name, $the_options );
				}

				// handle existing installs adding the ns_fba_clean_logs_interval option for the first time.
				if ( empty( $the_options['ns_fba_clean_logs_interval'] ) ) {
					$the_options ['ns_fba_clean_logs_interval'] = 30;
					update_option( $this->options_name, $the_options );
				}

				// handle existing installs adding the ns_fba_low_stock_threshold option for the first time.
				if ( empty( $the_options['ns_fba_low_stock_threshold'] ) ) {
					$the_options ['ns_fba_low_stock_threshold'] = '0';
					update_option( $this->options_name, $the_options );
				}

				$this->options = $the_options;

				// there is no return here, because you should use the $this->options variable.
			}

			/**
			 * Test order item fulfil filter.
			 *
			 * @param WC_Order $order The order.
			 *
			 * @return bool
			 */
			public function test_order_item_fulfill_filter( $order ) {
				return false;
			}

			/**
			 * Test order fulfil filter.
			 *
			 * @param WC_Order $order The order.
			 *
			 * @return bool
			 */
			public function test_order_fulfill_filter( $order ) {
				return false;
			}

			/**
			 * Register a new woocommerce shipping method.
			 *
			 * @param array $methods The current shipping methods.
			 *
			 * @return array
			 */
			public function ns_fba_shipping_methods( $methods ) {
				$methods['WC_Shipping_Amazon'] = 'WC_Shipping_Amazon';
				return $methods;

			} // End function add_amazon_shipping_method

		} // End class NS_FBA.

	}

	// instantiate the class.
	$ns_fba_inst = NS_FBA::get_instance();

}
