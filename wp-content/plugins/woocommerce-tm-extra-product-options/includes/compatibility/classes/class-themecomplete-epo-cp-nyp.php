<?php
/**
 * Compatibility class
 *
 * @package Extra Product Options/Compatibility
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * Name Your Price
 * https://woocommerce.com/products/name-your-price/
 *
 * @package Extra Product Options/Compatibility
 * @version 6.4
 */
final class THEMECOMPLETE_EPO_CP_NYP {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_NYP|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return THEMECOMPLETE_EPO_CP_NYP
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'add_compatibility' ] );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @return void
	 * @since 1.0
	 */
	public function add_compatibility() {
		if ( ! class_exists( 'WC_Name_Your_Price' ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 4 );
	}

	/**
	 * Enqueue scripts
	 *
	 * @return void
	 * @since 1.0
	 */
	public function wp_enqueue_scripts() {
		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {
			wp_enqueue_script( 'themecomplete-comp-nyp', THEMECOMPLETE_EPO_COMPATIBILITY_URL . 'assets/js/cp-nyp.js', [ 'jquery' ], THEMECOMPLETE_EPO_VERSION, true );
		}
	}
}
