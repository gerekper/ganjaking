<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Slack_Init' ) ) {
	/**
	 * WC Slack Init class.
	 *
	 * @since 1.1.0
	 * @deprecated 1.3.0
	 */
	class WC_Slack_Init {

		const VERSION = '1.1.8';

		protected static $instance = null;

		/**
		 * Construct.
		 */
		public function __construct() {
			wc_deprecated_function( __FUNCTION__, '1.3.0' );
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
		 * @since 1.0.0
		 */
		public function log( $message, $log_id = null ) {
			if ( is_null( $log_id ) ) {
				$log_id = 'wcslack';
			}

			\Themesquad\WC_Slack\Utilities\Log_Utils::log( $message, WC_Log_Levels::NOTICE, $log_id );
		}


		/**
		 * Saves errors or messages to WooCommerce Log (woocommerce/logs/plugin-id-xxx.txt)
		 *
		 * @since 1.0.0
		 *
		 * @param string $message The message we need to log.
		 */
		public function add_debug_message( $message ) {
			\Themesquad\WC_Slack\Utilities\Log_Utils::debug( $message );
		}


		/**
		 * Load plugin textdomain for i18n.
		 *
		 * @since 1.0.0
		 */
		public function load_plugin_textdomain() {}
	}

}
