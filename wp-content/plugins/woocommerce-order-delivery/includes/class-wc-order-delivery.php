<?php
/**
 * WooCommerce Order Delivery setup.
 *
 * @package WC_OD
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Singleton pattern.
 */
if ( ! trait_exists( 'WC_OD_Singleton_Trait' ) ) {
	require_once dirname( WC_OD_FILE ) . '/includes/traits/trait-wc-od-singleton.php';
}

/**
 * Class WC_Order_Delivery.
 */
final class WC_Order_Delivery {

	use WC_OD_Singleton_Trait;

	/**
	 * The plugin version.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $version = '2.4.2';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define constants.
	 *
	 * @since 1.1.0
	 */
	public function define_constants() {
		$this->define( 'WC_OD_VERSION', $this->version );
		$this->define( 'WC_OD_PATH', plugin_dir_path( WC_OD_FILE ) );
		$this->define( 'WC_OD_URL', plugin_dir_url( WC_OD_FILE ) );
		$this->define( 'WC_OD_BASENAME', plugin_basename( WC_OD_FILE ) );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @since 1.1.0
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
	 * @since 1.0.0
	 */
	public function includes() {
		/**
		 * Class autoloader.
		 */
		include_once WC_OD_PATH . 'includes/class-wc-od-autoloader.php';

		/**
		 * Interfaces.
		 */
		include_once WC_OD_PATH . 'includes/interfaces/interface-wc-od-integration.php';

		/**
		 * Traits.
		 */
		include_once WC_OD_PATH . 'includes/traits/trait-wc-od-data-lockout.php';
		include_once WC_OD_PATH . 'includes/traits/trait-wc-od-data-shipping-methods.php';
		include_once WC_OD_PATH . 'includes/traits/trait-wc-od-data-fee.php';

		/**
		 * Core classes.
		 */
		include_once WC_OD_PATH . 'includes/wc-od-functions.php';
		include_once WC_OD_PATH . 'includes/class-wc-od-order-query.php';
		include_once WC_OD_PATH . 'includes/class-wc-od-install.php';
		include_once WC_OD_PATH . 'includes/class-wc-od-emails.php';
		include_once WC_OD_PATH . 'includes/class-wc-od-integrations.php';

		if ( is_admin() ) {
			include_once WC_OD_PATH . 'includes/admin/class-wc-od-admin.php';
		}
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.1.0
	 */
	private function init_hooks() {
		register_activation_hook( WC_OD_FILE, array( 'WC_OD_Install', 'install' ) );

		add_action( 'before_woocommerce_init', array( $this, 'declare_compatibility' ) );
		add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_filter( 'woocommerce_data_stores', array( $this, 'register_data_stores' ) );
	}

	/**
	 * Declares compatibility with the WC features.
	 *
	 * @since 2.3.0
	 */
	public function declare_compatibility() {
		// Not compatible with the 'High-Performance Order Storage' feature.
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WC_OD_FILE, false );
		}
	}

	/**
	 * Init plugin.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		// Load text domain.
		load_plugin_textdomain( 'woocommerce-order-delivery', false, dirname( WC_OD_BASENAME ) . '/languages' );

		// Load checkout.
		$this->checkout();

		// Load order details.
		$this->order_details();

		// Load cache.
		$this->cache();
	}

	/**
	 * Register data stores.
	 *
	 * @since 1.8.0
	 *
	 * @param array $stores Data stores.
	 * @return array
	 */
	public function register_data_stores( $stores ) {
		$stores['delivery_range'] = 'WC_OD_Data_Store_Delivery_Range';
		$stores['delivery_day']   = 'WC_OD_Data_Store_Delivery_Day';
		$stores['time_frame']     = 'WC_OD_Data_Store_Time_Frame';

		return $stores;
	}

	/**
	 * Get Settings Class.
	 *
	 * @since 1.0.0
	 *
	 * @return WC_OD_Settings
	 */
	public function settings() {
		return WC_OD_Settings::instance();
	}

	/**
	 * Get Checkout Class.
	 *
	 * @since 1.0.0
	 *
	 * @return WC_OD_Checkout
	 */
	public function checkout() {
		return WC_OD_Checkout::instance();
	}

	/**
	 * Get Order_Details Class.
	 *
	 * @since 1.0.0
	 *
	 * @return WC_OD_Order_Details
	 */
	public function order_details() {
		return WC_OD_Order_Details::instance();
	}

	/**
	 * Gets Cache Class.
	 *
	 * @return mixed
	 */
	public function cache() {
		return WC_OD_Delivery_Cache::instance();
	}
}
