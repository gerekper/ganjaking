<?php
/**
 * Extra Product Options Setup
 *
 * @package Extra Product Options
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Setup
 *
 * This class is responsible for setting up the plugin.
 *
 * @package Extra Product Options
 * @author  ThemeComplete
 * @version 6.0
 */
final class Themecomplete_Extra_Product_Options_Setup {

	/**
	 * The single instance of the class
	 *
	 * @var Themecomplete_Extra_Product_Options_Setup|null
	 */
	protected static $instance = null;

	/**
	 * Main Extra Product Options Instance
	 *
	 * Ensures only one instance of Extra Product Options is loaded or can be loaded.
	 *
	 * @since 4.8
	 * @static
	 * @return Themecomplete_Extra_Product_Options_Setup - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Cloning is forbidden
	 *
	 * @since 4.8
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is forbidden.', 'woocommerce-tm-extra-product-options' ), '4.8' );
	}

	/**
	 * Unserializing instances of this class is forbidden
	 *
	 * @since 4.8
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of this class is forbidden.', 'woocommerce-tm-extra-product-options' ), '4.8' );
	}

	/**
	 * Class Constructor
	 *
	 * @since 4.8
	 */
	public function __construct() {

		// Declare HPOS compatiblity.
		add_action( 'before_woocommerce_init', [ $this, 'before_woocommerce_init' ], 10 );

		if ( function_exists( 'wp_installing' ) && wp_installing() ) {
			return;
		}

		$this->define_constants();
		$this->includes();
		$this->init_hooks();

		do_action( 'epo_loaded' );

	}

