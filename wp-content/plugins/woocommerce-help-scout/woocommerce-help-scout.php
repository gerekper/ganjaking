<?php
/**
 * Plugin Name: WooCommerce Help Scout
 * Plugin URI: https://woocommerce.com/products/woocommerce-help-scout/
 * Description: A Help Scout integration plugin for WooCommerce.
 * Version: 3.2
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Text Domain: woocommerce-help-scout
 * Domain Path: /languages
 * Woo: 395318:1f5df97b2bc60cdb3951b72387ec2e28
 * WC tested up to: 5.8
 * WC requires at least: 2.6
 *
 * Copyright (c) 2018 WooCommerce.
 *
 * @package  WC_Help_Scout
 * @category Core
 * @author   WooThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '1f5df97b2bc60cdb3951b72387ec2e28', '395318' );

if ( ! class_exists( 'WC_Help_Scout' ) ) :

	define( 'WC_HELP_SCOUT_VERSION', '2.5' );
	define( 'WC_HELP_SCOUT_PLUGINURL', plugin_dir_url( __FILE__ ) );
	/**
	 * WooCommerce Help Scout main class.
	 */
	class WC_Help_Scout {
		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected $app_key;
		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected $app_secret;

		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Component instances.
		 *
		 * @var array
		 */
		protected $_components = array();

		/**
		 * Initialize the plugin.
		 */
		private function __construct() {
			$nonce = wp_create_nonce( 'woocommerce_help_scout_nonce' );

			// Define user set variables.
			$woocommerce_help_scout_settings = get_option( 'woocommerce_help-scout_settings' );
			$this->app_key = isset( $woocommerce_help_scout_settings['app_key'] ) ? $woocommerce_help_scout_settings['app_key'] : '';
			$this->app_secret = isset( $woocommerce_help_scout_settings['app_secret'] ) ? $woocommerce_help_scout_settings['app_secret'] : '';
			$this->mailbox_id = isset( $woocommerce_help_scout_settings['mailbox_id'] ) ? $woocommerce_help_scout_settings['mailbox_id'] : '';

			// Load plugin text domain.
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
			

			// Checks with WooCommerce is installed.
			if ( class_exists( 'WC_Integration' ) ) {
				$this->includes();

				if ( is_admin() ) {
					require_once( dirname( __FILE__ ) . '/includes/class-wc-help-scout-privacy.php' );
				}

				// Register the integration.
				add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );

				// Instantiate components if API creds are defined.
				if ( $this->are_credentials_defined() ) {
					// Register API for Help Scout APP.
					add_action( 'woocommerce_api_loaded', array( $this, 'load_api' ) );
					add_filter( 'woocommerce_api_classes', array( $this, 'add_api' ) );
					add_action( 'wp_ajax_helpscot_test_Cron', array( $this, 'helpscot_test_Cron' ) );

					$this->_components['ajax']       = new WC_Help_Scout_Ajax();
					$this->_components['my_account'] = new WC_Help_Scout_My_Account();
					$this->_components['shortcodes'] = new WC_Help_Scout_Shortcodes();
				}

				if ( is_admin() ) {
					add_action( 'admin_notices', array( $this, 'admin_notices_helpscout' ) );
				}
			} else {
				add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			}
		}
		
		/**
		 * Displays notices in admin.
		 *
		 * Error notices.
		 */
		public function admin_notices_helpscout() {

			if ( ! empty( $_POST ) ) {
				wp_verify_nonce( 'woocommerce_help-scout_nonce', 'woocommerce_help_scout_nonce' );
			}
			// Define user set variables.
			$woocommerce_help_scout_settings = get_option( 'woocommerce_help-scout_settings' );
			$app_key          = isset( $woocommerce_help_scout_settings['app_key'] ) ? $woocommerce_help_scout_settings['app_key'] : '';
			$app_secret       = isset( $woocommerce_help_scout_settings['app_secret'] ) ? $woocommerce_help_scout_settings['app_secret'] : '';
			$mailbox_id       = isset( $woocommerce_help_scout_settings['mailbox_id'] ) ? $woocommerce_help_scout_settings['mailbox_id'] : '';
			$settings_id = 'woocommerce_help-scout_';

			$post_api_key = sanitize_text_field( isset( $_POST[ $settings_id . 'api_key' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ $settings_id . 'api_key' ] ) ) : '';
			$post_mailbox_id = sanitize_text_field( isset( $_POST[ $settings_id . 'mailbox_id' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ $settings_id . 'mailbox_id' ] ) ) : '';

			if ( ( ( empty( $app_key ) || ( empty( $app_secret ) ) || empty( $mailbox_id ) ) && ! $_POST ) || ( isset( $_POST[ $settings_id . 'api_key' ] ) || isset( $_POST[ $settings_id . 'mailbox_id' ] ) && empty( $_POST[ $settings_id . 'mailbox_id' ] ) ) ) {
				$url = $this->get_settings_url_helpscout();
				/* translators: %2$s: search term */
				$spint_r = sprintf( __( '%1$sWooCommerce Help Scout is almost ready.%2$s To get started, %3$sconnect your Help Scout account%4$s and specify a Mailbox ID.', 'woocommerce-help-scout' ), '<strong>', '</strong>', '<a href="' . esc_url( $url ) . '">', '</a>' );
				echo wp_kses_post(
					'<div class="updated fade"><p>' . $spint_r . '</p></div>' . "\n",
					array(
						'div' => array( 'class' => array() ),
						'a' => array( 'href' => array() ),
						'p' => array(),
						'strong' => array(),
					)
				);
			}
		}

		/**
		 * Check if client has defined Scout API Credentials.
		 *
		 * @return boolean
		 */
		public function are_credentials_defined() {
			if ( ! empty( $this->app_key ) && ! empty( $this->app_secret ) && ! empty( $this->mailbox_id ) ) {
				return true;
			}
			return false;
		}

		/**
		 * Generate a URL to our specific settings screen.
		 *
		 * @since  1.3.4
		 * @return string Generated URL.
		 */
		public function get_settings_url_helpscout() {
			return add_query_arg(
				array(
					'page'    => 'wc-settings',
					'tab'     => 'integration',
					'section' => 'help-scout',
				),
				admin_url( 'admin.php' )
			);
		}

		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Get the plugin path.
		 *
		 * @since 1.3.0
		 *
		 * @return string Plugin path
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Includes.
		 */
		private function includes() {
			include_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-help-scout-integration.php';
			include_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-help-scout-ajax.php';
			include_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-help-scout-my-account.php';
			include_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-help-scout-shortcodes.php';
			// include_once 'includes/deprecated.php';.
		}

		/**
		 * Return the WooCommerce logger API.
		 *
		 * @return WC_Logger
		 */
		public static function get_logger() {
			global $woocommerce;

			if ( class_exists( 'WC_Logger' ) ) {
				return new WC_Logger();
			} else {
				return $woocommerce->logger();
			}
		}

		/**
		 * Load the plugin text domain for translation.
		 *
		 * @return void
		 */
		public function load_plugin_textdomain() {
			$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-help-scout' );

			load_textdomain( 'woocommerce-help-scout', trailingslashit( WP_LANG_DIR ) . 'woocommerce-help-scout/woocommerce-help-scout-' . $locale . '.mo' );
			load_plugin_textdomain( 'woocommerce-help-scout', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * WooCommerce fallback notice.
		 */
		public function woocommerce_missing_notice() {
			/* translators: %s: search term */
			echo '<div class="error"><p>' . sprintf( esc_html_e( 'WooCommerce Help Scout depends on the last version of %s to work!', 'woocommerce-help-scout' ), '<a href="https://woocommerce.com/" target="_blank">' . esc_html_e( 'WooCommerce', 'woocommerce-help-scout' ) . '</a>' ) . '</p></div>';
		}

		/**
		 * Add a new integration to WooCommerce.
		 *
		 * @param  array $integrations WooCommerce integrations.
		 *
		 * @return array               Help Scout integration.
		 */
		public function add_integration( $integrations ) {
			$integrations[] = 'WC_Help_Scout_Integration';

			return $integrations;
		}

		/**
		 * Get integration instance.
		 *
		 * @since 1.3.0
		 *
		 * @return null|WC_Help_Scout_Integration Help Scout integration instance
		 */
		public static function get_integration_instance() {
			$integrations = WC()->integrations;

			if ( is_a( $integrations, 'WC_Integrations' ) && ! empty( $integrations->integrations['help-scout'] ) ) {
				return $integrations->integrations['help-scout'];
			}

			return null;
		}

		/**
		 * Load API class.
		 *
		 * @return void
		 */
		public function load_api() {
			include_once 'includes/class-wc-help-scout-api.php';
		}

		/**
		 * Add a new API to WooCommerce.
		 *
		 * @param  array $apis WooCommerce APIs.
		 *
		 * @return array       Help Scout API.
		 */
		public function add_api( $apis ) {
			$apis[] = 'WC_Help_Scout_API';

			return $apis;
		}
		/**
		 * Uninstall plugin and delete settings.
		 */
		public function plugin_uninstall() {
			delete_option( 'woocommerce_help-scout_settings' );
			delete_option( 'helpscout_access_refresh_token' );
			delete_option( 'helpscout_expires_in' );
			wp_clear_scheduled_hook( 'my_task_hook' );
		}

		
		/**
		 * Get customer subscription info
		 *
		 * @param int $user_id user_id.
		 */
		public function has_active_subscription( $user_id ) {
			$args = array(
				'post_type' => 'shop_subscription',
				'post_status' => 'wc-active',
				'posts_per_page' => -1,
				'author' => $user_id,
			);

			$loop = new WP_Query( $args );
			$html = '';
			while ( $loop->have_posts() ) :
				$loop->the_post();
				$parent_id = wp_get_post_parent_id( get_the_ID() );
				$title = str_replace( 'Protected: ', '', get_the_title() );
				$html .= '<h4>' . $title . '</h4>';
				$html .= '<a href="' . admin_url() . 'post.php?post=' . get_the_ID() . '&action=edit" class="btn btn-primary" style="margin-bottom:10px;text-decoration:none !important">Cancel Subscription</a>';
			endwhile;
			wp_reset_postdata();
			return $html;
		}
	}

	add_action( 'plugins_loaded', array( 'WC_Help_Scout', 'get_instance' ) );
	register_uninstall_hook( __FILE__, array( 'WC_Help_Scout', 'plugin_uninstall' ) );
endif;
