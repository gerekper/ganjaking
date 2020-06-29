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
if ( ! class_exists( 'WC_Store_Credit_Singleton' ) ) {
	require_once dirname( WC_STORE_CREDIT_FILE ) . '/includes/abstracts/abstract-wc-store-credit-singleton.php';
}

/**
 * WooCommerce Store Credit Class.
 *
 * @class WC_Store_Credit
 */
final class WC_Store_Credit extends WC_Store_Credit_Singleton {

	/**
	 * The plugin version.
	 *
	 * @var string
	 */
	public $version = '3.2.1';

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	protected function __construct() {
		parent::__construct();

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

		// Backward compatibility.
		$this->define( 'WC_STORE_CREDIT_PLUS_VERSION', $this->version );
		$this->define( 'WC_STORE_CREDIT_PLUGIN_DIR', untrailingslashit( WC_STORE_CREDIT_PATH ) );
		$this->define( 'WC_STORE_CREDIT_PLUGIN_URL', untrailingslashit( WC_STORE_CREDIT_URL ) );
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
		include_once WC_STORE_CREDIT_PATH . 'includes/wc-store-credit-functions.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-autoloader.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/wc-store-credit-functions.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-install.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-coupons.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-products.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-emails.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-order.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-order-details.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-paypal.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-rest-api.php';

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
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-cart.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-my-account.php';
		include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-product-addons.php';
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 3.0.0
	 */
	private function init_hooks() {
		register_activation_hook( WC_STORE_CREDIT_FILE, array( 'WC_Store_Credit_Install', 'install' ) );

		add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_action( 'woocommerce_loaded', array( $this, 'wc_loaded' ) );
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
	 * Load more functionality after WC has been initialized.
	 *
	 * @since 3.0.0
	 */
	public function wc_loaded() {
		if ( class_exists( 'WC_Abstract_Privacy' ) ) {
			include_once WC_STORE_CREDIT_PATH . 'includes/class-wc-store-credit-privacy.php';
		}
	}
}
