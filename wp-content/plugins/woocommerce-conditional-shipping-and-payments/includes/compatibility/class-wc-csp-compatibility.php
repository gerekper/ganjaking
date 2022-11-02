<?php
/**
 * WC_CSP_Compatibility class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles compatibility with other WC extensions.
 *
 * @class    WC_CSP_Compatibility
 * @version  1.14.4
 */
class WC_CSP_Compatibility {

	/**
	 * Array of min required plugin versions.
	 *
	 * @var array
	 */
	private static $required = array();

	/**
	 * Modules to load.
	 *
	 * @var array
	 */
	private static $modules = array();

	/**
	 * Setup compatibility class.
	 */
	public static function init() {

		self::$required = array(
			'gc'     => '1.1.1',
			'blocks' => '7.3.0',
		);

		// Initialize.
		self::load_modules();
		// Core compatibility inclusions.
		self::core_includes();
	}

	/**
	 * Initialize.
	 *
	 * @since  1.4.0
	 *
	 * @return void
	 */
	protected static function load_modules() {

		if ( is_admin() ) {
			// Check plugin min versions.
			add_action( 'admin_init', array( __CLASS__, 'add_compatibility_notices' ) );
		}

		// Declare HPOS compatibility.
		add_action( 'before_woocommerce_init', array( __CLASS__, 'declare_hpos_compatibility' ) );

		// Load modules.
		add_action( 'plugins_loaded', array( __CLASS__, 'module_includes' ), 100 );
	}

	/**
	 * Checks if a module has been loaded.
	 *
	 * @since  1.13.0
	 *
	 * @return boolean
	 */
	public static function is_module_loaded( $name ) {
		return isset( self::$modules[ $name ] );
	}

	/**
	 * Core compatibility functions.
	 *
	 * @return void
	 */
	public static function core_includes() {
		require_once( WC_CSP_ABSPATH . 'includes/compatibility/core/class-wc-csp-core-compatibility.php' );
	}

	/**
	 * Declare HPOS( Custom Order tables) compatibility.
	 *
	 * @since 1.14.3
	 */
	public static function declare_hpos_compatibility() {

		if ( ! class_exists( 'Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			return;
		}

		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WC_CSP()->get_plugin_basename(), true );
	}

	/**
	 * Load compatibility classes.
	 *
	 * @return void
	 */
	public static function module_includes() {

		$module_paths = array();

		// WooCommerce Cart/Checkout Blocks support.
		if ( class_exists( 'Automattic\WooCommerce\Blocks\Package' ) && version_compare( \Automattic\WooCommerce\Blocks\Package::get_version(), self::$required[ 'blocks' ] ) >= 0 ) {
			$module_paths[ 'blocks' ] = WC_CSP_ABSPATH . 'includes/compatibility/modules/class-wc-csp-blocks-compatibility.php';
		}

		// Stripe support.
		if ( class_exists( 'WC_Stripe' ) ) {
			$module_paths[ 'stripe' ] = WC_CSP_ABSPATH . 'includes/compatibility/modules/class-wc-csp-stripe-compatibility.php';
		}

		// PayPal Express support.
		if ( function_exists( 'wc_gateway_ppec' ) ) {
			$module_paths[ 'paypal_ppec' ] = WC_CSP_ABSPATH . 'includes/compatibility/modules/class-wc-csp-ppe-compatibility.php';
		}

		// Klarna Checkout support.
		if ( class_exists( 'Klarna_Checkout_For_WooCommerce' ) ) {
			$module_paths[ 'klarna_checkout' ] = WC_CSP_ABSPATH . 'includes/compatibility/modules/class-wc-csp-klc-compatibility.php';
		}

		// Klarna Payments support.
		if ( class_exists( 'WC_Klarna_Payments' ) ) {
			$module_paths[ 'klarna_payments' ] = WC_CSP_ABSPATH . 'includes/compatibility/modules/class-wc-csp-klp-compatibility.php';
		}

		// Amazon Pay support.
		if ( class_exists( 'WC_Amazon_Payments_Advanced' ) ) {
			$module_paths[ 'amazon_payments' ] = WC_CSP_ABSPATH . 'includes/compatibility/modules/class-wc-csp-ap-compatibility.php';
		}

		// Woocommerce Memberships support.
		if ( class_exists( 'WC_Memberships' ) ) {
			$module_paths[ 'memberships' ] = WC_CSP_ABSPATH . 'includes/compatibility/modules/class-wc-csp-memberships-compatibility.php';
		}

		// Woocommerce Subscriptions support.
		if ( class_exists( 'WC_Subscriptions' ) || class_exists( 'WC_Subscriptions_Core_Plugin' ) ) {
			$module_paths[ 'subscriptions' ] = WC_CSP_ABSPATH . 'includes/compatibility/modules/class-wc-csp-wcs-compatibility.php';
		}

		// Woocommerce MultiCurrency support.
		if ( defined( 'WOOCOMMERCE_MULTICURRENCY_VERSION' ) ) {
			$module_paths[ 'currency' ] = WC_CSP_ABSPATH . 'includes/compatibility/modules/class-wc-csp-multicurrency-compatibility.php';
		}

		// Woocommerce Gift Cards support.
		if ( class_exists( 'WC_GC_Gift_Cards' ) && function_exists( 'WC_GC' ) && version_compare( WC_GC()->get_plugin_version( true ), self::$required[ 'gc' ] ) >= 0 ) {
			$module_paths[ 'giftcards' ] = WC_CSP_ABSPATH . 'includes/compatibility/modules/class-wc-csp-gc-compatibility.php';
		}

		/**
		 * 'woocommerce_csp_compatibility_modules' filter.
		 *
		 * Use this to filter the loaded compatibility modules.
		 *
		 * @since  1.4.0
		 * @param  array $module_paths
		 */
		self::$modules = apply_filters( 'woocommerce_csp_compatibility_modules', $module_paths );

		foreach ( self::$modules as $name => $path ) {
			require_once( $path );
		}
	}

