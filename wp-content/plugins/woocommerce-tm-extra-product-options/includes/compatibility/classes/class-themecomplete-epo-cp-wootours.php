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
 * WooTour - WooCommerce Travel Tour Booking
 * https://codecanyon.net/item/wootour-woocommerce-travel-tour-and-appointment-booking/19404740
 *
 * @package Extra Product Options/Compatibility
 * @version 6.4
 */
final class THEMECOMPLETE_EPO_CP_Wootours {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_Wootours|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return THEMECOMPLETE_EPO_CP_Wootours
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
		add_action( 'init', [ $this, 'add_compatibility' ] );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @return void
	 * @since 1.0
	 */
	public function add_compatibility() {
		if ( ! class_exists( 'EX_WooTour' ) ) {
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
			wp_enqueue_script( 'themecomplete-comp-wootours', THEMECOMPLETE_EPO_COMPATIBILITY_URL . 'assets/js/cp-wootours.js', [ 'jquery' ], THEMECOMPLETE_EPO_VERSION, true );
		}
	}
}
