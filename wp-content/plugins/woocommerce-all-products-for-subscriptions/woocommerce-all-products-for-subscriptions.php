<?php
/**
* Plugin Name: WooCommerce All Products For Subscriptions
* Plugin URI: https://woocommerce.com/products/all-products-for-woocommerce-subscriptions/
* Description: Make existing products available on subscription, and give customers the freedom to add products to their existing subscriptions. WooCommerce Subscriptions add-on formerly known as Subscribe All The Things.
* Version: 4.1.2
* Author: WooCommerce
* Author URI: https://somewherewarm.com/
*
* Woo: 3978176:b0e6e19cf767e4fb9ca7fe9b0ff2c381
*
* Text Domain: woocommerce-all-products-for-subscriptions
* Domain Path: /languages/
*
* Requires PHP: 7.0
*
* Requires at least: 4.4
* Tested up to: 6.0
*
* WC requires at least: 3.9
* WC tested up to: 6.9
*
* License: GNU General Public License v3.0
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
*
* @package  WooCommerce All Products For Subscriptions
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
 * @version  4.1.2
 */
class WCS_ATT extends WCS_ATT_Abstract_Module {

	/* Plugin version. */
	const VERSION = '4.1.2';

	/* Required WC version. */
	const REQ_WC_VERSION = '3.9.0';

	/* Required WC version. */
	const REQ_WCS_VERSION = '3.0.0';

