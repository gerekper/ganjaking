<?php
/**
 * Compatibility - setup backcompatibility and extension compatibility
 *
 * @package  WooCommerce Mix and Match Products/Compatibility
 * @since    1.0.0
 * @version  2.0.3
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Formerly the WC_Mix_and_Match_Compatibility class.
 * Renamed in 2.0.0
 */
class_alias( 'WC_MNM_Compatibility', 'WC_Mix_and_Match_Compatibility' );

/**
 * WC_Mix_and_Match_Compatibility Class.
 *
 * Load classes for making Mix and Match compatible with other plugins|woocommerce|legacy.
 */
class WC_MNM_Compatibility {

	/**
	 * Define dependencies
	 *
	 * @var array of minimum versions
	 * @since 2.0.0
	 */
	public $required = array(
		'apfs'   => '3.0.0',
		'blocks' => '7.2.0',
		'subs'   => '3.0.0',
	);

	/**
	 * Array of deprecated hook handlers.
	 *
	 * @var array of WC_Deprecated_Hooks
	 * @since 2.0.0
	 */
	public $deprecated_hook_handlers = array();

	/**
	 * The single instance of the class.
	 * @var WC_MNM_Compatibility
	 *
	 * @since 1.9.2
	 */
	protected static $_instance = null;

	/**
	 * Main class instance. Ensures only one instance of class is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_Mix_and_Match_Compatibility
	 * @since  1.9.2
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	function __construct() {

		// Core compatibility functions and hooks.
		require_once 'core/class-wc-mnm-core-compatibility.php';

		// Support deprecated filter hooks and actions.
		require_once 'backcompatibility/class-wc-mnm-deprecated-action-hooks.php';
		require_once 'backcompatibility/class-wc-mnm-deprecated-filter-hooks.php';

		$this->deprecated_hook_handlers[ 'actions' ] = new WC_MNM_Deprecated_Action_Hooks();
		$this->deprecated_hook_handlers[ 'filters' ] = new WC_MNM_Deprecated_Filter_Hooks();

		// Theme compatibility modules.
		require_once 'class-wc-mnm-theme-compatibility.php';

		if ( is_admin() ) {
			// Check plugin min versions.
			add_action( 'admin_init', array( $this, 'add_compatibility_notices' ) );
		}

		// Deactivate functionality from mini-extensions.
		$this->unload();

		// Initialize.
		add_action( 'plugins_loaded', array( $this, 'init' ), 100 );
	}

	/**
	 * Unload mini-extensions.
	 */
	public function unload() {
		// Deactivate functionality added by the min/max quantities mini-extension.
		if ( class_exists( 'WC_MNM_Min_Max_Quantities' ) ) {
			remove_action( 'wc_mnm_loaded', 'WC_MNM_Min_Max_Quantities' );
		}

		// Deactivate functionality added by the min/max quantities mini-extension.
		if ( class_exists( 'WC_MNM_Grid' ) ) {
			remove_action( 'init', array( '\WC_MNM_Grid\Display', 'init' ) );
		}

		// Deactivate functionality added by the Discounts mini-extension.
		if ( class_exists( 'WC_MNM_Discount' ) ) {
			remove_action( 'plugins_loaded', array( 'WC_MNM_Discount', 'load_plugin' ) );
		}

		// Deactivate functionality added by the Lightbox mini-extension.
		if ( class_exists( 'WC_MNM_Lightbox' ) ) {
			remove_action( 'plugins_loaded', array( 'WC_MNM_Lightbox', 'init' ) );
		}

		// Deactivate functionality added by the Categories mini-extension.
		if ( class_exists( 'WC_MNM_Categories' ) ) {
			remove_action( 'plugins_loaded', array( 'WC_MNM_Categories', 'init' ) );
		}
	}

