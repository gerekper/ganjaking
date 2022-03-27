<?php
/**
* Plugin Name: WooCommerce All Products For Subscriptions
* Plugin URI: https://woocommerce.com/products/all-products-for-woocommerce-subscriptions/
* Description: Make existing products available on subscription, and give customers the freedom to add products to their existing subscriptions. WooCommerce Subscriptions add-on formerly known as Subscribe All The Things.
* Version: 3.2.1
* Author: WooCommerce
* Author URI: https://somewherewarm.com/
*
* Woo: 3978176:b0e6e19cf767e4fb9ca7fe9b0ff2c381
*
* Text Domain: woocommerce-all-products-for-subscriptions
* Domain Path: /languages/
*
* Requires PHP: 5.6
*
* Requires at least: 4.4
* Tested up to: 5.9
*
* WC requires at least: 3.3
* WC tested up to: 6.3
*
* License: GNU General Public License v3.0
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
*
* @package  WooCommerce All Products For Subscriptions
* @author   SomewhereWarm
* @since    2.2.0
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WCS_ATT' ) ) :

// Abstract modules container class.
require_once( 'includes/modules/abstract/class-wcs-att-abstract-module.php' );

/**
 * Main plugin class.
 *
 * @class    WCS_ATT
 * @version  3.2.1
 */
class WCS_ATT extends WCS_ATT_Abstract_Module {

	/* Plugin version. */
	const VERSION = '3.2.1';

	/* Required WC version. */
	const REQ_WC_VERSION = '3.3.0';

	/* Required WC version. */
	const REQ_WCS_VERSION = '2.3.0';

	/* Required WC Payments version. */
	const REQ_WCPAY_VERSION = '3.2.0';

	/* Docs URL. */
	const DOCS_URL = 'https://woocommerce.com/document/all-products-for-woocommerce-subscriptions/';

	/* Support URL. */
	const SUPPORT_URL = 'https://woocommerce.com/my-account/marketplace-ticket-form/';

	/* WCS URL. */
	const WCS_URL = 'https://woocommerce.com/products/woocommerce-subscriptions/';

	/* WC Payments. */
	const WCPAY_URL = 'https://woocommerce.com/products/woocommerce-payments/';

	/* WC Payments settings page. */
	const WCPAY_ADMIN_URL = '/wp-admin/admin.php?page=wc-settings&tab=checkout&section=woocommerce_payments';

	/**
	 * @var WCS_ATT - the single instance of the class.
	 *
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main WCS_ATT Instance.
	 *
	 * Ensures only one instance of WCS_ATT is loaded or can be loaded.
	 *
	 * @static
	 * @see WCS_ATT()
	 * @return WCS_ATT - Main instance
	 * @since 1.0.0
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
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-all-products-for-subscriptions' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-all-products-for-subscriptions' ), '1.0.0' );
	}

	/**
	 * Do some work.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 15 );
	}

	/**
	 * The plugin URL.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) );
	}

	/**
	 * The plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Plugin version getter.
	 *
	 * @since  2.4.0
	 *
	 * @param  boolean  $base
	 * @param  string   $version
	 * @return string
	 */
	public function plugin_version( $base = false, $version = '' ) {

		$version = $version ? $version : self::VERSION;

		if ( $base ) {
			$version_parts = explode( '-', $version );
			$version       = count( $version_parts ) > 1 ? $version_parts[ 0 ] : $version;
		}

		return $version;
	}

	/**
	 * Indicates whether the plugin has been fully initialized.
	 *
	 * @since  3.1.7
	 *
	 * @return boolean
	 */
	public function plugin_initialized() {
		return class_exists( 'WCS_ATT_Helpers' );
	}

