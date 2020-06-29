<?php
/**
 * WooCommerce 360° Image Main Class
 *
 * @package   WooCommerce 360° Image
 * @author    Captain Theme <info@captaintheme.com>
 * @license   GPL-2.0+
 * @link      http://captaintheme.com
 * @copyright 2014 Captain Theme
 * @since     1.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC360 Main Class
 *
 * @package  WooCommerce 360° Image
 * @author   Captain Theme <info@captaintheme.com>
 * @since    1.0.2
 */

if ( ! class_exists( 'WC_360_Image' ) ) {

	class WC_360_Image {

		protected static $instance = null;

		private function __construct() {

			// Load plugin text domain
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			// An action link pointing to the WC settings page where the WC360 settings are
			$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . 'woocommerce-360-image.php' );
			add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		}

		/**
		 * Start the Class when called
		 *
		 * @package WooCommerce 360° Image
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 */

		public static function get_instance() {

		  	// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;

		}

		/**
		 * Add permalinks settings action link to the plugins page.
		 *
		 * @package WooCommerce 360° Image
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 */

		public function add_action_links( $links ) {

			return array_merge(
				array(
					'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=products&section=wc360' ) . '">' . __( 'Settings', 'woocommerce-360-image' ) . '</a>'
				),
				$links
			);

		}


		/**
		 * Fire when plugin is activated
		 *
		 * @package WooCommerce 360° Image
		 * @author  Captain Theme <info@captaintheme.com>
		 * @since   1.0.0
		 */

		public static function activate() {

			// If 'wc360_fullscreen_enable' not set, set it to NO
			if ( ! get_option( 'wc360_fullscreen_enable' ) ) {
			  update_option( 'wc360_fullscreen_enable', 'no' );
			}

			// If 'wc360_navigation_enable' not set, set it to YES
			if ( ! get_option( 'wc360_navigation_enable' ) ) {
			  update_option( 'wc360_navigation_enable', 'yes' );
			}

		}


		/**
		  * Load plugin textdomain for i18n
		  *
		  * @package WooCommerce 360° Image
		  * @author  Captain Theme <info@captaintheme.com>
		  * @since   1.0.0
		  */

		public function load_plugin_textdomain() {

			$domain = 'woocommerce-360-image';
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

		}

	}

}
