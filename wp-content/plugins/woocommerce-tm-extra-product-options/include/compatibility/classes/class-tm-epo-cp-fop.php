<?php
/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * Food Online Premium for WooCommerce
 * https://arosoft.se/
 *
 * @package Extra Product Options/Compatibility
 * @version 5.0.12
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_CP_fop {

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'add_compatibility' ), 2 );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 1.0
	 */
	public function add_compatibility() {

		if ( ! class_exists( 'Food_Online' ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 4 );
	}


	/**
	 * Enqueue scripts
	 *
	 * @since 1.0
	 */
	public function wp_enqueue_scripts() {
		// Cannot for loading scripts here as this plugin can be laoded anywhere
		wp_enqueue_script( 'themecomplete-comp-fop', THEMECOMPLETE_EPO_PLUGIN_URL . '/include/compatibility/assets/js/cp-fop.js', array( 'jquery' ), THEMECOMPLETE_EPO_VERSION, TRUE );

	}

}


