<?php
/**
* Plugin Name: WooCommerce Conditional Shipping and Payments
* Plugin URI: https://woocommerce.com/products/woocommerce-conditional-shipping-and-payments
* Description: Exclude shipping methods, payment gateways and shipping countries/states using conditional logic.
* Version: 1.8.1
* Author: SomewhereWarm
* Author URI: https://somewherewarm.com/
*
* Woo: 680253:1f56ff002fa830b77017b0107505211a
*
* Text Domain: woocommerce-conditional-shipping-and-payments
* Domain Path: /languages/
*
* Requires at least: 4.1
* Tested up to: 5.4
*
* WC requires at least: 2.6
* WC tested up to: 4.2
*
* Copyright: © 2017-2020 SomewhereWarm SMPC.
* License: GNU General Public License v3.0
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * # WooCommerce Conditional Shipping and Payments
 *
 *
 * A small API for creating Restrictions (see the WC_CSP_Restriction abstract class and the WC_CSP_Restrictions loader class). Restrictions classes are loaded in the WC_CSP_Restrictions class via the 'woocommerce_csp_restrictions' filter.
 * Restrictions, which extend the WC_Settings_API class through WC_CSP_Restriction, may declare the existence of 'global' or 'product' fields and support for multiple rule instances.
 * The included restrictions all support multiple global and product-based definitions.
 *
 * Global restrictions are defined from WooCommerce->Settings->Restrictions, while product-level restrictions are created in a new "Restrictions" product metabox tab.
 *
 * Restrictions may implement 4 types of validation interfaces that fire on the i) add-to-cart, ii) cart check, iii) update cart, or iv) checkout validation action hooks. Additionally, restrictions themselves may hook into whatever WC property they need to modify, if necessary.
 * The 'validation_types' property of the WC_CSP_Restriction abstract class declares the validation interfaces supported by a restriction.
 *
 * If the restriction needs to hook itself into 'woocommerce_add_to_cart_validation', 'woocommerce_check_cart_items', 'woocommerce_update_cart_validation', or 'woocommerce_after_checkout_validation',
 * it must declare support for the 'add-to-cart', 'cart', 'cart-update', or 'checkout' validation types and implement the 'WC_CSP_Add_To_Cart_Restriction', 'WC_CSP_Cart_Restriction', 'WC_CSP_Update_Cart_Restriction', or 'WC_CSP_Checkout_Restriction' interfaces.
 *
 * The included restrictions all support the 'checkout' validation type only, and implement the 'WC_CSP_Checkout_Restriction' interface only.
 *
 *
 * ## Restrictions
 *
 * The extension includes 3 checkout restriction types:
 *
 *
 * 1) Shipping Country
 *
 * Restrict the allowed checkout shipping countries via global rules or rules defined at product level.
 * Excluded shipping countries can still be selected during checkout. However, selecting an excluded shipping country triggers a notice, while attempting to complete the order results in an error message.
 *
 *
 * 2) Payment Gateway
 *
 * Restrict the checkout payment gateways via global rules or rules defined at product level.
  * Excluded payment gateways can be removed completely from the checkout gateways list, or displayed as usual and trigger an error message if selected when attempting to complete the order.
 *
 *
 * 3) Shipping Method
 *
 * Restrict the checkout shipping methods via global rules or rules defined at product level.
  * Excluded shipping methods can be removed completely from the checkout methods list(s) at package level, or displayed as usual and trigger an error message if selected when attempting to complete the order.
 *
 * ## Conditions
 *
 * Conditions are used as the building blocks for restriction rules.
 * An exclusion rule (restriction instance) is in effect only if all defined conditions in it match (AND).
 * Multiple restriction instances can be added to implement OR-related rules.
 *
 * @class    WC_Conditional_Shipping_Payments
 * @version  1.8.1
 */

if ( ! class_exists( 'WC_Conditional_Shipping_Payments' ) ) :

class WC_Conditional_Shipping_Payments {

	/* Plugin version */
	const VERSION = '1.8.1';

	/* Required WC version */
	const REQ_WC_VERSION = '2.6.0';

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
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce-conditional-shipping-and-payments' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce-conditional-shipping-and-payments' ), '1.0.0' );
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
			$version       = sizeof( $version_parts ) > 1 ? $version_parts[ 0 ] : $version;
		}

		return $version;
	}

	/**
	 * Plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) );
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
			require_once( 'includes/admin/class-wc-csp-admin-notices.php' );
			$notice = sprintf( __( 'WooCommerce Conditional Shipping and Payments requires at least WooCommerce <strong>%s</strong>.', 'woocommerce-conditional-shipping-and-payments' ), self::REQ_WC_VERSION );
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
		require_once( 'includes/class-wc-csp-autoloader.php' );

		// CP functions.
		require_once( 'includes/wc-csp-functions.php' );

		// Compatibility.
		require_once( 'includes/compatibility/class-wc-csp-compatibility.php' );

		// Abstract restriction class extended by the included restriction classes.
		require_once( 'includes/abstracts/class-wc-csp-abstract-restriction.php' );

		// Restriction type interfaces implemented by the included restriction classes.
		require_once( 'includes/types/class-wc-csp-checkout-restriction.php' );
		require_once( 'includes/types/class-wc-csp-cart-restriction.php' );
		require_once( 'includes/types/class-wc-csp-update-cart-restriction.php' );
		require_once( 'includes/types/class-wc-csp-add-to-cart-restriction.php' );

		// Abstract condition classes extended by the included condition classes.
		require_once( 'includes/abstracts/class-wc-csp-abstract-condition.php' );
		require_once( 'includes/abstracts/class-wc-csp-abstract-package-condition.php' );

		// Admin functions and meta-boxes.
		if ( is_admin() ) {
			$this->admin_includes();
		}

		// Load declared restrictions.
		$this->restrictions = new WC_CSP_Restrictions();

		// Load restriction conditions.
		$this->conditions = new WC_CSP_Conditions();
	}

	/**
	 * Loads the Admin & AJAX filters / hooks.
	 *
	 * @return void
	 */
	public function admin_includes() {
		require_once( 'includes/admin/class-wc-csp-admin.php' );
		$this->admin = new WC_CSP_Admin();
	}

	/**
	 * Load textdomain.
	 *
	 * @return void
	 */
	public function init_textdomain() {
		load_plugin_textdomain( 'woocommerce-conditional-shipping-and-payments', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Store extension version.
	 *
	 * @return void
	 */
	public function activate() {

		$version = get_option( 'wc_csp_version', false );

		if ( $version === false ) {

			add_option( 'wc_csp_version', self::VERSION );

			// Clear cached shipping rates.
			WC_CSP_Core_Compatibility::clear_cached_shipping_rates();

			// Add dismissible welcome notice.
			WC_CSP_Admin_Notices::add_maintenance_notice( 'welcome' );

		} elseif ( version_compare( $version, self::VERSION, '<' ) ) {

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
