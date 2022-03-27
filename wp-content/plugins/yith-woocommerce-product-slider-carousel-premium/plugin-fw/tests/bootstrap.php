<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * PHPUnit bootstrap file.
 *
 * @package YITH Plugin Framework
 */

/**
 * YITH_Plugin_FW_Unit_Tests_Bootstrap class
 */
class YITH_Plugin_FW_Unit_Tests_Bootstrap {
	/**
	 * Instance of the class
	 *
	 * @var YITH_Plugin_FW_Unit_Tests_Bootstrap
	 */
	protected static $instance = null;

	/**
	 * Directory where wordpress-tests-lib is installed.
	 *
	 * @var string
	 */
	public $wp_tests_dir;

	/**
	 * Testing directory
	 *
	 * @var string
	 */
	public $tests_dir;

	/**
	 * The plugin directory.
	 *
	 * @var string
	 */
	public $plugin_dir;

	/**
	 * WooCommerce directory.
	 *
	 * @var string
	 */
	public $woocommerce_dir;

	/**
	 * Singleton implementation
	 *
	 * @return YITH_Plugin_FW_Unit_Tests_Bootstrap
	 */
	public static function instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * Setup the unit testing environment.
	 */
	protected function __construct() {

		ini_set( 'display_errors', 'on' );
		error_reporting( E_ALL );

		// Ensure server variable is set for WP email functions.
		if ( ! isset( $_SERVER['SERVER_NAME'] ) ) {
			$_SERVER['SERVER_NAME'] = 'localhost';
		}

		$this->tests_dir       = dirname( __FILE__ );
		$this->plugin_dir      = dirname( $this->tests_dir, 2 );
		$this->woocommerce_dir = dirname( $this->plugin_dir ) . '/woocommerce';
		$this->wp_tests_dir    = getenv( 'WP_TESTS_DIR' ) ? getenv( 'WP_TESTS_DIR' ) : ( rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib' );

		define( 'YITH_PLUGIN_FRAMEWORK_TESTS_DIR', $this->tests_dir );

		if ( ! file_exists( $this->wp_tests_dir . '/includes/functions.php' ) ) {
			$this->message( "Could not find {$this->wp_tests_dir}/includes/functions.php, have you run [npm run env:install] ?" );
			exit( 1 );
		}

		require_once $this->wp_tests_dir . '/includes/functions.php';

		// load plugins.
		tests_add_filter( 'muplugins_loaded', array( $this, 'load_plugins' ) );
		tests_add_filter( 'setup_theme', array( $this, 'install_wc' ) );
		tests_add_filter( 'setup_theme', array( $this, 'show_info' ), 20 );

		// load the WP testing environment.
		require_once $this->wp_tests_dir . '/includes/bootstrap.php';

		// load testing framework.
		$this->includes();
	}

	/**
	 * Load plugins
	 *
	 * @return void
	 */
	public function load_plugins() {
		require_once $this->woocommerce_dir . '/woocommerce.php';
		require_once $this->plugin_dir . '/yith-plugin-fw-loader.php';
	}

	/**
	 * Install WooCommerce
	 *
	 * @return void
	 */
	public function install_wc() {
		// Clean existing install first.
		define( 'WP_UNINSTALL_PLUGIN', true );
		define( 'WC_REMOVE_ALL_DATA', true );
		include $this->woocommerce_dir . '/uninstall.php';

		WC_Install::install();

		// Reload capabilities after install, see https://core.trac.wordpress.org/ticket/28374.
		if ( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) ) {
			$GLOBALS['wp_roles']->reinit();
		} else {
			$GLOBALS['wp_roles'] = null;
			wp_roles();
		}

		$this->message( 'Installing WooCommerce...' );
	}

	/**
	 * Include files
	 *
	 * @return void
	 */
	public function includes() {
		$helpers_dir = YITH_PLUGIN_FRAMEWORK_TESTS_DIR . '/framework/helpers';

		require_once $helpers_dir . '/class-yith-plugin-fw-panels-helper.php';
	}

	/**
	 * Print a message
	 *
	 * @param string $message The message to be shown.
	 * @return void
	 */
	public function message( $message ) {
		echo $message . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Show installation information
	 *
	 * @return void
	 */
	public function show_info() {
		$this->message( '' );
		$this->message( 'I N S T A L L A T I O N   I N F O :' );
		$this->message( '> WP Version: ' . get_bloginfo( 'version', 'display' ) );
		$this->message( '> WC Version: ' . get_plugin_data( $this->woocommerce_dir . '/woocommerce.php' )['Version'] );
		$this->message( '> Plugin Dir: ' . $this->plugin_dir );
		$this->message( '> ABSPATH: ' . ( defined( 'ABSPATH' ) ? ABSPATH : 'not defined!' ) );
		$this->message( '' );
	}

}

YITH_Plugin_FW_Unit_Tests_Bootstrap::instance();
