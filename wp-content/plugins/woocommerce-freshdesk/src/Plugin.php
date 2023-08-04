<?php
/**
 * WooCommerce Freshdesk.
 *
 * @since 1.2.0
 */

namespace Themesquad\WC_Freshdesk;

defined( 'ABSPATH' ) || exit;

use Themesquad\WC_Freshdesk\Admin\Admin;
use Themesquad\WC_Freshdesk\Internal\Traits\Singleton;

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
		$this->define( 'WC_FRESHDESK_VERSION', '1.3.0' );
		$this->define( 'WC_FRESHDESK_PATH', plugin_dir_path( WC_FRESHDESK_FILE ) );
		$this->define( 'WC_FRESHDESK_URL', plugin_dir_url( WC_FRESHDESK_FILE ) );
		$this->define( 'WC_FRESHDESK_BASENAME', plugin_basename( WC_FRESHDESK_FILE ) );
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
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WC_FRESHDESK_FILE, true );
		}
	}

	/**
	 * Load plugin text domain.
	 *
	 * @since 1.2.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'woocommerce-freshdesk', false, dirname( WC_FRESHDESK_BASENAME ) . '/languages' );
	}
}
