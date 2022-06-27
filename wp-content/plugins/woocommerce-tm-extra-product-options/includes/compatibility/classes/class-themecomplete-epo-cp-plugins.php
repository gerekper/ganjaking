<?php
/**
 * Compatibility class
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * various plugins
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_CP_Plugins {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_Plugins|null
	 * @since 6.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 6.0
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
	 * @since 6.0
	 */
	public function __construct() {
		add_action( 'wp', [ $this, 'add_compatibility' ] );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 6.0
	 */
	public function add_compatibility() {
		if ( defined( 'YITH_WC_Min_Max_Qty' ) ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'yith_wc_min_max_qty_wp_enqueue_scripts' ], 4 );
		}

	}

	/**
	 * Woodmart sticky add to cart
	 *
	 * @since 6.0
	 */
	public function yith_wc_min_max_qty_wp_enqueue_scripts() {

		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {
			wp_enqueue_script( 'themecomplete-comp-yith-wc-min-max-qty', THEMECOMPLETE_EPO_COMPATIBILITY_URL . 'assets/js/cp-yith-wc-min-max-qty.js', [ 'jquery' ], THEMECOMPLETE_EPO_VERSION, true );
		}

	}

}
