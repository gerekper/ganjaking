<?php
/**
 * Plugin Name: WooCommerce Mix and Match Products
 * Plugin URI: http://www.woocommerce.com/products/woocommerce-mix-and-match-products/
 * Description: Allow customers to choose products in any combination to fill a "container" of a specific size.
 * Version: 1.9.13
 * Author: Kathy Darling
 * Author URI: http://kathyisawesome.com/
 * Woo: 853021:e59883891b7bcd535025486721e4c09f
 * WC requires at least: 3.1.0
 * WC tested up to: 4.3.0
 *
 * Text Domain: woocommerce-mix-and-match-products
 * Domain Path: /languages
 *
 * @author Kathy Darling
 * @category Core
 * @package WooCommerce Mix and Match
 *
 * Copyright: © 2015 Kathy Darling and Manos Psychogyiopoulos
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 * Main WC_Mix_and_Match class.
 *
 * The main instance of the plugin.
 * 
 * @since  1.0.0
 */
if ( ! class_exists( 'WC_Mix_and_Match' ) ) :

	class WC_Mix_and_Match {

		/**
		 * The single instance of the class.
		 * 
		 * @var obj The WC_Mix_and_Match object
		 */
		protected static $_instance = null;

		/**
		 * Plugin Version.
		 * 
		 * @var str
		 */
		public $version      = '1.9.13';

		/**
		 * Required Version of WooCommerce.
		 * 
		 * @var str
		 */
		public $required_woo = '3.1.0';

		/**
		 * Main WC_Mix_and_Match instance.
		 *
		 * Ensures only one instance of WC_Mix_and_Match is loaded or can be loaded.
		 *
		 * @return WC_Mix_and_Match Single instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Cloning is forbidden.
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cloning this object is forbidden.', 'woocommerce-mix-and-match-products' ) );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'woocommerce-mix-and-match-products' ) );
		}

		/**
		 * Auto-load in-accessible properties.
		 *
		 * @param  mixed  $key
		 * @return mixed
		 */
		public function __get( $key ) {
			if ( in_array( $key, array( 'compatibility', 'cart', 'order', 'display' ) ) ) {
				$classname = 'WC_Mix_and_Match_' . ucfirst( $key );
				return call_user_func( array( $classname, 'get_instance' ) );
			}
		}

		/**
		 * WC_Mix_and_Match Constructor
		 *
		 * @return 	WC_Mix_and_Match
		 */
		public function __construct() {
			// Entry point.
			add_action( 'plugins_loaded', array( $this, 'initialize_plugin' ) );
		}

		/*-----------------------------------------------------------------------------------*/
		/*  Helper Functions                                                                 */
		/*-----------------------------------------------------------------------------------*/

		/**
		 * Get the plugin url.
		 *
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Get the plugin base path name.
		 *
		 * @since  1.2.0
		 * 
		 * @return string
		 */
		public function plugin_basename() {
			return plugin_basename( __FILE__ );
		}

		/*-----------------------------------------------------------------------------------*/
		/*  Load Files                                                                       */
		/*-----------------------------------------------------------------------------------*/

		/**
		 * Fire in the hole!
		 */
		public function initialize_plugin() {

			// Load translation files.
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );	

			// WC version sanity check.
			if ( ! defined( 'WC_VERSION' ) || version_compare( WC_VERSION, $this->required_woo, '<' ) ) {
				// translators: %1$s is opening link to WordPress plugin site. 2$s is closing link. %3$s is minimum version of WooCommerce.
				$notice = sprintf( __( '<strong>WooCommerce Mix and Match is inactive.</strong> The %1$sWooCommerce plugin%2$s must be active and at least version %3$s for Mix and Match to function. Please upgrade or activate WooCommerce.', 'woocommerce-mix-and-match-products' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', $this->required_woo );
				require_once( 'includes/admin/class-wc-mnm-admin-notices.php' );
				WC_MNM_Admin_Notices::add_notice( $notice, 'error' );
				return false;
			}

			// PHP version check.
			if ( ! function_exists( 'phpversion' ) || version_compare( phpversion(), '5.6.20', '<' ) ) {
				// translators: %1$s is minimum version of PHP required. %2$s is URL to how to update PHP documentation.
				$notice = sprintf( __( 'WooCommerce Mix and Match requires at least PHP <strong>%1$s</strong>. Learn <a href="%2$s">how to update PHP</a>.', 'woocommerce-mix-and-match-products' ), '5.6.20', 'https://docs.woocommerce.com/document/how-to-update-your-php-version/' );
				require_once( 'includes/admin/class-wc-mnm-admin-notices.php' );
				WC_MNM_Admin_Notices::add_notice( $notice, 'error' );
				return false;
			}

			$this->includes();

			// Include admin class to handle all back-end functions.
			if( is_admin() ){
				$this->admin_includes();
			}

			WC_Mix_and_Match_Display::get_instance();
			WC_Mix_and_Match_Order::get_instance();
			WC_Mix_and_Match_Compatibility::get_instance();

			// Include the cart module.
			if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
				WC_Mix_and_Match_Cart::get_instance();
			}

			// Include theme-level hooks and actions files.
			add_action( 'after_setup_theme', array( $this, 'theme_includes' ) );

			/**
			 * WooCommerce Mix and Match is fully loaded.
			 */
			do_action( 'woocommerce_mnm_loaded' );

		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes(){

			// Core compatibility functions and hooks.
			require_once( 'includes/compatibility/core/class-wc-mnm-core-compatibility.php' );

			// Install.
			require_once( 'includes/updates/class-wc-mnm-install.php' );

			// Functions.
			require_once( 'includes/wc-mnm-functions.php' );

			// Data class.
			require_once( 'includes/data/class-wc-mnm-data.php' );

			// Display class.
			require_once( 'includes/class-wc-mnm-display.php' );

			// Product class.
			require_once( 'includes/class-wc-product-mix-and-match.php' );
		
			// Cart-related functions and hooks.
			require_once( 'includes/class-wc-mnm-cart.php' );

			// Stock manager class.
			require_once( 'includes/class-wc-mnm-stock-manager.php' );

			// Helpers.
			require_once( 'includes/class-wc-mnm-helpers.php' );

			// Include order-related functions.
			require_once( 'includes/class-wc-mnm-order.php' );

		
			// Include order-again related functions.
			require_once( 'includes/class-wc-mnm-order-again.php' );

			// Class containing extenstions compatibility functions and filters.
			require_once( 'includes/compatibility/class-wc-mnm-compatibility.php' );
		
		}

		/**
		 * Admin & AJAX functions and hooks.
		 *
		 * @since 1.2.0
		 */
		public function admin_includes() {

			// Admin notices handling.
			require_once( 'includes/admin/class-wc-mnm-admin-notices.php' );

			// Admin menus and hooks.
			require_once( 'includes/admin/class-wc-mnm-admin.php' );
		}


		/**
		 * Displays a warning message if version check fails.
		 *
		 * @return string
		 */
		public function admin_notice() {
			wc_deprecated_function( 'WC_Mix_and_Match::admin_notice()', '1.6.0', 'Function is no longer used.' );
			// translators: %1$s is opening link to WordPress plugin site. 2$s is closing link. %3$s is minimum version of WooCommerce.
			echo '<div class="error"><p>' . sprintf( __( '<strong>WooCommerce Mix and Match is inactive.</strong> The %1$sWooCommerce plugin%2$s must be active and at least version %3$s for Mix and Match to function. Please upgrade or activate WooCommerce.', 'woocommerce-mix-and-match-products' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', $this->required_woo ) . '</p></div>';
		}

		/**
		 * Include template functions and hooks.
		 */
		public function theme_includes(){
			require_once( 'includes/wc-mnm-template-functions.php' );
			require_once( 'includes/wc-mnm-template-hooks.php' );
		}

		/*-----------------------------------------------------------------------------------*/
		/*  Localization                                                                     */
		/*-----------------------------------------------------------------------------------*/

		/**
		 * Make the plugin translation ready.
		 *
		 * Translations should be added in the WordPress language directory:
		 *      - WP_LANG_DIR/plugins/woocommerce-mix-and-match-products-LOCALE.mo
		 *
		 * @since  1.0.0
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'woocommerce-mix-and-match-products', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

	} // End class: do not remove or there will be no more guacamole for you.

endif; // End class_exists check.


/**
 * Returns the main instance of WC_Mix_and_Match to prevent the need to use globals.
 *
 * @return WooCommerce
 */
function WC_Mix_and_Match() {
	return WC_Mix_and_Match::instance();
}

// Launch the whole plugin.
WC_Mix_and_Match();
