<?php
/**
* Plugin Name: WooCommerce Conditional Shipping and Payments
* Plugin URI: https://woocommerce.com/products/woocommerce-conditional-shipping-and-payments
* Description: Exclude shipping methods, payment gateways and shipping destinations using conditional logic.
* Version: 1.15.2
* Author: WooCommerce
* Author URI: https://somewherewarm.com/
*
* Woo: 680253:1f56ff002fa830b77017b0107505211a
*
* Text Domain: woocommerce-conditional-shipping-and-payments
* Domain Path: /languages/
*
* Requires PHP: 7.0
*
* Requires at least: 4.1
* Tested up to: 6.0
*
* WC requires at least: 3.9
* WC tested up to: 6.6
*
* License: GNU General Public License v3.0
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @class    WC_Conditional_Shipping_Payments
 * @version  1.15.2
 */

if ( ! class_exists( 'WC_Conditional_Shipping_Payments' ) ) :

class WC_Conditional_Shipping_Payments {

	/* Plugin version */
	const VERSION = '1.15.2';

	/* Required WC version */
	const REQ_WC_VERSION = '3.9.0';

	/* Text domain */
	const TEXT_DOMAIN = 'woocommerce-conditional-shipping-and-payments';

	/**
	 * @var WC_Conditional_Shipping_Payments - the single instance of the class.
	 *
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main WC_Conditional_Shipping_Payments Instance.
	 *
	 * Ensures only one instance of WC_Conditional_Shipping_Payments is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @static
	 * @see WC_CSP()
	 *
	 * @return WC_Conditional_Shipping_Payments - Main instance
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
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'woocommerce-conditional-shipping-and-payments' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'woocommerce-conditional-shipping-and-payments' ), '1.0.0' );
	}

	/**
	 * Admin functions and filters.
	 *
	 * @var WC_CSP_Admin
	 */
	public $admin;

	/**
	 * Loaded restrictions.
	 *
	 * @var WC_CSP_Restrictions
	 */
	public $restrictions;

