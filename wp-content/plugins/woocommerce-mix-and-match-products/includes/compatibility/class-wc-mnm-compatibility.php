<?php
/**
 * Extension Compatibilty
 *
 * @author   Kathy Darling
 * @category Classes
 * @package  WooCommerce Mix and Match Products/Compatibility
 * @since    1.0.0
 * @version  1.9.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Mix_and_Match_Compatibility Class.
 *
 * Load classes for making Mix and Match compatible with other plugins.
 */
class WC_Mix_and_Match_Compatibility {

	/**
	 * The single instance of the class.
	 * @var WC_Mix_and_Match_Compatibility
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
			remove_action( 'woocommerce_mnm_loaded', 'WC_MNM_Min_Max_Quantities' );
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
	}

	/**
	 * Init compatibility classes.
	 */
	public function init() {

		// Multiple Shipping Addresses support.
		if ( class_exists( 'WC_Ship_Multiple' ) ) {
			$module_paths[ 'multiple_shipping' ] = 'modules/class-wc-mnm-ship-multiple-compatibility.php';
		}

		// Points and Rewards support.
		if ( class_exists( 'WC_Points_Rewards_Product' ) ) {
			$module_paths[ 'points_rewards' ] = 'modules/class-wc-mnm-pnr-compatibility.php';
		}

		// Pre-orders support.
		if ( class_exists( 'WC_Pre_Orders' ) ) {
			$module_paths[ 'pre_orders' ] = 'modules/class-wc-mnm-po-compatibility.php';
		}

		// Cost of Goods support.
		if ( class_exists( 'WC_COG' ) ) {
			$module_paths[ 'cog' ] = 'modules/class-wc-mnm-cog-compatibility.php';
		}

		// One Page Checkout support.
		if ( function_exists( 'is_wcopc_checkout' ) ) {
			$module_paths[ 'opc' ] = 'modules/class-wc-mnm-opc-compatibility.php';
		}

		// Wishlists support.
		if ( class_exists( 'WC_Wishlists_Plugin' ) ) {
			$module_paths[ 'wishlists' ] = 'modules/class-wc-mnm-wl-compatibility.php';
		}

		// PIP support.
		if ( class_exists( 'WC_PIP' ) ) {
			$module_paths[ 'pip' ] = 'modules/class-wc-mnm-pip-compatibility.php';
		}

		// Min Max Quantities integration.
		if ( class_exists( 'WC_Min_Max_Quantities' ) ) {
			$module_paths[ 'min_max_quantities' ] = 'modules/class-wc-mnm-min-max-compatibility.php';
		}

		// Shipstation integration.
		$module_paths[ 'shipstation' ] = 'modules/class-wc-mnm-shipstation-compatibility.php';

		if ( class_exists( 'CoCart' ) || defined( 'COCART_VERSION' ) ) {
			$module_paths[ 'cocart' ] = 'modules/class-wc-mnm-cocart-compatibility.php';
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
			WC_MNM_Admin_Notices::add_notice( $notice, 'warning' );
		}

		if ( class_exists( 'WC_MNM_Grid' ) ) {
			$notice = sprintf( __( 'The <strong>WC Mix and Match Grid</strong> mini-extension is now part of <strong>WooCommerce Mix and Match</strong> and should be deactivated and removed. Please enable the Grid layout (in the Mix and Match product options) for any product you\'d like to use it with.', 'woocommerce-mix-and-match-products' ) );
			WC_MNM_Admin_Notices::add_notice( $notice, 'warning' );
		}

		if ( class_exists( 'WC_MNM_Discount' ) ) {
			$notice = sprintf( __( 'The <strong>WC Mix and Match: Per-Item Discount</strong> mini-extension is now part of <strong>WooCommerce Mix and Match</strong>. Please deactivate and remove the<strong>WC Mix and Match: Per-Item Discount</strong> plugin.', 'woocommerce-mix-and-match-products' ) );
			WC_MNM_Admin_Notices::add_notice( $notice, 'warning' );
		}

		if ( class_exists( 'WC_MNM_Lightbox' ) ) {
			$notice = sprintf( __( 'The <strong>Mix and Match: Lightbox</strong> mini-extension is now part of <strong>WooCommerce Mix and Match</strong>. Please deactivate and remove the <strong>Mix and Match: Lightbox</strong> plugin.', 'woocommerce-mix-and-match-products' ) );
			WC_MNM_Admin_Notices::add_notice( $notice, 'warning' );
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
}
