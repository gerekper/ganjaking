<?php
/**
 * WooCommerce Sales Report Email.
 *
 * @since 1.2.0
 */

namespace Themesquad\WC_Sales_Report_Email;

defined( 'ABSPATH' ) || exit;

use Themesquad\WC_Sales_Report_Email\Admin\Admin;
use Themesquad\WC_Sales_Report_Email\Internal\Traits\Singleton;

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
		$this->define( 'WC_SALES_REPORT_EMAIL_VERSION', '1.2.1' );
		$this->define( 'WC_SALES_REPORT_EMAIL_PATH', plugin_dir_path( WC_SALES_REPORT_EMAIL_FILE ) );
		$this->define( 'WC_SALES_REPORT_EMAIL_URL', plugin_dir_url( WC_SALES_REPORT_EMAIL_FILE ) );
		$this->define( 'WC_SALES_REPORT_EMAIL_BASENAME', plugin_basename( WC_SALES_REPORT_EMAIL_FILE ) );
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
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WC_SALES_REPORT_EMAIL_FILE, true );
		}
	}

	/**
	 * Load plugin text domain.
	 *
	 * @since 1.2.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'woocommerce-sales-report-email', false, dirname( WC_SALES_REPORT_EMAIL_BASENAME ) . '/languages' );
	}
}
