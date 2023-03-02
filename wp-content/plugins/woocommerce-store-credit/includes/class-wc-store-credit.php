<?php
/**
 * WooCommerce Store Credit.
 *
 * @package WC_Store_Credit
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Singleton pattern.
 */
if ( ! trait_exists( 'WC_Store_Credit_Singleton_Trait' ) ) {
	require_once dirname( WC_STORE_CREDIT_FILE ) . '/includes/traits/trait-wc-store-credit-singleton.php';
}

/**
 * WooCommerce Store Credit Class.
 */
final class WC_Store_Credit {

	use WC_Store_Credit_Singleton_Trait;

	/**
	 * The plugin version.
	 *
	 * @var string
	 */
	public $version = '4.3.0';

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	protected function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define constants.
	 *
	 * @since 3.0.0
	 */
	public function define_constants() {
		$this->define( 'WC_STORE_CREDIT_VERSION', $this->version );
		$this->define( 'WC_STORE_CREDIT_PATH', plugin_dir_path( WC_STORE_CREDIT_FILE ) );
		$this->define( 'WC_STORE_CREDIT_URL', plugin_dir_url( WC_STORE_CREDIT_FILE ) );
		$this->define( 'WC_STORE_CREDIT_BASENAME', plugin_basename( WC_STORE_CREDIT_FILE ) );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @since 3.0.0
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
	 * @since 3.0.0
	 */
	public function includes() {
		/**
		 * Interfaces.
		 */
		include_once WC_STORE_CREDIT_PATH . 'includes/interfaces/interface-wc-store-credit-integration.php';

		/**
		 * Core classes.
		 */
		include_once WC_STORE_CREDIT_PATH . 'includes/wc-store-credit-functions.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-autoloader.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/wc-store-credit-functions.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-install.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-coupons.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-products.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-emails.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-order.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-order-details.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-order-query.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-paypal.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-rest-api.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-integrations.php';

		if ( wc_store_credit_is_request( 'admin' ) ) {
			include_once WC_STORE_CREDIT_PATH . 'includes/admin/class-wc-store-credit-admin.php';
		}

		if ( wc_store_credit_is_request( 'frontend' ) ) {
			$this->frontend_includes();
		}
	}

	/**
	 * Includes required frontend files.
	 *
	 * @since 3.0.0
	 */
	public function frontend_includes() {
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-session.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-cart.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-my-account.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-product-addons.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-frontend-scripts.php';
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 3.0.0
	 */
	private function init_hooks() {
		register_activation_hook( WC_STORE_CREDIT_FILE, array( 'WC_Store_Credit_Install', 'install' ) );

		add_action( 'plugins_loaded', array( $this, 'init' ) );

		// Declare Compatibility with the WC features.
		add_action( 'before_woocommerce_init', array( $this, 'declare_compatibility' ) );

		add_action( 'woocommerce_loaded', array( $this, 'wc_loaded' ) );
		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 15 );
	}

	/**
	 * Init plugin.
	 *
	 * @since 3.0.0
	 */
	public function init() {
		// Load text domain.
		load_plugin_textdomain( 'woocommerce-store-credit', false, dirname( WC_STORE_CREDIT_BASENAME ) . '/languages' );
	}

	/**
	 * Declares compatibility with the WC features.
	 *
	 * @since 4.2.4
	 */
	public function declare_compatibility() {
		// Compatible with the 'High-Performance Order Storage' feature.
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WC_STORE_CREDIT_FILE, true );
		}
	}

	/**
	 * Load more functionality after WC has been initialized.
	 *
	 * @since 3.0.0
	 */
	public function wc_loaded() {
		if ( class_exists( 'WC_Abstract_Privacy' ) ) {
			include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-privacy.php';
		}
	}

	/**
	 * Includes the Template Functions - This makes them pluggable by plugins and themes.
	 *
	 * @since 4.2.0
	 */
	public function include_template_functions() {
		include_once WC_STORE_CREDIT_PATH . 'includes/wc-store-credit-template-functions.php';
	}
}
