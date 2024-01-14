<?php
/**
 * WooCommerce Currency Converter Widget.
 *
 * @since 1.8.0
 */

namespace KoiLab\WC_Currency_Converter;

defined( 'ABSPATH' ) || exit;

use KoiLab\WC_Currency_Converter\Admin\Admin;
use KoiLab\WC_Currency_Converter\Internal\Traits\Singleton;

/**
 * Plugin class.
 */
class Plugin {

	use Singleton;

	/**
	 * Constructor.
	 *
	 * @since 1.8.0
	 */
	protected function __construct() {
		$this->define_constants();
		$this->init();
	}

	/**
	 * Define constants.
	 *
	 * @since 1.8.0
	 */
	private function define_constants() {
		$this->define( 'WC_CURRENCY_CONVERTER_VERSION', '2.2.1' );
		$this->define( 'WC_CURRENCY_CONVERTER_PATH', plugin_dir_path( WC_CURRENCY_CONVERTER_FILE ) );
		$this->define( 'WC_CURRENCY_CONVERTER_URL', plugin_dir_url( WC_CURRENCY_CONVERTER_FILE ) );
		$this->define( 'WC_CURRENCY_CONVERTER_BASENAME', plugin_basename( WC_CURRENCY_CONVERTER_FILE ) );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @since 1.8.0
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
	 * @since 1.8.0
	 */
	private function init() {
		add_action( 'before_woocommerce_init', array( $this, 'declare_compatibility' ) );
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'woocommerce_loaded', array( $this, 'wc_loaded' ) );
		add_filter( 'woocommerce_integrations', array( $this, 'register_integration' ) );
		add_action( 'woocommerce_checkout_init', array( '\KoiLab\WC_Currency_Converter\Checkout', 'init' ) );

		if ( is_admin() ) {
			Admin::init();
		}
	}

	/**
	 * Declares compatibility with the WC features.
	 *
	 * @since 1.8.0
	 */
	public function declare_compatibility() {
		// Compatible with the 'High-Performance Order Storage' feature.
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WC_CURRENCY_CONVERTER_FILE, true );
		}
	}

	/**
	 * Load plugin text domain.
	 *
	 * @since 1.8.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'woocommerce-currency-converter-widget', false, dirname( WC_CURRENCY_CONVERTER_BASENAME ) . '/languages' );
	}

	/**
	 * Load more functionality after WC has been initialized.
	 *
	 * @since 2.1.0
	 */
	public function wc_loaded() {
		if ( class_exists( 'WC_Abstract_Privacy' ) ) {
			new Privacy();
		}
	}

	/**
	 * Registers the integration.
	 *
	 * @since 1.8.0
	 *
	 * @param array $integrations Array of integration instances.
	 * @return array
	 */
	public function register_integration( $integrations ) {
		$integrations[] = '\KoiLab\WC_Currency_Converter\Admin\Settings\Integration';

		return $integrations;
	}
}

class_alias( Plugin::class, 'Themesquad\WC_Currency_Converter\Plugin' );
