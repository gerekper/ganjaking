<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC Slack Init Main Class
 *
 * @package  WooCommerce Slack
 * @author   Bryce <bryce@bryce.se>
 * @since    1.1.0
 */

if ( ! class_exists( 'WC_Slack_Init' ) ) {

	class WC_Slack_Init {

		const VERSION = '1.1.8';

		protected static $instance = null;

		public function __construct() {

			// Load plugin text domain
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		}

		/**
		 * Start the Class when called
		 *
		 * @package  WooCommerce Slack
		 * @author   Bryce <bryce@bryce.se>
		 * @since    1.0.0
		 */

		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;

		}


		/**
		 * Saves errors or messages to WooCommerce Log (woocommerce/logs/plugin-id-xxx.txt)
		 *
		 * @package  WooCommerce Slack
		 * @author   Bryce <bryce@bryce.se>
		 * @since    1.0.0
		 */

		public function log( $message, $log_id = null ) {

			if ( is_null( $log_id ) ) {
				$log_id = 'wcslack';
			}

			$logger = new WC_Logger();

			$logger->add( $log_id, $message );

		}


		/**
		 * Saves errors or messages to WooCommerce Log (woocommerce/logs/plugin-id-xxx.txt)
		 *
		 * @package  WooCommerce Slack
		 * @author   Bryce <bryce@bryce.se>
		 * @since    1.0.0
		 */

		public function add_debug_message( $message ) {

			$WC_Slack_Settings = new WC_Slack_Settings();
			$wrapper = $WC_Slack_Settings->wrapper();

			// If Debug Mode if off let's end it here
			if ( 'no' == $wrapper['debug'] ) {
				return;
			}

			$this->log( $message );

		}


		/**
		 * Load plugin textdomain for i18n
		 * @TODO Add Languages base files
		 *
		 * @package  WooCommerce Slack
		 * @author   Bryce <bryce@bryce.se>
		 * @since    1.0.0
		 */

		public function load_plugin_textdomain() {

			$domain = 'woocommerce-slack';
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

		}

	}

}