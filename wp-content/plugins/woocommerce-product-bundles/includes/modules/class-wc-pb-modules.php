<?php
/**
 * WC_PB_Modules class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.8.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PB Modules Loader
 *
 * @version  5.8.0
 */
class WC_PB_Modules {

	/**
	 * The single instance of the class.
	 * @var WC_PB_Modules
	 */
	protected static $_instance = null;

	/**
	 * Modules to instantiate.
	 * @var array
	 */
	protected $modules = array();

	/**
	 * Main WC_PB_Modules instance. Ensures only one instance of WC_PB_Modules is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_PB_Modules
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-product-bundles' ), '5.8.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-product-bundles' ), '5.8.0' );
	}

	/**
	 * Handles module initialization.
	 *
	 * @return void
	 */
	public function __construct() {

		// Abstract modules container class.
		require_once( 'abstract/class-wc-pb-abstract-module.php' );

		// Bundle-Sells module.
		require_once( 'bundle-sells/class-wc-pb-bs-module.php' );

		// Min/Max Items module.
		require_once( 'min-max-items/class-wc-pb-mmi-module.php' );

		$module_names = apply_filters( 'woocommerce_bundles_modules', array(
			'WC_PB_BS_Module',
			'WC_PB_MMI_Module'
		) );

		foreach ( $module_names as $module_name ) {
			$this->modules[] = new $module_name();
		}
	}

	/**
	 * Loads module functionality associated with a named component.
	 *
	 * @param  string  $name
	 */
	public function load_components( $name ) {

		foreach ( $this->modules as $module ) {
			$module->load_component( $name );
		}
	}
}
