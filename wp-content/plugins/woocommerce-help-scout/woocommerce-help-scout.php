<?php
/**
 * Plugin Name: WooCommerce Help Scout
 * Plugin URI: https://woocommerce.com/products/woocommerce-help-scout/
 * Description: A Help Scout integration plugin for WooCommerce.
 * Version: 2.3
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Text Domain: woocommerce-help-scout
 * Domain Path: /languages
 * Woo: 395318:1f5df97b2bc60cdb3951b72387ec2e28
 * WC tested up to: 3.6
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

define( 'WC_HELP_SCOUT_VERSION', '2.3' );
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
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		//
		register_deactivation_hook( __FILE__, array($this,'plugin_uninstall' ) );
		
		// Checks with WooCommerce is installed.
		if ( class_exists( 'WC_Integration' ) ) {
			$this->includes();

			if ( is_admin() ) {
				require_once( dirname( __FILE__ ) . '/includes/class-wc-help-scout-privacy.php' );
			}

			// Register the integration.
			add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );

			// Register API for Help Scout APP.
			add_action( 'woocommerce_api_loaded', array( $this, 'load_api' ) );
			add_filter( 'woocommerce_api_classes', array( $this, 'add_api' ) );

			// Instantiate components.
			$this->_components['ajax']       = new WC_Help_Scout_Ajax();
			$this->_components['my_account'] = new WC_Help_Scout_My_Account();
			$this->_components['shortcodes'] = new WC_Help_Scout_Shortcodes();

		} else {
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
		}
	}

	/**
	 * Return an instance of this class.
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
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
		include_once 'includes/class-wc-help-scout-integration.php';
		include_once 'includes/class-wc-help-scout-ajax.php';
		include_once 'includes/class-wc-help-scout-my-account.php';
		include_once 'includes/class-wc-help-scout-shortcodes.php';
		//include_once 'includes/deprecated.php';
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
	 *
	 * @return string
	 */
	public function woocommerce_missing_notice() {
		echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Help Scout depends on the last version of %s to work!', 'woocommerce-help-scout' ), '<a href="https://woocommerce.com/" target="_blank">' . __( 'WooCommerce', 'woocommerce-help-scout' ) . '</a>' ) . '</p></div>';
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


	function plugin_uninstall () {
		delete_option('woocommerce_help-scout_settings');
	    delete_option('helpscout_access_refresh_token');
	}
}

add_action( 'plugins_loaded', array( 'WC_Help_Scout', 'get_instance' ) );

endif;
