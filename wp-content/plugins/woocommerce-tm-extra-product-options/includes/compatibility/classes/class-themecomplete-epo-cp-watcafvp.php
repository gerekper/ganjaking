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
 * Woocommerce Add to cart Ajax for variable products
 * http://www.rcreators.com/woocommerce-ajax-add-to-cart-variable-products
 *
 * @package Extra Product Options/Compatibility
 * @version 6.4
 */
final class THEMECOMPLETE_EPO_CP_WATCAFVP {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_WATCAFVP|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return THEMECOMPLETE_EPO_CP_WATCAFVP
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
		if ( THEMECOMPLETE_EPO()->cart_edit_key && function_exists( 'woocommerce_add_to_cart_variable_rc_callback' ) ) {
			remove_action( 'wp_enqueue_scripts', 'ajax_add_to_cart_script', 99 );
		}
	}
}