	/**
	 * Checks versions of compatible/integrated/deprecated extensions.
	 *
	 * @since  1.8.0
	 *
	 * @return void
	 */
	public static function add_compatibility_notices() {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// Blocks plugin check.
		if ( defined( 'WC_BLOCKS_IS_FEATURE_PLUGIN' ) && WC_CSP_Core_Compatibility::is_block_based_checkout() ) {

			$required_version = self::$required[ 'blocks' ];
			if ( class_exists( 'Automattic\WooCommerce\Blocks\Package' ) && version_compare( \Automattic\WooCommerce\Blocks\Package::get_version(), self::$required[ 'blocks' ] ) < 0 ) {

				$plugin      = __( 'WooCommerce Blocks', 'woocommerce-conditional-shipping-and-payments' );
				$plugin_url  = 'https://woocommerce.com/products/woocommerce-gutenberg-products-block/';
				/* translators: %1$s: Plugin name, %2$s: Plugin URL, %3$s: Plugin name full, %4$s: Plugin version */
				$notice      = sprintf( __( 'The installed version of <strong>%1$s</strong> does not support <strong>Conditional Shipping and Payments</strong>. Please update <a href="%2$s" target="_blank">%3$s</a> to version <strong>%4$s</strong> or higher.', 'woocommerce-conditional-shipping-and-payments' ), $plugin, $plugin_url, $plugin, $required_version );

				WC_CSP_Admin_Notices::add_dismissible_notice( $notice, array( 'dismiss_class' => 'blocks_lt_' . $required_version, 'type' => 'warning' ) );
			}
		}

		// GC version check.
		if ( class_exists( 'WC_GC_Gift_Cards' ) && function_exists( 'WC_GC' ) ) {

			$required_version = self::$required[ 'gc' ];
			if ( version_compare( WC_GC()->get_plugin_version( true ), $required_version ) < 0 ) {

				$extension      = __( 'Gift Cards', 'woocommerce-conditional-shipping-and-payments' );
				$extension_full = __( 'WooCommerce Gift Cards', 'woocommerce-conditional-shipping-and-payments' );
				$extension_url  = 'https://woocommerce.com/products/gift-cards/';
				$notice         = sprintf( __( 'The installed version of <strong>%1$s</strong> is not supported by <strong>Conditional Shipping and Payments</strong>. Please update <a href="%2$s" target="_blank">%3$s</a> to version <strong>%4$s</strong> or higher.', 'woocommerce-conditional-shipping-and-payments' ), $extension, $extension_url, $extension_full, $required_version );

				WC_CSP_Admin_Notices::add_dismissible_notice( $notice, array( 'dismiss_class' => 'gc_lt_' . $required_version, 'type' => 'warning' ) );
			}
		}
	}

	/**
	 * True if a gateway is restricted.
	 *
	 * @since  1.4.0
	 *
	 * @param  string  $gateway_id
	 * @return boolean
	 */
	public static function is_gateway_restricted( $gateway_id ) {

		$raw_gateways = WC()->payment_gateways->payment_gateways();
		$restricted   = false;

		if ( ! empty( $raw_gateways ) ) {

			$restriction = WC_CSP()->restrictions->get_restriction( 'payment_gateways' );
			$gateways    = $restriction->exclude_payment_gateways( $raw_gateways, true );

			if ( ! isset( $gateways[ $gateway_id ] ) ) {
				$restricted = true;
			}
		}

		return $restricted;
	}
}

WC_CSP_Compatibility::init();
