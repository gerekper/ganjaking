<?php
/**
 * WooCommerce Products Compare.
 *
 * @since 1.2.0
 */

namespace KoiLab\WC_Products_Compare;

defined( 'ABSPATH' ) || exit;

use KoiLab\WC_Products_Compare\Admin\Admin;
use KoiLab\WC_Products_Compare\Internal\Traits\Singleton;

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
		$this->define( 'WC_PRODUCTS_COMPARE_VERSION', '1.3.0' );
		$this->define( 'WC_PRODUCTS_COMPARE_PATH', plugin_dir_path( WC_PRODUCTS_COMPARE_FILE ) );
		$this->define( 'WC_PRODUCTS_COMPARE_URL', plugin_dir_url( WC_PRODUCTS_COMPARE_FILE ) );
		$this->define( 'WC_PRODUCTS_COMPARE_BASENAME', plugin_basename( WC_PRODUCTS_COMPARE_FILE ) );
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
		add_action( 'before_woocommerce_init', array( $this, 'declare_compatibility' ) );
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'widgets_init', array( $this, 'register_widgets' ), 20 );

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
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WC_PRODUCTS_COMPARE_FILE, true );
		}
	}

	/**
	 * Load plugin text domain.
	 *
	 * @since 1.2.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'woocommerce-products-compare', false, dirname( WC_PRODUCTS_COMPARE_BASENAME ) . '/languages' );
	}

	/**
	 * Register widgets.
	 *
	 * @since 1.3.0
	 */
	public function register_widgets() {
		if ( ! class_exists( 'WC_Widget', false ) ) {
			require_once WC_ABSPATH . '/includes/abstracts/abstract-wc-widget.php';
		}

		register_widget( '\KoiLab\WC_Products_Compare\Widget' );
	}
}

class_alias( Plugin::class, 'Themesquad\WC_Products_Compare\Plugin' );
