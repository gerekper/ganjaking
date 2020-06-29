<?php
/**
 * Plugin Name: WooCommerce Amazon Fulfillment
 * Plugin URI: https://neversettle.it
 * Description: Integrates Amazon Fulfillment (FBA) with WooCommerce.
 * Version: 3.3.2
 * Author: Never Settle
 * Author URI: https://neversettle.it
 * Requires at least: 4.7
 * Tested up to: 5.3.2
 * WC requires at least: 3.0.0
 * WC tested up to: 4.0.0
 * Woo: 669839:b73d2c19a6ff0f06485e0f11eb4bf922
 *
 * Text Domain: ns-fba-for-woocommerce
 * Domain Path: /lang/
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 * @author Never Settle
 * @since 1.0.0
 * @copyright Copyright (c) 2012-2018, Never Settle (dev@neversettle.it)
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Plugin updates
if ( function_exists( 'woothemes_queue_update' ) ) {
	// Original IDs listed in product details under dev account
	//Woo: 669839:d41d8cd98f00b204e9800998ecf8427e
	//woothemes_queue_update( plugin_basename( __FILE__ ), 'd41d8cd98f00b204e9800998ecf8427e', '669839' );
	// New IDs provided by Woo
	woothemes_queue_update( plugin_basename( __FILE__ ), 'b73d2c19a6ff0f06485e0f11eb4bf922', '669839' );
}

// adds the Kint library for pretty debug logging
// see: http://raveren.github.io/kint/ for more details
if ( ! class_exists( 'Kint', false ) ) {
	require_once 'vendor/autoload.php';
}

// phpmailer for mail handling - primarily for notifications if shipment fails
// see: https://github.com/PHPMailer/PHPMailer
// use WP built in version to prevent issues with WP include not checking if it already exists
require_once( ABSPATH . 'wp-includes/class-phpmailer.php' );

// register our deactivation handler so that we can clear any scheduled syncs
register_deactivation_hook( __FILE__, array( 'NS_FBA', 'on_deactivation' ) );

/**
 * Check if WooCommerce is active
**/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	if ( ! class_exists( 'NS_FBA' ) ) {

		/**
		 * Main WooCommerce Amazon Fulfillment class.
		 *
		 * @since 1.0.0
		 */

		class NS_FBA {
			/**
			 * Plugin strings
			 *
			 * @var string $version
			 * @var string The options string name for this plugin
			 * @var string $text_domain Domain used for localization
			 */
			public $version = '3.3.2';
			public $app_name = 'WooCommerceMCF';
			private $options_name = 'woocommerce_fba_settings';
			public $text_domain = 'ns-fba-for-woocommerce';

			/**
			 * Local Helper Class Instances
			 *
			 * @var object $wc is a local instance of the woocommerce helper class
			 * @var object $utils is a local instance of the utils helper class
			 * @var object $maint is a local instance of the maint helper class
			 * @var object $inventory is a local instance of the inventory helper class
			 * @var object $outbound is a local instance of the outbound helper class
			 *
			 */
			public $wc;
			public $utils;
			public $maint;
			public $inventory;
			public $outbound;

			/**
			 * @var array $options Stores the options for this plugin
			 */
			public $options = array();

			/**
			 * Plugin log file paths and urls
			 *
			 * @var string $plugin_url The path to this plugin
			 * @var string $plugin_path The path to this plugin
			 * @var string $log_url The url to logs for this session
			 * @var string $log_path The path to logs for this session
			 * @var string $err_log_url The url to error logs for this session
			 * @var string $err_log_path The path to error logs for this session
			 * @var string $debug_log_url The URL to debug logs for this session
			 * @var string $debug_log_path The path to debug logs for this session
			 * @var string $inv_log_url The URL to inventory sync logs
			 * @var string $inv_log_path The path to inventory sync logs
			 *
			*/
			public $plugin_url = '';
			public $plugin_path = '';
			public $log_url = '';
			public $log_path = '';
			public $err_log_url = '';
			public $err_log_path = '';
			public $debug_log_url = '';
			public $debug_log_path = '';
			public $inv_log_url = '';
			public $inv_log_path = '';
			public $trans_log_url = '';
			public $trans_log_path = '';

			/**
			 * Plugin State Boolean variables
			 *
			 * @var boolean $is_configured used for skipping hooks that will fail without settings
			 * @var boolean $is_debug debug mode status
			 */
			public $is_configured = false;
			public $is_debug = false;

			// Single
			private static $nsfba;

			/**
			 ************* SINGLETON INSTANCE ******************************************************************************
			 */
			public static function get_instance() {
				if ( null === self::$nsfba ) {
					self::$nsfba = new self();
				}
				return self::$nsfba;
			}
			/**
			 ************* PRIVATE CONSTRUCTOR FOR SINGLETON ***************************************************************
			 */
			private function __construct() {
				self::$nsfba = $this;

				// language Setup
				$locale = get_locale();
				$mo = dirname( __FILE__ ) . '/lang/' . $this->text_domain . '-' . $locale . '.mo';
				load_textdomain( $this->text_domain, $mo );

				// constants and globals setup
				$this->plugin_url = WP_PLUGIN_URL . '/' . dirname( plugin_basename( __FILE__ ) ) . '/';
				$this->plugin_path = WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) . '/';
				$date_name = uniqid();
				$this->log_url = $this->plugin_url . 'logs/ns-fba-' . $date_name . '-success.html';
				$this->log_path = $this->plugin_path . 'logs/ns-fba-' . $date_name . '-success.html';
				$this->err_log_url = $this->plugin_url . 'logs/ns-fba-' . $date_name . '-ERROR.html';
				$this->err_log_path = $this->plugin_path . 'logs/ns-fba-' . $date_name . '-ERROR.html';
				$this->debug_log_url = $this->plugin_url . 'logs/ns-fba-' . $date_name . '-DEBUG.html';
				$this->debug_log_path = $this->plugin_path . 'logs/ns-fba-' . $date_name . '-DEBUG.html';
				$this->inv_log_url = $this->plugin_url . 'logs/ns-fba-inventory-log.html';
				$this->inv_log_path = $this->plugin_path . 'logs/ns-fba-inventory-log.html';
				$this->trans_log_url = $this->plugin_url . 'logs/ns-fba-translation-log.csv';
				$this->trans_log_path = $this->plugin_path . 'logs/ns-fba-translation-log.csv';

				// load our helper class includes and the Amazon PHP libraries
				$this->require_all( $this->plugin_path . 'lib', 3 );

				// instantiate and assign our helper class objects, passing $this in so they have access to properties
				$this->wc = new NS_FBA_WooCommerce( $this );
				$this->utils = new NS_FBA_Utils( $this );
				$this->maint = new NS_FBA_Maintenance( $this );
				$this->inventory = new NS_FBA_Inventory( $this );
				$this->outbound = new NS_FBA_Outbound( $this );

				// create the logs directory if it doesn't exist
				if ( ! file_exists( $this->plugin_path . 'logs' ) ) { mkdir( $this->plugin_path . 'logs' );
				}

				// initialize the options
				$this->get_options();

				// set is_debug based on retrieved value from DB
				$this->is_debug = empty( $this->options['ns_fba_debug_mode'] ) ? false : $this->options['ns_fba_debug_mode'];

				// this is an important test to prevent potential error conditions where
				// values aren't available that hooks depend on and therefore get wired prematurely
				$this->is_configured = $this->utils->is_configured();

				// PLUGIN SETUP ACTIONS - ALWAYS RUN
				// plugin init for wiring the WC Integration
				add_action( 'plugins_loaded', array( $this, 'init' ) );

				// add custom amazon fulfillment settings tab under WooCommerce Product Data Tabs
				add_filter( 'woocommerce_product_data_tabs', array( $this->wc, 'woo_fba_product_tab' ) );
				add_action( 'woocommerce_product_data_panels', array( $this->wc, 'custom_product_fba_panel' ) );

				// add custom save handler to our custom settings
				add_action( 'woocommerce_process_product_meta', array( $this->wc, 'save_custom_settings' ) );
				// add custom order statuses to normal reporting for woocommerce
				add_filter( 'woocommerce_reports_order_statuses', array( $this->wc, 'add_custom_status_reporting' ) );
				// add custom order statuses for fulfillment
				add_action( 'init', array( $this->wc, 'add_custom_order_status' ) );

				// add plugin upgrade notification
				// uncomment this in the future when complete and tested
				//add_action('in_plugin_update_message-woocommerce-amazon-fulfillment/woocommerce-amazon-fulfillment.php', array( $this->maint, 'show_upgrade_notice' ), 10, 2);

				// test filters
				//add_filter( 'ns_fba_is_order_fulfill', array ($this, 'test_order_fulfill_filter' ));
				//add_filter( 'ns_fba_is_order_item_fulfill', array ($this, 'test_order_item_fulfill_filter' ));

				// add admin notice for order edit page if the status is success or fail submission to FBA
				add_action( 'admin_notices', array( $this->wc, 'ns_fba_order_edit_notice' ) );

				add_action( 'admin_enqueue_scripts', array( $this, 'add_nsfba_scripts_and_styles' ) );

				// PLUGIN AUTOMATION AND EVENT HANDLING ACTIONS - ONLY RUN IF PLUGIN IS CONFIGURED
				if ( $this->is_configured ) {
					// add scheduled event for inventory sync and fulfillment status sync if either option is selected
					if ( $this->utils->isset_on( $this->options['ns_fba_sync_inventory'] ) ||
					     $this->utils->isset_on( $this->options['ns_fba_sync_ship_status'] ) ) {
						if ( ! wp_next_scheduled( 'ns_fba_inventory_sync' ) ) {
							wp_schedule_event( time(), 'hourly', 'ns_fba_inventory_sync' );
						}
						// make sure we only wire the actions to the active syncs
						if ( $this->utils->isset_on( $this->options['ns_fba_sync_inventory'] ) ) {
							add_action( 'ns_fba_inventory_sync', array( $this->inventory, 'sync_all_inventory' ) );
						}
						if ( $this->utils->isset_on( $this->options['ns_fba_sync_ship_status'] ) ) {
							add_action( 'ns_fba_inventory_sync', array( $this->outbound, 'sync_fulfillment_order_status' ) );
						}
					} else {
						// clear the event once the option is no longer checked
						wp_clear_scheduled_hook( 'ns_fba_inventory_sync' );
					}
					// add scheduled event for inventory sync and fulfillment status sync if either option is selected
					if ( $this->utils->isset_on( $this->options['ns_fba_clean_logs'] ) ) {
						if ( ! wp_next_scheduled( 'ns_fba_clean_logs_daily' ) ) {
							wp_schedule_event( time(), 'daily', 'ns_fba_clean_logs_daily' );
						}
						// make sure we only wire the actions to the active syncs
						if ( $this->utils->isset_on( $this->options['ns_fba_clean_logs'] ) ) {
							add_action( 'ns_fba_clean_logs_daily', array( $this->utils, 'delete_older_logs' ) );
						}
					} else {
						// clear the event once the option is no longer checked
						wp_clear_scheduled_hook( 'ns_fba_clean_logs_daily' );
					}
					// add custom action on payment complete to send order data to fulfillment
					add_action( 'woocommerce_payment_complete', array( $this->outbound, 'send_fulfillment_order' ) );
					// add 'Send to Amazon FBA' order meta box order action
					add_action( 'woocommerce_order_actions', array( $this->wc, 'add_order_meta_box_actions' ) );
					// process 'Send to Amazon FBA' order meta box order action
					add_action( 'woocommerce_order_action_ns_fba_send_to_fulfillment', array( $this->wc, 'process_order_meta_box_actions' ) );
					// view order page hook to get and display order shipping and tracking data from Amazon
					// only activate the hook if the option is turned ON
					if ( $this->utils->isset_on( $this->options['ns_fba_display_order_tracking'] ) ) {
						add_action( 'woocommerce_view_order', array( $this->outbound, 'get_fulfillment_order_shipping_info' ) );
					}
				}// End if().
			}

			/**
			 ************* PLUGIN SETUP AND ADMIN FUNCTIONS **********************************************************************************
			 */

			/**
			 * Scan the path, recursively including all PHP files. This is primarily to include all Amazon lib files
			 *
			 * @param string  $dir
			 * @param int     $depth (optional)
			 */
			protected function require_all( $dir, $depth = 0 ) {
				// require all php files
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
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( &$this, 'filter_plugin_actions' ), 10, 2 );
				// Checks if WooCommerce is installed.
				if ( class_exists( 'WC_Integration' ) ) {
					// Include our integration class.
					require_once( 'woocommerce-settings-integration.php' );
					// Register the integration.
					add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
				}
			}

			function add_nsfba_scripts_and_styles() {
				wp_enqueue_style( 'ns-fba-style', plugins_url( 'css/ns-fba-style.css', __FILE__ ) );
				wp_enqueue_script( 'ns-fba-script', plugins_url( 'js/ns-fba-script.js', __FILE__) );
				wp_localize_script( 'ns-fba-script', 'ns_fba', [
					'nonce' => wp_create_nonce('ns-fba-ajax'),
				]);
			}

			/**
			 * Add a new FBA integration to WooCommerce
			 */
			public function add_integration( $integrations ) {
				$integrations[] = 'WC_Integration_FBA';
				return $integrations;
			}

			/**
			 * Adds the Settings link to the plugin activate/deactivate page
			 */
			function filter_plugin_actions( $links, $file ) {
				$settings_link = '<a href="' . get_site_url() . '/wp-admin/admin.php?page=wc-settings&tab=integration&section=fba">' . __( 'Settings' ) . '</a>';
				array_unshift( $links, $settings_link ); // before other links
				return $links;
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
			function get_options() {

				// check if we are upgrading from NS FBA to WC Amazon Fulfillment
				// if so, copy the settings to the new key, make a backup and delete
				// the original so we know it's been updated
				if ( $old_options = get_option( 'ns_fba_options' ) ) {
					update_option( $this->options_name, $old_options );
					update_option( 'ns_fba_options_backup', $old_options );
					delete_option( 'ns_fba_options' );
				}

				// if the options don't already exist
				if ( ! $the_options = get_option( $this->options_name ) ) {
					// set up the default options
					$the_options = array(
							'ns_fba_version'				=> $this->version,
							'ns_fba_service_url'        	=> 'https://mws.amazonservices.com',
							'ns_fba_shipping_speed'     	=> 'Standard',
							'ns_fba_fulfillment_policy' 	=> 'FillOrKill',
							'ns_fba_order_prefix'       	=> 'WC-',
							'ns_fba_order_comment'       	=> 'Thank you for your order!',
							'ns_fba_email_on_error'     	=> 'yes',
							'ns_fba_manual_item_override'	=> 'yes',
							'ns_fba_display_order_tracking' => 'yes',
							'ns_fba_clean_logs'		 		=> 'no',
							'ns_fba_aws_access_key_id_na'   => 'AKIAJWS2EIS3BUSQ2S4A',
							'ns_fba_aws_access_key_id_eu'   => 'AKIAJWTDDIP74M6U6MCQ',
							'ns_fba_aws_access_key_id_fe'   => 'AKIAIMXMPRR23GW2TVJA',
							'ns_fba_aws_access_key_id_cn'   => '',
					);
					update_option( $this->options_name, $the_options );
				}
				// updates legacy checkbox setting format in case this installation is updated from an older version
				if ( empty( $the_options['ns_fba_version'] ) ) {
					error_log( 'updating checkbox settings to new format' );

					$the_options['ns_fba_update_inventory'] 		= $this->utils->isset_how( $the_options['ns_fba_update_inventory'] );
					$the_options['ns_fba_sync_inventory'] 			= $this->utils->isset_how( $the_options['ns_fba_sync_inventory'] );
					$the_options['ns_fba_email_on_error'] 			= $this->utils->isset_how( $the_options['ns_fba_email_on_error'] );
					$the_options['ns_fba_exclude_phone'] 			= $this->utils->isset_how( $the_options['ns_fba_exclude_phone'] );
					$the_options['ns_fba_encode_convert_bypass'] 	= $this->utils->isset_how( $the_options['ns_fba_encode_convert_bypass'] );
					$the_options['ns_fba_encode_check_override'] 	= $this->utils->isset_how( $the_options['ns_fba_encode_check_override'] );
					$the_options['ns_fba_automatic_completion'] 	= $this->utils->isset_how( $the_options['ns_fba_automatic_completion'] );
					$the_options['ns_fba_sync_ship_status'] 		= $this->utils->isset_how( $the_options['ns_fba_sync_ship_status'] );
					$the_options['ns_fba_disable_shipping_email'] 	= $this->utils->isset_how( $the_options['ns_fba_disable_shipping_email'] );
					$the_options['ns_fba_display_order_tracking'] 	= $this->utils->isset_how( $the_options['ns_fba_display_order_tracking'] );
					$the_options['ns_fba_debug_mode'] 				= $this->utils->isset_how( $the_options['ns_fba_debug_mode'] );
					$the_options['ns_fba_manual_order_override'] 	= $this->utils->isset_how( $the_options['ns_fba_manual_order_override'] );
					$the_options['ns_fba_disable_international'] 	= $this->utils->isset_how( $the_options['ns_fba_disable_international'] );
					$the_options['ns_fba_manual_item_override'] 	= $this->utils->isset_how( $the_options['ns_fba_manual_item_override'] );
					$the_options['ns_fba_vacation_mode'] 			= $this->utils->isset_how( $the_options['ns_fba_vacation_mode'] );
					$the_options['ns_fba_perfection_mode'] 			= $this->utils->isset_how( $the_options['ns_fba_perfection_mode'] );
					$the_options['ns_fba_version']					= $this->version;

					update_option( $this->options_name, $the_options );
				}

				// to test if the legacy update is working you can use this
				//$the_options['ns_fba_version']					= '';
				//update_option( $this->options_name, $the_options );
				// ----------------------------------------------------------

				// handle timing issues if this is a postback from the WC Integration settings that won't at this
				// point be reflected in the DB... we mainly need this for the not configured warning and hiding
				// or showing the rest of the options on the settings page right after a save operation
				if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'integration' && isset( $_POST['save'] ) ) {
					// if this is a save postback then have to assume it's not configured and properly check
					// (override the values in the DB with the values in POST)
					
					if ( isset( $_POST['woocommerce_fba_ns_fba_aws_access_key_id'] ) ) {
						$the_options['ns_fba_aws_access_key_id'] = $_POST['woocommerce_fba_ns_fba_aws_access_key_id'];
					}
					if ( isset( $_POST['woocommerce_fba_ns_fba_aws_secret_access_key'] ) ) {
						$the_options['ns_fba_aws_secret_access_key'] = $_POST['woocommerce_fba_ns_fba_aws_secret_access_key'];
					}
					$the_options['ns_fba_merchant_id']              = $_POST['woocommerce_fba_ns_fba_merchant_id'];
					$the_options['ns_fba_marketplace_id']           = $_POST['woocommerce_fba_ns_fba_marketplace_id'];
					// removed because as of 3.2.0 we set this automatically
					// $the_options['ns_fba_app_name']	       			= $_POST['woocommerce_fba_ns_fba_app_name'];
					// $the_options['ns_fba_app_name']	       			= 'estore';
					// removed because as of 3.1.4 we now set this automatically based on plugin version
					// $the_options['ns_fba_app_version']              = $_POST['woocommerce_fba_ns_fba_app_version'];
					// $the_options['ns_fba_app_version']              = $this->version;
				}

				// handle inventory SKU testing in case this is the first time the SKU has been provided it did not save first
				if ( isset( $_POST['ns_fba_test_inventory'] ) ) {
					//error_log ( 'sku in get_options = ' . $_POST['woocommerce_fba_ns_fba_test_inventory_sku'] );
					$the_options['ns_fba_test_inventory_sku']       = $_POST['woocommerce_fba_ns_fba_test_inventory_sku'];
				}
				
				$the_options = get_option( $this->options_name );
				
				// handle existing installs adding the ns_fba_aws_access_key_id_na option for the first time
				if ( empty( $the_options['ns_fba_aws_access_key_id_na'] ) ) {
					$the_options ['ns_fba_aws_access_key_id_na'] = 'AKIAJWS2EIS3BUSQ2S4A';
					update_option( $this->options_name, $the_options );
				}
				
				// handle existing installs adding the ns_fba_aws_access_key_id_eu option for the first time
				if ( empty( $the_options['ns_fba_aws_access_key_id_eu'] ) ) {
					$the_options ['ns_fba_aws_access_key_id_eu'] = 'AKIAJWTDDIP74M6U6MCQ';
					update_option( $this->options_name, $the_options );
				}
				
				// handle existing installs adding the ns_fba_aws_access_key_id_fe option for the first time
				if ( empty( $the_options['ns_fba_aws_access_key_id_fe'] ) ) {
					$the_options ['ns_fba_aws_access_key_id_fe'] = 'AKIAIMXMPRR23GW2TVJA';
					update_option( $this->options_name, $the_options );
				}
				
				// handle existing installs adding the ns_fba_aws_access_key_id_cn option for the first time
				if ( empty( $the_options['ns_fba_aws_access_key_id_cn'] ) ) {
					$the_options ['ns_fba_aws_access_key_id_cn'] = '';
					update_option( $this->options_name, $the_options );
				}
				
				// handle existing installs adding the ns_fba_clean_logs option for the first time
				if ( empty( $the_options['ns_fba_clean_logs'] ) ) {
					$the_options ['ns_fba_clean_logs'] = 'no';
					update_option( $this->options_name, $the_options );
				}
				
				// handle existing installs adding the ns_fba_low_stock_threshold option for the first time
				if ( empty( $the_options['ns_fba_low_stock_threshold'] ) ) {
					$the_options ['ns_fba_low_stock_threshold'] = '0';
					update_option( $this->options_name, $the_options );
				}
				
				$this->options = $the_options;

				// there is no return here, because you should use the $this->options variable
			}

			function test_order_item_fulfill_filter( $order ) {
				return false;
			}

			function test_order_fulfill_filter( $order ) {
				return false;
			}

		} // end class NS_FBA

	} // End if().

	// instantiate the class
	$ns_fba_inst = NS_FBA::get_instance();

} // End if().
