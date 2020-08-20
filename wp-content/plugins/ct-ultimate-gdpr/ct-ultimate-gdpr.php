<?php

/**
 * Plugin Name: Ultimate GDPR
 * Description: Complete General Data Protection Regulation compliance toolkit plugin for WordPress.
 * Version: 1.7.6
 * Author URI: https://www.createit.pl
 * Author: CreateIT
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class CT_Ultimate_GDPR
 *
 */
class CT_Ultimate_GDPR {

	/**
	 * Plugin text-domain
	 */
	const DOMAIN = 'ct-ultimate-gdpr';

	/**
	 * @var $this
	 */
	private static $instance;

	/**
	 * @var CT_Ultimate_GDPR_Controller_Admin
	 */
	private $admin_controller;
	/**
	 * @var array
	 */
	private $controllers;

	/**
	 * @var CT_Ultimate_GDPR_Model_Logger
	 */
	private $logger;

	/**
	 * Singleton
	 *
	 * @return CT_Ultimate_GDPR
	 */
	public static function instance() {

		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

	/**
	 * CT_Ultimate_GDPR constructor.
	 */
	private function __construct() {

		$this->register_autoload();
		$this->include_helpers();
		$this->include_integration();
		$this->include_acf();
		$this->logger = new CT_Ultimate_GDPR_Model_Logger();

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts_action' ) );
		add_action( 'wp', array( $this, 'controller_actions' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'ct_ultimate_gdpr_after_controllers_registered', array( $this, 'update_plugin_database' ) );

	}

	/**
	 * Run on init
	 */
	public function init() {

		$this->include_acf_fields();
		$this->register_controllers();
		$this->register_shortcodes();
		$this->register_services();

	}

	/**
	 * Include helpers
	 */
	private function include_helpers() {
		require_once __DIR__ . "/includes/helpers.php";
	}

	/**
	 *
	 */
	private function include_acf() {
		include_once plugin_dir_path(__FILE__) . 'vendor/acf/acf-filters.php';
	}

	/**
	 *
	 */
	private function include_acf_fields() {
		include plugin_dir_path(__FILE__) . 'vendor/acf-fields.php';
	}

	/**
	 * Register autoloader
	 */
	private function register_autoload() {

		if ( is_readable( __DIR__ . "/vendor/autoload.php" ) ) {
			require_once __DIR__ . "/vendor/autoload.php";
		} else {
			spl_autoload_register( array( $this, 'autoload' ) );
		}

	}

	/**
	 * Custom class loader
	 *
	 * @param $classname
	 */
	public function autoload( $classname ) {

		if ( 0 !== stripos( $classname, 'ct_ultimate_gdpr' ) ) {
			return;
		}

		$normalized = str_ireplace( 'ct_ultimate_gdpr_', '', $classname );
		$normalized = str_replace( '_', '-', strtolower( $normalized ) );
		$path       = __DIR__ . "/includes";
		$namespaces = array( 'controller', 'service', 'shortcode', 'model', 'update' );

		foreach ( $namespaces as $namespace ) {

			if ( 0 === strpos( $normalized, "$namespace-" ) ) {
				$path .= "/$namespace";
			}

		}

		require_once $path . "/$normalized.php";

	}

	/**
	 * Run all regitered controllers actions
	 */
	public function controller_actions() {

		/** @var CT_Ultimate_GDPR_Controller_Abstract $controller */
		foreach ( $this->controllers as $controller ) {

			if ( is_admin() ) {
				$controller->admin_action();
			} else {
				$controller->front_action();
			}

		}

	}

	/**
	 * Register all default and custom controllers
	 */
	private function register_controllers() {

		$this->admin_controller = new CT_Ultimate_GDPR_Controller_Admin();

		foreach (
			array(
				new CT_Ultimate_GDPR_Controller_Cookie( $this->logger ),
				new CT_Ultimate_GDPR_Controller_Terms( $this->logger ),
				new CT_Ultimate_GDPR_Controller_Policy( $this->logger ),
				new CT_Ultimate_GDPR_Controller_Forgotten( $this->logger ),
				new CT_Ultimate_GDPR_Controller_Data_Access( $this->logger ),
				new CT_Ultimate_GDPR_Controller_Breach( $this->logger ),
				new CT_Ultimate_GDPR_Controller_Rectification( $this->logger ),
				new CT_Ultimate_GDPR_Controller_Unsubscribe( $this->logger ),
				new CT_Ultimate_GDPR_Controller_Services( $this->logger ),
				new CT_Ultimate_GDPR_Controller_Pseudonymization( $this->logger ),
				new CT_Ultimate_GDPR_Controller_Plugins( $this->logger ),
			) as $controller
		) {

			/** @var CT_Ultimate_GDPR_Controller_Abstract $controller */
			$this->controllers[ $controller->get_id() ] = $controller->set_options( $this->admin_controller->get_options( $controller->get_id() ) );
			$controller->init();

		}

		$controllers = apply_filters( 'ct_ultimate_gdpr_controllers', array() );

		foreach ( $controllers as $controller ) {

			if ( $controller instanceof CT_Ultimate_GDPR_Controller_Interface ) {
				$this->controllers[ $controller->get_id() ] = $controller;
			}

		}

		do_action( 'ct_ultimate_gdpr_after_controllers_registered', $this->controllers );

	}

	/**
	 * Register all shortcodes
	 */
	private function register_shortcodes() {
		new CT_Ultimate_GDPR_Shortcode_Cookie_Popup;
		new CT_Ultimate_GDPR_Shortcode_Myaccount();
		new CT_Ultimate_GDPR_Shortcode_Terms_Accept();
		new CT_Ultimate_GDPR_Shortcode_Policy_Accept();
		new CT_Ultimate_GDPR_Shortcode_Privacy_Center();
		new CT_Ultimate_GDPR_Shortcode_Privacy_Policy();
		new CT_Ultimate_GDPR_Shortcode_Protection();
	}

	/**
	 * Add scripts
	 */
	public function wp_enqueue_scripts_action() {
		wp_enqueue_style( 'ct-ultimate-gdpr', ct_ultimate_gdpr_url( '/assets/css/style.min.css' ), array(), ct_ultimate_gdpr_get_plugin_version() );
		wp_enqueue_style( 'ct-ultimate-gdpr-font-awesome', ct_ultimate_gdpr_url( '/assets/css/fonts/font-awesome/css/font-awesome.min.css' ) );
	}

	/**
	 * @return CT_Ultimate_GDPR_Controller_Admin
	 */
	public function get_admin_controller() {
		return $this->admin_controller;
	}

	/**
	 * Register all services
	 */
	public function register_services() {
		CT_Ultimate_GDPR_Model_Services::instance()
		                               ->set_front_controller( $this )
		                               ->get_services();
	}

	/**
	 * Get a controller
	 *
	 * @param $id
	 *
	 * @return mixed|null
	 */
	public function get_controller_by_id( $id ) {
		return isset( $this->controllers[ $id ] ) ? $this->controllers[ $id ] : null;
	}

	/**
	 * Get a controller
	 *
	 * @param $phrase
	 *
	 * @return CT_Ultimate_GDPR_Controller_Abstract|null
	 */
	public function find_controller( $phrase ) {

		if ( $controller = $this->get_controller_by_id( $phrase ) ) {
			return $controller;
		}

		foreach ( $this->controllers as $id => $controller ) {
			if ( false !== stripos( $id, $phrase ) ) {
				return $controller;
			}
		}

		return null;
	}

	/**
	 * Load plugin text domain
	 */
	public function load_textdomain() {

		$locale    = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale    = apply_filters( 'ct_ultimate_gdpr_locale', $locale );
		$lang_file = trailingslashit( WP_LANG_DIR ) . 'plugins/' . self::DOMAIN . "-$locale.mo";

		load_textdomain( self::DOMAIN, $lang_file );
		load_plugin_textdomain( self::DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

	}

	/**
	 * Load user defined cookies to posts
	 */
	public function update_plugin_database() {
		$obj = new CT_Ultimate_GDPR_Update_Legacy_Options();
		$obj->run_updater();
	}

	/**
	 * Load default predefined services to posts
	 */
	public function update_cookie_manager_posts() {
		$obj = new CT_Ultimate_GDPR_Update_Legacy_Options();
		$obj->update_cookie_manager_posts();
	}

	/**
	 * Integrations with external plugins
	 */
	private function include_integration() {

		$path = __DIR__ . '/includes/integration/compatibility.php' ;
		include_once $path;

		$path = __DIR__ . '/includes/integration/deep.php' ;
		include_once $path;

		$path = __DIR__ . '/includes/integration/wp-rocket.php' ;
		include_once $path;

		$path = __DIR__ . '/includes/integration/polylang.php' ;
		include_once $path;

		if ( file_exists( __DIR__ . "/vendor/optimus-prime-plugin-update/load.php" ) ) {
			require_once __DIR__ . "/vendor/optimus-prime-plugin-update/load.php";
		}

	}

	/**
	 * @return array
	 */
	public function get_controllers(  ) {
		return $this->controllers;
	}


}

CT_Ultimate_GDPR::instance();