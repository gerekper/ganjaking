<?php
/**
 * Implementation of WC Integration class for Settings
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 * @since 3.0.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Integration_FBA' ) ) {

	/**
	 * WooCommerce integration class.
	 */
	class WC_Integration_FBA extends WC_Integration {

		/**
		 * Will store an NS_FBA class instance (initialized in __construct)
		 *
		 * @var NS_FBA $ns_fba
		 */
		private $ns_fba;

		/**
		 * Buttons with defaults values (initialized in __construct)
		 *
		 * @var array $button_defaults
		 */
		private $button_defaults;

		/**
		 * App text domain (initialized in __construct)
		 *
		 * @var string $text_domain
		 */
		public $text_domain;

		/**
		 * Will store the LWA flag status (initialized in __construct)
		 *
		 * @var bool $is_configured
		 */
		private $is_configured;

		/**
		 * Will store self-explanatory based on property name
		 *
		 * @var string
		 */
		private $ns_fba_lwa_auth_refresh_token;
		private $ns_fba_aws_access_key_id_na;        // phpcs:ignore
		private $ns_fba_aws_access_key_id_eu;        // phpcs:ignore
		private $ns_fba_aws_access_key_id_fe;        // phpcs:ignore
		private $ns_fba_aws_access_key_id_cn;        // phpcs:ignore
		private $ns_fba_service_url;                 // phpcs:ignore
		private $ns_fba_aws_access_key_id;           // phpcs:ignore
		private $ns_fba_aws_secret_access_key;       // phpcs:ignore
		private $ns_fba_merchant_id;                 // phpcs:ignore
		private $ns_fba_marketplace_id;              // phpcs:ignore
		private $ns_fba_mws_auth_token;              // phpcs:ignore
		private $ns_fba_update_inventory;            // phpcs:ignore
		private $ns_fba_low_stock_threshold;         // phpcs:ignore
		private $ns_fba_test_inventory_sku;          // phpcs:ignore
		private $ns_fba_order_prefix;                // phpcs:ignore
		private $ns_fba_order_comment;               // phpcs:ignore
		private $ns_fba_shipping_speed;              // phpcs:ignore
		private $ns_fba_shipping_speed_standard;     // phpcs:ignore
		private $ns_fba_shipping_speed_expedited;    // phpcs:ignore
		private $ns_fba_shipping_speed_priority;     // phpcs:ignore
		private $ns_fba_fulfillment_policy;          // phpcs:ignore
		private $ns_fba_notify_email;                // phpcs:ignore
		private $ns_fba_email_on_error;              // phpcs:ignore
		private $ns_fba_exclude_phone;               // phpcs:ignore
		private $ns_fba_encode_convert_bypass;       // phpcs:ignore
		private $ns_fba_encode_check_override;       // phpcs:ignore
		private $ns_fba_automatic_completion;        // phpcs:ignore
		private $ns_fba_sync_ship_status;            // phpcs:ignore
		private $ns_fba_sync_ship_retry;             // phpcs:ignore
		private $ns_fba_disable_shipping_email;      // phpcs:ignore
		private $ns_fba_display_order_tracking;      // phpcs:ignore
		private $ns_fba_debug_mode;                  // phpcs:ignore
		private $ns_fba_clean_logs;                  // phpcs:ignore
		private $ns_fba_clean_interval;              // phpcs:ignore
		private $ns_fba_manual_order_override;       // phpcs:ignore
		private $ns_fba_disable_international;       // phpcs:ignore
		private $ns_fba_manual_item_override;        // phpcs:ignore
		private $ns_fba_manual_only_mode;            // phpcs:ignore
		private $ns_fba_vacation_mode;               // phpcs:ignore
		private $ns_fba_perfection_mode;             // phpcs:ignore
		private $ns_fba_quantity_max_filter;         // phpcs:ignore
		private $ns_fba_currency_code;               // phpcs:ignore
		private $ns_fba_currency_conversion;         // phpcs:ignore

		// We can't set type for $ns_fba_disable_shipping because it can be string or array based on settings.
		// TODO: We should maybe split this setting into 2 separate ones or phase the setting out.
		private $ns_fba_disable_shipping;                   // phpcs:ignore

		/**
		 * This array defines the table header and the fields that must be shown in WC Products table.
		 *
		 * @var array $wc_data_header
		 */
		private $wc_data_header = array(
			'sku'            => 'SKU',
			'name'           => 'Name',
			'status'         => 'Status',
			'stock_quantity' => 'Qty',
			'stock_status'   => 'Stock Status',
			'date_modified'  => 'Updated',
		);

		/**
		 * This array defines the table header and the fields that must be shown in Seller Partner Products table.
		 *
		 * @var array $sp_data_header
		 */
		private $sp_data_header = array(
			'sellerSku'       => 'Seller SKU',
			'productName'     => 'Name',
			'condition'       => 'Condition',
			'totalQuantity'   => 'Qty',
			'lastUpdatedTime' => 'Updated',
			'fnSku'           => 'fnSKU',
			'asin'            => 'Asin',
		);

		/**
		 * The developer ids.
		 *
		 * TODO: 4.1.0 We shouldn't need these any more as of 4.1.0 since SP API apps include all dev id's in 1 app ID.
		 *
		 * @var array $developer_ids
		 */
		private $developer_ids = array(
			'https://sellingpartnerapi-na.amazon.com' => 446453545547,
			'https://sellingpartnerapi-eu.amazon.com' => 309795410647,
			'https://sellingpartnerapi-fe.amazon.com' => 733779550746,
		);

		/**
		 * The single instance of the class.
		 *
		 * @var WC_Integration_FBA $wc_integration
		 */
		private static $wc_integration;

		/**
		 * SINGLETON INSTANCE
		 */
		public static function get_instance(): WC_Integration_FBA {
			if ( null === self::$wc_integration ) {
				self::$wc_integration = new self();
			}
			return self::$wc_integration;
		}

		/**
		 * Init and hook in the integration.
		 */
		public function __construct() {
			global $woocommerce;

			self::$wc_integration = $this;

			// local reference to the singleton nsfba object.
			$this->ns_fba        = NS_FBA::get_instance();
			$this->text_domain   = $this->ns_fba->text_domain;
			$this->is_configured = $this->ns_fba->utils->is_configured();

			if ( $this->is_configured ) {
				$this->ns_fba_lwa_auth_refresh_token = get_option( 'ns_fba_lwa_auth_refresh_token' );
			}

			// defaults for settings parameters.
			$this->button_defaults = array(
				'class'             => 'button-secondary',
				'css'               => '',
				'custom_attributes' => array(),
				'desc_tip'          => false,
				'description'       => '',
				'title'             => '',
			);

			// normal integration properties.
			$this->id           = 'fba';
			$this->method_title = __( 'Amazon Multi-Channel Fulfillment (MCF)', $this->text_domain );

			// Load the settings.
			$this->init_settings();
			$this->init_check_tokens();
			$this->init_form_fields();

			// This is a hidden setting for the new authentication model
			// This is Never Settle's MWS Access ID per region
			// It is required in combination with the Seller's Auth Token
			// These are NOT sensitive and are NOT the Secret Keys.
			$this->ns_fba_aws_access_key_id_na = 'AKIAJWS2EIS3BUSQ2S4A';
			$this->ns_fba_aws_access_key_id_eu = 'AKIAJWTDDIP74M6U6MCQ';
			$this->ns_fba_aws_access_key_id_fe = 'AKIAIMXMPRR23GW2TVJA';
			$this->ns_fba_aws_access_key_id_cn = '';

			// Define user set variables
			// If a setting changes between postbacks we have to load the setting from $_POST rather than from the
			// object options which will not have been updated yet by the time the settings are initializing here.

			// SECTION Amazon Account and MWS Settings.
			// phpcs:disable WordPress.Security.NonceVerification
			if ( ! empty( $_POST['woocommerce_fba_ns_fba_service_url'] ) ) {
				$this->ns_fba->options['ns_fba_service_url'] = esc_url_raw( wp_unslash( $_POST['woocommerce_fba_ns_fba_service_url'] ) );
				$this->ns_fba_service_url                    = esc_url_raw( wp_unslash( $_POST['woocommerce_fba_ns_fba_service_url'] ) );
			} else {
				$this->ns_fba_service_url = $this->get_option( 'ns_fba_service_url' );
			}
			if ( ! empty( $_POST['woocommerce_fba_ns_fba_aws_access_key_id'] ) ) {
				$this->ns_fba->options['ns_fba_aws_access_key_id'] = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_aws_access_key_id'] ) );
				$this->ns_fba_aws_access_key_id                    = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_aws_access_key_id'] ) );
			} else {
				$this->ns_fba_aws_access_key_id = $this->get_option( 'ns_fba_aws_access_key_id' );
			}
			if ( ! empty( $_POST['woocommerce_fba_ns_fba_aws_secret_access_key'] ) ) {
				$this->ns_fba->options['ns_fba_aws_secret_access_key'] = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_aws_secret_access_key'] ) );
				$this->ns_fba_aws_secret_access_key                    = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_aws_secret_access_key'] ) );
			} else {
				$this->ns_fba_aws_secret_access_key = $this->get_option( 'ns_fba_aws_secret_access_key' );
			}
			if ( ! empty( $_POST['woocommerce_fba_ns_fba_merchant_id'] ) ) {
				$this->ns_fba->options['ns_fba_merchant_id'] = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_merchant_id'] ) );
				$this->ns_fba_merchant_id                    = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_merchant_id'] ) );
			} else {
				$this->ns_fba_merchant_id = $this->get_option( 'ns_fba_merchant_id' );
			}
			if ( ! empty( $_POST['woocommerce_fba_ns_fba_marketplace_id'] ) ) {
				$this->ns_fba->options['ns_fba_marketplace_id'] = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_marketplace_id'] ) );
				$this->ns_fba_marketplace_id                    = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_marketplace_id'] ) );
			} else {
				$this->ns_fba_marketplace_id = $this->get_option( 'ns_fba_marketplace_id' );
			}
			if ( ! empty( $_POST['woocommerce_fba_ns_fba_mws_auth_token'] ) ) {
				$this->ns_fba->options['ns_fba_mws_auth_token'] = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_mws_auth_token'] ) );
				$this->ns_fba_mws_auth_token                    = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_mws_auth_token'] ) );
			} else {
				$this->ns_fba_mws_auth_token = $this->get_option( 'ns_fba_mws_auth_token' );
			}

			// SECTION FBA Inventory (Stock Level) Settings.
			if ( ! empty( $_POST['woocommerce_fba_ns_fba_update_inventory'] ) ) {
				$this->ns_fba->options['ns_fba_update_inventory'] = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_update_inventory'] ) );
				$this->ns_fba_update_inventory                    = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_update_inventory'] ) );
			} else {
				$this->ns_fba_update_inventory = $this->get_option( 'ns_fba_update_inventory' );
			}

			if ( ! empty( $_POST['woocommerce_fba_ns_fba_low_stock_threshold'] ) ) {
				$this->ns_fba->options['ns_fba_low_stock_threshold'] = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_low_stock_threshold'] ) );
				$this->ns_fba_low_stock_threshold                    = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_low_stock_threshold'] ) );
			} else {
				$this->ns_fba_low_stock_threshold = $this->get_option( 'ns_fba_low_stock_threshold' );
			}
			if ( ! empty( $_POST['woocommerce_fba_ns_fba_test_inventory_sku'] ) ) {
				$this->ns_fba->options['ns_fba_test_inventory_sku'] = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_test_inventory_sku'] ) );
				$this->ns_fba_test_inventory_sku                    = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_test_inventory_sku'] ) );
			} else {
				$this->ns_fba_test_inventory_sku = $this->get_option( 'ns_fba_test_inventory_sku' );
			}

			// SECTION FBA Order Fulfillment Settings.
			if ( ! empty( $_POST['woocommerce_fba_ns_fba_order_prefix'] ) ) {
				$this->ns_fba->options['ns_fba_order_prefix'] = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_order_prefix'] ) );
				$this->ns_fba_order_prefix                    = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_order_prefix'] ) );
			} else {
				$this->ns_fba_order_prefix = $this->get_option( 'ns_fba_order_prefix' );
			}
			if ( ! empty( $_POST['woocommerce_fba_ns_fba_order_comment'] ) ) {
				$this->ns_fba->options['ns_fba_order_comment'] = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_order_comment'] ) );
				$this->ns_fba_order_comment                    = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_order_comment'] ) );
			} else {
				$this->ns_fba_order_comment = $this->get_option( 'ns_fba_order_comment' );
			}
			if ( ! empty( $_POST['woocommerce_fba_ns_fba_shipping_speed'] ) ) {
				$this->ns_fba->options['ns_fba_shipping_speed'] = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_shipping_speed'] ) );
				$this->ns_fba_shipping_speed                    = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_shipping_speed'] ) );
			} else {
				$this->ns_fba_shipping_speed = $this->get_option( 'ns_fba_shipping_speed' );
			}
			if ( ! empty( $_POST['woocommerce_fba_ns_fba_shipping_speed_standard'] ) ) {
				$this->ns_fba->options['ns_fba_shipping_speed_standard'] = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_shipping_speed_standard'] ) );
				$this->ns_fba_shipping_speed_standard                    = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_shipping_speed_standard'] ) );
			} else {
				$this->ns_fba_shipping_speed_standard = $this->get_option( 'ns_fba_shipping_speed_standard' );
			}
			if ( ! empty( $_POST['woocommerce_fba_ns_fba_shipping_speed_expedited'] ) ) {
				$this->ns_fba->options['ns_fba_shipping_speed_expedited'] = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_shipping_speed_expedited'] ) );
				$this->ns_fba_shipping_speed_expedited                    = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_shipping_speed_expedited'] ) );
			} else {
				$this->ns_fba_shipping_speed_expedited = $this->get_option( 'ns_fba_shipping_speed_expedited' );
			}
			if ( ! empty( $_POST['woocommerce_fba_ns_fba_shipping_speed_priority'] ) ) {
				$this->ns_fba->options['ns_fba_shipping_speed_priority'] = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_shipping_speed_priority'] ) );
				$this->ns_fba_shipping_speed_priority                    = sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_shipping_speed_priority'] ) );
			} else {
				$this->ns_fba_shipping_speed_priority = $this->get_option( 'ns_fba_shipping_speed_priority' );
			}
			// phpcs:ignore
			$this->ns_fba_fulfillment_policy = $this->get_option( 'ns_fba_fulfillment_policy' );

			// SECTION General Plugin Settings.
			$this->ns_fba_notify_email           = $this->get_option( 'ns_fba_notify_email' );
			$this->ns_fba_email_on_error         = $this->get_option( 'ns_fba_email_on_error' );
			$this->ns_fba_exclude_phone          = $this->get_option( 'ns_fba_exclude_phone' );
			$this->ns_fba_encode_convert_bypass  = $this->get_option( 'ns_fba_encode_convert_bypass' );
			$this->ns_fba_encode_check_override  = $this->get_option( 'ns_fba_encode_check_override' );
			$this->ns_fba_automatic_completion   = $this->get_option( 'ns_fba_automatic_completion' );
			$this->ns_fba_sync_ship_status       = $this->get_option( 'ns_fba_sync_ship_status' );
			$this->ns_fba_sync_ship_retry        = $this->get_option( 'ns_fba_sync_ship_retry' );
			$this->ns_fba_disable_shipping_email = $this->get_option( 'ns_fba_disable_shipping_email' );
			$this->ns_fba_display_order_tracking = $this->get_option( 'ns_fba_display_order_tracking' );
			$this->ns_fba_debug_mode             = $this->get_option( 'ns_fba_debug_mode' );
			$this->ns_fba_clean_logs             = $this->get_option( 'ns_fba_clean_logs' );
			$this->ns_fba_clean_interval         = $this->get_option( 'ns_fba_clean_logs_interval' );

			// SECTION Smart Fulfillment Settings.
			$this->ns_fba_manual_order_override = $this->get_option( 'ns_fba_manual_order_override' );
			$this->ns_fba_disable_international = $this->get_option( 'ns_fba_disable_international' );

			// for the custom controls we have to look for their new values in POST since they haven't been saved yet.
			if ( ! empty( $_POST['woocommerce_fba_ns_fba_disable_shipping'] ) ) {
				// This can be string or array so do NOT sanitize or unslash !!!
				$this->ns_fba->options['ns_fba_disable_shipping'] = $_POST['woocommerce_fba_ns_fba_disable_shipping']; // phpcs:ignore
				$this->ns_fba_disable_shipping                    = $_POST['woocommerce_fba_ns_fba_disable_shipping']; // phpcs:ignore
			} else {
				$this->ns_fba_disable_shipping = $this->get_option( 'ns_fba_disable_shipping' );
			}

			// phpcs:enable WordPress.Security.NonceVerification.Missing
			$this->ns_fba_manual_item_override = $this->get_option( 'ns_fba_manual_item_override' );
			$this->ns_fba_manual_only_mode     = $this->get_option( 'ns_fba_manual_only_mode' );
			$this->ns_fba_vacation_mode        = $this->get_option( 'ns_fba_vacation_mode' );
			$this->ns_fba_perfection_mode      = $this->get_option( 'ns_fba_perfection_mode' );
			$this->ns_fba_quantity_max_filter  = $this->get_option( 'ns_fba_quantity_max_filter' );

			// SECTION Configuration for Multiple Currencies.
			$this->ns_fba_currency_code       = $this->get_option( 'ns_fba_currency_code' );
			$this->ns_fba_currency_conversion = $this->get_option( 'ns_fba_currency_conversion' );

			// Actions.
			add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'init', array( $this, 'update_inventory_sync_schedule' ) );

			// AJAX Actions.
			add_action( 'wp_ajax_ns_fba_refresh_marketplace_options', array( $this, 'refresh_marketplace_options' ) );

			// Run the manual full inventory sync if the button was clicked.
			add_action( 'wp_ajax_ns_fba_sync_inventory_manually', array( $this, 'handle_manual_sync' ) );
			// Run the manual log deletion if that button was clicked.
			add_action( 'wp_ajax_ns_fba_clean_logs_now', array( $this, 'delete_logs_clicked' ) );
			// Disconnect Amazon.
			add_action( 'wp_ajax_ns_fba_sp_api_disconnect_amazon', array( $this, 'sp_api_disconnect_amazon' ) );

			// Manual inventory syncing.
			add_action( 'wp_ajax_ns_fba_sp_api_sync_inventory', array( $this, 'sp_api_sync_inventory_ajax' ) );

			// Run the inventory api test if this is a test request and they are configured.
			add_action( 'wp_ajax_ns_fba_test_inventory', array( $this, 'inventory_test_results' ) );

			if ( ! $this->is_configured ) {
				// After upgrade plugin will try to migrate to LWA.
				add_action( 'upgrader_process_complete', array( $this, 'after_upgrade_plugin' ), 10, 2 );
			} else {
				// Run the connection api test if this is a test request and they are configured.
				add_action( 'wp_ajax_ns_fba_test_api', array( $this, 'api_test_results' ) );
				// Check for more SKUs from SP API if button is clicked.
				add_action( 'wp_ajax_ns_fba_sp_api_check_skus', array( $this, 'sp_api_check_skus' ) );
				// Import seller partner SKUs.
				add_action( 'wp_ajax_import_seller_partner_sku', array( $this, 'import_seller_partner_sku' ) );
				// Toggle NS FBA Fulfill.
				add_action( 'wp_ajax_toggle_ns_fba_fulfill', array( $this, 'toggle_ns_fba_fulfill' ) );
				// Scheduled inventory syncing.
				add_action( 'sp_api_sync_inventory', array( $this->ns_fba->fulfill, 'sync_inventory' ) );
			}
		}

		/**
		 * Update the inventory Sync Schedule. We have to do this in a hook on init because while the WC Integration
		 * Settings are still processing is too soon and wpbd->actionscheduler_actions table doesn't resolve yet which
		 * makes as_unschedule_all_actions() fail and potentially other bad things happen. This SHOULD be after all
		 * settings are stored from any postbacks.
		 */
		public function update_inventory_sync_schedule() {
			// phpcs:disable WordPress.Security.NonceVerification

			// First, check whether or not the new ($_POST) sync schedule settings are a valid, active schedule.
			// This also makes sure we ONLY make changes to the schedule when our MCF settings change.
			if ( (
				! empty( $_POST['woocommerce_fba_ns_fba_sp_api_sync_inventory_interval_enabled'] )
				|| ! empty( $_POST['woocommerce_fba_ns_fba_sync_ship_status'] )
			) &&
			! empty( $_POST['woocommerce_fba_ns_fba_sp_api_sync_inventory_interval'] )
			) {
				$sync_status_old      = $this->ns_fba->utils->isset_on( $this->get_option( 'ns_fba_sp_api_sync_inventory_interval_enabled' ) );
				$sync_ship_status_old = $this->ns_fba->utils->isset_on( $this->get_option( 'ns_fba_sync_ship_status' ) );
				$sync_value_old       = (int) $this->get_option( 'ns_fba_sp_api_sync_inventory_interval' );
				$sync_value_new       = (int) sanitize_text_field( wp_unslash( $_POST['woocommerce_fba_ns_fba_sp_api_sync_inventory_interval'] ) );
				// We only need to change something if the old and new values are different OR...
				// If it is getting turned back ON after being OFF.
				if ( $sync_value_old !== $sync_value_new || false === $sync_status_old || false === $sync_ship_status_old ) {
					as_unschedule_all_actions( 'sp_api_sync_inventory' );
					$interval = $sync_value_new * 60;
					$start    = time() + $interval;
					as_schedule_recurring_action( $start, $interval, 'sp_api_sync_inventory' );
				}
			} else {
				// Clear any schedules that might have previously been configured.
				// But ONLY if we're still coming from the Settings Page postback which will always have service url.
				if ( ! empty( $_POST['woocommerce_fba_ns_fba_service_url'] ) && function_exists( 'as_unschedule_all_actions' ) ) {
					as_unschedule_all_actions( 'sp_api_sync_inventory' );
				}

				// Set up the cron schedules if its missing.
				$this->init_scheduled_actions();
			}
			// phpcs:enable WordPress.Security.NonceVerification.Missing
		}

		/**
		 * Schedule background actions.
		 * This is used to check and set the background actions.
		 * Most times there is an issue where an action runs once and is not scheduled again.
		 *
		 * @return void
		 */
		public function init_scheduled_actions() {

			$sync_status      = $this->ns_fba->utils->isset_on( $this->get_option( 'ns_fba_sp_api_sync_inventory_interval_enabled' ) );
			$sync_ship_status = $this->ns_fba->utils->isset_on( $this->get_option( 'ns_fba_sync_ship_status' ) );
			$sync_value       = (int) $this->get_option( 'ns_fba_sp_api_sync_inventory_interval' );

			// Check if WooCommerce is loaded fully to prevent throwing errors.
			if ( ! function_exists( 'as_has_scheduled_action' ) || ! function_exists( 'as_schedule_recurring_action' ) ) {
				return;
			}

			$action_scheduled = as_has_scheduled_action( 'sp_api_sync_inventory' );

			if ( ( ! $sync_status && ! $sync_ship_status ) || ! $this->is_configured ) {
				if ( true === $action_scheduled ) {
					as_unschedule_all_actions( 'sp_api_sync_inventory' );
				}
				return;
			}

			if ( false === $action_scheduled ) {
				$interval = $sync_value * 60;
				$start    = time() + $interval;
				as_schedule_recurring_action( $start, $interval, 'sp_api_sync_inventory' );
			}
		}

		/**
		 * Get the option for SP API instance configuration.
		 *
		 * @return array
		 */
		public function get_SP_API_options(): array {
			return array(
				'api_host'            => $this->get_option( 'ns_fba_service_url' ),
				'version'             => $this->get_version(),
				'customer_mcf_status' => $this->get_option( 'ns_fba_mws_auth_token' ) ? 'EXISTING' : 'NEW',
				'merchant_id'         => $this->is_configured ? get_option( 'ns_fba_lwa_merchant_id' ) : get_option( 'ns_fba_merchant_id' ),
				'token'               => $this->ns_fba_lwa_auth_refresh_token ?? '',
			);
		}

		/**
		 * Try to perform an automatic migration from MWS to LWA.
		 *
		 * @param WP_Upgrader $upgrader_object  The instance of `WP_Upgrader`.
		 * @param array       $options          Array of bulk item update data.
		 *
		 * @throws Exception Only added thanks to the use of random_bytes in get_lwa_migration_url.
		 */
		public function after_upgrade_plugin( $upgrader_object, $options ) {
			$default_lwa_endpoint = 'https://sellingpartnerapi-na.amazon.com';

			$service_mapping = array(
				// DEFAULT / NORTH AMERICA / ALL OTHERS.
				'https://mws.amazonservices.com'    => $default_lwa_endpoint,
				// EUROPE.
				'https://mws-eu.amazonservices.com' => 'https://sellingpartnerapi-eu.amazon.com',
				'https://mws.amazonservices.in'     => 'https://sellingpartnerapi-eu.amazon.com',
				// FAR EAST.
				'https://mws-fe.amazonservices.com' => 'https://sellingpartnerapi-fe.amazon.com',
				'https://mws.amazonservices.com.au' => 'https://sellingpartnerapi-fe.amazon.com',
				'https://mws.amazonservices.jp'     => 'https://sellingpartnerapi-fe.amazon.com',
			);

			$current_service_url = $this->get_option( 'ns_fba_service_url' );

			if ( ! empty( $current_service_url ) && array_key_exists( $current_service_url, $service_mapping ) ) {
				$service_endpoint_lwa = $service_mapping[ $current_service_url ];
			} else {
				// After upgrade if the current service endpoint is empty or not found in mapping, set to default.
				$service_endpoint_lwa = $default_lwa_endpoint;
			}

			$migrate_url = $this->get_lwa_migration_url( $service_endpoint_lwa );

			if ( ! empty( $migrate_url ) ) {
				$response = wp_remote_get( $migrate_url );

				if ( SP_API::is_error_in( $response ) ) {
					$this->ns_fba->logger->add_entry( $response, 'wc' );
					$this->show_migration_status( false );
					return;
				}

				$json = json_decode( $response['body'], true );

				$access_token    = $json['access_token'];
				$refresh_token   = $json['refresh_token'];
				$lwa_merchant_id = $json['merchant_id'];

				if ( $this->set_lwa_config_data( $access_token, $refresh_token, $lwa_merchant_id ) ) {
					$this->update_option( 'ns_fba_service_url', $service_endpoint_lwa );
					$this->show_migration_status( true );
				} else {
					$this->show_migration_status( false );
				}
			}
		}

		/**
		 * Updates the form values accordingly without the need of refreshing the page
		 */
		public function process_admin_options() {
			parent::process_admin_options();
			$this->init_form_fields();
		}

		/**
		 * Once the page load checks for values coming as query parameters so we can define
		 * if it's last part of LWA process and update proper options
		 */
		private function init_check_tokens() {

			if ( ! isset( $_GET['access_token'] ) ||
			! isset( $_GET['refresh_token'] ) ||
			! isset( $_GET['merchant_id'] ) ||
			! $this->set_lwa_config_data(
				sanitize_text_field( wp_unslash( $_GET['access_token'] ) ),
				sanitize_text_field( wp_unslash( $_GET['refresh_token'] ) ),
				sanitize_text_field( wp_unslash( $_GET['merchant_id'] ) )
			)
			) {
				return;
			}

			$request_url = isset( $_SERVER['REQUEST_URI'] ) ? wp_strip_all_tags( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
			$url         = remove_query_arg( array( 'access_token', 'refresh_token', 'merchant_id' ), $request_url );

			wp_safe_redirect( $url );

		}

		/**
		 * Checks for the LWA configuration data. Return true if success.
		 *
		 * @param string $access_token   The access token.
		 * @param string $refresh_token  The refresh token.
		 * @param string $merchant_id    The merchant id.
		 *
		 * @return bool
		 */
		private function set_lwa_config_data( string $access_token, string $refresh_token, string $merchant_id ): bool {
			if ( ! is_user_logged_in() || ! current_user_can( 'administrator' ) ) {
				return false;
			}
			if ( empty( $access_token ) || empty( $refresh_token ) || empty( $merchant_id ) || $this->is_configured ) {
				return false;
			}

			update_option( 'ns_fba_lwa_auth_token', $access_token );
			update_option( 'ns_fba_lwa_auth_refresh_token', $refresh_token );
			update_option( 'ns_fba_lwa_merchant_id', $merchant_id );
			update_option( 'ns_fba_lwa_configured', true );

			return true;
		}

		/**
		 * API Test Results
		 */
		public function api_test_results() {
			check_ajax_referer( 'ns-fba-ajax', 'nonce' );

			$result = $this->ns_fba->fulfill->test_api_connection();

			$message = $result['message'];

			if ( false === $result['success'] ) {
				wp_send_json_error( $message );
			} else {
				wp_send_json_success( $message );
			}

		}

		/**
		 * Inventory Test Results
		 */
		public function inventory_test_results() {
			check_ajax_referer( 'ns-fba-ajax', 'nonce' );

			$success = false;
			$message = '';

			if ( ! isset( $_REQUEST['options'] ) ) {
				wp_send_json_error( __( 'Inventory Test Fail! No options passed', $this->text_domain ) );
			}

			/** TODO: JUST FOR TESTING.
			if ( $this->ns_fba->is_debug ) {
				error_log( "\ninventory_test_results() request options BEFORE parse: \n" . print_r( $_REQUEST['options'], true ) ); // phpcs:ignore
			}
			*/

			// TODO: Clean up this atrocious mess. HINT: WE do NOT need to parse all the options into a var here!
			// We need to rawurldecode the options so that their values go into the array properly.
			// ALSO IGNORE sanitization warning here because trying to sanitize these options will mess up API requests.
			// phpcs:ignore
			parse_str( wp_unslash( $_REQUEST['options'] ), $options );
			$sku              = $options['woocommerce_fba_ns_fba_test_inventory_sku'];
			$inventory_number = -1;

			/** TODO: JUST FOR TESTING.
			if ( $this->ns_fba->is_debug ) {
				error_log( "\ninventory_test_results() request options AFTER parse: \n" . print_r( $options, true ) );              // phpcs:ignore
			}
			*/

			if ( $this->is_configured ) {
				if ( ! empty( $sku ) ) {
					$marketplace_id = $this->get_option( 'ns_fba_marketplace_id' );
					$path           = '/fba/inventory/v1/summaries?details=true&granularityType=Marketplace&granularityId=' . $marketplace_id . '&marketplaceIds=' . $marketplace_id . '&sellerSkus=' . $sku;

					$result = $this->ns_fba->fulfill->test_api_connection( $path );

					if ( $result['success'] &&
					isset( $result['response'] ) &&
					! empty( $result['response']->payload->inventorySummaries ) ) {
						$inventory_number = $result['response']->payload->inventorySummaries[0]->inventoryDetails->fulfillableQuantity;
					} else {
						$message = __( 'SKU not found' );
					}
				} else {
					$message = __( 'Please, specify an SKU to test' );
				}
			}

			if ( -1 !== $inventory_number ) {
				// translators: 1: The total inventory 2: The sku.
				$message = sprintf( __( 'Inventory Test Success! There are %1$s units of %2$s in FBA stock.', $this->text_domain ), $inventory_number, $sku );
				wp_send_json_success( $message );
			} else {
				// translators: The error message.
				$message = sprintf( __( 'Inventory Test Fail! Error Message: %s', $this->text_domain ), $message );
				wp_send_json_error( $message );
			}
		}

		/**
		 * Handle the POST request to manually sync all inventory levels
		 */
		public function handle_manual_sync() {
			$this->sp_api_sync_inventory_ajax();
		}

		/**
		 * Called via ajax when clicking Sync Inventory btn
		 */
		public function sp_api_sync_inventory_ajax() {
			check_ajax_referer( 'ns-fba-ajax', 'nonce' );

			$result = $this->ns_fba->fulfill->sync_inventory( true );

			if ( $result ) {
				$last_inventory_sync_date = $this->get_option( LAST_INVENTORY_SYNC_OPT_NAME );
				wp_send_json_success(
					array(
						'message'                  => __( 'Sync Success.', $this->text_domain ),
						'last_inventory_sync_date' => $last_inventory_sync_date,
					)
				);
			} else {
				wp_send_json_error(
					array(
						'message'                  => __( 'An error occurred trying to update products inventory', $this->text_domain ),
						'last_inventory_sync_date' => '---',
					)
				);
			}
		}

		/**
		 * Called via ajax when clicking Check for more SKUs btn
		 */
		public function sp_api_check_skus() {
			check_ajax_referer( 'ns-fba-ajax', 'nonce' );

			$marketplace_id = $this->get_option( 'ns_fba_marketplace_id' );
			$next_token     = '';

			if ( isset( $_POST['data'] ) ) {

				$is_first_call = isset( $_POST['data']['is_first_call'] ) ? (bool) $_POST['data']['is_first_call'] : false;
				$post_token    = isset( $_POST['data']['next_token'] ) ? sanitize_text_field( wp_unslash( $_POST['data']['next_token'] ) ) : '';

				if ( ! $is_first_call && ! empty( $post_token ) ) {
					$next_token = sanitize_text_field( $post_token );
				}
			}

			$result = $this->ns_fba->inventory->get_SKUs( $marketplace_id, $next_token );

			$message = $result['message'];

			if ( ! $result['success'] ) {
				wp_send_json_error( $message );
			} else {
				wp_send_json_success(
					array(
						'wc_data_header'    => $this->wc_data_header,  // WC Products table field/header.
						'sp_data_header'    => $this->sp_data_header,  // SP Products table field/header.
						'added_inventory'   => $result['data']['added_inventory'],      // WC product data.
						'pending_inventory' => $result['data']['pending_inventory'],  // SP product data.
						'next_token'        => $result['data']['next_token'],
					)
				);
			}
		}

		/**
		 * Delete Logs
		 */
		public function delete_logs_clicked() {
			check_ajax_referer( 'ns-fba-ajax', 'nonce' );
			$files_deleted = $this->ns_fba->utils->delete_older_logs();
			if ( 0 < $files_deleted ) {
				// translators: The total log files deleted.
				$text = sprintf( __( '%s log files deleted successfully.', $this->text_domain ), $files_deleted );
				wp_send_json_success( $text );
			} else {
				$text = __( 'There were no files older than 30 days, or there was an error trying to delete them.', $this->text_domain );
				wp_send_json_error( $text );
			}
		}

		/**
		 * Responds to NS_FBA_FULFILL toggle action in SKUs modal table.
		 */
		public function toggle_ns_fba_fulfill() {
			check_ajax_referer( 'ns-fba-ajax', 'nonce' );

			if ( isset( $_POST['data'] ) && ! empty( $_POST['data'] ) ) {
				$this->ns_fba->fulfill->toggle_fulfill( $_POST['data'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			}

			wp_send_json_success( array() );
		}

		/**
		 * Removes the flag option we used to know if LWA is already configured.
		 */
		public function sp_api_disconnect_amazon() {
			check_ajax_referer( 'ns-fba-ajax', 'nonce' );
			// Remove connection settings.
			delete_option( 'ns_fba_lwa_auth_token' );
			delete_option( 'ns_fba_lwa_auth_refresh_token' );
			delete_option( 'ns_fba_lwa_merchant_id' );
			delete_option( 'ns_fba_lwa_configured' );
			// Remove any actively scheduled inventory syncs.
			if ( function_exists( 'as_unschedule_all_actions' ) ) {
				as_unschedule_all_actions( 'sp_api_sync_inventory' );
			}
			wp_send_json_success();
		}

		/**
		 * Responds to synchro action in SKUs modal table.
		 */
		public function import_seller_partner_sku() {
			check_ajax_referer( 'ns-fba-ajax', 'nonce' );

			$skus   = isset( $_POST['data'] ) ? $_POST['data'] : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$result = $this->ns_fba->inventory->import_SKUs( $skus );

			wp_send_json_success(
				array(
					'added'          => $result['added'],
					'failure'        => $result['failure'],
					'ignored'        => $result['ignored'],
					'wc_data_header' => $this->wc_data_header,
				)
			);
		}

		/**
		 * Return an array with marketplaces ids based on the region configured
		 *
		 * @param   string $svc_url The string service url.
		 *
		 * @return array
		 */
		private function get_marketplaces_options( $svc_url = '' ): array {
			$home_region = $svc_url;

			if ( empty( $svc_url ) ) {
				$home_region = $this->get_option( 'ns_fba_service_url' );
			}

			switch ( $home_region ) {
				case 'https://mws-eu.amazonservices.com':
				case 'https://sellingpartnerapi-eu.amazon.com':
					$options = array(
						'A1F83G8C2ARO7P' => __( 'United Kingdom - A1F83G8C2ARO7P', $this->text_domain ),
						'A13V1IB3VIYZZH' => __( 'France - A13V1IB3VIYZZH', $this->text_domain ),
						'A1RKKUPIHCS9HS' => __( 'Spain - A1RKKUPIHCS9HS', $this->text_domain ),
						'A1PA6795UKMFR9' => __( 'Germany - A1PA6795UKMFR9', $this->text_domain ),
						'APJ6JRA9NG5V4'  => __( 'Italy - APJ6JRA9NG5V4', $this->text_domain ),
						'A2NODRKZP88ZB9' => __( 'Sweden - A2NODRKZP88ZB9', $this->text_domain ),
						'A1C3SOZRARQ6R3' => __( 'Poland - A1C3SOZRARQ6R3', $this->text_domain ),
						'A1805IZSGTT6HS' => __( 'Netherlands - A1805IZSGTT6HS', $this->text_domain ),
						'ARBP9OOSHTCHU'  => __( 'Egypt - ARBP9OOSHTCHU', $this->text_domain ),
						'A33AVAJ2PDY3EV' => __( 'Turkey - A33AVAJ2PDY3EV', $this->text_domain ),
						'A17E79C6D8DWNP' => __( 'Saudi Arabia - A17E79C6D8DWNP', $this->text_domain ),
						'A2VIGQ35RCS4UG' => __( 'United Arab Emirates - A2VIGQ35RCS4UG', $this->text_domain ),
						'A21TJRUUN4KGV'  => __( 'India - A21TJRUUN4KGV', $this->text_domain ),
					);
					break;
				case 'https://mws-fe.amazonservices.com':
				case 'https://sellingpartnerapi-fe.amazon.com':
					$options = array(
						'A39IBJ37TRP1C6' => __( 'Australia - A39IBJ37TRP1C6', $this->text_domain ),
						'A1VC38T7YXB528' => __( 'Japan - A1VC38T7YXB528', $this->text_domain ),
						'A19VAU5U5O7RUS' => __( 'Singapore - A19VAU5U5O7RUS', $this->text_domain ),
					);
					break;
				default:
					$options = array(
						'ATVPDKIKX0DER'  => __( 'USA - ATVPDKIKX0DER', $this->text_domain ),
						'A2EUQ1WTGCTBG2' => __( 'Canada - A2EUQ1WTGCTBG2', $this->text_domain ),
						'A1AM78C64UM0Y8' => __( 'Mexico - A1AM78C64UM0Y8', $this->text_domain ),
						'A2Q3Y263D00KWC' => __( 'Brazil - A2Q3Y263D00KWC', $this->text_domain ),
					);
					break;
			}

			return $options;
		}

		/**
		 * Initialize integration settings form fields.
		 */
		public function init_form_fields() {
			$home_region_options = array(
				'https://sellingpartnerapi-na.amazon.com' => __( 'North America (Default) - https://sellingpartnerapi-na.amazon.com', $this->text_domain ),
				'https://sellingpartnerapi-eu.amazon.com' => __( 'Europe - https://sellingpartnerapi-eu.amazon.com', $this->text_domain ),
				'https://sellingpartnerapi-fe.amazon.com' => __( 'Far East - https://sellingpartnerapi-fe.amazon.com', $this->text_domain ),
			);
			$this->ns_fba_service_url = $this->get_option( 'ns_fba_service_url' );
			$home_region_keys         = array_keys( $home_region_options );

			// Initialize the Home Region when going between LWA and MWS.
			if ( empty( $this->ns_fba_service_url ) ||
				! in_array( $this->ns_fba_service_url, $home_region_keys, true )
			) {
				$this->update_option( 'ns_fba_service_url', $home_region_keys[0] );
				$this->ns_fba->options['ns_fba_service_url'] = $home_region_keys[0];
				$this->ns_fba_service_url                    = $home_region_keys[0];
			}

			if ( ! $this->is_configured ) {
				$this->form_fields = array_merge(
					$this->form_fields,
					array(
						// SECTION Amazon Account and LWA Settings.
						'ns_fba_lwa'              => array(
							'title' => __( 'NEW Amazon Selling Partner API', $this->text_domain ),
							'type'  => 'title',
							'desc'  => '',
						),

						'ns_fba_lwa_btn'          => array(
							'title'       => __( 'Login', $this->text_domain ),
							'label'       => __( 'Login with Amazon', $this->text_domain ),
							'description' => __( 'Authorize this App with your Amazon Seller account to use the new features of the Selling Partner API!', $this->text_domain ),
							'desc_tip'    => false,
							'default'     => '',
							'type'        => 'ns_fba_lwa_button',
						),

						'ns_fba_title_connection' => array(
							'title' => __( 'Amazon Region and Marketplace', $this->text_domain ),
							'type'  => 'title',
							'desc'  => '',
						),
					)
				);
			} else {
				// Only show the Disconnect button when LWA connection is active.
				$this->form_fields = array_merge(
					$this->form_fields,
					array(
						'ns_fba_sp_api_disconnect_amazon' => array(
							'title'       => __( 'Disconnect from Amazon', $this->text_domain ),
							'description' => __( 'Click to de-authorize this app from your Seller Central account and Disconnect from the Selling Partner API.', $this->text_domain ),
							'label'       => __( 'Logout of Amazon', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => '',
							'type'        => 'ns_fba_form_button',
						),
					)
				);
			}

			$options = $this->get_marketplaces_options();

			$this->form_fields = array_merge(
				$this->form_fields,
				array(
					'ns_fba_service_url'    => array(
						'title'       => __( 'Select Home Region', $this->text_domain ),
						'description' => __(
							'The default is set for North America. IMPORTANT: You can only have
						ONE home region and it should match the region in which you opened your
						Seller Central account.',
							$this->text_domain
						),
						'desc_tip'    => true,
						'std'         => '',
						'default'     => '',
						'type'        => 'select',
						'options'     => $home_region_options,
					),
					'ns_fba_marketplace_id' => array(
						'title'    => __( 'Amazon Marketplace ID', $this->text_domain ),
						'desc_tip' => false,
						'std'      => '',
						'default'  => '',
						'type'     => 'select',
						'options'  => $options,
					),
				)
			);

			if ( ! $this->is_configured ) {

				// TODO: Eventually we can remove these fields because the LWA auth process replaces these auth fields.
				// Hide the legacy ns_fba_mws_auth_token field if ns_fba_mws_auth_token is blank.
				if ( ! empty( $this->get_option( 'ns_fba_mws_auth_token' ) ) || isset( $_GET['dev'] ) ) {
					$this->form_fields = array_merge(
						$this->form_fields,
						array(
							'ns_fba_mws_auth_token' => array(
								'title'       => __( 'MWS Authorization Token', $this->text_domain ),
								'description' => __( 'Required for sending requests and data to Amazon. Follow instructions above to generate this in Seller Central.', $this->text_domain ),
								'desc_tip'    => true,
								'default'     => '',
								'type'        => 'text',
							),
						)
					);
				}

				// Hide the legacy ns_fba_aws_access_key_id field if ns_fba_aws_secret_access_key is blank.
				if ( ! empty( $this->get_option( 'ns_fba_aws_secret_access_key' ) ) || isset( $_GET['dev'] ) ) {
					$this->form_fields = array_merge(
						$this->form_fields,
						array(
							'ns_fba_aws_access_key_id' => array(
								'title'       => __( 'AWS Access Key ID', $this->text_domain ),
								'description' => __(
									'ONLY included for Legacy Support. This option will be removed in the future. PLEASE follow the
								instructions above to authorize this app and make sure the MWS Auth Token field is set correctly.
								If there is an MWS Auth Token configured, then this field will be ignored. Also, if the
								ns_fba_aws_secret_access_key field is blank, this will be hidden.',
									$this->text_domain
								),
								'desc_tip'    => true,
								'default'     => '',
								'type'        => 'text',
							),
						)
					);
				}

				// Hide the legacy ns_fba_aws_secret_access_key field if it is blank.
				if ( ! empty( $this->get_option( 'ns_fba_aws_secret_access_key' ) ) || isset( $_GET['dev'] ) ) {
					$this->form_fields = array_merge(
						$this->form_fields,
						array(
							'ns_fba_aws_secret_access_key' => array(
								'title'       => __( 'AWS Client Secret', $this->text_domain ),
								'description' => __(
									'ONLY included for Legacy Support. This option will be removed in the future. PLEASE follow the
								instructions above to authorize this app and make sure the MWS Auth Token field is set correctly.
								If there is an MWS Auth Token configured, then this field will be ignored. Also, if the
								ns_fba_aws_secret_access_key field is blank, this will be hidden.',
									$this->text_domain
								),
								'desc_tip'    => true,
								'default'     => '',
								'type'        => 'text',
							),
						)
					);
				}
			}

			if ( $this->is_configured ) {

				$last_inventory_sync_date           = new DateTime( $this->get_option( LAST_INVENTORY_SYNC_OPT_NAME ) );
				$last_inventory_sync_date           = $last_inventory_sync_date->setTimezone( wp_timezone() );
				$last_inventory_sync_date           = $last_inventory_sync_date->format( 'm-d-Y H:i:s' );
				$last_inventory_sync_date_container = '<br>Last sync date: <span id="last-inventory-sync-date-container">' . ( '' !== $last_inventory_sync_date ? $last_inventory_sync_date : '---' ) . '</span>';

				// Adjust the ns_fba_enable_shipping_method_mapping value according to following vars.
				$ns_fba_shipping_speed_standard  = $this->get_option( 'ns_fba_shipping_speed_standard' );
				$ns_fba_shipping_speed_expedited = $this->get_option( 'ns_fba_shipping_speed_expedited' );
				$ns_fba_shipping_speed_priority  = $this->get_option( 'ns_fba_shipping_speed_priority' );

				// Need to look at either, empty value or "Disabled" string.
				$ns_fba_shipping_speed_standard  = empty( $ns_fba_shipping_speed_standard ) ? 'Disabled' : $ns_fba_shipping_speed_standard;
				$ns_fba_shipping_speed_expedited = empty( $ns_fba_shipping_speed_expedited ) ? 'Disabled' : $ns_fba_shipping_speed_expedited;
				$ns_fba_shipping_speed_priority  = empty( $ns_fba_shipping_speed_priority ) ? 'Disabled' : $ns_fba_shipping_speed_priority;

				// If any of them is not disabled, ns_fba_enable_shipping_method_mapping must be 'yes'.
				if ( 'Disabled' !== $ns_fba_shipping_speed_standard ||
				'Disabled' !== $ns_fba_shipping_speed_expedited ||
				'Disabled' !== $ns_fba_shipping_speed_priority ) {
					$this->update_option( 'ns_fba_enable_shipping_method_mapping', 'yes' );
				}

				$this->form_fields = array_merge(
					$this->form_fields,
					array(
						'ns_fba_test_api' => array(
							'title'       => __( 'Test API Connection', $this->text_domain ),
							'label'       => __( 'Click to TEST Connection between Amazon and WooCommerce', $this->text_domain ),
							'description' => __( 'This checks to see if you have an authorized connection to the Amazon API. <br>', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => '',
							'type'        => 'ns_fba_form_button',
						),
					)
				);

				// SECTION Product Import
				// Only show the Compare and Import Products section when connected to the SP-API.
				if ( $this->is_configured ) {
					$this->form_fields = array_merge(
						$this->form_fields,
						array(
							'ns_fba_title_import'      => array(
								'title' => __( 'Compare and Import Products from Amazon', $this->text_domain ),
								'type'  => 'title',
								'desc'  => '',
							),

							'ns_fba_sp_api_check_skus' => array(
								'title'       => __( 'Check Amazon SKUs', $this->text_domain ),
								'label'       => __( 'Compare & Import your Products into WooCommerce by SKU', $this->text_domain ),
								'description' => __( 'Click to look for SKUs that exist in Amazon but not in WooCommerce and select Products to import.', $this->text_domain ),
								'desc_tip'    => true,
								'default'     => '',
								'type'        => 'ns_fba_form_button',
							),

							'ns_fba_sp_api_check_skus_modal' => array(
								'type' => 'ns_fba_form_modal',
							),
						)
					);
				}

				$this->form_fields = array_merge(
					$this->form_fields,
					array(
						// SECTION Inventory (Stock Level) Settings.
						'ns_fba_title_inventory' => array(
							'title' => __( 'Inventory (Stock Level) Settings', $this->text_domain ),
							'type'  => 'title',
							'desc'  => '',
						),
					)
				);

				if ( $this->is_configured ) {
					$this->form_fields = array_merge(
						$this->form_fields,
						array(
							'ns_fba_sp_api_sync_inventory' => array(
								'title'       => __( 'Sync Local Levels Manually', $this->text_domain ),
								'label'       => __( 'Click to Sync WooCommerce Stock Levels to match Amazon', $this->text_domain ),
								'description' => __( 'Initiate Amazon > WooCommerce Inventory Level Sync. NOTE: Use sparingly. Syncing inventory this way can have performance implications and impact live traffic depending on multiple factors.', $this->text_domain ),
								'desc_tip'    => true,
								'default'     => '',
								'type'        => 'ns_fba_form_button',
							),
						)
					);
				} else {
					$this->form_fields = array_merge(
						$this->form_fields,
						array(
							'ns_fba_sync_inventory_manually' => array(
								'title'       => __( 'Sync Local Levels Manually', $this->text_domain ),
								'label'       => __( 'Click to Sync WooCommerce Stock Levels to match Amazon', $this->text_domain ),
								'description' => __( 'Initiate Amazon > WooCommerce Inventory Level Sync. NOTE: Use sparingly. Syncing inventory this way can have performance implications and impact live traffic depending on multiple factors.', $this->text_domain ),
								'desc_tip'    => true,
								'default'     => '',
								'type'        => 'ns_fba_form_button',
							),
						)
					);
				}

				$this->form_fields = array_merge(
					$this->form_fields,
					array(
						'ns_fba_test_inventory'           => array(
							'title'       => __( 'TEST Inventory Connection', $this->text_domain ),
							'label'       => __( 'Click to Test your Inventory Connection using the SKU Below', $this->text_domain ),
							'description' => __( 'Fill in a valid SKU above in the Test Inventory SKU field and click to test a stock level request', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => '',
							'type'        => 'ns_fba_form_button',
						),

						'ns_fba_test_inventory_sku'       => array(
							'title'       => __( 'TEST Inventory SKU', $this->text_domain ),
							'description' => __( 'Active SKU from your FBA inventory to test with the Test FBA Inventory Button Below', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => '',
							'type'        => 'text',
						),

						'ns_fba_update_inventory'         => array(
							'title'    => __( 'Update Local Levels on Order', $this->text_domain ),
							'label'    => __( 'Update local stock levels per Order item sent to Amazon Fulfillment', $this->text_domain ),
							'desc_tip' => false,
							'default'  => 'no',
							'type'     => 'checkbox',
						),

						'ns_fba_sp_api_sync_inventory_interval_enabled' => array(
							'title'       => __( 'Enable Auto Inventory Sync', $this->text_domain ),
							'label'       => __( 'Automatically sync stock levels from Amazon at the interval below.', $this->text_domain ),
							'description' => __( 'Regardless of the value specified in "Sync Inventory Interval", if this option is disabled, the automatic inventory sync will not occur.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 0,
							'type'        => 'checkbox',
						),

						'ns_fba_update_inventory_selected_only' => array(
							'title'    => __( 'Only Update Inventory of FBA Products', $this->text_domain ),
							'label'    => __( 'Update stock levels of FBA enabled products only with auto inventory sync', $this->text_domain ),
							'desc_tip' => false,
							'default'  => 'no',
							'type'     => 'checkbox',
						),

						'ns_fba_sp_api_sync_inventory_interval' => array(
							'title'       => __( 'Set Interval for Level Sync', $this->text_domain ),
							'label'       => __( 'Set Interval for Level Sync', $this->text_domain ),
							// translators: The last inventory sync container.
							'description' => sprintf( __( 'Interval in minutes to sync your order statuses and/or inventory levels automatically. %s', $this->text_domain ), $last_inventory_sync_date_container ),
							'desc_tip'    => false,
							'default'     => 1440,
							'type'        => 'number',
						),

						'ns_fba_low_stock_threshold'      => array(
							'title'       => __( 'Set Low Stock Threshold', $this->text_domain ),
							'description' => __( 'If Amazon stock level is detected to be less than this number, automatically set WooCommerce stock to 0 to prevent overselling an item. This should be a number like 10. Setting this to 0 is the same as turning this oversell protection OFF.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => '0',
							'type'        => 'text',
						),

						// SECTION FBA Order Fulfillment Settings.

						'ns_fba_title_fulfillment'        => array(
							'title' => __( 'Order Fulfillment Settings', $this->text_domain ),
							'type'  => 'title',
							'desc'  => '',
						),

						'ns_fba_order_prefix'             => array(
							'title'       => __( 'Order Prefix (Recommended)', $this->text_domain ),
							'description' => __( 'This will add a prefix to the unique order key sent to Amazon for fulfillment. If it is blank, only the WooCommerce Order ID (like "1234") will be sent. It is recommended to specify a short value here (with no spaces) like "ns_" specific to your store. ONLY USE letters, dashes, underscores!', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'ecommerce_',
							'type'        => 'text',
						),

						'ns_fba_order_comment'            => array(
							'title'       => __( 'Order Comment', $this->text_domain ),
							'description' => __( 'This text should be short (max 1000 characters) and will appear on customer-facing materials such as the outbound shipment packing slip.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'Thank you for your order!',
							'type'        => 'text',
						),

						'ns_fba_shipping_speed'           => array(
							'title'       => __( 'DEFAULT Shipping Speed', $this->text_domain ),
							'description' => __( 'This will be used when the shipping method chosen by the customer for their order does NOT match ANY of the mappings below', $this->text_domain ),
							'desc_tip'    => true,
							'std'         => 'Standard',
							'default'     => 'Standard',
							'type'        => 'select',
							'options'     => array(
								'Standard'  => __( 'Standard (Default)', $this->text_domain ),
								'Expedited' => __( 'Expedited', $this->text_domain ),
								'Priority'  => __( 'Priority', $this->text_domain ),
							),
						),

						'ns_fba_enable_shipping_method_mapping' => array(
							'title'       => __( 'Enable Shipping Method Mapping', $this->text_domain ),
							'description' => __( 'Enable setting the default shipping method for each rate STANDARD | EXPEDITED | PRIORITY', $this->text_domain ),
							'desc_tip'    => false,
							'default'     => '',
							'type'        => 'checkbox',
						),

						'ns_fba_shipping_speed_standard'  => array(
							'title'       => __( 'Shipping for STANDARD', $this->text_domain ),
							'description' => __( 'Orders with the selected shipping method will use Amazon STANDARD Shipping Speed. <strong>IMPORTANT:</strong> There are extra fees associated with using different Amazon Shipping Speeds. Check with Amazon for specifics related to your region.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => '',
							'type'        => 'ns_fba_map_shipping',
						),

						'ns_fba_shipping_speed_expedited' => array(
							'title'       => __( 'Shipping for EXPEDITED', $this->text_domain ),
							'description' => __( 'Orders with the selected shipping method will use Amazon EXPEDITED Shipping Speed. <strong>IMPORTANT:</strong> There are extra fees associated with using different Amazon Shipping Speeds. Check with Amazon for specifics related to your region.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => '',
							'type'        => 'ns_fba_map_shipping',
						),

						'ns_fba_shipping_speed_priority'  => array(
							'title'       => __( 'Shipping for PRIORITY', $this->text_domain ),
							'description' => __( 'Orders with the selected shipping method will use Amazon PRIORITY Shipping Speed. <strong>IMPORTANT:</strong> There are extra fees associated with using different Amazon Shipping Speeds. Check with Amazon for specifics related to your region.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => '',
							'type'        => 'ns_fba_map_shipping',
						),

						'ns_fba_fulfillment_policy'       => array(
							'title'       => __( 'FBA Fulfillment Policy', $this->text_domain ),
							// translators: 1: Opening html link tag 2: Closing html link tag.
							'description' => sprintf( __( 'More info about each option: %1$sCreateFulfillmentOrder MWS Documentation%2$s', $this->text_domain ), '<a href="http://docs.developer.amazonservices.com/en_US/fba_outbound/FBAOutbound_CreateFulfillmentOrder.html" target="_blank">', '</a>' ),
							'desc_tip'    => false,
							'std'         => 'FillOrKill',
							'default'     => 'FillOrKill',
							'type'        => 'select',
							'options'     => array(
								'FillOrKill'       => __( 'FillOrKill (Default)', $this->text_domain ),
								'FillAll'          => __( 'FillAll', $this->text_domain ),
								'FillAllAvailable' => __( 'FillAllAvailable', $this->text_domain ),
							),
						),
						'ns_fba_fulfillment_ship_blank_box' => array(
							'title'       => __( 'Ship only with Blank Boxes', $this->text_domain ),
							'label'       => __( 'Prevent the order if any item(s) cannot be shipped in non-Amazon packaging.', $this->text_domain ),
							'description' => __( 'The order will fail if item(s) cannot be shipped in non-Amazon packaging (blank boxes).', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'no',
							'type'        => 'checkbox',
						),
						'ns_fba_fulfillment_ship_amzl'    => array(
							'title'       => __( 'Ship without AMZ Logistics', $this->text_domain ),
							'label'       => __( 'Prevent Amazon Logistics from being a carrier for this order', $this->text_domain ),
							'description' => __( 'Preventing the use of Amazon Logistics for your MCF orders will result in a fee surcharge per order, and increase the risk of some of your orders being unfulfilled or estimated to be delivered later if there are no alternative carriers available.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'no',
							'type'        => 'checkbox',
						),

						// SECTION General Plugin Settings.

						'ns_fba_title_general'            => array(
							'title' => __( 'General and LEGACY Plugin Settings', $this->text_domain ),
							'type'  => 'title',
							'desc'  => '',
						),

						'ns_fba_notify_email'             => array(
							'title'       => __( 'Notification Email', $this->text_domain ),
							'description' => __( 'Include this email address on Amazon Notifications sent to the customer. Leave this setting BLANK to prevent Amazon Notifications to you. This will also be used as the TO: address for any error messages if the Email on Error setting is ON.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => '',
							'type'        => 'text',
						),

						'ns_fba_email_on_error'           => array(
							'title'       => __( 'Email on Error', $this->text_domain ),
							'label'       => __( 'Send Error Notifications', $this->text_domain ),
							'description' => __( 'Send an email message when an order fails to be sent to FBA to the address above or the site admin email if the notification email is blank.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'no',
							'type'        => 'checkbox',
						),

						'ns_fba_exclude_phone'            => array(
							'title'       => __( 'Exclude Customer Phone #', $this->text_domain ),
							'label'       => __( 'Do NOT send customer phone number to Amazon', $this->text_domain ),
							'description' => __( 'Sometimes Amazon uses the customer phone number to text them shipping tracking info. Use this option to exclude the customer phone number from the order data sent to FBA.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'no',
							'type'        => 'checkbox',
						),

						'ns_fba_encode_convert_bypass'    => array(
							'title'       => __( 'Encoding Convert BYPASS', $this->text_domain ),
							'label'       => __( 'Bypass automatic encoding conversion', $this->text_domain ),
							'description' => __( 'This will bypass WCAFs normal attempt to convert customer name and address characters into a format that FBA will always accept. Sometimes there is a problem with the conversion which results in [?] characters. If you see this with your FBA orders in Seller Central, try turning this option ON to bypass the conversion completely and pass the raw data directly to Amazon. Note: this might cause FBA to reject orders in certain situations.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'no',
							'type'        => 'checkbox',
						),

						'ns_fba_encode_check_override'    => array(
							'title'       => __( 'Encoding Check OVERRIDE', $this->text_domain ),
							'label'       => __( 'Override normal encoding conversion validation checking for unconverted characters', $this->text_domain ),
							'description' => __( 'This will override WCAFs final check on character encodings in the shipping address and allow the order to be sent to FBA even if it cannot convert some characters successfully. This might result in some addresses containing [?] characters.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'no',
							'type'        => 'checkbox',
						),

						'ns_fba_automatic_completion'     => array(
							'title'       => __( 'Auto-Complete Order Status', $this->text_domain ),
							'label'       => __( 'Automatically mark successful orders complete', $this->text_domain ),
							'description' => __( 'Instantly set orders successfully received by FBA to the standard WooCommerce Completed status instead of the custom WCAF status.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'no',
							'type'        => 'checkbox',
						),

						'ns_fba_sync_ship_status'         => array(
							'title'       => __( 'Sync Shipping Status', $this->text_domain ),
							'label'       => __( 'Automatically sync order status based on Amazon shipping status', $this->text_domain ),
							'description' => __( 'Check for updates to shipping status once per hour on orders that have been successfully Sent to FBA (including Partial to FBA). This will also automatically update the order status to Completed if FBA reports the order has shipped. If this option is ON then the Mark Orders Complete option should be OFF.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'no',
							'type'        => 'checkbox',
						),

						'ns_fba_sync_ship_retry'          => array(
							'title'       => __( 'Retry Failed Orders', $this->text_domain ),
							'label'       => __( 'Automatically re-submit failed orders when syncing shipping status', $this->text_domain ),
							'description' => __( 'Retry each failed order a maximum of one time during scheduled shipping status sync. This will only take effect if Sync Shipping Status is ON.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'no',
							'type'        => 'checkbox',
						),

						'ns_fba_disable_shipping_email'   => array(
							'title'       => __( 'Disable Shipping Email', $this->text_domain ),
							'label'       => __( 'Prevent Amazon from emailing the customer directly with order information', $this->text_domain ),
							'description' => __( 'Do NOT allow Amazon to send the customer a shipping notice email. Most stores should leave this option OFF. However, some might find this option useful when Amazon is sending confusing messages to the customer (like in the wrong language). When this option is ON the shipping notices will ONLY be sent to the admin email address. Changing this will NOT affect any orders already placed.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'no',
							'type'        => 'checkbox',
						),

						'ns_fba_display_order_tracking'   => array(
							'title'       => __( 'Display Order Tracking', $this->text_domain ),
							'label'       => __( 'Show order information from Amazon on your customer view order page', $this->text_domain ),
							'description' => __( 'Show Order Shipping and Tracking information on the customer Order View Page pulled directly from Amazon including the latest status and tracking number. It can take up to an hour for tracking info to be retrieved and updated.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'yes',
							'type'        => 'checkbox',
						),

						'ns_fba_debug_mode'               => array(
							'title'       => __( 'Enable DEBUG mode', $this->text_domain ),
							'label'       => __( 'Capture additional info to help with support cases', $this->text_domain ),
							'description' => __( 'Turn on additional logging for support cases. Normally, leave this turned OFF.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'no',
							'type'        => 'checkbox',
						),

						'ns_fba_clean_logs'               => array(
							'title'       => __( 'Enable Daily Log Deletion', $this->text_domain ),
							'label'       => __( 'Automatically delete error and success logs older than a certain number of days once per day', $this->text_domain ),
							'description' => __( 'This schedules a job to remove both error and success logs daily and automatically for log files older than 20 days. Any links in order notes pointing to deleted log files will be broken, but this will help keep logs from growing indefinitely. Default is OFF.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'no',
							'type'        => 'checkbox',
						),

						'ns_fba_clean_logs_interval'      => array(
							'title'    => __( 'Number of Days to Keep Logs', $this->text_domain ),
							'label'    => __( 'Define the limit of how old logs should be before being automatically deleted if \'Enable Daily Log Deletion\' is enabled.', $this->text_domain ),
							'desc_tip' => true,
							'default'  => 30,
							'type'     => 'number',
						),

						'ns_fba_clean_logs_now'           => array(
							'title'       => __( 'Delete Logs Manually', $this->text_domain ),
							'label'       => __( 'Click to Manually Delete Old Logs NOW', $this->text_domain ),
							'description' => __( 'This will delete ALL error and success logs older than 30 days. Links to these deleted logs inside order notes will be broken.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => '',
							'type'        => 'ns_fba_form_button',
						),

						// SECTION Smart Fulfillment Settings.

						'ns_fba_title6'                   => array(
							'title' => __( 'Order Level Processing Rules', $this->text_domain ),
							'type'  => 'title',
							'desc'  => '',
						),

						'ns_fba_manual_order_override'    => array(
							'title'       => __( 'Manual Order OVERRIDE', $this->text_domain ),
							'label'       => __( 'Skip all other processing rules below when manually submitting an order to Amazon', $this->text_domain ),
							'description' => __( 'This will bypass ALL other Order Level Processing Rules below <b>when manually sending an order to FBA</b> and force WCAF to try to send it through. Normally leave this turned OFF. This setting does NOT bypass the rules below for automatic fulfillment.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'no',
							'type'        => 'checkbox',
						),

						'ns_fba_disable_international'    => array(
							'title'       => __( 'Disable for International', $this->text_domain ),
							// translators: 1: Opening html link tag 2: Closing html link tag.
							'label'       => sprintf( __( 'ONLY Send Orders to FBA for addresses inside your %1$sBase Location Country%2$s', $this->text_domain ), '<a href="' . admin_url( 'admin.php?page=wc-settings' ) . '" target="_blank">', '</a>' ),
							'description' => __( 'Prevent orders from other countries from being sent to Amazon for fulfillment', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'no',
							'type'        => 'checkbox',
						),

						'ns_fba_disable_shipping'         => array(
							'title'       => __( 'Disable for Shipping Methods', $this->text_domain ),
							'description' => __( 'ONLY Send Orders to FBA that do not use any of the selected Shipping methods below (CTL+Click to Select Multiple Items).', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'no',
							'type'        => 'ns_fba_exclude_shipping',
						),

						'ns_fba_title7'                   => array(
							'title' => __( 'Order Item (Product) Level Processing Rules', $this->text_domain ),
							'type'  => 'title',
							'desc'  => '',
						),

						'ns_fba_manual_item_override'     => array(
							'title'       => __( 'Manual Order Item OVERRIDE', $this->text_domain ),
							'label'       => __( 'Skip all other item processing rules below when manually submitting an order to Amazon', $this->text_domain ),
							'description' => __( 'This will bypass ALL other Order Item Level Processing Rules below <b>when manually sending an order to FBA</b> and force WCAF to try to send ALL items in an order regardless of their individual Product settings. Normally leave this turned ON. This setting does NOT bypass the rules below for automatic fulfillment.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'no',
							'type'        => 'checkbox',
						),

						'ns_fba_manual_only_mode'         => array(
							'title'       => __( 'Manual Only Mode', $this->text_domain ),
							'label'       => __( 'Skip automatic order fulfillment', $this->text_domain ),
							'description' => __( 'Send orders to Amazon ONLY when manually sent from the order admin page', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'no',
							'type'        => 'checkbox',
						),

						'ns_fba_vacation_mode'            => array(
							'title'       => __( 'Vacation Mode', $this->text_domain ),
							'label'       => __( 'Force all items in all order to go to Amazon for fulfillment', $this->text_domain ),
							'description' => __( 'Send ALL Order Items to FBA Regardless of their individual Product Settings. You can also use this to avoid turning ON the Fulfill with Amazon FBA setting in every single product, but this is not recommended unless every SKU has a match in FBA.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'no',
							'type'        => 'checkbox',
						),

						'ns_fba_perfection_mode'          => array(
							'title'       => __( 'Perfection Mode', $this->text_domain ),
							'label'       => __( 'Do NOT send partially fulfillable orders to Amazon', $this->text_domain ),
							'description' => __( 'ONLY Send Orders to FBA if ALL order item products are set to Fulfill with Amazon FBA.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => 'no',
							'type'        => 'checkbox',
						),

						'ns_fba_quantity_max_filter'      => array(
							'title'       => __( 'Quantity Max Filter', $this->text_domain ),
							'description' => __( 'This is the maximum quantity per item that will be allowed to go to FBA. If the ordered quantity is more than this number for an item, it will NOT be sent to FBA. Leave this setting BLANK to send items to FBA regardless of the quantities ordered.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => '',
							'type'        => 'text',
						),

						// SECTION Configuration for Multiple Currencies.

						'ns_fba_title8'                   => array(
							'title' => __( 'Configuration for Multiple Currencies - Normally NOT Used', $this->text_domain ),
							'type'  => 'title',
							'desc'  => '',
						),

						'ns_fba_currency_code'            => array(
							'title'       => __( 'Currency Code OVERRIDE', $this->text_domain ),
							'description' => __( 'Manually Override the WooCommerce with a value like USD or GBP or EUR, etc. Leave this BLANK unless you know exactly what you are doing.</strong> Normally, WCAF will use the currency configured in WooCommerce. This setting is ONLY if your store (WooCommerce) currency is different than you default Amazon Marketplace currency.', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => '',
							'type'        => 'text',
						),

						'ns_fba_currency_conversion'      => array(
							'title'       => __( 'Currency Conversion Value', $this->text_domain ),
							'description' => __( 'Rate used to calculate Amazon PerUnitDeclaredValue. This is ONLY used if Currency Override is set. The formula is: Product Price * Currency Conversion = PerUnitDeclaredValue sent to Amazon', $this->text_domain ),
							'desc_tip'    => true,
							'default'     => '',
							'type'        => 'text',
						),
					)
				); // end array and array_merge.
			}
		}

		// TODO: Continue to remove any LEGACY functions not in use.

		/**
		 * Generate form modal html.
		 *
		 * @param   string $key   The key.
		 * @param   array  $data  The data.
		 *
		 * @return string
		 */
		public function generate_ns_fba_form_modal_html( string $key, array $data ): string {
			ob_start();
			$field = $this->plugin_id . $this->id . '_' . $key;
			$data  = wp_parse_args( $data, $this->button_defaults );
			?>
		<tr valign="top">
			<td class="forminp">
				<fieldset>
					<div id="check-skus-modal" title="<?php esc_html_e( 'Check for more SKUs', $this->text_domain ); ?>" style="display: none;">
						<form id="sku-existent-form" action="">
							<table id="existent-sku-table">
							</table>
						</form>
						<form id="sku-pending-form" action="">
							<table id="pending-sku-table">
							</table>
						</form>
					</div>
				</fieldset>
			</td>
		</tr>
			<?php
			return ob_get_clean();
		}

		/**
		 * Generate Form Action Buttons.
		 *
		 * @param string $key   The key.
		 * @param array  $data  The data.
		 *
		 * @return string
		 */
		public function generate_ns_fba_form_button_html( string $key, array $data ): string {
			$field = $this->plugin_id . $this->id . '_' . $key;
			$data  = wp_parse_args( $data, $this->button_defaults );
			ob_start();
			?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</th>
			<td class="forminp">
				<fieldset>
					<input type="submit" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $data['label'] ); ?>"/>
					<?php echo $this->get_description_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</fieldset>
			</td>
		</tr>
			<?php
			return ob_get_clean();
		}

		/**
		 * Generate LWA Buttons.
		 *
		 * @param string $key   The key.
		 * @param array  $data  The data.
		 *
		 * @return string
		 */
		public function generate_ns_fba_lwa_button_html( string $key, array $data ): string {
			$field = $this->plugin_id . $this->id . '_' . $key;
			$data  = wp_parse_args( $data, $this->button_defaults );
			ob_start();
			?>
		<a href="<?php echo esc_url( $this->get_lwa_consent_url() ); ?>" id="LoginWithAmazon">
			<img alt="<?php esc_attr_e( 'Login with Amazon', $this->text_domain ); ?>"
				src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'assets/btnLWA.png' ); ?>"
				width="156" height="32"/>
		</a>

			<?php
			// Ignore the phpcs warning to escape html which in this case breaks the admin render.
			// phpcs:ignore
			echo $this->get_description_html( $data );
			return ob_get_clean();
		}

		/**
		 * This function returns the consent URL to start the LWA process.
		 */
		public function get_lwa_consent_url(): string {
			// Ignore the phpcs warning about base64 which is required by Amazon SP-API.
			// phpcs:ignore
			$state = base64_encode( random_bytes( 128 ) );
			set_transient( SP_API_STATE_TRANSIENT, $state, 600 );
			$request_url = isset( $_SERVER['REQUEST_URI'] ) ? wp_strip_all_tags( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
			// Ignore the phpcs warning about base64 which is required by Amazon SP-API.
			// phpcs:ignore
			$state = base64_encode(
				wp_json_encode(
					array(
						'state'    => $state,
						'url'      => home_url() . $request_url,
						'rest_url' => get_rest_url(),
					)
				)
			);

			$url = sprintf( '%s?application_id=%s&state=%s&redirect_uri=%s', SP_API_CONSENT_URL, SP_API_ID, $state, SP_API_REDIRECT_URI );
			$url = SP_API_DEBUG_MODE ? $url . '&version=beta' : $url;

			return $url;
		}

		/**
		 * This function returns the URL to migrate from MWS to LWA.
		 *
		 * @param string $target_lwa_url  The new lwa endpoint for this seller's account.
		 *
		 * @return string
		 * @throws Exception Included thanks to random_bytes maybe throwing up.
		 */
		public function get_lwa_migration_url( string $target_lwa_url ): string {

			// SP service url and developer id must be reachable.
			if ( empty( $target_lwa_url ) || ! isset( $this->developer_ids[ $target_lwa_url ] ) ) {
				return '';
			}

			// LWA should not be set but MWS should, because the only way to migrate is from an existing MWS token.
			if ( $this->is_configured ) {
				return '';
			}

			$state = base64_encode( random_bytes( 128 ) ); // phpcs:ignore
			set_transient( SP_API_STATE_MIGRATE_TRANSIENT, $state, 600 );
			$request_url = isset( $_SERVER['REQUEST_URI'] ) ? wp_strip_all_tags( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

			$args = array(
				'service_url'    => $target_lwa_url,
				'application_id' => SP_API_ID,
				'client_state'   => $state,
				'url'            => rawurlencode( home_url() . $request_url ),
				'rest_url'       => rawurlencode( get_rest_url() ),
				'client_version' => $this->get_version(),
				'merchant_id'    => $this->get_option( 'ns_fba_merchant_id' ),
				'developer_id'   => $this->developer_ids[ $target_lwa_url ],
				'mws_auth_token' => $this->get_option( 'ns_fba_mws_auth_token' ),
				'requestType'    => 'MigrateMwsAuthToLwa',
				'requestQty'     => 0,
				'hostName'       => rawurlencode( get_site_url() ),
			);

			$url = add_query_arg( $args, SP_API_MWS_MIGRATE_URI );

			return $url;
		}

		/**
		 * Get file version.
		 *
		 * @return string
		 */
		public function get_version(): string {
			return $this->ns_fba->version;
		}

		/**
		 * Generate Shipping Map Dropdown.
		 *
		 * @param string $key   The key.
		 * @param array  $data  The data.
		 *
		 * @return string
		 */
		public function generate_ns_fba_map_shipping_html( string $key, array $data ): string {
			$field_name = $this->plugin_id . $this->id . '_' . $key;
			$defaults   = array();
			$data       = wp_parse_args( $data, $defaults );

			// Set up all our active shipping methods.
			$shipping_methods = array();
			$shipping_methods = $this->ns_fba->wc->get_active_shipping_methods();
			if ( empty( $shipping_methods ) ) {
				$shipping_methods[0] = __( 'No Active Shipping Methods Found', $this->text_domain );
			} else {
				// Add a disabled choice in case they don't want to use a shipping speed.
				array_unshift( $shipping_methods, 'Disabled' );
			}

			$amazon_shipping_index = array_search( 'Amazon', $shipping_methods ); // phpcs:ignore
			if ( false !== $amazon_shipping_index ) {
				unset( $shipping_methods[ $amazon_shipping_index ] );
			}

			ob_start();
			?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field_name ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</th>
			<td class="forminp">
				<select class="select" name="<?php echo esc_attr( $field_name ); ?>" id="<?php echo esc_attr( $field_name ); ?>">
					<?php foreach ( $shipping_methods as $method ) : ?>
						<?php $selected = ( ! empty( $this->ns_fba->options[ $key ] ) && $this->ns_fba->options[ $key ] === $method ); ?>
						<option value="<?php echo esc_attr( $method ); ?>" <?php echo ( $selected ? 'selected="selected"' : '' ); ?>><?php echo esc_html( $method ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
			<?php
			return ob_get_clean();
		}

		/**
		 * Generate Shipping Exclusion Multiple Select.
		 *
		 * @param string $key   The key.
		 * @param array  $data  The data.
		 *
		 * @return string
		 */
		public function generate_ns_fba_exclude_shipping_html( string $key, array $data ): string {
			$field_name = $this->plugin_id . $this->id . '_' . $key;
			$defaults   = array();
			$data       = wp_parse_args( $data, $defaults );

			// Set up all our active shipping methods.
			$shipping_methods = array();
			$shipping_methods = $this->ns_fba->wc->get_active_shipping_methods();
			if ( empty( $shipping_methods ) ) {
				$shipping_methods[0] = __( 'No Active Shipping Methods Found', $this->text_domain );
			} else {
				// Add a disabled choice in case they don't want to use a shipping speed.
				array_unshift( $shipping_methods, 'Disabled' );
			}

			$amazon_shipping_index = array_search( 'Amazon', $shipping_methods ); // phpcs:ignore
			if ( false !== $amazon_shipping_index ) {
				unset( $shipping_methods[ $amazon_shipping_index ] );
			}

			ob_start();
			?>
		<tr style="vertical-align: top;">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field_name ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</th>
			<td class="forminp">
				<select name="woocommerce_fba_ns_fba_disable_shipping[]" id="woocommerce_fba_ns_fba_disable_shipping"
						multiple>
					<?php
					// Update the default item to make more sense for this setting since we're reusing the active shipping methods.
					if ( count( $shipping_methods ) > 1 ) {
						$shipping_methods[0] = __( 'None (Allow Orders with ANY Shipping Method to be sent to FBA)', $this->text_domain );
					}
					foreach ( $shipping_methods as $method ) :
						?>
						<option value="<?php echo esc_attr( $method ); ?>"
						<?php
						echo( ( ! empty( $this->ns_fba->options['ns_fba_disable_shipping'] ) &&
								is_array( $this->ns_fba->options['ns_fba_disable_shipping'] ) &&
								in_array( $method, $this->ns_fba->options['ns_fba_disable_shipping'], true ) ) ? 'selected' : '' );
						?>
						>
						<?php echo esc_html( $method ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
			<?php
			return ob_get_clean();
		}

		/**
		 * Validate Shipping Exclusions Field.
		 *
		 * @param string $key The field key.
		 * @param mixed  $value The value.
		 *
		 * @return mixed
		 */
		public function validate_ns_fba_exclude_shipping_field( $key, $value ) {
			// override the default text field validation for our custom field because it tries to run stripslashes on our array.
			return $value;
		}

		/**
		 * Removes the flag option we used to know if LWA is already configured.
		 */
		public function refresh_marketplace_options() {
			check_ajax_referer( 'ns-fba-ajax', 'nonce' );
			// phpcs:ignore
			$region = sanitize_text_field( wp_unslash( $_POST['amazon_region_url'] ) );

			if ( $region ) {
				$options = $this->get_marketplaces_options( $region );

				$marketplace_options = '';
				foreach ( $options as $val => $option ) {
					$marketplace_options .= '<option value="' . $val . '">' . $option . '</option>';
				}
				wp_send_json_success( $marketplace_options );
			}
			wp_send_json_error( "Can't update Amazon Marketplace Selector" );
		}
		// *************************************** .

		/**
		 * Logs a message notifying that the migration failed.
		 *
		 * @param bool $is_success Whether or not the auto migration succeeded.
		 */
		private function show_migration_status( bool $is_success ) {

			if ( $is_success ) {
				add_action(
					'admin_notices',
					function() {
						echo esc_html(
							'<div class="notice notice-success is-dismissible">
							<p>' .
							__(
								'Amazon Fulfillment (MCF) for WooCommerce has successfully migrated your Seller Account 
							connection from MWS to LWA and the new Amazon Selling Partner API. New features like 
							Amazon Shipping methods for WooCommerce and real-time shipping costs and estimated 
							delivery timeframes for your customers are now available. See full details here: 
								https://woocommerce.com/document/amazon-fulfillment/',
								$this->text_domain
							)
							. '</p>
						</div>'
						);
					}
				);
			} else {
				add_action(
					'admin_notices',
					function() {
						echo esc_html(
							'<div class="notice notice-warning is-dismissible">
							<p>' .
							__(
								'Automatic migration from MWS to LWA and the new Amazon Selling Partner API failed. 
							Please manually migrate by using the "Login with Amazon" button to
							ensure uninterrupted integration with Amazon FBA / MCF and to access new features like 
							Amazon Shipping methods for WooCommerce, real-time shipping costs, and estimated 
								delivery timeframes for your customers.',
								$this->text_domain
							)
							. '</p>
						</div>'
						);
					}
				);
			}
		}

	} //class.
}
