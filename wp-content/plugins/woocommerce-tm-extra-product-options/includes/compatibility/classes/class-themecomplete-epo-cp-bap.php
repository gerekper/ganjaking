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
 * Booking & Appointment Plugin for WooCommerce.
 * https://www.tychesoftwares.com/store/premium-plugins/woocommerce-booking-plugin/
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_CP_BAP {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_BAP|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded
	 *
	 * @return THEMECOMPLETE_EPO_CP_BAP
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
		add_action( 'plugins_loaded', [ $this, 'add_compatibility' ], 2 );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @return void
	 * @since 1.0
	 */
	public function add_compatibility() {

		if ( ! class_exists( 'woocommerce_booking' ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 4 );
		add_action( 'bkap_before_booking_form', [ $this, 'bkap_before_booking_form' ], 4 );
	}

	/**
	 * Insert hidden input
	 *
	 * @return void
	 */
	public function bkap_before_booking_form() {
		echo '<input type="hidden" id="product-addons-total">';
	}

	/**
	 * Enqueue scripts
	 *
	 * @return void
	 * @since 1.0
	 */
	public function wp_enqueue_scripts() {
		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {
			wp_enqueue_script( 'themecomplete-comp-bap', THEMECOMPLETE_EPO_COMPATIBILITY_URL . 'assets/js/cp-bap.js', [ 'jquery' ], THEMECOMPLETE_EPO_VERSION, true );
		}
	}
}
