<?php
/**
 * The Main WC_Mix_and_Match class.
 *
 * @class    WC_Mix_and_Match
 * @package  WooCommerce Mix and Match
 * @since    1.0.0
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
	public $version      = '1.11.1';

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
	 * Constants.
	 *
	 * @since 1.10.0
	 */
	public function define_constants() {
		wc_maybe_define_constant( 'WC_MNM_SUPPORT_URL', 'https://woocommerce.com/my-account/marketplace-ticket-form/' );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes(){

		// Core compatibility functions and hooks.
		require_once( 'compatibility/core/class-wc-mnm-core-compatibility.php' );

		// Install.
		require_once( 'updates/class-wc-mnm-install.php' );

		// Functions.
		require_once( 'wc-mnm-functions.php' );

		// Data class.
		require_once( 'data/class-wc-mnm-data.php' );

		// Display class.
		require_once( 'class-wc-mnm-display.php' );

		// Product class.
		require_once( 'class-wc-product-mix-and-match.php' );

		// Cart-related functions and hooks.
		require_once( 'class-wc-mnm-cart.php' );

		// Stock manager class.
		require_once( 'class-wc-mnm-stock-manager.php' );

		// Helpers.
		require_once( 'class-wc-mnm-helpers.php' );

		// Include order-related functions.
		require_once( 'class-wc-mnm-order.php' );

		// REST API hooks.
		require_once( 'class-wc-mnm-rest-api.php' );

		// Include order-again related functions.
		require_once( 'class-wc-mnm-order-again.php' );

		// Class containing extenstions compatibility functions and filters.
		require_once( 'compatibility/class-wc-mnm-compatibility.php' );

	}

	/**
	 * Admin & AJAX functions and hooks.
	 *
	 * @since 1.2.0
	 */
	public function admin_includes() {

		// Admin notices handling.
		require_once( 'admin/class-wc-mnm-admin-notices.php' );

		// Admin menus and hooks.
		require_once( 'admin/class-wc-mnm-admin.php' );
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
		require_once( 'wc-mnm-template-functions.php' );
		require_once( 'wc-mnm-template-hooks.php' );
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
