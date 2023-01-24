<?php
/**
 * WooCommerce 360° Image.
 *
 * @since 1.3.0
 */

namespace Themesquad\WC_360_Image;

defined( 'ABSPATH' ) || exit;

use Themesquad\WC_360_Image\Admin\Admin;
use Themesquad\WC_360_Image\Internal\Traits\Singleton;

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
		$this->includes();
		$this->init();
	}

	/**
	 * Define constants.
	 *
	 * @since 1.3.0
	 */
	private function define_constants() {
		$this->define( 'WC_360_IMAGE_VERSION', '1.3.0' );
		$this->define( 'WC_360_IMAGE_PATH', plugin_dir_path( WC_360_IMAGE_FILE ) );
		$this->define( 'WC_360_IMAGE_URL', plugin_dir_url( WC_360_IMAGE_FILE ) );
		$this->define( 'WC_360_IMAGE_BASENAME', plugin_basename( WC_360_IMAGE_FILE ) );
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
	 * Includes the necessary files.
	 *
	 * @since 1.3.0
	 */
	private function includes() {
		// Include plugin classes.
		require_once WC_360_IMAGE_PATH . 'includes/class-wc360-display.php';
		require_once WC_360_IMAGE_PATH . 'includes/class-wc360-shortcode.php';
		require_once WC_360_IMAGE_PATH . 'includes/class-wc360-utils.php';

		if ( is_admin() ) {
			require_once WC_360_IMAGE_PATH . 'includes/class-wc360-settings.php';
			require_once WC_360_IMAGE_PATH . 'includes/class-wc360-meta.php';
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
		add_action( 'wp', array( 'WC_360_Image_Display', 'get_instance' ) );
		add_action( 'wp', array( 'WC_360_Image_Shortcode', 'get_instance' ) );

		if ( is_admin() ) {
			Admin::init();

			\WC_360_Image_Meta::get_instance();
			\WC_360_Image_Settings::get_instance();
		}
	}

	/**
	 * Declares compatibility with the WC features.
	 *
	 * @since 1.3.0
	 */
	public function declare_compatibility() {
		// Compatible with the 'High-Performance Order Storage' feature.
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WC_360_IMAGE_FILE, true );
		}
	}

	/**
	 * Load plugin text domain.
	 *
	 * @since 1.3.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'woocommerce-360-image', false, dirname( WC_360_IMAGE_BASENAME ) . '/languages' );
	}
}