	/* Required WC Payments version. */
	const REQ_WCPAY_VERSION = '3.2.0';

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
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Foul!', 'woocommerce-all-products-for-subscriptions' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Foul!', 'woocommerce-all-products-for-subscriptions' ), '1.0.0' );
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
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
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
				$notice = sprintf( __( 'All Products for WooCommerce Subscriptions requires at least <a href="%1$s" target="_blank">WooCommerce Subscriptions</a> version <strong>%2$s</strong>.', 'woocommerce-all-products-for-subscriptions' ), self::get_resource_url( 'wcs' ), self::REQ_WCS_VERSION );
			}

		} elseif ( class_exists( 'WC_Payments' ) && class_exists( 'WC_Payments_Features' ) ) {

			if ( ! defined( 'WCS_INIT_TIMESTAMP' ) || ! WC_Payments_Features::is_wcpay_subscriptions_enabled() ) {
				if ( version_compare( WCPAY_VERSION_NUMBER, self::REQ_WCPAY_VERSION ) < 0 ) {
					// Notice about plugin version when WooCommerce Payments is active.
					$notice = sprintf( __( 'All Products for WooCommerce Subscriptions requires at least <a href="%1$s" target="_blank">WooCommerce Payments</a> version <strong>%2$s</strong>.', 'woocommerce-all-products-for-subscriptions' ), self::get_resource_url( 'wcpay' ), self::REQ_WCPAY_VERSION );
				} else {
					// Notice about disabled Subscription features in WooCommerce Payments.
					$notice = sprintf( __( 'All Products for WooCommerce Subscriptions requires Subscriptions to be enabled in the <strong>WooCommerce Payments</strong> <a href="%1$s" target="_blank">settings</a>.', 'woocommerce-all-products-for-subscriptions' ), site_url( self::get_resource_url( 'wcpay-settings' ) ) );
				}
			}
		} else {
			// Notice about disabled Subscriptions core not being loaded.
			$notice = sprintf( __( 'All Products for WooCommerce Subscriptions requires at least <a href="%1$s" target="_blank">WooCommerce Payments</a> version <strong>%2$s</strong> or <a href="%3$s" target="_blank">WooCommerce Subscriptions</a> version <strong>%4$s</strong>.', 'woocommerce-all-products-for-subscriptions' ), self::get_resource_url( 'wcpay' ), self::REQ_WCPAY_VERSION, self::get_resource_url( 'wcs' ), self::REQ_WCS_VERSION );
		}

		if ( ! empty ( $notice ) ) {
			require_once( WCS_ATT_ABSPATH . 'includes/admin/class-wcs-att-admin-notices.php' );
			WCS_ATT_Admin_Notices::add_notice( $notice, 'error' );
			return false;
		}

		// PHP version check.
		if ( ! function_exists( 'phpversion' ) || version_compare( phpversion(), '7.0.0', '<' ) ) {
			$notice = sprintf( __( 'All Products for WooCommerce Subscriptions requires at least PHP <strong>%1$s</strong>. Learn <a href="%2$s">how to update PHP</a>.', 'woocommerce-all-products-for-subscriptions' ), '7.0.0', 'https://woocommerce.com/document/how-to-update-your-php-version/' );
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
		add_action( 'init', array( $this, 'init_plugin' ) );
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
		require_once( WCS_ATT_ABSPATH . 'includes/class-wcs-att-tracker.php' );
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
	public function register_modules() {
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
	 * Initialize plugin.
	 *
	 * @since 3.4.0
	 *
	 * @return void
	 */
	public function init_plugin() {
		$this->init_textdomain();
		$this->activate();
	}

	/**
	 * Load textdomain.
	 *
	 * @return void
	 */
	public function init_textdomain() {
		load_plugin_textdomain( 'woocommerce-all-products-for-subscriptions', false, dirname( $this->plugin_basename() ) . '/languages/' );
		// Subscribe to automated translations.
		add_filter( 'woocommerce_translations_updates_for_' . basename( __FILE__, '.php' ), '__return_true' );
	}

	/**
	 * Store plugin version.
	 *
	 * @return void
	 */
	public function activate() {

		$version = get_option( 'apfs_version', false );

		if ( ! $version ) {

			if ( ! class_exists( 'WCS_ATT_Admin_Notices' ) ) {
				require_once( WCS_ATT_ABSPATH . 'includes/admin/class-wcs-att-admin-notices.php' );
			}

			WCS_ATT_Admin_Notices::add_maintenance_notice( 'welcome' );
			add_option( 'apfs_version', self::VERSION );

		} elseif ( version_compare( $version, self::VERSION, '<' ) ) {

			// If adding carts to subscriptions is allowed and cart plans do not exist when updating to version 3.4.0, turn off the feature for backwards compatibility.
			if ( version_compare( $version, '3.4.0', '<' ) ) {
				if ( 'off' !== get_option( 'wcsatt_add_cart_to_subscription', 'off' ) && empty( get_option( 'wcsatt_subscribe_to_cart_schemes', false ) ) ) {
					update_option( 'wcsatt_add_cart_to_subscription', 'off' );
				}
			}

			if ( version_compare( $version, '4.0.0', '<' ) ) {
				if ( ! empty( get_option( 'wcsatt_subscribe_to_cart_schemes', false ) ) ) {

					if ( ! class_exists( 'WCS_ATT_Admin_Notices' ) ) {
						require_once( WCS_ATT_ABSPATH . 'includes/admin/class-wcs-att-admin-notices.php' );
					}

					WCS_ATT_Admin_Notices::add_maintenance_notice( 'v4' );
				}
			}

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
	public function deactivate() {}

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
	public function plugin_meta_links( $links, $file ) {

		if ( $file === WCS_ATT()->plugin_basename() ) {

			$row_meta = array(
				'docs'    => '<a href="' . self::get_resource_url( 'docs-contents' ) . '">' . __( 'Documentation', 'woocommerce-all-products-for-subscriptions' ) . '</a>',
				'support' => '<a href="' . self::get_resource_url( 'ticket-form' ) . '">' . __( 'Support', 'woocommerce-all-products-for-subscriptions' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return $links;
	}

	/**
	 * Returns URL to a doc or support resource.
	 *
	 * @since  4.0.0
	 *
	 * @param  string  $handle
	 * @return string
	 */
	public function get_resource_url( $handle ) {

		$resource = false;

		if ( 'update-php' === $handle ) {
			$resource = 'https://woocommerce.com/document/how-to-update-your-php-version/';
		} elseif ( 'docs-contents' === $handle ) {
			$resource = 'https://woocommerce.com/document/all-products-for-woocommerce-subscriptions/';
		} elseif ( 'docs-configuration' === $handle ) {
			$resource = 'https://woocommerce.com/document/all-products-for-woocommerce-subscriptions/store-owners-guide/#configuration';
		} elseif ( 'max-input-vars' === $handle ) {
			$resource = 'https://woocommerce.com/document/bundles/bundles-faq/#faq_bundled_items_dont_save';
		} elseif ( 'updating' === $handle ) {
			$resource = 'https://woocommerce.com/document/how-to-update-woocommerce/';
		} elseif ( 'global-plan-settings' === $handle ) {
			$resource = admin_url( 'admin.php?page=wc-settings&tab=subscriptions#wcsatt_subscribe_to_cart_options_pre-description' );
		} elseif ( 'wcpay-settings' === $handle ) {
			$resource = admin_url( 'admin.php?page=wc-settings&tab=checkout&section=woocommerce_payments' );
		} elseif ( 'wcs' === $handle ) {
			$resource = 'https://woocommerce.com/products/woocommerce-subscriptions/';
		} elseif ( 'wcpay' === $handle ) {
			$resource = 'https://woocommerce.com/products/woocommerce-payments/';
		}  elseif ( 'ticket-form' === $handle ) {
			$resource = 'https://woocommerce.com/my-account/marketplace-ticket-form/';
		}

		return $resource;
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

// Launch the whole plugin.
$GLOBALS[ 'woocommerce_subscribe_all_the_things' ] = WCS_ATT();
