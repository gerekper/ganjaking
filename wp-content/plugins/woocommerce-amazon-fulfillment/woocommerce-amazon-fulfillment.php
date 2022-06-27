<?php
/**
 * Plugin Name: WooCommerce Amazon Fulfillment
 * Plugin URI: https://neversettle.it
 * Description: Integrates Amazon MCF (Multi-channel Fulfillment) and FBA with WooCommerce.
 * Version: 4.0.7
 * Author: Never Settle
 * Author URI: https://neversettle.it
 * Requires at least: 5.0
 * Tested up to: 6.0
 * WC requires at least: 5.0.0
 * WC tested up to: 6.5.1
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

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Plugin updates.
if ( function_exists( 'woothemes_queue_update' ) ) {
	// Original IDs listed in product details under dev account
	// Woo: 669839:d41d8cd98f00b204e9800998ecf8427e
	// woothemes_queue_update( plugin_basename( __FILE__ ), 'd41d8cd98f00b204e9800998ecf8427e', '669839' );
	// New IDs provided by Woo.
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

if ( ! class_exists( 'WC_Shipping_Amazon' ) ) {
	require_once dirname( __FILE__ ) . '/lib/class-wc-shipping-amazon.php';
}

// register our deactivation handler so that we can clear any scheduled syncs.
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
			 * Transient name to be used to verify th state
			 *
			 * @const SP_API_STATE_COOKIE
			 */
			const SP_API_STATE_TRANSIENT = 'ns_fba_sp_api_state';

			/**
			 * Transient name to be used to verify state of MWS migration
			 *
			 * @const SP_API_STATE_COOKIE
			 */
			const SP_API_STATE_MIGRATE_TRANSIENT = 'ns_fba_sp_api_state_migrate';

			/**
			 * Plugin strings
			 *
			 * @var string $version
			 */
			public $version = '4.0.7';

			/**
			 * The App name.
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
			 * Outbound Helper Class Instance.
			 *
			 * @var object $outbound
			 */
			public $outbound;

			/**
			 * WooCommerce Integration Helper Class Instance.
			 *
			 * @var SP_Fulfillment $fulfill
			 */
			public $fulfill;

			/**
			 * WooCommerce Integration Helper Class Instance.
			 *
			 * @var WC_Integration_FBA $wc_integration
			 */
			public $wc_integration;

			/**
			 * Will store an SP_API class instance
			 *
			 * @var sp_api $sp_api
			 */
			public $sp_api;

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
			 * Plugin State Boolean variables.
			 * Used for skipping hooks that will fail without settings.
			 *
			 * @var bool $is_configured
			 */
			public $is_configured = false;

			/**
			 * Lwa configuration mode status.
			 *
			 * @var bool $is_lwa_configured
			 */
			public $is_lwa_configured = false;

			/**
			 * The single instance of the class.
			 *
			 * @var object $ns_fba
			 */
			private static $ns_fba;

			/**
			 * SINGLETON INSTANCE
			 */
			public static function get_instance() {
				if ( null === self::$ns_fba ) {
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

				// Constants and globals setup.
				$this->plugin_url     = WP_PLUGIN_URL . '/' . dirname( plugin_basename( __FILE__ ) ) . '/';
				$this->plugin_path    = WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) . '/';
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

				// Load our helper class includes and the Amazon PHP libraries.
				$this->require_all( $this->plugin_path . 'lib', 3 );

				// Instantiate and assign our helper class objects, passing $this in so they have access to properties.
				$this->wc        = new NS_FBA_WooCommerce( $this );
				$this->utils     = new NS_FBA_Utils( $this );
				$this->maint     = new NS_FBA_Maintenance( $this );
				$this->inventory = new NS_FBA_Inventory( $this );
				$this->outbound  = new NS_FBA_Outbound( $this );
				$this->fulfill   = new SP_Fulfillment( $this );

				// Create the logs directory if it doesn't exist.
				if ( ! file_exists( $this->plugin_path . 'logs' ) ) {
					mkdir( $this->plugin_path . 'logs' );
				}

				// Initialize the options.
				$this->get_options();

				// Set is_debug based on retrieved value from DB.
				$this->is_debug = $this->utils->isset_on( $this->options['ns_fba_debug_mode'] );

				// This is an important test to prevent potential error conditions where
				// values aren't available that hooks depend on and therefore get wired prematurely.
				$this->is_configured = $this->utils->is_configured();

				// PLUGIN SETUP ACTIONS - ALWAYS RUN.
				// Plugin init for wiring the WC Integration.
				add_action( 'plugins_loaded', array( $this, 'init' ) );

				// Add custom amazon fulfillment settings tab under WooCommerce Product Data Tabs.
				add_filter( 'woocommerce_product_data_tabs', array( $this->wc, 'woo_fba_product_tab' ) );
				add_action( 'woocommerce_product_data_panels', array( $this->wc, 'custom_product_fba_panel' ) );

				// Add custom save handler to our custom settings.
				add_action( 'woocommerce_process_product_meta', array( $this->wc, 'save_custom_settings' ) );
				// Add custom order statuses to normal reporting for woocommerce.
				add_filter( 'woocommerce_reports_order_statuses', array( $this->wc, 'add_custom_status_reporting' ) );
				// Add custom order statuses for fulfillment.
				add_action( 'init', array( $this->wc, 'add_custom_order_status' ) );

				// Add bulk send functionality.
				add_filter( 'bulk_actions-edit-shop_order', array( $this, 'register_bulk_actions' ) );
				add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'handle_bulk_actions' ), 10, 3 );
				add_action( 'admin_notices', array( $this, 'display_bulk_action_message' ) );
				add_action( 'rest_api_init', array( $this, 'register_sp_api_check_state' ) );

				// Add admin notice for configured status and manual order submission to FBA status.
				add_action( 'admin_notices', array( $this, 'not_configured_notice' ) );
				add_action( 'admin_notices', array( $this->wc, 'order_edit_notice' ) );
				add_action( 'admin_enqueue_scripts', array( $this, 'add_nsfba_scripts_and_styles' ) );

				// PLUGIN AUTOMATION AND EVENT HANDLING ACTIONS - ONLY RUN IF PLUGIN IS CONFIGURED.
				if ( $this->is_configured ) {

					$this->is_lwa_configured = $this->utils->is_lwa_configured();

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

					if ( ! $this->is_lwa_configured ) {
						// Add custom action on payment complete to send order data to fulfillment.
						add_action( 'woocommerce_payment_complete', array( $this->outbound, 'maybe_send_fulfillment_order' ) );
						add_action( 'woocommerce_payment_complete_order_status_processing', array( $this->outbound, 'maybe_send_fulfillment_order' ) );

						add_action(
							'woocommerce_order_action_ns_fba_check_tracking',
							function( $order ) {
								if ( empty( get_post_meta( $order->get_id(), '_sent_to_fba', true ) ) ) {
									wp_die( esc_html__( 'This order has not been sent to Amazon for fulfillment.', 'ns-fba-for-woocommerce' ) );
								}
								ob_start();
								$this->outbound->get_fulfillment_order_shipping_info( $order->get_id() );
								ob_end_clean();
							}
						);
					} else {
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
								$this->wc_integration->get_fulfillment_order_shipping_info( $order->get_id() );
								ob_end_clean();
							}
						);
					}

					// Add 'Send to Amazon FBA' order meta box order action.
					add_action( 'woocommerce_order_actions', array( $this->wc, 'add_order_meta_box_actions' ) );
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
			 * Initialize the plugin
			 */
			public function init() {
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( &$this, 'filter_plugin_actions' ) );
				// Checks if WooCommerce is installed.
				if ( class_exists( 'WC_Integration' ) ) {
					// Include our integration class.
					require_once 'woocommerce-settings-integration.php';

					$this->wc_integration = new WC_Integration_FBA( $this );

					$sp_api       = new SP_API( $this->wc_integration->get_SP_API_options() );
					$this->sp_api = $sp_api;

					// Register the integration.
					add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );

					// View order page hook to get and display order shipping and tracking data from Amazon.
					// Only activate the hook if the option is turned ON.
					if ( $this->utils->isset_on( $this->options['ns_fba_display_order_tracking'] ) ) {

						if ( ! $this->is_lwa_configured ) {
							add_action( 'woocommerce_view_order', array( $this->outbound, 'get_fulfillment_order_shipping_info' ) );
						} else {
							add_action( 'woocommerce_view_order', array( $this->fulfill, 'get_fulfillment_order_shipping_info' ) );
						}
					}

					if ( $this->is_configured ) {
						// Add scheduled event for inventory sync and fulfillment status sync if either option is selected.
						if ( $this->utils->isset_on( $this->options['ns_fba_sync_ship_status'] ) ) {

							if ( ! $this->is_lwa_configured ) {
								add_action( 'sp_api_sync_inventory', array( $this->inventory, 'sync_all_inventory' ) );
							} else {
								add_action( 'sp_api_sync_inventory', array( $this->fulfill, 'sync_inventory' ) );
							}

							if ( $this->utils->isset_on( $this->options['ns_fba_sync_ship_status'] ) ) {

								if ( ! $this->is_lwa_configured ) {
									add_action( 'sp_api_sync_inventory', array( $this->outbound, 'sync_fulfillment_order_status' ) );
								} else {
									add_action( 'sp_api_sync_inventory', array( $this->wc_integration, 'sync_fulfillment_order_status' ) );
								}
							}
						}
					}
				}
			}

			/**
			 * Not Configured Notice.
			 */
			public function not_configured_notice() {
				// Checks if saving the options has now left us in an unconfigured state.
				// This is now based on is_lwa_configured so that we get new users and existing users to transition asap.
				$this->is_lwa_configured = $this->utils->is_lwa_configured();
				if ( ! $this->is_lwa_configured ) {
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

				$response = $this->fulfill->post_fulfillment_order( $order );

				// TODO: Consider moving this handler code into the main code of post_fulfillment_order.
				// TODO: Consider refactor to make this entire function unnecessary.
				if ( 'yes' === $this->wc_integration->get_option( 'ns_fba_email_on_error' ) &&
					! $this->sp_api->is_error_in( $response ) ) {
					$body      = json_decode( $response['body'], true );
					$mail_body = "An error has occurred creating this order in your seller account in Amazon. \n";
					$mail_body = $mail_body . "The error is: \n" . $body['errors'][0]['message'];
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
							$transient = get_transient( self::SP_API_STATE_TRANSIENT );

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
							$transient = get_transient( self::SP_API_STATE_MIGRATE_TRANSIENT );

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

						if ( $this->is_lwa_configured ) {
							$order = wc_get_order( $id );

							if ( false !== $order ) {
								$this->fulfill->post_fulfillment_order( $order, true );
							}
						} else {
							$this->outbound->send_fulfillment_order( $id, true );
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

					$the_options['ns_fba_update_inventory']       = $this->utils->isset_how( $the_options['ns_fba_update_inventory'] );
					$the_options['ns_fba_email_on_error']         = $this->utils->isset_how( $the_options['ns_fba_email_on_error'] );
					$the_options['ns_fba_exclude_phone']          = $this->utils->isset_how( $the_options['ns_fba_exclude_phone'] );
					$the_options['ns_fba_encode_convert_bypass']  = $this->utils->isset_how( $the_options['ns_fba_encode_convert_bypass'] );
					$the_options['ns_fba_encode_check_override']  = $this->utils->isset_how( $the_options['ns_fba_encode_check_override'] );
					$the_options['ns_fba_automatic_completion']   = $this->utils->isset_how( $the_options['ns_fba_automatic_completion'] );
					$the_options['ns_fba_sync_ship_status']       = $this->utils->isset_how( $the_options['ns_fba_sync_ship_status'] );
					$the_options['ns_fba_disable_shipping_email'] = $this->utils->isset_how( $the_options['ns_fba_disable_shipping_email'] );
					$the_options['ns_fba_display_order_tracking'] = $this->utils->isset_how( $the_options['ns_fba_display_order_tracking'] );
					$the_options['ns_fba_debug_mode']             = $this->utils->isset_how( $the_options['ns_fba_debug_mode'] );
					$the_options['ns_fba_manual_order_override']  = $this->utils->isset_how( $the_options['ns_fba_manual_order_override'] );
					$the_options['ns_fba_disable_international']  = $this->utils->isset_how( $the_options['ns_fba_disable_international'] );
					$the_options['ns_fba_manual_item_override']   = $this->utils->isset_how( $the_options['ns_fba_manual_item_override'] );
					$the_options['ns_fba_manual_only_mode']       = $this->utils->isset_how( $the_options['ns_fba_manual_only_mode'] );
					$the_options['ns_fba_vacation_mode']          = $this->utils->isset_how( $the_options['ns_fba_vacation_mode'] );
					$the_options['ns_fba_perfection_mode']        = $this->utils->isset_how( $the_options['ns_fba_perfection_mode'] );
					$the_options['ns_fba_version']                = $this->version;

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

		} // End class NS_FBA.

	}

	// instantiate the class.
	$ns_fba_inst = NS_FBA::get_instance();

	/**
	 * Register a new woocommerce shipping method.
	 *
	 * @param array $methods The current shipping methods.
	 *
	 * @return array
	 */
	function ns_fba_shipping_methods( $methods ) {
		$methods['WC_Shipping_Amazon'] = 'WC_Shipping_Amazon';
		return $methods;

	} // End function add_amazon_shipping_method

	add_filter( 'woocommerce_shipping_methods', 'ns_fba_shipping_methods' );

}