	/**
	 * Init compatibility classes.
	 */
	public function init() {

		// Backcompatibility.
		if ( ! WC_MNM_Compatibility::is_db_version_gte( '2.0' ) ) {
			$module_paths['legacy_meta'] = 'backcompatibility/class-wc-mnm-legacy-meta.php';
		}

		// WooCommerce Cart/Checkout Blocks support.
		if ( class_exists( 'Automattic\WooCommerce\Blocks\Package' ) && version_compare( \Automattic\WooCommerce\Blocks\Package::get_version(), $this->required[ 'blocks' ], '>=' ) ) {
			$module_paths[ 'blocks' ] = 'modules/class-wc-mnm-blocks-compatibility.php';
		}

		// Multiple Shipping Addresses support.
		if ( class_exists( 'WC_Ship_Multiple' ) ) {
			$module_paths['multiple_shipping'] = 'modules/class-wc-mnm-ship-multiple-compatibility.php';
		}

		// Points and Rewards support.
		if ( class_exists( 'WC_Points_Rewards_Product' ) ) {
			$module_paths['points_rewards'] = 'modules/class-wc-mnm-pnr-compatibility.php';
		}

		// Pre-orders support.
		if ( class_exists( 'WC_Pre_Orders' ) ) {
			$module_paths['pre_orders'] = 'modules/class-wc-mnm-po-compatibility.php';
		}

		// Cost of Goods support.
		if ( class_exists( 'WC_COG' ) ) {
			$module_paths['cog'] = 'modules/class-wc-mnm-cog-compatibility.php';
		}

		// One Page Checkout support.
		if ( function_exists( 'is_wcopc_checkout' ) ) {
			$module_paths['opc'] = 'modules/class-wc-mnm-opc-compatibility.php';
		}

		// Wishlists support.
		if ( class_exists( 'WC_Wishlists_Plugin' ) ) {
			$module_paths['wishlists'] = 'modules/class-wc-mnm-wl-compatibility.php';
		}

		// PIP support.
		if ( class_exists( 'WC_PIP' ) ) {
			$module_paths['pip'] = 'modules/class-wc-mnm-pip-compatibility.php';
		}

		// Min Max Quantities integration.
		if ( class_exists( 'WC_Min_Max_Quantities' ) ) {
			$module_paths['min_max_quantities'] = 'modules/class-wc-mnm-min-max-compatibility.php';
		}

		// Shipstation integration.
		$module_paths['shipstation'] = 'modules/class-wc-mnm-shipstation-compatibility.php';

		if ( class_exists( 'CoCart' ) || defined( 'COCART_VERSION' ) ) {
			$module_paths['cocart'] = 'modules/class-wc-mnm-cocart-compatibility.php';
		}

		// WooCommerce Payments request buttons.
		if ( class_exists( 'WC_Payments' ) ) {
			$module_paths['wcpay'] = 'modules/class-wc-mnm-wcpay-compatibility.php';
		}

		// WooCommerce PayPal Payments request buttons.
		if ( class_exists( 'WooCommerce\PayPalCommerce\Plugin' ) ) {
			$module_paths['wcpaypal'] = 'modules/class-wc-mnm-paypal-payments-compatibility.php';
		}

		// Stripe fixes.
		if ( class_exists( 'WC_Stripe' ) ) {
			$module_paths['stripe'] = 'modules/class-wc-mnm-stripe-compatibility.php';
		}

		// Quickview support for "after summary" forms.
		if ( class_exists( 'WC_Quick_View' ) ) {
			$module_paths['quick-view'] = 'modules/class-wc-mnm-quick-view-compatibility.php';
		}

		// All Products for Subscriptions - per-item pricing and content switching support
		if ( function_exists( 'WCS_ATT' ) && version_compare( WCS_ATT()->plugin_version(), $this->required[ 'apfs' ], '>=' ) && WCS_ATT()->plugin_initialized() ) {
			$module_paths['apfs-pricing']   = 'modules/apfs/class-wc-mnm-apfs-pricing-compatibility.php';

			/*
			* Important: Switching Subscriptions and adding products/carts to existing Subscriptions
			* is only available with WooCommerce Subscriptions. These features are disabled when
			* Subscriptions core is loaded via WooCommerce Payments.
			*
			* See: https://woocommerce.com/document/payments/subscriptions/comparison/#feature-matrix
			*/
			if ( is_callable( array( WCS_ATT(), 'is_module_registered' ) ) && WCS_ATT()->is_module_registered( 'manage' ) ) {
				$module_paths['apfs-switching'] = 'modules/apfs/class-wc-mnm-apfs-switching-compatibility.php';
			}
		}

		/**
		 * 'wc_mnm_compatibility_modules' filter.
		 *
		 * Use this to filter the required compatibility modules.
		 *
		 * @since  1.6.1
		 * @param  array $module_paths
		 */
		$module_paths = apply_filters( 'wc_mnm_compatibility_modules', $module_paths );
		foreach ( $module_paths as $name => $path ) {
			require_once( $path );
		}

	}