	/**
	 * Loaded conditions.
	 *
	 * @var WC_CSP_Conditions
	 */
	public $conditions;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'initialize_plugin' ) );
		add_action( 'admin_init', array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
	}

	/**
	 * Plugin version getter.
	 *
	 * @since  1.5.9
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
	 * Plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Plugin base path name getter.
	 * @since  1.14.3
	 *
	 * @return string
	 */
	public function get_plugin_basename() {
		return plugin_basename( __FILE__ );
	}

	/**
	 * Indicates whether the plugin has been fully initialized.
	 *
	 * @since  1.7.6
	 *
	 * @return boolean
	 */
	public function plugin_initialized() {
		return class_exists( 'WC_CSP_Autoloader' );
	}

	/**
	 * Define constants if not present.
	 *
	 * @since  1.7.6
	 *
	 * @return boolean
	 */
	protected function maybe_define_constant( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Fire in the hole!
	 *
	 * @return void
	 */
	public function initialize_plugin() {

		$this->define_constants();

		// WC version check.
		if ( ! function_exists( 'WC' ) || version_compare( WC()->version, self::REQ_WC_VERSION ) < 0 ) {
			require_once( WC_CSP_ABSPATH . 'includes/admin/class-wc-csp-admin-notices.php' );
			/* translators: %s: WC min version */
			$notice = sprintf( __( 'WooCommerce Conditional Shipping and Payments requires at least WooCommerce <strong>%s</strong>.', 'woocommerce-conditional-shipping-and-payments' ), self::REQ_WC_VERSION );
			WC_CSP_Admin_Notices::add_notice( $notice, 'error' );
			return false;
		}

		// PHP version check.
		if ( ! function_exists( 'phpversion' ) || version_compare( phpversion(), '7.0.0', '<' ) ) {
			require_once( WC_CSP_ABSPATH . 'includes/admin/class-wc-csp-admin-notices.php' );
			/* translators: %1$s: Version %, %2$s: Update PHP doc URL */
			$notice = sprintf( __( 'WooCommerce Conditional Shipping and Payments requires at least PHP <strong>%1$s</strong>. Learn <a href="%2$s">how to update PHP</a>.', 'woocommerce-conditional-shipping-and-payments' ), '7.0.0', 'https://woocommerce.com/document/how-to-update-your-php-version/' );
			WC_CSP_Admin_Notices::add_notice( $notice, 'error' );
			return false;
		}

		$this->includes();

		// Load translations hook.
		add_action( 'init', array( $this, 'init_textdomain' ) );
	}

 	/**
	 * Define constants.
	 *
	 * @return void
	 */
	public function define_constants() {
		$this->maybe_define_constant( 'WC_CSP_VERSION', self::VERSION );
		$this->maybe_define_constant( 'WC_CSP_ABSPATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
	}


	/**
	 * Includes.
	 *
	 * @since 1.4.0
	 */
	public function includes() {

		// Class autoloader.
		require_once( WC_CSP_ABSPATH . 'includes/class-wc-csp-autoloader.php' );

		// Helpers.
		require_once( WC_CSP_ABSPATH . 'includes/class-wc-csp-helpers.php' );

		// Global functions.
		require_once( WC_CSP_ABSPATH . 'includes/wc-csp-functions.php' );

		// Compatibility.
		require_once( WC_CSP_ABSPATH . 'includes/compatibility/class-wc-csp-compatibility.php' );

		// Abstract restriction class extended by the included restriction classes.
		require_once( WC_CSP_ABSPATH . 'includes/abstracts/class-wc-csp-abstract-restriction.php' );

		// Restriction type interfaces implemented by the included restriction classes.
		require_once( WC_CSP_ABSPATH . 'includes/types/class-wc-csp-checkout-restriction.php' );
		require_once( WC_CSP_ABSPATH . 'includes/types/class-wc-csp-cart-restriction.php' );
		require_once( WC_CSP_ABSPATH . 'includes/types/class-wc-csp-update-cart-restriction.php' );
		require_once( WC_CSP_ABSPATH . 'includes/types/class-wc-csp-add-to-cart-restriction.php' );

		// Abstract condition classes extended by the included condition classes.
		require_once( WC_CSP_ABSPATH . 'includes/abstracts/class-wc-csp-abstract-condition.php' );
		require_once( WC_CSP_ABSPATH . 'includes/abstracts/class-wc-csp-abstract-package-condition.php' );

		// Add tracking.
		require_once( WC_CSP_ABSPATH . 'includes/class-wc-csp-tracker.php' );

		// Admin functions and meta-boxes.
		if ( is_admin() ) {
			$this->admin_includes();
		}

		// Load declared restrictions.
		$this->restrictions = new WC_CSP_Restrictions();

		// Load restriction conditions.
		$this->conditions = new WC_CSP_Conditions();

		// Debugger.
		require_once( WC_CSP_ABSPATH . 'includes/class-wc-csp-debugger.php' );
	}

	/**
	 * Loads the Admin & AJAX filters / hooks.
	 *
	 * @return void
	 */
	public function admin_includes() {
		require_once( WC_CSP_ABSPATH . 'includes/admin/class-wc-csp-admin.php' );
		$this->admin = new WC_CSP_Admin();
	}

	/**
	 * Load textdomain.
	 *
	 * @return void
	 */
	public function init_textdomain() {
		load_plugin_textdomain( 'woocommerce-conditional-shipping-and-payments', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		// Subscribe to automated translations.
		add_filter( 'woocommerce_translations_updates_for_' . basename( __FILE__, '.php' ), '__return_true' );
	}

	/**
	 * Store extension version.
	 *
	 * @return void
	 */
	public function activate() {

		$version = get_option( 'wc_csp_version', false );

		if ( ! class_exists( 'WC_CSP_Core_Compatibility' ) ){
			require_once( WC_CSP_ABSPATH . 'includes/compatibility/core/class-wc-csp-core-compatibility.php' );
		}

		if ( ! class_exists( 'WC_CSP_Admin_Notices' ) ){
			require_once( WC_CSP_ABSPATH . 'includes/admin/class-wc-csp-admin-notices.php' );
		}

		if ( $version === false ) {

			add_option( 'wc_csp_version', self::VERSION );

			// Clear cached shipping rates.
			WC_CSP_Core_Compatibility::clear_cached_shipping_rates();

			// Add dismissible welcome notice.
			WC_CSP_Admin_Notices::add_maintenance_notice( 'welcome' );

		} elseif ( version_compare( $version, self::VERSION, '<' ) ) {

			// The Cart Subtotal condition was introduced @ 1.9.0 and updated @ 1.10.0.
			if ( version_compare( $version, '1.9.0', '>=' ) && version_compare( $version, '1.10.0', '<' ) && wc_tax_enabled() ) {
				WC_CSP_Admin_Notices::add_one_time_maintenance_notice( 'cart_subtotal' );
			}

			update_option( 'wc_csp_version', self::VERSION );

			// Clear cached shipping rates.
			WC_CSP_Core_Compatibility::clear_cached_shipping_rates();
		}
	}

	/**
	 * Deactivate extension.
	 *
	 * @return void
	 */
	public function deactivate() {

		if ( ! class_exists( 'WC_CSP_Core_Compatibility' ) ){
			require_once( WC_CSP_ABSPATH . 'includes/compatibility/core/class-wc-csp-core-compatibility.php' );
		}
		
		// Clear cached shipping rates.
		WC_CSP_Core_Compatibility::clear_cached_shipping_rates();
	}
}

endif; // end class_exists check

/**
 * Returns the main instance of WC_Conditional_Shipping_Payments to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return WooCommerce Conditional Shipping and Payments
 */
function WC_CSP() {
	return WC_Conditional_Shipping_Payments::instance();
}

// Launch the whole plugin.
$GLOBALS[ 'woocommerce_conditional_shipping_and_payments' ] = WC_CSP();
