<?php
/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * Woocommerce Add to cart Ajax for variable products
 * http://www.rcreators.com/woocommerce-ajax-add-to-cart-variable-products
 *
 * @package Extra Product Options/Compatibility
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_CP_WATCAFVP {

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

		add_action( 'init', array( $this, 'add_compatibility' ) );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 1.0
	 */
	public function add_compatibility() {
		if ( THEMECOMPLETE_EPO()->cart_edit_key && function_exists( 'woocommerce_add_to_cart_variable_rc_callback' ) ) {
			remove_action( 'wp_enqueue_scripts', 'ajax_add_to_cart_script', 99 );
		}
	}

}