	/**
	 * Checks versions of compatible/integrated/deprecated extensions.
	 */
	public function add_compatibility_notices() {

		// Min/max mini-extension check.
		if ( class_exists( 'WC_MNM_Min_Max_Quantities' ) ) {
			$notice = sprintf( __( 'The <strong>WooCommerce Mix and Match: Min/Max Quantities</strong> mini-extension is now part of <strong>WooCommerce Mix and Match</strong>. Please deactivate and remove the <strong>WooCommerce Mix and Match: Min/Max Quantities</strong> plugin.', 'woocommerce-mix-and-match-products' ) );
			WC_MNM_Admin_Notices::add_custom_notice( 'compat-mnm-mmq', $notice, 'warning' );
		}

		if ( class_exists( 'WC_MNM_Grid' ) ) {
			$notice = sprintf( __( 'The <strong>WC Mix and Match Grid</strong> mini-extension is now part of <strong>WooCommerce Mix and Match</strong> and should be deactivated and removed. Please enable the Grid layout (in the Mix and Match product options) for any product you\'d like to use it with.', 'woocommerce-mix-and-match-products' ) );
			WC_MNM_Admin_Notices::add_custom_notice( 'compat-mnm-grid', $notice, 'warning' );
		}

		if ( class_exists( 'WC_MNM_Discount' ) ) {
			$notice = sprintf( __( 'The <strong>WC Mix and Match: Per-Item Discount</strong> mini-extension is now part of <strong>WooCommerce Mix and Match</strong>. Please deactivate and remove the<strong>WC Mix and Match: Per-Item Discount</strong> plugin.', 'woocommerce-mix-and-match-products' ) );
			WC_MNM_Admin_Notices::add_custom_notice( 'compat-mnm-discount', $notice, 'warning' );
		}

		if ( class_exists( 'WC_MNM_Lightbox' ) ) {
			$notice = sprintf( __( 'The <strong>Mix and Match: Lightbox</strong> mini-extension is now part of <strong>WooCommerce Mix and Match</strong>. Please deactivate and remove the <strong>Mix and Match: Lightbox</strong> plugin.', 'woocommerce-mix-and-match-products' ) );
			WC_MNM_Admin_Notices::add_custom_notice( 'compat-mnm-lightbox', $notice, 'warning' );
		}

		if ( class_exists( 'WC_MNM_Categories' ) ) {
			$notice = sprintf( __( 'The <strong>Mix and Match: Categories</strong> mini-extension is now part of <strong>WooCommerce Mix and Match</strong>. Please deactivate and remove the <strong>Mix and Match: Categories</strong> plugin.', 'woocommerce-mix-and-match-products' ) );
			WC_MNM_Admin_Notices::add_custom_notice( 'compat-mnm-categories', $notice, 'warning' );
		}

		if ( class_exists( 'WC_MNM_APFS_Compatibility' ) ) {
			$notice = sprintf( __( 'The <strong>Mix and Match: Subscriptions</strong> mini-extension is now part of <strong>WooCommerce Mix and Match</strong>. Please deactivate and remove the <strong>Mix and Match: Subscriptions</strong> plugin.', 'woocommerce-mix-and-match-products' ) );
			WC_MNM_Admin_Notices::add_custom_notice( 'compat-mnm-apfs', $notice, 'warning' );
		}
	}

	/**
	 * Tells if a product is a Name Your Price product, provided that the extension is installed.
	 *
	 * @param  mixed  $product
	 * @return bool
	 */
	public function is_nyp( $product ) {

		if ( ! class_exists( 'WC_Name_Your_Price_Helpers' ) ) {
			return false;
		}

		if ( WC_Name_Your_Price_Helpers::is_nyp( $product ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Returns true if the plugin version of MNM is greater than or equal to $version.
	 *
	 * @since  2.0.0
	 *
	 * @param  string  $version the version to compare
	 * @return bool true if the installed version of MNM is > $version
	 */
	public static function is_version_gte( $version ) {

		$cache_key = 'is_version_gte';

		$result = WC_MNM_Helpers::cache_get( $version, $cache_key );
		if ( null === $result ) {
			$result = version_compare( WC_Mix_and_Match()->version, $version, '>=' );
			WC_MNM_Helpers::cache_set( $version, $cache_key, $result );
		}
		return $result;
	}

	/**
	 * Returns true if the DB version of MNM is greater than or equal to $version.
	 *
	 * @since  2.0.0
	 *
	 * @param  string  $version the version to compare
	 * @return bool true if the installed version of MNM is > $version
	 */
	public static function is_db_version_gte( $version ) {

		$cache_key = 'is_db_version_gte';

		$result = WC_MNM_Helpers::cache_get( $version, $cache_key );
		if ( null === $result ) {
			$result = version_compare( get_option( 'wc_mix_and_match_db_version', null ), $version, '>=' );
			WC_MNM_Helpers::cache_set( $version, $cache_key, $result );
		}
		return $result;
	}

}
WC_MNM_Compatibility::get_instance();
