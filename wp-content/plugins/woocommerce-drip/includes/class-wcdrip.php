<?php
/**
 * WooCommerce Drip Main Class
 *
 * @package   WooCommerce Drip
 * @author    Bryce <bryce@bryce.se>
 * @license   GPL-2.0+
 * @link      http://bryce.se
 * @copyright 2014 Bryce Adams
 * @since     1.1.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC Drip Init Main Class
 *
 * @package  WooCommerce Drip
 * @author   Bryce <bryce@bryce.se>
 * @since    1.1.4
 */

if ( ! class_exists( 'WC_Drip_Init' ) ) {

	class WC_Drip_Init {

		const VERSION = '1.1.5';

		protected static $instance = null;

		public function __construct() {

			// Add Drip Tracking Code to Footer through enqueue/localize script
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts') );

			// Load plugin text domain
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		}


		/**
		 * Start the Class when called
		 *
		 * @package  WooCommerce Drip
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
		 * Add Drip Tracking Code to Footer
		 *
		 * @package  WooCommerce Drip
		 * @author   Bryce <bryce@bryce.se>
		 * @since    1.0.0
		 */

		public function enqueue_scripts() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_script( 'drip-js', plugin_dir_url( __FILE__ ) . '../assets/js/wcdrip-drip' . $suffix . '.js', array(), false, true );

			$WC_Drip_Settings = new WC_Drip_Settings();
			$wrapper = $WC_Drip_Settings->wrapper();

			$account = $wrapper['account'];

			if ( $account ) {

				$account_id = array( 'account_id' => $account );
				wp_localize_script( 'drip-js', 'wcdrip', $account_id );
				wp_enqueue_script( 'drip-js' );

			}

		}


		/**
		 * Load plugin textdomain for i18n
		 * @TODO Add Languages base files
		 *
		 * @package  WooCommerce Drip
		 * @author   Bryce <bryce@bryce.se>
		 * @since    1.0.0
		 */

		public function load_plugin_textdomain() {

			$domain = 'woocommerce-drip';
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

		}

	}

}