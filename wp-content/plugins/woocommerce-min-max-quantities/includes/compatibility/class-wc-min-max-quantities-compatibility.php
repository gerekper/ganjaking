<?php
/**
 * WC_MMQ_Compatibility class
 *
 * @package  Woo Min/Max Quantities
 * @since    4.0.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles compatibility with other WC extensions.
 *
 * @class    WC_MMQ_Compatibility
 * @version  4.1.0
 */
class WC_MMQ_Compatibility {

	/**
	 * Min required plugin versions to check.
	 *
	 * @var array
	 */
	private $required = array();

	/**
	 * Modules to load.
	 *
	 * @var array
	 */
	private $modules = array();

	/**
	 * The single instance of the class.
	 *
	 * @var WC_MMQ_Compatibility
	 *
	 * @since 4.0.4
	 */
	protected static $_instance = null;

	/**
	 * Main WC_MMQ_Compatibility instance. Ensures only one instance of WC_MMQ_Compatibility is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_MMQ_Compatibility
	 * @since  4.0.4
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
	 * @since 4.0.4
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Foul!', 'woocommerce-min-max-quantities' ), '4.0.4' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 4.0.4
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Foul!', 'woocommerce-min-max-quantities' ), '4.0.4' );
	}

	/**
	 * Setup compatibility class.
	 */
	protected function __construct() {

		// Define dependencies.
		$this->required = array(
			'pao'    => '3.0.14',
			'blocks' => '7.2.0'
		);

		// Initialize.
		$this->load_modules();
	}

	/**
	 * Initialize.
	 *
	 * @since  5.4.0
	 *
	 * @return void
	 */
	protected function load_modules() {

		// Load modules.
		add_action( 'plugins_loaded', array( $this, 'module_includes' ), 100 );

	}

	/**
	 * Core compatibility functions.
	 *
	 * @return void
	 */
	public static function core_includes() {
		include_once WC_MMQ_ABSPATH . 'includes/compatibility/core/class-wc-min-max-quantities-core-compatibility.php';
	}

	/**
	 * Checks if a module has been loaded.
	 *
	 * @return boolean
	 */
	public function is_module_loaded( $name ) {
		return isset( $this->modules[ $name ] );
	}

	/**
	 * Load compatibility classes.
	 *
	 * @return void
	 */
	public function module_includes() {

		$module_paths = array();

		// WooCommerce Cart/Checkout Blocks support.
		if ( class_exists( 'Automattic\WooCommerce\Blocks\Package' ) && version_compare( \Automattic\WooCommerce\Blocks\Package::get_version(), $this->required[ 'blocks' ] ) >= 0 ) {
			$module_paths[ 'blocks' ] = WC_MMQ_ABSPATH . 'includes/compatibility/modules/class-wc-min-max-quantities-blocks-compatibility.php';
		}

		// Addons support.
		if ( class_exists( 'WC_Product_Addons' ) && defined( 'WC_PRODUCT_ADDONS_VERSION' ) && version_compare( WC_PRODUCT_ADDONS_VERSION, $this->required[ 'pao' ] ) >= 0 ) {
			$module_paths[ 'product_addons' ] = WC_MMQ_ABSPATH . 'includes/compatibility/modules/class-wc-min-max-quantities-addons.php';
		}

		/**
		 * 'woocommerce_mmq_compatibility_modules' filter.
		 *
		 * Use this to filter the required compatibility modules.
		 *
		 * @since  4.0.4
		 * @param  array $module_paths
		 */
		$this->modules = apply_filters( 'woocommerce_mmq_compatibility_modules', $module_paths );

		foreach ( $this->modules as $name => $path ) {
			require_once( $path );
		}
	}

	/**
	 * Get min module version.
	 *
	 * @since  4.0.4
	 * @return bool
	 */
	public function get_required_module_version( $module ) {
		return isset( $this->required[ $module ] ) ? $this->required[ $module ] : null;
	}
	
}

WC_MMQ_Compatibility::core_includes();
