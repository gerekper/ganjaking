<?php
/**
 * WooCommerce Force Sells.
 *
 * @since 1.3.0
 */

namespace KoiLab\WC_Force_Sells;

defined( 'ABSPATH' ) || exit;

use KoiLab\WC_Force_Sells\Admin\Admin;
use KoiLab\WC_Force_Sells\Internal\Traits\Singleton;

/**
 * Plugin class.
 */
class Plugin {

	use Singleton;

	/**
	 * Constructor.
	 *
	 * @since 1.3.0
	 */
	protected function __construct() {
		$this->define_constants();
		$this->init();
	}

	/**
	 * Define constants.
	 *
	 * @since 1.3.0
	 */
	private function define_constants() {
		$this->define( 'WC_FORCE_SELLS_VERSION', '1.3.0' );
		$this->define( 'WC_FORCE_SELLS_PATH', plugin_dir_path( WC_FORCE_SELLS_FILE ) );
		$this->define( 'WC_FORCE_SELLS_URL', plugin_dir_url( WC_FORCE_SELLS_FILE ) );
		$this->define( 'WC_FORCE_SELLS_BASENAME', plugin_basename( WC_FORCE_SELLS_FILE ) );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @since 1.3.0
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
	 * @since 1.3.0
	 */
	private function init() {
		add_action( 'before_woocommerce_init', array( $this, 'declare_compatibility' ) );
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		Cart::init();

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
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WC_FORCE_SELLS_FILE, true );
		}
	}

	/**
	 * Load plugin text domain.
	 *
	 * @since 1.3.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'woocommerce-force-sells', false, dirname( WC_FORCE_SELLS_BASENAME ) . '/languages' );
	}
}
