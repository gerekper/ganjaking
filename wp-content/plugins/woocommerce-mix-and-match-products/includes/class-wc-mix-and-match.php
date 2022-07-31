<?php
/**
 * The Main WC_Mix_and_Match class.
 *
 * @class    WC_Mix_and_Match
 * @package  WooCommerce Mix and Match
 * @since    1.0.0
 * @version  2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main WC_Mix_and_Match class.
 *
 * The main instance of the plugin.
 *
 * @since  1.0.0
 */
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
	public $version = '2.1.0';

	/**
	 * Required Version of WooCommerce.
	 *
	 * @var str
	 */
	public $required_woo = WC_MNM_REQUIRED_WOO;

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
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning this object is forbidden.', 'woocommerce-mix-and-match-products' ) );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of this class is forbidden.', 'woocommerce-mix-and-match-products' ) );
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
	 * @return  WC_Mix_and_Match
	 */
	public function __construct() {
		$this->initialize_plugin();
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
		return untrailingslashit( plugins_url( '/', WC_MNM_PLUGIN_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( WC_MNM_PLUGIN_FILE ) );
	}

	/**
	 * Get the plugin base path name.
	 *
	 * @since  1.2.0
	 *
	 * @return string
	 */
	public function plugin_basename() {
		return plugin_basename( WC_MNM_PLUGIN_FILE );
	}

	/**
	 * Get the file modified time as a cache buster if we're in dev mode.
	 *
	 * @since 2.0.0
	 *
	 * @param  string  $file
	 * @param  string  $version - A version number, handy for mini-extensions to make use of this method.
	 * @return string
	 */
	public function get_file_version( $file, $version = '' ) {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG && file_exists( $file ) ) {
			return filemtime( $file );
		}
		return $version ? $version : $this->version;
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

		$this->define_constants();
		$this->includes();

		// Include admin class to handle all back-end functions.
		if ( is_admin() ) {
			$this->admin_includes();
		}

		WC_Mix_and_Match_Display::get_instance();
		WC_Mix_and_Match_Order::get_instance();
		WC_MNM_Customizer::get_instance();
		WC_Mix_and_Match_Cart::get_instance();

		// Include theme-level hooks and actions files.
		$this->theme_includes();

		/**
		 * WooCommerce Mix and Match is fully loaded.
		 */
		do_action( 'wc_mnm_loaded' );

	}

	/**
	 * Constants.
	 *
	 * @since 1.10.0
	 */
	public function define_constants() {
		wc_maybe_define_constant( 'WC_MNM_ABSPATH', trailingslashit( plugin_dir_path( WC_MNM_PLUGIN_FILE ) ) );
		wc_maybe_define_constant( 'WC_MNM_VERSION', $this->version );
		wc_maybe_define_constant( 'WC_MNM_SUPPORT_URL', $this->get_resource_url( 'ticket-form' ) );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {

		// Class containing compatibility functions and filters.
		require_once 'compatibility/class-wc-mnm-compatibility.php';

		// Install.
		require_once 'class-wc-mnm-install.php';

		// Functions.
		require_once 'wc-mnm-core-functions.php';

		// Data class.
		require_once 'data/class-wc-mnm-data.php';

		// Data sync.
		require_once 'data/class-wc-mnm-db-sync.php';

		// Product price filters and price-related functions.
		require_once 'class-wc-mnm-product-prices.php';

		// Display class.
		require_once 'class-wc-mnm-display.php';

		// Child Item class.
		require_once 'class-wc-mnm-child-item.php';

		// Product class.
		require_once 'class-wc-product-mix-and-match-legacy.php';
		require_once 'class-wc-product-mix-and-match.php';

		// Cart-related functions and hooks.
		require_once 'class-wc-mnm-cart.php';

		// Stock manager class.
		require_once 'class-wc-mnm-stock-manager.php';

		// Helpers.
		require_once 'class-wc-mnm-helpers.php';

		// Include order-related functions.
		require_once 'class-wc-mnm-order.php';

		// REST API hooks.
		require_once 'api/class-wc-mnm-rest-api.php';

		// Include order-again related functions.
		require_once 'class-wc-mnm-order-again.php';

		// Customizer functions and hooks.
		require_once 'customizer/class-wc-mnm-customizer.php';

	}

	/**
	 * Admin & AJAX functions and hooks.
	 *
	 * @since 1.2.0
	 */
	public function admin_includes() {

		// Admin menus and hooks.
		require_once 'admin/class-wc-mnm-admin.php';
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
	public function theme_includes() {
		require_once 'wc-mnm-template-functions.php';
		require_once 'wc-mnm-template-hooks.php';
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

	/*-----------------------------------------------------------------------------------*/
	/*  Resources                                                                        */
	/*-----------------------------------------------------------------------------------*/


	/**
	 * Returns URL to a doc or support resource.
	 *
	 * @since  2.0.0
	 *
	 * @param  string  $handle
	 * @return string
	 */
	public function get_resource_url( $handle ) {

		switch ( $handle ) {
			case 'ticket-form':
				$resource = 'https://woocommerce.com/my-account/marketplace-ticket-form/';
			case 'unsupported-types':
				$resource = 'https://woocommerce.com/document/woocommerce-mix-and-match-products/config/#h-supported-product-types';
			case 'docs':
				$resource = 'https://woocommerce.com/document/woocommerce-mix-and-match-products/';
			case 'updating':
				$resource = 'https://woocommerce.com/document/how-to-update-woocommerce/';
			case 'outdated-templates':
				$resource = 'https://woocommerce.com/document/fix-outdated-templates-woocommerce/';
			case 'new-1.3':
				$resource = 'https://woocommerce.com/document/woocommerce-mix-and-match-products/version-1-3';
			case 'new-2.0':
				$resource = 'https://woocommerce.com/document/woocommerce-mix-and-match-products/version-2-0';
			default:
				$resource = false;
		}

		return $resource;
	}

} // End class: do not remove or there will be no more guacamole for you.