	/**
	 * Declare HPOS compatiblity
	 *
	 * @since 6.2
	 */
	public function before_woocommerce_init() {

		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', THEMECOMPLETE_EPO_PLUGIN_FILE, true );
		}

	}

	/**
	 * Define constant if not already set
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 *
	 * @since 4.8
	 * @return void
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 *
	 * @since 4.8
	 * @return bool
	 */
	private function is_request( $type ) {
		$ret = false;
		switch ( $type ) {
			case 'admin':
				$ret = is_admin();
				break;
			case 'ajax':
				$ret = defined( 'DOING_AJAX' );
				break;
			case 'frontend':
				$ret = ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! defined( 'REST_REQUEST' );
				break;
		}

		return $ret;
	}

	/**
	 * Define constants
	 *
	 * @since 4.8
	 * @return void
	 */
	private function define_constants() {

		$version = get_file_data(
			THEMECOMPLETE_EPO_PLUGIN_FILE,
			[
				'version'      => 'Version',
				'wp_required'  => 'Requires at least',
				'wc_required'  => 'WC requires at least',
				'php_required' => 'Requires PHP',
			]
		);

		$this->define( 'THEMECOMPLETE_EPO_ABSPATH', dirname( THEMECOMPLETE_EPO_PLUGIN_FILE ) . '/' );
		$this->define( 'THEMECOMPLETE_EPO_VERSION', $version['version'] );
		$this->define( 'THEMECOMPLETE_EPO_WP_VERSION', $version['wp_required'] );
		$this->define( 'THEMECOMPLETE_EPO_WC_VERSION', $version['wc_required'] );
		$this->define( 'THEMECOMPLETE_EPO_PHP_VERSION', $version['php_required'] );
		$this->define( 'THEMECOMPLETE_EPO_PLUGIN_ID', '7908619' );
		$this->define( 'THEMECOMPLETE_EPO_LOCAL_POST_TYPE', 'tm_product_cp' );
		$this->define( 'THEMECOMPLETE_EPO_GLOBAL_POST_TYPE', 'tm_global_cp' );
		$this->define( 'THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK', 'tm-global-epo' );
		$this->define( 'THEMECOMPLETE_EPO_TEMPLATE_POST_TYPE', 'tm_template_cp' );
		$this->define( 'THEMECOMPLETE_EPO_LOOKUPTABLE_POST_TYPE', 'tm_lookuptable_cp' );
		$this->define( 'THEMECOMPLETE_EPO_WPML_LANG_META', 'tm_meta_lang' );
		$this->define( 'THEMECOMPLETE_EPO_WPML_PARENT_POSTID', 'tm_meta_parent_post_id' );
		$this->define( 'THEMECOMPLETE_EPO_PLUGIN_PATH', untrailingslashit( plugin_dir_path( THEMECOMPLETE_EPO_PLUGIN_FILE ) ) );
		$this->define( 'THEMECOMPLETE_EPO_ADMIN_PATH', THEMECOMPLETE_EPO_PLUGIN_PATH . '/admin/' );
		$this->define( 'THEMECOMPLETE_EPO_INCLUDES_PATH', THEMECOMPLETE_EPO_PLUGIN_PATH . '/includes/' );
		$this->define( 'THEMECOMPLETE_EPO_COMPATIBILITY_PATH', THEMECOMPLETE_EPO_INCLUDES_PATH . 'compatibility/' );
		$this->define( 'THEMECOMPLETE_EPO_TEMPLATE_PATH', THEMECOMPLETE_EPO_PLUGIN_PATH . '/templates/' );
		$this->define( 'THEMECOMPLETE_EPO_PLUGIN_URL', untrailingslashit( plugins_url( '/', THEMECOMPLETE_EPO_PLUGIN_FILE ) ) );
		$this->define( 'THEMECOMPLETE_EPO_INCLUDES_URL', THEMECOMPLETE_EPO_PLUGIN_URL . '/includes/' );
		$this->define( 'THEMECOMPLETE_EPO_COMPATIBILITY_URL', THEMECOMPLETE_EPO_INCLUDES_URL . 'compatibility/' );
		$this->define( 'THEMECOMPLETE_EPO_PLUGIN_NAME_HOOK', plugin_basename( THEMECOMPLETE_EPO_PLUGIN_FILE ) );
		$this->define( 'THEMECOMPLETE_EPO_ADMIN_SETTINGS_ID', 'tm_extra_product_options' );
		$this->define( 'THEMECOMPLETE_EPO_DIRECTORY', dirname( plugin_basename( THEMECOMPLETE_EPO_PLUGIN_FILE ) ) );
		$this->define( 'THEMECOMPLETE_EPO_PLUGIN_SLUG', THEMECOMPLETE_EPO_DIRECTORY . '/' . basename( THEMECOMPLETE_EPO_PLUGIN_FILE ) );
		$this->define( 'THEMECOMPLETE_EPO_FILE_SLUG', basename( THEMECOMPLETE_EPO_PLUGIN_FILE, '.php' ) );
		$this->define( 'THEMECOMPLETE_SUPPORTED_ECO_VERSION', '2.0' );
		$this->define( 'THEMECOMPLETE_EPO_NAMESPACE', 'tm-extra-product-options' );
		$this->define( 'THEMECOMPLETE_EPO_ELEMENTS_NAMESPACE', 'TM Extra Product Options' );

	}

	/**
	 * Include required core files used in admin and on the frontend
	 *
	 * @since 4.8
	 * @return void
	 */
	public function includes() {

		// Class autoloader.
		include_once THEMECOMPLETE_EPO_INCLUDES_PATH . 'class-themecomplete-epo-autoloader.php';

		// Functions.
		include_once THEMECOMPLETE_EPO_INCLUDES_PATH . 'functions/epo-functions.php';

		// Plugin compatibility functions.
		require_once THEMECOMPLETE_EPO_INCLUDES_PATH . 'functions/compatibility-functions.php';

	}

	/**
	 * Hook into actions and filters
	 *
	 * @since 4.8
	 * @return void
	 */
	private function init_hooks() {

		// Check if the plugin can be activated.
		register_activation_hook( THEMECOMPLETE_EPO_PLUGIN_FILE, [ 'THEMECOMPLETE_EPO_CHECK_Base', 'activation_check' ] );

		if ( THEMECOMPLETE_EPO_CHECK()->stop_plugin() ) {
			return;
		}

		// Initialize updater.
		THEMECOMPLETE_EPO_LICENSE();
		THEMECOMPLETE_EPO_UPDATER();

		// Load missing WooCommere functions if any.
		add_action( 'plugins_loaded', [ $this, 'wc_functions' ], 10 );

		// Load plugin textdomain.
		add_action( 'plugins_loaded', [ $this, 'load_textdomain' ], 10 );

		// Register post types.
		$priority = get_option( 'tm_epo_post_type_hook_priority' );
		if ( ! is_numeric( $priority ) ) {
			// The priority is 100 for compatibility with JetWooBuilder Custom Taxonomy Template.
			$priority = 100;
		} else {
			$priority = (int) $priority;
		}
		add_action( 'init', [ 'THEMECOMPLETE_EPO_POST_TYPES', 'register' ], $priority );

		// Load admin interface.
		if ( $this->is_request( 'admin' ) ) {

			// Add settings page.
			add_filter( 'woocommerce_get_settings_pages', [ $this, 'wc_admin_settings_page' ] );

			// woocommerce_bundle_rate_shipping chosen fix by removing.
			add_action( 'admin_enqueue_scripts', [ $this, 'fix_woocommerce_bundle_rate_shipping_scripts' ], 99 );

			// Globals Admin Interface.
			THEMECOMPLETE_EPO_ADMIN_GLOBAL();

			// Admin Interface.
			THEMECOMPLETE_EPO_ADMIN();

			// Admin Lookup Table Interface.
			THEMECOMPLETE_EPO_ADMIN_LOOKUPTABLE();

		} else {

			// Add shortcodes.
			add_action( 'init', [ 'THEMECOMPLETE_EPO_Shortcodes', 'add' ] );

		}

		// Add widgets.
		add_action( 'widgets_init', [ 'THEMECOMPLETE_EPO_Widgets', 'register' ] );

		// Main plugin interface.
		THEMECOMPLETE_EPO();

	}

	/**
	 * Required WooCommerce functions
	 *
	 * @since 4.8
	 * @return void
	 */
	public function wc_functions() {
		include_once THEMECOMPLETE_EPO_INCLUDES_PATH . 'functions/wc-functions.php';
	}

	/**
	 * Load plugin textdomain
	 *
	 * @since 4.8
	 * @return void
	 */
	public function load_textdomain() {
		if ( get_option( 'tm_epo_enable_translations', 'yes' ) === 'no' ) {
			return;
		}

		$domain     = THEMECOMPLETE_EPO_DIRECTORY;
		$locale     = apply_filters( 'plugin_locale', get_locale(), $domain );
		$global_mo  = trailingslashit( WP_LANG_DIR ) . 'plugins/' . $domain . '-' . $locale . '.mo';
		$global_mo2 = trailingslashit( WP_LANG_DIR ) . 'plugins/' . $domain . '/' . $domain . '-' . $locale . '.mo';

		if ( file_exists( $global_mo ) ) {
			// (wp-content/languages/plugins/plugin-name-$locale.mo).
			load_textdomain( $domain, $global_mo );
		} elseif ( file_exists( $global_mo2 ) ) {
			// (wp-content/languages/plugins/plugin-name/plugin-name-$locale.mo).
			load_textdomain( $domain, $global_mo2 );
		} else {
			// (wp-content/plugins/plugin-name/languages/plugin-name-$locale.mo).
			load_plugin_textdomain( 'woocommerce-tm-extra-product-options', false, $domain . '/languages/' );
		}

	}

	/**
	 * Admin Settings Page
	 *
	 * @param array<mixed> $settings Settings array.
	 * @return array<mixed>
	 * @since 4.8
	 */
	public function wc_admin_settings_page( $settings = [] ) {

		if ( class_exists( 'WC_Settings_Page' ) ) {

			$_setting = new THEMECOMPLETE_EPO_ADMIN_SETTINGS();

			if ( $_setting instanceof WC_Settings_Page ) {
				$settings[] = $_setting;
			}
		}

		return $settings;

	}

	/**
	 * Fix woocommerce_bundle_rate_shipping select chosen js conflict by removing
	 *
	 * @since 4.8
	 * @return void
	 */
	public function fix_woocommerce_bundle_rate_shipping_scripts() {
		// phpcs:ignore
		if ( ! ( isset( $_GET['page'] ) && isset( $_GET['tab'] ) && $_GET['page'] === 'wc-settings' && $_GET['tab'] === 'shipping' ) ) {
			wp_dequeue_script( 'woocommerce_bundle_rate_shipping_admin_js' );
		}
	}

}
