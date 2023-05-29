<?php
/**
 * WC_PAO_Compatibility class
 *
 * @package  WooCommerce Product Add-Ons
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 3rd-party Extensions Compatibility.
 *
 * @class    WC_PAO_Compatibility
 * @version  6.3.0
 */
class WC_PAO_Compatibility {

	/**
	 * Array of min required plugin versions.
	 *
	 * @var array
	 */
	private $required = array();

	/**
	 * The single instance of the class.
	 *
	 * @var WC_PAO_Compatibility
	 *
	 * @since 6.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main WC_PAO_Compatibility instance.
	 *
	 * Ensures only one instance of WC_PAO_Compatibility is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_PAO_Compatibility
	 * @since  6.0.0
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 6.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Foul!', 'woocommerce-product-addons' ), '6.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 6.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Foul!', 'woocommerce-product-addons' ), '6.0.0' );
	}

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->required = array(
			'pb'     => '6.18.0',
			'cp'     => '8.7.0'
		);

		// Initialize.
		$this->load_modules();
	}

	/**
	 * Initialize.
	 *
	 * @since  6.0.0
	 *
	 * @return void
	 */
	protected function load_modules() {

		if ( is_admin() ) {
			// Check plugin min versions.
			add_action( 'admin_init', array( $this, 'check_required_versions' ) );
		}

	}

	/**
	 * Core compatibility functions.
	 *
	 * @since  6.1.3
	 * @return void
	 */
	public static function core_includes() {
		require_once( WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/compatibility/core/class-wc-product-addons-core-compatibility.php' );
	}

	/**
	 * Get min module version.
	 *
	 * @since  6.0.0
	 * @return bool
	 */
	public function get_required_module_version( $module ) {
		return isset( $this->required[ $module ] ) ? $this->required[ $module ] : null;
	}

	/**
	 * Checks minimum required versions of compatible/integrated extensions.
	 */
	public function check_required_versions() {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// PB version check.
		if ( class_exists( 'WC_Bundles' ) && function_exists( 'WC_PB' ) ) {
			$required_version = $this->required[ 'pb' ];
			if ( version_compare( WC_PB()->version, $required_version ) < 0 ) {
				$extension      = __( 'Product Bundles', 'woocommerce-product-addons' );
				$extension_full = __( 'WooCommerce Product Bundles', 'woocommerce-product-addons' );
				$extension_url  = 'https://woocommerce.com/products/product-bundles/';
				/* translators: %1$s: Extension, %2$s: Extension URL, %3$s: Extension full name, %4$s: Required version. */
				$notice         = sprintf( __( 'The installed version of <strong>%1$s</strong> is not supported by <strong>Product Add-ons</strong>. Please update <a href="%2$s" target="_blank">%3$s</a> to version <strong>%4$s</strong> or higher.', 'woocommerce-product-addons' ), $extension, $extension_url, $extension_full, $required_version );
				WC_PAO_Admin_Notices::add_dismissible_notice( $notice, array( 'dismiss_class' => 'pb_lt_' . $required_version, 'type' => 'warning' ) );
			}
		}

		// CI version check.
		if ( class_exists( 'WC_Composite_Products' ) && function_exists( 'WC_CP' ) ) {
			$required_version = $this->required[ 'cp' ];
			if ( version_compare( WC_CP()->version, $required_version ) < 0 ) {
				$extension      = __( 'Composite Products', 'woocommerce-product-addons' );
				$extension_full = __( 'WooCommerce Composite Products', 'woocommerce-product-addons' );
				$extension_url  = 'https://woocommerce.com/products/composite-products/';
				$notice         = sprintf( __( 'The installed version of <strong>%1$s</strong> is not supported by <strong>Product Add-ons</strong>. Please update <a href="%2$s" target="_blank">%3$s</a> to version <strong>%4$s</strong> or higher.', 'woocommerce-product-addons' ), $extension, $extension_url, $extension_full, $required_version );
				WC_PAO_Admin_Notices::add_dismissible_notice( $notice, array( 'dismiss_class' => 'cp_lt_' . $required_version, 'type' => 'warning' ) );
			}
		}
	}
}

WC_PAO_Compatibility::core_includes();
