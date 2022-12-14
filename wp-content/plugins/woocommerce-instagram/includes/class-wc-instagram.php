<?php
/**
 * WooCommerce Instagram setup
 *
 * @package WC_Instagram
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Singleton pattern.
 */
if ( ! trait_exists( 'WC_Instagram_Singleton_Trait' ) ) {
	require_once dirname( WC_INSTAGRAM_FILE ) . '/includes/traits/trait-wc-instagram-singleton.php';
}

/**
 * WooCommerce Instagram Class.
 *
 * @class WC_Instagram
 */
final class WC_Instagram {

	use WC_Instagram_Singleton_Trait;

	/**
	 * The plugin version.
	 *
	 * @var string
	 */
	public $version = '4.3.3';

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	protected function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define constants.
	 *
	 * @since 2.0.0
	 */
	public function define_constants() {
		$upload_dir = wp_upload_dir( null, false );

		$this->define( 'WC_INSTAGRAM_VERSION', $this->version );
		$this->define( 'WC_INSTAGRAM_PATH', plugin_dir_path( WC_INSTAGRAM_FILE ) );
		$this->define( 'WC_INSTAGRAM_URL', plugin_dir_url( WC_INSTAGRAM_FILE ) );
		$this->define( 'WC_INSTAGRAM_BASENAME', plugin_basename( WC_INSTAGRAM_FILE ) );
		$this->define( 'WC_INSTAGRAM_CATALOGS_PATH', $upload_dir['basedir'] . '/wc-instagram-catalogs' );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @since 2.0.0
	 *
	 * @param string      $name  The constant name.
	 * @param string|bool $value The constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Includes the necessary files.
	 *
	 * @since 2.0.0
	 */
	public function includes() {
		/**
		 * Class autoloader.
		 */
		include_once WC_INSTAGRAM_PATH . 'includes/class-wc-instagram-autoloader.php';

		/**
		 * Core classes.
		 */
		include_once WC_INSTAGRAM_PATH . 'includes/wc-instagram-functions.php';
		include_once WC_INSTAGRAM_PATH . 'includes/class-wc-instagram-post-types.php';
		include_once WC_INSTAGRAM_PATH . 'includes/class-wc-instagram-product-catalogs.php';
		include_once WC_INSTAGRAM_PATH . 'includes/class-wc-instagram-backgrounds.php';
		include_once WC_INSTAGRAM_PATH . 'includes/class-wc-instagram-actions.php';
		include_once WC_INSTAGRAM_PATH . 'includes/class-wc-instagram-ajax.php';
		include_once WC_INSTAGRAM_PATH . 'includes/class-wc-instagram-router.php';
		include_once WC_INSTAGRAM_PATH . 'includes/class-wc-instagram-install.php';

		if ( wc_instagram_is_request( 'admin' ) ) {
			include_once WC_INSTAGRAM_PATH . 'includes/admin/class-wc-instagram-admin.php';
		}

		if ( wc_instagram_is_request( 'frontend' ) ) {
			include_once WC_INSTAGRAM_PATH . 'includes/wc-instagram-template-hooks.php';
		}
	}

	/**
	 * Includes the Template Functions - This makes them pluggable by plugins and themes.
	 *
	 * @since 2.0.0
	 */
	public function include_template_functions() {
		include_once WC_INSTAGRAM_PATH . 'includes/wc-instagram-template-functions.php';
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 2.0.0
	 */
	private function init_hooks() {
		register_activation_hook( WC_INSTAGRAM_FILE, array( 'WC_Instagram_Install', 'install' ) );
		register_deactivation_hook( WC_INSTAGRAM_FILE, array( 'WC_Instagram_Uninstall', 'deactivate' ) );

		add_action( 'before_woocommerce_init', array( $this, 'declare_compatibility' ) );
		add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 15 );
	}

	/**
	 * Init plugin.
	 *
	 * @since 2.0.0
	 */
	public function init() {
		// Load text domain.
		load_plugin_textdomain( 'woocommerce-instagram', false, dirname( WC_INSTAGRAM_BASENAME ) . '/languages' );

		add_filter( 'woocommerce_integrations', array( $this, 'register_integration' ) );
		add_action( 'wc_instagram_renew_access', 'wc_instagram_renew_access' );
	}

	/**
	 * Declares compatibility with the WC features.
	 *
	 * @since 4.3.1
	 */
	public function declare_compatibility() {
		// Compatible with the 'High-Performance Order Storage' feature.
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WC_INSTAGRAM_FILE, true );
		}
	}

	/**
	 * Registers the integration.
	 *
	 * @since 2.0.0
	 *
	 * @param array $integrations Array of integration instances.
	 * @return array
	 */
	public function register_integration( $integrations ) {
		include_once WC_INSTAGRAM_PATH . 'includes/class-wc-instagram-integration.php';

		$integrations[] = 'WC_Instagram_Integration';

		return $integrations;
	}

	/**
	 * Gets the WooCommerce Instagram API.
	 *
	 * @since 2.0.0
	 *
	 * @return WC_Instagram_API
	 */
	public function api() {
		return WC_Instagram_API::instance();
	}
}
