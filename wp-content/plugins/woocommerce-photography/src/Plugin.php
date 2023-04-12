<?php
/**
 * WooCommerce Photography.
 *
 * @since 1.2.0
 */

namespace Themesquad\WC_Photography;

defined( 'ABSPATH' ) || exit;

use Themesquad\WC_Photography\Admin\Admin;
use Themesquad\WC_Photography\Internal\Traits\Singleton;

/**
 * Plugin class.
 */
class Plugin {

	use Singleton;

	/**
	 * Constructor.
	 *
	 * @since 1.2.0
	 */
	protected function __construct() {
		$this->define_constants();
		$this->init();
	}

	/**
	 * Define constants.
	 *
	 * @since 1.2.0
	 */
	private function define_constants() {
		$this->define( 'WC_PHOTOGRAPHY_VERSION', '1.2.0' );
		$this->define( 'WC_PHOTOGRAPHY_PATH', plugin_dir_path( WC_PHOTOGRAPHY_FILE ) );
		$this->define( 'WC_PHOTOGRAPHY_URL', plugin_dir_url( WC_PHOTOGRAPHY_FILE ) );
		$this->define( 'WC_PHOTOGRAPHY_BASENAME', plugin_basename( WC_PHOTOGRAPHY_FILE ) );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @since 1.2.0
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
	 * Init plugin.
	 *
	 * @since 1.2.0
	 */
	private function init() {
		register_activation_hook( WC_PHOTOGRAPHY_FILE, array( 'WC_Photography_Install', 'install' ) );

		add_action( 'before_woocommerce_init', array( $this, 'declare_compatibility' ) );
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'woocommerce_loaded', array( $this, 'wc_loaded' ) );

		if ( is_admin() ) {
			Admin::init();
		}
	}

	/**
	 * Declares compatibility with the WC features.
	 *
	 * @since 1.2.0
	 */
	public function declare_compatibility() {
		// Compatible with the 'High-Performance Order Storage' feature.
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WC_PHOTOGRAPHY_FILE, true );
		}
	}

	/**
	 * Load plugin text domain.
	 *
	 * @since 1.2.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'woocommerce-photography', false, dirname( WC_PHOTOGRAPHY_BASENAME ) . '/languages' );
	}

	/**
	 * Load more functionality after WC has been initialized.
	 *
	 * @since 1.2.0
	 */
	public function wc_loaded() {
		if ( class_exists( 'WC_Abstract_Privacy' ) ) {
			include_once WC_PHOTOGRAPHY_PATH . 'includes/admin/class-wc-photography-privacy.php';
		}
	}
}