	/**
	 * Define constants if not present.
	 *
	 * @since  3.1.7
	 *
	 * @return boolean
	 */
	protected function maybe_define_constant( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Plugin base path name getter.
	 *
	 * @return string
	 */
	public function plugin_basename() {
		return plugin_basename( __FILE__ );
	}

	/**
	 * Bootstrap.
	 */
	public function plugins_loaded() {

		$this->define_constants();

		$notice = '';

		// Subs version check.
		if ( class_exists( 'WC_Subscriptions' ) ) {

			// Notice about plugin version when WooCommerce Subscriptions is active.
			if ( ! defined( 'WCS_INIT_TIMESTAMP' ) || version_compare( WC_Subscriptions::$version, self::REQ_WCS_VERSION ) < 0  ) {
				$notice = sprintf( __( 'All Products for WooCommerce Subscriptions requires at least <a href="%1$s" target="_blank">WooCommerce Subscriptions</a> version <strong>%2$s</strong>.', 'woocommerce-all-products-for-subscriptions' ), self::WCS_URL, self::REQ_WCS_VERSION );
			}

		} elseif ( class_exists( 'WC_Payments' ) && class_exists( 'WC_Payments_Features' ) ) {

			if ( ! defined( 'WCS_INIT_TIMESTAMP' ) || ! WC_Payments_Features::is_wcpay_subscriptions_enabled() ) {
				if ( version_compare( WCPAY_VERSION_NUMBER, self::REQ_WCPAY_VERSION ) < 0 ) {
					// Notice about plugin version when WooCommerce Payments is active.
					$notice = sprintf( __( 'All Products for WooCommerce Subscriptions requires at least <a href="%1$s" target="_blank">WooCommerce Payments</a> version <strong>%2$s</strong>.', 'woocommerce-all-products-for-subscriptions' ), self::WCPAY_URL, self::REQ_WCPAY_VERSION );
				} else {
					// Notice about disabled Subscription features in WooCommerce Payments.
					$notice = sprintf( __( 'All Products for WooCommerce Subscriptions requires Subscriptions to be enabled in the <strong>WooCommerce Payments</strong> <a href="%1$s" target="_blank">settings</a>.', 'woocommerce-all-products-for-subscriptions' ), site_url( self::WCPAY_ADMIN_URL ) );
				}
			}
		} else {
			// Notice about disabled Subscriptions core not being loaded.
			$notice = sprintf( __( 'All Products for WooCommerce Subscriptions requires at least <a href="%1$s" target="_blank">WooCommerce Payments</a> version <strong>%2$s</strong> or <a href="%3$s" target="_blank">WooCommerce Subscriptions</a> version <strong>%4$s</strong>.', 'woocommerce-all-products-for-subscriptions' ), self::WCPAY_URL, self::REQ_WCPAY_VERSION, self::WCS_URL, self::REQ_WCS_VERSION );
		}

		if ( ! empty ( $notice ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/admin/class-wcs-att-admin-notices.php' );
			WCS_ATT_Admin_Notices::add_notice( $notice, 'error' );
			return false;
		}

		// PHP version check.
		if ( ! function_exists( 'phpversion' ) || version_compare( phpversion(), '5.6.20', '<' ) ) {
			$notice = sprintf( __( 'All Products for WooCommerce Subscriptions requires at least PHP <strong>%1$s</strong>. Learn <a href="%2$s">how to update PHP</a>.', 'woocommerce-all-products-for-subscriptions' ), '5.6.20', 'https://woocommerce.com/document/how-to-update-your-php-version/' );
			require_once( WCS_ATT_ABSPATH . 'includes/admin/class-wcs-att-admin-notices.php' );
			WCS_ATT_Admin_Notices::add_notice( $notice, 'error' );
			return false;
		}

		// WC 3.0+ check.
		if ( ! function_exists( 'WC' ) || version_compare( WC()->version, self::REQ_WC_VERSION ) < 0 ) {
			$notice = __( 'All Products for WooCommerce Subscriptions requires at least WooCommerce version <strong>%1$s</strong>. %2$s', 'woocommerce-all-products-for-subscriptions' );
			if ( ! function_exists( 'WC' ) ) {
				$notice = sprintf( $notice, self::REQ_WC_VERSION, __( 'Please install and activate WooCommerce.', 'woocommerce-all-products-for-subscriptions' ) );
			} else {
				$notice = sprintf( $notice, self::REQ_WC_VERSION, __( 'Please update WooCommerce.', 'woocommerce-all-products-for-subscriptions' ) );
			}
			require_once( WCS_ATT_ABSPATH . 'includes/admin/class-wcs-att-admin-notices.php' );
			WCS_ATT_Admin_Notices::add_notice( $notice, 'error' );
			return false;
		}

		// Add init hooks.
		add_action( 'init', array( $this, 'init_textdomain' ) );
		add_action( 'admin_init', array( $this, 'activate' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_meta_links' ), 10, 4 );

		$this->includes();
	}

	/**
	 * Define constants.
	 *
	 * @return void
	 */
	protected function define_constants() {
		$this->maybe_define_constant( 'WCS_ATT_VERSION', self::VERSION );
		$this->maybe_define_constant( 'WCS_ATT_ABSPATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
	}

	/**
	 * Include plugin files.
	 *
	 * @return void
	 */
	public function includes() {

		// Classes.
		require_once( WCS_ATT_ABSPATH . 'includes/class-wcs-att-core-compatibility.php' );
		require_once( WCS_ATT_ABSPATH . 'includes/class-wcs-att-integrations.php' );
		require_once( WCS_ATT_ABSPATH . 'includes/class-wcs-att-helpers.php' );
		require_once( WCS_ATT_ABSPATH . 'includes/class-wcs-att-scheme.php' );
		require_once( WCS_ATT_ABSPATH . 'includes/class-wcs-att-product.php' );
		require_once( WCS_ATT_ABSPATH . 'includes/class-wcs-att-cart.php' );
		require_once( WCS_ATT_ABSPATH . 'includes/class-wcs-att-order.php' );
		require_once( WCS_ATT_ABSPATH . 'includes/class-wcs-att-sync.php' );

		// Modules.
		$this->register_modules();
		$this->initialize_modules();

		// Load display components.
		require_once( WCS_ATT_ABSPATH . 'includes/class-wcs-att-display.php' );
		$this->register_component_hooks( 'display' );

		// Load form handling components.
		$this->register_component_hooks( 'form' );

		// Legacy stuff.
		require_once( WCS_ATT_ABSPATH . 'includes/legacy/class-wcs-att-schemes.php' );

		// Admin includes.
		if ( is_admin() ) {
			$this->admin_includes();
		}
	}

	/**
	 * Include submodules.
	 *
	 * @since  2.1.0
	 *
	 * @return void
	 */
	protected function register_modules() {
		$modules = array();

		/*
		 * Important: Switching Subscriptions and adding products/carts to existing Subscriptions
		 * is only available with WooCommerce Subscriptions. These features are disabled when
		 * Subscriptions core is loaded via WooCommerce Payments.
		 *
		 * See: https://woocommerce.com/document/payments/subscriptions/comparison/#feature-matrix
		 */
		if ( class_exists( 'WC_Subscriptions_Switcher' ) && class_exists( 'WC_Subscriptions' ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/modules/class-wcs-att-management.php' );
			$modules = array( 'manage' => 'WCS_ATT_Management' );
		}
		$this->modules = apply_filters( 'wcsatt_modules', $modules );
	}

	/**
	 * Register all module hooks associated with a named SATT component.
	 *
	 * @since  2.1.0
	 *
	 * @param  string  $component
	 */
	protected function register_component_hooks( $component ) {

		foreach ( $this->modules as $module ) {
			$module->register_hooks( $component );
		}
	}

	/**
	 * Loads the Admin & AJAX filters / hooks.
	 *
	 * @return void
	 */
	public function admin_includes() {
		// Admin notices handling.
		require_once( WCS_ATT_ABSPATH . 'includes/admin/class-wcs-att-admin-notices.php' );
		// Addmin settings/metaboxes.
		require_once( WCS_ATT_ABSPATH . 'includes/admin/class-wcs-att-admin.php' );
	}

	/**
	 * Load textdomain.
	 *
	 * @return void
	 */
	public function init_textdomain() {
		load_plugin_textdomain( 'woocommerce-all-products-for-subscriptions', false, dirname( $this->plugin_basename() ) . '/languages/' );
	}

	/**
	 * Store plugin version.
	 *
	 * @return void
	 */
	public function activate() {

		$version = get_option( 'apfs_version', false );

		if ( ! $version ) {
			WCS_ATT_Admin_Notices::add_maintenance_notice( 'welcome' );
			add_option( 'apfs_version', self::VERSION );
		} elseif ( version_compare( $version, self::VERSION, '<' ) ) {
			update_option( 'apfs_version', self::VERSION );
		}
	}

	/**
	 * Clean-up on de-activation.
	 *
	 * @since 3.1.5
	 *
	 * @return void
	 */
	public function deactivate() {

		$notes_class = false;

		if ( class_exists( 'Automattic\WooCommerce\Admin\Notes\Notes' ) ) {
			$notes_class = 'Automattic\WooCommerce\Admin\Notes\Notes';
		} elseif ( class_exists( 'Automattic\WooCommerce\Admin\Notes\WC_Admin_Notes' ) ) {
			$notes_class = 'Automattic\WooCommerce\Admin\Notes\WC_Admin_Notes';
		} else {
			return;
		}

		$notes_class::delete_notes_with_name( 'wcsatt_first_product_note' );
	}

	/**
	 * Product types supported by the plugin.
	 *
	 * @return array
	 */
	public function get_supported_product_types() {
		return apply_filters( 'wcsatt_supported_product_types', array( 'simple', 'variable', 'variation', 'mix-and-match', 'bundle', 'composite' ) );
	}

	/**
	 * Log important stuff.
	 *
	 * @param  string  $message
	 * @param  string  $level
	 * @return void
	 */
	public function log( $message, $level ) {
		$logger = wc_get_logger();
		$logger->log( $level, $message, array( 'source' => 'wcs_att' ) );
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param	mixed  $links
	 * @param	mixed  $file
	 * @return	array
	 */
	public static function plugin_meta_links( $links, $file ) {

		if ( $file === WCS_ATT()->plugin_basename() ) {
			$row_meta = array(
				'docs'    => '<a href="' . self::DOCS_URL . '">' . __( 'Documentation', 'woocommerce-all-products-for-subscriptions' ) . '</a>',
				'support' => '<a href="' . self::SUPPORT_URL . '">' . __( 'Support', 'woocommerce-all-products-for-subscriptions' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return $links;
	}
}

// End class_exists check.
endif;

/**
 * Returns the main instance of WCS_ATT to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return WCS_ATT
 */
function WCS_ATT() {
  return WCS_ATT::instance();
}

// This code runs during plugin deactivation.
function deactivate_wcs_att(){
   WCS_ATT()->deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_wcs_att' );

// Launch the whole plugin.
$GLOBALS[ 'woocommerce_subscribe_all_the_things' ] = WCS_ATT();
